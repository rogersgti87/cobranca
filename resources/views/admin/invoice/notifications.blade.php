<style>
    table.dataTable th {
      font-size: 12px;
    }
    table.dataTable td {
      font-size: 12px;
    }

    .popup {
      display: none;
      position: fixed;
      padding: 25px 10px 25px 10px;
      width: 350px;
      left: 50%;
      margin-left: -200px;
      height: 350px;
      top: 50%;
      margin-top: -200px;
      background: #FFF;
      border: 3px solid #333;
      z-index: 20;
      font-size:12px;
      color:#333;
    }

    .popup:after {
      position: fixed;
      content: "";
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      background: rgba(0,0,0,0.5);
      z-index: -2;
    }

    .popup:before {
      position: absolute;
      content: "";
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      background: #FFF;
      z-index: -1;
    }
    .overflow{
        height:100%;
        overflow-y: scroll;
        word-break: break-word;
    }

    /* width */
    ::-webkit-scrollbar {
      width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #888;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #555;
    }


    </style>



    <div class="table-responsive">
        <table class="display table-striped" style="width:100%" id="notifications-table">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Evento</th>
                    <th>E-mail</th>
                    <th>Data</th>
                    <th>Assunto</th>
                    <th>IP</th>
                    <th>Status</th>
                    <th>Texto Whatsapp</th>
                </tr>
            </thead>
            <tbody>



                @foreach ($notifications as $notification)

                @if($notification->status != 'Error')
                    <tr>
                        <td>
                            @if ($notification->type_send == "whatsapp")
                                <span class="badge badge-success"><i class="fab fa-whatsapp"></i></span>
                            @else
                                <span class="badge badge-info"><i class="fa fa-envelope"></i></span>
                            @endif
                        </td>
                        <td>
                            @if($notification->event == 'request')
                                Enviada
                            @elseif($notification->event == 'click')
                                Clicada
                            @elseif($notification->event == 'deferred')
                                Adiada
                            @elseif($notification->event == 'delivered')
                                Entregue
                            @elseif($notification->event == 'soft_bounce')
                                Soft Bounce
                            @elseif($notification->event == 'spam')
                                Denunciado(Spam)
                            @elseif($notification->event == 'unique_opened')
                            1º Abertura
                            @elseif($notification->event == 'hard_bounce')
                                Hard Bounce
                            @elseif($notification->event == 'unsubscribed')
                                Inscrição cancelada
                            @elseif($notification->event == 'opened')
                                Aberto
                            @elseif($notification->event == 'invalid_email')
                                E-mail inválido
                            @elseif($notification->event == 'blocked')
                                Bloqueado
                            @elseif($notification->event == 'proxy_open')
                                Loaded by proxy
                            @elseif($notification->event == 'error')
                                Erro
                            @else
                                {{$notification->event}}
                            @endif
                        </td>
                        <td>{{ $notification->email}}</td>
                        <td>@if($notification->type_send == 'whatsapp') {{ $notification->date != null ? date('d/m/Y H:i:s',strtotime($notification->date)) : '-' }} @else {{ $notification->date_email != null ? date('d/m/Y H:i:s',strtotime($notification->date_email)) : '-'}} @endif</td>
                        <td>{{ $notification->subject }}</td>
                        <td>{{ $notification->sending_ip }}</td>
                        <td>
                            @if ($notification->type_send == 'whatsapp' && $notification->message_status == "success")
                                <span class="badge badge-success"></span>
                            @elseif ($notification->reason == 'send')
                                <span class="badge badge-success"></span>
                            @else
                                <span class="badge badge-danger"></span>
                            @endif
                        </td>
                        <td>@if($notification->type_send == 'whatsapp')
                            <div class="popup" id="popup-{{$notification->id}}">
                                <div class="overflow">
                                    @if($notification->message_status == "success")
                                        @if(property_exists(json_decode($notification->message)->response[0], 'body'))
                                            {!! str_replace("\n","<br>",json_decode($notification->message)->response[0]->body) !!}
                                        @endif
                                    @else
                                        <span>Mensagem não foi enviada!</span>
                                    @endif
                                <br>
                                <br>
                                </div>
                                <button type="button" class="btn btn-danger" onclick="hide({{$notification->id}})">Fechar</button>
                              </div>
                            <button onclick="openMessage({{$notification->id}})" class="btn btn-xs btn-success" type="button">Mensagem</button>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach

            </tbody>
        </table>
    </div><!-- table-responsive -->

    <script>

   $('#notifications-table').DataTable({
            order: [[3, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
            },
        });



        function openMessage(id){
            $("#popup-"+id).attr("style", "display:block");
        }

        var hide = function(id) {
            $("#popup-"+id).attr("style", "display:none");
        }





        </script>

