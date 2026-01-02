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
              <li class="breadcrumb-item active" style="color: #1F2937; opacity: 0.7;">{{ $title }}</li>
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
              <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Total</p>
                            <h5 id="total_payables_curerency" style="color: #1F2937; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_payables" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-receipt" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Pendentes</p>
                            <h5 id="pendent_payables_curerency" style="color: #FFBD59; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_pendent" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-hourglass" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Pagas</p>
                            <h5 id="pay_payables_curerency" style="color: #22C55E; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_pay" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(34,197,94,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="color: #22C55E; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Fixas</p>
                            <h5 id="fixed_payables_curerency" style="color: #1F2937; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_fixed" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Recorrentes</p>
                            <h5 id="recurring_payables_curerency" style="color: #1F2937; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_recurring" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-sync-alt" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #1F2937; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Canceladas</p>
                            <h5 id="cancelled_payables_curerency" style="color: #F87171; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_cancelled" style="color: #1F2937; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
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
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-next-month" data-filter="next-month" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-calendar-alt"></i> Próximo Mês
                        </button>
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-all" data-filter="all" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-list"></i> Todos
                        </button>
                    </div>
                </div>
                <a href="#" data-original-title="Nova Conta a Pagar" id="btn-modal-payable" data-type="add-payable" data-toggle="tooltip" class="btn" style="background-color: #FFBD59; color: #1F2937 !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;"> <i class="fa fa-plus"></i> Nova Conta a Pagar</a>
            </div>

            <div class="collapse mb-4" id="filtersCollapse">
                <div class="form-row" style="background-color: #F5F5DC; padding: 20px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1);">

                <div class="form-group col-md-4 col-6">
                    <label style="color: #1F2937; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Tipo de Data</label>
                    <select class="form-control" id="filter-type" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937;">
                        <option value="date_due">Data do Vencimento</option>
                        <option value="date_payment">Data do Pagamento</option>
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
                                <legend style="color: #FFBD59; font-size: 14px; font-weight: 600; padding: 0 10px; margin: 0; border: none;">Tipo de Conta</legend>
                                <div class="d-flex" style="gap: 20px; margin-top: 10px; flex-wrap: nowrap; overflow-x: auto;">
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="type-checkbox" value="Fixa" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Fixa
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="type-checkbox" value="Recorrente" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Recorrente
                                    </label>
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="type-checkbox" value="Parcelada" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Parcelada
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
                                        <input type="checkbox" class="status-checkbox" value="Cancelado" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Cancelado
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-12 mt-3">
                    <fieldset style="border: 1px solid rgba(255,189,89,0.5); border-radius: 8px; padding: 15px; margin: 0; background-color: #FFFFFF; position: relative;">
                        <legend style="color: #FFBD59; font-size: 14px; font-weight: 600; padding: 0 10px; margin: 0; border: none;">Categorias</legend>
                        <div class="mb-3" style="margin-bottom: 15px !important;">
                            <label style="color: #1F2937; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center;">
                                <input type="checkbox" class="category-checkbox" id="selectAllCategories" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                Selecionar Todas
                            </label>
                        </div>
                        <div class="d-flex" style="gap: 20px; margin-top: 10px; flex-wrap: wrap; overflow-x: auto;">
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $category)
                                    <label style="color: #1F2937; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="category-checkbox" value="{{ $category->id }}" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        <span style="display: inline-block; width: 12px; height: 12px; background-color: {{ $category->color }}; border-radius: 2px; margin-right: 6px;"></span>
                                        {{ $category->name }}
                                    </label>
                                @endforeach
                            @else
                                <p style="color: #6B7280; font-size: 14px; margin: 0;">Nenhuma categoria cadastrada</p>
                            @endif
                        </div>
                    </fieldset>
                </div>
            </div>
            </div>
        </div>

      <div class="col-md-12 mb-4">
        <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h5 style="color: #1F2937; font-weight: 600; margin-bottom: 20px;">
                <i class="fas fa-chart-pie"></i> Total por Categorias
            </h5>
            <div style="position: relative; height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
      </div>

      <div class="col-md-12">
        <div style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div class="row d-flex justify-content-center align-items-center mb-3">
                <div class="col-12">
                    <div id="pagination" class="d-flex justify-content-start align-items-center">
                        <button class="btn btn-sm" onclick="loadPayables(prevPage)" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 6px 12px; margin-right: 10px; font-weight: 500;">Anterior</button>
                        <span id="page-num" style="color: #1F2937; font-weight: 500; margin: 0 10px;"></span>
                        <button class="btn btn-sm" onclick="loadPayables(nextPage)" style="background-color: #FFFFFF; color: #1F2937; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 6px 12px; font-weight: 500;">Próxima</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                    <tr style="border-bottom: 2px solid rgba(0,0,0,0.1);">
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">#</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Fornecedor</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Descrição</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Categoria</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Tipo</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Vencimento</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Pago em</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Valor</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Forma de Pagamento</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Status</th>
                        <th style="color: #1F2937; font-weight: 600; padding: 12px; border: none; font-size: 14px;"></th>
                    </tr>
                    </thead>

                    <tbody id="list-payables" style="background-color: #F5F5DC;">

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



<!-- Modal :: Form Payable -->
<div class="modal fade" id="modalPayable" tabindex="-1" role="dialog" aria-labelledby="modalPayableLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
            <form action="" class="form-horizontal" id="form-request-payable">
                <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                    <h5 class="modal-title" id="modalPayableLabel" style="color: #FFBD59; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #1F2937;">
                        <span aria-hidden="true" style="color: #1F2937;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-payable" style="background-color: #F5F5DC;">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer" style="background-color: #FFFFFF; border-top: 1px solid rgba(0,0,0,0.1);">
                    <button type="button" class="btn" id="btn-save-payable" style="background-color: #22C55E; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-check"></i> Salvar</button>
                    <button type="button" class="btn" data-dismiss="modal" style="background-color: #F87171; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
 </div>
 <!-- Modal :: Form Payable -->



  <!-- Modal :: Form Notifications -->
  <div class="modal fade" id="modalNotifications" tabindex="-1" role="dialog" aria-labelledby="modalNotificationsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
            <form action="" class="form-horizontal" id="form-request-notifications">
                <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                    <h5 class="modal-title" id="modalNotificationsLabel" style="color: #FFBD59; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #1F2937;">
                        <span aria-hidden="true" style="color: #1F2937;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-notifications" style="background-color: #F5F5DC;">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer" style="background-color: #FFFFFF; border-top: 1px solid rgba(0,0,0,0.1);">
                    <button type="button" class="btn" data-dismiss="modal" style="background-color: #F87171; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-times"></i> Fechar</button>
                </div>
            </form>
        </div>
    </div>
 </div>
 <!-- Modal :: Form Notifications -->

   <!-- Modal :: Log -->
   <div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modalErrorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalErrorLabel" style="color: #FFBD59; font-weight: 600;"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #1F2937;">
                    <span aria-hidden="true" style="color: #1F2937;">&times;</span>
                </button>
            </div>

            <style>

                pre {
                   background-color: #FFFFFF;
                   border: 1px solid rgba(0,0,0,0.1);
                   padding: 10px 20px;
                   margin: 20px;
                   color: #1F2937;
                   }
                .json-key {
                   color: #FFBD59;
                   }
                .json-value {
                   color: #22C55E;
                   }
                .json-string {
                   color: #F87171;
                   }

                </style>

            <div class="modal-body" id="modal-content-error" style="background-color: #F5F5DC; color: #1F2937;">

                <!-- conteudo -->
                <!-- conteudo -->
            </div><!-- modal-body -->
        </div>
    </div>
 </div>
 <!-- Modal :: Log -->


