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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        $users      = User::where('status',1)->get();

        $path = public_path('pix/'.'teste'.'.png');

        QrCode::format('png')->generate('Welcome to Makitweb', $path );

        return 1;


        return view($this->datarequest['path'].'.dashboard',compact('users'))->with($this->datarequest);
    }




}
