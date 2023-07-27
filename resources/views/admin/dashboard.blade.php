@extends('layouts.admin')

@section('content')



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          {{-- <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol>
          </div><!-- /.col --> --}}
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12">

                <div class="card card-primary card-outline">
                  <div class="card-body d-flex justify-content-center">

                    {{-- <a href="{{url('admin/users')}}" class="btn btn-app bg-success">
                        <i class="fas fa-users"></i> Meu Perfil
                    </a>

                    <a href="{{url('admin/properties')}}" class="btn btn-app bg-warning">
                        <i class="far fa-building"></i> Propriedades
                    </a> --}}

                  </div>
                </div>
            </div>


            @if(\Auth::user()->type == 'Usuario')
          <div class="col-lg-12">

            <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0">Membros pendentes</h5>
              </div>
              <div class="card-body">
                <table id="users-datatable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Data Cadastro</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($users as $row)
                        <tr id="{{ $row->id }}" class="link-user" style="cursor:pointer;">
                            <td>{{$row->id}}</td>
                            <td>{{$row->name}}</td>
                            <td>{{date('d/m/Y H:i:s',strtotime($row->created_at))}}</td>
                            <td><label class="badge badge-{{$row->status === 1 ? 'success' : 'danger'}}">{{$row->status === 1 ? 'Ativo' : 'Inativo'}}</label></td>
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Data Cadastro</th>
                            <th>Status</th>
                        </tr>
                    </tfoot>
                </table>

              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->

          <div class="col-lg-12">

            <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0">Empresas pendentes</h5>
              </div>
              <div class="card-body">
                <table id="companies-datatable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Data Cadastro</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($companies as $row)
                        <tr id="{{ $row->id }}" class="link-company" style="cursor:pointer;">
                            <td>{{$row->id}}</td>
                            <td>{{$row->name}}</td>
                            <td>{{date('d/m/Y H:i:s',strtotime($row->created_at))}}</td>
                            <td><label class="badge badge-{{$row->status === 1 ? 'success' : 'danger'}}">{{$row->status === 1 ? 'Ativo' : 'Inativo'}}</label></td>
                        </tr>
                    @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Data Cadastro</th>
                            <th>Status</th>
                        </tr>
                    </tfoot>
                </table>

              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
    @else

    @endif
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

    $('.link-user').click(function(){

        var url = "{{ url('admin/users/form?act=edit')}}";
        var id  = $(this).attr('id');

        window.location = url+'&id='+id;

    });


    $('.link-company').click(function(){

        var url = "{{ url('admin/companies/form?act=edit')}}";
        var id  = $(this).attr('id');

        window.location = url+'&id='+id;

    });

    });

</script>


  @endsection

  @endsection
