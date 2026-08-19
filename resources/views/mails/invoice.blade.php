<!DOCTYPE html>
<html>
<head>
    <title>{{$title}}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #0A0A0A; }
        .btn-pay {
            display: inline-block;
            background: #D4AF37;
            color: #0A0A0A !important;
            padding: 14px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .muted { color: #666; font-size: 13px; }
    </style>
</head>
<body>
    <div style="text-align:left;">
        <h4 style="text-align: left;">Mensagem automática, favor não responder este e-mail.</h4>
        <br>

        @if(!empty($logo))
            <img src="{{$logo}}" style="max-width:200px;" title="{{$company}}">
        @endif

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
            @if(!empty($payment_url))
                <p style="text-align:left;margin:24px 0;">
                    <a href="{{ $payment_url }}" target="_blank" class="btn-pay">
                        Abrir página de pagamento
                    </a>
                </p>
                <p class="muted">Ou acesse: <a href="{{ $payment_url }}">{{ $payment_url }}</a></p>
                <p class="muted">Na página você poderá pagar com {{ $payment_method }}, copiar o código e imprimir a cobrança.</p>
            @endif

            <br>
            <p><strong>Obs:</strong> Prezado caso já tenha feito o pagamento, favor desconsiderar este e-mail.</p>

        @elseif($status == 'Pago')
            <p><b>Observação:</b> Este e-mail servirá como recibo para este pagamento.</p>
            @if(!empty($payment_url))
                <p class="muted"><a href="{{ $payment_url }}">Ver comprovante da cobrança</a></p>
            @endif
        @endif

        <p>Qualquer dúvida estamos à disposição. <br>
            Desejamos um ótimo dia!</p>

        <p>
            WhatsApp: <a href="https://api.whatsapp.com/send?phone=55{{$user_whatsapp}}" target="_Blank">{{ formatPhone($user_whatsapp)}}</a>
            || E-mail: <a href="mailto:{{$user_email}}">{{$user_email}}</a>
        </p>

        <br>
        <h4 style="text-align: left;">Mensagem automática, favor não responder este e-mail.</h4>
        <p class="muted">CobrançaSegura — www.cobrancasegura.com.br</p>
    </div>
</body>
</html>
