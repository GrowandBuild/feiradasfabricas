@extends('admin.layouts.app')

@section('title', 'Produtos')
@section('page-title', 'Gerenciar Produtos')
@section('page-subtitle')
    <p class="text-muted mb-0">Gerencie todos os produtos da loja</p>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="rounded-circle p-3 me-3" style="background-color: rgba(249, 115, 22, 0.1);">
            <i class="bi bi-box-seam" style="font-size: 1.5rem; color: var(--accent-color);"></i>
        </div>
        <div>
            <h4 class="mb-0">Produtos</h4>
            <p class="text-muted mb-0">{{ $products->total() }} produtos cadastrados</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.import') }}" class="btn btn-outline-primary">
            <i class="bi bi-upload me-1"></i> Importar
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Produto
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-funnel me-2" style="color: var(--accent-color);"></i>
        <h6 class="mb-0">Filtros e Busca</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-search me-1"></i>Buscar
                </label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Nome, SKU ou descrição">
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-award me-1"></i>Marca
                </label>
                <select name="brand" class="form-select">
                    <option value="">Todas</option>
                    @php
                        $brands = \App\Models\Product::distinct()->pluck('brand')->filter()->sort();
                    @endphp
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                            {{ $brand }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-tags me-1"></i>Categoria
                </label>
                <select name="category" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-toggle-on me-1"></i>Status
                </label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-x-circle me-1"></i>Disponibilidade
                </label>
                <select name="availability" class="form-select">
                    <option value="">Todas</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Disponível</option>
                    <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Indisponível</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-boxes me-1"></i>Estoque
                </label>
                <select name="stock_status" class="form-select">
                    <option value="">Todos</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Estoque Baixo</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Sem Estoque</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">
                    <i class="bi bi-truck me-1"></i>Fornecedor
                </label>
                <select name="supplier" class="form-select">
                    <option value="">Todos</option>
                    @php
                        $suppliers = \App\Models\Product::distinct()->whereNotNull('supplier')->pluck('supplier')->filter()->sort();
                    @endphp
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier }}" {{ request('supplier') == $supplier ? 'selected' : '' }}>
                            {{ $supplier }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        
        @if(request()->hasAny(['search', 'brand', 'category', 'status', 'stock_status', 'supplier']))
            <div class="mt-3 d-flex align-items-center">
                <span class="text-muted me-2">Filtros ativos:</span>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Limpar filtros
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Configuração de Margens de Lucro -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="bi bi-percent me-2" style="color: var(--accent-color);"></i>
            <h6 class="mb-0">Configuração de Margens de Lucro</h6>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="saveMarginsBtn" onclick="saveMargins(event)">
            <i class="bi bi-save me-1"></i>Salvar Margens
        </button>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">
                    <i class="bi bi-person me-1"></i>Margem B2C (%)
                </label>
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           id="b2c_margin" 
                           step="0.1" 
                           min="0" 
                           max="100"
                           value="{{ setting('b2c_margin_percentage', 10) }}">
                    <span class="input-group-text">%</span>
                </div>
                <small class="text-muted">Ex: 10% = R$ 1,00 → R$ 1,10</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">
                    <i class="bi bi-building me-1"></i>Margem B2B (%)
                </label>
                <div class="input-group">
                    <input type="number" 
                           class="form-control" 
                           id="b2b_margin" 
                           step="0.1" 
                           min="0" 
                           max="100"
                           value="{{ setting('b2b_margin_percentage', 20) }}">
                    <span class="input-group-text">%</span>
                </div>
                <small class="text-muted">Ex: 20% = R$ 1,00 → R$ 1,20</small>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="w-100">
                    <label class="form-label">Aplicar a todos os produtos</label>
                    <button type="button" class="btn btn-warning btn-sm w-100" id="applyMarginsBtn" onclick="applyMarginsToAll(event)">
                        <i class="bi bi-arrow-repeat me-1"></i>Recalcular Todos os Preços
                    </button>
                    <small class="text-muted d-block mt-1">⚠️ Recalcula B2B e B2C de todos os produtos</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Produtos -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <!-- Barra de Ações em Massa -->
            <div id="bulkActionsBar" class="mb-3 p-3 bg-light rounded d-none">
                <form id="bulkActionForm" action="{{ route('admin.products.bulk-availability') }}" method="POST">
                    @csrf
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span id="selectedCount" class="fw-semibold">0</span> produto(s) selecionado(s)
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="submitBulkAction('mark_unavailable')" class="btn btn-warning btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Marcar como Indisponível
                            </button>
                            <button type="button" onclick="submitBulkAction('mark_available')" class="btn btn-success btn-sm">
                                <i class="bi bi-check-circle me-1"></i>Marcar como Disponível
                            </button>
                            <button type="button" id="clearSelection" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x me-1"></i>Limpar
                            </button>
                        </div>
                        <input type="hidden" name="action" id="bulkAction" value="">
                    </div>
                    <input type="hidden" name="product_ids" id="selectedProductIds" value="">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-sm" style="font-size: 0.875rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 30px; padding: 8px 4px;">
                                <input type="checkbox" id="selectAll" class="form-check-input" title="Selecionar todos">
                            </th>
                            <th class="text-center" style="width: 45px; padding: 8px 4px;">
                                <i class="bi bi-image text-muted"></i>
                            </th>
                            <th style="min-width: 180px; max-width: 250px; padding: 8px;">Produto</th>
                            <th style="width: 90px; padding: 8px 4px;">Marca</th>
                            <th style="width: 110px; padding: 8px 4px;">Preço</th>
                            <th class="text-center" style="width: 80px; padding: 8px 4px;">Estoque</th>
                            <th class="text-center" style="width: 100px; padding: 8px 4px;">Variações</th>
                            <th class="text-center" style="width: 100px; padding: 8px 4px;">Status</th>
                            <th class="text-center" style="width: 90px; padding: 8px 4px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="{{ $product->is_unavailable ? 'table-secondary opacity-75' : '' }}">
                                <td class="text-center" style="padding: 8px 4px;">
                                    <input type="checkbox" 
                                           class="form-check-input product-checkbox" 
                                           value="{{ $product->id }}"
                                           data-product-id="{{ $product->id }}">
                                </td>
                                <td class="text-center" style="padding: 8px 4px;">
                                    @if($product->first_image)
                                        <img src="{{ $product->first_image }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded product-thumbnail" 
                                             style="width: 40px; height: 40px; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                                             data-product-id="{{ $product->id }}"
                                             data-product-name="{{ $product->name }}"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imagesModal"
                                             onclick="event.stopPropagation();"
                                             onmouseover="this.style.transform='scale(1.1)'"
                                             onmouseout="this.style.transform='scale(1)'"
                                             onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';"
                                             title="Clique para editar imagens">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center product-thumbnail" 
                                             style="width: 40px; height: 40px; cursor: pointer; transition: transform 0.2s;"
                                             data-product-id="{{ $product->id }}"
                                             data-product-name="{{ $product->name }}"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imagesModal"
                                             onclick="event.stopPropagation();"
                                             onmouseover="this.style.transform='scale(1.1)'"
                                             onmouseout="this.style.transform='scale(1)'"
                                             title="Clique para adicionar imagens">
                                            <i class="bi bi-image" style="font-size: 1rem; color: #fd7e14;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 8px;">
                                    <div style="line-height: 1.3;">
                                        <div class="fw-semibold" style="font-size: 0.875rem; margin-bottom: 2px;">{{ Str::limit($product->name, 35) }}</div>
                                        <div class="d-flex flex-wrap gap-1" style="margin-top: 2px;">
                                                @if($product->is_featured)
                                                <span class="badge bg-warning" style="font-size: 0.7rem; padding: 2px 6px;">
                                                    <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                                    </span>
                                                @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 8px 4px;">
                                    @if($product->brand)
                                        <span class="badge bg-primary" style="font-size: 0.75rem; padding: 3px 6px;">
                                            {{ Str::limit($product->brand, 12) }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 8px 4px;">
                                    <div class="price-editor" data-product-id="{{ $product->id }}">
                                        <div class="cost-price-input mb-1">
                                            <label class="form-label mb-0" style="font-size: 0.7rem; color: #6c757d;">
                                                Custo:
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text" style="font-size: 0.7rem; padding: 2px 6px;">R$</span>
                                                <input type="text" 
                                                       class="form-control form-control-sm cost-price-field" 
                                                       data-product-id="{{ $product->id }}"
                                                       value="{{ $product->cost_price ? number_format($product->cost_price, 2, ',', '.') : '0,00' }}" 
                                                       style="font-size: 0.75rem; padding: 2px 6px;"
                                                       placeholder="0,00"
                                                       onblur="normalizeAndUpdatePrice({{ $product->id }}, this.value)">
                                                <button class="btn btn-sm btn-outline-success" 
                                                        type="button" 
                                                        onclick="normalizeAndUpdatePrice({{ $product->id }}, document.querySelector('[data-product-id=\'{{ $product->id }}\'].cost-price-field').value)"
                                                        style="font-size: 0.7rem; padding: 2px 6px;"
                                                        title="Salvar">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="fw-semibold text-success" style="font-size: 0.875rem; line-height: 1.2;">
                                            R$ <span class="b2c-price-display">{{ number_format($product->price, 2, ',', '.') }}</span>
                                    </div>
                                    @if($product->b2b_price)
                                            <small class="text-muted" style="font-size: 0.7rem; line-height: 1.2;">
                                                B2B: R$ <span class="b2b-price-display">{{ number_format($product->b2b_price, 2, ',', '.') }}</span>
                                        </small>
                                    @endif
                                    </div>
                                </td>
                                <td class="text-center" style="padding: 8px 4px;">
                                    <div style="line-height: 1.2;">
                                        <span class="fw-semibold" style="font-size: 0.875rem;">{{ $product->current_stock }}</span>
                                        @if($product->isLowStock())
                                            <span class="badge bg-danger d-block mt-1" style="font-size: 0.7rem; padding: 2px 4px;">
                                                <i class="bi bi-exclamation-triangle" style="font-size: 0.7rem;"></i>
                                            </span>
                                        @elseif($product->current_stock == 0)
                                            <span class="badge bg-secondary d-block mt-1" style="font-size: 0.7rem; padding: 2px 4px;">
                                                <i class="bi bi-x-circle" style="font-size: 0.7rem;"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-success d-block mt-1" style="font-size: 0.7rem; padding: 2px 4px;">
                                                <i class="bi bi-check-circle" style="font-size: 0.7rem;"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center" style="padding: 8px 4px;">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary variations-btn" 
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#variationsModal"
                                            style="font-size: 0.7rem; padding: 2px 8px;"
                                            title="Gerenciar Variações">
                                        <i class="bi bi-list-ul me-1"></i>
                                        <span>{{ $product->variations()->count() }}</span>
                                    </button>
                                </td>
                                <td class="text-center" style="padding: 8px 4px;">
                                    <div style="line-height: 1.2;">
                                        <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }} d-block" style="font-size: 0.7rem; padding: 2px 4px; margin-bottom: 2px;">
                                            <i class="bi bi-{{ $product->is_active ? 'check-circle' : 'x-circle' }}" style="font-size: 0.7rem;"></i>
                                        </span>
                                        @if($product->is_unavailable)
                                            <span class="badge bg-warning d-block" style="font-size: 0.7rem; padding: 2px 4px;">
                                                <i class="bi bi-exclamation-triangle" style="font-size: 0.7rem;"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center" style="padding: 8px 4px;">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.products.show', $product) }}" 
                                           class="btn btn-outline-info" title="Visualizar" style="padding: 2px 6px; font-size: 0.75rem;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="btn btn-outline-primary" title="Editar" style="padding: 2px 6px; font-size: 0.75rem;">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Excluir" style="padding: 2px 6px; font-size: 0.75rem;">
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="mb-3 mb-md-0">
                    <p class="text-muted mb-0">
                        Mostrando <strong>{{ $products->firstItem() ?? 0 }}</strong> a <strong>{{ $products->lastItem() ?? 0 }}</strong> de <strong>{{ $products->total() }}</strong> resultados
                    </p>
                </div>
                <div>
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                     style="width: 100px; height: 100px;">
                    <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-muted mb-3">Nenhum produto encontrado</h5>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'brand', 'category', 'status', 'stock_status', 'supplier']))
                        Nenhum produto corresponde aos filtros aplicados.
                    @else
                        Comece criando seu primeiro produto para a loja.
                    @endif
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    @if(request()->hasAny(['search', 'brand', 'category', 'status', 'stock_status', 'supplier']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Limpar Filtros
                        </a>
                    @endif
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Criar Produto
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Incluir Modal de Variações -->
@include('admin.products.modals.variations')

<!-- Incluir Modal de Imagens -->
@include('admin.products.modals.images')

@section('styles')
<style>
    /* Corrigir z-index e garantir que o modal seja clicável */
    .modal-backdrop {
        z-index: 1040 !important;
        opacity: 0.5 !important;
        pointer-events: auto !important;
    }
    
    .modal-backdrop.show {
        z-index: 1040 !important;
        opacity: 0.5 !important;
    }
    
    /* Modal de Variações */
    #variationsModal {
        z-index: 1050 !important;
        pointer-events: none !important;
    }
    
    #variationsModal.show {
        display: block !important;
        pointer-events: auto !important;
    }
    
    #variationsModal .modal-dialog {
        z-index: 1051 !important;
        position: relative;
        pointer-events: auto !important;
    }
    
    #variationsModal .modal-content {
        position: relative;
        z-index: 1052 !important;
        pointer-events: auto !important;
    }
    
    /* Modal de Imagens */
    #imagesModal {
        z-index: 1050 !important;
        pointer-events: none !important;
    }
    
    #imagesModal.show {
        display: block !important;
        pointer-events: auto !important;
    }
    
    #imagesModal .modal-dialog {
        z-index: 1051 !important;
        position: relative;
        pointer-events: auto !important;
    }
    
    #imagesModal .modal-content {
        position: relative;
        z-index: 1052 !important;
        pointer-events: auto !important;
    }
