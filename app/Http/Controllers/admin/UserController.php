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


        if($this->request->input('filter')){
            $data = User::orderByRaw("$column_name")
                        ->whereraw("$field $operator $newValue")
                        ->paginate(15);
        }else{
            $data = User::orderByRaw("$column_name")
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

            $data = User::where('id',$this->request->input('id'))->first();

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

        $model->document        = $data['document'];
        $model->company         = $data['company'];
        $model->name            = $data['name'];
        $model->email           = $data['email'];

        if(isset($data['password']) && $data['password'] != null){
            $model->password = bcrypt($data['password']);
        }
        $model->status                      = $data['status'];
        $model->image                       = $data['image'];
        $model->telephone                   = $data['telephone'];
        $model->whatsapp                    = $data['whatsapp'];
        $model->cep                         = $data['cep'];
        $model->address                     = $data['address'];
        $model->number                      = $data['number'];
        $model->complement                  = $data['complement'];
        $model->district                    = $data['district'];
        $model->city                        = $data['city'];
        $model->state                       = $data['state'];
        $model->api_host_whatsapp           = $data['api_host_whatsapp'];
        $model->api_access_token_whatsapp   = $data['api_access_token_whatsapp'];
        $model->host_paghiper               = $data['host_paghiper'];
        $model->token_paghiper              = $data['token_paghiper'];
        $model->key_paghiper                = $data['key_paghiper'];
        $model->access_token_mp             = $data['access_token_mp'];

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

        $model->document        = $data['document'];
        $model->company         = $data['company'];
        $model->name            = $data['name'];
        $model->email           = $data['email'];

        if(isset($data['password']) && $data['password'] != null){
            $model->password = bcrypt($data['password']);
        }

        $model->status                      = $data['status'];
        $model->image                       = $data['image'];
        $model->telephone                   = $data['telephone'];
        $model->whatsapp                    = $data['whatsapp'];
        $model->cep                         = $data['cep'];
        $model->address                     = $data['address'];
        $model->number                      = $data['number'];
        $model->complement                  = $data['complement'];
        $model->district                    = $data['district'];
        $model->city                        = $data['city'];
        $model->state                       = $data['state'];
        $model->api_host_whatsapp           = $data['api_host_whatsapp'];
        $model->api_access_token_whatsapp   = $data['api_access_token_whatsapp'];
        $model->host_paghiper               = $data['host_paghiper'];
        $model->token_paghiper              = $data['token_paghiper'];
        $model->key_paghiper                = $data['key_paghiper'];
        $model->access_token_mp             = $data['access_token_mp'];


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

    public function getSession($session_id){

        $result = Session::where('id',$session_id)->first();

         //Deslogar Sessão
        $response_logout = Http::withHeaders([
            "Content-Type"  => "application/json",
        ])->withtoken($result->token)
        ->post('http://localhost:21465/api/'.$result->session.'/logout-session');

        //$result_logout = $response_logout->getBody();
        //$qrcode = json_decode($result_qrcode);



            //Iniciar sessão
            $response_start_session = Http::withHeaders([
                "Content-Type"  => "application/json",
            ])->withtoken($result->token)
            ->post('http://localhost:21465/api/'.$result->session.'/start-session');

            $result_start_session = $response_start_session->getBody();
            $qrcode = json_decode($result_start_session);


        return response()->json($qrcode);


    }


    public function getQRCODE($session_id){

        $result = Session::where('id',$session_id)->first();

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
        ])->withtoken($result->token)
        ->get('http://localhost:21465/api/'.$result->session.'/status-session');

        $result = $response->getBody();
        return  json_decode($result);
    }


}



}
