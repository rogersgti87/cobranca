<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\Service;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;

class CustomerServiceController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Serviços',
            'link'              => 'admin/customer-services',
            'filter'            => 'admin/customer-services?filter',
            'linkFormAdd'       => 'admin/customer-services/form?act=add',
            'linkFormEdit'      => 'admin/customer-services/form?act=edit',
            'linkStore'         => 'admin/customer-services',
            'linkUpdate'        => 'admin/customer-services/',
            'linkCopy'          => 'admin/customer-services/copy',
            'linkDestroy'       => 'admin/customer-services',
            'breadcrumb_new'    => 'Novo Serviço',
            'breadcrumb_edit'   => 'Editar Serviço',
            'path'              => 'admin.customer-service.'
        ];

    }

    public function index(){

        $data = CustomerService::where('user_id',auth()->user()->id)->orderby('id','desc')->get();

        return response()->json($data);
    }

    public function form(){

        $customer_id = $this->request->input('customer_id');

        $customer_id = $customer_id != null ? $customer_id : '';

        $services = Service::where('user_id',auth()->user()->id)->get();
        $data = CustomerService::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

        return view($this->datarequest['path'].'.form',compact('services','data','customer_id'))->render();

    }


    public function store()
    {

        $model = new CustomerService();
        $data = $this->request->all();

        $messages = [
            'service_id.required'           => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo descrição é obrigatório!',
            'price.required'                => 'O campo Valor é obrigatório!',
            'start_billing.required'        => 'O campo Data Inicio Cobrança é obrigatório',
            'period.required'               => 'O campo periodo é obrigatório',
            'gateway_payment.required'      => 'O campo Gateway de pagamento é obrigatório',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'service_id'        =>  'required',
            'description'       =>  'required',
            'price'             =>  'required',
            'start_billing'     =>  'required',
            'period'            =>  'required',
            'gateway_payment'   =>  'required',
            'payment_method'    =>  'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_id         = $data['customer_id'];
        $model->service_id          = $data['service_id'];
        $model->description         = $data['description'];
        $model->status              = $data['status'];
        $model->day_due             = $data['day_due'];
        $model->price               = moeda($data['price']);
        $model->period              = $data['period'];
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];
        $model->start_billing       = $data['start_billing'];
        $model->end_billing         = $data['end_billing'];

        try{
            $model->save();


            if(isset($data['generate_invoice'])){

                $newInvoice = new Invoice();
                $newInvoice->user_id             = $model->user_id;
                $newInvoice->customer_service_id = $model->id;
                $newInvoice->description         = $model->description;
                $newInvoice->price               = $model->price;
                $newInvoice->gateway_payment     = $model->gateway_payment;
                $newInvoice->payment_method      = $model->payment_method;
                $newInvoice->date_invoice        = Carbon::now();
                $newInvoice->date_due            = $model->day_due >= Carbon::parse(Carbon::now())->format('d') ? Carbon::createFromDate(Carbon::now())->day($model->day_due) : Carbon::parse(Carbon::now())->format('Y-m-d');
                $newInvoice->date_payment        = null;
                $newInvoice->status              = 'Pendente';
                $newInvoice->save();


                $invoice = Invoice::select('invoices.id','invoices.status','invoices.user_id','invoices.date_invoice','invoices.date_due','invoices.description',
                'customers.email','customers.email2','customers.phone','customers.whatsapp','customers.name','customers.notification_whatsapp','customers.type',
                'customers.company','customers.document','customers.phone','customers.address','customers.number','customers.complement',
                'customers.district','customers.city','customers.state','customers.cep','invoices.gateway_payment','invoices.payment_method',
                'services.id as service_id','services.name as service_name','invoices.price','users.access_token_mp','users.company as user_company',
                'users.whatsapp as user_whatsapp','users.image as user_image', 'users.telephone as user_telephone', 'users.email as user_email',
                'users.api_access_token_whatsapp','users.token_paghiper','users.key_paghiper','invoices.image_url_pix','invoices.pix_digitable',
                'invoices.qrcode_pix_base64','invoices.billet_digitable','invoices.billet_base64','invoices.billet_url','users.inter_chave_pix',
                'users.inter_host','users.inter_client_id','users.inter_client_secret','users.inter_scope','users.inter_crt_file','users.inter_key_file','users.inter_crt_file_webhook',
                DB::raw("DATEDIFF (invoices.date_due,invoices.date_invoice) as days_due_date"))
                ->join('customer_services','invoices.customer_service_id','customer_services.id')
                ->join('customers','customer_services.customer_id','customers.id')
                ->join('services','customer_services.service_id','services.id')
                ->join('users','users.id','invoices.user_id')
                ->where('invoices.id',$newInvoice->id)
                ->where('invoices.user_id',$newInvoice->user_id)
                ->first();


                if($invoice->payment_method == 'Pix'){
                    if($invoice->gateway_payment == 'Pag Hiper'){
                        $generatePixPH = Invoice::generatePixPH($invoice);
                        if($generatePixPH['status'] == 'reject'){
                            $newInvoice->delete();
                            return response()->json($generatePixPH['message'], 422);
                        }
                        try {


                            if(!file_exists(public_path('pix')))
                                \File::makeDirectory(public_path('pix'));

                            \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', base64_decode($generatePixPH['transaction']->pix_code->qrcode_base64));


                            $newInvoice->update([
                                'transaction_id'    => $generatePixPH['transaction']->transaction_id,
                                'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                                'pix_digitable'     => $generatePixPH['transaction']->pix_code->emv,
                                'qrcode_pix_base64' => $generatePixPH['transaction']->pix_code->qrcode_base64,
                            ]);

                        } catch (\Exception $e) {
                            \Log::error($e->getMessage());
                            $newInvoice->delete();
                            $model->delete();
                            return response()->json($e->getMessage(), 422);
                        }

                    }elseif($invoice->gateway_payment == 'Mercado Pago'){
                        $generatePixMP = Invoice::generatePixMP($invoice->id);
                        if($generatePixMP['status'] == 'reject'){
                            $newInvoice->delete();
                            $model->delete();
                            return response()->json($generatePixMP['message'], 422);
                        }
                        try {

                            $getInfoPixMP = Invoice::verifyStatusPixMP($invoice->access_token_mp,$generatePixMP['transaction_id']);

                            if(!file_exists(public_path('pix')))
                                \File::makeDirectory(public_path('pix'));

                            \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', base64_decode($getInfoPixMP->qr_code_base64));

                            $newInvoice->update([
                                'transaction_id'    => $generatePixMP['transaction_id'],
                                'image_url_pix'     => 'https://cobrancasegura.com.br/pix/'.$invoice->user_id.'_'.$invoice->id.'.png',
                                'pix_digitable'     => $getInfoPixMP->qr_code,
                                'qrcode_pix_base64' => $getInfoPixMP->qr_code_base64,
                            ]);

                        } catch (\Exception $e) {
                            \Log::error($e->getMessage());
                            $newInvoice->delete();
                            $model->delete();
                            return response()->json($e->getMessage(), 422);
                        }

                    }elseif($invoice->gateway_payment == 'Intermedium'){
                        $generatePixIntermedium = Invoice::generatePixIntermedium($invoice);
                        if($generatePixIntermedium['status'] == 'reject'){
                            $newInvoice->delete();
                            $model->delete();
                            $msgInterPix = '';
                            foreach($generatePixIntermedium['message'] as $messageInterPix){
                                $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                            }

                            return response()->json($generatePixIntermedium['title'].': '.$msgInterPix, 422);
                        }
                        try {

                            if(!file_exists(public_path('pix')))
                                \File::makeDirectory(public_path('pix'));

                            QrCode::format('png')->size(220)->generate($generatePixIntermedium['transaction']->pixCopiaECola, public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png');

                            $image_pix   = config()->get('app.url').'/pix/'.$invoice->user_id.'_'.$invoice->id.'.png';

                            $newInvoice->update([
                                'image_url_pix'     => $image_pix,
                                'pix_digitable'     => $generatePixIntermedium['transaction']->pixCopiaECola,
                                'qrcode_pix_base64' => base64_encode(file_get_contents($image_pix)),
                            ]);

                        } catch (\Exception $e) {
                            $newInvoice->delete();
                            $model->delete();
                            \Log::error($e->getMessage());
                            return response()->json($e->getMessage(), 422);
                        }

                    }
                } elseif($invoice->payment_method == 'Boleto'){

                    if($invoice->gateway_payment == 'Pag Hiper'){
                        $generateBilletPH = Invoice::generateBilletPH($invoice);
                        if($generateBilletPH['status'] == 'reject'){
                            $newInvoice->delete();
                            $model->delete();
                            return response()->json($generateBilletPH['message'], 422);
                        }
                        try {

                            if(!file_exists(public_path('boleto')))
                                \File::makeDirectory(public_path('boleto'));

                            $contents = Http::get($generateBilletPH['transaction']->bank_slip->url_slip_pdf)->body();
                            \File::put(public_path(). '/boleto/' .  $invoice->user_id.'_'.$invoice->id.'.'.'pdf', $contents);

                            $billet_pdf   = 'https://cobrancasegura.com.br/boleto/'.$invoice->user_id.'_'.$invoice->id.'.pdf';

                            $base64_pdf = chunk_split(base64_encode(file_get_contents($billet_pdf)));

                            $newInvoice->update([
                                'transaction_id'    =>  $generateBilletPH['transaction']->transaction_id,
                                'billet_url'        =>  $generateBilletPH['transaction']->bank_slip->url_slip,
                                'billet_base64'     =>  $base64_pdf,
                                'billet_digitable'  =>  $generateBilletPH['transaction']->bank_slip->digitable_line
                            ]);
                        } catch (\Exception $e) {
                            $newInvoice->delete();
                            $model->delete();
                            \Log::error($e->getMessage());
                            return response()->json($e->getMessage(), 422);
                        }

                    }elseif($invoice->gateway_payment == 'Intermedium'){

                        $generateBilletIntermedium = Invoice::generateBilletIntermedium($invoice);
                        if($generateBilletIntermedium['status'] == 'reject'){
                            $newInvoice->delete();
                            $model->delete();
                            $msgInterBillet = '';
                            foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                                $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                            }

                            return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
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

                            $newInvoice->update([
                                'transaction_id'    =>  $generateBilletIntermedium['transaction']->nossoNumero,
                                'billet_url'        =>  $billet_pdf,
                                'billet_base64'     =>  $getBilletPDFIntermedium,
                                'billet_digitable'  =>  $generateBilletIntermedium['transaction']->linhaDigitavel
                            ]);
                        } catch (\Exception $e) {
                            $newInvoice->delete();
                            $model->delete();
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


                if(isset($data['send_invoice_email']))
                    InvoiceNotification::Email($details);

                if(isset($data['send_invoice_whatsapp'])){
                    try{
                        InvoiceNotification::Whatsapp($details);
                    }catch (\Exception $e) {
                        \Log::error($e->getMessage());
                        InvoiceNotification::Whatsapp($details);
                        return response()->json('Erro ao enviar notificação via whatsapp, será feito mais uma tentativa. Caso o erro persista, verifique sua conexão com o whatsapp ou clique em reenviar notificação em faturas. ', 422);
                        }
                }




            }



        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }




    public function update($id)
    {

        $model = CustomerService::where('id',$id)->where('user_id',auth()->user()->id)->first();

        $data = $this->request->all();


        $messages = [
            'service_id.required'           => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo descrição é obrigatório!',
            'price.required'                => 'O campo Valor é obrigatório!',
            'start_billing.required'        => 'O campo Data Inicio Cobrança é obrigatório',
            'period.required'               => 'O campo periodo é obrigatório',
            'gateway_payment.required'      => 'O campo Gateway de pagamento é obrigatório',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'service_id'        =>  'required',
            'description'       =>  'required',
            'price'             =>  'required',
            'start_billing'     =>  'required',
            'period'            =>  'required',
            'gateway_payment'   =>  'required',
            'payment_method'    =>  'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_id         = $data['customer_id'];
        $model->service_id          = $data['service_id'];
        $model->description         = $data['description'];
        $model->status              = $data['status'];
        $model->day_due             = $data['day_due'];
        $model->price               = moeda($data['price']);
        $model->period              = $data['period'];
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];
        $model->start_billing       = $data['start_billing'];
        $model->end_billing         = $data['end_billing'];

        try{
            $model->save();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy($id)
    {
        $model = new CustomerService();

        try{

            $model->where('id',$id)->where('user_id',auth()->user()->id)->delete();

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }

        return response()->json(true, 200);


    }

    public function Load($customer_id){

        $result = CustomerService::join('services','services.id','customer_services.service_id')
                ->select('customer_services.id as id','customer_services.description','customer_services.price','customer_services.day_due','customer_services.period','customer_services.status','services.name as name')
                ->where('customer_services.customer_id',$customer_id)->where('customer_services.user_id',auth()->user()->id)
                ->orderby('services.name','ASC')
                ->get();
        return response()->json($result);


    }

}
