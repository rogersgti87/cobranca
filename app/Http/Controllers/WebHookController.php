<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Config;
use App\Models\Invoice;
use App\Models\InvoiceNotification;
use App\Models\LogGatewayPayment;
use Illuminate\Support\Facades\Http;
use DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WebHookController extends Controller
{

  public function __construct()
  {

  }


  public function email(Request $request)
  {
    $data = $request->all();

    if($data != null){

        $emailNotification = InvoiceNotification::where('email_id',$data['message-id'])->first();
        if($emailNotification != null){

            DB::table('email_events')->insert([
                'company_id'        => $emailNotification->company_id,
                'user_id'           => $emailNotification->user_id,
                'event'             =>  $data['event'],
                'email'             =>  $data['email'],
                'identification'    =>  $data['id'],
                'date'              =>  $data['date'],
                'message_id'        =>  $data['message-id'],
                'subject'           =>  isset($data['subject']) ? $data['subject'] : null,
                'tag'               =>  isset($data['tag']) ? $data['tag'] : null,
                'sending_ip'        =>  isset($data['sending_ip']) ? $data['sending_ip'] : null,
                'ts_epoch'          =>  isset($data['ts_epoch']) ? $data['ts_epoch'] : null,
                'link'              =>  isset($data['link']) ? $data['link'] : null,
                'reason'            =>  isset($data['reason']) ? $data['reason'] : null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);

        }

    }

  }

  public function paghiper(Request $request)
  {
    $data = $request->all();
    LogGatewayPayment::create([
        'gateway'   => 'Pag Hiper',
        'log'       =>  json_encode($data)
    ]);

    $invoice = Invoice::select('invoices.id','invoices.transaction_id','companies.token_paghiper','companies.key_paghiper','invoices.payment_method')
                        ->join('companies','companies.id','invoices.company_id')
                        ->where('transaction_id',$data['transaction_id'])
                        ->where('invoices.status','Pendente')
                        ->orwhere('invoices.status','Processamento')
                        ->first();

    if($invoice != null){
        $url = '';
        if($invoice->payment_method == 'Pix'){
            $url = 'https://pix.paghiper.com/invoice/notification/';
        }else{
            $url = 'https://api.paghiper.com/transaction/notification/';
        }

    $response = Http::withHeaders([
        'accept' => 'application/json',
        'content-type' => 'application/json',
    ])->post($url,[
        'token'             => $invoice->token_paghiper,
        'apiKey'            => $data['apiKey'],
        'transaction_id'    => $data['transaction_id'],
        'notification_id'   => $data['notification_id']
    ]);

    $result = $response->getBody();
    $result = json_decode($result)->status_request;

    if($result->status == 'reserved'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Processamento',
            'date_payment' =>   Null,
            'updated_at'   =>   Carbon::now()
        ]);
        //InvoiceNotification::Email($invoice->id);
        //InvoiceNotification::Whatsapp($invoice->id);
    }
    if($result->status == 'completed' || $result->status == 'paid'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Pago',
            'date_payment' =>   isset($data['paid_date']) ? date('d/m/Y', strtotime($data['paid_date'])) : Carbon::now(),
            'updated_at'   =>   Carbon::now()
        ]);
        InvoiceNotification::Email($invoice->id);

         if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');

            if ($now->between($start, $end)) {
        InvoiceNotification::Whatsapp($invoice->id);

            }
         }
    }

    if($result->status == 'canceled'){
        Invoice::where('id',$result->order_id)->where('transaction_id',$result->transaction_id)->update([
            'status'       =>   'Cancelado',
            'date_payment' =>   Null,
            'updated_at'   =>   Carbon::now()
        ]);
    //     InvoiceNotification::Email($invoice->id);
    //     InvoiceNotification::Whatsapp($invoice->id);
     }





    }

  }


  public function mercadopago(Request $request){

    $data = $request->all();

    LogGatewayPayment::create([
        'gateway'   => 'Mercado Pago',
        'log'       =>  json_encode($data)
    ]);


    $invoice = Invoice::select('invoices.id as id','invoices.transaction_id','companies.access_token_mp')
                ->join('companies','companies.id','invoices.company_id')
                ->where('transaction_id',$data['data']['id'])
                ->where('invoices.status','Pendente')
                ->first();
    if($invoice != null){

        \MercadoPago\SDK::setAccessToken($invoice->access_token_mp);
        $payment = \MercadoPago\Payment::find_by_id($invoice->transaction_id);

        if($payment->status == 'approved'){
            $result_invoice = Invoice::where('id',$invoice->id)->where('transaction_id',$invoice->transaction_id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::parse(now())->format('Y-m-d'),
                'updated_at'   =>   Carbon::now()
            ]);
            InvoiceNotification::Email($invoice->id);

             if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('19:00');

            if ($now->between($start, $end)) {
            InvoiceNotification::Whatsapp($invoice->id);

            }
             }
        }

        if($payment->status == 'cancelled'){
            Invoice::where('id',$invoice->id)->where('transaction_id',$invoice->transaction_id)->update([
                'status'       =>   'Cancelado',
                'date_payment' =>   Null,
                'updated_at'   =>   Carbon::now()
            ]);
            //InvoiceNotification::Email($invoice->id);
            //InvoiceNotification::Whatsapp($invoice->id);
        }



    }



  }



  //Intermedium - Cobrança unificada BolePix v3

  private function processIntermediumCobrancaWebhook(Request $request, string $gatewayLabel)
  {
    $data = $request->all();

    LogGatewayPayment::create([
        'gateway'   => $gatewayLabel,
        'log'       => json_encode($data),
    ]);

    $payload = $data[0] ?? null;
    if (!$payload || !is_array($payload)) {
        return;
    }

    $seuNumero = $payload['seuNumero'] ?? null;
    $codigoCobranca = $payload['codigoSolicitacao'] ?? ($payload['nossoNumero'] ?? null);
    $status = $payload['situacao'] ?? null;

    if (empty($seuNumero) || empty($codigoCobranca) || empty($status)) {
        return;
    }

    $result = Invoice::where('id', $seuNumero)
        ->where('transaction_id', $codigoCobranca)
        ->where(function ($query) {
            $query->where('status', 'Pendente')
                  ->orWhere('status', 'Processamento');
        })
        ->first();

    if ($result === null) {
        return;
    }

    if (in_array($status, ['RECEBIDO', 'PAGO'], true)) {
        $wasPaid = $result->status === 'Pago';
        Invoice::where('id', $seuNumero)->update([
            'status'       => 'Pago',
            'date_payment' => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ]);
        if (! $wasPaid) {
            Invoice::notifyPaymentReceived((int) $seuNumero);
        }
    }

    if ($status === 'CANCELADO') {
        Invoice::where('id', $seuNumero)->update([
            'status'       => 'Cancelado',
            'date_payment' => null,
            'updated_at'   => Carbon::now(),
        ]);
    }

    if ($status === 'EXPIRADO') {
        Invoice::where('id', $seuNumero)->update([
            'status'       => 'Expirado',
            'date_payment' => null,
            'updated_at'   => Carbon::now(),
        ]);
    }
  }

  public function intermediumBillet(Request $request) {
    $this->processIntermediumCobrancaWebhook($request, 'Intermedium Cobrança v3');
  }

  public function intermediumBilletPix(Request $request) {
    $this->processIntermediumCobrancaWebhook($request, 'Intermedium Cobrança v3 (BoletoPix)');
  }

  public function intermediumPix(Request $request) {
    $data = $request->all();

    LogGatewayPayment::create([
        'gateway'   => 'Intermedium Pix',
        'log'       =>  json_encode($data)
    ]);

    if(isset($data['pix'])){
        $txid = $data['pix'][0]['txid'];
    }else{
        $txid = $data[0]['codigoSolicitacao'];
        if($data[0]['situacao'] != 'RECEBIDO'){
            return 'nao recebido!';
        }
    }


    $result = Invoice::where('transaction_id',$txid)
    ->where(function ($query) {
        $query->where('status', 'Pendente')
              ->orWhere('status', 'Processamento');
    })
    ->first();

    if($result != null){
            $wasPaid = $result->status === 'Pago';

            Invoice::where('id',$result->id)->update([
                'status'       =>   'Pago',
                'date_payment' =>   Carbon::now(),
                'updated_at'   =>   Carbon::now()
            ]);

            if (! $wasPaid) {
                Invoice::notifyPaymentReceived($result->id);
            }

    }


  }


  public function Asaas(Request $request){

    $data = $request->all();

    LogGatewayPayment::create([
        'gateway'   => 'Asaas',
        'log'       =>  json_encode($data)
    ]);


    if(isset($data['event']) && isset($data['payment'])){

        // PAYMENT_RECEIVED e PAYMENT_DELETED agora são processados abaixo

        // Para PAYMENT_CONFIRMED e PAYMENT_RECEIVED, verifica se externalReference começa com REC_ ou rec_
        // Se começar, ignora pois não pertence ao Cobrança Segura (externalReference deve conter apenas números)
        if(($data['event'] == 'PAYMENT_CONFIRMED' || $data['event'] == 'PAYMENT_RECEIVED') && isset($data['payment']['externalReference'])){
            $externalRef = $data['payment']['externalReference'];
            if(is_string($externalRef) && (str_starts_with($externalRef, 'REC_') || str_starts_with($externalRef, 'rec_'))){
                return response()->json(['status' => 'success', 'message' => 'Pagamento não pertence ao Cobrança Segura'], 200);
            }
        }

        // Busca primeiro pelo transaction_id (payment.id)
        $invoice = Invoice::select('invoices.id as id','invoices.transaction_id','invoices.status','companies.access_token_mp')
                    ->join('companies','companies.id','invoices.company_id')
                    ->where('transaction_id',$data['payment']['id'])
                    ->where(function($query) {
                        $query->where('invoices.status','Pendente')
                              ->orWhere('invoices.status','Processamento');
                    })
                    ->first();

        // Log se não encontrou pelo transaction_id
        if($invoice == null){
            \Log::info('Webhook Asaas: Não encontrado pelo transaction_id, tentando externalReference', [
                'payment_id' => $data['payment']['id'] ?? null,
                'external_reference' => $data['payment']['externalReference'] ?? null,
                'event' => $data['event'] ?? null
            ]);
        }

        // Se não encontrou pelo transaction_id, tenta pelo externalReference (ID da fatura)
        // Remove restrição de status pois pode ser que a fatura já tenha sido processada
        if($invoice == null && isset($data['payment']['externalReference'])){
            $external_ref = is_numeric($data['payment']['externalReference'])
                ? (int)$data['payment']['externalReference']
                : $data['payment']['externalReference'];

            $invoice = Invoice::select('invoices.id as id','invoices.transaction_id','invoices.status','companies.access_token_mp')
                        ->join('companies','companies.id','invoices.company_id')
                        ->where('invoices.id', $external_ref)
                        ->first();

            // Log para debug
            if($invoice == null){
                \Log::warning('Webhook Asaas: Fatura não encontrada pelo externalReference', [
                    'external_reference' => $external_ref,
                    'payment_id' => $data['payment']['id'] ?? null,
                    'event' => $data['event'] ?? null
                ]);
            }
        }

        if($invoice != null){

            // Verifica se a fatura já está paga antes de processar
            if(($data['event'] == 'PAYMENT_CONFIRMED' || $data['event'] == 'PAYMENT_RECEIVED') && $invoice->status == 'Pago'){
                return response()->json(['status' => 'success', 'message' => 'Pagamento já foi processado anteriormente'], 200);
            }

            // Atualiza o transaction_id se ainda não estiver salvo
            if(empty($invoice->transaction_id) && isset($data['payment']['id'])){
                Invoice::where('id', $invoice->id)->update([
                    'transaction_id' => $data['payment']['id']
                ]);
            }

            if($data['event'] == 'PAYMENT_CONFIRMED' || $data['event'] == 'PAYMENT_RECEIVED'){
                $wasPaid = $invoice->status === 'Pago';
                $result_invoice = Invoice::where('id',$invoice->id)->update([
                    'status'       =>   'Pago',
                    'date_payment' =>   Carbon::parse(now())->format('Y-m-d'),
                    'updated_at'   =>   Carbon::now()
                ]);

                \Log::info('Webhook Asaas: Pagamento confirmado', [
                    'invoice_id' => $invoice->id,
                    'payment_id' => $data['payment']['id'] ?? null,
                    'event' => $data['event']
                ]);

                if (! $wasPaid) {
                    Invoice::notifyPaymentReceived($invoice->id);
                }
            }

            if($data['event'] == 'PAYMENT_DELETED' || $data['event'] == 'PAYMENT_REFUNDED'){
                Invoice::where('id',$invoice->id)->update([
                    'status'       =>   'Cancelado',
                    'date_payment' =>   Null,
                    'updated_at'   =>   Carbon::now()
                ]);

            }

            return response()->json(['status' => 'success', 'message' => 'Pagamento processado com sucesso'], 200);

        } else {
            \Log::warning('Webhook Asaas: Pagamento não encontrado', [
                'payment_id' => $data['payment']['id'] ?? null,
                'external_reference' => $data['payment']['externalReference'] ?? null,
                'event' => $data['event'] ?? null
            ]);

            // Se o evento for PAYMENT_RECEIVED e não encontrou a fatura, retorna 200 informando que não pertence ao sistema
            if($data['event'] == 'PAYMENT_RECEIVED'){
                return response()->json(['status' => 'success', 'message' => 'Pagamento não pertence a este sistema'], 200);
            }

            return response()->json(['status' => 'error', 'message' => 'Pagamento não encontrado'], 404);
        }

    }

    return response()->json(['status' => 'error', 'message' => 'Dados inválidos'], 400);

  }



public function teste(){

      $path = public_path('pix/'.'teste'.'.png');

     QrCode::format('png')->generate('Welcome to Makitweb', $path );

     return 1;


}

public function whatsapp($user_id){
    return $user_id;
}


}
