@extends('layouts.admin')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0" style="color: #333333; font-weight: 600;">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right" style="background-color: transparent; padding: 0;">
              <li class="breadcrumb-item"><a href="{{url('admin')}}" style="color: #333333; text-decoration: none; opacity: 0.7;">Home</a></li>
              <li class="breadcrumb-item active" style="color: #333333; opacity: 0.7;">{{ $title }}</li>
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

          <!-- Navegação de Mês -->
          <div class="col-md-12 mb-4">
            <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <div class="d-flex justify-content-between align-items-center">
                <button type="button" id="btn-prev-month" class="btn" style="background-color: #06b8f7; color: #FFFFFF !important; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600;">
                  <i class="fas fa-chevron-left"></i> Mês Anterior
                </button>
                <div class="text-center">
                  <h3 id="current-month" style="color: #333333; font-weight: 600; margin: 0; font-size: 24px;"></h3>
                </div>
                <button type="button" id="btn-next-month" class="btn" style="background-color: #06b8f7; color: #FFFFFF !important; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600;">
                  Próximo Mês <i class="fas fa-chevron-right"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Cards de Resumo -->
          <div class="col-md-4 col-6 mb-3">
            <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <p style="color: #333333; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Total Receitas</p>
                  <h5 id="total-receitas" style="color: #6ccb48; font-size: 24px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                </div>
                <div style="width: 48px; height: 48px; background-color: rgba(108,203,72,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-arrow-up" style="color: #6ccb48; font-size: 20px;"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-6 mb-3">
            <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <p style="color: #333333; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Total Despesas</p>
                  <h5 id="total-despesas" style="color: #06b8f7; font-size: 24px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                </div>
                <div style="width: 48px; height: 48px; background-color: rgba(6,184,247,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-arrow-down" style="color: #06b8f7; font-size: 20px;"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-4 col-12 mb-3">
            <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <p style="color: #333333; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Saldo</p>
                  <h5 id="saldo" style="color: #333333; font-size: 24px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                </div>
                <div style="width: 48px; height: 48px; background-color: rgba(254,201,17,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-balance-scale" style="color: #fec911; font-size: 20px;"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráfico -->
          <div class="col-md-12 mb-4">
            <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <h5 style="color: #333333; font-weight: 600; margin-bottom: 20px;">
                <i class="fas fa-chart-line"></i> Receitas e Despesas por Dia do Mês
              </h5>
              <div style="position: relative; height: 400px;">
                <canvas id="receitaDespesaChart"></canvas>
              </div>
            </div>
          </div>

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
    body {
        background-color: #FFFFFF !important;
    }

    .content-wrapper {
        background-color: #FFFFFF !important;
    }

    .content-header {
        background-color: #FFFFFF !important;
    }

    .content {
        background-color: #FFFFFF !important;
    }

    #btn-prev-month:hover,
    #btn-next-month:hover {
        background-color: #06b8f7 !important;
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(6, 184, 247, 0.3);
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

        #current-month {
            font-size: 18px !important;
        }

        #btn-prev-month,
        #btn-next-month {
            padding: 6px 12px !important;
            font-size: 12px !important;
        }

        #btn-prev-month i,
        #btn-next-month i {
            display: none;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
let receitaDespesaChart = null;
let currentYear = new Date().getFullYear();
let currentMonth = new Date().getMonth() + 1;

$(document).ready(function() {
    loadChart();

    $('#btn-prev-month').on('click', function() {
        currentMonth--;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        loadChart();
    });

    $('#btn-next-month').on('click', function() {
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        loadChart();
    });
});

function loadChart() {
    // Desabilitar botões durante o carregamento
    $('#btn-prev-month, #btn-next-month').prop('disabled', true);

    $.ajax({
        url: '{{ url("admin/receita-despesa/data") }}',
        method: 'GET',
        data: {
            year: currentYear,
            month: currentMonth
        },
        success: function(data) {
            // Reabilitar botões
            $('#btn-prev-month, #btn-next-month').prop('disabled', false);

            // Verificar se há dados
            if (!data || !data.labels) {
                console.error('Dados inválidos recebidos:', data);
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Não foi possível carregar os dados para este mês.'
                });
                return;
            }
            // Atualizar mês atual
            $('#current-month').text(data.month_name);

            // Atualizar totais
            $('#total-receitas').text(formatCurrency(data.total_receitas));
            $('#total-despesas').text(formatCurrency(data.total_despesas));

            // Calcular e atualizar saldo
            const saldo = data.saldo;
            // Mostrar saldo com sinal negativo se for negativo
            if (saldo < 0) {
                $('#saldo').text('- ' + formatCurrency(Math.abs(saldo)));
                $('#saldo').css('color', '#F87171');
            } else {
                $('#saldo').text(formatCurrency(saldo));
                $('#saldo').css('color', '#6ccb48');
            }

            // Destruir gráfico anterior se existir
            if (receitaDespesaChart) {
                receitaDespesaChart.destroy();
            }

            // Criar novo gráfico
            const ctx = document.getElementById('receitaDespesaChart').getContext('2d');
            receitaDespesaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Receitas',
                            data: data.receitas,
                            backgroundColor: 'rgba(108, 203, 72, 0.8)',
                            borderColor: '#6ccb48',
                            borderWidth: 2,
                            borderRadius: 4
                        },
                        {
                            label: 'Despesas',
                            data: data.despesas,
                            backgroundColor: 'rgba(6, 184, 247, 0.8)',
                            borderColor: '#06b8f7',
                            borderWidth: 2,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#333333',
                                font: {
                                    size: 14,
                                    weight: '600'
                                },
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: '#FFFFFF',
                            titleColor: '#333333',
                            bodyColor: '#333333',
                            borderColor: 'rgba(0,0,0,0.1)',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
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
                                color: '#333333',
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#333333'
                            }
                        }
                    }
                }
            });
        },
        error: function(xhr) {
            // Reabilitar botões em caso de erro
            $('#btn-prev-month, #btn-next-month').prop('disabled', false);

            console.error('Erro ao carregar dados:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Não foi possível carregar os dados do relatório. Verifique se há dados para o mês selecionado.'
            });
        }
    });
}

function formatCurrency(value) {
    return parseFloat(value).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}
</script>
@endsection
