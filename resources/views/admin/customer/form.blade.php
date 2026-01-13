@extends('layouts.admin')

@section('content')


<style>
    /* Estilos personalizados para a caixa de diálogo */
    .custom-swal {
        font-family: Arial, sans-serif;
        color: #333;
        font-size: 12px;
    }

    /* Estilos personalizados para o título da caixa de diálogo */
    .custom-swal .swal2-title {
        font-size: 24px;
        color: #007bff;
    }

    /* Estilos personalizados para o texto da caixa de diálogo */
    .custom-swal .swal2-content {
        font-size: 14px;
    }
 /* Estilos personalizados para os checkboxes */
 .checkbox-container {
            display: inline-flex; /* Alinhar os elementos em linha horizontal */
            align-items: center;
            margin-right: 20px; /* Espaçamento entre os checkboxes */
        }

        /* Aumentar o tamanho do checkbox */
        .checkbox-container input[type="checkbox"] {
            width: 20px; /* Largura do checkbox */
            height: 20px; /* Altura do checkbox */
        }

        .checkbox-container label {
            margin: 0; /* Remova margens para ajustar melhor o texto */
            display: flex;
            align-items: center;
        }
</style>

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
              <li class="breadcrumb-item"><a href="{{url($link)}}">{{ $title }}</a></li>
              <li class="breadcrumb-item active">{{Request::get('act') == 'add' ? $breadcrumb_new : $breadcrumb_edit}}</li>
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

    <div class="row">
        <div class="col-md-12">

            <div class="col-12 col-sm-12">
                <div class="card card-primary card-outline card-outline-tabs">
                  <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Dados do Cliente</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {{ !isset($data) ? 'disabled' : '' }}" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Serviços</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {{ !isset($data) ? 'disabled' : '' }}" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Faturas</a>
                      </li>
                    </ul>
                  </div>
                  <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                      <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">


                        <form class="form">

                            <div class="col-md-12">
                            <div class="form-row">

                                <a href="#" data-original-title="Salvar" data-toggle="tooltip" class="btn btn-secondary" id="btn-salvar"><i class="fa fa-save fa-1x"></i> Gravar</a>

                                        <div class="col-md-12">
                                            <br>
                                            <div class="form-row">

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Tipo</label>
                                                    <select class="form-control custom-select" name="type" id="type">
                                                        <option {{ isset($data->type) && $data->type === 'Física' ? 'selected' : '' }} value="Física">Física</option>
                                                        <option {{ isset($data->type) && $data->type === 'Jurídica' ? 'selected' : '' }} value="Jurídica">Jurídica</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>CPF/CNPJ</label>
                                                    <input type="text" class="form-control" name="document" id="" autocomplete="off" required value="{{isset($data->document) ? $data->document : ''}}">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Nome</label>
                                                    <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                                                </div>


                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Empresa</label>
                                                    <input type="text" class="form-control" name="company" id="company" autocomplete="off" required value="{{isset($data->company) ? $data->company : ''}}">
                                                </div>

                                                <div class="form-group col-md-5 col-sm-12">
                                                    <label>E-mail</label>
                                                    <input type="email" class="form-control" name="email" id="email" autocomplete="off" required value="{{isset($data->email) ? $data->email : ''}}">
                                                </div>

                                                <div class="form-group col-md-5 col-sm-12">
                                                    <label>E-mail 2</label>
                                                    <input type="email" class="form-control" name="email2" id="email2" autocomplete="off" required value="{{isset($data->email2) ? $data->email2 : ''}}">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Cep</label>
                                                    <input type="text" class="form-control" name="cep" id="cep" autocomplete="off" required value="{{isset($data->cep) ? $data->cep : ''}}">
                                                </div>

                                                <div class="form-group col-md-10 col-sm-12">
                                                    <label>Endereço</label>
                                                    <input type="text" class="form-control" name="address" id="address" autocomplete="off" required value="{{isset($data->address) ? $data->address : ''}}">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Número</label>
                                                    <input type="text" class="form-control" name="number" id="number" autocomplete="off" required value="{{isset($data->number) ? $data->number : ''}}">
                                                </div>

                                                <div class="form-group col-md-3 col-sm-12">
                                                    <label>Bairro</label>
                                                    <input type="text" class="form-control" name="district" id="district" autocomplete="off" required value="{{isset($data->district) ? $data->district : ''}}">
                                                </div>

                                                <div class="form-group col-md-3 col-sm-12">
                                                    <label>Cidade</label>
                                                    <input type="text" class="form-control" name="city" id="city" autocomplete="off" required value="{{isset($data->city) ? $data->city : ''}}">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Estado</label>
                                                    <input type="text" class="form-control" name="state" id="state" autocomplete="off" value="{{isset($data->state) ? $data->state : ''}}">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Complemento</label>
                                                    <input type="text" class="form-control" name="complement" id="complement" autocomplete="off" required value="{{isset($data->complement) ? $data->complement : ''}}">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Nascimento</label>
                                                    <input type="date" max="{{date('Y-m-d')}}" class="form-control" name="birthdate" id="birthdate" autocomplete="off" required value="{{isset($data->birthdate) ? $data->birthdate : ''}}">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Telefone</label>
                                                    <input type="text" class="form-control" name="phone" id="telephone" autocomplete="off" required value="{{isset($data->phone) ? $data->phone : ''}}">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Whatsapp</label>
                                                    <input type="text" class="form-control" name="whatsapp" id="whatsapp" autocomplete="off" required value="{{isset($data->whatsapp) ? $data->whatsapp : ''}}">
                                                </div>


                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Notificar Whatsapp?</label>
                                                    <select class="form-control custom-select" name="notification_whatsapp" id="notification_whatsapp">
                                                        <option {{ isset($data->notification_whatsapp) && $data->notification_whatsapp === 's' ? 'selected' : '' }} value="s">Sim</option>
                                                        <option {{ isset($data->notification_whatsapp) && $data->notification_whatsapp === 'n' ? 'selected' : '' }} value="n">Não</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Notificar Email?</label>
                                                    <select class="form-control custom-select" name="notification_email" id="notification_email">
                                                        <option {{ isset($data->notification_email) && $data->notification_email === 's' ? 'selected' : '' }} value="s">Sim</option>
                                                        <option {{ isset($data->notification_email) && $data->notification_email === 'n' ? 'selected' : '' }} value="n">Não</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Notificar 5 dias antes?</label>
                                                    <select class="form-control custom-select" name="notificate_5_days" id="notificate_5_days">
                                                        <option {{ isset($data->notificate_5_days) && $data->notificate_5_days === 's' ? 'selected' : '' }} value="s">Sim</option>
                                                        <option {{ isset($data->notificate_5_days) && $data->notificate_5_days === 'n' ? 'selected' : '' }} value="n">Não</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Notificar 2 dias antes?</label>
                                                    <select class="form-control custom-select" name="notificate_2_days" id="notificate_2_days">
                                                        <option {{ isset($data->notificate_2_days) && $data->notificate_2_days === 's' ? 'selected' : '' }} value="s">Sim</option>
                                                        <option {{ isset($data->notificate_2_days) && $data->notificate_2_days === 'n' ? 'selected' : '' }} value="n">Não</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Notificar no vencimento?</label>
                                                    <select class="form-control custom-select" name="notificate_due" id="notificate_due">
                                                        <option {{ isset($data->notificate_due) && $data->notificate_due === 's' ? 'selected' : '' }} value="s">Sim</option>
                                                        <option {{ isset($data->notificate_due) && $data->notificate_due === 'n' ? 'selected' : '' }} value="n">Não</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Status</label>
                                                    <select class="form-control custom-select" name="status" id="status">
                                                        <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                                                        <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-12 col-sm-12">
                                                    <label>Observação</label>
                                                    <textarea class="form-control" name="obs" id="obs" rows="6" autocomplete="off" required>{{isset($data->obs) ? $data->obs : ''}}</textarea>
                                                </div>


                                                    <a href="#" data-original-title="Salvar" data-toggle="tooltip" class="btn btn-secondary" id="btn-salvar"><i class="fa fa-save fa-1x"></i> Gravar</a>


                                            </div>




                                    </div>



                            </div>

                            </div>

                        </form>


                      </div>
                      <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">

                        <div class="col-md-12">
                            <div class="card-box">
                                <a href="#" data-original-title="Adicionar Serviço" data-toggle="tooltip" class="btn btn-secondary" id="btn-modal-customer-service" data-type="add-customer-service"><i class="fa fa-save fa-1x"></i> Adcionar serviço</a>
                                <br>
                                <br>
                                <div class="table-responsive fixed-solution">
                                    <table class="table table-hover table-striped table-sm">
                                        <thead class="thead-light">
                                        <tr>
                                            <th> Descrição</th>
                                            <th> Preço</th>
                                            <th> Vencimento</th>
                                            <th> Período</th>
                                            <th> Status</th>
                                            <th style="width: 150px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody class="tbodyCustom" id="load-customer-services">


                                        </tbody>
                                    </table>
                                </div>

                                <br>
                                <br>
                                <a href="#" data-original-title="Adicionar Serviço" data-toggle="tooltip" class="btn btn-secondary" id="btn-modal-customer-service"><i class="fa fa-save fa-1x"></i> Adcionar serviço</a>

                            </div>

                        </div>
                        <!-- FIM TABLE -->

                      </div>
                      <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">
                        <div class="col-md-12">
                            <div class="card-box">
                                {{-- <a href="#" data-original-title="Adicionar Fatura" data-toggle="tooltip" class="btn btn-secondary" id="btn-modal-invoice" data-type="add-invoice"><i class="fa fa-save fa-1x"></i> Adcionar Fatura</a> --}}
                                <br>
                                <br>
                                <div class="table-responsive fixed-solution">
                                    <table class="table table-hover table-striped table-sm">
                                        <thead class="thead-light">
                                        <tr>
                                            <th> #</th>
                                            <th> Descrição</th>
                                            <th> Preço</th>
                                            <th> Gateway de Pagamento</th>
                                            <th> Forma de Pagamento</th>
                                            <th> Data</th>
                                            <th> Vencimento</th>
                                            <th> Pago em</th>
                                            <th> Status</th>
                                            <th style="width: 150px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody class="tbodyCustom" id="load-invoices">


                                        </tbody>
                                    </table>
                                </div>

                                <br>
                                <br>
                                {{-- <a href="#" data-original-title="Adicionar Fatura" data-toggle="tooltip" class="btn btn-secondary" id="btn-modal-invoice" data-type="add-invoice"><i class="fa fa-save fa-1x"></i> Adcionar Fatura</a> --}}

                            </div>

                        </div>
                        <!-- FIM TABLE -->
                      </div>
                    </div>
                  </div>
                  <!-- /.card -->
                </div>
              </div>


        </div>
    </div>


