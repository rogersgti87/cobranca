<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\User;
use App\Models\Payable;
use RuntimeException;
use Illuminate\Support\Facades\Http;

class PayableController extends Controller
{

    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Contas a pagar',
            'link'              => 'admin/payables',
            'filter'            => 'admin/payables?filter',
            'linkFormAdd'       => 'admin/payables/form?act=add',
            'linkFormEdit'      => 'admin/payables/form?act=edit',
            'linkStore'         => 'admin/payables',
            'linkUpdate'        => 'admin/payables/',
            'linkCopy'          => 'admin/payables/copy',
            'linkDestroy'       => 'admin/payables',
            'breadcrumb_new'    => 'Nova conta',
            'breadcrumb_edit'   => 'Editar conta',
            'path'              => 'admin.payable.'
        ];

    }

    public function index(){

        return view('admin.payable.index')->with($this->datarequest);
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

        $model->user_id             = auth()->user()->id;
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];



        $model->date_payment        = $data['date_payment'] != null ? $data['date_payment'] : null;

        if($data['date_payment'] != null){
            $model->status   = 'Pago';
        }

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




}
