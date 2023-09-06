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

        $users      = User::where('status',1)->get();

        return view($this->datarequest['path'].'.dashboard',compact('users'))->with($this->datarequest);
    }

    public function chartInvoices(){

        $invoices = Invoice::select('status', \DB::raw('count(*) as count'))
            ->where('user_id',auth()->user()->id)
            ->whereIn('status', ['Pendente', 'Pago', 'Cancelado', 'Expirado', 'Processamento'])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();


        return response()->json($invoices);

    }




}
