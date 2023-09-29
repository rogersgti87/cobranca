@extends('layouts.admin')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">

        <div class="col-md-12">
         <div class="row d-flex justify-content-center">
              <div class="col-md-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h5 id="total_invoices_curerency">R$0,00</h5>
                        <p id="total_invoices">Total: 0</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h5 id="pendent_invoices_curerency">R$0,00</h5>
                        <p id="total_pendent">Pendentes: 0</p>
                    </div>
                    <div class="icon">
                        <i class="far fa-hourglass"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h5 id="pay_invoices_curerency">R$0,00</h5>
                        <p id="total_pay">Pagas: 0</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
            </div>


            <div class="col-md-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h5 id="proccessing_invoices_curerency">R$0,00</h5>
                        <p id="total_proccessing">Processamentos: 0</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-navy">
                    <div class="inner">
                        <h5 id="expired_invoices_curerency">R$0,00</h5>
                        <p id="total_expired">Expiradas: 0</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h5 id="cancelled_invoices_curerency">R$0,00</h5>
                        <p id="total_cancelled">Canceladas: 0</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

        </div>

        </div>

        <div class="col-md-12">

            <div class="form-row">

                <div class="form-group col-md-3 col-6">
                    <label>Tipo</label>
                    <select class="form-control"  id="filter-type">
                        <option value="date_invoice">Data da Fatura</option>
                        <option value="date_due">Data do Vencimento</option>
                    </select>
                </div>

                <div class="form-group col-md-3 col-6">
                    <label>Status</label>
                    <select class="form-control"  id="filter-status">
                        <option value="">Todos</option>
                        <option value="Pendente">Pendente</option>
                        <option value="Processamento">Processamento</option>
                        <option value="Expirado">Expirado</option>
                        <option value="Pago">Pago</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="form-group col-md-2 col-6" id="">
                    <label>Data inicial</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data incial" id="filter-date-ini" value="{{date('Y-m-d',strtotime('first day of this month'))}}">
                </div>

                <div class="form-group col-md-2 col-6" id="">
                    <label>Data final</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data Final" id="filter-date-end" value="{{date('Y-m-d',strtotime('last day of this month'))}}">
                </div>

                <div class="form-group col-md-2 col-12 d-flex justify-content-start align-items-end">
                    <button class="btn btn-secondary mb-2" type="button" id="filter-button"><i class="fa fa-search"></i> Filtrar</button>
                </div>
            </div>


      </div>


      <div class="col-md-12">
        <div class="card-box">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-12">
                    <div id="pagination" class="d-flex justify-content-start align-items-center mb-1">
                        <button class="btn btn-sm btn-secondary" onclick="loadInvoices(prevPage)">Anterior</button>
                        &nbsp;<span id="page-num"></span>&nbsp;
                        <button class="btn btn-sm btn-secondary" onclick="loadInvoices(nextPage)">Próxima</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive fixed-solution">
                <table class="table table-hover table-striped table-sm">
                    <thead class="thead-light">
                    <tr>
                        <th> #</th>
                        <th> Cliente</th>
                        <th> Serviço</th>
                        <th> Data</th>
                        <th> Vencimento</th>
                        <th> Pago em</th>
                        <th> Valor</th>
                        <th> Forma de Pagamento</th>
                        <th> Status</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody class="tbodyCustom" id="list-invoices">

                    </tbody>
                </table>
            </div>

        </div>

    </div>
    <!-- FIM TABLE -->


        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



 <!-- Modal :: Form Invoice -->
 <div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" class="form-horizontal" id="form-request-invoice">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInvoiceLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-invoice">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-save-invoice"><i class="fa fa-check"></i> Salvar</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
 </div>
 <!-- Modal :: Form Invoice -->



  <!-- Modal :: Form Invoice -->
  <div class="modal fade" id="modalNotifications" tabindex="-1" role="dialog" aria-labelledby="modalNotificationsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" class="form-horizontal" id="form-request-notifications">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNotificationsLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-notifications">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                </div>
            </form>
        </div>
    </div>
 </div>
 <!-- Modal :: Form Invoice -->


@section('scripts')

<script>
    function sendNotification(invoice_id){
        console.log(invoice_id);

        // Criação dos checkboxes e rótulos dentro de elementos div para alinhar na mesma linha
    const whatsappContainer = document.createElement('div');
    whatsappContainer.classList.add('checkbox-container');

    const whatsappCheckbox = document.createElement('input');
    whatsappCheckbox.type = 'checkbox';
    whatsappCheckbox.id = 'whatsapp-checkbox';
    whatsappCheckbox.value = 'whatsapp';
    whatsappContainer.appendChild(whatsappCheckbox);

    const whatsappLabel = document.createElement('label');
    whatsappLabel.textContent = 'WhatsApp';
    whatsappLabel.setAttribute('for', 'whatsapp-checkbox');
    whatsappContainer.appendChild(whatsappLabel);

    const emailContainer = document.createElement('div');
    emailContainer.classList.add('checkbox-container');

    const emailCheckbox = document.createElement('input');
    emailCheckbox.type = 'checkbox';
    emailCheckbox.id = 'email-checkbox';
    emailCheckbox.value = 'email';
    emailContainer.appendChild(emailCheckbox);

    const emailLabel = document.createElement('label');
    emailLabel.textContent = 'Email';
    emailLabel.setAttribute('for', 'email-checkbox');
    emailContainer.appendChild(emailLabel);

    // Criando o formulário e adicionando os elementos criados
    const form = document.createElement('form');
    form.appendChild(whatsappContainer);
    form.appendChild(emailContainer);

        Swal.fire({
            title: 'Selecione as opções:',
            html: form,
                // '<div class="col-md-12"><div class="inline">'+
                // '<div class="col-6"><input class="form-control" type="checkbox" id="whatsapp-checkbox" value="whatsapp"> WhatsApp</div>' +
                // '<div class="col-6"><input class="form-control" type="checkbox" id="email-checkbox" value="email"> Email</div>'+
                // '</div></div>',
            focusConfirm: false,
            preConfirm: () => {
            const selectedOptions = [];

            if (whatsappCheckbox.checked) {
                selectedOptions.push(whatsappCheckbox.value);
            }

            if (emailCheckbox.checked) {
                selectedOptions.push(emailCheckbox.value);
            }

            if (selectedOptions.length === 0) {
                Swal.showValidationMessage('Selecione pelo menos uma opção');
            }

            return selectedOptions;
        }
        }).then((result) => {
            if (!result.dismiss) {
                // Enviar os valores selecionados via API
                const selectedOptions = result.value;
                //console.log('Opções selecionadas: ', selectedOptions);

                // Aqui você pode enviar os valores selecionados via API
                // Substitua este código pelo chamado real da sua API
                // Exemplo de chamada da API usando fetch:

                const loadingAlert = Swal.fire({
                title: 'Aguarde...',
                allowOutsideClick: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });


                var url = '{{url("admin/invoice-notificate")}}'+'/'+invoice_id;

                fetch(url, {
                    method: 'POST',
                    body: JSON.stringify({ selectedOptions }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{csrf_token()}}",
                    }
                })
                .then(response => response.json())
                .then(data => {
                    var payment_method  = `<p><b class="text-primary">Forma de pagamento: </b>${data.payment_method}</p><hr>`;
                    var result_whatsapp = `<p class="text-primary"><b>Whatsapp:</b></p> <p><b>Mensagem:</b> ${data.whatsapp.mensagem}</p><p><b>QRCODE:</b> ${data.whatsapp.pix}</p><p><b>Boleto:</b> ${data.whatsapp.boleto}</p>`;
                    var result_email = `<p><b class="text-primary">E-mail:</b> ${data.email}</p>`;
                    loadingAlert.close();

                    Swal.fire({
                    title: 'Notificações',
                    html: payment_method+result_whatsapp+'<hr>'+result_email,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'custom-swal',
                        title: 'swal2-title',
                        content: 'swal2-content'
                    }
                });

                })
                .catch(error => {
                    console.error('Erro ao chamar a API: ', error);
                    loadingAlert.close();
                });

            }
        });
    }
</script>

<script>


   // Open Modal - Create - Invoices
   $(document).on("click", "#btn-modal-invoice", function() {
        var type = $(this).data('type');
        var customer_id = "{{ isset($data) ? $data->id : ''}}";
        $("#modalInvoice").modal('show');
        if(type == 'add-invoice'){
            $("#modalInvoiceLabel").html('Adicionar Fatura');
            var url = `{{ url("/admin/invoices/form?customer_id=") }}${customer_id}`;
        }else{
            $("#modalInvoiceLabel").html('Editar Fatura');
            var invoice = $(this).data('invoice');
            //console.log(invoice);
            var url = `{{ url("/admin/invoices/form?customer_id=") }}${customer_id}&id=${invoice}`;
        }

        //console.log(url);
        $.get(url,
            $(this)
            .addClass('modal-scrollfix')
            .find('#form-content-invoice')
            .html('Carregando...'),
            function(data) {
                // console.log(data);
                $("#form-content-invoice").html(data);
                $('.money').mask('000.000.000.000.000,00', {reverse: true});
                // aqui quando selecionar um serviço, buscar qual o valor dele e atualizar o campo de preço.
                $('#customer_service_id').on('change', function() {
                    //var service_id          = $(this).val();
                    var service_price       = $(this).find(':selected').data('price');
                    var service_description = $(this).find(':selected').data('description');
                    $('#invoice_price').val(parseFloat(service_price).toLocaleString('pt-br', {minimumFractionDigits: 2}));
                    $('#invoice_description').val(service_description);
                });

            });
    });




$('#btn-delete').click(function (e) {

Swal.fire({
    title: 'Deseja remover este registro?',
    text: "Você não poderá reverter isso!",
    icon: 'question',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, deletar!'
}).then((result) => {
    if (result.value) {
        $.ajax({
            url: "{{url($linkDestroy)}}",
            method: 'DELETE',
            data: $('.form').serialize(),
            success:function(data){
                location.href = "{{url($link)}}";
            },
            error:function (xhr) {

                if(xhr.status === 422){
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'warning',
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                } else{
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'error',
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                }


            }
        });

    }
});

});



        // Open Modal - Notifications
        $(document).on("click", "#btn-modal-notifications", function() {

$("#modalNotifications").modal('show');
    $("#modalNotificationsLabel").html('Notificações');
    var invoice = $(this).data('invoice');
    var url = "{{url('/admin/load-invoice-notifications')}}"+'/'+invoice;

//console.log(url);
$.get(url,
    $(this)
    .addClass('modal-scrollfix')
    .find('#form-content-notifications')
    .html('Carregando...'),
    function(data) {
        $("#form-content-notifications").html(data);
    });
});


$(document).on('click', '#btn-invoice-status', function(e) {
    var invoice_id = $(this).data('invoice');

    Swal.fire({
        title: 'Deseja atualizar o status da fatura?',
        text: "Se o status do Gateway de pagamento for diferente do sistema, o cliente será notificado sobre a mudança do status!",
        icon: 'question',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, atualizar!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "{{url('admin/invoices-check-status')}}"+'/'+invoice_id,
                method: 'GET',
                success:function(data){
                    loadInvoices();
                },
                error:function (xhr) {

                    if(xhr.status === 422){
                        Swal.fire({
                            text: xhr.responseJSON,
                            icon: 'warning',
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    } else{
                        Swal.fire({
                            text: xhr.responseJSON,
                            icon: 'error',
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    }


                }
            });

        }
    });

    });

    $(document).on('click', '#btn-delete-invoice', function(e) {
    var invoice = $(this).data('invoice');

    Swal.fire({
        title: 'Deseja cancelar esta fatura?',
        text: "Você não poderá reverter isso!",
        icon: 'question',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, cancelar!'
    }).then((result) => {
        if (result.value) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            $.ajax({
                url: "{{url('admin/invoices')}}"+'/'+invoice,
                method: 'DELETE',
                success:function(data){
                    loadInvoices();
                },
                error:function (xhr) {

                    if(xhr.status === 422){
                        Swal.fire({
                            text: xhr.responseJSON,
                            icon: 'warning',
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    } else{
                        Swal.fire({
                            text: xhr.responseJSON,
                            icon: 'error',
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    }


                }
            });

        }
    });

    });

     //Save Invoice
     $(document).on('click', '#btn-save-invoice', function(e) {
            e.preventDefault();

            $("#btn-save-invoice").attr("disabled", true);
            $("#btn-save-invoice").text('Aguarde...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            var data = $('#form-request-invoice').serialize();
            var invoice = $('#invoice').val();
            if(invoice != ''){
                var url = "{{ url('admin/invoices') }}"+'/'+invoice;
                var method = 'PUT';
            }else{
                var url = "{{ url('admin/invoices') }}";
                var method = 'POST';
            }

            $.ajax({
                url: url,
                data:data,
                method:method,
                beforeSend:function(){
                    $("#btn-save-invoice").attr("disabled", true);
                    $("#btn-save-invoice").text('Aguarde...');
                },
                success:function(data){
                    $("#btn-save-invoice").attr("disabled", false);
                    $("#btn-save-invoice").html('<i class="fa fa-check"></i> Salvar');
                    //console.log(data);
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>" + data + "</h5>",
                        icon: 'success',
                        showConfirmButton: true,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                    }).then((result) => {
                        $('#modalInvoice').modal('hide');
                        loadInvoices();
                    });
                },
                error:function (xhr) {
                    $("#btn-save-invoice").attr("disabled", false);
                    $("#btn-save-invoice").html('<i class="fa fa-check"></i> Salvar');
                    if(xhr.status === 422){
                        Swal.fire({
                            text: xhr.responseJSON,
                            width:300,
                            icon: 'warning',
                            color: '#007bff',
                            confirmButtonColor: "#007bff",
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    } else{
                        Swal.fire({
                            text: xhr.responseJSON,
                            width:300,
                            icon: 'error',
                            color: '#007bff',
                            confirmButtonColor: "#007bff",
                            showClass: {
                                popup: 'animate__animated animate__wobble'
                            }
                        });
                    }


                }
            });



        });



</script>


<script>
    const listInvoices          = document.getElementById('list-invoices');
    const filterInputType       = document.getElementById('filter-type');
    const filterInputDateIni    = document.getElementById('filter-date-ini');
    const filterInputDateEnd    = document.getElementById('filter-date-end');
    const filterInputStatus     = document.getElementById('filter-status');
    const filterButton          = document.getElementById('filter-button');
    const paginationContainer   = document.getElementById('pagination');


    function loadInvoices(page = 1) {
        const filterType        = filterInputType.value;
        const filterDateIni     = filterInputDateIni.value;
        const filterDateEnd     = filterInputDateEnd.value;
        const filterStatus      = filterInputStatus.value;

        $.ajax({
        type:'GET',
        url: `load-invoices?page=${page}&type=${filterType}&dateini=${filterDateIni}&dateend=${filterDateEnd}&status=${filterStatus}`,
        beforeSend: function(){
            $('#list-invoices').html('<tr><td style="text-align:center;" colspan="99"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></td></tr>');
            $('#list-invoices').html('');

        },
        success:function(data){
            console.log(data);
        $('#total_invoices').text('Total: '+data.result.data[0].qtd_invoices);
        $('#total_invoices_curerency').text(parseFloat(data.result.data[0].total_currency).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#total_pendent').text('Pendentes: '+data.result.data[0].qtd_pendente);
        $('#pendent_invoices_curerency').text(parseFloat(data.result.data[0].pendente_currency).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#total_pay').text('Pagas: '+data.result.data[0].qtd_pago);
        $('#pay_invoices_curerency').text(parseFloat(data.result.data[0].pago_currency).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#total_proccessing').text('Processamentos: '+data.result.data[0].qtd_processamento);
        $('#proccessing_invoices_curerency').text(data.result.data[0].processamento_currency != null ? parseFloat(data.result.data[0].processamento_currency).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : 'R$0,00');
        $('#total_cancelled').text('Canceladas: '+data.result.data[0].qtd_cancelado);
        $('#cancelled_invoices_curerency').text(parseFloat(data.result.data[0].cancelado_currency).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));


        $('#list-invoices').html('');


        var html = '';
            if(data.result.data.length > 0){
                $.each(data.result.data, function(i, item) {
                html += '<tr>';
                html += `<td>${item.id}</td>`;
                html += `<td>${item.customer_name}</td>`;
                html += `<td>${item.service_name}</td>`;
                html += `<td>${moment(item.date_invoice).format('DD/MM/YYYY')}</td>`;
                html += `<td>${moment(item.date_due).format('DD/MM/YYYY')}</td>`;
                html += `<td>${item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-'}</td>`;
                html += `<td>${item.price}</td>`;
                html += `<td>${item.gateway_payment+' ('+item.payment_method })</td>`;
                html += `<td class="badge ${item.status == 'Pago' ? 'badge-success' : item.status == 'Pendente' ? 'badge-warning' : 'badge-danger'}">${item.status}</td>`;
                html += `<td>
                            ${item.status == 'Pendente' ? '<a href="#" data-original-title="Editar fatura" id="btn-modal-invoice" data-type="edit-invoice" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-secondary btn-xs"> <i class="far fa-edit"></i></a>' : ''}
                            ${item.status != 'Pago' && item.status != 'Cancelado' ? '<a href="#" data-original-title="Consultar Status" id="btn-invoice-status" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-primary btn-xs"> <i class="fas fa-search"></i></a>' : ''}
                            <a href="#" data-original-title="Reenviar Notificação" onclick="sendNotification(${item.id})" id="btn-notificate" data-invoice="${item.id}" data-placement="left" data-tt="tooltip" class="btn btn-info btn-xs"> <i class="fa fa-paper-plane"></i></a>
                            <a href="#" data-original-title="Notificações" id="btn-modal-notifications" data-invoice="${item.id}" data-placement="left" data-tt="tooltip" class="btn btn-warning btn-xs"> <i style="padding:0 5px;" class="fa fa-info"></i></a>
                            ${item.status == 'Pendente' ? '<a href="#" data-original-title="Cancelar Fatura" id="btn-delete-invoice" data-placement="left" data-invoice="'+item.id+'" data-tt="tooltip" class="btn btn-danger btn-xs"> <i class="fas fa-undo-alt"></i></a>' : ''}
                            </td>`;
                html += '</tr>';
            });
            }else{
                html += '<tr><td style="text-align:center;" colspan="99">Fatura não encontrada!</tr>';
            }

            $('#list-invoices').append(html);

                currentPage = data.result.current_page;
                prevPage = data.result.prev_page_url ? currentPage - 1 : currentPage;
                nextPage = data.result.next_page_url ? currentPage + 1 : currentPage;

                document.getElementById('page-num').textContent = `Página ${currentPage}`;

                document.getElementById('pagination').querySelector('button:first-child').disabled = !data.result.prev_page_url;
                document.getElementById('pagination').querySelector('button:last-child').disabled = !data.result.next_page_url;


            },
            error:function (xhr) {
                $('#list-invoices').append('<tr><td style="text-align:center;" colspan="99">Fatura não encontrada!</tr>');
            }
            });

    }

    filterButton.addEventListener('click', () => {
        currentPage = 1;
        loadInvoices(currentPage);
    });

    // filterOperatorInput.addEventListener('change', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });

    // filterValueInput.addEventListener('input', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });

    // sortSelect.addEventListener('change', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });

    // directionSelect.addEventListener('change', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });


    loadInvoices();


</script>



@endsection


@endsection
