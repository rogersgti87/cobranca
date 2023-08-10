<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceNotification;

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

    $sql = "SELECT i.id,i.date_invoice,i.date_end,i.description,CONCAT(s.name, ' - ',cs.dominio) description_whatsapp,c.email,c.email2,c.phone,c.name,c.notification_whatsapp,c.company,c.document,c.phone,c.address,c.number,c.complement,
    c.district,c.city,c.state,c.cep,i.payment_method,s.id AS service_id,s.name AS service_name,i.price AS service_price FROM invoices i
        INNER JOIN customer_services cs ON i.customer_service_id = cs.id
        INNER JOIN customers c ON  cs.customer_id = c.id
        INNER JOIN services  s ON  cs.service_id  = s.id
        WHERE NOT EXISTS (SELECT * FROM invoice_notifications b WHERE i.id = b.invoice_id AND CURRENT_DATE = b.date)
        and i.status = 'nao_pago' AND i.date_end <= CURRENT_DATE";

   $verifyInvoices = DB::select($sql);


    foreach($verifyInvoices as $invoice){


        $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$invoice->id)->first();

        if($invoice->payment_method == 'Pix'){
          $getInfoPixPayment      = Invoice::verifyStatusPixPayment($verifyTransaction->transaction_id);
        }else{
          $getInfoBilletPayment   = Invoice::verifyStatusBilletPayment($verifyTransaction->transaction_id);
        }


        $details = [
            'title'                     =>  'Sua fatura',
            'text_remember'             =>  '',
            'customer'                  => $invoice->name,
            'customer_email'            => $invoice->email,
            'customer_email2'           => $invoice->email2,
            'customer_phone'            => $invoice->phone,
            'notification_whatsapp'     => $invoice->notification_whatsapp,
            'company'                   => $invoice->company,
            'data_fatura'               => date('d/m/Y', strtotime($invoice->date_invoice)),
            'data_vencimento'           => date('d/m/Y', strtotime($invoice->date_end)),
            'proxima_data_vencimento'   => date('d/m/Y', strtotime($invoice->date_end)),
            'price'                     => number_format($invoice->service_price, 2,',','.'),
            'payment_method'            => $invoice->payment_method,
            'description'               => $invoice->description,
            'description_whatsapp'      => $invoice->description_whatsapp,
            'invoice_id'                => $invoice->id,
            'url_base'                  => url('/'),
            'pix_qrcode_image_url'      =>  '',
            'pix_emv'                   =>  '',
            'billet_digitable_line'     =>  '',
            'billet_url_slip_pdf'       =>  '',
            'billet_url_slip'           =>  '',
        ];

        if($invoice->payment_method == 'Boleto'){
            $details['billet_digitable_line'] = $getInfoBilletPayment->status_request->bank_slip->digitable_line;
            $details['billet_url_slip_pdf']   = $getInfoBilletPayment->status_request->bank_slip->url_slip_pdf;
            $details['billet_url_slip']       = $getInfoBilletPayment->status_request->bank_slip->url_slip;
        }else{
            $details['pix_qrcode_image_url']  = $getInfoPixPayment->qr_code_base64;
            $details['pix_emv']               = $getInfoPixPayment->qr_code;
        }

        if($invoice->date_end == Carbon::now()->format('Y-m-d') ){
            $details['title']         .= ' vence hoje';
            $details['text_remember'] .= 'Esta é uma mensagem para notificá-lo(a) que sua fatura vence hoje.';

        }else if($invoice->date_end < Carbon::now()->format('Y-m-d') ){
            $details['title']         .= ' venceu';
            $details['text_remember'] .= 'Esta é uma mensagem para notificá-lo(a) que sua fatura está vencida.';

        }

        $details['body']  = view('mails.rememberinvoice',$details)->render();

        InvoiceNotification::sendNotification($details);

    }//Fim foreach


  }

}
