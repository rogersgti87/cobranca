<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\Service;
use RuntimeException;

class ServiceController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Serviços',
            'link'              => 'admin/services',
            'filter'            => 'admin/services?filter',
            'linkFormAdd'       => 'admin/services/form?act=add',
            'linkFormEdit'      => 'admin/services/form?act=edit',
            'linkStore'         => 'admin/services',
            'linkUpdate'        => 'admin/services/',
            'linkCopy'          => 'admin/services/copy',
            'linkDestroy'       => 'admin/services',
            'breadcrumb_new'    => 'Novo Serviço',
            'breadcrumb_edit'   => 'Editar Serviço',
            'path'              => 'admin.service.'
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

        if($field == 'data' || $field == 'dataini' || $field == 'datafim' || $field == 'date'){
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
                $data = Service::orderByRaw("$column_name")
                            ->forUserCompanies()
                            ->whereraw("$field $operator $newValue")
                            ->paginate(15);
            }else{
                $data = Service::orderByRaw("$column_name")->forUserCompanies()->paginate(15);
            }


        return view($this->datarequest['path'].'.index',compact('column','order','data'))->with($this->datarequest);
    }

    public function form(){

        if($this->request->input('act') == 'add'){
            return view($this->datarequest['path'].'form')->with($this->datarequest);
        }else if($this->request->input('act') == 'edit'){
            $this->datarequest['linkFormEdit'] = $this->datarequest['linkFormEdit'].'&id='.$this->request->input('id');
            $this->datarequest['linkUpdate']   = $this->datarequest['linkUpdate'].$this->request->input('id');

            $data = Service::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

            return view($this->datarequest['path'].'form',compact('data'))->with($this->datarequest);
        }else{
            return view($this->datarequest['path'].'index')->with($this->datarequest);
        }

    }


    public function store()
    {

        $newService         = new Service;

        $data = $this->request->all();

        $messages = [
            'name.required'         => 'O campo nome é obrigatório',
            'name.unique'           => 'Já existe um serviço com esse nome',
            'price.required'        => 'O campo preço é obrigatório',
        ];

        $validator = Validator::make($data, [
            'name'          => "required|unique:services,name,null,null,user_id,".auth()->user()->id,
            'price'         => "required",
        ],$messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $newService->company_id     = currentCompanyId();
        $newService->user_id        = auth()->user()->id;
        $newService->name           = $data['name'];
        $newService->price          = moeda($data['price']);
        $newService->status         = $data['status'];


        try{
            $newService->save();

        } catch(\Exception $e){
            \Log::info($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }


    public function update($id)
    {

        $newService = Service::forUserCompanies()->where('id',$id)->first();

        $data = $this->request->all();

        $messages = [
            'name.required'         => 'O campo nome é obrigatório',
            'name.unique'           => 'Já existe um serviço com esse nome',
            'price.required'        => 'O campo Preço é obrigatório',
        ];

        $validator = Validator::make($data, [
            'name'      => "required|unique:services,name,$id,id,user_id,".auth()->user()->id,
            'price'     => "required",
        ],$messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }



        $newService->user_id        = auth()->user()->id;
        $newService->name           = $data['name'];
        $newService->price          = moeda($data['price']);
        $newService->status         = $data['status'];


        try{
            $newService->save();

        } catch(\Exception $e){
            \Log::info($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy()
    {
        $model = new Service;
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = Service::forUserCompanies()->where('id',$result);
                $find->delete();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }


        return response()->json(true, 200);


    }

}
