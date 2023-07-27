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
            <div class="card-box">
                <ul class="button-action">
                    <li><a href="#" data-original-title="Salvar" data-toggle="tooltip" class="btn btn-secondary" id="btn-salvar"><i class="fa fa-save fa-2x"></i></a></li>
                </ul>
            </div>

    <form class="form">

        <div class="col-md-12">
        <div class="form-row">

                    <div class="col-md-4 col-sm-12">
                    <fieldset>
                        <legend>Logo</legend>

                        <div class="form-group col-md-12 col-sm-12 text-center">
                            <a class="btn btn-default" style="border:1px solid #333;" id="lfm" data-input="thumbnail" data-preview="holder" style="cursor: pointer">
                                <img src="{{ isset($data->image_thumb) && $data->image_thumb != null ? url("$data->image_thumb") : url('assets/admin/img/thumb.png') }}" id="holder" style="height: 235px;width: 235px;">
                            </a>
                            <input type="hidden" id="thumbnail" name="image" value="{{ isset($data->image) ? $data->image : '' }}">
                        </div>

                    </fieldset>

                    </div>

                    <div class="col-md-4 col-sm-12">
                        <fieldset>
                            <legend>QRCODE Whatsapp</legend>
                            <div class="form-group col-md-12 col-sm-12 text-center">
                                <img src="" id="qrcode-whatsapp" style="height: 250px;width: 250px;">
                            </div>

                        </fieldset>

                        </div>

                    <div class="col-md-12">

                    <fieldset>
                        <legend>Dados do Usuário/Empresa</legend>

                    <div class="form-row">

                    <div class="form-group col-md-2 col-sm-12">
                        <label>CPF/CNPJ</label>
                        <input type="text" class="form-control" name="document" id="cnpjcpf" autocomplete="off" required value="{{isset($data->document) ? $data->document : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Nome</label>
                        <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                    </div>


                    <div class="form-group col-md-6 col-sm-12">
                        <label>Empresa</label>
                        <input type="text" class="form-control" name="company" id="company" autocomplete="off" required value="{{isset($data->company) ? $data->company : ''}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>E-mail</label>
                        <input type="email" class="form-control" name="email" id="email" autocomplete="off" required value="{{isset($data->email) ? $data->email : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Senha</label>
                        <input type="password" class="form-control" name="password" id="password" autocomplete="off" required value="">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Confirme a senha</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" autocomplete="off" required value="">
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
                        <input type="text" class="form-control" name="telephone" id="telephone" autocomplete="off" required value="{{isset($data->telephone) ? $data->telephone : ''}}">
                    </div>

                    <div class="form-group col-md-3 col-sm-12">
                        <label>Whatsapp</label>
                        <input type="text" class="form-control" name="whatsapp" id="whatsapp" autocomplete="off" required value="{{isset($data->whatsapp) ? $data->whatsapp : ''}}">
                    </div>

                    <div class="form-group col-md-2 col-sm-12">
                        <label>Status</label>
                        <select class="form-control custom-select" name="status" id="status">
                            <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                            <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                        </select>
                    </div>

                        </div>

                    </fieldset>


                    <fieldset>
                        <legend>Dados da API/E-mail</legend>

                        <div class="form-row">

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Host API Whatsapp</label>
                        <input type="text" class="form-control" name="api_host_whatsapp" id="api_host_whatsapp" autocomplete="off" required value="{{isset($data->api_host_whatsapp) ? $data->api_host_whatsapp : ''}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Access Token API Whatsapp</label>
                        <input type="text" class="form-control" name="api_access_token_whatsapp" id="api_access_token_whatsapp" autocomplete="off" required value="{{isset($data->api_access_token_whatsapp) ? $data->api_access_token_whatsapp : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Host SMTP</label>
                        <input type="text" class="form-control" name="smtp_host" id="smtp_host" autocomplete="off" required value="{{isset($data->smtp_host) ? $data->smtp_host : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Usuário SMTP</label>
                        <input type="text" class="form-control" name="smtp_user" id="smtp_user" autocomplete="off" required value="{{isset($data->smtp_user) ? $data->smtp_user : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Senha SMTP</label>
                        <input type="text" class="form-control" name="smtp_password" id="smtp_password" autocomplete="off" required value="{{isset($data->smtp_password) ? $data->smtp_password : ''}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Porta SMTP</label>
                        <input type="text" class="form-control" name="smtp_port" id="smtp_port" autocomplete="off" required value="{{isset($data->smtp_port) ? $data->smtp_port : ''}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Segurança SMTP</label>
                        <select class="form-control" name="smtp_security">
                            <option {{isset($data->smtp_security) && $data->smtp_security == '' ? 'selected': ''}} value="">Nenhum</option>
                            <option {{isset($data->smtp_security) && $data->smtp_security == 'tls' ? 'selected': ''}} value="tls">TLS</option>
                            <option {{isset($data->smtp_security) && $data->smtp_security == 'ssl' ? 'selected': ''}} value="ssl">SSL</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Token SendPulse API</label>
                        <input type="text" class="form-control" name="sendpulse_token" id="sendpulse_token" autocomplete="off" required value="{{isset($data->sendpulse_token) ? $data->sendpulse_token : ''}}">
                    </div>

                    <div class="form-group col-md-6 col-sm-12">
                        <label>Secret SendPulse API</label>
                        <input type="text" class="form-control" name="sendpulse_secret" id="sendpulse_secret" autocomplete="off" required value="{{isset($data->sendpulse_secret) ? $data->sendpulse_secret : ''}}">
                    </div>

                        </div>

                    </fieldset>


                </div>



        </div>

        </div>

    </form>

        </div>
    </div>


</div>
</div>
</div>
  </div>
 </div>

@section('scripts')

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
                var url = "{{ url("$linkUpdate") }}";
                var method = 'PUT';
            }else{
                var url = "{{ url("$linkStore") }}";
                var method = 'POST';
            }

            var data = $('.form').serialize();
            console.log(url);

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
