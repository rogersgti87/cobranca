<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\InvoiceNotification;
use App\Models\Invoice;
use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;


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

$invoices = Invoice::with(['customerService.customer', 'company'])
    ->where('status', 'Gerando')
    ->whereNotNull('company_id')
    ->orderBy('id', 'asc')
    ->limit(3)
    ->get();

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

    $pixKey = $invoice['chave_pix'];
    $payload = gerarCodigoPix($pixKey, $invoice['price']);

    Invoice::where('id',$invoice['id'])->update([
        'status' => 'Pendente',
        'pix_digitable' => $payload,
    ]);

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
            }elseif(empty($generateBilletIntermedium['invoice_updated'])){
                Invoice::where('id',$invoice['id'])->update(['status' => $generateBilletIntermedium['invoice_status'] ?? 'Pendente']);
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
    }elseif(empty($generateBilletIntermedium['invoice_updated'])){
        Invoice::where('id',$invoice['id'])->update(['status' => $generateBilletIntermedium['invoice_status'] ?? 'Pendente']);
    }

}
elseif($invoice['gateway_payment'] == 'Estabelecimento'){
    Invoice::where('id',$invoice['id'])->update(['status' => 'Estabelecimento']);
}

}

   $invoice->refresh();
   $sendGenerate = $invoice->company->send_generate_invoice ?? null;
   if($sendGenerate == 'Sim' && $invoice->status == 'Pendente'){
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
