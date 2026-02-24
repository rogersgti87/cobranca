<?php

namespace App\Console\Commands;

use App\Jobs\sendInvoice;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Models\InvoiceNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class StatusInterCron extends Command
{

  protected $signature = 'statusinter:cron';

  protected $description = 'Consultar Status inter';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {
    // Configuração SSL
    $sslVerify = env('INTER_SSL_VERIFY', true);
    $caBundle = env('INTER_CA_BUNDLE', '');
    
    // Se há um bundle CA específico, usa ele; senão usa a configuração booleana
    $verifyOption = !empty($caBundle) ? $caBundle : $sslVerify;
    
    // Limitar a quantidade de faturas processadas por execução para evitar rate limit
    $limite_por_execucao = 50;
    
    // CONFIGURAÇÃO: Defina quais métodos de pagamento devem ser processados
    // Para desabilitar a consulta de PIX, remova 'Pix' do array abaixo
    $metodosAtivos = ['Boleto', 'BoletoPix']; // 'Pix' foi removido - adicione de volta se necessário
    
    $invoices = Invoice::where('status', 'Pendente')
        ->whereIn('gateway_payment', ['Inter', 'Intermedium'])
        ->whereIn('payment_method', $metodosAtivos) // Filtra apenas métodos ativos
        ->whereNotNull('company_id')
        ->whereNotNull('transaction_id')
        ->with('company')
        ->orderBy('date_due', 'asc') // Priorizar faturas com vencimento mais próximo
        ->limit($limite_por_execucao)
        ->get();

    $this->info('Processando '.$invoices->count().' faturas (Métodos: '.implode(', ', $metodosAtivos).')...');
    
    $contador = 0;
    foreach ($invoices as $invoice) {
        $contador++;
        $company = $invoice->company;
        if (!$company) {
            \Log::warning('StatusInterCron - Invoice ID: '.$invoice['id'].' sem company associada.');
            continue;
        }

        $access_token = $company->access_token_inter;
        if ($access_token == null) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - Access token inválido, Invoice ID: '.$invoice['id']);
            continue;
        }
        if ($company->inter_host == '' || $company->inter_host === null) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - HOST banco inter não cadastrado.');
            continue;
        }
        if ($company->inter_client_id == '' || $company->inter_client_id === null) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - CLIENT ID banco inter não cadastrado.');
            continue;
        }
        if ($company->inter_client_secret == '' || $company->inter_client_secret === null) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - CLIENT SECRET banco inter não cadastrado.');
            continue;
        }
        if ($company->inter_crt_file == '' || $company->inter_crt_file === null) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - Certificado CRT banco inter não cadastrado.');
            continue;
        }
        if (!file_exists(storage_path('/app/'.$company->inter_crt_file))) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - Certificado CRT banco inter não existe.');
            continue;
        }
        
        // Verifica se o certificado está válido (não expirado)
        $certData = openssl_x509_parse(file_get_contents(storage_path('/app/'.$company->inter_crt_file)));
        if ($certData && isset($certData['validTo_time_t'])) {
            if ($certData['validTo_time_t'] < time()) {
                \Log::warning('StatusInterCron - Company ID: '.$company->id.' - Certificado CRT banco inter EXPIRADO em '.date('d/m/Y', $certData['validTo_time_t']).' - Pulando faturas desta empresa.');
                continue;
            }
        }
        if ($company->inter_key_file == '' || $company->inter_key_file === null) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - Certificado KEY banco inter não cadastrado.');
            continue;
        }
        if (!file_exists(storage_path('/app/'.$company->inter_key_file))) {
            \Log::warning('StatusInterCron - Company ID: '.$company->id.' - Certificado KEY banco inter não existe.');
            continue;
        }

        if ($invoice['payment_method'] == 'Boleto') {

            // Valida e limpa o transaction_id
            $transaction_id = trim((string) $invoice['transaction_id']);
            if (empty($transaction_id)) {
                continue;
            }

            // Remove caracteres não numéricos/alphanuméricos que podem causar problemas
            $transaction_id_clean = preg_replace('/[^a-zA-Z0-9\-]/', '', $transaction_id);

            // Verifica se é UUID (V3) ou número (V2 antigo)
            // UUID tem hífens, nossoNumero antigo é apenas numérico
            $is_uuid = (strpos($transaction_id_clean, '-') !== false);

            if (!$is_uuid && is_numeric($transaction_id_clean)) {
                // É um nossoNumero antigo da V2 - tenta consultar via API V2
                $nossoNumero = $transaction_id_clean;

                // A API V2 requer dataInicial e dataFinal obrigatórios
                // Usa a data de vencimento do boleto como referência
                $dataVencimento = $invoice['date_due'] ?? Carbon::now()->format('Y-m-d');
                $dataInicial = Carbon::parse($dataVencimento)->subDays(30)->format('Y-m-d'); // 30 dias antes
                $dataFinal = Carbon::parse($dataVencimento)->addDays(30)->format('Y-m-d'); // 30 dias depois

                // Tenta consultar via API V2 usando o endpoint de listagem com filtro
                $url_v2 = rtrim($company->inter_host, '/').'/cobranca/v2/boletos?dataInicial='.$dataInicial.'&dataFinal='.$dataFinal.'&nossoNumero='.$nossoNumero.'&itensPorPagina=100';

                $options = [
                    'cert' => storage_path('/app/'.$company->inter_crt_file),
                    'ssl_key' => storage_path('/app/'.$company->inter_key_file),
                    'curl' => [],
                ];
                
                if ($sslVerify === false) {
                    $options['verify'] = false;
                    $options['curl'][CURLOPT_SSL_VERIFYPEER] = false;
                    $options['curl'][CURLOPT_SSL_VERIFYHOST] = false;
                } elseif (!empty($caBundle)) {
                    $options['verify'] = $caBundle;
                }
                
                $response_v2 = Http::withOptions($options)->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token
                ])->get($url_v2);
                        
                        if ($response_v2->successful()) {
                            $responseBody_v2 = $response_v2->body();
                            $responseData_v2 = json_decode($responseBody_v2, true); // true para retornar array
                            
                            // Procura o boleto na lista retornada
                            $boletos = $responseData_v2['content'] ?? [];
                            $boleto_encontrado = null;
                            
                            // Procura o boleto com o nossoNumero específico
                            foreach($boletos as $boleto){
                                if(isset($boleto['nossoNumero']) && $boleto['nossoNumero'] == $nossoNumero){
                                    $boleto_encontrado = $boleto;
                                    break;
                                }
                            }
                            
                            if($boleto_encontrado){
                                $status_v2 = $boleto_encontrado['situacao'] ?? null;
                                
                                // V2 usa 'PAGO' ao invés de 'RECEBIDO'
                                if($status_v2 == 'PAGO' || $status_v2 == 'RECEBIDO'){
                                    try{
                                        Invoice::where('id', $invoice['id'])->update(['status' => 'Pago', 'date_payment' => Carbon::now()]);
                                        InvoiceNotification::Email($invoice['id']);
                                        InvoiceNotification::Whatsapp($invoice['id']);
                                    } catch(\Exception $e){
                                        \Log::error('Erro ao atualizar status V2 - Invoice ID: '.$invoice['id'].' - '.$e->getMessage());
                                    }
                                }
                            }
                        } else {
                            if($response_v2->status() == 429){
                                \Log::warning('Status Boleto V2 - Rate limit (429) - Invoice ID: '.$invoice['id'].' - Aguardando para continuar...');
                                sleep(5); // Aguarda 5 segundos antes de continuar
                            } else {
                                \Log::error('Status Boleto V2 - Erro ao consultar - Invoice ID: '.$invoice['id'].' - nossoNumero: '.$nossoNumero.' - Status Code: '.$response_v2->status().' - Response: '.$response_v2->body());
                            }
                        }
                        
                        // Delay entre requisições para evitar rate limit
                        usleep(500000); // 0.5 segundo entre cada requisição
                        continue; // Pula para próximo invoice após processar V2
                    }

                    $transaction_id = $transaction_id_clean;
                    $url = rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas/'.$transaction_id;

                    $certPath = storage_path('/app/'.$company->inter_crt_file);
                    $keyPath = storage_path('/app/'.$company->inter_key_file);
                    
                    $options = [
                        'cert' => $certPath,
                        'ssl_key' => $keyPath,
                        'curl' => [],
                    ];
                    
                    if ($sslVerify === false) {
                        $options['verify'] = false;
                        $options['curl'][CURLOPT_SSL_VERIFYPEER] = false;
                        $options['curl'][CURLOPT_SSL_VERIFYHOST] = false;
                    } elseif (!empty($caBundle)) {
                        $options['verify'] = $caBundle;
                    }
                    
                    $response = Http::withOptions($options)->withHeaders([
                        'Authorization' => 'Bearer ' . $access_token
                    ])->get($url);

                    if ($response->successful()) {
                            $responseBody = $response->body();
                            $status = json_decode($responseBody)->cobranca->situacao;

                            if($status == 'RECEBIDO'){
                                try{
                                Invoice::where('transaction_id',$transaction_id)->update(['status' => 'Pago']);
                                InvoiceNotification::Email($invoice['id']);
                                InvoiceNotification::Whatsapp($invoice['id']);
                                 } catch(\Exception $e){
                                    \Log::error('Erro ao atualizar status Boleto - Invoice ID: '.$invoice['id'].' - '.$e->getMessage());
                                }
                            }

                        }else{
                            if($response->status() == 429){
                                \Log::warning('Status Boleto V3 - Rate limit (429) - Invoice ID: '.$invoice['id'].' - Aguardando para continuar...');
                                sleep(5);
                            } else {
                                \Log::error('Status Boleto - Erro na consulta - Invoice ID: '.$invoice['id'].' - transaction_id: '.$transaction_id.' - Status Code: '.$response->status().' - Response: '.$response->body());
                            }
                        }
                        
                        // Delay entre requisições
                        usleep(500000); // 0.5 segundo

                }

        elseif ($invoice['payment_method'] == 'BoletoPix') {
            // Valida e limpa o transaction_id
            $transaction_id = trim((string) $invoice['transaction_id']);
            if (empty($transaction_id)) {
                continue;
            }

            // Remove caracteres não numéricos/alphanuméricos que podem causar problemas
            $transaction_id = preg_replace('/[^a-zA-Z0-9\-]/', '', $transaction_id);
            $url = rtrim($company->inter_host, '/').'/cobranca/v3/cobrancas/'.$transaction_id;

            $certPath = storage_path('/app/'.$company->inter_crt_file);
            $keyPath = storage_path('/app/'.$company->inter_key_file);
            
            $options = [
                'cert' => $certPath,
                'ssl_key' => $keyPath,
                'curl' => [],
            ];
            
            if ($sslVerify === false) {
                $options['verify'] = false;
                $options['curl'][CURLOPT_SSL_VERIFYPEER] = false;
                $options['curl'][CURLOPT_SSL_VERIFYHOST] = false;
            } elseif (!empty($caBundle)) {
                $options['verify'] = $caBundle;
            }
            
            $response = Http::withOptions($options)->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get($url);

            if ($response->successful()) {
                $responseBody = $response->body();
                $responseData = json_decode($responseBody);
                $status = $responseData->cobranca->situacao ?? null;

                if ($status == 'RECEBIDO') {
                    try {
                        Invoice::where('transaction_id', $transaction_id)->update(['status' => 'Pago']);
                        InvoiceNotification::Email($invoice['id']);
                        InvoiceNotification::Whatsapp($invoice['id']);
                    } catch (\Exception $e) {
                        \Log::error('Erro ao atualizar status BoletoPix - Invoice ID: '.$invoice['id'].' - '.$e->getMessage());
                    }
                }
            } else {
                if($response->status() == 429){
                    \Log::warning('Status BoletoPix - Rate limit (429) - Invoice ID: '.$invoice['id'].' - Aguardando para continuar...');
                    sleep(5);
                } else {
                    \Log::error('Status BoletoPix - Erro na consulta - Invoice ID: '.$invoice['id'].' - transaction_id: '.$transaction_id.' - Status Code: '.$response->status().' - Response: '.$response->body());
                }
            }
            
            // Delay entre requisições
            usleep(500000); // 0.5 segundo
        }

        elseif ($invoice['payment_method'] == 'Pix') {

            // Valida e limpa o transaction_id
            $transaction_id = trim((string) $invoice['transaction_id']);
            if (empty($transaction_id)) {
                continue;
            }

            // VALIDAÇÃO EXTRA: Verifica se os certificados existem antes de tentar consultar PIX
            $certPath = storage_path('/app/'.$company->inter_crt_file);
            $keyPath = storage_path('/app/'.$company->inter_key_file);
            
            if (!file_exists($certPath) || !file_exists($keyPath)) {
                \Log::warning('Status Pix - Certificados não encontrados para Company ID: '.$company->id.' - Invoice ID: '.$invoice['id'].' - Pulando consulta');
                continue;
            }

            // Remove caracteres não numéricos/alphanuméricos que podem causar problemas
            $transaction_id = preg_replace('/[^a-zA-Z0-9\-]/', '', $transaction_id);
            $url = rtrim($company->inter_host, '/').'/pix/v2/cobv/'.$transaction_id;
            
            try {
                $options = [
                    'cert' => $certPath,
                    'ssl_key' => $keyPath,
                    'curl' => [],
                ];
                
                if ($sslVerify === false) {
                    $options['verify'] = false;
                    $options['curl'][CURLOPT_SSL_VERIFYPEER] = false;
                    $options['curl'][CURLOPT_SSL_VERIFYHOST] = false;
                } elseif (!empty($caBundle)) {
                    $options['verify'] = $caBundle;
                }
                
                $response = Http::withOptions($options)->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token
                ])->get($url);

                if ($response->successful()) {
                    $responseBody = $response->body();
                    $status = json_decode($responseBody)->status;

                    if ($status == 'CONCLUIDA') {
                        Invoice::where('transaction_id', $transaction_id)->update(['status' => 'Pago']);
                        InvoiceNotification::Email($invoice['id']);
                        InvoiceNotification::Whatsapp($invoice['id']);
                    }
                } else {
                    if($response->status() == 429){
                        \Log::warning('Status Pix - Rate limit (429) - Invoice ID: '.$invoice['id'].' - Aguardando para continuar...');
                        sleep(5);
                    } else {
                        \Log::error('Status Pix - Erro na consulta - Invoice ID: '.$invoice['id'].' - transaction_id: '.$transaction_id.' - Status Code: '.$response->status().' - Response: '.$response->body());
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Status Pix - Exceção ao consultar - Invoice ID: '.$invoice['id'].' - transaction_id: '.$transaction_id.' - Erro: '.$e->getMessage());
                // Continua processando próxima fatura ao invés de quebrar
            }
            
            // Delay entre requisições
            usleep(500000); // 0.5 segundo
        }
        
        // Informar progresso a cada 10 faturas
        if($contador % 10 == 0){
            $this->info("Processadas {$contador} de {$invoices->count()} faturas...");
        }
    }

    $this->info('StatusInterCron finalizado - Total: '.$contador.' faturas processadas');
    return 0;
  }
}
