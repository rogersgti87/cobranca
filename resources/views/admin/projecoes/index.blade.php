@extends('layouts.admin')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0" style="color: #1F2937; font-weight: 600;">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right" style="background-color: transparent; padding: 0;">
              <li class="breadcrumb-item"><a href="{{url('admin')}}" style="color: #1F2937; text-decoration: none; opacity: 0.7;">Home</a></li>
              <li class="breadcrumb-item"><a href="#" style="color: #1F2937; text-decoration: none; opacity: 0.7;">Relatórios</a></li>
              <li class="breadcrumb-item active" style="color: #1F2937; opacity: 0.7;">Projeções</li>
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

        <div class="col-md-12 mb-4">
         <div class="row">
              <div class="col-md-4 col-6 mb-3">
                <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Total Projetado</p>
                            <h5 id="total_currency" style="color: #1F2937; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_count" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 projeções</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(6,184,247,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-project-diagram" style="color: #06b8f7; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-6 mb-3">
                <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Média Mensal</p>
                            <h5 id="media_mensal" style="color: #06b8f7; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="meses_projetados" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 meses</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(6,184,247,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-chart-line" style="color: #06b8f7; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-6 mb-3">
                <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Período</p>
                            <h5 id="periodo_text" style="color: #22C55E; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">12 meses</h5>
                            <p style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">Projeções futuras</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(34,197,94,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt" style="color: #22C55E; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        </div>

        <div class="col-md-12">
            <div class="row">
                <!-- Sidebar de Filtros -->
                <div class="col-lg-3 col-md-12 mb-4">
                    <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: sticky; top: 20px;">
                        <!-- Botão para abrir/fechar filtros em mobile -->
                        <div class="d-lg-none mb-3">
                            <button class="btn btn-block" type="button" data-toggle="collapse" data-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse" style="background-color: #06b8f7; color: #FFFFFF; border: none; padding: 10px 15px; border-radius: 6px; font-weight: 600; font-size: 14px; display: flex; align-items: center; justify-content: space-between;">
                                <span style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fa fa-filter"></i> Filtros
                                </span>
                                <i class="fa fa-chevron-down" id="filterToggleIcon"></i>
                            </button>
                        </div>

                        <!-- Título dos filtros (visível apenas em desktop) -->
                        <h5 class="d-none d-lg-block" style="color: #1F2937; font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 14px;">
                            <i class="fa fa-filter"></i> Filtros
                        </h5>

                        <!-- Conteúdo dos filtros (collapse em mobile, sempre visível em desktop) -->
                        <div class="collapse d-lg-block" id="filtersCollapse">
                            <!-- Período de Projeção -->
                            <div class="mb-4">
                                <label style="color: #1F2937; font-weight: 500; font-size: 12px; margin-bottom: 10px; display: block;">Período de Projeção</label>
                                <div class="d-flex flex-column" style="gap: 8px;">
                                    <button class="btn btn-sm filter-quick-btn" type="button" data-months="current-month" id="btn-current-month" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                        <i class="fa fa-calendar"></i> <span class="month-name"></span>
                                    </button>
                                    <button class="btn btn-sm filter-quick-btn" type="button" data-months="next-month" id="btn-next-month" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                        <i class="fa fa-calendar-alt"></i> <span class="month-name"></span>
                                    </button>
                                    <button class="btn btn-sm filter-quick-btn" type="button" data-months="3" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                        <i class="fa fa-calendar-week"></i> 3 Meses
                                    </button>
                                    <button class="btn btn-sm filter-quick-btn" type="button" data-months="6" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                        <i class="fa fa-calendar-day"></i> 6 Meses
                                    </button>
                                    <button class="btn btn-sm filter-quick-btn active" type="button" data-months="12" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                        <i class="fa fa-calendar-check"></i> 12 Meses
                                    </button>
                                    <button class="btn btn-sm filter-quick-btn" type="button" data-months="24" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 10px 16px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left;">
                                        <i class="fa fa-calendar-plus"></i> 24 Meses
                                    </button>
                                </div>
                            </div>

                            <!-- Campo hidden para armazenar o valor e tipo de filtro -->
                            <input type="hidden" id="filter-months-ahead" value="12" data-filter-type="12">

                            <!-- Período de Recorrência -->
                            <div class="mb-4">
                                <label style="color: #1F2937; font-weight: 500; font-size: 12px; margin-bottom: 10px; display: block;">Período de Recorrência</label>
                                <div class="d-flex flex-column" style="gap: 8px;">
                                    <label style="color: #1F2937; font-weight: 400; font-size: 13px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="recurrence-checkbox" value="Semanal" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        Semanal
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 13px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="recurrence-checkbox" value="Quinzenal" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        Quinzenal
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 13px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="recurrence-checkbox" value="Mensal" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        Mensal
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 13px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="recurrence-checkbox" value="Bimestral" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        Bimestral
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 13px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="recurrence-checkbox" value="Trimestral" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        Trimestral
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 13px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="recurrence-checkbox" value="Semestral" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        Semestral
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 13px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="recurrence-checkbox" value="Anual" style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        Anual
                                    </label>
                                </div>
                            </div>

                            <!-- Categorias -->
                            <div class="mb-4">
                                <label style="color: #1F2937; font-weight: 500; font-size: 12px; margin-bottom: 10px; display: block;">Categorias</label>
                                <div class="mb-2">
                                    <label style="color: #1F2937; font-weight: 600; font-size: 12px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="category-checkbox" id="selectAllCategories" style="margin-right: 6px; width: 16px; height: 16px; cursor: pointer;">
                                        Selecionar Todas
                                    </label>
                                </div>
                                <div class="d-flex flex-column" style="gap: 6px; max-height: 200px; overflow-y: auto;">
                                    @if(isset($categories) && count($categories) > 0)
                                        @foreach($categories as $category)
                                            <label style="color: #1F2937; font-weight: 400; font-size: 12px; cursor: pointer; display: flex; align-items: center;">
                                                <input type="checkbox" class="category-checkbox" value="{{ $category->id }}" style="margin-right: 6px; width: 16px; height: 16px; cursor: pointer;">
                                                <span style="display: inline-block; width: 10px; height: 10px; background-color: {{ $category->color }}; border-radius: 2px; margin-right: 6px;"></span>
                                                {{ $category->name }}
                                            </label>
                                        @endforeach
                                    @else
                                        <p style="color: #6B7280; font-size: 12px; margin: 0;">Nenhuma categoria cadastrada</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Fornecedores -->
                            <div class="mb-4">
                                <label style="color: #1F2937; font-weight: 500; font-size: 12px; margin-bottom: 10px; display: block;">Fornecedores</label>
                                <div class="mb-2">
                                    <label style="color: #1F2937; font-weight: 600; font-size: 12px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="supplier-checkbox" id="selectAllSuppliers" style="margin-right: 6px; width: 16px; height: 16px; cursor: pointer;">
                                        Selecionar Todos
                                    </label>
                                </div>
                                <div class="d-flex flex-column" style="gap: 6px; max-height: 200px; overflow-y: auto;">
                                    @if(isset($suppliers) && count($suppliers) > 0)
                                        @foreach($suppliers as $supplier)
                                            <label style="color: #1F2937; font-weight: 400; font-size: 12px; cursor: pointer; display: flex; align-items: center;">
                                                <input type="checkbox" class="supplier-checkbox" value="{{ $supplier->id }}" style="margin-right: 6px; width: 16px; height: 16px; cursor: pointer;">
                                                {{ $supplier->name }}
                                            </label>
                                        @endforeach
                                    @else
                                        <p style="color: #6B7280; font-size: 12px; margin: 0;">Nenhum fornecedor cadastrado</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo Principal -->
                <div class="col-lg-9 col-md-12">
                    <div class="d-flex justify-content-end mb-4" style="gap: 10px;">
                        <button type="button" id="btn-export-pdf" class="btn" style="background-color: #F87171; color: #FFFFFF !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
                            <i class="fa fa-file-pdf"></i> Exportar PDF
                        </button>
                        <button type="button" id="btn-export-excel" class="btn" style="background-color: #22C55E; color: #FFFFFF !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
                            <i class="fa fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>

                    <div class="mb-4">
                        <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <h5 style="color: #1F2937; font-weight: 600; margin-bottom: 20px;">
                                <i class="fas fa-chart-bar"></i> Total por Mês
                            </h5>
                            <div style="position: relative; height: 300px;">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <h5 style="color: #1F2937; font-weight: 600; margin-bottom: 20px;">
                                <i class="fas fa-list"></i> Detalhamento das Projeções
                            </h5>
                            <div class="table-responsive">
                                <table class="table" id="projections-table" style="margin-bottom: 0;">
                                    <thead>
                                    <tr style="border-bottom: 2px solid rgba(0,0,0,0.1);">
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">#</th>
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Fornecedor</th>
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Descrição</th>
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Categoria</th>
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Recorrência</th>
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Vencimento</th>
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Valor</th>
                                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Forma de Pagamento</th>
                                    </tr>
                                    </thead>

                                    <tbody id="list-projections" style="background-color: #FFFFFF;">
                                        <tr>
                                            <td colspan="8" class="text-center" style="padding: 40px; color: #6B7280;">
                                                <i class="fas fa-project-diagram" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                                <p style="margin: 0; font-size: 16px;">Os dados serão carregados automaticamente quando você alterar os filtros</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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

@endsection

@section('styles')
<style>
    /* Checkboxes azuis */
    input[type="checkbox"],
    input[type="checkbox"]:checked,
    input[type="checkbox"]:focus {
        accent-color: #06b8f7 !important;
        cursor: pointer;
    }

    /* Garantir que fieldsets e legendas sejam azuis */
    fieldset {
        border-color: rgba(6,184,247,0.5) !important;
    }

    legend {
        color: #06b8f7 !important;
    }

    .filter-quick-btn.active {
        background-color: #06b8f7 !important;
        color: #FFFFFF !important;
        border-color: #06b8f7 !important;
    }

    /* Responsividade Mobile */
    @media (max-width: 767.98px) {
        .content-header h1,
        .content-header .breadcrumb {
            font-size: 14px;
        }

        .col-md-4.col-6 {
            margin-bottom: 15px;
        }

        .col-md-12.mb-4 > div {
            padding: 15px !important;
        }

        fieldset {
            padding: 10px !important;
            margin-bottom: 15px !important;
        }

        legend {
            font-size: 12px !important;
        }

        .d-flex[style*="gap: 20px"] {
            flex-wrap: wrap !important;
            gap: 10px !important;
        }

        .d-flex[style*="gap: 8px"] {
            flex-wrap: wrap !important;
            gap: 5px !important;
        }

        .filter-quick-btn {
            font-size: 12px !important;
            padding: 6px 12px !important;
        }

        .btn {
            font-size: 12px !important;
            padding: 8px 16px !important;
        }

        /* Garantir que o collapse funcione bem em mobile */
        .collapse {
            transition: all 0.3s ease;
        }

        .collapse.show {
            display: block !important;
        }

        /* Ajustar ícone do collapse */
        #filterToggleIcon {
            transition: transform 0.3s;
        }

        [aria-expanded="true"] #filterToggleIcon {
            transform: rotate(180deg);
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let projectionsData = [];
let generateProjectionsTimeout;
let monthlyChart = null;
let currentFilterType = '12'; // Variável global para armazenar o filtro selecionado

$(document).ready(function() {
    // Atualizar nomes dos meses nos botões
    function updateMonthNames() {
        const now = new Date();
        const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                       'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        const currentMonth = months[now.getMonth()];
        const nextMonth = months[(now.getMonth() + 1) % 12];

        $('#btn-current-month .month-name').text(currentMonth);
        $('#btn-next-month .month-name').text(nextMonth);
    }

    updateMonthNames();

    // Garantir que o botão "12 Meses" fique selecionado por padrão
    // IMPORTANTE: Remover estilos inline de todos os botões primeiro
    $('.filter-quick-btn').each(function() {
        $(this).removeClass('active');
        $(this).css({
            'background-color': '',
            'color': '',
            'border-color': ''
        });
    });
    const defaultButton = $('.filter-quick-btn[data-months="12"]');
    defaultButton.addClass('active');
    // Aplicar explicitamente os estilos do botão ativo
    defaultButton.css({
        'background-color': '#06b8f7',
        'color': '#FFFFFF',
        'border-color': '#06b8f7'
    });
    $('#filter-months-ahead').val('12');
    $('#filter-months-ahead').data('filter-type', '12');
    currentFilterType = '12'; // Inicializar variável global

    // Selecionar todas as categorias
    $('#selectAllCategories').on('change', function() {
        $('.category-checkbox').not('#selectAllCategories').prop('checked', $(this).prop('checked'));
        autoGenerateProjections();
    });

    // Selecionar todos os fornecedores
    $('#selectAllSuppliers').on('change', function() {
        $('.supplier-checkbox').not('#selectAllSuppliers').prop('checked', $(this).prop('checked'));
        autoGenerateProjections();
    });

    // Não precisa mais do evento de change no input, pois ele é hidden

    $('.recurrence-checkbox, .category-checkbox, .supplier-checkbox').on('change', function() {
        autoGenerateProjections();
    });

    // Filtros rápidos de meses
    $('.filter-quick-btn').on('click', function(e) {
        e.preventDefault();
        const months = $(this).data('months');

        // Atualizar variável global PRIMEIRO
        currentFilterType = months;

        // Remover active de todos e adicionar no clicado
        // IMPORTANTE: Também remover estilos inline que podem estar sobrescrevendo o CSS
        $('.filter-quick-btn').each(function() {
            $(this).removeClass('active');
            // Remover apenas os estilos que interferem, mantendo padding, border-radius, etc
            $(this).css({
                'background-color': '',
                'color': '',
                'border-color': ''
            });
        });
        $(this).addClass('active');
        // Aplicar explicitamente os estilos do botão ativo para garantir que sejam visíveis
        $(this).css({
            'background-color': '#06b8f7',
            'color': '#FFFFFF',
            'border-color': '#06b8f7'
        });

        // Armazenar o tipo de filtro selecionado no campo hidden também
        $('#filter-months-ahead').data('filter-type', months);

        // Para botões numéricos, definir o valor também
        if(months !== 'current-month' && months !== 'next-month') {
            $('#filter-months-ahead').val(parseInt(months) || 12);
        } else {
            // Para mês atual e próximo mês, não usar meses à frente, usar filtro especial
            $('#filter-months-ahead').val('');
        }

        // Gerar as projeções imediatamente
        autoGenerateProjections();
    });

    // Gerar projeções automaticamente ao carregar a página
    autoGenerateProjections();

    // Exportar PDF
    $('#btn-export-pdf').on('click', function() {
        if(projectionsData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Não há dados para exportar!'
            });
            return;
        }
        exportToPDF();
    });

    // Exportar Excel
    $('#btn-export-excel').on('click', function() {
        if(projectionsData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Não há dados para exportar!'
            });
            return;
        }
        exportToExcel();
    });
});

