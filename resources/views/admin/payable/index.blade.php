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
        <!-- Widgets de Resumo -->
        <div class="row mb-3 mb-md-4">
            <div class="col-md-2 col-6 mb-2 mb-md-3">
                <div class="widget-card" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1; min-width: 0;">
                            <p class="widget-label" style="color: #333333; font-size: 11px; margin: 0; font-weight: 500; opacity: 0.7;">Total</p>
                            <h5 id="total_payables_curerency" class="widget-value" style="color: #333333; font-size: 16px; font-weight: 600; margin: 3px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">R$0,00</h5>
                            <p id="total_payables" class="widget-count" style="color: #333333; font-size: 10px; margin: 2px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div class="widget-icon" style="width: 36px; height: 36px; background-color: rgba(255,189,89,0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 8px;">
                            <i class="fas fa-receipt" style="color: #06b8f7; font-size: 16px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-2 mb-md-3">
                <div class="widget-card" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1; min-width: 0;">
                            <p class="widget-label" style="color: #333333; font-size: 11px; margin: 0; font-weight: 500; opacity: 0.7;">Pendentes</p>
                            <h5 id="pendent_payables_curerency" class="widget-value" style="color: #fec911; font-size: 16px; font-weight: 600; margin: 3px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">R$0,00</h5>
                            <p id="total_pendent" class="widget-count" style="color: #333333; font-size: 10px; margin: 2px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div class="widget-icon" style="width: 36px; height: 36px; background-color: rgba(255,189,89,0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 8px;">
                            <i class="far fa-hourglass" style="color: #fec911; font-size: 16px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-2 mb-md-3">
                <div class="widget-card" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1; min-width: 0;">
                            <p class="widget-label" style="color: #333333; font-size: 11px; margin: 0; font-weight: 500; opacity: 0.7;">Pagas</p>
                            <h5 id="pay_payables_curerency" class="widget-value" style="color: #6ccb48; font-size: 16px; font-weight: 600; margin: 3px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">R$0,00</h5>
                            <p id="total_pay" class="widget-count" style="color: #333333; font-size: 10px; margin: 2px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div class="widget-icon" style="width: 36px; height: 36px; background-color: rgba(34,197,94,0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 8px;">
                            <i class="fas fa-check-circle" style="color: #6ccb48; font-size: 16px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-2 mb-md-3">
                <div class="widget-card" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1; min-width: 0;">
                            <p class="widget-label" style="color: #333333; font-size: 11px; margin: 0; font-weight: 500; opacity: 0.7;">Fixas</p>
                            <h5 id="fixed_payables_curerency" class="widget-value" style="color: #333333; font-size: 16px; font-weight: 600; margin: 3px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">R$0,00</h5>
                            <p id="total_fixed" class="widget-count" style="color: #333333; font-size: 10px; margin: 2px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div class="widget-icon" style="width: 36px; height: 36px; background-color: rgba(255,189,89,0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 8px;">
                            <i class="fas fa-calendar-alt" style="color: #06b8f7; font-size: 16px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-2 mb-md-3">
                <div class="widget-card" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1; min-width: 0;">
                            <p class="widget-label" style="color: #333333; font-size: 11px; margin: 0; font-weight: 500; opacity: 0.7;">Recorrentes</p>
                            <h5 id="recurring_payables_curerency" class="widget-value" style="color: #333333; font-size: 16px; font-weight: 600; margin: 3px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">R$0,00</h5>
                            <p id="total_recurring" class="widget-count" style="color: #333333; font-size: 10px; margin: 2px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div class="widget-icon" style="width: 36px; height: 36px; background-color: rgba(255,189,89,0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 8px;">
                            <i class="fas fa-sync-alt" style="color: #06b8f7; font-size: 16px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-2 mb-md-3">
                <div class="widget-card" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1; min-width: 0;">
                            <p class="widget-label" style="color: #333333; font-size: 11px; margin: 0; font-weight: 500; opacity: 0.7;">Canceladas</p>
                            <h5 id="cancelled_payables_curerency" class="widget-value" style="color: #F87171; font-size: 16px; font-weight: 600; margin: 3px 0 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">R$0,00</h5>
                            <p id="total_cancelled" class="widget-count" style="color: #333333; font-size: 10px; margin: 2px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div class="widget-icon" style="width: 36px; height: 36px; background-color: rgba(248,113,113,0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 8px;">
                            <i class="fas fa-times-circle" style="color: #F87171; font-size: 16px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
        <!-- Coluna Esquerda: Filtros -->
        <div class="col-lg-3 col-md-12 mb-4">
            <div style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
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
                <h5 class="d-none d-lg-block" style="color: #333333; font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; font-size: 14px;">
                    <i class="fa fa-filter"></i> Filtros
                </h5>

                <!-- Conteúdo dos filtros (collapse em mobile, sempre visível em desktop) -->
                <div class="collapse d-lg-block" id="filtersCollapse">
                <!-- Filtros Rápidos -->
                <div class="mb-3">
                    <label style="color: #333333; font-weight: 500; font-size: 12px; margin-bottom: 8px; display: block;">Filtros Rápidos</label>
                    <div class="d-flex flex-column" style="gap: 8px;">
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-current-month" data-filter="current-month" style="background-color: #FFFFFF; color: #333333; border: 2px solid #06b8f7; padding: 10px 12px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left; font-size: 12px; transition: all 0.2s;">
                            <i class="fa fa-calendar" style="margin-right: 6px;"></i> Mês Atual
                        </button>
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-next-month" data-filter="next-month" style="background-color: #FFFFFF; color: #333333; border: 1px solid rgba(0,0,0,0.1); padding: 10px 12px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left; font-size: 12px; transition: all 0.2s;">
                            <i class="fa fa-calendar-alt" style="margin-right: 6px;"></i> Próximo Mês
                        </button>
                        <button class="btn btn-sm filter-quick-btn" type="button" id="btn-filter-all" data-filter="all" style="background-color: #FFFFFF; color: #333333; border: 1px solid rgba(0,0,0,0.1); padding: 10px 12px; border-radius: 6px; font-weight: 500; width: 100%; text-align: left; font-size: 12px; transition: all 0.2s;">
                            <i class="fa fa-list" style="margin-right: 6px;"></i> Todos
                        </button>
                    </div>
                </div>

                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">

                <!-- Tipo de Data -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="color: #333333; font-weight: 500; font-size: 12px; margin-bottom: 6px; display: block;">Tipo de Data</label>
                    <select class="form-control" id="filter-type" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #333333; font-size: 12px; height: auto;">
                        <option value="date_due">Data do Vencimento</option>
                        <option value="date_payment">Data do Pagamento</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="color: #333333; font-weight: 500; font-size: 12px; margin-bottom: 6px; display: block;">Status</label>
                    <select class="form-control" id="filter-status" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #333333; font-size: 12px; height: auto;">
                        <option value="all">Todos</option>
                        <option value="Pendente">Pendente</option>
                        <option value="Pago">Pago</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>

                <!-- Data inicial -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="color: #333333; font-weight: 500; font-size: 12px; margin-bottom: 6px; display: block;">Data inicial</label>
                    <div style="position: relative;">
                        <input type="date" autocomplete="off" class="form-control" placeholder="Data inicial" id="filter-date-ini" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #333333; font-size: 12px;">
                    </div>
                </div>

                <!-- Data final -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="color: #333333; font-weight: 500; font-size: 12px; margin-bottom: 6px; display: block;">Data final</label>
                    <div style="position: relative;">
                        <input type="date" autocomplete="off" class="form-control" placeholder="Data Final" id="filter-date-end" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #333333; font-size: 12px;">
                    </div>
                </div>

                <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">

                <!-- Categorias -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <fieldset style="border: 1px solid rgba(255,189,89,0.5); border-radius: 6px; padding: 10px; margin: 0; background-color: #FFFFFF; position: relative;">
                        <legend style="color: #06b8f7; font-size: 12px; font-weight: 600; padding: 0 8px; margin: 0; border: none;">Categorias</legend>
                        <div class="mb-2" style="margin-bottom: 8px !important;">
                            <label style="color: #333333; font-weight: 600; font-size: 12px; cursor: pointer; display: flex; align-items: center;">
                                <input type="checkbox" class="category-checkbox" id="selectAllCategories" style="margin-right: 5px; width: 16px; height: 16px; cursor: pointer;">
                                Selecionar Todas
                            </label>
                        </div>
                        <div class="d-flex flex-column categories-list" style="gap: 6px; margin-top: 6px; max-height: 150px; overflow-y: auto;">
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $category)
                                    <label style="color: #333333; font-weight: 400; font-size: 12px; cursor: pointer; display: flex; align-items: center;">
                                        <input type="checkbox" class="category-checkbox" value="{{ $category->id }}" style="margin-right: 5px; width: 16px; height: 16px; cursor: pointer;">
                                        <span style="display: inline-block; width: 10px; height: 10px; background-color: {{ $category->color }}; border-radius: 2px; margin-right: 5px;"></span>
                                        {{ $category->name }}
                                    </label>
                                @endforeach
                            @else
                                <p style="color: #6B7280; font-size: 12px; margin: 0;">Nenhuma categoria cadastrada</p>
                            @endif
                        </div>
                        <style>
                            .categories-list::-webkit-scrollbar {
                                width: 8px;
                            }
                            .categories-list::-webkit-scrollbar-track {
                                background: #FFFFFF;
                                border-radius: 4px;
                            }
                            .categories-list::-webkit-scrollbar-thumb {
                                background: #D1D5DB;
                                border-radius: 4px;
                            }
                            .categories-list::-webkit-scrollbar-thumb:hover {
                                background: #9CA3AF;
                            }
                        </style>
                    </fieldset>
                </div>
                </div>
                <!-- Fim do collapse -->
            </div>
        </div>

        <!-- Coluna Direita: Gráfico e Tabela -->
        <div class="col-lg-9 col-md-12">
            <!-- Painel Superior: Gráfico Faturas por Status -->
            <div class="mb-3 mb-md-4">
                <div class="chart-panel" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h5 class="chart-title" style="color: #333333; font-weight: 600; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; font-size: 13px;">
                        <i class="fas fa-chart-pie"></i> Faturas por Status
                    </h5>
                    <div class="row align-items-center">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <div class="chart-container" style="position: relative; height: 200px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div id="statusLegend" class="chart-legend" style="display: flex; flex-direction: column; gap: 8px;">
                                <!-- Legenda será preenchida via JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Painel Inferior: Tabela -->
            <div class="table-panel" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; padding: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <!-- Botão Nova Conta a Pagar -->
                <div class="mb-2 mb-md-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center" style="gap: 10px;">
                    <a href="#" data-original-title="Nova Conta a Pagar" id="btn-modal-payable" data-type="add-payable" data-toggle="tooltip" class="btn btn-new-payable" style="background-color: #06b8f7; color: #FFFFFF !important; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; transition: all 0.3s; font-size: 12px; white-space: nowrap;">
                        <i class="fa fa-plus"></i> <span class="d-none d-sm-inline">Nova Conta a Pagar</span><span class="d-sm-none">Nova</span>
                    </a>
                    <!-- Paginação -->
                    <div id="pagination" class="pagination-mobile d-flex justify-content-end align-items-center flex-wrap" style="gap: 4px;">
                        <button class="btn btn-sm pagination-btn" onclick="loadPayables(prevPage)" style="background-color: #FFFFFF; color: #333333; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 4px 8px; font-weight: 500; font-size: 11px; min-width: auto;">«</button>
                        <button class="btn btn-sm pagination-page-btn" onclick="loadPayables(1)" id="page-btn-1" style="background-color: #06b8f7; color: #FFFFFF; border: none; border-radius: 6px; padding: 4px 10px; font-weight: 500; font-size: 11px; min-width: 32px;">1</button>
                        <button class="btn btn-sm pagination-page-btn" onclick="loadPayables(2)" id="page-btn-2" style="background-color: #FFFFFF; color: #333333; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 4px 10px; font-weight: 500; font-size: 11px; min-width: 32px; display: none;">2</button>
                        <button class="btn btn-sm pagination-btn" onclick="loadPayables(nextPage)" style="background-color: #FFFFFF; color: #333333; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 4px 8px; font-weight: 500; font-size: 11px;">»</button>
                        <span id="page-info" class="page-info-mobile" style="color: #333333; font-weight: 500; font-size: 11px; margin-left: 6px; white-space: nowrap;">Página 1 de 1</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-responsive-custom" style="margin-bottom: 0; width: 100%;">
                        <thead>
                        <tr style="border-bottom: 2px solid rgba(0,0,0,0.1);">
                            <th class="col-expand" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px; width: 40px;"></th>
                            <th class="col-id" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;">#</th>
                            <th class="col-supplier" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;">Fornecedor</th>
                            <th class="col-category" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;">Categoria</th>
                            <th class="col-date" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;">Data</th>
                            <th class="col-due" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;">Vencimento</th>
                            <th class="col-value" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;">Valor</th>
                            <th class="col-status" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;">Status</th>
                            <th class="col-actions" style="color: #333333; font-weight: 600; padding: 10px 8px; border: none; font-size: 12px;"></th>
                        </tr>
                        </thead>

                        <tbody id="list-payables" style="background-color: #FFFFFF;">

                        </tbody>
                    </table>
                </div>
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



<!-- Modal :: Form Payable -->
<div class="modal fade" id="modalPayable" tabindex="-1" role="dialog" aria-labelledby="modalPayableLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);">
            <form action="" class="form-horizontal" id="form-request-payable">
                <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                    <h5 class="modal-title" id="modalPayableLabel" style="color: #06b8f7; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #333333;">
                        <span aria-hidden="true" style="color: #333333;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-payable" style="background-color: #FFFFFF;">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer" style="background-color: #FFFFFF; border-top: 1px solid rgba(0,0,0,0.1);">
                    <button type="button" class="btn" id="btn-save-payable" style="background-color: #6ccb48; color: #FFFFFF; border: none; font-weight: 600;"><i class="fa fa-check"></i> Salvar</button>
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
        <div class="modal-content" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);">
            <form action="" class="form-horizontal" id="form-request-notifications">
                <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                    <h5 class="modal-title" id="modalNotificationsLabel" style="color: #06b8f7; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #333333;">
                        <span aria-hidden="true" style="color: #333333;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-notifications" style="background-color: #FFFFFF;">
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
        <div class="modal-content" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalErrorLabel" style="color: #06b8f7; font-weight: 600;"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #333333;">
                    <span aria-hidden="true" style="color: #333333;">&times;</span>
                </button>
            </div>

            <style>

                pre {
                   background-color: #FFFFFF;
                   border: 1px solid rgba(0,0,0,0.1);
                   padding: 10px 20px;
                   margin: 20px;
                   color: #333333;
                   }
                .json-key {
                   color: #06b8f7;
                   }
                .json-value {
                   color: #6ccb48;
                   }
                .json-string {
                   color: #F87171;
                   }

                </style>

            <div class="modal-body" id="modal-content-error" style="background-color: #FFFFFF; color: #333333;">

                <!-- conteudo -->
                <!-- conteudo -->
            </div><!-- modal-body -->
        </div>
    </div>
 </div>
 <!-- Modal :: Log -->


@section('styles')
<style>
    /* Estilos para Contas a Pagar */
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

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5) !important;
    }

    .modal-content,
    .modal-header,
    .modal-body,
    .modal-footer {
        background-color: #FFFFFF !important;
    }

    .main-footer {
        background-color: #FFFFFF !important;
    }

    #btn-modal-payable:hover {
        background-color: #06b8f7 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(6, 184, 247, 0.3);
    }

    /* Estilo para ícone do collapse */
    .fa-chevron-down {
        transition: transform 0.3s ease;
    }

    /* Estilos para dropdown de ações */
    .dropdown-menu {
        z-index: 1050;
    }

    .dropdown-item {
        border: none !important;
    }

    .dropdown-item:hover {
        background-color: #FFFFFF !important;
    }

    .dropdown-toggle::after {
        display: none;
    }

    /* Estilos responsivos para tabela */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (min-width: 1201px) {
        .table-responsive {
            overflow-x: visible;
        }
    }

    @media (max-width: 1200px) {
        .col-payment-method {
            display: none !important;
        }
        .table th,
        .table td {
            padding: 10px 8px !important;
        }
    }

    @media (max-width: 992px) {
        .col-category {
            display: none !important;
        }
        .table th,
        .table td {
            padding: 8px 6px !important;
            font-size: 13px !important;
        }
    }

    @media (max-width: 768px) {
        .col-type {
            display: none !important;
        }
        .table th,
        .table td {
            padding: 8px 4px !important;
            font-size: 12px !important;
        }
        .col-value {
            font-size: 13px !important;
        }
        .col-supplier {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    }

    @media (max-width: 576px) {
        .col-supplier {
            max-width: 100px;
        }
        .table th,
        .table td {
            padding: 6px 3px !important;
            font-size: 11px !important;
        }
        .col-value {
            font-size: 12px !important;
        }
        .col-actions .btn,
        .col-actions a {
            padding: 2px 4px !important;
            font-size: 10px !important;
        }
    }


    #pagination button:hover:not(:disabled) {
        background-color: #06b8f7 !important;
        border-color: #06b8f7 !important;
        color: #333333 !important;
    }

    #pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .form-control:focus {
        border-color: #06b8f7 !important;
        box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
        outline: none;
        background-color: #FFFFFF !important;
        color: #333333 !important;
    }

    select.form-control:focus,
    input.form-control:focus {
        border-color: #06b8f7 !important;
        box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
        outline: none;
        background-color: #FFFFFF !important;
        color: #333333 !important;
    }

    select.form-control,
    input.form-control {
        background-color: #FFFFFF !important;
        color: #333333 !important;
    }

    select.form-control option {
        background-color: #FFFFFF !important;
        color: #333333 !important;
    }

    select.form-control option:checked {
        background-color: #FFFFFF !important;
        color: #333333 !important;
    }

    /* Força a cor escura em todos os estados do select */
    select {
        color: #333333 !important;
    }

    select:focus {
        color: #333333 !important;
    }

    select option {
        color: #333333 !important;
    }

    select option:checked {
        color: #333333 !important;
    }

    /* Estilos para checkboxes de status e tipo */
    .status-checkbox,
    .type-checkbox,
    .category-checkbox {
        accent-color: #06b8f7;
        cursor: pointer;
    }

    .status-checkbox:checked,
    .type-checkbox:checked,
    .category-checkbox:checked {
        background-color: #06b8f7;
    }

    /* Botões de filtro rápido */
    #btn-filter-current-month:hover,
    #btn-filter-next-month:hover,
    #btn-filter-all:hover {
        background-color: #06b8f7 !important;
        color: #333333 !important;
        border-color: #06b8f7 !important;
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
        background-color: #FFFFFF !important;
    }

    @media (max-width: 768px) {
        .col-md-6 {
            margin-bottom: 15px;
        }
    }

    /* Estilos para collapse de filtros em mobile */
    @media (max-width: 991.98px) {
        #filtersCollapse {
            margin-top: 10px;
        }
    }

    @media (min-width: 992px) {
        #filtersCollapse {
            display: block !important;
        }
    }

    /* Transição suave para o ícone do collapse */
    #filterToggleIcon {
        transition: transform 0.3s ease;
    }

    /* Estilos responsivos para widgets */
    @media (max-width: 767.98px) {
        .widget-card {
            padding: 10px !important;
        }

        .widget-label {
            font-size: 10px !important;
        }

        .widget-value {
            font-size: 14px !important;
            margin: 2px 0 0 0 !important;
        }

        .widget-count {
            font-size: 9px !important;
            margin: 1px 0 0 0 !important;
        }

        .widget-icon {
            width: 32px !important;
            height: 32px !important;
        }

        .widget-icon i {
            font-size: 14px !important;
        }
    }

    @media (min-width: 768px) {
        .widget-card {
            padding: 15px !important;
        }

        .widget-label {
            font-size: 12px !important;
        }

        .widget-value {
            font-size: 18px !important;
        }

        .widget-count {
            font-size: 11px !important;
        }

        .widget-icon {
            width: 42px !important;
            height: 42px !important;
        }

        .widget-icon i {
            font-size: 18px !important;
        }
    }

    @media (min-width: 992px) {
        .widget-card {
            padding: 20px !important;
        }

        .widget-label {
            font-size: 14px !important;
        }

        .widget-value {
            font-size: 20px !important;
        }

        .widget-count {
            font-size: 12px !important;
        }

        .widget-icon {
            width: 48px !important;
            height: 48px !important;
        }

        .widget-icon i {
            font-size: 20px !important;
        }
    }

    /* Estilos responsivos para gráfico */
    @media (max-width: 767.98px) {
        .chart-panel {
            padding: 10px !important;
        }

        .chart-title {
            font-size: 12px !important;
            margin-bottom: 10px !important;
        }

        .chart-container {
            height: 180px !important;
        }

        .chart-legend {
            gap: 6px !important;
            margin-top: 10px;
        }

        .chart-legend > div {
            font-size: 11px !important;
        }

        .chart-legend > div > span {
            font-size: 10px !important;
        }
    }

    /* Estilos responsivos para tabela */
    @media (max-width: 767.98px) {
        .table-panel {
            padding: 10px !important;
        }

        .table th,
        .table td {
            padding: 6px 4px !important;
            font-size: 11px !important;
        }

        .table th {
            font-size: 10px !important;
        }

        .col-expand {
            width: 30px !important;
        }

        .col-id {
            min-width: 50px !important;
        }

        .col-supplier {
            max-width: 120px !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .col-category {
            display: none !important;
        }

        .col-date {
            display: none !important;
        }

        .col-value {
            font-size: 12px !important;
            font-weight: 600 !important;
        }

        .col-status span {
            padding: 2px 6px !important;
            font-size: 10px !important;
        }

        .col-actions .btn {
            padding: 2px 4px !important;
            font-size: 12px !important;
        }

        /* Ajustar colspan do collapse em mobile (7 colunas visíveis) */
        .table .collapse td[colspan="9"] {
            /* Será ajustado via JavaScript */
        }
    }

    @media (max-width: 575.98px) {
        .col-supplier {
            max-width: 100px !important;
        }

        .table th,
        .table td {
            padding: 5px 3px !important;
            font-size: 10px !important;
        }

        .col-value {
            font-size: 11px !important;
        }
    }

    /* Estilos responsivos para paginação */
    @media (max-width: 575.98px) {
        .pagination-mobile {
            gap: 2px !important;
        }

        .pagination-btn,
        .pagination-page-btn {
            padding: 3px 6px !important;
            font-size: 10px !important;
            min-width: 28px !important;
        }

        .page-info-mobile {
            font-size: 10px !important;
            margin-left: 4px !important;
        }

        .btn-new-payable {
            padding: 6px 10px !important;
            font-size: 11px !important;
        }
    }

    /* Estilos responsivos para filtros */
    @media (max-width: 767.98px) {
        .form-group {
            margin-bottom: 10px !important;
        }

        .form-group label {
            font-size: 11px !important;
            margin-bottom: 4px !important;
        }

        .form-control {
            padding: 6px 10px !important;
            font-size: 11px !important;
        }

        .filter-quick-btn {
            padding: 8px 10px !important;
            font-size: 11px !important;
        }

        fieldset {
            padding: 8px !important;
        }

        legend {
            font-size: 11px !important;
        }

        .categories-list {
            max-height: 120px !important;
            gap: 4px !important;
        }

        .categories-list label {
            font-size: 11px !important;
        }
    }

    /* Ajustes gerais para mobile */
    @media (max-width: 767.98px) {
        .content-header {
            padding: 10px 15px !important;
        }

        .content-header h1 {
            font-size: 18px !important;
        }

        .mb-4 {
            margin-bottom: 1rem !important;
        }

        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let statusChart = null;

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

                // Garantir que os scripts do formulário sejam executados
                // Executar scripts inline do formulário de forma segura
                var scripts = $("#form-content-payable").find('script');
                scripts.each(function() {
                    var scriptContent = $(this).html();
                    if(scriptContent && scriptContent.trim() !== '') {
                        try {
                            var scriptElement = document.createElement('script');
                            scriptElement.textContent = scriptContent;
                            document.body.appendChild(scriptElement);
                            document.body.removeChild(scriptElement);
                        } catch(e) {
                            console.error('Erro ao executar script:', e);
                        }
                    }
                });

                // Chamar função de inicialização do formulário se existir
                setTimeout(function() {
                    if(typeof window.initPayableForm === 'function') {
                        window.initPayableForm();
                    }
                }, 200);
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
                    var titleHtml = "<h5 style='color:#06b8f7'>" + data + "</h5>";
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
                            color: '#333333',
                            confirmButtonColor: "#2563EB",
                            showClass: showClassWarn
                        });
                    } else{
                        Swal.fire({
                            text: xhr.responseJSON,
                            width: 300,
                            icon: 'error',
                            color: '#333333',
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
    const filterInputStatus     = document.getElementById('filter-status');
    const filterInputDateIni    = document.getElementById('filter-date-ini');
    const filterInputDateEnd    = document.getElementById('filter-date-end');

    // Definir datas do mês atual por padrão
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    filterInputDateIni.value = firstDay.toISOString().split('T')[0];
    filterInputDateEnd.value = lastDay.toISOString().split('T')[0];


    function loadPayables(page = 1) {
        const filterType        = filterInputType.value;
        const filterStatus      = filterInputStatus.value;
        const filterDateIni     = filterInputDateIni.value;
        const filterDateEnd     = filterInputDateEnd.value;

        // Coletar categorias selecionadas dos checkboxes (excluindo "Selecionar Todas")
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox:checked:not(#selectAllCategories)');
        const filterCategories = Array.from(categoryCheckboxes).map(cb => cb.value);

        // Construir URL com filtros
        let url = 'load-payables?page='+page+'&type='+filterType;
        if(filterDateIni && filterDateEnd){
            url += '&dateini='+filterDateIni+'&dateend='+filterDateEnd;
        }
        if(filterStatus && filterStatus !== 'all'){
            url += '&status[0]='+filterStatus;
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

            // Atualizar widgets de resumo
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

            // Atualizar gráfico de status
            var statusData = {
                pendente_currency: 0,
                pago_currency: 0,
                cancelado_currency: 0
            };

            if(data.result.data && data.result.data.length > 0) {
                statusData = data.result.data[0];
            }

            updateStatusChart(statusData);
        $('#list-payables').html('');

        var html = '';
            if(data.result.data.length > 0){
                $.each(data.result.data, function(i, item) {
                var installmentText = item.installment_number ? ' ('+item.installment_number+'/'+item.installments+')' : '';
                var typeBadgeColor = item.type == 'Fixa' ? '#fec911' : item.type == 'Recorrente' ? '#6ccb48' : '#333333';
                var typeBadgeBg = item.type == 'Fixa' ? 'rgba(254,201,17,0.2)' : item.type == 'Recorrente' ? 'rgba(108,203,72,0.2)' : 'rgba(229,231,235,0.1)';
                var statusBadgeColor = item.status == 'Pago' ? '#6ccb48' : item.status == 'Pendente' ? '#fec911' : '#F87171';
                var statusBadgeBg = item.status == 'Pago' ? 'rgba(108,203,72,0.2)' : item.status == 'Pendente' ? 'rgba(254,201,17,0.2)' : 'rgba(248,113,113,0.2)';
                var datePayment = item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-';
                // Criar itens do dropdown de ações
                var actionsMenuItems = [];

                if(item.status == 'Pendente') {
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Editar conta" id="btn-modal-payable" data-type="edit-payable" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="color: #333333; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#FFFFFF\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="far fa-edit" style="margin-right: 8px; color: #06b8f7;"></i> Editar</a>');
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Cancelar Conta" id="btn-delete-payable" data-placement="left" data-payable="'+item.id+'" data-tt="tooltip" style="color: #333333; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#FFFFFF\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-times" style="margin-right: 8px; color: #F87171;"></i> Cancelar</a>');
                }

                if(item.status == 'Pago') {
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Editar Tipo de Conta" id="btn-modal-payable" data-type="edit-payable" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="color: #333333; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#FFFFFF\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="far fa-edit" style="margin-right: 8px; color: #06b8f7;"></i> Editar Tipo de Conta</a>');
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Estornar Pagamento" id="btn-reverse-payable" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="color: #333333; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#FFFFFF\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-undo" style="margin-right: 8px; color: #fec911;"></i> Estornar</a>');
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Ver Histórico de Estornos" id="btn-view-reversals" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="color: #333333; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#FFFFFF\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-history" style="margin-right: 8px; color: #06b8f7;"></i> Histórico de Estornos</a>');
                }

                if(item.type == 'Recorrente' && (!item.recurrence_end || item.recurrence_end == null || item.recurrence_end == '') && item.status == 'Pendente') {
                    actionsMenuItems.push('<a href="#" class="dropdown-item" data-original-title="Parar Recorrência" id="btn-stop-recurrence" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="color: #333333; padding: 8px 12px; text-decoration: none; display: block; font-size: 12px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#FFFFFF\'" onmouseout="this.style.backgroundColor=\'transparent\'"><i class="fas fa-stop-circle" style="margin-right: 8px; color: #fec911;"></i> Parar Recorrência</a>');
                }
                var categoryColor = item.category_color || '#06b8f7';
                var categoryBadge = item.category_name ? '<span style="background-color: rgba('+parseInt(categoryColor.slice(1,3),16)+','+parseInt(categoryColor.slice(3,5),16)+','+parseInt(categoryColor.slice(5,7),16)+',0.2); color: '+categoryColor+'; padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;"><span style="display: inline-block; width: 6px; height: 6px; background-color: '+categoryColor+'; border-radius: 50%;"></span>'+item.category_name+'</span>' : '<span style="color: #6B7280; font-size: 11px;">-</span>';

                var collapseId = 'collapse-'+item.id;
                var dateCreated = item.created_at ? moment(item.created_at).format('DD/MM/YYYY') : '-';
                html += '<tr style="border-bottom: 1px solid rgba(0,0,0,0.1); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#F9F9F9\'" onmouseout="this.style.backgroundColor=\'#FFFFFF\'">';
                html += '<td class="col-expand" style="padding: 10px 8px; text-align: center;">';
                html += '<button class="btn btn-sm" type="button" data-toggle="collapse" data-target="#'+collapseId+'" aria-expanded="false" aria-controls="'+collapseId+'" style="background-color: transparent; border: none; color: #333333; padding: 2px 4px; cursor: pointer; font-size: 11px;">';
                html += '<i class="fa fa-chevron-down" id="icon-'+collapseId+'"></i>';
                html += '</button>';
                html += '</td>';
                html += '<td class="col-id" style="padding: 10px 8px; color: #333333; font-size: 12px;">'+item.id+installmentText+'</td>';
                html += '<td class="col-supplier" style="padding: 10px 8px; color: #333333; font-size: 12px; font-weight: 500;">'+item.supplier_name+'</td>';
                html += '<td class="col-category" style="padding: 10px 8px;">'+categoryBadge+'</td>';
                html += '<td class="col-date" style="padding: 10px 8px; color: #333333; font-size: 12px;">'+dateCreated+'</td>';
                html += '<td class="col-due" style="padding: 10px 8px; color: #333333; font-size: 12px;">'+moment(item.date_due).format('DD/MM/YYYY')+'</td>';
                var priceOptions = {};
                priceOptions.minimumFractionDigits = 2;
                var priceFormatted = parseFloat(item.price).toLocaleString('pt-br', priceOptions);
                html += '<td class="col-value" style="padding: 10px 8px; color: #06b8f7; font-size: 12px; font-weight: 600;">R$ '+priceFormatted+'</td>';
                html += '<td class="col-status" style="padding: 10px 8px;"><span style="background-color: '+statusBadgeBg+'; color: '+statusBadgeColor+'; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 500;">'+item.status+'</span></td>';

                // Criar dropdown de ações
                var actionsMenuId = 'actions-menu-'+item.id;
                html += '<td class="col-actions" style="padding: 8px 6px; text-align: center;">';
                if(actionsMenuItems.length > 0) {
                    html += '<div class="dropdown" style="position: relative; display: inline-block;">';
                    html += '<button class="btn btn-sm" type="button" id="'+actionsMenuId+'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: transparent; border: none; color: #333333; padding: 4px 8px; cursor: pointer; font-size: 14px;">';
                    html += '<i class="fa fa-ellipsis-v"></i>';
                    html += '</button>';
                    html += '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="'+actionsMenuId+'" style="min-width: 180px; padding: 5px 0; background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">';
                    html += actionsMenuItems.join('');
                    html += '</div>';
                    html += '</div>';
                }
                html += '</td>';
                html += '</tr>';
                html += '<tr class="collapse" id="'+collapseId+'" style="background-color: #FFFFFF;">';
                html += '<td colspan="9" style="padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.1);">';
                html += '<div style="background-color: #FFFFFF; border-radius: 8px; padding: 12px;">';
                html += '<h6 style="color: #333333; font-weight: 600; margin-bottom: 8px; font-size: 13px;">Descrição:</h6>';
                html += '<p style="color: #333333; font-size: 12px; margin-bottom: 12px;">'+(item.description || '-')+'</p>';
                html += '<div class="row" style="margin-top: 8px;">';
                html += '<div class="col-md-6" style="margin-bottom: 8px;"><strong style="color: #333333; font-size: 12px;">Fornecedor:</strong> <span style="color: #333333; font-size: 12px;">'+item.supplier_name+'</span></div>';
                html += '<div class="col-md-6" style="margin-bottom: 8px;"><strong style="color: #333333; font-size: 12px;">Forma de Pagamento:</strong> <span style="color: #333333; font-size: 12px;">'+(item.payment_method || '-')+'</span></div>';
                html += '<div class="col-md-6" style="margin-bottom: 8px;"><strong style="color: #333333; font-size: 12px;">Pago em:</strong> <span style="color: #333333; font-size: 12px;">'+datePayment+'</span></div>';
                html += '</div>';
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            });
            }else{
                var colspan = window.innerWidth < 768 ? 7 : 9;
                html += '<tr><td style="text-align:center; padding: 40px; color: #333333; font-size: 14px;" colspan="'+colspan+'">Nenhuma conta encontrada</td></tr>';
            }

            $('#list-payables').append(html);
            $('[data-tt="tooltip"]').tooltip();

            // Ajustar colspan do collapse baseado no tamanho da tela
            function adjustCollapseColspan() {
                var isMobile = window.innerWidth < 768;
                var colspan = isMobile ? 7 : 9; // Em mobile: categoria e data estão ocultas
                $('.collapse td[colspan="9"]').attr('colspan', colspan);
            }
            adjustCollapseColspan();
            $(window).on('resize', adjustCollapseColspan);

            // Rotacionar ícone do collapse
            $('.collapse').on('show.bs.collapse', function () {
                var iconId = '#icon-' + $(this).attr('id');
                $(iconId).css('transform', 'rotate(180deg)');
            });
            $('.collapse').on('hide.bs.collapse', function () {
                var iconId = '#icon-' + $(this).attr('id');
                $(iconId).css('transform', 'rotate(0deg)');
            });

            // Atualizar paginação
            currentPage = data.result.current_page;
            prevPage = data.result.prev_page_url ? currentPage - 1 : currentPage;
            nextPage = data.result.next_page_url ? currentPage + 1 : currentPage;
            var totalPages = data.result.last_page || 1;

            // Atualizar texto da página
            document.getElementById('page-info').textContent = 'Página ' + currentPage + ' de ' + totalPages;

            // Atualizar botões de página
            for(var i = 1; i <= Math.min(totalPages, 2); i++) {
                var btn = document.getElementById('page-btn-' + i);
                if(btn) {
                    if(i === currentPage) {
                        btn.style.backgroundColor = '#06b8f7';
                        btn.style.color = '#FFFFFF';
                        btn.style.border = 'none';
                    } else {
                        btn.style.backgroundColor = '#FFFFFF';
                        btn.style.color = '#333333';
                        btn.style.border = '1px solid rgba(0,0,0,0.1)';
                    }
                    btn.style.display = 'inline-block';
                }
            }

            // Esconder botão da página 2 se não existir
            if(totalPages < 2) {
                var btn2 = document.getElementById('page-btn-2');
                if(btn2) btn2.style.display = 'none';
            }

            // Desabilitar botões de navegação
            var prevBtn = document.getElementById('pagination').querySelector('button:first-child');
            var nextBtn = document.getElementById('pagination').querySelector('button:last-child');
            if(prevBtn) {
                prevBtn.disabled = !data.result.prev_page_url;
                prevBtn.style.opacity = data.result.prev_page_url ? '1' : '0.5';
            }
            if(nextBtn) {
                nextBtn.disabled = !data.result.next_page_url;
                nextBtn.style.opacity = data.result.next_page_url ? '1' : '0.5';
            }
            },
            error:function (xhr) {
                var colspan = window.innerWidth < 768 ? 7 : 9;
                $('#list-payables').append('<tr><td style="text-align:center; padding: 40px; color: #F87171; font-size: 14px;" colspan="'+colspan+'">Erro ao carregar contas</td></tr>');
            }
            });
    }

    // Filtro automático - tipo de data
    filterInputType.addEventListener('change', function() {
        currentPage = 1;
        loadPayables(currentPage);
    });

    // Filtro automático - status
    filterInputStatus.addEventListener('change', function() {
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
            btn.style.backgroundColor = '#FFFFFF';
        });

        const dateIni = filterInputDateIni.value;
        const dateEnd = filterInputDateEnd.value;

        if (!dateIni || !dateEnd) {
            // Se não há datas, destacar "Todos"
            var btnAll = document.getElementById('btn-filter-all');
            btnAll.style.border = '2px solid #06b8f7';
            btnAll.style.borderWidth = '2px';
            return;
        }

        const now = new Date();
        const currentMonthFirst = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
        const currentMonthLast = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().split('T')[0];
        const nextMonthFirst = new Date(now.getFullYear(), now.getMonth() + 1, 1).toISOString().split('T')[0];
        const nextMonthLast = new Date(now.getFullYear(), now.getMonth() + 2, 0).toISOString().split('T')[0];

        if (dateIni === currentMonthFirst && dateEnd === currentMonthLast) {
            var btnCurrent = document.getElementById('btn-filter-current-month');
            btnCurrent.style.border = '2px solid #06b8f7';
            btnCurrent.style.borderWidth = '2px';
        } else if (dateIni === nextMonthFirst && dateEnd === nextMonthLast) {
            var btnNext = document.getElementById('btn-filter-next-month');
            btnNext.style.border = '2px solid #06b8f7';
            btnNext.style.borderWidth = '2px';
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
        filterInputStatus.value = 'all';

        // Marcar todas as categorias
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

    // Controlar ícone do collapse de filtros em mobile
    $('#filtersCollapse').on('show.bs.collapse', function () {
        $('#filterToggleIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    $('#filtersCollapse').on('hide.bs.collapse', function () {
        $('#filterToggleIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    loadPayables();


</script>

<!-- Modal para confirmação de exclusão -->
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="modalConfirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalConfirmDeleteLabel" style="color: #06b8f7; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Cancelamento
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #333333;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: #333333;">
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
        <div class="modal-content" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalViewReversalsLabel" style="color: #06b8f7; font-weight: 600;">
                    <i class="fas fa-history"></i> Histórico de Estornos
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #333333;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: #333333;">
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
        <div class="modal-content" style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1);">
            <div class="modal-header" style="background-color: #FFFFFF; border-bottom: 1px solid rgba(0,0,0,0.1);">
                <h5 class="modal-title" id="modalDeleteInstallmentsLabel" style="color: #06b8f7; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Selecionar Parcelas para Cancelar
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #333333;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: #333333;">
                <p style="margin-bottom: 20px;">Selecione quais parcelas deseja cancelar:</p>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="selectAllInstallments" style="background-color: #FFFFFF; border-color: #06b8f7;">
                    <label class="form-check-label" for="selectAllInstallments" style="color: #333333; font-weight: 600;">
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
                recurringWarning = '<div style="background-color: rgba(254,201,17,0.2); border-left: 3px solid #fec911; padding: 12px; margin: 15px 0; border-radius: 4px;"><i class="fas fa-exclamation-triangle" style="color: #fec911;"></i> <strong style="color: #fec911;">Atenção:</strong> Esta é a conta original de uma recorrência. Ao cancelá-la, a geração automática de novas contas será interrompida. As contas já geradas não serão afetadas.</div>';
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
            html += '<input class="form-check-input installment-checkbox" type="checkbox" value="' + inst.id + '" id="installment_' + inst.id + '" ' + (inst.status == 'Cancelado' ? 'disabled' : '') + ' style="background-color: #FFFFFF; border-color: #06b8f7;">';
            html += '<label class="form-check-label" for="installment_' + inst.id + '" style="color: #333333; width: 100%; cursor: pointer;">';
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
        var confirmMsg = 'Deseja estornar o pagamento desta conta?<br><br><small style="color: #6B7280;">A conta será alterada para "Pendente" e poderá ser paga novamente. O histórico do estorno será mantido.</small><br><br><label for="reversalReason" style="color: #333333; display: block; margin-top: 15px; margin-bottom: 5px;">Motivo do estorno (opcional):</label><textarea id="reversalReason" class="form-control" rows="3" placeholder="Ex: Pagamento registrado por engano, valor incorreto, etc." style="background-color: #FFFFFF; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; padding: 8px 12px; color: #333333; width: 100%; resize: vertical;"></textarea>';

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
                    confirmButtonColor: '#6ccb48',
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
                        html += '<strong style="color: #fec911;">Estorno realizado em ' + reversal.reversed_at + '</strong><br>';
                        html += '<small style="color: #6B7280;">Por: ' + reversal.user_name + '</small>';
                        if(reversal.reversal_reason) {
                            html += '<p style="color: #333333; margin-top: 8px; margin-bottom: 0;">' + reversal.reversal_reason + '</p>';
                        }
                        html += '</div>';
                        html += '<span style="background-color: rgba(254,201,17,0.2); color: #fec911; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">Estorno #' + reversal.id + '</span>';
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

function updateStatusChart(statusData) {
    const ctx = document.getElementById('statusChart');
    const legendContainer = document.getElementById('statusLegend');

    if (!ctx) return;

    // Destruir gráfico anterior se existir
    if (statusChart) {
        statusChart.destroy();
    }

    // Preparar dados
    const pagoValue = parseFloat(statusData.pago_currency || 0);
    const pendenteValue = parseFloat(statusData.pendente_currency || 0);
    const canceladoValue = parseFloat(statusData.cancelado_currency || 0);

    const data = [pagoValue, pendenteValue, canceladoValue];
    const labels = ['Pago', 'Pendente', 'Cancelado'];
    const colors = ['#22C55E', '#FFBD59', '#F87171'];

    // Criar gráfico donut
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: '#FFFFFF',
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
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(2) : 0;
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

    // Atualizar legenda customizada
    if (legendContainer) {
        let legendHtml = '';
        labels.forEach(function(label, index) {
            const value = data[index];
            const formattedValue = parseFloat(value).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            legendHtml += '<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">';
            legendHtml += '<div style="width: 16px; height: 16px; background-color: ' + colors[index] + '; border-radius: 4px;"></div>';
            legendHtml += '<span style="color: #1F2937; font-size: 12px; font-weight: 500;">' + label + ' - ' + formattedValue + '</span>';
            legendHtml += '</div>';
        });
        legendContainer.innerHTML = legendHtml;
    }
}
</script>

@endsection
@endsection