</div>
</div>
</div>
  </div>
 </div>



   <!-- Modal :: Form MyService -->
   <div class="modal fade" id="modalCustomerService" tabindex="-1" role="dialog" aria-labelledby="modalCustomerServiceLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <form action="" class="form-horizontal" id="form-request-customer-service">
               <div class="modal-header">
                   <h5 class="modal-title" id="modalCustomerServiceLabel"></h5>
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
               </div>
               <div class="modal-body" id="form-content-customer-service">
                   <!-- conteudo -->
                   <!-- conteudo -->
               </div><!-- modal-body -->
               <div class="modal-footer">
                   <button type="button" class="btn btn-success" id="btn-save-customer-service"><i class="fa fa-check"></i> Salvar</button>
                   <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
               </div>
           </form>
       </div>
   </div>
</div>
<!-- Modal :: Form MyService -->


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
                   background-color: #FFFFFF;
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


@section('scripts')


    <script src="{{url('/vendor/laravel-filemanager/js/stand-alone-button-normal.js')}}"></script>


    <script>

        // Open Modal - Error
        $(document).on("click", "#btn-modal-error", function() {
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
                        var result_whatsapp = `<p class="text-primary"><b>Whatsapp:</b></p> <p><b>Mensagem:</b> ${data.whatsapp.mensagem}</p><p><b>PIX:</b> ${data.whatsapp.pix}</p><p><b>Boleto:</b> ${data.whatsapp.boleto}</p>`;
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


$(window).on("load", function(){

    @if(isset($data))
        loadCustomerServices();
        loadInvoices();
    @endif





});

    $('#lfm').filemanager('image');

    //Save data
        $(document).on('click', '#btn-salvar', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            var url_act = "{{Request::get('act')}}";
            if(url_act == 'edit'){
                var url = "{{ url($linkUpdate) }}";
                var method = 'PUT';
            }else{
                var url = "{{ url("$linkStore") }}";
                var method = 'POST';
            }
            var data = $('.form').serialize();
            $.ajax({
                url: url,
                data:data,
                method:method,
                success:function(data){
                    //console.log(data);
                    var message = url_act == 'add' ? data.data : data;
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>" + message + "</h5>",
                        icon: 'success',
                        showConfirmButton: false,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                        html:
                        '<a href="{{url($linkFormAdd)}}" data-original-title="Novo" data-toggle="tooltip" class="btn btn-secondary btn-md"> <i class="fa fa-plus"></i> Novo</a>  ' +
                        `<a href="${url_act == 'add' ? '{{url($linkFormEdit)}}'+data.id : '{{url($linkFormEdit)}}'}" class="btn btn-success btn-md"> <i class="fa fa-plus"></i> Editar</a>  ` +
                        '<a href="{{url($link)}}" data-original-title="Listar" data-toggle="tooltip" class="btn btn-primary btn-md"> <i class="fa fa-list"></i> Listar</a>',
                    });
                },
                error:function (xhr) {

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
    // Open Modal - Create - Services
    $(document).on("click", "#btn-modal-customer-service", function() {
        var type = $(this).data('type');
        var customer_id = "{{ isset($data) ? $data->id : ''}}";
        $("#modalCustomerService").modal('show');
        if(type == 'add-customer-service'){
            $("#modalCustomerServiceLabel").html('Adicionar Serviço');
            var url = `{{ url("/admin/customer-services/form?customer_id=") }}${customer_id}`;
        }else{
            $("#modalCustomerServiceLabel").html('Editar Serviço');
            var customer_service_id = $(this).data('customer-service-id');
            var url = `{{ url("/admin/customer-services/form?customer_id=") }}${customer_id}&id=${customer_service_id}`;
        }

        //console.log(url);
        $.get(url,
            $(this)
            .addClass('modal-scrollfix')
            .find('#form-content-customer-service')
            .html('Carregando...'),
            function(data) {
                // console.log(data);
                $("#form-content-customer-service").html(data);
                $('.money').mask('000.000.000.000.000,00', {reverse: true});
                $('[data-tt="tooltip"]').tooltip();
                // aqui quando selecionar um serviço, buscar qual o valor dele e atualizar o campo de preço.
                $('#service_id').on('change', function() {
                    var service_id = $(this).val();
                    var service_price = $(this).find(':selected').data('price');
                    $('#price').val(parseFloat(service_price).toLocaleString('pt-br', {minimumFractionDigits: 2}));
                });

            });
    });



    //Save customer service
     $(document).on('click', '#btn-save-customer-service', function(e) {
            e.preventDefault();

            $("#btn-save-customer-service").attr("disabled", true);
            $("#btn-save-customer-service").text('Aguarde...');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            var data = $('#form-request-customer-service').serialize();
            var customer_service_id = $('#customer_service_id').val();
            if(customer_service_id != ''){
                var url = "{{ url('admin/customer-services') }}"+'/'+customer_service_id;
                var method = 'PUT';
            }else{
                var url = "{{ url('admin/customer-services') }}";
                var method = 'POST';
            }

            $.ajax({
                url: url,
                data:data,
                method:method,
                success:function(data){
//console.log(data);
//return false;
                    $("#btn-save-customer-service").attr("disabled", false);
                    $("#btn-save-customer-service").html('<i class="fa fa-check"></i> Salvar');
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
                        $('#modalCustomerService').modal('hide');
                        loadCustomerServices();
                        loadInvoices();
                    });
                },
                error:function (xhr) {
                    $("#btn-save-customer-service").attr("disabled", false);
                    $("#btn-save-customer-service").html('<i class="fa fa-check"></i> Salvar');
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


function loadCustomerServices(){

    var customer_id = "{{ isset($data) ? $data->id : ''}}";

    $.ajax({
                url: "{{url('/admin/load-customer-services')}}"+'/'+customer_id,
                method: 'GET',
                success:function(data){
                    //console.log(data);
                    $('#load-customer-services').html('');
                    var html = '';
                    $.each(data, function(i, item) {
                        html += '<tr>';
                        html += `<td>${item.description}</td>`;
                        html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
                        html += `<td>${item.day_due}</td>`;
                        html += `<td>${item.period}</td>`;
                        html += `<td><label class="badge badge-${item.status == 'Ativo' ? 'success' : 'danger'}">${item.status}</label></td>`;
                        html += `<td>
                            <a href="#" data-original-title="Editar Serviço" id="btn-modal-customer-service" data-type="edit-customer-service" data-customer-service-id="${item.id}" data-toggle="tooltip" class="btn btn-primary btn-xs"> <i class="fa fa-list"></i> Editar</a>
                            <a href="#" data-original-title="Deletar Serviço" id="btn-delete-customer-service" data-customer-service-id="${item.id}" data-toggle="tooltip" class="btn btn-danger btn-xs"> <i class="fa fa-list"></i> Deletar</a>
                            </td>`;
                        html += '</tr>';

                    });
                    $('#load-customer-services').append(html);
                    $('[data-tt="tooltip"]').tooltip();

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


$(document).on('click', '#btn-delete-customer-service', function(e) {
    var customer_service_id = $(this).data('customer-service-id');

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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });

            $.ajax({
                url: "{{url('admin/customer-services')}}"+'/'+customer_service_id,
                method: 'DELETE',
                success:function(data){
                    loadCustomerServices();
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

</script>

{{-- //  Invoices // --}}

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
            beforeSend: function(){
                $("#btn-save-invoice").attr("disabled", true);
                $("#btn-save-invoice").text('Aguarde...');
            },
            success: function(data){
                $("#btn-save-invoice").attr("disabled", false);
                $("#btn-save-invoice").html('<i class="fa fa-check"></i> Salvar');
                Swal.fire({
                    width: 350,
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
            error: function(xhr) {
                $("#btn-save-invoice").attr("disabled", false);
                $("#btn-save-invoice").html('<i class="fa fa-check"></i> Salvar');
                if(xhr.status === 422){
                    Swal.fire({
                        text: xhr.responseJSON,
                        width: 300,
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
                        width: 300,
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


function loadInvoices(){

    var customer_id = "{{ isset($data) ? $data->id : ''}}";

    $.ajax({
                url: "{{url('/admin/load-invoices')}}"+'/'+customer_id,
                method: 'GET',
                success:function(data){
                    //console.log(data);
                    $('#load-invoices').html('');
                    var html = '';
                    $.each(data, function(i, item) {
                        html += '<tr>';
                        html += `<td>${item.id}</td>`;
                        html += `<td>${item.description}</td>`;
                        html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
                        html += `<td>${item.gateway_payment}</td>`;
                        html += `<td>${item.payment_method}</td>`;
                        html += `<td>${moment(item.date_invoice).format('DD/MM/YYYY')}</td>`;
                        html += `<td>${moment(item.date_due).format('DD/MM/YYYY')}</td>`;
                        html += `<td>${item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-' }</td>`;
                        html += `<td><label class="badge badge-${item.status == 'Pago' ? 'badge-success' :
    item.status == 'Pendente' ? 'warning' :
    item.status == 'Estabelecimento' ? 'info' :
    'danger'}">${item.status}</label></td>`;
                        html += `<td>
                            ${item.status == 'Pendente' || item.status == 'Erro' || item.status == 'Estabelecimento' ? '<a href="#" data-original-title="Editar fatura" id="btn-modal-invoice" data-type="edit-invoice" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-secondary btn-xs"> <i class="far fa-edit"></i></a>' : ''}
                            ${item.status == 'Erro' ? '<a href="#" data-original-title="Erros" id="btn-modal-error" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-danger btn-xs"> <i class="fas fa-exclamation-triangle"></i></a>' : ''}
                            ${item.status != 'Erro' ? '<a href="#" data-original-title="Notificações" id="btn-modal-notifications" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-info btn-xs" style="background-color: #06b8f7; border-color: #06b8f7; color: #FFFFFF;"> <i style="padding:0 5px;" class="fa fa-info"></i></a>' : ''}
                            ${item.status == 'Pendente' || item.status == 'Erro' || item.status == 'Estabelecimento' ? '<a href="#" data-original-title="Cancelar Fatura" id="btn-delete-invoice" data-placement="left" data-invoice="'+item.id+'" data-tt="tooltip" class="btn btn-danger btn-xs"> <i class="fas fa-undo-alt"></i></a>' : ''}
                            ${item.status == 'Pendente' ? '<a href="'+`${item.payment_method == "Pix" ? item.image_url_pix : item.billet_url}`+'" target="_blank" data-original-title="Baixar Fatura" id="btn-download-invoice" data-placement="left" data-tt="tooltip" class="btn btn-primary btn-xs"> <i class="fas fa-download"></i></a>' : ''}
                            </td>`;
                        html += '</tr>';

                    });
                    $('#load-invoices').append(html);
                    $('[data-tt="tooltip"]').tooltip();

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

</script>

@endsection


@endsection
