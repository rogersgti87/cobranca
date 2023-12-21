@extends('layouts.admin')

@section('content')



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">

    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">


            <div class="col-md-12">
                <div class="row d-flex justify-content-center">


                     <div class="col-md-2 col-6">
                       <div class="small-box bg-indigo">
                           <div class="inner">
                               <h3>{{ $total_customers }}</h3>
                               <p>Clientes</p>
                           </div>
                           <div class="icon">
                               <i class="fas fa-users"></i>
                           </div>
                       </div>
                   </div>


                   <div class="col-md-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $invoice->pay }}</h3>
                            <p>Faturas Pagas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="small-box bg-maroon">
                        <div class="inner">
                            <h3>{{ $invoice->proccessing }}</h3>
                            <p>Faturas em Processamento</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $invoice->pendent }}</h3>
                            <p>Faturas Pendentes</p>
                        </div>
                        <div class="icon">
                            <i class="far fa-hourglass"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $invoice->cancelled }}</h3>
                            <p>Faturas Canceladas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ban"></i>
                        </div>
                    </div>
                </div>


               </div>

               </div>
{{-- fim widget --}}

<div class="col-md-4 col-12">

    <div class="card">
        <div class="card-header border-0">
        <div class="d-flex justify-content-between">
        <h3 class="card-title">Faturas por Status Anual ({{date('Y')}})</h3>
        </div>
        </div>
        <div class="card-body">

            <canvas id="paymentStatusChartYear"></canvas>

        </div>
        </div>


</div>


<div class="col-md-4 col-12">

    <div class="card">
        <div class="card-header border-0">
        <div class="d-flex justify-content-between">
        <h3 class="card-title">Faturas por Status Mensal ({{date('M')}})</h3>
        </div>
        </div>
        <div class="card-body">

            <canvas id="paymentStatusChartMonth"></canvas>

        </div>
        </div>

</div>

<div class="col-md-4">

    <div class="info-box mb-3 bg-danger">
    <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
    <div class="info-box-content">
    <span class="info-box-text">Vencidas</span>
    <span class="info-box-number">{{ $invoice->due }}</span>
    </div>

    </div>

    <div class="info-box mb-3 bg-info">
    <span class="info-box-icon"><i class="fas fa-business-time"></i></span>
    <div class="info-box-content">
    <span class="info-box-text">Vencendo em 5 dias</span>
    <span class="info-box-number">{{ $invoice->five_days }}</span>
    </div>

    </div>

    <div class="info-box mb-3 bg-success">
    <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
    <div class="info-box-content">
    <span class="info-box-text">Vencendo Hoje</span>
    <span class="info-box-number">{{ $invoice->today }}</span>
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


    function updateChartYear() {
        $.ajax({
            url: '{{url("admin/chart-invoices")}}',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                var labels = Object.keys(data.year);
                var values = Object.values(data.year);

                var colors = [
                    'rgba(255, 99, 132, 0.5)', // Pendente
                    'rgba(54, 162, 235, 0.5)', // Pago
                    'rgba(255, 206, 86, 0.5)', // Cancelado
                    'rgba(75, 192, 192, 0.5)', // Expirado
                    'rgba(153, 102, 255, 0.5)', // Processamento
                    'rgba(0, 0, 0, 0.5)' // Total
                ];

                var ctx = document.getElementById('paymentStatusChartYear').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '',
                            data: values,
                            backgroundColor: colors,
                        }],
                    },
                    options: {
                        animation: {
                            // Configuração das animações
                            duration: 1000, // Duração da animação em milissegundos
                            easing: 'easeOutBounce' // Tipo de easing (por exemplo, 'linear', 'easeInOutQuart', 'easeOutBounce')
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
                setTimeout(updateChartYear, 5000);
            },
        });
    }


    function updateChartMonth() {
        $.ajax({
            url: '{{url("admin/chart-invoices")}}',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                var labels = Object.keys(data.month);
                var values = Object.values(data.month);

                var colors = [
                    'rgba(255, 99, 132, 0.5)', // Pendente
                    'rgba(54, 162, 235, 0.5)', // Pago
                    'rgba(255, 206, 86, 0.5)', // Cancelado
                    'rgba(75, 192, 192, 0.5)', // Expirado
                    'rgba(153, 102, 255, 0.5)', // Processamento
                    'rgba(0, 0, 0, 0.5)' // Total
                ];

                var ctx = document.getElementById('paymentStatusChartMonth').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '',
                            data: values,
                            backgroundColor: colors,
                        }],
                    },
                    options: {
                        animation: {
                            // Configuração das animações
                            duration: 1000, // Duração da animação em milissegundos
                            easing: 'easeOutBounce' // Tipo de easing (por exemplo, 'linear', 'easeInOutQuart', 'easeOutBounce')
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
                setTimeout(updateChartMonth, 5000);
            },
        });
    }


    $(document).ready(function () {
        updateChartYear();
        updateChartMonth();
    });

    </script>


<script>


function invoiceError(){

$("#modalInvoiceError").modal('show');
    $("#modalInvoiceErrorLabel").html('Faturas com erro');
    var invoice = $(this).data('invoice');
    var url = "{{url('/admin/load-invoice-error')}}";
//console.log(url);
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


  @endsection

  @endsection
