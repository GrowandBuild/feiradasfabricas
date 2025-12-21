@extends('admin.layouts.app')

@section('title', 'Editar Produto')
@section('page-title', 'Editar Produto')
@section('page-subtitle')
    <p class="text-muted mb-0">Atualize as informações do produto</p>
@endsection

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin-product-edit.css') }}">

@php
    $productCategories = $product->categories->pluck('id')->toArray();
    $initialImages = [];
    if (!empty($product->images)) {
        foreach ($product->images as $idx => $img) {
            $initialImages[] = [
                'id' => 'existing-' . $idx,
                'path' => $img,
                'preview' => strpos($img,'http')===0 ? $img : asset('storage/'.$img),
                'existing' => true,
                'isPrimary' => $idx === 0,
                'file' => null
            ];
        }
    }
    $initialPrimaryImage = (!empty($product->images) && count($product->images) > 0) ? $product->images[0] : '';
@endphp

<style>
/* Layout Principal - Estrutura Simples e Direta */
.product-edit-wrapper {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: start;
}

.product-edit-form {
    min-width: 0;
}

.product-edit-sidebar {
    position: sticky;
    top: 20px;
}

@media (max-width: 1200px) {
    .product-edit-wrapper {
        grid-template-columns: 1fr;
    }
    .product-edit-sidebar {
        position: static;
    }
}

/* Estilos Modernos para Inputs e Formulários - COM !IMPORTANT */
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
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), 0 8px 25px rgba(59, 130, 246, 0.2) !important;
    outline: none !important;
    transform: translateY(-2px) !important;
}

.form-control:hover, .form-select:hover {
    border-color: #3b82f6 !important;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15) !important;
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
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
    border: 2px solid #e5e7eb !important;
    border-right: none !important;
    font-weight: 700 !important;
    color: #6b7280 !important;
    padding: 14px 18px !important;
}

.input-group .form-control {
    border-left: none !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

.input-group:focus-within {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), 0 8px 25px rgba(59, 130, 246, 0.2) !important;
}

.input-group:focus-within .input-group-text {
    border-color: #3b82f6 !important;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%) !important;
    color: #3b82f6 !important;
}

/* Estilos para Textarea */
textarea.form-control {
    resize: vertical !important;
    min-height: 140px !important;
    line-height: 1.6 !important;
}

textarea.form-control:focus {
    min-height: 160px !important;
}

/* Estilos para Checkbox */
.form-check-input {
    width: 22px !important;
    height: 22px !important;
    border: 2px solid #d1d5db !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
}

.form-check-input:checked {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3) !important;
}

.form-check-input:hover {
    border-color: #3b82f6 !important;
    transform: scale(1.1) !important;
}

.form-check-label {
    font-weight: 600 !important;
    color: #4b5563 !important;
    cursor: pointer !important;
    margin-left: 10px !important;
}

/* Cards Modernos */
.card-modern {
    border: none !important;
    border-radius: 16px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
    background: #ffffff !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    margin-bottom: 24px !important;
}

.card-modern:hover {
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16) !important;
    transform: translateY(-4px) !important;
}

.card-modern .card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
    border: none !important;
    padding: 24px 28px !important;
    border-bottom: 1px solid #e5e7eb !important;
}

.card-modern .card-header h6 {
    color: #1f2937 !important;
    font-weight: 800 !important;
    font-size: 18px !important;
    margin: 0 !important;
}

.card-modern .card-body {
    padding: 28px !important;
}

/* Botões Modernos */
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
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4) !important;
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

.btn-accent {
    background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), black 12%) 100%) !important;
    color: white !important;
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4) !important;
}

/* Animações */
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

.card-modern {
    animation: fadeInUp 0.6s ease-out !important;
}

/* Responsividade */
@media (max-width: 768px) {
    .form-control, .form-select {
        padding: 16px 18px !important;
        font-size: 16px !important;
    }
    
    .card-modern .card-body {
        padding: 24px !important;
    }
    
    .btn {
        padding: 16px 24px !important;
        font-size: 15px !important;
    }
}
</style>

