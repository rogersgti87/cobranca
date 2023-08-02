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
use RuntimeException;

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

        $data = CustomerService::orderby('id','desc')->get();

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
            'service_id.required' => 'O Campo Serviço é obrigatório!',
            'description.unique'   => 'O campo descrição é obrigatório!',
            'price.required'     => 'O campo preço é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'service_id'    => 'required',
            'description'   => 'required',
            'price'         => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id     = auth()->user()->id;
        $model->customer_id = $data['customer_id'];
        $model->service_id  = $data['service_id'];
        $model->description = $data['description'];
        $model->status      = $data['status'];
        $model->day_due     = $data['day_due'];
        $model->price       = moeda($data['price']);
        $model->period      = $data['period'];

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

        $model = CustomerService::where('id',$id)->where('user_id',auth()->user()->id)->first();

        $data = $this->request->all();


        $messages = [
            'service_id.required' => 'O Campo Serviço é obrigatório!',
            'description.unique'   => 'O campo descrição é obrigatório!',
            'price.required'     => 'O campo preço é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'service_id'    => 'required',
            'description'   => 'required',
            'price'         => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id     = auth()->user()->id;
        $model->customer_id = $data['customer_id'];
        $model->service_id  = $data['service_id'];
        $model->description = $data['description'];
        $model->status      = $data['status'];
        $model->day_due     = $data['day_due'];
        $model->price       = moeda($data['price']);
        $model->period      = $data['period'];

        try{
            $model->save();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy()
    {
        $model = new CustomerService();
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = $model->where('id',$result);
                $find->delete();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }


        return response()->json(true, 200);


    }

    public function Load($customer_id){

        $result = CustomerService::where('customer_id',$customer_id)->where('user_id',auth()->user()->id)->get();
        return response()->json($result);


    }


}
