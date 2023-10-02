<?php

namespace App\Console\Commands;

use App\Models\ViewInvoice;
use App\Models\User;
use App\Models\InvoiceNotification;
use App\Models\Invoice;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class GenerateInvoiceCron extends Command
{

  protected $signature = 'generateinvoice:cron';

  protected $description = 'Gerar Faturas';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {

$invoices = ViewInvoice::where('status','Gerando')->limit(10)->get();

if($invoices != null){

foreach($invoices as $invoice){

    if($invoice['payment_method'] == 'Pix'){
        //PIX PAG HIPER
        if($invoice['gateway_payment'] == 'Pag Hiper'){
            $generatePixPH = Invoice::generatePixPH($invoice['id']);
            if($generatePixPH['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => $generatePixPH['message']]);
                return response()->json($generatePixPH['message'], 422);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }elseif($invoice['gateway_payment'] == 'Mercado Pago'){
            $generatePixMP = Invoice::generatePixMP($invoice['id']);
            if($generatePixMP['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => $generatePixMP['message']]);
                return response()->json($generatePixMP['message'], 422);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }elseif($invoice['gateway_payment'] == 'Intermedium'){
            $generatePixIntermedium = Invoice::generatePixIntermedium($invoice['id']);
            if($generatePixIntermedium['status'] == 'reject'){
                $msgInterPix = '';
                foreach($generatePixIntermedium['message'] as $messageInterPix){
                    $msgInterPix .= $messageInterPix['razao'].' - '.$messageInterPix['propriedade'].',';
                }
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => $generatePixIntermedium['title'].': '.$msgInterPix]);
                return response()->json($generatePixIntermedium['title'].': '.$msgInterPix, 422);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }
    } elseif($invoice['payment_method'] == 'Boleto'){

        if($invoice['gateway_payment'] == 'Pag Hiper'){
            $generateBilletPH = Invoice::generateBilletPH($invoice['id']);
            if($generateBilletPH['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => $generateBilletPH['message']]);
                return response()->json($generateBilletPH['message'], 422);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }elseif($invoice['gateway_payment'] == 'Intermedium'){

            $generateBilletIntermedium = Invoice::generateBilletIntermedium($invoice['id']);
            if($generateBilletIntermedium['status'] == 'reject'){
                $msgInterBillet = '';
                foreach($generateBilletIntermedium['message'] as $messageInterBillet){
                    $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
                }
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => $generateBilletIntermedium['title'].': '.$msgInterBillet]);
                return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }


}

elseif($invoice['payment_method'] == 'BoletoPix'){

if($invoice['gateway_payment'] == 'Intermedium'){

    $generateBilletIntermedium = Invoice::generateBilletPixIntermedium($invoice['id']);
    if($generateBilletIntermedium['status'] == 'reject'){
        $msgInterBillet = '';
        foreach($generateBilletIntermedium['message'] as $messageInterBillet){
            $msgInterBillet .= $messageInterBillet['razao'].' - '.$messageInterBillet['propriedade'].' - '.$messageInterBillet['valor'].',';
        }
        Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => $generateBilletIntermedium['title'].': '.$msgInterBillet]);
        return response()->json($generateBilletIntermedium['title'].': '.$msgInterBillet, 422);
    }else{
        Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
    }

}
}


 //   if($invoice['send_generate_invoice'] == 'Sim'){
        InvoiceNotification::Email($invoice['id']);
        InvoiceNotification::Whatsapp($invoice['id']);
   // }


}
}

  }

}