<div class="product-edit-wrapper">
    <!-- COLUNA ESQUERDA: FORMULÁRIO -->
    <div class="product-edit-form">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Informações Básicas -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informações Básicas</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome do Produto *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU *</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Preços e Estoque -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Preços e Estoque</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="price" class="form-label">Preço (B2C) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="b2b_price" class="form-label">Preço (B2B)</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('b2b_price') is-invalid @enderror" 
                                           id="b2b_price" name="b2b_price" value="{{ old('b2b_price', $product->b2b_price) }}">
                                </div>
                                @error('b2b_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">Preço de Custo</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                                           id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}">
                                </div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Quantidade em Estoque *</label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_stock" class="form-label">Estoque Mínimo *</label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror" 
                                       id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 10) }}" required>
                                @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categorias e Departamento -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-tags me-2"></i>Categorias e Departamento</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Categorias *</label>
                        <div class="row">
                            @foreach($categories ?? [] as $category)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="categories[]" value="{{ $category->id }}" 
                                               id="category_{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', $productCategories)) ? 'checked' : '' }}>
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

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Departamento</label>
                        <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                            <option value="">— Nenhum departamento selecionado —</option>
                            @foreach($departments ?? [] as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $product->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Selecione o departamento do produto (opcional)</small>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Imagens -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-images me-2"></i>Imagens do Produto</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="images" class="form-label">Adicionar Imagens</label>
                        <input type="file" class="form-control @error('images') is-invalid @enderror" 
                               id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">Formatos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)</div>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($product->images && count($product->images) > 0)
                        <div class="mb-3">
                            <label class="form-label">Imagens Atuais</label>
                            <div class="row g-2" id="existing-images-container">
                                @foreach($product->images as $idx => $img)
                                    <div class="col-md-3 existing-image-item" data-image-path="{{ $img }}">
                                        <div class="position-relative">
                                            <img src="{{ strpos($img,'http')===0 ? $img : asset('storage/'.$img) }}" 
                                                 class="img-thumbnail w-100" 
                                                 style="height: 100px; object-fit: cover;">
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-existing-image" 
                                                    data-image-path="{{ $img }}">
                                                <i class="bi bi-x"></i>
                                            </button>
                                            @if($idx === 0)
                                                <span class="badge bg-primary position-absolute top-0 start-0 m-1">Principal</span>
                                            @endif
                                        </div>
                                        <input type="hidden" name="existing_images[]" value="{{ $img }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row g-2" id="images-container"></div>
                </div>
            </div>

            <!-- Dimensões e Modelo -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-rulers me-2"></i>Dimensões e Modelo</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
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
                </div>
            </div>

            <!-- Variações do Produto -->
            <div class="card card-modern mb-4" id="variations-section">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        Variações do Produto
                        <span class="badge bg-light text-primary ms-2" id="variations-count">
                            @php
                                $variationsCount = isset($variations) ? $variations->count() : ($product->variations ?? collect())->count();
                            @endphp
                            {{ $variationsCount }} variação(ões)
                        </span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle me-2 fs-5"></i>
                        <div>
                            <strong>Como funciona:</strong> Ative as variações para criar diferentes versões deste produto (ex: diferentes cores, tamanhos). 
                            Primeiro, crie os atributos globais em <a href="{{ route('admin.attributes.index') }}" target="_blank" class="alert-link">Atributos</a>.
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" 
                                   id="has_variations" name="has_variations" value="1"
                                   {{ old('has_variations', $product->has_variations) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="has_variations">
                                <i class="bi bi-toggle-on me-2"></i>
                                Este produto possui variações
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-lightbulb me-1"></i>
                            Ao ativar, você poderá criar variações com diferentes atributos (cor, tamanho, etc.)
                        </small>
                    </div>

                    <div id="variations-management" style="display: {{ $product->has_variations ? 'block' : 'none' }};">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>
                                Gerenciar Variações
                            </h6>
                            <div class="d-flex gap-2">
                                @php
                                    $productVariations = $product->variations ?? collect();
                                @endphp
                                @if($product->has_variations && $productVariations->count() > 0)
                                    <button type="button" class="btn btn-danger btn-sm" id="delete-all-variations-btn">
                                        <i class="bi bi-trash me-1"></i>
                                        Apagar Todas
                                    </button>
                                @endif
                                <button type="button" class="btn btn-primary btn-sm" id="add-variation-btn">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Adicionar Variação
                                </button>
                            </div>
                        </div>

                        <div id="variations-list" class="mb-3">
                            @if($product->has_variations && $productVariations->count() > 0)
                                @foreach($productVariations as $variation)
                                    <div class="variation-item border rounded p-3 mb-2" data-variation-id="{{ $variation->id }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <!-- Imagem da Variação -->
                                            <div class="variation-thumb me-3" style="width: 70px; flex-shrink: 0;">
                                                @php
                                                    $variationImage = $variation->first_image ?? ($product->first_image ?? asset('images/no-image.svg'));
                                                    $hasOwnImages = !empty($variation->images) && count($variation->images) > 0;
                                                @endphp
                                                <div class="position-relative">
                                                    <img src="{{ $variationImage }}" 
                                                         alt="{{ $variation->name }}" 
                                                         class="img-thumbnail variation-preview-img"
                                                         style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                                                         data-variation-id="{{ $variation->id }}">
                                                    @if($hasOwnImages)
                                                        <span class="position-absolute bottom-0 end-0 badge bg-success" 
                                                              style="font-size: 9px; padding: 2px 4px;">
                                                            {{ count($variation->images) }}
                                                        </span>
                                                    @else
                                                        <span class="position-absolute bottom-0 end-0 badge bg-secondary" 
                                                              style="font-size: 8px; padding: 2px 3px;" title="Usando imagem do produto">
                                                            <i class="bi bi-link-45deg"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <strong class="me-2">{{ $variation->name ?: $variation->sku }}</strong>
                                                    @if($variation->is_default)
                                                        <span class="badge bg-success me-2">Padrão</span>
                                                    @endif
                                                    @if(!$variation->in_stock || $variation->stock_quantity <= 0)
                                                        <span class="badge bg-danger">Sem Estoque</span>
                                                    @else
                                                        <span class="badge bg-info">Estoque: {{ $variation->stock_quantity }}</span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted">
                                                    <span class="me-3">SKU: {{ $variation->sku }}</span>
                                                    <span class="me-3">Preço: R$ {{ number_format($variation->price, 2, ',', '.') }}</span>
                                                    @if($variation->attributeValues->count() > 0)
                                                        <span>Atributos: {{ $variation->attributeValues->map(function($v) { return $v->display_value ?: $v->value; })->implode(', ') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary manage-variation-images" 
                                                        data-variation-id="{{ $variation->id }}" title="Gerenciar Imagens">
                                                    <i class="bi bi-images"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-variation" 
                                                        data-variation-id="{{ $variation->id }}" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-variation" 
                                                        data-variation-id="{{ $variation->id }}" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Nenhuma variação cadastrada ainda.</p>
                                    <small>Clique em "Adicionar Variação" para começar.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card card-modern mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Status</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Produto Ativo
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
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
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Campos marcados com * são obrigatórios
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-check-circle me-1"></i> Atualizar Produto
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- CORRIGIDO: Validação antes de submeter formulário principal -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainForm = document.querySelector('form[action*="/admin/products/"]');
        if (mainForm) {
            mainForm.addEventListener('submit', function(e) {
                // Validar has_variations
                const hasVariations = document.getElementById('has_variations');
                const variationsCount = document.querySelectorAll('.variation-item').length;
                
                if (hasVariations && hasVariations.checked && variationsCount === 0) {
                    e.preventDefault();
                    alert('Você marcou o produto como tendo variações, mas não há variações cadastradas.\n\nPor favor, desmarque a opção "Este produto possui variações" ou crie pelo menos uma variação antes de salvar.');
                    return false;
                }
                
                // Prevenir double submit
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn && submitBtn.disabled) {
                    e.preventDefault();
                    return false;
                }
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Salvando...';
                    
                    // Reabilitar após 10 segundos como segurança
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Atualizar Produto';
                    }, 10000);
                }
            });
        }
    });
    </script>

    <!-- COLUNA DIREITA: PREVIEW E AÇÕES -->
    <div class="product-edit-sidebar">
        <!-- Preview do Produto -->
        <div class="card card-modern mb-3">
            <div class="card-body p-3 text-center">
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded" style="overflow:hidden;">
                        @php $first = true; @endphp
                        @if($product->images && count($product->images) > 0)
                            @foreach($product->images as $img)
                                <div class="carousel-item {{ $first ? 'active' : '' }}">
                                    <img src="{{ strpos($img,'http')===0 ? $img : asset('storage/'.$img) }}" 
                                         class="d-block w-100" 
                                         style="height:180px; object-fit:cover;">
                                </div>
                                @php $first = false; @endphp
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <img src="{{ asset('images/no-image.svg') }}" 
                                     class="d-block w-100" 
                                     style="height:180px; object-fit:contain; background:#f8fafc; padding:12px;">
                            </div>
                        @endif
                    </div>
                    @if($product->images && count($product->images) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    @endif
                </div>
                <h5 class="mt-3 mb-0">{{ $product->name }}</h5>
                <p class="text-muted small mb-0">SKU: {{ $product->sku }} • ID: {{ $product->id }}</p>
            </div>
        </div>

        <!-- Preview de Preço -->
        <div class="card card-modern mb-3" id="pricePreviewCard" x-data="pricePreview()">
            <div class="card-body p-3">
                <h6 class="fw-semibold mb-2">Pré-visualização de Preço</h6>
                <div class="small text-muted mb-3">Valores ao vivo com base nos campos</div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted">Custo</div>
                    <div class="fw-semibold" x-text="formatCurrency(cost)"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted">Preço</div>
                    <div class="fw-semibold" x-text="formatCurrency(price)"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted">Lucro (R$)</div>
                    <div class="fw-semibold" x-text="formatCurrency(profit)"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Margem</div>
                    <div class="fw-semibold" x-text="margin + '%'"></div>
                </div>
                <div class="d-grid gap-2">
                    <button @click="applyMarkup(30)" type="button" class="btn btn-sm btn-outline-info">Aplicar Markup 30%</button>
                    <button @click="applyMarkup(50)" type="button" class="btn btn-sm btn-outline-secondary">Aplicar Markup 50%</button>
                </div>
            </div>
            <script>
            function pricePreview() {
                return {
                    cost: @json($product->cost_price ?? 0),
                    price: @json($product->price ?? 0),
                    get profit() { return this.price - this.cost; },
                    get margin() {
                        if (this.cost === 0) return 0;
                        const marginValue = ((this.profit / this.cost) * 100);
                        // CORRIGIDO: Tratar margem negativa
                        if (marginValue < 0) {
                            return marginValue.toFixed(1) + ' (prejuízo)';
                        }
                        return marginValue.toFixed(1);
                    },
                    formatCurrency(value) {
                        return value ? 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '-';
                    },
                    applyMarkup(percent) {
                        if (this.cost > 0) {
                            const newPrice = this.cost * (1 + percent / 100);
                            document.getElementById('price').value = newPrice.toFixed(2);
                            this.price = newPrice;
                            // Disparar evento input para atualizar preview
                            const priceInput = document.getElementById('price');
                            if (priceInput) {
                                priceInput.dispatchEvent(new Event('input'));
                            }
                        } else {
                            alert('Defina um preço de custo antes de aplicar markup');
                        }
                    },
                    init() {
                        const costInput = document.getElementById('cost_price');
                        const priceInput = document.getElementById('price');
                        if (costInput) {
                            costInput.addEventListener('input', (e) => {
                                this.cost = parseFloat(e.target.value) || 0;
                            });
                        }
                        if (priceInput) {
                            priceInput.addEventListener('input', (e) => {
                                this.price = parseFloat(e.target.value) || 0;
                            });
                        }
                    }
                }
            }
            </script>
        </div>

        <!-- Status e Estoque -->
        <div class="card card-modern mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted d-block">Status</small>
                        <span id="statusBadge" class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <button id="quickToggleActive" class="btn btn-sm btn-outline-primary">
                        {{ $product->is_active ? 'Desativar' : 'Ativar' }}
                    </button>
                </div>
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <small class="text-muted d-block">Estoque</small>
                        <div class="fw-semibold mt-1">{{ $product->stock_quantity }}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Min. Alerta</small>
                        <div class="fw-semibold mt-1">{{ $product->min_stock ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card card-modern">
            <div class="card-body p-3">
                <h6 class="mb-2">Ações Rápidas</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.clone', $product) }}" class="btn btn-outline-info">
                        <i class="bi bi-files me-2"></i> Clonar Produto
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Modal para Adicionar/Editar Variação - DESIGN MODERNO -->
<div class="modal fade" id="addVariationModal" tabindex="-1" aria-labelledby="addVariationModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-height: 95vh;">
        <div class="modal-content" style="max-height: 95vh; display: flex; flex-direction: column; border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 28px;">
                <h5 class="modal-title" id="variationModalTitle" style="color: white; font-weight: 800; font-size: 20px; margin: 0;">
                    <i class="bi bi-layers me-3"></i>Adicionar Variação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar" style="filter: brightness(0) invert(1); opacity: 0.8;"></button>
            </div>
            <form id="variation-form" style="display: flex; flex-direction: column; height: 100%;">
                <div class="modal-body" style="flex: 1; overflow-y: auto; max-height: calc(95vh - 200px); padding: 32px 28px; background: #fafbfc;">
                    <input type="hidden" id="variation_id" name="variation_id">
                    
                    <div class="alert" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border: none; border-radius: 16px; padding: 20px; margin-bottom: 28px;">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                                <i class="bi bi-info-circle" style="color: white; font-size: 20px;"></i>
                            </div>
                            <div>
                                <strong style="color: #1a237e; font-size: 16px;">Atributos da Variação</strong>
                                <p style="margin: 4px 0 0 0; color: #5e35b1; font-size: 14px;">Selecione os atributos que compõem esta variação. Ex: Cor + Tamanho</p>
                            </div>
                        </div>
                    </div>

                    <!-- Seleção de Atributos -->
                    <div class="mb-4" id="attributes-selection">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <label class="form-label" style="font-weight: 800; color: #1f2937; font-size: 16px; margin: 0;">
                                <i class="bi bi-tags me-2" style="color: #667eea;"></i>Atributos da Variação *
                            </label>
                            <button type="button" class="btn" id="generate-all-combinations-btn" style="display: none; white-space: nowrap; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; padding: 10px 20px; font-weight: 700;">
                                <i class="bi bi-magic me-2"></i>
                                Gerar Combinações
                            </button>
                        </div>
                        <div id="attributes-container" style="background: white; border-radius: 16px; padding: 20px; border: 2px solid #e5e7eb; min-height: 120px;">
                            <p class="text-muted" style="margin: 0; display: flex; align-items: center;">
                                <i class="bi bi-hourglass-split me-2"></i>Carregando atributos...
                            </p>
                        </div>
                        <div id="combinations-preview" class="mt-3" style="display: none;">
                            <div class="alert" style="background: linear-gradient(135deg, #e8f5e8 0%, #f0f8ff 100%); border: none; border-radius: 12px; padding: 16px; margin: 0;">
                                <div style="display: flex; align-items: center;">
                                    <i class="bi bi-calculator" style="color: #2e7d32; font-size: 20px; margin-right: 12px;"></i>
                                    <span style="color: #2e7d32; font-weight: 600;">
                                        <strong id="combinations-count">0</strong> combinações serão criadas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 700; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="bi bi-currency-dollar me-2" style="color: #667eea;"></i>Preço (B2C) *
                                </label>
                                <div class="input-group" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);">
                                    <span class="input-group-text" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: 700; padding: 14px 18px;">R$</span>
                                    <input type="number" step="0.01" class="form-control" id="variation_price" 
                                           name="price" value="{{ $product->price }}" required 
                                           style="border: none; padding: 14px 18px; font-size: 16px; font-weight: 600; background: white;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 700; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="bi bi-box-seam me-2" style="color: #667eea;"></i>Estoque *
                                </label>
                                <input type="number" class="form-control" id="variation_stock" 
                                       name="stock_quantity" value="0" min="0" required 
                                       style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 14px 18px; font-size: 16px; font-weight: 600; background: white; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 700; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="bi bi-briefcase me-2" style="color: #667eea;"></i>Preço (B2B)
                                </label>
                                <div class="input-group" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);">
                                    <span class="input-group-text" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white; border: none; font-weight: 700; padding: 14px 18px;">R$</span>
                                    <input type="number" step="0.01" class="form-control" id="variation_b2b_price" 
                                           name="b2b_price" value="{{ $product->b2b_price ?? '' }}"
                                           style="border: none; padding: 14px 18px; font-size: 16px; font-weight: 600; background: white;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 700; color: #374151; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <i class="bi bi-star me-2" style="color: #667eea;"></i>Variação Padrão
                                </label>
                                <div class="form-check" style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px 20px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);">
                                    <input class="form-check-input" type="checkbox" id="variation_is_default" name="is_default" 
                                           style="width: 24px; height: 24px; border: 2px solid #667eea; border-radius: 6px;">
                                    <label class="form-check-label" for="variation_is_default" style="font-weight: 600; color: #4b5563; margin-left: 12px; cursor: pointer;">
                                        <i class="bi bi-check-circle me-2"></i>Marcar como padrão
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: none; border-radius: 12px; padding: 16px; margin-top: 20px;">
                        <div style="display: flex; align-items: center;">
                            <i class="bi bi-lightbulb" style="color: #d97706; font-size: 20px; margin-right: 12px;"></i>
                            <div>
                                <strong style="color: #92400e; font-size: 14px;">Geração Automática</strong>
                                <p style="margin: 4px 0 0 0; color: #78350f; font-size: 13px;">SKU e Nome serão gerados automaticamente baseados nos atributos selecionados</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="flex-shrink: 0; border: none; padding: 24px 28px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                    <button type="button" class="btn" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white; border: none; border-radius: 12px; padding: 14px 28px; font-weight: 700; box-shadow: 0 8px 25px rgba(107, 114, 128, 0.3);">
                        <i class="bi bi-x-circle me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn" id="save-variation-btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; padding: 14px 28px; font-weight: 700; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);">
                        <i class="bi bi-check-circle me-2"></i> Salvar Variação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Gerenciar Imagens da Variação -->