function updateQuickFilterButtons() {
    const monthsValue = parseInt($('#filter-months-ahead').val()) || 12;

    $('.filter-quick-btn').removeClass('active');

    // Calcular valores para mês atual e próximo mês
    const now = new Date();
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    const daysRemaining = lastDay.getDate() - now.getDate() + 1;
    const currentMonthValue = Math.max(1, Math.ceil(daysRemaining / 30));

    const nextMonthLastDay = new Date(now.getFullYear(), now.getMonth() + 2, 0);
    const daysUntilNextMonthEnd = Math.ceil((nextMonthLastDay - now) / (1000 * 60 * 60 * 24));
    const nextMonthValue = Math.max(1, Math.ceil(daysUntilNextMonthEnd / 30));

    // Verificar qual botão corresponde ao valor atual
    // Prioridade: botões numéricos exatos primeiro
    let foundMatch = false;

    // Primeiro verificar botões numéricos exatos (3, 6, 12, 24)
    $('.filter-quick-btn').each(function() {
        const btnMonths = $(this).data('months');
        if(btnMonths !== 'current-month' && btnMonths !== 'next-month') {
            const btnMonthsNum = parseInt(btnMonths);
            if(btnMonthsNum === monthsValue) {
                $(this).addClass('active');
                foundMatch = true;
                return false; // break
            }
        }
    });

    // Se não encontrou match numérico exato, verificar ranges especiais
    if(!foundMatch) {
        $('.filter-quick-btn').each(function() {
            const btnMonths = $(this).data('months');

            if(btnMonths === 'current-month') {
                // Ativo se o valor está dentro do range do mês atual
                if(monthsValue > 0 && monthsValue <= currentMonthValue) {
                    $(this).addClass('active');
                    foundMatch = true;
                    return false; // break
                }
            } else if(btnMonths === 'next-month') {
                // Ativo se o valor está entre o mês atual e o próximo mês
                if(monthsValue > currentMonthValue && monthsValue <= nextMonthValue) {
                    $(this).addClass('active');
                    foundMatch = true;
                    return false; // break
                }
            }
        });
    }

    // Se ainda não encontrou match, manter o padrão (12 meses)
    if(!foundMatch) {
        $('.filter-quick-btn[data-months="12"]').addClass('active');
    }
}

