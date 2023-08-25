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
use App\Models\Invoice;
use RuntimeException;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class AdminController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Dashboard',
            'link'              => 'admin/',
            'path'              => 'admin'
        ];

    }

    public function index(){

        // $response = Http::withOptions([
        //     'cert' => 'c:\1_inter_crt_file.crt',   // Caminho para o certificado
        //     'ssl_key' => 'c:\1_inter_key_file.key', // Caminho para a chave privada
        // ])->asForm()->post('https://cdpj.partners.bancointer.com.br/oauth/v2/token', [
        //     'client_id' => 'f3be20a1-de7f-4de7-9706-ed4e003b4a31',
        //     'client_secret' => '41366ad8-0417-4685-bc3e-00f31935b3cb',
        //     'scope' => 'boleto-cobranca.read',
        //     'grant_type' => 'client_credentials',
        // ]);

        // $responseBody = $response->body();
        // echo $responseBody;

        $users      = User::where('status',1)->get();

        return view($this->datarequest['path'].'.dashboard',compact('users'))->with($this->datarequest);
    }




}