</style>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    // Função para limpar backdrops duplicados e garantir ordem correta
    function fixModalBackdrop() {
        // Remover backdrops duplicados (manter apenas o último)
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 1) {
            for (let i = 0; i < backdrops.length - 1; i++) {
                backdrops[i].remove();
            }
        }
        
        // Garantir que o backdrop tenha z-index menor que os modais
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.zIndex = '1040';
        }
        
        // Garantir que os modais abertos tenham z-index maior
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach((modal, index) => {
            modal.style.zIndex = (1050 + index).toString();
            const dialog = modal.querySelector('.modal-dialog');
            if (dialog) {
                dialog.style.zIndex = (1051 + index).toString();
            }
            const content = modal.querySelector('.modal-content');
            if (content) {
                content.style.zIndex = (1052 + index).toString();
            }
        });
    }
    
    // Aguardar o DOM estar completamente carregado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Monitorar quando modais são abertos
    document.addEventListener('show.bs.modal', function(e) {
        setTimeout(fixModalBackdrop, 10);
    });
    
    // Monitorar quando modais são fechados
    document.addEventListener('hidden.bs.modal', function(e) {
        // Remover backdrops órfãos
        const backdrops = document.querySelectorAll('.modal-backdrop');
        const openModals = document.querySelectorAll('.modal.show');
        if (openModals.length === 0 && backdrops.length > 0) {
            backdrops.forEach(backdrop => backdrop.remove());
        }
    });
    
    function init() {
        // Listener específico para modais de variações e imagens
        const variationsModal = document.getElementById('variationsModal');
        const imagesModal = document.getElementById('imagesModal');
        
        if (variationsModal) {
            variationsModal.addEventListener('show.bs.modal', function() {
                setTimeout(fixModalBackdrop, 50);
            });
        }
        
        if (imagesModal) {
            imagesModal.addEventListener('show.bs.modal', function() {
                setTimeout(fixModalBackdrop, 50);
            });
        }
        console.log('🚀 Inicializando seleção de produtos...');
        
        const selectAllCheckbox = document.getElementById('selectAll');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        const selectedProductIds = document.getElementById('selectedProductIds');
        const clearSelectionBtn = document.getElementById('clearSelection');

        if (!selectAllCheckbox) {
            console.error('❌ Checkbox "Selecionar todos" não encontrado!');
            return;
        }

        // Função para obter todos os checkboxes visíveis na página atual
        function getVisibleCheckboxes() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            console.log('📋 Checkboxes encontrados:', checkboxes.length);
            return checkboxes;
        }

        // Função para atualizar a barra de ações
        function updateBulkActionsBar() {
            const productCheckboxes = getVisibleCheckboxes();
            const checkedBoxes = Array.from(productCheckboxes).filter(cb => cb.checked);
            const count = checkedBoxes.length;
            const ids = checkedBoxes.map(cb => cb.value);

            if (selectedCount) selectedCount.textContent = count;
            if (selectedProductIds) selectedProductIds.value = JSON.stringify(ids);

            if (bulkActionsBar) {
                if (count > 0) {
                    bulkActionsBar.classList.remove('d-none');
                } else {
                    bulkActionsBar.classList.add('d-none');
                }
            }

            // Atualizar estado do checkbox "Selecionar todos"
            if (selectAllCheckbox) {
                if (count === 0) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = false;
                } else if (count === productCheckboxes.length && productCheckboxes.length > 0) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = true;
                } else {
                    selectAllCheckbox.indeterminate = true;
                }
            }
        }

        // Selecionar todos (apenas os produtos visíveis na página atual)
        selectAllCheckbox.addEventListener('click', function(e) {
            e.stopPropagation();
            const productCheckboxes = getVisibleCheckboxes();
            const isChecked = this.checked;
            
            console.log('✅ Selecionar todos clicado. Marcando', productCheckboxes.length, 'produtos como:', isChecked);
            
            // Marcar/desmarcar todos os checkboxes visíveis
            productCheckboxes.forEach((cb, index) => {
                cb.checked = isChecked;
                console.log(`  - Produto ${index + 1} (ID: ${cb.value}): ${isChecked ? 'marcado' : 'desmarcado'}`);
            });
            
            updateBulkActionsBar();
        });

        // Atualizar quando checkbox individual é clicado
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('product-checkbox')) {
                console.log('📦 Checkbox individual alterado:', e.target.value, e.target.checked);
                updateBulkActionsBar();
            }
        });

        // Limpar seleção
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                const productCheckboxes = getVisibleCheckboxes();
                productCheckboxes.forEach(cb => {
                    cb.checked = false;
                });
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
                updateBulkActionsBar();
            });
        }

        // Função para submeter ação em massa
        window.submitBulkAction = function(action) {
            const productCheckboxes = getVisibleCheckboxes();
            const checkedBoxes = Array.from(productCheckboxes).filter(cb => cb.checked);
            
            if (checkedBoxes.length === 0) {
                alert('Por favor, selecione pelo menos um produto.');
                return false;
            }

            const actionText = action === 'mark_unavailable' ? 'marcar como indisponível' : 'marcar como disponível';
            if (confirm(`Tem certeza que deseja ${actionText} ${checkedBoxes.length} produto(s)?`)) {
                document.getElementById('bulkAction').value = action;
                document.getElementById('bulkActionForm').submit();
            }
        };

        // Inicializar
        updateBulkActionsBar();
        console.log('✅ Sistema de seleção inicializado com sucesso!');
    }
})();

