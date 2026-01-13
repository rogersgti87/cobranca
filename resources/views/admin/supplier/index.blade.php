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
                            </select>
                        </div>
                        <div class="form-group col-md-2 col-sm-12">
                            <select class="form-control" name="operator" id="operator">
                                <option {{  request()->operator == 'like' ? 'selected' : '' }} value="like">Contém</option>
                                <option {{  request()->operator == '='    ? 'selected' : '' }} value="=">=</option>

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
                <li><a href="#" data-original-title="Novo" id="btn-modal-supplier" data-type="add-supplier" data-toggle="tooltip" class="btn btn-sm" style="background-color: #06b8f7; color: #FFFFFF !important; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600;"> <i class="fa fa-plus"></i> Novo</a></li>
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
                            <th><a href="{{ request()->fullUrlWithQuery(['column' => 'name',   'order'  => "$order"]) }}"><i class="fas fa-sort"></i></a> Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Status</th>
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

                                    <td>{{$result->name}} {{$result->company != null ?  ' - ( '. $result->company. ' ) ' : ''}}</td>
                                    <td>{{$result->email}}</td>
                                    <td>{{$result->phone}}</td>
                                    <td><span class="badge badge-{{$result->status == 'Ativo' ? 'success' : 'danger'}}">{{$result->status}}</span></td>
                                    <td><a href="#" data-original-title="Editar" id="btn-modal-supplier" data-type="edit-supplier" data-supplier="{{$result->id}}" data-toggle="tooltip" class="btn btn-xs" style="background-color: #06b8f7; color: #FFFFFF !important; border: none; padding: 4px 8px; border-radius: 4px; font-weight: 600;"> <i class="fa fa-list"></i> Editar</a></td>
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

<!-- Modal :: Form Supplier -->
<div class="modal fade" id="modalSupplier" tabindex="-1" role="dialog" aria-labelledby="modalSupplierLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1);">
            <form action="" class="form-horizontal" id="form-request-supplier">
                <div class="modal-header" style="background-color: #1E293B; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="modal-title" id="modalSupplierLabel" style="color: #06b8f7; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #E5E7EB;">
                        <span aria-hidden="true" style="color: #E5E7EB;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-supplier" style="background-color: #111827;">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer" style="background-color: #1E293B; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button type="button" class="btn" id="btn-save-supplier" style="background-color: #22C55E; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-check"></i> Salvar</button>
                    <button type="button" class="btn" data-dismiss="modal" style="background-color: #F87171; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
 </div>
 <!-- Modal :: Form Supplier -->

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

// Open Modal - Create/Edit Supplier
$(document).on("click", "#btn-modal-supplier", function() {
    var type = $(this).data('type');
    var supplier_id = $(this).data('supplier');

    $("#modalSupplier").modal('show');

    if(type == 'add-supplier'){
        $("#modalSupplierLabel").html('Adicionar Fornecedor');
        var url = "{{url('admin/suppliers/form')}}?act=add";
    }else{
        $("#modalSupplierLabel").html('Editar Fornecedor');
        var url = "{{url('admin/suppliers/form')}}?act=edit&id=" + supplier_id;
    }

    $.ajax({
        url: url,
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {
            $("#form-content-supplier").html(data);
            $("#modalSupplier").modal('show')
                .addClass('modal-scrollfix');
        },
        error: function(xhr) {
            Swal.fire({
                text: 'Erro ao carregar formulário',
                icon: 'error'
            });
        }
    });
});

// Save Supplier
$(document).on("click", "#btn-save-supplier", function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        }
    });

    var supplier_id = $("#supplier_id").val();
    var url, method;

    if(supplier_id && supplier_id != ''){
        url = "{{url('admin/suppliers')}}/" + supplier_id;
        method = 'PUT';
    }else{
        url = "{{url('admin/suppliers')}}";
        method = 'POST';
    }

    var formData = $("#form-request-supplier").serialize();

    $.ajax({
        url: url,
        data: formData,
        method: method,
        success: function(data) {
            var message = method == 'POST' ? data.data : data;
            Swal.fire({
                width: 350,
                title: "<h5 style='color:#06b8f7'>" + message + "</h5>",
                icon: 'success',
                confirmButtonColor: '#06b8f7',
                cancelButtonColor: '#1E293B',
                showConfirmButton: true,
                showClass: {
                    popup: 'animate__animated animate__backInUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#modalSupplier').modal('hide');
                    location.reload();
                }
            });
        },
        error: function(xhr) {
            if(xhr.status === 422){
                Swal.fire({
                    text: xhr.responseJSON,
                    width: 300,
                    icon: 'warning',
                    confirmButtonColor: '#06b8f7',
                    cancelButtonColor: '#1E293B',
                    showClass: {
                        popup: 'animate__animated animate__wobble'
                    }
                });
            } else{
                Swal.fire({
                    text: xhr.responseJSON || 'Erro ao salvar fornecedor',
                    width: 300,
                    icon: 'error',
                    confirmButtonColor: '#06b8f7',
                    cancelButtonColor: '#1E293B',
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
    confirmButtonColor: '#FFBD59',
    cancelButtonColor: '#1E293B',
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
                        confirmButtonColor: '#06b8f7',
                        cancelButtonColor: '#1E293B',
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                } else{
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'error',
                        confirmButtonColor: '#06b8f7',
                        cancelButtonColor: '#1E293B',
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

