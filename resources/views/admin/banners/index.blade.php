@extends('admin.layouts.app')

@section('title', 'Banners')
@section('page-title', 'Gerenciar Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Banners</h4>
        <p class="text-muted mb-0">Gerencie os banners promocionais por departamento</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Banner
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.banners.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="department_id" class="form-label">Departamento</label>
                <select name="department_id" id="department_id" class="form-select">
                    <option value="">Todos os departamentos</option>
                    <option value="global" {{ request('department_id') === 'global' ? 'selected' : '' }}>Banners Globais</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="position" class="form-label">Posição</label>
                <select name="position" id="position" class="form-select">
                    <option value="">Todas as posições</option>
                    <option value="hero" {{ request('position') === 'hero' ? 'selected' : '' }}>Hero (Topo)</option>
                    <option value="category" {{ request('position') === 'category' ? 'selected' : '' }}>Categorias</option>
                    <option value="product" {{ request('position') === 'product' ? 'selected' : '' }}>Produtos</option>
                    <option value="footer" {{ request('position') === 'footer' ? 'selected' : '' }}>Rodapé</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Banners -->
<div class="card">
    <div class="card-body">
        @if($banners->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Título</th>
                            <th>Departamento</th>
                            <th>Posição</th>
                            <th>Público-Alvo</th>
                            <th>Ordem</th>
                            <th>Período</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banners as $banner)
                            <tr>
                                <td>
                                    @if($banner->image)
                                        <img src="{{ asset('storage/' . $banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             class="rounded" 
                                             style="width: 100px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $banner->title }}</strong>
                                    </div>
                                    @if($banner->description)
                                        <small class="text-muted">{{ Str::limit($banner->description, 50) }}</small>
                                    @endif
                                    @if($banner->link)
                                        <div>
                                            <small class="text-primary">
                                                <i class="bi bi-link-45deg"></i> {{ Str::limit($banner->link, 40) }}
                                            </small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($banner->department)
                                        <span class="badge bg-primary">
                                            <i class="bi bi-building"></i> {{ $banner->department->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-globe"></i> Global
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        @switch($banner->position)
                                            @case('hero')
                                                Topo (Hero)
                                                @break
                                            @case('category')
                                                Categorias
                                                @break
                                            @case('product')
                                                Produtos
                                                @break
                                            @case('footer')
                                                Rodapé
                                                @break
                                            @default
                                                {{ ucfirst($banner->position) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        @switch($banner->target_audience)
                                            @case('all')
                                                Todos
                                                @break
                                            @case('b2c')
                                                B2C
                                                @break
                                            @case('b2b')
                                                B2B
                                                @break
                                            @default
                                                {{ strtoupper($banner->target_audience) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $banner->sort_order ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($banner->starts_at || $banner->expires_at)
                                        <small>
                                            @if($banner->starts_at)
                                                <div><i class="bi bi-calendar-check"></i> {{ $banner->starts_at->format('d/m/Y') }}</div>
                                            @endif
                                            @if($banner->expires_at)
                                                <div><i class="bi bi-calendar-x"></i> {{ $banner->expires_at->format('d/m/Y') }}</div>
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">Sempre ativo</small>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.banners.toggle-active', $banner) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $banner->is_active ? 'success' : 'secondary' }}" title="Alterar status">
                                            @if($banner->is_active)
                                                <i class="bi bi-toggle-on"></i> Ativo
                                            @else
                                                <i class="bi bi-toggle-off"></i> Inativo
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm edit-banner-btn" 
                                                title="Editar"
                                                data-banner-id="{{ $banner->id }}"
                                                data-banner-title="{{ $banner->title }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este banner?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $banners->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum banner encontrado</h5>
                <p class="text-muted">Comece criando seu primeiro banner promocional.</p>
                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Banner
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@include('admin.banners.modal-edit')

@section('styles')
<style>
    /* Garantir que o modal fique acima de tudo */
    .modal-backdrop.show {
        z-index: 9998 !important;
        opacity: 0.5;
    }
    
    #editBannerModal {
        z-index: 9999 !important;
    }
    
    #editBannerModal.show {
        display: block !important;
    }
    
    #editBannerModal .modal-dialog {
        max-width: 90%;
        z-index: 10000 !important;
        margin: 1.75rem auto;
    }
    
    #editBannerModal .modal-content {
        position: relative;
        z-index: 10001 !important;
    }
    
    #editBannerModal .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    #editBannerModal .form-control-color {
        width: 50px;
        height: 40px;
    }
    
    #editBannerModal .nav-tabs .nav-link {
        color: #495057;
    }
    
    #editBannerModal .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editBannerModal'));
    const modalBody = document.getElementById('editBannerModalBody');
    const editBannerForm = document.getElementById('banner-edit-form');
    const toastEl = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');
    const toast = new bootstrap.Toast(toastEl);

    // Event listener para botões de editar
    document.querySelectorAll('.edit-banner-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bannerId = this.dataset.bannerId;
            const bannerTitle = this.dataset.bannerTitle;
            
            // Atualizar título do modal
            document.getElementById('editBannerModalLabel').innerHTML = 
                `<i class="bi bi-pencil"></i> Editar Banner: ${bannerTitle}`;
            
            // Mostrar loading
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-3 text-muted">Carregando formulário...</p>
                </div>
            `;
            
            // Abrir modal
            editModal.show();
            
            // Carregar formulário via AJAX
            fetch(`{{ route('admin.banners.edit', ':id') }}`.replace(':id', bannerId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar formulário');
                }
                return response.text();
            })
            .then(html => {
                modalBody.innerHTML = html;
                
                // Inicializar abas do Bootstrap se existirem
                const tabElements = modalBody.querySelectorAll('[data-bs-toggle="tab"]');
                if (tabElements.length > 0) {
                    tabElements.forEach(tab => {
                        tab.addEventListener('click', function(e) {
                            e.preventDefault();
                            const target = this.getAttribute('data-bs-target');
                            const tabPane = modalBody.querySelector(target);
                            if (tabPane) {
                                // Remover active de todas as abas
                                modalBody.querySelectorAll('.nav-link').forEach(link => {
                                    link.classList.remove('active');
                                });
                                modalBody.querySelectorAll('.tab-pane').forEach(pane => {
                                    pane.classList.remove('active', 'show');
                                });
                                // Adicionar active na aba clicada
                                this.classList.add('active');
                                tabPane.classList.add('active', 'show');
                            }
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Erro ao carregar formulário:</strong> ${error.message}
                    </div>
                `;
            });
        });
    });

    // Event listener para submit do formulário (usando delegação de eventos)
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'banner-edit-form') {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Desabilitar botão e mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
            
            // Enviar via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw { data, status: response.status };
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    toastMessage.innerHTML = data.message;
                    toastEl.querySelector('.toast-header i').className = 'bi bi-check-circle-fill text-success me-2';
                    toast.show();
                    
                    // Fechar modal
                    editModal.hide();
                    
                    // Recarregar página após 1 segundo para atualizar a tabela
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Mostrar erros
                    let errorHtml = '<ul class="mb-0">';
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            data.errors[key].forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                        });
                    } else {
                        errorHtml += `<li>${data.message || 'Erro desconhecido'}</li>`;
                    }
                    errorHtml += '</ul>';
                    
                    // Remover erros anteriores se existirem
                    const existingError = form.querySelector('.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Mostrar erros no topo do formulário
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                    errorDiv.innerHTML = `
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro ao atualizar banner:</strong>
                        ${errorHtml}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.insertBefore(errorDiv, form.firstChild);
                    
                    // Scroll para o topo do formulário
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Reabilitar botão
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                
                // Se for erro de validação (tem data.errors)
                if (error.data && error.data.errors) {
                    // Mostrar erros de validação
                    let errorHtml = '<ul class="mb-0">';
                    Object.keys(error.data.errors).forEach(key => {
                        error.data.errors[key].forEach(err => {
                            errorHtml += `<li>${err}</li>`;
                        });
                    });
                    errorHtml += '</ul>';
                    
                    // Remover erros anteriores se existirem
                    const existingError = form.querySelector('.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Mostrar erros no topo do formulário
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                    errorDiv.innerHTML = `
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro de validação:</strong>
                        ${errorHtml}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.insertBefore(errorDiv, form.firstChild);
                } else {
                    // Erro genérico
                    // Remover erros anteriores se existirem
                    const existingError = form.querySelector('.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Mostrar erro no formulário
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                    errorDiv.innerHTML = `
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro ao salvar banner:</strong>
                        <ul class="mb-0"><li>${error.data?.message || error.message || 'Erro ao salvar banner. Tente novamente.'}</li></ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.insertBefore(errorDiv, form.firstChild);
                }
                
                // Scroll para o topo do formulário
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Reabilitar botão
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    });

    // Limpar conteúdo do modal ao fechar
    editModal._element.addEventListener('hidden.bs.modal', function() {
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-3 text-muted">Carregando formulário...</p>
            </div>
        `;
    });
});
</script>
@endsection

