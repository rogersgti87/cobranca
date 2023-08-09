<!DOCTYPE html>
<html>

<head>
    <title>Recuperação de Senha</title>
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
<h1>Recuperação de Senha</h1>

<p>Você está recebendo este e-mail porque recebemos um pedido de redefinição de senha para sua conta.</p>
<p>Caso você tenha solicitado a recuperação de senha, clique no botão abaixo para inserir uma nova senha.</p>

<a href="{{ route('reset.password.get', $token) }}">Redefinir Senha</a>

<p>Se você não solicitou a recuperação de senha, desconsidere o e-mail.</p>
</body>
</html>