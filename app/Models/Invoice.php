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
    ];

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
    private static function interApiGet(Company $company, string $url, array $httpOptions, bool $retryOnUnauthorized = true)
    {
        $access_token = $company->access_token_inter;

        if (empty($access_token)) {
            $refresh = self::refreshInterAccessToken($company, $httpOptions);
            if (!$refresh['success']) {
                return null;
            }
            $access_token = $refresh['token'];
        }

        $response = Http::withOptions($httpOptions)->withHeaders([
            'Authorization' => 'Bearer '.$access_token,
        ])->get($url);

        if ($retryOnUnauthorized && $response->status() === 401) {
            $refresh = self::refreshInterAccessToken($company, $httpOptions);
            if (!$refresh['success']) {
                return $response;
            }

            return Http::withOptions($httpOptions)->withHeaders([
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

        $cobranca = $result->cobranca ?? $result;

        if (!empty($cobranca->boleto->linhaDigitavel ?? null)) {
            return true;
        }

        if (!empty($cobranca->pix->pixCopiaECola ?? null)) {
            return true;
        }

        return false;
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
        if (!Storage::disk('public')->exists('pix')) {
            Storage::disk('public')->makeDirectory('pix');
        }

        $filePath = storage_path('app/public/pix/'.$invoice->user_id.'_'.$invoice->id.'.png');
        QrCode::format('png')->size(220)->generate($pixCopiaECola, $filePath);
        $image_pix = env('APP_URL').Storage::url('pix/'.$invoice->user_id.'_'.$invoice->id.'.png');

        return [
            'image_url_pix' => $image_pix,
            'qrcode_pix_base64' => base64_encode(file_get_contents($filePath)),
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

        if (!empty($pdfBase64)) {
            Storage::disk('public')->put($storageFolder.'/'.$invoice->user_id.'_'.$invoice->id.'.pdf', base64_decode($pdfBase64));
            $update['billet_url'] = env('APP_URL').Storage::url($storageFolder.'/'.$invoice->user_id.'_'.$invoice->id.'.pdf');
            $update['billet_base64'] = $pdfBase64;
        }

        if ($includePix) {
            $pixCopiaECola = self::extractInterPixCopiaECola($result);
            if (!empty($pixCopiaECola)) {
                $pixQr = self::generateInterPixQrCode($invoice, $pixCopiaECola);
                $update['pix_digitable'] = $pixCopiaECola;
                $update['image_url_pix'] = $pixQr['image_url_pix'];
                $update['qrcode_pix_base64'] = $pixQr['qrcode_pix_base64'];
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

        $response_generate = Http::withOptions($options)->withHeaders([
            'Authorization' => 'Bearer '.$access_token,
        ])->post(rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas', [
            'seuNumero' => (string) $invoice->id,
            'valorNominal' => $invoice->price,
            'dataVencimento' => $dataVencimentoFormatada,
            'numDiasAgenda' => 60,
            'pagador' => [
                'cpfCnpj' => $customer->document,
                'nome' => $customer->name,
                'email' => $customer->email,
                'telefone' => substr($customer->whatsapp, 2),
                'cep' => removeEspeciais($customer->cep),
                'numero' => $customer->number,
                'complemento' => $customer->complement,
                'bairro' => $customer->district,
                'cidade' => $customer->city,
                'uf' => $customer->state,
                'endereco' => $customer->address,
                'ddd' => substr($customer->whatsapp, 0, 2),
                'tipoPessoa' => $customer->type == 'Física' ? 'FISICA' : 'JURIDICA',
            ],
            'multa' => [
                'codigo' => 'PERCENTUAL',
                'taxa' => 1,
            ],
        ]);

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
        $result_get = self::fetchInterCobrancaDetails($company, $access_token, $codigoSolicitacao, $logContext);

        if ($result_get === null) {
            return ['status' => 'reject', 'title' => $title, 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Erro ao obter detalhes da cobrança intermedium!']]];
        }

        if (self::interCobrancaIsProcessing($result_get) && !self::interCobrancaHasPaymentData($result_get)) {
            self::where('id', $invoice_id)->update([
                'status' => 'Processamento',
                'msg_erro' => null,
                'transaction_id' => $codigoSolicitacao,
            ]);

            \Log::warning('Cobrança Inter ainda em processamento'.$logContext.' para invoice ID: '.$invoice_id.'. Será concluída pelo cron.');

            return [
                'status' => 'success',
                'title' => 'OK',
                'message' => [['razao' => 'OK', 'propriedade' => 'Cobrança em processamento no Inter']],
                'invoice_status' => 'Processamento',
                'invoice_updated' => true,
            ];
        }

        $response_pdf = self::fetchInterCobrancaPdf($company, $access_token, $codigoSolicitacao, $logContext);
        $pdfBase64 = null;

        if ($response_pdf && $response_pdf->successful()) {
            $pdfBase64 = json_decode($response_pdf->getBody())->pdf ?? null;
        } elseif ($response_pdf) {
            \Log::warning('PDF indisponível'.$logContext.' para invoice ID: '.$invoice_id.'. Salvando cobrança sem PDF.');
        }

        $invoiceStatus = !empty($pdfBase64) || self::interCobrancaHasPaymentData($result_get) ? 'Pendente' : 'Processamento';
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

        $result_get = self::fetchInterCobrancaDetails($company, $access_token, $codigoSolicitacao, $logContext, 10, 3);
        if ($result_get === null) {
            return ['success' => false, 'completed' => false, 'message' => 'Erro ao consultar cobrança no Inter.'];
        }

        if (self::interCobrancaIsProcessing($result_get) && !self::interCobrancaHasPaymentData($result_get)) {
            return ['success' => true, 'completed' => false, 'message' => 'Cobrança ainda em processamento no Inter.'];
        }

        $pdfBase64 = null;
        if (empty($invoice->billet_url)) {
            $response_pdf = self::fetchInterCobrancaPdf($company, $access_token, $codigoSolicitacao, $logContext, 10, 3);
            if ($response_pdf && $response_pdf->successful()) {
                $pdfBase64 = json_decode($response_pdf->getBody())->pdf ?? null;
            }
        }

        $invoiceStatus = !empty($pdfBase64) || self::interCobrancaHasPaymentData($result_get) ? 'Pendente' : 'Processamento';
        self::persistInterCobrancaInvoice($invoice_id, $invoice, $codigoSolicitacao, $result_get, $pdfBase64, $storageFolder, $includePix, $invoiceStatus);

        return [
            'success' => true,
            'completed' => $invoiceStatus === 'Pendente',
            'message' => $invoiceStatus === 'Pendente' ? 'Cobrança concluída.' : 'Cobrança parcialmente concluída, aguardando PDF.',
        ];
    }

    /**
     * Consulta o status de pagamento no Banco Inter e atualiza a fatura se necessário.
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

        $certData = openssl_x509_parse(file_get_contents($certPath));
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

        if (in_array($invoice->payment_method, ['Boleto', 'BoletoPix'], true)) {
            $isUuid = str_contains($transaction_id_clean, '-');

            if (!$isUuid && is_numeric($transaction_id_clean) && $invoice->payment_method === 'Boleto') {
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

                if ($gatewayStatus === 'EM_PROCESSAMENTO' && in_array($invoice->status, ['Processamento', 'Gerando'], true)) {
                    self::completeInterCobrancaProcessing($invoice->id);
                }
            }
        } elseif ($invoice->payment_method === 'Pix') {
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
                'message' => 'Status atualizado para Pago.',
            ];
        }

        return [
            'success' => true,
            'updated' => false,
            'gateway_status' => $gatewayStatus,
            'message' => 'Status consultado. Situação no gateway: '.($gatewayStatus ?? 'desconhecida').'.',
        ];
    }

    public static function generateBilletPH($invoice_id){

        if (!Storage::disk('public')->exists('boletos')) {
            Storage::disk('public')->makeDirectory('boletos');
        }


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

            $contents = Http::get($result->bank_slip->url_slip_pdf)->body();
            Storage::disk('public')->put('boletos/' .  $invoice->user_id.'_'.$invoice->id.'.pdf', $contents);
            $billet_url = env('APP_URL').Storage::url('boletos/' . $invoice->user_id . '_' . $invoice->id . '.pdf');
            //\File::put(public_path(). '/boleto/' .  $invoice->user_id.'_'.$invoice->id.'.'.'pdf', $contents);
            //$billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoice->user_id.'_'.$invoice->id.'.pdf';
            //$base64_pdf = chunk_split(base64_encode(file_get_contents($billet_pdf)));

            Invoice::where('id',$invoice_id)->update([
                'status'            =>  'Pendente',
                'msg_erro'          =>  null,
                'transaction_id'    =>  $result->transaction_id,
                'billet_url'        =>  $billet_url,
                //'billet_base64'     =>  $base64_pdf,
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

        if (!Storage::disk('public')->exists('pix')) {
            Storage::disk('public')->makeDirectory('pix');
        }

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
                    'image_url_pix'     => 'https://cobrancasegura.com.br/storage/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                    'pix_digitable'     => $result->pix_code->emv,
                    'qrcode_pix_base64' => $result->pix_code->qrcode_base64,
                ]);



                //\File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.png', base64_decode($result->pix_code->qrcode_base64));
                $contents = Http::get($result->pix_code->qrcode_image_url)->body();
                Storage::disk('public')->put('pix/' .  $invoice->user_id.'_'.$invoice->id.'.png', $contents);

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

        if (!Storage::disk('public')->exists('pix')) {
            Storage::disk('public')->makeDirectory('pix');
        }

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
                    'image_url_pix'     => 'https://cobrancasegura.com.br/storage/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                    'pix_digitable'     => $payment->point_of_interaction->transaction_data->qr_code,
                    'qrcode_pix_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                ]);

                //$invoice = Invoice::where('id',$invoice_id)->first();

                //\File::put(public_path(). '/pix/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'png', base64_decode($payment->point_of_interaction->transaction_data->qr_code_base64));

                Storage::disk('public')->put('pix/' .  $invoice->user_id.'_'.$invoice->id.'.png', base64_decode($payment->point_of_interaction->transaction_data->qr_code_base64));
                return ['status' => 'success', 'message' => 'OK'];

            } catch(\Exception $e){
                \Log::error($e->getMessage());
            }

        }

      }



      public static function generatePixIntermedium($invoice_id){

        if (!Storage::disk('public')->exists('pix')) {
            Storage::disk('public')->makeDirectory('pix');
        }

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


            QrCode::format('png')->size(220)->generate($result_generate_pix->pixCopiaECola, storage_path('app/public'). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.png');
            $image_pix = env('APP_URL').Storage::url('pix/'.$invoice->user_id.'_'.$invoice->id.'.png');

            //QrCode::format('png')->size(220)->generate($result_generate_pix->pixCopiaECola, storage_path('public'). '/pix/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'png');
            //$image_pix   = config()->get('app.url').'/pix/'.$invoice['user_id'].'_'.$invoice['id'].'.png';

            Invoice::where('id',$invoice_id)->update([
                'status'            => 'Pendente',
                'msg_erro'          =>  null,
                'transaction_id'    => $result_generate_pix->txid,
                'image_url_pix'     => $image_pix,
                'pix_digitable'     => $result_generate_pix->pixCopiaECola,
                'qrcode_pix_base64' => base64_encode(file_get_contents($image_pix)),
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



        public static function cancelBilletIntermedium($user_id, $transaction_id){

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

            ])->post($company->inter_host.'cobranca/v3/cobrancas/'.$transaction_id.'/cancelar',[
                "motivoCancelamento" => "ACERTOS"
            ]);

            if ($response_cancel_billet->successful()) {
                return 'success';
            }

            \Log::info('Erro ao cancelar pagamento intermedium: '.$response_cancel_billet->body());

            return 'error';

        }


        public static function cancelBilletPixIntermedium($user_id, $transaction_id){
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




            public static function generateBilletAsaas($invoice_id){

                if (!Storage::disk('public')->exists('boletos')) {
                    Storage::disk('public')->makeDirectory('boletos');
                }

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

                    $contents = Http::get($result['bankSlipUrl'])->body();
                    Storage::disk('public')->put('boletos/' .  $invoice->user_id.'_'.$invoice->id.'.'.'pdf', $contents);
                    $billet_url = env('APP_URL').Storage::url('boletos/' . $invoice->user_id . '_' . $invoice->id . '.pdf');

                    //$billet_pdf = Storage::disk('public')->get('boletos/' . $invoice->user_id . '_' . $invoice->id . '.pdf');
                    //$base64_pdf = base64_encode($billet_pdf);

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
                    $billet_digitable = $result_get_digitable['barCode'];

                  } else{
                    $result_error_digitable = $get_digitable->json();
                    return ['status' => 'reject', 'message' => $result_error_digitable['errors'][0]['description']];
                }

                Invoice::where('id',$invoice_id)->update([
                    'status'            =>  'Pendente',
                    'msg_erro'          =>  null,
                    'transaction_id'    =>  $result['id'],
                    'billet_url'        =>  $billet_url,
                    //'billet_base64'     =>  $base64_pdf,
                    'billet_digitable'  =>  $billet_digitable
                ]);

                return ['status' => 'success', 'message' => 'ok'];


              }


        public static function generatePixAsaas($invoice_id){

            if (!Storage::disk('public')->exists('pix')) {
                Storage::disk('public')->makeDirectory('pix');
            }

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

            if(empty($pix_data['payload']) || empty($pix_data['encodedImage'])){
                return ['status' => 'reject', 'message' => 'Dados do PIX não retornados pelo Asaas.'];
            }

            $encodedImage = $pix_data['encodedImage'];
            $imageParts = explode(',', $encodedImage);
            $imageBase64 = count($imageParts) > 1 ? $imageParts[1] : $imageParts[0];
            $fileName = $invoice->user_id.'_'.$invoice->id.'.png';

            Storage::disk('public')->put('pix/' .  $fileName, base64_decode($imageBase64));
            $image_pix = env('APP_URL').Storage::url('pix/' . $fileName);

            Invoice::where('id',$invoice_id)->update([
                'status'            =>  'Pendente',
                'msg_erro'          =>  null,
                'transaction_id'    =>  $result['id'],
                'image_url_pix'     =>  $image_pix,
                'pix_digitable'     =>  $pix_data['payload'],
                'qrcode_pix_base64' =>  $imageBase64
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
