@extends('layouts.admin')

@section('content')

<style>
/* Estilos modernos - Cliente */
.profile-card {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.profile-photo-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto 15px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.profile-photo-wrapper:hover {
    transform: scale(1.05);
}

.photo-overlay {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.photo-overlay i {
    color: #007bff;
    font-size: 18px;
}

.profile-photo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid #fff;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.profile-photo-fallback {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: 5px solid #fff;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-photo-fallback i {
    font-size: 70px;
    color: #fff;
}

.profile-card h5 {
    color: #fff;
    margin: 10px 0 5px;
    font-weight: 600;
    font-size: 18px;
}

.profile-card p {
    color: rgba(255,255,255,0.8);
    font-size: 14px;
    margin: 0;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.modern-card {
    background: #fff;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 25px;
    border: none;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
}

.section-title i {
    margin-right: 10px;
    color: #007bff;
    font-size: 18px;
}

.form-group label {
    font-weight: 500;
    color: #4a5568;
    font-size: 13px;
    margin-bottom: 5px;
}

.form-control {
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.save-button {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    border-radius: 10px;
    padding: 12px 30px;
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
}

.save-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 123, 255, 0.4);
}

.save-button i {
    margin-right: 8px;
}

.compact-input {
    height: 42px;
}

#form-content-customer-service .form-control,
#modalCustomerService .form-control {
    height: 42px;
}

