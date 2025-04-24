<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\User;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


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

        $data = CustomerService::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

        return view($this->datarequest['path'].'.form',compact('data','customer_id'))->render();

    }


    public function store()
    {

        $model = new CustomerService();
        $data = $this->request->all();

        $messages = [
            'description.unique'            => 'O campo descrição é obrigatório!',
            'price.required'                => 'O campo Valor é obrigatório!',
            'start_billing.required'        => 'O campo Data Inicio Cobrança é obrigatório',
            'period.required'               => 'O campo periodo é obrigatório',
            'gateway_payment.required'      => 'O campo Gateway de pagamento é obrigatório',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
        ];

        $validator = Validator::make($data, [
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

        if(isset($data['generate_invoice'])){
            if($data['date_due'] == ''){
                return response()->json('Você marcou a opção Gerar Fatura, Selecione a Data de vencimento.', 422);
            }
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_id         = $data['customer_id'];
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
                $newInvoice->customer_id         = $model->customer_id;
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

                if($newInvoice['payment_method'] == 'Pix'){
                    //PIX PAG HIPER
                    if($newInvoice['gateway_payment'] == 'Pag Hiper'){
                        $generatePixPH = Invoice::generatePixPH($newInvoice['id']);
                        if($generatePixPH['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generatePixPH['message'], 422);
                        }

                    }elseif($newInvoice['gateway_payment'] == 'Mercado Pago'){
                        $generatePixMP = Invoice::generatePixMP($newInvoice['id']);
                        if($generatePixMP['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generatePixMP['message'], 422);
                        }

                    }
                    elseif($newInvoice['gateway_payment'] == 'Intermedium'){
                        $generatePixIntermedium = Invoice::generatePixIntermedium($newInvoice['id']);
                        if($generatePixIntermedium['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            $msgInterPix = '';
                            foreach($generatePixIntermedium['message'] as $messageInterPix){
                                $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                            }

                            return response()->json($generatePixIntermedium['title'].': '.$msgInterPix, 422);
                        }

                    }
                    elseif($model['gateway_payment'] == 'Asaas'){
                        $generatePixAsaas = Invoice::generatePixAsaas($newInvoice['id']);
                        if($generatePixAsaas['status'] == 'reject'){
                            return response()->json($generatePixAsaas['message'], 422);
                        }
                    }
                    elseif($model['gateway_payment'] == 'Estabelecimento'){
                        Invoice::where('id',$newInvoice['id'])->update(['status' => 'Estabelecimento']);
                    }
                } elseif($newInvoice['payment_method'] == 'Boleto'){

                    if($newInvoice['gateway_payment'] == 'Pag Hiper'){
                        $generateBilletPH = Invoice::generateBilletPH($newInvoice['id']);
                        if($generateBilletPH['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generateBilletPH['message'], 422);
                        }

                    }
                    elseif($newInvoice['gateway_payment'] == 'Intermedium'){

                        $generateBilletIntermedium = Invoice::generateBilletIntermedium($newInvoice['id']);
                        if($generateBilletIntermedium['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            $msgInterBillet = '';
                            foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                                $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                            }

                            return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                        }

                    }

                    elseif($model['gateway_payment'] == 'Asaas'){

                        $generateBilletAsaas = Invoice::generateBilletAsaas($newInvoice['id']);
                        if($generateBilletAsaas['status'] == 'reject'){
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generateBilletAsaas['message'], 422);
                        }

                    }
                    elseif($model['gateway_payment'] == 'Estabelecimento'){
                        Invoice::where('id',$newInvoice['id'])->update(['status' => 'Estabelecimento']);
                    }


            }

            elseif($newInvoice['payment_method'] == 'BoletoPix'){

            if($newInvoice['gateway_payment'] == 'Intermedium'){

                $generateBilletIntermedium = Invoice::generateBilletPixIntermedium($newInvoice['id']);
                if($generateBilletIntermedium['status'] == 'reject'){
                    CustomerService::where('id',$model->id)->delete();
                    Invoice::where('id',$newInvoice['id'])->delete();
                    $msgInterBillet = '';
                    foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                        $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'];
                    }

                    return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                }

            }
            elseif($model['gateway_payment'] == 'Estabelecimento'){
                Invoice::where('id',$newInvoice['id'])->update(['status' => 'Estabelecimento']);
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
            'description.unique'            => 'O campo descrição é obrigatório!',
            'price.required'                => 'O campo Valor é obrigatório!',
            'start_billing.required'        => 'O campo Data Inicio Cobrança é obrigatório',
            'period.required'               => 'O campo periodo é obrigatório',
            'gateway_payment.required'      => 'O campo Gateway de pagamento é obrigatório',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
        ];

        $validator = Validator::make($data, [
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

        if(isset($data['generate_invoice'])){
            if($data['date_due'] == ''){
                return response()->json('Você marcou a opção Gerar Fatura, Selecione a Data de vencimento.', 422);
            }
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_id         = $data['customer_id'];
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
                $newInvoice->customer_id         = $model->customer_id;
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

                if($newInvoice['payment_method'] == 'Pix'){
                    //PIX PAG HIPER
                    if($newInvoice['gateway_payment'] == 'Pag Hiper'){
                        $generatePixPH = Invoice::generatePixPH($newInvoice['id']);
                        if($generatePixPH['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generatePixPH['message'], 422);
                        }

                    }elseif($newInvoice['gateway_payment'] == 'Mercado Pago'){
                        $generatePixMP = Invoice::generatePixMP($newInvoice['id']);
                        if($generatePixMP['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generatePixMP['message'], 422);
                        }

                    }
                    elseif($newInvoice['gateway_payment'] == 'Intermedium'){
                        $generatePixIntermedium = Invoice::generatePixIntermedium($newInvoice['id']);
                        if($generatePixIntermedium['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            $msgInterPix = '';
                            foreach($generatePixIntermedium['message'] as $messageInterPix){
                                $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                            }

                            return response()->json($generatePixIntermedium['title'].': '.$msgInterPix, 422);
                        }

                    }
                    elseif($newInvoice['gateway_payment'] == 'Asaas'){
                        $generatePixAsaas = Invoice::generatePixAsaas($newInvoice['id']);
                        if($generatePixAsaas['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generatePixAsaas['message'], 422);
                        }
                    }
                    elseif($model['gateway_payment'] == 'Estabelecimento'){

                        $fileName = $newInvoice->user_id . '_' . $newInvoice['id'];
                        $pixKey = $user->chave_pix;
                        $payload = gerarCodigoPix($pixKey, $newInvoice->price);


                        QrCode::format('png')->size(174)->generate($payload, storage_path('app/public'). '/pix/' . $fileName . '.'.'png');

                        $image_url_pix = env('APP_URL') . Storage::url('pix/' . $fileName . '.png');
                        $qrcode_pix_base64 = base64_encode(file_get_contents($image_url_pix));

                        Invoice::where('id',$newInvoice['id'])->update([
                            'status' => 'Pendente',
                            'pix_digitable' => $payload,
                            'image_url_pix' => $image_url_pix,
                            'qrcode_pix_base64' => $qrcode_pix_base64,
                            'status' => 'Pendente'

                        ]);

                    }
                } elseif($newInvoice['payment_method'] == 'Boleto'){

                    if($newInvoice['gateway_payment'] == 'Pag Hiper'){
                        $generateBilletPH = Invoice::generateBilletPH($newInvoice['id']);
                        if($generateBilletPH['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generateBilletPH['message'], 422);
                        }

                    }
                    elseif($newInvoice['gateway_payment'] == 'Intermedium'){

                        $generateBilletIntermedium = Invoice::generateBilletIntermedium($newInvoice['id']);
                        if($generateBilletIntermedium['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            $msgInterBillet = '';
                            foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                                $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                            }

                            return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                        }
                    }
                    elseif($newInvoice['gateway_payment'] == 'Asaas'){
                        $generateBilletAsaas = Invoice::generateBilletAsaas($newInvoice['id']);
                        if($generateBilletAsaas['status'] == 'reject'){
                            //CustomerService::where('id',$model->id)->delete();
                            Invoice::where('id',$newInvoice['id'])->delete();
                            return response()->json($generateBilletAsaas['message'], 422);
                        }
                    }
                    elseif($model['gateway_payment'] == 'Estabelecimento'){
                        Invoice::where('id',$newInvoice['id'])->update(['status' => 'Estabelecimento']);
                    }


            }

            elseif($newInvoice['payment_method'] == 'BoletoPix'){

            if($newInvoice['gateway_payment'] == 'Intermedium'){

                $generateBilletIntermedium = Invoice::generateBilletPixIntermedium($newInvoice['id']);
                if($generateBilletIntermedium['status'] == 'reject'){
                    //CustomerService::where('id',$model->id)->delete();
                    Invoice::where('id',$newInvoice['id'])->delete();
                    $msgInterBillet = '';
                    foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                        $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'];
                    }

                    return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
                }

            }
            elseif($model['gateway_payment'] == 'Estabelecimento'){
                Invoice::where('id',$newInvoice['id'])->update(['status' => 'Estabelecimento']);
            }
    }


                if(isset($data['send_invoice_email']))
                    InvoiceNotification::Email($newInvoice['id']);

                if(isset($data['send_invoice_whatsapp'])){
                    $whatsapp = InvoiceNotification::Whatsapp($newInvoice['id']);
                    //return response()->json($whatsapp['message']);
                }








            }



        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
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

        $result = CustomerService::select('customer_services.id as id','customer_services.description','customer_services.price',
                'customer_services.day_due','customer_services.period','customer_services.status')
                ->where('customer_services.customer_id',$customer_id)->where('customer_services.user_id',auth()->user()->id)
                ->get();
        return response()->json($result);


    }

}
