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




    //     $user = User::where('id',3)->first();
    //     $access_token = $user['access_token_inter'];

    //     //$transaction_id = '01211758917';

    //     $response = Http::withOptions(
    //         [
    //         'cert' => storage_path('/app/'.$user['inter_crt_file']),
    //         'ssl_key' => storage_path('/app/'.$user['inter_key_file'])
    //         ]
    //         )->withHeaders([
    //         'Authorization' => 'Bearer ' . $access_token
    //     ])->get('https://cdpj.partners.bancointer.com.br/cobranca/v2/boletos?dataInicial=2024-02-24&dataFinal=2024-02-24&filtrarDataPor=VENCIMENTO&situacao=VENCIDO&cpfCnpj=73287021000103&itensPorPagina=100');

    //         if ($response->successful()) {
    //             $responseBody = $response->json();

    //             foreach($responseBody['content'] as $r){

    //                 //if($r['nossoNumero'] != '01237657986'){

    //                     $response_cancel_billet = Http::withOptions([
    //                         'cert' => storage_path('/app/'.$user->inter_crt_file),
    //                         'ssl_key' => storage_path('/app/'.$user->inter_key_file),
    //                     ])->withHeaders([
    //                         'Authorization' => 'Bearer ' . $access_token

    //                     ])->post($user->inter_host.'cobranca/v2/boletos/'.$r['nossoNumero'].'/cancelar',[
    //                         "motivoCancelamento" => "ACERTOS"
    //                     ]);

    //                     if ($response_cancel_billet->successful()) {
    //                         echo 'success';
    //                     }else{
    //                         \Log::info('Erro ao cancelar pagamento intermedium: '.$response_cancel_billet->json());
    //                     }
    //                 //}
    //             }
    //         }

    // return 'ok';








        // $invoices = Invoice::select('id','user_id','payment_method','image_url_pix','pix_digitable')->where('status','Pendente')->where('payment_method','Pix')->get();

        // foreach($invoices as $invoice){
        //     $image = 'https://gerarqrcodepix.com.br/api/v1?brcode=$invoice->pix_digitable';
        //     \File::put(public_path(). '/pix/' . $invoice->user_id.'_'.$invoice->id.'.'.'png', file_get_contents("$image"));
        // }


        // return 1;

        $total_customers  = Customer::where('user_id',auth()->user()->id)->count();
        $invoice = Invoice::select(DB::raw("
        (select count(*) from invoices where user_id = ".auth()->user()->id." ) as total,
        (select count(*) from invoices where status = 'Pendente' and user_id = ".auth()->user()->id.") as pendent,
        (select count(*) from invoices where status = 'Pago' and user_id = ".auth()->user()->id." ) as pay,
        (select count(*) from invoices where status = 'Processamento' and user_id = ".auth()->user()->id." ) as proccessing,
        (select count(*) from invoices where status = 'Cancelado' and user_id = ".auth()->user()->id." ) as cancelled,
        (select count(*) from invoices where status = 'Pendente' and user_id = ".auth()->user()->id." and CURRENT_DATE > date_due) as due,
        (select count(*) from invoices where status = 'Pendente' and user_id = ".auth()->user()->id." and DATEDIFF(date_due, CURRENT_DATE) = 5 ) as five_days,
        (select count(*) from invoices where status = 'Pendente' and user_id = ".auth()->user()->id." and DATEDIFF(date_due, CURRENT_DATE) = 0 ) as today,
        (select count(*) from invoices where status = 'Erro' and user_id = ".auth()->user()->id.") as error
        "))->first();

        return view($this->datarequest['path'].'.dashboard',compact('total_customers','invoice'))->with($this->datarequest);

    }

    public function chartInvoices(){

        $year = Invoice::select('status', DB::raw('count(*) as count'))
            ->where('user_id',auth()->user()->id)
            ->whereYear('date_due', Carbon::now()->year)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $total_year = array_sum($year);
        $year['Total'] = $total_year;

        $month = Invoice::select('status', DB::raw('count(*) as count'))
        ->where('user_id',auth()->user()->id)
        ->whereMonth('date_due', Carbon::now()->month)
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
        $total_month = array_sum($month);
        $month['Total'] = $total_month;




        return response()->json(['year' => $year,'month' => $month ]);

    }




}
