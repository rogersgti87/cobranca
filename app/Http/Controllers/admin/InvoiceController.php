<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Image;
use DB;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use App\Models\Service;
use App\Models\CustomerService;
use App\Models\Customer;
use RuntimeException;

class InvoiceController extends Controller
{

    public function __construct(Request $request)
    {
        $this->request              = $request;

        $this->datarequest = [
            'title'             => 'Faturas',
            'link'              => 'admin/invoices',
            'filter'            => 'admin/invoices?filter',
            'linkFormAdd'       => 'admin/invoices/form?act=add',
            'linkFormEdit'      => 'admin/invoices/form?act=edit',
            'linkStore'         => 'admin/invoices',
            'linkUpdate'        => 'admin/invoices/',
            'linkCopy'          => 'admin/invoices/copy',
            'linkDestroy'       => 'admin/invoices',
            'breadcrumb_new'    => 'Nova Fatura',
            'breadcrumb_edit'   => 'Editar Fatura',
            'path'              => 'admin.invoice.'
        ];

    }

    public function index(){

        $data = Invoice::where('user_id',auth()->user()->id)->orderby('id','desc')->get();

        return response()->json($data);
    }

    public function form(){

        $customer_id = $this->request->input('customer_id');

        $customer_id = $customer_id != null ? $customer_id : '';

        $customer_services = CustomerService::join('services','services.id','customer_services.service_id')
        ->select('customer_services.id as id','customer_services.description','customer_services.price','customer_services.day_due','customer_services.period','customer_services.status','services.name as name')
        ->where('customer_services.customer_id',$customer_id)->where('customer_services.user_id',auth()->user()->id)->get();

        $data = Invoice::where('id',$this->request->input('id'))->where('user_id',auth()->user()->id)->first();

        return view($this->datarequest['path'].'.form',compact('customer_services','data','customer_id'))->render();

    }


    public function store()
    {


        $customer_id = $this->request->input('customer_id');

        $model = new Invoice();
        $data = $this->request->all();

        $messages = [
            'customer_service_id.required'  => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo Descrição é obrigatório!',
            'price.required'                => 'O campo Preço é obrigatório!',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
            'date_invoice.required'         => 'O campo Data data fatura é obrigatório!',
            'date_due.required'             => 'O campo Data de vencimento é obrigatório!',
            'status.required'               => 'O campo Status é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'customer_service_id'   => 'required',
            'description'           => 'required',
            'price'                 => 'required',
            'payment_method'        => 'required',
            'date_invoice'          => 'required',
            'date_due'              => 'required',
            'status'                => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_service_id = $data['customer_service_id'];
        $model->description         = $data['description'];
        $model->price               = moeda($data['price']);
        $model->payment_method      = $data['payment_method'];
        $model->date_invoice        = $data['date_invoice'];
        $model->date_due            = $data['date_due'];
        $model->date_payment        = $data['date_payment'] != null ? $data['date_payment'] : null;
        $model->status              = $data['status'];



        try{
            $model->save();

            $invoice = Invoice::where('id',$model->id)->where('user_id',auth()->user()->id)->first();
            $customer = Customer::where('id',$customer_id)->where('user_id',auth()->user()->id)->first();
            $customer_service = CustomerService::join('services','services.id','customer_services.service_id')
            ->select('customer_services.id as id','customer_services.description','customer_services.price',
            'customer_services.day_due','customer_services.period','customer_services.status','services.name as name')
            ->where('customer_services.id',$invoice->customer_service_id)->where('customer_services.user_id',auth()->user()->id)->first();


            if($invoice->payment_method == 'Pix'){
                if($customer->gateway_pix == 'Pag Hiper'){
                    $generatePixPagHiper = Invoice::generatePixPH($invoice->id);
                    if($generatePixPagHiper['status'] == 'reject'){
                        return response()->json($generatePixPagHiper['message'], 422);
                    }
                    try {
                        Invoice::where('id',$invoice->id)->where('user_id',auth()->user()->id)->update([
                            'transaction_id' => $generatePixPagHiper['transaction_id']
                        ]);
                    } catch (\Exception $e) {
                        \Log::error($e->getMessage());
                        return response()->json($e->getMessage(), 422);
                    }
                }elseif($customer->gateway_pix == 'Mercado Pago'){
                    $generatePixPagHiper = Invoice::generatePixMP($invoice->id);
                    if($generatePixPagHiper['status'] == 'reject'){
                        return response()->json($generatePixPagHiper['message'], 422);
                    }
                    try {
                        Invoice::where('id',$invoice->id)->where('user_id',auth()->user()->id)->update([
                            'transaction_id' => $generatePixPagHiper['transaction_id']
                        ]);
                    } catch (\Exception $e) {
                        \Log::error($e->getMessage());
                        return response()->json($e->getMessage(), 422);
                    }



                    $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$invoice->id)->where('user_id',auth()->user()->id)->first();
                    $getInfoPixPayment = Invoice::verifyStatusPixMP(auth()->user()->access_token_mp,$verifyTransaction->transaction_id);

                    if(!file_exists(public_path('pix')))
                            \File::makeDirectory(public_path('pix'));

                    $image = $getInfoPixPayment->qr_code_base64;
                    $imageName = auth()->user()->id.'_'.$invoice->id.'.'.'png';
                    \File::put(public_path(). '/pix/' . $imageName, base64_decode($image));

                    $image_pix_email = 'https://financeiro.rogerti.com.br/pix/'.auth()->user()->id.'_'.$invoice->id.'.png';
                    $qr_code_digitable = $getInfoPixPayment->qr_code;

                }
            } elseif($invoice->payment_method == 'Boleto'){

                if($customer->gateway_billet == 'Pag Hiper'){
                    $generatePixPagHiper = Invoice::generateBilletPH($invoice->id);
                    if($generatePixPagHiper['status'] == 'reject'){
                        return response()->json($generatePixPagHiper['message'], 422);
                    }
                    try {
                        Invoice::where('id',$invoice->id)->where('user_id',auth()->user()->id)->update([
                            'transaction_id' => $generatePixPagHiper['transaction_id']
                        ]);
                    } catch (\Exception $e) {
                        \Log::error($e->getMessage());
                        return response()->json($e->getMessage(), 422);
                    }

                    $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$invoice->id)->where('user_id',auth()->user()->id)->first();
                    \Log::info('Transaction da variavel verifyTransaction: '.$verifyTransaction->transaction_id);
                    $getInfoBilletPayment   = Invoice::verifyStatusBilletPH(auth()->user()->id ,$verifyTransaction->transaction_id);


                }elseif($customer->gateway_billet == 'Mercado Pago'){
                    //
                }


        }

            $details = [
                'title'                     => 'Nova fatura gerada',
                'logo'                      => 'https://rogerti.com.br/wp-content/uploads/2023/06/rogerti.png',
                'user_id'                   => $invoice->user_id,
                'customer'                  => $customer->name,
                'customer_email'            => $customer->email,
                'customer_email2'           => $customer->email2,
                'customer_phone'            => $customer->phone,
                'notification_whatsapp'     => $customer->notification_whatsapp,
                'company'                   => $customer->company,
                'date_invoice'              => date('d/m/Y', strtotime($invoice->date_invoice)),
                'date_due'                  => date('d/m/Y', strtotime($invoice->date_due)),
                'price'                     => number_format($invoice->price, 2,',','.'),
                'payment_method'            => $invoice->payment_method,
                'service'                   => $customer_service->name .' - '. $invoice->description,
                'invoice'                   => $invoice->id,
                'url_base'                  => url('/'),
                'pix_qrcode_image_url'      =>  '',
                'pix_emv'                   =>  '',
                'billet_digitable_line'     =>  '',
                'billet_url_slip_pdf'       =>  '',
                'billet_url_slip'           =>  '',
            ];


            if($invoice->payment_method == 'Boleto'){
                $details['billet_digitable_line'] = $getInfoBilletPayment->status_request->bank_slip->digitable_line;
                $details['billet_url_slip_pdf']   = $getInfoBilletPayment->status_request->bank_slip->url_slip_pdf;
                $details['billet_url_slip']       = $getInfoBilletPayment->status_request->bank_slip->url_slip;

                InvoiceNotification::EmailBillet($details);
            }else{

                $details['pix_qrcode_image_url']  = $image_pix_email;
                $details['pix_emv']               = $qr_code_digitable;
                InvoiceNotification::EmailPix($details);
            }




        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Registro salvo com sucesso', 200);


    }




