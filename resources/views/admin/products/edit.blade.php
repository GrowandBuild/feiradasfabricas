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
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
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
                        @if(setting('b2b_enabled', false))
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
                        @endif
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
                            @foreach($homepageSections ?? [] as $section)
                                <option value="{{ $section->id }}" 
                                        @if(old('homepage_section_ids', $product->homepage_section_ids ?? []))
                                            {{ in_array($section->id, old('homepage_section_ids', $product->homepage_section_ids ?? [])) ? 'selected' : '' }}
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

                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Marca</label>
                        <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                            <option value="">— Nenhuma marca selecionada —</option>
                            @foreach($brands ?? [] as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Selecione a marca do produto (opcional)</small>
                        @error('brand_id')
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
                        
                        <!-- Zona de colar imagens -->
                        <div class="paste-zone" id="editProductPasteZone">
                            <div class="paste-content">
                                <i class="bi bi-cloud-upload" style="font-size: 2.5rem; color: #6b7280; margin-bottom: 12px;"></i>
                                <p class="mb-2" style="color: #374151; font-weight: 600; font-size: 1.1rem;">Arraste imagens aqui ou cole com Ctrl+V</p>
                                <small class="text-muted d-block">Ou clique para selecionar arquivos (múltiplo permitido)</small>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Formatos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)
                                </small>
                            </div>
                            <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*" style="display: none;">
                        </div>
                        
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
                        @if(setting('b2b_enabled', false))
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
                        @endif
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

<script>
window.adminProductEditConfig = {
    productId: {{ $product->id }},
    csrfToken: '{{ csrf_token() }}',
    productIsActive: {{ $product->is_active ? 'true' : 'false' }}
};
</script>

<script src="{{ asset('js/admin-product-edit.js') }}?v={{ filemtime(public_path('js/admin-product-edit.js')) }}"></script>

<script>
// Handler para remover imagem existente do produto
document.addEventListener('click', function(e) {
    // Debug: mostrar qual elemento foi clicado
    console.log('Elemento clicado:', e.target);
    console.log('Classes do elemento clicado:', e.target.className);
    
    // Verificar se é o botão ou filho do botão
    const removeBtn = e.target.closest('.remove-existing-image');
    if (removeBtn) {
        console.log('Botão de remover encontrado!');
        e.stopPropagation();
        e.preventDefault();
        
        const imagePath = removeBtn.dataset.imagePath;
        console.log('Caminho da imagem:', imagePath);
        
        const item = removeBtn.closest('.existing-image-item');
        console.log('Item encontrado:', item);
        
        if (!confirm('Tem certeza que deseja remover esta imagem?')) {
            return;
        }
        
        // Remover do DOM
        if (item) {
            item.remove();
            console.log('Item removido do DOM');
        }
        
        // Remover hidden input
        const hiddenInput = document.querySelector(`input[value="${imagePath}"][name="existing_images[]"]`);
        if (hiddenInput) {
            hiddenInput.remove();
            console.log('Hidden input removido');
        }
    }
});

// Alternativa: bind direto nos botões
document.addEventListener('DOMContentLoaded', function() {
    const removeButtons = document.querySelectorAll('.remove-existing-image');
    console.log('Botões de remover encontrados:', removeButtons.length);
    
    removeButtons.forEach((btn, index) => {
        console.log(`Botão ${index}:`, btn);
        btn.addEventListener('click', function(e) {
            console.log('Clique direto no botão funcionando!');
            e.stopPropagation();
            e.preventDefault();
            
            const imagePath = this.dataset.imagePath;
            const item = this.closest('.existing-image-item');
            
            if (!confirm('Tem certeza que deseja remover esta imagem?')) {
                return;
            }
            
            if (item) {
                item.remove();
            }
            
            const hiddenInput = document.querySelector(`input[value="${imagePath}"][name="existing_images[]"]`);
            if (hiddenInput) {
                hiddenInput.remove();
            }
        });
    });
});
</script>

@endsection
