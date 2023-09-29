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


class TokenInterCron extends Command
{

  protected $signature = 'tokeninter:cron';

  protected $description = 'Gerar access token inter';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {


    $users = User::where('status','Ativo')->get();


    foreach($users as $user){

        if($user->inter_host == ''){
            $error = ['status' => 'reject', 'message' => 'HOST banco inter não cadastrado!'];
            \Log::info('TokenInterCron: '.json_encode($error));
        }

        else if($user->inter_client_id == ''){
            $error =  ['status' => 'reject', 'message' => 'CLIENT ID banco inter não cadastrado!'];
            \Log::info('TokenInterCron: '.json_encode($error));
        }
        else if($user->inter_client_secret == ''){
            $error =  ['status' => 'reject', 'message' => 'CLIENT SECRET banco inter não cadastrado!'];
            \Log::info('TokenInterCron: '.json_encode($error));
        }
        else if($user->inter_crt_file == ''){
            $error =  ['status' => 'reject', 'message' => 'Certificado CRT banco inter não cadastrado!'];
            \Log::info('TokenInterCron: '.json_encode($error));
        }
        else if(!file_exists(storage_path('/app/'.$user->inter_crt_file))){
            $error =  ['status' => 'reject', 'message' => 'Certificado CRT banco inter não existe!'];
            \Log::info('TokenInterCron: '.json_encode($error));
        }

        else if($user->inter_key_file == ''){
            $error =  ['status' => 'reject', 'message' => 'Certificado KEY banco inter não cadastrado!'];
            \Log::info('TokenInterCron: '.json_encode($error));
        }
        else if(!file_exists(storage_path('/app/'.$user->inter_key_file))){
            $error =  ['status' => 'reject', 'message' => 'Certificado KEY banco inter não existe!'];
            \Log::info('TokenInterCron: '.json_encode($error));
        }
        else{

            $response = Http::withOptions([
                'cert' => storage_path('/app/'.$user->inter_crt_file),
                'ssl_key' => storage_path('/app/'.$user->inter_key_file),
            ])->asForm()->post($user->inter_host.'oauth/v2/token', [
                'client_id' => $user->inter_client_id,
                'client_secret' => $user->inter_client_secret,
                'scope' => $user->inter_scope,
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $responseBody = $response->body();
                $access_token = json_decode($responseBody)->access_token;
                User::where('id',$user->id)->update([
                    'access_token_inter' => $access_token
                ]);
            }else{
                $error = ['status' => 'reject', 'message' => 'Erro ao autenticar com o Banco Inter, informe ao Administrador do sistema!'];

                \Log::info('TokenInterCron: '.json_encode($error));
            }

        }


    }



  }
}
