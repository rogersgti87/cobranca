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

  <!-- Dark Mode CSS -->
  <link rel="stylesheet" href="{{url('assets/admin/css/dark-mode.css')}}">

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
          <li class="nav-item d-none d-md-block">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>
        <!-- Logo para mobile -->
        <a href="{{url('/admin')}}" class="navbar-brand d-md-none" style="position: absolute; left: 50%; transform: translateX(-50%); padding: 8px 0;">
          <img src="{{url('img/logo.png')}}" alt="{{auth()->user()->company}}" style="height: 40px; max-width: 150px; width: auto; object-fit: contain;">
        </a>
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

          <!-- Company Selector -->
          @if(auth()->user()->companies()->count() > 1)
          <div class="px-3 pb-3 mb-3">
            <label class="text-sm text-muted">Empresa Ativa:</label>
            <select id="company-selector" class="form-control form-control-sm" onchange="switchCompany(this.value)">
                @foreach(auth()->user()->companies as $company)
                    <option value="{{ $company->id }}" {{ auth()->user()->current_company_id == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
            
            {{-- Avisos de certificados --}}
            @if(isset($certificatesWarnings) && count($certificatesWarnings) > 0)
              <div class="mt-2">
                @foreach($certificatesWarnings as $warning)
                  @if($warning['cert_info']['expired'])
                    <div class="alert alert-danger alert-dismissible fade show p-2 mb-1" style="font-size: 0.75rem;">
                      <button type="button" class="close p-1" data-dismiss="alert" style="font-size: 1rem; line-height: 1;">&times;</button>
                      <i class="fas fa-exclamation-triangle"></i> 
                      <strong>{{ $warning['company']->name }}</strong><br>
                      Certificado Inter <strong>EXPIRADO</strong> em {{ $warning['cert_info']['expires_at_formatted'] }}
                    </div>
                  @elseif($warning['cert_info']['expires_soon'])
                    <div class="alert alert-warning alert-dismissible fade show p-2 mb-1" style="font-size: 0.75rem;">
                      <button type="button" class="close p-1" data-dismiss="alert" style="font-size: 1rem; line-height: 1;">&times;</button>
                      <i class="fas fa-clock"></i> 
                      <strong>{{ $warning['company']->name }}</strong><br>
                      Certificado Inter expira em <strong>{{ $warning['cert_info']['days_until_expiration'] }} dias</strong> ({{ $warning['cert_info']['expires_at_formatted'] }})
                    </div>
                  @endif
                @endforeach
              </div>
            @endif
          </div>
          @else
          <div class="px-3 pb-3 mb-3 text-center">
            <small class="text-muted">
                <i class="fas fa-building"></i> 
                {{ auth()->user()->currentCompany->name ?? 'Sem empresa' }}
            </small>
            
            {{-- Avisos de certificados --}}
            @if(isset($certificatesWarnings) && count($certificatesWarnings) > 0)
              <div class="mt-2">
                @foreach($certificatesWarnings as $warning)
                  @if($warning['cert_info']['expired'])
                    <div class="alert alert-danger alert-dismissible fade show p-2 mb-1" style="font-size: 0.75rem;">
                      <button type="button" class="close p-1" data-dismiss="alert" style="font-size: 1rem; line-height: 1;">&times;</button>
                      <i class="fas fa-exclamation-triangle"></i> 
                      <strong>{{ $warning['company']->name }}</strong><br>
                      Certificado Inter <strong>EXPIRADO</strong> em {{ $warning['cert_info']['expires_at_formatted'] }}
                    </div>
                  @elseif($warning['cert_info']['expires_soon'])
                    <div class="alert alert-warning alert-dismissible fade show p-2 mb-1" style="font-size: 0.75rem;">
                      <button type="button" class="close p-1" data-dismiss="alert" style="font-size: 1rem; line-height: 1;">&times;</button>
                      <i class="fas fa-clock"></i> 
                      <strong>{{ $warning['company']->name }}</strong><br>
                      Certificado Inter expira em <strong>{{ $warning['cert_info']['days_until_expiration'] }} dias</strong> ({{ $warning['cert_info']['expires_at_formatted'] }})
                    </div>
                  @endif
                @endforeach
              </div>
            @endif
          </div>
          @endif

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

                  <!-- Menu Cadastros -->
                  <li class="nav-item has-treeview {{ in_array(Request::segment(2), ['users', 'companies', 'services', 'customers', 'suppliers', 'payable-categories']) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ in_array(Request::segment(2), ['users', 'companies', 'services', 'customers', 'suppliers', 'payable-categories']) ? 'active' : '' }}">
                      <i class="nav-icon fas fa-folder"></i>
                      <p>
                        Cadastros
                        <i class="right fas fa-angle-left"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{url('admin/users')}}" class="nav-link {{Request::segment(2) == 'users' ? 'active' : ''}}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Usuários</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{url('admin/companies')}}" class="nav-link {{Request::segment(2) == 'companies' ? 'active' : ''}}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Empresas</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{url('admin/services')}}" class="nav-link {{Request::segment(2) == 'services' ? 'active' : ''}}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Serviços</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{url('admin/customers')}}" class="nav-link {{Request::segment(2) == 'customers' ? 'active' : ''}}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Clientes</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{url('admin/suppliers')}}" class="nav-link {{Request::segment(2) == 'suppliers' ? 'active' : ''}}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Fornecedores</p>
                        </a>
                      </li>
                      @if(auth()->user()->id == 1)
                      <li class="nav-item">
                        <a href="{{url('admin/payable-categories')}}" class="nav-link {{Request::segment(2) == 'payable-categories' ? 'active' : ''}}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Categorias</p>
                        </a>
                      </li>
                      @endif
                    </ul>
                  </li>

              <li class="nav-item">
                <a href="{{url('admin/invoices')}}" class="nav-link  {{Request::segment(2) == 'invoices' ? 'active' : ''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Contas a receber</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{url('admin/payables')}}" class="nav-link  {{Request::segment(2) == 'payables' ? 'active' : ''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Contas a pagar</p>
                </a>
              </li>


              <!-- Menu Relatórios -->
              <li class="nav-item has-treeview {{ in_array(Request::segment(2), ['reports', 'receita-despesa', 'projecoes']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array(Request::segment(2), ['reports', 'receita-despesa', 'projecoes']) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-chart-bar"></i>
                  <p>
                    Relatórios
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{url('admin/reports/invoices')}}" class="nav-link {{Request::segment(3) == 'invoices' ? 'active' : ''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Contas a receber</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{url('admin/reports/payables')}}" class="nav-link {{Request::segment(3) == 'payables' ? 'active' : ''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Contas a pagar</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{url('admin/receita-despesa')}}" class="nav-link {{Request::segment(2) == 'receita-despesa' ? 'active' : ''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Receita x Despesa</p>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="{{url('admin/projecoes')}}" class="nav-link {{Request::segment(2) == 'projecoes' ? 'active' : ''}}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Projeções</p>
                    </a>
                  </li>
                </ul>
              </li>

            @if(auth()->user()->id == 1)
              <li class="nav-item">
                <a href="{{url('admin/logs')}}" class="nav-link  {{Request::segment(2) == 'logs' ? 'active' : ''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Logs</p>
                </a>
              </li>
            @endif

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
      Desenvolvido por <a target="_blank" href="https://integreai.com.br">IntegreAI</a>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {{date('Y')}} <a target="_blank" href="https://cobrancasegura.com.br">Cobrança Segura</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- Bottom Navigation Bar (Mobile) -->
<nav class="bottom-nav d-md-none">
  <div class="bottom-nav-container">
    <a href="{{ url('/admin') }}" class="bottom-nav-item {{ Request::segment(2) == null || Request::segment(2) == '' ? 'active' : '' }}" id="bottomNavHome">
      <i class="fas fa-home"></i>
      <span>Principal</span>
    </a>
    <a href="{{url('admin/invoices')}}" class="bottom-nav-item {{Request::segment(2) == 'invoices' ? 'active' : '' }}" id="bottomNavInvoices">
      <i class="fas fa-file-invoice"></i>
      <span>Contas a Receber</span>
    </a>
    <a href="#" class="bottom-nav-item bottom-nav-add" id="bottomNavAdd">
      <i class="fas fa-plus"></i>
    </a>
    <a href="{{url('admin/payables')}}" class="bottom-nav-item {{Request::segment(2) == 'payables' ? 'active' : '' }}" id="bottomNavPayables">
      <i class="fas fa-file-invoice-dollar"></i>
      <span>Contas a Pagar</span>
    </a>
  </div>
</nav>

<!-- Overlay para fechar menus -->
<div class="bottom-nav-overlay"></div>

<style>
/* Cores Globais do Projeto */
:root {
  --primary-blue: #06b8f7;
  --primary-green: #6ccb48;
  --primary-yellow: #fec911;
  --bg-white: #FFFFFF;
}

/* Fundos Brancos */
body {
  background-color: #FFFFFF !important;
}

.main-sidebar,
.sidebar,
.content-wrapper,
.content,
.content-header {
  background-color: #FFFFFF !important;
}

.brand-link {
  background-color: #FFFFFF !important;
}

.brand-link.bg-gray {
  background-color: #FFFFFF !important;
}

.main-header {
  background-color: #FFFFFF !important;
}

.navbar-white {
  background-color: #FFFFFF !important;
}

.main-footer {
  background-color: #FFFFFF !important;
}

.modal-content,
.modal-header,
.modal-body,
.modal-footer {
  background-color: #FFFFFF !important;
}

/* Garantir fundo branco em todas as tabelas - Sobrescrever dark-mode.css */
.table,
.table tbody,
.table tbody tr,
.table tbody td,
.table tbody tr td,
.tbodyCustom,
.tbodyCustom tr,
.tbodyCustom tr td {
  background-color: #FFFFFF !important;
}

.table-striped,
.table-striped tbody,
.table-striped tbody tr,
.table-striped tbody tr:nth-of-type(odd),
.table-striped tbody tr:nth-of-type(even),
.table-striped tbody tr:nth-of-type(odd) td,
.table-striped tbody tr:nth-of-type(even) td {
  background-color: #FFFFFF !important;
}

.table-hover tbody tr:hover,
.table-hover tbody tr:hover td {
  background-color: rgba(6, 184, 247, 0.05) !important;
}

.card,
.card-body,
.card-box,
.collapse .card-body,
.collapse .card,
div[class*="collapse"] .card-body {
  background-color: #FFFFFF !important;
}

/* Sobrescrever especificamente o dark-mode.css com maior especificidade */
body .table,
body .table tbody,
body .table tbody tr,
body .table tbody td,
body .table tbody tr td,
body .table-striped,
body .table-striped tbody,
body .table-striped tbody tr,
body .table-striped tbody tr:nth-of-type(even),
body .table-striped tbody tr:nth-of-type(odd),
body .table-striped tbody tr:nth-of-type(even) td,
body .table-striped tbody tr:nth-of-type(odd) td {
  background-color: #FFFFFF !important;
}

/* Sobrescrever card do dark-mode.css */
body .card,
body .card-body,
body .card-box {
  background-color: #FFFFFF !important;
}

/* Sobrescrever collapse cards */
body .collapse .card,
body .collapse .card-body,
body div[class*="collapse"] .card,
body div[class*="collapse"] .card-body,
body div[id*="collapse"] .card,
body div[id*="collapse"] .card-body {
  background-color: #FFFFFF !important;
}

/* Sobrescrever dark-mode.css com máxima especificidade */
.content-wrapper .table,
.content-wrapper .table tbody,
.content-wrapper .table tbody tr,
.content-wrapper .table tbody td,
.content-wrapper .table-striped tbody tr:nth-of-type(even),
.content-wrapper .table-striped tbody tr:nth-of-type(odd),
.content-wrapper .card,
.content-wrapper .card-body,
.content-wrapper .card-box {
  background-color: #FFFFFF !important;
}

/* Sobrescrever cores amarelas do Bootstrap */
.btn-warning {
  background-color: #06b8f7 !important;
  border-color: #06b8f7 !important;
  color: #FFFFFF !important;
}

.btn-warning:hover,
.btn-warning:focus,
.btn-warning:active {
  background-color: #06b8f7 !important;
  border-color: #06b8f7 !important;
  color: #FFFFFF !important;
  opacity: 0.9;
}

.badge-warning {
  background-color: #06b8f7 !important;
  color: #FFFFFF !important;
}

.text-warning {
  color: #06b8f7 !important;
}

/* Garantir que botões Novo e Editar sejam azuis */
.btn-secondary,
.btn-secondary:hover,
.btn-secondary:focus,
.btn-secondary:active {
  background-color: #06b8f7 !important;
  border-color: #06b8f7 !important;
  color: #FFFFFF !important;
}

.btn-primary,
.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active {
  background-color: #06b8f7 !important;
  border-color: #06b8f7 !important;
  color: #FFFFFF !important;
}

.btn-primary.btn-xs,
.btn-secondary.btn-sm {
  background-color: #06b8f7 !important;
  border-color: #06b8f7 !important;
  color: #FFFFFF !important;
}

/* Garantir que fieldsets e legendas sejam azuis - Sobrescrever custom.css e dark-mode.css */
fieldset {
  border-color: rgba(6,184,247,0.5) !important;
}

legend {
  color: #06b8f7 !important;
  background-color: transparent !important;
  border: 1px solid #06b8f7 !important;
  padding: 0 10px !important;
  position: relative !important;
  top: -12px !important;
  margin-bottom: -12px !important;
}

/* Checkboxes azuis */
input[type="checkbox"],
input[type="checkbox"]:checked,
input[type="checkbox"]:focus {
  accent-color: #06b8f7 !important;
  cursor: pointer;
}

/* Checkbox customizado (containerchekbox) - Sobrescrever custom.css e dark-mode.css */
.containerchekbox .checkmark {
  border-color: #06b8f7 !important;
  background-color: #FFFFFF !important;
}

.containerchekbox:hover input ~ .checkmark {
  background-color: rgba(6,184,247,0.1) !important;
  border-color: #06b8f7 !important;
}

.containerchekbox input[type="checkbox"]:checked ~ .checkmark {
  background-color: #06b8f7 !important;
  border-color: #06b8f7 !important;
}

.containerchekbox input[type="checkbox"]:checked ~ .checkmark:after {
  border-color: #FFFFFF !important;
}

/* Sobrescrever dark-mode.css com maior especificidade */
body .containerchekbox .checkmark {
  border-color: #06b8f7 !important;
  background-color: #FFFFFF !important;
}

body .containerchekbox:hover input ~ .checkmark {
  background-color: rgba(6,184,247,0.1) !important;
  border-color: #06b8f7 !important;
}

body .containerchekbox input:checked ~ .checkmark {
  background-color: #06b8f7 !important;
  border-color: #06b8f7 !important;
}

body fieldset {
  border-color: rgba(6,184,247,0.5) !important;
}

body legend {
  color: #06b8f7 !important;
  background-color: transparent !important;
  border: 1px solid #06b8f7 !important;
  padding: 0 10px !important;
  position: relative !important;
  top: -12px !important;
  margin-bottom: -12px !important;
}

/* Nav-tabs com texto preto - Sobrescrever dark-mode.css */
.nav-tabs .nav-link,
.nav-tabs .nav-link:hover,
.nav-tabs .nav-link:focus,
.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
  color: #000000 !important;
}

body .nav-tabs .nav-link,
body .nav-tabs .nav-link:hover,
body .nav-tabs .nav-link:focus,
body .nav-tabs .nav-link.active,
body .nav-tabs .nav-item.show .nav-link {
  color: #000000 !important;
}

.sidebar-light-primary .nav-sidebar > .nav-item > .nav-link.active {
  background-color: var(--primary-blue) !important;
  color: #FFFFFF !important;
}

.sidebar-light-primary .nav-sidebar > .nav-item > .nav-link:hover {
  background-color: rgba(6, 184, 247, 0.1) !important;
  color: var(--primary-blue) !important;
}

.sidebar-light-primary .nav-sidebar > .nav-item > .nav-link {
  color: #333333 !important;
}

.sidebar-light-primary .nav-sidebar > .nav-item > .nav-link i {
  color: var(--primary-blue) !important;
}

.sidebar-light-primary .nav-sidebar > .nav-item > .nav-link.active i {
  color: #FFFFFF !important;
}

/* Bottom Navigation Bar Styles */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #FFFFFF;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
  z-index: 1030;
  padding: 0;
  height: 65px;
  display: flex;
  align-items: center;
  justify-content: center;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
}

.bottom-nav-container {
  display: flex;
  width: 100%;
  max-width: 100%;
  justify-content: space-around;
  align-items: center;
  padding: 0 10px;
  height: 100%;
}

.bottom-nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: #000000 !important;
  font-size: 10px;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 8px;
  transition: all 0.3s ease;
  min-width: 60px;
  position: relative;
  flex: 1;
  max-width: 80px;
}

.bottom-nav-item i {
  font-size: 20px;
  margin-bottom: 4px;
  transition: all 0.3s ease;
  color: #000000 !important;
}

.bottom-nav-item span {
  font-size: 9px;
  line-height: 1.2;
  text-align: center;
  white-space: normal;
  word-break: break-word;
  max-width: 100%;
  padding: 0 2px;
  color: #000000 !important;
}

.bottom-nav-item:hover {
  color: #000000 !important;
  background-color: rgba(0, 0, 0, 0.1);
  text-decoration: none;
}

.bottom-nav-item:hover span {
  color: #000000 !important;
}

.bottom-nav-item:hover i {
  color: #000000 !important;
}

.bottom-nav-item.active {
  color: #000000 !important;
  background-color: rgba(0, 0, 0, 0.15);
}

.bottom-nav-item.active span {
  color: #000000 !important;
}

.bottom-nav-item.active i {
  color: #000000 !important;
}

/* Botão central especial (Add) */
.bottom-nav-add {
  background: #06b8f7;
  color: #FFFFFF !important;
  border-radius: 50%;
  width: 56px;
  height: 56px;
  min-width: 56px;
  max-width: 56px;
  margin: 0 8px;
  box-shadow: 0 4px 12px rgba(6, 184, 247, 0.4);
  position: relative;
  top: -12px;
  flex: 0 0 auto;
  border: 2px solid #06b8f7;
}

.bottom-nav-add i {
  font-size: 24px;
  margin-bottom: 0;
  color: #FFFFFF !important;
}

.bottom-nav-add span {
  display: none;
}

.bottom-nav-add:hover {
  background: #06b8f7;
  box-shadow: 0 6px 16px rgba(6, 184, 247, 0.5);
  transform: translateY(-2px);
  color: #FFFFFF !important;
  border-color: #06b8f7;
  opacity: 0.9;
}

.bottom-nav-add:hover i {
  color: #FFFFFF !important;
}

/* Ajustar conteúdo para não ficar escondido atrás da barra inferior */
@media (max-width: 767.98px) {
  .content-wrapper {
    padding-bottom: 80px !important;
    margin-left: 0 !important;
  }

  .content {
    padding-bottom: 20px;
  }

  .main-footer {
    margin-bottom: 70px !important;
    margin-left: 0 !important;
  }

  /* Ocultar sidebar em mobile por padrão e desabilitar toggle */
  .main-sidebar {
    transform: translateX(-100%) !important;
    transition: transform 0.3s ease;
    position: fixed;
    z-index: 1040;
  }

  .sidebar-open .main-sidebar,
  .sidebar-open .main-sidebar.show {
    transform: translateX(-100%) !important;
  }

  /* Desabilitar o botão hambúrguer em mobile */
  .main-header .nav-link[data-widget="pushmenu"] {
    display: none !important;
  }

  /* Ajustar navbar superior */
  .main-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1031;
    margin-left: 0 !important;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 57px;
  }

  /* Logo na navbar mobile */
  .main-header .navbar-brand.d-md-none {
    display: flex !important;
    align-items: center;
    justify-content: center;
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    padding: 8px 0;
    z-index: 1;
  }

  .main-header .navbar-brand.d-md-none img {
    height: 40px;
    max-width: 150px;
    width: auto;
    object-fit: contain;
    display: block;
  }

  /* Garantir que navbar-nav não interfira */
  .main-header .navbar-nav {
    flex-direction: row;
  }

  .content-wrapper {
    margin-top: 57px;
  }

  /* Ocultar título e breadcrumbs em mobile */
  .content-header {
    display: none !important;
  }

  /* Ajustar margem do conteúdo quando não há header */
  .content {
    padding-top: 15px !important;
  }

  /* Reduzir espaçamento lateral em mobile */
  .container-fluid {
    padding-left: 8px !important;
    padding-right: 8px !important;
  }

  /* Reduzir padding dos cards e widgets em mobile */
  .card,
  .dashboard-card-modern,
  .widget-card,
  .table-panel,
  .chart-panel {
    margin-left: 0 !important;
    margin-right: 0 !important;
  }

  /* Reduzir padding interno dos cards em mobile */
  .card-body,
  .dashboard-card-body {
    padding-left: 12px !important;
    padding-right: 12px !important;
  }

  /* Ajustar row para não ter margem negativa */
  .row {
    margin-left: -4px !important;
    margin-right: -4px !important;
  }

  .row > * {
    padding-left: 4px !important;
    padding-right: 4px !important;
  }

  /* Garantir que modais não fiquem atrás da barra */
  .modal {
    z-index: 1055;
  }

  .modal-backdrop {
    z-index: 1050;
  }
}


/* Menu de adicionar (dropdown do botão central) - Agora contém todos os menus */
.bottom-nav-add-menu {
  position: fixed;
  bottom: 80px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #FFFFFF;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  padding: 8px;
  min-width: 280px;
  max-width: 90vw;
  max-height: 70vh;
  overflow-y: auto;
  display: none;
  z-index: 1032;
}

.bottom-nav-add-menu.show {
  display: block;
}

.bottom-nav-add-menu::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 8px solid #FFFFFF;
}

.bottom-nav-add-menu a {
  display: flex;
  align-items: center;
  padding: 12px 15px;
  color: #000000 !important;
  text-decoration: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  transition: background-color 0.2s;
}

.bottom-nav-add-menu a:hover {
  background-color: #FFFFFF;
  text-decoration: none;
}

.bottom-nav-add-menu a i {
  margin-right: 12px;
  width: 20px;
  font-size: 18px;
  color: #06b8f7;
}

.bottom-nav-add-menu strong {
  color: #000000 !important;
}

.bottom-nav-add-menu a,
.bottom-nav-add-menu a * {
  color: #000000 !important;
}

.bottom-nav-add-menu a i {
  color: #06b8f7 !important;
}

/* Overlay para fechar menus ao clicar fora */
.bottom-nav-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.3);
  z-index: 1029;
  display: none;
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
}

.bottom-nav-overlay.show {
  display: block;
}

/* Animações suaves */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.bottom-nav-add-menu.show {
  animation: fadeInUp 0.3s ease;
}

/* Scrollbar customizada para o menu */
.bottom-nav-add-menu::-webkit-scrollbar {
  width: 6px;
}

.bottom-nav-add-menu::-webkit-scrollbar-track {
  background: #FFFFFF;
  border-radius: 3px;
}

.bottom-nav-add-menu::-webkit-scrollbar-thumb {
  background: #D1D5DB;
  border-radius: 3px;
}

.bottom-nav-add-menu::-webkit-scrollbar-thumb:hover {
  background: #9CA3AF;
}

/* Ajustes para tablets */
@media (min-width: 768px) and (max-width: 991.98px) {
  .bottom-nav {
    display: none;
  }
}

/* Melhorias de acessibilidade e usabilidade */
@media (max-width: 767.98px) {
  /* Garantir que botões sejam grandes o suficiente para toque */
  .bottom-nav-item {
    min-height: 44px;
    min-width: 44px;
  }

  /* Melhorar contraste e visibilidade */
  .bottom-nav-item:active {
    background-color: rgba(0, 0, 0, 0.15);
    transform: scale(0.95);
    color: #000000 !important;
  }

  .bottom-nav-item:active span {
    color: #000000 !important;
  }

  .bottom-nav-item:active i {
    color: #000000 !important;
  }

  /* Prevenir scroll horizontal */
  body {
    overflow-x: hidden;
  }

  /* Ajustar espaçamento do conteúdo principal */
  .content-wrapper > .content {
    padding: 15px;
  }
}
</style>

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

<script>
// Script da barra de navegação inferior (mobile)
$(document).ready(function() {
  // Menu Adicionar (botão central) - Agora contém todos os menus
  $('#bottomNavAdd').on('click', function(e) {
    e.preventDefault();
    var menu = $('.bottom-nav-add-menu');
    if (menu.length === 0) {
      var menuHtml = `
        <div class="bottom-nav-add-menu">
          <div style="padding: 8px 12px; border-bottom: 1px solid rgba(0,0,0,0.1); margin-bottom: 8px;">
            <strong style="color: #000000 !important; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Cadastros</strong>
          </div>
          <a href="{{url('admin/services')}}">
            <i class="fas fa-briefcase"></i> Serviços
          </a>
          <a href="{{url('admin/customers')}}">
            <i class="fas fa-users"></i> Clientes
          </a>
          <a href="{{url('admin/suppliers')}}">
            <i class="fas fa-truck"></i> Fornecedores
          </a>
          <a href="{{url('admin/users')}}">
            <i class="fas fa-user-cog"></i> Usuários
          </a>
          <a href="{{url('admin/companies')}}">
            <i class="fas fa-building"></i> Empresas
          </a>
          <a href="{{url('admin/payable-categories')}}">
            <i class="fas fa-tags"></i> Categorias
          </a>
          <div style="padding: 8px 12px; border-top: 1px solid rgba(0,0,0,0.1); border-bottom: 1px solid rgba(0,0,0,0.1); margin: 8px 0;">
            <strong style="color: #000000 !important; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Relatórios</strong>
          </div>
          <a href="{{url('admin/reports/payables')}}">
            <i class="fas fa-chart-pie"></i> Relatório Contas a Pagar
          </a>
          <a href="{{url('admin/reports/invoices')}}">
            <i class="fas fa-chart-bar"></i> Relatório Contas a Receber
          </a>
          <a href="{{url('admin/receita-despesa')}}">
            <i class="fas fa-chart-line"></i> Receita x Despesa
          </a>
          <a href="{{url('admin/projecoes')}}">
            <i class="fas fa-project-diagram"></i> Projeções
          </a>
          @if(auth()->user()->id == 1)
          <div style="padding: 8px 12px; border-top: 1px solid rgba(0,0,0,0.1); margin-top: 8px;">
            <strong style="color: #000000 !important; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Sistema</strong>
          </div>
          <a href="{{url('admin/logs')}}">
            <i class="fas fa-file-alt"></i> Logs
          </a>
          @endif
        </div>
      `;
      $('body').append(menuHtml);
      menu = $('.bottom-nav-add-menu');
    }

    // Verificar se o menu já está aberto
    if (menu.hasClass('show')) {
      // Se estiver aberto, fechar
      $('.bottom-nav-overlay').removeClass('show');
      menu.removeClass('show');
    } else {
      // Se estiver fechado, abrir
      $('.bottom-nav-overlay').addClass('show');
      menu.addClass('show');
    }
  });

  // Fechar menus ao clicar no overlay
  $(document).on('click', '.bottom-nav-overlay', function() {
    $('.bottom-nav-overlay').removeClass('show');
    $('.bottom-nav-add-menu').removeClass('show');
  });

  // Fechar menus ao clicar em um item do menu
  $(document).on('click', '.bottom-nav-add-menu a', function() {
    setTimeout(function() {
      $('.bottom-nav-overlay').removeClass('show');
      $('.bottom-nav-add-menu').removeClass('show');
    }, 100);
  });

  // Atualizar estado ativo baseado na URL atual
  function updateBottomNavActive() {
    var currentPath = window.location.pathname;
    var segment2 = currentPath.split('/')[2] || '';

    // Remover active de todos
    $('.bottom-nav-item').removeClass('active');

    // Adicionar active baseado na rota
    if (segment2 === '' || segment2 === 'admin' || currentPath === '/admin' || currentPath === '/admin/') {
      $('#bottomNavHome').addClass('active');
    } else if (segment2 === 'invoices') {
      $('#bottomNavInvoices').addClass('active');
    } else if (segment2 === 'payables') {
      $('#bottomNavPayables').addClass('active');
    }
  }

  // Atualizar ao carregar a página
  updateBottomNavActive();

  // Atualizar ao navegar (para SPAs ou mudanças de rota)
  $(window).on('popstate', function() {
    updateBottomNavActive();
  });
});

// Função para trocar de empresa
function switchCompany(companyId) {
    if (!companyId) return;
    
    $.ajax({
        url: '{{ url("admin/companies") }}/' + companyId + '/switch',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            location.reload();
        },
        error: function() {
            alert('Erro ao trocar empresa. Tente novamente.');
        }
    });
}
</script>

@yield('scripts')


</body>
</html>
