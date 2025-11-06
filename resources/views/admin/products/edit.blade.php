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
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-pencil-square me-2" style="color: var(--accent-color);"></i>
                <h5 class="card-title mb-0">Informações do Produto</h5>
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

                    <!-- Imagens -->
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
                        <input type="file" class="form-control @error('images') is-invalid @enderror" 
                               id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Você pode selecionar múltiplas imagens. Formatos aceitos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)
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

                    <!-- Informações Adicionais -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Marca</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand', $product->brand) }}">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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

<script>
// Aguardar o DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de imagens e preços carregado');
    
    // ========== FUNCIONALIDADE DE CÁLCULO DE PREÇOS EM TEMPO REAL ==========
    
    // Elementos dos campos de preço
    const priceInput = document.getElementById('price');
    const b2bPriceInput = document.getElementById('b2b_price');
    const costPriceInput = document.getElementById('cost_price');
    
    // Função para calcular preço baseado em markup
    function calculatePriceFromMarkup(costPrice, markupPercent) {
        return costPrice * (1 + markupPercent / 100);
    }
    
    // Função para calcular preço B2B (10% desconto padrão)
    function calculateB2BPrice(price) {
        return price * 0.9;
    }
    
    // Função para calcular markup baseado no preço de custo e venda
    function calculateMarkup(costPrice, salePrice) {
        if (costPrice > 0) {
            return ((salePrice - costPrice) / costPrice) * 100;
        }
        return 0;
    }
    
    // Função para formatar valores monetários
    function formatCurrency(value) {
        return parseFloat(value).toFixed(2);
    }
    
    // Função para atualizar preço B2B automaticamente
    function updateB2BPrice() {
        if (priceInput && b2bPriceInput && priceInput.value) {
            const newB2BPrice = calculateB2BPrice(parseFloat(priceInput.value));
            b2bPriceInput.value = formatCurrency(newB2BPrice);
        }
    }
    
    // Função para calcular preço baseado no custo e markup
    function calculatePriceFromCost() {
        if (costPriceInput && priceInput && costPriceInput.value) {
            const costPrice = parseFloat(costPriceInput.value);
            const markupPercent = 30; // Markup padrão de 30%
            const newPrice = calculatePriceFromMarkup(costPrice, markupPercent);
            priceInput.value = formatCurrency(newPrice);
            updateB2BPrice();
        }
    }
    
    // Event listeners para cálculos automáticos
    if (priceInput) {
        priceInput.addEventListener('input', updateB2BPrice);
        priceInput.addEventListener('blur', function() {
            this.value = formatCurrency(this.value);
            updateB2BPrice();
        });
    }
    
    if (costPriceInput) {
        costPriceInput.addEventListener('input', function() {
            // Só calcula automaticamente se o campo de preço estiver vazio
            if (!priceInput.value || priceInput.value == 0) {
                calculatePriceFromCost();
            }
        });
        costPriceInput.addEventListener('blur', function() {
            this.value = formatCurrency(this.value);
        });
    }
    
    if (b2bPriceInput) {
        b2bPriceInput.addEventListener('blur', function() {
            this.value = formatCurrency(this.value);
        });
    }
    
    // Botão para calcular preço baseado no custo
    const calculatePriceBtn = document.createElement('button');
    calculatePriceBtn.type = 'button';
    calculatePriceBtn.className = 'btn btn-outline-info btn-sm mt-2';
    calculatePriceBtn.innerHTML = '<i class="bi bi-calculator me-1"></i>Calcular Preço (30% markup)';
    calculatePriceBtn.onclick = calculatePriceFromCost;
    
    // Adicionar botão após o campo de preço de custo
    if (costPriceInput && costPriceInput.parentNode) {
        costPriceInput.parentNode.appendChild(calculatePriceBtn);
    }
    
    // ========== FUNCIONALIDADE DE IMAGENS ==========
    // Seguindo a mesma lógica simples dos selos de categorias
    
    const imageInput = document.getElementById('images');
    const container = document.getElementById('images-container');
    
    if (imageInput && container) {
        console.log('✅ Campo de imagens e container encontrados');
        
        // Armazenar referência aos arquivos selecionados
        let selectedFiles = [];
        
        // Adicionar preview das novas imagens selecionadas
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            
            if (!files || files.length === 0) {
                return;
            }
            
            console.log('📸 Arquivos selecionados:', files.length);
            
            // Limpar previews anteriores de novas imagens
            const newImagePreviews = container.querySelectorAll('.new-image-preview');
            newImagePreviews.forEach(preview => preview.remove());
            
            // Adicionar novos arquivos à lista
            selectedFiles = Array.from(files);
            
            // Adicionar preview das novas imagens
            selectedFiles.forEach((file, index) => {
                console.log(`📷 Processando arquivo ${index + 1}:`, file.name);
                
                if (file.type.startsWith('image/')) {
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
                        console.log('✅ Preview adicionado para:', file.name);
                    };
                    
                    reader.onerror = function() {
                        console.error('❌ Erro ao ler arquivo:', file.name);
                    };
                    
                    reader.readAsDataURL(file);
                } else {
                    console.warn('⚠️ Arquivo não é uma imagem:', file.name);
                    alert(`O arquivo "${file.name}" não é uma imagem válida.`);
                }
            });
        });
    } else {
        if (!imageInput) {
            console.error('❌ Campo de imagens não encontrado!');
        }
        if (!container) {
            console.error('❌ Container de imagens não encontrado!');
        }
    }
    
    // Função para remover preview de nova imagem
    window.removeNewImagePreview = function(button, fileName) {
        if (confirm('Tem certeza que deseja remover esta imagem do upload?')) {
            const preview = button.closest('.new-image-preview');
            if (preview) {
                preview.remove();
                
                // Remover o arquivo do input usando DataTransfer
                const imageInput = document.getElementById('images');
                if (imageInput && imageInput.files) {
                    const dt = new DataTransfer();
                    const files = Array.from(imageInput.files);
                    
                    // Remover o arquivo pelo nome
                    const filteredFiles = files.filter(file => file.name !== fileName);
                    
                    // Adicionar os arquivos restantes ao DataTransfer
                    filteredFiles.forEach(file => dt.items.add(file));
                    
                    // Atualizar o input
                    imageInput.files = dt.files;
                    
                    console.log('🗑️ Imagem removida do upload. Arquivos restantes:', dt.files.length);
                }
            }
        }
    };
    
    console.log('✅ Funcionalidades de preços e imagens inicializadas!');
});

function removeExistingImage(button, imagePath) {
    if (confirm('Tem certeza que deseja remover esta imagem?')) {
        // Remove o elemento visual
        const imageItem = button.closest('.image-item');
        imageItem.remove();
        
        // Remove o input hidden correspondente
        const existingInputs = document.querySelectorAll('.existing-image-input');
        existingInputs.forEach(input => {
            if (input.value === imagePath) {
                input.remove();
            }
        });
        
        // Verificar se todas as imagens foram removidas
        const remainingInputs = document.querySelectorAll('.existing-image-input');
        if (remainingInputs.length === 0) {
            document.getElementById('all-images-removed').value = '1';
        }
        
        // Debug: verificar se o input foi removido
        console.log('Imagem removida:', imagePath);
        console.log('Inputs restantes:', remainingInputs.length);
    }
}

</script>
@endsection
