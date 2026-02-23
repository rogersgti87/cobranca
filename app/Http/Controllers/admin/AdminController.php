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
use App\Models\Supplier;
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

        $total_customers  = Customer::forUserCompanies()->count();
        $companyIds = userCompanyIds();
        $companyIdsStr = implode(',', $companyIds);
        
        $invoice = Invoice::select(DB::raw("
        (select count(*) from invoices where company_id IN ({$companyIdsStr}) ) as total,
        (select count(*) from invoices where status = 'Pendente' and company_id IN ({$companyIdsStr})) as pendent,
        (select count(*) from invoices where status = 'Pago' and company_id IN ({$companyIdsStr}) ) as pay,
        (select count(*) from invoices where status = 'Processamento' and company_id IN ({$companyIdsStr}) ) as proccessing,
        (select count(*) from invoices where status = 'Cancelado' and company_id IN ({$companyIdsStr}) ) as cancelled,
        (select count(*) from invoices where status = 'Pendente' and company_id IN ({$companyIdsStr}) and CURRENT_DATE > date_due) as due,
        (select count(*) from invoices where status = 'Pendente' and company_id IN ({$companyIdsStr}) and DATEDIFF(date_due, CURRENT_DATE) = 5 ) as five_days,
        (select count(*) from invoices where status = 'Pendente' and company_id IN ({$companyIdsStr}) and DATEDIFF(date_due, CURRENT_DATE) = 0 ) as today,
        (select count(*) from invoices where status = 'Erro' and company_id IN ({$companyIdsStr})) as error
        "))->first();

        $payable = (object) [
            'total' => Payable::forUserCompanies()->count(),
            'pendent' => Payable::forUserCompanies()->where('status', 'Pendente')->count(),
            'pay' => Payable::forUserCompanies()->where('status', 'Pago')->count(),
            'cancelled' => Payable::forUserCompanies()->where('status', 'Cancelado')->count(),
            'due' => Payable::forUserCompanies()
                ->where('status', 'Pendente')
                ->whereRaw('CURRENT_DATE > date_due')
                ->count(),
            'five_days' => Payable::forUserCompanies()
                ->where('status', 'Pendente')
                ->whereRaw('DATEDIFF(date_due, CURRENT_DATE) = 5')
                ->count(),
            'today' => Payable::forUserCompanies()
                ->where('status', 'Pendente')
                ->whereRaw('DATEDIFF(date_due, CURRENT_DATE) = 0')
                ->count(),
            'total_pendente' => (float) Payable::forUserCompanies()
                ->where('status', 'Pendente')
                ->sum('price'),
            'total_pago' => (float) Payable::forUserCompanies()
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
            ->forUserCompanies()
            ->whereYear('date_due', $yearParam)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $total_year = array_sum($year);
        $year['Total'] = $total_year;

        $month = Invoice::select('status', DB::raw('count(*) as count'))
        ->forUserCompanies()
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
            ->forUserCompanies()
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
            ->forUserCompanies()
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

    public function chartReceitasDespesas(){
        $yearParam = $this->request->input('year', Carbon::now()->year);
        $monthParam = $this->request->input('month', Carbon::now()->month);
        $yearParam = (int) $yearParam;
        $monthParam = (int) $monthParam;

        // Receitas (Invoices) por mês do ano
        $receitasYear = Invoice::select(
                DB::raw('MONTH(date_due) as month'),
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->whereYear('date_due', $yearParam)
            ->where('status', 'Pago')
            ->groupBy(DB::raw('MONTH(date_due)'))
            ->get()
            ->mapWithKeys(function($item) {
                return [(int)$item->month => (float)$item->total];
            })
            ->toArray();

        // Despesas (Payables) por mês do ano
        $despesasYear = Payable::select(
                DB::raw('MONTH(date_due) as month'),
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->whereYear('date_due', $yearParam)
            ->where('status', 'Pago')
            ->groupBy(DB::raw('MONTH(date_due)'))
            ->get()
            ->mapWithKeys(function($item) {
                return [(int)$item->month => (float)$item->total];
            })
            ->toArray();

        // Receitas do mês selecionado
        $receitasMonth = Invoice::select(
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->whereMonth('date_due', $monthParam)
            ->whereYear('date_due', $yearParam)
            ->where('status', 'Pago')
            ->first();

        // Despesas do mês selecionado
        $despesasMonth = Payable::select(
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->whereMonth('date_due', $monthParam)
            ->whereYear('date_due', $yearParam)
            ->where('status', 'Pago')
            ->first();

        // Preencher todos os meses do ano (meses sem dados = 0)
        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $receitasYearData = [];
        $despesasYearData = [];

        for($i = 1; $i <= 12; $i++) {
            $receitasYearData[] = isset($receitasYear[$i]) ? $receitasYear[$i] : 0;
            $despesasYearData[] = isset($despesasYear[$i]) ? $despesasYear[$i] : 0;
        }

        return response()->json([
            'year' => [
                'labels' => $months,
                'receitas' => $receitasYearData,
                'despesas' => $despesasYearData
            ],
            'month' => [
                'receitas' => (float)($receitasMonth->total ?? 0),
                'despesas' => (float)($despesasMonth->total ?? 0)
            ],
            'selected_year' => $yearParam,
            'selected_month' => $monthParam
        ]);
    }

    public function receitaDespesa(){
        $this->datarequest['title'] = 'Receita x Despesa';
        $this->datarequest['link'] = 'admin/receita-despesa';
        $this->datarequest['path'] = 'admin.receita-despesa';

        return view('admin.receita-despesa.index', $this->datarequest);
    }

    public function chartReceitaDespesaDiario(){
        $yearParam = $this->request->input('year', Carbon::now()->year);
        $monthParam = $this->request->input('month', Carbon::now()->month);
        $yearParam = (int) $yearParam;
        $monthParam = (int) $monthParam;

        // Receitas por dia do mês - separar por date_payment e date_due
        $receitasComPayment = Invoice::select(
                DB::raw('DAY(date_payment) as day'),
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->whereNotNull('date_payment')
            ->where('date_payment', '!=', '0000-00-00')
            ->whereMonth('date_payment', $monthParam)
            ->whereYear('date_payment', $yearParam)
            ->groupBy(DB::raw('DAY(date_payment)'))
            ->get()
            ->mapWithKeys(function($item) {
                return [(int)$item->day => (float)$item->total];
            })
            ->toArray();

        $receitasSemPayment = Invoice::select(
                DB::raw('DAY(date_due) as day'),
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->where(function($q) {
                $q->whereNull('date_payment')
                  ->orWhere('date_payment', '0000-00-00');
            })
            ->whereMonth('date_due', $monthParam)
            ->whereYear('date_due', $yearParam)
            ->groupBy(DB::raw('DAY(date_due)'))
            ->get()
            ->mapWithKeys(function($item) {
                return [(int)$item->day => (float)$item->total];
            })
            ->toArray();

        // Combinar receitas
        $receitasDiarias = [];
        foreach($receitasComPayment as $day => $total) {
            $receitasDiarias[$day] = ($receitasDiarias[$day] ?? 0) + $total;
        }
        foreach($receitasSemPayment as $day => $total) {
            $receitasDiarias[$day] = ($receitasDiarias[$day] ?? 0) + $total;
        }

        // Despesas por dia do mês - separar por date_payment e date_due
        $despesasComPayment = Payable::select(
                DB::raw('DAY(date_payment) as day'),
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->whereNotNull('date_payment')
            ->where('date_payment', '!=', '0000-00-00')
            ->whereMonth('date_payment', $monthParam)
            ->whereYear('date_payment', $yearParam)
            ->groupBy(DB::raw('DAY(date_payment)'))
            ->get()
            ->mapWithKeys(function($item) {
                return [(int)$item->day => (float)$item->total];
            })
            ->toArray();

        $despesasSemPayment = Payable::select(
                DB::raw('DAY(date_due) as day'),
                DB::raw('COALESCE(sum(price),0) as total')
            )
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->where(function($q) {
                $q->whereNull('date_payment')
                  ->orWhere('date_payment', '0000-00-00');
            })
            ->whereMonth('date_due', $monthParam)
            ->whereYear('date_due', $yearParam)
            ->groupBy(DB::raw('DAY(date_due)'))
            ->get()
            ->mapWithKeys(function($item) {
                return [(int)$item->day => (float)$item->total];
            })
            ->toArray();

        // Combinar despesas
        $despesasDiarias = [];
        foreach($despesasComPayment as $day => $total) {
            $despesasDiarias[$day] = ($despesasDiarias[$day] ?? 0) + $total;
        }
        foreach($despesasSemPayment as $day => $total) {
            $despesasDiarias[$day] = ($despesasDiarias[$day] ?? 0) + $total;
        }

        // Total do mês - Receitas
        $totalReceitasComPayment = Invoice::select(DB::raw('COALESCE(sum(price),0) as total'))
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->whereNotNull('date_payment')
            ->where('date_payment', '!=', '0000-00-00')
            ->whereMonth('date_payment', $monthParam)
            ->whereYear('date_payment', $yearParam)
            ->first();

        $totalReceitasSemPayment = Invoice::select(DB::raw('COALESCE(sum(price),0) as total'))
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->where(function($q) {
                $q->whereNull('date_payment')
                  ->orWhere('date_payment', '0000-00-00');
            })
            ->whereMonth('date_due', $monthParam)
            ->whereYear('date_due', $yearParam)
            ->first();

        $totalReceitasValue = (float)($totalReceitasComPayment->total ?? 0) + (float)($totalReceitasSemPayment->total ?? 0);

        // Total do mês - Despesas
        $totalDespesasComPayment = Payable::select(DB::raw('COALESCE(sum(price),0) as total'))
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->whereNotNull('date_payment')
            ->where('date_payment', '!=', '0000-00-00')
            ->whereMonth('date_payment', $monthParam)
            ->whereYear('date_payment', $yearParam)
            ->first();

        $totalDespesasSemPayment = Payable::select(DB::raw('COALESCE(sum(price),0) as total'))
            ->forUserCompanies()
            ->where('status', 'Pago')
            ->where(function($q) {
                $q->whereNull('date_payment')
                  ->orWhere('date_payment', '0000-00-00');
            })
            ->whereMonth('date_due', $monthParam)
            ->whereYear('date_due', $yearParam)
            ->first();

        $totalDespesasValue = (float)($totalDespesasComPayment->total ?? 0) + (float)($totalDespesasSemPayment->total ?? 0);

        // Obter número de dias do mês
        $daysInMonth = Carbon::create($yearParam, $monthParam, 1)->daysInMonth;

        // Preencher todos os dias do mês (dias sem dados = 0)
        $labels = [];
        $receitasData = [];
        $despesasData = [];

        for($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = $i;
            $receitasData[] = isset($receitasDiarias[$i]) ? $receitasDiarias[$i] : 0;
            $despesasData[] = isset($despesasDiarias[$i]) ? $despesasDiarias[$i] : 0;
        }

        // Calcular saldo (Receitas - Despesas)
        $saldo = $totalReceitasValue - $totalDespesasValue;

        // Configurar locale para Carbon e formatar nome do mês
        try {
            Carbon::setLocale('pt_BR');
            $monthName = Carbon::create($yearParam, $monthParam, 1)->translatedFormat('F \d\e Y');
        } catch (\Exception $e) {
            // Fallback caso o locale não esteja disponível
            $months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                      'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            $monthName = $months[$monthParam - 1] . ' de ' . $yearParam;
        }

        return response()->json([
            'labels' => $labels,
            'receitas' => $receitasData,
            'despesas' => $despesasData,
            'total_receitas' => $totalReceitasValue,
            'total_despesas' => $totalDespesasValue,
            'saldo' => $saldo,
            'selected_year' => $yearParam,
            'selected_month' => $monthParam,
            'month_name' => $monthName
        ]);
    }

    public function projecoes(){
        // Buscar categorias globais (company_id NULL) e da empresa atual
        $categories = PayableCategory::where(function($query) {
                $query->whereNull('company_id')
                      ->orWhere('company_id', currentCompanyId());
            })
            ->orderByRaw('CASE WHEN company_id IS NULL THEN 0 ELSE 1 END') // Globais primeiro
            ->orderBy('name','ASC')
            ->get();

        // Buscar fornecedores
        $suppliers = Supplier::forUserCompanies()
            ->orderBy('name', 'ASC')
            ->get();

        $datarequest = [
            'title'             => 'Projeções Futuras',
            'link'              => 'admin/projecoes',
            'path'              => 'admin.projecoes.'
        ];

        return view('admin.projecoes.index', compact('categories', 'suppliers'))->with($datarequest);
    }

    public function loadProjecoesData(){
        $filterType = $this->request->input('filter_type', '12'); // current-month, next-month, ou número
        $monthsAhead = $this->request->input('months_ahead', 12);
        $monthsAhead = $monthsAhead ? (int) $monthsAhead : 12;

        // Calcular datas de início e fim baseado no tipo de filtro
        $today = Carbon::today();
        $filterStartDate = $today->copy();
        $filterEndDate = $today->copy();

        if($filterType === 'current-month') {
            // Mês atual: do primeiro dia do mês até o último dia do mês
            $filterStartDate = $today->copy()->startOfMonth();
            $filterEndDate = $today->copy()->endOfMonth();
        } elseif($filterType === 'next-month') {
            // Próximo mês: do primeiro dia do próximo mês até o último dia do próximo mês
            $filterStartDate = $today->copy()->addMonth()->startOfMonth();
            $filterEndDate = $today->copy()->addMonth()->endOfMonth();
        } else {
            // Período de X meses à frente
            $filterEndDate = $today->copy()->addMonths($monthsAhead);
        }

        // Filtros
        $categoryFilter = $this->request->input('category');
        $supplierFilter = $this->request->input('supplier');
        $recurrencePeriodFilter = $this->request->input('recurrence_period');

        // Buscar todas as contas recorrentes ativas da empresa
        $query = Payable::with(['supplier', 'category'])
            ->forUserCompanies()
            ->where('type', 'Recorrente')
            ->where('status', '!=', 'Cancelado')
            ->whereNotNull('recurrence_period');

        // Aplicar filtros
        if($categoryFilter && $categoryFilter != ''){
            $query->where('category_id', $categoryFilter);
        }

        if($supplierFilter && $supplierFilter != ''){
            $query->where('supplier_id', $supplierFilter);
        }

        if($recurrencePeriodFilter && $recurrencePeriodFilter != ''){
            $query->where('recurrence_period', $recurrencePeriodFilter);
        }

        // Buscar apenas as contas originais (primeira de cada recorrência)
        $recurringPayables = $query->get()->filter(function($payable) {
            // Verificar se é a conta original (primeira da recorrência)
            $hasEarlier = Payable::forUserCompanies()
                ->where('supplier_id', $payable->supplier_id)
                ->where('description', $payable->description)
                ->where('type', 'Recorrente')
                ->where('date_due', '<', $payable->date_due)
                ->exists();

            return !$hasEarlier;
        });

        $projections = [];
        $today = Carbon::today();
        $endDate = $today->copy()->addMonths($monthsAhead);

        foreach($recurringPayables as $payable) {
            // Verificar se já passou da data de término
            if($payable->recurrence_end && Carbon::parse($payable->recurrence_end)->isPast()) {
                continue;
            }

            // Buscar a última conta gerada desta recorrência
            $lastPayable = Payable::forUserCompanies()
                ->where('supplier_id', $payable->supplier_id)
                ->where('description', $payable->description)
                ->where('type', 'Recorrente')
                ->where('recurrence_period', $payable->recurrence_period)
                ->orderBy('date_due', 'DESC')
                ->first();

            // Usar a última conta gerada ou a original como base
            $basePayable = $lastPayable ?: $payable;
            $baseDueDate = Carbon::parse($basePayable->date_due);

            // Se a data base já passou, calcular a próxima data válida a partir da data inicial do filtro
            if($baseDueDate->isPast()) {
                // Calcular quantas recorrências já passaram desde a data base até a data inicial do filtro
                $currentDate = $baseDueDate->copy();
                $iterationsBeforeFilter = 0;
                while($currentDate->lt($filterStartDate) && $iterationsBeforeFilter < 100) {
                    $nextDate = $this->calculateNextProjectionDate($payable, $currentDate);
                    if(!$nextDate) {
                        break;
                    }
                    if($nextDate->gt($filterEndDate)) {
                        break;
                    }
                    $currentDate = $nextDate;
                    $iterationsBeforeFilter++;
                }
                // Se ainda está antes da data inicial do filtro, começar da data inicial
                if($currentDate->lt($filterStartDate)) {
                    $currentDate = $filterStartDate->copy();
                }
            } else {
                // Se a data base é futura, usar a maior entre a data base e a data inicial do filtro
                $currentDate = $baseDueDate->gte($filterStartDate) ? $baseDueDate->copy() : $filterStartDate->copy();
            }

            // Gerar projeções até a data limite ou até a data de término da recorrência
            $maxIterations = 200; // Limite de segurança
            $iterations = 0;

            $payableProjectionsCount = 0;

            // Se a data atual já está dentro do período do filtro, começar a partir dela
            // Caso contrário, calcular a próxima data que cai dentro do período
            $dateToCheck = $currentDate->copy();

            while($iterations < $maxIterations && $dateToCheck->lte($filterEndDate)) {
                $iterations++;

                // Verificar se a data atual está dentro do período
                if($dateToCheck->gte($filterStartDate) && $dateToCheck->lte($filterEndDate)) {
                    // Verificar se passou da data de término
                    if($payable->recurrence_end && $dateToCheck->gt(Carbon::parse($payable->recurrence_end))) {
                        break;
                    }

                    // Adicionar projeção
                    $projections[] = [
                        'description' => $payable->description,
                        'supplier_id' => $payable->supplier_id,
                        'supplier_name' => $payable->supplier->name ?? '',
                        'category_id' => $payable->category_id,
                        'category_name' => $payable->category->name ?? '',
                        'category_color' => $payable->category->color ?? '',
                        'price' => (float) $payable->price,
                        'date_due' => $dateToCheck->format('Y-m-d'),
                        'recurrence_period' => $payable->recurrence_period,
                        'payment_method' => $payable->payment_method,
                    ];
                    $payableProjectionsCount++;
                }

                // Calcular próxima data baseada no período de recorrência
                $nextDate = $this->calculateNextProjectionDate($payable, $dateToCheck);

                if(!$nextDate) {
                    break;
                }

                // Verificar se passou da data de término
                if($payable->recurrence_end && $nextDate->gt(Carbon::parse($payable->recurrence_end))) {
                    break;
                }

                // Verificar se passou da data limite
                if($nextDate->gt($filterEndDate)) {
                    break;
                }

                $dateToCheck = $nextDate;
            }
        }

        // Ordenar por data de vencimento
        usort($projections, function($a, $b) {
            return strcmp($a['date_due'], $b['date_due']);
        });

        // Calcular totais por mês
        $monthlyTotals = [];
        foreach($projections as $projection) {
            $monthKey = Carbon::parse($projection['date_due'])->format('Y-m');
            if(!isset($monthlyTotals[$monthKey])) {
                $monthlyTotals[$monthKey] = [
                    'month' => Carbon::parse($projection['date_due'])->format('m/Y'),
                    'total' => 0,
                    'count' => 0
                ];
            }
            $monthlyTotals[$monthKey]['total'] += $projection['price'];
            $monthlyTotals[$monthKey]['count']++;
        }

        // Calcular total geral
        $totalAmount = array_sum(array_column($projections, 'price'));

        return response()->json([
            'projections' => $projections,
            'monthly_totals' => array_values($monthlyTotals),
            'total_amount' => $totalAmount,
            'total_count' => count($projections),
            'months_ahead' => $monthsAhead,
            'debug' => [
                'filter_type' => $filterType,
                'filter_start_date' => $filterStartDate->format('Y-m-d'),
                'filter_end_date' => $filterEndDate->format('Y-m-d'),
                'projections_count' => count($projections)
            ]
        ]);
    }

    private function calculateNextProjectionDate($payable, $currentDate)
    {
        $nextDate = null;

        switch($payable->recurrence_period) {
            case 'Semanal':
                $nextDate = Carbon::parse($currentDate)->addWeek();
                break;

            case 'Quinzenal':
                $nextDate = Carbon::parse($currentDate)->addDays(15);
                break;

            case 'Mensal':
                $nextDate = Carbon::parse($currentDate)->addMonth();
                if($payable->recurrence_day) {
                    $nextDate->day($payable->recurrence_day);
                }
                break;

            case 'Bimestral':
                $nextDate = Carbon::parse($currentDate)->addMonths(2);
                if($payable->recurrence_day) {
                    $nextDate->day($payable->recurrence_day);
                }
                break;

            case 'Trimestral':
                $nextDate = Carbon::parse($currentDate)->addMonths(3);
                if($payable->recurrence_day) {
                    $nextDate->day($payable->recurrence_day);
                }
                break;

            case 'Semestral':
                $nextDate = Carbon::parse($currentDate)->addMonths(6);
                if($payable->recurrence_day) {
                    $nextDate->day($payable->recurrence_day);
                }
                break;

            case 'Anual':
                $nextDate = Carbon::parse($currentDate)->addYear();
                if($payable->recurrence_day) {
                    $nextDate->day($payable->recurrence_day);
                }
                break;

            default:
                return null;
        }

        return $nextDate;
    }

    public function exportProjecoesPdf(){
        $monthsAhead = $this->request->input('months_ahead', 12);
        $monthsAhead = (int) $monthsAhead;

        // Filtros
        $categoryFilter = $this->request->input('category');
        $supplierFilter = $this->request->input('supplier');
        $recurrencePeriodFilter = $this->request->input('recurrence_period');

        // Buscar todas as contas recorrentes ativas da empresa
        $query = Payable::with(['supplier', 'category'])
            ->forUserCompanies()
            ->where('type', 'Recorrente')
            ->where('status', '!=', 'Cancelado')
            ->whereNotNull('recurrence_period');

        // Aplicar filtros
        if($categoryFilter && is_array($categoryFilter) && count($categoryFilter) > 0){
            $query->whereIn('category_id', $categoryFilter);
        }

        if($supplierFilter && is_array($supplierFilter) && count($supplierFilter) > 0){
            $query->whereIn('supplier_id', $supplierFilter);
        }

        if($recurrencePeriodFilter && is_array($recurrencePeriodFilter) && count($recurrencePeriodFilter) > 0){
            $query->whereIn('recurrence_period', $recurrencePeriodFilter);
        }

        // Buscar apenas as contas originais (primeira de cada recorrência)
        $recurringPayables = $query->get()->filter(function($payable) {
            $hasEarlier = Payable::forUserCompanies()
                ->where('supplier_id', $payable->supplier_id)
                ->where('description', $payable->description)
                ->where('type', 'Recorrente')
                ->where('date_due', '<', $payable->date_due)
                ->exists();

            return !$hasEarlier;
        });

        $projections = [];
        $today = Carbon::today();
        $endDate = $today->copy()->addMonths($monthsAhead);

        foreach($recurringPayables as $payable) {
            if($payable->recurrence_end && Carbon::parse($payable->recurrence_end)->isPast()) {
                continue;
            }

            $lastPayable = Payable::forUserCompanies()
                ->where('supplier_id', $payable->supplier_id)
                ->where('description', $payable->description)
                ->where('type', 'Recorrente')
                ->where('recurrence_period', $payable->recurrence_period)
                ->orderBy('date_due', 'DESC')
                ->first();

            $basePayable = $lastPayable ?: $payable;
            $startDate = Carbon::parse($basePayable->date_due);

            if($startDate->isPast()) {
                $currentDate = $startDate->copy();
                while($currentDate->lt($startDate)) {
                    $nextDate = $this->calculateNextProjectionDate($payable, $currentDate);
                    if(!$nextDate || $nextDate->gt($endDate)) {
                        break;
                    }
                    $currentDate = $nextDate;
                }
                if($currentDate->lt($startDate)) {
                    $currentDate = $startDate->copy();
                }
            } else {
                $currentDate = $startDate->copy();
            }

            $maxIterations = 200;
            $iterations = 0;

            while($iterations < $maxIterations && $currentDate->lte($endDate)) {
                $iterations++;

                $nextDate = $this->calculateNextProjectionDate($payable, $currentDate);

                if(!$nextDate) {
                    break;
                }

                if($payable->recurrence_end && $nextDate->gt(Carbon::parse($payable->recurrence_end))) {
                    break;
                }

                if($nextDate->gt($endDate)) {
                    break;
                }

                if($nextDate->gte($today)) {
                    $projections[] = [
                        'description' => $payable->description,
                        'supplier_name' => $payable->supplier->name ?? '',
                        'category_name' => $payable->category->name ?? '',
                        'price' => (float) $payable->price,
                        'date_due' => $nextDate->format('Y-m-d'),
                        'recurrence_period' => $payable->recurrence_period,
                        'payment_method' => $payable->payment_method,
                    ];
                }

                $currentDate = $nextDate;
            }
        }

        usort($projections, function($a, $b) {
            return strcmp($a['date_due'], $b['date_due']);
        });

        // Calcular totais por mês
        $monthlyTotals = [];
        foreach($projections as $projection) {
            $monthKey = Carbon::parse($projection['date_due'])->format('Y-m');
            if(!isset($monthlyTotals[$monthKey])) {
                $monthlyTotals[$monthKey] = [
                    'month' => Carbon::parse($projection['date_due'])->format('m/Y'),
                    'total' => 0,
                    'count' => 0
                ];
            }
            $monthlyTotals[$monthKey]['total'] += $projection['price'];
            $monthlyTotals[$monthKey]['count']++;
        }

        $totalAmount = array_sum(array_column($projections, 'price'));

        $user = User::where('id', auth()->user()->id)->first();

        $filters = [
            'months_ahead' => $monthsAhead,
            'category' => $categoryFilter,
            'supplier' => $supplierFilter,
            'recurrence_period' => $recurrencePeriodFilter
        ];

        return view('admin.projecoes.pdf', compact('projections', 'monthlyTotals', 'totalAmount', 'filters', 'user'));
    }

}
