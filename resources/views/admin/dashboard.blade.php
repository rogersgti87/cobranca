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

        <!-- Faturas a Receber -->
        <div class="row mb-4">
          <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-file-invoice-dollar text-primary"></i> Faturas a Receber</h4>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-indigo shadow-sm">
              <div class="inner">
                <h3>{{ $total_customers }}</h3>
                <p>Clientes</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-success shadow-sm">
              <div class="inner">
                <h3>{{ $invoice->pay }}</h3>
                <p>Faturas Pagas</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-maroon shadow-sm">
              <div class="inner">
                <h3>{{ $invoice->proccessing }}</h3>
                <p>Em Processamento</p>
              </div>
              <div class="icon">
                <i class="fas fa-spinner fa-spin"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-warning shadow-sm">
              <div class="inner">
                <h3>{{ $invoice->pendent }}</h3>
                <p>Faturas Pendentes</p>
              </div>
              <div class="icon">
                <i class="far fa-hourglass"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-danger shadow-sm">
              <div class="inner">
                <h3>{{ $invoice->cancelled }}</h3>
                <p>Faturas Canceladas</p>
              </div>
              <div class="icon">
                <i class="fas fa-ban"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-info shadow-sm">
              <div class="inner">
                <h3>{{ $invoice->total }}</h3>
                <p>Total de Faturas</p>
              </div>
              <div class="icon">
                <i class="fas fa-list"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Contas a Pagar -->
        <div class="row mb-4">
          <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-file-invoice text-danger"></i> Contas a Pagar</h4>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-primary shadow-sm">
              <div class="inner">
                <h3>{{ $payable->total ?? 0 }}</h3>
                <p>Total de Contas</p>
              </div>
              <div class="icon">
                <i class="fas fa-receipt"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-success shadow-sm">
              <div class="inner">
                <h3>{{ $payable->pay ?? 0 }}</h3>
                <p>Contas Pagas</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-double"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-warning shadow-sm">
              <div class="inner">
                <h3>{{ $payable->pendent ?? 0 }}</h3>
                <p>Contas Pendentes</p>
              </div>
              <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-danger shadow-sm">
              <div class="inner">
                <h3>{{ $payable->cancelled ?? 0 }}</h3>
                <p>Contas Canceladas</p>
              </div>
              <div class="icon">
                <i class="fas fa-times-circle"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-secondary shadow-sm">
              <div class="inner">
                <h3>R$ {{ number_format($payable->total_pendente ?? 0, 2, ',', '.') }}</h3>
                <p>Total Pendente</p>
              </div>
              <div class="icon">
                <i class="fas fa-dollar-sign"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="small-box bg-gradient-info shadow-sm">
              <div class="inner">
                <h3>R$ {{ number_format($payable->total_pago ?? 0, 2, ',', '.') }}</h3>
                <p>Total Pago</p>
              </div>
              <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráficos de Faturas -->
        <div class="row mb-4">
          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary"></i>
                    Faturas por Status - Anual
                  </h3>
                  <select class="form-control form-control-sm" id="invoiceYearSelect" style="width: auto; max-width: 120px;" onchange="changeInvoiceYear()">
                    @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                      <option value="{{$y}}" {{$y == date('Y') ? 'selected' : ''}}>{{$y}}</option>
                    @endfor
                  </select>
                </div>
              </div>
              <div class="card-body">
                <canvas id="paymentStatusChartYear" height="250"></canvas>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary"></i>
                    Faturas por Status - Mensal
                  </h3>
                  <div class="d-flex align-items-center gap-2">
                    <select class="form-control form-control-sm" id="invoiceMonthSelect" style="width: auto; max-width: 120px;" onchange="changeInvoiceMonth()">
                      @php
                        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
                        $currentMonth = date('m');
                      @endphp
                      @foreach($months as $index => $month)
                        <option value="{{$index + 1}}" {{($index + 1) == $currentMonth ? 'selected' : ''}}>{{$month}}</option>
                      @endforeach
                    </select>
                    <select class="form-control form-control-sm" id="invoiceYearMonthSelect" style="width: auto; max-width: 100px;" onchange="changeInvoiceMonth()">
                      @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{$y}}" {{$y == date('Y') ? 'selected' : ''}}>{{$y}}</option>
                      @endfor
                    </select>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <canvas id="paymentStatusChartMonth" height="250"></canvas>
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

        <!-- Alertas e Vencimentos -->
        <div class="row">
          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h3 class="card-title mb-0">
                  <i class="fas fa-bell text-warning"></i>
                  Alertas de Faturas
                </h3>
              </div>
              <div class="card-body">
                <div class="info-box mb-3 bg-danger shadow-sm">
                  <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Faturas Vencidas</span>
                    <span class="info-box-number">{{ $invoice->due }}</span>
                  </div>
                </div>

                <div class="info-box mb-3 bg-info shadow-sm">
                  <span class="info-box-icon"><i class="fas fa-business-time"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Vencendo em 5 dias</span>
                    <span class="info-box-number">{{ $invoice->five_days }}</span>
                  </div>
                </div>

                <div class="info-box mb-3 bg-success shadow-sm">
                  <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Vencendo Hoje</span>
                    <span class="info-box-number">{{ $invoice->today }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm">
              <div class="card-header bg-white border-bottom">
                <h3 class="card-title mb-0">
                  <i class="fas fa-bell text-warning"></i>
                  Alertas de Contas a Pagar
                </h3>
              </div>
              <div class="card-body">
                <div class="info-box mb-3 bg-danger shadow-sm">
                  <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Contas Vencidas</span>
                    <span class="info-box-number">{{ $payable->due ?? 0 }}</span>
                  </div>
                </div>

                <div class="info-box mb-3 bg-info shadow-sm">
                  <span class="info-box-icon"><i class="fas fa-business-time"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Vencendo em 5 dias</span>
                    <span class="info-box-number">{{ $payable->five_days ?? 0 }}</span>
                  </div>
                </div>

                <div class="info-box mb-3 bg-success shadow-sm">
                  <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Vencendo Hoje</span>
                    <span class="info-box-number">{{ $payable->today ?? 0 }}</span>
                  </div>
                </div>
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
    var chartYear = null;
    var chartMonth = null;
    var payableChartYear = null;
    var payableChartMonth = null;

    // Função para mudar ano dos gráficos de faturas (anual)
    function changeInvoiceYear() {
        invoiceYear = parseInt(document.getElementById('invoiceYearSelect').value);
        updateChartYear();
    }

    // Função para mudar mês dos gráficos de faturas (mensal)
    function changeInvoiceMonth() {
        invoiceMonth = parseInt(document.getElementById('invoiceMonthSelect').value);
        invoiceYear = parseInt(document.getElementById('invoiceYearMonthSelect').value);
        updateChartMonth();
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

    function updateChartYear() {
        var year = parseInt(document.getElementById('invoiceYearSelect').value);
        $.ajax({
            url: '{{url("admin/chart-invoices")}}',
            type: 'GET',
            data: {
                year: year
            },
            dataType: 'json',
            success: function (data) {
                var labels = Object.keys(data.year);
                var values = Object.values(data.year);

                var colors = [
                    'rgba(255, 99, 132, 0.8)', // Pendente
                    'rgba(54, 162, 235, 0.8)', // Pago
                    'rgba(255, 206, 86, 0.8)', // Cancelado
                    'rgba(75, 192, 192, 0.8)', // Expirado
                    'rgba(153, 102, 255, 0.8)', // Processamento
                    'rgba(0, 0, 0, 0.8)' // Total
                ];

                var ctx = document.getElementById('paymentStatusChartYear');
                if (chartYear) {
                    chartYear.destroy();
                }
                chartYear = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
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
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                },
                            },
                        },
                    },
                });
            },
            complete: function () {
                setTimeout(updateChartYear, 30000);
            },
        });
    }

    function updateChartMonth() {
        $.ajax({
            url: '{{url("admin/chart-invoices")}}',
            type: 'GET',
            data: {
                month: invoiceMonth,
                year: invoiceYear
            },
            dataType: 'json',
            success: function (data) {
                var labels = Object.keys(data.month || {});
                var values = labels.length > 0 ? Object.values(data.month) : [0];

                // Se não houver dados, mostrar mensagem
                if (labels.length === 0) {
                    labels = ['Sem dados'];
                    values = [0];
                }

                var colors = [
                    'rgba(255, 99, 132, 0.8)', // Pendente
                    'rgba(54, 162, 235, 0.8)', // Pago
                    'rgba(255, 206, 86, 0.8)', // Cancelado
                    'rgba(75, 192, 192, 0.8)', // Expirado
                    'rgba(153, 102, 255, 0.8)', // Processamento
                    'rgba(0, 0, 0, 0.8)' // Total
                ];

                var ctx = document.getElementById('paymentStatusChartMonth');
                if (chartMonth) {
                    chartMonth.destroy();
                }
                chartMonth = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
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
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                },
                            },
                        },
                    },
                });
            },
            complete: function () {
                setTimeout(updateChartMonth, 30000);
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
        invoiceMonth = parseInt(document.getElementById('invoiceMonthSelect').value);
        invoiceYear = parseInt(document.getElementById('invoiceYearSelect').value);
        payableMonth = parseInt(document.getElementById('payableMonthSelect').value);
        payableYear = parseInt(document.getElementById('payableYearSelect').value);

        updateChartYear();
        updateChartMonth();
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
        .small-box {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .small-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
        }
        .card {
            transition: box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }
        .info-box {
            transition: transform 0.2s ease-in-out;
        }
        .info-box:hover {
            transform: translateX(5px);
        }
        h4 {
            font-weight: 600;
            color: #495057;
        }
        @media (max-width: 768px) {
            .small-box h3 {
                font-size: 1.5rem !important;
            }
            .form-control-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            .card-header .d-flex {
                flex-wrap: wrap;
            }
            .card-header .form-control-sm {
                margin-top: 0.5rem;
            }
        }
    </style>

  @endsection

  @endsection
