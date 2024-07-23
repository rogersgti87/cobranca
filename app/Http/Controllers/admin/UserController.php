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

        if(auth()->user()->id == 1){

            if($this->request->input('filter')){
                    $data = User::orderByRaw("$column_name")
                                ->whereraw("$field $operator $newValue")
                                ->paginate(15);
                }else{
                    $data = User::orderByRaw("$column_name")->paginate(15);
                }

        } else {
            if($this->request->input('filter')){
                    $data = User::orderByRaw("$column_name")
                                ->whereraw("$field $operator $newValue")
                                ->where('id',auth()->user()->id)
                                ->paginate(15);
                }else{
                    $data = User::orderByRaw("$column_name")->where('id',auth()->user()->id)
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


        if(!validarDocumento($data['document'])){
            return response()->json('CPF/CNPJ inválido!', 422);
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
        $model->telephone                   = removeEspeciais($data['telephone']);
        $model->whatsapp                    = removeEspeciais($data['whatsapp']);
        $model->cep                         = $data['cep'];
        $model->address                     = $data['address'];
        $model->number                      = $data['number'];
        $model->complement                  = $data['complement'];
        $model->district                    = $data['district'];
        $model->city                        = $data['city'];
        $model->state                       = $data['state'];
        //$model->api_host_whatsapp           = $data['api_host_whatsapp'];
        //$model->api_access_token_whatsapp   = $data['api_access_token_whatsapp'];
        //$model->token_paghiper              = $data['token_paghiper'];
        //$model->key_paghiper                = $data['key_paghiper'];
        //$model->access_token_mp             = $data['access_token_mp'];
        $model->day_generate_invoice        = $data['day_generate_invoice'];
        $model->send_generate_invoice       = $data['send_generate_invoice'];




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

        if(!validarDocumento($data['document'])){
            return response()->json('CPF/CNPJ inválido!', 422);
        }


        $model->document        = removeEspeciais($data['document']);
        $model->company         = $data['company'];
        $model->name            = $data['name'];
        $model->email           = $data['email'];

        if(isset($data['password']) && $data['password'] != null){
            $model->password = bcrypt($data['password']);
        }

        $model->status                      = $data['status'];
        $model->image                       = $data['image'];
        $model->telephone                   = removeEspeciais($data['telephone']);
        $model->whatsapp                    = removeEspeciais($data['whatsapp']);
        $model->cep                         = $data['cep'];
        $model->address                     = $data['address'];
        $model->number                      = $data['number'];
        $model->complement                  = $data['complement'];
        $model->district                    = $data['district'];
        $model->city                        = $data['city'];
        $model->state                       = $data['state'];
        //$model->api_host_whatsapp           = $data['api_host_whatsapp'];
        //$model->api_access_token_whatsapp   = $data['api_access_token_whatsapp'];
        //$model->token_paghiper              = $data['token_paghiper'];
        //$model->key_paghiper                = $data['key_paghiper'];
        //$model->access_token_mp             = $data['access_token_mp'];
        $model->day_generate_invoice        = $data['day_generate_invoice'];
        $model->send_generate_invoice       = $data['send_generate_invoice'];



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

    public function loadWhatsapp(){

        $data = User::where('id',auth()->user()->id)->select('api_token_whatsapp','api_session_whatsapp','api_status_whatsapp')->first();

        return response()->json($data);


    }


    public function createWhatsapp(){

        if(auth()->user()->api_token_whatsapp != null || auth()->user()->api_session_whatsapp != null || auth()->user()->api_status_whatsapp != null){
            return response()->json(['icon' => 'warning', 'title' => 'Sessão já cadastrada, remova a sessão anterior para cadastrar uma nova.']);
        }

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => config('options.api_key_evolution')
        ])
        ->post(config('options.api_url_evolution').'instance/create',[
            "instanceName"      => auth()->user()->id.'-cobsegura',
            "token"             => auth()->user()->id.'-'.date('ymdhis').str::uuid(),
            "qrcode"            => false,
            "webhook"           => "https://cobrancasegura.com.br/webhook/whatsapp/".auth()->user()->id."-cobsegura",
            "webhookByEvents"   => false,
            "events"            =>  [
                "MESSAGES_UPSERT",
                "MESSAGES_UPDATE",
                "SEND_MESSAGE"
            ]

        ]);

        if ($response->successful()) {
            $result = $response->json();
            User::where('id',auth()->user()->id)->update(['api_session_whatsapp' => $result['instance']['instanceName'],'api_token_whatsapp' => $result['hash']['apikey']]);
            return response()->json(['icon' => 'success', 'title' => 'Sessão criada com sucesso'], 200);
        }else{
            return $response->json('Erro ao criar sessão', 422);
        }


    }


    public function statusWhatsapp(){


        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => auth()->user()->api_token_whatsapp
        ])
        ->get(config('options.api_url_evolution').'instance/connectionState/'.auth()->user()->api_session_whatsapp);

        if ($response->successful()) {

            $result = $response->json();

            if(isset($result['instance']['state'])){
                if($result['instance']['state'] == 'open'){
                    $status = 'Conectado';
                    $icon   = 'success';
                }else{
                    $status = 'Desconectado';
                    $icon   = 'warning';
                }

                User::where('id',auth()->user()->id)->update(['api_status_whatsapp' => $result['instance']['state']]);
            }

           return response()->json(['title' => $status,'icon' => $icon,'msg' => '']);
        }else{
            return $response->json();
        }


    }


    public function qrcodeWhatsapp(){

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => auth()->user()->api_token_whatsapp
        ])
        ->get(config('options.api_url_evolution').'instance/connect/'.auth()->user()->api_session_whatsapp);
        if ($response->successful()) {
            $result = $response->json();
            if(isset($result['code'])){
                return response()->json(['title' => 'Leia o QRCODE','icon' => 'success', 'msg' => '<img src="'.$result['base64'].'" class="image-thumbnail" width="220" height="220">'], 200);
            }else{
                if($result['instance']['state'] == 'open'){
                    $status = 'A Sessão "'.strtoupper(auth()->user()->api_session_whatsapp).'" já está conectada';
                }else{
                    $status = 'Desconectado';
                }
               return response()->json(['title' => $status,'icon' => 'success', 'msg' => ''], 200);
            }

        }else{
            if($response->status() === 404){
                return response()->json(['title' => 'Sessão não existe!','icon' => 'warning','msg' => '']);
            }

        }

    }

    public function logoutWhatsapp(){

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => auth()->user()->api_token_whatsapp
        ])
        ->delete(config('options.api_url_evolution').'instance/logout/'.auth()->user()->api_session_whatsapp);

        if ($response->successful()) {
           return response()->json(['icon' => 'success', 'msg' => 'A Sessão "'.strtoupper(auth()->user()->api_session_whatsapp).'" foi desconectada'], 200);
        }

        if($response->status() === 404){
            return response()->json(['icon' => 'warning','msg' => 'Sessão não existe!']);
        }

        if($response->status() === 400){
            return response()->json(['icon' => 'warning','msg' => 'Sessão já desconectada!']);
        }
    }

    public function deleteWhatsapp()
    {

        if(auth()->user()->api_token_whatsapp == null){
            User::where('id',auth()->user()->id)->update(['api_status_whatsapp' => null,'api_session_whatsapp' => null, 'api_token_whatsapp' => null]);
        }else{
            $response = Http::withHeaders([
                "Content-Type"  => "application/json",
                'apikey'        => auth()->user()->api_token_whatsapp
            ])
            ->delete(config('options.api_url_evolution').'instance/delete/'.auth()->user()->api_session_whatsapp);

            $result = $response->json();

            if($result['status'] === 404){
                User::where('id',auth()->user()->id)->update(['api_status_whatsapp' => null,'api_session_whatsapp' => null, 'api_token_whatsapp' => null]);
                return response()->json(['icon' => 'success', 'msg' => 'A Sessão "'.strtoupper(auth()->user()->api_session_whatsapp).'" foi removida'], 200);
            }

            if($result['status'] === 400){
                return response()->json('Atenção, desconecte a sua sessão antes de remover!',422);
            }

            if ($response->successful()) {
                User::where('id',auth()->user()->id)->update(['api_status_whatsapp' => null,'api_session_whatsapp' => null, 'api_token_whatsapp' => null]);
                return response()->json(['icon' => 'success', 'msg' => 'A Sessão "'.strtoupper(auth()->user()->api_session_whatsapp).'" foi removida'], 200);
            }else{
                if($response->status() === 404){
                    User::where('id',auth()->user()->id)->update(['api_status_whatsapp' => null,'api_session_whatsapp' => null, 'api_token_whatsapp' => null]);
                    return response()->json(['icon' => 'warning','msg' => 'Sessão não existe!']);
                }

            }
        }

    }


    public function inter(){


        $model = User::where('id',auth()->user()->id)->first();

        $data = $this->request->all();

        $model->inter_host                  = $data['inter_host'];
        $model->inter_client_id             = $data['inter_client_id'];
        $model->inter_client_secret         = $data['inter_client_secret'];
        $model->inter_scope                 = $data['inter_scope'];
        $model->inter_webhook_url_billet    = $data['inter_webhook_url_billet'];
        $model->inter_webhook_url_pix       = $data['inter_webhook_url_pix'];
        $model->inter_chave_pix             = $data['inter_chave_pix'];

        if(!file_exists(storage_path('app/certificates')))
            \File::makeDirectory(storage_path('app/certificates'));


        if($this->request->has('inter_crt_file')){
            $inter_crt_file = $this->request->file('inter_crt_file');
            $inter_crt_file->storeAs('certificates/',auth()->user()->id.'_inter_crt_file.crt');
            $model->inter_crt_file = 'certificates/'.auth()->user()->id.'_inter_crt_file.crt';
        }


        if($this->request->has('inter_key_file')){
            $inter_key_file = $this->request->file('inter_key_file');
            $inter_key_file->storeAs('certificates/',auth()->user()->id.'_inter_key_file.key');
            $model->inter_key_file = 'certificates/'.auth()->user()->id.'_inter_key_file.key';
        }

        if($this->request->has('inter_crt_file_webhook')){
            $inter_crt_file_webhook = $this->request->file('inter_crt_file_webhook');
            $inter_crt_file_webhook->storeAs('certificates/',auth()->user()->id.'_inter_crt_file_webhook.crt');
            $model->inter_crt_file_webhook = 'certificates/'.auth()->user()->id.'_inter_crt_file_webhook.crt';
        }

        $model->save();

        $user = User::where('id',auth()->user()->id)->first();

        $access_token = $user['access_token_inter'];

        if($user['inter_host'] == ''){
            return response()->json('HOST banco inter não cadastrado!', 422);
        }
        if($user['inter_client_id'] == ''){
            return response()->json('CLIENT ID banco inter não cadastrado!', 422);
        }
        if($user['inter_client_secret'] == ''){
            return response()->json('CLIENT SECRET banco inter não cadastrado!', 422);
        }
        if($user['inter_crt_file'] == ''){
            return response()->json('Certificado CRT banco inter não cadastrado!', 422);
        }
        if(!file_exists(storage_path('/app/'.$user['inter_crt_file']))){
            return response()->json('Certificado CRT banco inter não existe!', 422);
        }
        if($user['inter_key_file'] == ''){
            return response()->json('Certificado KEY banco inter não cadastrado!', 422);
        }
        if(!file_exists(storage_path('/app/'.$user['inter_key_file']))){
            return response()->json('Certificado KEY banco inter não existe!', 422);
        }

        $check_access_token = Http::withOptions(
            [
            'cert' => storage_path('/app/'.$user['inter_crt_file']),
            'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
            ]
            )->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
        ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v3/cobrancas?dataInicial=2023-01-01&dataFinal=2023-01-01');

        if ($check_access_token->unauthorized()) {
            $response = Http::withOptions([
                'cert' => storage_path('/app/'.$user['inter_crt_file']),
                'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
            ])->asForm()->post($user['inter_host'].'oauth/v2/token', [
                'client_id' => $user['inter_client_id'],
                'client_secret' => $user['inter_client_secret'],
                'scope' => $user['inter_scope'],
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $responseBody = $response->body();
                $access_token = json_decode($responseBody)->access_token;
                User::where('id',$user['id'])->update([
                    'access_token_inter' => $access_token
                ]);

                $user = User::where('id',auth()->user()->id)->first();
            }else{
                return response()->json('Verifique suas credenciais, erro ao autenticar!', 422);
            }
        }


        $response_webhook_billet = Http::withOptions([
            'cert' => storage_path('/app/'.$user['inter_crt_file']),
            'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
            ])->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
          ])->put($user['inter_host'].'cobranca/v2/boletos/webhook',[
            "webhookUrl"=> "https://cobrancasegura.com.br/webhook/intermediumbillet"
        ]);

        if ($response_webhook_billet->status() != 204) {
            return response()->json('Erro ao gravar Webhook Boleto Inter!', 422);
        }

        $response_webhook_pix = Http::withOptions([
            'cert' => storage_path('/app/'.$user['inter_crt_file']),
            'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
            ])->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
          ])->put($user['inter_host'].'pix/v2/webhook/'.$user['inter_chave_pix'],[
            "webhookUrl"=> "https://cobrancasegura.com.br/webhook/intermediumpix"
        ]);

        if ($response_webhook_pix->status() != 204) {
            return response()->json('Erro ao gravar Webhook Boleto Inter!');
        }

        $response_webhook_pix = Http::withOptions([
            'cert' => storage_path('/app/'.$user['inter_crt_file']),
            'ssl_key' => storage_path('/app/'.$user['inter_key_file']),
            ])->withHeaders([
            'Authorization' => 'Bearer ' . $access_token
          ])->put($user['inter_host'].'cobranca/v3/cobrancas/webhook/',[
            "webhookUrl"=> "https://cobrancasegura.com.br/webhook/intermediumpix"
        ]);

        if ($response_webhook_pix->status() != 204) {
            return response()->json('Erro ao gravar Webhook Boleto Inter!');
        }


        return response()->json('Salvo com sucesso!',200);



    }


    public function ph(){


        $model = User::where('id',auth()->user()->id)->first();

        $data = $this->request->all();

        $model->token_paghiper  = $data['token_paghiper'];
        $model->key_paghiper    = $data['key_paghiper'];
        $model->save();

        return response()->json('Salvo com sucesso!',200);


    }

    public function mp(){


        $model = User::where('id',auth()->user()->id)->first();

        $data = $this->request->all();

        $model->access_token_mp  = $data['access_token_mp'];
        $model->save();

        return response()->json('Salvo com sucesso!',200);


    }

    public function asaas(){


        $model = User::where('id',auth()->user()->id)->first();

        $data = $this->request->all();

        $model->environment_asaas   = $data['environment_asaas'];
        $model->at_asaas_prod       = $data['at_asaas_prod'];
        $model->asaas_url_prod      = $data['asaas_url_prod'];
        $model->at_asaas_test       = $data['at_asaas_test'];
        $model->asaas_url_test      = $data['asaas_url_test'];
        $model->save();

        return response()->json('Salvo com sucesso!',200);


    }


}
