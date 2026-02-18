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

.company-logo {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.company-logo-fallback {
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

.badge-ativa {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: #fff;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 6px;
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

.btn-select {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    color: #fff;
}

.btn-select:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: #fff;
}

.btn-integrations {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: #fff;
}

.btn-integrations:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
    color: #fff;
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
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Modern Header -->
    <div class="modern-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1><i class="fas fa-building"></i> Minhas Empresas</h1>
                <p style="margin: 5px 0 0; opacity: 0.9; font-size: 14px;">Gerencie suas empresas</p>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('companies.create') }}" class="btn btn-new">
                    <i class="fas fa-plus"></i> Nova Empresa
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
                            <option value="name" {{ request()->field == 'name' ? 'selected' : '' }}>Nome</option>
                            <option value="document" {{ request()->field == 'document' ? 'selected' : '' }}>Documento</option>
                            <option value="email" {{ request()->field == 'email' ? 'selected' : '' }}>E-mail</option>
                            <option value="status" {{ request()->field == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>

                    <div class="col-md-8" id="filter-value-container">
                        @if(request()->field == 'status')
                        <select class="form-control modern-select" name="value" id="filter-value">
                            <option value="">Todos</option>
                            <option value="Ativo" {{ request()->value == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="Inativo" {{ request()->value == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                        @else
                        <input type="text" class="form-control modern-input" name="value" id="filter-value" 
                               value="{{ request()->value }}" placeholder="Digite para filtrar...">
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Ações em Lote -->
        <div class="bulk-actions" id="bulk-actions">
            <div>
                <span class="selected-count"><span id="selected-count">0</span> empresa(s) selecionada(s)</span>
            </div>
            <div>
                <button type="button" class="btn btn-delete-modern" id="btn-bulk-delete">
                    <i class="fas fa-trash"></i> Excluir Selecionadas
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
                            <th style="width: 80px;">Logo</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 220px;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="companies-tbody">
                        @forelse($companies as $company)
                            <tr>
                                <td>
                                    @if($company->isOwner(auth()->id()) && auth()->user()->companies()->count() > 1)
                                    <input type="checkbox" class="checkbox-modern select-item" name="selected[]" value="{{ $company->id }}">
                                    @else
                                    <input type="checkbox" class="checkbox-modern select-item" name="selected[]" value="{{ $company->id }}" disabled title="Não é possível excluir sua única empresa">
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $hasLogo = $company->logo && file_exists(public_path('storage/' . $company->logo));
                                    @endphp
                                    @if($hasLogo)
                                        <img src="{{ asset('storage/' . $company->logo) }}" class="company-logo" alt="{{ $company->name }}">
                                    @else
                                        <div class="company-logo-fallback">
                                            {{ strtoupper(substr($company->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $company->name }}</strong>
                                    @if($currentCompany && $currentCompany->id == $company->id)
                                    <span class="badge-ativa">ATIVA</span>
                                    @endif
                                </td>
                                <td>{{ $company->type ?? '-' }}</td>
                                <td>{{ $company->document ?? '-' }}</td>
                                <td>
                                    <span class="status-badge-modern {{ ($company->status ?? 'Ativo') == 'Ativo' ? 'status-active' : 'status-inactive' }}">
                                        <i class="fas fa-circle" style="font-size: 8px;"></i> {{ $company->status ?? 'Ativo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if(!$currentCompany || $currentCompany->id != $company->id)
                                    <form action="{{ route('companies.switch', $company) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-action-modern btn-select" title="Selecionar">
                                            <i class="fas fa-check"></i> Selecionar
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('companies.integrations', $company) }}" class="btn btn-sm btn-action-modern btn-integrations" title="Integrações">
                                        <i class="fas fa-plug"></i> Integrações
                                    </a>
                                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-action-modern btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    @if($company->isOwner(auth()->id()) && auth()->user()->companies()->count() > 1)
                                    <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline btn-delete-company-form" data-name="{{ $company->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-action-modern btn-delete-modern btn-delete-company-trigger" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-building"></i>
                                        <h5>Nenhuma empresa encontrada</h5>
                                        <p>Tente ajustar os filtros ou adicione uma nova empresa</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div style="padding: 20px;">
                {!! $companies->withQueryString()->links('pagination::bootstrap-4') !!}
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>

$(document).ready(function() {
    
    let filterTimeout;
    const $loadingOverlay = $('#loading-overlay');
    const $tbody = $('#companies-tbody');

    // Filtro em tempo real (AJAX)
    function applyFilters(showLoading = true) {
        if (showLoading) {
            $loadingOverlay.addClass('active');
        }

        const formData = $('#filter-form').serialize();
        const currentUrl = new URL(window.location.href);
        
        const params = new URLSearchParams(formData);
        params.forEach((value, key) => {
            if (value) {
                currentUrl.searchParams.set(key, value);
            } else {
                currentUrl.searchParams.delete(key);
            }
        });
        
        window.history.pushState({}, '', currentUrl);

        $.ajax({
            url: "{{ route('companies.index') }}",
            method: 'GET',
            data: formData,
            success: function(response) {
                const $newContent = $(response).find('#companies-tbody');
                if ($newContent.length) {
                    $tbody.html($newContent.html());
                }
                const $newCard = $(response).find('.modern-table-card');
                const $newPagDiv = $newCard.children('div').last();
                if ($newPagDiv.length) {
                    $('.modern-table-card').children('div').last().html($newPagDiv.html());
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

    // Filtro automático ao digitar (debounce 500ms)
    $(document).on('input', '#filter-value', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });

    // Filtro ao mudar select
    $('#filter-field').on('change', function() {
        changeInputType();
        applyFilters();
    });

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
        } else {
            newInput = `
                <input type="text" class="form-control modern-input" name="value" id="filter-value" 
                       value="{{ request()->value }}" placeholder="Digite para filtrar...">
            `;
        }
        
        $("#filter-value-container").html(newInput);
        
        $('#filter-value').on('input change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }

    // Selecionar todos (apenas os habilitados)
    $('#select-all').on('change', function() {
        $('.select-item:not(:disabled)').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });

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
                title: 'Nenhuma empresa selecionada',
                confirmButtonColor: '#007bff'
            });
            return;
        }

        Swal.fire({
            title: 'Deseja remover as empresas selecionadas?',
            text: `${selected.length} empresa(s) será(ão) removida(s)!`,
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Sim, deletar!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{ route('companies.bulkDestroy') }}",
                    method: 'DELETE',
                    data: { selected: selected },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Empresas removidas!',
                            confirmButtonColor: '#007bff'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Erro ao deletar';
                        if (xhr.responseJSON) {
                            msg = typeof xhr.responseJSON === 'string' ? xhr.responseJSON : (xhr.responseJSON.message || msg);
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: msg,
                            confirmButtonColor: '#007bff'
                        });
                    }
                });
            }
        });
    });

    // Confirmação de exclusão individual
    $(document).on('click', '.btn-delete-company-trigger', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var companyName = form.data('name') || 'esta empresa';

        Swal.fire({
            title: 'Excluir empresa?',
            text: 'Deseja realmente excluir ' + companyName + '? Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#007bff',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

});

</script>
@endsection
