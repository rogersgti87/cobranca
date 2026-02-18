<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;
use App\Models\Supplier;
use RuntimeException;

class SupplierController extends Controller
{

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->datarequest = [
            'title'                 => 'Fornecedores',
            'link'                  => 'admin/suppliers',
            'filter'                => 'admin/suppliers?filter',
            'linkFormAdd'           => 'admin/suppliers/form?act=add',
            'linkFormEdit'          => 'admin/suppliers/form?act=edit',
            'linkStore'             => 'admin/suppliers',
            'linkUpdate'            => 'admin/suppliers/',
            'linkCopy'              => 'admin/suppliers/copy',
            'linkDestroy'           => 'admin/suppliers',
            'breadcrumb_new'        => 'Novo Fornecedor',
            'breadcrumb_edit'       => 'Editar Fornecedor',
            'path'                  => 'admin.supplier.'
        ];
    }

    public function index()
    {
        $column    = $this->request->input('column');
        $order     = $this->request->input('order') == 'desc' ? 'asc' : 'desc';

        if($column){
            $column = $this->request->input('column');
            $column_name = "$column $order";
        } else {
            $column_name = "name asc";
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
            $data = Supplier::orderByRaw("$column_name")
                        ->forCompany(currentCompanyId())
                        ->whereraw("$field $operator $newValue")
                        ->orderby('name','ASC')
                        ->paginate(20);
        }else{
            $data = Supplier::orderByRaw("$column_name")->forCompany(currentCompanyId())->paginate(20);
        }

        return view($this->datarequest['path'].'.index',compact('column','order','data'))->with($this->datarequest);
    }

    public function form()
    {
        // Se for requisição AJAX, retorna apenas o conteúdo do formulário
        if($this->request->ajax()){
            if($this->request->input('act') == 'add'){
                $this->datarequest['linkFormEdit'] = $this->datarequest['linkFormEdit'].'&id=';
                return view($this->datarequest['path'].'form')->with($this->datarequest);
            }else if($this->request->input('act') == 'edit'){
                $this->datarequest['linkFormEdit'] = $this->datarequest['linkFormEdit'].'&id='.$this->request->input('id');
                $this->datarequest['linkUpdate']   = $this->datarequest['linkUpdate'].$this->request->input('id');

                $data = Supplier::forCompany(currentCompanyId())->where('id',$this->request->input('id'))->first();

                return view($this->datarequest['path'].'form',compact('data'))->with($this->datarequest);
            }
        }
        
        // Se não for AJAX, redireciona para index (compatibilidade)
        return redirect()->to('admin/suppliers');
    }

    public function store()
    {
        $model = new Supplier;
        $data = $this->request->all();

        $messages = [
            'name.required'     => 'O campo nome é obrigatório!',
            'name.unique'       => 'Já existe um fornecedor com este nome nesta empresa!',
            'state.max'         => 'O Campo estado deve ter no máximo 2 caracteres!',
        ];

        $validator = Validator::make($data, [
            'name'     => 'required|unique:suppliers,name,NULL,id,company_id,'.currentCompanyId(),
            'state'    => 'nullable|max:2',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->company_id  = currentCompanyId();
        $model->user_id     = auth()->user()->id;
        $model->name        = $data['name'];
        $model->type        = isset($data['type']) ? $data['type'] : 'Física';
        $model->document    = isset($data['document']) ? removeEspeciais($data['document']) : null;
        $model->company     = isset($data['company']) ? $data['company'] : null;
        $model->email       = isset($data['email']) ? Str::lower($data['email']) : null;
        $model->email2      = isset($data['email2']) ? Str::lower($data['email2']) : null;
        $model->status      = isset($data['status']) ? $data['status'] : 'Ativo';
        $model->cep         = isset($data['cep']) ? $data['cep'] : null;
        $model->address     = isset($data['address']) ? $data['address'] : null;
        $model->number      = isset($data['number']) ? $data['number'] : null;
        $model->complement  = isset($data['complement']) ? $data['complement'] : null;
        $model->district    = isset($data['district']) ? $data['district'] : null;
        $model->city        = isset($data['city']) ? $data['city'] : null;
        $model->state       = isset($data['state']) ? $data['state'] : null;
        $model->phone       = isset($data['phone']) ? removeEspeciais($data['phone']) : null;
        $model->whatsapp    = isset($data['whatsapp']) ? removeEspeciais($data['whatsapp']) : null;
        $model->obs         = isset($data['obs']) ? $data['obs'] : null;

        try{
            $model->save();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(['data' => 'Registro salvo com sucesso','id' => $model->id], 200);
    }

    public function update($id)
    {
        $model = Supplier::forCompany(currentCompanyId())->where('id',$id)->first();

        if(!$model){
            return response()->json('Registro não encontrado', 404);
        }

        $data = $this->request->all();

        $messages = [
            'name.required'     => 'O campo nome é obrigatório!',
            'name.unique'       => 'Já existe um fornecedor com este nome nesta empresa!',
            'state.max'         => 'O Campo estado deve ter no máximo 2 caracteres!',
        ];

        $validator = Validator::make($data, [
            'name'     => 'required|unique:suppliers,name,'.$id.',id,company_id,'.currentCompanyId(),
            'state'    => 'nullable|max:2',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->name        = $data['name'];
        $model->type        = isset($data['type']) ? $data['type'] : $model->type;
        $model->document    = isset($data['document']) ? removeEspeciais($data['document']) : $model->document;
        $model->company     = isset($data['company']) ? $data['company'] : $model->company;
        $model->email       = isset($data['email']) ? Str::lower($data['email']) : $model->email;
        $model->email2      = isset($data['email2']) ? Str::lower($data['email2']) : $model->email2;
        $model->status      = isset($data['status']) ? $data['status'] : $model->status;
        $model->cep         = isset($data['cep']) ? $data['cep'] : $model->cep;
        $model->address     = isset($data['address']) ? $data['address'] : $model->address;
        $model->number      = isset($data['number']) ? $data['number'] : $model->number;
        $model->complement  = isset($data['complement']) ? $data['complement'] : $model->complement;
        $model->district    = isset($data['district']) ? $data['district'] : $model->district;
        $model->city        = isset($data['city']) ? $data['city'] : $model->city;
        $model->state       = isset($data['state']) ? $data['state'] : $model->state;
        $model->phone       = isset($data['phone']) ? removeEspeciais($data['phone']) : $model->phone;
        $model->whatsapp    = isset($data['whatsapp']) ? removeEspeciais($data['whatsapp']) : $model->whatsapp;
        $model->obs         = isset($data['obs']) ? $data['obs'] : $model->obs;

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
        $data = $this->request->all();

        if(!isset($data['selected'])){
            return response()->json('Selecione ao menos um registro', 422);
        }

        try{
            foreach($data['selected'] as $result){
                $find = Supplier::forCompany(currentCompanyId())->where('id',$result);
                $find->delete();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(true, 200);
    }

    public function loadSuppliers()
    {
        $suppliers = Supplier::forCompany(currentCompanyId())
            ->where('status','Ativo')
            ->orderBy('name','ASC')
            ->get();

        return response()->json($suppliers);
    }
}

