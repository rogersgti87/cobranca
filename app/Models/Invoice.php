<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use DB;
use InvoiceNotification;
use App\Models\User;

class Invoice extends Model
{


    public static function generateBilletPH($invoice_id){

        $invoice = DB::table('invoices as i')
                    ->select('i.id','c.email','c.email2','c.name','c.document','c.phone','c.notification_whatsapp','c.address','c.number','c.complement',
                    'c.district','c.city','c.state','c.cep','s.id as service_id','s.name as service_name','i.price as service_price','u.token_paghiper','u.key_paghiper','u.access_token_mp',
                    DB::raw("DATEDIFF (i.date_due,i.date_invoice) as days_due_date"))
                    ->join('customer_services as cs','i.customer_service_id','cs.id')
                    ->join('customers as c','cs.customer_id','c.id')
                    ->join('services as s','cs.service_id','s.id')
                    ->join('users as u','i.user_id','u.id')
                    ->where('i.id',$invoice_id)
                    ->first();

        //Gerar PIX
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
          ])->post('https://api.paghiper.com/transaction/create/',[
            'apiKey'            =>  $invoice->key_paghiper,
            'order_id'          =>  $invoice->id,
            'payer_email'       =>  $invoice->email,
            'payer_name'        =>  $invoice->name,
            'payer_cpf_cnpj'    =>  removeEspeciais($invoice->document),
            'payer_phone'       =>  $invoice->phone,
            'payer_street'      =>  $invoice->address,
            'payer_number'      =>  $invoice->number,
            'payer_complement'  =>  $invoice->complement,
            'payer_district'    =>  $invoice->district,
            'payer_city'        =>  $invoice->city,
            'payer_state'       =>  $invoice->state,
            'payer_zip_code'    =>  $invoice->cep,
            'type_bank_slip'    => 'boletoA4',
            'days_due_date'     =>  $invoice->days_due_date,
            'notification_url'  => 'https://cobrancasegura.com.br/webhook/paghiper',
            'late_payment_fine' => '1',// Percentual de multa após vencimento.
            'per_day_interest'  => true, // Juros após vencimento.
            'items' => array([
                'item_id'       => $invoice->service_id,
                'description'   => $invoice->service_name,
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice->service_price,100)
          ])
          ]);



        $result = $response->getBody();

        $result = json_decode($result)->create_request;

        if($result->result == 'reject'){
            return ['staus' => 'reject', 'message' => $result->response_message];
        }else{

            return ['status' => 'ok', 'transaction_id' => $result->transaction_id];
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
                    //$payment->date_of_expiration = Carbon::now()->addDays(40)->format('Y-m-d\TH:i:s') . '.000-04:00';
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


    public static function generatePixPH($invoice_id){

        $invoice = DB::table('invoices as i')
        ->select('i.id','c.email','c.email2','c.name','c.document','c.phone','c.notification_whatsapp','c.address','c.number','c.complement',
        'c.district','c.city','c.state','c.cep','s.id as service_id','s.name as service_name','i.price as service_price','u.token_paghiper','u.key_paghiper','u.access_token_mp',
        DB::raw("DATEDIFF (i.date_due,i.date_invoice) as days_due_date"))
        ->join('customer_services as cs','i.customer_service_id','cs.id')
        ->join('customers as c','cs.customer_id','c.id')
        ->join('services as s','cs.service_id','s.id')
        ->join('users as u','i.user_id','u.id')
        ->where('i.id',$invoice_id)
        ->first();



        //Gerar PIX
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
          ])->post('https://pix.paghiper.com/invoice/create/',[
            'apiKey'            =>  $invoice->key_paghiper,
            'order_id'          =>  $invoice->id,
            'payer_email'       =>  $invoice->email,
            'payer_name'        =>  $invoice->name,
            'payer_cpf_cnpj'    =>  $invoice->document,
            'days_due_date'     =>  90,
            'notification_url'  => 'https://cobrancasegura.com.br/webhook/paghiper',
            'items' => array([
                'item_id'       => $invoice->service_id,
                'description'   => $invoice->service_name,
                'quantity'      => 1,
                'price_cents'   => bcmul($invoice->service_price,100)
          ])
          ]);



        $result = $response->getBody();

        $result = json_decode($result)->pix_create_request;

        if($result->result == 'reject'){
            return ['staus' => 'reject', 'message' => $result->response_message];
        }else{

            return ['status' => 'ok', 'transaction_id' => $result->transaction_id];
        }


    }


    public static function verifyStatusBilletPH($user_id,$transaction_id){

      $user = User::where('id',$user_id)->first();

        //Consultar status da transação
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.paghiper.com/transaction/status/',[
            'token'             => $user->token_paghiper,
            'apiKey'            => $user->key_paghiper,
            'transaction_id'    => $transaction_id
        ]);

        $result = $response->getBody();
        return json_decode($result);

    }

    public static function verifyStatusPixMP($access_token, $transaction_id){

    \MercadoPago\SDK::setAccessToken($access_token);

    $payment = \MercadoPago\Payment::find_by_id($transaction_id);

    return $payment->point_of_interaction->transaction_data;

    }


    public static function verifyStatusPixPH($user_id,$transaction_id){


        $user = User::where('id',$user_id)->first();
        //Consultar status da transação
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://pix.paghiper.com/invoice/status/',[
            'token'             => $user->token_paghiper,
            'apiKey'            => $user->key_paghiper,
            'transaction_id'    => $transaction_id
        ]);

        $result = $response->getBody();
        return json_decode($result)->status_request;

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


}
