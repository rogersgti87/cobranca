@extends('layouts.admin')

@section('content')

<style>
/* Estilos modernos para o index */
.modern-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    color: #fff;
    box-shadow: 0 10px 30px rgba(0, 123, 255, 0.3);
}

.modern-header h1 {
    margin: 0;
    font-weight: 700;
    font-size: 28px;
}

.header-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.modern-filter-card {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.filter-title {
    font-size: 14px;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.filter-title i {
    margin-right: 8px;
    color: #007bff;
}

.modern-input {
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
    height: 42px;
}

.modern-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.modern-select {
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    padding: 10px 15px;
    font-size: 14px;
    height: 42px;
    background: #fff url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23007bff' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E") no-repeat right 0.75rem center;
    background-size: 8px 10px;
    appearance: none;
}

.modern-table-card {
    background: #fff;
    border-radius: 15px;
    padding: 0;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}

.table-modern {
    margin: 0;
}

.table-modern thead {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
}

.table-modern thead th {
    border: none;
    padding: 18px 15px;
    font-weight: 600;
    font-size: 13px;
    color: #4a5568;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-modern tbody tr {
    border-bottom: 1px solid #f7fafc;
    transition: all 0.3s ease;
}

.table-modern tbody tr:hover {
    background: #f7fafc;
    transform: scale(1.01);
}

.table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border: none;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.user-avatar-fallback {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    font-size: 16px;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.status-badge-modern {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.status-active {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.status-inactive {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

.btn-action-modern {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.btn-edit {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: #fff;
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    color: #fff;
}

.btn-delete-modern {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    color: #fff;
    margin-left: 8px;
}

.btn-delete-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 101, 101, 0.3);
    color: #fff;
}

.btn-new {
    background: #fff;
    color: #007bff;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.btn-new:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    color: #007bff;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #a0aec0;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.9);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: 15px;
}

.loading-overlay.active {
    display: flex;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #e2e8f0;
    border-top-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.checkbox-modern {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    cursor: pointer;
}

.bulk-actions {
    background: #f7fafc;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 15px;
    display: none;
    align-items: center;
    justify-content: space-between;
}

.bulk-actions.active {
    display: flex;
}

.selected-count {
    font-weight: 600;
    color: #4a5568;
}
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1><i class="fas fa-users"></i> {{ $title }}</h1>
                <p style="margin: 5px 0 0; opacity: 0.9; font-size: 14px;">Gerencie os usuários do sistema</p>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{url($linkFormAdd)}}" class="btn btn-new">
                    <i class="fas fa-plus"></i> Novo Usuário
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        
        <!-- Filtros Modernos -->
        <div class="modern-filter-card">
            <div class="filter-title">
                <i class="fas fa-filter"></i> Filtros de Busca
            </div>
            
            <form id="filter-form">
                <input type="hidden" name="operator" value="like">
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-control modern-select" name="field" id="filter-field">
                            <option value="name" {{  request()->field == 'name' ? 'selected' : '' }}>Nome</option>
                            <option value="email" {{  request()->field == 'email' ? 'selected' : '' }}>E-mail</option>
                            <option value="created_at" {{  request()->field == 'created_at' ? 'selected' : '' }}>Data</option>
                            <option value="status" {{  request()->field == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>

                    <div class="col-md-8" id="filter-value-container">
                        <input type="text" class="form-control modern-input" name="value" id="filter-value" 
                               value="{{  request()->value }}" placeholder="Digite para filtrar...">
                    </div>
                </div>
            </form>
        </div>

        <!-- Ações em Lote -->
        <div class="bulk-actions" id="bulk-actions">
            <div>
                <span class="selected-count"><span id="selected-count">0</span> usuário(s) selecionado(s)</span>
            </div>
            <div>
                <button type="button" class="btn btn-delete-modern" id="btn-bulk-delete">
                    <i class="fas fa-trash"></i> Excluir Selecionados
                </button>
            </div>
        </div>

        <!-- Tabela Moderna -->
        <div class="modern-table-card" style="position: relative;">
            <div class="loading-overlay" id="loading-overlay">
                <div class="spinner"></div>
            </div>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="checkbox-modern" id="select-all">
                            </th>
                            <th style="width: 80px;">Foto</th>
                            <th><a href="#" class="sort-link" data-column="name">Nome <i class="fas fa-sort"></i></a></th>
                            <th><a href="#" class="sort-link" data-column="email">E-mail <i class="fas fa-sort"></i></a></th>
                            <th style="width: 120px;"><a href="#" class="sort-link" data-column="status">Status <i class="fas fa-sort"></i></a></th>
                            <th style="width: 150px;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="users-tbody">
                        @forelse($data as $result)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox-modern select-item" name="selected[]" value="{{$result->id}}">
                                </td>
                                <td>
                                    @php
                                        $hasImage = false;
                                        if($result->image && file_exists(public_path($result->image))) {
                                            $hasImage = true;
                                        }
                                    @endphp
                                    @if($hasImage)
                                        <img src="{{ url($result->image) }}" class="user-avatar" alt="{{$result->name}}">
                                    @else
                                        <div class="user-avatar-fallback">
                                            {{ strtoupper(substr($result->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td><strong>{{$result->name}}</strong></td>
                                <td>{{$result->email}}</td>
                                <td>
                                    <span class="status-badge-modern {{ $result->status == 'Ativo' ? 'status-active' : 'status-inactive' }}">
                                        <i class="fas fa-circle" style="font-size: 8px;"></i> {{ $result->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{url($linkFormEdit."&id=$result->id")}}" class="btn btn-sm btn-action-modern btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <h5>Nenhum usuário encontrado</h5>
                                        <p>Tente ajustar os filtros ou adicione um novo usuário</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div style="padding: 20px;">
                {!! $data->withQueryString()->links('pagination::bootstrap-4') !!}
            </div>
        </div>

    </div>
</div>

@section('scripts')

<script>

$(document).ready(function() {
    
    let filterTimeout;
    const $loadingOverlay = $('#loading-overlay');
    const $tbody = $('#users-tbody');

    // Filtro em tempo real (AJAX)
    function applyFilters(showLoading = true) {
        if (showLoading) {
            $loadingOverlay.addClass('active');
        }

        const formData = $('#filter-form').serialize();
        const currentUrl = new URL(window.location.href);
        
        // Atualizar URL sem recarregar
        const params = new URLSearchParams(formData);
        params.forEach((value, key) => {
            if (value) {
                currentUrl.searchParams.set(key, value);
            } else {
                currentUrl.searchParams.delete(key);
            }
        });
        
        window.history.pushState({}, '', currentUrl);

        // Fazer requisição AJAX
        $.ajax({
            url: "{{url($link)}}",
            method: 'GET',
            data: formData,
            success: function(response) {
                // Extrair tbody do HTML retornado
                const $newContent = $(response).find('#users-tbody');
                if ($newContent.length) {
                    $tbody.html($newContent.html());
                }
                $loadingOverlay.removeClass('active');
                updateSelectedCount();
            },
            error: function() {
                $loadingOverlay.removeClass('active');
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao filtrar',
                    text: 'Tente novamente',
                    confirmButtonColor: '#007bff'
                });
            }
        });
    }

    // Filtro automático ao digitar
    $('#filter-value').on('input', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            applyFilters();
        }, 500); // 500ms de delay
    });

    // Filtro ao mudar select
    $('#filter-field').on('change', function() {
        applyFilters();
        changeInputType();
    });

    // Mudar tipo de input baseado no campo selecionado
    function changeInputType() {
        const field = $("#filter-field").val();
        let newInput = '';
        
        if (field == 'status') {
            newInput = `
                <select class="form-control modern-select" name="value" id="filter-value">
                    <option value="">Todos</option>
                    <option value="Ativo" {{ request()->value == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="Inativo" {{ request()->value == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            `;
        } else if (field == 'created_at') {
            newInput = `
                <input type="date" class="form-control modern-input" name="value" id="filter-value" 
                       value="{{ request()->value }}">
            `;
        } else {
            newInput = `
                <input type="text" class="form-control modern-input" name="value" id="filter-value" 
                       value="{{ request()->value }}" placeholder="Digite para filtrar...">
            `;
        }
        
        $("#filter-value-container").html(newInput);
        
        // Reativar eventos
        $('#filter-value').on('input change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }

    // Selecionar todos
    $('#select-all').on('change', function() {
        $('.select-item').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });

    // Atualizar contagem de selecionados
    $(document).on('change', '.select-item', function() {
        updateSelectedCount();
    });

    function updateSelectedCount() {
        const count = $('.select-item:checked').length;
        $('#selected-count').text(count);
        
        if (count > 0) {
            $('#bulk-actions').addClass('active');
        } else {
            $('#bulk-actions').removeClass('active');
        }
    }

    // Excluir em lote
    $('#btn-bulk-delete').on('click', function() {
        const selected = $('.select-item:checked').map(function() {
            return $(this).val();
        }).get();

        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Nenhum usuário selecionado',
                confirmButtonColor: '#007bff'
            });
            return;
        }

        Swal.fire({
            title: 'Deseja remover os usuários selecionados?',
            text: `${selected.length} usuário(s) será(ão) removido(s)!`,
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Sim, deletar!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{url($linkDestroy)}}",
                    method: 'DELETE',
                    data: { selected: selected },
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Usuários removidos!',
                            confirmButtonColor: '#007bff'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: xhr.responseJSON || 'Erro ao deletar',
                            confirmButtonColor: '#007bff'
                        });
                    }
                });
            }
        });
    });

    // Ordenação
    $('.sort-link').on('click', function(e) {
        e.preventDefault();
        const column = $(this).data('column');
        const currentOrder = '{{ request()->order ?? "desc" }}';
        const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';
        
        const url = new URL(window.location.href);
        url.searchParams.set('column', column);
        url.searchParams.set('order', newOrder);
        
        window.location.href = url.toString();
    });

});

</script>

@endsection

@endsection
