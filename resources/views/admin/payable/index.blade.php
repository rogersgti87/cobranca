@extends('layouts.admin')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0" style="color: #E5E7EB; font-weight: 600;">{{ $title }}</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right" style="background-color: transparent; padding: 0;">
              <li class="breadcrumb-item"><a href="{{url('admin')}}" style="color: #E5E7EB; text-decoration: none; opacity: 0.7;">Home</a></li>
              <li class="breadcrumb-item active" style="color: #E5E7EB; opacity: 0.7;">{{ $title }}</li>
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
                <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #E5E7EB; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Total</p>
                            <h5 id="total_payables_curerency" style="color: #E5E7EB; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_payables" style="color: #E5E7EB; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-receipt" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #E5E7EB; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Pendentes</p>
                            <h5 id="pendent_payables_curerency" style="color: #FFBD59; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_pendent" style="color: #E5E7EB; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="far fa-hourglass" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #E5E7EB; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Pagas</p>
                            <h5 id="pay_payables_curerency" style="color: #22C55E; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_pay" style="color: #E5E7EB; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(34,197,94,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle" style="color: #22C55E; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #E5E7EB; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Fixas</p>
                            <h5 id="fixed_payables_curerency" style="color: #E5E7EB; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_fixed" style="color: #E5E7EB; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #E5E7EB; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Recorrentes</p>
                            <h5 id="recurring_payables_curerency" style="color: #E5E7EB; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_recurring" style="color: #E5E7EB; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
                        </div>
                        <div style="width: 48px; height: 48px; background-color: rgba(255,189,89,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-sync-alt" style="color: #FFBD59; font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-6 mb-3">
                <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #E5E7EB; font-size: 14px; margin: 0; font-weight: 500; opacity: 0.7;">Canceladas</p>
                            <h5 id="cancelled_payables_curerency" style="color: #F87171; font-size: 20px; font-weight: 600; margin: 5px 0 0 0;">R$0,00</h5>
                            <p id="total_cancelled" style="color: #E5E7EB; font-size: 12px; margin: 5px 0 0 0; opacity: 0.6;">0 contas</p>
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
            <div class="d-flex justify-content-end align-items-center mb-4">
                <a href="#" data-original-title="Nova Conta a Pagar" id="btn-modal-payable" data-type="add-payable" data-toggle="tooltip" class="btn" style="background-color: #FFBD59; color: #0F172A !important; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; transition: all 0.3s;"> <i class="fa fa-plus"></i> Nova Conta a Pagar</a>
            </div>

            <div class="form-row mb-4" style="background-color: #111827; padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">

                <div class="form-group col-md-12 mb-3">
                    <label style="color: #E5E7EB; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Filtros Rápidos</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-sm" type="button" id="btn-filter-current-month" style="background-color: #1E293B; color: #E5E7EB; border: 1px solid rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-calendar"></i> Mês Atual
                        </button>
                        <button class="btn btn-sm" type="button" id="btn-filter-next-month" style="background-color: #1E293B; color: #E5E7EB; border: 1px solid rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-calendar-alt"></i> Próximo Mês
                        </button>
                        <button class="btn btn-sm" type="button" id="btn-filter-all" style="background-color: #1E293B; color: #E5E7EB; border: 1px solid rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 6px; font-weight: 500;">
                            <i class="fa fa-list"></i> Todos
                        </button>
                    </div>
                </div>

                <div class="form-group col-md-4 col-6">
                    <label style="color: #E5E7EB; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Tipo de Data</label>
                    <select class="form-control" id="filter-type" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 12px; color: #E5E7EB;">
                        <option value="date_due">Data do Vencimento</option>
                        <option value="date_payment">Data do Pagamento</option>
                    </select>
                </div>

                <div class="form-group col-md-4 col-6">
                    <label style="color: #E5E7EB; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Data inicial</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data inicial" id="filter-date-ini" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 12px; color: #E5E7EB;">
                </div>

                <div class="form-group col-md-4 col-6">
                    <label style="color: #E5E7EB; font-weight: 500; font-size: 14px; margin-bottom: 8px;">Data final</label>
                    <input type="date" autocomplete="off" class="form-control" placeholder="Data Final" id="filter-date-end" style="background-color: #0F172A; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 12px; color: #E5E7EB;">
                </div>

                <div class="form-group col-md-12 mt-3">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <fieldset style="border: 1px solid rgba(255,189,89,0.3); border-radius: 8px; padding: 15px; margin: 0; background-color: #1E293B; position: relative;">
                                <legend style="color: #FFBD59; font-size: 14px; font-weight: 600; padding: 0 10px; margin: 0; border: none;">Tipo de Conta</legend>
                                <div class="d-flex" style="gap: 20px; margin-top: 10px; flex-wrap: nowrap; overflow-x: auto;">
                                    <label style="color: #E5E7EB; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="type-checkbox" value="Fixa" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Fixa
                                    </label>
                                    <label style="color: #E5E7EB; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="type-checkbox" value="Recorrente" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Recorrente
                                    </label>
                                    <label style="color: #E5E7EB; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="type-checkbox" value="Parcelada" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Parcelada
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6 col-12">
                            <fieldset style="border: 1px solid rgba(255,189,89,0.3); border-radius: 8px; padding: 15px; margin: 0; background-color: #1E293B; position: relative;">
                                <legend style="color: #FFBD59; font-size: 14px; font-weight: 600; padding: 0 10px; margin: 0; border: none;">Status</legend>
                                <div class="d-flex" style="gap: 20px; margin-top: 10px; flex-wrap: nowrap; overflow-x: auto;">
                                    <label style="color: #E5E7EB; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="status-checkbox" value="Pendente" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Pendente
                                    </label>
                                    <label style="color: #E5E7EB; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="status-checkbox" value="Pago" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Pago
                                    </label>
                                    <label style="color: #E5E7EB; font-weight: 400; font-size: 14px; cursor: pointer; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                                        <input type="checkbox" class="status-checkbox" value="Cancelado" style="margin-right: 6px; width: 18px; height: 18px; cursor: pointer;">
                                        Cancelado
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>



      <div class="col-md-12">
        <div style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
            <div class="row d-flex justify-content-center align-items-center mb-3">
                <div class="col-12">
                    <div id="pagination" class="d-flex justify-content-start align-items-center">
                        <button class="btn btn-sm" onclick="loadPayables(prevPage)" style="background-color: #111827; color: #E5E7EB; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 6px 12px; margin-right: 10px; font-weight: 500;">Anterior</button>
                        <span id="page-num" style="color: #E5E7EB; font-weight: 500; margin: 0 10px;"></span>
                        <button class="btn btn-sm" onclick="loadPayables(nextPage)" style="background-color: #111827; color: #E5E7EB; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 6px 12px; font-weight: 500;">Próxima</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">#</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Fornecedor</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Descrição</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Tipo</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Vencimento</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Pago em</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Valor</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Forma de Pagamento</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;">Status</th>
                        <th style="color: #E5E7EB; font-weight: 600; padding: 12px; border: none; font-size: 14px;"></th>
                    </tr>
                    </thead>

                    <tbody id="list-payables" style="background-color: #111827;">

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
        <div class="modal-content" style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1);">
            <form action="" class="form-horizontal" id="form-request-payable">
                <div class="modal-header" style="background-color: #1E293B; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="modal-title" id="modalPayableLabel" style="color: #FFBD59; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #E5E7EB;">
                        <span aria-hidden="true" style="color: #E5E7EB;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-payable" style="background-color: #111827;">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer" style="background-color: #1E293B; border-top: 1px solid rgba(255,255,255,0.1);">
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
        <div class="modal-content" style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1);">
            <form action="" class="form-horizontal" id="form-request-notifications">
                <div class="modal-header" style="background-color: #1E293B; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="modal-title" id="modalNotificationsLabel" style="color: #FFBD59; font-weight: 600;"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #E5E7EB;">
                        <span aria-hidden="true" style="color: #E5E7EB;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-notifications" style="background-color: #111827;">
                    <!-- conteudo -->
                    <!-- conteudo -->
                </div><!-- modal-body -->
                <div class="modal-footer" style="background-color: #1E293B; border-top: 1px solid rgba(255,255,255,0.1);">
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
        <div class="modal-content" style="background-color: #111827; border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header" style="background-color: #1E293B; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h5 class="modal-title" id="modalErrorLabel" style="color: #FFBD59; font-weight: 600;"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #E5E7EB;">
                    <span aria-hidden="true" style="color: #E5E7EB;">&times;</span>
                </button>
            </div>

            <style>

                pre {
                   background-color: #0F172A;
                   border: 1px solid rgba(255,255,255,0.1);
                   padding: 10px 20px;
                   margin: 20px;
                   color: #E5E7EB;
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

            <div class="modal-body" id="modal-content-error" style="background-color: #111827; color: #E5E7EB;">

                <!-- conteudo -->
                <!-- conteudo -->
            </div><!-- modal-body -->
        </div>
    </div>
 </div>
 <!-- Modal :: Log -->


@section('styles')
<style>
    /* Estilos Dark Mode para Contas a Pagar */
    body {
        background-color: #0F172A !important;
    }

    .content-wrapper {
        background-color: #0F172A !important;
    }

    .content-header {
        background-color: #0F172A !important;
    }

    .content {
        background-color: #0F172A !important;
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7) !important;
    }

    #btn-modal-payable:hover {
        background-color: #FFD700 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(255, 189, 89, 0.3);
    }


    #pagination button:hover:not(:disabled) {
        background-color: #1E293B !important;
        border-color: #FFBD59 !important;
        color: #FFBD59 !important;
    }

    #pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .form-control:focus {
        border-color: #FFBD59 !important;
        box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
        outline: none;
        background-color: #0F172A !important;
        color: #E5E7EB !important;
    }

    select.form-control:focus,
    input.form-control:focus {
        border-color: #FFBD59 !important;
        box-shadow: 0 0 0 3px rgba(255, 189, 89, 0.2) !important;
        outline: none;
        background-color: #0F172A !important;
        color: #E5E7EB !important;
    }

    select.form-control,
    input.form-control {
        background-color: #0F172A !important;
        color: #E5E7EB !important;
    }

    select.form-control option {
        background-color: #111827 !important;
        color: #E5E7EB !important;
    }

    select.form-control option:checked {
        background-color: #1E293B !important;
        color: #E5E7EB !important;
    }

    /* Força a cor branca em todos os estados do select */
    select {
        color: #E5E7EB !important;
    }

    select:focus {
        color: #E5E7EB !important;
    }

    select option {
        color: #E5E7EB !important;
    }

    select option:checked {
        color: #E5E7EB !important;
    }

    /* Estilos para checkboxes de status e tipo */
    .status-checkbox,
    .type-checkbox {
        accent-color: #FFBD59;
        cursor: pointer;
    }

    .status-checkbox:checked,
    .type-checkbox:checked {
        background-color: #FFBD59;
    }

    /* Botões de filtro rápido */
    #btn-filter-current-month:hover,
    #btn-filter-next-month:hover,
    #btn-filter-all:hover {
        background-color: #FFBD59 !important;
        color: #0F172A !important;
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
        background-color: #111827 !important;
    }

    @media (max-width: 768px) {
        .col-md-6 {
            margin-bottom: 15px;
        }
    }
