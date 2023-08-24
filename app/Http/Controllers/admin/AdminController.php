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



   $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL,"https://cdpj.partners.bancointer.com.br/oauth/v2/token");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,
    http_build_query(array('client_id' => 'f3be20a1-de7f-4de7-9706-ed4e003b4a31',
                                 'client_secret' => '41366ad8-0417-4685-bc3e-00f31935b3cb',
                                 'scope' => 'boleto-cobranca.read',
                                 'grant_type' => 'client_credentials')));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

  // Receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $server_response = curl_exec($ch);
  $error = curl_error($ch);
  $errno = curl_errno($ch);
  curl_close ($ch);

  if ($error !== '') {
      throw new \Exception($error);
  }

  if ($server_response == '') {
      throw new \Exception("Resposta vazia, provavelmente o limite de chamadas foi atingido...\n");
  }

  $obj = json_decode($server_response);

  $bearerToken=$obj->{'access_token'};

  dd($bearerToken);


        $users      = User::where('status',1)->get();

        return view($this->datarequest['path'].'.dashboard',compact('users'))->with($this->datarequest);
    }




}
