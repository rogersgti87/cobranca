<?php

namespace App\Console\Commands;

use App\Jobs\sendInvoice;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\User;
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

    $users = User::where('status','Ativo')->where('use_intermedium','s')->get();

    foreach($users as $user){

        $access_token = $user['access_token_inter'];

        $invoices = Invoice::where('gateway_payment','Intermedium')->where('status','Pendente')->where('user_id',$user['id'])->whereNotNull('transaction_id')->get();

        foreach($invoices as $invoice){
            if($invoices != null){

                if($access_token == null){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token inválido!']]];
                }
                if($user['inter_host'] == ''){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'HOST banco inter não cadastrado!']]];
                }
                if($user['inter_client_id'] == ''){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT ID banco inter não cadastrado!']]];
                }
                if($user['inter_client_secret'] == ''){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT SECRET banco inter não cadastrado!']]];
                }
                if($user['inter_crt_file'] == ''){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado CRT banco inter não cadastrado!']]];
                }
                if(!file_exists(storage_path('/app/'.$user['inter_crt_file']))){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado CRT banco inter não existe!']]];
                }
                if($user['inter_key_file'] == ''){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado KEY banco inter não cadastrado!']]];
                }
                if(!file_exists(storage_path('/app/'.$user['inter_key_file']))){
                    return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado KEY banco inter não existe!']]];
                }

                if($invoice['payment_method'] == 'Boleto'){

                    // Valida e limpa o transaction_id
                    $transaction_id = trim((string) $invoice['transaction_id']);
                    if(empty($transaction_id)){
                        continue;
                    }

                    // Remove caracteres não numéricos/alphanuméricos que podem causar problemas
                    $transaction_id_clean = preg_replace('/[^a-zA-Z0-9\-]/', '', $transaction_id);
                    
                    // Verifica se é UUID (V3) ou número (V2 antigo)
                    // UUID tem hífens, nossoNumero antigo é apenas numérico
                    $is_uuid = (strpos($transaction_id_clean, '-') !== false);
                    
                    if(!$is_uuid && is_numeric($transaction_id_clean)){
                        // É um nossoNumero antigo da V2 - tenta consultar via API V2
                        $nossoNumero = $transaction_id_clean;
                        
                        // A API V2 requer dataInicial e dataFinal obrigatórios
                        // Usa a data de vencimento do boleto como referência
                        $dataVencimento = $invoice['date_due'] ?? Carbon::now()->format('Y-m-d');
                        $dataInicial = Carbon::parse($dataVencimento)->subDays(30)->format('Y-m-d'); // 30 dias antes
                        $dataFinal = Carbon::parse($dataVencimento)->addDays(30)->format('Y-m-d'); // 30 dias depois
                        
                        // Tenta consultar via API V2 usando o endpoint de listagem com filtro
                        $url_v2 = rtrim($user['inter_host'], '/').'/cobranca/v2/boletos?dataInicial='.$dataInicial.'&dataFinal='.$dataFinal.'&nossoNumero='.$nossoNumero.'&itensPorPagina=100';
                        
                        $response_v2 = Http::withOptions(
                            [
                            'cert' => storage_path('/app/'.$user['inter_crt_file']),
                            'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                            ]
                            )->withHeaders([
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
                            \Log::error('Status Boleto V2 - Erro ao consultar - Invoice ID: '.$invoice['id'].' - nossoNumero: '.$nossoNumero.' - Status Code: '.$response_v2->status().' - Response: '.$response_v2->body());
                        }
                        
                        continue; // Pula para próximo invoice após processar V2
                    }
                    
                    $transaction_id = $transaction_id_clean;
                    $url = rtrim($user['inter_host'], '/').'/cobranca/v3/cobrancas/'.$transaction_id;

                    $response = Http::withOptions(
                        [
                        'cert' => storage_path('/app/'.$user['inter_crt_file']),
                        'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                        ]
                        )->withHeaders([
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
                            \Log::error('Status Boleto - Erro na consulta - Invoice ID: '.$invoice['id'].' - transaction_id: '.$transaction_id.' - Status Code: '.$response->status().' - Response: '.$response->body());
                        }

                }

                if($invoice['payment_method'] == 'BoletoPix'){
                    // Valida e limpa o transaction_id
                    $transaction_id = trim((string) $invoice['transaction_id']);
                    if(empty($transaction_id)){
                        continue;
                    }

                    // Remove caracteres não numéricos/alphanuméricos que podem causar problemas
                    $transaction_id = preg_replace('/[^a-zA-Z0-9\-]/', '', $transaction_id);
                    $url = rtrim($user['inter_host'], '/').'/cobranca/v3/cobrancas/'.$transaction_id;

                    $response = Http::withOptions(
                        [
                        'cert' => storage_path('/app/'.$user['inter_crt_file']),
                        'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                        ]
                        )->withHeaders([
                        'Authorization' => 'Bearer ' . $access_token
                    ])->get($url);

                        if ($response->successful()) {
                            $responseBody = $response->body();
                            $responseData = json_decode($responseBody);
                            $status = $responseData->cobranca->situacao ?? null;

                            if($status == 'RECEBIDO'){
                                try{
                                Invoice::where('transaction_id',$transaction_id)->update(['status' => 'Pago']);
                                InvoiceNotification::Email($invoice['id']);
                                InvoiceNotification::Whatsapp($invoice['id']);
                                 } catch(\Exception $e){
                                    \Log::error('Erro ao atualizar status BoletoPix - Invoice ID: '.$invoice['id'].' - '.$e->getMessage());
                                }
                            }

                        }else{
                            \Log::error('Status BoletoPix - Erro na consulta - Invoice ID: '.$invoice['id'].' - transaction_id: '.$transaction_id.' - Status Code: '.$response->status().' - Response: '.$response->body());
                        }

                }

                if($invoice['payment_method'] == 'Pix'){

                    // Valida e limpa o transaction_id
                    $transaction_id = trim((string) $invoice['transaction_id']);
                    if(empty($transaction_id)){
                        continue;
                    }

                    // Remove caracteres não numéricos/alphanuméricos que podem causar problemas
                    $transaction_id = preg_replace('/[^a-zA-Z0-9\-]/', '', $transaction_id);
                    $url = rtrim($user['inter_host'], '/').'/pix/v2/cobv/'.$transaction_id;

                    $response = Http::withOptions(
                        [
                        'cert' => storage_path('/app/'.$user['inter_crt_file']),
                        'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                        ]
                        )->withHeaders([
                        'Authorization' => 'Bearer ' . $access_token
                    ])->get($url);

                        if ($response->successful()) {
                            $responseBody = $response->body();
                            $status = json_decode($responseBody)->status;

                            if($status == 'CONCLUIDA'){
                                Invoice::where('transaction_id',$transaction_id)->update(['status' => 'Pago']);
                                InvoiceNotification::Email($invoice['id']);
                                InvoiceNotification::Whatsapp($invoice['id']);
                            }

                        }else{
                            \Log::error('Status Pix - Erro na consulta - Invoice ID: '.$invoice['id'].' - transaction_id: '.$transaction_id.' - Status Code: '.$response->status().' - Response: '.$response->body());
                        }

                }



            }
        }

    }



  }
}
