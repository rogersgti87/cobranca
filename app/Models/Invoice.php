<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use DB;
use App\Models\User;
use Carbon\Carbon;

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

    public static function generateBilletPH($invoice){


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



        $result = $response->getBody();

        $result = json_decode($result)->create_request;

        if($result->result == 'reject'){
            \Log::info(json_encode($result->response_message));
            return ['status' => 'reject', 'message' => $result->response_message];
        }else{

            return ['status' => 'ok', 'transaction' => $result];
        }

      }



      public static function generatePixMP($invoice_id){

        $invoice = DB::table('invoices as i')
                    ->select('i.id','c.email','c.email2','c.name','c.document','c.phone','c.notification_whatsapp','c.address','c.number','c.complement',
                    'c.district','c.city','c.state','c.cep','s.id as service_id','s.name as service_name','i.price as service_price','u.token_paghiper',
                    'u.key_paghiper','u.access_token_mp','u.company',
                    DB::raw("DATEDIFF (i.date_due,i.date_invoice) as days_due_date"))
                    ->join('customer_services as cs','i.customer_service_id','cs.id')
                    ->join('customers as c','cs.customer_id','c.id')
                    ->join('services as s','cs.service_id','s.id')
                    ->join('users as u','i.user_id','u.id')
                    ->where('i.id',$invoice_id)
                    ->first();

                    \MercadoPago\SDK::setAccessToken($invoice->access_token_mp);

                    $payment = new \MercadoPago\Payment();
                    $payment->transaction_amount    = $invoice->service_price;
                    $payment->statement_descriptor  = $invoice->company;
                    $payment->description           = $invoice->service_name;
                    $payment->payment_method_id     = "pix";
                    $payment->notification_url      = 'https://cobrancasegura.com.br/webhook/mercadopago?source_news=webhooks';
                    $payment->external_reference    = $invoice->id;
                    $payment->date_of_expiration = Carbon::now()->addDays(40)->format('Y-m-d\TH:i:s') . '.000-04:00';
                    $payment->payer = array(
                        "email"    => $invoice->email
                    );

        $status_payment = $payment->save();
        \Log::info(json_encode($status_payment));

       $payment_id = $payment->id ? $payment->id : '';

       if($payment_id == ''){
            return ['status' => 'reject', 'message' => 'Erro ao Gerar Pix'];
        }else{
            return ['status' => 'ok', 'transaction_id' => $payment_id];
        }


      }


    public static function generatePixPH($invoice){

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



        $result = $response->getBody();

        $result = json_decode($result)->pix_create_request;

        if($result->result == 'reject'){
            return ['status' => 'reject', 'message' => $result->response_message];
        }else{

            return ['status' => 'ok', 'transaction' => $result];
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


        public static function generateBilletIntermedium($invoice){


                $date_multa = Carbon::parse($invoice['date_due'])->addDays(1);
                if(date('l') == 'Saturday' || date('l') == 'Sábado'){
                    $date_multa = Carbon::parse($invoice['date_due'])->addDays(2);
                }

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

            $response_generate_pix = Http::withOptions([
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

                Invoice::where('id',$invoice['id'])->where('user_id',$invoice['user_id'])->update(['transaction_id' => $result_generate_billet->nossoNumero]);

                return ['status' => 'ok', 'transaction' => $result_generate_billet];
            }else{
                $result_generate_billet = $response_generate_billet->json();
                \Log::info(json_decode($result_generate_billet));
                return ['status' => 'reject', 'message' => json_decode($result_generate_billet)];
            }



        }




        public static function getBilletPDFIntermedium($invoice){

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

            $response_pdf_billet = Http::withOptions(
                [
                'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$invoice['inter_key_file'])
                ]
                )->withHeaders([
                'Authorization' => 'Bearer ' . $access_token

            ])->get($invoice['inter_host'].'cobranca/v2/boletos/'.$invoice['transaction_id'].'/pdf');

            $responseBodyPdf = $response_pdf_billet->getBody();

            $pdf = json_decode($responseBodyPdf)->pdf;

            return $pdf;

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



            public static function generatePixIntermedium($invoice){

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

            $txid = $invoice['user_id'].$invoice['id'].date('Ymdhis');


           $body = [
                "calendario"                    => [
                    "dataDeVencimento"          => $invoice['date_due'],
                    "validadeAposVencimento"    => 30,
                ],
                "loc"                           => $invoice['id'],
                "devedor"                       => [
                    "logradouro"                => $invoice['address'],
                    "cidade"                    => $invoice['city'],
                    "uf"                        => $invoice['state'],
                    "cep"                       => removeEspeciais($invoice['cep']),
                    "nome"                      => $invoice['name'],

                  ],
                "valor"                         => [
                    "original"                  => $invoice['price'],
                    "juros"                     => [
                        "modalidade"            => 2,
                        "valorPerc"             => 1
                    ],
                ],
                "chave"                         => $document['chave_pix'],
                "solicitacaoPagador"            => $documeent['service_name']
                ];

            if($invoice['type'] == 'Física'){
                $body['pagador']['cpf'] = $invoice['document'];
            }else{
                $body['pagador']['cnpj'] = $invoice['document'];
            }



            $response_generate_pix = Http::withOptions([
                'cert' => storage_path('/app/'.$invoice['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$invoice['inter_key_file']),
                ])->withHeaders([
                'Authorization' => 'Bearer ' . $access_token
              ])->post($invoice['inter_host'].'pix/v2/'.$txid,$body);



            if ($response_generate_pix->successful()) {

                $result_generate_pix = $response_generate_pix->json();
                $result_generate_pix = json_decode($response_generate_pix);

                Invoice::where('id',$invoice['id'])->where('user_id',$invoice['user_id'])->update([
                    'transaction_id' => $result_generate_pix->txid,
                    'pix_digitable'  => $result_generate_pix->loc->location
                ]);

                return ['status' => 'ok', 'transaction' => $result_generate_pix];
            }else{
                $result_generate_pix = $response_generate_pix->json();
                \Log::info(json_decode($result_generate_pix));
                return ['status' => 'reject', 'message' => json_decode($result_generate_pix)];
            }



        }


}
