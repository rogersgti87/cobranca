<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PayableCategory;

class PayableCategoryController extends Controller
{

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->datarequest = [
            'title'                 => 'Categorias de Contas a Pagar',
            'link'                  => 'admin/payable-categories',
            'filter'                => 'admin/payable-categories?filter',
            'linkFormAdd'           => 'admin/payable-categories/form?act=add',
            'linkFormEdit'          => 'admin/payable-categories/form?act=edit',
            'linkStore'             => 'admin/payable-categories',
            'linkUpdate'            => 'admin/payable-categories/',
            'linkDestroy'           => 'admin/payable-categories',
            'breadcrumb_new'        => 'Nova Categoria',
            'breadcrumb_edit'       => 'Editar Categoria',
            'path'                  => 'admin.payable-category.'
        ];
    }

    public function index()
    {
        // Buscar categorias globais (user_id NULL) e do usuário atual
        $data = PayableCategory::forCompany(currentCompanyId())
            ->where(function($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', auth()->user()->id);
            })
            ->orderByRaw('CASE WHEN user_id IS NULL THEN 0 ELSE 1 END') // Globais primeiro
            ->orderBy('name', 'ASC')
            ->paginate(20);

        return view($this->datarequest['path'].'index', compact('data'))->with($this->datarequest);
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

                $data = PayableCategory::forCompany(currentCompanyId())
                    ->where('id', $this->request->input('id'))
                    ->where(function($query) {
                        $query->whereNull('user_id')
                              ->orWhere('user_id', auth()->user()->id);
                    })
                    ->first();

                // Verificar se é categoria global (não pode editar)
                if($data && is_null($data->user_id)){
                    return response()->json('Categorias padrão não podem ser editadas', 403);
                }

                return view($this->datarequest['path'].'form', compact('data'))->with($this->datarequest);
            }
        }
        
        // Se não for AJAX, redireciona para index (compatibilidade)
        return redirect()->to('admin/payable-categories');
    }

    public function store()
    {
        $model = new PayableCategory();
        $data = $this->request->all();

        $messages = [
            'name.required'     => 'O campo nome é obrigatório!',
            'name.unique'       => 'Já existe uma categoria com este nome!',
            'color.required'    => 'O campo cor é obrigatório!',
        ];

        // Validação customizada: verificar se já existe categoria com mesmo nome para este usuário
        $exists = PayableCategory::forCompany(currentCompanyId())
            ->where('name', $data['name'])
            ->where('user_id', auth()->user()->id)
            ->first();

        if($exists){
            return response()->json('Já existe uma categoria com este nome!', 422);
        }

        $validator = Validator::make($data, [
            'name'     => 'required',
            'color'    => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->company_id = currentCompanyId();
        $model->user_id    = auth()->user()->id;
        $model->name       = $data['name'];
        $model->color  = isset($data['color']) ? $data['color'] : '#FFBD59';

        try{
            $model->save();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(['data' => 'Registro salvo com sucesso', 'id' => $model->id], 200);
    }

    public function update($id)
    {
        $model = PayableCategory::forCompany(currentCompanyId())
            ->where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if(!$model){
            return response()->json('Registro não encontrado', 404);
        }

        // Verificar se é categoria global (não pode editar)
        if(is_null($model->user_id)){
            return response()->json('Categorias padrão não podem ser editadas', 403);
        }

        $data = $this->request->all();

        $messages = [
            'name.required'     => 'O campo nome é obrigatório!',
            'name.unique'       => 'Já existe uma categoria com este nome!',
            'color.required'    => 'O campo cor é obrigatório!',
        ];

        // Validação customizada: verificar se já existe categoria com mesmo nome para este usuário (exceto a atual)
        $exists = PayableCategory::forCompany(currentCompanyId())
            ->where('name', $data['name'])
            ->where('user_id', auth()->user()->id)
            ->where('id', '!=', $id)
            ->first();

        if($exists){
            return response()->json('Já existe uma categoria com este nome!', 422);
        }

        $validator = Validator::make($data, [
            'name'     => 'required',
            'color'    => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->name  = $data['name'];
        $model->color  = isset($data['color']) ? $data['color'] : $model->color;

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
                $find = PayableCategory::forCompany(currentCompanyId())
                    ->where('id', $result)
                    ->where('user_id', auth()->user()->id)
                    ->first();
                
                if($find){
                    // Verificar se é categoria global (não pode deletar)
                    if(is_null($find->user_id)){
                        return response()->json('Categorias padrão não podem ser excluídas', 403);
                    }

                    // Verificar se há contas a pagar usando esta categoria
                    $payablesCount = \App\Models\Payable::where('category_id', $result)
                        ->where('user_id', auth()->user()->id)
                        ->count();
                    
                    if($payablesCount > 0){
                        return response()->json('Não é possível excluir esta categoria pois existem '.$payablesCount.' conta(s) a pagar vinculada(s) a ela.', 422);
                    }
                    
                    $find->delete();
                }
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(true, 200);
    }
}

