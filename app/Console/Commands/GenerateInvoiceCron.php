<?php

namespace App\Console\Commands;

use App\Jobs\sendInvoice;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;


class GenerateInvoiceCron extends Command
{

  protected $signature = 'generateinvoice:cron';

  protected $description = 'Gerar Faturas a vencer';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {

\Log::info('Job Generate invoice.');

$users = User::where('status','Ativo')->get();

foreach($users as $user){

    $queueSize = Queue::size($user->id);

    if ($queueSize > 0) {
        //$this->info('A Fila já tem trabalho pendente');
        return 'A Fila já tem trabalho pendente';
    }

$sql = "SELECT DATE_ADD(CONCAT(YEAR(a.start_billing),'-',MONTH(a.start_billing),'-',a.day_due), INTERVAL TIMESTAMPDIFF(month, a.start_billing, now()) + 1 MONTH) as date_due, CURDATE(),
a.id, a.user_id, c.name customer,c.email,c.email2,c.phone, c.notification_whatsapp,c.notification_email, c.company, a.description,a.price, u.access_token_mp,c.type,
    u.inter_host,u.inter_client_id,u.inter_client_secret,u.inter_scope,u.inter_crt_file,u.inter_key_file,u.inter_crt_file_webhook,u.inter_chave_pix,
    a.gateway_payment,a.payment_method,a.period, CURRENT_DATE date_invoice,
    a.status, 'Pendente',CURRENT_TIMESTAMP created_at,CURRENT_TIMESTAMP updated_at FROM customer_services a
    INNER JOIN customers c ON a.customer_id = c.id
    INNER JOIN services s ON a.service_id = s.id
    INNER JOIN users u ON a.user_id = u.id
    WHERE NOT EXISTS (SELECT * FROM invoices b WHERE a.id = b.customer_service_id AND b.date_invoice = CURRENT_DATE) AND a.status = 'Ativo' and a.period = 'Recorrente'
    and u.day_generate_invoice = day(CURDATE())
    and CURDATE() >= a.start_billing AND (a.end_billing >= CURDATE() OR a.end_billing IS NULL)";

    $verifyInvoices = DB::select($sql);

    $verifyInvoices = collect($verifyInvoices);

    if($verifyInvoices != null){
        $count = 1;
        foreach($verifyInvoices as $vInvoice){

            //foreach ($chunk as $vInvoice) {
                $job = new sendInvoice($vInvoice);
                $job->onQueue($vInvoice->user_id);
                dispatch($job);
            //}

            if($count > 5){
                //dispatch(new GenerateInvoiceCron())->delay(now()->addMinutes(1));
                //dispatch(new GenerateInvoiceCron());
                //$this->info('Notificação enviada.');
                return 'Notificação enviada.';
            }

            $count++;

         }
         //$this->info('Notificações em fila iniciadas.');
         \Log::info('Notificações em fila iniciadas.');
    }

    }//end foreach users


  }
}
