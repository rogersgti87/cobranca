Nome: {{$email->name}} <br>
E-mail: {{$email->email}} <br>
Mensagem: {{$email->message}} <br> <br>
Data: {{\Carbon\Carbon::now()->format('d/m/Y H:i')}}