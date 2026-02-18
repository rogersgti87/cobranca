<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


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
    $companies = Company::where('status', 'Ativo')->whereNotNull('inter_client_id')->get();

    foreach ($companies as $company) {
        if ($company->inter_host == '' || $company->inter_host === null) {
            $error = ['status' => 'reject', 'message' => 'HOST banco inter não cadastrado!', 'company_id' => $company->id];
            Log::info('TokenInterCron: ' . json_encode($error));
            continue;
        }

        if ($company->inter_client_secret == '' || $company->inter_client_secret === null) {
            $error = ['status' => 'reject', 'message' => 'CLIENT SECRET banco inter não cadastrado!', 'company_id' => $company->id];
            Log::info('TokenInterCron: ' . json_encode($error));
            continue;
        }

        if ($company->inter_crt_file == '' || $company->inter_crt_file === null) {
            $error = ['status' => 'reject', 'message' => 'Certificado CRT banco inter não cadastrado!', 'company_id' => $company->id];
            Log::info('TokenInterCron: ' . json_encode($error));
            continue;
        }

        if (!file_exists(storage_path('/app/' . $company->inter_crt_file))) {
            $error = ['status' => 'reject', 'message' => 'Certificado CRT banco inter não existe!', 'company_id' => $company->id];
            Log::info('TokenInterCron: ' . json_encode($error));
            continue;
        }

        if ($company->inter_key_file == '' || $company->inter_key_file === null) {
            $error = ['status' => 'reject', 'message' => 'Certificado KEY banco inter não cadastrado!', 'company_id' => $company->id];
            Log::info('TokenInterCron: ' . json_encode($error));
            continue;
        }

        if (!file_exists(storage_path('/app/' . $company->inter_key_file))) {
            $error = ['status' => 'reject', 'message' => 'Certificado KEY banco inter não existe!', 'company_id' => $company->id];
            Log::info('TokenInterCron: ' . json_encode($error));
            continue;
        }

        $response = Http::withOptions([
            'cert' => storage_path('/app/' . $company->inter_crt_file),
            'ssl_key' => storage_path('/app/' . $company->inter_key_file),
        ])->asForm()->post($company->inter_host . 'oauth/v2/token', [
            'client_id' => $company->inter_client_id,
            'client_secret' => $company->inter_client_secret,
            'scope' => $company->inter_scope ?? 'boleto-cobranca.read boleto-cobranca.write',
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            $responseBody = $response->body();
            $access_token = json_decode($responseBody)->access_token;
            $company->update([
                'access_token_inter' => $access_token,
            ]);
        } else {
            $error = [
                'status' => 'reject',
                'message' => 'Erro ao autenticar com o Banco Inter, informe ao Administrador do sistema!',
                'company_id' => $company->id,
            ];
            Log::info('TokenInterCron: ' . json_encode($error));
        }
    }

    return 'TokenInterCron atualizado com sucesso';
  }
}
