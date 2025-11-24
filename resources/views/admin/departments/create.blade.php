@extends('admin.layouts.app')

@section('title', 'Criar Departamento')
@section('page-title', 'Criar Departamento')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.departments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Slug (opcional)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Cor (HEX)</label>
                <input type="text" name="color" class="form-control" value="{{ old('color', '#667eea') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Ícone</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="Ex: bi bi-phone">
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                <label class="form-check-label" for="is_active">Ativo</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

@endsection
@extends('admin.layouts.app')

@section('title', 'Criar Departamento')
@section('page-title', 'Criar Departamento')
@section('page-subtitle')
    <p class="text-muted mb-0">Adicione um novo departamento à loja</p>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle"></i> Informações do Departamento
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome do Departamento *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Ordem de Exibição</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Números menores aparecem primeiro</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Ícone FontAwesome</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon') }}" 
                                       placeholder="Ex: fas fa-tshirt">
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Use classes do FontAwesome (ex: fas fa-tshirt, fas fa-laptop)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Cor Principal *</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color', '#667eea') }}">
                                    <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                           id="color_text" value="{{ old('color', '#667eea') }}" readonly>
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Departamento Ativo
                            </label>
                        </div>
                        <small class="form-text text-muted">Departamentos inativos não aparecerão no site</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Departamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Preview do Departamento -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye"></i> Preview
                </h5>
            </div>
            <div class="card-body">
                <div class="department-preview">
                    <div class="d-flex align-items-center mb-3">
                        <i id="preview-icon" class="fas fa-folder me-2" style="color: #667eea; font-size: 1.5rem;"></i>
                        <div>
                            <h6 id="preview-name" class="mb-0">Nome do Departamento</h6>
                            <small id="preview-slug" class="text-muted">slug-do-departamento</small>
                        </div>
                    </div>
                    <p id="preview-description" class="text-muted small">Descrição do departamento aparecerá aqui...</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">Ativo</span>
                        <small class="text-muted">Ordem: <span id="preview-order">0</span></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ícones Sugeridos -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-palette"></i> Ícones Sugeridos
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-laptop">
                            <i class="fas fa-laptop"></i><br>
                            <small>Eletrônicos</small>
                        </button>
                    </div>
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-tshirt">
                            <i class="fas fa-tshirt"></i><br>
                            <small>Vestuário</small>
                        </button>
                    </div>
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-home">
                            <i class="fas fa-home"></i><br>
                            <small>Casa</small>
                        </button>
                    </div>
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-dumbbell">
                            <i class="fas fa-dumbbell"></i><br>
                            <small>Esportes</small>
                        </button>
                    </div>
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-car">
                            <i class="fas fa-car"></i><br>
                            <small>Automóveis</small>
                        </button>
                    </div>
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-book">
                            <i class="fas fa-book"></i><br>
                            <small>Livros</small>
                        </button>
                    </div>
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-utensils">
                            <i class="fas fa-utensils"></i><br>
                            <small>Alimentação</small>
                        </button>
                    </div>
                    <div class="col-3 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 icon-suggestion" data-icon="fas fa-gamepad">
                            <i class="fas fa-gamepad"></i><br>
                            <small>Games</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cores Sugeridas -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-palette"></i> Cores Sugeridas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#667eea" style="background-color: #667eea;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#e91e63" style="background-color: #e91e63;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#4caf50" style="background-color: #4caf50;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#ff9800" style="background-color: #ff9800;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#9c27b0" style="background-color: #9c27b0;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#f44336" style="background-color: #f44336;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#2196f3" style="background-color: #2196f3;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#795548" style="background-color: #795548;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#607d8b" style="background-color: #607d8b;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#ff5722" style="background-color: #ff5722;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#8bc34a" style="background-color: #8bc34a;"></button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn p-2 w-100 color-suggestion" data-color="#00bcd4" style="background-color: #00bcd4;"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const iconInput = document.getElementById('icon');
    const colorInput = document.getElementById('color');
    const colorTextInput = document.getElementById('color_text');
    const descriptionInput = document.getElementById('description');
    const sortOrderInput = document.getElementById('sort_order');
    const isActiveCheckbox = document.getElementById('is_active');

    const previewName = document.getElementById('preview-name');
    const previewSlug = document.getElementById('preview-slug');
    const previewIcon = document.getElementById('preview-icon');
    const previewDescription = document.getElementById('preview-description');
    const previewOrder = document.getElementById('preview-order');

    // Atualizar preview do nome e slug
    nameInput.addEventListener('input', function() {
        const name = this.value || 'Nome do Departamento';
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '-')
            .replace(/^-+|-+$/g, '');
        
        previewName.textContent = name;
        previewSlug.textContent = slug;
    });

    // Atualizar preview do ícone
    iconInput.addEventListener('input', function() {
        const icon = this.value || 'fas fa-folder';
        previewIcon.className = icon + ' me-2';
    });

    // Atualizar preview da cor
    colorInput.addEventListener('input', function() {
        const color = this.value;
        colorTextInput.value = color;
        previewIcon.style.color = color;
    });

    // Atualizar preview da descrição
    descriptionInput.addEventListener('input', function() {
        const description = this.value || 'Descrição do departamento aparecerá aqui...';
        previewDescription.textContent = description;
    });

    // Atualizar preview da ordem
    sortOrderInput.addEventListener('input', function() {
        previewOrder.textContent = this.value || '0';
    });

    // Sugestões de ícones
    document.querySelectorAll('.icon-suggestion').forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.dataset.icon;
            iconInput.value = icon;
            previewIcon.className = icon + ' me-2';
        });
    });

    // Sugestões de cores
    document.querySelectorAll('.color-suggestion').forEach(button => {
        button.addEventListener('click', function() {
            const color = this.dataset.color;
            colorInput.value = color;
            colorTextInput.value = color;
            previewIcon.style.color = color;
        });
    });

    // Atualizar status no preview
    isActiveCheckbox.addEventListener('change', function() {
        const statusBadge = document.querySelector('.department-preview .badge');
        if (this.checked) {
            statusBadge.className = 'badge bg-success';
            statusBadge.textContent = 'Ativo';
        } else {
            statusBadge.className = 'badge bg-secondary';
            statusBadge.textContent = 'Inativo';
        }
    });
});
</script>
@endsection
