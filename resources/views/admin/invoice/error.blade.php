<style>
    table.dataTable th {
      font-size: 12px;
    }
    table.dataTable td {
      font-size: 11px;
    }


    </style>


    <div class="table-responsive">
        <table class="display table-striped compact" style="width:100%" id="notifications-table">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Cliente</th>
                    <th>Serviço</th>
                    <th>Data</th>
                    <th>Vencimento</th>
                    <th>Gateway Pagamento</th>
                    <th>Método de pagamento</th>
                    <th>Erro</th>
                    <th width="100"></th>
                </tr>
            </thead>
            <tbody>



                @foreach ($data as $result)
                    <tr>
                        <td>{{ $result->id }}</td>
                        <td>{{ $result->name }}</td>
                        <td>{{ $result->service_name.' - '.$result->description }}</td>
                        <td>{{ date('d/m/Y',strtotime($result->date_invoice))}}</td>
                        <td>{{ date('d/m/Y',strtotime($result->date_due))}}</td>
                        <td>{{ $result->gateway_payment }}</td>
                        <td>{{ $result->payment_method }}</td>
                        <td>{{ $result->msg_erro}}</td>
                        <td>
                            <a href="{{ url('admin/customers/form?act=edit&id='.$result->customer_id)}}" data-original-title="Editar cliente" id="btn-edit-customer" data-placement="left" data-tt="tooltip" class="btn btn-secondary btn-xs"> <i class="fas fa-user"></i> Editar</a>
                        </td>
                    </tr>

                @endforeach

            </tbody>
        </table>
    </div><!-- table-responsive -->

    <script>

   $('#notifications-table').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
            },
        });

        </script>

