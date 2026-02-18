@extends('layouts.admin')

@section('content')

<style>
/* Estilos modernos - Empresas */
.profile-card {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.company-logo-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto 15px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.company-logo-wrapper:hover {
    transform: scale(1.05);
}

.company-logo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid #fff;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.company-logo-fallback {
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

.company-logo-fallback i {
    font-size: 70px;
    color: #fff;
}

.logo-overlay {
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

.logo-overlay i {
    color: #007bff;
    font-size: 18px;
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
    color: #fff;
}

.save-button i {
    margin-right: 8px;
}

.compact-input {
    height: 42px;
}

#logo-input {
    display: none;
}
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: 600; color: #2d3748;">Nova Empresa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Empresas</a></li>
                        <li class="breadcrumb-item active">Nova Empresa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">

            <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- Coluna Esquerda: Logo da Empresa -->
                    <div class="col-lg-4 col-md-5">
                        <div class="profile-card">
                            <div class="company-logo-wrapper" id="logo-trigger">
                                @php
                                    $hasLogo = false;
                                @endphp
                                @if($hasLogo)
                                    <img src="" id="logo-preview" class="company-logo" alt="Logo da empresa" style="display: none;">
                                    <div class="company-logo-fallback" id="logo-fallback">
                                        <i class="fas fa-building"></i>
                                    </div>
                                @else
                                    <img src="" id="logo-preview" class="company-logo" alt="Logo da empresa" style="display: none;">
                                    <div class="company-logo-fallback" id="logo-fallback">
                                        <i class="fas fa-building"></i>
                                    </div>
                                @endif
                                <div class="logo-overlay">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </div>
                            <input type="file" name="logo" id="logo-input" accept="image/*">
                            @error('logo')
                                <div class="text-white mt-2 small"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                            <h5 id="company-name-display">Nova Empresa</h5>
                            <p><i class="fas fa-building"></i> Cadastro de empresa</p>
                        </div>

                        <!-- Botão Salvar -->
                        <button type="submit" class="btn save-button btn-block">
                            <i class="fas fa-save"></i> Salvar Empresa
                        </button>
                    </div>

                    <!-- Coluna Direita: Formulário -->
                    <div class="col-lg-8 col-md-7">

                        <!-- 🏢 Dados da Empresa -->
                        <div class="modern-card">
                            <h6 class="section-title">
                                <i class="fas fa-building"></i> Dados da Empresa
                            </h6>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label><i class="fas fa-building text-muted"></i> Nome Razão Social <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control compact-input @error('name') is-invalid @enderror" 
                                               name="name" id="name" value="{{ old('name') }}" required autocomplete="off" 
                                               placeholder="Nome da empresa">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-tag text-muted"></i> Nome Fantasia</label>
                                        <input type="text" class="form-control compact-input @error('trade_name') is-invalid @enderror" 
                                               name="trade_name" id="trade_name" value="{{ old('trade_name') }}" autocomplete="off">
                                        @error('trade_name')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-file-alt text-muted"></i> Tipo <span class="text-danger">*</span></label>
                                        <select class="form-control compact-input @error('type') is-invalid @enderror" name="type" id="type" required>
                                            <option value="">Selecione...</option>
                                            <option value="Física" {{ old('type') == 'Física' ? 'selected' : '' }}>Física</option>
                                            <option value="Jurídica" {{ old('type') == 'Jurídica' ? 'selected' : '' }}>Jurídica</option>
                                        </select>
                                        @error('type')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-id-card text-muted"></i> CNPJ/CPF</label>
                                        <input type="text" class="form-control compact-input @error('document') is-invalid @enderror" 
                                               name="document" id="document" value="{{ old('document') }}" autocomplete="off" 
                                               placeholder="00.000.000/0001-00">
                                        @error('document')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-toggle-on text-muted"></i> Status</label>
                                        <select class="form-control compact-input" name="status">
                                            <option value="Ativo" selected>Ativo</option>
                                            <option value="Inativo">Inativo</option>
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
                                        <label><i class="fas fa-envelope text-muted"></i> E-mail</label>
                                        <input type="email" class="form-control compact-input @error('email') is-invalid @enderror" 
                                               name="email" id="email" value="{{ old('email') }}" autocomplete="off">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone text-muted"></i> Telefone</label>
                                        <input type="text" class="form-control compact-input @error('phone') is-invalid @enderror" 
                                               name="phone" id="phone" value="{{ old('phone') }}" autocomplete="off">
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fab fa-whatsapp text-muted"></i> WhatsApp</label>
                                        <input type="text" class="form-control compact-input @error('whatsapp') is-invalid @enderror" 
                                               name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}" autocomplete="off">
                                        @error('whatsapp')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
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
                                        <label><i class="fas fa-mail-bulk text-muted"></i> CEP</label>
                                        <input type="text" class="form-control compact-input @error('cep') is-invalid @enderror" 
                                               name="cep" id="cep" value="{{ old('cep') }}" autocomplete="off">
                                        @error('cep')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-road text-muted"></i> Endereço</label>
                                        <input type="text" class="form-control compact-input @error('address') is-invalid @enderror" 
                                               name="address" id="address" value="{{ old('address') }}" autocomplete="off">
                                        @error('address')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Número</label>
                                        <input type="text" class="form-control compact-input @error('number') is-invalid @enderror" 
                                               name="number" id="number" value="{{ old('number') }}" autocomplete="off">
                                        @error('number')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input type="text" class="form-control compact-input @error('district') is-invalid @enderror" 
                                               name="district" id="district" value="{{ old('district') }}" autocomplete="off">
                                        @error('district')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input type="text" class="form-control compact-input @error('city') is-invalid @enderror" 
                                               name="city" id="city" value="{{ old('city') }}" autocomplete="off">
                                        @error('city')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>UF</label>
                                        <input type="text" class="form-control compact-input @error('state') is-invalid @enderror" 
                                               name="state" id="state" value="{{ old('state') }}" autocomplete="off" maxlength="2" placeholder="SP">
                                        @error('state')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Complemento</label>
                                        <input type="text" class="form-control compact-input @error('complement') is-invalid @enderror" 
                                               name="complement" id="complement" value="{{ old('complement') }}" autocomplete="off">
                                        @error('complement')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 📅 Configurações de Fatura -->
                        <div class="modern-card">
                            <h6 class="section-title">
                                <i class="fas fa-calendar-alt"></i> Configurações de Fatura
                            </h6>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-day text-muted"></i> Dia de geração</label>
                                        <select class="form-control compact-input @error('day_generate_invoice') is-invalid @enderror" 
                                                name="day_generate_invoice" id="day_generate_invoice">
                                            <option value="">Selecione o dia...</option>
                                            @for($d = 1; $d <= 31; $d++)
                                                <option value="{{ $d }}" {{ old('day_generate_invoice') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                            @endfor
                                        </select>
                                        <small class="text-muted">Dia do mês para gerar faturas automaticamente</small>
                                        @error('day_generate_invoice')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-paper-plane text-muted"></i> Enviar ao gerar?</label>
                                        <select class="form-control compact-input @error('send_generate_invoice') is-invalid @enderror" 
                                                name="send_generate_invoice" id="send_generate_invoice">
                                            <option value="Não" {{ old('send_generate_invoice', 'Não') == 'Não' ? 'selected' : '' }}>Não</option>
                                            <option value="Sim" {{ old('send_generate_invoice') == 'Sim' ? 'selected' : '' }}>Sim</option>
                                        </select>
                                        <small class="text-muted">Enviar fatura automaticamente ao gerar?</small>
                                        @error('send_generate_invoice')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Logo upload - click to select
    $('#logo-trigger').on('click', function() {
        $('#logo-input').click();
    });

    // Logo preview
    $('#logo-input').on('change', function(e) {
        var file = e.target.files[0];
        if (file && file.type.match('image.*')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview').attr('src', e.target.result).show();
                $('#logo-fallback').hide();
            };
            reader.readAsDataURL(file);
        }
    });

    // Atualizar nome exibido no card
    $('#name').on('input', function() {
        var val = $(this).val();
        $('#company-name-display').text(val || 'Nova Empresa');
    });

    // ViaCEP - Buscar endereço por CEP
    $('#cep').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/')
                .done(function(data) {
                    if (!data.erro) {
                        $('#address').val(data.logradouro);
                        $('#district').val(data.bairro);
                        $('#city').val(data.localidade);
                        $('#state').val(data.uf);
                    }
                });
        }
    });
});
</script>
@endsection

@endsection
