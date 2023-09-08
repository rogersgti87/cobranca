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
    \Log::info('Linha 59 - Retorno webhook paghiper: '.json_encode($data));
    $invoice = Invoice::select('invoices.transaction_id','users.token_paghiper','users.key_paghiper','invoices.payment_method')
                        ->join('users','users.id','invoices.user_id')
                        ->where('transaction_id',$data['transaction_id'])
                        ->where('invoices.status','Pendente')
                        ->orwhere('invoices.status','Processamento')
                        ->first();

    \Log::info('Linha 66: '.json_encode($invoice));

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

    $title = '';
    $message_notification = '';

    \Log::info('Result Status linha 93: '.$result->status);

    if($result->status == 'reserved'){
        $title = 'Fatura #'.$result->order_id;
        $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Processamento</b>';
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Processamento',
            'date_payment' =>   Null,
            'updated_at'   =>   Carbon::now()
        ]);
    }
    if($result->status == 'completed' || $result->status == 'paid'){
        $title = 'Fatura #'.$result->order_id;
        $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Pago</b>';
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Pago',
            'date_payment' =>   isset($data['paid_date']) ? date('d/m/Y', strtotime($data['paid_date'])) : Carbon::now(),
            'updated_at'   =>   Carbon::now()
        ]);
    }

    if($result->status == 'canceled' || $result->status == 'refunded'){
        $title = 'Fatura #'.$result->order_id;
        $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Cancelado</b>';
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Cancelado',
            'date_payment' =>   Null,
            'updated_at'   =>   Carbon::now()
        ]);
    }

        $invoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
        'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.type',
        'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
        'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
        'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company','users.inter_chave_pix',
        'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
        'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email','users.api_access_token_whatsapp')
        ->join('customer_services','invoices.customer_service_id','customer_services.id')
        ->join('customers','customer_services.customer_id','customers.id')
        ->join('services','customer_services.service_id','services.id')
        ->join('users','users.id','invoices.user_id')
        ->where('invoices.id',$result->order_id)
        ->where('invoices.transaction_id',$result->transaction_id)
        ->first();


        $details = [
            'type_send'                 => 'Confirm',
            'title'                     => $title,
            'message_customer'          => 'Olá '.$invoice->name.', tudo bem?',
            'message_notification'      => $message_notification,
            'logo'                      => 'https://cobrancasegura.com.br/'.$invoice->user_image,
            'company'                   => $invoice->user_company,
            'user_whatsapp'             => removeEspeciais($invoice->user_whatsapp),
            'user_telephone'            => removeEspeciais($invoice->user_telephone),
            'user_email'                => $invoice->user_email,
            'user_access_token_wp'      => $invoice->api_access_token_whatsapp,
            'user_id'                   => $invoice->user_id,
            'customer'                  => $invoice->name,
            'customer_email'            => $invoice->email,
            'customer_email2'           => $invoice->email2,
            'customer_whatsapp'         => removeEspeciais($invoice->whatsapp),
            'notification_whatsapp'     => $invoice->notification_whatsapp,
            'customer_company'          => $invoice->company,
            'date_invoice'              => date('d/m/Y', strtotime($invoice->date_invoice)),
            'date_due'                  => date('d/m/Y', strtotime($invoice->date_due)),
            'price'                     => number_format($invoice->price, 2,',','.'),
            'date_payment'              => $invoice->date_payment != null ? date('d/m/Y', strtotime($invoice->date_payment)) : '',
            'gateway_payment'           => $invoice->gateway_payment,
            'payment_method'            => $invoice->payment_method,
            'service'                   => $invoice->service_name.' - '.$invoice->description,
            'invoice'                   => $invoice->id,
            'status'                    => $invoice->status,
            'url_base'                  => url('/'),
            'status_payment'            => $invoice->status,
            'pix_emv'                   => $invoice->pix_digitable,
            'pix_qrcode_base64'         => $invoice->qrcode_pix_base64,
            'billet_digitable_line'     => $invoice->billet_digitable,
            'billet_url_slip_base64'    => $invoice->billet_base64,
            'billet_url_slip'           => $invoice->billet_url,
            'pix_qrcode_image_url'      => $invoice->image_url_pix
        ];


        $details['body']  = view('mails.invoice',$details)->render();

        InvoiceNotification::Email($details);

        if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:30');

            if ($now->between($start, $end)) {

                if($invoice->notification_whatsapp)
                    InvoiceNotification::Whatsapp($details);

            }
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
            \Log::info('Linha: 214 - '.$invoice);
    if($invoice != null){

        \MercadoPago\SDK::setAccessToken($invoice->access_token_mp);
        $payment = \MercadoPago\Payment::find_by_id($invoice->transaction_id);
        \Log::info('Linha: 219 - '.json_encode($invoice->transaction_id));
        \Log::info('Linha: 220 - '.json_encode($payment->status));

        $title = '';
        $message_notification = '';


        if($payment->status == 'approved'){
            \Log::info('Linha: 227 - '.json_encode($payment->status));
            $title = 'Fatura #'.$invoice->id;
            $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Pago</b>';
            $result_invoice = Invoice::where('id',$invoice->id)->where('transaction_id',$invoice->transaction_id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::parse(now())->format('Y-m-d'),
                'updated_at'   =>   Carbon::now()
            ]);
            \Log::info('Linha: 235 - '.json_encode($result_invoice));
        }

        if($payment->status == 'cancelled'){
            $title = 'Fatura #'.$invoice->id;
            $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Cancelado</b>';
            Invoice::where('id',$invoice->id)->where('transaction_id',$invoice->transaction_id)->update([
                'status'       =>   'Cancelado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
        }


        $invoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
        'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp',
        'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement','customers.type',
        'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
        'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company','users.inter_chave_pix',
        'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
        'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email','users.api_access_token_whatsapp')
        ->join('customer_services','invoices.customer_service_id','customer_services.id')
        ->join('customers','customer_services.customer_id','customers.id')
        ->join('services','customer_services.service_id','services.id')
        ->join('users','users.id','invoices.user_id')
        ->where('invoices.id',$invoice->id)
        ->where('invoices.transaction_id',$invoice->transaction_id)
        ->first();


        if($invoice != null){

            $details = [
                'type_send'                 => 'Confirm',
                'title'                     => $title,
                'message_customer'          => 'Olá '.$invoice->name.', tudo bem?',
                'message_notification'      => $message_notification,
                'logo'                      => 'https://cobrancasegura.com.br/'.$invoice->user_image,
                'company'                   => $invoice->user_company,
                'user_whatsapp'             => removeEspeciais($invoice->user_whatsapp),
                'user_telephone'            => removeEspeciais($invoice->user_telephone),
                'user_email'                => $invoice->user_email,
                'user_access_token_wp'      => $invoice->api_access_token_whatsapp,
                'user_id'                   => $invoice->user_id,
                'customer'                  => $invoice->name,
                'customer_email'            => $invoice->email,
                'customer_email2'           => $invoice->email2,
                'customer_whatsapp'         => removeEspeciais($invoice->whatsapp),
                'notification_whatsapp'     => $invoice->notification_whatsapp,
                'customer_company'          => $invoice->company,
                'date_invoice'              => date('d/m/Y', strtotime($invoice->date_invoice)),
                'date_due'                  => date('d/m/Y', strtotime($invoice->date_due)),
                'price'                     => number_format($invoice->price, 2,',','.'),
                'date_payment'              => $invoice->date_payment != null ? date('d/m/Y', strtotime($invoice->date_payment)) : '',
                'gateway_payment'           => $invoice->gateway_payment,
                'payment_method'            => $invoice->payment_method,
                'service'                   => $invoice->service_name.' - '.$invoice->description,
                'invoice'                   => $invoice->id,
                'status'                    => $invoice->status,
                'url_base'                  => url('/'),
                'status_payment'            => $invoice->status,
                'pix_qrcode_image_url'      =>  '',
                'pix_emv'                   =>  '',
                'pix_qrcode_wp'             =>  '',
                'billet_digitable_line'     =>  '',
                'billet_url_slip_pdf'       =>  '',
                'billet_url_slip'           =>  '',
                'pix_qrcode_base64'         => '',
                'billet_url_slip_base64'    => ''
            ];


            $details['body']  = view('mails.invoice',$details)->render();

            InvoiceNotification::Email($details);

            if($invoice->notification_whatsapp)
                InvoiceNotification::Whatsapp($details);


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

        $title = '';
        $message_notification = '';

        if($status == 'PAGO'){
            $title = 'Fatura';
            $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Pago</b>';
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::now(),
                'updated_at'   =>   Carbon::now()
            ]);
        }

        if($status == 'CANCELADO'){
            $title = 'Fatura';
            $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Cancelado</b>';
            Invoice::where('id',$result->id)->where('transaction_id',$result->transaction_id)->update([
                'status'       =>   'Cancelado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
        }

            $invoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
            'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.type',
            'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
            'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
            'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company','users.inter_chave_pix',
            'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
            'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email','users.api_access_token_whatsapp')
            ->join('customer_services','invoices.customer_service_id','customer_services.id')
            ->join('customers','customer_services.customer_id','customers.id')
            ->join('services','customer_services.service_id','services.id')
            ->join('users','users.id','invoices.user_id')
            ->where('invoices.id',$result->id)
            ->where('invoices.transaction_id',$result->transaction_id)
            ->first();


            $details = [
                'type_send'                 => 'Confirm',
                'title'                     => $title,
                'message_customer'          => 'Olá '.$invoice->name.', tudo bem?',
                'message_notification'      => $message_notification,
                'logo'                      => 'https://cobrancasegura.com.br/'.$invoice->user_image,
                'company'                   => $invoice->user_company,
                'user_whatsapp'             => removeEspeciais($invoice->user_whatsapp),
                'user_telephone'            => removeEspeciais($invoice->user_telephone),
                'user_email'                => $invoice->user_email,
                'user_access_token_wp'      => $invoice->api_access_token_whatsapp,
                'user_id'                   => $invoice->user_id,
                'customer'                  => $invoice->name,
                'customer_email'            => $invoice->email,
                'customer_email2'           => $invoice->email2,
                'customer_whatsapp'         => removeEspeciais($invoice->whatsapp),
                'notification_whatsapp'     => $invoice->notification_whatsapp,
                'customer_company'          => $invoice->company,
                'date_invoice'              => date('d/m/Y', strtotime($invoice->date_invoice)),
                'date_due'                  => date('d/m/Y', strtotime($invoice->date_due)),
                'price'                     => number_format($invoice->price, 2,',','.'),
                'date_payment'              => $invoice->date_payment != null ? date('d/m/Y', strtotime($invoice->date_payment)) : '',
                'gateway_payment'           => $invoice->gateway_payment,
                'payment_method'            => $invoice->payment_method,
                'service'                   => $invoice->service_name.' - '.$invoice->description,
                'invoice'                   => $invoice->id,
                'status'                    => $invoice->status,
                'url_base'                  => url('/'),
                'status_payment'            => $invoice->status,
                'pix_emv'                   => $invoice->pix_digitable,
                'pix_qrcode_base64'         => $invoice->qrcode_pix_base64,
                'billet_digitable_line'     => $invoice->billet_digitable,
                'billet_url_slip_base64'    => $invoice->billet_base64,
                'billet_url_slip'           => $invoice->billet_url,
                'pix_qrcode_image_url'      => $invoice->image_url_pix
            ];


            $details['body']  = view('mails.invoice',$details)->render();

            InvoiceNotification::Email($details);

            if(date('l') != 'Sunday'){

                $now = Carbon::now();
                $start = Carbon::createFromTimeString('08:00');
                $end = Carbon::createFromTimeString('19:30');

                if ($now->between($start, $end)) {

                    if($invoice->notification_whatsapp)
                        InvoiceNotification::Whatsapp($details);

                }
            }


    }


  }

  public function intermediumPix(Request $request) {
    $data = $request->all();
    \Log::info('Linha 449  - Retorno webhook intermedium: '.json_encode($data));

    $txid = $data['pix'][0]['txid'];
    \Log::info($txid);

    $result = Invoice::where('transaction_id',$txid)
    ->where('invoices.status','Pendente')
    ->orwhere('invoices.status','Processamento')
    ->first();
    \Log::info('Linha 458: '.$result);


    if($result != null){

        $title = '';
        $message_notification = '';
        \Log::info('Linha 465');


            $title = 'Fatura';
            $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>Pago</b>';
            $ok = Invoice::where('id',$result->id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::now(),
                'updated_at'   =>   Carbon::now()
            ]);

            \Log::info('Linha 476: '.$ok);

            $invoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
            'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.type',
            'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
            'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
            'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company','users.inter_chave_pix',
            'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
            'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email','users.api_access_token_whatsapp')
            ->join('customer_services','invoices.customer_service_id','customer_services.id')
            ->join('customers','customer_services.customer_id','customers.id')
            ->join('services','customer_services.service_id','services.id')
            ->join('users','users.id','invoices.user_id')
            ->where('invoices.id',$result->id)
            ->where('invoices.transaction_id',$result->transaction_id)
            ->first();


            $details = [
                'type_send'                 => 'Confirm',
                'title'                     => $title,
                'message_customer'          => 'Olá '.$invoice->name.', tudo bem?',
                'message_notification'      => $message_notification,
                'logo'                      => 'https://cobrancasegura.com.br/'.$invoice->user_image,
                'company'                   => $invoice->user_company,
                'user_whatsapp'             => removeEspeciais($invoice->user_whatsapp),
                'user_telephone'            => removeEspeciais($invoice->user_telephone),
                'user_email'                => $invoice->user_email,
                'user_access_token_wp'      => $invoice->api_access_token_whatsapp,
                'user_id'                   => $invoice->user_id,
                'customer'                  => $invoice->name,
                'customer_email'            => $invoice->email,
                'customer_email2'           => $invoice->email2,
                'customer_whatsapp'         => removeEspeciais($invoice->whatsapp),
                'notification_whatsapp'     => $invoice->notification_whatsapp,
                'customer_company'          => $invoice->company,
                'date_invoice'              => date('d/m/Y', strtotime($invoice->date_invoice)),
                'date_due'                  => date('d/m/Y', strtotime($invoice->date_due)),
                'price'                     => number_format($invoice->price, 2,',','.'),
                'date_payment'              => $invoice->date_payment != null ? date('d/m/Y', strtotime($invoice->date_payment)) : '',
                'gateway_payment'           => $invoice->gateway_payment,
                'payment_method'            => $invoice->payment_method,
                'service'                   => $invoice->service_name.' - '.$invoice->description,
                'invoice'                   => $invoice->id,
                'status'                    => $invoice->status,
                'url_base'                  => url('/'),
                'status_payment'            => $invoice->status,
                'pix_emv'                   => $invoice->pix_digitable,
                'pix_qrcode_base64'         => $invoice->qrcode_pix_base64,
                'billet_digitable_line'     => $invoice->billet_digitable,
                'billet_url_slip_base64'    => $invoice->billet_base64,
                'billet_url_slip'           => $invoice->billet_url,
                'pix_qrcode_image_url'      => $invoice->image_url_pix
            ];


            $details['body']  = view('mails.invoice',$details)->render();

            InvoiceNotification::Email($details);

            if(date('l') != 'Sunday'){

                $now = Carbon::now();
                $start = Carbon::createFromTimeString('08:00');
                $end = Carbon::createFromTimeString('19:30');

                if ($now->between($start, $end)) {

                    if($invoice->notification_whatsapp)
                        InvoiceNotification::Whatsapp($details);

                }
            }


    }


  }



public function teste(){

      $path = public_path('pix/'.'teste'.'.png');

     QrCode::format('png')->generate('Welcome to Makitweb', $path );

     return 1;


}



}
