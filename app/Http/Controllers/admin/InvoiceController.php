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
use App\Models\ViewInvoice;
use App\Models\InvoiceNotification;
use App\Models\Service;
use App\Models\CustomerService;
use App\Models\Customer;
use RuntimeException;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        return view('admin.invoice.index')->with($this->datarequest);
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

            if($model['status'] == 'Pago' || $model['status'] == 'Cancelado'){

                if(isset($data['send_invoice_email']))
                    InvoiceNotification::Email($model['id']);

                if(isset($data['send_invoice_whatsapp']))
                    InvoiceNotification::Whatsapp($model['id']);

                return response()->json('Registro salvo com sucesso', 200);
            }


            if($model['payment_method'] == 'Pix'){
                //PIX PAG HIPER
                if($model['gateway_payment'] == 'Pag Hiper'){
                    $generatePixPH = Invoice::generatePixPH($model['id']);
                    if($generatePixPH['status'] == 'reject'){
                        return response()->json($generatePixPH['message'], 422);
                    }

                }elseif($model['gateway_payment'] == 'Mercado Pago'){
                    $generatePixMP = Invoice::generatePixMP($model['id']);
                    if($generatePixMP['status'] == 'reject'){
                        return response()->json($generatePixMP['message'], 422);
                    }

                }elseif($model['gateway_payment'] == 'Intermedium'){
                    $generatePixIntermedium = Invoice::generatePixIntermedium($model['id']);
                    if($generatePixIntermedium['status'] == 'reject'){
                        $msgInterPix = '';
                        foreach($generatePixIntermedium['message'] as $messageInterPix){
                            $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                        }

                        return response()->json($generatePixIntermedium['title'].': '.$msgInterPix, 422);
                    }

                }
            } elseif($model['payment_method'] == 'Boleto'){

                if($model['gateway_payment'] == 'Pag Hiper'){
                    $generateBilletPH = Invoice::generateBilletPH($model['id']);
                    if($generateBilletPH['status'] == 'reject'){
                        return response()->json($generateBilletPH['message'], 422);
                    }

                }elseif($model['gateway_payment'] == 'Intermedium'){

                    $generateBilletIntermedium = Invoice::generateBilletIntermedium($model['id']);
                    if($generateBilletIntermedium['status'] == 'reject'){
                        $msgInterBillet = '';
                        foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                            $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                        }

                        return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                    }

                }
        }
        elseif($model['payment_method'] == 'BoletoPix'){

            if($model['gateway_payment'] == 'Intermedium'){

                $generateBilletIntermedium = Invoice::generateBilletPixIntermedium($model['id']);
                if($generateBilletIntermedium['status'] == 'reject'){
                    $msgInterBillet = '';
                    foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                        $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                    }

                    return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                }

            }
    }


            if(isset($data['send_invoice_email']))
                InvoiceNotification::Email($model['id']);

            if(isset($data['send_invoice_whatsapp']))
                InvoiceNotification::Whatsapp($model['id']);

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
            //'customer_service_id'   => 'required',
            //'description'           => 'required',
            //'price'                 => 'required',
            'payment_method'        => 'required',
            //'date_invoice'          => 'required',
            //'date_due'              => 'required',
            //'status'                => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        // if($data['status'] == 'Pago'){
        //     if($data['date_payment'] == null){
        //         return response()->json('Data de Pagamento é obrigatório!', 422);
        //     }
        // }

        $model->user_id             = auth()->user()->id;
        //$model->customer_service_id = $data['customer_service_id'];
        //$model->description         = $data['description'];
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];
        //$model->date_invoice        = $data['date_invoice'];


        if(isset($data['generate_invoice'])){
            if($data['date_due'] == null){
                return response()->json('Data de vencimento é obrigatório!', 422);
            }else{
                $model->date_due            = $data['date_due'];
                $model->price               = moeda($data['price']);
            }

        }

        $model->date_payment        = $data['date_payment'] != null ? $data['date_payment'] : null;

        if($data['date_payment'] != null){
            $model->status   = 'Pago';
        }

        try{
            $model->save();

            if(isset($data['generate_invoice'])){

                if($model['payment_method'] == 'Pix'){
                    //PIX PAG HIPER
                    if($model['gateway_payment'] == 'Pag Hiper'){
                        $generatePixPH = Invoice::generatePixPH($model['id']);
                        if($generatePixPH['status'] == 'reject'){
                            return response()->json($generatePixPH['message'], 422);
                        }

                    }elseif($model['gateway_payment'] == 'Mercado Pago'){
                        $generatePixMP = Invoice::generatePixMP($model['id']);
                        if($generatePixMP['status'] == 'reject'){
                            return response()->json($generatePixMP['message'], 422);
                        }

                    }elseif($model['gateway_payment'] == 'Intermedium'){
                        $generatePixIntermedium = Invoice::generatePixIntermedium($model['id']);
                        if($generatePixIntermedium['status'] == 'reject'){
                            $msgInterPix = '';
                            foreach($generatePixIntermedium['message'] as $messageInterPix){
                                $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                            }

                            return response()->json($generatePixIntermedium['title'].': '.$msgInterPix, 422);
                        }

                    }
                } elseif($model['payment_method'] == 'Boleto'){

                    if($model['gateway_payment'] == 'Pag Hiper'){
                        $generateBilletPH = Invoice::generateBilletPH($model['id']);
                        if($generateBilletPH['status'] == 'reject'){
                            return response()->json($generateBilletPH['message'], 422);
                        }

                    }elseif($model['gateway_payment'] == 'Intermedium'){

                        $generateBilletIntermedium = Invoice::generateBilletIntermedium($model['id']);
                        if($generateBilletIntermedium['status'] == 'reject'){
                            $msgInterBillet = '';
                            foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                                $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                            }

                            return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                        }

                    }


            }

            elseif($model['payment_method'] == 'BoletoPix'){

            if($model['gateway_payment'] == 'Intermedium'){

                $generateBilletIntermedium = Invoice::generateBilletPixIntermedium($model['id']);
                if($generateBilletIntermedium['status'] == 'reject'){
                    $msgInterBillet = '';
                    foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                        $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'];
                    }

                    return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                }

            }
    }


}


