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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WebHookController extends Controller
{

  public function __construct()
  {

  }


  public function email(Request $request)
  {
    $data = $request->all();

    if($data != null){

        $emailNotification = InvoiceNotification::where('email_id',$data['message-id'])->first();
        if($emailNotification != null){

            DB::table('email_events')->insert([
                'user_id'           => $emailNotification->user_id,
                'event'             =>  $data['event'],
                'email'             =>  $data['email'],
                'identification'    =>  $data['id'],
                'date'              =>  $data['date'],
                'message_id'        =>  $data['message-id'],
                'subject'           =>  isset($data['subject']) ? $data['subject'] : null,
                'tag'               =>  isset($data['tag']) ? $data['tag'] : null,
                'sending_ip'        =>  isset($data['sending_ip']) ? $data['sending_ip'] : null,
                'ts_epoch'          =>  isset($data['ts_epoch']) ? $data['ts_epoch'] : null,
                'link'              =>  isset($data['link']) ? $data['link'] : null,
                'reason'            =>  isset($data['reason']) ? $data['reason'] : null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);

        }

    }

  }

  public function paghiper(Request $request)
  {
    $data = $request->all();
    \Log::info('Linha 60 - Retorno webhook paghiper: '.json_encode($data));
    $invoice = Invoice::select('invoices.id','invoices.transaction_id','users.token_paghiper','users.key_paghiper','invoices.payment_method')
                        ->join('users','users.id','invoices.user_id')
                        ->where('transaction_id',$data['transaction_id'])
                        ->where('invoices.status','Pendente')
                        ->orwhere('invoices.status','Processamento')
                        ->first();

    \Log::info('Linha 68: '.json_encode($invoice));

    if($invoice != null){
        $url = '';
        if($invoice->payment_method == 'Pix'){
            $url = 'https://pix.paghiper.com/invoice/notification/';
        }else{
            $url = 'https://api.paghiper.com/transaction/notification/';
        }

    $response = Http::withHeaders([
        'accept' => 'application/json',
        'content-type' => 'application/json',
    ])->post($url,[
        'token'             => $invoice->token_paghiper,
        'apiKey'            => $data['apiKey'],
        'transaction_id'    => $data['transaction_id'],
        'notification_id'   => $data['notification_id']
    ]);

    $result = $response->getBody();
    \Log::info('Result linha 87: '.$result);
    $result = json_decode($result)->status_request;

    \Log::info('Result Status linha 93: '.$result->status);

    if($result->status == 'reserved'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Processamento',
            'date_payment' =>   Null,
            'updated_at'   =>   Carbon::now()
        ]);
        InvoiceNotification::Email($invoice->id);
        InvoiceNotification::Whatsapp($invoice->id);
    }
    if($result->status == 'completed' || $result->status == 'paid'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Pago',
            'date_payment' =>   isset($data['paid_date']) ? date('d/m/Y', strtotime($data['paid_date'])) : Carbon::now(),
            'updated_at'   =>   Carbon::now()
        ]);
        InvoiceNotification::Email($invoice->id);
        InvoiceNotification::Whatsapp($invoice->id);
    }

    if($result->status == 'canceled' || $result->status == 'refunded'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Cancelado',
            'date_payment' =>   Null,
            'updated_at'   =>   Carbon::now()
        ]);
        InvoiceNotification::Email($invoice->id);
        InvoiceNotification::Whatsapp($invoice->id);
    }





    }

  }


  public function mercadopago(Request $request){

    $data = $request->all();

    \Log::info($request->all());

    $invoice = Invoice::select('invoices.id as id','invoices.transaction_id','users.access_token_mp')
                ->join('users','users.id','invoices.user_id')
                ->where('transaction_id',$data['data']['id'])
                ->where('invoices.status','Pendente')
                ->first();
    if($invoice != null){

        \MercadoPago\SDK::setAccessToken($invoice->access_token_mp);
        $payment = \MercadoPago\Payment::find_by_id($invoice->transaction_id);

        if($payment->status == 'approved'){
            $result_invoice = Invoice::where('id',$invoice->id)->where('transaction_id',$invoice->transaction_id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::parse(now())->format('Y-m-d'),
                'updated_at'   =>   Carbon::now()
            ]);
            InvoiceNotification::Email($invoice->id);
            InvoiceNotification::Whatsapp($invoice->id);
        }

        if($payment->status == 'cancelled'){
            Invoice::where('id',$invoice->id)->where('transaction_id',$invoice->transaction_id)->update([
                'status'       =>   'Cancelado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
            InvoiceNotification::Email($invoice->id);
            InvoiceNotification::Whatsapp($invoice->id);
        }



    }



  }



  //Intermedium

  public function intermediumBillet(Request $request) {
    $data = $request->all();
    \Log::info('Linha 332  - Retorno webhook intermedium: '.json_encode($data));

    $seuNumero      = $data[0]['seuNumero'];
    $nossoNumero    = $data[0]['nossoNumero'];
    $status         = $data[0]['situacao'];

    $result = Invoice::where('id',$seuNumero)->where('transaction_id',$nossoNumero)
    ->where('invoices.status','Pendente')
    ->orwhere('invoices.status','Processamento')
    ->first();

    if($result != null){

        if($status == 'PAGO'){
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::now(),
                'updated_at'   =>   Carbon::now()
            ]);
            InvoiceNotification::Email($result->id);
            InvoiceNotification::Whatsapp($result->id);
        }

        if($status == 'CANCELADO'){
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Cancelado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
            InvoiceNotification::Email($result->id);
            InvoiceNotification::Whatsapp($result->id);
        }

        if($status == 'EXPIRADO'){
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Expirado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
        }

    }

  }


  public function intermediumBilletPix(Request $request) {
    $data = $request->all();
    \Log::info('Linha 222  - Retorno webhook intermedium boletopix: '.json_encode($data));

    $seuNumero      = $data[0]['seuNumero'];
    $codigoCobranca = $data[0]['codigoCobranca'];
    $status         = $data[0]['situacao'];

    $result = Invoice::where('id',$seuNumero)->where('transaction_id',$codigoCobranca)
    ->where('invoices.status','Pendente')
    ->orwhere('invoices.status','Processamento')
    ->first();

    if($result != null){

        if($status == 'RECEBIDO'){
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::now(),
                'updated_at'   =>   Carbon::now()
            ]);
            InvoiceNotification::Email($result->id);
            InvoiceNotification::Whatsapp($result->id);
        }

        if($status == 'CANCELADO'){
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Cancelado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
            InvoiceNotification::Email($result->id);
            InvoiceNotification::Whatsapp($result->id);
        }

        if($status == 'EXPIRADO'){
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Expirado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
        }

    }

  }

  public function intermediumPix(Request $request) {
    $data = $request->all();
    \Log::info('Linha 261  - Retorno webhook intermedium pix: '.json_encode($data));

    if(isset($data['pix'])){
        $txid = $data['pix'][0]['txid'];
    }else{
        $txid = $data[0]['codigoCobranca'];
        if($data['situacao'] != 'RECEBIDO'){
            return 'nao recebido!';
        }
    }


    $result = Invoice::where('transaction_id',$txid)
    ->where('invoices.status','Pendente')
    ->orwhere('invoices.status','Processamento')
    ->first();

    if($result != null){

        \Log::info('Linha 236');

            Invoice::where('id',$result->id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::now(),
                'updated_at'   =>   Carbon::now()
            ]);

            InvoiceNotification::Email($result->id);
            InvoiceNotification::Whatsapp($result->id);

    }


  }



public function teste(){

      $path = public_path('pix/'.'teste'.'.png');

     QrCode::format('png')->generate('Welcome to Makitweb', $path );

     return 1;


}



}
