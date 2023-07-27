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
          <div class="col-md-10">

            <div class="card-box">
                <form class="form-busca" action="{{url($filter)}}">
                    <input type="hidden" name="filter" value="true">
                    <div class="form-row">
                        <div class="form-group col-md-4 col-sm-12">
                            <select class="form-control" name="field" id="filter-field">
                                <option data-type="input" {{  request()->field  ==  'name'       ? 'selected' : '' }} value="name">Nome</option>
                                <option data-type="input" {{  request()->field  ==  'created_at' ? 'selected' : '' }} value="created_at">Data</option>
                                <option data-type="select" {{  request()->field ==  'status'     ? 'selected' : '' }} value="status">Status</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-12">
                            <select class="form-control" name="operator" id="operator">
                                <option {{  request()->operator == '='    ? 'selected' : '' }} value="=">=</option>
                                <option {{  request()->operator == 'like' ? 'selected' : '' }} value="like">Contém</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 col-sm-12" id="addField">
                            <input type="text" autocomplete="off" class="form-control @if(request()->field == 'data' || request()->field == 'dataini' || request()->field == 'datafim') datepicker @endif" name="value" id="filter-value" value="{{  request()->value }}">
                        </div>
                        <div class="form-group">
                            <button type="submit" id="btn-buscar" class="btn btn-primary">BUSCAR</button>
                        </div>
                    </div>
                </form>
            </div>
          </div>
          <!-- /.col-md-12 -->

          <div class="col-md-2">
            <ul class="button-action">
                <li><a href="{{url($linkFormAdd)}}" data-original-title="Novo" data-toggle="tooltip" class="btn btn-secondary btn-sm"> <i class="fa fa-plus"></i> Novo</a></li>
                <li><a href="#" data-original-title="Copiar" id="btn-copy" data-toggle="tooltip" class="btn btn-info btn-sm"> <i class="fa fa-copy"></i> Copiar</a></li>
                <li><a href="#" data-original-title="Deletar" id="btn-delete" data-toggle="tooltip" class="btn btn-danger btn-sm"> <i class="fa fa-trash"></i> Excluir</a></li>
             </ul>
          </div>

          <!-- FIM FORM BUSCA -->


          <div class="col-md-12">
            <div class="card-box">
                <div class="table-responsive fixed-solution">
                    <table class="table table-hover table-striped table-sm">
                        <thead class="thead-light">
                        <tr>
                            <th style="width: 1px;">
                            <label class="containerchekbox">
                                <input type="checkbox" id="selectAllChekBox" value="">
                                <span class="checkmark"></span>
                            </label>
                            </th>
                            <th>Imagem</th>
                            <th><a href="{{ request()->fullUrlWithQuery(['column' => 'name',   'order'  => "$order"]) }}"><i class="fas fa-sort"></i></a> Nome</th>
                            <th><a href="{{ request()->fullUrlWithQuery(['column' => 'status', 'order'  => "$order" ]) }}"><i class="fas fa-sort"></i></a> Status</th>
                            <th style="width: 100px;"></th>
                        </tr>
                        </thead>

                        <tbody class="tbodyCustom">
                        <form class="form">
                            {{ csrf_field() }}
                            @foreach($data as $result)
                                <tr>
                                    <td>
                                        <label class="containerchekbox">
                                            <input type="checkbox" name="selected[]" value="{{$result->id}}">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>

                                    <td><img src="{{ $result->image_thumb != null ? url("$result->image_thumb") : url('assets/admin/img/thumb.png')}}" class="thumbnail" style="width:45px;height: 45px;"></td>
                                    <td>{{$result->name}}</td>
                                    <td><label class="badge badge-{{$result->status === 1 ? 'success' : 'danger'}}">{{$result->status === 1 ? 'Ativo' : 'Inativo'}}</label></td>
                                    <td><a href="{{url($linkFormEdit."&id=$result->id")}}" data-original-title="Editar" data-toggle="tooltip" class="btn btn-primary btn-xs"> <i class="fa fa-list"></i> Editar</a></td>
                                </tr>
                            @endforeach
                        </form>
                        </tbody>
                    </table>
                </div>
                <div class="paginate">
                    {!! $data->withQueryString()->links('pagination::bootstrap-4') !!}
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

@section('scripts')

<script>

$(document).ready(function () {
    changeInput();

    $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            orientation: 'bottom'
        });



    });

function changeInput() {
        var field = '';
        if ($("#filter-field option:selected").data('type') == 'input') {
            field += `<input type="text" autocomplete="off" class="form-control @if(request()->field == 'data' || request()->field == 'dataini' || request()->field == 'datafim' || request()->field == 'created_at') datepicker @endif" name="value" id="filter-value" value="{{  request()->value }}">`;
        } else if ($("#filter-field option:selected").val() == 'status') {
            field += '<select class="form-control" name="value">';
            field += '<option value=""></option>';
            field += '<option {{ request()->value == 1 ? 'selected' : '' }} value="1">Ativo</option>';
            field += '<option {{ request()->value == 0 ? 'selected' : '' }} value="0">Inativo</option>';
            field += '</select>';
        }

        $("#addField").html('');
        $("#addField").html(field);
    }

$("#filter-field").change(function (e) {
    changeInput();
    if(e.target.options[e.target.selectedIndex].value == 'data' || e.target.options[e.target.selectedIndex].value == 'dataini' || e.target.options[e.target.selectedIndex].value == 'datafim' || e.target.options[e.target.selectedIndex].value == 'created_at'){
        $("#filter-value").addClass('datepicker');
        $("#filter-value").val('');
        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            orientation: 'bottom'
        });

    }else{
        $("#filter-value").removeClass('datepicker');
        $("#filter-value").datepicker("destroy");
        $("#filter-value").val('');
    }
});



$('#btn-copy').click(function (e) {

var data = $('.form').serialize();

$.ajax({
    url: "{{url($linkCopy)}}",
    method: 'POST',
    data: data,
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


</script>

@endsection


@endsection
