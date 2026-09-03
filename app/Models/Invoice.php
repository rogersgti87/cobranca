<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\InvoiceNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Log;

class Invoice extends Model
{


    protected $fillable = [
        'company_id',
        'user_id',
        'customer_id',
        'customer_service_id',
        'description',
        'price',
        'payment_method',
        'date_invoice',
        'date_due',
        'date_payment',
        'transaction_id',
        'status',
        'gateway_payment',
        'image_url_pix',
        'pix_digitable',
        'qrcode_pix_base64',
        'billet_url',
        'billet_base64',
        'billet_digitable',
        'tax',
        'tax_percent',
        'notificated',
        'msg_erro',
        'public_token',
    ];

    protected static function booted()
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->public_token)) {
                $invoice->public_token = static::generatePublicToken();
            }
        });
    }

    public static function generatePublicToken(): string
    {
        do {
            $token = Str::random(48);
        } while (static::where('public_token', $token)->exists());

        return $token;
    }

    /**
     * Garante token público e retorna a URL da página de pagamento.
     */
    public function publicUrl(): string
    {
        if (empty($this->public_token)) {
            $this->public_token = static::generatePublicToken();
            if ($this->exists) {
                $this->saveQuietly();
            }
        }

        return route('public.invoice.show', $this->public_token);
    }

    public function isPixPayment(): bool
    {
        return $this->payment_method === 'Pix';
    }

    public function isBoletoPayment(): bool
    {
        return in_array($this->payment_method, ['Boleto', 'BoletoPix'], true);
    }

    /**
     * Empresa a qual a fatura pertence
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Usuário que criou a fatura
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cliente da fatura
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Assinatura relacionada à fatura
     */
    public function customerService()
    {
        return $this->belongsTo(CustomerService::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('invoices.company_id', $companyId);
    }

    /**
     * Scope para filtrar por todas as empresas do usuário autenticado
     */
    public function scopeForUserCompanies($query)
    {
        $companyIds = userCompanyIds();
        
        if (empty($companyIds)) {
            return $query->whereRaw('1 = 0');
        }
        
        return $query->whereIn('invoices.company_id', $companyIds);
    }

    /**
     * Scope para faturas pendentes
     */
    public function scopePendente($query)
    {
        return $query->where('status', 'Pendente');
    }

    /**
     * Scope para faturas pagas
     */
    public function scopePago($query)
    {
        return $query->where('status', 'Pago');
    }

    /**
     * Monta as opções HTTP para requisições ao Banco Inter.
     */
    private static function buildInterHttpOptions(Company $company): array
    {
        $certPath = storage_path('/app/'.$company->inter_crt_file);
        $keyPath = storage_path('/app/'.$company->inter_key_file);
        $sslVerify = env('INTER_SSL_VERIFY', true);
        $caBundle = env('INTER_CA_BUNDLE', '');

        $httpOptions = [
            'cert' => $certPath,
            'ssl_key' => $keyPath,
            'curl' => [],
        ];

        if ($sslVerify === false) {
            $httpOptions['verify'] = false;
            $httpOptions['curl'][CURLOPT_SSL_VERIFYPEER] = false;
            $httpOptions['curl'][CURLOPT_SSL_VERIFYHOST] = false;
        } elseif (!empty($caBundle)) {
            $httpOptions['verify'] = $caBundle;
        }

        return $httpOptions;
    }

    /**
     * Renova o access token OAuth do Banco Inter.
     */
    private static function refreshInterAccessToken(Company $company, array $httpOptions): array
    {
        $response = Http::withOptions($httpOptions)->asForm()->post(
            rtrim($company->inter_host, '/').'/oauth/v2/token',
            [
                'client_id' => $company->inter_client_id,
                'client_secret' => $company->inter_client_secret,
                'scope' => $company->inter_scope ?? 'boleto-cobranca.read boleto-cobranca.write',
                'grant_type' => 'client_credentials',
            ]
        );

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => 'Não foi possível autenticar no Banco Inter. Verifique client_id, client_secret e certificado.',
            ];
        }

        $access_token = json_decode($response->body())->access_token ?? null;

        if (empty($access_token)) {
            return ['success' => false, 'message' => 'Banco Inter não retornou token de acesso.'];
        }

        Company::where('id', $company->id)->update(['access_token_inter' => $access_token]);
        $company->access_token_inter = $access_token;

        return ['success' => true, 'token' => $access_token];
    }

    /**
     * Executa GET na API do Inter, renovando o token automaticamente em caso de 401.
     */
    private static function interApiGet(Company $company, string $url, array $httpOptions, bool $retryOnUnauthorized = true, int $timeoutSeconds = 15)
    {
        $access_token = $company->access_token_inter;

        if (empty($access_token)) {
            $refresh = self::refreshInterAccessToken($company, $httpOptions);
            if (!$refresh['success']) {
                return null;
            }
            $access_token = $refresh['token'];
        }

        $response = Http::timeout($timeoutSeconds)->withOptions($httpOptions)->withHeaders([
            'Authorization' => 'Bearer '.$access_token,
        ])->get($url);

        if ($retryOnUnauthorized && $response->status() === 401) {
            $refresh = self::refreshInterAccessToken($company, $httpOptions);
            if (!$refresh['success']) {
                return $response;
            }

            return Http::timeout($timeoutSeconds)->withOptions($httpOptions)->withHeaders([
                'Authorization' => 'Bearer '.$refresh['token'],
            ])->get($url);
        }

        return $response;
    }

    /**
     * Formata mensagens de erro retornadas pela API do Banco Inter.
     * Nem todos os erros incluem a chave "valor" (ex.: certificado ausente).
     */
    public static function formatInterErrorMessages($messages): string
    {
        if (!is_array($messages)) {
            return (string) $messages;
        }

        $parts = [];

        foreach ($messages as $message) {
            if (!is_array($message)) {
                $parts[] = (string) $message;
                continue;
            }

            $line = trim(
                ($message['razao'] ?? '') .
                (isset($message['propriedade']) ? ' - ' . $message['propriedade'] : '') .
                (isset($message['valor']) ? ' - ' . $message['valor'] : ''),
                ' -'
            );

            if ($line !== '') {
                $parts[] = $line;
            }
        }

        return implode(', ', $parts);
    }

    private static function interCertOptions($company): array
    {
        return [
            'cert' => storage_path('/app/'.$company->inter_crt_file),
            'ssl_key' => storage_path('/app/'.$company->inter_key_file),
        ];
    }

    private static function interCobrancaHasPaymentData($result): bool
    {
        if (!$result) {
            return false;
        }

        // A API v3 retorna boleto/pix no nível raiz (irmãos de "cobranca"),
        // e em alguns casos também aninhados em cobranca.
        return self::extractInterLinhaDigitavel($result) !== ''
            || !empty(self::extractInterPixCopiaECola($result));
    }

    private static function interCobrancaIsProcessing($result): bool
    {
        $situacao = $result->cobranca->situacao ?? null;

        return $situacao === 'EM_PROCESSAMENTO';
    }

    private static function extractInterLinhaDigitavel($result): string
    {
        if (!$result) {
            return '';
        }

        $paths = [
            $result->cobranca->boleto->linhaDigitavel ?? null,
            $result->boleto->linhaDigitavel ?? null,
            $result->cobranca->linhaDigitavel ?? null,
            $result->linhaDigitavel ?? null,
        ];

        foreach ($paths as $value) {
            if (!empty($value)) {
                return (string) $value;
            }
        }

        return '';
    }

    private static function extractInterPixCopiaECola($result): ?string
    {
        if (!$result) {
            return null;
        }

        $paths = [
            $result->cobranca->pix->pixCopiaECola ?? null,
            $result->pix->pixCopiaECola ?? null,
            $result->cobranca->pixCopiaECola ?? null,
            $result->pixCopiaECola ?? null,
        ];

        foreach ($paths as $value) {
            if (!empty($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    private static function validateInterCredentials($company, string $title = 'Erro ao gerar cobrança'): ?array
    {
        if (empty($company->access_token_inter)) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token inválido!']]];
        }
        if (empty($company->inter_host)) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'HOST banco inter não cadastrado!']]];
        }
        if (empty($company->inter_client_id)) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT ID banco inter não cadastrado!']]];
        }
        if (empty($company->inter_client_secret)) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT SECRET banco inter não cadastrado!']]];
        }
        if (empty($company->inter_crt_file) || !file_exists(storage_path('/app/'.$company->inter_crt_file))) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado CRT banco inter não cadastrado ou inexistente!']]];
        }
        if (empty($company->inter_key_file) || !file_exists(storage_path('/app/'.$company->inter_key_file))) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado KEY banco inter não cadastrado ou inexistente!']]];
        }

        return null;
    }

    private static function ensureInterAccessToken($company): array
    {
        $access_token = $company->access_token_inter;
        $options = self::interCertOptions($company);

        $check = Http::withOptions($options)->withHeaders([
            'Authorization' => 'Bearer '.$access_token,
        ])->get(rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

        if (!$check->unauthorized()) {
            return ['success' => true, 'token' => $access_token];
        }

        $response = Http::withOptions($options)->asForm()->post(rtrim($company->inter_host, '/').'/oauth/v2/token', [
            'client_id' => $company->inter_client_id,
            'client_secret' => $company->inter_client_secret,
            'scope' => $company->inter_scope,
            'grant_type' => 'client_credentials',
        ]);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'Access token expirado ou inválido!'];
        }

        $access_token = json_decode($response->body())->access_token ?? null;
        if (empty($access_token)) {
            return ['success' => false, 'message' => 'Banco Inter não retornou token de acesso.'];
        }

        Company::where('id', $company->id)->update(['access_token_inter' => $access_token]);
        $company->access_token_inter = $access_token;

        return ['success' => true, 'token' => $access_token];
    }

    private static function resolveInterDueDate($invoice): string
    {
        $dataVencimento = Carbon::parse($invoice->date_due);
        $dataAtual = Carbon::now();

        if ($dataVencimento->lt($dataAtual)) {
            $dataVencimento = $dataAtual->copy();

            if ($dataVencimento->isSaturday()) {
                $dataVencimento->addDays(2);
            } elseif ($dataVencimento->isSunday()) {
                $dataVencimento->addDay();
            }

            \Log::warning("Data de vencimento ajustada para invoice ID: {$invoice->id} ({$invoice->payment_method}). Data original: {$invoice->date_due}, Nova data: {$dataVencimento->format('Y-m-d')}");
        }

        return $dataVencimento->format('Y-m-d');
    }

    private static function fetchInterCobrancaDetails($company, $access_token, $codigoSolicitacao, string $logContext = '', int $maxAttempts = 30, int $sleepSeconds = 3)
    {
        $url = rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas/'.$codigoSolicitacao;
        $options = self::interCertOptions($company);
        $headers = ['Authorization' => 'Bearer '.$access_token];
        $result = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $response = Http::withOptions($options)->withHeaders($headers)->get($url);
            if (!$response->successful()) {
                \Log::error('Erro ao obter detalhes da cobrança Inter'.$logContext.' (tentativa '.$attempt.'): '.$response->body());

                return null;
            }

            $result = json_decode($response->body());

            if (!self::interCobrancaIsProcessing($result) || self::interCobrancaHasPaymentData($result)) {
                if ($attempt > 1) {
                    $situacao = $result->cobranca->situacao ?? 'desconhecida';
                    \Log::info('Cobrança Inter pronta'.$logContext.' após '.$attempt.' tentativa(s). Situação: '.$situacao);
                }
                break;
            }

            if ($attempt < $maxAttempts) {
                \Log::info('Cobrança Inter em processamento'.$logContext.', tentativa '.$attempt.'/'.$maxAttempts);
                sleep($sleepSeconds);
            }
        }

        \Log::info('Estrutura resposta get_billet'.$logContext.': '.json_encode($result));

        return $result;
    }

    private static function fetchInterCobrancaPdf($company, $access_token, $codigoSolicitacao, string $logContext = '', int $maxAttempts = 20, int $sleepSeconds = 3)
    {
        $url = rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas/'.$codigoSolicitacao.'/pdf';
        $options = self::interCertOptions($company);
        $headers = ['Authorization' => 'Bearer '.$access_token];
        $response = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $response = Http::withOptions($options)->withHeaders($headers)->get($url);

            if ($response->successful()) {
                return $response;
            }

            $body = $response->body();
            if ($response->status() === 400 && str_contains(strtolower($body), 'processamento')) {
                \Log::info('PDF Inter ainda em processamento'.$logContext.', tentativa '.$attempt.'/'.$maxAttempts);
                sleep($sleepSeconds);
                continue;
            }

            \Log::error('Erro ao gerar PDF da cobrança Inter'.$logContext.': '.$body);

            return $response;
        }

        return $response;
    }

    private static function generateInterPixQrCode($invoice, string $pixCopiaECola): array
    {
        // QR Code é gerado dinamicamente na página pública a partir do pix_digitable.
        return [
            'image_url_pix' => null,
            'qrcode_pix_base64' => null,
        ];
    }

    private static function persistInterCobrancaInvoice(
        int $invoice_id,
        $invoice,
        string $codigoSolicitacao,
        $result,
        ?string $pdfBase64,
        string $storageFolder,
        bool $includePix,
        string $invoiceStatus = 'Pendente'
    ): void {
        $update = [
            'status' => $invoiceStatus,
            'msg_erro' => null,
            'transaction_id' => $codigoSolicitacao,
            'billet_digitable' => self::extractInterLinhaDigitavel($result),
        ];

        // Não baixar/salvar PDF do boleto no storage — a página pública usa a linha digitável.

        if ($includePix) {
            $pixCopiaECola = self::extractInterPixCopiaECola($result);
            if (!empty($pixCopiaECola)) {
                $update['pix_digitable'] = $pixCopiaECola;
            }
        }

        self::where('id', $invoice_id)->update($update);
    }

    /**
     * Emite cobrança unificada BolePix v3 (boleto + QR Code Pix) no Banco Inter.
     * Usado tanto para payment_method Boleto quanto BoletoPix.
     */
    public static function generateInterCobrancaV3($invoice_id): array
    {
        $invoice = self::with(['customerService.customer', 'company'])->find($invoice_id);
        if (!$invoice) {
            return ['status' => 'reject', 'title' => 'Erro ao gerar cobrança', 'message' => [['razao' => 'Fatura não encontrada', 'propriedade' => 'invoice_id']]];
        }

        $customer = $invoice->customerService->customer;
        $company = $invoice->company;
        $title = $invoice->payment_method === 'BoletoPix' ? 'Erro ao gerar BoletoPix' : 'Erro ao gerar Boleto';
        $storageFolder = $invoice->payment_method === 'BoletoPix' ? 'boletopix' : 'boletos';
        $includePix = $invoice->payment_method === 'BoletoPix';
        $logContext = $includePix ? ' (BoletoPix)' : '';

        if (!Storage::disk('public')->exists($storageFolder)) {
            Storage::disk('public')->makeDirectory($storageFolder);
        }

        // Se já existe cobrança no Inter, só conclui PDF/Pix (evita duplicar emissão).
        if (!empty($invoice->transaction_id)) {
            $complete = self::completeInterCobrancaProcessing($invoice_id);
            if ($complete['success'] ?? false) {
                return [
                    'status' => 'success',
                    'title' => 'OK',
                    'message' => [['razao' => 'OK', 'propriedade' => $complete['message'] ?? 'OK']],
                    'invoice_status' => ($complete['completed'] ?? false) ? 'Pendente' : 'Processamento',
                    'invoice_updated' => true,
                ];
            }

            return [
                'status' => 'reject',
                'title' => $title,
                'message' => [['razao' => $complete['message'] ?? 'Erro ao concluir cobrança', 'propriedade' => 'completeInterCobrancaProcessing']],
            ];
        }

        $credentialError = self::validateInterCredentials($company, $title);
        if ($credentialError !== null) {
            return $credentialError;
        }

        $tokenResult = self::ensureInterAccessToken($company);
        if (!$tokenResult['success']) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => $tokenResult['message']]]];
        }

        $access_token = $tokenResult['token'];
        $dataVencimentoFormatada = self::resolveInterDueDate($invoice);
        $options = self::interCertOptions($company);

        // BolePix v3 unificado (Boleto e BoletoPix): mesma API com boleto + Pix.
        // Doc Inter: formasRecebimento default ["BOLETO","PIX"] (PIX só se houver chave registrada).
        $formasRecebimento = ['BOLETO', 'PIX'];

        $payload = [
            'seuNumero' => (string) $invoice->id,
            'valorNominal' => (float) $invoice->price,
            'dataVencimento' => $dataVencimentoFormatada,
            'numDiasAgenda' => 60,
            'formasRecebimento' => $formasRecebimento,
            'pagador' => [
                'cpfCnpj' => preg_replace('/\D/', '', (string) $customer->document),
                'nome' => $customer->name,
                'email' => $customer->email,
                'telefone' => substr(preg_replace('/\D/', '', (string) $customer->whatsapp), 2),
                'cep' => removeEspeciais($customer->cep),
                'numero' => $customer->number,
                'complemento' => $customer->complement,
                'bairro' => $customer->district,
                'cidade' => $customer->city,
                'uf' => $customer->state,
                'endereco' => $customer->address,
                'ddd' => substr(preg_replace('/\D/', '', (string) $customer->whatsapp), 0, 2),
                'tipoPessoa' => $customer->type == 'Física' ? 'FISICA' : 'JURIDICA',
            ],
            'multa' => [
                'codigo' => 'PERCENTUAL',
                'taxa' => 1,
            ],
        ];

        \Log::info('Emitindo cobrança Inter v3'.$logContext, [
            'invoice_id' => $invoice_id,
            'formasRecebimento' => $formasRecebimento,
            'dataVencimento' => $dataVencimentoFormatada,
        ]);

        $response_generate = Http::withOptions($options)->withHeaders([
            'Authorization' => 'Bearer '.$access_token,
            'Content-Type' => 'application/json',
        ])->post(rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas', $payload);

        if (!$response_generate->successful()) {
            $result_generate = $response_generate->json();

            return [
                'status' => 'reject',
                'title' => $result_generate['title'] ?? $title,
                'message' => $result_generate['violacoes'] ?? [['razao' => 'Erro na API Inter', 'propriedade' => 'cobranca/v3/cobrancas']],
            ];
        }

        $result_generate = json_decode($response_generate->body());
        if (empty($result_generate->codigoSolicitacao)) {
            \Log::error('Erro ao gerar cobrança Inter: codigoSolicitacao não retornado para invoice ID: '.$invoice_id);

            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'codigoSolicitacao não retornado pela API', 'propriedade' => 'Erro ao gerar cobrança intermedium!']]];
        }

        $codigoSolicitacao = trim((string) $result_generate->codigoSolicitacao);

        // Salva o codigo já na emissão para não perder se o worker cair.
        self::where('id', $invoice_id)->update([
            'transaction_id' => $codigoSolicitacao,
            'status' => 'Processamento',
            'msg_erro' => null,
        ]);

        // Poll curto: o restante fica para StatusInterCron / completeInterCobrancaProcessing.
        $result_get = self::fetchInterCobrancaDetails($company, $access_token, $codigoSolicitacao, $logContext, 10, 2);

        if ($result_get === null) {
            return [
                'status' => 'success',
                'title' => 'OK',
                'message' => [['razao' => 'OK', 'propriedade' => 'Cobrança emitida; detalhes serão concluídos em background']],
                'invoice_status' => 'Processamento',
                'invoice_updated' => true,
            ];
        }

        if (self::interCobrancaIsProcessing($result_get) && !self::interCobrancaHasPaymentData($result_get)) {
            \Log::warning('Cobrança Inter ainda em processamento'.$logContext.' para invoice ID: '.$invoice_id.'. Será concluída pelo cron.');

            return [
                'status' => 'success',
                'title' => 'OK',
                'message' => [['razao' => 'OK', 'propriedade' => 'Cobrança em processamento no Inter']],
                'invoice_status' => 'Processamento',
                'invoice_updated' => true,
            ];
        }

        $response_pdf = null;
        $pdfBase64 = null;

        $invoiceStatus = self::interCobrancaHasPaymentData($result_get) ? 'Pendente' : 'Processamento';
        self::persistInterCobrancaInvoice($invoice_id, $invoice, $codigoSolicitacao, $result_get, $pdfBase64, $storageFolder, $includePix, $invoiceStatus);

        return [
            'status' => 'success',
            'title' => 'OK',
            'message' => [['razao' => 'OK', 'propriedade' => 'OK']],
            'invoice_status' => $invoiceStatus,
            'invoice_updated' => true,
        ];
    }

    /**
     * Conclui faturas Inter em Processamento (PDF/Pix ainda não disponíveis na emissão).
     */
    public static function completeInterCobrancaProcessing($invoice_id): array
    {
        $invoice = self::with(['customerService.customer', 'company'])->find($invoice_id);
        if (!$invoice || empty($invoice->transaction_id)) {
            return ['success' => false, 'completed' => false, 'message' => 'Fatura não encontrada ou sem transaction_id.'];
        }

        $company = $invoice->company;
        $credentialError = self::validateInterCredentials($company, 'Erro ao concluir cobrança');
        if ($credentialError !== null) {
            return ['success' => false, 'completed' => false, 'message' => self::formatInterErrorMessages($credentialError['message'])];
        }

        $tokenResult = self::ensureInterAccessToken($company);
        if (!$tokenResult['success']) {
            return ['success' => false, 'completed' => false, 'message' => $tokenResult['message']];
        }

        $access_token = $tokenResult['token'];
        $codigoSolicitacao = trim((string) $invoice->transaction_id);
        $logContext = $invoice->payment_method === 'BoletoPix' ? ' (BoletoPix)' : '';
        $storageFolder = $invoice->payment_method === 'BoletoPix' ? 'boletopix' : 'boletos';
        $includePix = $invoice->payment_method === 'BoletoPix';

        $result_get = self::fetchInterCobrancaDetails($company, $access_token, $codigoSolicitacao, $logContext, 5, 2);
        if ($result_get === null) {
            return ['success' => false, 'completed' => false, 'message' => 'Erro ao consultar cobrança no Inter.'];
        }

        $hasPaymentData = self::interCobrancaHasPaymentData($result_get);

        if (self::interCobrancaIsProcessing($result_get) && !$hasPaymentData) {
            return ['success' => true, 'completed' => false, 'message' => 'Cobrança ainda em processamento no Inter.'];
        }

        $pdfBase64 = null;

        // Libera a fatura com linha digitável/Pix — sem baixar PDF do gateway.
        $invoiceStatus = $hasPaymentData || !empty($invoice->billet_digitable)
            ? 'Pendente'
            : 'Processamento';

        self::persistInterCobrancaInvoice($invoice_id, $invoice, $codigoSolicitacao, $result_get, $pdfBase64, $storageFolder, $includePix, $invoiceStatus);

        return [
            'success' => true,
            'completed' => $invoiceStatus === 'Pendente',
            'message' => $invoiceStatus === 'Pendente'
                ? 'Cobrança concluída com dados de pagamento.'
                : 'Cobrança parcialmente concluída, aguardando dados do Inter.',
        ];
    }

    /**
     * Consulta o status de pagamento no Banco Inter (rápido: 1 request, sem polling).
     * Boleto/BoletoPix: GET /cobranca/v3/cobrancas/{codigoSolicitacao}
     * Pix puro: GET /pix/v2/cobv/{txid}
     * Legado: nossoNumero numérico ainda usa cobranca/v2.
     */
    public static function syncInterPaymentStatus(self $invoice, bool $notify = true): array
    {
        $company = $invoice->company;

        if (!$company || $company->status !== 'Ativo') {
            return ['success' => false, 'message' => 'Empresa inativa ou não encontrada.'];
        }

        $certPath = storage_path('/app/'.$company->inter_crt_file);
        $keyPath = storage_path('/app/'.$company->inter_key_file);

        if (empty($company->inter_host) || empty($company->inter_client_id) || empty($company->inter_client_secret)
            || empty($company->inter_crt_file) || empty($company->inter_key_file)
            || !file_exists($certPath) || !file_exists($keyPath)) {
            return ['success' => false, 'message' => 'Credenciais ou certificados do Banco Inter não configurados.'];
        }

        $certData = @openssl_x509_parse(file_get_contents($certPath));
        if ($certData && isset($certData['validTo_time_t']) && $certData['validTo_time_t'] < time()) {
            return ['success' => false, 'message' => 'Certificado do Banco Inter expirado.'];
        }

        $httpOptions = self::buildInterHttpOptions($company);

        $transaction_id = trim((string) $invoice->transaction_id);
        if ($transaction_id === '') {
            return ['success' => false, 'message' => 'Fatura sem identificador de transação.'];
        }

        $transaction_id_clean = preg_replace('/[^a-zA-Z0-9\-]/', '', $transaction_id);
        $gatewayStatus = null;
        $apiVersion = null;

        if (in_array($invoice->payment_method, ['Boleto', 'BoletoPix'], true)) {
            $isUuid = (bool) preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $transaction_id_clean);

            // Legado: nossoNumero numérico da API v2 antiga.
            if (!$isUuid && is_numeric($transaction_id_clean) && $invoice->payment_method === 'Boleto') {
                $apiVersion = 'v2';
                $nossoNumero = $transaction_id_clean;
                $dataVencimento = $invoice->date_due ?? Carbon::now()->format('Y-m-d');
                $dataInicial = Carbon::parse($dataVencimento)->subDays(30)->format('Y-m-d');
                $dataFinal = Carbon::parse($dataVencimento)->addDays(30)->format('Y-m-d');
                $url_v2 = rtrim($company->inter_host, '/').'/cobranca/v2/boletos?dataInicial='.$dataInicial.'&dataFinal='.$dataFinal.'&nossoNumero='.$nossoNumero.'&itensPorPagina=100';

                $response_v2 = self::interApiGet($company, $url_v2, $httpOptions);

                if ($response_v2 === null) {
                    return ['success' => false, 'message' => 'Não foi possível autenticar no Banco Inter.'];
                }

                if (!$response_v2->successful()) {
                    if ($response_v2->status() === 404) {
                        return ['success' => true, 'updated' => false, 'gateway_status' => null, 'message' => 'Cobrança não encontrada no Banco Inter (legado v2).'];
                    }

                    return ['success' => false, 'message' => 'Erro ao consultar status no Banco Inter (HTTP '.$response_v2->status().').'];
                }

                $boletos = json_decode($response_v2->body(), true)['content'] ?? [];
                $boleto_encontrado = null;

                foreach ($boletos as $boleto) {
                    if (isset($boleto['nossoNumero']) && $boleto['nossoNumero'] == $nossoNumero) {
                        $boleto_encontrado = $boleto;
                        break;
                    }
                }

                if (!$boleto_encontrado) {
                    return ['success' => true, 'updated' => false, 'gateway_status' => null, 'message' => 'Boleto não encontrado no Banco Inter.'];
                }

                $gatewayStatus = $boleto_encontrado['situacao'] ?? null;
            } else {
                // API correta BolePix v3: GET /cobranca/v3/cobrancas/{codigoSolicitacao}
                $apiVersion = 'v3';
                $url = rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas/'.$transaction_id_clean;

                $response = self::interApiGet($company, $url, $httpOptions);

                if ($response === null) {
                    return ['success' => false, 'message' => 'Não foi possível autenticar no Banco Inter.'];
                }

                if (!$response->successful()) {
                    if ($response->status() === 404) {
                        return ['success' => true, 'updated' => false, 'gateway_status' => null, 'message' => 'Cobrança não encontrada no Banco Inter.'];
                    }

                    return ['success' => false, 'message' => 'Erro ao consultar status no Banco Inter (HTTP '.$response->status().').'];
                }

                $responseData = json_decode($response->body());
                $gatewayStatus = $responseData->cobranca->situacao ?? null;

                // Não bloqueia a UI com polling: completa PDF/Pix em background se necessário.
                if (
                    in_array($gatewayStatus, ['EM_PROCESSAMENTO', 'A_RECEBER'], true)
                    && in_array($invoice->status, ['Processamento', 'Gerando'], true)
                    && (empty($invoice->billet_url) || ($invoice->payment_method === 'BoletoPix' && empty($invoice->pix_digitable)))
                ) {
                    $queue = $invoice->company_id ? 'company_'.$invoice->company_id : 'default';
                    \App\Jobs\GenerateInterInvoiceJob::dispatch($invoice->id, false, false)
                        ->onQueue($queue)
                        ->afterResponse();
                }
            }
        } elseif ($invoice->payment_method === 'Pix') {
            $apiVersion = 'pix/v2';
            $url = rtrim($company->inter_host, '/').'/pix/v2/cobv/'.$transaction_id_clean;

            $response = self::interApiGet($company, $url, $httpOptions);

            if ($response === null) {
                return ['success' => false, 'message' => 'Não foi possível autenticar no Banco Inter.'];
            }

            if (!$response->successful()) {
                if ($response->status() === 404) {
                    return ['success' => true, 'updated' => false, 'gateway_status' => null, 'message' => 'Pix não encontrado no Banco Inter.'];
                }

                return ['success' => false, 'message' => 'Erro ao consultar status no Banco Inter (HTTP '.$response->status().').'];
            }

            $responseData = json_decode($response->body());
            $gatewayStatus = $responseData->status ?? null;
        } else {
            return ['success' => false, 'message' => 'Método de pagamento não suportado para consulta no Inter.'];
        }

        $paidStatuses = $invoice->payment_method === 'Pix'
            ? ['CONCLUIDA']
            : ['PAGO', 'RECEBIDO'];

        $cancelledStatuses = $invoice->payment_method === 'Pix'
            ? ['REMOVIDA_PELO_USUARIO_RECEBEDOR', 'REMOVIDA_PELO_PSP']
            : ['CANCELADO', 'CANCELADA', 'EXPIRADO'];

        if (in_array($gatewayStatus, $paidStatuses, true)) {
            self::where('id', $invoice->id)->update([
                'status' => 'Pago',
                'date_payment' => $invoice->date_payment ?? Carbon::now(),
            ]);

            if ($notify) {
                InvoiceNotification::Email($invoice->id);
                InvoiceNotification::Whatsapp($invoice->id);
            }

            return [
                'success' => true,
                'updated' => true,
                'gateway_status' => $gatewayStatus,
                'api_version' => $apiVersion,
                'message' => 'Status atualizado para Pago.',
            ];
        }

        if (in_array($gatewayStatus, $cancelledStatuses, true) && $invoice->status !== 'Cancelado') {
            self::where('id', $invoice->id)->update([
                'status' => $gatewayStatus === 'EXPIRADO' ? 'Expirado' : 'Cancelado',
                'date_payment' => null,
            ]);

            return [
                'success' => true,
                'updated' => true,
                'gateway_status' => $gatewayStatus,
                'api_version' => $apiVersion,
                'message' => 'Status atualizado conforme o Inter: '.$gatewayStatus.'.',
            ];
        }

        return [
            'success' => true,
            'updated' => false,
            'gateway_status' => $gatewayStatus,
            'api_version' => $apiVersion,
            'message' => 'Status consultado. Situação no gateway: '.($gatewayStatus ?? 'desconhecida').'.',
        ];
    }

      public static function generateBilletPH($invoice_id){

        $invoice = Invoice::with(['customerService.customer', 'company'])->find($invoice_id);

        $customer = $invoice->customerService->customer;
        $company = $invoice->company;
        $days_due_date = Carbon::parse($invoice->date_due)->diffInDays($invoice->date_invoice);

        if($days_due_date == 0){
            if(date('l') == 'Saturday' || date('l') == 'Sábado'){
                $days_due_date = 2;
            }
            if(date('l') == 'Sunday' || date('l') == 'Domingo'){
                $days_due_date = 1;
            }
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
          ])->post('https://api.paghiper.com/transaction/create/',[
            'apiKey'            =>  $company->key_paghiper,
            'order_id'          =>  $invoice->id,
            'payer_email'       =>  $customer->email,
            'payer_name'        =>  $customer->name,
            'payer_cpf_cnpj'    =>  removeEspeciais($customer->document),
            'payer_phone'       =>  $customer->phone,
            'payer_street'      =>  $customer->address,
            'payer_number'      =>  $customer->number,
            'payer_complement'  =>  $customer->complement,
            'payer_district'    =>  $customer->district,
            'payer_city'        =>  $customer->city,
            'payer_state'       =>  $customer->state,
            'payer_zip_code'    =>  $customer->cep,
            'type_bank_slip'    => 'boletoA4',
            'days_due_date'     =>  $days_due_date,
            'notification_url'  => 'https://cobrancasegura.com.br/webhook/paghiper',
            'late_payment_fine' => '1',// Percentual de multa após vencimento.
            'per_day_interest'  => true, // Juros após vencimento.
            'items' => array([
                'item_id'       => 1,
                'description'   => $invoice->description,
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice->price,100)
          ])
          ]);


          if ($response->successful()) {

            $result = $response->getBody();
            $result = json_decode($result)->create_request;

            if($result->result == 'success'){

            Invoice::where('id',$invoice_id)->update([
                'status'            =>  'Pendente',
                'msg_erro'          =>  null,
                'transaction_id'    =>  $result->transaction_id,
                'billet_digitable'  =>  $result->bank_slip->digitable_line
            ]);

            return ['status' => 'success', 'message' => 'ok'];

        }
            if($result->result == 'reject'){
                return ['status' => 'reject', 'message' => $result->response_message];
            }

        } else{
            return ['status' => 'reject', 'message' => 'Erro interno no servidor'];
        }


      }

      public static function generatePixPH($invoice_id){

        $invoice = Invoice::with(['customerService.customer', 'company'])->find($invoice_id);

        $customer = $invoice->customerService->customer;
        $company = $invoice->company;

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
          ])->post('https://pix.paghiper.com/invoice/create/',[
            'apiKey'            =>  $company->key_paghiper,
            'order_id'          =>  $invoice->id,
            'payer_email'       =>  $customer->email,
            'payer_name'        =>  $customer->name,
            'payer_cpf_cnpj'    =>  $customer->document,
            'days_due_date'     =>  90,
            'notification_url'  => 'https://cobrancasegura.com.br/webhook/paghiper',
            'items' => array([
                'item_id'       => 1,
                'description'   => $invoice->description,
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice->price,100)
          ])
          ]);

        if ($response->successful()) {
            $result = $response->getBody();
            $result = json_decode($result)->pix_create_request;

            if($result->result == 'success'){
                Invoice::where('id',$invoice_id)->update([
                    'status'            =>  'Pendente',
                    'msg_erro'          =>  null,
                    'transaction_id'    => $result->transaction_id,
                    'pix_digitable'     => $result->pix_code->emv,
                ]);

                return ['status' => 'success', 'message' => 'ok'];
            }

            if($result->result == 'reject'){
                return ['status' => 'reject', 'message' => $result->response_message];
            }

        }else{
            return ['status' => 'reject', 'message' => 'Erro interno no servidor'];
        }

    }



      public static function generatePixMP($invoice_id){

        $invoice = Invoice::with(['customerService.customer', 'company'])->find($invoice_id);

        $customer = $invoice->customerService->customer;
        $company = $invoice->company;

        \MercadoPago\SDK::setAccessToken($company->access_token_mp);

        $payment = new \MercadoPago\Payment();
        $payment->transaction_amount    = $invoice->price;
        $payment->statement_descriptor  = $customer->company;
        $payment->description           = $invoice->description;
        $payment->payment_method_id     = "pix";
        $payment->notification_url      = 'https://cobrancasegura.com.br/webhook/mercadopago?source_news=webhooks';
        $payment->external_reference    = $invoice->id;
        $payment->date_of_expiration    = Carbon::now()->addDays(40)->format('Y-m-d\TH:i:s') . '.000-04:00';
        $payment->payer = array(
            "email"    => $customer->email
        );

        $status_payment = $payment->save();

       $payment_id = $payment->id ? $payment->id : '';

       if($payment_id == ''){
            return ['status' => 'reject', 'message' => 'O valor do pix esta abaixo do minimo permitido de R$ 3,00.'];
        }else{

            \MercadoPago\SDK::setAccessToken($company->access_token_mp);

            try{
                $payment = \MercadoPago\Payment::find_by_id($payment_id);

                Invoice::where('id',$invoice_id)->update([
                    'status'            =>  'Pendente',
                    'msg_erro'          =>  null,
                    'transaction_id'    => $payment_id,
                    'pix_digitable'     => $payment->point_of_interaction->transaction_data->qr_code,
                ]);

                return ['status' => 'success', 'message' => 'OK'];

            } catch(\Exception $e){
                \Log::error($e->getMessage());
            }

        }

      }



      public static function generatePixIntermedium($invoice_id){

        $invoice = Invoice::with(['customerService.customer', 'company'])->find($invoice_id);

        $customer = $invoice->customerService->customer;
        $company = $invoice->company;

        $access_token = $company->access_token_inter;

        if($access_token == null){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token inválido!']]];
        }
        if($company->inter_host == ''){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'HOST banco inter não cadastrado!']]];
        }
        if($company->inter_client_id == ''){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT ID banco inter não cadastrado!']]];
        }
        if($company->inter_client_secret == ''){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT SECRET banco inter não cadastrado!']]];
        }
        if($company->inter_crt_file == ''){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado CRT banco inter não cadastrado!']]];
        }
        if(!file_exists(storage_path('/app/'.$company->inter_crt_file))){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado CRT banco inter não existe!']]];
        }
        if($company->inter_key_file == ''){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado KEY banco inter não cadastrado!']]];
        }
        if(!file_exists(storage_path('/app/'.$company->inter_key_file))){
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado KEY banco inter não existe!']]];
        }


        $check_access_token = Http::withOptions(
            [
            'cert' => storage_path('/app/'.$company->inter_crt_file),
            'ssl_key' => storage_path('/app/'.$company->inter_key_file)
            ]
            )->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
        ])->get('https://cdpj.partners.bancointer.com.br/pix/v2/cob?inicio=2023-02-30T12:09:00Z&fim=2023-03-30T12:09:00Z');

        if ($check_access_token->unauthorized()) {
            $response = Http::withOptions([
                'cert' => storage_path('/app/'.$company->inter_crt_file),
                'ssl_key' => storage_path('/app/'.$company->inter_key_file),
            ])->asForm()->post($company->inter_host.'oauth/v2/token', [
                'client_id' => $company->inter_client_id,
                'client_secret' => $company->inter_client_secret,
                'scope' => $company->inter_scope,
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $responseBody = $response->body();
                $access_token = json_decode($responseBody)->access_token;
                \App\Models\Company::where('id',$company->id)->update([
                    'access_token_inter' => $access_token
                ]);

                $company->refresh();
            }else{
                return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token Expirado ou inválido!']]];
            }
        }

        $txid = generateUniqueId();

       $body = [
            "calendario"                    => [
                "dataDeVencimento"          => $invoice->date_due,
                "validadeAposVencimento"    => 30,
            ],
            // "loc"                           => [
            //     "id"                        => $invoice->id,
            // ],
            "devedor"                       => [
                "logradouro"                => $customer->address,
                "cidade"                    => $customer->city,
                "uf"                        => $customer->state,
                "cep"                       => removeEspeciais($customer->cep),
              ],
            "valor"                         => [
                "original"                  => $invoice->price,
                "juros"                     => [
                    "modalidade"            => "2",
                    "valorPerc"             => "1.00"
                ],
            ],
            "chave"                         => $company->inter_chave_pix,
            "solicitacaoPagador"            => $invoice->description
            ];

        if($customer->type == 'Física'){
            $body['devedor']['cpf']     = $customer->document;
            $body['devedor']['nome']    = Str::limit($customer->name, 30);
        }else{
            $body['devedor']['cnpj']    = $customer->document;
            $body['devedor']['nome']    = Str::limit($customer->name, 30);
        }


        $response_generate_pix = Http::withOptions([
            'cert' => storage_path('/app/'.$company->inter_crt_file),
            'ssl_key' => storage_path('/app/'.$company->inter_key_file),
            ])->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
          ])->put($company->inter_host.'pix/v2/cobv/'.$txid,$body);


        if ($response_generate_pix->successful()) {

            $result_generate_pix = $response_generate_pix->json();
            $result_generate_pix = json_decode(json_encode($result_generate_pix));


            Invoice::where('id',$invoice_id)->update([
                'status'            => 'Pendente',
                'msg_erro'          =>  null,
                'transaction_id'    => $result_generate_pix->txid,
                'pix_digitable'     => $result_generate_pix->pixCopiaECola,
            ]);

            return ['status' => 'success', 'title' => 'OK', 'message' => [['razao' => 'OK', 'propriedade' => 'OK']]];

        }else{

            $result_generate_pix = $response_generate_pix->json();
            return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => $result_generate_pix['violacoes']];
        }

    }



    public static function verifyStatusPixMP($access_token, $transaction_id){

    \MercadoPago\SDK::setAccessToken($access_token);

    try{
        $payment = \MercadoPago\Payment::find_by_id($transaction_id);

        return $payment->point_of_interaction->transaction_data;

    } catch(\Exception $e){
        \Log::error($e->getMessage());
    }

    }



    public static function cancelPixPH($user_id,$transaction_id){

        $invoice = Invoice::where('transaction_id', $transaction_id)->first();

        if (!$invoice) {
            \Log::error('cancelPixPH: Invoice não encontrada com transaction_id: ' . $transaction_id);
            return (object)['status' => 'reject', 'message' => 'Fatura não encontrada'];
        }

        $company = \App\Models\Company::find($invoice->company_id);

        if (!$company) {
            \Log::error('cancelPixPH: Company não encontrada para invoice ID: ' . $invoice->id);
            return (object)['status' => 'reject', 'message' => 'Empresa não encontrada'];
        }

        if (!$company->token_paghiper || !$company->key_paghiper) {
            \Log::error('cancelPixPH: Credenciais Pag Hiper não configuradas para company ID: ' . $company->id);
            return (object)['status' => 'reject', 'message' => 'Credenciais Pag Hiper não configuradas'];
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://pix.paghiper.com/invoice/cancel/',[
            'token'             => $company->token_paghiper,
            'apiKey'            => $company->key_paghiper,
            'status'            => 'canceled',
            'transaction_id'    => $transaction_id
        ]);

        $response = $response->getBody();
        return json_decode($response)->cancellation_request;

    }


    public static function cancelBilletPH($user_id,$transaction_id){

        $invoice = Invoice::where('transaction_id', $transaction_id)->first();

        if (!$invoice) {
            \Log::error('cancelBilletPH: Invoice não encontrada com transaction_id: ' . $transaction_id);
            return (object)['status' => 'reject', 'message' => 'Fatura não encontrada'];
        }

        $company = \App\Models\Company::find($invoice->company_id);

        if (!$company) {
            \Log::error('cancelBilletPH: Company não encontrada para invoice ID: ' . $invoice->id);
            return (object)['status' => 'reject', 'message' => 'Empresa não encontrada'];
        }

        if (!$company->token_paghiper || !$company->key_paghiper) {
            \Log::error('cancelBilletPH: Credenciais Pag Hiper não configuradas para company ID: ' . $company->id);
            return (object)['status' => 'reject', 'message' => 'Credenciais Pag Hiper não configuradas'];
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.paghiper.com/transaction/cancel/',[
            'token'             => $company->token_paghiper,
            'apiKey'            => $company->key_paghiper,
            'status'            => 'canceled',
            'transaction_id'    => $transaction_id
        ]);

        $response = $response->getBody();
        return json_decode($response)->cancellation_request;

    }


    public static function cancelPixMP($access_token, $transaction_id){

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $access_token,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->put('https://api.mercadopago.com/v1/payments/'.$transaction_id,[
            'status'            => 'cancelled'
        ]);

        $result = $response->getBody();
        return json_decode($result)->status;

        }


        public static function generateBilletIntermedium($invoice_id){
            return self::generateInterCobrancaV3($invoice_id);
        }

        public static function generateBilletPixIntermedium($invoice_id){
            return self::generateInterCobrancaV3($invoice_id);
        }



        /**
         * Agenda geração Inter em background (fila + cron como fallback via status Gerando).
         */
        public static function queueInterGeneration(int $invoiceId, bool $sendEmail = false, bool $sendWhatsapp = false): void
        {
            $invoice = self::find($invoiceId);
            if (!$invoice) {
                return;
            }

            self::where('id', $invoiceId)->update([
                'status' => 'Gerando',
                'msg_erro' => null,
            ]);

            $queue = $invoice->company_id ? 'company_'.$invoice->company_id : 'default';

            // afterResponse libera a tela imediatamente; em produção a fila company_* processa o job.
            \App\Jobs\GenerateInterInvoiceJob::dispatch($invoiceId, $sendEmail, $sendWhatsapp)
                ->onQueue($queue)
                ->afterResponse();
        }

        public static function isInterBackgroundMethod(?string $gateway, ?string $paymentMethod): bool
        {
            return in_array($gateway, ['Inter', 'Intermedium'], true)
                && in_array($paymentMethod, ['Boleto', 'BoletoPix', 'Pix'], true);
        }

        public static function cancelBilletIntermedium($user_id, $transaction_id)
        {
            $invoice = self::where('transaction_id', $transaction_id)->first();
            if (!$invoice) {
                return ['status' => 'error', 'message' => 'Fatura não encontrada!'];
            }

            $company = Company::find($invoice->company_id);
            if (!$company) {
                return ['status' => 'error', 'message' => 'Empresa não encontrada!'];
            }

            $credentialError = self::validateInterCredentials($company, 'Erro ao cancelar cobrança');
            if ($credentialError !== null) {
                return ['status' => 'error', 'message' => self::formatInterErrorMessages($credentialError['message'])];
            }

            $tokenResult = self::ensureInterAccessToken($company);
            if (!$tokenResult['success']) {
                return ['status' => 'error', 'message' => $tokenResult['message']];
            }

            $access_token = $tokenResult['token'];
            $options = self::interCertOptions($company);
            $headers = ['Authorization' => 'Bearer '.$access_token];
            $base = rtrim($company->inter_host, '/');
            $detailUrl = $base.'/cobranca/v3/cobrancas/'.$transaction_id;
            $cancelUrl = $detailUrl.'/cancelar';

            $detail = Http::withOptions($options)->withHeaders($headers)->get($detailUrl);
            if ($detail->status() === 404) {
                return ['status' => 'success', 'message' => 'Cobrança não encontrada no Inter; cancelamento local permitido.'];
            }

            if ($detail->successful()) {
                $situacao = json_decode($detail->body())->cobranca->situacao ?? null;
                if (in_array($situacao, ['CANCELADO', 'CANCELADA', 'EXPIRADO'], true)) {
                    return ['status' => 'success', 'message' => 'Cobrança já cancelada/expirada no Inter.'];
                }

                // Inter não cancela enquanto EM_PROCESSAMENTO — aguarda ficar pronta.
                if ($situacao === 'EM_PROCESSAMENTO') {
                    for ($attempt = 1; $attempt <= 10; $attempt++) {
                        sleep(3);
                        $detail = Http::withOptions($options)->withHeaders($headers)->get($detailUrl);
                        if (!$detail->successful()) {
                            break;
                        }
                        $situacao = json_decode($detail->body())->cobranca->situacao ?? null;
                        if ($situacao !== 'EM_PROCESSAMENTO') {
                            \Log::info("Cancelamento Inter: cobrança saiu de EM_PROCESSAMENTO após {$attempt} tentativa(s). Situação: {$situacao}");
                            break;
                        }
                    }

                    if (in_array($situacao, ['CANCELADO', 'CANCELADA', 'EXPIRADO'], true)) {
                        return ['status' => 'success', 'message' => 'Cobrança já cancelada/expirada no Inter.'];
                    }

                    if ($situacao === 'EM_PROCESSAMENTO') {
                        return [
                            'status' => 'error',
                            'message' => 'A cobrança ainda está em processamento no Banco Inter e não pode ser cancelada agora. Aguarde alguns minutos e tente novamente.',
                        ];
                    }
                }
            }

            $response_cancel = Http::withOptions($options)->withHeaders($headers)->post($cancelUrl, [
                'motivoCancelamento' => 'ACERTOS',
            ]);

            if ($response_cancel->successful()) {
                return ['status' => 'success', 'message' => 'Cobrança cancelada no Inter.'];
            }

            $body = $response_cancel->json();
            $detailMsg = $body['detail'] ?? $response_cancel->body();
            \Log::info('Erro ao cancelar pagamento intermedium: '.$response_cancel->body());

            // Se o Inter diz que já está cancelada, trata como sucesso.
            if (is_string($detailMsg) && (str_contains(mb_strtolower($detailMsg), 'cancelad') || str_contains(mb_strtolower($detailMsg), 'já se encontra'))) {
                return ['status' => 'success', 'message' => $detailMsg];
            }

            return [
                'status' => 'error',
                'message' => is_string($detailMsg) ? $detailMsg : 'Não foi possível cancelar a cobrança no Banco Inter.',
            ];
        }

        public static function cancelBilletPixIntermedium($user_id, $transaction_id)
        {
            return self::cancelBilletIntermedium($user_id, $transaction_id);
        }


        public static function cancelPixIntermedium($user_id, $transaction_id){

            $invoice = Invoice::where('transaction_id', $transaction_id)->first();

            if(!$invoice){
                return response()->json('Fatura não encontrada!', 404);
            }

            $company = Company::find($invoice->company_id);

            if(!$company){
                return response()->json('Empresa não encontrada!', 404);
            }

            $access_token = $company->access_token_inter;

            if($company->inter_host == '' || $company->inter_host == null){
                return response()->json('HOST banco inter não cadastrado!', 422);
            }
            if($company->inter_client_id == '' || $company->inter_client_id == null){
                return response()->json('CLIENT ID banco inter não cadastrado!', 422);
            }
            if($company->inter_client_secret == '' || $company->inter_client_secret == null){
                return response()->json('CLIENT SECRET banco inter não cadastrado!', 422);
            }
            if($company->inter_crt_file == '' || $company->inter_crt_file == null){
                return response()->json('Certificado CRT banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$company->inter_crt_file))){
                return response()->json('Certificado CRT banco inter não existe!', 422);
            }
            if($company->inter_key_file == '' || $company->inter_key_file == null){
                return response()->json('Certificado KEY banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$company->inter_key_file))){
                return response()->json('Certificado KEY banco inter não existe!', 422);
            }

            $check_access_token = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$company->inter_crt_file),
                'ssl_key' => storage_path('/app/'.$company->inter_key_file)
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

            if ($check_access_token->unauthorized()) {
                $response = Http::withOptions([
                    'cert' => storage_path('/app/'.$company->inter_crt_file),
                    'ssl_key' => storage_path('/app/'.$company->inter_key_file),
                ])->asForm()->post($company->inter_host.'oauth/v2/token', [
                    'client_id' => $company->inter_client_id,
                    'client_secret' => $company->inter_client_secret,
                    'scope' => $company->inter_scope,
                    'grant_type' => 'client_credentials',
                ]);

                if ($response->successful()) {
                    $responseBody = $response->body();
                    $access_token = json_decode($responseBody)->access_token;
                    Company::where('id',$company->id)->update([
                        'access_token_inter' => $access_token
                    ]);

                    $company = Company::find($company->id);
                }else{
                    return response()->json('Verifique suas credenciais, erro ao autenticar!', 422);
                }
            }

            $response_cancel_billet = Http::withOptions([
                'cert' => storage_path('/app/'.$company->inter_crt_file),
                'ssl_key' => storage_path('/app/'.$company->inter_key_file),
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $access_token

            ])->patch($company->inter_host.'pix/v2/cobv/'.$transaction_id,[
                "status" => "REMOVIDA_PELO_USUARIO_RECEBEDOR"
            ]);

            if ($response_cancel_billet->successful()) {
                return 'success';
            }else{
                return ['status' => 'reject', 'title' => 'Erro ao cancelar Pix', 'message' => $response_cancel_billet->json()];
            }

            }




            /**
             * Extrai a linha digitável (47 dígitos) da resposta do Asaas.
             * A API retorna identificationField (linha digitável) e barCode (44 dígitos) — não confundir.
             */
            public static function extractAsaasIdentificationField(array $payload): ?string
            {
                $line = $payload['identificationField']
                    ?? $payload['identification_field']
                    ?? null;

                if (empty($line)) {
                    return null;
                }

                return preg_replace('/\D/', '', (string) $line) ?: null;
            }

            /**
             * Reconsulta a linha digitável no Asaas para faturas já criadas no gateway.
             */
            public static function refreshAsaasBilletDigitable(int $invoiceId): array
            {
                $invoice = Invoice::with('company')->find($invoiceId);

                if (! $invoice || $invoice->gateway_payment !== 'Asaas' || empty($invoice->transaction_id)) {
                    return ['success' => false, 'message' => 'Fatura Asaas inválida ou sem transaction_id'];
                }

                $company = $invoice->company;

                if ($company->environment_asaas == 'Teste') {
                    $url = $company->asaas_url_test;
                    $token = $company->at_asaas_test;
                } else {
                    $url = $company->asaas_url_prod;
                    $token = $company->at_asaas_prod;
                }

                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'access_token' => $token,
                    'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)',
                ])->get($url . 'v3/payments/' . $invoice->transaction_id . '/identificationField');

                if (! $response->successful()) {
                    $error = $response->json();

                    return [
                        'success' => false,
                        'message' => $error['errors'][0]['description'] ?? 'Erro ao consultar linha digitável no Asaas',
                    ];
                }

                $digitable = self::extractAsaasIdentificationField($response->json());

                if (empty($digitable)) {
                    return ['success' => false, 'message' => 'Linha digitável não retornada pelo Asaas'];
                }

                $invoice->update(['billet_digitable' => $digitable]);

                return ['success' => true, 'message' => 'Linha digitável atualizada', 'digitable' => $digitable];
            }

            /**
             * Corrige faturas Asaas que gravaram barCode (44 dígitos) em vez da linha digitável (47).
             */
            public static function fixAsaasBarcodeStoredAsDigitable(int $invoiceId): array
            {
                $invoice = Invoice::find($invoiceId);

                if (! $invoice || $invoice->gateway_payment !== 'Asaas') {
                    return ['success' => false, 'message' => 'Fatura Asaas inválida'];
                }

                $stored = preg_replace('/\D/', '', (string) $invoice->billet_digitable);

                if (strlen($stored) === 47 || strlen($stored) === 48) {
                    return ['success' => true, 'message' => 'Linha digitável já está no formato correto', 'digitable' => $stored];
                }

                if (strlen($stored) !== 44) {
                    return ['success' => false, 'message' => 'Formato de boleto não reconhecido para correção local'];
                }

                $digitable = \App\Helpers\BoletoHelper::barcodeToDigitableLine($stored);

                if (empty($digitable) || strlen($digitable) !== 47) {
                    return ['success' => false, 'message' => 'Não foi possível converter o código de barras para linha digitável'];
                }

                if (\App\Helpers\BoletoHelper::digitableToBarcode($digitable) !== $stored) {
                    return ['success' => false, 'message' => 'Conversão local do boleto falhou na validação'];
                }

                $invoice->update(['billet_digitable' => $digitable]);

                return ['success' => true, 'message' => 'Linha digitável corrigida localmente', 'digitable' => $digitable];
            }

            public static function generateBilletAsaas($invoice_id){

                $invoice = Invoice::with(['customerService.customer', 'company'])->find($invoice_id);

                $customer = $invoice->customerService->customer;
                $company = $invoice->company;
                $days_due_date = Carbon::parse($invoice->date_due)->diffInDays($invoice->date_invoice);

                if($days_due_date == 0){
                    if(date('l') == 'Saturday' || date('l') == 'Sábado'){
                        $days_due_date = 2;
                    }
                    if(date('l') == 'Sunday' || date('l') == 'Domingo'){
                        $days_due_date = 1;
                    }
                }

                if($company->environment_asaas == 'Teste'){
                    $url = $company->asaas_url_test;
                    $at_asaas = $company->at_asaas_test;
                }else{
                    $url = $company->asaas_url_prod;
                    $at_asaas = $company->at_asaas_prod;
                }


                //Verifica se o cliente já está cadastrado e retona o ID
                $get_customer = Http::withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'access_token' => $at_asaas,
                    'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
                  ])->get($url.'v3/customers?cpfCnpj='.removeEspeciais($customer->document));

                  if ($get_customer->successful()) {
                    $result_get_customer = $get_customer->json();
                    if($result_get_customer['totalCount'] > 0){
                        $customer_id = $result_get_customer['data'][0]['id'];
                    }else{
                        $post_customer = Http::withHeaders([
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                            'access_token' => $at_asaas,
                            'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
                          ])->post($url.'v3/customers',[
                            'name'                  =>  $customer->name,
                            'cpfCnpj'               =>  $customer->document,
                            'notificationDisabled'  =>  true,
                            'postalCode'            =>  $customer->cep,
                            'addressNumber'         =>  $customer->number,
                          ]);

                          if ($post_customer->successful()) {
                            $result_post_customer = $post_customer->json();
                            $customer_id = $result_post_customer['id'];
                        }

                    }

                }


                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'access_token' => $at_asaas,
                    'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
                  ])->post($url.'v3/payments',[
                    'customer'          =>  $customer_id,
                    'billingType'       =>  'BOLETO',
                    'value'             =>  $invoice->price,
                    'dueDate'           => $invoice->date_due,
                    'description'       => $invoice->description,
                    //'daysAfterDueDateToRegistrationCancellation'    => 40,
                    'externalReference' =>  $invoice->id,
                    'fine'              =>  [
                        'value'         =>  2,
                        'type'          =>  'PERCENTAGE'
                    ],
                    'interest'          =>  [
                        'value'         =>  1
                    ]
                  ]);


                  if ($response->successful()) {

                    $result = $response->json();

                } else{
                    $result_error = $response->json();
                    return ['status' => 'reject', 'message' => $result_error['errors'][0]['description']];
                }

                  //Pegar o codigo de barras
                  $get_digitable = Http::withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'access_token' => $at_asaas,
                    'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
                  ])->get($url.'v3/payments/'.$result['id'].'/identificationField');

                  if ($get_digitable->successful()) {
                    $result_get_digitable = $get_digitable->json();
                    $billet_digitable = self::extractAsaasIdentificationField($result_get_digitable);

                    if (empty($billet_digitable)) {
                        return ['status' => 'reject', 'message' => 'Linha digitável não retornada pelo Asaas.'];
                    }

                  } else{
                    $result_error_digitable = $get_digitable->json();
                    return ['status' => 'reject', 'message' => $result_error_digitable['errors'][0]['description']];
                }

                Invoice::where('id',$invoice_id)->update([
                    'status'            =>  'Pendente',
                    'msg_erro'          =>  null,
                    'transaction_id'    =>  $result['id'],
                    'billet_digitable'  =>  $billet_digitable
                ]);

                return ['status' => 'success', 'message' => 'ok'];


              }


        public static function generatePixAsaas($invoice_id){

            $invoice = Invoice::with(['customerService.customer', 'company'])->find($invoice_id);

            $customer = $invoice->customerService->customer;
            $company = $invoice->company;

            if($company->environment_asaas == 'Teste'){
                $url = $company->asaas_url_test;
                $at_asaas = $company->at_asaas_test;
            }else{
                $url = $company->asaas_url_prod;
                $at_asaas = $company->at_asaas_prod;
            }

            $headers = [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'access_token' => $at_asaas,
                'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
            ];

            $customer_id = null;

            $get_customer = Http::withHeaders($headers)->get($url.'v3/customers?cpfCnpj='.removeEspeciais($customer->document));

            if ($get_customer->successful()) {
                $result_get_customer = $get_customer->json();
                if($result_get_customer['totalCount'] > 0){
                    $customer_id = $result_get_customer['data'][0]['id'];
                }else{
                    $post_customer = Http::withHeaders($headers)->post($url.'v3/customers',[
                        'name'                  =>  $customer->name,
                        'cpfCnpj'               =>  $customer->document,
                        'notificationDisabled'  =>  true,
                        'postalCode'            =>  $customer->cep,
                        'addressNumber'         =>  $customer->number,
                    ]);

                    if ($post_customer->successful()) {
                        $result_post_customer = $post_customer->json();
                        $customer_id = $result_post_customer['id'];
                    } else{
                        $result_error_customer = $post_customer->json();
                        return ['status' => 'reject', 'message' => $result_error_customer['errors'][0]['description']];
                    }
                }
            } else{
                $result_error_customer = $get_customer->json();
                return ['status' => 'reject', 'message' => $result_error_customer['errors'][0]['description']];
            }

            $response = Http::withHeaders($headers)->post($url.'v3/payments',[
                'customer'          =>  $customer_id,
                'billingType'       =>  'PIX',
                'value'             =>  $invoice->price,
                'dueDate'           => $invoice->date_due,
                'description'       => $invoice->description,
                'externalReference' =>  $invoice->id,
                'fine'              =>  [
                    'value'         =>  2,
                    'type'          =>  'PERCENTAGE'
                ],
                'interest'          =>  [
                    'value'         =>  1
                ]
            ]);

            if(!$response->successful()){
                $result_error = $response->json();
                return ['status' => 'reject', 'message' => $result_error['errors'][0]['description']];
            }

            $result = $response->json();

            $pix_qrcode = Http::withHeaders($headers)->get($url.'v3/payments/'.$result['id'].'/pixQrCode');

            if(!$pix_qrcode->successful()){
                $result_error_qrcode = $pix_qrcode->json();
                return ['status' => 'reject', 'message' => $result_error_qrcode['errors'][0]['description']];
            }

            $pix_data = $pix_qrcode->json();

            if(empty($pix_data['payload'])){
                return ['status' => 'reject', 'message' => 'Dados do PIX não retornados pelo Asaas.'];
            }

            Invoice::where('id',$invoice_id)->update([
                'status'            =>  'Pendente',
                'msg_erro'          =>  null,
                'transaction_id'    =>  $result['id'],
                'pix_digitable'     =>  $pix_data['payload'],
            ]);

            return ['status' => 'success', 'message' => 'ok'];

        }




