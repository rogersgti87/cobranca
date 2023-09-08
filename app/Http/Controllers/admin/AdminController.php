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
use App\Models\Customer;
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

        $total_customers  = Customer::where('user_id',auth()->user()->id)->count();
        $invoice = Invoice::select(DB::raw("
        (select count(*) from invoices where user_id = ".auth()->user()->id." ) as total,
        (select count(*) from invoices where status = 'Pendente') as pendent,
        (select count(*) from invoices where status = 'Pago' and user_id = ".auth()->user()->id." ) as pay,
        (select count(*) from invoices where status = 'Processamento' and user_id = ".auth()->user()->id." ) as proccessing,
        (select count(*) from invoices where status = 'Cancelado' and user_id = ".auth()->user()->id." ) as cancelled,
        (select count(*) from invoices where status = 'Pendente' and user_id = ".auth()->user()->id." and CURRENT_DATE > date_due) as due,
        (select count(*) from invoices where status = 'Pendente' and user_id = ".auth()->user()->id." and DATEDIFF(date_due, CURRENT_DATE) = 5 ) as five_days,
        (select count(*) from invoices where status = 'Pendente' and user_id = ".auth()->user()->id." and DATEDIFF(date_due, CURRENT_DATE) = 0 ) as today
        "))->first();

        return view($this->datarequest['path'].'.dashboard',compact('total_customers','invoice'))->with($this->datarequest);

    }

    public function chartInvoices(){

        $year = Invoice::select('status', DB::raw('count(*) as count'))
            ->where('user_id',auth()->user()->id)
            ->whereYear('date_invoice', Carbon::now()->year)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $total_year = array_sum($year);
        $year['Total'] = $total_year;

        $month = Invoice::select('status', DB::raw('count(*) as count'))
        ->where('user_id',auth()->user()->id)
        ->whereMonth('date_invoice', Carbon::now()->month)
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
        $total_month = array_sum($month);
        $month['Total'] = $total_month;




        return response()->json(['year' => $year,'month' => $month ]);

    }




}
