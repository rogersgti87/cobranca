@extends('layouts.admin')

@section('content')
@php
    $logoExists = $company->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo);
@endphp

<style>
    .card-modern { border-left: 4px solid #007bff; box-shadow: 0 2px 12px rgba(0,123,255,0.15); }
    .card-header-modern { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: #fff; font-weight: 600; }
    .logo-preview-area { min-height: 180px; border: 2px dashed #007bff; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #f8f9fc; }
    .logo-placeholder { color: #007bff; font-size: 4rem; opacity: 0.5; }
    .btn-primary-modern { background: #007bff; border-color: #007bff; }
    .btn-primary-modern:hover { background: #0056b3; border-color: #0056b3; }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Empresa: {{ $company->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('admin') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Empresas</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Coluna Logo --}}
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card card-modern h-100">
                            <div class="card-header card-header-modern">
                                <h5 class="mb-0"><i class="fas fa-image"></i> Logo</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="logo-preview-area mb-3" id="logoPreviewArea">
                                    @if($logoExists)
                                        <img id="logoPreviewImg" src="{{ \Illuminate\Support\Facades\Storage::url($company->logo) }}" alt="Logo atual" class="img-fluid" style="max-height: 160px;">
                                        <i class="fas fa-building logo-placeholder" id="logoPlaceholder" style="display: none;"></i>
                                    @else
                                        <i class="fas fa-building logo-placeholder" id="logoPlaceholder"></i>
                                        <img id="logoPreviewImg" src="" alt="Preview" class="img-fluid" style="max-height: 160px; display: none;">
                                    @endif
                                </div>
                                <input type="file" class="form-control form-control-sm @error('logo') is-invalid @enderror" name="logo" id="logo" accept="image/*">
                                <small class="text-muted d-block mt-1">Máximo 2MB. Formatos: jpg, png, gif.</small>
                                @error('logo')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Coluna Formulário --}}
                    <div class="col-md-8 col-lg-9">
                        <div class="card card-modern mb-4">
                            <div class="card-header card-header-modern">
                                <h5 class="mb-0"><i class="fas fa-building"></i> Dados da Empresa</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Nome <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $company->name) }}" required autocomplete="off">
                                        @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="trade_name">Nome Fantasia</label>
                                        <input type="text" class="form-control @error('trade_name') is-invalid @enderror" name="trade_name" id="trade_name" value="{{ old('trade_name', $company->trade_name) }}" autocomplete="off">
                                        @error('trade_name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="type">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" name="type" id="type" required>
                                            <option value="">Selecione...</option>
                                            <option value="Física" {{ old('type', $company->type) == 'Física' ? 'selected' : '' }}>Física</option>
                                            <option value="Jurídica" {{ old('type', $company->type) == 'Jurídica' ? 'selected' : '' }}>Jurídica</option>
                                        </select>
                                        @error('type')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="document">Documento (CPF/CNPJ)</label>
                                        <input type="text" class="form-control @error('document') is-invalid @enderror" name="document" id="document" value="{{ old('document', $company->document) }}" autocomplete="off">
                                        @error('document')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="status">Status</label>
                                        <select class="form-control @error('status') is-invalid @enderror" name="status" id="status">
                                            <option value="Ativo" {{ old('status', $company->status) == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                                            <option value="Inativo" {{ old('status', $company->status) == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                                        </select>
                                        @error('status')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email', $company->email) }}" autocomplete="off">
                                        @error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="phone">Telefone</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{ old('phone', $company->phone) }}" autocomplete="off">
                                        @error('phone')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="whatsapp">WhatsApp</label>
                                        <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $company->whatsapp) }}" autocomplete="off">
                                        @error('whatsapp')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Endereço --}}
                        <div class="card card-modern mb-4">
                            <div class="card-header card-header-modern">
                                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Endereço</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="cep">CEP</label>
                                        <input type="text" class="form-control @error('cep') is-invalid @enderror" name="cep" id="cep" value="{{ old('cep', $company->cep) }}" autocomplete="off">
                                        @error('cep')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="address">Endereço</label>
                                        <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" id="address" value="{{ old('address', $company->address) }}" autocomplete="off">
                                        @error('address')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="number">Número</label>
                                        <input type="text" class="form-control @error('number') is-invalid @enderror" name="number" id="number" value="{{ old('number', $company->number) }}" autocomplete="off">
                                        @error('number')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="complement">Complemento</label>
                                        <input type="text" class="form-control @error('complement') is-invalid @enderror" name="complement" id="complement" value="{{ old('complement', $company->complement) }}" autocomplete="off">
                                        @error('complement')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="district">Bairro</label>
                                        <input type="text" class="form-control @error('district') is-invalid @enderror" name="district" id="district" value="{{ old('district', $company->district) }}" autocomplete="off">
                                        @error('district')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="city">Cidade</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" id="city" value="{{ old('city', $company->city) }}" autocomplete="off">
                                        @error('city')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="state">Estado</label>
                                        <select class="form-control @error('state') is-invalid @enderror" name="state" id="state">
                                            <option value="">Selecione...</option>
                                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                                <option value="{{ $uf }}" {{ old('state', $company->state) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                            @endforeach
                                        </select>
                                        @error('state')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary-modern btn btn-primary"><i class="fas fa-save"></i> Atualizar</button>
                            <a href="{{ route('companies.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const placeholder = document.getElementById('logoPlaceholder');
    const img = document.getElementById('logoPreviewImg');
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function() {
            img.src = reader.result;
            img.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        if (img) img.style.display = 'none';
        if (img) img.src = '';
        if (placeholder) placeholder.style.display = 'block';
    }
});
</script>
@endsection