@section('styles')
<style>
    /* Estilos Ivory Theme para Contas a Pagar */
    body {
        background-color: #FFFFF0 !important;
    }

    .content-wrapper {
        background-color: #FFFFF0 !important;
    }

    .content-header {
        background-color: #FFFFF0 !important;
    }

    .content {
        background-color: #FFFFF0 !important;
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5) !important;
    }

    #btn-modal-payable:hover {
        background-color: #FFD700 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(255, 189, 89, 0.3);
    }


    #pagination button:hover:not(:disabled) {
        background-color: #FFBD59 !important;
        border-color: #FFBD59 !important;
        color: #1F2937 !important;
    }

    #pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .form-control:focus {
        border-color: #FFBD59 !important;
        box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
        outline: none;
        background-color: #FFFFFF !important;
        color: #1F2937 !important;
    }

    select.form-control:focus,
    input.form-control:focus {
        border-color: #FFBD59 !important;
        box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
        outline: none;
        background-color: #FFFFFF !important;
        color: #1F2937 !important;
    }

    select.form-control,
    input.form-control {
        background-color: #FFFFFF !important;
        color: #1F2937 !important;
    }

    select.form-control option {
        background-color: #FFFFFF !important;
        color: #1F2937 !important;
    }

    select.form-control option:checked {
        background-color: #F5F5DC !important;
        color: #1F2937 !important;
    }

    /* Força a cor escura em todos os estados do select */
    select {
        color: #1F2937 !important;
    }

    select:focus {
        color: #1F2937 !important;
    }

    select option {
        color: #1F2937 !important;
    }

    select option:checked {
        color: #1F2937 !important;
    }

    /* Estilos para checkboxes de status e tipo */
    .status-checkbox,
    .type-checkbox,
    .category-checkbox {
        accent-color: #FFBD59;
        cursor: pointer;
    }

    .status-checkbox:checked,
    .type-checkbox:checked,
    .category-checkbox:checked {
        background-color: #FFBD59;
    }

    /* Botões de filtro rápido */
    #btn-filter-current-month:hover,
    #btn-filter-next-month:hover,
    #btn-filter-all:hover {
        background-color: #FFBD59 !important;
        color: #1F2937 !important;
        border-color: #FFBD59 !important;
    }

    /* Gap para flexbox */
    .gap-2 {
        gap: 0.5rem;
    }

    /* Estilos para fieldsets */
    fieldset {
        transition: border-color 0.3s ease;
    }

    fieldset:hover {
        border-color: rgba(255,189,89,0.5) !important;
    }

    legend {
        background-color: #F5F5DC !important;
    }

    @media (max-width: 768px) {
        .col-md-6 {
            margin-bottom: 15px;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let categoryChart = null;

    // Open Modal - Create/Edit Payable
    $(document).on("click", "#btn-modal-payable", function() {
          var type = $(this).data('type');
          var supplier_id = "{{ isset($supplier_id) ? $supplier_id : ''}}";
          $("#modalPayable").modal('show');
          if(type == 'add-payable'){
              $("#modalPayableLabel").html('Adicionar Conta a Pagar');
              var url = '{{ url("/admin/payables/form?supplier_id=") }}'+supplier_id;
          }else{
              $("#modalPayableLabel").html('Editar Conta a Pagar');
              var payable = $(this).data('payable');
              var url = '{{ url("/admin/payables/form?supplier_id=") }}'+supplier_id+'&id='+payable;
          }

          $.get(url,
              $(this)
              .addClass('modal-scrollfix')
              .find('#form-content-payable')
              .html('Carregando...'),
              function(data) {
                  $("#form-content-payable").html(data);
                  var maskOptions = {};
                  maskOptions.reverse = true;
                  $('.money').mask('000.000.000.000.000,00', maskOptions);
              });
      });

  </script>


<script>
</script>

<script>


   // Open Modal - Create/Edit Payable
   $(document).on("click", "#btn-modal-payable", function() {
        var type = $(this).data('type');
        var supplier_id = "{{ isset($supplier_id) ? $supplier_id : ''}}";
        $("#modalPayable").modal('show');
        if(type == 'add-payable'){
            $("#modalPayableLabel").html('Adicionar Conta a Pagar');
            var url = '{{ url("/admin/payables/form?supplier_id=") }}'+supplier_id;
        }else{
            $("#modalPayableLabel").html('Editar Conta a Pagar');
            var payable = $(this).data('payable');
            var url = '{{ url("/admin/payables/form?supplier_id=") }}'+supplier_id+'&id='+payable;
        }

        $.get(url,
            $(this)
            .addClass('modal-scrollfix')
            .find('#form-content-payable')
            .html('Carregando...'),
            function(data) {
                $("#form-content-payable").html(data);
                var maskOptions = {};
                maskOptions.reverse = true;
                $('.money').mask('000.000.000.000.000,00', maskOptions);
            });
    });




$('#btn-delete').click(function (e) {

Swal.fire({
    title: 'Deseja remover este registro?',
    text: "Você não poderá reverter isso!",
    icon: 'question',
    showCancelButton: true,
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#2563EB',
    cancelButtonColor: '#D1D5DB',
    confirmButtonText: 'Sim, deletar!'
}).then(function(result) {
    if (result.value) {
        $.ajax({
            url: "{{url($linkDestroy)}}",
            method: 'DELETE',
            data: $('.form').serialize(),
            success:function(data){
                location.href = "{{url($link)}}";
            },
            error:function (xhr) {
                var showClassWobbleErr = {};
                showClassWobbleErr.popup = 'animate__animated animate__wobble';
                if(xhr.status === 422){
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'warning',
                        showClass: showClassWobbleErr
                    });
                } else{
                    Swal.fire({
                        text: xhr.responseJSON,
                        icon: 'error',
                        showClass: showClassWobbleErr
                    });
                }
            }
        });

    }
});

});




     //Save Payable
     $(document).on('click', '#btn-save-payable', function(e) {
            e.preventDefault();

            $("#btn-save-payable").attr("disabled", true);
            $("#btn-save-payable").text('Aguarde...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            var data = $('#form-request-payable').serialize();
            var payable = $('#payable').val();
            var baseUrl = "{{ url('admin/payables') }}";
            if(payable != ''){
                var url = baseUrl + '/' + payable;
                var method = 'PUT';
            }else{
                var url = baseUrl;
                var method = 'POST';
            }

            $.ajax({
                url: url,
                data: data,
                method: method,
                beforeSend: function(){
                    $("#btn-save-payable").attr("disabled", true);
                    $("#btn-save-payable").text('Aguarde...');
                },
                success: function(data){
                    $("#btn-save-payable").attr("disabled", false);
                    $("#btn-save-payable").html('<i class="fa fa-check"></i> Salvar');
                    var showClassObj = {};
                    showClassObj.popup = 'animate__animated animate__backInUp';
                    var titleHtml = "<h5 style='color:#FFBD59'>" + data + "</h5>";
                    Swal.fire({
                        width: 350,
                        title: titleHtml,
                        icon: 'success',
                        showConfirmButton: true,
                        showClass: showClassObj,
                        allowOutsideClick: false
                    }).then(function(result) {
                        $('#modalPayable').modal('hide');
                        loadPayables();
                    });
                },
                error: function(xhr) {
                    $("#btn-save-payable").attr("disabled", false);
                    $("#btn-save-payable").html('<i class="fa fa-check"></i> Salvar');
                    var showClassWarn = {};
                    showClassWarn.popup = 'animate__animated animate__wobble';
                    var showClassError = {};
                    showClassError.popup = 'animate__animated animate__wobble';
                    if(xhr.status === 422){
                        Swal.fire({
                            text: xhr.responseJSON,
                            width: 300,
                            icon: 'warning',
                            color: '#1F2937',
                            confirmButtonColor: "#2563EB",
                            showClass: showClassWarn
                        });
                    } else{
                        Swal.fire({
                            text: xhr.responseJSON,
                            width: 300,
                            icon: 'error',
                            color: '#1F2937',
                            confirmButtonColor: "#2563EB",
                            showClass: showClassError
                        });
                    }
                }
            });
        });



</script>


<script>
    const listPayables          = document.getElementById('list-payables');
    const filterInputType       = document.getElementById('filter-type');
    const filterInputDateIni    = document.getElementById('filter-date-ini');
    const filterInputDateEnd    = document.getElementById('filter-date-end');

    // Definir datas do mês atual por padrão
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    filterInputDateIni.value = firstDay.toISOString().split('T')[0];
    filterInputDateEnd.value = lastDay.toISOString().split('T')[0];

    // Inicializar com todos os status marcados, exceto Cancelado
    document.querySelectorAll('.status-checkbox').forEach(function(cb) {
        if(cb.value !== 'Cancelado') {
            cb.checked = true;
        } else {
            cb.checked = false;
        }
    });

    document.querySelectorAll('.type-checkbox').forEach(function(cb) {
        cb.checked = true;
    });

    // Categorias não vêm marcadas por padrão
    // Selecionar todas as categorias
    var selectAllCategories = document.getElementById('selectAllCategories');
    if(selectAllCategories) {
        selectAllCategories.addEventListener('change', function() {
            document.querySelectorAll('.category-checkbox:not(#selectAllCategories)').forEach(function(cb) {
                cb.checked = this.checked;
            }.bind(this));
            currentPage = 1;
            loadPayables(currentPage);
        });
    }

    const paginationContainer   = document.getElementById('pagination');


    function loadPayables(page = 1) {
        const filterType        = filterInputType.value;
        const filterDateIni     = filterInputDateIni.value;
        const filterDateEnd     = filterInputDateEnd.value;

        // Coletar status selecionados dos checkboxes
        const statusCheckboxes = document.querySelectorAll('.status-checkbox:checked');
        const filterStatus = Array.from(statusCheckboxes).map(cb => cb.value);

        // Coletar tipos selecionados dos checkboxes
        const typeCheckboxes = document.querySelectorAll('.type-checkbox:checked');
        const filterTypes = Array.from(typeCheckboxes).map(cb => cb.value);

        // Coletar categorias selecionadas dos checkboxes (excluindo "Selecionar Todas")
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox:checked:not(#selectAllCategories)');
        const filterCategories = Array.from(categoryCheckboxes).map(cb => cb.value);

        // Construir URL com múltiplos status, tipos e categorias
        let url = 'load-payables?page='+page+'&type='+filterType;
        if(filterDateIni && filterDateEnd){
            url += '&dateini='+filterDateIni+'&dateend='+filterDateEnd;
        }
        if(filterStatus.length > 0){
            filterStatus.forEach(function(status, index) {
                url += '&status['+index+']='+status;
            });
        }
        if(filterTypes.length > 0){
            filterTypes.forEach(function(type, index) {
                url += '&payable_type['+index+']='+type;
            });
        }
        if(filterCategories.length > 0){
            filterCategories.forEach(function(category, index) {
                url += '&category['+index+']='+category;
            });
        }

        $.ajax({
        type:'GET',
        url: url,
        beforeSend: function(){
            $('#list-payables').html('<tr><td style="text-align:center;" colspan="99"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></td></tr>');
            $('#list-payables').html('');
        },
        success:function(data){
            var currencyOptions = {};
            currencyOptions.style = 'currency';
            currencyOptions.currency = 'BRL';

            // Atualizar gráfico de categorias
            if(data.categories) {
                updateCategoryChart(data.categories);
            }

            if(data.result.data.length == 0){
                $('#total_payables').text('0 contas');
                $('#total_payables_curerency').text(parseFloat(0).toLocaleString('pt-BR', currencyOptions));
                $('#total_pendent').text('0 contas');
                $('#pendent_payables_curerency').text(parseFloat(0).toLocaleString('pt-BR', currencyOptions));
                $('#total_pay').text('0 contas');
                $('#pay_payables_curerency').text(parseFloat(0).toLocaleString('pt-BR', currencyOptions));
                $('#total_fixed').text('0 contas');
                $('#fixed_payables_curerency').text(parseFloat(0).toLocaleString('pt-BR', currencyOptions));
                $('#total_recurring').text('0 contas');
                $('#recurring_payables_curerency').text(parseFloat(0).toLocaleString('pt-BR', currencyOptions));
                $('#total_cancelled').text('0 contas');
                $('#cancelled_payables_curerency').text(parseFloat(0).toLocaleString('pt-BR', currencyOptions));
            }else{
                $('#total_payables').text(data.result.data[0].qtd_payables + ' contas');
                $('#total_payables_curerency').text(parseFloat(data.result.data[0].total_currency).toLocaleString('pt-BR', currencyOptions));
                $('#total_pendent').text(data.result.data[0].qtd_pendente + ' contas');
                $('#pendent_payables_curerency').text(parseFloat(data.result.data[0].pendente_currency).toLocaleString('pt-BR', currencyOptions));
                $('#total_pay').text(data.result.data[0].qtd_pago + ' contas');
                $('#pay_payables_curerency').text(parseFloat(data.result.data[0].pago_currency).toLocaleString('pt-BR', currencyOptions));
                $('#total_fixed').text(data.result.data[0].qtd_fixed + ' contas');
                $('#fixed_payables_curerency').text(parseFloat(data.result.data[0].fixed_currency || 0).toLocaleString('pt-BR', currencyOptions));
                $('#total_recurring').text(data.result.data[0].qtd_recurring + ' contas');
                $('#recurring_payables_curerency').text(parseFloat(data.result.data[0].recurring_currency || 0).toLocaleString('pt-BR', currencyOptions));
                $('#total_cancelled').text(data.result.data[0].qtd_cancelado + ' contas');
                $('#cancelled_payables_curerency').text(parseFloat(data.result.data[0].cancelado_currency).toLocaleString('pt-BR', currencyOptions));
            }
        $('#list-payables').html('');

        var html = '';
            if(data.result.data.length > 0){
                $.each(data.result.data, function(i, item) {
                var installmentText = item.installment_number ? ' ('+item.installment_number+'/'+item.installments+')' : '';
                var typeBadgeColor = item.type == 'Fixa' ? '#FFBD59' : item.type == 'Recorrente' ? '#22C55E' : '#1F2937';
                var typeBadgeBg = item.type == 'Fixa' ? 'rgba(255,189,89,0.2)' : item.type == 'Recorrente' ? 'rgba(34,197,94,0.2)' : 'rgba(229,231,235,0.1)';
                var statusBadgeColor = item.status == 'Pago' ? '#22C55E' : item.status == 'Pendente' ? '#FFBD59' : '#F87171';
                var statusBadgeBg = item.status == 'Pago' ? 'rgba(34,197,94,0.2)' : item.status == 'Pendente' ? 'rgba(255,189,89,0.2)' : 'rgba(248,113,113,0.2)';
                var datePayment = item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-';
                var editButton = item.status == 'Pendente' ? '<a href="#" data-original-title="Editar conta" id="btn-modal-payable" data-type="edit-payable" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="background-color: #FFBD59; color: #1F2937 !important; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="far fa-edit"></i></a>' : '';
                var deleteButton = item.status == 'Pendente' ? '<a href="#" data-original-title="Cancelar Conta" id="btn-delete-payable" data-placement="left" data-payable="'+item.id+'" data-tt="tooltip" style="background-color: #F87171; color: #FFFFFF; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="fas fa-times"></i></a>' : '';

                // Botão para estornar (apenas para contas pagas)
                var reverseButton = '';
                var viewReversalsButton = '';
                if(item.status == 'Pago') {
                    reverseButton = '<a href="#" data-original-title="Estornar Pagamento" id="btn-reverse-payable" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="background-color: #F59E0B; color: #FFFFFF; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="fas fa-undo"></i></a>';
                    viewReversalsButton = '<a href="#" data-original-title="Ver Histórico de Estornos" id="btn-view-reversals" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="background-color: #6366F1; color: #FFFFFF; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="fas fa-history"></i></a>';
                }

                // Botão para parar recorrência (apenas para contas recorrentes que ainda não têm data de término definida)
                var stopRecurrenceButton = '';
                if(item.type == 'Recorrente' && (!item.recurrence_end || item.recurrence_end == null || item.recurrence_end == '') && item.status == 'Pendente') {
                    stopRecurrenceButton = '<a href="#" data-original-title="Parar Recorrência" id="btn-stop-recurrence" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="background-color: #F59E0B; color: #FFFFFF; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="fas fa-stop-circle"></i></a>';
                }
                var categoryColor = item.category_color || '#FFBD59';
                var categoryBadge = item.category_name ? '<span style="background-color: rgba('+parseInt(categoryColor.slice(1,3),16)+','+parseInt(categoryColor.slice(3,5),16)+','+parseInt(categoryColor.slice(5,7),16)+',0.2); color: '+categoryColor+'; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 6px;"><span style="display: inline-block; width: 8px; height: 8px; background-color: '+categoryColor+'; border-radius: 50%;"></span>'+item.category_name+'</span>' : '<span style="color: #6B7280; font-size: 12px;">-</span>';

                html += '<tr style="border-bottom: 1px solid rgba(0,0,0,0.1); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#FFFFFF\'" onmouseout="this.style.backgroundColor=\'#F5F5DC\'">';
                html += '<td style="padding: 12px; color: #1F2937; font-size: 14px;">'+item.id+installmentText+'</td>';
                html += '<td style="padding: 12px; color: #1F2937; font-size: 14px; font-weight: 500;">'+item.supplier_name+'</td>';
                html += '<td style="padding: 12px; color: #1F2937; font-size: 14px;">'+item.description+'</td>';
                html += '<td style="padding: 12px;">'+categoryBadge+'</td>';
                html += '<td style="padding: 12px;"><span style="background-color: '+typeBadgeBg+'; color: '+typeBadgeColor+'; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">'+item.type+'</span></td>';
                html += '<td style="padding: 12px; color: #1F2937; font-size: 14px;">'+moment(item.date_due).format('DD/MM/YYYY')+'</td>';
                html += '<td style="padding: 12px; color: #1F2937; font-size: 14px;">'+datePayment+'</td>';
                var priceOptions = {};
                priceOptions.minimumFractionDigits = 2;
                var priceFormatted = parseFloat(item.price).toLocaleString('pt-br', priceOptions);
                html += '<td style="padding: 12px; color: #FFBD59; font-size: 14px; font-weight: 600;">R$ '+priceFormatted+'</td>';
                html += '<td style="padding: 12px; color: #1F2937; font-size: 14px;">'+(item.payment_method || '-')+'</td>';
                html += '<td style="padding: 12px;"><span style="background-color: '+statusBadgeBg+'; color: '+statusBadgeColor+'; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">'+item.status+'</span></td>';
                html += '<td style="padding: 12px;">'+editButton+reverseButton+viewReversalsButton+stopRecurrenceButton+deleteButton+'</td>';
                html += '</tr>';
            });
            }else{
                html += '<tr><td style="text-align:center; padding: 40px; color: #1F2937; font-size: 14px;" colspan="99">Nenhuma conta encontrada</td></tr>';
            }

            $('#list-payables').append(html);
            $('[data-tt="tooltip"]').tooltip();

                currentPage = data.result.current_page;
                prevPage = data.result.prev_page_url ? currentPage - 1 : currentPage;
                nextPage = data.result.next_page_url ? currentPage + 1 : currentPage;

                document.getElementById('page-num').textContent = 'Página '+currentPage;

                document.getElementById('pagination').querySelector('button:first-child').disabled = !data.result.prev_page_url;
                document.getElementById('pagination').querySelector('button:last-child').disabled = !data.result.next_page_url;
            },
            error:function (xhr) {
                $('#list-payables').append('<tr><td style="text-align:center; padding: 40px; color: #F87171; font-size: 14px;" colspan="99">Erro ao carregar contas</td></tr>');
            }
            });
    }

    // Filtro automático - tipo de data
    filterInputType.addEventListener('change', function() {
        currentPage = 1;
        loadPayables(currentPage);
    });

    // Filtro automático - data inicial
    filterInputDateIni.addEventListener('change', function() {
        currentPage = 1;
        loadPayables(currentPage);
    });

    // Filtro automático - data final
    filterInputDateEnd.addEventListener('change', function() {
        currentPage = 1;
        loadPayables(currentPage);
    });

    // Filtro de status - carregar automaticamente quando mudar
    document.querySelectorAll('.status-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            currentPage = 1;
            loadPayables(currentPage);
        });
    });

    // Filtro de tipo - carregar automaticamente quando mudar
    document.querySelectorAll('.type-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            currentPage = 1;
            loadPayables(currentPage);
        });
    });

    // Filtro de categoria - carregar automaticamente quando mudar (exceto "Selecionar Todas")
    document.querySelectorAll('.category-checkbox:not(#selectAllCategories)').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // Atualizar "Selecionar Todas" baseado no estado das categorias
            var allChecked = true;
            var hasAnyChecked = false;
            document.querySelectorAll('.category-checkbox:not(#selectAllCategories)').forEach(function(cb) {
                if(cb.checked) {
                    hasAnyChecked = true;
                } else {
                    allChecked = false;
                }
            });
            if(selectAllCategories) {
                selectAllCategories.checked = allChecked && hasAnyChecked;
            }

            currentPage = 1;
            loadPayables(currentPage);
        });
    });

    // Função para atualizar o destaque dos filtros rápidos
    function updateQuickFilterHighlight() {
        // Remover destaque de todos os botões
        document.querySelectorAll('.filter-quick-btn').forEach(function(btn) {
            btn.style.border = '1px solid rgba(0,0,0,0.1)';
            btn.style.borderWidth = '1px';
        });

        const dateIni = filterInputDateIni.value;
        const dateEnd = filterInputDateEnd.value;

        if (!dateIni || !dateEnd) {
            // Se não há datas, destacar "Todos"
            document.getElementById('btn-filter-all').style.border = '2px solid #FFBD59';
            document.getElementById('btn-filter-all').style.borderWidth = '2px';
            return;
        }

        const now = new Date();
        const currentMonthFirst = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
        const currentMonthLast = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
        const nextMonthFirst = new Date(now.getFullYear(), now.getMonth() + 1, 1).toISOString().split('T')[0];
        const nextMonthLast = new Date(now.getFullYear(), now.getMonth() + 2, 0).toISOString().split('T')[0];

        if (dateIni === currentMonthFirst && dateEnd === currentMonthLast) {
            document.getElementById('btn-filter-current-month').style.border = '2px solid #FFBD59';
            document.getElementById('btn-filter-current-month').style.borderWidth = '2px';
        } else if (dateIni === nextMonthFirst && dateEnd === nextMonthLast) {
            document.getElementById('btn-filter-next-month').style.border = '2px solid #FFBD59';
            document.getElementById('btn-filter-next-month').style.borderWidth = '2px';
        }
    }

    // Botão Mês Atual
    document.getElementById('btn-filter-current-month').addEventListener('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        filterInputDateIni.value = firstDay.toISOString().split('T')[0];
        filterInputDateEnd.value = lastDay.toISOString().split('T')[0];

        updateQuickFilterHighlight();
        currentPage = 1;
        loadPayables(currentPage);
    });

    // Botão Próximo Mês
    document.getElementById('btn-filter-next-month').addEventListener('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 2, 0);

        filterInputDateIni.value = firstDay.toISOString().split('T')[0];
        filterInputDateEnd.value = lastDay.toISOString().split('T')[0];

        updateQuickFilterHighlight();
        currentPage = 1;
        loadPayables(currentPage);
    });

    // Botão Todos
    document.getElementById('btn-filter-all').addEventListener('click', function() {
        filterInputDateIni.value = '';
        filterInputDateEnd.value = '';

        // Marcar todos os checkboxes de status, tipo e categoria
        document.querySelectorAll('.status-checkbox').forEach(function(cb) {
            cb.checked = true;
        });

        document.querySelectorAll('.type-checkbox').forEach(function(cb) {
            cb.checked = true;
        });

        document.querySelectorAll('.category-checkbox').forEach(function(cb) {
            cb.checked = true;
        });

        updateQuickFilterHighlight();
        currentPage = 1;
        loadPayables(currentPage);
    });

    // Atualizar destaque quando as datas mudarem manualmente
    filterInputDateIni.addEventListener('change', function() {
        updateQuickFilterHighlight();
    });

    filterInputDateEnd.addEventListener('change', function() {
        updateQuickFilterHighlight();
    });

    // Atualizar destaque ao carregar a página (já que definimos mês atual por padrão)
    updateQuickFilterHighlight();

    loadPayables();


