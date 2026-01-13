@extends('layouts.admin')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0" style="color: #E5E7EB; font-weight: 600;">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right" style="background-color: transparent; padding: 0;">
              <li class="breadcrumb-item"><a href="{{url('admin')}}" style="color: #E5E7EB; text-decoration: none; opacity: 0.7;">Home</a></li>
              <li class="breadcrumb-item active" style="color: #E5E7EB; opacity: 0.7;">{{ $title }}</li>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="#" data-original-title="Deletar Selecionados" id="btn-delete" class="btn" style="background-color: #F87171; color: #FFFFFF !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;"> <i class="fa fa-trash"></i> Deletar Selecionados</a>
                </div>
                <div>
                    <a href="#" data-original-title="Nova Categoria" id="btn-modal-category" data-type="add-category" data-toggle="tooltip" class="btn" style="background-color: #06b8f7; color: #FFFFFF !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;"> <i class="fa fa-plus"></i> Nova Categoria</a>
                </div>
            </div>

            <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                <div class="table-responsive">
                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                            <th style="width: 1px;">
                                <label class="containerchekbox">
                                    <input type="checkbox" id="selectAllChekBox" value="">
                                    <span class="checkmark"></span>
                                </label>
                            </th>
                            <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Nome</th>
                            <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Cor</th>
                            <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Contas Vinculadas</th>
                            <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px; width: 100px;"></th>
                        </tr>
                        </thead>

                        <tbody class="tbodyCustom" style="background-color: #111827;">
                        <form class="form">
                            {{ csrf_field() }}
                            @foreach($data as $result)
                                @php
                                    $isGlobal = is_null($result->user_id);
                                    $payablesCount = \App\Models\Payable::where('category_id', $result->id)
                                        ->where('user_id', auth()->user()->id)
                                        ->count();
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: background-color 0.2s; {{ $isGlobal ? 'opacity: 0.8;' : '' }}" onmouseover="this.style.backgroundColor='#1E293B'" onmouseout="this.style.backgroundColor='#111827'">
                                    <td style="padding: 12px;">
                                        @if(!$isGlobal)
                                            <label class="containerchekbox">
                                                <input type="checkbox" name="selected[]" value="{{$result->id}}" class="category-checkbox">
                                                <span class="checkmark"></span>
                                            </label>
                                        @else
                                            <span style="color: #9CA3AF; font-size: 12px;" title="Categoria padrão - não pode ser editada ou excluída">🔒</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px; color: #E5E7EB; font-size: 14px; font-weight: 500;">
                                        {{$result->name}}
                                        @if($isGlobal)
                                            <span style="color: #9CA3AF; font-size: 11px; margin-left: 8px;">(Padrão)</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px;">
                                        <span style="background-color: {{$result->color}}; color: #FFFFFF; padding: 6px 16px; border-radius: 12px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                                            <span style="display: inline-block; width: 12px; height: 12px; background-color: {{$result->color}}; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%;"></span>
                                            {{$result->color}}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; color: #E5E7EB; font-size: 14px;">
                                        {{ $payablesCount }} conta(s)
                                    </td>
                                    <td style="padding: 12px;">
                                        @if(!$isGlobal)
                                            <a href="#" data-original-title="Editar" id="btn-modal-category" data-type="edit-category" data-category="{{$result->id}}" data-toggle="tooltip" class="btn" style="background-color: #06b8f7; color: #FFFFFF !important; border: none; padding: 4px 12px; border-radius: 4px; font-size: 12px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="far fa-edit"></i> Editar</a>
                                        @else
                                            <span style="color: #9CA3AF; font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </form>
                        </tbody>
                    </table>
                </div>
                <div class="paginate mt-3">
                    {!! $data->links('pagination::bootstrap-4') !!}
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

<!-- Modal :: Form Category -->
<div class="modal fade" id="modalCategory" tabindex="-1" role="dialog" aria-labelledby="modalCategoryLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1);">
            <form action="" class="form-horizontal" id="form-request-category">
                <div class="modal-header" style="background-color: #1E293B; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="modal-title" id="modalCategoryLabel" style="color: #06b8f7; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #E5E7EB;">
                        <span aria-hidden="true" style="color: #E5E7EB;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-category" style="background-color: #111827;">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer" style="background-color: #1E293B; border-top: 1px solid rgba(255,255,255,0.1);">
                    <button type="button" class="btn" id="btn-save-category" style="background-color: #22C55E; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-check"></i> Salvar</button>
                    <button type="button" class="btn" data-dismiss="modal" style="background-color: #F87171; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
 </div>
 <!-- Modal :: Form Category -->

@endsection

@section('styles')
<style>
    body {
        background-color: #0F172A !important;
    }

    .content-wrapper {
        background-color: #0F172A !important;
    }

    .content-header {
        background-color: #0F172A !important;
    }

    .content {
        background-color: #0F172A !important;
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7) !important;
    }

    #btn-modal-category:hover {
        background-color: #06b8f7 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(6, 184, 247, 0.3);
        opacity: 0.9;
    }

    .form-control:focus {
        border-color: #06b8f7 !important;
        box-shadow: 0 0 0 3px rgba(6, 184, 247, 0.2) !important;
        outline: none;
        background-color: #0F172A !important;
        color: #E5E7EB !important;
    }

    select.form-control,
    input.form-control {
        background-color: #0F172A !important;
        color: #E5E7EB !important;
    }

    select.form-control option {
        background-color: #111827 !important;
        color: #E5E7EB !important;
    }
</style>
@endsection

@section('scripts')

<script>
    // Open Modal - Create/Edit Category
    $(document).on("click", "#btn-modal-category", function() {
        var type = $(this).data('type');
        $("#modalCategory").modal('show');
        if(type == 'add-category'){
            $("#modalCategoryLabel").html('Adicionar Categoria');
            var url = '{{ url("/admin/payable-categories/form?act=add") }}';
        }else{
            $("#modalCategoryLabel").html('Editar Categoria');
            var category = $(this).data('category');
            var url = '{{ url("/admin/payable-categories/form?act=edit") }}&id='+category;
        }

        $.get(url,
            $(this)
            .addClass('modal-scrollfix')
            .find('#form-content-category')
            .html('Carregando...'),
            function(data) {
                $("#form-content-category").html(data);
            });
    });

    //Save Category
    $(document).on('click', '#btn-save-category', function(e) {
        e.preventDefault();

        $("#btn-save-category").attr("disabled", true);
        $("#btn-save-category").text('Aguarde...');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });
        var data = $('#form-request-category').serialize();
        var category = $('#category').val();
        var baseUrl = "{{ url('admin/payable-categories') }}";
        if(category != ''){
            var url = baseUrl + '/' + category;
            var method = 'PUT';
        }else{
            var url = baseUrl;
            var method = 'POST';
        }

        $.ajax({
            url: url,
            data: data,
            method: method,
            beforeSend: function(){
                $("#btn-save-category").attr("disabled", true);
                $("#btn-save-category").text('Aguarde...');
            },
            success: function(data){
                $("#btn-save-category").attr("disabled", false);
                $("#btn-save-category").html('<i class="fa fa-check"></i> Salvar');
                var showClassObj = {};
                showClassObj.popup = 'animate__animated animate__backInUp';
                var titleHtml = "<h5 style='color:#06b8f7'>" + data.data + "</h5>";
                Swal.fire({
                    width: 350,
                    title: titleHtml,
                    icon: 'success',
                    showConfirmButton: true,
                    showClass: showClassObj,
                    allowOutsideClick: false
                }).then(function(result) {
                    $('#modalCategory').modal('hide');
                    location.reload();
                });
            },
            error: function(xhr) {
                $("#btn-save-category").attr("disabled", false);
                $("#btn-save-category").html('<i class="fa fa-check"></i> Salvar');
                var showClassWarn = {};
                showClassWarn.popup = 'animate__animated animate__wobble';
                var showClassError = {};
                showClassError.popup = 'animate__animated animate__wobble';
                if(xhr.status === 422){
                    Swal.fire({
                        text: xhr.responseJSON,
                        width: 300,
                        icon: 'warning',
                        color: '#E5E7EB',
                        confirmButtonColor: "#2563EB",
                        showClass: showClassWarn
                    });
                } else{
                    Swal.fire({
                        text: xhr.responseJSON,
                        width: 300,
                        icon: 'error',
                        color: '#E5E7EB',
                        confirmButtonColor: "#2563EB",
                        showClass: showClassError
                    });
                }
            }
        });
    });

    // Delete Category
    $('#btn-delete').click(function (e) {
        Swal.fire({
            title: 'Deseja remover este(s) registro(s)?',
            text: "Você não poderá reverter isso!",
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2563EB',
            cancelButtonColor: '#D1D5DB',
            confirmButtonText: 'Sim, deletar!'
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    url: "{{url($linkDestroy)}}",
                    method: 'DELETE',
                    data: $('.form').serialize(),
                    success:function(data){
                        location.reload();
                    },
                    error:function (xhr) {
                        var showClassWobbleErr = {};
                        showClassWobbleErr.popup = 'animate__animated animate__wobble';
                        if(xhr.status === 422){
                            Swal.fire({
                                text: xhr.responseJSON,
                                icon: 'warning',
                                showClass: showClassWobbleErr
                            });
                        } else{
                            Swal.fire({
                                text: xhr.responseJSON,
                                icon: 'error',
                                showClass: showClassWobbleErr
                            });
                        }
                    }
                });
            }
        });
    });

    // Select All Checkboxes (apenas categorias não globais)
    $('#selectAllChekBox').change(function() {
        $('.category-checkbox').prop('checked', this.checked);
    });

    $('[data-toggle="tooltip"]').tooltip();
</script>

@endsection