function autoGenerateProjections() {
    // Debounce: aguarda 500ms após a última mudança antes de gerar
    clearTimeout(generateProjectionsTimeout);
    generateProjectionsTimeout = setTimeout(function() {
        generateProjections();
    }, 500);
}

function generateProjections() {
    // Prioridade: botão ativo > variável global > padrão
    const activeButton = $('.filter-quick-btn.active');
    let filterType = '12';

    if(activeButton.length > 0) {
        filterType = activeButton.data('months') || '12';
    } else {
        filterType = currentFilterType || '12';
    }

    const monthsValue = $('#filter-months-ahead').val();

    const filters = {
        filter_type: filterType, // current-month, next-month, ou número
        months_ahead: (filterType === 'current-month' || filterType === 'next-month') ? null : (monthsValue || 12),
        recurrence_period: [],
        category: [],
        supplier: []
    };

    $('.recurrence-checkbox:checked').each(function() {
        filters.recurrence_period.push($(this).val());
    });

    $('.category-checkbox:checked').not('#selectAllCategories').each(function() {
        if($(this).val() != 'on') {
            filters.category.push($(this).val());
        }
    });

    $('.supplier-checkbox:checked').not('#selectAllSuppliers').each(function() {
        if($(this).val() != 'on') {
            filters.supplier.push($(this).val());
        }
    });

    $.ajax({
        url: '{{ url("admin/projecoes/data") }}',
        type: 'GET',
        data: filters,
        beforeSend: function() {
            $('#list-projections').html('<tr><td colspan="8" class="text-center" style="padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Carregando...</td></tr>');
        },
        success: function(response) {
            // PRESERVAR a seleção do botão atual ANTES de processar a resposta
            // IMPORTANTE: Usar a variável global currentFilterType que foi atualizada no clique
            const currentFilterTypeValue = currentFilterType || '12';

            projectionsData = response.projections || [];
            displayProjections(response.projections || [], response.monthly_totals || [], response.total_amount || 0, response.months_ahead || 12);
            if(response.monthly_totals) {
                updateMonthlyChart(response.monthly_totals);
            }

            // RESTAURAR a seleção do botão IMEDIATAMENTE após processar a resposta
            // Não usar setTimeout, restaurar diretamente para evitar que outros códigos interfiram
            // Remover active de TODOS os botões primeiro
            // IMPORTANTE: Também remover estilos inline que podem estar sobrescrevendo o CSS
            $('.filter-quick-btn').each(function() {
                $(this).removeClass('active');
                // Remover apenas os estilos que interferem, mantendo padding, border-radius, etc
                $(this).css({
                    'background-color': '',
                    'color': '',
                    'border-color': ''
                });
            });

            // Selecionar o botão correto baseado no currentFilterType preservado
            const buttonToSelect = $('.filter-quick-btn[data-months="' + currentFilterTypeValue + '"]');
            if(buttonToSelect.length > 0) {
                buttonToSelect.addClass('active');
                // Aplicar explicitamente os estilos do botão ativo para garantir que sejam visíveis
                buttonToSelect.css({
                    'background-color': '#06b8f7',
                    'color': '#FFFFFF',
                    'border-color': '#06b8f7'
                });

                // Verificar novamente após um pequeno delay para garantir que não foi resetado
                setTimeout(function() {
                    const stillActive = $('.filter-quick-btn.active');
                    const activeMonths = stillActive.map(function() { return $(this).data('months'); }).get();
                    if(activeMonths.length === 0 || activeMonths[0] !== currentFilterTypeValue) {
                        $('.filter-quick-btn').each(function() {
                            $(this).removeClass('active');
                            $(this).css({
                                'background-color': '',
                                'color': '',
                                'border-color': ''
                            });
                        });
                        buttonToSelect.addClass('active');
                        // Aplicar explicitamente os estilos
                        buttonToSelect.css({
                            'background-color': '#06b8f7',
                            'color': '#FFFFFF',
                            'border-color': '#06b8f7'
                        });
                    }
                }, 200);
            }

            // Garantir que o currentFilterType permaneça o mesmo
            currentFilterType = currentFilterTypeValue;
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao gerar projeções! Verifique o console para mais detalhes.'
            });
        }
    });
}

