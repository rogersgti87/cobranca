<!DOCTYPE html>
<html>

<head>
    <title>{{$details['title']}}</title>
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
    <h1>Novo Pedido de Cliente</h1>

    <p>O cliente <b>{{$details['customer']}}</b>, contratou um <b>Novo Pedido</b></p>

    <p><b>Serviço(s) Contratado(s):</b></p>
    <ul>
        @foreach($details['description'] as $description)
        <li>{{$description}}</li>
        @endforeach
    </ul>

    <p>-----------------------------------------</p>

    <p>
        <b>Data da Fatura:</b> {{$details['data_fatura']}} <br>
        <b>Vencimento:</b> {{$details['data_vencimento']}} <br><br>
        <b>Total:</b> R$ {{$details['price']}} <br>
    </p>

</body>

</html>