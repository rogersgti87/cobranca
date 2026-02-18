<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Image;
use DB;
use App\Models\User;
use RuntimeException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    protected $user;

    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Usuários',
            'link'              => 'admin/users',
            'filter'            => 'admin/users?filter',
            'linkFormAdd'       => 'admin/users/form?act=add',
            'linkFormEdit'      => 'admin/users/form?act=edit',
            'linkStore'         => 'admin/users',
            'linkUpdate'        => 'admin/users/',
            'linkCopy'          => 'admin/users/copy',
            'linkDestroy'       => 'admin/users',
            'breadcrumb_new'    => 'Novo usuário',
            'breadcrumb_edit'   => 'Editar usuário',
            'path'              => 'admin.user.'
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

        // Superadmin (ID 1) vê todos os usuários de todas as empresas
        if(auth()->user()->id == 1){
            if($this->request->input('filter')){
                $data = User::orderByRaw("$column_name")
                            ->whereraw("$field $operator $newValue")
                            ->paginate(15);
            }else{
                $data = User::orderByRaw("$column_name")->paginate(15);
            }
        } 
        // Usuários normais veem apenas os usuários da(s) empresa(s) que gerenciam
        else {
            // Pegar IDs das empresas que o usuário tem acesso
            $companyIds = auth()->user()->companies()->pluck('companies.id')->toArray();
            
            // Se não tem empresas, mostra só ele mesmo
            if(empty($companyIds)){
                $companyIds = [auth()->user()->current_company_id];
            }
            
            if($this->request->input('filter')){
                $data = User::orderByRaw("$column_name")
                            ->whereraw("$field $operator $newValue")
                            ->whereHas('companies', function($query) use ($companyIds) {
                                $query->whereIn('companies.id', $companyIds);
                            })
                            ->paginate(15);
            }else{
                $data = User::orderByRaw("$column_name")
                            ->whereHas('companies', function($query) use ($companyIds) {
                                $query->whereIn('companies.id', $companyIds);
                            })
                            ->paginate(15);
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

            $data = User::where('id',$this->request->input('id'))->first();

            return view($this->datarequest['path'].'form',compact('data'))->with($this->datarequest);
        }else{
            return view($this->datarequest['path'].'index')->with($this->datarequest);
        }

    }


    public function store()
    {

        $model = new User;
        $data = $this->request->all();

        $messages = [
            'name.required' => 'O campo nome é obrigatório',
            'email.required' => 'O Campo e-mail é obrigatório',
            'password.required' => 'O Campo senha é obrigatório',
            'password.min' => 'O campo senha precisa ter 6 caracteres',
        ];

        $validator = Validator::make($data, [
            'name'      => 'required',
            'email'     => "required|email|max:255|unique:users,email",
            'password'  => 'required|min:6|confirmed',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        // Validar CPF apenas se foi preenchido
        if(isset($data['document']) && !empty($data['document'])){
            $cpf = preg_replace('/[^0-9]/', '', $data['document']);
            if (strlen($cpf) != 11 || !validarCPF($cpf)) {
                return response()->json('CPF inválido!', 422);
            }
            $model->document = removeEspeciais($data['document']);
        }else{
            $model->document = null;
        }
        $model->name            = $data['name'];
        $model->email           = $data['email'];

        if(isset($data['password']) && $data['password'] != null){
            $model->password = bcrypt($data['password']);
        }
        $model->status          = $data['status'];
        $model->image           = $data['image'];
        $model->telephone       = removeEspeciais($data['telephone']);
        $model->whatsapp        = removeEspeciais($data['whatsapp']);
        $model->cep             = $data['cep'];
        $model->address         = $data['address'];
        $model->number          = $data['number'];
        $model->complement      = $data['complement'];
        $model->district        = $data['district'];
        $model->city            = $data['city'];
        $model->state           = $data['state'];




        try{
            $model->save();
            
            // Vincular o novo usuário à empresa ativa do usuário logado
            if(auth()->user()->current_company_id){
                $model->companies()->attach(auth()->user()->current_company_id, ['role' => 'user']);
                $model->current_company_id = auth()->user()->current_company_id;
                $model->save();
            }
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }


    public function update($id)
    {

        $model = User::where('id',$id)->first();

        $data = $this->request->all();


        $messages = [
            'name.required' => 'O campo nome é obrigatório',
            'email.required' => 'O Campo e-mail é obrigatório',
            'password.required' => 'O Campo senha é obrigatório',
            'password.min' => 'O campo senha precisa ter 6 caracteres',
        ];

        $validator = Validator::make($data, [
            'name'      => 'required',
            'email'     => "required|email|max:255|unique:users,email,$id",
            'password'  => 'nullable|min:6|confirmed',
        ],$messages);



        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        // Validar CPF apenas se foi preenchido
        if(isset($data['document']) && !empty($data['document'])){
            $cpf = preg_replace('/[^0-9]/', '', $data['document']);
            if (strlen($cpf) != 11 || !validarCPF($cpf)) {
                return response()->json('CPF inválido!', 422);
            }
            $model->document = removeEspeciais($data['document']);
        }else{
            $model->document = null;
        }

        $model->name            = $data['name'];
        $model->email           = $data['email'];

        if(isset($data['password']) && $data['password'] != null){
            $model->password = bcrypt($data['password']);
        }

        $model->status          = $data['status'];
        $model->image           = $data['image'];
        $model->telephone       = removeEspeciais($data['telephone']);
        $model->whatsapp        = removeEspeciais($data['whatsapp']);
        $model->cep             = $data['cep'];
        $model->address         = $data['address'];
        $model->number          = $data['number'];
        $model->complement      = $data['complement'];
        $model->district        = $data['district'];
        $model->city            = $data['city'];
        $model->state           = $data['state'];



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
        $model = new User;
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
            return response()->json($e->getMessage(), 500);
        }


        return response()->json(true, 200);
    }
}
