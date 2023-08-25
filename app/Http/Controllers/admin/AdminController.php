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


        // $certPath = '/caminho/para/seu/certificado.crt';

        // $formData = [
        //     'client_id'     => 'f3be20a1-de7f-4de7-9706-ed4e003b4a31',
        //     'client_secret' => '41366ad8-0417-4685-bc3e-00f31935b3cb',
        //     'scope'         => 'boleto-cobranca.read',
        //     'grant_type'    => 'client_credentials'
        // ];

        // $queryString = http_build_query($formData);

        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/x-www-form-urlencoded',
        // ])->post('https://cdpj.partners.bancointer.com.br/oauth/v2/token', $queryString);

        // if ($response->successful()) {
        //     $responseData = $response->json(); // Se a resposta for JSON
        //     dd($responseData);
        //     // Faça algo com os dados da resposta
        // } else {
        //     // Lidar com erros
        //     $statusCode = $response->status();
        //     $errorMessage = $response->body();
        //     dd($statusCode,$errorMessage);
        //     // Lide com o erro de acordo com suas necessidades
        // }


        // Copy code
        // use Illuminate\Support\Facades\Http;

        // class BancoInterController extends Controller
        // {
        //     public function requestWithCertificate()
        //     {
        //         $certPath = '/caminho/para/seu/certificado.crt';
        //         $certPassword = 'senha_do_certificado';

        //         $postData = [
        //             'key1' => 'value1',
        //             'key2' => 'value2',
        //         ];

        //         $postData = http_build_query($postData);

        //         $response = Http::withOptions([
        //             'cert' => [$certPath, $certPassword],
        //         ])->asForm()->post('https://api.bancointer.com.br/endpoint', $postData);

        //         $statusCode = $response->status();
        //         $responseData = $response->json();

        //         // Faça o que for necessário com a resposta da requisição
        //     }
        // }



        $users      = User::where('status',1)->get();

        return view($this->datarequest['path'].'.dashboard',compact('users'))->with($this->datarequest);
    }




}
