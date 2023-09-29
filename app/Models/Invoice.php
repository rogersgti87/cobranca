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

        if(!file_exists(public_path('boleto')))
            \File::makeDirectory(public_path('boleto'));


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
                'item_id'       => $invoice['service_id'],
                'description'   => $invoice['service_name'],
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice['price'],100)
          ])
          ]);


          if ($response->successful()) {
            $result = $response->getBody();
            $result = json_decode($result)->create_request;

            $contents = Http::get($result->bank_slip->url_slip_pdf)->body();
            \File::put(public_path(). '/boleto/' .  $invoice->user_id.'_'.$invoice->id.'.'.'pdf', $contents);
            $billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoice->user_id.'_'.$invoice->id.'.pdf';
            $base64_pdf = chunk_split(base64_encode(file_get_contents($billet_pdf)));

            Invoice::where('id',$invoice_id)->update([
                'transaction_id'    =>  $result->transaction_id,
                'billet_url'        =>  $result->bank_slip->url_slip,
                'billet_base64'     =>  $base64_pdf,
                'billet_digitable'  =>  $result->bank_slip->digitable_line
            ]);

            if($result->result == 'reject'){
                \Log::info(json_encode($result->response_message));
                return ['status' => 'reject', 'message' => $result->response_message];
            }

        } else{
            \Log::info('Linha 96: '.json_encode($response->getBody()));
            return ['status' => 'reject', 'message' => 'Erro interno no servidor'];
        }


      }

      public static function generatePixPH($invoice_id){

        if(!file_exists(public_path('pix')))
            \File::makeDirectory(public_path('pix'));

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
                'item_id'       => $invoice['service_id'],
                'description'   => $invoice['service_name'],
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice['price'],100)
          ])
          ]);

        if ($response->successful()) {
            $result = $response->getBody();
            $result = json_decode($result)->pix_create_request;

            if($result->result == 'success'){

                Invoice::where('id',$invoice_id)->update([
                    'transaction_id'    => $result->transaction_id,
                    'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice['user_id'].'_'.$invoice['id'].'.png',
                    'pix_digitable'     => $result->pix_code->emv,
                    'qrcode_pix_base64' => $result->pix_code->qrcode_base64,
                ]);


                \File::put(public_path(). '/pix/' . $invoice['user_id'].'_'.$invoice['id'].'.'.'png', base64_decode($result->pix_code->qrcode_base64));

            }

            if($result->result == 'reject'){
                \Log::info('Linha 181: '.$result->response_message);
                return ['status' => 'reject', 'message' => $result->response_message];
            }

        }else{
            \Log::info('Linha 186: '.json_encode($response->getBody()));
            return ['status' => 'reject', 'message' => 'Erro interno no servidor'];
        }

    }



      public static function generatePixMP($invoice_id){

        if(!file_exists(public_path('pix')))
            \File::makeDirectory(public_path('pix'));

        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        \MercadoPago\SDK::setAccessToken($invoice->access_token_mp);

        $payment = new \MercadoPago\Payment();
        $payment->transaction_amount    = $invoice->service_price;
        $payment->statement_descriptor  = $invoice->company;
        $payment->description           = $invoice->service_name;
        $payment->payment_method_id     = "pix";
        $payment->notification_url      = 'https://cobrancasegura.com.br/webhook/mercadopago?source_news=webhooks';
        $payment->external_reference    = $invoice->id;
        $payment->date_of_expiration    = Carbon::now()->addDays(40)->format('Y-m-d\TH:i:s') . '.000-04:00';
        $payment->payer = array(
            "email"    => $invoice->email
        );

        $status_payment = $payment->save();

       $payment_id = $payment->id ? $payment->id : '';

       if($payment_id == ''){
            \Log::info(json_encode($status_payment));
            return ['status' => 'reject', 'message' => 'Erro ao Gerar Pix'];
        }else{

            \MercadoPago\SDK::setAccessToken($invoice->access_token_mp);

            try{
                //$invoice = Invoice::where('id',$invoice_id)->first();
                $payment = \MercadoPago\Payment::find_by_id($payment_id);

                Invoice::where('id',$invoice_id)->update([
                    'transaction_id'    => $payment_id,
                    'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                    'pix_digitable'     => $payment->point_of_interaction->transaction_data->qr_code,
                    'qrcode_pix_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                ]);

                $invoice = Invoice::where('id',$invoice_id)->first();

                \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', base64_decode($payment->point_of_interaction->transaction_data->qr_code_base64));

            } catch(\Exception $e){
                \Log::error($e->getMessage());
            }

        }

      }



      public static function generatePixIntermedium($invoice_id){

        if(!file_exists(public_path('pix')))
            \File::makeDirectory(public_path('pix'));

        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        $user = User::where('id',$invoice['user_id'])->first();

        $access_token = $user->access_token_inter;

        if($access_token == null){
            \Log::info('Access token inválido!');
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
            "solicitacaoPagador"            => $invoice['service_name']
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

            QrCode::format('png')->size(220)->generate($result_generate_pix->pixCopiaECola, public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png');

            $image_pix   = config()->get('app.url').'/pix/'.$invoice->user_id.'_'.$invoice->id.'.png';

            Invoice::where('id',$invoice_id)->update([
                'transaction_id'    => $result_generate_pix->txid,
                'image_url_pix'     => $image_pix,
                'pix_digitable'     => $result_generate_pix->pixCopiaECola,
                'qrcode_pix_base64' => base64_encode(file_get_contents($image_pix)),
            ]);

        }else{

            $result_generate_pix = $response_generate_pix->json();
            \Log::info($result_generate_pix);
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

            if(!file_exists(public_path('boleto')))
                \File::makeDirectory(public_path('boleto'));

            $invoice = ViewInvoice::where('id',$invoice_id)->first();

            $user = User::where('id',$invoice['user_id'])->first();

            $access_token = $user->access_token_inter;

            if($access_token == null){
                \Log::info('Access token inválido!');
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token inválido!']]];
            }


            if($user['inter_host'] == ''){
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'HOST banco inter não cadastrado!']]];
            }
            if($user['inter_client_id'] == ''){
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT ID banco inter não cadastrado!']]];
            }
            if($user['inter_client_secret'] == ''){
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'CLIENT SECRET banco inter não cadastrado!']]];
            }
            if($user['inter_crt_file'] == ''){
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado CRT banco inter não cadastrado!']]];
            }
            if(!file_exists(storage_path('/app/'.$user['inter_crt_file']))){
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado CRT banco inter não existe!']]];
            }
            if($user['inter_key_file'] == ''){
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado KEY banco inter não cadastrado!']]];
            }
            if(!file_exists(storage_path('/app/'.$user['inter_key_file']))){
                return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Certificado KEY banco inter não existe!']]];
            }


            $check_access_token = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v2/boletos/sumario?dataInicial=2023-01-01&dataFinal=2023-01-01');

            if ($check_access_token->unauthorized()) {
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

                    $invoice = ViewInvoice::where('id',$invoice_id)->first();
                }else{
                    return ['status' => 'reject', 'title' => 'Erro ao gerar Boleto', 'message' => [['razao' => 'Não autorizado', 'propriedade' => 'Access token Expirado ou inválido!']]];
                }
            }


            $date_multa = Carbon::parse($invoice['date_due'])->addDays(1);
            if(date('l') == 'Saturday' || date('l') == 'Sábado'){
                $date_multa = Carbon::parse($invoice['date_due'])->addDays(2);
            }

            $response_generate_billet = Http::withOptions([
                'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$invoice['inter_key_file']),
                ])->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
              ])->post($invoice['inter_host'].'cobranca/v2/boletos',[
                "seuNumero"=> $invoice['id'],
                "valorNominal"=> $invoice['price'],
                "dataVencimento"=> $invoice['date_due'],
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
                "codigoMulta"=> "PERCENTUAL",
                "data"=> $date_multa,
                "taxa"=> 1,
                "valor"=> 0
              ]
              ]);

            if ($response_generate_billet->successful()) {

                $result_generate_billet = $response_generate_billet->json();
                $result_generate_billet = json_decode($response_generate_billet);

                //Invoice::where('id',$invoice['id'])->where('user_id',$invoice['user_id'])->update(['transaction_id' => $result_generate_billet->nossoNumero]);

                //$invoice = Invoice::where('id',$invoice_id)->first();

                $response_pdf_billet = Http::withOptions(
                    [
                    'cert' => storage_path('/app/'.$user['inter_crt_file']),
                    'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
                    ]
                    )->withHeaders([
                    'Authorization' => 'Bearer ' . $access_token

                ])->get($invoice['inter_host'].'cobranca/v2/boletos/'.$result_generate_billet->nossoNumero.'/pdf');

                if ($response_pdf_billet->successful()) {

                    $responseBodyPdf = $response_pdf_billet->getBody();
                    $pdf = json_decode($responseBodyPdf)->pdf;

                    \File::put(public_path(). '/boleto/' . $invoice->user_id.'_'.$invoice->id.'.'.'pdf', base64_decode($pdf));
                    $billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoice->user_id.'_'.$invoice->id.'.pdf';

                    $invoice->update([
                        'transaction_id'    =>  $result_generate_billet->nossoNumero,
                        'billet_url'        =>  $billet_pdf,
                        'billet_base64'     =>  $pdf,
                        'billet_digitable'  =>  $result_generate_billet->linhaDigitavel
                    ]);

                }else{
                    \Log::info('Erro ao gerar pdf pagamento intermedium: '.$response_pdf_billet->json());
                }



            }else{
                $result_generate_billet = $response_generate_billet->json();
                \Log::info($result_generate_billet);
                return ['status' => 'reject', 'title' => $result_generate_billet['title'], 'message' => $result_generate_billet['violacoes']];
            }


        }




        public static function getBilletPDFIntermedium($invoice){

            \Log::info('Transaction_id: '.$invoice['transaction_id']);

            $response = Http::withOptions([
                'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$invoice['inter_key_file']),
            ])->asForm()->post($invoice['inter_host'].'oauth/v2/token', [
                'client_id' => $invoice['inter_client_id'],
                'client_secret' => $invoice['inter_client_secret'],
                'scope' => $invoice['inter_scope'],
                'grant_type' => 'client_credentials',
            ]);

            $responseBody = $response->body();
            $access_token = json_decode($responseBody)->access_token;

            \Log::info('access_token: '.$access_token);

            $response_pdf_billet = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$invoice['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token

            ])->get($invoice['inter_host'].'cobranca/v2/boletos/'.$invoice['transaction_id'].'/pdf');

            if ($response_pdf_billet->successful()) {

                $responseBodyPdf = $response_pdf_billet->getBody();

                \Log::info('Linha 383: '.json_encode($responseBodyPdf));

                $pdf = json_decode($responseBodyPdf)->pdf;

                return $pdf;

            }else{
                \Log::info('Erro ao gerar pdf pagamento intermedium: '.$response_pdf_billet->json());
            }


        }

        public static function cancelBilletIntermedium($user_id, $transaction_id){

            $user = User::where('id',$user_id)->first();

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

            ])->post($user->inter_host.'cobranca/v2/boletos/'.$transaction_id.'/cancelar',[
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

            ])->patch($user->inter_host.'pix/v2/cobv/'.$transaction_id,[
                "status" => "REMOVIDA_PELO_USUARIO_RECEBEDOR"
            ]);

            if ($response_cancel_billet->successful()) {
                return 'success';
            }else{
                \Log::info('Erro ao cancelar pagamento intermedium: '.$response_cancel_billet->json());
            }

            }


}
