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

        $data = CustomerService::orderby('id','desc')->get();

        return response()->json($data);
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
