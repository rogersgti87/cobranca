<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\CustomerService;
use RuntimeException;

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

        $model = new Invoice();
        $data = $this->request->all();

        $messages = [
            'customer_service_id.required'  => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo Descrição é obrigatório!',
            'price.required'                => 'O campo Preço é obrigatório!',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
            'date_invoice.required'         => 'O campo Data data fatura é obrigatório!',
            'date_due.required'             => 'O campo Data de vencimento é obrigatório!',
            'status.required'             => 'O campo Status é obrigatório!',
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

        $model = Invoice::where('id',$id)->where('user_id',auth()->user()->id)->first();

        $data = $this->request->all();


        $messages = [
            'customer_service_id.required'  => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo Descrição é obrigatório!',
            'price.required'                => 'O campo Preço é obrigatório!',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
            'date_invoice.required'         => 'O campo Data é obrigatório!',
            'date_due.required'             => 'O campo Data de vencimento é obrigatório!',
            'status.required'             => 'O campo Status é obrigatório!',
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

            $model->where('id',$id)->where('user_id',auth()->user()->id)->delete();

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }

        return response()->json(true, 200);


    }

    public function Load($customer_id){

        $result = Invoice::join('customer_services','customer_services.id','invoices.customer_service_id')
                ->join('services','services.id','customer_services.service_id')
                ->select('invoices.id as id','invoices.description','invoices.payment_method','invoices.price','invoices.date_invoice','invoices.date_due','invoices.date_payment','invoices.status','services.name as name')
                ->where('customer_services.customer_id',$customer_id)->where('invoices.user_id',auth()->user()->id)->get();
        return response()->json($result);


    }

}
