<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Image;
use DB;
use App\Models\Customer;
use RuntimeException;

class CustomerController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'                 => 'Clientes',
            'link'                  => 'admin/customers',
            'filter'                => 'admin/customers?filter',
            'linkFormAdd'           => 'admin/customers/form?act=add',
            'linkFormEdit'          => 'admin/customers/form?act=edit',
            'linkStore'             => 'admin/customers',
            'linkUpdate'            => 'admin/customers/',
            'linkCopy'              => 'admin/customers/copy',
            'linkDestroy'           => 'admin/customers',
            'breadcrumb_new'        => 'Novo Cliente',
            'breadcrumb_edit'       => 'Editar Cliente',
            'path'                  => 'admin.customer.'
        ];

    }

    public function index(){

        $column    = $this->request->input('column');
        $order     = $this->request->input('order') == 'desc' ? 'asc' : 'desc';

        if($column){
            $column = $this->request->input('column');
            $column_name = "$column $order";
        } else {
            $column_name = "id desc";
        }

        $field     = $this->request->input('field')    ? $this->request->input('field')    : 'name';
        $operator  = $this->request->input('operator') ? $this->request->input('operator') : 'like';
        $value     = $this->request->input('value')    ? $this->request->input('value')    : '';

        if($field == 'data' || $field == 'dataini' || $field == 'datafim'){
            $value = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }

        if($field == 'created_at'){
            $field = 'CAST(created_at as DATE)';
            $value = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        }

        if($operator == 'like'){
            $newValue = "'%$value%'";
        }else{
            $newValue = "'$value'";
        }


        if($this->request->input('filter')){
            $data = Customer::orderByRaw("$column_name")
                        ->where('user_id',auth()->user()->id)
                        ->whereraw("$field $operator $newValue")
                        ->paginate(15);
        }else{
            $data = Customer::orderByRaw("$column_name")->where('user_id',auth()->user()->id)->paginate(15);
        }



        return view($this->datarequest['path'].'.index',compact('column','order','data'))->with($this->datarequest);
    }

    public function form(){

        if($this->request->input('act') == 'add'){
            return view($this->datarequest['path'].'form')->with($this->datarequest);
        }else if($this->request->input('act') == 'edit'){

            $this->datarequest['linkFormEdit'] = $this->datarequest['linkFormEdit'].'&id='.$this->request->input('id');
            $this->datarequest['linkUpdate']   = $this->datarequest['linkUpdate'].$this->request->input('id');

            $data = Customer::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();


            return view($this->datarequest['path'].'form',compact('data'))->with($this->datarequest);
        }else{
            return view($this->datarequest['path'].'index')->with($this->datarequest);
        }

    }


    public function store()
    {

        $model = new Customer;
        $data = $this->request->all();

        $validator = Validator::make($data, [
            'document'     => "required|max:20|unique:customers,document",
        ]);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }
        $model->user_id                 = auth()->user()->id;
        $model->name                    = $data['name'];
        $model->type                    = $data['type'];
        $model->document                = $data['document'];
        $model->company                 = $data['company'];
        $model->email                   = $data['email'];
        $model->email2                  = $data['email2'];
        $model->status                  = $data['status'];
        $model->cep                     = $data['cep'];
        $model->address                 = $data['address'];
        $model->number                  = $data['number'];
        $model->complement              = $data['complement'];
        $model->district                = $data['district'];
        $model->city                    = $data['city'];
        $model->state                   = $data['state'];
        $model->phone                   = $data['phone'];
        $model->whatsapp                = $data['whatsapp'];
        $model->payment_method          = $data['payment_method'];
        $model->notification_whatsapp   = $data['notification_whatsapp'];



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

        $model = Customer::where('id',$id)->first();

        $data = $this->request->all();

        $validator = Validator::make($data, [
            'document'      => "required|max:20|unique:customers,document,$id",
        ]);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id                 = auth()->user()->id;
        $model->name                    = $data['name'];
        $model->type                    = $data['type'];
        $model->document                = $data['document'];
        $model->company                 = $data['company'];
        $model->email                   = $data['email'];
        $model->email2                  = $data['email2'];
        $model->status                  = $data['status'];
        $model->cep                     = $data['cep'];
        $model->address                 = $data['address'];
        $model->number                  = $data['number'];
        $model->complement              = $data['complement'];
        $model->district                = $data['district'];
        $model->city                    = $data['city'];
        $model->state                   = $data['state'];
        $model->phone                   = $data['phone'];
        $model->whatsapp                = $data['whatsapp'];
        $model->payment_method          = $data['payment_method'];
        $model->notification_whatsapp   = $data['notification_whatsapp'];

        try{
            $model->save();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy()
    {
        $model = new Customer;
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = $model->where('id',$result)->where('user_id',auth()->user()->id);
                $find->delete();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json(true, 200);


    }


}
