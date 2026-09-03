@extends('layouts.admin')

@section('content')
<style>
    .integration-card {
        background: #1A1A20;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid #e3e6f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        height: 100%;
    }
    .integration-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,123,255,0.2);
        border-color: #D4AF37;
    }
    .integration-card.configured {
        border-color: #28a745;
        background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
    }
    .integration-card.configured .integration-icon {
        color: #28a745;
    }
    .integration-icon {
        font-size: 3.5rem;
        margin-bottom: 15px;
        color: #D4AF37;
    }
    .integration-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2d3748;
    }
    .integration-status {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .integration-status.configured {
        color: #28a745;
        font-weight: 600;
    }
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .page-header h1 {
        margin: 0;
        font-weight: 600;
        font-size: 2rem;
    }
    .modal-header {
        background: linear-gradient(135deg, #D4AF37 0%, #B8960C 100%);
        color: white;
        border-radius: 10px 10px 0 0;
    }
    .modal-header .close {
        color: white;
        opacity: 1;
    }
    /* WhatsApp modal */
    .wa-status-banner {
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 2px solid transparent;
    }
    .wa-status-banner .wa-status-icon {
        font-size: 2.5rem;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .wa-status-banner .wa-status-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0 0 4px;
    }
    .wa-status-banner .wa-status-sub {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .wa-status-open { background: #d4edda; border-color: #28a745; color: #155724; }
    .wa-status-open .wa-status-icon { background: #28a745; color: #fff; }
    .wa-status-close { background: #f8d7da; border-color: #dc3545; color: #721c24; }
    .wa-status-close .wa-status-icon { background: #dc3545; color: #fff; }
    .wa-status-connecting { background: #d1ecf1; border-color: #17a2b8; color: #0c5460; }
    .wa-status-connecting .wa-status-icon { background: #17a2b8; color: #fff; }
    .wa-status-unknown { background: #fff3cd; border-color: #ffc107; color: #856404; }
    .wa-status-unknown .wa-status-icon { background: #ffc107; color: #fff; }
    .wa-info-card {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 10px;
        padding: 12px 14px;
        height: 100%;
    }
    .wa-info-card label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
        margin-bottom: 4px;
        display: block;
    }
    .wa-info-card .wa-info-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2d3748;
        word-break: break-all;
    }
    .wa-section-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e3e6f0;
    }
    .wa-action-group {
        margin-bottom: 16px;
    }
    .wa-action-group .btn {
        margin: 0 6px 8px 0;
    }
    .wa-test-panel {
        background: linear-gradient(135deg, #f0fff4 0%, #e8f5e9 100%);
        border: 2px solid #28a745;
        border-radius: 12px;
        padding: 20px;
        margin-top: 8px;
    }
    .wa-test-result {
        margin-top: 12px;
        display: none;
    }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="page-header">
                <h1><i class="fas fa-plug"></i> Integrações - {{ $company->name }}</h1>
                <p class="mb-0 mt-2">Clique em cada card para configurar as integrações</p>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Erro!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                {{-- Card Configurações de Fatura --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="integration-card {{ $company->day_generate_invoice ? 'configured' : '' }}" data-toggle="modal" data-target="#modalFatura">
                        <div class="integration-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="integration-title">Configurações de Fatura</div>
                        <div class="integration-status {{ $company->day_generate_invoice ? 'configured' : '' }}">
                            {{ $company->day_generate_invoice ? '✓ Configurado' : 'Não configurado' }}
                        </div>
                    </div>
                </div>

                {{-- Card PIX --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="integration-card {{ $company->chave_pix ? 'configured' : '' }}" data-toggle="modal" data-target="#modalPix">
                        <div class="integration-icon">
                            <i class="fab fa-pix"></i>
                        </div>
                        <div class="integration-title">PIX</div>
                        <div class="integration-status {{ $company->chave_pix ? 'configured' : '' }}">
                            {{ $company->chave_pix ? '✓ Configurado' : 'Não configurado' }}
                        </div>
                    </div>
                </div>

                {{-- Card PagHiper --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="integration-card {{ $company->token_paghiper ? 'configured' : '' }}" data-toggle="modal" data-target="#modalPagHiper">
                        <div class="integration-icon">
                            <i class="fas fa-barcode"></i>
                        </div>
                        <div class="integration-title">PagHiper</div>
                        <div class="integration-status {{ $company->token_paghiper ? 'configured' : '' }}">
                            {{ $company->token_paghiper ? '✓ Configurado' : 'Não configurado' }}
                        </div>
                    </div>
                </div>

                {{-- Card Mercado Pago --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="integration-card {{ $company->access_token_mp ? 'configured' : '' }}" data-toggle="modal" data-target="#modalMercadoPago">
                        <div class="integration-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="integration-title">Mercado Pago</div>
                        <div class="integration-status {{ $company->access_token_mp ? 'configured' : '' }}">
                            {{ $company->access_token_mp ? '✓ Configurado' : 'Não configurado' }}
                        </div>
                    </div>
                </div>

                {{-- Card Banco Inter --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="integration-card {{ $company->inter_client_id ? 'configured' : '' }}" data-toggle="modal" data-target="#modalBancoInter">
                        <div class="integration-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="integration-title">Banco Inter</div>
                        <div class="integration-status {{ $company->inter_client_id ? 'configured' : '' }}">
                            {{ $company->inter_client_id ? '✓ Configurado' : 'Não configurado' }}
                        </div>
                        @if($certInfo['exists'])
                            <div class="mt-2" style="font-size: 0.75rem; line-height: 1.3;">
                                @if($certInfo['expired'])
                                    <div class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Certificado EXPIRADO<br>
                                        <small>{{ $certInfo['expires_at_formatted'] }}</small>
                                    </div>
                                @elseif($certInfo['expires_soon'])
                                    <div class="text-warning">
                                        <i class="fas fa-clock"></i> Expira em {{ $certInfo['days_until_expiration'] }} dias<br>
                                        <small>{{ $certInfo['expires_at_formatted'] }}</small>
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <i class="fas fa-certificate"></i> Válido até<br>
                                        <small>{{ $certInfo['expires_at_formatted'] }}</small>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card Asaas --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="integration-card {{ $company->at_asaas_prod || $company->at_asaas_test ? 'configured' : '' }}" data-toggle="modal" data-target="#modalAsaas">
                        <div class="integration-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="integration-title">Asaas</div>
                        <div class="integration-status {{ $company->at_asaas_prod || $company->at_asaas_test ? 'configured' : '' }}">
                            {{ $company->at_asaas_prod || $company->at_asaas_test ? '✓ Configurado' : 'Não configurado' }}
                        </div>
                    </div>
                </div>

                {{-- Card WhatsApp --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    @php
                        $whatsappProvider = $company->whatsapp_provider ?: config('services.integreai.whatsapp_provider', 'evogo');
                    @endphp
                    <div class="integration-card {{ $company->api_status_whatsapp === 'open' ? 'configured' : '' }}" data-toggle="modal" data-target="#modalWhatsApp">
                        <div class="integration-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="integration-title">WhatsApp</div>
                        <div class="integration-status {{ $company->api_status_whatsapp === 'open' ? 'configured' : '' }}">
                            @if($company->api_status_whatsapp === 'open')
                                ✓ Conectado ({{ strtoupper($whatsappProvider) }})
                            @elseif($company->api_session_whatsapp)
                                ⚠ Configurado ({{ strtoupper($whatsappProvider) }})
                            @else
                                Não configurado
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Typebot --}}
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="integration-card {{ $company->typebot_id ? 'configured' : '' }}" data-toggle="modal" data-target="#modalTypebot">
                        <div class="integration-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="integration-title">Typebot</div>
                        <div class="integration-status {{ $company->typebot_id ? 'configured' : '' }}">
                            {{ $company->typebot_id ? '✓ Configurado' : 'Não configurado' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Modal Configurações de Fatura --}}
<div class="modal fade" id="modalFatura" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Configurações de Fatura</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="day_generate_invoice"><i class="fas fa-calendar-day text-primary"></i> Dia de geração de faturas</label>
                                <select class="form-control" id="day_generate_invoice" name="day_generate_invoice">
                                    <option value="">Selecione o dia...</option>
                                    @for($d = 1; $d <= 31; $d++)
                                        <option value="{{ $d }}" {{ old('day_generate_invoice', $company->day_generate_invoice) == $d ? 'selected' : '' }}>Dia {{ $d }}</option>
                                    @endfor
                                </select>
                                <small class="text-muted">Dia do mês para gerar faturas automaticamente</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="send_generate_invoice"><i class="fas fa-paper-plane text-primary"></i> Enviar fatura ao gerar?</label>
                                <select class="form-control" id="send_generate_invoice" name="send_generate_invoice">
                                    <option value="Não" {{ old('send_generate_invoice', $company->send_generate_invoice ?? 'Não') == 'Não' ? 'selected' : '' }}>Não</option>
                                    <option value="Sim" {{ old('send_generate_invoice', $company->send_generate_invoice) == 'Sim' ? 'selected' : '' }}>Sim</option>
                                </select>
                                <small class="text-muted">Enviar automaticamente por email/WhatsApp após geração</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> As faturas serão geradas automaticamente no dia configurado através do cron job.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal PIX --}}
<div class="modal fade" id="modalPix" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fab fa-pix"></i> PIX</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="chave_pix"><i class="fas fa-key text-primary"></i> Chave PIX</label>
                        <input type="text" class="form-control" id="chave_pix" name="chave_pix" value="{{ old('chave_pix', $company->chave_pix ?? '') }}" placeholder="Digite sua chave PIX">
                        <small class="text-muted">CPF, CNPJ, email, telefone ou chave aleatória</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal PagHiper --}}
<div class="modal fade" id="modalPagHiper" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-barcode"></i> PagHiper</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="token_paghiper"><i class="fas fa-key text-primary"></i> Token PagHiper</label>
                                <input type="text" class="form-control" id="token_paghiper" name="token_paghiper" value="{{ old('token_paghiper', $company->token_paghiper ?? '') }}" placeholder="Token de acesso">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="key_paghiper"><i class="fas fa-key text-primary"></i> Key PagHiper</label>
                                <input type="text" class="form-control" id="key_paghiper" name="key_paghiper" value="{{ old('key_paghiper', $company->key_paghiper ?? '') }}" placeholder="Chave de acesso">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Mercado Pago --}}
<div class="modal fade" id="modalMercadoPago" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-shopping-cart"></i> Mercado Pago</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="access_token_mp"><i class="fas fa-key text-primary"></i> Access Token Mercado Pago</label>
                        <input type="text" class="form-control" id="access_token_mp" name="access_token_mp" value="{{ old('access_token_mp', $company->access_token_mp ?? '') }}" placeholder="Token de acesso da API">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Banco Inter --}}
<div class="modal fade" id="modalBancoInter" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-university"></i> Banco Inter</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {{-- Avisos sobre o certificado --}}
                    @if($certInfo['exists'])
                        @if($certInfo['expired'])
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Certificado EXPIRADO!</strong><br>
                                O certificado Inter expirou em <strong>{{ $certInfo['expires_at_formatted'] }}</strong>. 
                                Faça upload de um novo certificado para continuar usando a integração.
                            </div>
                        @elseif($certInfo['expires_soon'])
                            <div class="alert alert-warning">
                                <i class="fas fa-clock"></i> 
                                <strong>Atenção: Certificado expira em breve!</strong><br>
                                O certificado Inter irá expirar em <strong>{{ $certInfo['days_until_expiration'] }} dias</strong> ({{ $certInfo['expires_at_formatted'] }}). 
                                Renove o certificado para evitar interrupções no serviço.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-certificate"></i> 
                                <strong>Certificado válido até {{ $certInfo['expires_at_formatted'] }}</strong><br>
                                <small class="text-muted">Faltam {{ $certInfo['days_until_expiration'] }} dias para expiração</small>
                            </div>
                        @endif
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_host"><i class="fas fa-server text-primary"></i> Host Inter</label>
                                <input type="text" class="form-control" id="inter_host" name="inter_host" value="{{ old('inter_host', $company->inter_host ?? 'https://cdpj.partners.bancointer.com.br/') }}" placeholder="URL da API">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_client_id"><i class="fas fa-id-card text-primary"></i> Client ID</label>
                                <input type="text" class="form-control" id="inter_client_id" name="inter_client_id" value="{{ old('inter_client_id', $company->inter_client_id ?? '') }}" placeholder="Client ID">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_client_secret"><i class="fas fa-lock text-primary"></i> Client Secret</label>
                                <input type="password" class="form-control" id="inter_client_secret" name="inter_client_secret" value="{{ old('inter_client_secret', $company->inter_client_secret ?? '') }}" placeholder="Client Secret">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_chave_pix"><i class="fab fa-pix text-primary"></i> Chave PIX Inter</label>
                                <input type="text" class="form-control" id="inter_chave_pix" name="inter_chave_pix" value="{{ old('inter_chave_pix', $company->inter_chave_pix ?? '') }}" placeholder="Chave PIX do Banco Inter">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inter_scope"><i class="fas fa-tasks text-primary"></i> Scope</label>
                                <textarea class="form-control" id="inter_scope" name="inter_scope" rows="2" placeholder="Escopos de permissão">{{ old('inter_scope', $company->inter_scope ?? 'boleto-cobranca.read boleto-cobranca.write extrato.read cob.write cob.read cobv.write cobv.read pix.write pix.read webhook.read webhook.write') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_webhook_url_billet"><i class="fas fa-link text-primary"></i> Webhook URL Boleto</label>
                                <input type="text" class="form-control" id="inter_webhook_url_billet" name="inter_webhook_url_billet" value="{{ old('inter_webhook_url_billet', $company->inter_webhook_url_billet ?? '') }}" placeholder="URL do webhook para boletos">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_webhook_url_pix"><i class="fas fa-link text-primary"></i> Webhook URL PIX</label>
                                <input type="text" class="form-control" id="inter_webhook_url_pix" name="inter_webhook_url_pix" value="{{ old('inter_webhook_url_pix', $company->inter_webhook_url_pix ?? '') }}" placeholder="URL do webhook para PIX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_crt_file"><i class="fas fa-certificate text-primary"></i> Arquivo CRT</label>
                                <input type="file" class="form-control-file" id="inter_crt_file" name="inter_crt_file">
                                @if($company->inter_crt_file ?? null)
                                    <small class="form-text text-success"><i class="fas fa-check-circle"></i> Arquivo: {{ basename($company->inter_crt_file) }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inter_key_file"><i class="fas fa-key text-primary"></i> Arquivo KEY</label>
                                <input type="file" class="form-control-file" id="inter_key_file" name="inter_key_file">
                                @if($company->inter_key_file ?? null)
                                    <small class="form-text text-success"><i class="fas fa-check-circle"></i> Arquivo: {{ basename($company->inter_key_file) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Asaas --}}
<div class="modal fade" id="modalAsaas" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-credit-card"></i> Asaas</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="environment_asaas"><i class="fas fa-cog text-primary"></i> Ambiente</label>
                                <select class="form-control" id="environment_asaas" name="environment_asaas">
                                    <option value="Teste" {{ old('environment_asaas', $company->environment_asaas ?? 'Teste') == 'Teste' ? 'selected' : '' }}>Teste (Sandbox)</option>
                                    <option value="Produção" {{ old('environment_asaas', $company->environment_asaas) == 'Produção' ? 'selected' : '' }}>Produção</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="at_asaas_prod"><i class="fas fa-key text-primary"></i> Token Produção</label>
                                <input type="text" class="form-control" id="at_asaas_prod" name="at_asaas_prod" value="{{ old('at_asaas_prod', $company->at_asaas_prod ?? '') }}" placeholder="Token da API de produção">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="at_asaas_test"><i class="fas fa-key text-primary"></i> Token Teste</label>
                                <input type="text" class="form-control" id="at_asaas_test" name="at_asaas_test" value="{{ old('at_asaas_test', $company->at_asaas_test ?? '') }}" placeholder="Token da API de teste">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal WhatsApp --}}
<div class="modal fade" id="modalWhatsApp" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fab fa-whatsapp"></i> WhatsApp — IntegreAI</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @php
                        $whatsappProvider = old('whatsapp_provider', $company->whatsapp_provider ?: config('services.integreai.whatsapp_provider', 'evogo'));
                        $whatsappLocal = brazilWhatsappLocalPart(old('whatsapp', $company->whatsapp));
                        $whatsappFull = normalizeBrazilWhatsapp(old('whatsapp', $company->whatsapp)) ?? '';
                        $waStatus = $company->api_status_whatsapp ?: 'unknown';
                        $waStatusClass = match ($waStatus) {
                            'open' => 'wa-status-open',
                            'connecting' => 'wa-status-connecting',
                            'close', 'disconnected' => 'wa-status-close',
                            default => 'wa-status-unknown',
                        };
                        $waStatusLabel = match ($waStatus) {
                            'open' => 'Conectado e pronto para enviar',
                            'connecting' => 'Aguardando pareamento',
                            'close', 'disconnected' => 'Desconectado',
                            default => 'Não configurado',
                        };
                        $testWhatsappLocal = brazilWhatsappLocalPart(Auth::user()->whatsapp ?? '');
                    @endphp

                    <div id="wa-status-banner" class="wa-status-banner {{ $waStatusClass }}">
                        <div class="wa-status-icon"><i id="wa-status-icon" class="fas fa-{{ $waStatus === 'open' ? 'check' : ($waStatus === 'connecting' ? 'sync' : 'exclamation') }}"></i></div>
                        <div>
                            <p class="wa-status-title" id="wa-status-title">{{ $waStatusLabel }}</p>
                            <p class="wa-status-sub" id="wa-status-sub">
                                Provedor: <strong>{{ strtoupper($whatsappProvider) }}</strong>
                                @if($company->api_session_whatsapp)
                                    · Sessão: <strong>{{ $company->api_session_whatsapp }}</strong>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-light border mb-4 py-2">
                        <small class="text-muted">
                            <i class="fas fa-server"></i> API: <code>{{ config('services.integreai.url', 'Não configurado') }}</code>
                            · Chave M2M no <code>.env</code> (<code>INTEGREAI_API_KEY</code>)
                        </small>
                    </div>

                    <div class="wa-section-title"><i class="fas fa-cog"></i> Configuração</div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="whatsapp_provider"><i class="fas fa-server text-primary"></i> Provedor</label>
                                <select class="form-control" id="whatsapp_provider" name="whatsapp_provider">
                                    <option value="evogo" {{ $whatsappProvider === 'evogo' ? 'selected' : '' }}>EVOGO — QR Code / sessão</option>
                                    <option value="ycloud" {{ $whatsappProvider === 'ycloud' ? 'selected' : '' }}>YCloud — WhatsApp Oficial</option>
                                </select>
                                <small class="text-muted" id="whatsapp-provider-help"></small>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group mb-0">
                                <label for="whatsapp_local"><i class="fab fa-whatsapp text-success"></i> Número da empresa</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white font-weight-bold">+55</span>
                                    </div>
                                    <input type="tel"
                                           class="form-control @error('whatsapp') is-invalid @enderror"
                                           id="whatsapp_local"
                                           value="{{ old('whatsapp_local', $whatsappLocal) }}"
                                           placeholder="22988280129"
                                           maxlength="11"
                                           inputmode="numeric"
                                           autocomplete="tel-national">
                                </div>
                                <input type="hidden" name="whatsapp" id="whatsapp_full" value="{{ old('whatsapp', $whatsappFull) }}">
                                <input type="hidden" name="integreai_instance_id" id="integreai_instance_id" value="{{ old('integreai_instance_id', $company->integreai_instance_id) }}">
                                <input type="hidden" name="api_session_whatsapp" id="api_session_whatsapp" value="{{ old('api_session_whatsapp', $company->api_session_whatsapp) }}">
                                <input type="hidden" name="api_status_whatsapp" id="api_status_whatsapp" value="{{ $company->api_status_whatsapp ?? '' }}">
                                <small class="text-muted">DDD + número. Ao alterar, a instância será revalidada no CRM.</small>
                                @error('whatsapp')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <div id="whatsapp-lookup-status" class="mt-2" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 mb-4">
                        <div class="col-md-4">
                            <div class="wa-info-card">
                                <label><i class="fas fa-plug"></i> Sessão CRM</label>
                                <div class="wa-info-value" id="api_session_whatsapp_display">{{ $company->api_session_whatsapp ?: '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wa-info-card">
                                <label><i class="fas fa-fingerprint"></i> Tenant M2M</label>
                                <div class="wa-info-value" id="integreai_external_tenant_id">cobranca:empresa:{{ $company->id }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wa-info-card">
                                <label><i class="fas fa-key"></i> Token instância</label>
                                <div class="wa-info-value" id="api_token_whatsapp_display">{{ $company->api_token_whatsapp ? '••••' . substr($company->api_token_whatsapp, -8) : 'Sincroniza ao conectar' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 px-0" id="instances-picker" style="display: none;">
                        <div class="form-group">
                            <label for="integreai_instance_select"><i class="fas fa-list text-primary"></i> Instância do CRM</label>
                            <select class="form-control" id="integreai_instance_select"></select>
                            <small class="text-muted" id="instances-help">Selecione a instância e clique em Conectar.</small>
                        </div>
                    </div>

                    <div class="wa-section-title mt-2"><i class="fas fa-bolt"></i> Ações</div>

                    <div class="wa-action-group" id="wa-group-connect">
                        <small class="text-muted d-block mb-2">Conexão</small>
                        <button type="button" class="btn btn-primary" id="btnConnect">
                            <i class="fas fa-link"></i> Conectar
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnCreateNew">
                            <i class="fas fa-plus"></i> Criar nova instância
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnCheckStatus">
                            <i class="fas fa-sync-alt"></i> Atualizar status
                        </button>
                    </div>

                    <div class="wa-action-group" id="wa-group-pairing" style="display: none;">
                        <small class="text-muted d-block mb-2">Pareamento (EVOGO)</small>
                        <button type="button" class="btn btn-info" id="btnGetQrCode">
                            <i class="fas fa-qrcode"></i> Obter QR Code
                        </button>
                    </div>

                    <div class="wa-action-group" id="wa-group-danger" style="display: none;">
                        <small class="text-muted d-block mb-2">Manutenção</small>
                        <button type="button" class="btn btn-outline-danger" id="btnDisconnect">
                            <i class="fas fa-unlink"></i> Desvincular instância
                        </button>
                    </div>

                    <div id="wa-test-panel" class="wa-test-panel" style="display: none;">
                        <div class="wa-section-title border-0 mb-3 pb-0"><i class="fas fa-paper-plane text-success"></i> Testar envio</div>
                        <p class="text-muted small mb-3">Envie uma mensagem de teste para validar a integração antes de notificar faturas.</p>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="test_whatsapp_local">Número de destino</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+55</span>
                                        </div>
                                        <input type="tel" class="form-control" id="test_whatsapp_local"
                                               value="{{ $testWhatsappLocal }}"
                                               placeholder="22999999999" maxlength="11" inputmode="numeric">
                                    </div>
                                    <small class="text-muted">Use seu celular ou um número de teste.</small>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="test_message">Mensagem <span class="text-muted">(opcional)</span></label>
                                    <textarea class="form-control" id="test_message" rows="2" maxlength="1000"
                                              placeholder="Deixe em branco para usar a mensagem padrão de teste."></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-lg" id="btnTestSend">
                            <i class="fas fa-paper-plane"></i> Enviar mensagem de teste
                        </button>
                        <div id="wa-test-result" class="wa-test-result alert"></div>
                    </div>

                    <div id="qrcodeContainer" class="row mt-3" style="display: none;">
                        <div class="col-md-12 text-center">
                            <div class="alert alert-info mb-2">
                                <i class="fas fa-mobile-alt"></i> Escaneie o QR Code com o WhatsApp do aparelho
                            </div>
                            <img id="qrcodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 280px; border: 3px solid #D4AF37; border-radius: 10px; padding: 10px; background: #1A1A20;">
                        </div>
                    </div>

                    <div id="statusMessage" class="alert mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <small class="text-muted"><i class="fas fa-info-circle"></i> Salve para gravar provedor e número da empresa.</small>
                    <div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companyId = {{ $company->id }};
    const csrfToken = @json(csrf_token());
    const initialHasSession = @json((bool) $company->api_session_whatsapp);
    const providerSelect = document.getElementById('whatsapp_provider');
    const providerHelp = document.getElementById('whatsapp-provider-help');
    const btnGetQrCode = document.getElementById('btnGetQrCode');
    const whatsappLocal = document.getElementById('whatsapp_local');
    const whatsappFull = document.getElementById('whatsapp_full');
    const instanceIdInput = document.getElementById('integreai_instance_id');
    const instanceSelect = document.getElementById('integreai_instance_select');
    const instancesPicker = document.getElementById('instances-picker');
    const instancesHelp = document.getElementById('instances-help');
    const lookupStatus = document.getElementById('whatsapp-lookup-status');
    const sessionInput = document.getElementById('api_session_whatsapp');
    const sessionDisplay = document.getElementById('api_session_whatsapp_display');
    const testWhatsappLocal = document.getElementById('test_whatsapp_local');
    let lookupTimer = null;
    let lookupInFlight = false;
    let hasLinkedSession = initialHasSession;

    function updateProviderUi() {
        const provider = providerSelect ? providerSelect.value : 'evogo';
        const isEvogo = provider === 'evogo';

        if (providerHelp) {
            providerHelp.textContent = isEvogo
                ? 'Conexão por QR Code via EVOGO. Use "Obter QR Code" após conectar.'
                : 'Instância oficial via YCloud. Configure no painel IntegreAI (BYOC).';
        }

        updateActionsForStatus(document.getElementById('api_status_whatsapp')?.value || 'unknown');
    }

    function getLocalWhatsappDigits() {
        return (whatsappLocal?.value || '').replace(/\D/g, '').slice(0, 11);
    }

    function getFullWhatsapp() {
        const local = getLocalWhatsappDigits();
        return local ? '55' + local : '';
    }

    function getTestFullWhatsapp() {
        const local = (testWhatsappLocal?.value || '').replace(/\D/g, '').slice(0, 11);
        return local ? '55' + local : '';
    }

    function isValidLocalWhatsapp(local) {
        return /^[1-9][0-9][0-9]{8,9}$/.test(local);
    }

    function syncWhatsappHidden() {
        if (whatsappLocal) {
            whatsappLocal.value = getLocalWhatsappDigits();
        }
        if (whatsappFull) {
            whatsappFull.value = getFullWhatsapp();
        }
    }

    function apiHeaders() {
        return {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
    }

    function translateStatus(status) {
        const statusMap = {
            'open': 'Conectado e pronto para enviar',
            'close': 'Desconectado',
            'connecting': 'Aguardando pareamento',
            'disconnected': 'Desconectado'
        };
        return statusMap[status] || 'Não configurado';
    }

    function updateStatusBanner(status, sessionName) {
        const banner = document.getElementById('wa-status-banner');
        const title = document.getElementById('wa-status-title');
        const sub = document.getElementById('wa-status-sub');
        const icon = document.getElementById('wa-status-icon');
        const provider = (providerSelect?.value || 'evogo').toUpperCase();

        const normalized = status === 'disconnected' ? 'close' : (status || 'unknown');
        const classes = {
            open: 'wa-status-open',
            close: 'wa-status-close',
            connecting: 'wa-status-connecting',
            unknown: 'wa-status-unknown'
        };
        const icons = {
            open: 'fa-check',
            close: 'fa-times',
            connecting: 'fa-sync fa-spin',
            unknown: 'fa-question'
        };

        if (banner) {
            banner.className = 'wa-status-banner ' + (classes[normalized] || classes.unknown);
        }
        if (title) {
            title.textContent = translateStatus(normalized);
        }
        if (icon) {
            icon.className = 'fas ' + (icons[normalized] || icons.unknown);
        }
        if (sub) {
            let html = 'Provedor: <strong>' + provider + '</strong>';
            if (sessionName) {
                html += ' · Sessão: <strong>' + sessionName + '</strong>';
            }
            sub.innerHTML = html;
        }

        if (document.getElementById('api_status_whatsapp')) {
            document.getElementById('api_status_whatsapp').value = normalized === 'unknown' ? '' : normalized;
        }

        updateActionsForStatus(normalized);
    }

    function updateActionsForStatus(status) {
        const normalized = status === 'disconnected' ? 'close' : status;
        const isEvogo = (providerSelect?.value || 'evogo') === 'evogo';
        const isOpen = normalized === 'open';
        const isConnecting = normalized === 'connecting';
        const isLinked = hasLinkedSession || !!instanceIdInput?.value || !!sessionInput?.value;

        const groupConnect = document.getElementById('wa-group-connect');
        const groupPairing = document.getElementById('wa-group-pairing');
        const groupDanger = document.getElementById('wa-group-danger');
        const testPanel = document.getElementById('wa-test-panel');
        const btnConnect = document.getElementById('btnConnect');
        const btnCreateNew = document.getElementById('btnCreateNew');

        if (groupConnect) {
            groupConnect.style.display = '';
        }
        if (btnConnect) {
            btnConnect.style.display = isOpen ? 'none' : '';
        }
        if (btnCreateNew) {
            btnCreateNew.style.display = isLinked && isOpen ? 'none' : '';
        }

        if (groupPairing) {
            groupPairing.style.display = (isEvogo && !isOpen) ? '' : 'none';
        }
        if (groupDanger) {
            groupDanger.style.display = isLinked ? '' : 'none';
        }
        if (testPanel) {
            testPanel.style.display = isOpen ? '' : 'none';
        }
    }

    function setSessionName(name) {
        const value = name || '';
        if (sessionInput) {
            sessionInput.value = value;
        }
        if (sessionDisplay) {
            sessionDisplay.textContent = value || '—';
        }
        hasLinkedSession = !!value;
    }

    function clearWhatsappBindingUi() {
        if (instanceIdInput) {
            instanceIdInput.value = '';
        }
        setSessionName('');
        hasLinkedSession = false;
        renderInstances([]);
        if (lookupStatus) {
            lookupStatus.style.display = 'none';
        }
        document.getElementById('qrcodeContainer').style.display = 'none';
        updateStatusBanner('close', '');
    }

    function renderInstances(instances, selectedId) {
        if (!instanceSelect || !instancesPicker) {
            return;
        }

        const list = instances || [];

        if (list.length === 0) {
            instancesPicker.style.display = 'none';
            instanceSelect.innerHTML = '';
            return;
        }

        instanceSelect.innerHTML = '';
        list.forEach(function (instance) {
            const option = document.createElement('option');
            option.value = instance.id || '';
            option.textContent = instance.label || instance.name || ('Instância #' + instance.id);
            instanceSelect.appendChild(option);
        });

        const preferredId = selectedId || instanceIdInput?.value || list[0]?.id;
        if (preferredId) {
            instanceSelect.value = String(preferredId);
            if (instanceIdInput) {
                instanceIdInput.value = String(preferredId);
            }
        }

        instancesPicker.style.display = '';
        if (instancesHelp) {
            instancesHelp.textContent = list.length === 1
                ? 'Instância localizada no CRM. Clique em Conectar para confirmar o vínculo.'
                : 'Selecione a instância encontrada no CRM e clique em Conectar.';
        }
    }

    instanceSelect?.addEventListener('change', function () {
        if (instanceIdInput) {
            instanceIdInput.value = this.value;
        }
    });

    function handleWhatsappApiResult(data) {
        const instances = data.instances || (data.instance ? [data.instance] : []);

        if (data.external_tenant_id) {
            const tenantEl = document.getElementById('integreai_external_tenant_id');
            if (tenantEl) {
                tenantEl.textContent = data.external_tenant_id;
            }
        }

        const sessionName = data.session_name || data.instance?.name || '';
        if (sessionName) {
            setSessionName(sessionName);
        }

        if (data.instance?.id && instanceIdInput) {
            instanceIdInput.value = data.instance.id;
            hasLinkedSession = true;
        }

        renderInstances(instances, data.instance?.id);

        if (data.success && (data.found || data.instance)) {
            const label = data.instance?.label || data.instance?.whatsapp_number_formatted || getFullWhatsapp();
            setLookupStatus('<i class="fas fa-check-circle"></i> <strong>Encontrado no CRM:</strong> ' + label, 'success');
        } else if (data.success && !data.found && instances.length === 0) {
            setLookupStatus('<i class="fas fa-exclamation-triangle"></i> Número não encontrado no CRM. Use "Criar nova instância" ou cadastre no painel IntegreAI.', 'warning');
        } else if (!data.success && instances.length > 0) {
            setLookupStatus('<i class="fas fa-info-circle"></i> ' + (data.message || 'Selecione a instância abaixo e clique em Conectar.'), 'warning');
        } else if (!data.success) {
            setLookupStatus('<i class="fas fa-times-circle"></i> ' + (data.message || 'Erro na verificação'), 'danger');
        }

        if (data.status) {
            updateStatusBanner(data.status, sessionName || sessionInput?.value);
        }
    }

    function setLookupStatus(html, type) {
        if (!lookupStatus) {
            return;
        }

        lookupStatus.className = 'mt-2 alert alert-' + (type || 'secondary');
        lookupStatus.innerHTML = html;
        lookupStatus.style.display = 'block';
    }

    function lookupWhatsapp(autoConnect) {
        syncWhatsappHidden();

        const local = getLocalWhatsappDigits();
        const full = getFullWhatsapp();

        if (!isValidLocalWhatsapp(local)) {
            if (local.length > 0) {
                setLookupStatus('<i class="fas fa-info-circle"></i> Digite DDD + número (10 ou 11 dígitos após +55).', 'light');
            } else if (lookupStatus) {
                lookupStatus.style.display = 'none';
            }
            return Promise.resolve();
        }

        if (lookupInFlight) {
            return Promise.resolve();
        }

        lookupInFlight = true;
        setLookupStatus('<i class="fas fa-spinner fa-spin"></i> Verificando no CRM IntegreAI...', 'info');

        return fetch(`/admin/companies/${companyId}/whatsapp/lookup`, {
            method: 'POST',
            headers: apiHeaders(),
            body: JSON.stringify({
                whatsapp: full,
                auto_connect: !!autoConnect
            })
        })
            .then(function (response) {
                return response.json()
                    .catch(function () {
                        return { success: false, message: 'Resposta inválida do servidor (HTTP ' + response.status + ').' };
                    })
                    .then(function (data) {
                        return { ok: response.ok, data: data };
                    });
            })
            .then(function ({ data }) {
                handleWhatsappApiResult(data);

                if (autoConnect && data.message) {
                    showMessage(data.message, data.success ? 'success' : 'danger');
                }

                return data;
            })
            .catch(function (error) {
                setLookupStatus('<i class="fas fa-times-circle"></i> ' + error.message, 'danger');
            })
            .finally(function () {
                lookupInFlight = false;
            });
    }

    function connectWhatsapp(createNew) {
        syncWhatsappHidden();

        const full = getFullWhatsapp();
        if (!isValidLocalWhatsapp(getLocalWhatsappDigits())) {
            showMessage('Informe um WhatsApp válido (+55, DDD e número com 8 ou 9 dígitos).', 'danger');
            return Promise.resolve({ success: false });
        }

        const instanceId = instanceIdInput ? instanceIdInput.value : '';

        return fetch(`/admin/companies/${companyId}/whatsapp/connect`, {
            method: 'POST',
            headers: apiHeaders(),
            body: JSON.stringify({
                whatsapp: full,
                integreai_instance_id: instanceId || null,
                create_new: !!createNew
            })
        }).then(response => response.json());
    }

    providerSelect?.addEventListener('change', updateProviderUi);
    updateProviderUi();
    syncWhatsappHidden();
    updateStatusBanner(
        document.getElementById('api_status_whatsapp')?.value || 'unknown',
        sessionInput?.value || ''
    );

    whatsappLocal?.addEventListener('input', function () {
        const previousFull = whatsappFull?.value || '';
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
        syncWhatsappHidden();

        const newFull = getFullWhatsapp();
        if (previousFull && newFull && previousFull !== newFull) {
            clearWhatsappBindingUi();
        }

        clearTimeout(lookupTimer);

        const local = getLocalWhatsappDigits();
        if (local.length === 10 || local.length === 11) {
            lookupTimer = setTimeout(function () {
                lookupWhatsapp(true);
            }, 600);
        } else if (local.length > 0) {
            setLookupStatus('<i class="fas fa-info-circle"></i> Continue digitando o número...', 'light');
        } else if (lookupStatus) {
            lookupStatus.style.display = 'none';
        }
    });

    whatsappLocal?.addEventListener('paste', function (event) {
        event.preventDefault();
        const pasted = (event.clipboardData || window.clipboardData).getData('text') || '';
        let digits = pasted.replace(/\D/g, '');

        if (digits.startsWith('55')) {
            digits = digits.slice(2);
        }

        this.value = digits.slice(0, 11);
        syncWhatsappHidden();
        this.dispatchEvent(new Event('input'));
    });

    testWhatsappLocal?.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
    });

    document.getElementById('btnConnect')?.addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Conectando...';

        connectWhatsapp(false)
            .then(data => {
                if (!data) {
                    return;
                }
                handleWhatsappApiResult(data);
                showMessage(data.message, data.success ? 'success' : 'danger');
            })
            .catch(error => {
                showMessage('Erro ao conectar: ' + error.message, 'danger');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-link"></i> Conectar';
            });
    });

    document.getElementById('btnCreateNew')?.addEventListener('click', function() {
        if (!confirm('Criar uma nova instância no provedor selecionado? Prefira reutilizar uma instância existente do CRM.')) {
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Criando...';

        connectWhatsapp(true)
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'danger');
                if (data.success) {
                    lookupWhatsapp(false);
                }
            })
            .catch(error => {
                showMessage('Erro ao criar instância: ' + error.message, 'danger');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus"></i> Criar nova instância';
            });
    });

    document.getElementById('btnCheckStatus')?.addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';

        fetch(`/admin/companies/${companyId}/whatsapp/status`)
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'danger');
                if (data.success && data.status) {
                    if (data.session_name) {
                        setSessionName(data.session_name);
                    }
                    if (data.external_tenant_id) {
                        const tenantEl = document.getElementById('integreai_external_tenant_id');
                        if (tenantEl) {
                            tenantEl.textContent = data.external_tenant_id;
                        }
                    }
                    updateStatusBanner(data.status, data.session_name || sessionInput?.value);
                }
            })
            .catch(error => {
                showMessage('Erro ao verificar status: ' + error.message, 'danger');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> Atualizar status';
            });
    });

    document.getElementById('btnGetQrCode')?.addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Obtendo...';

        fetch(`/admin/companies/${companyId}/whatsapp/qrcode`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.qrcode) {
                    document.getElementById('qrcodeImage').src = data.qrcode;
                    document.getElementById('qrcodeContainer').style.display = 'block';
                    showMessage(data.message, 'success');
                } else {
                    showMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                showMessage('Erro ao obter QR Code: ' + error.message, 'danger');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-qrcode"></i> Obter QR Code';
            });
    });

    document.getElementById('btnDisconnect')?.addEventListener('click', function() {
        if (!confirm('Desvincular a instância WhatsApp atual desta empresa?\n\nVocê poderá informar outro número e conectar uma nova instância do CRM.')) {
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Desvinculando...';

        fetch(`/admin/companies/${companyId}/whatsapp/disconnect`, {
            method: 'POST',
            headers: apiHeaders(),
            body: JSON.stringify({ force_local: false })
        })
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'danger');
                if (data.success) {
                    clearWhatsappBindingUi();
                }
            })
            .catch(error => {
                showMessage('Erro ao desvincular: ' + error.message, 'danger');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-unlink"></i> Desvincular instância';
            });
    });

    document.getElementById('btnTestSend')?.addEventListener('click', function() {
        const btn = this;
        const resultEl = document.getElementById('wa-test-result');
        const local = (testWhatsappLocal?.value || '').replace(/\D/g, '').slice(0, 11);
        const full = local ? '55' + local : '';
        const text = (document.getElementById('test_message')?.value || '').trim();

        if (!isValidLocalWhatsapp(local)) {
            if (resultEl) {
                resultEl.className = 'wa-test-result alert alert-danger';
                resultEl.innerHTML = '<i class="fas fa-times-circle"></i> Informe um número de destino válido (+55, DDD e 8 ou 9 dígitos).';
                resultEl.style.display = 'block';
            }
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        if (resultEl) {
            resultEl.style.display = 'none';
        }

        fetch(`/admin/companies/${companyId}/whatsapp/test`, {
            method: 'POST',
            headers: apiHeaders(),
            body: JSON.stringify({ number: full, text: text || null })
        })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ data }) => {
                if (resultEl) {
                    resultEl.className = 'wa-test-result alert alert-' + (data.success ? 'success' : 'danger');
                    resultEl.innerHTML = '<i class="fas fa-' + (data.success ? 'check' : 'times') + '-circle"></i> ' + (data.message || (data.success ? 'Enviado!' : 'Falha no envio.'));
                    resultEl.style.display = 'block';
                }
                showMessage(data.message, data.success ? 'success' : 'danger');
            })
            .catch(error => {
                if (resultEl) {
                    resultEl.className = 'wa-test-result alert alert-danger';
                    resultEl.innerHTML = '<i class="fas fa-times-circle"></i> ' + error.message;
                    resultEl.style.display = 'block';
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar mensagem de teste';
            });
    });

    function showMessage(message, type) {
        const statusMessage = document.getElementById('statusMessage');
        statusMessage.className = 'alert alert-' + type + ' mt-3';
        statusMessage.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check' : 'exclamation') + '-circle"></i> ' + message;
        statusMessage.style.display = 'block';

        setTimeout(() => {
            statusMessage.style.display = 'none';
        }, 6000);
    }
});
</script>

{{-- Modal Typebot --}}
<div class="modal fade" id="modalTypebot" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-robot"></i> Typebot</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="typebot_id"><i class="fas fa-robot text-primary"></i> Typebot ID</label>
                                <input type="text" class="form-control" id="typebot_id" name="typebot_id" value="{{ old('typebot_id', $company->typebot_id ?? '') }}" placeholder="ID do Typebot">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="typebot_enable"><i class="fas fa-toggle-on text-primary"></i> Habilitado</label>
                                <select class="form-control" id="typebot_enable" name="typebot_enable">
                                    <option value="n" {{ old('typebot_enable', $company->typebot_enable ?? 'n') == 'n' ? 'selected' : '' }}>Não</option>
                                    <option value="s" {{ old('typebot_enable', $company->typebot_enable) == 's' ? 'selected' : '' }}>Sim</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
