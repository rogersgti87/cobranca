<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use DB;
use App\Models\User;

class InvoiceNotification extends Model
{


    public static function Email($data){


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

                "subject"       => $data['title'],
                "htmlContent"   => $data['body']
          ]);

        $result = $response->getBody();

         \Log::info('Debug Email: '. json_encode(json_decode($result)));
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


    }

    public static function Whatsapp($data){


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


        if($data['notification_whatsapp'] == 's'){

        $response = Http::withHeaders([
            "Content-Type"  => "application/json"
        ])->post('https://zapestrategico.com.br/api/send-message',[
            "access_token"  => $data['user_access_token_wp'],
            "whatsapp"      => '55'.$data['customer_whatsapp'],
            "message"       => $data['text_whatsapp']
        ]);

        $result = $response->getBody();

        $whats_status           = json_decode($result);
        if($whats_status->status == 'success'){
            $whats_message_status   = $whats_status->status;
            $whats_message          = json_encode($whats_status);
        }else{
            $whats_message_status   = json_encode($whats_status);
            $whats_message          = '';
        }


        DB::table('invoice_notifications')->insert([
            'user_id'           => $data['user_id'],
            'invoice_id'        => $data['invoice'],
            'type_send'         => 'whatsapp',
            'date'              => Carbon::now(),
            'subject'           => $data['title'],
            'email_id'          => '',
            'status'            => $whats_status == true ? 'Success' : 'Error',
            'message_status'    => $whats_message_status,
            'message'           => $whats_message,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now()
        ]);


        \Log::info('Status do pedido: '.$data['status']);
        if($data['status'] == 'Pendente') {

            //Enviar imagem qrcode pix
        if($whats_payment_method == 'Pix'){

            $response = Http::withHeaders([
            "Content-Type"  => "application/json"
        ])->post('https://zapestrategico.com.br/api/send-image',[
                "access_token"  => $data['user_access_token_wp'],
                "whatsapp"      => '55'.$data['customer_whatsapp'],
                "message"       => 'data:image/png;base64,'.$data['pix_qrcode_base64'],
                "caption"       =>  $whats_pix_emv
            ]);

            $result = $response->getBody();

            $whats_status           = json_decode($result);
        if($whats_status->status == 'success'){
            $whats_message_status   = $whats_status->status;
            $whats_message          = json_encode($whats_status);
        }else{
            $whats_message_status   = json_encode($whats_status);
            $whats_message          = '';
        }


            DB::table('invoice_notifications')->insert([
                'user_id'           => $data['user_id'],
                'invoice_id'        => $data['invoice'],
                'type_send'         => 'whatsapp',
                'date'              => Carbon::now(),
                'subject'           => $data['title'],
                'email_id'          => '',
                'status'            => $whats_status == true ? 'Error' : 'Success',
                'message_status'    => $whats_message_status,
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
                    "Content-Type"  => "application/json"
                ])->post('https://zapestrategico.com.br/api/send-file',[
                    "access_token"  => $data['user_access_token_wp'],
                    "whatsapp"      => '55'.$data['customer_whatsapp'],
                    "file"          => 'data:application/pdf;base64,'.$whats_billet_base64,
                    "caption"       =>  $whats_billet_digitable_line,
                    "filename"      =>  'Fatura_'.$whats_invoice_id.'.pdf'
                ]);

            $result = $response->getBody();

            $whats_status           = json_decode($result);
        if($whats_status->status == 'success'){
            $whats_message_status   = $whats_status->status;
            $whats_message          = json_encode($whats_status);
        }else{
            $whats_message_status   = json_encode($whats_status);
            $whats_message          = '';
        }


            DB::table('invoice_notifications')->insert([
                'user_id'           => $data['user_id'],
                'invoice_id'        => $data['invoice'],
                'type_send'         => 'whatsapp',
                'date'              => Carbon::now(),
                'subject'           => $data['title'],
                'email_id'          => '',
                'status'            => $whats_status == true ? 'Error' : 'Success',
                'message_status'    => $whats_message_status,
                'message'           => $whats_message,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);

        }
    }
    }


    }



}