if(isset($data['send_invoice_email'])){
    InvoiceNotification::Email($model['id']);
}


if(isset($data['send_invoice_whatsapp'])){
    InvoiceNotification::Whatsapp($model['id']);
}


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

            if($invoice->payment_method == 'Pix' && $invoice->transaction_id != ''){

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

                else if($invoice->gateway_payment == 'Intermedium'){
                    $status = Invoice::cancelPixIntermedium(auth()->user()->id,$invoice->transaction_id);
                    if($status == 'success'){
                        $status = 'success';
                    }else{
                        return response()->json($status['message'],422);
                    }
                }

            }
            else if($invoice->payment_method == 'Boleto' && $invoice->transaction_id != ''){

                if($invoice->gateway_payment == 'Pag Hiper'){
                    $status = Invoice::cancelBilletPH(auth()->user()->id,$invoice->transaction_id);
                    if($status->result == 'success'){
                        $status = 'success';
                    }

                }
                else if($invoice->gateway_payment == 'Mercado Pago'){
                    //$cancelPixMP = Invoice::cancelBilletMP(auth()->user()->id,$invoice->transaction_id);
                }
                else if($invoice->gateway_payment == 'Intermedium'){
                    $status = Invoice::cancelBilletIntermedium(auth()->user()->id,$invoice->transaction_id);
                    if($status == 'success'){
                        $status = 'success';
                    }
                }
            }
            else if($invoice->payment_method == 'BoletoPix' && $invoice->transaction_id != ''){
                if($invoice->gateway_payment == 'Intermedium'){
                    $status = Invoice::cancelBilletPixIntermedium(auth()->user()->id,$invoice->transaction_id);
                    if($status == 'success'){
                        $status = 'success';
                    }
                }
            }

            if($status == 'success' || $invoice->payment_method == 'Dinheiro' || $invoice->payment_method == 'Cartão' || $invoice->payment_method == 'Depósito' || $invoice->transaction_id == ''){
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
                'invoices.date_due','invoices.date_payment','invoices.status','services.name as name','invoices.gateway_payment','invoices.billet_url','invoices.image_url_pix')
                ->where('customer_services.customer_id',$customer_id)->where('invoices.user_id',auth()->user()->id)
                ->orderby('invoices.id','DESC')
                ->get();

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


    public function loadinvoiceerror(){

        $data = ViewInvoice::where('user_id',auth()->user()->id)->where('status','Erro')->get();

        return view('admin.invoice.error',compact('data'))->render();
    }


    public function checkStatus($invoice_id){

        $checkInvoice = Invoice::select('invoices.id','invoices.transaction_id','users.token_paghiper','users.key_paghiper','invoices.payment_method','invoices.gateway_payment')
                        ->join('users','users.id','invoices.user_id')
                        ->where('invoices.id',$invoice_id)
                        ->where('invoices.user_id',auth()->user()->id)
                        ->where('invoices.status','Pendente')
                        ->orwhere('invoices.status','Processamento')
                        ->first();



    if($checkInvoice != null){
        if($checkInvoice->gateway_payment == 'Pag Hiper'){
            $url = '';
            if($checkInvoice->payment_method == 'Pix'){
                $url = 'https://pix.paghiper.com/invoice/status/';
            }else{
                $url = 'https://api.paghiper.com/transaction/status/';
            }


            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post($url,[
                'token'             => $checkInvoice->token_paghiper,
                'apiKey'            => $checkInvoice->key_paghiper,
                'transaction_id'    => $checkInvoice->transaction_id,
            ]);

            $result = $response->getBody();
            $result = json_decode($result)->status_request;




            if($result->status == 'completed' || $result->status == 'paid'){
                Invoice::where('id',$checkInvoice->id)->where('user_id',auth()->user()->id)->update([
                    'status'       =>   'Pago',
                    'date_payment' =>   isset($result->status_date) ? date('d/m/Y', strtotime($result->status_date)) : Carbon::now(),
                    'updated_at'   =>   Carbon::now()
                ]);
                if($checkInvoice->notification_email == 's'){
                    InvoiceNotification::Email($invoice_id);
                }
                if($checkInvoice->notification_whatsapp == 's'){
                    InvoiceNotification::Whatsapp($invoice_id);
                }
            }

            if($result->status == 'canceled' || $result->status == 'refunded'){
                Invoice::where('id',$checkInvoice->id)->where('user_id',auth()->user()->id)->update([
                    'status'       =>   'Cancelado',
                    'date_payment' =>   Null,
                    'updated_at'   =>   Carbon::now()
                ]);
                if($checkInvoice->notification_email == 's'){
                    InvoiceNotification::Email($invoice_id);
                }
                if($checkInvoice->notification_whatsapp == 's'){
                    InvoiceNotification::Whatsapp($invoice_id);
                }
            }



            }
            //Fim paghiper

    }



    return response()->json(true, 200);

}


