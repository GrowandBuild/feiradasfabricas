@extends('admin.layouts.app')

@section('title', 'Editar Produto')
@section('page-title', 'Editar Produto')
@section('page-subtitle')
    <p class="text-muted mb-0">Atualize as informações do produto</p>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-pencil-square me-2" style="color: var(--accent-color);"></i>
                    <h5 class="card-title mb-0">Informações do Produto</h5>
                </div>
                <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-bs-toggle="modal"
                        data-bs-target="#variationsModal">
                    <i class="bi bi-list-ul me-1"></i> Gerenciar Variações
                </button>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informações Básicas -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3" style="color: var(--accent-color);">
                                <i class="bi bi-info-circle me-2"></i>Informações Básicas
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-tag me-1"></i>Nome do Produto *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $product->name) }}" 
                                       placeholder="Digite o nome do produto" required>
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sku" class="form-label">
                                    <i class="bi bi-upc-scan me-1"></i>SKU *
                                </label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku', $product->sku) }}" 
                                       placeholder="Ex: PROD-001" required>
                                @error('sku')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-file-text me-1"></i>Descrição *
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Descreva as características do produto" required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Preços e Estoque -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3" style="color: var(--accent-color);">
                                <i class="bi bi-currency-dollar me-2"></i>Preços e Estoque
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    <i class="bi bi-cart me-1"></i>Preço (B2C) *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: var(--accent-color); color: white;">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $product->price) }}" 
                                           placeholder="0,00" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="b2b_price" class="form-label">
                                    <i class="bi bi-building me-1"></i>Preço (B2B)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary text-white">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                    <input type="number" step="0.01" class="form-control @error('b2b_price') is-invalid @enderror" 
                                           id="b2b_price" name="b2b_price" value="{{ old('b2b_price', $product->b2b_price) }}"
                                           placeholder="0,00">
                                </div>
                                <small class="text-muted">Preço especial para empresas</small>
                                @error('b2b_price')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">
                                    <i class="bi bi-receipt me-1"></i>Preço de Custo
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-info text-white">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                    <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                                           id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}"
                                           placeholder="0,00">
                                </div>
                                <small class="text-muted">Para controle de margem</small>
                                @error('cost_price')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">
                                    <i class="bi bi-boxes me-1"></i>Quantidade em Estoque *
                                </label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                       placeholder="0" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_stock" class="form-label">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Estoque Mínimo *
                                </label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror" 
                                       id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" 
                                       placeholder="0" required>
                                <small class="text-muted">Alerta quando estoque estiver baixo</small>
                                @error('min_stock')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Categorias -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3" style="color: var(--accent-color);">
                                <i class="bi bi-tags me-2"></i>Categorias *
                            </h6>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-4 col-lg-3 mb-2">
                                    <div class="form-check form-check-card">
                                        <input class="form-check-input" type="checkbox" 
                                               name="categories[]" value="{{ $category->id }}" 
                                               id="category_{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', $productCategories)) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center" for="category_{{ $category->id }}">
                                            <span class="badge bg-light text-dark me-2">
                                                <i class="bi bi-tag"></i>
                                            </span>
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Selecione pelo menos uma categoria</small>
                        @error('categories')
                            <div class="text-danger small mt-2">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Marca -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3" style="color: var(--accent-color);">
                                <i class="bi bi-tag me-2"></i>Marca
                            </h6>
                        </div>
                    </div>
                    <!-- Sugestões de Atributos (pelo Departamento) -->
                    <div class="row mb-4" id="dept-attributes-section">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3" style="color: var(--accent-color);">
                                <i class="bi bi-list-ul me-2"></i>Sugestões de Atributos (pelo Departamento)
                            </h6>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3" id="deptAttributesPanel">
                                <p class="text-muted" id="deptAttributesLoading">Carregando atributos do departamento...</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="brand_id" class="form-label">
                            <i class="bi bi-tag-fill me-1"></i>Marca do Produto
                        </label>
                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                id="brand_id" name="brand_id">
                            <option value="">— Nenhuma marca selecionada —</option>
                            @php
                                $brands = \App\Models\Brand::active()->orderBy('sort_order')->orderBy('name')->get();
                            @endphp
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Selecione a marca do produto (opcional)</small>
                        @error('brand_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3" style="color: var(--accent-color);">
                                <i class="bi bi-images me-2"></i>Imagens do Produto
                            </h6>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="images" class="form-label">
                            <i class="bi bi-cloud-upload me-1"></i>Adicionar Novas Imagens
                        </label>
                        <div class="dropzone p-2 rounded" id="images-dropzone">
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Você pode selecionar múltiplas imagens. Formatos aceitos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)
                            </div>
                        </div>
                        @error('images')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        
                        <!-- Container de Imagens -->
                        <div class="mt-3">
                            <label class="form-label">Imagens do Produto</label>
                            <div class="row" id="images-container">
                                @if($product->images && count($product->images) > 0)
                                    @foreach($product->images as $index => $image)
                                        <div class="col-md-3 mb-2 image-item" data-image="{{ $image }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image) }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 100%; height: 100px; object-fit: cover;">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                                        onclick="removeExistingImage(this, '{{ $image }}')">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <!-- Campos hidden para manter as imagens existentes -->
                            <div id="existing-images-inputs">
                                @if($product->images && count($product->images) > 0)
                                    @foreach($product->images as $image)
                                        <input type="hidden" name="existing_images[]" value="{{ $image }}" class="existing-image-input">
                                    @endforeach
                                @endif
                            </div>
                            <!-- Campo para indicar quando todas as imagens foram removidas -->
                            <input type="hidden" name="all_images_removed" value="0" id="all-images-removed">
                        </div>
                    </div>

                    <!-- Informações Adicionais (Marca removida) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Modelo</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model', $product->model) }}">
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
                                       id="weight" name="weight" value="{{ old('weight', $product->weight) }}">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="length" class="form-label">Comprimento (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('length') is-invalid @enderror" 
                                       id="length" name="length" value="{{ old('length', $product->length) }}">
                                @error('length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="width" class="form-label">Largura (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('width') is-invalid @enderror" 
                                       id="width" name="width" value="{{ old('width', $product->width) }}">
                                @error('width')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="height" class="form-label">Altura (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('height') is-invalid @enderror" 
                                       id="height" name="height" value="{{ old('height', $product->height) }}">
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Produto Ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_featured" name="is_featured" value="1"
                                           {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Produto em Destaque
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Campos marcados com * são obrigatórios
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Atualizar Produto
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-info-square me-2" style="color: var(--accent-color);"></i>
                <h5 class="card-title mb-0">Informações do Produto</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-upc-scan text-muted me-2"></i>
                        <div>
                            <small class="text-muted">SKU</small>
                            <div class="fw-semibold">{{ $product->sku }}</div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-plus text-muted me-2"></i>
                        <div>
                            <small class="text-muted">Criado em</small>
                            <div class="fw-semibold">{{ $product->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-check text-muted me-2"></i>
                        <div>
                            <small class="text-muted">Última atualização</small>
                            <div class="fw-semibold">{{ $product->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-toggle-on text-muted me-2"></i>
                        <div>
                            <small class="text-muted">Status</small>
                            <div>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                    <i class="bi bi-{{ $product->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($product->is_featured)
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-star text-muted me-2"></i>
                            <div>
                                <span class="badge bg-warning">
                                    <i class="bi bi-star-fill me-1"></i>Produto em Destaque
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-lightbulb text-warning me-2"></i>
                <h5 class="card-title mb-0">Dicas</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle me-1"></i>Informações Importantes
                    </h6>
                    <ul class="mb-0 small">
                        <li class="mb-1">O SKU deve ser único para cada produto</li>
                        <li class="mb-1">Selecione pelo menos uma categoria</li>
                        <li class="mb-1">O preço B2B é opcional</li>
                        <li class="mb-1">As imagens devem ter boa qualidade</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
    @include('admin.products.modals.variations')
@endpush

 
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de imagens, preços e atributos carregado');

    const productId = @json($product->id ?? null);
    const CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');

    // ========== FUNCIONALIDADE DE CÁLCULO DE PREÇOS EM TEMPO REAL ==========
    const priceInput = document.getElementById('price');
    const b2bPriceInput = document.getElementById('b2b_price');
    const costPriceInput = document.getElementById('cost_price');

    function normalizePrice(value) {
        if (!value && value !== 0) return null;
        let cleanValue = value.toString().trim();
        cleanValue = cleanValue.replace(/[^0-9,.-]/g, '');
        cleanValue = cleanValue.replace('\u00a0', '').replace('\u00a0', '');
        if (cleanValue === '' || cleanValue === ',') return null;
        const commaCount = (cleanValue.match(/,/g) || []).length;
        const dotCount = (cleanValue.match(/\./g) || []).length;
        if (commaCount > 1 || dotCount > 1) {
            cleanValue = cleanValue.replace(/\./g, '');
            cleanValue = cleanValue.replace(/,/g, '.');
        } else if (commaCount === 1 && dotCount === 0) {
            cleanValue = cleanValue.replace(/,/g, '.');
        } else if (dotCount === 1 && commaCount === 0) {
            cleanValue = cleanValue.replace(/\./g, '.');
        } else if (commaCount === 1 && dotCount === 1) {
            const commaIndex = cleanValue.indexOf(',');
            const dotIndex = cleanValue.indexOf('.');
            if (commaIndex > dotIndex) {
                cleanValue = cleanValue.replace(/\./g, '');
                cleanValue = cleanValue.replace(/,/g, '.');
            } else {
                cleanValue = cleanValue.replace(/,/g, '');
            }
        } else {
            cleanValue = cleanValue.replace(/\./g, '');
        }
        const parsed = parseFloat(cleanValue);
        return isNaN(parsed) ? null : parsed;
    }

    function formatCurrency(value) {
        if (value === null || value === undefined || value === '') return '';
        const numberValue = typeof value === 'number' ? value : parseFloat(value);
        if (isNaN(numberValue)) return '';
        return numberValue.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateCostPriceFromServer(costPrice) {
        if (!productId || !costPrice || costPrice <= 0) return;
        const loaderClass = 'is-loading';
        costPriceInput.classList.add(loaderClass);
        costPriceInput.disabled = true;
        fetch(`/admin/products/${productId}/update-cost-price`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ cost_price: costPrice })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.product) {
                const { cost_price, b2c_price, b2b_price } = data.product;
                if (costPriceInput) costPriceInput.value = cost_price ?? formatCurrency(costPrice);
                if (priceInput && b2c_price) priceInput.value = b2c_price;
                if (b2bPriceInput && b2b_price) b2bPriceInput.value = b2b_price;
                costPriceInput.classList.add('border-success');
                setTimeout(() => costPriceInput.classList.remove('border-success'), 2000);
            } else {
                throw new Error(data.message || 'Erro ao atualizar preços');
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar custo:', error);
            alert('Erro ao atualizar preços com base no custo. Tente novamente.');
        })
        .finally(() => {
            costPriceInput.disabled = false;
            costPriceInput.classList.remove(loaderClass);
        });
    }

    function updateB2BPrice() {
        if (priceInput && b2bPriceInput && priceInput.value) {
            const normalized = normalizePrice(priceInput.value);
            if (normalized !== null) b2bPriceInput.value = formatCurrency(normalized * 0.9);
        }
    }

    function calculatePriceFromCost() {
        const normalizedCost = normalizePrice(costPriceInput.value);
        if (normalizedCost !== null) updateCostPriceFromServer(normalizedCost);
    }

    if (priceInput) priceInput.addEventListener('blur', function() { this.value = formatCurrency(normalizePrice(this.value)); updateB2BPrice(); });
    if (costPriceInput) costPriceInput.addEventListener('blur', function() { const normalizedCost = normalizePrice(this.value); if (normalizedCost !== null && normalizedCost > 0) { this.value = formatCurrency(normalizedCost); updateCostPriceFromServer(normalizedCost); } else { this.value = ''; } });
    if (b2bPriceInput) b2bPriceInput.addEventListener('blur', function() { this.value = formatCurrency(normalizePrice(this.value)); });

    const calculatePriceBtn = document.createElement('button');
    calculatePriceBtn.type = 'button';
    calculatePriceBtn.className = 'btn btn-outline-info btn-sm mt-2';
    calculatePriceBtn.innerHTML = '<i class="bi bi-calculator me-1"></i>Calcular Preço (30% markup)';
    calculatePriceBtn.onclick = calculatePriceFromCost;
    if (costPriceInput && costPriceInput.parentNode) costPriceInput.parentNode.appendChild(calculatePriceBtn);

    // ========== FUNCIONALIDADE DE IMAGENS ==========
    const imageInput = document.getElementById('images');
    const container = document.getElementById('images-container');
    if (imageInput && container) {
        console.log('✅ Campo de imagens e container encontrados');
        let selectedFiles = [];
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (!files || files.length === 0) return;
            const newImagePreviews = container.querySelectorAll('.new-image-preview');
            newImagePreviews.forEach(preview => preview.remove());
            selectedFiles = Array.from(files);
            selectedFiles.forEach((file, index) => {
                if (file.type.startsWith('image/') || file.name.toLowerCase().endsWith('.avif')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-2 new-image-preview';
                        col.setAttribute('data-file-name', file.name);
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" 
                                     class="img-thumbnail" 
                                     style="width: 100%; height: 100px; object-fit: cover; cursor: pointer;"
                                     alt="Preview ${file.name}">
                                <span class="badge bg-success position-absolute top-0 start-0">Nova</span>
                                <button type="button" 
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                        onclick="removeNewImagePreview(this, '${file.name}')">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `;
                        container.appendChild(col);
                    };
                    reader.onerror = function() { console.error('❌ Erro ao ler arquivo:', file.name); };
                    reader.readAsDataURL(file);
                } else {
                    alert(`O arquivo "${file.name}" não é uma imagem válida.`);
                }
            });
        });
    } else {
        if (!imageInput) console.error('❌ Campo de imagens não encontrado!');
        if (!container) console.error('❌ Container de imagens não encontrado!');
    }

    window.removeNewImagePreview = function(button, fileName) {
        if (!confirm('Tem certeza que deseja remover esta imagem do upload?')) return;
        const preview = button.closest('.new-image-preview');
        if (preview) {
            preview.remove();
            const imageInput = document.getElementById('images');
            if (imageInput && imageInput.files) {
                const dt = new DataTransfer();
                const files = Array.from(imageInput.files);
                const filteredFiles = files.filter(file => file.name !== fileName);
                filteredFiles.forEach(file => dt.items.add(file));
                imageInput.files = dt.files;
            }
        }
    };

    console.log('✅ Funcionalidades de preços e imagens inicializadas!');

    // ========== MÓDULO DE ATRIBUTOS DO DEPARTAMENTO ==========
    try {
        const deptAttributesPanel = document.getElementById('deptAttributesPanel');
        let currentDepartment = @json($product->department_id ?? null);

        function renderAttributes(data) {
            if (!deptAttributesPanel) return;
            deptAttributesPanel.innerHTML = '';
            if (!data || !data.attributes || data.attributes.length === 0) {
                deptAttributesPanel.innerHTML = '<p class="text-muted">Nenhum atributo encontrado para este departamento.</p>';
                return;
            }
            data.attributes.forEach(attr => {
                const group = document.createElement('div');
                group.className = 'mb-3';
                const title = document.createElement('label');
                title.className = 'form-label fw-semibold';
                title.textContent = attr.name || attr.key;
                group.appendChild(title);
                const wrap = document.createElement('div');
                wrap.className = 'd-flex flex-wrap gap-2';
                attr.values.forEach(v => {
                    const id = `dept-attr-${attr.key}-${v.value}`.replace(/[^a-zA-Z0-9-_]/g, '_');
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    div.style.minWidth = '160px';
                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.className = 'form-check-input dept-attr-checkbox';
                    input.id = id;
                    input.dataset.type = attr.key;
                    input.dataset.value = v.value;
                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = id;
                    if (attr.key === 'color' && v.hex) {
                        label.innerHTML = `<span class="me-2" style="display:inline-block;width:18px;height:14px;background:${v.hex};border:1px solid #ddd;vertical-align:middle;"></span> ${v.value}`;
                    } else {
                        label.textContent = v.value;
                    }
                    div.appendChild(input);
                    div.appendChild(label);
                    wrap.appendChild(div);
                });
                group.appendChild(wrap);
                deptAttributesPanel.appendChild(group);
            });
            const actions = document.createElement('div');
            actions.className = 'd-flex gap-2 mt-2';
            const addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.className = 'btn btn-sm btn-primary';
            addBtn.textContent = 'Adicionar valores selecionados como variações';
            addBtn.addEventListener('click', applySelectedAttributes);
            const openModalBtn = document.createElement('button');
            openModalBtn.type = 'button';
            openModalBtn.className = 'btn btn-sm btn-outline-secondary';
            openModalBtn.textContent = 'Abrir Gerenciador de Variações';
            openModalBtn.addEventListener('click', function() { const modalEl = document.getElementById('variationsModal'); if (modalEl) { const modal = new bootstrap.Modal(modalEl); modal.show(); } });
            const refreshBtn = document.createElement('button');
            refreshBtn.type = 'button';
            refreshBtn.className = 'btn btn-sm btn-outline-info';
            refreshBtn.textContent = 'Atualizar atributos';
            refreshBtn.addEventListener('click', function(){ fetchAndRenderForDepartment(currentDepartment); });
            actions.appendChild(addBtn); actions.appendChild(refreshBtn); actions.appendChild(openModalBtn); deptAttributesPanel.appendChild(actions);
        }

        function applySelectedAttributes() {
            const checkboxes = Array.from(document.querySelectorAll('.dept-attr-checkbox')).filter(cb => cb.checked);
            if (checkboxes.length === 0) { alert('Selecione ao menos um valor para adicionar.'); return; }
            if (!confirm(`Adicionar ${checkboxes.length} valor(es) selecionado(s) como variações deste produto?`)) return;
            const groups = {};
            checkboxes.forEach(cb => { const type = cb.dataset.type; const value = cb.dataset.value; if (!groups[type]) groups[type] = []; groups[type].push(value); });
            const keys = Object.keys(groups);
            const arrays = keys.map(k => groups[k].map(v => ({ key: k, value: v })));
            function cartesianProduct(arr) { return arr.reduce((a, b) => a.flatMap(d => b.map(e => d.concat([e]))), [[]]); }
            function slugify(str) { return String(str || '').toLowerCase().normalize('NFD').replace(/[^\w\s-]/g, '').replace(/\s+/g, '_').replace(/[^a-z0-9_-]/g, '').replace(/^_+|_+$/g, ''); }
            const combos = cartesianProduct(arrays).map(combo => { const attrs = {}; combo.forEach(c => { const slug = slugify(c.key); attrs[slug] = c.value; }); return { attributes: attrs }; });
            if (combos.length > 300) { if (!confirm(`Serão criadas ${combos.length} variações. Continuar?`)) return; }
            fetch(`/admin/products/${productId}/variations/bulk-add`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, body: JSON.stringify({ combos }) })
            .then(r => r.json()).then(data => { if (data && data.success) { alert(`Operação concluída. ${data.created} nova(s) variação(ões) criada(s).`); const variationsModalEl = document.getElementById('variationsModal'); if (variationsModalEl && bootstrap.Modal.getInstance(variationsModalEl)) { const prodIdInput = document.getElementById('variationsProductId'); if (prodIdInput) loadVariations(prodIdInput.value); } } else { console.error('bulk-add failed', data); alert('Erro ao criar variações em lote. Veja console para detalhes.'); } }).catch(err => { console.error(err); alert('Erro ao criar variações. Veja console para detalhes.'); });
        }

        function fetchAndRenderForDepartment(dept) {
            if (!dept) { deptAttributesPanel.innerHTML = '<p class="text-muted">Produto sem departamento definido. Atribua um departamento para obter sugestões.</p>'; return; }
            deptAttributesPanel.innerHTML = '<p class="text-muted">Carregando atributos do departamento...</p>';
            fetch(`/admin/attributes/list?department=${dept}`, { headers: { 'Accept': 'application/json' } }).then(response => response.json()).then(data => renderAttributes(data)).catch(error => { console.error('Erro ao carregar atributos do departamento:', error); deptAttributesPanel.innerHTML = '<p class="text-muted text-danger">Erro ao carregar atributos. Verifique o console.</p>'; });
        }

        if (deptAttributesPanel) fetchAndRenderForDepartment(currentDepartment);

        function bindDepartmentChange() {
            const selectors = [document.querySelector('select[name="department_id"]'), document.getElementById('qpDepartment'), document.getElementById('qpDeptCombo'), document.getElementById('department_id')];
            selectors.forEach(el => { if (!el) return; const handler = function(e) { let val = el.value || null; if (el.id === 'qpDeptCombo') { const sel = document.getElementById('qpDepartment'); if (sel && sel.value) val = sel.value; } if (val === null || val === '') { currentDepartment = null; fetchAndRenderForDepartment(null); return; } if (val == currentDepartment) return; currentDepartment = val; fetchAndRenderForDepartment(currentDepartment); }; el.addEventListener('change', handler); el.addEventListener('input', handler); });
        }

        bindDepartmentChange();

        (function startDeptPoll(){ let last = currentDepartment; setInterval(function(){ const candidates = [document.querySelector('select[name="department_id"]'), document.getElementById('qpDepartment'), document.getElementById('qpDeptCombo'), document.getElementById('department_id')]; let found = null; for (const el of candidates) { if (!el) continue; let v = el.value || null; if (el.id === 'qpDeptCombo') { const sel = document.getElementById('qpDepartment'); if (sel && sel.value) v = sel.value; } if (v) { found = v; break; } } if ((found || null) !== last) { last = found || null; currentDepartment = last; fetchAndRenderForDepartment(currentDepartment); } }, 800); })();
    } catch (e) {
        console.error('Erro no módulo de atributos do departamento:', e);
    }
});

function removeExistingImage(button, imagePath) {
    if (confirm('Tem certeza que deseja remover esta imagem?')) {
        const imageItem = button.closest('.image-item');
        if (imageItem) imageItem.remove();
        const existingInputs = document.querySelectorAll('.existing-image-input');
        existingInputs.forEach(input => { if (input.value === imagePath) input.remove(); });
        const remainingInputs = document.querySelectorAll('.existing-image-input');
        if (remainingInputs.length === 0) {
            const allRemoved = document.getElementById('all-images-removed');
            if (allRemoved) allRemoved.value = '1';
        }
        console.log('Imagem removida:', imagePath);
        console.log('Inputs restantes:', remainingInputs.length);
    }
}

</script>
@endsection