    public function update($id)
    {

        $customer_id = $this->request->input('customer_id');

        $model = Invoice::where('id',$id)->where('user_id',auth()->user()->id)->first();

        $data = $this->request->all();


        $messages = [
            'customer_service_id.required'  => 'O Campo Serviço é obrigatório!',
            'description.unique'            => 'O campo Descrição é obrigatório!',
            'price.required'                => 'O campo Preço é obrigatório!',
            'payment_method.required'       => 'O campo Forma de pagamento é obrigatório!',
            'date_invoice.required'         => 'O campo Data é obrigatório!',
            'date_due.required'             => 'O campo Data de vencimento é obrigatório!',
            'status.required'               => 'O campo Status é obrigatório!',
        ];

        $validator = Validator::make($data, [
            'customer_service_id'   => 'required',
            'description'           => 'required',
            'price'                 => 'required',
            'payment_method'        => 'required',
            'date_invoice'          => 'required',
            'date_due'              => 'required',
            'status'                => 'required',
        ], $messages);

        if( $validator->fails() ){
            return response()->json($validator->errors()->first(), 422);
        }

        $model->user_id             = auth()->user()->id;
        $model->customer_service_id = $data['customer_service_id'];
        $model->description         = $data['description'];
        $model->price               = moeda($data['price']);
        $model->payment_method      = $data['payment_method'];
        $model->date_invoice        = $data['date_invoice'];
        $model->date_due            = $data['date_due'];
        $model->date_payment        = $data['date_payment'] != null ? $data['date_payment'] : null;
        $model->status              = $data['status'];

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
        $model = new Invoice();

        try{

            $model->where('id',$id)->where('user_id',auth()->user()->id)->delete();

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return response()->json('Erro interno, favor comunicar ao administrador', 500);
        }

        return response()->json(true, 200);


    }

    public function Load($customer_id){

        $result = Invoice::join('customer_services','customer_services.id','invoices.customer_service_id')
                ->join('services','services.id','customer_services.service_id')
                ->select('invoices.id as id','invoices.description','invoices.payment_method','invoices.price','invoices.date_invoice','invoices.date_due','invoices.date_payment','invoices.status','services.name as name')
                ->where('customer_services.customer_id',$customer_id)->where('invoices.user_id',auth()->user()->id)->get();
        return response()->json($result);


    }

}
