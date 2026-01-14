<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\User;
use App\Models\Payable;
use App\Models\Supplier;
use App\Models\PayableCategory;
use App\Models\PayableReversal;
use RuntimeException;
use Illuminate\Support\Facades\Http;

class PayableController extends Controller
{

    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Contas a pagar',
            'link'              => 'admin/payables',
            'filter'            => 'admin/payables?filter',
            'linkFormAdd'       => 'admin/payables/form?act=add',
            'linkFormEdit'      => 'admin/payables/form?act=edit',
            'linkStore'         => 'admin/payables',
            'linkUpdate'        => 'admin/payables/',
            'linkCopy'          => 'admin/payables/copy',
            'linkDestroy'       => 'admin/payables',
            'breadcrumb_new'    => 'Nova conta',
            'breadcrumb_edit'   => 'Editar conta',
            'path'              => 'admin.payable.'
        ];

    }

    public function index(){

        // Buscar categorias globais (user_id NULL) e do usuário atual
        $categories = PayableCategory::where(function($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', auth()->user()->id);
            })
            ->orderByRaw('CASE WHEN user_id IS NULL THEN 0 ELSE 1 END') // Globais primeiro
            ->orderBy('name','ASC')
            ->get();

        return view('admin.payable.index', compact('categories'))->with($this->datarequest);
    }

    public function form(){

        $supplier_id = $this->request->input('supplier_id');
        $supplier_id = $supplier_id != null ? $supplier_id : '';

        $suppliers = Supplier::where('user_id',auth()->user()->id)
            ->where('status','Ativo')
            ->orderBy('name','ASC')
            ->get();

        // Buscar categorias globais (user_id NULL) e do usuário atual
        $categories = PayableCategory::where(function($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', auth()->user()->id);
            })
            ->orderByRaw('CASE WHEN user_id IS NULL THEN 0 ELSE 1 END') // Globais primeiro
            ->orderBy('name','ASC')
            ->get();

        $data = Payable::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

        return view($this->datarequest['path'].'.form',compact('suppliers','categories','data','supplier_id'))->render();

    }


    public function store()
    {
        $model = new Payable();
        $data = $this->request->all();

        $messages = [
            'supplier_id.required'      => 'O Campo Fornecedor é obrigatório!',
            'description.required'      => 'O campo Descrição é obrigatório!',
            'price.required'            => 'O campo Valor é obrigatório!',
            'type.required'            => 'O campo Tipo é obrigatório!',
            'date_due.required'        => 'O campo Data de vencimento é obrigatório!',
            'category_id.required'     => 'O campo Categoria é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'supplier_id'   => 'required',
            'description'   => 'required',
            'price'         => 'required',
            'type'          => 'required',
            'date_due'      => 'required',
            'category_id'   => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id         = auth()->user()->id;
        $model->supplier_id     = $data['supplier_id'];
        $model->category_id     = isset($data['category_id']) && $data['category_id'] != '' ? $data['category_id'] : null;
        $model->description     = $data['description'];
        $model->price           = moeda($data['price']);
        $model->type            = $data['type']; // Fixa, Recorrente, Parcelada
        $model->payment_method  = isset($data['payment_method']) ? $data['payment_method'] : null;
        $model->date_due        = $data['date_due'];
        $model->date_payment    = isset($data['date_payment']) && $data['date_payment'] != '' ? $data['date_payment'] : null;
        $model->status          = isset($data['date_payment']) && $data['date_payment'] != '' ? 'Pago' : 'Pendente';

        // Campos para conta recorrente
        if($data['type'] == 'Recorrente'){
            $model->recurrence_period = isset($data['recurrence_period']) ? $data['recurrence_period'] : null; // Mensal, Semanal, etc
            $model->recurrence_day    = isset($data['recurrence_day']) ? $data['recurrence_day'] : null;
            $model->recurrence_end    = isset($data['recurrence_end']) ? $data['recurrence_end'] : null;
        }

        // Campos para conta parcelada
        if($data['type'] == 'Parcelada'){
            $model->installments      = isset($data['installments']) ? $data['installments'] : 1;
            $model->installment_number = 1;
            $model->parent_id        = null; // ID da conta pai
        }

        try{
            // Se for parcelada, calcular valores das parcelas com tratamento de dízimas
            if($data['type'] == 'Parcelada' && isset($data['installments']) && $data['installments'] > 1){
                $totalValue = $model->price; // Valor total original
                $numInstallments = $data['installments'];

                // Calcular valor base de cada parcela (arredondado para 2 casas decimais)
                $baseInstallmentValue = round($totalValue / $numInstallments, 2);

                // Calcular o total das parcelas com valor arredondado
                $totalRounded = $baseInstallmentValue * $numInstallments;

                // Calcular a diferença (resto) que será adicionada na primeira parcela
                $difference = round($totalValue - $totalRounded, 2);

                // Primeira parcela recebe o valor base + a diferença (para compensar dízimas)
                $firstInstallmentValue = round($baseInstallmentValue + $difference, 2);

                // Atualizar a primeira parcela com o valor calculado
                $model->price = $firstInstallmentValue;
                $model->description = $data['description'] . ' - Parcela 1/' . $numInstallments;
            }

            $model->save();

            // Se for parcelada, criar as demais parcelas
            if($data['type'] == 'Parcelada' && isset($data['installments']) && $data['installments'] > 1){
                $totalValue = moeda($data['price']); // Valor original total
                $numInstallments = $data['installments'];

                // Calcular valor base (arredondado) para as parcelas 2 em diante
                $baseValue = round($totalValue / $numInstallments, 2);

                $dueDate = Carbon::parse($data['date_due']);
                $originalDay = $dueDate->day; // Capturar o dia original
                $originalMonth = $dueDate->month;
                $originalYear = $dueDate->year;

                for($i = 2; $i <= $numInstallments; $i++){
                    $installment = new Payable();
                    $installment->user_id         = auth()->user()->id;
                    $installment->supplier_id     = $data['supplier_id'];
                    $installment->category_id     = isset($data['category_id']) && $data['category_id'] != '' ? $data['category_id'] : null;
                    $installment->description     = $data['description'] . ' - Parcela ' . $i . '/' . $numInstallments;
                    $installment->price           = $baseValue; // Valor base para as demais parcelas
                    $installment->type            = 'Parcelada';
                    $installment->payment_method  = isset($data['payment_method']) ? $data['payment_method'] : null;

                    // Calcular data de vencimento: adicionar (i-1) meses à data original
                    $nextDueDate = Carbon::create($originalYear, $originalMonth, 1)->addMonths($i - 1);

                    // Verificar quantos dias tem o novo mês
                    $lastDayOfMonth = $nextDueDate->copy()->endOfMonth()->day;

                    // Tentar usar o dia original, mas se não existir no mês, usar o último dia
                    if($originalDay <= $lastDayOfMonth){
                        // O dia original existe no mês, usar ele
                        $nextDueDate->day($originalDay);
                    } else {
                        // O dia original não existe no mês (ex: 31 em fevereiro), usar o último dia
                        $nextDueDate->endOfMonth();
                    }

                    $installment->date_due        = $nextDueDate->format('Y-m-d');

                    $installment->status          = 'Pendente';
                    $installment->installments    = $numInstallments;
                    $installment->installment_number = $i;
                    $installment->parent_id       = $model->id;
                    $installment->save();
                }
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);
    }



    public function update($id)
    {
        $model = Payable::where('id',$id)->where('user_id',auth()->user()->id)->first();

        if(!$model){
            return response()->json('Registro não encontrado', 404);
        }

        $data = $this->request->all();
        $originalStatus = $model->status;

        // Se o status original for "Pago", permitir atualizar todos os campos exceto valor, forma de pagamento e data de pagamento
        if($originalStatus == 'Pago'){
            $messages = [
                'supplier_id.required'      => 'O Campo Fornecedor é obrigatório!',
                'description.required'      => 'O campo Descrição é obrigatório!',
                'date_due.required'        => 'O campo Data de vencimento é obrigatório!',
                'category_id.required'     => 'O campo Categoria é obrigatório!',
                'type.required'            => 'O campo Tipo de Conta é obrigatório!',
            ];

            $validator = Validator::make($data, [
                'supplier_id'   => 'required',
                'description'   => 'required',
                'date_due'      => 'required',
                'category_id'   => 'required',
                'type'          => 'required',
            ], $messages);

            if( $validator->fails() ){
                return response()->json($validator->errors()->first(), 422);
            }

            // Atualizar campos permitidos (não atualizar valor, forma de pagamento e data de pagamento)
            $model->supplier_id     = $data['supplier_id'];
            $model->category_id     = $data['category_id'];
            $model->description     = $data['description'];
            $model->date_due        = $data['date_due'];
            $model->type            = $data['type'];

            // Atualizar campos de recorrência se necessário
            if($data['type'] == 'Recorrente'){
                $model->recurrence_period = isset($data['recurrence_period']) ? $data['recurrence_period'] : null;
                $model->recurrence_day    = isset($data['recurrence_day']) ? $data['recurrence_day'] : null;
                $model->recurrence_end    = isset($data['recurrence_end']) ? $data['recurrence_end'] : null;
            } else {
                // Limpar campos de recorrência se mudou para outro tipo
                $model->recurrence_period = null;
                $model->recurrence_day    = null;
                $model->recurrence_end    = null;
            }

            // Limpar campos de parcelamento se não for mais parcelada
            if($data['type'] != 'Parcelada'){
                $model->installments = null;
                $model->installment_number = null;
                $model->parent_id = null;
            }

            try{
                $model->save();
                return response()->json('Conta atualizada com sucesso', 200);
            } catch(\Exception $e){
                \Log::error($e->getMessage());
                return response()->json($e->getMessage(), 500);
            }
        }

        $messages = [
            'supplier_id.required'      => 'O Campo Fornecedor é obrigatório!',
            'description.required'      => 'O campo Descrição é obrigatório!',
            'price.required'            => 'O campo Valor é obrigatório!',
            'date_due.required'        => 'O campo Data de vencimento é obrigatório!',
            'category_id.required'     => 'O campo Categoria é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'supplier_id'   => 'required',
            'description'   => 'required',
            'price'         => 'required',
            'date_due'      => 'required',
            'category_id'   => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->supplier_id     = $data['supplier_id'];
        $model->category_id     = $data['category_id'];
        $model->description     = $data['description'];
        $model->price           = moeda($data['price']);
        $model->payment_method  = isset($data['payment_method']) ? $data['payment_method'] : null;
        $model->date_due        = $data['date_due'];
        $model->date_payment    = isset($data['date_payment']) && $data['date_payment'] != '' ? $data['date_payment'] : null;

        if(isset($data['date_payment']) && $data['date_payment'] != ''){
            $model->status = 'Pago';
        } else {
            $model->status = 'Pendente';
        }

        // Atualizar tipo se fornecido
        if(isset($data['type']) && !empty($data['type'])){
            $model->type = $data['type'];
        }

        // Atualizar campos de recorrência se necessário
        if($model->type == 'Recorrente'){
            $model->recurrence_period = isset($data['recurrence_period']) ? $data['recurrence_period'] : $model->recurrence_period;
            $model->recurrence_day    = isset($data['recurrence_day']) ? $data['recurrence_day'] : $model->recurrence_day;
            $model->recurrence_end    = isset($data['recurrence_end']) ? $data['recurrence_end'] : $model->recurrence_end;
        }

        try{
            $newPrice = moeda($data['price']);
            $model->save();

            // Se for conta parcelada e o usuário marcou para atualizar parcelas futuras
            if($model->type == 'Parcelada' && isset($data['update_future_installments']) && $data['update_future_installments'] == '1'){
                // Identificar o parent_id (conta pai)
                $parentId = $model->parent_id ?? $model->id;

                // Buscar todas as parcelas do mesmo grupo
                $allInstallments = Payable::where(function($query) use ($parentId) {
                    $query->where('id', $parentId)
                          ->orWhere('parent_id', $parentId);
                })
                ->where('user_id', auth()->user()->id)
                ->where('type', 'Parcelada')
                ->orderBy('installment_number', 'ASC')
                ->get();

                // Atualizar apenas as parcelas futuras (com data de vencimento maior e status Pendente)
                $currentDueDate = Carbon::parse($model->date_due);
                $updatedCount = 0;

                foreach($allInstallments as $installment){
                    // Pular a parcela atual (já foi atualizada)
                    if($installment->id == $model->id){
                        continue;
                    }

                    // Atualizar apenas parcelas futuras (data maior) e que estejam pendentes
                    $installmentDueDate = Carbon::parse($installment->date_due);
                    if($installmentDueDate->gt($currentDueDate) && $installment->status == 'Pendente'){
                        $installment->price = $newPrice;
                        $installment->save();
                        $updatedCount++;
                    }
                }

                if($updatedCount > 0){
                    \Log::info("Atualizadas {$updatedCount} parcelas futuras para o valor de R$ " . number_format($newPrice, 2, ',', '.'));
                }
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        $message = 'Registro salvo com sucesso';
        if(isset($updatedCount) && $updatedCount > 0){
            $message .= ". {$updatedCount} parcela(s) futura(s) atualizada(s) com o novo valor.";
        }

        return response()->json($message, 200);
    }

    public function getReversals($id)
    {
        try{
            $payable = Payable::where('id',$id)->where('user_id',auth()->user()->id)->first();

            if(!$payable){
                return response()->json('Registro não encontrado', 404);
            }

            $reversals = PayableReversal::where('payable_id', $payable->id)
                ->with('user:id,name')
                ->orderBy('reversed_at', 'DESC')
                ->get()
                ->map(function($reversal) {
                    return [
                        'id' => $reversal->id,
                        'reversed_at' => $reversal->reversed_at->format('d/m/Y H:i:s'),
                        'reversal_reason' => $reversal->reversal_reason,
                        'user_name' => $reversal->user->name ?? 'Usuário não encontrado'
                    ];
                });

            return response()->json($reversals, 200);

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getInstallments($id)
    {
        try{
            $payable = Payable::where('id',$id)->where('user_id',auth()->user()->id)->first();

            if(!$payable){
                return response()->json('Registro não encontrado', 404);
            }

            $installments = [];
            $isOriginalRecurring = false;

            // Verificar se é a conta original de uma recorrência
            if($payable->type == 'Recorrente') {
                $hasEarlier = Payable::where('user_id', $payable->user_id)
                    ->where('supplier_id', $payable->supplier_id)
                    ->where('description', $payable->description)
                    ->where('type', 'Recorrente')
                    ->where('date_due', '<', $payable->date_due)
                    ->exists();
                $isOriginalRecurring = !$hasEarlier;
            }

            // Se for uma conta parcelada
            if($payable->type == 'Parcelada'){
                // Se for a primeira parcela (parent_id == null)
                if($payable->parent_id == null){
                    // Buscar todas as parcelas relacionadas
                    $allInstallments = Payable::where(function($query) use ($id) {
                        $query->where('id', $id)
                              ->orWhere('parent_id', $id);
                    })
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('installment_number', 'ASC')
                    ->get();

                    foreach($allInstallments as $inst){
                        $installments[] = [
                            'id' => $inst->id,
                            'description' => $inst->description,
                            'price' => number_format($inst->price, 2, ',', '.'),
                            'date_due' => date('d/m/Y', strtotime($inst->date_due)),
                            'status' => $inst->status,
                            'installment_number' => $inst->installment_number
                        ];
                    }
                } else {
                    // Se for uma parcela filha, buscar todas as parcelas do mesmo grupo
                    $parentId = $payable->parent_id;
                    $allInstallments = Payable::where(function($query) use ($parentId) {
                        $query->where('id', $parentId)
                              ->orWhere('parent_id', $parentId);
                    })
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('installment_number', 'ASC')
                    ->get();

                    foreach($allInstallments as $inst){
                        $installments[] = [
                            'id' => $inst->id,
                            'description' => $inst->description,
                            'price' => number_format($inst->price, 2, ',', '.'),
                            'date_due' => date('d/m/Y', strtotime($inst->date_due)),
                            'status' => $inst->status,
                            'installment_number' => $inst->installment_number
                        ];
                    }
                }
            } else {
                // Se não for parcelada, retornar apenas a conta
                $installments[] = [
                    'id' => $payable->id,
                    'description' => $payable->description,
                    'price' => number_format($payable->price, 2, ',', '.'),
                    'date_due' => date('d/m/Y', strtotime($payable->date_due)),
                    'status' => $payable->status,
                    'installment_number' => null,
                    'is_original_recurring' => $isOriginalRecurring,
                    'type' => $payable->type
                ];
            }

            return response()->json($installments, 200);

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try{
            // Verificar se foi enviado array de IDs no body da requisição
            $ids = $this->request->input('ids');

            // Se não tiver array, usar apenas o ID da URL
            if(!$ids || !is_array($ids)){
                $ids = [$id];
            }

            $payables = Payable::whereIn('id', $ids)
                ->where('user_id', auth()->user()->id)
                ->get();

            if($payables->isEmpty()){
                return response()->json('Registro não encontrado', 404);
            }

            foreach($payables as $payable){
                $payable->status = 'Cancelado';
                $payable->save();
            }

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(true, 200);
    }

    public function stopRecurrence($id)
    {
        try{
            $payable = Payable::where('id',$id)->where('user_id',auth()->user()->id)->first();

            if(!$payable){
                return response()->json('Registro não encontrado', 404);
            }

            // Verificar se é uma conta recorrente
            if($payable->type != 'Recorrente'){
                return response()->json('Esta conta não é do tipo Recorrente', 422);
            }

            // Identificar a conta original da recorrência
            $originalPayable = $payable;
            if($payable->parent_id){
                // Se tem parent_id, buscar a conta original
                $originalPayable = Payable::where('id', $payable->parent_id)
                    ->where('user_id', auth()->user()->id)
                    ->first();
                if(!$originalPayable){
                    $originalPayable = $payable;
                }
            }

            // Buscar todas as contas desta recorrência (original + geradas)
            // Usar a mesma lógica do comando GenerateRecurringPayables
            $allRecurringPayables = Payable::where('user_id', $originalPayable->user_id)
                ->where('supplier_id', $originalPayable->supplier_id)
                ->where('description', $originalPayable->description)
                ->where('type', 'Recorrente')
                ->where('recurrence_period', $originalPayable->recurrence_period)
                ->get();

            // Buscar a última conta gerada (maior data de vencimento)
            $lastPayable = $allRecurringPayables->sortByDesc('date_due')->first();

            // Usar a data da última conta gerada como data de término
            $endDate = $lastPayable ? $lastPayable->date_due : $originalPayable->date_due;

            // Atualizar todas as contas desta recorrência com a data de término
            Payable::whereIn('id', $allRecurringPayables->pluck('id'))
                ->update(['recurrence_end' => $endDate]);

            return response()->json('Recorrência interrompida com sucesso. Não serão geradas novas contas a partir de ' . date('d/m/Y', strtotime($endDate)) . '.', 200);

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    public function reverse($id)
    {
        try{
            $payable = Payable::where('id',$id)->where('user_id',auth()->user()->id)->first();

            if(!$payable){
                return response()->json('Registro não encontrado', 404);
            }

            // Verificar se a conta está paga
            if($payable->status != 'Pago'){
                return response()->json('Apenas contas com status "Pago" podem ser estornadas', 422);
            }

            // Obter motivo do estorno (se fornecido)
            $reversalReason = $this->request->input('reversal_reason', 'Estorno realizado pelo usuário');

            // Estornar: mudar status para Pendente, limpar data de pagamento
            $payable->status = 'Pendente';
            $payable->date_payment = null;
            $payable->save();

            // Registrar o estorno no histórico (mantém todos os estornos)
            PayableReversal::create([
                'payable_id' => $payable->id,
                'user_id' => auth()->user()->id,
                'reversed_at' => Carbon::now(),
                'reversal_reason' => $reversalReason
            ]);

            return response()->json('Conta estornada com sucesso. O status foi alterado para "Pendente" e pode ser paga novamente.', 200);

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }



public function loadPayables(){

    $query = Payable::query();

    $fields = "payables.id as id,payables.description,payables.payment_method,payables.price,payables.date_due,payables.date_payment,payables.status,payables.type,payables.installment_number,payables.installments,payables.recurrence_end,payables.recurrence_period,suppliers.id as supplier_id, suppliers.name as supplier_name,payable_categories.id as category_id,payable_categories.name as category_name,payable_categories.color as category_color,payables.updated_at";

    $query->leftJoin('suppliers','suppliers.id','payables.supplier_id')
            ->leftJoin('payable_categories','payable_categories.id','payables.category_id')
            ->where('payables.user_id',auth()->user()->id);

    // Coletar filtros para usar nas subqueries
    $statuses = [];
    if($this->request->has('status') && $this->request->input('status') != ''){
        $statuses = is_array($this->request->input('status')) ? $this->request->input('status') : [$this->request->input('status')];
        $statuses = array_filter($statuses);
    }

    $types = [];
    if($this->request->has('payable_type') && $this->request->input('payable_type') != ''){
        $types = is_array($this->request->input('payable_type')) ? $this->request->input('payable_type') : [$this->request->input('payable_type')];
        $types = array_filter($types);
    }

    $categories = [];
    if($this->request->has('category') && $this->request->input('category') != ''){
        $categories = is_array($this->request->input('category')) ? $this->request->input('category') : [$this->request->input('category')];
        $categories = array_filter($categories);
    }

    // Construir condições de filtro para as subqueries
    $filterConditions = "user_id = " . auth()->user()->id;

    if(!empty($types)){
        $typesList = "'" . implode("','", array_map(function($t) { return addslashes($t); }, $types)) . "'";
        $filterConditions .= " AND type IN ($typesList)";
    }

    if(!empty($categories)){
        $categoriesList = implode(',', array_map('intval', $categories));
        $filterConditions .= " AND category_id IN ($categoriesList)";
    }

    if ($this->request->has('dateini') && $this->request->has('dateend') && $this->request->input('dateini') != '' && $this->request->input('dateend') != '') {
            $dateType = $this->request->input('type', 'date_due');
            $dateIni = $this->request->input('dateini');
            $dateEnd = $this->request->input('dateend');

            // Adicionar filtro de data às condições
            $dateFilter = $dateType . " BETWEEN '" . addslashes($dateIni) . "' AND '" . addslashes($dateEnd) . "'";
            $fullFilterConditions = $filterConditions . " AND " . $dateFilter;

            // Construir subqueries com todos os filtros aplicados
            $subqueryBase = "from payables where " . $fullFilterConditions;

            // Para totais gerais, aplicar filtros de status se houver
            $subqueryTotal = $subqueryBase;
            if(!empty($statuses)){
                $statusesList = "'" . implode("','", array_map(function($s) { return addslashes($s); }, $statuses)) . "'";
                $subqueryTotal .= " AND status IN ($statusesList)";
            }

            $query->select(DB::raw("$fields,
            (select count(*) $subqueryTotal) as qtd_payables,
            (select COALESCE(sum(price),0) $subqueryTotal) as total_currency,
            (select count(*) $subqueryBase AND status = 'Pendente') as qtd_pendente,
            (select COALESCE(sum(price),0) $subqueryBase AND status = 'Pendente') as pendente_currency,
            (select count(*) $subqueryBase AND status = 'Pago') as qtd_pago,
            (select COALESCE(sum(price),0) $subqueryBase AND status = 'Pago') as pago_currency,
            (select count(*) $subqueryBase AND status = 'Cancelado') as qtd_cancelado,
            (select COALESCE(sum(price),0) $subqueryBase AND status = 'Cancelado') as cancelado_currency,
            (select count(*) $subqueryBase AND type = 'Fixa') as qtd_fixed,
            (select COALESCE(sum(price),0) $subqueryBase AND type = 'Fixa') as fixed_currency,
            (select count(*) $subqueryBase AND type = 'Recorrente') as qtd_recurring,
            (select COALESCE(sum(price),0) $subqueryBase AND type = 'Recorrente') as recurring_currency
            "));

            // Aplicar filtros na query principal
            if(!empty($statuses)){
                $query->whereIn('payables.status', $statuses);
            }

            if(!empty($types)){
                $query->whereIn('payables.type', $types);
            }

            if(!empty($categories)){
                $query->whereIn('payables.category_id', $categories);
            }

            $query->whereBetween('payables.'.$dateType,[$dateIni,$dateEnd]);
    }else{
        // Quando não há filtro de data
        $subqueryBase = "from payables where " . $filterConditions;

        // Para totais gerais, aplicar filtros de status se houver
        $subqueryTotal = $subqueryBase;
        if(!empty($statuses)){
            $statusesList = "'" . implode("','", array_map(function($s) { return addslashes($s); }, $statuses)) . "'";
            $subqueryTotal .= " AND status IN ($statusesList)";
        }

        $query->select(DB::raw("$fields,
        (select count(*) $subqueryTotal) as qtd_payables,
        (select COALESCE(sum(price),0) $subqueryTotal) as total_currency,
        (select count(*) $subqueryBase AND status = 'Pendente') as qtd_pendente,
        (select COALESCE(sum(price),0) $subqueryBase AND status = 'Pendente') as pendente_currency,
        (select count(*) $subqueryBase AND status = 'Pago') as qtd_pago,
        (select COALESCE(sum(price),0) $subqueryBase AND status = 'Pago') as pago_currency,
        (select count(*) $subqueryBase AND status = 'Cancelado') as qtd_cancelado,
        (select COALESCE(sum(price),0) $subqueryBase AND status = 'Cancelado') as cancelado_currency,
        (select count(*) $subqueryBase AND type = 'Fixa') as qtd_fixed,
        (select COALESCE(sum(price),0) $subqueryBase AND type = 'Fixa') as fixed_currency,
        (select count(*) $subqueryBase AND type = 'Recorrente') as qtd_recurring,
        (select COALESCE(sum(price),0) $subqueryBase AND type = 'Recorrente') as recurring_currency
        "));

        // Aplicar filtros na query principal
        if(!empty($statuses)){
            $query->whereIn('payables.status', $statuses);
        }

        if(!empty($types)){
            $query->whereIn('payables.type', $types);
        }

        if(!empty($categories)){
            $query->whereIn('payables.category_id', $categories);
        }
    }

    // Ordenação: Pendentes primeiro (ordem crescente de data), depois os demais
    $query->orderByRaw("CASE WHEN payables.status = 'Pendente' THEN 0 ELSE 1 END")
          ->orderBy('payables.date_due', 'ASC');

    // Criar uma cópia da query para buscar todos os dados (sem paginação) para o gráfico
    $queryForChart = clone $query;
    $allData = $queryForChart->get();

    // Calcular totais por categoria para o gráfico
    $categoriesData = $allData->groupBy('category_id')->map(function ($items, $categoryId) {
        $firstItem = $items->first();
        return [
            'category_id' => $categoryId,
            'category_name' => $firstItem->category_name ?? 'Sem Categoria',
            'category_color' => $firstItem->category_color ?? '#CCCCCC',
            'total' => $items->sum('price'),
            'count' => $items->count()
        ];
    })->values();

    // Agora fazer a paginação na query original
    $data = $query->paginate(20);

    $data->prev_page_url = $data->previousPageUrl();
    $data->next_page_url = $data->nextPageUrl();

    return response()->json([
        'result' => $data,
        'categories' => $categoriesData
    ]);
}

    public function report(){

        // Buscar categorias globais (user_id NULL) e do usuário atual
        $categories = PayableCategory::where(function($query) {
                $query->whereNull('user_id')
                      ->orWhere('user_id', auth()->user()->id);
            })
            ->orderByRaw('CASE WHEN user_id IS NULL THEN 0 ELSE 1 END') // Globais primeiro
            ->orderBy('name','ASC')
            ->get();

        // Buscar fornecedores
        $suppliers = Supplier::where('user_id', auth()->user()->id)
            ->orderBy('name', 'ASC')
            ->get();

        $datarequest = [
            'title'             => 'Relatório de Contas a Pagar',
            'link'              => 'admin/reports/payables',
            'path'              => 'admin.report.payable.'
        ];

        return view('admin.report.payable.index', compact('categories', 'suppliers'))->with($datarequest);
    }

    public function loadReportData(){

        $query = Payable::query();

        $query->leftJoin('suppliers','suppliers.id','payables.supplier_id')
                ->leftJoin('payable_categories','payable_categories.id','payables.category_id')
                ->where('payables.user_id',auth()->user()->id);

        // Filtros
        if($this->request->has('status') && $this->request->input('status') != ''){
            $statuses = is_array($this->request->input('status')) ? $this->request->input('status') : [$this->request->input('status')];
            $statuses = array_filter($statuses);
            if(!empty($statuses)){
                $query->whereIn('payables.status', $statuses);
            }
        }

        if($this->request->has('payable_type') && $this->request->input('payable_type') != ''){
            $types = is_array($this->request->input('payable_type')) ? $this->request->input('payable_type') : [$this->request->input('payable_type')];
            $types = array_filter($types);
            if(!empty($types)){
                $query->whereIn('payables.type', $types);
            }
        }

        if($this->request->has('category') && $this->request->input('category') != ''){
            $categories = is_array($this->request->input('category')) ? $this->request->input('category') : [$this->request->input('category')];
            $categories = array_filter($categories);
            if(!empty($categories)){
                $query->whereIn('payables.category_id', $categories);
            }
        }

        if($this->request->has('supplier') && $this->request->input('supplier') != ''){
            $suppliers = is_array($this->request->input('supplier')) ? $this->request->input('supplier') : [$this->request->input('supplier')];
            $suppliers = array_filter($suppliers);
            if(!empty($suppliers)){
                $query->whereIn('payables.supplier_id', $suppliers);
            }
        }

        if ($this->request->has('dateini') && $this->request->has('dateend') && $this->request->input('dateini') != '' && $this->request->input('dateend') != '') {
            $dateType = $this->request->input('type', 'date_due');
            $dateIni = $this->request->input('dateini');
            $dateEnd = $this->request->input('dateend');
            $query->whereBetween('payables.'.$dateType,[$dateIni,$dateEnd]);
        }

        // Selecionar campos
        $query->select(
            'payables.id',
            'payables.description',
            'payables.payment_method',
            'payables.price',
            'payables.date_due',
            'payables.date_payment',
            'payables.status',
            'payables.type',
            'payables.installment_number',
            'payables.installments',
            'suppliers.id as supplier_id',
            'suppliers.name as supplier_name',
            'payable_categories.id as category_id',
            'payable_categories.name as category_name',
            'payable_categories.color as category_color',
            'payables.created_at',
            'payables.updated_at'
        );

        // Ordenação
        $query->orderBy('payables.date_due', 'ASC');

        // Buscar todos os registros (sem paginação para relatório)
        $data = $query->get();

        // Calcular totais
        $totals = [
            'total' => $data->sum('price'),
            'pendente' => $data->where('status', 'Pendente')->sum('price'),
            'pago' => $data->where('status', 'Pago')->sum('price'),
            'cancelado' => $data->where('status', 'Cancelado')->sum('price'),
            'qtd_total' => $data->count(),
            'qtd_pendente' => $data->where('status', 'Pendente')->count(),
            'qtd_pago' => $data->where('status', 'Pago')->count(),
            'qtd_cancelado' => $data->where('status', 'Cancelado')->count(),
        ];

        // Calcular totais por categoria
        $categoriesData = $data->groupBy('category_id')->map(function ($items, $categoryId) {
            $firstItem = $items->first();
            return [
                'category_id' => $categoryId,
                'category_name' => $firstItem->category_name ?? 'Sem Categoria',
                'category_color' => $firstItem->category_color ?? '#CCCCCC',
                'total' => $items->sum('price'),
                'count' => $items->count()
            ];
        })->values();

        return response()->json([
            'result' => $data,
            'totals' => $totals,
            'categories' => $categoriesData
        ]);
    }




}
