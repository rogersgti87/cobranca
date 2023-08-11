<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use App\Models\InvoiceNotification;


class GenerateInvoiceCron extends Command
{

  protected $signature = 'generateinvoice:cron';

  protected $description = 'Gerar Faturas a vencer';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {


    $sql = "SELECT a.id, a.user_id, c.name customer,c.email,c.email2,c.phone, c.notification_whatsapp, c.company, a.description,a.price, u.access_token_mp,
	a.gateway_payment,a.payment_method,a.period, CURRENT_DATE date_invoice, DATE_ADD(CONCAT(YEAR(CURRENT_DATE),'-',MONTH(CURRENT_DATE),'-',a.day_due), INTERVAL 0 MONTH) date_due,
	a.status, 'Pendente',CURRENT_TIMESTAMP created_at,CURRENT_TIMESTAMP updated_at FROM customer_services a
    INNER JOIN customers c ON a.customer_id = c.id
    INNER JOIN services s ON a.service_id = s.id
    INNER JOIN users u ON a.user_id = u.id
    WHERE NOT EXISTS (SELECT * FROM invoices b WHERE a.id = b.customer_service_id AND DATE_ADD(CONCAT(YEAR(CURRENT_DATE),'-',MONTH(CURRENT_DATE),'-',a.day_due), INTERVAL 0 MONTH) = b.date_due)
    AND CURRENT_DATE = DATE_ADD(DATE_ADD(CONCAT(YEAR(CURRENT_DATE),'-',MONTH(CURRENT_DATE),'-','10'), INTERVAL 0 MONTH), INTERVAL 0 DAY) AND a.status = 'Ativo'";

   $verifyInvoices = DB::select($sql);

//   return $verifyInvoices;

    foreach($verifyInvoices as $invoice){

        $newInvoice = DB::table('invoices')->insertGetId([
            'user_id'               => $invoice->user_id,
            'customer_service_id'   => $invoice->id,
            'description'           => $invoice->description,
            'price'                 => $invoice->price,
            'gateway_payment'       => $invoice->gateway_payment,
            'payment_method'        => $invoice->payment_method,
            'date_invoice'          => $invoice->date_invoice,
            'date_due'              => $invoice->date_due,
            'date_payment'          => null,
            'status'                => 'Pendente',
            'created_at'            => $invoice->created_at,
            'updated_at'            => $invoice->updated_at
        ]);


        if($invoice->payment_method == 'Pix'){
            if($invoice->gateway_payment == 'Pag Hiper'){
                $generatePixPagHiper = Invoice::generatePixPH($newInvoice);
                if($generatePixPagHiper['status'] == 'reject'){
                    return response()->json($generatePixPagHiper['message'], 422);
                }
                try {
                    Invoice::where('id',$newInvoice)->where('user_id',$invoice->user_id)->update([
                        'transaction_id' => $generatePixPagHiper['transaction_id']
                    ]);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                    return response()->json($e->getMessage(), 422);
                }


            }elseif($invoice->gateway_payment == 'Mercado Pago'){
                $generatePixPagHiper = Invoice::generatePixMP($newInvoice);
                if($generatePixPagHiper['status'] == 'reject'){
                    return response()->json($generatePixPagHiper['message'], 422);
                }
                try {
                    Invoice::where('id',$newInvoice)->where('user_id',$invoice->user_id)->update([
                        'transaction_id' => $generatePixPagHiper['transaction_id']
                    ]);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                    return response()->json($e->getMessage(), 422);
                }

                $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$newInvoice)->where('user_id',$invoice->user_id)->first();
                $getInfoPixPayment = Invoice::verifyStatusPixMP($invoice->access_token_mp,$verifyTransaction->transaction_id);

                if(!file_exists(public_path('pix')))
                        \File::makeDirectory(public_path('pix'));

                $image = $getInfoPixPayment->qr_code_base64;
                $imageName = $invoice->user_id.'_'.$newInvoice.'.'.'png';
                \File::put(public_path(). '/pix/' . $imageName, base64_decode($image));

            }
        } elseif($invoice->payment_method == 'Boleto'){

            if($invoice->gateway_payment == 'Pag Hiper'){
                $generatePixPagHiper = Invoice::generateBilletPH($newInvoice);
                if($generatePixPagHiper['status'] == 'reject'){
                    return response()->json($generatePixPagHiper['message'], 422);
                }
                try {
                    Invoice::where('id',$newInvoice)->where('user_id',$invoice->user_id)->update([
                        'transaction_id' => $generatePixPagHiper['transaction_id']
                    ]);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                    return response()->json($e->getMessage(), 422);
                }

                $verifyTransaction = DB::table('invoices')->select('transaction_id')->where('id',$newInvoice)->where('user_id',$invoice->user_id)->first();
                $getInfoBilletPayment   = Invoice::verifyStatusBilletPH($invoice->user_id ,$verifyTransaction->transaction_id);


            }elseif($invoice->gateway_payment == 'Mercado Pago'){
                //
            }
    }



    }



  }
}