//Cancelar Boleto Asaas


public static function cancelBilletAsaas($transaction_id){

    $invoice = Invoice::with('company')->where('transaction_id',$transaction_id)->first();

    if($invoice->company->environment_asaas == 'Teste'){
        $url        = $invoice->company->asaas_url_test;
        $at_asaas   = $invoice->company->at_asaas_test;
    }else{
        $url        = $invoice->company->asaas_url_prod;
        $at_asaas   = $invoice->company->at_asaas_prod;
    }

    $response = Http::withHeaders([
        'accept' => 'application/json',
        'content-type' => 'application/json',
        'access_token' => $at_asaas,
        'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
      ])->delete($url.'v3/payments/'.$transaction_id);


      if ($response->successful()) {
            return 'success';
    } else{
        $result_error = $response->json();
        Log::info('Model::cancelBilletAsaas');
        Log::info($result_error);
        return ['status' => 'reject', 'message' => $result_error['errors'][0]['description']];
    }




  }


//Cancelar PIX Asaas

public static function cancelPixAsaas($user_id, $transaction_id){

    $invoice = Invoice::with('company')->where('transaction_id',$transaction_id)->first();

    if($invoice->company->environment_asaas == 'Teste'){
        $url        = $invoice->company->asaas_url_test;
        $at_asaas   = $invoice->company->at_asaas_test;
    }else{
        $url        = $invoice->company->asaas_url_prod;
        $at_asaas   = $invoice->company->at_asaas_prod;
    }

    $response = Http::withHeaders([
        'accept' => 'application/json',
        'content-type' => 'application/json',
        'access_token' => $at_asaas,
        'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
      ])->delete($url.'v3/payments/'.$transaction_id);


      if ($response->successful()) {
            return 'success';
    } else{
        $result_error = $response->json();
        Log::info('Model::cancelPixAsaas');
        Log::info($result_error);
        return ['status' => 'reject', 'message' => $result_error['errors'][0]['description']];
    }




  }






}