// Função para normalizar valor digitado de forma inteligente
// Aceita: 2580, 2580.00, 2.580,00, 2580,50, etc
function normalizePrice(value) {
    if (!value || value === '') return null;
    
    // Remove espaços e caracteres não numéricos exceto vírgula e ponto
    let cleanValue = value.toString().trim().replace(/[^\d,.]/g, '');
    
    // Se não tem vírgula nem ponto, trata como valor direto (2580 = 2580.00)
    if (!cleanValue.includes(',') && !cleanValue.includes('.')) {
        return parseFloat(cleanValue);
    }
    
    // Se tem vírgula, assume formato brasileiro (2.580,00 ou 2580,50)
    if (cleanValue.includes(',')) {
        // Remove pontos (separadores de milhar brasileiro)
        cleanValue = cleanValue.replace(/\./g, '');
        // Substitui vírgula por ponto para parseFloat
        cleanValue = cleanValue.replace(',', '.');
        return parseFloat(cleanValue);
    }
    
    // Se tem ponto mas não vírgula
    if (cleanValue.includes('.')) {
        const parts = cleanValue.split('.');
        
        // Se tem apenas um ponto
        if (parts.length === 2) {
            // Se a parte após o ponto tem 1 ou 2 dígitos, é decimal (2580.50 ou 2580.5)
            if (parts[1].length <= 2) {
                return parseFloat(cleanValue);
            }
            // Se tem mais de 2 dígitos após o ponto, é separador de milhar (2.580)
            // Remove o ponto e trata como valor inteiro
            return parseFloat(parts.join(''));
        }
        
        // Se tem múltiplos pontos, são separadores de milhar (2.580.000)
        // Remove todos os pontos
        cleanValue = cleanValue.replace(/\./g, '');
        return parseFloat(cleanValue);
    }
    
    return parseFloat(cleanValue);
}

