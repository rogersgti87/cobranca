<!DOCTYPE html>
<html>

<head>
    <title>{{$title}}</title>
    <style>

        table {
            border: 1px solid #DDD;
        }

        table th,
        table td {
            padding: 3px 5px;
        }

        table th {
            border-bottom: 1px solid #DDD;
            border-right: 1px solid #DDD;
        }

        table td {
            border-right: 1px solid #DDD;
        }

        table th:last-child,
        table td:last-child {
            border-right: 0;
        }
    </style>
</head>

<body>
    <div style="text-align:left;">
    <h4 style="text-align: left;">Mensagem automática, favor não responder este e-mail.</h4>
    <br>

        <img src="{{$logo}}" style="max-width:200px;" title="{{$company}}">

    <br>
    <h1>{{$title}}</h1>
    <p>{{ $message_customer }}</p>
    <p>{!! $message_notification !!}</p>

    <p><b>Serviço(s) Contratado(s):</b></p>
    <ul>

        <li>{{$service}}</li>

    </ul>

    <p>-----------------------------------------</p>

    <p>
        <b>Data da Fatura:</b> {{$date_invoice}} <br>
        <b>Forma de pagamento:</b> {{ $payment_method }} <br>
        <b>Vencimento:</b> {{$date_due}} <br>
        <b>Total:</b> R$ {{$price}} <br>
        <b>Status:</b> {{$status}} <br>
    </p>


    @if($status == 'Pendente')

    @if($payment_method == 'Pix')

        <p style="text-align:left">Se ainda não realizou o pagamento, ainda dá tempo, basta scannear o QrCode a baixo:</p>
        <p style="text-align:left"><img src="{{$pix_qrcode_image_url}}" alt="QR Code" style="max-width:220px;"></p>

        <p style="text-align:left">Você também pode copiar e colar o código PIX:</p>
        <p style="text-align:left"><textarea rows="4" cols="60">{{$pix_emv}}</textarea></p>

        <ul>
            <li>O Pix será aprovado em poucos instantes após o pagamento.</li>
        </ul>

    @endif

    @if($payment_method == 'Boleto')
        <p style="text-align:left">Para gerar o Boleto é só clicar abaixo:</p>
        <p style="text-align:left"><a href="{{$billet_url_slip}}" target="_blank"><img src="https://s7003039.sendpul.se/image/747991a0e145ac2bbe69f063a9402e69/files/emailservice/userfiles/afdeb61c8175066a32c78dbe45c9569d7003039/rogerti/boleto.png"></a></p>
        <p style="text-align:left">Código digitável:</p>
        <p style="text-align:left"><textarea rows="4" cols="60">{{$billet_digitable_line}}</textarea></p>
    @endif

    <br>
    <br>

    <p><strong>Obs:</strong> Prezado caso já tenha feito o pagamento, favor desconsiderar este e-mail.</p>

    @elseif($status == 'Pago')

    <p><b>Observação:</b> Este e-mail servirá como recibo para este pagamento.</p>

    @endif



    <p>Qualquer dúvida estamos à disposição. <br>
        Desejamos um ótimo dia!</p>

    <p>WhatsApp: <a href="https://api.whatsapp.com/send?phone=55{{$user_whatsapp}}" target="_Blank">{{ formatPhone($user_whatsapp)}}</a> || E-mail: <a href="mailto:{{$user_email}}">{{$user_email}}</a></p>

    <br>

    <h4 style="text-align: left;">Mensagem automática, favor não responder este e-mail.</h4>

</div>
</body>

</html>