</style>
@endsection

@section('scripts')

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
                            color: '#E5E7EB',
                            confirmButtonColor: "#2563EB",
                            showClass: showClassWarn
                        });
                    } else{
                        Swal.fire({
                            text: xhr.responseJSON,
                            width: 300,
                            icon: 'error',
                            color: '#E5E7EB',
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

    // Inicializar com todos os status e tipos marcados
    document.querySelectorAll('.status-checkbox').forEach(function(cb) {
        cb.checked = true;
    });

    document.querySelectorAll('.type-checkbox').forEach(function(cb) {
        cb.checked = true;
    });
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

        // Construir URL com múltiplos status e tipos
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
                $('#total_cancelled').text(data.result.data[0].qtd_cancelado + ' contas');
                $('#cancelled_payables_curerency').text(parseFloat(data.result.data[0].cancelado_currency).toLocaleString('pt-BR', currencyOptions));
            }
        $('#list-payables').html('');

        var html = '';
            if(data.result.data.length > 0){
                $.each(data.result.data, function(i, item) {
                var installmentText = item.installment_number ? ' ('+item.installment_number+'/'+item.installments+')' : '';
                var typeBadgeColor = item.type == 'Fixa' ? '#FFBD59' : item.type == 'Recorrente' ? '#22C55E' : '#E5E7EB';
                var typeBadgeBg = item.type == 'Fixa' ? 'rgba(255,189,89,0.2)' : item.type == 'Recorrente' ? 'rgba(34,197,94,0.2)' : 'rgba(229,231,235,0.1)';
                var statusBadgeColor = item.status == 'Pago' ? '#22C55E' : item.status == 'Pendente' ? '#FFBD59' : '#F87171';
                var statusBadgeBg = item.status == 'Pago' ? 'rgba(34,197,94,0.2)' : item.status == 'Pendente' ? 'rgba(255,189,89,0.2)' : 'rgba(248,113,113,0.2)';
                var datePayment = item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-';
                var editButton = item.status == 'Pendente' ? '<a href="#" data-original-title="Editar conta" id="btn-modal-payable" data-type="edit-payable" data-payable="'+item.id+'" data-placement="left" data-tt="tooltip" style="background-color: #FFBD59; color: #0F172A !important; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="far fa-edit"></i></a>' : '';
                var deleteButton = item.status == 'Pendente' ? '<a href="#" data-original-title="Cancelar Conta" id="btn-delete-payable" data-placement="left" data-payable="'+item.id+'" data-tt="tooltip" style="background-color: #F87171; color: #FFFFFF; border: none; padding: 4px 8px; border-radius: 4px; font-size: 12px; text-decoration: none; display: inline-block; font-weight: 600;"> <i class="fas fa-times"></i></a>' : '';

                html += '<tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#1E293B\'" onmouseout="this.style.backgroundColor=\'#111827\'">';
                html += '<td style="padding: 12px; color: #E5E7EB; font-size: 14px;">'+item.id+installmentText+'</td>';
                html += '<td style="padding: 12px; color: #E5E7EB; font-size: 14px; font-weight: 500;">'+item.supplier_name+'</td>';
                html += '<td style="padding: 12px; color: #E5E7EB; font-size: 14px;">'+item.description+'</td>';
                html += '<td style="padding: 12px;"><span style="background-color: '+typeBadgeBg+'; color: '+typeBadgeColor+'; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">'+item.type+'</span></td>';
                html += '<td style="padding: 12px; color: #E5E7EB; font-size: 14px;">'+moment(item.date_due).format('DD/MM/YYYY')+'</td>';
                html += '<td style="padding: 12px; color: #E5E7EB; font-size: 14px;">'+datePayment+'</td>';
                var priceOptions = {};
                priceOptions.minimumFractionDigits = 2;
                var priceFormatted = parseFloat(item.price).toLocaleString('pt-br', priceOptions);
                html += '<td style="padding: 12px; color: #FFBD59; font-size: 14px; font-weight: 600;">R$ '+priceFormatted+'</td>';
                html += '<td style="padding: 12px; color: #E5E7EB; font-size: 14px;">'+(item.payment_method || '-')+'</td>';
                html += '<td style="padding: 12px;"><span style="background-color: '+statusBadgeBg+'; color: '+statusBadgeColor+'; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;">'+item.status+'</span></td>';
                html += '<td style="padding: 12px;">'+editButton+deleteButton+'</td>';
                html += '</tr>';
            });
            }else{
                html += '<tr><td style="text-align:center; padding: 40px; color: #E5E7EB; font-size: 14px;" colspan="99">Nenhuma conta encontrada</td></tr>';
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

    // Botão Mês Atual
    document.getElementById('btn-filter-current-month').addEventListener('click', function() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        filterInputDateIni.value = firstDay.toISOString().split('T')[0];
        filterInputDateEnd.value = lastDay.toISOString().split('T')[0];

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

        currentPage = 1;
        loadPayables(currentPage);
    });

    // Botão Todos
    document.getElementById('btn-filter-all').addEventListener('click', function() {
        filterInputDateIni.value = '';
        filterInputDateEnd.value = '';

        // Marcar todos os checkboxes de status e tipo
        document.querySelectorAll('.status-checkbox').forEach(function(cb) {
            cb.checked = true;
        });

        document.querySelectorAll('.type-checkbox').forEach(function(cb) {
            cb.checked = true;
        });

        currentPage = 1;
        loadPayables(currentPage);
    });

    $(document).on('click', '#btn-delete-payable', function(e) {
        var payable = $(this).data('payable');
        Swal.fire({
            title: 'Deseja cancelar esta conta?',
            text: "Você não poderá reverter isso!",
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#FFBD59',
            cancelButtonColor: '#1E293B',
            confirmButtonText: 'Sim, cancelar!'
        }).then(function(result) {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    }
                });
                $.ajax({
                    url: "{{url('admin/payables')}}"+'/'+payable,
                    method: 'DELETE',
                    success:function(data){
                        loadPayables();
                    },
                    error:function (xhr) {
                        var showClassWobble = {};
                        showClassWobble.popup = 'animate__animated animate__wobble';
                        if(xhr.status === 422){
                            Swal.fire({
                                text: xhr.responseJSON,
                                icon: 'warning',
                                showClass: showClassWobble
                            });
                        } else{
                            Swal.fire({
                                text: xhr.responseJSON,
                                icon: 'error',
                                showClass: showClassWobble
                            });
                        }
                    }
                });
            }
        });
    });

    loadPayables();


</script>



@endsection


@endsection
