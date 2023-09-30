<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\User;
use App\Models\Service;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;


class CustomerServiceController extends Controller
{


    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Serviços',
            'link'              => 'admin/customer-services',
            'filter'            => 'admin/customer-services?filter',
            'linkFormAdd'       => 'admin/customer-services/form?act=add',
            'linkFormEdit'      => 'admin/customer-services/form?act=edit',
            'linkStore'         => 'admin/customer-services',
            'linkUpdate'        => 'admin/customer-services/',
            'linkCopy'          => 'admin/customer-services/copy',
            'linkDestroy'       => 'admin/customer-services',
            'breadcrumb_new'    => 'Novo Serviço',
            'breadcrumb_edit'   => 'Editar Serviço',
            'path'              => 'admin.customer-service.'
        ];

    }

    public function index(){

        $data = CustomerService::where('user_id',auth()->user()->id)->orderby('id','desc')->get();

        return response()->json($data);
    }

    public function form(){

        $customer_id = $this->request->input('customer_id');

        $customer_id = $customer_id != null ? $customer_id : '';

        $services = Service::where('user_id',auth()->user()->id)->get();
        $data = CustomerService::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

        return view($this->datarequest['path'].'.form',compact('services','data','customer_id'))->render();

    }


    public function store()
    {

        $model = new CustomerService();
        $data = $this->request->all();

        $messages = [
            'service_id.required'           => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo descrição é obrigatório!',
            'price.required'                => 'O campo Valor é obrigatório!',
            'start_billing.required'        => 'O campo Data Inicio Cobrança é obrigatório',
            'period.required'               => 'O campo periodo é obrigatório',
            'gateway_payment.required'      => 'O campo Gateway de pagamento é obrigatório',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'service_id'        =>  'required',
            'description'       =>  'required',
            'price'             =>  'required',
            'start_billing'     =>  'required',
            'period'            =>  'required',
            'gateway_payment'   =>  'required',
            'payment_method'    =>  'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_id         = $data['customer_id'];
        $model->service_id          = $data['service_id'];
        $model->description         = $data['description'];
        $model->status              = $data['status'];
        $model->day_due             = $data['day_due'];
        $model->price               = moeda($data['price']);
        $model->period              = $data['period'];
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];
        $model->start_billing       = $data['start_billing'];
        $model->end_billing         = $data['end_billing'];
        try{
            $model->save();


            if(isset($data['generate_invoice'])){
                $user = User::where('id',auth()->user()->id)->first();



                $newInvoice = new Invoice();
                $newInvoice->user_id             = $model->user_id;
                $newInvoice->customer_service_id = $model->id;
                $newInvoice->description         = $model->description;
                $newInvoice->price               = $model->price;
                $newInvoice->gateway_payment     = $model->gateway_payment;
                $newInvoice->payment_method      = $model->payment_method;
                $newInvoice->date_invoice        = Carbon::now();
                $newInvoice->date_due            = $data['date_due'];
                $newInvoice->date_payment        = null;
                $newInvoice->status              = 'Pendente';
                $newInvoice->save();


                if($model['payment_method'] == 'Pix'){
                    //PIX PAG HIPER
                    if($model['gateway_payment'] == 'Pag Hiper'){
                        $generatePixPH = Invoice::generatePixPH($newInvoice['id']);
                        if($generatePixPH['status'] == 'reject'){
                            return response()->json($generatePixPH['message'], 422);
                        }

                    }elseif($model['gateway_payment'] == 'Mercado Pago'){
                        $generatePixMP = Invoice::generatePixMP($newInvoice['id']);
                        if($generatePixMP['status'] == 'reject'){
                            return response()->json($generatePixMP['message'], 422);
                        }

                    }elseif($model['gateway_payment'] == 'Intermedium'){
                        $generatePixIntermedium = Invoice::generatePixIntermedium($newInvoice['id']);
                        if($generatePixIntermedium['status'] == 'reject'){
                            $msgInterPix = '';
                            foreach($generatePixIntermedium['message'] as $messageInterPix){
                                $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                            }

                            return response()->json($generatePixIntermedium['title'].': '.$msgInterPix, 422);
                        }

                    }
                } elseif($model['payment_method'] == 'Boleto'){

                    if($model['gateway_payment'] == 'Pag Hiper'){
                        $generateBilletPH = Invoice::generateBilletPH($newInvoice['id']);
                        if($generateBilletPH['status'] == 'reject'){
                            return response()->json($generateBilletPH['message'], 422);
                        }

                    }elseif($model['gateway_payment'] == 'Intermedium'){

                        $generateBilletIntermedium = Invoice::generateBilletIntermedium($newInvoice['id']);
                        if($generateBilletIntermedium['status'] == 'reject'){
                            $msgInterBillet = '';
                            foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                                $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                            }

                            return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                        }

                    }


            }

            elseif($model['payment_method'] == 'BoletoPix'){

            if($model['gateway_payment'] == 'Intermedium'){

                $generateBilletIntermedium = Invoice::generateBilletPixIntermedium($model['id']);
                if($generateBilletIntermedium['status'] == 'reject'){
                    $msgInterBillet = '';
                    foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                        $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                    }

                    return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                }

            }
    }


                if(isset($data['send_invoice_email']))
                    InvoiceNotification::Email($newInvoice['id']);

                if(isset($data['send_invoice_whatsapp']))
                    InvoiceNotification::Whatsapp($newInvoice['id']);






            }



        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }




    public function update($id)
    {

        $model = CustomerService::where('id',$id)->where('user_id',auth()->user()->id)->first();

        $data = $this->request->all();


        $messages = [
            'service_id.required'           => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo descrição é obrigatório!',
            'price.required'                => 'O campo Valor é obrigatório!',
            'start_billing.required'        => 'O campo Data Inicio Cobrança é obrigatório',
            'period.required'               => 'O campo periodo é obrigatório',
            'gateway_payment.required'      => 'O campo Gateway de pagamento é obrigatório',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'service_id'        =>  'required',
            'description'       =>  'required',
            'price'             =>  'required',
            'start_billing'     =>  'required',
            'period'            =>  'required',
            'gateway_payment'   =>  'required',
            'payment_method'    =>  'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_id         = $data['customer_id'];
        $model->service_id          = $data['service_id'];
        $model->description         = $data['description'];
        $model->status              = $data['status'];
        $model->day_due             = $data['day_due'];
        $model->price               = moeda($data['price']);
        $model->period              = $data['period'];
        $model->gateway_payment     = $data['gateway_payment'];
        $model->payment_method      = $data['payment_method'];
        $model->start_billing       = $data['start_billing'];
        $model->end_billing         = $data['end_billing'];

        try{
            $model->save();
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }


        return response()->json('Registro salvo com sucesso', 200);

    }

    public function destroy($id)
    {
        $model = new CustomerService();

        try{

            $model->where('id',$id)->where('user_id',auth()->user()->id)->delete();

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }

        return response()->json(true, 200);


    }

    public function Load($customer_id){

        $result = CustomerService::join('services','services.id','customer_services.service_id')
                ->select('customer_services.id as id','customer_services.description','customer_services.price','customer_services.day_due','customer_services.period','customer_services.status','services.name as name')
                ->where('customer_services.customer_id',$customer_id)->where('customer_services.user_id',auth()->user()->id)
                ->orderby('services.name','ASC')
                ->get();
        return response()->json($result);


    }

}
