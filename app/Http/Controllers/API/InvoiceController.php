<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Customer;
use App\Models\InvoiceNotification;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        
        // if(!$request->input('typebot_id') || $request->input('typebot_id') == null){
        //     return response()->json('typebot_id é obrigatório!');
        // }

        if(!$request->input('document') || $request->input('document') == null){
            return response()->json('document é obrigatório!');
        }

        $user     = User::where('id',$request->input('user_cob_seg'))->first();
        $customer = Customer::where('document',removeEspeciais($request->input('document')))->where('user_id',$user->id)->first();
        
        if($request->input('invoice_id')){
            $invoice = Invoice::select('id')->where('status','Pendente')->where('user_id',$user->id)->where('customer_id',$customer->id)->where('id',$request->input('invoice_id'))->first();
             if($invoice != null){
                return response()->json('ok', 200);
            }else{
                return response()->json('Número da Fatura incorrreta!', 400);
            }
        }
        
        $invoices = Invoice::select('id','price','date_invoice','date_due','description')->where('status','Pendente')->where('user_id',$user->id)->where('customer_id',$customer->id)->get();

$data = "";

foreach($invoices as $invoice){

    $data .= "*• ".$invoice->id ."* (R$ ".number_format($invoice->price,2,',','.')." - ".date('d/m/Y',strtotime($invoice->date_due)) .")\n";    

}

        if($invoices != null){
            return response()->json($data, 200);
        }else{
            return response()->json('Fatura não encontrada!', 400);
        }

    }


    public function notificar(Request $request)
    {

        InvoiceNotification::Email($request->input('invoice_id'));
        InvoiceNotification::Whatsapp($request->input('invoice_id'));
        
        // if($request->input('tipo') == 'email'){
        //     InvoiceNotification::Email($request->input('invoice_id'));
        // }


        // if($request->input('tipo') == 'whatsapp'){
        //     InvoiceNotification::Whatsapp($request->input('invoice_id'));
        // }
    }


    public function checkInvoice(Request $request)
    {
        $invoice = Invoice::select('id')->where('status','Pendente')->where('user_id',$request->input('user_id'))->where('customer_id',$request->input('customer_id'))->where('id',$request->input('invoice_id'))->first();
         if($invoice != null){
            return response()->json('ok', 200);
        }else{
            return response()->json('Número da Fatura incorrreta!', 400);
        }
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
