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
                        <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Serviços</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Faturas</a>
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
                                                    <input type="text" class="form-control" name="document" id="cnpjcpf" autocomplete="off" required value="{{isset($data->document) ? $data->document : ''}}">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Nome</label>
                                                    <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                                                </div>


                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Empresa</label>
                                                    <input type="text" class="form-control" name="company" id="company" autocomplete="off" required value="{{isset($data->company) ? $data->company : ''}}">
                                                </div>

                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>E-mail</label>
                                                    <input type="email" class="form-control" name="email" id="email" autocomplete="off" required value="{{isset($data->email) ? $data->email : ''}}">
                                                </div>

                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>E-mail 2</label>
                                                    <input type="email" class="form-control" name="email2" id="email2" autocomplete="off" required value="{{isset($data->email2) ? $data->email2 : ''}}">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Cep</label>
                                                    <input type="text" class="form-control" name="cep" id="cep" autocomplete="off" required value="{{isset($data->cep) ? $data->cep : ''}}">
                                                </div>

                                                <div class="form-group col-md-8 col-sm-12">
                                                    <label>Endereço</label>
                                                    <input type="text" class="form-control" name="address" id="address" autocomplete="off" required value="{{isset($data->address) ? $data->address : ''}}">
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Cep</label>
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
                                                    <input type="text" class="form-control" name="state" id="state" autocomplete="off" readonly="readonly" value="{{isset($data->state) ? $data->state : ''}}">
                                                </div>

                                                <div class="form-group col-md-4 col-sm-12">
                                                    <label>Complemento</label>
                                                    <input type="text" class="form-control" name="complement" id="complement" autocomplete="off" required value="{{isset($data->complement) ? $data->complement : ''}}">
                                                </div>

                                                <div class="form-group col-md-3 col-sm-12">
                                                    <label>Telefone</label>
                                                    <input type="text" class="form-control" name="phone" id="telephone" autocomplete="off" required value="{{isset($data->phone) ? $data->phone : ''}}">
                                                </div>

                                                <div class="form-group col-md-3 col-sm-12">
                                                    <label>Whatsapp</label>
                                                    <input type="text" class="form-control" name="whatsapp" id="whatsapp" autocomplete="off" required value="{{isset($data->whatsapp) ? $data->whatsapp : ''}}">
                                                </div>


                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Forma de Pagamento</label>
                                                    <select class="form-control custom-select" name="payment_method" id="payment_method">
                                                        <option {{ isset($data->payment_method) && $data->payment_method === 'Pix' ? 'selected' : '' }} value="Pix">Pix</option>
                                                        <option {{ isset($data->payment_method) && $data->payment_method === 'Boleto' ? 'selected' : '' }} value="Boleto">Boleto</option>
                                                        <option {{ isset($data->payment_method) && $data->payment_method === 'Depósito' ? 'selected' : '' }} value="Depósito">Depósito</option>
                                                        <option {{ isset($data->payment_method) && $data->payment_method === 'Dinheiro' ? 'selected' : '' }} value="Dinheiro">Dinheiro</option>
                                                        <option {{ isset($data->payment_method) && $data->payment_method === 'Cartão' ? 'selected' : '' }} value="Cartão">Cartão</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Notificar Whatsapp?</label>
                                                    <select class="form-control custom-select" name="notification_whatsapp" id="notification_whatsapp">
                                                        <option {{ isset($data->notification_whatsapp) && $data->notification_whatsapp === 's' ? 'selected' : '' }} value="s">Sim</option>
                                                        <option {{ isset($data->notification_whatsapp) && $data->notification_whatsapp === 'n' ? 'selected' : '' }} value="n">Não</option>
                                                    </select>
                                                </div>

                                                <div class="form-group col-md-2 col-sm-12">
                                                    <label>Status</label>
                                                    <select class="form-control custom-select" name="status" id="status">
                                                        <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                                                        <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                                                    </select>
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
                                <div class="table-responsive fixed-solution">
                                    <table class="table table-hover table-striped table-sm">
                                        <thead class="thead-light">
                                        <tr>
                                            <th> Nome</th>
                                            <th> Descrição</th>
                                            <th> Preço</th>
                                            <th> Vencimento</th>
                                            <th> Período</th>
                                            <th> Status</th>
                                            <th style="width: 100px;"></th>
                                        </tr>
                                        </thead>
                                        <tbody class="tbodyCustom">
                                                {{-- <tr>
                                                    <td></td>
                                                    <td>R$ </td>
                                                    <td><label class="badge badge-{{$result->status == 'Ativo' ? 'success' : 'danger'}}">{{$result->status}}</label></td>
                                                    <td><a href="{{url($linkFormEdit."&id=$result->id")}}" data-original-title="Editar" data-toggle="tooltip" class="btn btn-primary btn-xs"> <i class="fa fa-list"></i> Editar</a></td>
                                                </tr> --}}

                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                        <!-- FIM TABLE -->

                      </div>
                      <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">
                         Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
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

@section('scripts')


    <script src="{{url('/vendor/laravel-filemanager/js/stand-alone-button-normal.js')}}"></script>

<script>

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
                    console.log(data);
                    Swal.fire({
                        width:350,
                        title: "<h5 style='color:#007bff'>" + data + "</h5>",
                        icon: 'success',
                        showConfirmButton: false,
                        showClass: {
                            popup: 'animate__animated animate__backInUp'
                        },
                        allowOutsideClick: false,
                        html:
                        '<a href="{{url($linkFormAdd)}}" data-original-title="Novo" data-toggle="tooltip" class="btn btn-secondary btn-md"> <i class="fa fa-plus"></i> Novo</a>  ' +
                        `<a href="{{url($linkFormEdit)}}" class="btn btn-success btn-md" style="${url_act == 'add' ? 'display:none;' : ''}"> <i class="fa fa-plus"></i> Editar</a>  ` +
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




@endsection


@endsection
