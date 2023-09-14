<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class sendInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $vInvoice;

    public function __construct($vInvoice)
    {
        $this->vInvoice = $vInvoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $newInvoice = DB::table('invoices')->insertGetId([
            'user_id'               => $this->vInvoice->user_id,
            'customer_service_id'   => $this->vInvoice->id,
            'description'           => $this->vInvoice->description,
            'price'                 => $this->vInvoice->price,
            'gateway_payment'       => $this->vInvoice->gateway_payment,
            'payment_method'        => $this->vInvoice->payment_method,
            'date_invoice'          => $this->vInvoice->date_invoice,
            'date_due'              => $this->vInvoice->date_due,
            'date_payment'          => null,
            'status'                => 'Pendente',
            'created_at'            => $this->vInvoice->created_at,
            'updated_at'            => $this->vInvoice->updated_at
        ]);



        $invoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
        'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.type',
        'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
        'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
        'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company',
        'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email',
        'users.api_access_token_whatsapp','users.token_paghiper','users.key_paghiper','users.inter_chave_pix',
        'invoices.image_url_pix','invoices.pix_digitable','invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url',
        'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
        DB::raw("DATEDIFF (invoices.date_due,invoices.date_invoice) as days_due_date"))
        ->join('customer_services','invoices.customer_service_id','customer_services.id')
        ->join('customers','customer_services.customer_id','customers.id')
        ->join('services','customer_services.service_id','services.id')
        ->join('users','users.id','invoices.user_id')
        ->where('invoices.id',$newInvoice)
        ->where('invoices.user_id',$this->vInvoice->user_id)
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
                }

            }elseif($invoice->gateway_payment == 'Intermedium'){


                $generateBilletIntermedium = Invoice::generateBilletIntermedium($invoice);
                if($generateBilletIntermedium['status'] == 'reject'){
                    return response()->json($generateBilletIntermedium['message'], 422);
                }
                try {

                    if(!file_exists(public_path('boleto')))
                        \File::makeDirectory(public_path('boleto'));

                        $invoicePDF = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
                        'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.type',
                        'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
                        'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
                        'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company',
                        'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email',
                        'users.api_access_token_whatsapp','users.token_paghiper','users.key_paghiper','invoices.image_url_pix','invoices.pix_digitable',
                        'invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url','invoices.transaction_id','users.inter_chave_pix',
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
                    return response()->json($e->getMessage(), 422);
                }

            }


    }



    $details = [
        'type_send'                 => 'New',
        'title'                     => 'Nova fatura gerada',
        'message_customer'          => 'Olá '.$invoice->name.', tudo bem?',
        'message_notification'      => 'Esta é uma mensagem para notificá-lo(a) que sua Fatura foi gerada',
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
        'service'                   => $invoice->service_name .' - '. $invoice->description,
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


    $details['body']  = view('mails.invoice',$details)->render();

    InvoiceNotification::Email($details);

    if($invoice->notification_whatsapp)
        InvoiceNotification::Whatsapp($details);



    }
}