<div class="modal fade" id="variationImagesModal" tabindex="-1" aria-labelledby="variationImagesModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="variationImagesModalLabel">
                    <i class="bi bi-images me-2"></i>
                    Imagens da Variação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="variation_images_id">
                
                <!-- Info da Variação -->
                <div class="alert alert-light border mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam me-2 text-primary fs-5"></i>
                        <div>
                            <strong id="variation_images_name">Variação</strong>
                            <small class="d-block text-muted" id="variation_images_sku">SKU</small>
                        </div>
                    </div>
                </div>
                
                <!-- Upload de Nova Imagem -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bi bi-cloud-upload me-2"></i>
                            Adicionar Nova Imagem
                        </h6>
                        <form id="variation-image-upload-form" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="variation_image_file" class="form-label">Selecione uma imagem</label>
                                <input type="file" class="form-control" id="variation_image_file" 
                                       name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                <small class="text-muted">Formatos aceitos: JPEG, PNG, GIF, WebP. Máximo: 5MB</small>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-sm" id="upload-variation-image-btn">
                                    <i class="bi bi-upload me-1"></i> Fazer Upload
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="select-from-album-btn">
                                    <i class="bi bi-images me-1"></i> Do Álbum
                                </button>
                                <button type="button" class="btn btn-info btn-sm" id="select-from-product-btn">
                                    <i class="bi bi-box-seam me-1"></i> Do Produto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
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
                
                <!-- Lista de Imagens da Variação -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-image me-2"></i>
                            Imagens desta Variação
                        </h6>
                        <span class="badge bg-primary" id="variation-images-count">0</span>
                    </div>
                    <div class="card-body">
                        <div id="variation-images-grid" class="row g-2">
                            <div class="col-12 text-center text-muted py-4" id="no-variation-images">
                                <i class="bi bi-image fs-1 d-block mb-2 opacity-50"></i>
                                <p class="mb-1">Nenhuma imagem específica</p>
                                <small>Esta variação usa as imagens do produto principal</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Imagens do Produto (referência) -->
                <div class="card mt-3" id="product-images-reference">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-muted">
                            <i class="bi bi-link-45deg me-2"></i>
                            Imagens do Produto Principal (referência)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="product-images-grid" class="row g-2">
                            <!-- Imagens do produto serão carregadas aqui -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para o grid de imagens de variação */
.variation-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: #f8f9fa;
    transition: all 0.2s ease;
}
.variation-image-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.variation-image-item img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    cursor: pointer;
}
.variation-image-item .image-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    padding: 8px 4px 4px;
    display: flex;
    justify-content: center;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.2s;
}
.variation-image-item:hover .image-actions {
    opacity: 1;
}
.variation-image-item .primary-badge {
    position: absolute;
    top: 4px;
    left: 4px;
    background: var(--bs-success);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
}
.product-ref-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    border: 2px solid #dee2e6;
    opacity: 0.7;
}
</style>
@endpush

@push('scripts')
@endpush

