@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Integrações - {{ $company->name }}</h1>
    </section>
    <section class="content">
        <div class="card">
            <form method="POST" action="{{ route('companies.integrations.update', $company) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <ul class="nav nav-tabs" id="integrationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="config-tab" data-toggle="tab" href="#config" role="tab">Configurações</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pix-tab" data-toggle="tab" href="#pix" role="tab">PIX</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="paghiper-tab" data-toggle="tab" href="#paghiper" role="tab">PagHiper</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="mercado-pago-tab" data-toggle="tab" href="#mercado-pago" role="tab">Mercado Pago</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="banco-inter-tab" data-toggle="tab" href="#banco-inter" role="tab">Banco Inter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="asaas-tab" data-toggle="tab" href="#asaas" role="tab">Asaas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab">WhatsApp</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="integrationTabContent">
                        {{-- Aba Configurações --}}
                        <div class="tab-pane fade show active" id="config" role="tabpanel">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="day_generate_invoice"><i class="fas fa-calendar-day"></i> Dia de geração de faturas</label>
                                        <select class="form-control" id="day_generate_invoice" name="day_generate_invoice">
                                            <option value="">Selecione o dia...</option>
                                            @for($d = 1; $d <= 31; $d++)
                                                <option value="{{ $d }}" {{ old('day_generate_invoice', $company->day_generate_invoice) == $d ? 'selected' : '' }}>
                                                    Dia {{ $d }}
                                                </option>
                                            @endfor
                                        </select>
                                        <small class="text-muted">Dia do mês para gerar faturas automaticamente (ex: dia 1, 5, 10, etc.)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="send_generate_invoice"><i class="fas fa-paper-plane"></i> Enviar fatura ao gerar?</label>
                                        <select class="form-control" id="send_generate_invoice" name="send_generate_invoice">
                                            <option value="Não" {{ old('send_generate_invoice', $company->send_generate_invoice ?? 'Não') == 'Não' ? 'selected' : '' }}>Não</option>
                                            <option value="Sim" {{ old('send_generate_invoice', $company->send_generate_invoice) == 'Sim' ? 'selected' : '' }}>Sim</option>
                                        </select>
                                        <small class="text-muted">Enviar automaticamente por email/WhatsApp após geração?</small>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Importante:</strong> As faturas serão geradas automaticamente no dia configurado através do cron job. 
                                Se a opção "Enviar ao gerar" estiver ativada, as notificações serão enviadas automaticamente para os clientes.
                            </div>
                        </div>
                        
                        {{-- Aba PIX --}}
                        <div class="tab-pane fade" id="pix" role="tabpanel">
                            <div class="form-group">
                                <label for="chave_pix">Chave PIX</label>
                                <input type="text" class="form-control" id="chave_pix" name="chave_pix" value="{{ old('chave_pix', $company->chave_pix ?? '') }}">
                            </div>
                        </div>
                        {{-- Aba PagHiper --}}
                        <div class="tab-pane fade" id="paghiper" role="tabpanel">
                            <div class="form-group">
                                <label for="token_paghiper">Token PagHiper</label>
                                <input type="text" class="form-control" id="token_paghiper" name="token_paghiper" value="{{ old('token_paghiper', $company->token_paghiper ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="key_paghiper">Key PagHiper</label>
                                <input type="text" class="form-control" id="key_paghiper" name="key_paghiper" value="{{ old('key_paghiper', $company->key_paghiper ?? '') }}">
                            </div>
                        </div>
                        {{-- Aba Mercado Pago --}}
                        <div class="tab-pane fade" id="mercado-pago" role="tabpanel">
                            <div class="form-group">
                                <label for="access_token_mp">Access Token Mercado Pago</label>
                                <input type="text" class="form-control" id="access_token_mp" name="access_token_mp" value="{{ old('access_token_mp', $company->access_token_mp ?? '') }}">
                            </div>
                        </div>
                        {{-- Aba Banco Inter --}}
                        <div class="tab-pane fade" id="banco-inter" role="tabpanel">
                            <div class="form-group">
                                <label for="inter_host">Host Inter</label>
                                <input type="text" class="form-control" id="inter_host" name="inter_host" value="{{ old('inter_host', $company->inter_host ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="inter_client_id">Client ID Inter</label>
                                <input type="text" class="form-control" id="inter_client_id" name="inter_client_id" value="{{ old('inter_client_id', $company->inter_client_id ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="inter_client_secret">Client Secret Inter</label>
                                <input type="text" class="form-control" id="inter_client_secret" name="inter_client_secret" value="{{ old('inter_client_secret', $company->inter_client_secret ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="inter_scope">Scope Inter</label>
                                <textarea class="form-control" id="inter_scope" name="inter_scope" rows="3">{{ old('inter_scope', $company->inter_scope ?? '') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="inter_chave_pix">Chave PIX Inter</label>
                                <input type="text" class="form-control" id="inter_chave_pix" name="inter_chave_pix" value="{{ old('inter_chave_pix', $company->inter_chave_pix ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="inter_webhook_url_billet">Webhook URL Boleto</label>
                                <input type="text" class="form-control" id="inter_webhook_url_billet" name="inter_webhook_url_billet" value="{{ old('inter_webhook_url_billet', $company->inter_webhook_url_billet ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="inter_webhook_url_pix">Webhook URL PIX</label>
                                <input type="text" class="form-control" id="inter_webhook_url_pix" name="inter_webhook_url_pix" value="{{ old('inter_webhook_url_pix', $company->inter_webhook_url_pix ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="inter_crt_file">Arquivo CRT Inter</label>
                                <input type="file" class="form-control-file" id="inter_crt_file" name="inter_crt_file">
                                @if($company->inter_crt_path ?? null)
                                    <small class="form-text text-muted">Arquivo atual: {{ $company->inter_crt_path }}</small>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="inter_key_file">Arquivo KEY Inter</label>
                                <input type="file" class="form-control-file" id="inter_key_file" name="inter_key_file">
                                @if($company->inter_key_path ?? null)
                                    <small class="form-text text-muted">Arquivo atual: {{ $company->inter_key_path }}</small>
                                @endif
                            </div>
                        </div>
                        {{-- Aba Asaas --}}
                        <div class="tab-pane fade" id="asaas" role="tabpanel">
                            <div class="form-group">
                                <label for="environment_asaas">Ambiente Asaas</label>
                                <select class="form-control" id="environment_asaas" name="environment_asaas">
                                    <option value="Teste" {{ old('environment_asaas', $company->environment_asaas ?? '') == 'Teste' ? 'selected' : '' }}>Teste</option>
                                    <option value="Producao" {{ old('environment_asaas', $company->environment_asaas ?? '') == 'Producao' ? 'selected' : '' }}>Produção</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="at_asaas_prod">Token Asaas Produção</label>
                                <input type="text" class="form-control" id="at_asaas_prod" name="at_asaas_prod" value="{{ old('at_asaas_prod', $company->at_asaas_prod ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="at_asaas_test">Token Asaas Teste</label>
                                <input type="text" class="form-control" id="at_asaas_test" name="at_asaas_test" value="{{ old('at_asaas_test', $company->at_asaas_test ?? '') }}">
                            </div>
                        </div>
                        {{-- Aba WhatsApp --}}
                        <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                            <div class="form-group">
                                <label for="api_session_whatsapp">API Session WhatsApp</label>
                                <input type="text" class="form-control" id="api_session_whatsapp" name="api_session_whatsapp" value="{{ old('api_session_whatsapp', $company->api_session_whatsapp ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="api_token_whatsapp">API Token WhatsApp</label>
                                <input type="text" class="form-control" id="api_token_whatsapp" name="api_token_whatsapp" value="{{ old('api_token_whatsapp', $company->api_token_whatsapp ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="typebot_id">Typebot ID</label>
                                <input type="text" class="form-control" id="typebot_id" name="typebot_id" value="{{ old('typebot_id', $company->typebot_id ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="typebot_enable">Typebot Habilitado</label>
                                <select class="form-control" id="typebot_enable" name="typebot_enable">
                                    <option value="s" {{ old('typebot_enable', $company->typebot_enable ?? '') == 's' ? 'selected' : '' }}>Sim</option>
                                    <option value="n" {{ old('typebot_enable', $company->typebot_enable ?? '') == 'n' ? 'selected' : '' }}>Não</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="{{ route('companies.index') }}" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