// Função para normalizar e atualizar preço
function normalizeAndUpdatePrice(productId, inputValue) {
    const normalizedPrice = normalizePrice(inputValue);
    
    if (normalizedPrice === null || normalizedPrice < 0 || isNaN(normalizedPrice)) {
        alert('Por favor, insira um preço de custo válido.');
        const input = document.querySelector(`[data-product-id='${productId}'].cost-price-field`);
        // Restaurar valor original
        const originalValue = input.getAttribute('data-original-value') || '0,00';
        input.value = originalValue;
        return;
    }
    
    const input = document.querySelector(`[data-product-id='${productId}'].cost-price-field`);
    const row = input.closest('tr');
    
    // Salvar valor original para possível rollback
    if (!input.getAttribute('data-original-value')) {
        input.setAttribute('data-original-value', input.value);
    }
    
    // Atualizar o valor exibido no campo com formato brasileiro
    input.value = normalizedPrice.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    // Chamar função de atualização com o valor normalizado
    updateProductPrice(productId, normalizedPrice);
}

// Função para atualizar preço de custo e recalcular B2B/B2C
function updateProductPrice(productId, costPrice) {
    if (!costPrice || costPrice <= 0 || isNaN(costPrice)) {
        alert('Por favor, insira um preço de custo válido.');
        return;
    }

    const input = document.querySelector(`[data-product-id='${productId}'].cost-price-field`);
    const row = input.closest('tr');
    
    // Mostrar loading
    const originalValue = input.getAttribute('data-original-value') || input.value;
    input.disabled = true;
    
    fetch(`/admin/products/${productId}/update-cost-price`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            cost_price: parseFloat(costPrice)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar exibição dos preços
            const priceEditor = row.querySelector('.price-editor');
            const b2cDisplay = priceEditor.querySelector('.b2c-price-display');
            const b2bDisplay = priceEditor.querySelector('.b2b-price-display');
            
            if (b2cDisplay) {
                b2cDisplay.textContent = data.product.b2c_price;
            }
            if (b2bDisplay) {
                b2bDisplay.textContent = data.product.b2b_price;
            }
            
            // Atualizar valor original salvo
            input.setAttribute('data-original-value', input.value);
            
            // Mostrar feedback visual
            input.classList.add('border-success');
            setTimeout(() => {
                input.classList.remove('border-success');
            }, 2000);
        } else {
            alert('Erro ao atualizar preço: ' + (data.message || 'Erro desconhecido'));
            // Restaurar valor original formatado
            const originalNormalized = normalizePrice(originalValue);
            if (originalNormalized !== null) {
                input.value = originalNormalized.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar preço. Tente novamente.');
        // Restaurar valor original formatado
        const originalNormalized = normalizePrice(originalValue);
        if (originalNormalized !== null) {
            input.value = originalNormalized.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    })
    .finally(() => {
        input.disabled = false;
    });
}

// Salvar margens de lucro
function saveMargins(e) {
    const b2bMarginInput = document.getElementById('b2b_margin');
    const b2cMarginInput = document.getElementById('b2c_margin');
    
    if (!b2bMarginInput || !b2cMarginInput) {
        alert('Erro: Campos de margem não encontrados. Por favor, recarregue a página.');
        console.error('Inputs não encontrados:', { b2b: b2bMarginInput, b2c: b2cMarginInput });
        return;
    }
    
    const b2bMargin = b2bMarginInput.value;
    const b2cMargin = b2cMarginInput.value;
    
    if (!b2bMargin || !b2cMargin || b2bMargin === '' || b2cMargin === '') {
        alert('Por favor, preencha ambas as margens.');
        return;
    }
    
    const b2bValue = parseFloat(b2bMargin);
    const b2cValue = parseFloat(b2cMargin);
    
    if (isNaN(b2bValue) || isNaN(b2cValue)) {
        alert('Por favor, insira valores numéricos válidos para as margens.');
        return;
    }
    
    // Mostrar loading
    let btn = null;
    if (e && e.target) {
        btn = e.target.closest('button');
    }
    if (!btn) {
        btn = document.getElementById('saveMarginsBtn');
    }
    if (!btn) {
        btn = document.querySelector('button[onclick*="saveMargins"]');
    }
    const originalBtnText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
    }
    
    fetch('/admin/products/save-margins', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            b2b_margin: b2bValue,
            b2c_margin: b2cValue
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Atualizar os valores nos inputs com os valores salvos confirmados
            if (data.data) {
                if (data.data.b2c_margin !== undefined) {
                    document.getElementById('b2c_margin').value = data.data.b2c_margin;
                }
                if (data.data.b2b_margin !== undefined) {
                    document.getElementById('b2b_margin').value = data.data.b2b_margin;
                }
            }
            alert('✅ Margens salvas com sucesso!\n\nB2C: ' + (data.data?.b2c_margin || b2cValue) + '%\nB2B: ' + (data.data?.b2b_margin || b2bValue) + '%');
        } else {
            alert('❌ Erro ao salvar margens: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('❌ Erro ao salvar margens: ' + error.message + '\n\nVerifique o console para mais detalhes.');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    });
}

// Aplicar margens a todos os produtos
function applyMarginsToAll(e) {
    if (!confirm('Tem certeza que deseja recalcular os preços B2B e B2C de TODOS os produtos baseado nas margens configuradas? Esta ação não pode ser desfeita.')) {
        return;
    }
    
    // Capturar o botão corretamente - tentar múltiplas formas
    let btn = null;
    if (e && e.target) {
        btn = e.target.closest('button');
    }
    if (!btn) {
        btn = document.getElementById('applyMarginsBtn');
    }
    if (!btn) {
        // Última tentativa: buscar pelo texto do botão
        const buttons = document.querySelectorAll('button');
        for (let button of buttons) {
            if (button.textContent.includes('Recalcular Todos os Preços')) {
                btn = button;
                break;
            }
        }
    }
    
    if (!btn) {
        alert('Erro: botão não encontrado. Por favor, recarregue a página.');
        console.error('Botão não encontrado. Elementos disponíveis:', document.querySelectorAll('button'));
        return;
    }
    
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Processando...';
    
    fetch('/admin/products/apply-margins-to-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message + '\n\nTotal de produtos processados: ' + (data.total_products || 0) + '\nProdutos atualizados: ' + (data.updated || 0));
            location.reload(); // Recarregar página para mostrar novos preços
        } else {
            alert('Erro ao recalcular preços: ' + (data.message || 'Erro desconhecido'));
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao recalcular preços: ' + error.message + '\n\nVerifique o console para mais detalhes.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Permitir Enter para salvar e formatação inteligente
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cost-price-field').forEach(input => {
        // Salvar valor original
        input.setAttribute('data-original-value', input.value);
        
        // Evento Enter
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                normalizeAndUpdatePrice(productId, this.value);
            }
        });
        
        // Formatar ao perder foco (se não foi salvo)
        input.addEventListener('blur', function(e) {
            // Só formata se não foi clicado no botão de salvar
            const normalized = normalizePrice(this.value);
            if (normalized !== null && normalized >= 0 && !isNaN(normalized)) {
                this.value = normalized.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else if (this.value && this.value !== '') {
                // Se o valor não é válido, restaurar original
                this.value = this.getAttribute('data-original-value') || '0,00';
            }
        });
    });
});
</script>
@endpush
@endsection
