<!DOCTYPE html>

<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Cobrança Segura</title>
  <link rel="icon" href="{{url('/img/favicon.png')}}">
  <!-- Google Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
  <!-- Font Awesome Icons -->
{{--  <link rel="stylesheet" href="{{url('assets/admin/plugins/fontawesome-free/css/all.min.css')}}">--}}
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('assets/admin/css/adminlte.min.css')}}">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{url('assets/admin/css/custom.css')}}">

  <!-- App Theme (Dark + Gold) -->
  <link rel="stylesheet" href="{{url('assets/admin/css/app-theme.css')}}?v=8">
  <link rel="stylesheet" href="{{url('assets/admin/css/dark-mode.css')}}?v=8">
  <link rel="stylesheet" href="{{url('assets/admin/css/theme-overrides.css')}}?v=8">

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

<body class="hold-transition sidebar-mini layout-fixed">
    @php
        $currentCompany = auth()->user()->currentCompany;
        $companyLogoPath = $currentCompany?->logo;
        $hasCompanyLogo = $companyLogoPath && file_exists(public_path('storage/' . $companyLogoPath));
        $companyLogoSrc = $hasCompanyLogo ? asset('storage/' . $companyLogoPath) : null;
        $systemLogoSrc = url('img/logo.png');
    @endphp
    <div class="wrapper">

     <!-- Navbar -->
     <nav class="main-header navbar navbar-expand navbar-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item d-none d-md-block">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
          </li>
        </ul>
        <!-- Logo para mobile -->
        <a href="{{url('/admin')}}" class="navbar-brand d-md-none app-logo-mobile">
          <img src="{{ $systemLogoSrc }}" alt="Cobrança Segura" class="app-logo-img">
        </a>
     </nav>

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar elevation-4 sidebar-dark-primary">
        <!-- Brand Logo — sistema -->
        <a href="{{url('/admin')}}" class="brand-link text-center">
            <img src="{{ $systemLogoSrc }}" alt="Cobrança Segura" class="app-logo-sidebar">
        </a>
        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="user-panel mt-3 pb-3 mb-3 d-flex text-center">
            <div class="info">
                @if($hasCompanyLogo)
                <div class="app-company-logo-wrap">
                    <img src="{{ $companyLogoSrc }}" alt="{{ $currentCompany->name ?? 'Empresa' }}" class="app-company-logo">
                </div>
                @endif
                <img src="{{ \Auth::user()->image != null ? url(\Auth::user()->image) : url('assets/admin/img/thumb.png')}}" class="img-thumbnail app-user-avatar" alt="{{auth()->user()->name}}">
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

<!-- Tema carregado após conteúdo para sobrescrever estilos inline das páginas -->
<link rel="stylesheet" href="{{url('assets/admin/css/app-theme.css')}}?v=8">
<link rel="stylesheet" href="{{url('assets/admin/css/theme-overrides.css')}}?v=8">

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
          <div style="padding: 8px 12px; border-bottom: 1px solid rgba(212,175,55,0.2); margin-bottom: 8px;">
            <strong style="color: #D4AF37 !important; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Cadastros</strong>
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
          <div style="padding: 8px 12px; border-top: 1px solid rgba(212,175,55,0.2); border-bottom: 1px solid rgba(212,175,55,0.2); margin: 8px 0;">
            <strong style="color: #D4AF37 !important; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Relatórios</strong>
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
          <div style="padding: 8px 12px; border-top: 1px solid rgba(212,175,55,0.2); margin-top: 8px;">
            <strong style="color: #D4AF37 !important; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Sistema</strong>
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