function displayProjections(data, monthlyTotals, totalAmount, monthsAhead) {
    let html = '';

    if(data.length === 0) {
        html = '<tr><td colspan="8" class="text-center" style="padding: 40px; color: #6B7280;">Nenhuma projeção encontrada</td></tr>';
    } else {
        data.forEach(function(item, index) {
            html += '<tr>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (index + 1) + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (item.supplier_name || '-') + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + item.description + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">';
            if(item.category_name) {
                html += '<span style="display: inline-block; width: 12px; height: 12px; background-color: ' + (item.category_color || '#CCCCCC') + '; border-radius: 2px; margin-right: 6px;"></span>';
                html += item.category_name;
            } else {
                html += '-';
            }
            html += '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + item.recurrence_period + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + formatDateDisplay(item.date_due) + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1); font-weight: 600;">R$ ' + formatCurrency(item.price) + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (item.payment_method || '-') + '</td>';
            html += '</tr>';
        });
    }

    $('#list-projections').html(html);

    // Atualizar totais
    $('#total_currency').text('R$ ' + formatCurrency(totalAmount));
    $('#total_count').text(data.length + ' projeções');

    // Calcular média mensal
    const uniqueMonths = new Set();
    data.forEach(function(item) {
        const monthKey = item.date_due.substring(0, 7); // YYYY-MM
        uniqueMonths.add(monthKey);
    });
    const monthsCount = uniqueMonths.size || 1;
    const mediaMensal = totalAmount / monthsCount;
    $('#media_mensal').text('R$ ' + formatCurrency(mediaMensal));
    $('#meses_projetados').text(monthsCount + ' meses');

    // Atualizar texto do período baseado no filtro selecionado, não apenas no valor retornado
    let periodoText = '';
    if(currentFilterType === 'current-month') {
        const now = new Date();
        const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                       'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        periodoText = months[now.getMonth()];
    } else if(currentFilterType === 'next-month') {
        const now = new Date();
        const months = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                       'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        periodoText = months[(now.getMonth() + 1) % 12];
    } else {
        // Para filtros numéricos, usar o valor do filtro ou o retornado pelo backend
        const filterMonths = parseInt(currentFilterType) || monthsAhead || 12;
        periodoText = filterMonths + ' meses';
    }
    $('#periodo_text').text(periodoText);
}