</script>

<!-- Modal para confirmação de exclusão -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="modalConfirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalConfirmDeleteLabel" style="color: #FFBD59; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Cancelamento
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #1F2937;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: #1F2937;">
                <p id="confirmDeleteMessage" style="margin-bottom: 0;"></p>
            </div>
            <div class="modal-footer" style="background-color: #FFFFFF; border-top: 1px solid rgba(0,0,0,0.1);">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: #FFFFFF;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteSingle" style="background-color: #F87171; border-color: #F87171; color: #FFFFFF;">
                    <i class="fas fa-trash"></i> Sim, Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para histórico de estornos -->
<div class="modal fade" id="modalViewReversals" tabindex="-1" role="dialog" aria-labelledby="modalViewReversalsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalViewReversalsLabel" style="color: #FFBD59; font-weight: 600;">
                    <i class="fas fa-history"></i> Histórico de Estornos
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #1F2937;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: #1F2937;">
                <div id="reversalsList" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                    <!-- Lista de estornos será inserida aqui via JavaScript -->
                </div>
                <style>
                    #reversalsList::-webkit-scrollbar {
                        width: 8px;
                    }
                    #reversalsList::-webkit-scrollbar-track {
                        background: #FFFFFF;
                        border-radius: 4px;
                    }
                    #reversalsList::-webkit-scrollbar-thumb {
                        background: #D1D5DB;
                        border-radius: 4px;
                    }
                    #reversalsList::-webkit-scrollbar-thumb:hover {
                        background: #9CA3AF;
                    }
                </style>
            </div>
            <div class="modal-footer" style="background-color: #FFFFFF; border-top: 1px solid rgba(0,0,0,0.1);">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: #FFFFFF;">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exclusão de parcelas -->
