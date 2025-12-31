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

        return view('admin.payable.index')->with($this->datarequest);
    }

    public function form(){

        $supplier_id = $this->request->input('supplier_id');
        $supplier_id = $supplier_id != null ? $supplier_id : '';

        $suppliers = Supplier::where('user_id',auth()->user()->id)
            ->where('status','Ativo')
            ->orderBy('name','ASC')
            ->get();

        $data = Payable::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

        return view($this->datarequest['path'].'.form',compact('suppliers','data','supplier_id'))->render();

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
        ];

        $validator = Validator::make($data, [
            'supplier_id'   => 'required',
            'description'   => 'required',
            'price'         => 'required',
            'type'          => 'required',
            'date_due'      => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id         = auth()->user()->id;
        $model->supplier_id     = $data['supplier_id'];
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
            $model->save();

            // Se for parcelada, criar as demais parcelas
            if($data['type'] == 'Parcelada' && isset($data['installments']) && $data['installments'] > 1){
                $installmentValue = $model->price / $data['installments'];
                $dueDate = Carbon::parse($data['date_due']);

                for($i = 2; $i <= $data['installments']; $i++){
                    $installment = new Payable();
                    $installment->user_id         = auth()->user()->id;
                    $installment->supplier_id     = $data['supplier_id'];
                    $installment->description     = $data['description'] . ' - Parcela ' . $i . '/' . $data['installments'];
                    $installment->price           = $installmentValue;
                    $installment->type            = 'Parcelada';
                    $installment->payment_method  = isset($data['payment_method']) ? $data['payment_method'] : null;
                    $installment->date_due        = $dueDate->copy()->addMonths($i - 1)->format('Y-m-d');
                    $installment->status          = 'Pendente';
                    $installment->installments    = $data['installments'];
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

        $messages = [
            'supplier_id.required'      => 'O Campo Fornecedor é obrigatório!',
            'description.required'      => 'O campo Descrição é obrigatório!',
            'price.required'            => 'O campo Valor é obrigatório!',
            'date_due.required'        => 'O campo Data de vencimento é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'supplier_id'   => 'required',
            'description'   => 'required',
            'price'         => 'required',
            'date_due'      => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->supplier_id     = $data['supplier_id'];
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

        // Atualizar campos de recorrência se necessário
        if($model->type == 'Recorrente'){
            $model->recurrence_period = isset($data['recurrence_period']) ? $data['recurrence_period'] : $model->recurrence_period;
            $model->recurrence_day    = isset($data['recurrence_day']) ? $data['recurrence_day'] : $model->recurrence_day;
            $model->recurrence_end    = isset($data['recurrence_end']) ? $data['recurrence_end'] : $model->recurrence_end;
        }

        try{
            $model->save();

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);
    }

    public function destroy($id)
    {
        try{
            $payable = Payable::where('id',$id)->where('user_id',auth()->user()->id)->first();

            if(!$payable){
                return response()->json('Registro não encontrado', 404);
            }

            // Se for uma conta parcelada, verificar se é a primeira parcela
            if($payable->type == 'Parcelada' && $payable->parent_id == null){
                // Cancelar todas as parcelas
                Payable::where('parent_id',$id)->where('user_id',auth()->user()->id)->update(['status' => 'Cancelado']);
            }

            $payable->status = 'Cancelado';
            $payable->save();

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(true, 200);
    }



public function loadPayables(){

    $query = Payable::query();

    $fields = "payables.id as id,payables.description,payables.payment_method,payables.price,payables.date_due,payables.date_payment,payables.status,payables.type,payables.installment_number,payables.installments,suppliers.id as supplier_id, suppliers.name as supplier_name,payables.updated_at";

    $query->leftJoin('suppliers','suppliers.id','payables.supplier_id')
            ->where('payables.user_id',auth()->user()->id);

    if ($this->request->has('dateini') && $this->request->has('dateend') && $this->request->input('dateini') != '' && $this->request->input('dateend') != '') {
            $dateType = $this->request->input('type', 'date_due');
            $dateIni = $this->request->input('dateini');
            $dateEnd = $this->request->input('dateend');

            $query->select(DB::raw("$fields,
            (select count(*) from payables where ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as qtd_payables ,
            (select COALESCE(sum(price),0) from payables where ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as total_currency ,
            (select count(*) from payables where status = 'Pendente' and ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as qtd_pendente,
            (select COALESCE(sum(price),0) from payables where status = 'Pendente' and ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as pendente_currency,
            (select count(*) from payables where status = 'Pago'  and ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as qtd_pago,
            (select COALESCE(sum(price),0) from payables where status = 'Pago'  and ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as pago_currency,
            (select count(*) from payables where status = 'Cancelado'  and ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as qtd_cancelado,
            (select COALESCE(sum(price),0) from payables where status = 'Cancelado'  and ".$dateType." between '".$dateIni."' and '".$dateEnd."' and user_id = ".auth()->user()->id." ) as cancelado_currency
            "));

            // Filtro de status - aceita múltiplos valores
            if($this->request->has('status') && $this->request->input('status') != ''){
                $statuses = is_array($this->request->input('status')) ? $this->request->input('status') : [$this->request->input('status')];
                $statuses = array_filter($statuses); // Remove valores vazios
                if(!empty($statuses)){
                    $query->whereIn('payables.status', $statuses);
                }
            }

            // Filtro de tipo - aceita múltiplos valores
            if($this->request->has('payable_type') && $this->request->input('payable_type') != ''){
                $types = is_array($this->request->input('payable_type')) ? $this->request->input('payable_type') : [$this->request->input('payable_type')];
                $types = array_filter($types); // Remove valores vazios
                if(!empty($types)){
                    $query->whereIn('payables.type', $types);
                }
            }

            $query->whereBetween('payables.'.$dateType,[$dateIni,$dateEnd]);
    }else{
        // Quando não há filtros, mostra todas as contas (ou um intervalo maior)
        $query->select(DB::raw("$fields,
        (select count(*) from payables where user_id = ".auth()->user()->id." ) as qtd_payables ,
            (select COALESCE(sum(price),0) from payables where user_id = ".auth()->user()->id." ) as total_currency ,
            (select count(*) from payables where status = 'Pendente' and user_id = ".auth()->user()->id." ) as qtd_pendente,
            (select COALESCE(sum(price),0) from payables where status = 'Pendente' and user_id = ".auth()->user()->id." ) as pendente_currency,
            (select count(*) from payables where status = 'Pago'  and user_id = ".auth()->user()->id." ) as qtd_pago,
            (select COALESCE(sum(price),0) from payables where status = 'Pago'  and user_id = ".auth()->user()->id." ) as pago_currency,
            (select count(*) from payables where status = 'Cancelado'  and user_id = ".auth()->user()->id." ) as qtd_cancelado,
            (select COALESCE(sum(price),0) from payables where status = 'Cancelado'  and user_id = ".auth()->user()->id." ) as cancelado_currency
        "));
        // Não aplica filtro de data quando não há filtros - mostra todas as contas
        
        // Filtro de status quando não há filtro de data
        if($this->request->has('status') && $this->request->input('status') != ''){
            $statuses = is_array($this->request->input('status')) ? $this->request->input('status') : [$this->request->input('status')];
            $statuses = array_filter($statuses); // Remove valores vazios
            if(!empty($statuses)){
                $query->whereIn('payables.status', $statuses);
            }
        }
        
        // Filtro de tipo quando não há filtro de data
        if($this->request->has('payable_type') && $this->request->input('payable_type') != ''){
            $types = is_array($this->request->input('payable_type')) ? $this->request->input('payable_type') : [$this->request->input('payable_type')];
            $types = array_filter($types); // Remove valores vazios
            if(!empty($types)){
                $query->whereIn('payables.type', $types);
            }
        }
    }

    // Ordenação: Pendentes primeiro (ordem crescente de data), depois os demais
    $query->orderByRaw("CASE WHEN payables.status = 'Pendente' THEN 0 ELSE 1 END")
          ->orderBy('payables.date_due', 'ASC');

    $data = $query->paginate(20);

    $data->prev_page_url = $data->previousPageUrl();
    $data->next_page_url = $data->nextPageUrl();

    return response()->json(['result' => $data]);
}




}
