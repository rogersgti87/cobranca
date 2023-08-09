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
    <h2>{{$title}}</h2>
    <p>Olá {{$customer}}, tudo bem?</p>
    <p>{{$text_remember}} <b>Fatura #{{$invoice_id}}</b></p>
    <p>Não se esqueça de efetuar seu pagamento para evitar multas e juros!</p>

    <p><b>Serviço(s) Contratado(s):</b></p>
    <ul>

        <li>{{$description}}</li>

    </ul>

    <p>-----------------------------------------</p>

    <p>
        <b>Data da Fatura:</b> {{$data_fatura}} <br>
        <b>Vencimento:</b> {{$data_vencimento}} <br><br>
        <b>Total:</b> R$ {{$price}} <br>
    </p>

    <p><b>Forma de pagamento:</b> {{ $payment_method }}</p>

    @if($payment_method == 'Pix')

        <p style="text-align:center">Se ainda não realizou o pagamento, ainda dá tempo, basta scannear o QrCode a baixo:</p>
        <p style="text-align:center"><img src="https://financeiro.rogerti.com.br/pix/{{$invoice_id}}.png" alt="QR Code" style="max-width:220px;"></p>
        <p style="text-align:center">Código digitavel pix :</p>
        <p style="text-align:center">{{$pix_emv}}</p>

        <ul>
            <li>O Pix será aprovado em poucos instantes após o pagamento.</li>
        </ul>

    @endif

    @if($payment_method == 'Boleto')
        <p style="text-align:center">Para gerar o Boleto é só clicar abaixo:</p>
        <p style="text-align:center"><a href="{{$billet_url_slip}}" target="_blank"><img src="https://s7003039.sendpul.se/image/747991a0e145ac2bbe69f063a9402e69/files/emailservice/userfiles/afdeb61c8175066a32c78dbe45c9569d7003039/rogerti/boleto.png"></a></p>
        <p style="text-align:center">Código digitável:</p>
        <p style="text-align:center">{{$billet_digitable_line}}</p>
    @endif

    <br>
    <br>

    <p><strong>Obs:</strong> Prezado caso já tenha feito o pagamento, favor desconsiderar este e-mail.</p>


    <p>Qualquer dúvida estamos à disposição. <br>
        Desejamos um ótimo dia!</p>

    <p>WhatsApp: <a href="https://api.whatsapp.com/send?phone=5522988280129" target="_Blank">(22) 9.8828-0129</a> || E-mail: <a href="mailto:financeiro@rogerti.com.br">financeiro@rogerti.com.br</a></p>
</body>

</html>
