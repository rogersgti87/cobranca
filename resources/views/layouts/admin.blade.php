<!DOCTYPE html>

<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cobrança Segura</title>
  <link rel="icon" href="{{url('/img/favicon.png')}}">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
{{--  <link rel="stylesheet" href="{{url('assets/admin/plugins/fontawesome-free/css/all.min.css')}}">--}}
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('assets/admin/css/adminlte.min.css')}}">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{url('assets/admin/css/custom.css')}}">

<!-- datepicker styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css" />

  <!-- Select2 -->
  <link rel="stylesheet" href="{{url('assets/admin/plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{url('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" integrity="sha512-xmGTNt20S0t62wHLmQec2DauG9T+owP9e6VU8GigI0anN7OXLip9i7IwEhelasml2osdxX71XcYm6BQunTQeQg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Bootstrap Color Picker -->
<link rel="stylesheet" href="{{url('assets/admin/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">

<link rel="stylesheet" href="{{ url('assets/admin/plugins/iconpicker/dist/fontawesome-5.11.2/css/all.min.css') }}">
<link rel="stylesheet" href="{{ url('assets/admin/plugins/iconpicker/dist/iconpicker-1.5.0.css') }}">

<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">


<link rel="stylesheet" href="{{ url('assets/admin/plugins/dropzone/min/dropzone.min.css')}}">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

     <!-- Navbar -->
     <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>
     </nav>

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar elevation-4 sidebar-light-primary">
        <!-- Brand Logo -->
        <a href="{{url('/admin')}}" class="brand-link bg-gray text-center">
            <img src="{{url('img/logo.png')}}" alt="{{auth()->user()->company}}" class="" style="width:80% !important;float:none !important;" style="opacity: .8">
        </a>
        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex text-center">
            <div class="info">
                <img src="{{ \Auth::user()->image != null ? url(\Auth::user()->image) : url('assets/admin/img/thumb.png')}}" style="width:80% !important;" class="img-thumbnail" alt="{{auth()->user()->name}}">
                <br>
              <span class="d-block"><strong>{{ Auth::user()->name }}</strong></span>
            </div>
          </div>

          <!-- SidebarSearch Form -->
          <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
              <input class="form-control form-control-sidebar" type="search" placeholder="Pesquisar" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-sidebar">
                  <i class="fas fa-search fa-fw"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <!-- Add icons to the links using the .nav-icon class
                   with font-awesome or any other icon font library -->

                   <li class="nav-item">
                    <a href="{{ url('/admin') }}" class="nav-link">
                      <i class="nav-icon fas fa-home"></i>
                      <p>Home</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{url('admin/users')}}" class="nav-link {{Request::segment(2) == 'users' ? 'active' : ''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Usuários</p>
                    </a>
                  </li>

                <li class="nav-item">
                <a href="{{url('admin/services')}}" class="nav-link  {{Request::segment(2) == 'services' ? 'active' : ''}}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Serviços</p>
                </a>
                </li>

              <li class="nav-item">
                <a href="{{url('admin/customers')}}" class="nav-link  {{Request::segment(2) == 'customers' ? 'active' : ''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Clientes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('admin/invoices')}}" class="nav-link  {{Request::segment(2) == 'invoices' ? 'active' : ''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Faturas</p>
                </a>
              </li>



              <li class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                  <i class="nav-icon fas fa-sign-out-alt"></i>
                  <p>Sair</p>
                </a>
              </li>

           <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
               @csrf
           </form>



            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>

@yield('content')

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Desenvolvido por <a target="_blank" href="https://rogerti.com.br">ROGER.TI</a>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {{date('Y')}} <a target="_blank" href="https://cobrancasegura.com.br">Cobrança Segura</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{url('assets/admin/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{url('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{url('assets/admin/js/adminlte.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

<!-- Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script src="{{ url('assets/admin/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/0.9.0/jquery.mask.min.js" integrity="sha512-oJCa6FS2+zO3EitUSj+xeiEN9UTr+AjqlBZO58OPadb2RfqwxHpjTU8ckIC8F4nKvom7iru2s8Jwdo+Z8zm0Vg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Select2 -->
<script src="{{url('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{url('assets/admin/plugins/select2/js/i18n/pt-BR.js')}}"></script>

<script src="{{url('/vendor/laravel-filemanager/js/stand-alone-button-normal.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.5/tinymce.min.js" integrity="sha512-TXT0EzcpK/3KaFksZ59D/1A3orhVtDzhwgtYeSIGxM6ZgCW1+ak+2BqbJPps2JQlkvRApI37Xqbr8ligoIGjBQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js" integrity="sha512-9UR1ynHntZdqHnwXKTaOm1s6V9fExqejKvg5XMawEMToW4sSw+3jtLrYfZPijvnwnnE8Uol1O9BcAskoxgec+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- bootstrap color picker -->
<script src="{{url('assets/admin/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>

<script src="{{ url('assets/admin/plugins/iconpicker/dist/iconpicker-1.5.0.js') }}"></script>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"></script>

<script src="{{ url('assets/admin/plugins/dropzone/min/dropzone.min.js') }}"></script>

<!-- Custom JS-->
<script src="{{ url('assets/admin/js/custom.js') }}"></script>

<script>
    var table = new DataTable('#users-datatable, #companies-datatable', {
        language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
    },
    });

    $(function () {
        $("[data-tt=tooltip]").tooltip();
    });

</script>




@yield('scripts')


</body>
</html>