function formatDateDisplay(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString + 'T00:00:00');
    return String(date.getDate()).padStart(2, '0') + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + date.getFullYear();
}

function formatCurrency(value) {
    return parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function exportToPDF() {
    // Coletar filtros atuais
    const filters = {
        months_ahead: $('#filter-months-ahead').val() || 12,
        recurrence_period: [],
        category: [],
        supplier: []
    };

    $('.recurrence-checkbox:checked').each(function() {
        filters.recurrence_period.push($(this).val());
    });

    $('.category-checkbox:checked').not('#selectAllCategories').each(function() {
        if($(this).val() != 'on') {
            filters.category.push($(this).val());
        }
    });

    $('.supplier-checkbox:checked').not('#selectAllSuppliers').each(function() {
        if($(this).val() != 'on') {
            filters.supplier.push($(this).val());
        }
    });

    // Construir URL com parâmetros
    const params = new URLSearchParams();
    if(filters.months_ahead) params.append('months_ahead', filters.months_ahead);
    filters.recurrence_period.forEach(rp => params.append('recurrence_period[]', rp));
    filters.category.forEach(c => params.append('category[]', c));
    filters.supplier.forEach(s => params.append('supplier[]', s));

    // Abrir PDF em nova aba
    const url = '{{ url("admin/projecoes/pdf") }}?' + params.toString();
    window.open(url, '_blank');
}

function exportToExcel() {
    // Criar CSV para Excel
    let csv = 'Fornecedor,Descrição,Categoria,Recorrência,Vencimento,Valor,Forma de Pagamento\n';

    projectionsData.forEach(function(item) {
        csv += '"' + (item.supplier_name || '') + '",';
        csv += '"' + item.description + '",';
        csv += '"' + (item.category_name || '') + '",';
        csv += '"' + item.recurrence_period + '",';
        csv += '"' + formatDateDisplay(item.date_due) + '",';
        csv += '"' + item.price + '",';
        csv += '"' + (item.payment_method || '') + '"\n';
    });

    // Criar blob e fazer download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'projecoes_futuras_' + new Date().getTime() + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function updateMonthlyChart(monthlyTotals) {
    const ctx = document.getElementById('monthlyChart');

    if (!ctx) return;

    // Destruir gráfico anterior se existir
    if (monthlyChart) {
        monthlyChart.destroy();
    }

    if (!monthlyTotals || monthlyTotals.length === 0) {
        return;
    }

    // Preparar dados
    const labels = monthlyTotals.map(item => item.month);
    const data = monthlyTotals.map(item => parseFloat(item.total));

    // Criar gráfico
    monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total por Mês',
                data: data,
                backgroundColor: 'rgba(6,184,247,0.6)',
                borderColor: '#06b8f7',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y || 0;
                            return 'Total: R$ ' + formatCurrency(value);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + formatCurrency(value);
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection
