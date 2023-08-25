<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use App\Models\Service;
use App\Models\CustomerService;
use App\Models\Customer;
use RuntimeException;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{

    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Faturas',
            'link'              => 'admin/invoices',
            'filter'            => 'admin/invoices?filter',
            'linkFormAdd'       => 'admin/invoices/form?act=add',
            'linkFormEdit'      => 'admin/invoices/form?act=edit',
            'linkStore'         => 'admin/invoices',
            'linkUpdate'        => 'admin/invoices/',
            'linkCopy'          => 'admin/invoices/copy',
            'linkDestroy'       => 'admin/invoices',
            'breadcrumb_new'    => 'Nova Fatura',
            'breadcrumb_edit'   => 'Editar Fatura',
            'path'              => 'admin.invoice.'
        ];

    }

    public function index(){

        $data = Invoice::where('user_id',auth()->user()->id)->orderby('id','desc')->get();

        return response()->json($data);
    }

    public function form(){

        $customer_id = $this->request->input('customer_id');

        $customer_id = $customer_id != null ? $customer_id : '';

        $customer_services = CustomerService::join('services','services.id','customer_services.service_id')
        ->select('customer_services.id as id','customer_services.description','customer_services.price','customer_services.day_due','customer_services.period','customer_services.status','services.name as name')
        ->where('customer_services.customer_id',$customer_id)->where('customer_services.user_id',auth()->user()->id)->get();

        $data = Invoice::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

        return view($this->datarequest['path'].'.form',compact('customer_services','data','customer_id'))->render();

    }


    public function store()
    {


        $customer_id = $this->request->input('customer_id');

        $model = new Invoice();
        $data = $this->request->all();

        $messages = [
            'customer_service_id.required'  => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo Descrição é obrigatório!',
            'price.required'                => 'O campo Preço é obrigatório!',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
            'date_invoice.required'         => 'O campo Data data fatura é obrigatório!',
            'date_due.required'             => 'O campo Data de vencimento é obrigatório!',
            'status.required'               => 'O campo Status é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'customer_service_id'   => 'required',
            'description'           => 'required',
            'price'                 => 'required',
            'payment_method'        => 'required',
            'date_invoice'          => 'required',
            'date_due'              => 'required',
            'status'                => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_service_id = $data['customer_service_id'];
        $model->description         = $data['description'];
        $model->price               = moeda($data['price']);
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];
        $model->date_invoice        = $data['date_invoice'];
        $model->date_due            = $data['date_due'];
        $model->date_payment        = $data['date_payment'] != null ? $data['date_payment'] : null;
        $model->status              = $data['status'];



        try{
            $model->save();

            $invoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
            'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp',
            'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
            'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
            'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company',
            'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email',
            'users.api_access_token_whatsapp','users.token_paghiper','users.key_paghiper','invoices.image_url_pix','invoices.pix_digitable',
            'invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url',
            DB::raw("DATEDIFF (invoices.date_due,invoices.date_invoice) as days_due_date"))
            ->join('customer_services','invoices.customer_service_id','customer_services.id')
            ->join('customers','customer_services.customer_id','customers.id')
            ->join('services','customer_services.service_id','services.id')
            ->join('users','users.id','invoices.user_id')
            ->where('invoices.id',$model->id)
            ->where('invoices.user_id',auth()->user()->id)
            ->first();


            if($invoice->payment_method == 'Pix'){
                if($invoice->gateway_payment == 'Pag Hiper'){
                    $generatePixPH = Invoice::generatePixPH($invoice);
                    if($generatePixPH['status'] == 'reject'){
                        return response()->json($generatePixPH['message'], 422);
                    }
                    try {


                        if(!file_exists(public_path('pix')))
                            \File::makeDirectory(public_path('pix'));

                        \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', base64_decode($generatePixPH['transaction']->pix_code->qrcode_base64));


                        $invoice->update([
                            'transaction_id'    => $generatePixPH['transaction']->transaction_id,
                            'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                            'pix_digitable'     => $generatePixPH['transaction']->pix_code->emv,
                            'qrcode_pix_base64' => $generatePixPH['transaction']->pix_code->qrcode_base64,
                        ]);

                    } catch (\Exception $e) {
                        \Log::error($e->getMessage());
                        return response()->json($e->getMessage(), 422);
                    }

                }elseif($invoice->gateway_payment == 'Mercado Pago'){
                    $generatePixMP = Invoice::generatePixMP($invoice->id);
                    if($generatePixMP['status'] == 'reject'){
                        return response()->json($generatePixMP['message'], 422);
                    }
                    try {

                        $getInfoPixMP = Invoice::verifyStatusPixMP($invoice->access_token_mp,$generatePixMP['transaction_id']);

                        if(!file_exists(public_path('pix')))
                            \File::makeDirectory(public_path('pix'));

                        \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', base64_decode($getInfoPixMP->qr_code_base64));

                        $invoice->update([
                            'transaction_id'    => $generatePixMP['transaction_id'],
                            'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                            'pix_digitable'     => $getInfoPixMP->qr_code,
                            'qrcode_pix_base64' => $getInfoPixMP->qr_code_base64,
                        ]);

                    } catch (\Exception $e) {
                        \Log::error($e->getMessage());
                        return response()->json($e->getMessage(), 422);
                    }



                }
            } elseif($invoice->payment_method == 'Boleto'){

                if($invoice->gateway_payment == 'Pag Hiper'){
                    $generateBilletPH = Invoice::generateBilletPH($invoice);
                    if($generateBilletPH['status'] == 'reject'){
                        return response()->json($generateBilletPH['message'], 422);
                    }
                    try {

                        if(!file_exists(public_path('boleto')))
                            \File::makeDirectory(public_path('boleto'));

                        $contents = Http::get($generateBilletPH['transaction']->bank_slip->url_slip_pdf)->body();
                        \File::put(public_path(). '/boleto/' .  $invoice->user_id.'_'.$invoice->id.'.'.'pdf', $contents);

                        $billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoice->user_id.'_'.$invoice->id.'.pdf';

                        $base64_pdf = chunk_split(base64_encode(file_get_contents($billet_pdf)));

                        $invoice->update([
                            'transaction_id'    =>  $generateBilletPH['transaction']->transaction_id,
                            'billet_url'        =>  $generateBilletPH['transaction']->bank_slip->url_slip,
                            'billet_base64'     =>  $base64_pdf,
                            'billet_digitable'  =>  $generateBilletPH['transaction']->bank_slip->digitable_line
                        ]);
                    } catch (\Exception $e) {
                        \Log::error($e->getMessage());
                        return response()->json($e->getMessage(), 422);
                    }

                }elseif($invoice->gateway_payment == 'Mercado Pago'){

                }


        }


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


            if(isset($data['send_invoice_email']))
                InvoiceNotification::Email($details);

            if(isset($data['send_invoice_whatsapp']))
                InvoiceNotification::Whatsapp($details);

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }




    public function update($id)
    {

        $customer_id = $this->request->input('customer_id');

        $model = Invoice::where('id',$id)->where('user_id',auth()->user()->id)->first();

        $data = $this->request->all();


        $messages = [
            'customer_service_id.required'  => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo Descrição é obrigatório!',
            'price.required'                => 'O campo Preço é obrigatório!',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
            'date_invoice.required'         => 'O campo Data é obrigatório!',
            'date_due.required'             => 'O campo Data de vencimento é obrigatório!',
            'status.required'               => 'O campo Status é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'customer_service_id'   => 'required',
            'description'           => 'required',
            'price'                 => 'required',
            'payment_method'        => 'required',
            'date_invoice'          => 'required',
            'date_due'              => 'required',
            'status'                => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_service_id = $data['customer_service_id'];
        $model->description         = $data['description'];
        $model->price               = moeda($data['price']);
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];
        $model->date_invoice        = $data['date_invoice'];
        $model->date_due            = $data['date_due'];
        $model->date_payment        = $data['date_payment'] != null ? $data['date_payment'] : null;
        $model->status              = $data['status'];

        try{
            $model->save();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy($id)
    {
        $model = new Invoice();

        try{

            $invoice = $model->where('id',$id)->where('user_id',auth()->user()->id)->first();

            $status = 'error';

            if($invoice->payment_method == 'Pix'){

                if($invoice->gateway_payment == 'Pag Hiper'){
                    $status = Invoice::cancelPixPH(auth()->user()->id,$invoice->transaction_id);
                    if($status->result == 'success'){
                        $status = 'success';
                    }

                }
                else if($invoice->gateway_payment == 'Mercado Pago'){
                    $status = Invoice::cancelPixMP(auth()->user()->access_token_mp,$invoice->transaction_id);
                    if($status == 'cancelled'){
                        $status = 'success';
                    }
                }

            }
            else if($invoice->payment_method == 'Boleto'){

                if($invoice->gateway_payment == 'Pag Hiper'){
                    $status = Invoice::cancelBilletPH(auth()->user()->id,$invoice->transaction_id);
                    if($status->result == 'success'){
                        $status = 'success';
                    }

                }
                else if($invoice->gateway_payment == 'Mercado Pago'){
                    //$cancelPixMP = Invoice::cancelBilletMP(auth()->user()->id,$invoice->transaction_id);
                }
            }


            if($status == 'success' || $invoice->payment_method == 'Dinheiro' || $invoice->payment_method == 'Cartão' || $invoice->payment_method == 'Depósito'){
                $model->where('id',$id)->where('user_id',auth()->user()->id)->update([
                    'status' => 'Cancelado'
                ]);
            }


        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(true, 200);


    }

    public function Load($customer_id){

        $result = Invoice::join('customer_services','customer_services.id','invoices.customer_service_id')
                ->join('services','services.id','customer_services.service_id')
                ->select('invoices.id as id','invoices.description','invoices.payment_method','invoices.price','invoices.date_invoice',
                'invoices.date_due','invoices.date_payment','invoices.status','services.name as name','invoices.gateway_payment')
                ->where('customer_services.customer_id',$customer_id)->where('invoices.user_id',auth()->user()->id)->get();

        return response()->json($result);


    }

    public function loadnotifications($invoice){

        $notifications = DB::table('invoice_notifications as a')
        ->select('a.id','a.type_send', 'ev.event', 'ev.date as date_email','ev.email', 'a.date','a.subject','a.status','ev.sending_ip','ev.reason','a.message_status','a.message')
        ->leftJoin('email_events as ev','ev.message_id','a.email_id')
        ->where('a.user_id',auth()->user()->id)
        ->where('a.invoice_id',$invoice)
        ->orderby('ev.date','desc')
        ->get();

        return view('admin.invoice.notifications',compact('notifications'))->render();
    }

}
