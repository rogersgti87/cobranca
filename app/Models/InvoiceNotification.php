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
        $whats_payment_method           = $data['payment_method'];
        $whats_pix_emv                  = $data['pix_emv'];
        $whats_pix_image                = $data['pix_qrcode_image_url'];
        $whats_billet_digitable_line    = $data['billet_digitable_line'];
        $whats_billet_url_slip          = $data['billet_url_slip'];


        $data['text_whatsapp'] = "*MENSAGEM AUTOMÁTICA*\n\n";
        $data['text_whatsapp'] .= "$message_customer\n\n";
        $data['text_whatsapp'] .= "$message_notification \n\n";
        $data['text_whatsapp'] .= "*Serviço(s) Contratado(s):* \n\n";
        $data['text_whatsapp'] .= "$whats_description \n\n";
        $data['text_whatsapp'] .= "*Data da Fatura:* $whats_data_fatura \n";
        $data['text_whatsapp'] .= "*Vencimento:* $whats_data_vencimento \n";
        $data['text_whatsapp'] .= "*Forma de pagamento:* $whats_payment_method \n";
        $data['text_whatsapp'] .= "*Total:* R$ $whats_price \n\n";



        if($whats_payment_method == 'Boleto'){
            $data['text_whatsapp'] .= "Para abrir o Boleto é só clicar no link abaixo\n";
            $data['text_whatsapp'] .= "$whats_billet_url_slip\n\n";
        }

        $data['text_whatsapp'] .= "*Linha Digitável abaixo* \n\n";

        if($data['notification_whatsapp'] == 's'){

        $response = Http::withHeaders([
            "Content-Type"  => "application/json"
        ])->post('https://whatsapp.rogerti.com.br:8000/api/send-message',[
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
            'status'            => $whats_status == true ? 'Error' : 'Success',
            'message_status'    => $whats_message_status,
            'message'           => $whats_message,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now()
        ]);


        //Enviar imagem qrcode pix

        if($whats_payment_method == 'Pix'){

            $response = Http::withHeaders([
            "Content-Type"  => "application/json"
        ])->post('https://whatsapp.rogerti.com.br:8000/api/send-image',[
                "access_token"  => $data['user_access_token_wp'],
                "whatsapp"      => '55'.$data['customer_whatsapp'],
                "message"       => 'data:image/png;base64,'.$whats_pix_image
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
                'subject_whatsapp'  => $data['title'],
                'senpulse_email_id' => '',
                'status'            => $whats_status == true ? 'Error' : 'Success',
                'message_status'    => $whats_message_status,
                'message'           => $whats_message,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);

        }
            //Fim imagem qrcode pix

            $data['text_whatsapp_payment'] = '';

        if($whats_payment_method == 'Pix'){
            $data['text_whatsapp_payment'] .= "$whats_pix_emv\n\n";
        }else{
            $whats_billet_digitable_line = removeEspeciais($whats_billet_digitable_line);
            $data['text_whatsapp_payment'] .= "$whats_billet_digitable_line\n\n";
        }


             $response = Http::withHeaders([
                    "Content-Type"  => "application/json"
                ])->post('https://whatsapp.rogerti.com.br:8000/api/send-message',[
                    "access_token"  => $data['user_access_token_wp'],
                    "whatsapp"      => '55'.$data['customer_whatsapp'],
                    "message"       => $data['text_whatsapp_payment']
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


    public static function sendNotificationConfirm($data){

        $config = DB::table('configs')->where('id',1)->first();

        $api_token  = $config->sendpulse_token;
        $api_key    = $config->sendpulse_secret;

        $response = Http::post('https://api.sendpulse.com/oauth/access_token',[
            'grant_type'        => 'client_credentials',
            'client_id'         =>  $api_token,
            'client_secret'     =>  $api_key
          ]);

        $result = $response->getBody();

        $access_token = json_decode($result)->access_token;

        if($data['customer_email2'] != null){
            $emails = array(
                [
                    "customer"  => $data['customer'],
                    "email"     => $data['customer_email']
                ],
                [
                    "customer"  => $data['customer'],
                    "email"     => $data['customer_email2']
                ]
                );
        } else {
            $emails = array(
                [
                "customer"  => $data['customer'],
                "email"     => $data['customer_email']
            ]
        );
        }

        $response = Http::withToken($access_token)->post('https://api.sendpulse.com/smtp/emails',[
            "email" =>  [
                "subject"  => $data['title'],
                "html" => base64_encode($data['body']),
                "from" => [
                    "name"  => "Financeiro",
                    "email" => $config->smtp_user
                ],
                "to" => $emails
            ],
          ]);

        $result = $response->getBody();

        $senpulse_email_id = json_decode($result)->id;

        DB::table('invoice_notifications')->insert([
            'invoice_id'        => $data['invoice_id'],
            'type_send'         => 'email',
            'date'              => Carbon::now(),
            'subject_whatsapp'  => '',
            'senpulse_email_id' => $senpulse_email_id,
            'status'            => null,
            'message_status'    => null,
            'message'           => null,
            'subject_whatsapp'  => null,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now()
        ]);


        $whats_customer_name            = $data['customer'];
        $whats_invoice_id               = $data['invoice_id'];
        $whats_description              = $data['description_whatsapp'];
        $whats_data_fatura              = $data['data_fatura'];
        $whats_data_vencimento          = $data['data_vencimento'];
        $whats_data_pagamento           = $data['data_pagamento'];
        $whats_price                    = $data['price'];
        $whats_payment_method           = $data['payment_method'];


        $data['text_whatsapp'] = "*MENSAGEM AUTOMÁTICA*\n\n";
        $data['text_whatsapp'] .= "Olá $whats_customer_name, tudo bem?\n\n";
        $data['text_whatsapp'] .= "Seu pagamento referente a *Fatura #$whats_invoice_id* foi confimado!\n\n";
        $data['text_whatsapp'] .= "*Serviço(s) Contratado(s):* \n\n";
        $data['text_whatsapp'] .= "$whats_description \n\n";
        $data['text_whatsapp'] .= "*Data da Fatura:* $whats_data_fatura \n";
        $data['text_whatsapp'] .= "*Vencimento:* $whats_data_vencimento \n";
        $data['text_whatsapp'] .= "*Data do Pagamento:* $whats_data_pagamento \n";
        $data['text_whatsapp'] .= "*Forma de pagamento:* $whats_payment_method \n";
        $data['text_whatsapp'] .= "*Total:* R$ $whats_price \n\n";


        if($data['notification_whatsapp'] == 's'){

           $response = Http::withHeaders([
            "Content-Type"  => "application/json"
        ])->post('https://whatsapp.rogerti.com.br:8000/api/send-message',[
            "access_token"  => 'FX1UVGhGVs5Ndj1oqhcpsJBNCc4GRe6p',
            "whatsapp"      => '55'.$data['customer_phone'],
            "message"       => $data['text_whatsapp']
        ]);

            $result = $response->getBody();

            $whats_status           = json_decode($result);
        if($whats_status->status == 'success'){
            $whats_message_status   = $whats_status->status;
            $whats_message          = json_encode($whats_status->response);
        }else{
            $whats_message_status   = json_encode($whats_status->response);
            $whats_message          = '';
        }

            DB::table('invoice_notifications')->insert([
                'invoice_id'        => $data['invoice_id'],
                'type_send'         => 'whatsapp',
                'date'              => Carbon::now(),
                'subject_whatsapp'  => $data['title'],
                'senpulse_email_id' => '',
                'status'            => $whats_status == true ? 'Error' : 'Success',
                'message_status'    => $whats_message_status,
                'message'           => $whats_message,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now()
            ]);


            }

    }



}