/* Estilos para Swal e checkboxes */
.custom-swal { font-family: Arial, sans-serif; color: #333; font-size: 12px; }
.custom-swal .swal2-title { font-size: 24px; color: #007bff; }
.custom-swal .swal2-content { font-size: 14px; }
.checkbox-container { display: inline-flex; align-items: center; margin-right: 20px; }
.checkbox-container input[type="checkbox"] { width: 20px; height: 20px; }
.checkbox-container label { margin: 0; display: flex; align-items: center; }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: 600; color: #2d3748;">{{ $title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{url($link)}}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">{{Request::get('act') == 'add' ? $breadcrumb_new : $breadcrumb_edit}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab"><i class="fas fa-user mr-1"></i> Dados do Cliente</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ !isset($data) ? 'disabled' : '' }}" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab"><i class="fas fa-cogs mr-1"></i> Serviços</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ !isset($data) ? 'disabled' : '' }}" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab"><i class="fas fa-file-invoice mr-1"></i> Faturas</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <!-- TAB: Dados do Cliente -->
                                <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel">
                                    <form class="form" enctype="multipart/form-data">
                                        <input type="hidden" id="thumbnail" name="image" value="{{ isset($data->image) ? $data->image : '' }}">
                                        <div class="row">
                                            <!-- Coluna Esquerda: Foto do Cliente -->
                                            <div class="col-lg-4 col-md-5">
                                                <div class="profile-card">
                                                    @php
                                                        $hasImage = false;
                                                        if(isset($data->image) && $data->image != null && file_exists(public_path($data->image))) {
                                                            $hasImage = true;
                                                        }
                                                    @endphp
                                                    <div class="profile-photo-wrapper" id="lfm" data-input="thumbnail" data-preview="holder">
                                                        @if($hasImage)
                                                            <img src="{{ url($data->image) }}" id="holder" class="profile-photo" alt="Foto do cliente">
                                                        @else
                                                            <div class="profile-photo-fallback" id="holder">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                        @endif
                                                        <div class="photo-overlay">
                                                            <i class="fas fa-camera"></i>
                                                        </div>
                                                    </div>
                                                    <h5>{{ isset($data->name) ? $data->name : 'Novo Cliente' }}</h5>
                                                    <p><i class="fas fa-envelope"></i> {{ isset($data->email) ? $data->email : 'email@exemplo.com' }}</p>
                                                    @if(isset($data->status))
                                                    <div class="mt-3">
                                                        <span class="status-badge {{ $data->status === 'Ativo' ? 'status-active' : 'status-inactive' }}">
                                                            <i class="fas fa-circle" style="font-size: 8px;"></i> {{ $data->status }}
                                                        </span>
                                                    </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn save-button btn-block" id="btn-salvar">
                                                    <i class="fas fa-save"></i> Salvar Cliente
                                                </button>
                                            </div>

                                            <!-- Coluna Direita: Formulário em Cards -->
                                            <div class="col-lg-8 col-md-7">
                                                <!-- 👤 Dados do Cliente -->
                                                <div class="modern-card">
                                                    <h6 class="section-title">
                                                        <i class="fas fa-user"></i> Dados do Cliente
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Tipo</label>
                                                                <select class="form-control compact-input" name="type" id="type">
                                                                    <option {{ isset($data->type) && $data->type === 'Física' ? 'selected' : '' }} value="Física">Física</option>
                                                                    <option {{ isset($data->type) && $data->type === 'Jurídica' ? 'selected' : '' }} value="Jurídica">Jurídica</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>CPF/CNPJ <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="document" autocomplete="off" required value="{{isset($data->document) ? $data->document : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Nome <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="name" id="name" autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label>Empresa (Razão Social)</label>
                                                                <input type="text" class="form-control compact-input" name="company" id="company" autocomplete="off" value="{{isset($data->company) ? $data->company : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Nascimento</label>
                                                                <input type="date" max="{{date('Y-m-d')}}" class="form-control compact-input" name="birthdate" id="birthdate" autocomplete="off" value="{{isset($data->birthdate) ? $data->birthdate : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <select class="form-control compact-input" name="status" id="status">
                                                                    <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                                                                    <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 📧 Contato -->
                                                <div class="modern-card">
                                                    <h6 class="section-title">
                                                        <i class="fas fa-envelope"></i> Contato
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><i class="fas fa-envelope text-muted"></i> E-mail <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control compact-input" name="email" id="email" autocomplete="off" required value="{{isset($data->email) ? $data->email : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><i class="fas fa-envelope text-muted"></i> E-mail 2</label>
                                                                <input type="email" class="form-control compact-input" name="email2" id="email2" autocomplete="off" value="{{isset($data->email2) ? $data->email2 : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><i class="fas fa-phone text-muted"></i> Telefone</label>
                                                                <input type="text" class="form-control compact-input" name="phone" id="telephone" autocomplete="off" value="{{isset($data->phone) ? $data->phone : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label><i class="fab fa-whatsapp text-muted"></i> WhatsApp</label>
                                                                <input type="text" class="form-control compact-input" name="whatsapp" id="whatsapp" autocomplete="off" value="{{isset($data->whatsapp) ? $data->whatsapp : ''}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 📍 Endereço -->
                                                <div class="modern-card">
                                                    <h6 class="section-title">
                                                        <i class="fas fa-map-marker-alt"></i> Endereço
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label><i class="fas fa-mail-bulk text-muted"></i> CEP <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="cep" id="cep" autocomplete="off" required value="{{isset($data->cep) ? $data->cep : ''}}" placeholder="00000-000">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class="form-group">
                                                                <label><i class="fas fa-road text-muted"></i> Endereço <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="address" id="address" autocomplete="off" required value="{{isset($data->address) ? $data->address : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>Número <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="number" id="number" autocomplete="off" required value="{{isset($data->number) ? $data->number : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Bairro <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="district" id="district" autocomplete="off" required value="{{isset($data->district) ? $data->district : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Cidade <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="city" id="city" autocomplete="off" required value="{{isset($data->city) ? $data->city : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label>UF <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control compact-input" name="state" id="state" autocomplete="off" readonly required value="{{isset($data->state) ? $data->state : ''}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Complemento</label>
                                                                <input type="text" class="form-control compact-input" name="complement" id="complement" autocomplete="off" value="{{isset($data->complement) ? $data->complement : ''}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 🔔 Notificações -->
                                                <div class="modern-card">
                                                    <h6 class="section-title">
                                                        <i class="fas fa-bell"></i> Notificações
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group d-flex align-items-center" style="min-height: 42px;">
                                                                <div class="form-check checkbox-container mr-4">
                                                                    <input type="hidden" name="notification_whatsapp" value="n">
                                                                    <input type="checkbox" class="form-check-input" name="notification_whatsapp" id="notification_whatsapp" value="s" {{ isset($data->notification_whatsapp) && $data->notification_whatsapp === 's' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="notification_whatsapp"><i class="fab fa-whatsapp text-success mr-1"></i> Notificar WhatsApp</label>
                                                                </div>
                                                                <div class="form-check checkbox-container">
                                                                    <input type="hidden" name="notification_email" value="n">
                                                                    <input type="checkbox" class="form-check-input" name="notification_email" id="notification_email" value="s" {{ isset($data->notification_email) && $data->notification_email === 's' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="notification_email"><i class="fas fa-envelope text-primary mr-1"></i> Notificar E-mail</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="d-block mb-2" style="font-size: 13px; color: #4a5568;">Quando notificar:</label>
                                                            <div class="d-flex flex-wrap mb-3">
                                                                <div class="form-check checkbox-container mr-4">
                                                                    <input type="hidden" name="notificate_5_days" value="n">
                                                                    <input type="checkbox" class="form-check-input" name="notificate_5_days" id="notificate_5_days" value="s" {{ isset($data->notificate_5_days) && $data->notificate_5_days === 's' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="notificate_5_days">5 dias antes</label>
                                                                </div>
                                                                <div class="form-check checkbox-container mr-4">
                                                                    <input type="hidden" name="notificate_2_days" value="n">
                                                                    <input type="checkbox" class="form-check-input" name="notificate_2_days" id="notificate_2_days" value="s" {{ isset($data->notificate_2_days) && $data->notificate_2_days === 's' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="notificate_2_days">2 dias antes</label>
                                                                </div>
                                                                <div class="form-check checkbox-container">
                                                                    <input type="hidden" name="notificate_due" value="n">
                                                                    <input type="checkbox" class="form-check-input" name="notificate_due" id="notificate_due" value="s" {{ isset($data->notificate_due) && $data->notificate_due === 's' ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="notificate_due">No vencimento</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label><i class="fas fa-sticky-note text-muted"></i> Observação</label>
                                                                <textarea class="form-control" name="obs" id="obs" rows="4" autocomplete="off" style="border-radius: 8px;">{{isset($data->obs) ? $data->obs : ''}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- TAB: Serviços -->
                                <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel">
                                    <div class="col-md-12">
                                        <a href="#" class="btn save-button" id="btn-modal-customer-service" data-type="add-customer-service"><i class="fa fa-plus"></i> Adicionar serviço</a>
                                        <br><br>
                                        <div class="table-responsive fixed-solution">
                                            <table class="table table-hover table-striped table-sm">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th>Descrição</th>
                                                    <th>Preço</th>
                                                    <th>Vencimento</th>
                                                    <th>Período</th>
                                                    <th>Status</th>
                                                    <th style="width: 150px;"></th>
                                                </tr>
                                                </thead>
                                                <tbody class="tbodyCustom" id="load-customer-services"></tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <a href="#" class="btn save-button" id="btn-modal-customer-service"><i class="fa fa-plus"></i> Adicionar serviço</a>
                                    </div>
                                </div>

                                <!-- TAB: Faturas -->
                                <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel">
                                    <div class="col-md-12">
                                        <div class="table-responsive fixed-solution">
                                            <table class="table table-hover table-striped table-sm">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Descrição</th>
                                                    <th>Preço</th>
                                                    <th>Gateway de Pagamento</th>
                                                    <th>Forma de Pagamento</th>
                                                    <th>Data</th>
                                                    <th>Vencimento</th>
                                                    <th>Pago em</th>
                                                    <th>Status</th>
                                                    <th style="width: 150px;"></th>
                                                </tr>
                                                </thead>
                                                <tbody class="tbodyCustom" id="load-invoices"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal :: Form Customer Service -->
<div class="modal fade" id="modalCustomerService" tabindex="-1" role="dialog" aria-labelledby="modalCustomerServiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" class="form-horizontal" id="form-request-customer-service">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCustomerServiceLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-customer-service"><!-- carregado via AJAX --></div>
                <div class="modal-footer">
                    <button type="button" class="btn save-button" id="btn-save-customer-service"><i class="fa fa-check"></i> Salvar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal :: Form Invoice -->
<div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" class="form-horizontal" id="form-request-invoice">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInvoiceLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-invoice"></div>
                <div class="modal-footer">
                    <button type="button" class="btn save-button" id="btn-save-invoice"><i class="fa fa-check"></i> Salvar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal :: Notifications -->
<div class="modal fade" id="modalNotifications" tabindex="-1" role="dialog" aria-labelledby="modalNotificationsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" class="form-horizontal" id="form-request-notifications">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNotificationsLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="form-content-notifications"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal :: Error -->
<div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modalErrorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalErrorLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <style>
                pre { background-color: #fff; border: 1px solid silver; padding: 10px 20px; margin: 20px; }
                .json-key { color: brown; }
                .json-value { color: navy; }
                .json-string { color: olive; }
            </style>
            <div class="modal-body" id="modal-content-error"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{url('/vendor/laravel-filemanager/js/stand-alone-button-normal.js')}}"></script>
<script>

$('#lfm').filemanager('image');

// Open Modal - Error
$(document).on("click", "#btn-modal-error", function() {
    var id = $(this).data('invoice');
    var url = "{{url('admin/invoice-error')}}"+'/'+id;
    $("#modal-error").modal('show');
    $.get(url,
        $(this).addClass('modal-scrollfix').find('#modal-content-error').html('Carregando...'),
        function(data) {
            var json = JSON.stringify(JSON.parse(data.msg_erro), null, 2);
            $("#modal-content-error").html('<pre>'+json+'</pre>');
        });
});

function sendNotification(invoice_id){
    const whatsappContainer = document.createElement('div');
    whatsappContainer.classList.add('checkbox-container');
    const whatsappCheckbox = document.createElement('input');
    whatsappCheckbox.type = 'checkbox';
    whatsappCheckbox.id = 'whatsapp-checkbox';
    whatsappCheckbox.value = 'whatsapp';
    whatsappContainer.appendChild(whatsappCheckbox);
    const whatsappLabel = document.createElement('label');
    whatsappLabel.textContent = 'WhatsApp';
    whatsappLabel.setAttribute('for', 'whatsapp-checkbox');
    whatsappContainer.appendChild(whatsappLabel);
    const emailContainer = document.createElement('div');
    emailContainer.classList.add('checkbox-container');
    const emailCheckbox = document.createElement('input');
    emailCheckbox.type = 'checkbox';
    emailCheckbox.id = 'email-checkbox';
    emailCheckbox.value = 'email';
    emailContainer.appendChild(emailCheckbox);
    const emailLabel = document.createElement('label');
    emailLabel.textContent = 'Email';
    emailLabel.setAttribute('for', 'email-checkbox');
    emailContainer.appendChild(emailLabel);
    const form = document.createElement('form');
    form.appendChild(whatsappContainer);
    form.appendChild(emailContainer);
    Swal.fire({
        title: 'Selecione as opções:',
        html: form,
        focusConfirm: false,
        preConfirm: () => {
            const selectedOptions = [];
            if (whatsappCheckbox.checked) selectedOptions.push(whatsappCheckbox.value);
            if (emailCheckbox.checked) selectedOptions.push(emailCheckbox.value);
            if (selectedOptions.length === 0) Swal.showValidationMessage('Selecione pelo menos uma opção');
            return selectedOptions;
        }
    }).then((result) => {
        if (!result.dismiss) {
            const selectedOptions = result.value;
            const loadingAlert = Swal.fire({ title: 'Aguarde...', allowOutsideClick: false, showConfirmButton: false, onBeforeOpen: () => Swal.showLoading() });
            var url = '{{url("admin/invoice-notificate")}}'+'/'+invoice_id;
            fetch(url, {
                method: 'POST',
                body: JSON.stringify({ selectedOptions }),
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{csrf_token()}}" }
            })
            .then(response => response.json())
            .then(data => {
                var payment_method = `<p><b class="text-primary">Forma de pagamento: </b>${data.payment_method}</p><hr>`;
                var result_whatsapp = `<p class="text-primary"><b>Whatsapp:</b></p> <p><b>Mensagem:</b> ${data.whatsapp.mensagem}</p><p><b>PIX:</b> ${data.whatsapp.pix}</p><p><b>Boleto:</b> ${data.whatsapp.boleto}</p>`;
                var result_email = `<p><b class="text-primary">E-mail:</b> ${data.email}</p>`;
                loadingAlert.close();
                Swal.fire({ title: 'Notificações', html: payment_method+result_whatsapp+'<hr>'+result_email, icon: 'success', confirmButtonText: 'OK', customClass: { popup: 'custom-swal', title: 'swal2-title', content: 'swal2-content' } });
            })
            .catch(error => { console.error('Erro:', error); loadingAlert.close(); });
        }
    });
}

$(window).on("load", function(){
    @if(isset($data))
        loadCustomerServices();
        loadInvoices();
    @endif
});

// Save customer
$(document).on('click', '#btn-salvar', function(e) {
    e.preventDefault();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{csrf_token()}}" } });
    var url_act = "{{Request::get('act')}}";
    var url = url_act == 'edit' ? "{{ url($linkUpdate) }}" : "{{ url($linkStore) }}";
    var method = url_act == 'edit' ? 'PUT' : 'POST';
    var data = $('.form').serialize();
    $.ajax({
        url: url,
        data: data,
        method: method,
        success: function(data){
            var message = url_act == 'add' ? data.data : data;
            Swal.fire({
                width: 400,
                title: "<h5 style='color:#007bff'>" + message + "</h5>",
                icon: 'success',
                showConfirmButton: false,
                showClass: { popup: 'animate__animated animate__backInUp' },
                allowOutsideClick: false,
                html: '<a href="{{url($linkFormAdd)}}" class="btn btn-secondary btn-md" style="border-radius: 8px;"><i class="fa fa-plus"></i> Novo</a> ' +
                    `<a href="${url_act == 'add' ? '{{url($linkFormEdit)}}'+data.id : '{{url($linkFormEdit)}}'}" class="btn btn-success btn-md" style="border-radius: 8px; ${url_act == 'add' ? '' : 'display:none;'}"><i class="fa fa-edit"></i> Editar</a> ` +
                    '<a href="{{url($link)}}" class="btn btn-primary btn-md" style="border-radius: 8px;"><i class="fa fa-list"></i> Listar</a>',
            });
        },
        error: function(xhr) {
            if(xhr.status === 422){
                Swal.fire({ text: xhr.responseJSON, width: 300, icon: 'warning', confirmButtonColor: "#007bff", showClass: { popup: 'animate__animated animate__wobble' } });
            } else {
                Swal.fire({ text: xhr.responseJSON, width: 300, icon: 'error', confirmButtonColor: "#007bff", showClass: { popup: 'animate__animated animate__wobble' } });
            }
        }
    });
});

// Open Modal - Customer Service
$(document).on("click", "#btn-modal-customer-service", function(e) {
    e.preventDefault();
    var type = $(this).data('type');
    var customer_id = "{{ isset($data) ? $data->id : ''}}";
    $("#modalCustomerService").modal('show');
    if(type == 'add-customer-service'){
        $("#modalCustomerServiceLabel").html('Adicionar Serviço');
        var url = `{{ url("/admin/customer-services/form?customer_id=") }}${customer_id}`;
    } else {
        $("#modalCustomerServiceLabel").html('Editar Serviço');
        var customer_service_id = $(this).data('customer-service-id');
        var url = `{{ url("/admin/customer-services/form?customer_id=") }}${customer_id}&id=${customer_service_id}`;
    }
    $.get(url, $(this).addClass('modal-scrollfix').find('#form-content-customer-service').html('Carregando...'), function(data) {
        $("#form-content-customer-service").html(data);
        $('.money').mask('000.000.000.000.000,00', {reverse: true});
        $('[data-tt="tooltip"]').tooltip();
        $('#service_id').on('change', function() {
            var service_price = $(this).find(':selected').data('price');
            $('#price').val(parseFloat(service_price).toLocaleString('pt-br', {minimumFractionDigits: 2}));
        });
    });
});

// Save customer service
$(document).on('click', '#btn-save-customer-service', function(e) {
    e.preventDefault();
    $("#btn-save-customer-service").attr("disabled", true).html('Aguarde...');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{csrf_token()}}" } });
    var data = $('#form-request-customer-service').serialize();
    var customer_service_id = $('#customer_service_id').val();
    var url = customer_service_id != '' ? "{{ url('admin/customer-services') }}/"+customer_service_id : "{{ url('admin/customer-services') }}";
    var method = customer_service_id != '' ? 'PUT' : 'POST';
    $.ajax({
        url: url,
        data: data,
        method: method,
        success: function(data){
            $("#btn-save-customer-service").attr("disabled", false).html('<i class="fa fa-check"></i> Salvar');
            Swal.fire({ width: 350, title: "<h5 style='color:#007bff'>" + data + "</h5>", icon: 'success', showConfirmButton: true, showClass: { popup: 'animate__animated animate__backInUp' }, allowOutsideClick: false }).then((result) => {
                $('#modalCustomerService').modal('hide');
                loadCustomerServices();
                loadInvoices();
            });
        },
        error: function(xhr) {
            $("#btn-save-customer-service").attr("disabled", false).html('<i class="fa fa-check"></i> Salvar');
            Swal.fire({ text: xhr.responseJSON, width: 300, icon: xhr.status === 422 ? 'warning' : 'error', confirmButtonColor: "#007bff", showClass: { popup: 'animate__animated animate__wobble' } });
        }
    });
});

function loadCustomerServices(){
    var customer_id = "{{ isset($data) ? $data->id : ''}}";
    $.ajax({
        url: "{{url('/admin/load-customer-services')}}"+'/'+customer_id,
        method: 'GET',
        success: function(data){
            $('#load-customer-services').html('');
            var html = '';
            $.each(data, function(i, item) {
                html += '<tr>';
                html += `<td>${item.description}</td>`;
                html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
                html += `<td>${item.day_due}</td>`;
                html += `<td>${item.period}</td>`;
                html += `<td><label class="badge badge-${item.status == 'Ativo' ? 'success' : 'danger'}">${item.status}</label></td>`;
                html += `<td>
                    <a href="#" data-original-title="Editar Serviço" id="btn-modal-customer-service" data-type="edit-customer-service" data-customer-service-id="${item.id}" data-toggle="tooltip" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Editar</a>
                    <a href="#" data-original-title="Deletar Serviço" id="btn-delete-customer-service" data-customer-service-id="${item.id}" data-toggle="tooltip" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Deletar</a>
                </td>`;
                html += '</tr>';
            });
            $('#load-customer-services').append(html);
            $('[data-tt="tooltip"]').tooltip();
        },
        error: function(xhr) { Swal.fire({ text: xhr.responseJSON, icon: xhr.status === 422 ? 'warning' : 'error', showClass: { popup: 'animate__animated animate__wobble' } }); }
    });
}

$(document).on('click', '#btn-delete-customer-service', function(e) {
    e.preventDefault();
    var customer_service_id = $(this).data('customer-service-id');
    Swal.fire({ title: 'Deseja remover este registro?', text: "Você não poderá reverter isso!", icon: 'question', showCancelButton: true, cancelButtonText: 'Cancelar', confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sim, deletar!' }).then((result) => {
        if (result.value) {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{csrf_token()}}" } });
            $.ajax({
                url: "{{url('admin/customer-services')}}/"+customer_service_id,
                method: 'DELETE',
                success: function(){ loadCustomerServices(); },
                error: function(xhr) { Swal.fire({ text: xhr.responseJSON, icon: xhr.status === 422 ? 'warning' : 'error', showClass: { popup: 'animate__animated animate__wobble' } }); }
            });
        }
    });
});
</script>
{{-- Scripts Invoices --}}
<script>
$(document).on("click", "#btn-modal-invoice", function() {
    var type = $(this).data('type');
    var customer_id = "{{ isset($data) ? $data->id : ''}}";
    $("#modalInvoice").modal('show');
    if(type == 'add-invoice'){
        $("#modalInvoiceLabel").html('Adicionar Fatura');
        var url = `{{ url("/admin/invoices/form?customer_id=") }}${customer_id}`;
    } else {
        $("#modalInvoiceLabel").html('Editar Fatura');
        var invoice = $(this).data('invoice');
        var url = `{{ url("/admin/invoices/form?customer_id=") }}${customer_id}&id=${invoice}`;
    }
    $.get(url, $(this).addClass('modal-scrollfix').find('#form-content-invoice').html('Carregando...'), function(data) {
        $("#form-content-invoice").html(data);
        $('.money').mask('000.000.000.000.000,00', {reverse: true});
        $('#customer_service_id').on('change', function() {
            var service_price = $(this).find(':selected').data('price');
            var service_description = $(this).find(':selected').data('description');
            $('#invoice_price').val(parseFloat(service_price).toLocaleString('pt-br', {minimumFractionDigits: 2}));
            $('#invoice_description').val(service_description);
        });
    });
});

$(document).on('click', '#btn-save-invoice', function(e) {
    e.preventDefault();
    $("#btn-save-invoice").attr("disabled", true).html('Aguarde...');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{csrf_token()}}" } });
    var formData = new FormData($('#form-request-invoice')[0]);
    var invoice = $('#invoice').val();
    var url = invoice != '' ? "{{ url('admin/invoices') }}/"+invoice : "{{ url('admin/invoices') }}";
    $.ajax({
        url: url,
        data: formData,
        method: 'POST',
        processData: false,
        contentType: false,
        success: function(data){
            $("#btn-save-invoice").attr("disabled", false).html('<i class="fa fa-check"></i> Salvar');
            Swal.fire({ width: 350, title: "<h5 style='color:#007bff'>" + data + "</h5>", icon: 'success', showConfirmButton: true, showClass: { popup: 'animate__animated animate__backInUp' }, allowOutsideClick: false }).then(() => {
                $('#modalInvoice').modal('hide');
                loadInvoices();
            });
        },
        error: function(xhr) {
            $("#btn-save-invoice").attr("disabled", false).html('<i class="fa fa-check"></i> Salvar');
            Swal.fire({ text: xhr.responseJSON, width: 300, icon: xhr.status === 422 ? 'warning' : 'error', confirmButtonColor: "#007bff", showClass: { popup: 'animate__animated animate__wobble' } });
        }
    });
});

function loadInvoices(){
    var customer_id = "{{ isset($data) ? $data->id : ''}}";
    $.ajax({
        url: "{{url('/admin/load-invoices')}}"+'/'+customer_id,
        method: 'GET',
        success: function(data){
            $('#load-invoices').html('');
            var html = '';
            $.each(data, function(i, item) {
                html += '<tr>';
                html += `<td>${item.id}</td>`;
                html += `<td>${item.description}</td>`;
                html += `<td>R$ ${parseFloat(item.price).toLocaleString('pt-br', {minimumFractionDigits: 2})}</td>`;
                html += `<td>${item.gateway_payment}</td>`;
                html += `<td>${item.payment_method}</td>`;
                html += `<td>${moment(item.date_invoice).format('DD/MM/YYYY')}</td>`;
                html += `<td>${moment(item.date_due).format('DD/MM/YYYY')}</td>`;
                html += `<td>${item.date_payment != null ? moment(item.date_payment).format('DD/MM/YYYY') : '-'}</td>`;
                html += `<td><label class="badge badge-${item.status == 'Pago' ? 'success' : item.status == 'Pendente' ? 'warning' : item.status == 'Estabelecimento' ? 'info' : 'danger'}">${item.status}</label></td>`;
                html += `<td>
                    ${item.status == 'Pendente' || item.status == 'Erro' || item.status == 'Estabelecimento' ? '<a href="#" data-original-title="Editar fatura" id="btn-modal-invoice" data-type="edit-invoice" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-secondary btn-xs"><i class="far fa-edit"></i></a> ' : ''}
                    ${item.status == 'Erro' ? '<a href="#" data-original-title="Erros" id="btn-modal-error" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-danger btn-xs"><i class="fas fa-exclamation-triangle"></i></a> ' : ''}
                    ${item.status != 'Erro' ? '<a href="#" data-original-title="Notificações" id="btn-modal-notifications" data-invoice="'+item.id+'" data-placement="left" data-tt="tooltip" class="btn btn-info btn-xs" style="background-color: #06b8f7; border-color: #06b8f7;"><i class="fa fa-info"></i></a> ' : ''}
                    ${item.status == 'Pendente' || item.status == 'Erro' || item.status == 'Estabelecimento' ? '<a href="#" data-original-title="Cancelar Fatura" id="btn-delete-invoice" data-invoice="'+item.id+'" data-tt="tooltip" class="btn btn-danger btn-xs"><i class="fas fa-undo-alt"></i></a> ' : ''}
                    ${item.status == 'Pendente' ? '<a href="'+`${item.payment_method == "Pix" ? item.image_url_pix : item.billet_url}`+'" target="_blank" data-original-title="Baixar Fatura" data-tt="tooltip" class="btn btn-primary btn-xs"><i class="fas fa-download"></i></a>' : ''}
                </td>`;
                html += '</tr>';
            });
            $('#load-invoices').append(html);
            $('[data-tt="tooltip"]').tooltip();
        },
        error: function(xhr) { Swal.fire({ text: xhr.responseJSON, icon: xhr.status === 422 ? 'warning' : 'error', showClass: { popup: 'animate__animated animate__wobble' } }); }
    });
}

$(document).on('click', '#btn-delete-invoice', function(e) {
    e.preventDefault();
    var invoice = $(this).data('invoice');
    Swal.fire({ title: 'Deseja cancelar esta fatura?', text: "Você não poderá reverter isso!", icon: 'question', showCancelButton: true, cancelButtonText: 'Cancelar', confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sim, cancelar!' }).then((result) => {
        if (result.value) {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{csrf_token()}}" } });
            $.ajax({
                url: "{{url('admin/invoices')}}/"+invoice,
                method: 'DELETE',
                success: function(){ loadInvoices(); },
                error: function(xhr) { Swal.fire({ text: xhr.responseJSON, icon: xhr.status === 422 ? 'warning' : 'error', showClass: { popup: 'animate__animated animate__wobble' } }); }
            });
        }
    });
});

$(document).on("click", "#btn-modal-notifications", function() {
    $("#modalNotifications").modal('show');
    $("#modalNotificationsLabel").html('Notificações');
    var invoice = $(this).data('invoice');
    var url = "{{url('/admin/load-invoice-notifications')}}"+'/'+invoice;
    $.get(url, $(this).addClass('modal-scrollfix').find('#form-content-notifications').html('Carregando...'), function(data) {
        $("#form-content-notifications").html(data);
        $('#btn-notificate').attr('onclick',`sendNotification(${invoice})`);
    });
});

$(document).on('click', '#btn-invoice-status', function(e) {
    var invoice_id = $(this).data('invoice');
    Swal.fire({ title: 'Deseja atualizar o status da fatura?', text: "Se o status do Gateway for diferente do sistema, o cliente será notificado!", icon: 'question', showCancelButton: true, cancelButtonText: 'Cancelar', confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Sim, atualizar!' }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "{{url('admin/invoices-check-status')}}"+'/'+invoice_id,
                method: 'GET',
                success: function(){ loadInvoices(); },
                error: function(xhr) { Swal.fire({ text: xhr.responseJSON, icon: xhr.status === 422 ? 'warning' : 'error', showClass: { popup: 'animate__animated animate__wobble' } }); }
            });
        }
    });
});
</script>
@endsection
