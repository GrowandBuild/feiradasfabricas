@extends('admin.layouts.app')

@section('title', 'Novo Produto')
@section('page-title', 'Criar Novo Produto')

@section('content')
<style>
/* Estilos Modernos para Create Product - SMART SEARCH */
.form-control, .form-select {
    border: 2px solid #e5e7eb !important;
    border-radius: 12px !important;
    padding: 14px 18px !important;
    font-size: 15px !important;
    font-weight: 500 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background: #ffffff !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    color: #1f2937 !important;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea !important;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15), 0 8px 25px rgba(102, 126, 234, 0.2) !important;
    outline: none !important;
    transform: translateY(-2px) !important;
}

.form-control:hover, .form-select:hover {
    border-color: #667eea !important;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15) !important;
}

.form-label {
    font-weight: 700 !important;
    color: #1f2937 !important;
    margin-bottom: 10px !important;
    font-size: 14px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.input-group {
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
}

.input-group-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    font-weight: 700 !important;
    color: white !important;
    padding: 14px 18px !important;
}

.input-group .form-control {
    border-left: none !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

.input-group:focus-within {
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15), 0 8px 25px rgba(102, 126, 234, 0.2) !important;
}

.input-group:focus-within .input-group-text {
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%) !important;
}

textarea.form-control {
    resize: vertical !important;
    min-height: 140px !important;
    line-height: 1.6 !important;
}

textarea.form-control:focus {
    min-height: 160px !important;
}

.form-check-input {
    width: 22px !important;
    height: 22px !important;
    border: 2px solid #d1d5db !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
}

.form-check-input:checked {
    background-color: #667eea !important;
    border-color: #667eea !important;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
}

.form-check-input:hover {
    border-color: #667eea !important;
    transform: scale(1.1) !important;
}

.form-check-label {
    font-weight: 600 !important;
    color: #4b5563 !important;
    cursor: pointer !important;
    margin-left: 10px !important;
}

.card {
    border: none !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
    background: #ffffff !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    margin-bottom: 24px !important;
}

.card:hover {
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16) !important;
    transform: translateY(-4px) !important;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    padding: 24px 28px !important;
    border-bottom: none !important;
}

.card-header h5 {
    color: white !important;
    font-weight: 800 !important;
    font-size: 20px !important;
    margin: 0 !important;
}

.card-body {
    padding: 32px 28px !important;
}

.btn {
    border-radius: 12px !important;
    padding: 14px 28px !important;
    font-weight: 700 !important;
    font-size: 15px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: none !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
}

.btn:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2) !important;
}

.btn:active {
    transform: translateY(0) !important;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
}

.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
    color: white !important;
    box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4) !important;
}

.btn-outline-secondary {
    background: transparent !important;
    border: 2px solid #e5e7eb !important;
    color: #6b7280 !important;
}

.btn-outline-secondary:hover {
    background: #f3f4f6 !important;
    border-color: #d1d5db !important;
    color: #4b5563 !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.6s ease-out !important;
}

/* Estilos para o álbum de imagens */
.album-image-item:hover .position-absolute {
    opacity: 1 !important;
}

.album-image-item {
    transition: transform 0.2s ease;
}