<script src="{{ asset('js/admin-product-edit.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagens (CORRIGIDO: valida tipo e tamanho)
    const imageInput = document.getElementById('images');
    const container = document.getElementById('images-container');
    
    if (imageInput && container) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (!files || files.length === 0) return;
            
            // Limpar previews anteriores de novas imagens
            container.querySelectorAll('.new-image-preview').forEach(el => el.remove());
            
            Array.from(files).forEach((file, index) => {
                // Validar tipo de arquivo
                if (!file.type.startsWith('image/')) {
                    alert(`Arquivo "${file.name}" não é uma imagem válida. Será ignorado.`);
                    return;
                }
                
                // Validar tamanho (10MB = 10485760 bytes)
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert(`Arquivo "${file.name}" é muito grande (${(file.size / 1024 / 1024).toFixed(2)}MB). Máximo permitido: 10MB. Será ignorado.`);
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-2 new-image-preview';
                    col.setAttribute('data-file-name', file.name);
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail w-100" style="height: 100px; object-fit: cover;">
                            <span class="badge bg-success position-absolute top-0 start-0 m-1">Nova</span>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-new-image" data-file-name="${file.name}">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                    container.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        });
    }
    
    // Handler para remover imagem existente do produto
    document.querySelectorAll('.remove-existing-image').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const imagePath = this.dataset.imagePath;
            const item = this.closest('.existing-image-item');
            
            if (!confirm('Tem certeza que deseja remover esta imagem?')) {
                return;
            }
            
            // Remover do DOM
            if (item) {
                item.remove();
            }
            
            // Atualizar preview do carousel se necessário
            const carousel = document.getElementById('productCarousel');
            if (carousel) {
                // O carousel será atualizado no próximo reload, mas podemos forçar atualização se necessário
            }
        });
    });
    
    // Handler para remover preview de nova imagem
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-new-image')) {
            const btn = e.target.closest('.remove-new-image');
            const fileName = btn.dataset.fileName;
            const preview = document.querySelector(`[data-file-name="${fileName}"]`);
            
            if (preview) {
                preview.remove();
                
                // Remover arquivo do input também
                const dataTransfer = new DataTransfer();
                const files = Array.from(imageInput.files).filter(f => f.name !== fileName);
                files.forEach(f => dataTransfer.items.add(f));
                imageInput.files = dataTransfer.files;
            }
        }
    });

    // Quick toggle active (CORRIGIDO: atualiza checkbox principal e trata erros)
    const quickToggle = document.getElementById('quickToggleActive');
    if (quickToggle) {
        quickToggle.addEventListener('click', function() {
            const newState = {{ $product->is_active ? '0' : '1' }};
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            quickToggle.disabled = true;
            quickToggle.textContent = 'Aguarde...';
            
            fetch(`/admin/products/{{ $product->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ _method: 'PUT', is_active: newState })
            })
            .then(async r => {
                if (!r.ok) {
                    const errorText = await r.text();
                    throw new Error(`HTTP ${r.status}: ${errorText}`);
                }
                return r.json();
            })
            .then(data => {
                if (data && data.success) {
                    const badge = document.getElementById('statusBadge');
                    if (badge) {
                        badge.className = newState == '1' ? 'badge bg-success' : 'badge bg-danger';
                        badge.textContent = newState == '1' ? 'Ativo' : 'Inativo';
                    }
                    
                    // CORRIGIDO: Atualizar checkbox principal também
                    const isActiveCheckbox = document.getElementById('is_active');
                    if (isActiveCheckbox) {
                        isActiveCheckbox.checked = newState == '1';
                    }
                    
                    quickToggle.textContent = newState == '1' ? 'Desativar' : 'Ativar';
                } else {
                    throw new Error(data.message || 'Erro ao atualizar status');
                }
            })
            .catch(err => {
                console.error('Erro ao alternar status:', err);
                alert('Erro ao atualizar status do produto: ' + (err.message || 'Erro desconhecido'));
            })
            .finally(() => { 
                quickToggle.disabled = false; 
            });
        });
    }

    // Gerenciamento de Variações
    const hasVariationsToggle = document.getElementById('has_variations');
    const variationsManagement = document.getElementById('variations-management');
    const addVariationBtn = document.getElementById('add-variation-btn');
    const variationsList = document.getElementById('variations-list');
    const productId = {{ $product->id }};

    // Toggle para mostrar/ocultar seção de variações (CORRIGIDO: valida se há variações ao desmarcar)
    if (hasVariationsToggle && variationsManagement) {
        hasVariationsToggle.addEventListener('change', function() {
            const variationsCount = variationsList.querySelectorAll('.variation-item').length;
            
            // Se desmarcar mas houver variações, avisar
            if (!this.checked && variationsCount > 0) {
                if (!confirm(`Este produto possui ${variationsCount} variação(ões) cadastrada(s).\n\nAo desmarcar esta opção, as variações não serão excluídas, mas o produto não será exibido como tendo variações.\n\nDeseja continuar?`)) {
                    // Reverter checkbox
                    this.checked = true;
                    return;
                }
            }
            
            variationsManagement.style.display = this.checked ? 'block' : 'none';
        });
    }

    // AbortController para cancelar requisições anteriores (CORRIGIDO)
    let attributesFetchController = null;
    
    // Função para carregar atributos disponíveis (CORRIGIDO: cancela requisições anteriores)
    function loadAttributesForVariation() {
        // Cancelar requisição anterior se existir
        if (attributesFetchController) {
            attributesFetchController.abort();
        }
        attributesFetchController = new AbortController();
        
        const container = document.getElementById('attributes-container');
        container.innerHTML = '<p class="text-muted"><i class="bi bi-hourglass-split me-2"></i>Carregando atributos...</p>';
        
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        return fetch('{{ route("admin.attributes.list") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            signal: attributesFetchController.signal
        })
            .then(async r => {
                if (!r.ok) {
                    const errorText = await r.text();
                    throw new Error(`HTTP ${r.status}: ${errorText}`);
                }
                return r.json();
            })
            .then(data => {
                console.log('Atributos carregados:', data);
                if (data && data.success && data.attributes && data.attributes.length > 0) {
                    container.innerHTML = '';
                    data.attributes.forEach(attr => {
                        const attrDiv = document.createElement('div');
                        attrDiv.className = 'mb-3';
                        attrDiv.innerHTML = `
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i>${attr.name}
                                <small class="text-muted">(Selecione um ou mais)</small>
                            </label>
                            <select class="form-select form-select-sm attribute-select" 
                                    data-attribute-id="${attr.id}" 
                                    name="attribute_values[]" 
                                    multiple 
                                    size="3"
                                    style="min-height: 80px;"
                                    required>
                            </select>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Mantenha Ctrl/Cmd pressionado para selecionar múltiplos valores
                            </small>
                        `;
                        const select = attrDiv.querySelector('select');
                        if (attr.values && attr.values.length > 0) {
                            attr.values.forEach(value => {
                                const option = document.createElement('option');
                                option.value = value.id;
                                option.textContent = value.display_value || value.value;
                                select.appendChild(option);
                            });
                        } else {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'Nenhum valor disponível';
                            option.disabled = true;
                            select.appendChild(option);
                        }
                        container.appendChild(attrDiv);
                    });
                    
                    // Mostrar botão de gerar combinações se houver atributos
                    const attributeSelects = container.querySelectorAll('.attribute-select');
                    const generateBtn = document.getElementById('generate-all-combinations-btn');
                    if (attributeSelects.length > 0 && generateBtn) {
                        generateBtn.style.display = 'block';
                        // Atualizar preview quando seleções mudarem
                        attributeSelects.forEach(select => {
                            select.addEventListener('change', updateCombinationsPreview);
                        });
                        updateCombinationsPreview();
                    }
                } else {
                    container.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Nenhum atributo encontrado.</strong><br>
                            Crie atributos globais primeiro em 
                            <a href="{{ route('admin.attributes.create') }}" target="_blank" class="alert-link">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Atributos
                            </a>
                        </div>
                    `;
                }
            })
            .catch(err => {
                // CORRIGIDO: Não mostrar erro se foi cancelado intencionalmente
                if (err.name === 'AbortError') {
                    return; // Requisição foi cancelada, não mostrar erro
                }
                
                console.error('Erro ao carregar atributos:', err);
                container.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Erro ao carregar atributos</strong><br>
                        <small>${err.message || 'Erro desconhecido'}</small><br>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="loadAttributesForVariation()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Tentar novamente
                        </button>
                    </div>
                `;
            });
    }

    // Botão adicionar variação - abrir modal
    if (addVariationBtn) {
        addVariationBtn.addEventListener('click', function() {
            const modalEl = document.getElementById('addVariationModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            if (!modal) {
                modal = new bootstrap.Modal(modalEl);
            }
            // Limpar formulário (CORRIGIDO: limpa selects múltiplos corretamente)
            document.getElementById('variation-form').reset();
            document.getElementById('variation_id').value = '';
            document.getElementById('variationModalTitle').textContent = 'Adicionar Variação';
            document.getElementById('variation_price').value = '{{ $product->price }}';
            document.getElementById('variation_b2b_price').value = '{{ $product->b2b_price ?? "" }}';
            document.getElementById('variation_stock').value = '0';
            document.getElementById('variation_is_default').checked = false;
            
            // CORRIGIDO: Limpar seleções de selects múltiplos
            document.querySelectorAll('.attribute-select').forEach(select => {
                Array.from(select.options).forEach(opt => opt.selected = false);
            });
            
            // Carregar atributos disponíveis
            loadAttributesForVariation();
            modal.show();
        });
    }

    // Botões editar variação (EVENT DELEGATION - evita múltiplos listeners)
    document.addEventListener('click', async function(e) {
        const editBtn = e.target.closest('.edit-variation');
        if (!editBtn) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const variationId = editBtn.dataset.variationId;
        const modalEl = document.getElementById('addVariationModal');
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) {
            modal = new bootstrap.Modal(modalEl);
        }
        
        try {
            const response = await fetch(`/admin/products/variations/${variationId}`);
            const data = await response.json();
                
                if (data.success && data.variation) {
                    const v = data.variation;
                    document.getElementById('variation_id').value = variationId;
                    document.getElementById('variationModalTitle').textContent = 'Editar Variação';
                    // REMOVIDO: variation_sku e variation_name não existem no formulário (são gerados automaticamente)
                    document.getElementById('variation_price').value = v.price || '';
                    document.getElementById('variation_b2b_price').value = v.b2b_price || '';
                    document.getElementById('variation_stock').value = v.stock_quantity || 0;
                    document.getElementById('variation_is_default').checked = v.is_default || false;
                    
                    loadAttributesForVariation().then(() => {
                        // Selecionar valores dos atributos (CORRIGIDO: seleciona TODOS os valores múltiplos corretamente)
                        if (v.attribute_values && v.attribute_values.length > 0) {
                            // Agrupar por attribute_id para selecionar múltiplos valores do mesmo atributo
                            const valuesByAttribute = {};
                            v.attribute_values.forEach(av => {
                                if (!valuesByAttribute[av.attribute_id]) {
                                    valuesByAttribute[av.attribute_id] = [];
                                }
                                valuesByAttribute[av.attribute_id].push(av.attribute_value_id);
                            });
                            
                            // Selecionar todos os valores de cada atributo
                            Object.keys(valuesByAttribute).forEach(attributeId => {
                                const select = document.querySelector(`select[data-attribute-id="${attributeId}"]`);
                                if (select) {
                                    // Limpar seleções anteriores
                                    Array.from(select.options).forEach(opt => opt.selected = false);
                                    
                                    // Selecionar todos os valores deste atributo
                                    valuesByAttribute[attributeId].forEach(valueId => {
                                        const option = select.querySelector(`option[value="${valueId}"]`);
                                        if (option) {
                                            option.selected = true;
                                        }
                                    });
                                    
                                    // Disparar evento change para atualizar preview
                                    select.dispatchEvent(new Event('change'));
                                }
                            });
                        }
                    });
                    
                    modal.show();
                } else {
                    alert('Erro ao carregar variação');
                }
            } catch (error) {
                console.error(error);
                alert('Erro ao carregar variação');
            }
        });
    });

    // Botões excluir variação (EVENT DELEGATION - evita múltiplos listeners)
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-variation');
        if (!deleteBtn) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const variationId = deleteBtn.dataset.variationId;
        if (!confirm('Tem certeza que deseja excluir esta variação?')) return;
        
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        fetch(`/admin/products/variations/${variationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`[data-variation-id="${variationId}"]`)?.remove();
                // Atualizar contador
                const count = variationsList.querySelectorAll('.variation-item').length;
                const countEl = document.getElementById('variations-count');
                if (countEl) {
                    countEl.textContent = `${count} variação(ões)`;
                }
                
                // CORRIGIDO: Atualizar flag has_variations se não houver mais variações
                if (count === 0) {
                    const hasVariationsCheckbox = document.getElementById('has_variations');
                    if (hasVariationsCheckbox) {
                        hasVariationsCheckbox.checked = false;
                    }
                    const variationsManagement = document.getElementById('variations-management');
                    if (variationsManagement) {
                        variationsManagement.style.display = 'none';
                    }
                }
                
                // Atualizar visibilidade do botão "Apagar Todas"
                const deleteAllBtn = document.getElementById('delete-all-variations-btn');
                if (deleteAllBtn) {
                    deleteAllBtn.style.display = count > 0 ? 'inline-block' : 'none';
                }
                
                // Se não houver mais variações, mostrar mensagem
                if (count === 0) {
                    variationsList.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">Nenhuma variação cadastrada ainda.</p>
                            <small>Clique em "Adicionar Variação" para começar.</small>
                        </div>
                    `;
                }
            } else {
                alert(data.message || 'Erro ao excluir variação');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erro ao excluir variação');
        });
    });

    // Botão apagar todas as variações
    const deleteAllVariationsBtn = document.getElementById('delete-all-variations-btn');
    if (deleteAllVariationsBtn) {
        deleteAllVariationsBtn.addEventListener('click', function() {
            const variationItems = variationsList.querySelectorAll('.variation-item');
            const totalVariations = variationItems.length;
            
            if (totalVariations === 0) {
                alert('Não há variações para excluir');
                return;
            }
            
            if (!confirm(`Tem certeza que deseja excluir TODAS as ${totalVariations} variação(ões)?\n\nEsta ação não pode ser desfeita!`)) {
                return;
            }
            
            // Confirmar novamente para evitar exclusão acidental
            if (!confirm('ATENÇÃO: Esta ação irá excluir TODAS as variações permanentemente!\n\nDeseja continuar?')) {
                return;
            }
            
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Excluindo...';
            
            // Coletar todos os IDs das variações
            const variationIds = Array.from(variationItems).map(item => item.dataset.variationId);
            let deleted = 0;
            let errors = 0;
            
            // Deletar todas as variações sequencialmente
            const deleteNext = (index) => {
                if (index >= variationIds.length) {
                    // Todas foram processadas
                    if (errors > 0) {
                        alert(`Concluído! ${deleted} variação(ões) excluída(s), ${errors} erro(s).`);
                    } else {
                        alert(`Todas as ${deleted} variação(ões) foram excluídas com sucesso!`);
                    }
                    
                    // Atualizar interface
                    variationsList.innerHTML = `
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">Nenhuma variação cadastrada ainda.</p>
                            <small>Clique em "Adicionar Variação" para começar.</small>
                        </div>
                    `;
                    
                    // CORRIGIDO: Atualizar flag has_variations
                    const hasVariationsCheckbox = document.getElementById('has_variations');
                    if (hasVariationsCheckbox) {
                        hasVariationsCheckbox.checked = false;
                    }
                    const variationsManagement = document.getElementById('variations-management');
                    if (variationsManagement) {
                        variationsManagement.style.display = 'none';
                    }
                    
                    // Atualizar contador
                    const countEl = document.getElementById('variations-count');
                    if (countEl) {
                        countEl.textContent = '0 variação(ões)';
                    }
                    
                    // Ocultar botão de apagar todas
                    if (deleteAllVariationsBtn) {
                        deleteAllVariationsBtn.style.display = 'none';
                    }
                    
                    // Recarregar página para garantir sincronização
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    
                    return;
                }
                
                const variationId = variationIds[index];
                
                fetch(`/admin/products/variations/${variationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        deleted++;
                        // Remover elemento da lista
                        const item = document.querySelector(`[data-variation-id="${variationId}"]`);
                        if (item) {
                            item.remove();
                        }
                    } else {
                        errors++;
                        console.error(`Erro ao excluir variação ${variationId}:`, data.message);
                    }
                    // Continuar com a próxima
                    deleteNext(index + 1);
                })
                .catch(err => {
                    errors++;
                    console.error(`Erro ao excluir variação ${variationId}:`, err);
                    // Continuar com a próxima mesmo em caso de erro
                    deleteNext(index + 1);
                });
            };
            
            // Iniciar exclusão
            deleteNext(0);
        });
    }

    // Função para atualizar preview de combinações (CORRIGIDO: lógica mais clara)
    function updateCombinationsPreview() {
        const selects = document.querySelectorAll('.attribute-select');
        const preview = document.getElementById('combinations-preview');
        const countEl = document.getElementById('combinations-count');
        
        if (selects.length < 2) {
            if (preview) preview.style.display = 'none';
            return;
        }
        
        let totalCombinations = 1;
        let hasSelectedValues = false;
        let allAttributesHaveValues = true;
        
        selects.forEach(select => {
            const selectedCount = Array.from(select.selectedOptions)
                .filter(opt => opt.value && opt.value !== '' && !opt.disabled).length;
            const totalOptions = Array.from(select.options)
                .filter(opt => opt.value && opt.value !== '' && !opt.disabled).length;
            
            if (totalOptions === 0) {
                allAttributesHaveValues = false;
                return; // Pular atributos sem valores
            }
            
            if (selectedCount > 0) {
                hasSelectedValues = true;
                totalCombinations *= selectedCount;
            } else if (selectedCount === 0 && totalOptions > 0) {
                // CORRIGIDO: Se nenhum selecionado mas há opções, contar todas (para preview)
                totalCombinations *= totalOptions;
            }
        });
        
        // Mostrar preview apenas se houver pelo menos 2 atributos com valores e combinações > 1
        if (allAttributesHaveValues && hasSelectedValues && totalCombinations > 1) {
            if (preview) preview.style.display = 'block';
            if (countEl) countEl.textContent = totalCombinations;
        } else {
            if (preview) preview.style.display = 'none';
        }
    }

    // Função para gerar todas as combinações (CORRIGIDO: validações e limites)
    function generateAllCombinations() {
        const selects = document.querySelectorAll('.attribute-select');
        const combinations = [];
        
        // Coletar valores selecionados de cada atributo (CORRIGIDO: valida se cada atributo tem valores)
        const attributeValues = [];
        selects.forEach(select => {
            const selectedValues = Array.from(select.selectedOptions)
                .map(opt => opt.value)
                .filter(v => v && v !== '');
            if (selectedValues.length > 0) {
                attributeValues.push(selectedValues);
            } else {
                // Se nenhum selecionado, usar todos os valores disponíveis
                const allValues = Array.from(select.options)
                    .map(opt => opt.value)
                    .filter(v => v && v !== '' && !opt.disabled);
                if (allValues.length > 0) {
                    attributeValues.push(allValues);
                }
            }
        });
        
        // Verificar se há pelo menos um atributo com valores selecionados
        const hasMultipleValues = attributeValues.some(arr => arr.length > 1);
        const totalAttributes = attributeValues.length;
        
        if (totalAttributes === 0) {
            alert('Selecione pelo menos um atributo com valores para gerar combinações');
            return;
        }
        
        // Se há apenas um atributo, deve ter múltiplos valores para gerar combinações
        if (totalAttributes === 1 && !hasMultipleValues) {
            alert('Para gerar combinações com um único atributo, selecione múltiplos valores desse atributo.\n\nExemplo: Selecione P, M e G no atributo Tamanho para criar 3 variações.');
            return;
        }
        
        // CORRIGIDO: Validar se algum atributo não tem valores
        const emptyAttributes = [];
        selects.forEach((select, index) => {
            const hasValues = Array.from(select.options).some(opt => opt.value && opt.value !== '' && !opt.disabled);
            const selectedCount = Array.from(select.selectedOptions).filter(opt => opt.value && opt.value !== '').length;
            if (hasValues && selectedCount === 0 && attributeValues[index] && attributeValues[index].length === 0) {
                emptyAttributes.push(select.previousElementSibling?.textContent || `Atributo ${index + 1}`);
            }
        });
        
        if (emptyAttributes.length > 0) {
            if (!confirm(`Os seguintes atributos não têm valores selecionados:\n${emptyAttributes.join(', ')}\n\nDeseja continuar mesmo assim?`)) {
                return;
            }
        }
        
        // Gerar todas as combinações
        function generateCombos(arrays, index = 0, current = []) {
            if (index === arrays.length) {
                combinations.push([...current]);
                return;
            }
            arrays[index].forEach(value => {
                current.push(value);
                generateCombos(arrays, index + 1, current);
                current.pop();
            });
        }
        
        generateCombos(attributeValues);
        
        if (combinations.length === 0) {
            alert('Nenhuma combinação encontrada');
            return;
        }
        
        // CORRIGIDO: Validar limite de combinações (máximo 100)
        const MAX_COMBINATIONS = 100;
        if (combinations.length > MAX_COMBINATIONS) {
            alert(`Muitas combinações geradas (${combinations.length}). O limite é ${MAX_COMBINATIONS} variações por vez.\n\nPor favor, selecione menos valores de atributos ou crie as variações manualmente.`);
            return;
        }
        
        if (!confirm(`Deseja criar ${combinations.length} variação(ões) automaticamente?\n\nIsso criará uma variação para cada combinação de atributos selecionados.\n\nEsta operação pode levar alguns segundos.`)) {
            return;
        }
        
        // Coletar dados do formulário
        const formData = {
            price: document.getElementById('variation_price').value,
            b2b_price: document.getElementById('variation_b2b_price').value,
            stock_quantity: document.getElementById('variation_stock').value,
            is_default: document.getElementById('variation_is_default').checked
        };
        
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let created = 0;
        let errors = 0;
        
        // CORRIGIDO: Coletar dados do formulário antes de iniciar
        const variationForm = document.getElementById('variation-form');
        const formDataObj = new FormData(variationForm);
        const basePrice = parseFloat(formDataObj.get('price')) || parseFloat(document.getElementById('variation_price').value) || 0;
        const baseB2bPrice = formDataObj.get('b2b_price') || document.getElementById('variation_b2b_price').value || null;
        const baseStock = parseInt(formDataObj.get('stock_quantity')) || parseInt(document.getElementById('variation_stock').value) || 0;
        
        // Validar preço base
        if (basePrice <= 0) {
            alert('Defina um preço válido antes de gerar combinações');
            return;
        }
        
        // CORRIGIDO: Mostrar progresso
        const generateBtn = document.getElementById('generate-all-combinations-btn');
        const originalBtnText = generateBtn.innerHTML;
        generateBtn.disabled = true;
        
        // Criar cada combinação (CORRIGIDO: mostra progresso)
        const createNext = (index) => {
            if (index >= combinations.length) {
                generateBtn.disabled = false;
                generateBtn.innerHTML = originalBtnText;
                alert(`Concluído! ${created} variação(ões) criada(s)${errors > 0 ? `, ${errors} erro(s)` : ''}`);
                bootstrap.Modal.getInstance(document.getElementById('addVariationModal')).hide();
                location.reload();
                return;
            }
            
            // Atualizar progresso
            generateBtn.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> Criando ${index + 1}/${combinations.length}...`;
            
            const data = {
                price: basePrice,
                b2b_price: baseB2bPrice ? parseFloat(baseB2bPrice) : null,
                stock_quantity: baseStock,
                is_default: false, // Não marcar como padrão ao gerar múltiplas variações
                attribute_values: combinations[index],
                _token: csrf
            };
            
            fetch(`/admin/products/{{ $product->slug }}/variations`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(async r => {
                if (!r.ok) {
                    const errorData = await r.json().catch(() => ({ message: 'Erro desconhecido' }));
                    throw new Error(errorData.message || `HTTP ${r.status}`);
                }
                return r.json();
            })
            .then(result => {
                if (result.success) {
                    created++;
                } else {
                    errors++;
                    console.error('Erro ao criar variação:', result.message);
                }
                createNext(index + 1);
            })
            .catch(err => {
                errors++;
                console.error('Erro:', err);
                createNext(index + 1);
            });
        };
        
        createNext(0);
    }

    // Botão gerar todas as combinações
    const generateAllBtn = document.getElementById('generate-all-combinations-btn');
    if (generateAllBtn) {
        generateAllBtn.addEventListener('click', generateAllCombinations);
    }

    // Submeter formulário de variação
    const variationForm = document.getElementById('variation-form');
    if (variationForm) {
        variationForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const variationId = document.getElementById('variation_id').value;
            const formData = new FormData(this);
            const attributeValues = [];
            
            // Coletar valores dos atributos selecionados (CORRIGIDO: coleta TODOS os valores de selects múltiplos)
            document.querySelectorAll('.attribute-select').forEach(select => {
                const selectedValues = Array.from(select.selectedOptions)
                    .map(opt => opt.value)
                    .filter(v => v && v !== '');
                
                if (selectedValues.length === 0) {
                    return; // Pular se nenhum valor selecionado neste atributo
                }
                
                // Validar se não há valores duplicados do mesmo atributo
                const attributeId = select.dataset.attributeId;
                selectedValues.forEach(valueId => {
                    // Verificar se já existe este attribute_id + value_id
                    const exists = attributeValues.some(av => 
                        av.attribute_id === attributeId && av.attribute_value_id === valueId
                    );
                    if (!exists) {
                        attributeValues.push({
                            attribute_id: attributeId,
                            attribute_value_id: valueId
                        });
                    }
                });
            });
            
            // CORRIGIDO: Validar que não há múltiplos valores do mesmo atributo
            const attributeIdsCount = {};
            attributeValues.forEach(av => {
                if (!attributeIdsCount[av.attribute_id]) {
                    attributeIdsCount[av.attribute_id] = [];
                }
                attributeIdsCount[av.attribute_id].push(av.attribute_value_id);
            });
            
            // Verificar se algum atributo tem múltiplos valores selecionados
            const attributesWithMultipleValues = [];
            Object.keys(attributeIdsCount).forEach(attrId => {
                if (attributeIdsCount[attrId].length > 1) {
                    const select = document.querySelector(`select[data-attribute-id="${attrId}"]`);
                    const attrName = select?.previousElementSibling?.textContent?.split('(')[0]?.trim() || `Atributo ${attrId}`;
                    attributesWithMultipleValues.push({
                        id: attrId,
                        name: attrName,
                        count: attributeIdsCount[attrId].length
                    });
                }
            });
            
            if (attributesWithMultipleValues.length > 0) {
                const attrNames = attributesWithMultipleValues.map(a => `${a.name} (${a.count} valores)`).join('\n');
                const generateBtn = document.getElementById('generate-all-combinations-btn');
                const btnVisible = generateBtn && generateBtn.style.display !== 'none';
                
                let message = `ERRO: Você selecionou múltiplos valores do mesmo atributo:\n\n${attrNames}\n\nUma variação só pode ter UM valor por atributo.`;
                
                if (btnVisible) {
                    message += `\n\nPara criar múltiplas variações automaticamente, use o botão "Gerar Combinações" acima que criará uma variação para cada combinação possível.`;
                } else {
                    message += `\n\nPor favor, selecione apenas um valor por atributo ou use o botão "Gerar Combinações" se disponível.`;
                }
                
                alert(message);
                return;
            }
            
            // Validar se pelo menos um atributo tem valores selecionados
            const uniqueAttributeIds = [...new Set(attributeValues.map(av => av.attribute_id))];
            if (attributeValues.length === 0 || uniqueAttributeIds.length === 0) {
                alert('Selecione pelo menos um valor de atributo para a variação');
                return;
            }
            
            // Validar se cada atributo tem pelo menos um valor
            const allSelects = document.querySelectorAll('.attribute-select');
            let hasEmptyAttribute = false;
            allSelects.forEach(select => {
                const selectedCount = Array.from(select.selectedOptions)
                    .filter(opt => opt.value && opt.value !== '').length;
                const hasValues = Array.from(select.options)
                    .some(opt => opt.value && opt.value !== '' && !opt.disabled);
                
                if (hasValues && selectedCount === 0) {
                    hasEmptyAttribute = true;
                }
            });
            
            if (hasEmptyAttribute) {
                if (!confirm('Alguns atributos não têm valores selecionados. Deseja continuar mesmo assim?')) {
                    return;
                }
            }
            
            // CORRIGIDO: Validar se is_default está sendo definido e garantir unicidade
            const isDefault = formData.get('is_default') === 'on';
            
            // Se marcando como padrão, verificar se já existe outra variação padrão
            if (isDefault && !variationId) {
                // Verificar no DOM se há outra variação marcada como padrão
                const existingDefault = variationsList.querySelector('.variation-item .badge.bg-primary[title*="Padrão"]');
                if (existingDefault) {
                    if (!confirm('Já existe uma variação padrão. Ao criar esta como padrão, a outra será desmarcada automaticamente.\n\nDeseja continuar?')) {
                        return;
                    }
                }
            }
            
            const data = {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                sku: formData.get('sku') || null, // SKU é gerado automaticamente se não fornecido
                name: formData.get('name') || null, // Nome é gerado automaticamente se não fornecido
                price: formData.get('price') || 0,
                b2b_price: formData.get('b2b_price') || null,
                stock_quantity: formData.get('stock_quantity') || 0,
                is_default: isDefault,
                attribute_values: attributeValues.map(av => av.attribute_value_id)
            };
            
            // Validações adicionais
            if (!data.price || parseFloat(data.price) <= 0) {
                alert('O preço deve ser maior que zero');
                return;
            }
            
            if (data.stock_quantity < 0) {
                alert('A quantidade em estoque não pode ser negativa');
                return;
            }
            
            const url = variationId 
                ? `/admin/products/variations/${variationId}`
                : `/admin/products/{{ $product->slug }}/variations`;
            const method = variationId ? 'PUT' : 'POST';
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Salvando...';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': data._token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addVariationModal'))?.hide();
                    // CORRIGIDO: Atualizar contador antes de recarregar
                    const countEl = document.getElementById('variations-count');
                    if (countEl && !variationId) {
                        const currentCount = parseInt(countEl.textContent) || 0;
                        countEl.textContent = `${currentCount + 1} variação(ões)`;
                    }
                    location.reload(); // Recarregar para mostrar nova variação
                } else {
                    alert(result.message || 'Erro ao salvar variação');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Erro ao salvar variação:', error);
                alert('Erro ao salvar variação: ' + (error.message || 'Erro desconhecido'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
    
    // ============================================
    // GERENCIAMENTO DE IMAGENS DE VARIAÇÕES
    // ============================================
    
    let currentVariationId = null;
    
    // Função global para abrir modal de imagens
    window.openVariationImagesModal = function(variationId) {
        currentVariationId = variationId;
        document.getElementById('variation_images_id').value = variationId;
        loadVariationImages(variationId);
        
        const modalEl = document.getElementById('variationImagesModal');
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) {
            modal = new bootstrap.Modal(modalEl);
        }
        modal.show();
    };
    
    // Event listeners para botões de gerenciar imagens (CORRIGIDO: remove listeners anteriores)
    document.querySelectorAll('.manage-variation-images').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        newBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const variationId = this.dataset.variationId;
            openVariationImagesModal(variationId);
        });
    });
    
    // Event listener para imagem da variação (clique na imagem) (CORRIGIDO: remove listeners anteriores)
    document.querySelectorAll('.variation-preview-img').forEach(img => {
        const newImg = img.cloneNode(true);
        img.parentNode.replaceChild(newImg, img);
        newImg.addEventListener('click', function(e) {
            e.stopPropagation();
            const variationId = this.dataset.variationId;
            if (variationId) {
                openVariationImagesModal(variationId);
            }
        });
    });
    
    // Carregar imagens da variação (CORRIGIDO: trata erro 404 e outros erros)
    async function loadVariationImages(variationId) {
        const grid = document.getElementById('variation-images-grid');
        const countBadge = document.getElementById('variation-images-count');
        const noImages = document.getElementById('no-variation-images');
        const productGrid = document.getElementById('product-images-grid');
        
        if (!grid || !countBadge || !noImages || !productGrid) {
            console.error('Elementos do modal de imagens não encontrados');
            return;
        }
        
        grid.innerHTML = '<div class="col-12 text-center py-3"><i class="bi bi-hourglass-split"></i> Carregando...</div>';
        
        try {
            const response = await fetch(`/admin/products/variations/${variationId}/images`);
            
            // CORRIGIDO: Tratar erro 404 (variação deletada)
            if (response.status === 404) {
                grid.innerHTML = '<div class="col-12 text-center py-3 text-danger">Variação não encontrada. Ela pode ter sido deletada.</div>';
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('variationImagesModal'))?.hide();
                }, 2000);
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                // Atualizar info da variação
                const variationItem = document.querySelector(`[data-variation-id="${variationId}"]`);
                if (variationItem) {
                    const name = variationItem.querySelector('strong')?.textContent || 'Variação';
                    const sku = variationItem.querySelector('.small.text-muted span')?.textContent || '';
                    document.getElementById('variation_images_name').textContent = name;
                    document.getElementById('variation_images_sku').textContent = sku;
                }
                
                // Renderizar imagens da variação
                if (data.images && data.images.length > 0) {
                    noImages.style.display = 'none';
                    countBadge.textContent = data.images.length;
                    
                    grid.innerHTML = data.images.map((img, index) => `
                        <div class="col-4 col-md-3">
                            <div class="variation-image-item">
                                ${index === 0 ? '<span class="primary-badge">Principal</span>' : ''}
                                <img src="${img.url}" alt="Imagem ${index + 1}" onclick="viewFullImage('${img.url}')">
                                <div class="image-actions">
                                    ${index !== 0 ? `
                                        <button type="button" class="btn btn-xs btn-success" onclick="setPrimaryImage('${img.path}')" title="Definir como principal">
                                            <i class="bi bi-star-fill"></i>
                                        </button>
                                    ` : ''}
                                    <button type="button" class="btn btn-xs btn-danger" onclick="removeVariationImage('${img.path}')" title="Remover">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    noImages.style.display = 'block';
                    countBadge.textContent = '0';
                    grid.innerHTML = '';
                    grid.appendChild(noImages);
                }
                
                // Renderizar imagens do produto (referência)
                if (data.product_images && data.product_images.length > 0) {
                    productGrid.innerHTML = data.product_images.map(img => `
                        <div class="col-auto">
                            <img src="${img.url}" alt="Imagem do produto" class="product-ref-image">
                        </div>
                    `).join('');
                } else {
                    productGrid.innerHTML = '<p class="text-muted mb-0">Nenhuma imagem no produto principal</p>';
                }
            } else {
                grid.innerHTML = '<div class="col-12 text-center py-3 text-danger">Erro ao carregar imagens</div>';
            }
        } catch (error) {
            console.error('Erro ao carregar imagens:', error);
            grid.innerHTML = '<div class="col-12 text-center py-3 text-danger">Erro ao carregar imagens</div>';
        }
    }
    
    // Upload de imagem (CORRIGIDO: valida tamanho antes de enviar)
    document.getElementById('variation-image-upload-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('variation_image_file');
        const variationId = document.getElementById('variation_images_id').value;
        
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Selecione uma imagem para fazer upload');
            return;
        }
        
        const file = fileInput.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        // CORRIGIDO: Validar tamanho antes de enviar
        if (file.size > maxSize) {
            alert(`Arquivo muito grande (${(file.size / 1024 / 1024).toFixed(2)}MB). O tamanho máximo permitido é 5MB.`);
            return;
        }
        
        // Validar tipo
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WebP.');
            return;
        }
        
        const formData = new FormData();
        formData.append('image', fileInput.files[0]);
        
        const btn = document.getElementById('upload-variation-image-btn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Enviando...';
        
        try {
            const response = await fetch(`/admin/products/variations/${variationId}/images`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                fileInput.value = '';
                loadVariationImages(variationId);
                
                // Atualizar thumbnail na lista de variações
                updateVariationThumbnail(variationId, data.images);
                
                // Feedback visual
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Enviado!';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.disabled = false;
                }, 1500);
            } else {
                alert(data.message || 'Erro ao fazer upload');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Erro no upload:', error);
            alert('Erro ao fazer upload da imagem');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    
    // Remover imagem (CORRIGIDO: valida se é a última imagem)
    window.removeVariationImage = async function(imagePath) {
        const variationId = document.getElementById('variation_images_id').value;
        const grid = document.getElementById('variation-images-grid');
        
        // Verificar quantas imagens existem
        const currentImages = grid.querySelectorAll('.variation-image-item').length;
        
        if (currentImages <= 1) {
            if (!confirm('Esta é a última imagem da variação. Ao removê-la, a variação usará as imagens do produto principal.\n\nDeseja continuar?')) {
                return;
            }
        } else {
            if (!confirm('Tem certeza que deseja remover esta imagem?')) {
                return;
            }
        }
        
        try {
            const response = await fetch(`/admin/products/variations/${variationId}/images`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ image_path: imagePath })
            });
            
            const data = await response.json();
            
            if (data.success) {
                loadVariationImages(variationId);
                updateVariationThumbnail(variationId, data.images);
            } else {
                alert(data.message || 'Erro ao remover imagem');
            }
        } catch (error) {
            console.error('Erro ao remover:', error);
            alert('Erro ao remover imagem');
        }
    };
    
    // Definir imagem principal
    window.setPrimaryImage = async function(imagePath) {
        const variationId = document.getElementById('variation_images_id').value;
        
        try {
            const response = await fetch(`/admin/products/variations/${variationId}/images/primary`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ image_path: imagePath })
            });
            
            const data = await response.json();
            
            if (data.success) {
                loadVariationImages(variationId);
                updateVariationThumbnail(variationId, data.images);
            } else {
                alert(data.message || 'Erro ao definir imagem principal');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao definir imagem principal');
        }
    };
    
    // Atualizar thumbnail na lista de variações (CORRIGIDO: trata erro se variação não existe)
    function updateVariationThumbnail(variationId, images) {
        const variationItem = document.querySelector(`[data-variation-id="${variationId}"]`);
        if (!variationItem) {
            console.warn(`Variação ${variationId} não encontrada na lista. Ela pode ter sido deletada.`);
            return;
        }
        
        const img = variationItem.querySelector('.variation-preview-img');
        const badge = variationItem.querySelector('.variation-thumb .badge');
        
        if (!img) {
            console.warn('Imagem de preview não encontrada');
            return;
        }
        
        if (images && images.length > 0) {
            img.src = images[0].url;
            if (badge) {
                badge.textContent = images.length;
                badge.classList.remove('bg-secondary');
                badge.classList.add('bg-success');
            }
        } else {
            // Se não há imagens, usar imagem do produto ou padrão
            const productImage = document.querySelector('#productCarousel .carousel-item.active img')?.src;
            if (productImage) {
                img.src = productImage;
            }
            if (badge) {
                badge.innerHTML = '<i class="bi bi-link-45deg"></i>';
                badge.classList.remove('bg-success');
                badge.classList.add('bg-secondary');
            }
        }
    }
    
    // Visualizar imagem em tamanho completo
    window.viewFullImage = function(url) {
        window.open(url, '_blank');
    };
    
    // Botão para selecionar imagem do produto principal
    document.getElementById('select-from-product-btn')?.addEventListener('click', async function() {
        const variationId = document.getElementById('variation_images_id').value;
        
        // Carregar imagens da variação para pegar as imagens do produto
        try {
            const response = await fetch(`/admin/products/variations/${variationId}/images`);
            const data = await response.json();
            
            if (data.success && data.product_images && data.product_images.length > 0) {
                // Criar modal simples para selecionar imagem do produto
                const modalHtml = `
                    <div class="modal fade" id="productImagesSelectModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Selecionar Imagem do Produto Principal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted mb-3">Clique em uma imagem para adicioná-la à variação:</p>
                                    <div class="row g-3">
                                        ${data.product_images.map(img => `
                                            <div class="col-4 col-md-3">
                                                <div class="position-relative border rounded p-2 product-image-select" 
                                                     data-image-path="${img.path}"
                                                     style="cursor: pointer; transition: all 0.2s;">
                                                    <img src="${img.url}" class="w-100" style="height: 120px; object-fit: cover; border-radius: 4px;">
                                                    <div class="position-absolute top-0 end-0 m-1">
                                                        <span class="badge bg-primary d-none selected-badge">
                                                            <i class="bi bi-check-circle"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remover modal anterior se existir
                const existingModal = document.getElementById('productImagesSelectModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Adicionar modal ao body
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('productImagesSelectModal'));
                modal.show();
                
                // Adicionar event listeners para seleção
                document.querySelectorAll('.product-image-select').forEach(el => {
                    el.addEventListener('click', function() {
                        const imagePath = this.dataset.imagePath;
                        
                        // Adicionar imagem à variação
                        fetch(`/admin/products/variations/${variationId}/images`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ product_image_path: imagePath })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                bootstrap.Modal.getInstance(document.getElementById('productImagesSelectModal'))?.hide();
                                loadVariationImages(variationId);
                                updateVariationThumbnail(variationId, data.images);
                            } else {
                                alert(data.message || 'Erro ao adicionar imagem');
                            }
                        })
                        .catch(err => {
                            console.error('Erro:', err);
                            alert('Erro ao adicionar imagem do produto');
                        });
                    });
                    
                    el.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.05)';
                        this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                    });
                    
                    el.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                        this.style.boxShadow = 'none';
                    });
                });
            } else {
                alert('O produto principal não possui imagens para selecionar');
            }
        } catch (error) {
            console.error('Erro ao carregar imagens do produto:', error);
            alert('Erro ao carregar imagens do produto');
        }
    });
});
</script>

@endsection