public function loadInvoices(){


    $query = Invoice::query();


    $fields = "invoices.id as id,invoices.description,invoices.payment_method,invoices.price,invoices.date_invoice,customers.id as customer_id, customers.name as customer_name,
            invoices.date_due,invoices.date_payment,invoices.status,services.name as service_name,invoices.gateway_payment,invoices.payment_method,invoices.billet_url,invoices.image_url_pix,invoices.updated_at";

    $query->join('customer_services','customer_services.id','invoices.customer_service_id')
            ->join('services','services.id','customer_services.service_id')
            ->join('customers','customers.id','customer_services.customer_id')
            ->where('invoices.user_id',auth()->user()->id)
            ->orderby('invoices.date_due','ASC');


    if ($this->request->has('dateini') && $this->request->has('dateend')) {
            $query->select(DB::raw("$fields,
            (select count(*) from invoices where ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as qtd_invoices ,
            (select COALESCE(sum(price),0) from invoices where ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as total_currency ,
            (select count(*) from invoices where status = 'Pendente' and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as qtd_pendente,
            (select COALESCE(sum(price),0) from invoices where status = 'Pendente' and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as pendente_currency,
            (select count(*) from invoices where status = 'Pago'  and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as qtd_pago,
            (select COALESCE(sum(price),0) from invoices where status = 'Pago'  and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as pago_currency,
            (select count(*) from invoices where status = 'Processamento'  and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as qtd_processamento,
            (select COALESCE(sum(price),0) from invoices where status = 'Processamento'  and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as processamento_currency,
            (select count(*) from invoices where status = 'Cancelado'  and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as qtd_cancelado,
            (select COALESCE(sum(price),0) from invoices where status = 'Cancelado'  and ".$this->request->input('type')." between '".$this->request->input('dateini')."' and '".$this->request->input('dateend')."' and user_id = ".auth()->user()->id." ) as cancelado_currency
            "));

            if($this->request->has('status') && $this->request->input('status') != ''){
                $query->where('invoices.status',$this->request->input('status'));
            }


            $query->whereBetween('invoices.'.$this->request->input('type'),[$this->request->input('dateini'),$this->request->input('dateend')]);
    }else{

        $query->select(DB::raw("$fields,
        (select count(*) from invoices where date_due between '".Carbon::now()->startOfMonth()."' and '".Carbon::now()->lastOfMonth()."' and user_id = ".auth()->user()->id." ) as qtd_invoices ,
            (select count(*) from invoices where status = 'Pendente' and date_due between '".Carbon::now()->startOfMonth()."' and '".Carbon::now()->lastOfMonth()."' and user_id = ".auth()->user()->id." ) as qtd_pendente,
            (select count(*) from invoices where status = 'Pago'  and date_due between '".Carbon::now()->startOfMonth()."' and '".Carbon::now()->lastOfMonth()."' and user_id = ".auth()->user()->id." ) as qtd_pago,
            (select count(*) from invoices where status = 'Processamento'  and date_due between '".Carbon::now()->startOfMonth()."' and '".Carbon::now()->lastOfMonth()."' and user_id = ".auth()->user()->id." ) as qtd_processamento,
            (select count(*) from invoices where status = 'Cancelado'  and date_due between '".Carbon::now()->startOfMonth()."' and '".Carbon::now()->lastOfMonth()."' and user_id = ".auth()->user()->id." ) as qtd_cancelado
        "));
        $query->whereBetween('invoices.date_due',[Carbon::now()->startOfMonth(),Carbon::now()->lastOfMonth()]);
    }

    $data = $query->paginate(20);

    $data->prev_page_url = $data->previousPageUrl();
    $data->next_page_url = $data->nextPageUrl();

    return response()->json(['result' => $data]);


}


public function invoiceNotificate($invoice_id){

    $options = $this->request->input('selectedOptions');


    $status_whatsapp = [
        'mensagem' => 'Não enviado',
        'pix'      => 'Não enviado',
        'boleto'   => 'Não enviado'
    ];

    $status_email    = 'Não enviado';


    $invoice = ViewInvoice::where('id',$invoice_id)->first();

        foreach($options as $option){
            if($option == 'whatsapp'){
                if($invoice['notification_whatsapp'] == 's'){
                    $result_whatsapp = InvoiceNotification::Whatsapp($invoice_id);
                    $status_whatsapp = [
                        'mensagem' => $result_whatsapp['message'],
                        'pix'      => $result_whatsapp['image'],
                        'boleto'   => $result_whatsapp['file'],
                    ];
                }
            }

            if($option == 'email'){
                if($invoice['notification_email'] == 's'){
                    $result_email =  InvoiceNotification::Email($invoice_id);
                    $status_email = $result_email;
                }
            }
        }


    return response()->json(['payment_method' => $invoice->payment_method,'whatsapp' => $status_whatsapp, 'email' => $status_email]);

}


public function error($id){
    $data = Invoice::select('msg_erro')->where('id',$id)->first();
    return response()->json($data);
}

}
