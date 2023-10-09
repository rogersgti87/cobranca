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

    $sql = "SELECT i.id,i.status,i.user_id,i.date_invoice,i.date_due,i.description,c.email,c.email2,c.phone,c.whatsapp,c.notificate_5_days,c.notificate_2_days,c.notificate_due,
    c.name,c.notification_whatsapp,c.notification_email,c.company,c.document,c.phone,c.address,c.number,c.complement,c.type,u.send_generate_invoice,
    c.district,c.city,c.state,c.cep,i.gateway_payment, i.payment_method,s.id AS service_id,s.name AS service_name,i.price,
    u.access_token_mp, u.company user_company, u.whatsapp user_whatsapp, u.image user_image, u.telephone user_telephone,
     u.email user_email, u.api_access_token_whatsapp,i.image_url_pix, i.pix_digitable, i.qrcode_pix_base64,u.inter_chave_pix,
     u.inter_host,u.inter_client_id,u.inter_client_secret,u.inter_scope,u.inter_crt_file,u.inter_key_file,u.inter_crt_file_webhook,
      i.billet_digitable, i.billet_base64, i.billet_url FROM invoices i
        INNER JOIN customer_services cs ON i.customer_service_id = cs.id
        INNER JOIN customers c ON  cs.customer_id = c.id
        INNER JOIN services  s ON  cs.service_id  = s.id
        INNER JOIN users u ON i.user_id = u.id
        WHERE NOT EXISTS (SELECT * FROM invoice_notifications b WHERE i.id = b.invoice_id AND CURRENT_DATE = cast(b.date as date))
        and i.status = 'Pendente' AND i.date_due <= CURDATE() + interval 5 DAY";

    $verifyInvoices = DB::select($sql);


    foreach($verifyInvoices as $invoice){


    if($invoice->date_due == Carbon::now()->format('Y-m-d') ){

        if($invoice->notificate_due == 's'){

        try {
            InvoiceNotification::Email($invoice->id);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }


        if(date('l') != 'Sunday'){
            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');

            if ($now->between($start, $end)) {
                try {
                    if($invoice->notification_whatsapp){
                        InvoiceNotification::Whatsapp($invoice->id);
                    }
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }
        }
    }
    }

    else if(Carbon::parse($invoice->date_due)->subDays(5)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){
        if($invoice->notificate_5_days == 's'){

        try {
            InvoiceNotification::Email($invoice->id);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');

            if ($now->between($start, $end)) {

        try {
            if($invoice->notification_whatsapp){
                InvoiceNotification::Whatsapp($invoice->id);
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        }
    }
}

    }
    else if(Carbon::parse($invoice->date_due)->subDays(2)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){

        if($invoice->notificate_2_days == 's'){
        try {
            InvoiceNotification::Email($invoice->id);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');

            if ($now->between($start, $end)) {

        try {
            if($invoice->notification_whatsapp){
                InvoiceNotification::Whatsapp($invoice->id);
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        }
    }
        }
    }else if($invoice->date_due < Carbon::now()->format('Y-m-d') ){

        try {
            InvoiceNotification::Email($invoice->id);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }


        if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');

            if ($now->between($start, $end)) {

        try {
            if($invoice->notification_whatsapp){
                InvoiceNotification::Whatsapp($invoice->id);
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        }
    }

    }



    }//Fim foreach




  }

}
