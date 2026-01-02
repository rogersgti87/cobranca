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
              <li class="breadcrumb-item active" style="color: #1F2937; opacity: 0.7;">Contas a receber</li>
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
              <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Total</p>
                            <h5 id="total_currency" style="color: #1F2937; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_count" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-receipt" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Pendentes</p>
                            <h5 id="pendente_currency" style="color: #FFBD59; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="pendente_count" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-hourglass" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Recebidas</p>
                            <h5 id="pago_currency" style="color: #22C55E; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="pago_count" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(34,197,94,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="color: #22C55E; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Canceladas</p>
                            <h5 id="cancelado_currency" style="color: #F87171; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="cancelado_count" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 faturas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(248,113,113,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-times-circle" style="color: #F87171; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        </div>

        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <button class="btn" type="button" data-toggle="collapse" data-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse" style="background-color: #FFBD59; color: #1F2937 !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
                        <i class="fa fa-filter"></i> Filtros
                    </button>
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-current-month" data-filter="current-month" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-calendar"></i> Mês Atual
                        </button>
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-last-month" data-filter="last-month" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-calendar-alt"></i> Mês Anterior
                        </button>
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-current-year" data-filter="current-year" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-calendar-check"></i> Ano Atual
                        </button>
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-all" data-filter="all" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-list"></i> Todos
                        </button>
                    </div>
                </div>
                <div class="d-flex" style="gap: 10px;">
                    <button type="button" id="btn-export-pdf" class="btn" style="background-color: #F87171; color: #FFFFFF !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
                        <i class="fa fa-file-pdf"></i> Exportar PDF
                    </button>
                    <button type="button" id="btn-export-excel" class="btn" style="background-color: #22C55E; color: #FFFFFF !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;">
                        <i class="fa fa-file-excel"></i> Exportar Excel
                    </button>
                </div>
            </div>

            <div class="collapse mb-4" id="filtersCollapse">
                <div class="form-row" style="background-color: #F5F5DC; padding: 20px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1);">

                <div class="form-group col-md-4 col-6">
                    <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Tipo de Data</label>
                    <select class="form-control" id="filter-type" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;">
                        <option value="date_due">Data do Vencimento</option>
                        <option value="date_payment">Data do Recebimento</option>
                        <option value="date_invoice">Data da Fatura</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-6">
                    <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Data inicial</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data inicial" id="filter-date-ini" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;">
                </div>

                <div class="form-group col-md-4 col-6">
                    <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Data final</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data Final" id="filter-date-end" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;">
                </div>

                <div class="form-group col-md-12 mt-3">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <fieldset style="border: 1px solid rgba(255,189,89,0.5); border-radius: 8px; padding: 15px; margin: 0; background-color: #FFFFFF; position: relative;">
                                <legend style="color: #FFBD59; font-size: 14px; font-weight: 600; padding: 0 10px; margin: 0; border: none;">Forma de Pagamento</legend>
                                <div class="d-flex" style="gap: 20px; margin-top: 10px; flex-wrap: nowrap; overflow-x: auto;">
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="payment-method-checkbox" value="Pix" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Pix
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="payment-method-checkbox" value="Boleto" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Boleto
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="payment-method-checkbox" value="Cartão" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Cartão
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="payment-method-checkbox" value="Dinheiro" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Dinheiro
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="payment-method-checkbox" value="Depósito" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Depósito
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6 col-12">
                            <fieldset style="border: 1px solid rgba(255,189,89,0.5); border-radius: 8px; padding: 15px; margin: 0; background-color: #FFFFFF; position: relative;">
                                <legend style="color: #FFBD59; font-size: 14px; font-weight: 600; padding: 0 10px; margin: 0; border: none;">Status</legend>
                                <div class="d-flex" style="gap: 20px; margin-top: 10px; flex-wrap: nowrap; overflow-x: auto;">
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="status-checkbox" value="Pendente" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Pendente
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="status-checkbox" value="Pago" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Pago
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="status-checkbox" value="Expirado" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Expirado
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="status-checkbox" value="Cancelado" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Cancelado
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-12 mt-3">
                    <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Clientes</label>
                    <select class="form-control select2bs4" id="filter-customers" multiple="multiple" style="width: 100%;">
                        @if(isset($customers) && count($customers) > 0)
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            </div>
        </div>

      <div class="col-md-12 mb-4">
        <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h5 style="color: #1F2937; font-weight: 600; margin-bottom: 20px;">
                <i class="fas fa-chart-pie"></i> Total por Status
            </h5>
            <div style="position: relative; height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
      </div>

      <div class="col-md-12">
        <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h5 style="color: #1F2937; font-weight: 600; margin-bottom: 20px;">
                <i class="fas fa-list"></i> Detalhamento
            </h5>
            <div class="table-responsive">
                <table class="table" id="report-table" style="margin-bottom: 0;">
                    <thead>
                    <tr style="border-bottom: 2px solid rgba(0,0,0,0.1);">
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">#</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Cliente</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Descrição</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Serviço</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Vencimento</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Recebido em</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Valor</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Forma de Pagamento</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Status</th>
                    </tr>
                    </thead>

                    <tbody id="list-report" style="background-color: #F5F5DC;">
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 40px; color: #6B7280;">
                                <i class="fas fa-chart-bar" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                <p style="margin: 0; font-size: 16px;">Os dados serão carregados automaticamente quando você alterar os filtros</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let reportData = [];
