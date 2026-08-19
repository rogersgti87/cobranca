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
            'company_id'            => $this->vInvoice->company_id ?? null,
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



        $invoice =Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
        'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.notification_email','customers.type',
        'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
        'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
        'services.id as service_id','services.name as service_name','invoices.price','companies.access_token_mp','companies.name as user_company',
        'companies.whatsapp as user_whatsapp','companies.logo as user_image', 'companies.phone as user_telephone', 'companies.email as user_email','companies.send_generate_invoice',
        'companies.api_token_whatsapp as api_access_token_whatsapp','invoices.image_url_pix','invoices.pix_digitable',
        'invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url',
        DB::raw("DATEDIFF (invoices.date_due,invoices.date_invoice) as days_due_date"))
        ->join('customer_services','invoices.customer_service_id','customer_services.id')
        ->join('customers','customer_services.customer_id','customers.id')
        ->join('services','customer_services.service_id','services.id')
        ->join('companies','companies.id','invoices.company_id')
        ->where('invoices.id',$newInvoice)
        ->where('invoices.user_id',$this->vInvoice->user_id)
        ->first();

        if($invoice->payment_method == 'Pix'){
            if($invoice->gateway_payment == 'Pag Hiper'){
                try {
                    $generatePixPH = Invoice::generatePixPH($invoice->id);
                    if($generatePixPH['status'] == 'reject'){
                        \Log::info($generatePixPH['message']);
                    }
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }

            }elseif($invoice->gateway_payment == 'Mercado Pago'){
                try {
                    $generatePixMP = Invoice::generatePixMP($invoice->id);
                    if($generatePixMP['status'] == 'reject'){
                        \Log::info($generatePixMP['message']);
                    }
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }

            } elseif($invoice->gateway_payment == 'Intermedium'){
                try {
                    $generatePixIntermedium = Invoice::generatePixIntermedium($invoice->id);
                    if($generatePixIntermedium['status'] == 'reject'){
                        \Log::info(json_encode($generatePixIntermedium['message']));
                    }
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }
        } elseif($invoice->payment_method == 'Boleto'){

            if($invoice->gateway_payment == 'Pag Hiper'){
                try {
                    $generateBilletPH = Invoice::generateBilletPH($invoice->id);
                    if($generateBilletPH['status'] == 'reject'){
                        \Log::info($generateBilletPH['message']);
                    }
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }

            }elseif($invoice->gateway_payment == 'Intermedium'){

                // generateBilletIntermedium já persiste linha digitável no banco
                $generateBilletIntermedium = Invoice::generateBilletIntermedium($invoice->id);
                if($generateBilletIntermedium['status'] == 'reject'){
                    \Log::error('Erro ao gerar boleto Intermedium no Job sendInvoice - Invoice ID: '.$invoice->id.' - Erro: '.json_encode($generateBilletIntermedium['message']));
                } else {
                    \Log::info('Boleto Intermedium gerado com sucesso no Job sendInvoice - Invoice ID: '.$invoice->id);
                }

            }


    }


    // Recarrega a fatura com relacionamentos para verificar preferências de envio
    $getInvoice = Invoice::with(['company', 'customerService.customer'])->find($newInvoice);

    $sendGenerate       = $getInvoice->company->send_generate_invoice ?? null;
    $notifEmail         = $getInvoice->customerService->customer->notification_email ?? 'n';
    $notifWhatsapp      = $getInvoice->customerService->customer->notification_whatsapp ?? 'n';

    \Log::info('sendInvoice Job - send_generate_invoice: '.$sendGenerate.' | invoice: '.$newInvoice);

    if($sendGenerate == 'Sim'){
        if($notifEmail == 's'){
            InvoiceNotification::Email($newInvoice);
        }
        if($notifWhatsapp == 's'){
            InvoiceNotification::Whatsapp($newInvoice);
        }
    }

    return "Notificação para o cliente está na fila para processamento.";

    }
}
