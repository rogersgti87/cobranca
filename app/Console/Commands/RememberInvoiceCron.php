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

    $sql = "SELECT i.id,i.user_id,i.date_invoice,i.date_due,i.description,c.email,c.email2,c.phone,c.name,c.notification_whatsapp,c.company,c.document,c.phone,c.address,c.number,c.complement,
    c.district,c.city,c.state,c.cep,i.gateway_payment, i.payment_method,s.id AS service_id,s.name AS service_name,i.price AS service_price, u.access_token_mp FROM invoices i
        INNER JOIN customer_services cs ON i.customer_service_id = cs.id
        INNER JOIN customers c ON  cs.customer_id = c.id
        INNER JOIN services  s ON  cs.service_id  = s.id
        INNER JOIN users u ON i.user_id = u.id
        WHERE NOT EXISTS (SELECT * FROM invoice_notifications b WHERE i.id = b.invoice_id AND CURRENT_DATE = b.date)
        and i.status = 'Pendente' AND i.date_due <= CURRENT_DATE +5";

    $verifyInvoices = DB::select($sql);


    foreach($verifyInvoices as $invoice){

        if($invoice->payment_method == 'Pix'){
            if($invoice->gateway_payment == 'Pag Hiper'){

                $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$invoice->id)->where('user_id',$invoice->user_id)->first();
                $getInfoPixPayment = Invoice::verifyStatusPixPH($invoice->user_id,$verifyTransaction->transaction_id);

                $image_pix_email    = $getInfoPixPayment->pix_code->qrcode_image_url;
                $image_pix_wp       = $getInfoPixPayment->pix_code->qrcode_base64;
                $qr_code_digitable  = $getInfoPixPayment->pix_code->emv;


            }elseif($invoice->gateway_payment == 'Mercado Pago'){

                $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$invoice->id)->where('user_id',$invoice->user_id)->first();
                $getInfoPixPayment = Invoice::verifyStatusPixMP($invoice->access_token_mp,$verifyTransaction->transaction_id);

                $image_pix_email    = 'https://cobrancasegura.com.br/pix/'.$invoice->uder_id.'_'.$invoice->id.'.png';
                $image_pix_wp       = $getInfoPixPayment->qr_code_base64;
                $qr_code_digitable  = $getInfoPixPayment->qr_code;

            }
        } elseif($invoice->payment_method == 'Boleto'){

            if($invoice->gateway_payment == 'Pag Hiper'){

                $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$invoice->id)->where('user_id',$invoice->user_id)->first();
                $getInfoBilletPayment   = Invoice::verifyStatusBilletPH($invoice->user_id ,$verifyTransaction->transaction_id);

            }elseif($invoice->gateway_payment == 'Mercado Pago'){
                //
            }


    }


    $details = [
        'type_send'                 => 'New',
        'title'                     => 'Nova fatura gerada',
        'message_customer'          => 'Olá '.$customer->name.', tudo bem?',
        'message_notification'      => 'Esta é uma mensagem para notificá-lo(a) que foi gerado a <b>Fatura #'.$invoice->id.'</b>',
        'logo'                      => 'https://cobrancasegura.com.br/'.$user->image,
        'company'                   => $user->company,
        'user_whatsapp'             => removeEspeciais($user->whatsapp),
        'user_telephone'            => removeEspeciais($user->telephone),
        'user_email'                => $user->email,
        'user_access_token_wp'      => $user->api_access_token_whatsapp,
        'user_id'                   => $invoice->user_id,
        'customer'                  => $customer->name,
        'customer_email'            => $customer->email,
        'customer_email2'           => $customer->email2,
        'customer_whatsapp'         => removeEspeciais($customer->whatsapp),
        'notification_whatsapp'     => $customer->notification_whatsapp,
        'customer_company'          => $customer->company,
        'date_invoice'              => date('d/m/Y', strtotime($invoice->date_invoice)),
        'date_due'                  => date('d/m/Y', strtotime($invoice->date_due)),
        'price'                     => number_format($invoice->price, 2,',','.'),
        'gateway_payment'           => $invoice->gateway_payment,
        'payment_method'            => $invoice->payment_method,
        'service'                   => $customer_service->name .' - '. $invoice->description,
        'invoice'                   => $invoice->id,
        'url_base'                  => url('/'),
        'pix_qrcode_image_url'      =>  '',
        'pix_emv'                   =>  '',
        'pix_qrcode_wp'             =>  '',
        'billet_digitable_line'     =>  '',
        'billet_url_slip_pdf'       =>  '',
        'billet_url_slip'           =>  '',
    ];

//verificar mensagem de data que vencerá em 5 dias

    if($invoice->date_end == Carbon::now()->format('Y-m-d') ){
        $details['title']         .= ' vence hoje';
        $details['text_remember'] .= 'Esta é uma mensagem para notificá-lo(a) que sua fatura vence hoje.';

    }else if($invoice->date_end < Carbon::now()->format('Y-m-d') ){
        $details['title']         .= ' venceu';
        $details['text_remember'] .= 'Esta é uma mensagem para notificá-lo(a) que sua fatura está vencida.';

    }


    if($invoice->payment_method == 'Boleto'){
        $details['billet_digitable_line'] = $getInfoBilletPayment->status_request->bank_slip->digitable_line;
        $details['billet_url_slip_pdf']   = $getInfoBilletPayment->status_request->bank_slip->url_slip_pdf;
        $details['billet_url_slip']       = $getInfoBilletPayment->status_request->bank_slip->url_slip;

    }else{

        $details['pix_qrcode_image_url']  = $image_pix_email;
        $details['pix_qrcode_wp']         = $image_pix_wp;
        $details['pix_emv']               = $qr_code_digitable;

    }


    $details['body']  = view('mails.invoice',$details)->render();

    InvoiceNotification::Email($details);

    if($invoice->notification_whatsapp)
        InvoiceNotification::Whatsapp($details);







    }//Fim foreach


  }

}
