<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\ViewInvoice;
use App\Models\InvoiceNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Log;

class Invoice extends Model
{


    protected $fillable = [
        'status',
        'date_payment',
        'updated_at',
        'transaction_id',
        'billet_url',
        'billet_base64',
        'billet_digitable',
        'image_url_pix',
        'pix_digitable',
        'qrcode_pix_base64',
        'days_due_date',

    ];

    public static function generateBilletPH($invoice_id){

        if (!Storage::disk('public')->exists('boletos')) {
            Storage::disk('public')->makeDirectory('boletos');
        }


        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        if($invoice['days_due_date'] == 0){
            if(date('l') == 'Saturday' || date('l') == 'Sábado'){
                $invoice['days_due_date'] = 2;
            }
            if(date('l') == 'Sunday' || date('l') == 'Domingo'){
                $invoice['days_due_date'] = 1;
            }
        }

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
          ])->post('https://api.paghiper.com/transaction/create/',[
            'apiKey'            =>  $invoice['key_paghiper'],
            'order_id'          =>  $invoice['id'],
            'payer_email'       =>  $invoice['email'],
            'payer_name'        =>  $invoice['name'],
            'payer_cpf_cnpj'    =>  removeEspeciais($invoice['document']),
            'payer_phone'       =>  $invoice['phone'],
            'payer_street'      =>  $invoice['address'],
            'payer_number'      =>  $invoice['number'],
            'payer_complement'  =>  $invoice['complement'],
            'payer_district'    =>  $invoice['district'],
            'payer_city'        =>  $invoice['city'],
            'payer_state'       =>  $invoice['state'],
            'payer_zip_code'    =>  $invoice['cep'],
            'type_bank_slip'    => 'boletoA4',
            'days_due_date'     =>  $invoice['days_due_date'],
            'notification_url'  => 'https://cobrancasegura.com.br/webhook/paghiper',
            'late_payment_fine' => '1',// Percentual de multa após vencimento.
            'per_day_interest'  => true, // Juros após vencimento.
            'items' => array([
                'item_id'       => 1,
                'description'   => $invoice['description'],
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice['price'],100)
          ])
          ]);


          if ($response->successful()) {

            $result = $response->getBody();
            $result = json_decode($result)->create_request;

            if($result->result == 'success'){

            $contents = Http::get($result->bank_slip->url_slip_pdf)->body();
            Storage::disk('public')->put('boletos/' .  $invoice->user_id.'_'.$invoice->id.'.'.'pdf', $contents);
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

        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
          ])->post('https://pix.paghiper.com/invoice/create/',[
            'apiKey'            =>  $invoice['key_paghiper'],
            'order_id'          =>  $invoice['id'],
            'payer_email'       =>  $invoice['email'],
            'payer_name'        =>  $invoice['name'],
            'payer_cpf_cnpj'    =>  $invoice['document'],
            'days_due_date'     =>  90,
            'notification_url'  => 'https://cobrancasegura.com.br/webhook/paghiper',
            'items' => array([
                'item_id'       => 1,
                'description'   => $invoice['description'],
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice['price'],100)
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
                    'image_url_pix'     => 'https://cobrancasegura.com.br/storage/pix/'.$invoice['user_id'].'_'.$invoice['id'].'.png',
                    'pix_digitable'     => $result->pix_code->emv,
                    'qrcode_pix_base64' => $result->pix_code->qrcode_base64,
                ]);



                //\File::put(public_path(). '/pix/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'png', base64_decode($result->pix_code->qrcode_base64));
                $contents = Http::get($result->pix_code->qrcode_image_url)->body();
                Storage::disk('public')->put('pix/' .  $invoice['user_id'].'_'.$invoice['id'].'.'.'png', $contents);

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

        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        \MercadoPago\SDK::setAccessToken($invoice['access_token_mp']);

        $payment = new \MercadoPago\Payment();
        $payment->transaction_amount    = $invoice['price'];
        $payment->statement_descriptor  = $invoice['company'];
        $payment->description           = $invoice['description'];
        $payment->payment_method_id     = "pix";
        $payment->notification_url      = 'https://cobrancasegura.com.br/webhook/mercadopago?source_news=webhooks';
        $payment->external_reference    = $invoice['id'];
        $payment->date_of_expiration    = Carbon::now()->addDays(40)->format('Y-m-d\TH:i:s') . '.000-04:00';
        $payment->payer = array(
            "email"    => $invoice['email']
        );

        $status_payment = $payment->save();

       $payment_id = $payment->id ? $payment->id : '';

       if($payment_id == ''){
            return ['status' => 'reject', 'message' => 'O valor do pix esta abaixo do minimo permitido de R$ 3,00.'];
        }else{

            \MercadoPago\SDK::setAccessToken($invoice['access_token_mp']);

            try{
                $payment = \MercadoPago\Payment::find_by_id($payment_id);

                Invoice::where('id',$invoice_id)->update([
                    'status'            =>  'Pendente',
                    'msg_erro'          =>  null,
                    'transaction_id'    => $payment_id,
                    'image_url_pix'     => 'https://cobrancasegura.com.br/storage/pix/'.$invoice['user_id'].'_'.$invoice['id'].'.png',
                    'pix_digitable'     => $payment->point_of_interaction->transaction_data->qr_code,
                    'qrcode_pix_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                ]);

                //$invoice = Invoice::where('id',$invoice_id)->first();

                //\File::put(public_path(). '/pix/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'png', base64_decode($payment->point_of_interaction->transaction_data->qr_code_base64));

                Storage::disk('public')->put('pix/' .  $invoice['user_id'].'_'.$invoice['id'].'.'.'png', base64_decode($payment->point_of_interaction->transaction_data->qr_code_base64));
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

        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        $user = User::where('id',$invoice['user_id'])->first();

        $access_token = $user['access_token_inter'];

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


        $check_access_token = Http::withOptions(
            [
            'cert' => storage_path('/app/'.$user['inter_crt_file']),
            'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
            ]
            )->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
        ])->get('https://cdpj.partners.bancointer.com.br/pix/v2/cob?inicio=2023-02-30T12:09:00Z&fim=2023-03-30T12:09:00Z');

        if ($check_access_token->unauthorized()) {
            $response = Http::withOptions([
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
            ])->asForm()->post($user['inter_host'].'oauth/v2/token', [
                'client_id' => $user['inter_client_id'],
                'client_secret' => $user['inter_client_secret'],
                'scope' => $user['inter_scope'],
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $responseBody = $response->body();
                $access_token = json_decode($responseBody)->access_token;
                User::where('id',$user['id'])->update([
                    'access_token_inter' => $access_token
                ]);

                $invoice = ViewInvoice::where('id',$invoice_id)->first();
            }else{
                return ['status' => 'reject', 'title' => 'Erro ao gerar PIX', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token Expirado ou inválido!']]];
            }
        }

        $txid = generateUniqueId();

       $body = [
            "calendario"                    => [
                "dataDeVencimento"          => $invoice['date_due'],
                "validadeAposVencimento"    => 30,
            ],
            // "loc"                           => [
            //     "id"                        => $invoice['id'],
            // ],
            "devedor"                       => [
                "logradouro"                => $invoice['address'],
                "cidade"                    => $invoice['city'],
                "uf"                        => $invoice['state'],
                "cep"                       => removeEspeciais($invoice['cep']),
              ],
            "valor"                         => [
                "original"                  => $invoice['price'],
                "juros"                     => [
                    "modalidade"            => "2",
                    "valorPerc"             => "1.00"
                ],
            ],
            "chave"                         => $invoice['inter_chave_pix'],
            "solicitacaoPagador"            => $invoice['description']
            ];

        if($invoice['type'] == 'Física'){
            $body['devedor']['cpf']     = $invoice['document'];
            $body['devedor']['nome']    = Str::limit($invoice['name'], 30);
        }else{
            $body['devedor']['cnpj']    = $invoice['document'];
            $body['devedor']['nome']    = Str::limit($invoice['name'], 30);
        }


        $response_generate_pix = Http::withOptions([
            'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
            'ssl_key' => storage_path('/app/'.$invoice['inter_key_file']),
            ])->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
          ])->put($invoice['inter_host'].'pix/v2/cobv/'.$txid,$body);


        if ($response_generate_pix->successful()) {

            $result_generate_pix = $response_generate_pix->json();
            $result_generate_pix = json_decode($response_generate_pix);


            QrCode::format('png')->size(220)->generate($result_generate_pix->pixCopiaECola, storage_path('app/public'). '/pix/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'png');
            $image_pix = env('APP_URL').Storage::url('pix/'.$invoice['user_id'].'_'.$invoice['id'].'.png');

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


        $user = User::where('id',$user_id)->first();
        //Consultar status da transação
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://pix.paghiper.com/invoice/cancel/',[
            'token'             => $user->token_paghiper,
            'apiKey'            => $user->key_paghiper,
            'status'            => 'canceled',
            'transaction_id'    => $transaction_id
        ]);

        $response = $response->getBody();
        return json_decode($response)->cancellation_request;

    }


    public static function cancelBilletPH($user_id,$transaction_id){


        $user = User::where('id',$user_id)->first();
        //Consultar status da transação
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.paghiper.com/transaction/cancel/',[
            'token'             => $user->token_paghiper,
            'apiKey'            => $user->key_paghiper,
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

            if (!Storage::disk('public')->exists('boletos')) {
                Storage::disk('public')->makeDirectory('boletos');
            }

            $invoice = ViewInvoice::where('id',$invoice_id)->first();

            $user = User::where('id',$invoice['user_id'])->first();

            $access_token = $user['access_token_inter'];

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

            $check_access_token = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

            if ($check_access_token->unauthorized()) {
                $response = Http::withOptions([
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
                ])->asForm()->post($user['inter_host'].'oauth/v2/token', [
                    'client_id' => $user['inter_client_id'],
                    'client_secret' => $user['inter_client_secret'],
                    'scope' => $user['inter_scope'],
                    'grant_type' => 'client_credentials',
                ]);

                if ($response->successful()) {
                    $responseBody = $response->body();
                    $access_token = json_decode($responseBody)->access_token;
                    User::where('id',$user->id)->update([
                        'access_token_inter' => $access_token
                    ]);

                    $invoice = ViewInvoice::where('id',$invoice_id)->first();
                }else{
                    return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token Expirado ou inválido!']]];
                }
            }


            $date_multa = Carbon::parse($invoice['date_due'])->addDays(1);
            if(date('l') == 'Saturday' || date('l') == 'Sábado'){
                $date_multa = Carbon::parse($invoice['date_due'])->addDays(2);
            }

            // Validar e corrigir data de vencimento
            $dataVencimento = Carbon::parse($invoice['date_due']);
            $dataAtual = Carbon::now();
            
            // Se a data de vencimento for menor que a data atual, ajustar para hoje
            if($dataVencimento->lt($dataAtual)){
                $dataVencimento = $dataAtual->copy();
                
                // Se for sábado, ajustar para segunda-feira
                if($dataVencimento->isSaturday()){
                    $dataVencimento->addDays(2);
                }
                // Se for domingo, ajustar para segunda-feira
                elseif($dataVencimento->isSunday()){
                    $dataVencimento->addDay();
                }
                
                \Log::warning("Data de vencimento ajustada para invoice ID: {$invoice_id}. Data original: {$invoice['date_due']}, Nova data: {$dataVencimento->format('Y-m-d')}");
            }
            
            // Garantir formato Y-m-d
            $dataVencimentoFormatada = $dataVencimento->format('Y-m-d');

            $response_generate_billet = Http::withOptions([
                'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$invoice['inter_key_file']),
                ])->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
              ])->post($invoice['inter_host'].'cobranca/v3/cobrancas',[
                "seuNumero"=> $invoice['id'],
                "valorNominal"=> $invoice['price'],
                "dataVencimento"=> $dataVencimentoFormatada,
                "numDiasAgenda"=> 60,
                "pagador"=> [
                  "cpfCnpj"=> $invoice['document'],
                  "nome"=> $invoice['name'],
                  "email"=> $invoice['email'],
                  "telefone"=> substr($invoice['whatsapp'],2),
                  "cep"=> removeEspeciais($invoice['cep']),
                  "numero"=> $invoice['number'],
                  "complemento"=> $invoice['complement'],
                  "bairro"=> $invoice['district'],
                  "cidade"=> $invoice['city'],
                  "uf"=> $invoice['state'],
                  "endereco"=> $invoice['address'],
                  "ddd"=> substr($invoice['whatsapp'],0,2),
                  "tipoPessoa"=> $invoice['type'] == 'Física' ? 'FISICA' : 'JURIDICA'
                ],
                "multa"=> [
                "codigo"=> "PERCENTUAL",
                "taxa"=> 1
              ]
              ]);

            if ($response_generate_billet->successful()) {

                $result_generate_billet = json_decode($response_generate_billet->body());
                
                // Valida se codigoSolicitacao foi retornado
                if(empty($result_generate_billet->codigoSolicitacao)){
                    \Log::error('Erro ao gerar boleto: codigoSolicitacao não retornado para invoice ID: '.$invoice_id);
                    return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'codigoSolicitacao não retornado pela API', 'propriedade' => 'Erro ao gerar boleto intermedium!']]];
                }

                $codigoSolicitacao = trim((string) $result_generate_billet->codigoSolicitacao);

                // V3 API: Need to get the cobranca details to retrieve boleto information
                $response_get_billet = Http::retry(3, 100)->withOptions(
                    [
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                    ]
                    )->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token

                ])->get($invoice['inter_host'].'cobranca/v3/cobrancas/'.$codigoSolicitacao);

                if ($response_get_billet->successful()) {
                    $result_get_billet = json_decode($response_get_billet->body());
                    
                    // Log da estrutura para debug
                    \Log::info('Estrutura resposta get_billet: '.json_encode($result_get_billet));
                } else {
                    \Log::error('Erro ao obter detalhes do boleto: '.$response_get_billet->body());
                    return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Erro ao obter codigo boleto intermedium!']]];
                }

                $response_pdf_billet = Http::retry(3, 100)->withOptions(
                    [
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                    ]
                    )->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token

                ])->get($invoice['inter_host'].'cobranca/v3/cobrancas/'.$codigoSolicitacao.'/pdf');

                if ($response_pdf_billet->successful()) {

                    $responseBodyPdf = $response_pdf_billet->getBody();
                    $pdf = json_decode($responseBodyPdf)->pdf;

                    //\File::put(public_path(). '/boleto/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'pdf', base64_decode($pdf));
                    //$billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoice['user_id'].'_'.$invoice['id'].'.pdf';

                    Storage::disk('public')->put('boletos/' .  $invoice['user_id'].'_'.$invoice['id'].'.'.'pdf', base64_decode($pdf));
                    $billet_pdf = env('APP_URL').Storage::url('boletos/' .$invoice['user_id'].'_'.$invoice['id'].'.pdf');

                    // Tenta obter linhaDigitavel de diferentes estruturas possíveis
                    $linhaDigitavel = null;
                    if(isset($result_get_billet->cobranca->boleto->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->cobranca->boleto->linhaDigitavel;
                    } elseif(isset($result_get_billet->boleto->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->boleto->linhaDigitavel;
                    } elseif(isset($result_get_billet->cobranca->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->cobranca->linhaDigitavel;
                    } elseif(isset($result_get_billet->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->linhaDigitavel;
                    } else {
                        \Log::warning('linhaDigitavel não encontrada na resposta para invoice ID: '.$invoice_id);
                        // Pode ser que a linha digitável não esteja disponível ainda, mas o boleto foi criado
                        $linhaDigitavel = '';
                    }

                    Invoice::where('id',$invoice_id)->update([
                        'status'            =>  'Pendente',
                        'msg_erro'          =>  null,
                        'transaction_id'    =>  $codigoSolicitacao,
                        'billet_url'        =>  $billet_pdf,
                        'billet_base64'     =>  $pdf,
                        'billet_digitable'  =>  $linhaDigitavel
                    ]);
                    return ['status' => 'success', 'title' => 'OK', 'message' => [['razao' => 'OK', 'propriedade' => 'OK']]];

                }else{
                    $error_response = json_decode($response_pdf_billet->body());
                    \Log::error('Erro ao gerar PDF do boleto: '.$response_pdf_billet->body());
                    return ['status' => 'reject', 'title' => isset($error_response->title) ? $error_response->title : 'Erro ao gerar PDF', 'message' => isset($error_response->violacoes) ? $error_response->violacoes : [['razao' => 'Erro ao gerar pdf pagamento intermedium', 'propriedade' => 'PDF']]];
                }


            }else{
                $result_generate_billet = $response_generate_billet->json();
                return ['status' => 'reject', 'title' => $result_generate_billet['title'], 'message' => $result_generate_billet['violacoes']];
            }


        }



        public static function generateBilletPixIntermedium($invoice_id){

            if (!Storage::disk('public')->exists('boletopix')) {
                Storage::disk('public')->makeDirectory('boletopix');
            }

            $invoice = ViewInvoice::where('id',$invoice_id)->first();

            $user = User::where('id',$invoice['user_id'])->first();

            $access_token = $user['access_token_inter'];

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

            $check_access_token = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

            if ($check_access_token->unauthorized()) {
                $response = Http::withOptions([
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
                ])->asForm()->post($user['inter_host'].'oauth/v2/token', [
                    'client_id' => $user['inter_client_id'],
                    'client_secret' => $user['inter_client_secret'],
                    'scope' => $user['inter_scope'],
                    'grant_type' => 'client_credentials',
                ]);

                if ($response->successful()) {
                    $responseBody = $response->body();
                    $access_token = json_decode($responseBody)->access_token;
                    User::where('id',$user->id)->update([
                        'access_token_inter' => $access_token
                    ]);

                    $invoice = ViewInvoice::where('id',$invoice_id)->first();
                }else{
                    return ['status' => 'reject', 'title' => 'Erro ao gerar BoletoPix', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token Expirado ou inválido!']]];
                }
            }

            // Validar e corrigir data de vencimento
            $dataVencimento = Carbon::parse($invoice['date_due']);
            $dataAtual = Carbon::now();
            
            // Se a data de vencimento for menor que a data atual, ajustar para hoje
            if($dataVencimento->lt($dataAtual)){
                $dataVencimento = $dataAtual->copy();
                
                // Se for sábado, ajustar para segunda-feira
                if($dataVencimento->isSaturday()){
                    $dataVencimento->addDays(2);
                }
                // Se for domingo, ajustar para segunda-feira
                elseif($dataVencimento->isSunday()){
                    $dataVencimento->addDay();
                }
                
                \Log::warning("Data de vencimento ajustada para invoice ID: {$invoice_id} (BoletoPix). Data original: {$invoice['date_due']}, Nova data: {$dataVencimento->format('Y-m-d')}");
            }
            
            // Garantir formato Y-m-d
            $dataVencimentoFormatada = $dataVencimento->format('Y-m-d');

            $response_generate_billet = Http::withOptions([
                'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$invoice['inter_key_file']),
                ])->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
              ])->post($invoice['inter_host'].'cobranca/v3/cobrancas',[
                "seuNumero"=> $invoice['id'],
                "valorNominal"=> $invoice['price'],
                "dataVencimento"=> $dataVencimentoFormatada,
                "numDiasAgenda"=> 60,
                "pagador"=> [
                  "cpfCnpj"=> $invoice['document'],
                  "nome"=> $invoice['name'],
                  "email"=> $invoice['email'],
                  "telefone"=> substr($invoice['whatsapp'],2),
                  "cep"=> removeEspeciais($invoice['cep']),
                  "numero"=> $invoice['number'],
                  "complemento"=> $invoice['complement'],
                  "bairro"=> $invoice['district'],
                  "cidade"=> $invoice['city'],
                  "uf"=> $invoice['state'],
                  "endereco"=> $invoice['address'],
                  "ddd"=> substr($invoice['whatsapp'],0,2),
                  "tipoPessoa"=> $invoice['type'] == 'Física' ? 'FISICA' : 'JURIDICA'
                ],
                "multa"=> [
                "codigo"=> "PERCENTUAL",
                "taxa"=> 1
              ]
              ]);

            if ($response_generate_billet->successful()) {

                $result_generate_billet = json_decode($response_generate_billet->body());
                
                // Valida se codigoSolicitacao foi retornado
                if(empty($result_generate_billet->codigoSolicitacao)){
                    \Log::error('Erro ao gerar boletopix: codigoSolicitacao não retornado para invoice ID: '.$invoice_id);
                    return ['status' => 'reject', 'title' => 'Erro ao gerar BoletoPix', 'message' => [['razao' => 'codigoSolicitacao não retornado pela API', 'propriedade' => 'Erro ao gerar boletopix intermedium!']]];
                }

                $codigoSolicitacao = trim((string) $result_generate_billet->codigoSolicitacao);

                $response_get_billet = Http::retry(3, 100)->withOptions(
                    [
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                    ]
                    )->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token

                ])->get($invoice['inter_host'].'cobranca/v3/cobrancas/'.$codigoSolicitacao);

                if ($response_get_billet->successful()) {
                    $result_get_billet = json_decode($response_get_billet->body());
                    
                    // Log da estrutura para debug
                    \Log::info('Estrutura resposta get_billet (BoletoPix): '.json_encode($result_get_billet));
                }else{
                    \Log::error('Erro ao obter detalhes do boletopix: '.$response_get_billet->body());
                    return ['status' => 'reject', 'title' => 'Erro ao gerar BoletoPix', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Erro ao obter codigo boletopix intermedium!']]];
                }

                $response_pdf_billet = Http::retry(3, 100)->withOptions(
                    [
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                    ]
                    )->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token

                ])->get($invoice['inter_host'].'cobranca/v3/cobrancas/'.$codigoSolicitacao.'/pdf');

                if ($response_pdf_billet->successful()) {

                    $responseBodyPdf = $response_pdf_billet->getBody();
                    $pdf = json_decode($responseBodyPdf)->pdf;

                    //\File::put(public_path(). '/boletopix/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'pdf', base64_decode($pdf));
                    //$billet_pdf   = 'https://cobrancasegura.com.br/boletopix/'.$invoice['user_id'].'_'.$invoice['id'].'.pdf';

                    Storage::disk('public')->put('boletopix/' .  $invoice['user_id'].'_'.$invoice['id'].'.'.'pdf', base64_decode($pdf));
                    $billet_pdf = env('APP_URL').Storage::url('boletopix/' .$invoice['user_id'].'_'.$invoice['id'].'.pdf');

                    // Tenta obter linhaDigitavel de diferentes estruturas possíveis
                    $linhaDigitavel = null;
                    if(isset($result_get_billet->cobranca->boleto->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->cobranca->boleto->linhaDigitavel;
                    } elseif(isset($result_get_billet->boleto->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->boleto->linhaDigitavel;
                    } elseif(isset($result_get_billet->cobranca->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->cobranca->linhaDigitavel;
                    } elseif(isset($result_get_billet->linhaDigitavel)){
                        $linhaDigitavel = $result_get_billet->linhaDigitavel;
                    } else {
                        \Log::warning('linhaDigitavel não encontrada na resposta (BoletoPix) para invoice ID: '.$invoice_id);
                        // Pode ser que a linha digitável não esteja disponível ainda, mas o boleto foi criado
                        $linhaDigitavel = '';
                    }

                    Invoice::where('id',$invoice_id)->update([
                        'status'            =>  'Pendente',
                        'msg_erro'          =>  null,
                        'transaction_id'    =>  $codigoSolicitacao,
                        'billet_url'        =>  $billet_pdf,
                        'billet_base64'     =>  $pdf,
                        'billet_digitable'  =>  $linhaDigitavel
                    ]);
                    return ['status' => 'success', 'title' => 'OK', 'message' => [['razao' => 'OK', 'propriedade' => 'OK']]];

                }else{
                    $error_response = json_decode($response_pdf_billet->body());
                    \Log::error('Erro ao gerar PDF do boletopix: '.$response_pdf_billet->body());
                    return ['status' => 'reject', 'title' => isset($error_response->title) ? $error_response->title : 'Erro ao gerar PDF', 'message' => isset($error_response->violacoes) ? $error_response->violacoes : [['razao' => 'Erro ao gerar pdf pagamento intermedium', 'propriedade' => 'PDF']]];
                }


            }else{
                $result_generate_billet = $response_generate_billet->json();
                return ['status' => 'reject', 'title' => $result_generate_billet['title'], 'message' => $result_generate_billet['violacoes']];
            }


        }



        public static function cancelBilletIntermedium($user_id, $transaction_id){

            $user = User::where('id',$user_id)->first();

            $access_token = $user['access_token_inter'];

            if($user['inter_host'] == ''){
                return response()->json('HOST banco inter não cadastrado!', 422);
            }
            if($user['inter_client_id'] == ''){
                return response()->json('CLIENT ID banco inter não cadastrado!', 422);
            }
            if($user['inter_client_secret'] == ''){
                return response()->json('CLIENT SECRET banco inter não cadastrado!', 422);
            }
            if($user['inter_crt_file'] == ''){
                return response()->json('Certificado CRT banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$user['inter_crt_file']))){
                return response()->json('Certificado CRT banco inter não existe!', 422);
            }
            if($user['inter_key_file'] == ''){
                return response()->json('Certificado KEY banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$user['inter_key_file']))){
                return response()->json('Certificado KEY banco inter não existe!', 422);
            }

            $check_access_token = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

            if ($check_access_token->unauthorized()) {
                $response = Http::withOptions([
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
                ])->asForm()->post($user['inter_host'].'oauth/v2/token', [
                    'client_id' => $user['inter_client_id'],
                    'client_secret' => $user['inter_client_secret'],
                    'scope' => $user['inter_scope'],
                    'grant_type' => 'client_credentials',
                ]);

                if ($response->successful()) {
                    $responseBody = $response->body();
                    $access_token = json_decode($responseBody)->access_token;
                    User::where('id',$user['id'])->update([
                        'access_token_inter' => $access_token
                    ]);

                    $user = User::where('id',$user_id)->first();
                }else{
                    return response()->json('Verifique suas credenciais, erro ao autenticar!', 422);
                }
            }


            $response = Http::withOptions([
                'cert' => storage_path('/app/'.$user->inter_crt_file),
                'ssl_key' => storage_path('/app/'.$user->inter_key_file),
            ])->asForm()->post($user->inter_host.'oauth/v2/token', [
                'client_id' => $user->inter_client_id,
                'client_secret' => $user->inter_client_secret,
                'scope' => $user->inter_scope,
                'grant_type' => 'client_credentials',
            ]);

            $responseBody = $response->body();
            $access_token = json_decode($responseBody)->access_token;


            $response_cancel_billet = Http::withOptions([
                'cert' => storage_path('/app/'.$user->inter_crt_file),
                'ssl_key' => storage_path('/app/'.$user->inter_key_file),
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $access_token

            ])->post($user->inter_host.'cobranca/v3/cobrancas/'.$transaction_id.'/cancelar',[
                "motivoCancelamento" => "ACERTOS"
            ]);

            if ($response_cancel_billet->successful()) {
                return 'success';
            }else{
                \Log::info('Erro ao cancelar pagamento intermedium: '.$response_cancel_billet->json());
            }

            }


            public static function cancelBilletPixIntermedium($user_id, $transaction_id){

                $user = User::where('id',$user_id)->first();


                $access_token = $user['access_token_inter'];

            if($user['inter_host'] == ''){
                return response()->json('HOST banco inter não cadastrado!', 422);
            }
            if($user['inter_client_id'] == ''){
                return response()->json('CLIENT ID banco inter não cadastrado!', 422);
            }
            if($user['inter_client_secret'] == ''){
                return response()->json('CLIENT SECRET banco inter não cadastrado!', 422);
            }
            if($user['inter_crt_file'] == ''){
                return response()->json('Certificado CRT banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$user['inter_crt_file']))){
                return response()->json('Certificado CRT banco inter não existe!', 422);
            }
            if($user['inter_key_file'] == ''){
                return response()->json('Certificado KEY banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$user['inter_key_file']))){
                return response()->json('Certificado KEY banco inter não existe!', 422);
            }

            $check_access_token = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

            if ($check_access_token->unauthorized()) {
                $response = Http::withOptions([
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
                ])->asForm()->post($user['inter_host'].'oauth/v2/token', [
                    'client_id' => $user['inter_client_id'],
                    'client_secret' => $user['inter_client_secret'],
                    'scope' => $user['inter_scope'],
                    'grant_type' => 'client_credentials',
                ]);

                if ($response->successful()) {
                    $responseBody = $response->body();
                    $access_token = json_decode($responseBody)->access_token;
                    User::where('id',$user['id'])->update([
                        'access_token_inter' => $access_token
                    ]);

                    $user = User::where('id',$user_id)->first();
                }else{
                    return response()->json('Verifique suas credenciais, erro ao autenticar!', 422);
                }
            }


                $response_cancel_billet = Http::withOptions([
                    'cert' => storage_path('/app/'.$user->inter_crt_file),
                    'ssl_key' => storage_path('/app/'.$user->inter_key_file),
                ])->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token

                ])->post($user->inter_host.'cobranca/v3/cobrancas/'.$transaction_id.'/cancelar',[
                    "motivoCancelamento" => "ACERTOS"
                ]);

                if ($response_cancel_billet->successful()) {
                    return 'success';
                }else{
                    \Log::info('Erro ao cancelar pagamento intermedium: '.$response_cancel_billet->json());
                }

                }




        public static function cancelPixIntermedium($user_id, $transaction_id){

            $user = User::where('id',$user_id)->first();

            $access_token = $user['access_token_inter'];

            if($user['inter_host'] == ''){
                return response()->json('HOST banco inter não cadastrado!', 422);
            }
            if($user['inter_client_id'] == ''){
                return response()->json('CLIENT ID banco inter não cadastrado!', 422);
            }
            if($user['inter_client_secret'] == ''){
                return response()->json('CLIENT SECRET banco inter não cadastrado!', 422);
            }
            if($user['inter_crt_file'] == ''){
                return response()->json('Certificado CRT banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$user['inter_crt_file']))){
                return response()->json('Certificado CRT banco inter não existe!', 422);
            }
            if($user['inter_key_file'] == ''){
                return response()->json('Certificado KEY banco inter não cadastrado!', 422);
            }
            if(!file_exists(storage_path('/app/'.$user['inter_key_file']))){
                return response()->json('Certificado KEY banco inter não existe!', 422);
            }

            $check_access_token = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

            if ($check_access_token->unauthorized()) {
                $response = Http::withOptions([
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
                ])->asForm()->post($user['inter_host'].'oauth/v2/token', [
                    'client_id' => $user['inter_client_id'],
                    'client_secret' => $user['inter_client_secret'],
                    'scope' => $user['inter_scope'],
                    'grant_type' => 'client_credentials',
                ]);

                if ($response->successful()) {
                    $responseBody = $response->body();
                    $access_token = json_decode($responseBody)->access_token;
                    User::where('id',$user['id'])->update([
                        'access_token_inter' => $access_token
                    ]);

                    $user = User::where('id',$user_id)->first();
                }else{
                    return response()->json('Verifique suas credenciais, erro ao autenticar!', 422);
                }
            }

            $response_cancel_billet = Http::withOptions([
                'cert' => storage_path('/app/'.$user->inter_crt_file),
                'ssl_key' => storage_path('/app/'.$user->inter_key_file),
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $access_token

            ])->patch($user->inter_host.'pix/v2/cobv/'.$transaction_id,[
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

                $invoice = ViewInvoice::where('id',$invoice_id)->first();

                if($invoice['days_due_date'] == 0){
                    if(date('l') == 'Saturday' || date('l') == 'Sábado'){
                        $invoice['days_due_date'] = 2;
                    }
                    if(date('l') == 'Sunday' || date('l') == 'Domingo'){
                        $invoice['days_due_date'] = 1;
                    }
                }

                if($invoice->environment_asaas == 'Teste'){
                    $url = $invoice->asaas_url_test;
                    $at_asaas = $invoice->at_asaas_test;
                }else{
                    $url = $invoice->asaas_url_prod;
                    $at_asaas = $invoice->at_asaas_prod;
                }


                //Verifica se o cliente já está cadastrado e retona o ID
                $get_customer = Http::withHeaders([
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'access_token' => $at_asaas,
                    'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
                  ])->get($url.'v3/customers?cpfCnpj='.removeEspeciais($invoice['document']));

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
                            'name'                  =>  $invoice['name'],
                            'cpfCnpj'               =>  $invoice['document'],
                            'notificationDisabled'  =>  true,
                            'postalCode'            =>  $invoice['cep'],
                            'addressNumber'         =>  $invoice['number'],
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
                    'value'             =>  $invoice['price'],
                    'dueDate'           => $invoice['date_due'],
                    'description'       => $invoice['description'],
                    //'daysAfterDueDateToRegistrationCancellation'    => 40,
                    'externalReference' =>  $invoice['id'],
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

            $invoice = ViewInvoice::where('id',$invoice_id)->first();

            if($invoice->environment_asaas == 'Teste'){
                $url = $invoice->asaas_url_test;
                $at_asaas = $invoice->at_asaas_test;
            }else{
                $url = $invoice->asaas_url_prod;
                $at_asaas = $invoice->at_asaas_prod;
            }

            $headers = [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'access_token' => $at_asaas,
                'User-Agent' => 'CobrancaSegura/1.0 (Laravel API)'
            ];

            $customer_id = null;

            $get_customer = Http::withHeaders($headers)->get($url.'v3/customers?cpfCnpj='.removeEspeciais($invoice['document']));

            if ($get_customer->successful()) {
                $result_get_customer = $get_customer->json();
                if($result_get_customer['totalCount'] > 0){
                    $customer_id = $result_get_customer['data'][0]['id'];
                }else{
                    $post_customer = Http::withHeaders($headers)->post($url.'v3/customers',[
                        'name'                  =>  $invoice['name'],
                        'cpfCnpj'               =>  $invoice['document'],
                        'notificationDisabled'  =>  true,
                        'postalCode'            =>  $invoice['cep'],
                        'addressNumber'         =>  $invoice['number'],
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
                'value'             =>  $invoice['price'],
                'dueDate'           => $invoice['date_due'],
                'description'       => $invoice['description'],
                'externalReference' =>  $invoice['id'],
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
            $fileName = $invoice['user_id'].'_'.$invoice['id'].'.png';

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

    $invoice = ViewInvoice::where('transaction_id',$transaction_id)->first();

    if($invoice->environment_asaas == 'Teste'){
        $url        = $invoice->asaas_url_test;
        $at_asaas   = $invoice->at_asaas_test;
    }else{
        $url        = $invoice->asaas_url_prod;
        $at_asaas   = $invoice->at_asaas_prod;
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

    $invoice = ViewInvoice::where('transaction_id',$transaction_id)->first();

    if($invoice->environment_asaas == 'Teste'){
        $url        = $invoice->asaas_url_test;
        $at_asaas   = $invoice->at_asaas_test;
    }else{
        $url        = $invoice->asaas_url_prod;
        $at_asaas   = $invoice->at_asaas_prod;
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
