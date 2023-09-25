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

$users = User::where('status','Ativo')->get();

foreach($users as $user){

    $queueSize = Queue::size($user->id);

    if ($queueSize > 0) {
        return 'A Fila já tem trabalho pendente';
    }

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
    and u.day_generate_invoice = day(CURDATE())
    and CURDATE() >= a.start_billing AND (a.end_billing >= CURDATE() OR a.end_billing IS NULL) limit 1";

    $verifyInvoices = DB::select($sql);

    $verifyInvoices = collect($verifyInvoices);

    if($verifyInvoices != null){

        foreach($verifyInvoices as $vInvoice){
                // $job = new sendInvoice($vInvoice);
                // $job->onQueue($vInvoice->user_id);
                // dispatch($job);

                $newInvoice = DB::table('invoices')->insertGetId([
                    'user_id'               => $vInvoice->user_id,
                    'customer_service_id'   => $vInvoice->id,
                    'description'           => $vInvoice->description,
                    'price'                 => $vInvoice->price,
                    'gateway_payment'       => $vInvoice->gateway_payment,
                    'payment_method'        => $vInvoice->payment_method,
                    'date_invoice'          => $vInvoice->date_invoice,
                    'date_due'              => $vInvoice->date_due,
                    'date_payment'          => null,
                    'status'                => 'Pendente',
                    'created_at'            => $vInvoice->created_at,
                    'updated_at'            => $vInvoice->updated_at
                ]);


                $invoice =Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
                'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.notification_email','customers.type',
                'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
                'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
                'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company',
                'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email','users.send_generate_invoice',
                'users.api_access_token_whatsapp','users.token_paghiper','users.key_paghiper','invoices.image_url_pix','invoices.pix_digitable','invoices.transaction_id',
                'invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url','users.inter_chave_pix',
                'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
                DB::raw("DATEDIFF (invoices.date_due,invoices.date_invoice) as days_due_date"))
                ->join('customer_services','invoices.customer_service_id','customer_services.id')
                ->join('customers','customer_services.customer_id','customers.id')
                ->join('services','customer_services.service_id','services.id')
                ->join('users','users.id','invoices.user_id')
                ->where('invoices.id',$newInvoice)
                ->where('invoices.user_id',$vInvoice->user_id)
                ->first();


                if($invoice->payment_method == 'Pix'){
                    if($invoice->gateway_payment == 'Pag Hiper'){
                        try {
                        $generatePixPH = Invoice::generatePixPH($invoice);
                        if($generatePixPH['status'] == 'reject'){
                            \Log::info($generatePixPH['message']);
                        }



                            if(!file_exists(public_path('pix')))
                                \File::makeDirectory(public_path('pix'));

                            \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', base64_decode($generatePixPH['transaction']->pix_code->qrcode_base64));


                            Invoice::where('id',$newInvoice)->where('user_id',$invoice->user_id)->update([
                                'transaction_id'    => $generatePixPH['transaction']->transaction_id,
                                'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                                'pix_digitable'     => $generatePixPH['transaction']->pix_code->emv,
                                'qrcode_pix_base64' => $generatePixPH['transaction']->pix_code->qrcode_base64,
                            ]);

                        } catch (\Exception $e) {
                            \Log::error($e->getMessage());
                            Invoice::where('id',$newInvoice)->delete();
                        }

                    }elseif($invoice->gateway_payment == 'Mercado Pago'){
                        try {
                        $generatePixMP = Invoice::generatePixMP($invoice->id);
                        if($generatePixMP['status'] == 'reject'){
                            \Log::info($generatePixMP['message']);
                        }


                            $getInfoPixMP = Invoice::verifyStatusPixMP($invoice->access_token_mp,$generatePixMP['transaction_id']);

                            if(!file_exists(public_path('pix')))
                                \File::makeDirectory(public_path('pix'));

                            \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', base64_decode($getInfoPixMP->qr_code_base64));

                            Invoice::where('id',$newInvoice)->where('user_id',$invoice->user_id)->update([
                                'transaction_id'    => $generatePixMP['transaction_id'],
                                'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                                'pix_digitable'     => $getInfoPixMP->qr_code,
                                'qrcode_pix_base64' => $getInfoPixMP->qr_code_base64,
                            ]);

                        } catch (\Exception $e) {
                            \Log::error($e->getMessage());
                            Invoice::where('id',$newInvoice)->delete();
                        }



                    } elseif($invoice->gateway_payment == 'Intermedium'){
                        try {
                        $generatePixIntermedium = Invoice::generatePixIntermedium($invoice);
                        if($generatePixIntermedium['status'] == 'reject'){
                            // $msgInterPix = '';
                            // foreach($generatePixIntermedium['message'] as $messageInterPix){
                            //     $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                            // }

                            \Log::info(json_encode($generatePixIntermedium['message']));
                            Invoice::where('id',$newInvoice)->delete();
                        }
                        try {

                            if(!file_exists(public_path('pix')))
                                \File::makeDirectory(public_path('pix'));

                            QrCode::format('png')->size(220)->generate($generatePixIntermedium['transaction']->pixCopiaECola, public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png');

                            //$imageQRCODE = 'https://gerarqrcodepix.com.br/api/v1?brcode='.$generatePixIntermedium['transaction']->pixCopiaECola;

                            //dd(\File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', file_get_contents($imageQRCODE)));

                            $image_pix   = config()->get('app.url').'/pix/'.$invoice->user_id.'_'.$invoice->id.'.png';

                            $invoice->update([
                                'image_url_pix'     => $image_pix,
                                'pix_digitable'     => $generatePixIntermedium['transaction']->pixCopiaECola,
                                'qrcode_pix_base64' => base64_encode(file_get_contents($image_pix)),
                            ]);

                        } catch (\Exception $e) {
                            Invoice::where('id',$newInvoice)->delete();
                            \Log::error($e->getMessage());
                            //return response()->json($e->getMessage(), 422);
                        }
                    } catch (\Exception $e) {
                        \Log::error($e->getMessage());
                    }
                    }
                } elseif($invoice->payment_method == 'Boleto'){

                    if($invoice->gateway_payment == 'Pag Hiper'){
                        try {
                        $generateBilletPH = Invoice::generateBilletPH($invoice);
                        if($generateBilletPH['status'] == 'reject'){
                            \Log::info($generateBilletPH['message']);
                        }


                            if(!file_exists(public_path('boleto')))
                                \File::makeDirectory(public_path('boleto'));

                            $contents = Http::get($generateBilletPH['transaction']->bank_slip->url_slip_pdf)->body();
                            \File::put(public_path(). '/boleto/' .  $invoice->user_id.'_'.$invoice->id.'.'.'pdf', $contents);

                            $billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoice->user_id.'_'.$invoice->id.'.pdf';

                            $base64_pdf = chunk_split(base64_encode(file_get_contents($billet_pdf)));

                            Invoice::where('id',$newInvoice)->where('user_id',$invoice->user_id)->update([
                                'transaction_id'    =>  $generateBilletPH['transaction']->transaction_id,
                                'billet_url'        =>  $generateBilletPH['transaction']->bank_slip->url_slip,
                                'billet_base64'     =>  $base64_pdf,
                                'billet_digitable'  =>  $generateBilletPH['transaction']->bank_slip->digitable_line
                            ]);
                        } catch (\Exception $e) {
                            \Log::error($e->getMessage());
                            Invoice::where('id',$newInvoice)->delete();
                        }

                    }elseif($invoice->gateway_payment == 'Intermedium'){


                        $generateBilletIntermedium = Invoice::generateBilletIntermedium($invoice);
                        if($generateBilletIntermedium['status'] == 'reject'){
                            \Log::info('Linha 170: '.$generateBilletIntermedium['message']);
                        }
                        try {

                            if(!file_exists(public_path('boleto')))
                                \File::makeDirectory(public_path('boleto'));

                                $invoicePDF = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
                                'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.notification_email','customers.type',
                                'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
                                'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
                                'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company',
                                'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email',
                                'users.api_access_token_whatsapp','users.token_paghiper','users.key_paghiper','invoices.image_url_pix','invoices.pix_digitable','invoices.transaction_id',
                                'invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url','users.inter_chave_pix',
                                'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
                                DB::raw("DATEDIFF (invoices.date_due,invoices.date_invoice) as days_due_date"))
                                ->join('customer_services','invoices.customer_service_id','customer_services.id')
                                ->join('customers','customer_services.customer_id','customers.id')
                                ->join('services','customer_services.service_id','services.id')
                                ->join('users','users.id','invoices.user_id')
                                ->where('invoices.id',$invoice->id)
                                ->first();


                            $getBilletPDFIntermedium = Invoice::getBilletPDFIntermedium($invoicePDF);

                            \File::put(public_path(). '/boleto/' . $invoicePDF->user_id.'_'.$invoicePDF->id.'.'.'pdf', base64_decode($getBilletPDFIntermedium));

                            $billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoicePDF->user_id.'_'.$invoicePDF->id.'.pdf';

                            $invoice->update([
                                'transaction_id'    =>  $generateBilletIntermedium['transaction']->nossoNumero,
                                'billet_url'        =>  $billet_pdf,
                                'billet_base64'     =>  $getBilletPDFIntermedium,
                                'billet_digitable'  =>  $generateBilletIntermedium['transaction']->linhaDigitavel
                            ]);
                        } catch (\Exception $e) {
                            \Log::error($e->getMessage());
                            //return response()->json($e->getMessage(), 422);
                        }

                    }


            }


            $getInvoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
            'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.notification_email','customers.type',
            'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
            'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
            'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company',
            'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email','users.send_generate_invoice',
            'users.api_access_token_whatsapp','users.token_paghiper','users.key_paghiper','invoices.image_url_pix','invoices.pix_digitable','invoices.transaction_id',
            'invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url','users.inter_chave_pix',
            'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
            DB::raw("DATEDIFF (invoices.date_due,invoices.date_invoice) as days_due_date"))
            ->join('customer_services','invoices.customer_service_id','customer_services.id')
            ->join('customers','customer_services.customer_id','customers.id')
            ->join('services','customer_services.service_id','services.id')
            ->join('users','users.id','invoices.user_id')
            ->where('invoices.id',$invoice['id'])
            ->first();


            if($getInvoice == null){
                \Log::info('Linha 311 Getinvoice null:');
                return 1;
            }

            $details = [
                'type_send'                 => 'New',
                'title'                     => 'Nova fatura gerada',
                'message_customer'          => 'Olá '.$getInvoice['name'].', tudo bem?',
                'message_notification'      => 'Esta é uma mensagem para notificá-lo(a) que sua Fatura foi gerada',
                'logo'                      => 'https://cobrancasegura.com.br/'.$getInvoice['user_image'],
                'company'                   => $getInvoice['user_company'],
                'user_whatsapp'             => removeEspeciais($getInvoice['user_whatsapp']),
                'user_telephone'            => removeEspeciais($getInvoice['user_telephone']),
                'user_email'                => $getInvoice['user_email'],
                'user_access_token_wp'      => $getInvoice['api_access_token_whatsapp'],
                'user_id'                   => $getInvoice['user_id'],
                'customer'                  => $getInvoice['name'],
                'customer_email'            => $getInvoice['email'],
                'customer_email2'           => $getInvoice['email2'],
                'customer_whatsapp'         => removeEspeciais($getInvoice['whatsapp']),
                'notification_whatsapp'     => $getInvoice['notification_whatsapp'],
                'notification_email'        => $getInvoice['notification_email'],
                'customer_company'          => $getInvoice['company'],
                'date_invoice'              => date('d/m/Y', strtotime($getInvoice['date_invoice'])),
                'date_due'                  => date('d/m/Y', strtotime($getInvoice['date_due'])),
                'price'                     => number_format($getInvoice['price'], 2,',','.'),
                'gateway_payment'           => $getInvoice['gateway_payment'],
                'payment_method'            => $getInvoice['payment_method'],
                'service'                   => $getInvoice['service_name'] .' - '. $getInvoice['description'],
                'invoice'                   => $getInvoice['id'],
                'status'                    => $getInvoice['status'],
                'url_base'                  => url('/'),
                'pix_qrcode_image_url'      => $getInvoice['image_url_pix'],
                'pix_emv'                   => $getInvoice['pix_digitable'],
                'pix_qrcode_base64'         => $getInvoice['qrcode_pix_base64'],
                'billet_digitable_line'     => $getInvoice['billet_digitable'],
                'billet_url_slip_base64'    => $getInvoice['billet_base64'],
                'billet_url_slip'           => $getInvoice['billet_url'],
            ];



            if($getInvoice['send_generate_invoice'] == 'Sim'){
                if($getInvoice['notification_email'] == 's'){
                    if( $getInvoice['payment_method'] == 'Boleto' && $getInvoice['billet_digitable'] == null){
                        \Log::info('Linha 341: boleto em branco');
                        Invoice::where('id',$newInvoice)->delete();
                    }
                    else if( $getInvoice['payment_method'] == 'Pix' && $getInvoice['pix_digitable'] == null){
                        \Log::info('Linha 344: pix em branco');
                        Invoice::where('id',$newInvoice)->delete();
                    }else{
                        $details['body']  = view('mails.invoice',$details)->render();
                        InvoiceNotification::Email($details);
                    }

                }
                if($getInvoice['notification_whatsapp'] == 's'){

                    InvoiceNotification::Whatsapp($details);
                }

            }

            //sleep(5);
         }

    }

    }


  }
}
