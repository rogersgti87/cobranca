<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use Illuminate\Support\Facades\Http;

class RememberInvoiceCron extends Command
{

  protected $signature = 'rememberinvoice:cron';

  protected $description = 'Enviar notificações de invoices';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {

    $sql = "SELECT i.id,i.status,i.user_id,i.date_invoice,i.date_due,i.description,c.email,c.email2,c.phone,c.whatsapp,
    c.name,c.notification_whatsapp,c.company,c.document,c.phone,c.address,c.number,c.complement,c.type,
    c.district,c.city,c.state,c.cep,i.gateway_payment, i.payment_method,s.id AS service_id,s.name AS service_name,i.price,
    u.access_token_mp, u.company user_company, u.whatsapp user_whatsapp, u.image user_image, u.telephone user_telephone,
     u.email user_email, u.api_access_token_whatsapp,i.image_url_pix, i.pix_digitable, i.qrcode_pix_base64,
     u.inter_host,u.inter_client_id,u.inter_client_secret,u.inter_scope,u.inter_crt_file,u.inter_key_file,u.inter_crt_file_webhook,
      i.billet_digitable, i.billet_base64, i.billet_url FROM invoices i
        INNER JOIN customer_services cs ON i.customer_service_id = cs.id
        INNER JOIN customers c ON  cs.customer_id = c.id
        INNER JOIN services  s ON  cs.service_id  = s.id
        INNER JOIN users u ON i.user_id = u.id
        WHERE NOT EXISTS (SELECT * FROM invoice_notifications b WHERE i.id = b.invoice_id AND CURRENT_DATE = cast(b.date as date))
        and i.status = 'Pendente' AND i.date_due <= CURRENT_DATE +28";

    $verifyInvoices = DB::select($sql);


    foreach($verifyInvoices as $invoice){

    $details = [
        'type_send'                 => 'New',
        'title'                     => 'Nova fatura gerada',
        'message_customer'          => 'Olá '.$invoice->name.', tudo bem?',
        'message_notification'      => 'Esta é uma mensagem para notificá-lo(a) que foi gerado a <b>Fatura #'.$invoice->id.'</b>',
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
        'gateway_payment'           => $invoice->gateway_payment,
        'payment_method'            => $invoice->payment_method,
        'service'                   => $invoice->service_name. ' - ' .$invoice->description,
        'invoice'                   => $invoice->id,
        'status'                    => $invoice->status,
        'url_base'                  => url('/'),
        'pix_qrcode_image_url'      => $invoice->image_url_pix,
        'pix_emv'                   => $invoice->pix_digitable,
        'pix_qrcode_base64'         => $invoice->qrcode_pix_base64,
        'billet_digitable_line'     => $invoice->billet_digitable,
        'billet_url_slip_base64'    => $invoice->billet_base64,
        'billet_url_slip'           => $invoice->billet_url,
    ];

//verificar mensagem que vencerá em 5 dias

    $send_notification = false;

    if($invoice->date_due == Carbon::now()->format('Y-m-d') ){
        $details['title']         = 'Sua Fatura vence hoje';
        $details['message_notification'] = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura vence hoje.';
        $send_notification = true;

    }else if(Carbon::parse($invoice->date_due)->diffInDays(Carbon::now()->format('Y-m-d')) == 2 ){
        $details['title']         = 'Sua Fatura vencerá em 2 dias';
        $details['message_notification'] = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura vencerá em 2 dias.';
        $send_notification = true;

    }else if($invoice->date_due < Carbon::now()->format('Y-m-d') ){
        $details['title']         = 'Sua Fatura venceu';
        $details['message_notification'] = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura está vencida.';
        $send_notification = true;
    }

    $details['body']  = view('mails.invoice',$details)->render();


    if($send_notification == true){



        try {
            InvoiceNotification::Email($details);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        //if(date('l') != 'Sunday'){
            // $now = Carbon::now();
            // $start = Carbon::createFromTimeString('09:00');
            // $end = Carbon::createFromTimeString('19:30');

            // if ($now->between($start, $end)) {

                try {
                    if($invoice->notification_whatsapp){
                        InvoiceNotification::Whatsapp($details);
                    }
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            //}

    //}

    }



    }//Fim foreach


  }

}