<div class="modal fade" id="modalDeleteInstallments" tabindex="-1" role="dialog" aria-labelledby="modalDeleteInstallmentsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color: #F5F5DC; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalDeleteInstallmentsLabel" style="color: #FFBD59; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Selecionar Parcelas para Cancelar
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #1F2937;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: #1F2937;">
                <p style="margin-bottom: 20px;">Selecione quais parcelas deseja cancelar:</p>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="selectAllInstallments" style="background-color: #FFFFFF; border-color: #FFBD59;">
                    <label class="form-check-label" for="selectAllInstallments" style="color: #1F2937; font-weight: 600;">
                        Selecionar Todas
                    </label>
                </div>
                <div id="installmentsList" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                    <!-- Lista de parcelas será inserida aqui via JavaScript -->
                </div>
                <style>
                    #installmentsList::-webkit-scrollbar {
                        width: 8px;
                    }
                    #installmentsList::-webkit-scrollbar-track {
                        background: #FFFFFF;
                        border-radius: 4px;
                    }
                    #installmentsList::-webkit-scrollbar-thumb {
                        background: #D1D5DB;
                        border-radius: 4px;
                    }
                    #installmentsList::-webkit-scrollbar-thumb:hover {
                        background: #9CA3AF;
                    }
                    .list-group-item:hover {
                        background-color: #FFFFFF !important;
                    }
                </style>
            </div>
            <div class="modal-footer" style="background-color: #FFFFFF; border-top: 1px solid rgba(0,0,0,0.1);">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: #FFFFFF;">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteInstallments" style="background-color: #F87171; border-color: #F87171; color: #FFFFFF;">
                    <i class="fas fa-trash"></i> Confirmar Cancelamento
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var currentPayableId = null;
    var allInstallments = [];
    var pendingDeleteIds = null;

    function showConfirmDeleteModal(payable, ids, customMessage) {
        var message = '';
        if(customMessage) {
            message = customMessage;
            pendingDeleteIds = ids;
        } else if(payable) {
            var recurringWarning = '';
            if(payable.type == 'Recorrente' && payable.is_original_recurring) {
                recurringWarning = '<div style="background-color: rgba(255,189,89,0.2); border-left: 3px solid #FFBD59; padding: 12px; margin: 15px 0; border-radius: 4px;"><i class="fas fa-exclamation-triangle" style="color: #FFBD59;"></i> <strong style="color: #FFBD59;">Atenção:</strong> Esta é a conta original de uma recorrência. Ao cancelá-la, a geração automática de novas contas será interrompida. As contas já geradas não serão afetadas.</div>';
            }
            message = 'Deseja cancelar a conta "<strong>' + payable.description + '</strong>"?<br><small style="color: #6B7280;">Vencimento: ' + payable.date_due + ' | Valor: R$ ' + payable.price + '</small>' + recurringWarning + '<br>Esta ação não pode ser desfeita!';
            pendingDeleteIds = [payable.id];
        }

        $('#confirmDeleteMessage').html(message);
        $('#modalConfirmDelete').modal('show');
    }

    // Confirmar exclusão de conta única ou múltiplas parcelas selecionadas
    $(document).on('click', '#btnConfirmDeleteSingle', function() {
        if(pendingDeleteIds && pendingDeleteIds.length > 0) {
            $('#modalConfirmDelete').modal('hide');
            deletePayables(pendingDeleteIds);
            pendingDeleteIds = null;
        }
    });

    $(document).on('click', '#btn-delete-payable', function(e) {
        e.preventDefault();
        currentPayableId = $(this).data('payable');

        // Buscar informações da conta
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });

        $.ajax({
            url: "{{url('admin/payables')}}/" + currentPayableId + "/installments",
            method: 'GET',
            success: function(data) {
                allInstallments = data;

                // Se tiver mais de uma parcela, mostrar modal de seleção
                if(data.length > 1) {
                    populateInstallmentsModal(data);
                    $('#modalDeleteInstallments').modal('show');
                } else {
                    // Se for apenas uma conta, mostrar modal de confirmação
                    var payable = data[0];
                    showConfirmDeleteModal(payable);
                }
            },
            error: function(xhr) {
                console.error('Erro ao buscar parcelas:', xhr);
                alert('Erro ao carregar informações das parcelas.');
            }
        });
    });

    function populateInstallmentsModal(installments) {
        var html = '<div class="list-group">';

        installments.forEach(function(inst) {
            var statusBadge = inst.status == 'Pago'
                ? '<span class="badge badge-success" style="background-color: #10B981;">Pago</span>'
                : inst.status == 'Cancelado'
                ? '<span class="badge badge-secondary" style="background-color: #6B7280;">Cancelado</span>'
                : '<span class="badge badge-warning" style="background-color: #F59E0B;">Pendente</span>';

            html += '<div class="list-group-item" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); margin-bottom: 10px; border-radius: 6px;">';
            html += '<div class="form-check">';
            html += '<input class="form-check-input installment-checkbox" type="checkbox" value="' + inst.id + '" id="installment_' + inst.id + '" ' + (inst.status == 'Cancelado' ? 'disabled' : '') + ' style="background-color: #FFFFFF; border-color: #FFBD59;">';
            html += '<label class="form-check-label" for="installment_' + inst.id + '" style="color: #1F2937; width: 100%; cursor: pointer;">';
            html += '<div class="d-flex justify-content-between align-items-center">';
            html += '<div>';
            html += '<strong>' + inst.description + '</strong><br>';
            html += '<small style="color: #6B7280;">Vencimento: ' + inst.date_due + ' | Valor: R$ ' + inst.price + '</small>';
            html += '</div>';
            html += '<div>' + statusBadge + '</div>';
            html += '</div>';
            html += '</label>';
            html += '</div>';
            html += '</div>';
        });

        html += '</div>';
        $('#installmentsList').html(html);

        // Limpar seleção anterior
        $('#selectAllInstallments').prop('checked', false);
    }

    // Selecionar todas as parcelas
    $(document).on('change', '#selectAllInstallments', function() {
        $('.installment-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
    });

    // Confirmar exclusão
    $(document).on('click', '#btnConfirmDeleteInstallments', function() {
        var selectedIds = [];
        $('.installment-checkbox:checked:not(:disabled)').each(function() {
            selectedIds.push($(this).val());
        });

        if(selectedIds.length === 0) {
            alert('Por favor, selecione pelo menos uma parcela para cancelar.');
            return;
        }

        // Fechar modal de seleção e mostrar modal de confirmação
        $('#modalDeleteInstallments').modal('hide');

        setTimeout(function() {
            var confirmMsg = 'Tem certeza que deseja cancelar <strong>' + selectedIds.length + ' parcela(s)</strong> selecionada(s)?<br><br>Esta ação não pode ser desfeita!';
            showConfirmDeleteModal(null, selectedIds, confirmMsg);
        }, 300);
    });

    function deletePayables(ids) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });

        $.ajax({
            url: "{{url('admin/payables')}}/" + ids[0],
            method: 'DELETE',
            data: {
                ids: ids
            },
            success: function(data) {
                $('#modalDeleteInstallments').modal('hide');
                loadPayables();
            },
            error: function(xhr) {
                var errorMsg = 'Erro ao cancelar conta(s).';
                if(xhr.responseJSON){
                    if(typeof xhr.responseJSON === 'string'){
                        errorMsg = xhr.responseJSON;
                    } else if(xhr.responseJSON.message){
                        errorMsg = xhr.responseJSON.message;
                    }
                }
                alert(errorMsg);
            }
        });
    }

    // Parar recorrência
    $(document).on('click', '#btn-stop-recurrence', function(e) {
        e.preventDefault();
        var payableId = $(this).data('payable');

        // Mostrar modal de confirmação
        var confirmMsg = 'Deseja parar a geração automática desta recorrência?<br><br><small style="color: #6B7280;">A data de término será definida automaticamente como a data da última conta gerada. As contas já geradas não serão afetadas.</small><br><br>Esta ação pode ser revertida editando a conta e removendo a data de término.';

        $('#confirmDeleteMessage').html(confirmMsg);
        $('#modalConfirmDelete').modal('show');

        // Sobrescrever temporariamente a função de confirmação
        $('#btnConfirmDeleteSingle').off('click').on('click', function() {
            stopRecurrence(payableId);
        });
    });

    function stopRecurrence(payableId) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });

        $.ajax({
            url: "{{url('admin/payables')}}/" + payableId + "/stop-recurrence",
            method: 'POST',
            success: function(data) {
                $('#modalConfirmDelete').modal('hide');
                loadPayables();
                // Restaurar função original do botão
                $('#btnConfirmDeleteSingle').off('click').on('click', function() {
                    if(pendingDeleteIds && pendingDeleteIds.length > 0) {
                        $('#modalConfirmDelete').modal('hide');
                        deletePayables(pendingDeleteIds);
                        pendingDeleteIds = null;
                    }
                });
            },
            error: function(xhr) {
                var errorMsg = 'Erro ao parar recorrência.';
                if(xhr.responseJSON){
                    if(typeof xhr.responseJSON === 'string'){
                        errorMsg = xhr.responseJSON;
                    } else if(xhr.responseJSON.message){
                        errorMsg = xhr.responseJSON.message;
                    }
                }
                alert(errorMsg);
            }
        });
    }

    // Estornar pagamento
    $(document).on('click', '#btn-reverse-payable', function(e) {
        e.preventDefault();
        var payableId = $(this).data('payable');

        // Mostrar modal de confirmação com campo para motivo
        var confirmMsg = 'Deseja estornar o pagamento desta conta?<br><br><small style="color: #6B7280;">A conta será alterada para "Pendente" e poderá ser paga novamente. O histórico do estorno será mantido.</small><br><br><label for="reversalReason" style="color: #1F2937; display: block; margin-top: 15px; margin-bottom: 5px;">Motivo do estorno (opcional):</label><textarea id="reversalReason" class="form-control" rows="3" placeholder="Ex: Pagamento registrado por engano, valor incorreto, etc." style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #1F2937; width: 100%; resize: vertical;"></textarea>';

        $('#confirmDeleteMessage').html(confirmMsg);
        $('#modalConfirmDelete').modal('show');

        // Sobrescrever temporariamente a função de confirmação
        $('#btnConfirmDeleteSingle').off('click').on('click', function() {
            var reason = $('#reversalReason').val() || 'Estorno realizado pelo usuário';
            reversePayable(payableId, reason);
        });
    });

    function reversePayable(payableId, reason) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });

        $.ajax({
            url: "{{url('admin/payables')}}/" + payableId + "/reverse",
            method: 'POST',
            data: {
                reversal_reason: reason
            },
            success: function(data) {
                $('#modalConfirmDelete').modal('hide');
                $('#reversalReason').val(''); // Limpar campo
                loadPayables();

                // Mostrar mensagem de sucesso
                Swal.fire({
                    title: 'Estorno realizado!',
                    text: data,
                    icon: 'success',
                    confirmButtonColor: '#22C55E',
                    timer: 3000
                });

                // Restaurar função original do botão
                $('#btnConfirmDeleteSingle').off('click').on('click', function() {
                    if(pendingDeleteIds && pendingDeleteIds.length > 0) {
                        $('#modalConfirmDelete').modal('hide');
                        deletePayables(pendingDeleteIds);
                        pendingDeleteIds = null;
                    }
                });
            },
            error: function(xhr) {
                var errorMsg = 'Erro ao estornar pagamento.';
                if(xhr.responseJSON){
                    if(typeof xhr.responseJSON === 'string'){
                        errorMsg = xhr.responseJSON;
                    } else if(xhr.responseJSON.message){
                        errorMsg = xhr.responseJSON.message;
                    }
                }
                Swal.fire({
                    title: 'Erro!',
                    text: errorMsg,
                    icon: 'error',
                    confirmButtonColor: '#F87171'
                });
            }
        });
    }

    // Ver histórico de estornos
    $(document).on('click', '#btn-view-reversals', function(e) {
        e.preventDefault();
        var payableId = $(this).data('payable');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });

        $.ajax({
            url: "{{url('admin/payables')}}/" + payableId + "/reversals",
            method: 'GET',
            beforeSend: function() {
                $('#reversalsList').html('<div style="text-align: center; padding: 20px; color: #6B7280;"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>');
            },
            success: function(data) {
                if(data.length === 0) {
                    $('#reversalsList').html('<div style="text-align: center; padding: 20px; color: #6B7280;">Nenhum estorno registrado para esta conta.</div>');
                } else {
                    var html = '<div class="list-group">';
                    data.forEach(function(reversal) {
                        html += '<div class="list-group-item" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); margin-bottom: 10px; border-radius: 6px;">';
                        html += '<div class="d-flex justify-content-between align-items-start">';
                        html += '<div style="flex: 1;">';
                        html += '<strong style="color: #F59E0B;">Estorno realizado em ' + reversal.reversed_at + '</strong><br>';
                        html += '<small style="color: #6B7280;">Por: ' + reversal.user_name + '</small>';
                        if(reversal.reversal_reason) {
                            html += '<p style="color: #1F2937; margin-top: 8px; margin-bottom: 0;">' + reversal.reversal_reason + '</p>';
                        }
                        html += '</div>';
                        html += '<span style="background-color: rgba(245,158,11,0.2); color: #F59E0B; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Estorno #' + reversal.id + '</span>';
                        html += '</div>';
                        html += '</div>';
                    });
                    html += '</div>';
                    $('#reversalsList').html(html);
                }
                $('#modalViewReversals').modal('show');
            },
            error: function(xhr) {
                var errorMsg = 'Erro ao carregar histórico de estornos.';
                if(xhr.responseJSON){
                    if(typeof xhr.responseJSON === 'string'){
                        errorMsg = xhr.responseJSON;
                    } else if(xhr.responseJSON.message){
                        errorMsg = xhr.responseJSON.message;
                    }
                }
                $('#reversalsList').html('<div style="text-align: center; padding: 20px; color: #F87171;">' + errorMsg + '</div>');
            }
        });
    });

function updateCategoryChart(categoriesData) {
    const ctx = document.getElementById('categoryChart');

    if (!ctx) return;

    // Destruir gráfico anterior se existir
    if (categoryChart) {
        categoryChart.destroy();
    }

    if (!categoriesData || categoriesData.length === 0) {
        const canvas = ctx.getContext('2d');
        canvas.clearRect(0, 0, ctx.width, ctx.height);
        return;
    }

    // Preparar dados
    const labels = categoriesData.map(cat => cat.category_name);
    const data = categoriesData.map(cat => parseFloat(cat.total));
    const colors = categoriesData.map(cat => cat.category_color || '#CCCCCC');
    const borderColors = colors.map(color => color);

    // Criar gráfico
    categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total por Categoria',
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
                                    const formattedValue = parseFloat(value).toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    return {
                                        text: label + ' - R$ ' + formattedValue,
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
                            const formattedValue = parseFloat(value).toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return label + ': R$ ' + formattedValue + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}
</script>

@endsection


@endsection
