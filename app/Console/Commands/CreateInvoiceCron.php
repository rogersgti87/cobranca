<?php

namespace App\Console\Commands;

use App\Jobs\sendInvoice;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Models\InvoiceNotification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class CreateInvoiceCron extends Command
{

  protected $signature = 'createinvoice:cron';

  protected $description = 'Criar Faturas';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {

// $sql = "SELECT
// case
//  when DATE_ADD(CONCAT(YEAR(a.start_billing),'-',MONTH(a.start_billing),'-',a.day_due), INTERVAL TIMESTAMPDIFF(month, a.start_billing, now()) + 1 MONTH) is null THEN last_day(CURDATE() + INTERVAL 1 MONTH)
//  else
//  DATE_ADD(CONCAT(YEAR(a.start_billing),'-',MONTH(a.start_billing),'-',a.day_due), INTERVAL TIMESTAMPDIFF(month, a.start_billing, now()) + 1 MONTH)
//  end date_due,
//  CURDATE(),
// a.id, a.user_id, c.id customer_id, c.name customer,c.email,c.email2,c.phone, c.notification_whatsapp,c.notification_email, c.company, a.description,a.price, u.access_token_mp,c.type,
//     u.inter_host,u.inter_client_id,u.inter_client_secret,u.inter_scope,u.inter_crt_file,u.inter_key_file,u.inter_crt_file_webhook,u.inter_chave_pix,
//     a.gateway_payment,a.payment_method,a.period, CURRENT_DATE date_invoice,
//     a.status, 'Pendente',CURRENT_TIMESTAMP created_at,CURRENT_TIMESTAMP updated_at FROM customer_services a
//     INNER JOIN customers c ON a.customer_id = c.id
//     INNER JOIN services s ON a.service_id = s.id
//     INNER JOIN users u ON a.user_id = u.id
//     WHERE NOT EXISTS (SELECT * FROM invoices b WHERE a.id = b.customer_service_id AND b.date_invoice = CURRENT_DATE) AND a.status = 'Ativo' and a.period = 'Recorrente'
//     and u.day_generate_invoice = day(CURDATE())
//     and CURDATE() >= a.start_billing AND (a.end_billing >= CURDATE() OR a.end_billing IS NULL) limit 5000";

$sql = "SELECT
case
 when DATE_ADD(CONCAT(YEAR(a.start_billing),'-',MONTH(a.start_billing),'-',a.day_due), INTERVAL TIMESTAMPDIFF(month, a.start_billing, now()) + 1 MONTH) is null THEN last_day(CURDATE() + INTERVAL 1 MONTH)
 else
 DATE_ADD(CONCAT(YEAR(a.start_billing),'-',MONTH(a.start_billing),'-',a.day_due), INTERVAL TIMESTAMPDIFF(month, a.start_billing, now()) + 1 MONTH)
 end date_due,
 CURDATE(),
a.id, a.user_id, c.name customer,c.email,c.email2,c.phone, c.notification_whatsapp,c.notification_email, c.company, a.description,a.price, u.access_token_mp,c.type,
    u.inter_host,u.inter_client_id,u.inter_client_secret,u.inter_scope,u.inter_crt_file,u.inter_key_file,u.inter_crt_file_webhook,u.inter_chave_pix,
    a.gateway_payment,a.payment_method,a.period, CURRENT_DATE date_invoice,
    a.status, 'Pendente',CURRENT_TIMESTAMP created_at,CURRENT_TIMESTAMP updated_at FROM customer_services a
    INNER JOIN customers c ON a.customer_id = c.id
    INNER JOIN services s ON a.service_id = s.id
    INNER JOIN users u ON a.user_id = u.id
    WHERE NOT EXISTS (SELECT * FROM invoices b WHERE a.id = b.customer_service_id AND b.date_invoice = CURRENT_DATE) AND a.status = 'Ativo' and a.period = 'Recorrente'
   and a.customer_id = 61";

    $verifyInvoices = DB::select($sql);

    $verifyInvoices = collect($verifyInvoices);

    if($verifyInvoices != null){

        foreach($verifyInvoices as $vInvoice){

                $newInvoice = DB::table('invoices')->insertGetId([
                    'user_id'               => $vInvoice->user_id,
                    'customer_id'           => $vInvoice->customer_id,
                    'customer_service_id'   => $vInvoice->id,
                    'description'           => $vInvoice->description,
                    'price'                 => $vInvoice->price,
                    'gateway_payment'       => $vInvoice->gateway_payment,
                    'payment_method'        => $vInvoice->payment_method,
                    'date_invoice'          => $vInvoice->date_invoice,
                    'date_due'              => $vInvoice->date_due,
                    'date_payment'          => null,
                    'status'                => 'Gerando',
                    'created_at'            => $vInvoice->created_at,
                    'updated_at'            => $vInvoice->updated_at
                ]);
         }

    }

  }
}
