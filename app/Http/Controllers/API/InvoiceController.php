<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Customer;
use App\Models\InvoiceNotification;
use App\Models\ViewInvoice;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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

        $invoice = ViewInvoice::where('id',$request->input('invoice_id'))->first();

        if($invoice['status'] == 'Pendente'){
            $title                  = 'Fatura';
            $message_notification   = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura foi gerada.';

            if($invoice['date_due'] == Carbon::now()->format('Y-m-d') ){
                $title                      = 'Sua Fatura vence hoje';
                $message_notification       = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura vence hoje.';
            }

            if(Carbon::parse($invoice['date_due'])->subDays(5)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){
                if($invoice['send_generate_invoice'] == 'Não'){
                    $title                  = 'Nova Fatura Gerada';
                    $message_notification   = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura foi gerada.';
                }else{
                    $title                  = 'Sua Fatura vencerá em 5 dias';
                    $message_notification   = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura vencerá em 5 dias.';
                }
            }

            if(Carbon::parse($invoice['date_due'])->subDays(2)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){
                $title                      = 'Sua Fatura vencerá em 2 dias';
                $message_notification       = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura vencerá em 2 dias.';
            }

            if($invoice['date_due'] < Carbon::now()->format('Y-m-d') ){
                $title                      = 'Sua Fatura venceu';
                $message_notification       = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura está vencida.';
            }

        } else {
            $title = 'Fatura';
            if($invoice['status'] == 'Pago'){
                $title = 'Pagamento confirmado';
            }
            $message_notification = 'Esta é uma mensagem para notificá-lo(a) que sua Fatura mudou o status para: <b>'.$invoice['status'].'</b>';
        }

        $data = [
            'type_send'                 => 'New',
            'title'                     => $title,
            'message_customer'          => 'Olá '.$invoice['name'],
            'message_notification'      => $message_notification,
            'logo'                      => 'https://cobrancasegura.com.br/'.$invoice['user_image'],
            'company'                   => $invoice['user_company'],
            'user_whatsapp'             => removeEspeciais($invoice['user_whatsapp']),
            'user_telephone'            => removeEspeciais($invoice['user_telephone']),
            'user_email'                => $invoice['user_email'],
            //'user_access_token_wp'      => $invoice['api_access_token_whatsapp'],
            'user_id'                   => $invoice['user_id'],
            'customer'                  => $invoice['name'],
            'customer_email'            => $invoice['email'],
            'customer_email2'           => $invoice['email2'],
            'customer_whatsapp'         => removeEspeciais($invoice['whatsapp']),
            'notification_whatsapp'     => $invoice['notification_whatsapp'],
            'notification_email'        => $invoice['notification_email'],
            'customer_company'          => $invoice['company'],
            'date_invoice'              => date('d/m/Y', strtotime($invoice['date_invoice'])),
            'date_due'                  => date('d/m/Y', strtotime($invoice['date_due'])),
            'price'                     => number_format($invoice['price'], 2,',','.'),
            'gateway_payment'           => $invoice['gateway_payment'],
            'payment_method'            => $invoice['payment_method'],
            'service'                   => $invoice['description'],
            'invoice'                   => $invoice['id'],
            'status'                    => $invoice['status'],
            'url_base'                  => url('/'),
            'pix_qrcode_image_url'      => $invoice['image_url_pix'],
            'pix_emv'                   => $invoice['pix_digitable'],
            'pix_qrcode_base64'         => $invoice['qrcode_pix_base64'],
            'billet_digitable_line'     => $invoice['billet_digitable'],
            'billet_url_slip_base64'    => $invoice['billet_base64'],
            'billet_url_slip'           => $invoice['billet_url'],
            'status_whatsapp'           => $invoice['status_whatsapp'],
            'api_session_whatsapp'      => $invoice['api_session_whatsapp'],
            'api_token_whatsapp'        => $invoice['api_token_whatsapp'],
            'api_status_whatsapp'       => $invoice['api_status_whatsapp'],
        ];


        if($data['api_status_whatsapp'] != 'open'){
            return response()->json('Whatsapp desconectado! Fatura não enviada para whatsapp.',422);
        }

        $message_customer               = $data['message_customer'];
        $whats_invoice_id               = $data['invoice'];
        $message_notification           = whatsappBold($data['message_notification']);
        $whats_description              = $data['service'];
        $whats_data_fatura              = $data['date_invoice'];
        $whats_data_vencimento          = $data['date_due'];
        $whats_price                    = $data['price'];
        $whats_status                   = $data['status'];
        $whats_payment_method           = $data['payment_method'];
        $whats_pix_emv                  = $data['pix_emv'];
        $whats_pix_image                = $data['pix_qrcode_image_url'];
        $whats_billet_digitable_line    = $data['billet_digitable_line'];
        $whats_billet_url_slip          = $data['billet_url_slip'];
        $whats_billet_base64            = $data['billet_url_slip_base64'];

        $data['text_whatsapp'] = "*MENSAGEM AUTOMÁTICA*\n\n";
        $data['text_whatsapp'] .= "$message_customer\n\n";
        $data['text_whatsapp'] .= "$message_notification \n\n";
        $data['text_whatsapp'] .= "*Serviço Contratado:* \n";
        $data['text_whatsapp'] .= "$whats_description \n";
        $data['text_whatsapp'] .= "*Data da Fatura:* $whats_data_fatura \n";
        $data['text_whatsapp'] .= "*Vencimento:* $whats_data_vencimento \n";
        $data['text_whatsapp'] .= "*Forma de pagamento:* $whats_payment_method \n";
        $data['text_whatsapp'] .= "*Total:* R$ $whats_price \n";
        $data['text_whatsapp'] .= "*Status:* $whats_status \n\n";

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => $data['api_token_whatsapp']
        ])
        ->post(config('options.api_url_evolution').'message/sendText/'.$data['api_session_whatsapp'],[
            "number"        => $request->input('remoteJid'),
            "options"       =>  [
                //"delay"     => $this->campaign->interval_send * 1000
            ],
            "textMessage"   => [
                "text"      =>  $data['text_whatsapp']
            ]
        ]);

        if ($response->successful()) {

            return $response->json();
        }

        if($response->badRequest()){
            \Log::info($response->json());
            return $response->json();
        }

        if($response->serverError()){
            \Log::info($response->json());
            return $response->json();
        }


        if($data['status'] == 'Pendente') {

            //Enviar imagem qrcode pix
        if($whats_payment_method == 'Pix'){

            $response = Http::withHeaders([
                "Content-Type"  => "application/json",
                'apikey'        => $data['api_token_whatsapp']
            ])
            ->post(config('options.api_url_evolution').'message/sendMedia/'.$data['api_session_whatsapp'],[
                "number"         => $request->input('remoteJid'),
                "mediaMessage"   => [
                    "mediatype"  =>  "image",
                    "caption"    =>  $whats_pix_emv,
                    "media"      =>  $whats_pix_image
                    //"media"      => preg_replace('/[^a-zA-Z0-9\/\+=]/', '', $data['pix_qrcode_base64'])
                ]
            ]);

            $result = $response->getBody();

            if ($response->successful()) {
                return $response->json();
            }

            if($response->badRequest()){
                \Log::info($response->json());
                return $response->json();
            }

            if($response->serverError()){
                \Log::info($response->json());
                return $response->json();
            }

        }
            //Fim imagem qrcode pix

            $data['text_whatsapp_payment'] = '';

        if($whats_payment_method == 'Boleto'){
            $whats_billet_digitable_line = removeEspeciais($whats_billet_digitable_line);

            $response = Http::withHeaders([
                "Content-Type"  => "application/json",
                'apikey'        => $data['api_token_whatsapp']
            ])
            ->post(config('options.api_url_evolution').'message/sendMedia/'.$data['api_session_whatsapp'],[
                "number"         => $request->input('remoteJid'),
                "mediaMessage"   => [
                    "mediatype"  =>  "document",
                    "caption"    =>  $whats_billet_digitable_line,
                    "media"      =>  $whats_billet_url_slip,
                    //"media"      => preg_replace('/[^a-zA-Z0-9\/\+=]/', '', $whats_billet_base64),
                    "fileName"   => 'Fatura_'.$whats_invoice_id.'.pdf'
                ]
            ]);
        }
            if($whats_payment_method == 'BoletoPix'){
                $whats_billet_digitable_line = removeEspeciais($whats_billet_digitable_line);

                $response = Http::withHeaders([
                    "Content-Type"  => "application/json",
                    'apikey'        => $data['api_token_whatsapp']
                ])
                ->post(config('options.api_url_evolution').'message/sendMedia/'.$data['api_session_whatsapp'],[
                    "number"         => $request->input('remoteJid'),
                    "mediaMessage"   => [
                        "mediatype"  =>  "document",
                        "caption"    =>  $whats_billet_digitable_line,
                        //"media"      =>  config('app.url').'/boletopix/'.$whats_billet_url_slip,
                        //"media"      => $whats_billet_base64,
                        //"media"      => preg_replace('/[^a-zA-Z0-9\/\+=]/', '', $whats_billet_base64),
                        "media"      =>  $whats_billet_url_slip,
                        "fileName"   => 'Fatura_'.$whats_invoice_id.'.pdf'
                    ]
                ]);

            }

            $result = $response->getBody();

            if ($response->successful()) {
                return $response->json();
            }

            if($response->badRequest()){
                \Log::info($response->json());
                return $response->json();
            }


            if($response->serverError()){
                \Log::info($response->json());
                return $response->json();
            }

        }



        //$email = InvoiceNotification::Email($request->input('invoice_id'));
         //\Log::info($email);

        //$whatsapp = InvoiceNotification::Whatsapp($request->input('invoice_id'));
        //\Log::info($whatsapp);

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
