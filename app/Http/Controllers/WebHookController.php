<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Config;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use Illuminate\Support\Facades\Http;
use DB;

class WebHookController extends Controller
{

  public function __construct()
  {

  }


  public function index(Request $request)
  {
    $data = $request->all();
    if($data != null){
        foreach($data as $result){
            DB::table('email_events')->insert([
                'event'         =>  $result['event'],
                'timestamp'     =>  Carbon::createFromTimestamp($result['timestamp'])->format('Y-m-d H:i:s'),
                'message_id'    =>  $result['message_id'],
                'recipient'     =>  $result['recipient'],
                'sender'        =>  $result['sender'],
                'subject'       =>  $result['subject'],
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);
        }
    }

  }

  public function paghiper(Request $request)
  {
    $data = $request->all();
    $api_token  = 'GUY0NUU1AA4EBWID21B36INWY14GR9Z84X9SS3U2DZHO';
    $api_key    = 'apk_49587512-BbMWUgPOyyjwePnrDopJtToAMHoEpZCq';

    $invoice = Invoice::where('transaction_id',$data['transaction_id'])->where('status','nao_pago')->first();

    if($invoice != null){

        $url = 'https://api.paghiper.com/transaction/notification/';

    $response = Http::withHeaders([
        'accept' => 'application/json',
        'content-type' => 'application/json',
    ])->post($url,[
        'token'             => $api_token,
        'apiKey'            => $data['apiKey'],
        'transaction_id'    => $data['transaction_id'],
        'notification_id'   => $data['notification_id']
    ]);

    $result = $response->getBody();
    \Log::info($result);
    $result = json_decode($result)->status_request;


    if($result->status == 'completed' || $result->status == 'paid' || $result->status == 'reserved'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'pago',
            'date_payment' =>   isset($data['paid_date']) ? date('d/m/Y', strtotime($data['paid_date'])) : Carbon::now(),
            'updated_at'   =>   Carbon::now()
        ]);


        $invoice = DB::table('invoices as i')
        ->select('i.id','i.date_invoice','i.date_end','i.description','i.date_payment','c.notification_whatsapp','c.email','c.email2','c.name','c.company','c.document','c.phone','c.address','c.number','c.complement',
        'c.district','c.city','c.state','c.cep','c.payment_method','s.id as service_id','s.name as service_name','i.price as service_price','cs.dominio')
        ->join('customer_services as cs','i.customer_service_id','cs.id')
        ->join('customers as c','cs.customer_id','c.id')
        ->join('services as s','cs.service_id','s.id')
        ->where('i.id',$result->order_id)
        ->where('transaction_id',$result->transaction_id)
        ->first();

        $details = [
            'title'                     => 'Confirmação de Pagamento',
            'customer'                  => $invoice->name,
            'customer_email'            => $invoice->email,
            'customer_email2'           => $invoice->email2,
            'customer_phone'            => $invoice->phone,
            'notification_whatsapp'     => $invoice->notification_whatsapp,
            'company'                   => $invoice->company,
            'data_fatura'               => date('d/m/Y', strtotime($invoice->date_invoice)),
            'data_vencimento'           => date('d/m/Y', strtotime($invoice->date_end)),
            'data_pagamento'            => date('d/m/Y', strtotime($invoice->date_payment)),
            'price'                     => number_format($invoice->service_price, 2),
            'payment_method'            => $invoice->payment_method,
            'description'               => $invoice->description,
            'description_whatsapp'      => $invoice->service_name . ' - ' . $invoice->dominio,
            'invoice_id'                => $invoice->id,
            'status_payment'            => 'Pago',
            'url_base'                  => url('/')
        ];


        $details['body']  = view('mails.payinvoice',$details)->render();

        InvoiceNotification::sendNotificationConfirm($details);


    }else if($result->status == 'canceled' || $result->status == 'refunded'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'cancelado',
            'date_payment' =>   Null,
            'updated_at'   =>   Carbon::now()
        ]);
    }

    }

  }


  public function mercadopago(Request $request){

    $data = $request->all();

    \Log::info($request->all());

    $invoice = Invoice::where('transaction_id',$data['data']['id'])->where('status','nao_pago')->first();

    if($invoice != null){


        \MercadoPago\SDK::setAccessToken('APP_USR-6577696952434644-080712-6d90a29d25117994829ffa1c31f661fe-74837694');
        $payment = \MercadoPago\Payment::find_by_id($invoice->transaction_id);

        if($payment->status == 'approved'){
            Invoice::where('id',$invoice->id)->update([
                'status'       =>   'pago',
                'date_payment' =>   Carbon::now(),
                'updated_at'   =>   Carbon::now()
            ]);


            $invoice = DB::table('invoices as i')
            ->select('i.id','i.date_invoice','i.date_end','i.description','i.date_payment','c.notification_whatsapp','c.email','c.email2','c.name','c.company','c.document','c.phone','c.address','c.number','c.complement',
            'c.district','c.city','c.state','c.cep','c.payment_method','s.id as service_id','s.name as service_name','i.price as service_price','cs.dominio')
            ->join('customer_services as cs','i.customer_service_id','cs.id')
            ->join('customers as c','cs.customer_id','c.id')
            ->join('services as s','cs.service_id','s.id')
            ->where('i.id',$invoice->id)
            ->first();

            $details = [
                'title'                     => 'Confirmação de Pagamento',
                'customer'                  => $invoice->name,
                'customer_email'            => $invoice->email,
                'customer_email2'           => $invoice->email2,
                'customer_phone'            => $invoice->phone,
                'notification_whatsapp'     => $invoice->notification_whatsapp,
                'company'                   => $invoice->company,
                'data_fatura'               => date('d/m/Y', strtotime($invoice->date_invoice)),
                'data_vencimento'           => date('d/m/Y', strtotime($invoice->date_end)),
                'data_pagamento'            => date('d/m/Y', strtotime($invoice->date_payment)),
                'price'                     => number_format($invoice->service_price, 2),
                'payment_method'            => $invoice->payment_method,
                'description'               => $invoice->description,
                'description_whatsapp'      => $invoice->service_name . ' - ' . $invoice->dominio,
                'invoice_id'                => $invoice->id,
                'status_payment'            => 'Pago',
                'url_base'                  => url('/')
            ];


            $details['body']  = view('mails.payinvoice',$details)->render();

            InvoiceNotification::sendNotificationConfirm($details);


        }else if($payment->status == 'cancelled'){
            Invoice::where('id',$invoice->id)->update([
                'status'       =>   'cancelado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
        }


    }



  }

  public function backups(Request $request,$user){
    $data = $request->all();
    if($data['token'] == 'abc,1234'){
        return DB::table('customer_backups')->where('user',$user)->get();
    }

  }

  public function whatsappMessage(Request $request){

    // $config = DB::table('configs')->where('id',1)->first();

    // $data = $request->all();
    // \Log::info(json_encode($data));

    // if($data != null){

    // foreach ($data as $result){

    //     //dd($result['data']['body'], $result['data']['sender']['shortName']);
    //     \Log::info($result['data']['body']);

    //     if($result['data']['body'] == 'Teste'){

    //         //\Log::info(preg_replace('/[^0-9]/', '', $result['to']));
    //         //\Log::info($result['body']);
    //         //\Log::info(print_r($result['sender']['shortName']));

    //         $response = Http::withHeaders([
    //             "Content-Type"  => "application/json",
    //             "SecretKey"     =>  $config->api_brasil_secret_key,
    //             "PublicToken"   =>  $config->api_brasil_public_token,
    //             "DeviceToken"   =>  $config->api_brasil_device_token
    //         ])->withToken($config->api_brasil_bearer_token)
    //         ->post($config->api_brasil_host.'/whatsapp/sendText',[
    //             "number" => preg_replace('/[^0-9]/', '', $result['data']['to']),
    //             "text"   => 'Olá ,'. $result['data']['sender']['shortName'].'\n Você digitou a palavara '.$result['data']['body']
    //         ]);

    //         $result = $response->getBody();

    //         \Log::info($result);


    //     }

    // }

    // }

  }


}
