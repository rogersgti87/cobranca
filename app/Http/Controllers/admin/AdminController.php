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
use App\Models\Payable;
use App\Models\PayableCategory;
use RuntimeException;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

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

        $userId = auth()->user()->id;
        
        $payable = (object) [
            'total' => Payable::where('user_id', $userId)->count(),
            'pendent' => Payable::where('user_id', $userId)->where('status', 'Pendente')->count(),
            'pay' => Payable::where('user_id', $userId)->where('status', 'Pago')->count(),
            'cancelled' => Payable::where('user_id', $userId)->where('status', 'Cancelado')->count(),
            'due' => Payable::where('user_id', $userId)
                ->where('status', 'Pendente')
                ->whereRaw('CURRENT_DATE > date_due')
                ->count(),
            'five_days' => Payable::where('user_id', $userId)
                ->where('status', 'Pendente')
                ->whereRaw('DATEDIFF(date_due, CURRENT_DATE) = 5')
                ->count(),
            'today' => Payable::where('user_id', $userId)
                ->where('status', 'Pendente')
                ->whereRaw('DATEDIFF(date_due, CURRENT_DATE) = 0')
                ->count(),
            'total_pendente' => (float) Payable::where('user_id', $userId)
                ->where('status', 'Pendente')
                ->sum('price'),
            'total_pago' => (float) Payable::where('user_id', $userId)
                ->where('status', 'Pago')
                ->sum('price')
        ];

        return view($this->datarequest['path'].'.dashboard',compact('total_customers','invoice','payable'))->with($this->datarequest);

    }

    public function chartInvoices(){
        $yearParam = $this->request->input('year', Carbon::now()->year);
        $monthParam = $this->request->input('month', Carbon::now()->month);
        $yearParam = (int) $yearParam;
        $monthParam = (int) $monthParam;

        $year = Invoice::select('status', DB::raw('count(*) as count'))
            ->where('user_id',auth()->user()->id)
            ->whereYear('date_due', $yearParam)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $total_year = array_sum($year);
        $year['Total'] = $total_year;

        $month = Invoice::select('status', DB::raw('count(*) as count'))
        ->where('user_id',auth()->user()->id)
        ->whereMonth('date_due', $monthParam)
        ->whereYear('date_due', $yearParam)
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
        $total_month = array_sum($month);
        $month['Total'] = $total_month;

        return response()->json([
            'year' => $year,
            'month' => $month,
            'selected_year' => $yearParam,
            'selected_month' => $monthParam
        ]);

    }

    public function chartPayables(){
        $yearParam = $this->request->input('year', Carbon::now()->year);
        $monthParam = $this->request->input('month', Carbon::now()->month);
        $yearParam = (int) $yearParam;
        $monthParam = (int) $monthParam;
        
        // Gráfico por categoria - ano selecionado
        $year = Payable::select(
                DB::raw('COALESCE(payable_categories.name, "Sem Categoria") as category'), 
                DB::raw('COALESCE(payable_categories.color, "#6c757d") as category_color'), 
                DB::raw('count(*) as count'), 
                DB::raw('COALESCE(sum(payables.price),0) as total')
            )
            ->leftJoin('payable_categories', 'payable_categories.id', '=', 'payables.category_id')
            ->where('payables.user_id', auth()->user()->id)
            ->whereYear('payables.date_due', $yearParam)
            ->groupBy(DB::raw('COALESCE(payable_categories.name, "Sem Categoria")'), DB::raw('COALESCE(payable_categories.color, "#6c757d")'))
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->category => [
                    'count' => (int) $item->count,
                    'total' => (float) $item->total,
                    'color' => $item->category_color
                ]];
            })
            ->toArray();

        // Gráfico por categoria - mês selecionado
        $month = Payable::select(
                DB::raw('COALESCE(payable_categories.name, "Sem Categoria") as category'), 
                DB::raw('COALESCE(payable_categories.color, "#6c757d") as category_color'), 
                DB::raw('count(*) as count'), 
                DB::raw('COALESCE(sum(payables.price),0) as total')
            )
            ->leftJoin('payable_categories', 'payable_categories.id', '=', 'payables.category_id')
            ->where('payables.user_id', auth()->user()->id)
            ->whereMonth('payables.date_due', $monthParam)
            ->whereYear('payables.date_due', $yearParam)
            ->groupBy(DB::raw('COALESCE(payable_categories.name, "Sem Categoria")'), DB::raw('COALESCE(payable_categories.color, "#6c757d")'))
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->category => [
                    'count' => (int) $item->count,
                    'total' => (float) $item->total,
                    'color' => $item->category_color
                ]];
            })
            ->toArray();

        return response()->json([
            'year' => $year,
            'month' => $month,
            'selected_year' => $yearParam,
            'selected_month' => $monthParam
        ]);
    }




}
