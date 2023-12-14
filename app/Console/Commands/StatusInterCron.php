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

                    $response = Http::withOptions(
                        [
                        'cert' => storage_path('/app/'.$user['inter_crt_file']),
                        'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                        ]
                        )->withHeaders([
                        'Authorization' => 'Bearer ' . $access_token
                    ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v2/boletos/'.$invoice['transaction_id']);

                        if ($response->successful()) {
                            $responseBody = $response->body();
                            $status = json_decode($responseBody)->situacao;

                            if($status == 'PAGO'){
                                Invoice::where('transaction_id',$invoice['transaction_id'])->update(['status','Pago']);
                                InvoiceNotification::Email($invoice['id']);
                                InvoiceNotification::Whatsapp($invoice['id']);
                            }

                        }else{
                            \Log::info('Status Boleto: '. json_encode($response->body()));
                        }

                }

                if($invoice['payment_method'] == 'BoletoPix'){

                    $response = Http::withOptions(
                        [
                        'cert' => storage_path('/app/'.$user['inter_crt_file']),
                        'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                        ]
                        )->withHeaders([
                        'Authorization' => 'Bearer ' . $access_token
                    ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas/'.$invoice['transaction_id']);
dd( $responseBody = $response->body());
                        if ($response->successful()) {
                            $responseBody = $response->body();
                            $status = json_decode($responseBody)->cobranca->situacao;

                            if($status == 'RECEBIDO'){
                                Invoice::where('transaction_id',$invoice['transaction_id'])->update(['status','Pago']);
                                InvoiceNotification::Email($invoice['id']);
                                InvoiceNotification::Whatsapp($invoice['id']);
                            }

                        }else{
                            \Log::info('Status BoletoPix: '. json_encode($response->body()));
                        }

                }

                if($invoice['payment_method'] == 'Pix'){

                    $response = Http::withOptions(
                        [
                        'cert' => storage_path('/app/'.$user['inter_crt_file']),
                        'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                        ]
                        )->withHeaders([
                        'Authorization' => 'Bearer ' . $access_token
                    ])->get('https://cdpj.partners.bancointer.com.br/pix/v2/cobv/'.$invoice['transaction_id']);

                        if ($response->successful()) {
                            $responseBody = $response->body();
                            $status = json_decode($responseBody)->status;

                            if($status == 'CONCLUIDA'){
                                Invoice::where('transaction_id',$invoice['transaction_id'])->update(['status','Pago']);
                                InvoiceNotification::Email($invoice['id']);
                                InvoiceNotification::Whatsapp($invoice['id']);
                            }

                        }else{
                            \Log::info('Status Pix: '. json_encode($response->body()));
                        }

                }



            }
        }

    }



  }
}
