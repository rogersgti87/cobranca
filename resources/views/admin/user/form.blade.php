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
                                <img src="{{ isset($data->image) && $data->image != null ? url("$data->image") : url('assets/admin/img/thumb.png') }}" id="holder" style="height: 100px;width: 100px;">
                            </a>
                            <input type="hidden" id="thumbnail" name="image" value="{{ isset($data->image) ? $data->image : '' }}">
                        </div>

                    </fieldset>

                    </div>

                    <div class="col-md-4 col-sm-12">
                        <fieldset>
                            <legend>Whatsapp</legend>
                            @if(Request::get('id'))
                                <button type="button" data-toggle="modal" data-target="#modal-ler-qrcode" id="{{ Request::get('id') }}" data-original-title="LER QRCODE" data-toggle="tooltip" class="btn btn-success btn-md"> <i class="fa fa-qrcode"></i> LER QRCODE</button>
                                <button type="button" data-toggle="modal" data-target="#modal-novo-qrcode" id="{{ Request::get('id') }}" data-original-title="NOVO QRCODE" data-toggle="tooltip" class="btn btn-success btn-md"> <i class="fa fa-qrcode"></i> NOVO QRCODE</button>
                            @endif
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
                        <legend>Dados da API</legend>

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
                        <label>Token (PagHiper)</label>
                        <input type="text" class="form-control" name="token_paghiper" id="token_paghiper" autocomplete="off" required value="{{isset($data->token_paghiper) ? $data->token_paghiper : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Key (PagHiper)</label>
                        <input type="text" class="form-control" name="key_paghiper" id="key_paghiper" autocomplete="off" required value="{{isset($data->key_paghiper) ? $data->key_paghiper : ''}}">
                    </div>

                    <div class="form-group col-md-4 col-sm-12">
                        <label>Access Token (Mercado Pago)</label>
                        <input type="text" class="form-control" name="access_token_mp" id="access_token_mp" autocomplete="off" required value="{{isset($data->access_token_mp) ? $data->access_token_mp : ''}}">
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




 <!-- Modal Product -->
 <div class="modal fade" id="modal-ler-qrcode">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <div class="modal-body-ler-qrcode"></div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->



 <!-- Modal Product -->
 <div class="modal fade" id="modal-novo-qrcode">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <div class="modal-body-novo-qrcode"></div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

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




        $("#modal-ler-qrcode").on("show.bs.modal", function(e) {

        var url = '{{ url('admin/users/getqrcode') }}';
        $.get(url,
            $(this)
            .addClass('modal-scrollfix')
            .find('.modal-body-ler-qrcode')
            .html('Carregando...'),
            function(data) {
                console.log(data);
            $(".modal-body-ler-qrcode").html(`<img src="${data.qrcode}" style="width:500px; height:500px;">`);
            });



        });



$("#modal-novo-qrcode").on("show.bs.modal", function(e) {

    if(confirm("Deseja gerar outro QRCODE?")){

        var url = '{{ url('admin/users/getsession') }}';
    $.get(url,
    $(this)
    .addClass('modal-scrollfix')
    .find('.modal-body-novo-qrcode')
    .html('Carregando...'),
    function(data) {
        console.log(data);
    $(".modal-body-novo-qrcode").html(`<img src="${data.qrcode}" style="width:500px; height:500px;">`);
    });

    }else{
        return false;
    }

});



    </script>




@endsection


@endsection
