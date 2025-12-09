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
                        return ((this.profit / this.cost) * 100).toFixed(1);
                    },
                    formatCurrency(value) {
                        return value ? 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '-';
                    },
                    applyMarkup(percent) {
                        if (this.cost > 0) {
                            const newPrice = this.cost * (1 + percent / 100);
                            document.getElementById('price').value = newPrice.toFixed(2);
                            this.price = newPrice;
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
                    <button id="openVariationsManager" type="button" class="btn btn-outline-secondary" 
                            data-bs-toggle="modal" data-bs-target="#variationsModal" data-product-id="{{ $product->id }}">
                        <i class="bi bi-list-ul me-2"></i> Gerenciar Variações
                    </button>
                    <a href="{{ route('admin.products.clone', $product) }}" class="btn btn-outline-info">
                        <i class="bi bi-files me-2"></i> Clonar Produto
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i> Voltar à lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
    @include('admin.products.modals.variations-shopify')
@endpush

@push('scripts')
<script src="{{ asset('js/admin/simple-variations.js') }}"></script>

<!-- Script de Diagnóstico Automatizado -->
<script>
console.log('=== INÍCIO DO DIAGNÓSTICO AUTOMATIZADO ===');

// Função para verificar o estado das funções
function verificarEstado() {
    console.log('VERIFICAÇÃO DE ESTADO:', {
        timestamp: new Date().toISOString(),
        url: window.location.href,
        readyState: document.readyState,
        addAttributeDisponivel: typeof window.addAttribute !== 'undefined',
        removeAttributeDisponivel: typeof window.removeAttribute !== 'undefined',
        modalExiste: !!document.getElementById('variationsModal'),
        containerExiste: !!document.getElementById('attributesContainer'),
        botaoAdicionarExiste: !!document.querySelector('button[onclick*="addAttribute"]'),
        simpleGeneratorExiste: !!window.simpleGenerator
    });
}

// Verificar imediatamente
verificarEstado();

// Verificar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded executado');
        verificarEstado();
        
        // Verificar após um pequeno delay
        setTimeout(verificarEstado, 100);
        setTimeout(verificarEstado, 500);
        setTimeout(verificarEstado, 1000);
    });
} else {
    console.log('DOM já estava pronto');
    verificarEstado();
    setTimeout(verificarEstado, 100);
    setTimeout(verificarEstado, 500);
    setTimeout(verificarEstado, 1000);
}

// Verificar quando o modal for aberto
document.addEventListener('shown.bs.modal', function(e) {
    if (e.target.id === 'variationsModal') {
        console.log('Modal variationsModal foi aberto');
        verificarEstado();
        
        // Testar o clique no botão após 100ms
        setTimeout(() => {
            const botao = document.querySelector('button[onclick*="addAttribute"]');
            if (botao) {
                console.log('Botão encontrado, testando clique...');
                try {
                    botao.click();
                    console.log('Clique executado com sucesso');
                } catch (error) {
                    console.error('ERRO AO CLICAR NO BOTÃO:', error);
                }
            } else {
                console.error('Botão addAttribute não encontrado no modal');
            }
        }, 100);
    }
});

// Verificar quando o script simple-variations for carregado
window.addEventListener('load', function() {
    console.log('Window.load executado');
    verificarEstado();
});

// Monitorar mudanças no DOM
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    if (node.id === 'variationsModal' || (node.querySelector && node.querySelector('#variationsModal'))) {
                        console.log('Modal detectado no DOM via MutationObserver');
                        verificarEstado();
                    }
                }
            });
        }
    });
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});

console.log('Diagnóstico automatizado iniciado');
</script>
@endpush

<script src="{{ asset('js/admin-product-edit.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagens
    const imageInput = document.getElementById('images');
    const container = document.getElementById('images-container');
    
    if (imageInput && container) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (!files || files.length === 0) return;
            
            container.innerHTML = '';
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-2';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-thumbnail w-100" style="height: 100px; object-fit: cover;">
                                <span class="badge bg-success position-absolute top-0 start-0 m-1">Nova</span>
                            </div>
                        `;
                        container.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    // Quick toggle active
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
            .then(r => r.json())
            .then(data => {
                if (data && data.success) {
                    const badge = document.getElementById('statusBadge');
                    if (badge) {
                        badge.className = newState == '1' ? 'badge bg-success' : 'badge bg-danger';
                        badge.textContent = newState == '1' ? 'Ativo' : 'Inativo';
                    }
                    quickToggle.textContent = newState == '1' ? 'Desativar' : 'Ativar';
                }
            })
            .catch(err => console.error(err))
            .finally(() => { quickToggle.disabled = false; });
        });
    }
});
</script>

@endsection
