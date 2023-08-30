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
        //     'cert' => storage_path('/app/'.$user->inter_crt_file),
        //     'ssl_key' => storage_path('/app/'.$user->inter_key_file),
        // ])->asForm()->post($user->inter_host.'oauth/v2/token', [
        //     'client_id' => $user->inter_client_id,
        //     'client_secret' => $user->inter_client_secret,
        //     'scope' => $user->inter_scope,
        //     'grant_type' => 'client_credentials',
        // ]);

        // $responseBody = $response->body();

        // $access_token = json_decode($responseBody);
        // dd($access_token);


        $users      = User::where('status',1)->get();

        return view($this->datarequest['path'].'.dashboard',compact('users'))->with($this->datarequest);
    }




}
