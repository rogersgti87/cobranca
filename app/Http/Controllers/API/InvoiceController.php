<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Customer;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {

        if(!$request->input('typebot_id') || $request->input('typebot_id') == null){
            return response()->json('typebot_id é obrigatório!');
        }

        if(!$request->input('document') || $request->input('document') == null){
            return response()->json('document é obrigatório!');
        }

        $user     = User::where('typebot_id',$request->input('typebot_id'))->first();
        $customer = Customer::where('document',$request->input('document'))->where('user_id',$user->id)->first();
        $invoices = Invoice::select('id','price','date_invoice','date_due','description')->where('status','Pendente')->where('user_id',$user->id)->where('customer_id',$customer->id)->get();

// $data = [];

//         foreach($invoices as $invoice){
//             $data[] = [
//                 'id' => $invoice['id']
//             ];
//         }

        if($invoices != null){
            return response()->json($invoices, 200);
        }else{
            return response()->json('Fatura não localizada!', 200);
        }

    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
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
