@extends('layouts.admin')

@section('content')
<style>
    .integration-card {
        background: white;
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
        border-color: #007bff;
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
        color: #007bff;
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
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border-radius: 10px 10px 0 0;
    }
    .modal-header .close {
        color: white;
        opacity: 1;
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
                    <div class="integration-card {{ $company->api_status_whatsapp === 'open' ? 'configured' : '' }}" data-toggle="modal" data-target="#modalWhatsApp">
                        <div class="integration-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="integration-title">WhatsApp</div>
                        <div class="integration-status {{ $company->api_status_whatsapp === 'open' ? 'configured' : '' }}">
                            @if($company->api_status_whatsapp === 'open')
                                ✓ Conectado
                            @elseif($company->api_session_whatsapp)
                                ⚠ Configurado
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
                    <h5 class="modal-title"><i class="fab fa-whatsapp"></i> WhatsApp</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>API URL Evolution:</strong> {{ env('API_URL_EVOLUTION', 'Não configurado no .env') }}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="api_session_whatsapp"><i class="fas fa-mobile-alt text-primary"></i> Nome da Instância</label>
                                <input type="text" class="form-control" id="api_session_whatsapp" name="api_session_whatsapp" value="{{ old('api_session_whatsapp', $company->api_session_whatsapp ?? '') }}" placeholder="Nome da instância">
                                <small class="text-muted">Nome da instância criada na Evolution API</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="api_token_whatsapp"><i class="fas fa-key text-primary"></i> API Key</label>
                                <input type="text" class="form-control" id="api_token_whatsapp" name="api_token_whatsapp" value="{{ old('api_token_whatsapp', $company->api_token_whatsapp ?? '') }}" placeholder="API Key da instância">
                                <small class="text-muted">Chave de acesso da instância</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="api_status_whatsapp"><i class="fas fa-signal text-primary"></i> Status da Conexão</label>
                                <input type="text" class="form-control form-control-lg text-center font-weight-bold" id="api_status_whatsapp_display" value="{{ $company->api_status_whatsapp === 'open' ? 'Conectado' : ($company->api_status_whatsapp === 'close' ? 'Desconectado' : ($company->api_status_whatsapp === 'connecting' ? 'Conectando...' : 'Desconhecido')) }}" readonly style="background-color: {{ $company->api_status_whatsapp === 'open' ? '#d4edda' : ($company->api_status_whatsapp === 'close' ? '#f8d7da' : '#fff3cd') }}; color: {{ $company->api_status_whatsapp === 'open' ? '#155724' : ($company->api_status_whatsapp === 'close' ? '#721c24' : '#856404') }}; border-color: {{ $company->api_status_whatsapp === 'open' ? '#c3e6cb' : ($company->api_status_whatsapp === 'close' ? '#f5c6cb' : '#ffeeba') }};">
                                <input type="hidden" name="api_status_whatsapp" id="api_status_whatsapp" value="{{ $company->api_status_whatsapp ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <hr>
                            <h6 class="text-muted mb-3"><i class="fas fa-cogs"></i> Ações de Conexão</h6>
                            <div class="btn-group btn-group-lg w-100" role="group">
                                <button type="button" class="btn btn-info" id="btnCheckStatus">
                                    <i class="fas fa-sync-alt"></i> Verificar Status
                                </button>
                                <button type="button" class="btn btn-success" id="btnGetQrCode">
                                    <i class="fas fa-qrcode"></i> Obter QR Code
                                </button>
                                <button type="button" class="btn btn-danger" id="btnDisconnect">
                                    <i class="fas fa-unlink"></i> Desconectar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="qrcodeContainer" class="row" style="display: none;">
                        <div class="col-md-12 text-center">
                            <div class="alert alert-info">
                                <i class="fas fa-mobile-alt"></i> <strong>Escaneie o QR Code com seu WhatsApp</strong>
                            </div>
                            <img id="qrcodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 300px; border: 3px solid #007bff; border-radius: 10px; padding: 10px; background: white;">
                        </div>
                    </div>

                    <div id="statusMessage" class="alert" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Configurações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companyId = {{ $company->id }};
    
    // Função para traduzir status
    function translateStatus(status) {
        const statusMap = {
            'open': 'Conectado',
            'close': 'Desconectado',
            'connecting': 'Conectando...',
            'disconnected': 'Desconectado'
        };
        return statusMap[status] || status || 'Desconhecido';
    }
    
    // Função para colorir o campo de status
    function updateStatusDisplay(status) {
        const displayInput = document.getElementById('api_status_whatsapp_display');
        displayInput.value = translateStatus(status);
        
        // Definir cores baseado no status
        let bgColor, textColor, borderColor;
        if (status === 'open') {
            bgColor = '#d4edda';
            textColor = '#155724';
            borderColor = '#c3e6cb';
        } else if (status === 'close' || status === 'disconnected') {
            bgColor = '#f8d7da';
            textColor = '#721c24';
            borderColor = '#f5c6cb';
        } else if (status === 'connecting') {
            bgColor = '#d1ecf1';
            textColor = '#0c5460';
            borderColor = '#bee5eb';
        } else {
            bgColor = '#fff3cd';
            textColor = '#856404';
            borderColor = '#ffeeba';
        }
        
        displayInput.style.backgroundColor = bgColor;
        displayInput.style.color = textColor;
        displayInput.style.borderColor = borderColor;
    }
    
    // Verificar Status
    document.getElementById('btnCheckStatus')?.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
        
        fetch(`/admin/companies/${companyId}/whatsapp/status`)
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'danger');
                if (data.success && data.status) {
                    updateStatusDisplay(data.status);
                    document.getElementById('api_status_whatsapp').value = data.status;
                }
            })
            .catch(error => {
                showMessage('Erro ao verificar status: ' + error.message, 'danger');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt"></i> Verificar Status';
            });
    });
    
    // Obter QR Code
    document.getElementById('btnGetQrCode')?.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Obtendo...';
        
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
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-qrcode"></i> Obter QR Code';
            });
    });
    
    // Desconectar
    document.getElementById('btnDisconnect')?.addEventListener('click', function() {
        if (!confirm('Tem certeza que deseja desconectar o WhatsApp?')) {
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Desconectando...';
        
        fetch(`/admin/companies/${companyId}/whatsapp/disconnect`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                showMessage(data.message, data.success ? 'success' : 'danger');
                if (data.success) {
                    updateStatusDisplay('close');
                    document.getElementById('api_status_whatsapp').value = 'close';
                    document.getElementById('qrcodeContainer').style.display = 'none';
                }
            })
            .catch(error => {
                showMessage('Erro ao desconectar: ' + error.message, 'danger');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-unlink"></i> Desconectar';
            });
    });
    
    function showMessage(message, type) {
        const statusMessage = document.getElementById('statusMessage');
        statusMessage.className = `alert alert-${type}`;
        statusMessage.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i> ${message}`;
        statusMessage.style.display = 'block';
        
        setTimeout(() => {
            statusMessage.style.display = 'none';
        }, 5000);
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
