@extends('layouts.admin')

@section('content')

<style>
/* Estilos modernos e elegantes */
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
}

.save-button i {
    margin-right: 8px;
}

.compact-input {
    height: 42px;
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
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
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

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            
            <form class="form" enctype="multipart/form-data">
                <input type="hidden" id="url" value="{{url($link)}}">
                <input type="hidden" id="user-id" value="{{ isset($data->id) ? $data->id : '' }}">
                <input type="hidden" id="thumbnail" name="image" value="{{ isset($data->image) ? $data->image : '' }}">

                <div class="row">
                    <!-- Coluna Esquerda: Foto do Perfil -->
                    <div class="col-lg-4 col-md-5">
                        <div class="profile-card">
                            <div class="profile-photo-wrapper" id="lfm" data-input="thumbnail" data-preview="holder">
                                @php
                                    $hasImage = false;
                                    if(isset($data->image) && $data->image != null && file_exists(public_path($data->image))) {
                                        $hasImage = true;
                                    }
                                @endphp
                                @if($hasImage)
                                    <img src="{{ url($data->image) }}" 
                                         id="holder" 
                                         class="profile-photo"
                                         alt="Foto do perfil">
                                @else
                                    <div class="profile-photo-fallback" id="holder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <div class="photo-overlay">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </div>
                            <h5>{{ isset($data->name) ? $data->name : 'Novo Usuário' }}</h5>
                            <p><i class="fas fa-envelope"></i> {{ isset($data->email) ? $data->email : 'email@example.com' }}</p>
                            
                            @if(isset($data->status))
                            <div class="mt-3">
                                <span class="status-badge {{ $data->status === 'Ativo' ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas fa-circle" style="font-size: 8px;"></i> {{ $data->status }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <!-- Botão Salvar -->
                        <button type="button" class="btn save-button btn-block" id="btn-salvar">
                            <i class="fas fa-save"></i> Salvar Usuário
                        </button>
                    </div>

                    <!-- Coluna Direita: Formulário -->
                    <div class="col-lg-8 col-md-7">
                        
                        <!-- Dados Pessoais -->
                        <div class="modern-card">
                            <h6 class="section-title">
                                <i class="fas fa-user"></i> Dados Pessoais
                            </h6>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-id-card text-muted"></i> CPF</label>
                                        <input type="text" class="form-control compact-input" name="document" 
                                               autocomplete="off" value="{{isset($data->document) ? $data->document : ''}}" 
                                               maxlength="14" placeholder="000.000.000-00">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label><i class="fas fa-user text-muted"></i> Nome Completo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control compact-input" name="name" 
                                               autocomplete="off" required value="{{isset($data->name) ? $data->name : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-envelope text-muted"></i> E-mail <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control compact-input" name="email" 
                                               autocomplete="off" required value="{{isset($data->email) ? $data->email : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fas fa-phone text-muted"></i> Telefone</label>
                                        <input type="text" class="form-control compact-input" name="telephone" 
                                               autocomplete="off" value="{{isset($data->telephone) ? $data->telephone : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fab fa-whatsapp text-muted"></i> WhatsApp</label>
                                        <input type="text" class="form-control compact-input" name="whatsapp" 
                                               autocomplete="off" value="{{isset($data->whatsapp) ? $data->whatsapp : ''}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Segurança -->
                        <div class="modern-card">
                            <h6 class="section-title">
                                <i class="fas fa-lock"></i> Segurança
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-key text-muted"></i> Senha <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control compact-input" name="password" 
                                               autocomplete="off" {{ !isset($data->id) ? 'required' : '' }}>
                                        @if(isset($data->id))
                                        <small class="text-muted">Deixe em branco para manter a senha atual</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-check-circle text-muted"></i> Confirmar Senha <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control compact-input" name="password_confirmation" 
                                               autocomplete="off" {{ !isset($data->id) ? 'required' : '' }}>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><i class="fas fa-toggle-on text-muted"></i> Status</label>
                                        <select class="form-control compact-input" name="status">
                                            <option {{ isset($data->status) && $data->status === 'Ativo' ? 'selected' : '' }} value="Ativo">Ativo</option>
                                            <option {{ isset($data->status) && $data->status === 'Inativo' ? 'selected' : '' }} value="Inativo">Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="modern-card">
                            <h6 class="section-title">
                                <i class="fas fa-map-marker-alt"></i> Endereço
                            </h6>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fas fa-mail-bulk text-muted"></i> CEP</label>
                                        <input type="text" class="form-control compact-input" name="cep" id="cep" 
                                               autocomplete="off" value="{{isset($data->cep) ? $data->cep : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label><i class="fas fa-road text-muted"></i> Endereço</label>
                                        <input type="text" class="form-control compact-input" name="address" id="address" 
                                               autocomplete="off" value="{{isset($data->address) ? $data->address : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Número</label>
                                        <input type="text" class="form-control compact-input" name="number" id="number" 
                                               autocomplete="off" value="{{isset($data->number) ? $data->number : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input type="text" class="form-control compact-input" name="district" id="district" 
                                               autocomplete="off" value="{{isset($data->district) ? $data->district : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input type="text" class="form-control compact-input" name="city" id="city" 
                                               autocomplete="off" value="{{isset($data->city) ? $data->city : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>UF</label>
                                        <input type="text" class="form-control compact-input" name="state" id="state" 
                                               autocomplete="off" readonly value="{{isset($data->state) ? $data->state : ''}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Complemento</label>
                                        <input type="text" class="form-control compact-input" name="complement" id="complement" 
                                               autocomplete="off" value="{{isset($data->complement) ? $data->complement : ''}}">
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

    $('#lfm').filemanager('image');

    // Save data
    $(document).on('click', '#btn-salvar', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });
        var url_act = "{{Request::get('act')}}";
        if(url_act == 'edit'){
            var url = "{{ url("$linkUpdate") }}";
            var method = 'POST';
        }else{
            var url = "{{ url("$linkStore") }}";
            var method = 'POST';
        }

        var data = new FormData($('.form')[0]);

        $.ajax({
            url: url,
            data:data,
            method:method,
            processData: false,
            contentType: false,
            success:function(data){
                Swal.fire({
                    width:400,
                    title: "<h5 style='color:#007bff'>" + data + "</h5>",
                    icon: 'success',
                    showConfirmButton: false,
                    showClass: {
                        popup: 'animate__animated animate__backInUp'
                    },
                    allowOutsideClick: false,
                    html:
                    '<a href="{{url($linkFormAdd)}}" class="btn btn-secondary btn-md" style="border-radius: 8px;"> <i class="fa fa-plus"></i> Novo</a>  ' +
                    `<a href="{{url($linkFormEdit)}}" class="btn btn-success btn-md" style="border-radius: 8px; ${url_act == 'add' ? 'display:none;' : ''}"> <i class="fa fa-edit"></i> Editar</a>  ` +
                    '<a href="{{url($link)}}" class="btn btn-primary btn-md" style="border-radius: 8px;"> <i class="fa fa-list"></i> Listar</a>',
                });
            },
            error:function (xhr) {
                if(xhr.status === 422){
                    Swal.fire({
                        text: xhr.responseJSON,
                        width:350,
                        icon: 'warning',
                        confirmButtonColor: "#007bff",
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                } else{
                    Swal.fire({
                        text: xhr.responseJSON,
                        width:350,
                        icon: 'error',
                        confirmButtonColor: "#007bff",
                        showClass: {
                            popup: 'animate__animated animate__wobble'
                        }
                    });
                }
            }
        });
    });

</script>

@endsection

@endsection
