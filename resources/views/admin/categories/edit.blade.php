@extends('admin.layouts.app')

@section('title', 'Editar Categoria')
@section('page-title', 'Editar Categoria')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Editar Categoria: {{ $category->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Nome -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da Categoria *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name) }}" 
                               required
                               placeholder="Ex: iPhone, Acessórios, Cases">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Slug atual: <code>{{ $category->slug }}</code></small>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Descreva esta categoria...">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Imagem Atual -->
                    @if($category->image)
                    <div class="mb-3">
                        <label class="form-label">Imagem Atual</label>
                        <div>
                            <img src="{{ asset('storage/' . $category->image) }}" 
                                 alt="{{ $category->name }}" 
                                 class="img-thumbnail"
                                 style="max-height: 200px;">
                        </div>
                    </div>
                    @endif

                    <!-- Nova Imagem -->
                    <div class="mb-3">
                        <label for="image" class="form-label">
                            {{ $category->image ? 'Alterar Imagem' : 'Adicionar Imagem' }}
                        </label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image"
                               accept="image/*"
                               onchange="previewImage(this)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Formatos aceitos: JPEG, PNG, JPG, GIF (máx. 2MB)</small>
                        
                        <!-- Preview da nova imagem -->
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Ordem de Exibição -->
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Ordem de Exibição</label>
                        <input type="number" 
                               class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', $category->sort_order ?? 0) }}" 
                               min="0"
                               placeholder="0">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Categorias com menor número aparecem primeiro</small>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Categoria Ativa
                            </label>
                        </div>
                        <small class="text-muted">Apenas categorias ativas aparecerão na loja</small>
                    </div>

                    <!-- Informações Adicionais -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Esta categoria possui <strong>{{ $category->products()->count() }}</strong> produto(s) associado(s).
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Atualizar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card de Exclusão -->
        @if($category->products()->count() == 0)
        <div class="card mt-3 border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Zona de Perigo</h6>
            </div>
            <div class="card-body">
                <p class="mb-3">Esta categoria não possui produtos associados e pode ser excluída permanentemente.</p>
                <form action="{{ route('admin.categories.destroy', $category) }}" 
                      method="POST"
                      onsubmit="return confirm('Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir Categoria
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="card mt-3 border-warning">
            <div class="card-header bg-warning">
                <h6 class="mb-0">Exclusão Não Permitida</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">Esta categoria não pode ser excluída pois possui produtos associados. Remova todos os produtos primeiro.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection

