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
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="col-md-12">
        <div class="form-row">

                    <div class="col-md-12">

                    <fieldset>
                        <legend>Dados do Serviço</legend>
                        <div class="form-row">

                            <div class="form-group col-md-6 col-sm-12">
                                <label>Nome</label>
                                <input type="text" class="form-control" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                            </div>

                            <div class="form-group col-md-3 col-sm-12">
                                <label>Preço</label>
                                <input type="text" class="form-control money" name="price" id="price" autocomplete="off" required value="{{isset($data->price) ? $data->price : ''}}">
                            </div>

                            <div class="form-group col-md-3 col-sm-12">
                                <label>Status</label>
                                <select class="form-control custom-select" name="status" id="status">
                                    <option {{ isset($data->status) && $data->status == 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                                    <option {{ isset($data->status) && $data->status == 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                                </select>
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

    //Save data
        $(document).on('click', '#btn-salvar', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            tinymce.triggerSave();
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
                        '<a href="{{url($linkFormAdd)}}"  class="btn btn-secondary btn-md"> <i class="fa fa-plus"></i> Novo</a>  ' +
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
