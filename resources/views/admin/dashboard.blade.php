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
                               <h3>0</h3>
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
                            <h3>0</h3>
                            <p>Faturas Pagas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="small-box bg-maroon">
                        <div class="inner">
                            <h3>0</h3>
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
                            <h3>0</h3>
                            <p>Faturas Pendentes</p>
                        </div>
                        <div class="icon">
                            <i class="far fa-hourglass"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="small-box bg-indigo">
                        <div class="inner">
                            <h3>0</h3>
                            <p>Faturas Vencidas</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>0</h3>
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

<div class="col-md-6 col-12">

    <div class="card">
        <div class="card-header border-0">
        <div class="d-flex justify-content-between">
        <h3 class="card-title">Faturas por Status ({{date('Y')}})</h3>
        </div>
        </div>
        <div class="card-body">

            <canvas id="paymentStatusChart"></canvas>

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


    @section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script>


    function updateChart() {
        $.ajax({
            url: '{{url("admin/chart-invoices")}}',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                var labels = Object.keys(data);
                var values = Object.values(data);

                var colors = [
                    'rgba(255, 99, 132, 0.5)', // Pendente
                    'rgba(54, 162, 235, 0.5)', // Pago
                    'rgba(255, 206, 86, 0.5)', // Cancelado
                    'rgba(75, 192, 192, 0.5)', // Expirado
                    'rgba(153, 102, 255, 0.5)' // Processamento
                ];

                var ctx = document.getElementById('paymentStatusChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderColor: colors,
                            borderWidth: 1,
                        }],
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            },
            complete: function () {
                setTimeout(updateChart, 5000);
            },
        });
    }


    $(document).ready(function () {
        updateChart();
    });

    </script>


  @endsection

  @endsection
