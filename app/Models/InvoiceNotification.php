<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use DB;
use App\Models\User;

class InvoiceNotification extends Model
{


    public static function Email($invoice_id){


        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        if($invoice['notification_email'] == 's' && $invoice['status'] != 'Erro'){

        $status_email = 'Não enviado';

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
            'message_customer'          => 'Olá '.$invoice['name'].', tudo bem?',
            'message_notification'      => $message_notification,
            'logo'                      => 'https://cobrancasegura.com.br/'.$invoice['user_image'],
            'company'                   => $invoice['user_company'],
            'user_whatsapp'             => removeEspeciais($invoice['user_whatsapp']),
            'user_telephone'            => removeEspeciais($invoice['user_telephone']),
            'user_email'                => $invoice['user_email'],
            'user_access_token_wp'      => $invoice['api_access_token_whatsapp'],
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
            'service'                   => $invoice['service_name'] .' - '. $invoice['description'],
            'invoice'                   => $invoice['id'],
            'status'                    => $invoice['status'],
            'url_base'                  => url('/'),
            'pix_qrcode_image_url'      => $invoice['image_url_pix'],
            'pix_emv'                   => $invoice['pix_digitable'],
            'pix_qrcode_base64'         => $invoice['qrcode_pix_base64'],
            'billet_digitable_line'     => $invoice['billet_digitable'],
            'billet_url_slip_base64'    => $invoice['billet_base64'],
            'billet_url_slip'           => $invoice['billet_url'],
        ];


        $data['body']  = view('mails.invoice',$data)->render();

        if($data['customer_email2'] != null){
            $emails = array(
                [
                    "name"      => $data['customer'],
                    "email"     => $data['customer_email']
                ],
                [
                    "name"      => $data['customer'],
                    "email"     => $data['customer_email2']
                ]
                );
        } else {
            $emails = array(
                [
                "name"  => $data['customer'],
                "email"     => $data['customer_email']
            ]
        );
        }



        $response = Http::withHeaders(
            [
                "Accept"        =>  "application/json",
                "Content-Type"  =>  "application/json",
                "api-key"       =>  config('mail.api_key_brevo')
            ]
            )->post('https://api.brevo.com/v3/smtp/email',[

                "sender" => [
                    "name"  => $data['company'],
                    "email" => "cobrancasegura@cobrancasegura.com.br"
                ],
                "to" => $emails,

                "subject"       => $data['title'] != null ? $data['title'] : 'Cobrança Segura',
                "htmlContent"   => $data['body']
          ]);

          if ($response->successful()) {

        $status_email = 'Enviado';
        $result = $response->getBody();
        $email_id = json_decode($result)->messageId;

        DB::table('invoice_notifications')->insert([
            'user_id'           => $data['user_id'],
            'invoice_id'        => $data['invoice'],
            'type_send'         => 'email',
            'date'              => Carbon::now(),
            'subject'           => $data['title'],
            'email_id'          => $email_id,
            'status'            => null,
            'message_status'    => null,
            'message'           => null,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now()
        ]);
    }else{
        $status_email = 'Erro eo enviar';
    }


    return $status_email;
}

    }

    public static function Whatsapp($invoice_id){

        $invoice = ViewInvoice::where('id',$invoice_id)->first();

        $status_message       = 'Não enviado';
        $status_image         = 'Não enviado';
        $status_file          = 'Não enviado';

        if(date('l') != 'Sunday'){

            $now = Carbon::now();
            $start = Carbon::createFromTimeString('08:00');
            $end = Carbon::createFromTimeString('21:00');

            if ($now->between($start, $end)) {
                if($invoice['notification_whatsapp'] == 's' && $invoice['status'] != 'Erro'){
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
            'message_customer'          => 'Olá '.$invoice['name'].', tudo bem?',
            'message_notification'      => $message_notification,
            'logo'                      => 'https://cobrancasegura.com.br/'.$invoice['user_image'],
            'company'                   => $invoice['user_company'],
            'user_whatsapp'             => removeEspeciais($invoice['user_whatsapp']),
            'user_telephone'            => removeEspeciais($invoice['user_telephone']),
            'user_email'                => $invoice['user_email'],
            'user_access_token_wp'      => $invoice['api_access_token_whatsapp'],
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
            'service'                   => $invoice['service_name'] .' - '. $invoice['description'],
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
            return 'Whatsapp desconectado';
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


        if($data['notification_whatsapp'] == 's' && $invoice['status'] != 'Erro'){

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            'apikey'        => $data['api_token_whatsapp']
        ])
        ->post(config('options.api_url_evolution').'message/sendText/'.$data['api_session_whatsapp'],[
            "number"        => '55'.$data['customer_whatsapp'],
            "options"       =>  [
                //"delay"     => $this->campaign->interval_send * 1000
            ],
            "textMessage"   => [
                "text"      =>  $data['text_whatsapp']
            ]
        ]);

        if ($response->successful()) {

            $result = $response->json();

            if($result['status'] == 'PENDING'){
                $status                  = 'Success';
                $status_message          = 'Enviado';
                //$whats_message_status    = $result;
                $whats_message           = $result;
            }else{
                $status                  = 'Error';
                $status_message          = 'Erro ao enviar';
                //$whats_message_status   = $result;
                $whats_message          = $result;
            }
        }

        if($response->badRequest()){
            $status                  = 'Error';
            $status_message          = 'Erro ao enviar';
            //$whats_message_status   = $result;
            $whats_message          = $result;
        }

        DB::table('invoice_notifications')->insert([
            'user_id'           => $data['user_id'],
            'invoice_id'        => $data['invoice'],
            'type_send'         => 'whatsapp',
            'date'              => Carbon::now(),
            'subject'           => $data['title'],
            'email_id'          => '',
            'status'            => $status,
            'message_status'    => null,
            'message'           => $whats_message,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now()
        ]);

    }else{
        $whats_message          = $response->json();
        return ['message' => $response->json(), 'image' => '', 'file' => ''];
    }

        if($data['status'] == 'Pendente') {

            //Enviar imagem qrcode pix
        if($whats_payment_method == 'Pix'){

            $response = Http::withHeaders([
                "Content-Type"  => "application/json",
                'apikey'        => $data['api_token_whatsapp']
            ])
            ->post(config('options.api_url_evolution').'message/sendMedia/'.$data['api_session_whatsapp'],[
                "number"         => '55'.$data['customer_whatsapp'],
                "mediaMessage"   => [
                    "mediatype"  =>  "image",
                    "caption"    =>  $whats_pix_emv,
                    "media"      =>  config('app.url').'/pix/'.$data['whats_pix_image']
                ]
            ]);

            $result = $response->getBody();

            if ($response->successful()) {

                $result = $response->json();

                if($result['status'] == 'PENDING'){
                    $status                  = 'Success';
                    $status_message          = 'Enviado';
                    //$whats_message_status    = $result;
                    $whats_message           = $result;
                }else{
                    $status                  = 'Error';
                    $status_message          = 'Erro ao enviar';
                    //$whats_message_status   = $result;
                    $whats_message          = $result;
                }
            }

            if($response->badRequest()){
                $status                  = 'Error';
                $status_message          = 'Erro ao enviar';
                //$whats_message_status   = $result;
                $whats_message          = $result;
            }

            DB::table('invoice_notifications')->insert([
                'user_id'           => $data['user_id'],
                'invoice_id'        => $data['invoice'],
                'type_send'         => 'whatsapp',
                'date'              => Carbon::now(),
                'subject'           => $data['title'],
                'email_id'          => '',
                'status'            => $status,
                'message_status'    => null,
                'message'           => $whats_message,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);

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
                "number"         => '55'.$data['customer_whatsapp'],
                "mediaMessage"   => [
                    "mediatype"  =>  "document",
                    "caption"    =>  $whats_billet_digitable_line,
                    //"media"      => config('app.url').'/boleto/'.$whats_billet_url_slip,
                    "media"      => $whats_billet_base64,
                    "fileName"   => 'Fatura_'.$whats_invoice_id.'.pdf'
                ]
            ]);

            if($whats_payment_method == 'BoletoPix'){
                $whats_billet_digitable_line = removeEspeciais($whats_billet_digitable_line);

                $response = Http::withHeaders([
                    "Content-Type"  => "application/json",
                    'apikey'        => $data['api_token_whatsapp']
                ])
                ->post(config('options.api_url_evolution').'message/sendMedia/'.$data['api_session_whatsapp'],[
                    "number"         => '55'.$data['customer_whatsapp'],
                    "mediaMessage"   => [
                        "mediatype"  =>  "document",
                        "caption"    =>  $whats_billet_digitable_line,
                        //"media"      =>  config('app.url').'/boletopix/'.$whats_billet_url_slip,
                        "media"      => $whats_billet_base64,
                        "fileName"   => 'Fatura_'.$whats_invoice_id.'.pdf'
                    ]
                ]);

            }
            $result = $response->getBody();

            if ($response->successful()) {

                $result = $response->json();

                if($result['status'] == 'PENDING'){
                    $status                  = 'Success';
                    $status_message          = 'Enviado';
                    //$whats_message_status    = $result;
                    $whats_message           = $result;
                }else{
                    $status                  = 'Error';
                    $status_message          = 'Erro ao enviar';
                    //$whats_message_status   = $result;
                    $whats_message          = $result;
                }
            }

            if($response->badRequest()){
                $status                  = 'Error';
                $status_message          = 'Erro ao enviar';
                //$whats_message_status   = $result;
                $whats_message          = $result;
            }

            DB::table('invoice_notifications')->insert([
                'user_id'           => $data['user_id'],
                'invoice_id'        => $data['invoice'],
                'type_send'         => 'whatsapp',
                'date'              => Carbon::now(),
                'subject'           => $data['title'],
                'email_id'          => '',
                'status'            => $status,
                'message_status'    => null,
                'message'           => $whats_message,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);

        }

        }
    }
    }

    //\Log::info('Linha 430:'. json_encode(['message' => $status_message, 'image' => $status_image, 'file' => $status_file]));

    return ['message' => $status_message, 'image' => $status_image, 'file' => $status_file];
    }

}



}
