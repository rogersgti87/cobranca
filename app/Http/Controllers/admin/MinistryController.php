<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Image;
use DB;
use App\Models\Ministry;
use RuntimeException;
use ResponseCache;

class MinistryController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Ministérios',
            'link'              => 'admin/ministries',
            'filter'            => 'admin/ministries?filter',
            'linkFormAdd'       => 'admin/ministries/form?act=add',
            'linkFormEdit'      => 'admin/ministries/form?act=edit',
            'linkStore'         => 'admin/ministries',
            'linkUpdate'        => 'admin/ministries/',
            'linkCopy'          => 'admin/ministries/copy',
            'linkDestroy'       => 'admin/ministries',
            'breadcrumb_new'    => 'Novo Ministério',
            'breadcrumb_edit'   => 'Editar Ministério',
            'path'              => 'admin.ministry.'
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
            $data = Ministry::orderByRaw("$column_name")
                        ->whereraw("$field $operator $newValue")
                        ->paginate(15);
        }else{
            $data = Ministry::orderByRaw("$column_name")
                        ->paginate(15);
        }

        foreach($data as $key => $result){
            if(isJSON($result->image) == true){
                $data[$key]['image_thumb']    = property_exists(json_decode($result->image), 'thumb')    ? json_decode($result->image)->thumb : '';
                $data[$key]['image_original'] = property_exists(json_decode($result->image), 'original') ? json_decode($result->image)->original : '';
            }else{
                $data[$key]['image_thumb']    = '';
                $data[$key]['image_original'] = '';
            }
        }

        return view($this->datarequest['path'].'.index',compact('column','order','data'))->with($this->datarequest);
    }

    public function form(){

        if($this->request->input('act') == 'add'){
            return view($this->datarequest['path'].'form')->with($this->datarequest);
        }else if($this->request->input('act') == 'edit'){

            $this->datarequest['linkFormEdit'] = $this->datarequest['linkFormEdit'].'&id='.$this->request->input('id');
            $this->datarequest['linkUpdate']   = $this->datarequest['linkUpdate'].$this->request->input('id');

            $data = Ministry::where('id',$this->request->input('id'))->first();

            if(isJSON($data->image) == true){
                    $data['image_thumb']    = property_exists(json_decode($data->image), 'thumb')    ? json_decode($data->image)->thumb : '';
                    $data['image_original'] = property_exists(json_decode($data->image), 'original') ? json_decode($data->image)->original : '';
                }else{
                    $data['image_thumb']    = '';
                    $data['image_original'] = '';
                }


            return view($this->datarequest['path'].'form',compact('data'))->with($this->datarequest);
        }else{
            return view($this->datarequest['path'].'index')->with($this->datarequest);
        }

    }


    public function store()
    {

        $model = new Ministry;
        $data = $this->request->all();

        $validator = Validator::make($data, [
            'name'     => "required|max:150|unique:ministries,name",
            'status'    => 'required',
        ]);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }
        $model->image   = $data['image'];
        $model->name    = $data['name'];
        $model->status  = $data['status'];

        try{
            $model->save();
            ResponseCache::clear();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }

    public function copy()
    {

        $model = new Ministry;
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = $model->find($result);
                $newRegister = $find->replicate();
                $newRegister->name = $result.'-'.$newRegister->name;
                $newRegister->save();
                ResponseCache::clear();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);

        }


        return response()->json(true, 200);


    }


    public function update($id)
    {

        $model = Ministry::where('id',$id)->first();

        $data = $this->request->all();

        $validator = Validator::make($data, [
            'name'      => "required|max:150|unique:ministries,name,$id",
            'status'    => 'required',
        ]);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->image   = $data['image'];
        $model->name    = $data['name'];
        $model->status  = $data['status'];

        try{
            $model->save();
            ResponseCache::clear();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy()
    {
        $model = new Ministry;
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = $model->where('id',$result);
                $find->delete();
                ResponseCache::clear();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }


        return response()->json(true, 200);


    }

    public function getMinistries(){

        $Ministry = $this->request->input('ministry');

        $result = Ministry::whereraw("name LIKE '%$Ministry%'")
        ->take(20)
        ->get();
        return response()->json($result);


    }


}
