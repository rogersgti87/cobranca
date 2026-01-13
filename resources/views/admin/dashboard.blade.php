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
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        <!-- Resumo Geral Compacto -->
        <div class="row mb-3">
          <!-- Faturas a Receber - Agrupado -->
          <div class="col-lg-6 col-md-12 mb-3">
            <div class="dashboard-card-modern">
              <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                  <i class="fas fa-file-invoice-dollar"></i> Faturas a Receber
                </h5>
              </div>
              <div class="dashboard-card-body">
                <div class="dashboard-stats-grid">
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(6,184,247,0.1);">
                      <i class="fas fa-users" style="color: #06b8f7;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $total_customers }}</span>
                      <span class="stat-label">Clientes</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(108,203,72,0.2);">
                      <i class="fas fa-check-circle" style="color: #6ccb48;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $invoice->pay }}</span>
                      <span class="stat-label">Pagas</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(254,201,17,0.2);">
                      <i class="fas fa-hourglass" style="color: #fec911;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $invoice->pendent }}</span>
                      <span class="stat-label">Pendentes</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(6,184,247,0.1);">
                      <i class="fas fa-spinner" style="color: #06b8f7;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $invoice->proccessing }}</span>
                      <span class="stat-label">Processamento</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(239,68,68,0.1);">
                      <i class="fas fa-ban" style="color: #EF4444;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $invoice->cancelled }}</span>
                      <span class="stat-label">Canceladas</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(6,184,247,0.2);">
                      <i class="fas fa-list" style="color: #06b8f7;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $invoice->total }}</span>
                      <span class="stat-label">Total</span>
                    </div>
                  </div>
                </div>
                <!-- Alertas de Faturas - Compactos -->
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(0,0,0,0.1);">
                  <div class="dashboard-alerts-compact">
                    <div class="alert-item-compact alert-danger">
                      <i class="fas fa-calendar-times"></i>
                      <div class="alert-content-compact">
                        <span class="alert-label-compact">Vencidas</span>
                        <span class="alert-value-compact">{{ $invoice->due }}</span>
                      </div>
                    </div>
                    <div class="alert-item-compact alert-warning">
                      <i class="fas fa-business-time"></i>
                      <div class="alert-content-compact">
                        <span class="alert-label-compact">5 dias</span>
                        <span class="alert-value-compact">{{ $invoice->five_days }}</span>
                      </div>
                    </div>
                    <div class="alert-item-compact alert-info">
                      <i class="fas fa-calendar-day"></i>
                      <div class="alert-content-compact">
                        <span class="alert-label-compact">Hoje</span>
                        <span class="alert-value-compact">{{ $invoice->today }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Contas a Pagar - Agrupado -->
          <div class="col-lg-6 col-md-12 mb-3">
            <div class="dashboard-card-modern">
              <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                  <i class="fas fa-file-invoice"></i> Contas a Pagar
                </h5>
              </div>
              <div class="dashboard-card-body">
                <div class="dashboard-stats-grid">
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(6,184,247,0.1);">
                      <i class="fas fa-receipt" style="color: #06b8f7;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $payable->total ?? 0 }}</span>
                      <span class="stat-label">Total</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(108,203,72,0.2);">
                      <i class="fas fa-check-double" style="color: #6ccb48;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $payable->pay ?? 0 }}</span>
                      <span class="stat-label">Pagas</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(254,201,17,0.2);">
                      <i class="fas fa-exclamation-triangle" style="color: #fec911;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $payable->pendent ?? 0 }}</span>
                      <span class="stat-label">Pendentes</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item">
                    <div class="stat-icon" style="background: rgba(239,68,68,0.1);">
                      <i class="fas fa-times-circle" style="color: #EF4444;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">{{ $payable->cancelled ?? 0 }}</span>
                      <span class="stat-label">Canceladas</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item stat-item-large">
                    <div class="stat-icon" style="background: rgba(254,201,17,0.2);">
                      <i class="fas fa-dollar-sign" style="color: #fec911;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">R$ {{ number_format($payable->total_pendente ?? 0, 2, ',', '.') }}</span>
                      <span class="stat-label">Total Pendente</span>
                    </div>
                  </div>
                  <div class="dashboard-stat-item stat-item-large">
                    <div class="stat-icon" style="background: rgba(108,203,72,0.2);">
                      <i class="fas fa-money-bill-wave" style="color: #6ccb48;"></i>
                    </div>
                    <div class="stat-content">
                      <span class="stat-value">R$ {{ number_format($payable->total_pago ?? 0, 2, ',', '.') }}</span>
                      <span class="stat-label">Total Pago</span>
                    </div>
                  </div>
                </div>
                <!-- Alertas de Contas a Pagar - Compactos -->
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(0,0,0,0.1);">
                  <div class="dashboard-alerts-compact">
                    <div class="alert-item-compact alert-danger">
                      <i class="fas fa-calendar-times"></i>
                      <div class="alert-content-compact">
                        <span class="alert-label-compact">Vencidas</span>
                        <span class="alert-value-compact">{{ $payable->due ?? 0 }}</span>
                      </div>
                    </div>
                    <div class="alert-item-compact alert-warning">
                      <i class="fas fa-business-time"></i>
                      <div class="alert-content-compact">
                        <span class="alert-label-compact">5 dias</span>
                        <span class="alert-value-compact">{{ $payable->five_days ?? 0 }}</span>
                      </div>
                    </div>
                    <div class="alert-item-compact alert-info">
                      <i class="fas fa-calendar-day"></i>
                      <div class="alert-content-compact">
                        <span class="alert-label-compact">Hoje</span>
                        <span class="alert-value-compact">{{ $payable->today ?? 0 }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráficos de Receitas e Despesas -->
        <div class="row mb-4">
          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="card-title mb-0">
                    <i class="fas fa-chart-bar"></i>
                    Receitas e Despesas - Anual
                  </h3>
                  <select class="form-control form-control-sm" id="receitasDespesasYearSelect" style="width: auto; max-width: 120px;" onchange="changeReceitasDespesasYear()">
                    @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                      <option value="{{$y}}" {{$y == date('Y') ? 'selected' : ''}}>{{$y}}</option>
                    @endfor
                  </select>
                </div>
              </div>
              <div class="card-body">
                <canvas id="receitasDespesasChartYear" height="250"></canvas>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="card-title mb-0">
                    <i class="fas fa-chart-bar"></i>
                    Receitas e Despesas - Mensal
                  </h3>
                  <div class="d-flex align-items-center gap-2">
                    <select class="form-control form-control-sm" id="receitasDespesasMonthSelect" style="width: auto; max-width: 120px;" onchange="changeReceitasDespesasMonth()">
                      @php
                        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                        $currentMonth = date('m');
                      @endphp
                      @foreach($months as $index => $month)
                        <option value="{{$index + 1}}" {{($index + 1) == $currentMonth ? 'selected' : ''}}>{{$month}}</option>
                      @endforeach
                    </select>
                    <select class="form-control form-control-sm" id="receitasDespesasYearMonthSelect" style="width: auto; max-width: 100px;" onchange="changeReceitasDespesasMonth()">
                      @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{$y}}" {{$y == date('Y') ? 'selected' : ''}}>{{$y}}</option>
                      @endfor
                    </select>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <canvas id="receitasDespesasChartMonth" height="250"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráficos de Contas a Pagar por Categoria -->
        <div class="row mb-4">
          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-danger"></i>
                    Contas a Pagar por Categoria - Anual
                  </h3>
                  <select class="form-control form-control-sm" id="payableYearSelect" style="width: auto; max-width: 120px;" onchange="changePayableYear()">
                    @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                      <option value="{{$y}}" {{$y == date('Y') ? 'selected' : ''}}>{{$y}}</option>
                    @endfor
                  </select>
                </div>
              </div>
              <div class="card-body">
                <canvas id="payableCategoryChartYear" height="250"></canvas>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-danger"></i>
                    Contas a Pagar por Categoria - Mensal
                  </h3>
                  <div class="d-flex align-items-center gap-2">
                    <select class="form-control form-control-sm" id="payableMonthSelect" style="width: auto; max-width: 120px;" onchange="changePayableMonth()">
                      @php
                        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                        $currentMonth = date('m');
                      @endphp
                      @foreach($months as $index => $month)
                        <option value="{{$index + 1}}" {{($index + 1) == $currentMonth ? 'selected' : ''}}>{{$month}}</option>
                      @endforeach
                    </select>
                    <select class="form-control form-control-sm" id="payableYearMonthSelect" style="width: auto; max-width: 100px;" onchange="changePayableMonth()">
                      @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{$y}}" {{$y == date('Y') ? 'selected' : ''}}>{{$y}}</option>
                      @endfor
                    </select>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <canvas id="payableCategoryChartMonth" height="250"></canvas>
              </div>
            </div>
          </div>
        </div>


      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



  <!-- Modal :: Form Invoice -->
  <div class="modal fade" id="modalInvoiceError" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceErrorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" class="form-horizontal" id="form-request-invoice-error">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInvoiceErrorLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-invoice-error">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                </div>
            </form>
        </div>
    </div>
 </div>
 <!-- Modal :: Form Invoice -->



    @section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Variáveis globais para os gráficos
    var receitasDespesasChartYear = null;
    var receitasDespesasChartMonth = null;
    var payableChartYear = null;
    var payableChartMonth = null;

    // Variáveis para controlar mês/ano dos gráficos de receitas e despesas
    var receitasDespesasMonth = {{date('m')}};
    var receitasDespesasYear = {{date('Y')}};

    // Função para mudar ano dos gráficos de receitas e despesas (anual)
    function changeReceitasDespesasYear() {
        receitasDespesasYear = parseInt(document.getElementById('receitasDespesasYearSelect').value);
        updateReceitasDespesasChartYear();
    }

    // Função para mudar mês dos gráficos de receitas e despesas (mensal)
    function changeReceitasDespesasMonth() {
        receitasDespesasMonth = parseInt(document.getElementById('receitasDespesasMonthSelect').value);
        receitasDespesasYear = parseInt(document.getElementById('receitasDespesasYearMonthSelect').value);
        updateReceitasDespesasChartMonth();
    }

    // Função para mudar ano dos gráficos de contas a pagar (anual)
    function changePayableYear() {
        payableYear = parseInt(document.getElementById('payableYearSelect').value);
        updatePayableChartYear();
    }

    // Função para mudar mês dos gráficos de contas a pagar (mensal)
    function changePayableMonth() {
        payableMonth = parseInt(document.getElementById('payableMonthSelect').value);
        payableYear = parseInt(document.getElementById('payableYearMonthSelect').value);
        updatePayableChartMonth();
    }

    // Variáveis para controlar mês/ano dos gráficos (inicializadas com valores padrão)
    var invoiceMonth = {{date('m')}};
    var invoiceYear = {{date('Y')}};
    var payableMonth = {{date('m')}};
    var payableYear = {{date('Y')}};

    function updateReceitasDespesasChartYear() {
        var year = parseInt(document.getElementById('receitasDespesasYearSelect').value);
        $.ajax({
            url: '{{url("admin/chart-receitas-despesas")}}',
            type: 'GET',
            data: {
                year: year
            },
            dataType: 'json',
            success: function (data) {
                var ctx = document.getElementById('receitasDespesasChartYear');
                if (receitasDespesasChartYear) {
                    receitasDespesasChartYear.destroy();
                }
                receitasDespesasChartYear = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.year.labels,
                        datasets: [
                            {
                                label: 'Receitas',
                                data: data.year.receitas,
                                backgroundColor: 'rgba(108, 203, 72, 0.8)',
                                borderColor: '#6ccb48',
                                borderWidth: 2,
                            },
                            {
                                label: 'Despesas',
                                data: data.year.despesas,
                                backgroundColor: 'rgba(6, 184, 247, 0.8)',
                                borderColor: '#06b8f7',
                                borderWidth: 2,
                            }
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        backgroundColor: '#FFFFFF',
                        animation: {
                            duration: 1000,
                            easing: 'easeOutBounce'
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        var value = context.parsed.y || 0;
                                        return label + ': R$ ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        });
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                    },
                });
            },
            complete: function () {
                setTimeout(updateReceitasDespesasChartYear, 30000);
            },
        });
    }

    function updateReceitasDespesasChartMonth() {
        $.ajax({
            url: '{{url("admin/chart-receitas-despesas")}}',
            type: 'GET',
            data: {
                month: receitasDespesasMonth,
                year: receitasDespesasYear
            },
            dataType: 'json',
            success: function (data) {
                var ctx = document.getElementById('receitasDespesasChartMonth');
                if (receitasDespesasChartMonth) {
                    receitasDespesasChartMonth.destroy();
                }
                receitasDespesasChartMonth = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Receitas', 'Despesas'],
                        datasets: [{
                            label: 'Valores',
                            data: [data.month.receitas, data.month.despesas],
                            backgroundColor: [
                                'rgba(108, 203, 72, 0.8)',
                                'rgba(6, 184, 247, 0.8)'
                            ],
                            borderColor: [
                                '#6ccb48',
                                '#06b8f7'
                            ],
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        backgroundColor: '#FFFFFF',
                        animation: {
                            duration: 1000,
                            easing: 'easeOutBounce'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.parsed.y || 0;
                                        return label + ': R$ ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        });
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            }
                        },
                    },
                });
            },
            complete: function () {
                setTimeout(updateReceitasDespesasChartMonth, 30000);
            },
        });
    }

    function updatePayableChartYear() {
        var year = parseInt(document.getElementById('payableYearSelect').value);
        $.ajax({
            url: '{{url("admin/chart-payables")}}',
            type: 'GET',
            data: {
                year: year
            },
            dataType: 'json',
            success: function (data) {
                var categories = Object.keys(data.year);
                var values = categories.map(cat => data.year[cat].count);
                var totals = categories.map(cat => data.year[cat].total);

                // Gerar cores dinâmicas baseadas nas categorias
                var colors = categories.map(function(category) {
                    var categoryData = data.year[category];
                    if (categoryData && categoryData.color) {
                        var color = categoryData.color;
                        // Converter hex para rgba
                        if (color.startsWith('#')) {
                            var r = parseInt(color.slice(1, 3), 16);
                            var g = parseInt(color.slice(3, 5), 16);
                            var b = parseInt(color.slice(5, 7), 16);
                            return `rgba(${r}, ${g}, ${b}, 0.8)`;
                        }
                        return color;
                    }
                    return 'rgba(108, 117, 125, 0.8)'; // Cor padrão cinza
                });

                var ctx = document.getElementById('payableCategoryChartYear');
                if (payableChartYear) {
                    payableChartYear.destroy();
                }
                payableChartYear = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'Quantidade',
                            data: values,
                            backgroundColor: colors,
                            borderColor: colors.map(c => c.replace('0.8', '1')),
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1000,
                            easing: 'easeOutBounce'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.parsed || 0;
                                        var category = categories[context.dataIndex];
                                        var total = totals[context.dataIndex];
                                        return label + ': ' + value + ' conta(s) - R$ ' + total.toFixed(2).replace('.', ',');
                                    }
                                }
                            }
                        }
                    },
                });
            },
            complete: function () {
                setTimeout(updatePayableChartYear, 30000);
            },
        });
    }

    function updatePayableChartMonth() {
        $.ajax({
            url: '{{url("admin/chart-payables")}}',
            type: 'GET',
            data: {
                month: payableMonth,
                year: payableYear
            },
            dataType: 'json',
            success: function (data) {
                var categories = Object.keys(data.month || {});
                var values = categories.length > 0 ? categories.map(cat => data.month[cat].count) : [0];
                var totals = categories.length > 0 ? categories.map(cat => data.month[cat].total) : [0];

                // Se não houver dados, mostrar mensagem
                if (categories.length === 0) {
                    categories = ['Sem dados'];
                    values = [0];
                    totals = [0];
                }

                // Gerar cores dinâmicas baseadas nas categorias
                var colors = categories.map(function(category) {
                    var categoryData = data.month[category];
                    if (categoryData && categoryData.color) {
                        var color = categoryData.color;
                        // Converter hex para rgba
                        if (color.startsWith('#')) {
                            var r = parseInt(color.slice(1, 3), 16);
                            var g = parseInt(color.slice(3, 5), 16);
                            var b = parseInt(color.slice(5, 7), 16);
                            return `rgba(${r}, ${g}, ${b}, 0.8)`;
                        }
                        return color;
                    }
                    return 'rgba(108, 117, 125, 0.8)'; // Cor padrão cinza
                });

                var ctx = document.getElementById('payableCategoryChartMonth');
                if (payableChartMonth) {
                    payableChartMonth.destroy();
                }
                payableChartMonth = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'Quantidade',
                            data: values,
                            backgroundColor: colors,
                            borderColor: colors.map(c => c.replace('0.8', '1')),
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1000,
                            easing: 'easeOutBounce'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.parsed || 0;
                                        var category = categories[context.dataIndex];
                                        var total = totals[context.dataIndex];
                                        return label + ': ' + value + ' conta(s) - R$ ' + total.toFixed(2).replace('.', ',');
                                    }
                                }
                            }
                        }
                    },
                });
            },
            complete: function () {
                setTimeout(updatePayableChartMonth, 30000);
            },
        });
    }

    $(document).ready(function () {
        // Inicializar variáveis com valores dos selects após o DOM estar carregado
        receitasDespesasMonth = parseInt(document.getElementById('receitasDespesasMonthSelect').value);
        receitasDespesasYear = parseInt(document.getElementById('receitasDespesasYearSelect').value);
        payableMonth = parseInt(document.getElementById('payableMonthSelect').value);
        payableYear = parseInt(document.getElementById('payableYearSelect').value);

        updateReceitasDespesasChartYear();
        updateReceitasDespesasChartMonth();
        updatePayableChartYear();
        updatePayableChartMonth();
    });

    </script>


    <script>
    function invoiceError(){
        $("#modalInvoiceError").modal('show');
        $("#modalInvoiceErrorLabel").html('Faturas com erro');
        var url = "{{url('/admin/load-invoice-error')}}";
        $.get(url,
            $(this)
            .addClass('modal-scrollfix')
            .find('#form-content-invoice-error')
            .html('Carregando...'),
            function(data) {
                $("#form-content-invoice-error").html(data);
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        @if($invoice->error > 0)
            invoiceError();
        @endif
    });
    </script>

    <style>
        /* Dashboard Moderno - Estilos Compactos */
        .dashboard-card-modern {
            background: #FFFFFF;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card-modern:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .dashboard-card-header {
            background: #FFFFFF;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .dashboard-card-title {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #333333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dashboard-card-title i {
            color: #06b8f7;
            font-size: 16px;
        }

        .dashboard-card-body {
            padding: 16px;
        }

        .dashboard-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .dashboard-stat-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #F9F9F9;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .dashboard-stat-item:hover {
            background: rgba(6, 184, 247, 0.05);
            transform: translateY(-2px);
        }

        .stat-item-large {
            grid-column: span 3;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon i {
            font-size: 18px;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #333333;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 11px;
            color: #6B7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Alertas Compactos - Dentro dos Cards */
        .dashboard-alerts-compact {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .alert-item-compact {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .alert-item-compact:hover {
            transform: translateY(-1px);
        }

        .alert-item-compact i {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .alert-content-compact {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }

        .alert-label-compact {
            font-size: 10px;
            color: #6B7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .alert-value-compact {
            font-size: 16px;
            font-weight: 700;
            color: #333333;
            line-height: 1.2;
        }

        .alert-item-compact.alert-danger {
            background: rgba(239, 68, 68, 0.08);
        }

        .alert-item-compact.alert-danger i {
            background: rgba(239, 68, 68, 0.2);
            color: #EF4444;
        }

        .alert-item-compact.alert-warning {
            background: rgba(254, 201, 17, 0.08);
        }

        .alert-item-compact.alert-warning i {
            background: rgba(254, 201, 17, 0.2);
            color: #fec911;
        }

        .alert-item-compact.alert-info {
            background: rgba(6, 184, 247, 0.08);
        }

        .alert-item-compact.alert-info i {
            background: rgba(6, 184, 247, 0.2);
            color: #06b8f7;
        }

        /* Cards de Gráficos */
        .card {
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card-header {
            background: #FFFFFF;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            border-radius: 12px 12px 0 0;
        }

        .card-header h3 {
            color: #333333;
            font-size: 14px;
            font-weight: 600;
        }

        .card-header h3 i {
            color: #06b8f7;
        }

        /* Gráficos com fundo branco */
        canvas {
            background-color: #FFFFFF !important;
        }

        .card-body {
            background-color: #FFFFFF !important;
        }

        /* Responsivo */
        @media (max-width: 992px) {
            .dashboard-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .stat-item-large {
                grid-column: span 2;
            }
        }

        @media (max-width: 768px) {
            .dashboard-stats-grid {
                grid-template-columns: 1fr;
            }
            .stat-item-large {
                grid-column: span 1;
            }
            .stat-value {
                font-size: 16px;
            }
            .dashboard-alerts-compact {
                grid-template-columns: 1fr;
            }
            .form-control-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            .card-header .d-flex {
                flex-wrap: wrap;
            }
        }
    </style>

  @endsection

  @endsection