.album-image-item:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .form-control, .form-select {
        padding: 16px 18px !important;
        font-size: 16px !important;
    }
    
    .card-body {
        padding: 24px !important;
    }
    
    .btn {
        padding: 16px 24px !important;
        font-size: 15px !important;
    }
}
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-3"></i>Criar Novo Produto
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Informações Básicas -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-tag me-2"></i>Nome do Produto *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Digite o nome do produto">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sku" class="form-label">
                                    <i class="bi bi-upc me-2"></i>SKU
                                    <small class="text-muted">(será gerado automaticamente se deixado em branco)</small>
                                </label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku') }}"
                                       placeholder="Ex: PROD-001">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-text-paragraph me-2"></i>Descrição *
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required
                                  placeholder="Descreva detalhes importantes sobre o produto...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Preços e Estoque -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    <i class="bi bi-currency-dollar me-2"></i>Preço (B2C) *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" required
                                           placeholder="0.00">
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @if(setting('b2b_enabled', false))
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="b2b_price" class="form-label">
                                    <i class="bi bi-briefcase me-2"></i>Preço (B2B)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('b2b_price') is-invalid @enderror" 
                                           id="b2b_price" name="b2b_price" value="{{ old('b2b_price') }}"
                                           placeholder="0.00">
                                </div>
                                @error('b2b_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">
                                    <i class="bi bi-receipt me-2"></i>Preço de Custo
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                                           id="cost_price" name="cost_price" value="{{ old('cost_price') }}"
                                           placeholder="0.00">
                                </div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">
                                    <i class="bi bi-box-seam me-2"></i>Quantidade em Estoque *
                                </label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                                       placeholder="0">
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_stock" class="form-label">Estoque Mínimo *</label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror" 
                                       id="min_stock" name="min_stock" value="{{ old('min_stock', 10) }}" required>
                                @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Categorias -->
                    <div class="mb-3">
                        <label class="form-label">Categorias *</label>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="categories[]" value="{{ $category->id }}" 
                                               id="category_{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('categories')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Marca -->
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Marca do Produto</label>
                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                id="brand_id" name="brand_id">
                            <option value="">— Nenhuma marca selecionada —</option>
                            @php
                                $brands = \App\Models\Brand::active()->orderBy('sort_order')->orderBy('name')->get();
                            @endphp
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                        {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Departamento</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                            <option value="">— Nenhum departamento selecionado —</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Selecione o departamento do produto (opcional)</small>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="homepage_section_ids" class="form-label">
                            <i class="bi bi-house-door me-2"></i>Seções da Homepage
                        </label>
                        <select class="form-select @error('homepage_section_ids') is-invalid @enderror" 
                                id="homepage_section_ids" 
                                name="homepage_section_ids[]" 
                                multiple 
                                size="4"
                                style="min-height: 100px;">
                            <option value="">— Nenhuma seção selecionada —</option>
                            @foreach($homepageSections as $section)
                                <option value="{{ $section->id }}" 
                                        @if(old('homepage_section_ids', []))
                                            {{ in_array($section->id, old('homepage_section_ids', [])) ? 'selected' : '' }}
                                        @endif>
                                    {{ $section->title }}
                                    @if(!$section->enabled)
                                        <small class="text-muted">(Inativo)</small>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Selecione as seções da homepage onde este produto deve aparecer. 
                            Mantenha Ctrl/Cmd pressionado para selecionar múltiplas seções.
                        </small>
                        @error('homepage_section_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Imagens -->
                    <div class="mb-3">
                        <label for="images" class="form-label">Imagens do Produto</label>
                        
                        <!-- Zona de colar imagens -->
                        <div class="paste-zone" id="createProductPasteZone" style="border: 2px dashed #dee2e6; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 15px; transition: all 0.3s ease; cursor: pointer;">
                            <div class="paste-content">
                                <i class="bi bi-clipboard2-pulse" style="font-size: 2rem; color: #6c757d; margin-bottom: 10px;"></i>
                                <p class="mb-2" style="color: #6c757d; font-weight: 500;">Arraste imagens aqui ou cole com Ctrl+V</p>
                                <small class="text-muted">Ou clique para selecionar arquivos (múltiplo permitido)</small>
                            </div>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*"
                                   style="display: none;">
                        </div>
                        
                        <!-- Botões de seleção de imagens -->
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="select-from-album-btn">
                                <i class="bi bi-images me-1"></i> Selecionar do Álbum
                            </button>
                        </div>
                        
                        @if(!empty($preselectedImages ?? []))
                            <div class="mt-2" id="preselected-images">
                                <div class="small text-muted mb-2">Imagens selecionadas do álbum:</div>
                                <div class="d-flex flex-wrap gap-2" id="preselected-images-list">
                                    @foreach($preselectedImages as $img)
                                        <div class="position-relative border rounded" data-album-image-id="{{ $img['id'] }}" style="width:100px; height:100px; overflow:hidden">
                                            <img src="{{ $img['url'] }}" class="w-100 h-100" style="object-fit:cover;" loading="lazy">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute remove-preselected" style="top:6px; right:6px; padding:4px 6px">Remover</button>
                                            <input type="hidden" name="existing_image_ids[]" value="{{ $img['id'] }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div id="preselected-images" class="d-none"></div>
                        @endif
                        <div class="form-text">Você pode selecionar múltiplas imagens. Formatos aceitos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)</div>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Preview Container -->
                        <div id="images-container" class="row g-2 mt-2">
                            <!-- Preview images will be inserted here -->
                        </div>
                    </div>

                    <!-- Informações Adicionais (Marca removida) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Modelo</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model') }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dimensões -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="weight" class="form-label">Peso (kg)</label>
                                <input type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror" 
                                       id="weight" name="weight" value="{{ old('weight') }}">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="length" class="form-label">Comprimento (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('length') is-invalid @enderror" 
                                       id="length" name="length" value="{{ old('length') }}">
                                @error('length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="width" class="form-label">Largura (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('width') is-invalid @enderror" 
                                       id="width" name="width" value="{{ old('width') }}">
                                @error('width')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="height" class="form-label">Altura (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('height') is-invalid @enderror" 
                                       id="height" name="height" value="{{ old('height') }}">
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Dicas</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Informações Importantes</h6>
                    <ul class="mb-0">
                        <li>O SKU será gerado automaticamente se não informado</li>
                        <li>Selecione pelo menos uma categoria</li>
                        <li>O preço B2B é opcional</li>
                        <li>As imagens devem ter boa qualidade</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
    @endsection

    <!-- Modal para selecionar imagens do álbum -->
    <div class="modal fade" id="albumImagesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selecionar Imagem do Álbum</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="album-images-container" class="row g-3">
                        <div class="col-12 text-center py-4">
                            <i class="bi bi-hourglass-split"></i> Carregando álbuns...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        // remove preselected album image before form submit
        document.querySelectorAll('.remove-preselected').forEach(function(btn){
            btn.addEventListener('click', function(e){
                const wrapper = btn.closest('[data-album-image-id]');
                if (!wrapper) return;
                // remove the hidden input and the preview
                const input = wrapper.querySelector('input[name="existing_image_ids[]"]');
                if (input) input.remove();
                wrapper.remove();
                // if list becomes empty hide container
                const list = document.getElementById('preselected-images-list');
                if (list && list.children.length === 0) {
                    const container = document.getElementById('preselected-images');
                    if (container) container.classList.add('d-none');
                }
            });
        });

        // Funcionalidade do álbum de imagens
        const selectFromAlbumBtn = document.getElementById('select-from-album-btn');
        const albumImagesModal = document.getElementById('albumImagesModal');
        
        if (selectFromAlbumBtn && albumImagesModal) {
            selectFromAlbumBtn.addEventListener('click', function() {
                loadAlbumImages();
                const modal = new bootstrap.Modal(albumImagesModal);
                modal.show();
            });
        }

        function loadAlbumImages() {
            const container = document.getElementById('album-images-container');
            container.innerHTML = '<div class="col-12 text-center py-4"><i class="bi bi-hourglass-split"></i> Carregando álbuns...</div>';
            
            fetch('{{ route("admin.products.album-images") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderAlbumImages(data.albums);
                    } else {
                        container.innerHTML = '<div class="col-12 text-center py-4 text-danger">Erro ao carregar álbuns: ' + (data.message || 'Erro desconhecido') + '</div>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    container.innerHTML = '<div class="col-12 text-center py-4 text-danger">Erro ao carregar álbuns. Verifique o console.</div>';
                });
        }

        function renderAlbumImages(albums) {
            const container = document.getElementById('album-images-container');
            container.innerHTML = '';
            
            if (albums.length === 0) {
                container.innerHTML = '<div class="col-12 text-center py-4 text-muted">Nenhum álbum encontrado</div>';
                return;
            }
            
            albums.forEach(album => {
                if (album.images.length === 0) return;
                
                const albumCol = document.createElement('div');
                albumCol.className = 'col-12';
                albumCol.innerHTML = `
                    <h6 class="mb-3">
                        <i class="bi bi-folder me-2"></i>${album.title}
                        <span class="badge bg-secondary ms-2">${album.images.length} imagens</span>
                    </h6>
                    <div class="row g-2" id="album-${album.id}-images">
                        ${album.images.map(image => `
                            <div class="col-md-2 col-sm-3 col-4">
                                <div class="position-relative album-image-item" style="cursor: pointer;" 
                                     data-image-id="${image.id}" 
                                     data-image-url="${image.url}" 
                                     data-image-path="${image.path}"
                                     onclick="selectAlbumImage(${image.id}, '${image.url}', '${image.path}')">
                                    <img src="${image.url}" class="img-thumbnail w-100" style="height: 80px; object-fit: cover;">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 text-white" style="opacity: 0; transition: opacity 0.2s;">
                                        <i class="bi bi-check-circle fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
                container.appendChild(albumCol);
            });
        }

        window.selectAlbumImage = function(imageId, imageUrl, imagePath) {
            // Verificar se a imagem já foi selecionada
            const existingImage = document.querySelector(`[data-album-image-id="${imageId}"]`);
            if (existingImage) {
                alert('Esta imagem já foi selecionada!');
                return;
            }
            
            // Adicionar à lista de imagens pré-selecionadas
            const preselectedContainer = document.getElementById('preselected-images');
            const preselectedList = document.getElementById('preselected-images-list');
            
            if (preselectedContainer) {
                preselectedContainer.classList.remove('d-none');
            }
            
            const imageItem = document.createElement('div');
            imageItem.className = 'position-relative border rounded';
            imageItem.setAttribute('data-album-image-id', imageId);
            imageItem.style.cssText = 'width:100px; height:100px; overflow:hidden';
            imageItem.innerHTML = `
                <img src="${imageUrl}" class="w-100 h-100" style="object-fit:cover;" loading="lazy">
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute remove-preselected" style="top:6px; right:6px; padding:4px 6px">Remover</button>
                <input type="hidden" name="existing_image_ids[]" value="${imageId}">
            `;
            
            if (preselectedList) {
                preselectedList.appendChild(imageItem);
            }
            
            // Adicionar evento de remover ao novo botão
            const removeBtn = imageItem.querySelector('.remove-preselected');
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    const wrapper = this.closest('[data-album-image-id]');
                    if (!wrapper) return;
                    const input = wrapper.querySelector('input[name="existing_image_ids[]"]');
                    if (input) input.remove();
                    wrapper.remove();
                    
                    const list = document.getElementById('preselected-images-list');
                    if (list && list.children.length === 0) {
                        const container = document.getElementById('preselected-images');
                        if (container) container.classList.add('d-none');
                    }
                });
            }
            
            // Fechar modal
            const modal = bootstrap.Modal.getInstance(albumImagesModal);
            if (modal) {
                modal.hide();
            }
            
            // Feedback visual
            showToast('success', 'Imagem selecionada com sucesso!', 'Sucesso');
        };
    });
</script>

<script src="{{ asset('js/admin-product-edit.js') }}?v={{ filemtime(public_path('js/admin-product-edit.js')) }}"></script>
@endpush
