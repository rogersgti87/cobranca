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
                <form class="form-search">
                    <div class="form-row">

                        <div class="form-group col-md-4 col-sm-12" id="addField">
                            <input type="date" autocomplete="off" class="form-control" name="date" id="date" value="">
                        </div>
                        <input type="hidden" name="page" id="page" value="0">

                        <div class="form-group">
                            <button type="submit" id="btn-search" class="btn btn-primary">BUSCAR</button>
                        </div>
                    </div>
                </form>
            </div>
          </div>
          <!-- /.col-md-12 -->

          <div class="col-md-2">
            <ul class="button-action">
                <li><a href="#" data-original-title="Deletar" id="btn-delete" data-toggle="tooltip" class="btn btn-danger btn-sm"> <i class="fa fa-trash"></i> Excluir</a></li>
             </ul>
          </div>

          <!-- FIM FORM BUSCA -->
          <div class="col-md-12">
            <div class="card-box" id="load-data">
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


     <!-- Modal :: Log -->
     <div class="modal fade" id="modal-log" tabindex="-1" role="dialog" aria-labelledby="modalLogLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLogLabel"></h5>
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

                <div class="modal-body" id="modal-content-log">

                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
            </div>
        </div>
     </div>
     <!-- Modal :: Log -->

@section('scripts')


<script>

  // Open Modal - Create - Services
  $(document).on("click", "#btn-modal-log", function() {
        var id = $(this).data('log-id');
        var url = "{{url('admin/logs')}}"+'/'+id;
        $("#modal-log").modal('show');
        $.get(url,
            $(this)
            .addClass('modal-scrollfix')
            .find('#modal-content-log')
            .html('Carregando...'),
            function(data) {
                var json = JSON.stringify(JSON.parse(data.log), null, 2);
                $("#modal-content-log").html('<pre>'+json+'</pre>');
            });
    });

</script>

<script>

$(document).ready(function(){
    loadData(0);
});

$(document).on('click','.pagination .page-item a', function(e){
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    loadData(page);
});

$(document).on('submit','.form-search', function(e){
    e.preventDefault();
    loadData(0);
});

function loadData(page){
    $('#page').val(page);
    var data = $('.form-search').serialize();

    $.ajax({
        url: "{{url('admin/logs-list')}}",
        method: "GET",
        data: data,

    }).done(function(data){
        $('#load-data').html(data);
    });
}


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
            url: "",
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