let generateReportTimeout;
let statusChart = null;

$(document).ready(function() {
    
    // Inicializar Select2 para clientes
    $('#filter-customers').select2({
        theme: 'bootstrap4',
        placeholder: 'Selecione os clientes...',
        allowClear: true,
        language: 'pt-BR'
    });

    // Eventos de mudança automática nos filtros
    $('#filter-type, #filter-date-ini, #filter-date-end').on('change', function() {
        autoGenerateReport();
    });

    $('#filter-customers').on('change', function() {
        autoGenerateReport();
    });

    $('.payment-method-checkbox, .status-checkbox').on('change', function() {
        autoGenerateReport();
    });

    // Função para atualizar o destaque dos filtros rápidos
    function updateQuickFilterHighlight() {
        // Remover destaque de todos os botões
        $('.filter-quick-btn').css({
            'border': '1px solid rgba(0,0,0,0.1)',
            'borderWidth': '1px'
        });

        const dateIni = $('#filter-date-ini').val();
        const dateEnd = $('#filter-date-end').val();

        if (!dateIni || !dateEnd) {
            // Se não há datas, destacar "Todos"
            $('#btn-filter-all').css({
                'border': '2px solid #FFBD59',
                'borderWidth': '2px'
            });
            return;
        }

        const now = new Date();
        const currentMonthFirst = formatDate(new Date(now.getFullYear(), now.getMonth(), 1));
        const currentMonthLast = formatDate(new Date(now.getFullYear(), now.getMonth() + 1, 0));
        const lastMonthFirst = formatDate(new Date(now.getFullYear(), now.getMonth() - 1, 1));
        const lastMonthLast = formatDate(new Date(now.getFullYear(), now.getMonth(), 0));
        const currentYearFirst = formatDate(new Date(now.getFullYear(), 0, 1));
        const currentYearLast = formatDate(new Date(now.getFullYear(), 11, 31));

        if (dateIni === currentMonthFirst && dateEnd === currentMonthLast) {
            $('#btn-filter-current-month').css({
                'border': '2px solid #FFBD59',
                'borderWidth': '2px'
            });
        } else if (dateIni === lastMonthFirst && dateEnd === lastMonthLast) {
            $('#btn-filter-last-month').css({
                'border': '2px solid #FFBD59',
                'borderWidth': '2px'
            });
        } else if (dateIni === currentYearFirst && dateEnd === currentYearLast) {
            $('#btn-filter-current-year').css({
                'border': '2px solid #FFBD59',
                'borderWidth': '2px'
            });
        }
    }

    // Filtros rápidos
    $('#btn-filter-current-month').on('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        
        $('#filter-date-ini').val(formatDate(firstDay));
        $('#filter-date-end').val(formatDate(lastDay));
        updateQuickFilterHighlight();
        autoGenerateReport();
    });

    $('#btn-filter-last-month').on('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
        
        $('#filter-date-ini').val(formatDate(firstDay));
        $('#filter-date-end').val(formatDate(lastDay));
        updateQuickFilterHighlight();
        autoGenerateReport();
    });

    $('#btn-filter-current-year').on('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), 0, 1);
        const lastDay = new Date(now.getFullYear(), 11, 31);
        
        $('#filter-date-ini').val(formatDate(firstDay));
        $('#filter-date-end').val(formatDate(lastDay));
        updateQuickFilterHighlight();
        autoGenerateReport();
    });

    $('#btn-filter-all').on('click', function() {
        $('#filter-date-ini').val('');
        $('#filter-date-end').val('');
        $('.payment-method-checkbox, .status-checkbox').prop('checked', false);
        $('#filter-customers').val(null).trigger('change');
        updateQuickFilterHighlight();
        autoGenerateReport();
    });

    // Atualizar destaque quando as datas mudarem manualmente
    $('#filter-date-ini, #filter-date-end').on('change', function() {
        updateQuickFilterHighlight();
    });

    // Definir datas do mês atual por padrão
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    $('#filter-date-ini').val(formatDate(firstDay));
    $('#filter-date-end').val(formatDate(lastDay));

    // Atualizar destaque do filtro rápido (mês atual por padrão)
    updateQuickFilterHighlight();

    // Gerar relatório automaticamente ao carregar a página
    autoGenerateReport();

    // Exportar PDF
    $('#btn-export-pdf').on('click', function() {
        if(reportData.length === 0) {
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
        if(reportData.length === 0) {
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

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function autoGenerateReport() {
    // Debounce: aguarda 500ms após a última mudança antes de gerar o relatório
    clearTimeout(generateReportTimeout);
    generateReportTimeout = setTimeout(function() {
        generateReport();
    }, 500);
}

function generateReport() {
    const filters = {
        type: $('#filter-type').val(),
        dateini: $('#filter-date-ini').val(),
        dateend: $('#filter-date-end').val(),
        status: [],
        payment_method: [],
        customer: []
    };

    $('.status-checkbox:checked').each(function() {
        filters.status.push($(this).val());
    });

    $('.payment-method-checkbox:checked').each(function() {
        filters.payment_method.push($(this).val());
    });

    // Pegar clientes selecionados do Select2
    const selectedCustomers = $('#filter-customers').val();
    if(selectedCustomers && selectedCustomers.length > 0) {
        filters.customer = selectedCustomers;
    }

    $.ajax({
        url: '{{ url("admin/reports/invoices/data") }}',
        type: 'GET',
        data: filters,
        beforeSend: function() {
            $('#list-report').html('<tr><td colspan="9" class="text-center" style="padding: 40px;"><i class="fas fa-spinner fa-spin"></i> Carregando...</td></tr>');
        },
        success: function(response) {
            reportData = response.result;
            displayReport(response.result, response.totals);
            if(response.status) {
                updateStatusChart(response.status);
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao gerar relatório!'
            });
        }
    });
}

function displayReport(data, totals) {
    let html = '';

    if(data.length === 0) {
        html = '<tr><td colspan="9" class="text-center" style="padding: 40px; color: #6B7280;">Nenhum registro encontrado</td></tr>';
    } else {
        data.forEach(function(item, index) {
            const statusColor = item.status === 'Pago' ? '#22C55E' : item.status === 'Pendente' ? '#FFBD59' : item.status === 'Expirado' ? '#F59E0B' : '#F87171';
            
            html += '<tr>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (index + 1) + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (item.customer_name || '-') + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + item.description + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (item.service_name || '-') + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (item.date_due ? formatDateDisplay(item.date_due) : '-') + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (item.date_payment ? formatDateDisplay(item.date_payment) : '-') + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1); font-weight: 600;">R$ ' + formatCurrency(item.price) + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">' + (item.payment_method || '-') + '</td>';
            html += '<td style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">';
            html += '<span style="background-color: ' + statusColor + '20; color: ' + statusColor + '; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">' + item.status + '</span>';
            html += '</td>';
            html += '</tr>';
        });
    }

    $('#list-report').html(html);

    // Atualizar totais
    if(totals) {
        $('#total_currency').text('R$ ' + formatCurrency(totals.total));
        $('#total_count').text(totals.qtd_total + ' faturas');
        $('#pendente_currency').text('R$ ' + formatCurrency(totals.pendente));
        $('#pendente_count').text(totals.qtd_pendente + ' faturas');
        $('#pago_currency').text('R$ ' + formatCurrency(totals.pago));
        $('#pago_count').text(totals.qtd_pago + ' faturas');
        $('#cancelado_currency').text('R$ ' + formatCurrency(totals.cancelado));
        $('#cancelado_count').text(totals.qtd_cancelado + ' faturas');
    }
}

function formatDateDisplay(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    return String(date.getDate()).padStart(2, '0') + '/' + String(date.getMonth() + 1).padStart(2, '0') + '/' + date.getFullYear();
}

function formatCurrency(value) {
    return parseFloat(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function exportToPDF() {
    // Coletar filtros atuais
    const filters = {
        type: $('#filter-type').val(),
        dateini: $('#filter-date-ini').val(),
        dateend: $('#filter-date-end').val(),
        status: [],
        payment_method: [],
        customer: []
    };

    $('.status-checkbox:checked').each(function() {
        filters.status.push($(this).val());
    });

    $('.payment-method-checkbox:checked').each(function() {
        filters.payment_method.push($(this).val());
    });

    const selectedCustomers = $('#filter-customers').val();
    if(selectedCustomers && selectedCustomers.length > 0) {
        filters.customer = selectedCustomers;
    }

    // Construir URL com parâmetros
    const params = new URLSearchParams();
    if(filters.type) params.append('type', filters.type);
    if(filters.dateini) params.append('dateini', filters.dateini);
    if(filters.dateend) params.append('dateend', filters.dateend);
    filters.status.forEach(s => params.append('status[]', s));
    filters.payment_method.forEach(pm => params.append('payment_method[]', pm));
    filters.customer.forEach(c => params.append('customer[]', c));

    // Abrir PDF em nova aba
    const url = '{{ url("admin/reports/invoices/pdf") }}?' + params.toString();
    window.open(url, '_blank');
}

function exportToExcel() {
    // Criar CSV para Excel
    let csv = 'Cliente,Descrição,Serviço,Vencimento,Recebido em,Valor,Forma de Pagamento,Status\n';
    
    reportData.forEach(function(item) {
        csv += '"' + (item.customer_name || '') + '",';
        csv += '"' + item.description + '",';
        csv += '"' + (item.service_name || '') + '",';
        csv += '"' + (item.date_due ? formatDateDisplay(item.date_due) : '') + '",';
        csv += '"' + (item.date_payment ? formatDateDisplay(item.date_payment) : '') + '",';
        csv += '"' + item.price + '",';
        csv += '"' + (item.payment_method || '') + '",';
        csv += '"' + item.status + '"\n';
    });

    // Criar blob e fazer download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'relatorio_contas_receber_' + new Date().getTime() + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function updateStatusChart(statusData) {
    const ctx = document.getElementById('statusChart');
    
    if (!ctx) return;

    // Destruir gráfico anterior se existir
    if (statusChart) {
        statusChart.destroy();
    }

    if (!statusData || statusData.length === 0) {
        const canvas = ctx.getContext('2d');
        canvas.clearRect(0, 0, ctx.width, ctx.height);
        return;
    }

    // Mapear cores para cada status
    const statusColors = {
        'Pendente': '#FFBD59',
        'Pago': '#22C55E',
        'Cancelado': '#F87171',
        'Expirado': '#F59E0B',
        'Processamento': '#6B7280'
    };

    // Preparar dados
    const labels = statusData.map(item => item.status);
    const data = statusData.map(item => parseFloat(item.total));
    const colors = statusData.map(item => statusColors[item.status] || '#CCCCCC');
    const borderColors = colors.map(color => color);

    // Criar gráfico
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total por Status',
                data: data,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            family: 'Source Sans Pro'
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const color = data.datasets[0].backgroundColor[i];
                                    return {
                                        text: label + ' - R$ ' + formatCurrency(value),
                                        fillStyle: color,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(2);
                            return label + ': R$ ' + formatCurrency(value) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection

