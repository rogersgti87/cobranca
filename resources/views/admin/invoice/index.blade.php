@extends('layouts.admin')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Contas a receber</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Contas a receber</li>
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

        <div class="col-md-12 mb-4">
         <div class="row">
              <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Total</p>
                            <h5 id="total_invoices_curerency" style="color: #1F2937; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_invoices" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(59,130,246,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-receipt" style="color: #3B82F6; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Pendentes</p>
                            <h5 id="pendent_invoices_curerency" style="color: #FFBD59; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_pendent" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-hourglass" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Pagas</p>
                            <h5 id="pay_invoices_curerency" style="color: #22C55E; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_pay" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(34,197,94,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="color: #22C55E; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Canceladas</p>
                            <h5 id="cancelled_invoices_curerency" style="color: #F87171; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_cancelled" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(248,113,113,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-times-circle" style="color: #F87171; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        </div>

        <!-- Layout com Sidebar e Conteúdo Principal -->
        <div class="col-md-12">
            <div class="row">
                <!-- Sidebar de Filtros -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: sticky; top: 20px;">
                        <h5 style="color: #1F2937; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa fa-filter"></i> Filtros
                        </h5>

                        <!-- Filtros Rápidos -->
                        <div class="mb-4">
                            <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 10px; display: block;">Filtros Rápidos</label>
                            <div class="d-flex flex-column" style="gap: 8px;">
                                <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-current-month" data-filter="current-month" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                    <i class="fa fa-calendar"></i> Mês Atual
                                </button>
                                <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-next-month" data-filter="next-month" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                    <i class="fa fa-calendar-alt"></i> Próximo Mês
                                </button>
                                <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-all" data-filter="all" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                    <i class="fa fa-list"></i> Todos
                                </button>
                            </div>
                        </div>

                        <hr style="border-color: rgba(0,0,0,0.1); margin: 20px 0;">

                        <!-- Filtros Detalhados -->
                        <div class="form-group">
                            <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Tipo de Data</label>
                            <select class="form-control" id="filter-type" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;">
                                <option value="date_due">Data do Vencimento</option>
                                <option value="date_invoice">Data da Fatura</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Status</label>
                            <select class="form-control" id="filter-status" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;">
                                <option value="">Todos</option>
                                <option value="Pendente">Pendente</option>
                                <option value="Estabelecimento">Estabelecimento</option>
                                <option value="Pago">Pago</option>
                                <option value="Cancelado">Cancelado</option>
                                <option value="Erro">Erro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Data inicial</label>
                            <input type="date" autocomplete="off" class="form-control" placeholder="Data inicial" id="filter-date-ini" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;" value="{{date('Y-m-d',strtotime('first day of this month'))}}">
                        </div>

                        <div class="form-group">
                            <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Data final</label>
                            <input type="date" autocomplete="off" class="form-control" placeholder="Data Final" id="filter-date-end" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;" value="{{date('Y-m-d',strtotime('last day of this month'))}}">
                        </div>
                    </div>
                </div>

                <!-- Conteúdo Principal -->
                <div class="col-lg-9 col-md-8">
                    <!-- Gráfico -->
                    <div class="mb-4">
                        <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <h5 style="color: #1F2937; font-weight: 600; margin-bottom: 15px; font-size: 16px;">
                                <i class="fas fa-chart-pie"></i> Faturas por Status
                            </h5>
                            <div style="position: relative; height: 200px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Faturas -->
                    <div class="card-box">
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="col-12">
                                <div id="pagination" class="d-flex justify-content-center align-items-center mb-3 flex-wrap" style="gap: 5px;">
                                    <!-- Paginação será gerada dinamicamente aqui -->
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive fixed-solution">
                            <table class="table table-hover table-striped table-sm">
                                <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th> #</th>
                                    <th> Cliente</th>
                                    <th> Data</th>
                                    <th> Vencimento</th>
                                    <th> Valor</th>
                                    <th> Status</th>
                                    <th style="width: 60px; text-align: center;">Ações</th>
                                </tr>
                                </thead>

                                <tbody class="tbodyCustom" id="list-invoices">

                                </tbody>
                            </table>
                        </div>
                    </div>
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

   <!-- Modal :: Log -->
   <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modalErrorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalErrorLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <style>

                pre {
                   background-color: ghostwhite;
                   border: 1px solid silver;
                   padding: 10px 20px;
                   margin: 20px;
                   }
                .json-key {
                   color: brown;
                   }
                .json-value {
                   color: navy;
                   }
                .json-string {
                   color: olive;
                   }

                </style>

            <div class="modal-body" id="modal-content-error">

                <!-- conteudo -->
                <!-- conteudo -->
            </div><!-- modal-body -->
        </div>
    </div>
 </div>
 <!-- Modal :: Log -->


@section('styles')
<style>
    /* Estilos para tabela responsiva e collapse */
    .table-responsive {
        overflow-x: auto;
    }

    /* Estilos para o sidebar */
    @media (min-width: 992px) {
        .sidebar-filters {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }
    }

    @media (max-width: 991px) {
        .sidebar-filters {
            position: relative;
            top: 0;
        }
    }

    @media (max-width: 768px) {
        .table {
            font-size: 12px;
        }

        .table td, .table th {
            padding: 8px 4px;
        }

        .btn-xs {
            padding: 2px 5px;
            font-size: 10px;
        }

        .badge {
            font-size: 10px;
            padding: 3px 6px;
        }

        /* Sidebar em mobile ocupa toda a largura */
        .col-md-4 {
            margin-bottom: 20px;
        }
    }

    .accordion-toggle {
        transition: background-color 0.2s;
    }

    .accordion-toggle:hover {
        background-color: #1E293B !important;
    }

    .hiddenRow {
        padding: 0 !important;
    }

    .collapse {
        transition: all 0.3s ease;
    }

    .card-body {
        padding: 15px;
    }

    /* Garantir que o collapse funcione corretamente */
    .accordion-toggle[data-toggle="collapse"] {
        cursor: pointer;
    }

    .accordion-toggle i {
        transition: transform 0.3s ease;
    }

    /* Evitar que botões acionem o collapse */
    .table td:last-child {
        position: relative;
        z-index: 10;
    }

    .table td:last-child a,
    .table td:last-child button {
        position: relative;
        z-index: 11;
    }

    /* Estilo para os botões de filtro rápido no sidebar */
    .filter-quick-btn:hover {
        background-color: #FFBD59 !important;
        color: #1F2937 !important;
        border-color: #FFBD59 !important;
    }

    /* Estilos para dropdown de ações */
    .dropdown-menu {
        z-index: 1050;
    }

    .dropdown-item {
        border: none !important;
    }

    .dropdown-item:hover {
        background-color: #F5F5DC !important;
    }

    .dropdown-toggle::after {
        display: none;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let statusChart = null;

    // Open Modal - Error
    $(document).on("click", "#btn-modal-error", function(e) {
          e.preventDefault();
          e.stopPropagation();
          var id = $(this).data('invoice');
          var url = "{{url('admin/invoice-error')}}"+'/'+id;
          $("#modal-error").modal('show');
          $.get(url,
              $(this)
              .addClass('modal-scrollfix')
              .find('#modal-content-error')
              .html('Carregando...'),
              function(data) {
                  var json = JSON.stringify(JSON.parse(data.msg_erro), null, 2);
                  $("#modal-content-error").html('<pre>'+json+'</pre>');
              });
      });

  </script>


<script>
    function sendNotification(invoice_id){

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

    const form = document.createElement('form');
    form.appendChild(whatsappContainer);
    form.appendChild(emailContainer);

        Swal.fire({
            title: 'Selecione as opções:',
            html: form,
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
                const selectedOptions = result.value;

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
   $(document).on("click", "#btn-modal-invoice", function(e) {
        e.preventDefault();
        e.stopPropagation();
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
        $(document).on("click", "#btn-modal-notifications", function(e) {
            e.preventDefault();
            e.stopPropagation();

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
                    $('#btn-notificate').attr('onclick',`sendNotification(${invoice})`);
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

          var formData = new FormData($('#form-request-invoice')[0]);
          var invoice = $('#invoice').val();
          if(invoice != ''){
             var url = "{{ url('admin/invoices') }}"+'/'+invoice;
          }else{
             var url = "{{ url('admin/invoices') }}";

          }

          $.ajax({
             url: url,
             data: formData,
             method: 'POST',
             processData: false,
             contentType: false,
             beforeSend:function(){
                $("#btn-save-invoice").attr("disabled", true);
                $("#btn-save-invoice").text('Aguarde...');
             },
             success:function(data){
                $("#btn-save-invoice").attr("disabled", false);
                $("#btn-save-invoice").html('<i class="fa fa-check"></i> Salvar');
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
    const paginationContainer   = document.getElementById('pagination');

    let loadInvoicesTimeout;
    let currentPage = 1;

    // Função para gerar paginação numérica com grupos de 10 páginas
    function updatePagination(currentPage, totalPages) {
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';

        if (totalPages <= 1) {
            return;
        }

        // Calcular o grupo de páginas atual (grupos de 10)
        const currentGroup = Math.floor((currentPage - 1) / 10);
        const startPage = currentGroup * 10 + 1;
        const endPage = Math.min(startPage + 9, totalPages);

        // Botão Anterior (para o grupo anterior)
        if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'btn btn-sm btn-secondary';
            prevBtn.textContent = '« Anterior';
            prevBtn.onclick = () => loadInvoices(currentPage - 1);
            paginationContainer.appendChild(prevBtn);
        }

        // Botão para ir ao primeiro grupo (se não estiver no primeiro grupo)
        if (currentGroup > 0) {
            const firstBtn = document.createElement('button');
            firstBtn.className = 'btn btn-sm btn-secondary';
            firstBtn.textContent = '1';
            firstBtn.onclick = () => loadInvoices(1);
            paginationContainer.appendChild(firstBtn);

            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2';
                ellipsis.textContent = '...';
                paginationContainer.appendChild(ellipsis);
            }
        }

        // Botões numéricos do grupo atual
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = 'btn btn-sm ' + (i === currentPage ? 'btn-primary' : 'btn-secondary');
            pageBtn.textContent = i;
            pageBtn.onclick = () => loadInvoices(i);
            paginationContainer.appendChild(pageBtn);
        }

        // Botão para ir ao último grupo (se não estiver no último grupo)
        if (currentGroup < Math.floor((totalPages - 1) / 10)) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2';
                ellipsis.textContent = '...';
                paginationContainer.appendChild(ellipsis);
            }

            const lastBtn = document.createElement('button');
            lastBtn.className = 'btn btn-sm btn-secondary';
            lastBtn.textContent = totalPages;
            lastBtn.onclick = () => loadInvoices(totalPages);
            paginationContainer.appendChild(lastBtn);
        }

        // Botão Próxima (para a próxima página)
        if (currentPage < totalPages) {
            const nextBtn = document.createElement('button');
            nextBtn.className = 'btn btn-sm btn-secondary';
            nextBtn.textContent = 'Próxima »';
            nextBtn.onclick = () => loadInvoices(currentPage + 1);
            paginationContainer.appendChild(nextBtn);
        }

        // Informação de páginas
        const info = document.createElement('span');
        info.className = 'ml-3';
        info.style.color = '#1F2937';
        info.textContent = `Página ${currentPage} de ${totalPages}`;
        paginationContainer.appendChild(info);
    }

    function loadInvoices(page = 1) {
        currentPage = page;
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
            // Atualizar gráfico de status
            if(data.status) {
                updateStatusChart(data.status);
            }

            if(data.result.data.length == 0){
                $('#total_invoices').text('0 faturas');
                $('#total_invoices_curerency').text(parseFloat(0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                $('#total_pendent').text('0 faturas');
                $('#pendent_invoices_curerency').text(parseFloat(0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                $('#total_pay').text('0 faturas');
                $('#pay_invoices_curerency').text(parseFloat(0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                $('#total_cancelled').text('0 faturas');
                $('#cancelled_invoices_curerency').text(parseFloat(0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
            }else{
                $('#total_invoices').text(data.result.data[0].qtd_invoices + ' faturas');
                $('#total_invoices_curerency').text(parseFloat(data.result.data[0].total_currency || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                $('#total_pendent').text(data.result.data[0].qtd_pendente + ' faturas');
                $('#pendent_invoices_curerency').text(parseFloat(data.result.data[0].pendente_currency || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                $('#total_pay').text(data.result.data[0].qtd_pago + ' faturas');
                $('#pay_invoices_curerency').text(parseFloat(data.result.data[0].pago_currency || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
                $('#total_cancelled').text(data.result.data[0].qtd_cancelado + ' faturas');
                $('#cancelled_invoices_curerency').text(parseFloat(data.result.data[0].cancelado_currency || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
            }
        $('#list-invoices').html('');


        var html = '';
            if(data.result.data.length > 0){
                $.each(data.result.data, function(i, item) {
                var statusBadgeClass = item.status == 'Pago' ? 'badge-success' :
                                       item.status == 'Pendente' ? 'badge-warning' :
                                       item.status == 'Estabelecimento' ? 'badge-info' :
                                       item.status == 'Erro' ? 'badge-danger' :
                                       item.status == 'Cancelado' ? 'badge-danger' :
                                       'badge-secondary';

                var datePayment = item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-';
                var paymentMethod = item.gateway_payment ? item.gateway_payment + ' (' + item.payment_method + ')' : item.payment_method || '-';

                html += '<tr class="accordion-toggle">';
                html += '<td><button class="btn btn-sm btn-link" style="color: #FFBD59; padding: 0; border: none; background: transparent;" onclick="event.stopPropagation(); $(\'#collapse-invoice-' + item.id + '\').collapse(\'toggle\');"><i class="fas fa-chevron-down" id="icon-' + item.id + '"></i></button></td>';
                html += '<td onclick="$(\'#collapse-invoice-' + item.id + '\').collapse(\'toggle\');" style="cursor: pointer;">' + item.id + '</td>';
                html += '<td onclick="$(\'#collapse-invoice-' + item.id + '\').collapse(\'toggle\');" style="cursor: pointer;">' + item.customer_name + '</td>';
                html += '<td onclick="$(\'#collapse-invoice-' + item.id + '\').collapse(\'toggle\');" style="cursor: pointer;">' + moment(item.date_invoice).format('DD/MM/YYYY') + '</td>';
                html += '<td onclick="$(\'#collapse-invoice-' + item.id + '\').collapse(\'toggle\');" style="cursor: pointer;">' + moment(item.date_due).format('DD/MM/YYYY') + '</td>';
                html += '<td onclick="$(\'#collapse-invoice-' + item.id + '\').collapse(\'toggle\');" style="cursor: pointer; color: #FFBD59; font-weight: 600;">' + item.price + '</td>';
                html += '<td onclick="$(\'#collapse-invoice-' + item.id + '\').collapse(\'toggle\');" style="cursor: pointer;"><span class="badge ' + statusBadgeClass + '">' + item.status + '</span></td>';

                // Criar itens do dropdown de ações
                var actionsMenuItems = [];

                // Editar Cliente (sempre disponível)
                actionsMenuItems.push('<a href="{{ url('admin/customers/form?act=edit&id=')}}' + item.customer_id + '" class="dropdown-item" data-original-title="Editar cliente" id="btn-edit-customer" data-placement="left" data-tt="tooltip" style="color: #1F2937; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#F5F5DC\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-user" style="margin-right: 8px; color: #1E293B;"></i> Editar Cliente</a>');

                // Editar Fatura (apenas para Pendente, Erro ou Estabelecimento)
                if(item.status == 'Pendente' || item.status == 'Erro' || item.status == 'Estabelecimento'){
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Editar fatura" id="btn-modal-invoice" data-type="edit-invoice" data-invoice="' + item.id + '" data-placement="left" data-tt="tooltip" style="color: #1F2937; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#F5F5DC\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="far fa-edit" style="margin-right: 8px; color: #1E293B;"></i> Editar Fatura</a>');
                }

                // Ver Erros (apenas para Erro)
                if(item.status == 'Erro'){
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Erros" id="btn-modal-error" data-invoice="' + item.id + '" data-placement="left" data-tt="tooltip" style="color: #1F2937; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#F5F5DC\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-exclamation-triangle" style="margin-right: 8px; color: #DC2626;"></i> Ver Erros</a>');
                }

                // Notificações (para todos exceto Erro)
                if(item.status != 'Erro'){
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Notificações" id="btn-modal-notifications" data-invoice="' + item.id + '" data-placement="left" data-tt="tooltip" style="color: #1F2937; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#F5F5DC\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fa fa-info" style="margin-right: 8px; color: #FFBD59;"></i> Notificações</a>');
                }

                // Cancelar Fatura (apenas para Pendente, Erro ou Estabelecimento)
                if(item.status == 'Pendente' || item.status == 'Erro' || item.status == 'Estabelecimento'){
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Cancelar Fatura" id="btn-delete-invoice" data-placement="left" data-invoice="' + item.id + '" data-tt="tooltip" style="color: #1F2937; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#F5F5DC\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-undo-alt" style="margin-right: 8px; color: #F87171;"></i> Cancelar Fatura</a>');
                }

                // Baixar Fatura (apenas para Pendente)
                if(item.status == 'Pendente'){
                    var downloadUrl = item.payment_method == "Pix" ? item.image_url_pix : item.billet_url;
                    actionsMenuItems.push('<a href="' + downloadUrl + '" target="_blank" class="dropdown-item" data-original-title="Baixar Fatura" id="btn-download-invoice" data-placement="left" data-tt="tooltip" style="color: #1F2937; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#F5F5DC\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-download" style="margin-right: 8px; color: #FFBD59;"></i> Baixar Fatura</a>');
                }

                // Criar dropdown de ações
                var actionsMenuId = 'actions-menu-'+item.id;
                html += '<td style="text-align: center;">';
                if(actionsMenuItems.length > 0) {
                    html += '<div class="dropdown" style="position: relative; display: inline-block;">';
                    html += '<button class="btn btn-sm" type="button" id="'+actionsMenuId+'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: transparent; border: none; color: #1F2937; padding: 4px 8px; cursor: pointer; font-size: 14px;">';
                    html += '<i class="fa fa-ellipsis-v"></i>';
                    html += '</button>';
                    html += '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="'+actionsMenuId+'" style="min-width: 180px; padding: 5px 0; background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">';
                    html += actionsMenuItems.join('');
                    html += '</div>';
                    html += '</div>';
                }
                html += '</td>';
                html += '</tr>';

                // Linha de collapse com informações adicionais
                html += '<tr>';
                html += '<td colspan="8" class="hiddenRow">';
                html += '<div class="collapse" id="collapse-invoice-' + item.id + '">';
                html += '<div class="card card-body" style="background-color: #1E293B; border: 1px solid rgba(255,255,255,0.1); margin: 10px 0;">';
                html += '<div class="row">';
                html += '<div class="col-md-6 col-sm-12 mb-3">';
                html += '<strong style="color: #FFBD59;">Descrição:</strong><br>';
                html += '<span style="color: #000000;">' + (item.description || '-') + '</span>';
                html += '</div>';
                html += '<div class="col-md-3 col-sm-6 mb-3">';
                html += '<strong style="color: #FFBD59;">Pago em:</strong><br>';
                html += '<span style="color: #000000;">' + datePayment + '</span>';
                html += '</div>';
                html += '<div class="col-md-3 col-sm-6 mb-3">';
                html += '<strong style="color: #FFBD59;">Forma de Pagamento:</strong><br>';
                html += '<span style="color: #000000;">' + paymentMethod + '</span>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            });
            }else{
                html += '<tr><td style="text-align:center;" colspan="99">Fatura não encontrada!</tr>';
            }

            $('#list-invoices').append(html);
            $('[data-tt="tooltip"]').tooltip();

            // Rotacionar ícone do collapse quando expandir/colapsar
            $(document).off('show.bs.collapse hide.bs.collapse', '.collapse').on('show.bs.collapse', '.collapse', function() {
                var invoiceId = $(this).attr('id').replace('collapse-invoice-', '');
                $('#icon-' + invoiceId).removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }).on('hide.bs.collapse', '.collapse', function() {
                var invoiceId = $(this).attr('id').replace('collapse-invoice-', '');
                $('#icon-' + invoiceId).removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });

                currentPage = data.result.current_page;
                const totalPages = data.result.last_page;

                // Gerar paginação numérica
                updatePagination(currentPage, totalPages);


            },
            error:function (xhr) {
                $('#list-invoices').append('<tr><td style="text-align:center;" colspan="99">Fatura não encontrada!</tr>');
            }
            });

    }

    // Função para atualizar o destaque dos filtros rápidos
    function updateQuickFilterHighlight() {
        // Remover destaque de todos os botões
        document.querySelectorAll('.filter-quick-btn').forEach(function(btn) {
            btn.style.border = '1px solid rgba(0,0,0,0.1)';
            btn.style.borderWidth = '1px';
        });

        const dateIni = filterInputDateIni.value;
        const dateEnd = filterInputDateEnd.value;

        if (!dateIni || !dateEnd) {
            // Se não há datas, destacar "Todos"
            document.getElementById('btn-filter-all').style.border = '2px solid #FFBD59';
            document.getElementById('btn-filter-all').style.borderWidth = '2px';
            return;
        }

        const now = new Date();
        const currentMonthFirst = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
        const currentMonthLast = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
        const nextMonthFirst = new Date(now.getFullYear(), now.getMonth() + 1, 1).toISOString().split('T')[0];
        const nextMonthLast = new Date(now.getFullYear(), now.getMonth() + 2, 0).toISOString().split('T')[0];

        if (dateIni === currentMonthFirst && dateEnd === currentMonthLast) {
            document.getElementById('btn-filter-current-month').style.border = '2px solid #FFBD59';
            document.getElementById('btn-filter-current-month').style.borderWidth = '2px';
        } else if (dateIni === nextMonthFirst && dateEnd === nextMonthLast) {
            document.getElementById('btn-filter-next-month').style.border = '2px solid #FFBD59';
            document.getElementById('btn-filter-next-month').style.borderWidth = '2px';
        }
    }

    // Botão Mês Atual
    document.getElementById('btn-filter-current-month').addEventListener('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        filterInputDateIni.value = firstDay.toISOString().split('T')[0];
        filterInputDateEnd.value = lastDay.toISOString().split('T')[0];

        updateQuickFilterHighlight();
        currentPage = 1;
        loadInvoices(currentPage);
    });

    // Botão Próximo Mês
    document.getElementById('btn-filter-next-month').addEventListener('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 2, 0);

        filterInputDateIni.value = firstDay.toISOString().split('T')[0];
        filterInputDateEnd.value = lastDay.toISOString().split('T')[0];

        updateQuickFilterHighlight();
        currentPage = 1;
        loadInvoices(currentPage);
    });

    // Botão Todos
    document.getElementById('btn-filter-all').addEventListener('click', function() {
        filterInputDateIni.value = '';
        filterInputDateEnd.value = '';
        filterInputStatus.value = '';

        updateQuickFilterHighlight();
        currentPage = 1;
        loadInvoices(currentPage);
    });

    // Atualizar destaque ao carregar a página (já que definimos mês atual por padrão)
    updateQuickFilterHighlight();

    // Função para carregar automaticamente com debounce
    function autoLoadInvoices() {
        clearTimeout(loadInvoicesTimeout);
        loadInvoicesTimeout = setTimeout(function() {
            currentPage = 1;
            loadInvoices(currentPage);
        }, 500);
    }

    // Eventos de mudança automática nos filtros
    filterInputType.addEventListener('change', function() {
        autoLoadInvoices();
    });

    filterInputStatus.addEventListener('change', function() {
        autoLoadInvoices();
    });

    filterInputDateIni.addEventListener('change', function() {
        updateQuickFilterHighlight();
        autoLoadInvoices();
    });

    filterInputDateEnd.addEventListener('change', function() {
        updateQuickFilterHighlight();
        autoLoadInvoices();
    });

    loadInvoices();

function updateStatusChart(statusData) {
    const ctx = document.getElementById('statusChart');

    if (!ctx) return;

    // Destruir gráfico anterior se existir
    if (statusChart) {
        statusChart.destroy();
    }

    if (!statusData || statusData.length === 0) {
        const canvas = ctx.getContext('2d');
        canvas.clearRect(0, 0, ctx.width, ctx.height);
        return;
    }

    // Mapear cores para cada status
    const statusColors = {
        'Pendente': '#FFBD59',
        'Pago': '#22C55E',
        'Cancelado': '#F87171',
        'Erro': '#DC2626',
        'Estabelecimento': '#6366F1',
        'Processamento': '#6B7280'
    };

    // Preparar dados
    const labels = statusData.map(item => item.status);
    const data = statusData.map(item => parseFloat(item.total));
    const colors = statusData.map(item => statusColors[item.status] || '#CCCCCC');
    const borderColors = colors.map(color => color);

    // Criar gráfico
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total por Status',
                data: data,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            family: 'Source Sans Pro'
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const color = data.datasets[0].backgroundColor[i];
                                    const formattedValue = parseFloat(value).toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    return {
                                        text: label + ' - R$ ' + formattedValue,
                                        fillStyle: color,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(2);
                            const formattedValue = parseFloat(value).toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return label + ': R$ ' + formattedValue + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
</script>



@endsection


@endsection
