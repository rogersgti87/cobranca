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

$invoices = ViewInvoice::where('status','Gerando')->limit(1)->get();

if($invoices != null){

foreach($invoices as $invoice){

    //\Log::info('Loop Generate Invoice: '. $invoice['id']);

    if($invoice['payment_method'] == 'Pix'){
        if($invoice['gateway_payment'] == 'Pag Hiper'){
            $generatePixPH = Invoice::generatePixPH($invoice['id']);
            if($generatePixPH['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generatePixPH['message'])]);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }elseif($invoice['gateway_payment'] == 'Mercado Pago'){
            $generatePixMP = Invoice::generatePixMP($invoice['id']);
            if($generatePixMP['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generatePixMP['message'])]);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }
        elseif($invoice['gateway_payment'] == 'Intermedium'){
            $generatePixIntermedium = Invoice::generatePixIntermedium($invoice['id']);
            if($generatePixIntermedium['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generatePixIntermedium['message'])]);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }
        elseif($invoice['gateway_payment'] == 'Asaas'){
            $generatePixAsaas = Invoice::generatePixAsaas($invoice['id']);
            if($generatePixAsaas['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generatePixAsaas['message'])]);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }
 elseif($invoice['gateway_payment'] == 'Estabelecimento'){
            Invoice::where('id',$invoice['id'])->update(['status' => 'Estabelecimento']);
        }
    } elseif($invoice['payment_method'] == 'Boleto'){

        if($invoice['gateway_payment'] == 'Pag Hiper'){
            $generateBilletPH = Invoice::generateBilletPH($invoice['id']);
            if($generateBilletPH['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generateBilletPH['message'])]);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }
        elseif($invoice['gateway_payment'] == 'Intermedium'){
            \Log::info('Loop Boleto Inter - Invoice: '. $invoice['id']);
            $generateBilletIntermedium = Invoice::generateBilletIntermedium($invoice['id']);
            if($generateBilletIntermedium['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generateBilletIntermedium['message'])]);
            }else{
                \Log::info('Boleto Inter - Invoice: '. $invoice['id'].' - Atualizado para Pendente.');
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }
        elseif($invoice['gateway_payment'] == 'Asaas'){

            $generateBilletAsaas = Invoice::generateBilletAsaas($invoice['id']);
            if($generateBilletAsaas['status'] == 'reject'){
                Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generateBilletAsaas['message'])]);
            }else{
                Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
            }

        }
        elseif($invoice['gateway_payment'] == 'Estabelecimento'){
            Invoice::where('id',$invoice['id'])->update(['status' => 'Estabelecimento']);
        }


}

elseif($invoice['payment_method'] == 'BoletoPix'){

if($invoice['gateway_payment'] == 'Intermedium'){

    $generateBilletIntermedium = Invoice::generateBilletPixIntermedium($invoice['id']);
    if($generateBilletIntermedium['status'] == 'reject'){
        Invoice::where('id',$invoice['id'])->update(['status' => 'Erro','msg_erro' => json_encode($generateBilletIntermedium['message'])]);
    }else{
        Invoice::where('id',$invoice['id'])->update(['status' => 'Pendente']);
    }

}
elseif($invoice['gateway_payment'] == 'Estabelecimento'){
    Invoice::where('id',$invoice['id'])->update(['status' => 'Estabelecimento']);
}

}

   if($invoice['send_generate_invoice'] == 'Sim'){
        InvoiceNotification::Email($invoice['id']);

        if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');

            if ($now->between($start, $end)) {

                InvoiceNotification::Whatsapp($invoice['id']);

            }

        }
   }


}
return 'fim';
}

  }

}
