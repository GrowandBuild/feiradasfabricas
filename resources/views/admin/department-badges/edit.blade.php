@extends('admin.layouts.app')

@section('title', 'Editar Selo')
@section('page-title', 'Editar Selo de Marca')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.department-badges.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar para Selos
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro ao salvar selo:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.department-badges.update', $departmentBadge) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Departamento *</label>
                        <select name="department_id" 
                                id="department_id" 
                                class="form-select @error('department_id') is-invalid @enderror" 
                                required>
                            <option value="">Selecione um departamento</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $departmentBadge->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Título da Marca *</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $departmentBadge->title) }}" 
                               placeholder="Ex: Apple, Samsung, Infinix"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nome da marca que aparecerá abaixo do logo</small>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagem do Logo</label>
                        <div class="mb-2">
                            <p class="text-muted mb-2">Imagem atual:</p>
                            <img src="{{ $departmentBadge->image_url }}" 
                                 alt="{{ $departmentBadge->title }}" 
                                 class="rounded-circle border" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div id="image-preview-container" class="mb-2" style="display: none;">
                            <p class="text-muted mb-2">Nova imagem:</p>
                            <img id="image-preview" src="" alt="Preview" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                            <div class="small text-success mt-1">
                                <i class="bi bi-check-circle"></i> <span id="image-filename"></span>
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        <small class="text-muted">
                            <strong>📁 Escolha um arquivo para substituir a imagem atual</strong><br>
                            Formatos aceitos: JPG, PNG, GIF, WEBP. Tamanho máximo: 5MB<br>
                            Recomendado: Imagem quadrada (ex: 200x200px) para melhor visualização<br>
                            <strong>Deixe em branco para manter a imagem atual</strong>
                        </small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label">Link (Opcional)</label>
                        <input type="url" 
                               class="form-control @error('link') is-invalid @enderror" 
                               id="link" 
                               name="link" 
                               value="{{ old('link', $departmentBadge->link) }}" 
                               placeholder="https://exemplo.com.br/produtos/marca">
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Link para onde o usuário será redirecionado ao clicar no selo</small>
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Ordem de Exibição</label>
                        <input type="number" 
                               class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', $departmentBadge->sort_order) }}" 
                               min="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Número menor aparece primeiro (0 = primeiro)</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $departmentBadge->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Ativo
                            </label>
                        </div>
                        <small class="text-muted">Selos inativos não aparecerão no site</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.department-badges.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Atualizar Selo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const imageFilename = document.getElementById('image-filename');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.style.display = 'block';
                imageFilename.textContent = file.name;
            };
            reader.readAsDataURL(file);
        } else {
            imagePreviewContainer.style.display = 'none';
        }
    });
});
</script>
@endsection

