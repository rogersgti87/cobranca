@extends('layouts.admin')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">{{ $title }}</li>
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

        <div class="col-md-12">
         <div class="row d-flex justify-content-center">
              <div class="col-md-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total_invoices">0</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="total_pendent">0</h3>
                        <p>Pendente</p>
                    </div>
                    <div class="icon">
                        <i class="far fa-hourglass"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total_pay">0</h3>
                        <p>Paga</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
            </div>


            <div class="col-md-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3 id="total_proccessing">0</h3>
                        <p>Processamento</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-navy">
                    <div class="inner">
                        <h3 id="total_expired">0</h3>
                        <p>Expirada</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="total_cancelled">0</h3>
                        <p>Cancelada</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

        </div>

        </div>

        <div class="col-md-12">

            <div class="form-row">

                <div class="form-group col-md-3 col-6">
                    <label>Tipo</label>
                    <select class="form-control"  id="filter-type">
                        <option value="date_invoice">Data da Fatura</option>
                        <option value="date_due">Data do Vencimento</option>
                    </select>
                </div>

                <div class="form-group col-md-3 col-6">
                    <label>Status</label>
                    <select class="form-control"  id="filter-status">
                        <option value="">Todos</option>
                        <option value="Pendente">Pendente</option>
                        <option value="Processamento">Processamento</option>
                        <option value="Expirado">Expirado</option>
                        <option value="Pago">Pago</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="form-group col-md-2 col-6" id="">
                    <label>Data inicial</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data incial" id="filter-date-ini" value="{{date('Y-m-d',strtotime('first day of this month'))}}">
                </div>

                <div class="form-group col-md-2 col-6" id="">
                    <label>Data final</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data Final" id="filter-date-end" value="{{date('Y-m-d',strtotime('last day of this month'))}}">
                </div>

                <div class="form-group col-md-2 col-12 d-flex justify-content-start align-items-end">
                    <button class="btn btn-secondary mb-2" type="button" id="filter-button"><i class="fa fa-search"></i> Filtrar</button>
                </div>
            </div>


      </div>


      <div class="col-md-12">
        <div class="card-box">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-12">
                    <div id="pagination" class="d-flex justify-content-start align-items-center mb-1">
                        <button class="btn btn-sm btn-secondary" onclick="loadInvoices(prevPage)">Anterior</button>
                        &nbsp;<span id="page-num"></span>&nbsp;
                        <button class="btn btn-sm btn-secondary" onclick="loadInvoices(nextPage)">Próxima</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive fixed-solution">
                <table class="table table-hover table-striped table-sm">
                    <thead class="thead-light">
                    <tr>
                        <th> #</th>
                        <th> Cliente</th>
                        <th> Serviço</th>
                        <th> Data</th>
                        <th> Vencimento</th>
                        <th> Pago em</th>
                        <th> Valor</th>
                        <th> Forma de Pagamento</th>
                        <th> Status</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody class="tbodyCustom" id="list-invoices">

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

@section('scripts')

<script>

$('#btn-delete').click(function (e) {

Swal.fire({
    title: 'Deseja remover este registro?',
    text: "Você não poderá reverter isso!",
    icon: 'question',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, deletar!'
}).then((result) => {
    if (result.value) {
        $.ajax({
            url: "{{url($linkDestroy)}}",
            method: 'DELETE',
            data: $('.form').serialize(),
            success:function(data){
                location.href = "{{url($link)}}";
            },
            error:function (xhr) {

                if(xhr.status === 422){
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'warning',
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                } else{
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'error',
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                }


            }
        });

    }
});

});


</script>


<script>
    const listInvoices          = document.getElementById('list-invoices');
    const filterInputType       = document.getElementById('filter-type');
    const filterInputDateIni    = document.getElementById('filter-date-ini');
    const filterInputDateEnd    = document.getElementById('filter-date-end');
    const filterInputStatus     = document.getElementById('filter-status');
    const filterButton          = document.getElementById('filter-button');
    const paginationContainer   = document.getElementById('pagination');


    function loadInvoices(page = 1) {
        const filterType        = filterInputType.value;
        const filterDateIni     = filterInputDateIni.value;
        const filterDateEnd     = filterInputDateEnd.value;
        const filterStatus      = filterInputStatus.value;

        $.ajax({
        type:'GET',
        url: `load-invoices?page=${page}&type=${filterType}&dateini=${filterDateIni}&dateend=${filterDateEnd}&status=${filterStatus}`,
        beforeSend: function(){
            $('#list-invoices').html('<tr><td style="text-align:center;" colspan="99"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></td></tr>');
            $('#list-invoices').html('');

        },
        success:function(data){
            console.log(data);
        $('#total_invoices').text(data.result.data[0].qtd_invoices);
        $('#total_pendent').text(data.result.data[0].qtd_pendente);
        $('#total_pay').text(data.result.data[0].qtd_pago);
        $('#total_total_proccessing').text(data.result.data[0].qtd_processamento);
        $('#total_cancelled').text(data.result.data[0].qtd_cancelado);


        $('#list-invoices').html('');


        var html = '';
            if(data.result.data.length > 0){
                $.each(data.result.data, function(i, item) {
                html += '<tr>';
                html += `<td>${item.id}</td>`;
                html += `<td>${item.customer_name}</td>`;
                html += `<td>${item.service_name}</td>`;
                html += `<td>${moment(item.date_invoice).format('DD/MM/YYYY')}</td>`;
                html += `<td>${moment(item.date_due).format('DD/MM/YYYY')}</td>`;
                html += `<td>${item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-'}</td>`;
                html += `<td>${item.price}</td>`;
                html += `<td>${item.gateway_payment+' ('+item.payment_method })</td>`;
                html += `<td class="badge ${item.status == 'Pago' ? 'badge-success' : item.status == 'Pendente' ? 'badge-warning' : 'badge-danger'}">${item.status}</td>`;
                html += '</tr>';
            });
            }else{
                html += '<tr><td style="text-align:center;" colspan="99">Fatura não encontrada!</tr>';
            }

            $('#list-invoices').append(html);

                currentPage = data.result.current_page;
                prevPage = data.result.prev_page_url ? currentPage - 1 : currentPage;
                nextPage = data.result.next_page_url ? currentPage + 1 : currentPage;

                document.getElementById('page-num').textContent = `Página ${currentPage}`;

                document.getElementById('pagination').querySelector('button:first-child').disabled = !data.result.prev_page_url;
                document.getElementById('pagination').querySelector('button:last-child').disabled = !data.result.next_page_url;


            },
            error:function (xhr) {
                $('#list-invoices').append('<tr><td style="text-align:center;" colspan="99">Fatura não encontrada!</tr>');
            }
            });

    }

    filterButton.addEventListener('click', () => {
        currentPage = 1;
        loadInvoices(currentPage);
    });

    // filterOperatorInput.addEventListener('change', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });

    // filterValueInput.addEventListener('input', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });

    // sortSelect.addEventListener('change', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });

    // directionSelect.addEventListener('change', () => {
    //     currentPage = 1;
    //     loadInvoices(currentPage);
    // });


    loadInvoices();


</script>



@endsection


@endsection
