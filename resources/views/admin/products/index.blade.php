@extends('admin.layouts.app')

@section('title', 'Produtos')
@section('page-title', 'Gerenciar Produtos')
@section('page-subtitle')
    <p class="text-muted mb-0">Gerencie todos os produtos da loja</p>
@endsection

@section('content')
<!-- Header Melhorado -->
<div class="card mb-4 border-0 shadow-sm" data-section-id="products-hero" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-start align-items-md-center gap-3" style="min-width:0;">
                <div class="rounded-circle p-3 me-3" style="background-color: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                    <i class="bi bi-box-seam" style="font-size: 1.8rem; color: white;"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-white fw-bold">Gerenciar Produtos</h3>
                    @if(isset($departments) && $departments->count() > 0)
                        @php
                            $deptParam = request()->query('department');
                            $currentDept = null;
                            if ($deptParam) {
                                $currentDept = $departments->firstWhere('slug', $deptParam) ?: $departments->firstWhere('id', $deptParam);
                            }
                        @endphp
                        <div class="mt-2">
                            <div class="dropdown d-inline-block" style="min-width:0;">
                                <button class="btn btn-sm btn-white dropdown-toggle w-100 w-md-auto text-truncate" type="button" id="deptDropdownButton" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight:600; color:#374151; background-color: rgba(255,255,255,0.9);">
                                    @if($currentDept)
                                        {{ $currentDept->name }}
                                    @else
                                        Departamento
                                    @endif
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="deptDropdownButton">
                                    @foreach($departments as $d)
                                        <li><a class="dropdown-item dept-option" href="#" data-slug="{{ $d->slug ?? $d->id }}">{{ $d->name }}</a></li>
                                    @endforeach
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item dept-clear" href="#">Ver todos</a></li>
                                </ul>
                            </div>
                        </div>
                    @endif
                    <p class="mb-0 text-white" style="opacity: 0.9;">
                        <i class="bi bi-database me-1"></i>
                        <strong>{{ $products->total() }}</strong> produtos cadastrados
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto justify-content-md-end">
                <button type="button" class="btn btn-sm btn-outline-light section-toggle-btn d-flex align-items-center gap-2" data-section-target="products-hero" aria-expanded="true" title="Recolher sessão" style="font-weight:600;">
                    <span class="section-toggle-label">Recolher</span>
                    <i class="bi bi-chevron-up"></i>
                </button>
                <a href="{{ route('admin.products.import') }}" class="btn btn-light shadow-sm w-100 w-md-auto text-center" style="font-weight: 500;">
                    <i class="bi bi-upload me-1"></i> Importar
                </a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-white shadow w-100 w-md-auto text-center" style="font-weight: 600; background-color: white; color: #f97316;">
                    <i class="bi bi-plus-circle me-1"></i> Novo Produto
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Controlador Global de Margens de Lucro - Redesenhado -->
<div class="card mb-4 border-0 shadow-sm" data-section-id="global-margins" data-section-title="Controlador Global de Margens de Lucro" style="border-left: 4px solid #f97316 !important;">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(to right, rgba(249, 115, 22, 0.1) 0%, rgba(249, 115, 22, 0.05) 100%); border-bottom: 2px solid rgba(249, 115, 22, 0.2);">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-2" style="background-color: #f97316;">
                <i class="bi bi-sliders" style="color: white; font-size: 1rem;"></i>
            </div>
            <h6 class="mb-0 fw-bold" style="color: #f97316;">Controlador Global de Margens de Lucro</h6>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary section-toggle-btn d-flex align-items-center gap-2" data-section-target="global-margins" aria-expanded="true" title="Recolher sessão">
                <span class="section-toggle-label">Recolher</span>
                <i class="bi bi-chevron-up"></i>
            </button>
        </div>
    </div>
    <div class="card-body" style="background-color: #fafafa;">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-percent me-1"></i>Margem de Lucro B2B (%)
                </label>
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           id="global-profit-b2b" 
                           value="" 
                           placeholder="Ex: 10"
                           style="font-size: 1rem;">
                    <span class="input-group-text">%</span>
                </div>
                <small class="text-muted">Margem para clientes B2B</small>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-percent me-1"></i>Margem de Lucro B2C (%)
                </label>
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           id="global-profit-b2c" 
                           value="" 
                           placeholder="Ex: 20"
                           style="font-size: 1rem;">
                    <span class="input-group-text">%</span>
                </div>
                <small class="text-muted">Margem para clientes B2C</small>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-info-circle me-1"></i>Opções de Aplicação
                </label>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="apply-to-all-products" checked>
                    <label class="form-check-label" for="apply-to-all-products">
                        Aplicar a todos os produtos
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="recalculate-prices" checked>
                    <label class="form-check-label" for="recalculate-prices">
                        Recalcular preços automaticamente
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" 
                        class="btn w-100 fw-bold shadow-sm" 
                        id="apply-global-margins"
                        style="height: 38px; background-color: #f97316; border-color: #f97316; color: white;">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Aplicar
                </button>
            </div>
        </div>
        <div class="alert alert-info mt-3 mb-0 border-0" style="font-size: 0.875rem; background-color: rgba(13, 110, 253, 0.1); color: #084298;">
            <i class="bi bi-info-circle-fill me-2" style="color: #0d6efd;"></i>
            <strong>Atenção:</strong> Esta ação irá atualizar as margens de lucro e recalcular os preços de todos os produtos que possuem preço de custo definido. 
            Produtos sem custo não serão afetados.
        </div>
    </div>
</div>

<!-- Filtros - Redesenhado -->
<div class="card mb-4 border-0 shadow-sm" data-section-id="filters" data-section-title="Filtros e Busca">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(to right, rgba(249, 115, 22, 0.08) 0%, rgba(249, 115, 22, 0.03) 100%); border-bottom: 2px solid rgba(249, 115, 22, 0.15);">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-2" style="background-color: rgba(249, 115, 22, 0.15);">
                <i class="bi bi-funnel" style="color: #f97316; font-size: 0.9rem;"></i>
            </div>
            <h6 class="mb-0 fw-semibold" style="color: #374151;">Filtros e Busca</h6>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-outline-secondary section-toggle-btn d-flex align-items-center gap-2" data-section-target="filters" aria-expanded="true" title="Recolher sessão">
                <span class="section-toggle-label">Recolher</span>
                <i class="bi bi-chevron-up"></i>
            </button>
        </div>
    </div>
    <div class="card-body" style="background-color: #fafafa;">
        <form method="GET" id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-search me-1"></i>Buscar
                </label>
                <input type="text" class="form-control filter-input" name="search" value="{{ request('search') }}" 
                       placeholder="Nome, SKU ou descrição">
            </div>
            <!-- Marca filter removed per request -->
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-tags me-1"></i>Categoria
                </label>
                <select name="category" class="form-select filter-select">
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
                <select name="status" class="form-select filter-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-x-circle me-1"></i>Disponibilidade
                </label>
                <select name="availability" class="form-select filter-select">
                    <option value="">Todas</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Disponível</option>
                    <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Indisponível</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-boxes me-1"></i>Estoque
                </label>
                <select name="stock_status" class="form-select filter-select">
                    <option value="">Todos</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Estoque Baixo</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Sem Estoque</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">
                    <i class="bi bi-truck me-1"></i>Fornecedor
                </label>
                <select name="supplier" class="form-select filter-select">
                    <option value="">Todos</option>
                    @foreach(($suppliers ?? collect()) as $supplier)
                        <option value="{{ $supplier }}" {{ request('supplier') == $supplier ? 'selected' : '' }}>
                            {{ $supplier }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn w-100 shadow-sm" title="Filtrar" style="background-color: #f97316; border-color: #f97316; color: white; font-weight: 500;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        
        @if(request()->hasAny(['search', 'category', 'status', 'stock_status', 'supplier']))
            <div class="mt-3 p-2 d-flex align-items-center justify-content-between" style="background-color: white; border-radius: 6px; border-left: 3px solid #f97316;">
                <span class="text-muted me-2" style="font-size: 0.85rem;">
                    <i class="bi bi-funnel-fill me-1" style="color: #f97316;"></i>
                    <strong>Filtros ativos</strong>
                </span>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm shadow-sm" style="background-color: #f97316; border-color: #f97316; color: white; font-weight: 500;">
                    <i class="bi bi-x-circle me-1"></i>Limpar filtros
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Lista de Produtos -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($products->count() > 0)
            <!-- Barra de Ações em Massa - Redesenhada -->
            <div id="bulkActionsBar" class="m-3 p-3 rounded d-none" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(249, 115, 22, 0.05) 100%); border-left: 4px solid #f97316;">
                <form id="bulkActionForm" action="{{ route('admin.products.bulk-availability') }}" method="POST">
                    @csrf
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 me-2" style="background-color: #f97316;">
                                <i class="bi bi-check2-square" style="color: white; font-size: 1rem;"></i>
                            </div>
                            <div>
                                <span id="selectedCount" class="fw-bold" style="font-size: 1.5rem; color: #f97316;">0</span>
                                <span class="text-muted ms-1">produto(s) selecionado(s)</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" onclick="submitBulkAction('mark_unavailable')" class="btn btn-sm shadow-sm" style="background-color: #fbbf24; border-color: #fbbf24; color: white; font-weight: 500;">
                                <i class="bi bi-x-circle me-1"></i>Indisponível
                            </button>
                            <button type="button" onclick="submitBulkAction('mark_available')" class="btn btn-success btn-sm shadow-sm" style="font-weight: 500;">
                                <i class="bi bi-check-circle me-1"></i>Disponível
                            </button>
                            <button type="button" onclick="submitBulkAction('delete')" class="btn btn-danger btn-sm shadow-sm" style="font-weight: 500;">
                                <i class="bi bi-trash me-1"></i>Excluir
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
                <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                    <thead style="background: linear-gradient(to right, rgba(249, 115, 22, 0.08) 0%, rgba(249, 115, 22, 0.03) 100%); border-bottom: 2px solid rgba(249, 115, 22, 0.2);">
                        <tr>
                            <th class="text-center" style="width: 40px; padding: 16px 8px;">
                                <input type="checkbox" id="selectAll" class="form-check-input" title="Selecionar todos" style="cursor: pointer; width: 20px; height: 20px;">
                            </th>
                            <th style="width: 320px; padding: 16px; font-weight: 600; color: #374151; font-size: 0.95rem;">Produto</th>
                            <!-- Marca column removed -->
                            <th style="width: 240px; padding: 16px 8px; font-weight: 600; color: #374151; font-size: 0.95rem;">Preço e Margens</th>
                            <th class="text-center" style="width: 90px; padding: 16px 8px; font-weight: 600; color: #374151; font-size: 0.95rem;">Estoque</th>
                            <th class="text-center" style="width: 120px; padding: 16px 8px; font-weight: 600; color: #374151; font-size: 0.95rem;">Variações</th>
                            <th class="text-center" style="width: 120px; padding: 16px 8px; font-weight: 600; color: #374151; font-size: 0.95rem;">Status</th>
                            <th class="text-center" style="width: 110px; padding: 16px 8px; font-weight: 600; color: #374151; font-size: 0.95rem;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="product-row {{ $product->is_unavailable ? 'table-secondary opacity-75' : '' }}" 
                                id="product-{{ $product->id }}"
                                data-product-id="{{ $product->id }}"
                                style="cursor: pointer;">
                                <td class="text-center" style="padding: 14px 8px;">
                                    <input type="checkbox" 
                                           class="form-check-input product-checkbox" 
                                           value="{{ $product->id }}"
                                           data-product-id="{{ $product->id }}"
                                           onclick="event.stopPropagation();"
                                           style="width: 20px; height: 20px;">
                                </td>
                                <!-- COLUNA: Produto com Foto, Título e Descrição -->
                                <td style="padding: 12px;" onclick="event.stopPropagation();">
                                    <div class="d-flex gap-3">
                                        <!-- Miniatura -->
                                        <div class="flex-shrink-0">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="rounded product-thumbnail" 
                                                     style="width: 70px; height: 70px; object-fit: cover; cursor: pointer; transition: transform 0.2s;"
                                                     data-product-id="{{ $product->id }}"
                                                     data-product-name="{{ $product->name }}"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#imagesModal"
                                                     onmouseover="this.style.transform='scale(1.1)'"
                                                     onmouseout="this.style.transform='scale(1)'"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';"
                                                     loading="lazy" decoding="async"
                                                     title="Clique para editar imagens">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center product-thumbnail rounded" 
                                                     style="width: 70px; height: 70px; background-color: #f3f4f6; cursor: pointer; transition: transform 0.2s;"
                                                     data-product-id="{{ $product->id }}"
                                                     data-product-name="{{ $product->name }}"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#imagesModal"
                                                     onmouseover="this.style.transform='scale(1.1)'"
                                                     onmouseout="this.style.transform='scale(1)'"
                                                     title="Clique para adicionar imagens">
                                                    <i class="bi bi-image" style="font-size: 1.2rem; color: #d1d5db;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Título e Descrição -->
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <!-- Título e Badge -->
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="fw-semibold" style="font-size: 1rem; color: #1f2937; line-height: 1.3;">
                                                    {{ Str::limit($product->name, 40) }}
                                                </div>
                                                @if($product->is_featured)
                                                    <span class="badge bg-warning" style="font-size: 0.75rem; padding: 3px 7px;">
                                                        <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Descrição Editável -->
                                            <div class="description-preview-container" 
                                                 style="cursor: pointer; transition: all 0.2s ease; padding: 6px; margin: -6px; border-radius: 4px;"
                                                 data-product-id="{{ $product->id }}"
                                                 data-product-name="{{ $product->name }}"
                                                 data-product-description="{{ htmlspecialchars($product->description ?? '') }}"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#descriptionModal"
                                                 onmouseover="this.style.backgroundColor='rgba(249, 115, 22, 0.05)'"
                                                 onmouseout="this.style.backgroundColor='transparent'"
                                                 title="Clique para editar descrição completa">
                                                @if($product->description)
                                                    <div class="description-preview" style="font-size: 0.85rem; color: #6b7280; line-height: 1.5; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 4px;">
                                                        {{ $product->description }}
                                                    </div>
                                                    <small class="d-flex align-items-center" style="font-size: 0.75rem;">
                                                        <i class="bi bi-pencil me-1" style="color: #f97316; font-size: 0.75rem;"></i>
                                                        <span style="color: #f97316;">Editar descrição</span>
                                                    </small>
                                                @else
                                                    <div class="text-muted d-flex align-items-center" style="font-size: 0.8rem; padding: 6px 0;">
                                                        <i class="bi bi-file-text me-1" style="font-size: 0.95rem; color: #d1d5db;"></i>
                                                        <span style="color: #9ca3af;">Sem descrição</span>
                                                        <i class="bi bi-plus-circle ms-2 me-1" style="font-size: 0.8rem; color: #f97316;"></i>
                                                        <span style="color: #f97316;">Adicionar</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Marca cell removed -->
                                <td style="padding: 10px 8px;" onclick="event.stopPropagation();">
                                    @php
                                        $hasVariations = ($product->variations_count ?? 0) > 0;
                                    @endphp
                                    @if($hasVariations)
                                        <!-- PRODUTO COM VARIAÇÕES - Resumo + edição inline sob demanda -->
                                        <div class="d-flex flex-column gap-2" style="min-width: 220px; max-width: 260px;">
                                            <div class="px-3 py-2 d-flex align-items-center justify-content-between"
                                                 style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(249, 115, 22, 0.05) 100%); border-left: 3px solid #f97316; border-radius: 4px;">
                                                <small style="font-size: 0.8rem; font-weight: 600; color: #f97316;">
                                                    <i class="bi bi-list-ul me-1"></i>{{ $product->variations_count }} variações
                                                </small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                        class="btn btn-outline-secondary btn-sm toggle-inline-variations"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        style="font-size: 0.75rem; padding: 6px 8px; font-weight: 600;">
                                                    <i class="bi bi-chevron-down me-1"></i> Editar aqui
                                                </button>
                                                <button type="button"
                                                        class="btn btn-outline-warning btn-sm open-quick-variations"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        style="font-size: 0.75rem; padding: 6px 8px; font-weight: 600;">
                                                    <i class="bi bi-list-columns-reverse me-1"></i> Lista
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#variationsModal"
                                                        style="font-size: 0.75rem; padding: 6px 8px; background-color: #f97316; border-color: #f97316; color: white; font-weight: 600;"
                                                        onclick="event.stopPropagation();">
                                                    <i class="bi bi-gear me-1"></i> Modal
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <!-- PRODUTO SEM VARIAÇÕES - Mostrar campos editáveis do produto pai -->
                                        <div class="price-editor" data-product-id="{{ $product->id }}" style="min-width: 180px; max-width: 200px;">
                                            <!-- Custo -->
                                            <div class="mb-1">
                                                <div class="d-flex align-items-center mb-0">
                                                    <label class="form-label mb-0 me-1" style="font-size: 0.65rem; color: #6c757d; white-space: nowrap;">Custo:</label>
                                                    <div class="input-group input-group-sm flex-grow-1" style="min-width: 0;">
                                                        <span class="input-group-text" style="font-size: 0.65rem; padding: 1px 4px;">R$</span>
                                                        <input type="text" 
                                                               class="form-control form-control-sm cost-price-field" 
                                                               data-product-id="{{ $product->id }}"
                                                               value="{{ $product->cost_price ? number_format($product->cost_price, 2, ',', '.') : '0,00' }}" 
                                                               style="font-size: 0.7rem; padding: 1px 4px;"
                                                               placeholder="0,00"
                                                               onblur="normalizeAndUpdatePrice({{ $product->id }}, this.value)"
                                                               onclick="event.stopPropagation();">
                                                        <button class="btn btn-sm btn-outline-success" 
                                                                type="button" 
                                                                onclick="event.stopPropagation(); normalizeAndUpdatePrice({{ $product->id }}, document.querySelector('[data-product-id=\'{{ $product->id }}\'].cost-price-field').value)"
                                                                style="font-size: 0.65rem; padding: 1px 4px;"
                                                                title="Salvar">
                                                            <i class="bi bi-check" style="font-size: 0.7rem;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Margens de Lucro - Layout Compacto -->
                                            <div class="mb-1" style="border-top: 1px solid #e0e0e0; padding-top: 3px;">
                                                <div class="d-flex gap-1 align-items-center mb-1">
                                                    <label class="form-label mb-0 me-1" style="font-size: 0.6rem; color: #6c757d; white-space: nowrap; min-width: 45px;">B2B:</label>
                                                    <div class="input-group input-group-sm flex-grow-1" style="min-width: 0;">
                                                        <input type="text" 
                                                               class="form-control form-control-sm profit-b2b-field" 
                                                               data-product-id="{{ $product->id }}"
                                                               value="{{ $product->profit_margin_b2b ? ($product->profit_margin_b2b % 1 == 0 ? number_format($product->profit_margin_b2b, 0, ',', '.') : number_format($product->profit_margin_b2b, 2, ',', '.')) : '10' }}" 
                                                               style="font-size: 0.65rem; padding: 1px 3px;"
                                                               placeholder="10"
                                                               onblur="updateProfitMargin({{ $product->id }}, 'b2b', this.value)"
                                                               onclick="event.stopPropagation();">
                                                        <span class="input-group-text" style="font-size: 0.6rem; padding: 1px 3px;">%</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-1 align-items-center">
                                                    <label class="form-label mb-0 me-1" style="font-size: 0.6rem; color: #6c757d; white-space: nowrap; min-width: 45px;">B2C:</label>
                                                    <div class="input-group input-group-sm flex-grow-1" style="min-width: 0;">
                                                        <input type="text" 
                                                               class="form-control form-control-sm profit-b2c-field" 
                                                               data-product-id="{{ $product->id }}"
                                                               value="{{ $product->profit_margin_b2c ? ($product->profit_margin_b2c % 1 == 0 ? number_format($product->profit_margin_b2c, 0, ',', '.') : number_format($product->profit_margin_b2c, 2, ',', '.')) : '20' }}" 
                                                               style="font-size: 0.65rem; padding: 1px 3px;"
                                                               placeholder="20"
                                                               onblur="updateProfitMargin({{ $product->id }}, 'b2c', this.value)"
                                                               onclick="event.stopPropagation();">
                                                        <span class="input-group-text" style="font-size: 0.6rem; padding: 1px 3px;">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Preços Calculados - Layout Compacto -->
                                            <div class="calculated-prices" style="border-top: 1px solid #e0e0e0; padding-top: 3px;">
                                                @php
                                                    $b2bPrice = $product->b2b_price ?? $product->price;
                                                    $b2cPrice = $product->price;
                                                    $b2bMargin = $product->profit_margin_b2b ?? 10.00;
                                                    $b2cMargin = $product->profit_margin_b2c ?? 20.00;
                                                    $hasInvertedMargins = $b2bMargin > $b2cMargin;
                                                    $hasInvertedPrices = $b2bPrice > $b2cPrice;
                                                @endphp
                                                @if($hasInvertedMargins || $hasInvertedPrices)
                                                    <div class="alert alert-warning p-1 mb-1" style="font-size: 0.6rem; line-height: 1.1; padding: 2px 4px !important;">
                                                        <i class="bi bi-exclamation-triangle" style="font-size: 0.65rem;"></i>
                                                        <strong>B2B > B2C</strong>
                                                    </div>
                                                @endif
                                                <div class="d-flex justify-content-between align-items-center mb-0" style="font-size: 0.75rem; line-height: 1.2;">
                                                    <span class="text-success fw-semibold">B2C:</span>
                                                    <span class="text-success fw-semibold">R$ <span class="b2c-price-display">{{ number_format($b2cPrice, 2, ',', '.') }}</span></span>
                                                    <small class="text-muted" style="font-size: 0.6rem;">({{ number_format($b2cMargin, 0, ',', '.') }}%)</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem; line-height: 1.2;">
                                                    <span class="text-primary fw-semibold">B2B:</span>
                                                    <span class="text-primary fw-semibold">R$ <span class="b2b-price-display">{{ number_format($b2bPrice, 2, ',', '.') }}</span></span>
                                                    <small class="text-muted" style="font-size: 0.6rem;">({{ number_format($b2bMargin, 0, ',', '.') }}%)</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center" style="padding: 8px 4px;">
                                    <div style="line-height: 1.2; cursor: pointer; transition: transform 0.2s;"
                                         class="stock-trigger"
                                         data-product-id="{{ $product->id }}"
                                         data-product-name="{{ $product->name }}"
                                         data-current-stock="{{ $product->stock_quantity }}"
                                         data-bs-toggle="modal" 
                                         data-bs-target="#adjustStockModal"
                                         onclick="event.stopPropagation();"
                                         onmouseover="this.style.transform='scale(1.1)'"
                                         onmouseout="this.style.transform='scale(1)'"
                                         title="Clique para ajustar estoque">
                                        <span class="fw-semibold" style="font-size: 0.875rem;">{{ $product->stock_quantity }}</span>
                                        @if($product->stock_quantity <= ($product->min_stock ?? 0))
                                            <span class="badge bg-danger d-block mt-1" style="font-size: 0.7rem; padding: 2px 4px;">
                                                <i class="bi bi-exclamation-triangle" style="font-size: 0.7rem;"></i>
                                            </span>
                                        @elseif($product->stock_quantity == 0)
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
                                <td class="text-center" style="padding: 12px 8px;">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary variations-btn" 
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#variationsModal"
                                            style="font-size: 0.85rem; padding: 5px 12px;"
                                            title="Gerenciar Variações"
                                            onclick="event.stopPropagation();">
                                        <i class="bi bi-list-ul me-1"></i>
                                        <span>{{ $product->variations_count }}</span>
                                    </button>
                                </td>
                                <td class="text-center" style="padding: 12px 8px;">
                                    <div style="line-height: 1.4;">
                                        <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }} d-block" style="font-size: 0.8rem; padding: 4px 8px; margin-bottom: 4px;">
                                            <i class="bi bi-{{ $product->is_active ? 'check-circle' : 'x-circle' }}" style="font-size: 0.8rem;"></i>
                                        </span>
                                        @if($product->is_unavailable)
                                            <span class="badge bg-warning d-block" style="font-size: 0.8rem; padding: 4px 8px;">
                                                <i class="bi bi-exclamation-triangle" style="font-size: 0.8rem;"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center" style="padding: 12px 8px;">
                                    <div class="btn-group btn-group-sm" role="group" onclick="event.stopPropagation();">
                                        <a href="{{ route('admin.products.show', $product) }}" 
                                           class="btn btn-outline-info" title="Visualizar" style="padding: 6px 10px; font-size: 0.9rem;">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="btn btn-outline-primary" title="Editar" style="padding: 6px 10px; font-size: 0.9rem;">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Excluir" style="padding: 6px 10px; font-size: 0.9rem;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <!-- Linha de edição inline das variações (sob demanda) -->
                            <tr id="inline-variations-row-{{ $product->id }}" class="d-none">
                                <td colspan="8" class="bg-light">
                                    <div class="p-3 border-start" style="border-left: 4px solid #f97316 !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-pencil-square text-warning"></i>
                                                <strong>Edição de variações - {{ $product->name }}</strong>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-inline-variations" data-product-id="{{ $product->id }}">
                                                    <i class="bi bi-x-lg"></i> Fechar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-action="open-modal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-bs-toggle="modal" data-bs-target="#variationsModal">
                                                    <i class="bi bi-gear"></i> Abrir no modal
                                                </button>
                                            </div>
                                        </div>
                                        <div id="inline-variations-container-{{ $product->id }}" class="row g-2">
                                            <div class="text-muted small">Carregando...</div>
                                        </div>
                                        <div class="d-flex justify-content-center mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="inline-variations-load-more-{{ $product->id }}">
                                                Carregar mais
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação Melhorada -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-4" style="background-color: #fafafa; border-top: 2px solid rgba(249, 115, 22, 0.1);">
                <div class="mb-3 mb-md-0">
                    <p class="mb-0" style="color: #6b7280; font-size: 0.95rem;">
                        Mostrando 
                        <span class="fw-bold" style="color: #f97316;">{{ $products->firstItem() ?? 0 }}</span> a 
                        <span class="fw-bold" style="color: #f97316;">{{ $products->lastItem() ?? 0 }}</span> de 
                        <span class="fw-bold" style="color: #f97316;">{{ $products->total() }}</span> resultados
                    </p>
                </div>
                <div>
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="text-center py-5 m-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                     style="width: 120px; height: 120px; background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(249, 115, 22, 0.05) 100%);">
                    <i class="bi bi-box" style="font-size: 3.5rem; color: #f97316;"></i>
                </div>
                <h4 class="mb-3" style="color: #374151; font-weight: 600;">Nenhum produto encontrado</h4>
                <p class="text-muted mb-4" style="font-size: 1rem;">
                                @if(request()->hasAny(['search', 'category', 'status', 'stock_status', 'supplier']))
                        <i class="bi bi-funnel me-1"></i>
                        Nenhum produto corresponde aos filtros aplicados.
                    @else
                        <i class="bi bi-info-circle me-1"></i>
                        Comece criando seu primeiro produto para a loja.
                    @endif
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    @if(request()->hasAny(['search', 'category', 'status', 'stock_status', 'supplier']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i> Limpar Filtros
                        </a>
                    @endif
                    <a href="{{ route('admin.products.create') }}" class="btn shadow" style="background-color: #f97316; border-color: #f97316; color: white; font-weight: 500;">
                        <i class="bi bi-plus-circle me-1"></i> Criar Produto
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@section('styles')
<style>
    /* Mobile: transform product table into card-like rows, hide less important columns */
    @media (max-width: 768px) {
        .table-responsive thead { display: none !important; }
        .table-responsive, .table-responsive table { display: block; width: 100%; }
        .table-responsive tbody { display: block; }
        .table-responsive tbody tr { display: block; margin-bottom: 12px; border: 1px solid rgba(0,0,0,0.04); border-radius: 8px; padding: 8px; background: #fff; }
        .table-responsive tbody tr td { display: flex; align-items: center; justify-content: space-between; padding: 8px; }

        /* Keep checkbox, product and actions visible; hide detailed columns */
        .table-responsive tbody tr td:nth-child(1),
        .table-responsive tbody tr td:nth-child(2),
        .table-responsive tbody tr td:last-child { display: flex; }

        .table-responsive tbody tr td:nth-child(3),
        .table-responsive tbody tr td:nth-child(4),
        .table-responsive tbody tr td:nth-child(5),
        .table-responsive tbody tr td:nth-child(6) { display: none !important; }

        .product-thumbnail { width: 56px !important; height: 56px !important; }
        .product-row .flex-grow-1 { min-width: 0; }

        /* Actions: make touch-friendly and wrap if needed */
        .btn-group.btn-group-sm { display: flex; gap: 6px; }
        .btn-group.btn-group-sm .btn { padding: 8px 10px; font-size: 0.95rem; }

        /* Inline variations row should remain hidden by default on mobile */
        tr[id^="inline-variations-row-"] { display: none !important; }

        /* Adjust pagination and info spacing */
        .d-flex.flex-column.flex-md-row { gap: 12px; }
    }
</style>
@endsection

@push('modals')
<!-- Incluir Modal de Variações -->
@include('admin.products.modals.variations')

<!-- Incluir Modal de Imagens -->
@include('admin.products.modals.images')

<!-- Modal de Edição de Descrição -->
<div class="modal fade" id="descriptionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="descriptionForm" method="POST">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="bi bi-file-text me-2"></i>Editar Descrição do Produto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold d-flex align-items-center">
                            <i class="bi bi-box me-2" style="color: #f97316;"></i>
                            Produto
                        </label>
                        <p class="text-muted mb-0 fs-5 fw-semibold" id="description-product-name"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold d-flex align-items-center">
                            <i class="bi bi-file-text me-2" style="color: #f97316;"></i>
                            Descrição Completa
                        </label>
                        <textarea name="description" 
                                  id="description-content" 
                                  class="form-control" 
                                  rows="10" 
                                  placeholder="Digite a descrição completa do produto aqui..."
                                  style="resize: vertical; min-height: 200px;"></textarea>
                        <small class="text-muted mt-1 d-block">
                            <i class="bi bi-info-circle me-1"></i>
                            Descreva características, especificações técnicas, benefícios e diferenciais do produto.
                        </small>
                    </div>
                    
                    <div class="alert alert-info border-0" style="background-color: rgba(13, 110, 253, 0.1); color: #084298;">
                        <i class="bi bi-lightbulb-fill me-2" style="color: #0d6efd;"></i>
                        <strong>Dica:</strong> Uma boa descrição aumenta as chances de venda! Seja claro, objetivo e destaque os benefícios.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn shadow" style="background-color: #f97316; border-color: #f97316; color: white; font-weight: 500;">
                        <i class="bi bi-check-circle me-1"></i>Salvar Descrição
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Ajuste de Estoque -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="adjustStockForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-seam me-2"></i>Ajustar Estoque
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Produto</label>
                        <p class="text-muted mb-0" id="stock-product-name"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estoque Atual</label>
                        <p class="text-primary fs-4 mb-0" id="stock-current-value">0 unidades</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Ajuste</label>
                        <select name="type" id="stock-type" class="form-select" required>
                            <option value="in">Entrada (adicionar ao estoque)</option>
                            <option value="out">Saída (remover do estoque)</option>
                            <option value="adjustment">Ajuste Manual (definir novo valor)</option>
                        </select>
                        <small class="text-muted mt-1 d-block">
                            <strong>Entrada:</strong> adiciona ao estoque atual<br>
                            <strong>Saída:</strong> remove do estoque atual<br>
                            <strong>Ajuste:</strong> define um novo valor absoluto
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantity" id="stock-quantity" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="notes" id="stock-notes" class="form-control" rows="3" placeholder="Motivo do ajuste (opcional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Salvar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Offcanvas: Lista Rápida de Variações (rolagem vertical com todos os itens) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="quickVariationsOffcanvas" aria-labelledby="quickVariationsLabel">
  <div class="offcanvas-header" style="border-bottom:1px solid #eee;">
    <h5 class="offcanvas-title d-flex align-items-center gap-2" id="quickVariationsLabel">
        <i class="bi bi-list-columns-reverse text-warning"></i>
        <span>Variações</span>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <div id="quick-variations-filter" class="mb-2">
          <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control" id="quick-variations-search" placeholder="Filtrar por cor/ram/armazenamento/sku">
          </div>
          <small class="text-muted">Dica: Enter para focar próximo campo, setas para rolar.</small>
      </div>
      <div id="quick-variations-list" class="variations-price-list">
          <div class="text-muted small">Selecione um produto para carregar as variações…</div>
      </div>
  </div>
</div>
@endpush

@push('scripts')
<script>
// Handler global para trocar departamento via badge/dropdown
document.addEventListener('click', function(e){
    const opt = e.target.closest('.dept-option');
    if (opt) {
        e.preventDefault();
        const slug = opt.getAttribute('data-slug');
        const params = new URLSearchParams(window.location.search);
        if (slug) params.set('department', slug);
        else params.delete('department');
        params.delete('page');
        window.location.href = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
        return;
    }

    const clr = e.target.closest('.dept-clear');
    if (clr) {
        e.preventDefault();
        const params = new URLSearchParams(window.location.search);
        params.delete('department');
        params.delete('page');
        window.location.href = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
        return;
    }
});
</script>
@endpush

@push('modals')
@if(false)
<!-- Modal: Seleção de Departamento ao acessar a página de produtos -->
<div class="modal fade" id="selectDepartmentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); color: white;">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Escolha o departamento</h5>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Selecione para filtrar marcas, categorias e opções por departamento.</p>
                <div class="list-group">
                    @foreach($departments as $dept)
                        <button type="button" class="list-group-item list-group-item-action dept-select-btn" data-slug="{{ $dept->slug ?? $dept->id }}">
                            {{ $dept->name }}
                        </button>
                    @endforeach
                </div>
                <div class="mt-3 text-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="deptSkipBtn">Ver todos (sem departamento)</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            var selectModalEl = document.getElementById('selectDepartmentModal');
            if (!selectModalEl) return;

            var modal = new bootstrap.Modal(selectModalEl);
            // show modal shortly after load so layout settles
            setTimeout(function(){ modal.show(); }, 120);

            // Helper to redirect preserving other query params
            function redirectWithDepartment(deptSlug) {
                var params = new URLSearchParams(window.location.search);
                if (deptSlug) params.set('department', deptSlug);
                else params.delete('department');
                // ensure page resets to first page when changing department
                params.delete('page');
                var newUrl = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
                window.location.href = newUrl;
            }

            document.querySelectorAll('.dept-select-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var slug = this.getAttribute('data-slug');
                    redirectWithDepartment(slug);
                });
            });

            document.getElementById('deptSkipBtn').addEventListener('click', function() {
                // Close modal and keep user on page without department param
                modal.hide();
            });
        } catch (e) {
            console.error('Erro no modal de seleção de departamento', e);
        }
    });
</script>
@endif
@endpush

@section('styles')
<style>
    /* Estilos específicos para os modais de produtos */
    #variationsModal .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    #imagesModal .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    /* Estilos para linhas de produtos clicáveis */
    .product-row {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .product-row:hover {
        background: linear-gradient(to right, rgba(249, 115, 22, 0.04) 0%, rgba(249, 115, 22, 0.01) 100%) !important;
        box-shadow: 0 1px 3px rgba(249, 115, 22, 0.1);
        transform: translateX(2px);
    }
    
    .product-row:active {
        background-color: rgba(249, 115, 22, 0.08) !important;
    }
    
    /* Checkboxes maiores e mais clicáveis */
    .product-checkbox {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }
    
    .product-checkbox:checked {
        background-color: #f97316;
        border-color: #f97316;
    }
    
    /* Botões de ação com hover suave */
    .btn-group .btn {
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Cards com sombra suave */
    .card {
        transition: box-shadow 0.3s ease;
    }
    
    /* Inputs com foco laranja */
    .form-control:focus,
    .form-select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.15);
    }
    
    /* Badges modernos */
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
        padding: 0.35em 0.65em;
    }
    
    /* Animação no contador de seleção */
    #selectedCount {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Paginação personalizada */
    .pagination .page-link {
        color: #f97316;
        border-color: #dee2e6;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        color: #fff;
        background-color: #f97316;
        border-color: #f97316;
        transform: translateY(-1px);
    }
    
    .pagination .page-item.active .page-link {
        background-color: #f97316;
        border-color: #f97316;
    }
    
    /* Estilos para lista de variações */
    .variations-price-list {
        max-height: 450px;
        overflow-y: auto;
        overflow-x: visible;
    }
    
    .variations-price-list::-webkit-scrollbar {
        width: 4px;
    }
    
    .variations-price-list::-webkit-scrollbar-track {
        background: rgba(249, 115, 22, 0.05);
        border-radius: 2px;
    }
    
    .variations-price-list::-webkit-scrollbar-thumb {
        background: rgba(249, 115, 22, 0.3);
        border-radius: 2px;
    }
    
    .variations-price-list::-webkit-scrollbar-thumb:hover {
        background: rgba(249, 115, 22, 0.5);
    }
    
    .variation-price-item {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .variation-price-item:hover {
        box-shadow: 0 3px 8px rgba(249, 115, 22, 0.15);
        transform: translateX(2px);
        border-left-width: 3px;
    }
    
    /* Animação suave nos inputs das variações */
    .variation-cost-field:focus,
    .variation-b2b-field:focus,
    .variation-b2c-field:focus {
        border-color: #f97316 !important;
        box-shadow: 0 0 0 0.2rem rgba(249, 115, 22, 0.15) !important;
    }
    
    /* Estilo para os badges de variação */
    .variation-price-item .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Offcanvas - Lista rolável de variações */
    #quickVariationsOffcanvas .offcanvas-body { display: flex; flex-direction: column; padding-top: 0; }
    /* Ensure the quick-variations list accounts for the fixed admin header height */
    #quick-variations-list { overflow-y: auto; max-height: calc(100vh - var(--admin-header-height, 72px) - 110px); padding-right: 6px; }
    #quick-variations-list .variation-price-item { border-left: 2px solid #f97316; background: #fff; }
    /* Sticky filter should be positioned below the fixed admin header */
    #quick-variations-filter { position: sticky; top: calc(var(--admin-header-height, 72px) + 8px); background: #fff; z-index: 2; padding-top: 1rem; padding-bottom: .75rem; border-bottom: 1px solid #eee; }
</style>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    // Aguardar o DOM estar completamente carregado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        console.log('🚀 Inicializando seleção de produtos...');
        
        // Inicializar persistência de filtros
        initFilterPersistence();
        
        const selectAllCheckbox = document.getElementById('selectAll');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        const selectedProductIds = document.getElementById('selectedProductIds');
        const clearSelectionBtn = document.getElementById('clearSelection');

        if (!selectAllCheckbox) {
            console.error('❌ Checkbox "Selecionar todos" não encontrado!');
            return;
        }

        // Inicializar toggles de sessão (mostrar/ocultar) que persistem em localStorage
        (function initSectionToggles(){
            try{
                const toggles = document.querySelectorAll('.section-toggle-btn[data-section-target]');
                if(!toggles || toggles.length === 0) return;

                toggles.forEach(btn => {
                    const id = btn.getAttribute('data-section-target');
                    const selector = `[data-section-id="${id}"]`;
                    const el = document.querySelector(selector);
                    if(!el) return;

                    const storageKey = 'admin:section:' + id;

                    function applyState(state){
                        if(state === 'hidden'){
                            el.style.display = 'none';
                            btn.setAttribute('aria-expanded', 'false');
                            btn.title = 'Exibir sessão';
                            btn.innerHTML = '<span class="section-toggle-label">Exibir</span> <i class="bi bi-chevron-down"></i>';
                        } else {
                            el.style.display = '';
                            btn.setAttribute('aria-expanded', 'true');
                            btn.title = 'Recolher sessão';
                            btn.innerHTML = '<span class="section-toggle-label">Recolher</span> <i class="bi bi-chevron-up"></i>';
                        }
                    }

                    // restore
                    const saved = localStorage.getItem(storageKey);
                    if(saved === 'hidden') {
                        applyState('hidden');
                    } else {
                        applyState('visible');
                    }

                    // toggle on click
                    btn.addEventListener('click', function(e){
                        e.preventDefault();
                        const isHidden = localStorage.getItem(storageKey) === 'hidden';
                        if(!isHidden){
                            localStorage.setItem(storageKey, 'hidden');
                            applyState('hidden');
                        } else {
                            localStorage.removeItem(storageKey);
                            applyState('visible');
                        }
                    });
                });
            }catch(err){ console.error('section toggles init error', err); }
        })();

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

        // Quick Variations Offcanvas - carrega todas as variações em lista vertical
        document.addEventListener('DOMContentLoaded', function(){
            const offcanvasEl = document.getElementById('quickVariationsOffcanvas');
            if (!offcanvasEl) return;
            const offcanvas = new bootstrap.Offcanvas(offcanvasEl);
            const listEl = document.getElementById('quick-variations-list');
            const searchEl = document.getElementById('quick-variations-search');
            let currentProductId = null;
            let allItems = [];

            // Abrir por botão
            document.addEventListener('click', async function(e){
                const btn = e.target.closest('.open-quick-variations');
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();
                const productId = btn.getAttribute('data-product-id');
                const productName = btn.getAttribute('data-product-name') || 'Variações';
                currentProductId = productId;
                document.getElementById('quickVariationsLabel').querySelector('span').textContent = `Variações — ${productName}`;
                listEl.innerHTML = '<div class="smart-search-loading text-center py-4"><i class="bi bi-arrow-repeat"></i> Carregando…</div>';
                offcanvas.show();
                try {
                    const res = await fetch(`/admin/products/${productId}/variations`);
                    const data = await res.json();
                    if (!data.success) throw new Error(data.message || 'Erro ao carregar');
                    // Renderizar todos como lista única (rolagem vertical)
                    allItems = (data.variations || []).map(v => ({
                        id: v.id,
                        ram: v.ram || '',
                        storage: v.storage || '',
                        color: v.color || '',
                        sku: v.sku || '',
                        cost_price: v.cost_price || 0,
                        b2b_price: v.b2b_price || 0,
                        b2c_price: v.price || 0
                    }));
                    renderQuickList(allItems);
                    // Foco na busca
                    setTimeout(() => searchEl && searchEl.focus(), 100);
                } catch (err) {
                    listEl.innerHTML = `<div class="text-danger small">${err.message}</div>`;
                }
            }, true);

            // Filtro simples
            if (searchEl) {
                let t;
                searchEl.addEventListener('input', function(){
                    clearTimeout(t);
                    const q = this.value.toLowerCase().trim();
                    t = setTimeout(() => {
                        if (!q) return renderQuickList(allItems);
                        const filtered = allItems.filter(v =>
                            v.sku.toLowerCase().includes(q) ||
                            v.color.toLowerCase().includes(q) ||
                            v.ram.toLowerCase().includes(q) ||
                            v.storage.toLowerCase().includes(q)
                        );
                        renderQuickList(filtered);
                    }, 200);
                });
            }

            function renderQuickList(items){
                if (!items.length) {
                    listEl.innerHTML = '<div class="text-muted small">Nenhuma variação encontrada</div>';
                    return;
                }
                const frag = document.createDocumentFragment();
                listEl.innerHTML = '';
                items.forEach(v => {
                    const div = document.createElement('div');
                    div.className = 'variation-price-item mb-2 p-3 rounded';
                    div.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                ${v.color ? `<span class='badge bg-secondary-subtle text-secondary'>${v.color}</span>` : ''}
                                ${v.ram ? `<span class='badge bg-info-subtle text-info'>${v.ram}</span>` : ''}
                                ${v.storage ? `<span class='badge bg-primary-subtle text-primary'>${v.storage}</span>` : ''}
                            </div>
                            <small class="text-muted">${v.sku}</small>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-sm-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control variation-cost-field" data-variation-id="${v.id}" value="${(v.cost_price ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})}" placeholder="0,00" onblur="updateVariationPrice(${v.id}, 'cost_price', this.value)">
                                    <button class="btn btn-sm btn-outline-success" type="button" title="Salvar" onclick="updateVariationPrice(${v.id}, 'cost_price', this.previousElementSibling.value)"><i class="bi bi-check-lg"></i></button>
                                </div>
                                <small class="text-muted">Custo</small>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control variation-b2c-field" data-variation-id="${v.id}" value="${(v.b2c_price ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})}" placeholder="0,00" onblur="updateVariationPrice(${v.id}, 'b2c_price', this.value)">
                                    <button class="btn btn-sm btn-outline-success" type="button" title="Salvar" onclick="updateVariationPrice(${v.id}, 'b2c_price', this.previousElementSibling.value)"><i class="bi bi-check-lg"></i></button>
                                </div>
                                <small class="text-muted">B2C</small>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" class="form-control variation-b2b-field" data-variation-id="${v.id}" value="${(v.b2b_price ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})}" placeholder="0,00" onblur="updateVariationPrice(${v.id}, 'b2b_price', this.value)">
                                    <button class="btn btn-sm btn-outline-success" type="button" title="Salvar" onclick="updateVariationPrice(${v.id}, 'b2b_price', this.previousElementSibling.value)"><i class="bi bi-check-lg"></i></button>
                                </div>
                                <small class="text-muted">B2B</small>
                            </div>
                        </div>
                    `;
                    frag.appendChild(div);
                });
                listEl.appendChild(frag);
            }
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

        // Tornar linhas de produtos clicáveis para seleção
        document.querySelectorAll('.product-row').forEach(row => {
            row.addEventListener('click', function(e) {
                // Não fazer nada se o clique foi em um elemento interativo
                // (já tem stopPropagation nos elementos interativos)
                const checkbox = this.querySelector('.product-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateBulkActionsBar();
                }
            });
        });

        // Função para submeter ação em massa
        window.submitBulkAction = function(action) {
            const productCheckboxes = getVisibleCheckboxes();
            const checkedBoxes = Array.from(productCheckboxes).filter(cb => cb.checked);
            
            if (checkedBoxes.length === 0) {
                alert('Por favor, selecione pelo menos um produto.');
                return false;
            }

            let actionText = '';
            let confirmMessage = '';
            
            if (action === 'delete') {
                actionText = 'excluir';
                confirmMessage = `⚠️ ATENÇÃO: Esta ação é IRREVERSÍVEL!\n\nTem certeza que deseja excluir ${checkedBoxes.length} produto(s) selecionado(s)?\n\nTodos os dados, imagens e variações serão permanentemente removidos.`;
            } else if (action === 'mark_unavailable') {
                actionText = 'marcar como indisponível';
                confirmMessage = `Tem certeza que deseja ${actionText} ${checkedBoxes.length} produto(s)?`;
            } else if (action === 'mark_available') {
                actionText = 'marcar como disponível';
                confirmMessage = `Tem certeza que deseja ${actionText} ${checkedBoxes.length} produto(s)?`;
            }
            
            if (confirm(confirmMessage)) {
                document.getElementById('bulkAction').value = action;
                document.getElementById('bulkActionForm').submit();
            }
        };

        // Inicializar
        updateBulkActionsBar();
        console.log('✅ Sistema de seleção inicializado com sucesso!');
    }
    
    // Função para persistir filtros na URL
    function initFilterPersistence() {
        const filterForm = document.getElementById('filterForm');
        if (!filterForm) return;
        
        // Função para atualizar URL e submeter formulário
        function updateURL() {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();
            
            // Adicionar todos os parâmetros do formulário
            for (const [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    params.set(key, value);
                }
            }
            
            // Atualizar URL sem recarregar a página
            const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newURL);
            
            // Submeter formulário para aplicar filtros
            filterForm.submit();
        }
        
        // Restaurar valores dos filtros da URL ao carregar a página
        const urlParams = new URLSearchParams(window.location.search);
        
        // Restaurar valores dos selects
        filterForm.querySelectorAll('.filter-select').forEach(select => {
            const paramValue = urlParams.get(select.name);
            if (paramValue !== null) {
                select.value = paramValue;
            }
        });
        
        // Restaurar valor do input de busca
        const searchInput = filterForm.querySelector('.filter-input[name="search"]');
        if (searchInput) {
            const searchValue = urlParams.get('search');
            if (searchValue !== null) {
                searchInput.value = searchValue;
            }
        }
        
        // Auto-submit quando filtros mudarem (com debounce para o input de busca)
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    updateURL();
                }, 800); // Aguardar 800ms após parar de digitar
            });
            
            // Permitir Enter no input de busca para submeter imediatamente
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    updateURL();
                }
            });
        }
        
        // Auto-submit quando selects mudarem
        filterForm.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                updateURL();
            });
        });
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
            
            if (b2cDisplay && data.product.b2c_price) {
                b2cDisplay.textContent = data.product.b2c_price;
            }
            if (b2bDisplay && data.product.b2b_price) {
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

// Função para normalizar percentual (aceita valores como 10, 20, 10.5, 10,50, etc)
// Valores inteiros são tratados diretamente como porcentagem
function normalizePercent(value) {
    if (!value || value === '') return null;
    
    // Remove espaços e caracteres não numéricos exceto vírgula e ponto
    let cleanValue = value.toString().trim().replace(/[^\d,.]/g, '');
    
    // Se não tem vírgula nem ponto, trata como valor inteiro direto (10 = 10%, 20 = 20%)
    if (!cleanValue.includes(',') && !cleanValue.includes('.')) {
        return parseFloat(cleanValue);
    }
    
    // Se tem vírgula, assume formato brasileiro (10,50 ou 10,5)
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
            // Se a parte após o ponto tem 1 ou 2 dígitos, é decimal (10.5 ou 10.50)
            if (parts[1].length <= 2) {
                return parseFloat(cleanValue);
            }
            // Se tem mais de 2 dígitos após o ponto, é separador de milhar (1.000)
            // Remove o ponto e trata como valor inteiro
            return parseFloat(parts.join(''));
        }
        
        // Se tem múltiplos pontos, são separadores de milhar (1.000.000)
        // Remove todos os pontos
        cleanValue = cleanValue.replace(/\./g, '');
        return parseFloat(cleanValue);
    }
    
    return parseFloat(cleanValue);
}

// Função para atualizar margem de lucro
function updateProfitMargin(productId, type, inputValue) {
    const normalizedMargin = normalizePercent(inputValue);
    
    if (normalizedMargin === null || normalizedMargin < 0 || isNaN(normalizedMargin)) {
        alert('Por favor, insira uma margem de lucro válida (0% a 1000%).');
        const input = document.querySelector(`[data-product-id='${productId}'].profit-${type}-field`);
        // Restaurar valor original
        const originalValue = input.getAttribute('data-original-value') || (type === 'b2b' ? '10,00' : '20,00');
        input.value = originalValue;
        return;
    }
    
    // Validar margem máxima
    if (normalizedMargin > 1000) {
        alert('A margem de lucro não pode ser maior que 1000%.');
        const input = document.querySelector(`[data-product-id='${productId}'].profit-${type}-field`);
        const originalValue = input.getAttribute('data-original-value') || (type === 'b2b' ? '10,00' : '20,00');
        input.value = originalValue;
        return;
    }
    
    const input = document.querySelector(`[data-product-id='${productId}'].profit-${type}-field`);
    const row = input.closest('tr');
    
    // Salvar valor original para possível rollback
    if (!input.getAttribute('data-original-value')) {
        input.setAttribute('data-original-value', input.value);
    }
    
            // Atualizar o valor exibido no campo
            // Se for inteiro, mostra sem decimais; se tiver decimais, mostra até 2 casas
            if (normalizedMargin % 1 === 0) {
                // Valor inteiro - mostra sem decimais
                input.value = normalizedMargin.toString();
            } else {
                // Valor decimal - mostra com até 2 casas decimais
                input.value = normalizedMargin.toLocaleString('pt-BR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }
    
    // Mostrar loading
    const originalValue = input.getAttribute('data-original-value') || input.value;
    input.disabled = true;
    
    fetch(`/admin/products/${productId}/update-profit-margin`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            type: type,
            margin: parseFloat(normalizedMargin)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar exibição dos preços se houver custo
            const priceEditor = row.querySelector('.price-editor');
            const b2cDisplay = priceEditor.querySelector('.b2c-price-display');
            const b2bDisplay = priceEditor.querySelector('.b2b-price-display');
            
            if (b2cDisplay && data.product.b2c_price) {
                b2cDisplay.textContent = data.product.b2c_price;
            }
            if (b2bDisplay && data.product.b2b_price) {
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
            alert('Erro ao atualizar margem: ' + (data.message || 'Erro desconhecido'));
            // Restaurar valor original formatado
            const originalNormalized = normalizePercent(originalValue);
            if (originalNormalized !== null) {
                if (originalNormalized % 1 === 0) {
                    input.value = originalNormalized.toString();
                } else {
                    input.value = originalNormalized.toLocaleString('pt-BR', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                }
            }
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar margem. Tente novamente.');
        // Restaurar valor original formatado
        const originalNormalized = normalizePercent(originalValue);
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

// Função para atualizar preços das variações
function updateVariationPrice(variationId, field, inputValue) {
    const normalizedPrice = normalizePrice(inputValue);
    
    if (normalizedPrice === null || normalizedPrice < 0 || isNaN(normalizedPrice)) {
        alert('Por favor, insira um preço válido.');
        const input = document.querySelector(`[data-variation-id='${variationId}'].variation-${field.replace('_', '-')}-field`);
        const originalValue = input.getAttribute('data-original-value') || '0,00';
        input.value = originalValue;
        return;
    }
    
    const input = document.querySelector(`[data-variation-id='${variationId}']`);
    
    // Salvar valor original para possível rollback
    if (!input.getAttribute('data-original-value')) {
        input.setAttribute('data-original-value', input.value);
    }
    
    // Atualizar o valor exibido no campo com formato brasileiro
    const fieldInput = document.querySelector(`[data-variation-id='${variationId}'].variation-${field.replace('_', '-')}-field`);
    if (fieldInput) {
        fieldInput.value = normalizedPrice.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    // Encontrar todos os campos desta variação para possível atualização
    const variationContainer = fieldInput ? fieldInput.closest('.variation-price-item') : null;
    const costField = variationContainer ? variationContainer.querySelector('.variation-cost-field') : null;
    const b2bField = variationContainer ? variationContainer.querySelector('.variation-b2b-field') : null;
    const b2cField = variationContainer ? variationContainer.querySelector('.variation-b2c-field') : null;
    
    // Mostrar loading
    const originalValue = input.getAttribute('data-original-value') || input.value;
    if (fieldInput) fieldInput.disabled = true;
    
    fetch(`/admin/products/variations/${variationId}/update-price`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            field: field,
            value: parseFloat(normalizedPrice)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Se atualizou o custo, atualizar também B2B e B2C automaticamente
            if (field === 'cost_price' && data.formatted) {
                if (b2bField && data.formatted.b2b_price) {
                    b2bField.value = data.formatted.b2b_price;
                    b2bField.setAttribute('data-original-value', data.formatted.b2b_price);
                    b2bField.classList.add('border-success');
                    setTimeout(() => b2bField.classList.remove('border-success'), 2000);
                }
                if (b2cField && data.formatted.b2c_price) {
                    b2cField.value = data.formatted.b2c_price;
                    b2cField.setAttribute('data-original-value', data.formatted.b2c_price);
                    b2cField.classList.add('border-success');
                    setTimeout(() => b2cField.classList.remove('border-success'), 2000);
                }
            }
            
            // Atualizar valor original salvo
            if (fieldInput) {
                fieldInput.setAttribute('data-original-value', fieldInput.value);
                
                // Mostrar feedback visual
                fieldInput.classList.add('border-success');
                setTimeout(() => {
                    fieldInput.classList.remove('border-success');
                }, 2000);
            }
        } else {
            alert('Erro ao atualizar preço: ' + (data.message || 'Erro desconhecido'));
            // Restaurar valor original formatado
            const originalNormalized = normalizePrice(originalValue);
            if (originalNormalized !== null && fieldInput) {
                fieldInput.value = originalNormalized.toLocaleString('pt-BR', {
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
        if (originalNormalized !== null && fieldInput) {
            fieldInput.value = originalNormalized.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    })
    .finally(() => {
        if (fieldInput) fieldInput.disabled = false;
    });
}

// Permitir Enter para salvar e formatação inteligente
document.addEventListener('DOMContentLoaded', function() {
    // Campos de preço de custo
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
    
    // Campos de variações (custo, B2B, B2C)
    document.querySelectorAll('.variation-cost-field, .variation-b2b-field, .variation-b2c-field').forEach(input => {
        // Salvar valor original
        input.setAttribute('data-original-value', input.value);
        
        // Evento Enter
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const variationId = this.getAttribute('data-variation-id');
                let field = 'cost_price';
                if (this.classList.contains('variation-b2b-field')) field = 'b2b_price';
                if (this.classList.contains('variation-b2c-field')) field = 'b2c_price';
                updateVariationPrice(variationId, field, this.value);
            }
        });
        
        // Formatar ao perder foco
        input.addEventListener('blur', function(e) {
            const normalized = normalizePrice(this.value);
            if (normalized !== null && normalized >= 0 && !isNaN(normalized)) {
                this.value = normalized.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else if (this.value && this.value !== '') {
                this.value = this.getAttribute('data-original-value') || '0,00';
            }
        });
    });
    
    // Campos de margem de lucro
    document.querySelectorAll('.profit-b2b-field, .profit-b2c-field').forEach(input => {
        // Salvar valor original
        input.setAttribute('data-original-value', input.value);
        
        // Evento Enter
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const type = this.classList.contains('profit-b2b-field') ? 'b2b' : 'b2c';
                updateProfitMargin(productId, type, this.value);
            }
        });
        
        // Formatar ao perder foco (se não foi salvo)
        input.addEventListener('blur', function(e) {
            // Só formata se não foi clicado no botão de salvar
            const normalized = normalizePercent(this.value);
            if (normalized !== null && normalized >= 0 && normalized <= 1000 && !isNaN(normalized)) {
                // Se for inteiro, mostra sem decimais; se tiver decimais, mostra até 2 casas
                if (normalized % 1 === 0) {
                    this.value = normalized.toString();
                } else {
                    this.value = normalized.toLocaleString('pt-BR', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                }
            } else if (this.value && this.value !== '') {
                // Se o valor não é válido, restaurar original
                this.value = this.getAttribute('data-original-value') || (this.classList.contains('profit-b2b-field') ? '10' : '20');
            }
        });
    });
    
    // Controlador Global de Margens
    const applyGlobalMarginsBtn = document.getElementById('apply-global-margins');
    if (applyGlobalMarginsBtn) {
        applyGlobalMarginsBtn.addEventListener('click', function() {
            const profitB2B = document.getElementById('global-profit-b2b').value.trim();
            const profitB2C = document.getElementById('global-profit-b2c').value.trim();
            const recalculatePrices = document.getElementById('recalculate-prices').checked;
            
            // Validar que os campos foram preenchidos
            if (!profitB2B || !profitB2C) {
                alert('Por favor, preencha ambas as margens de lucro (B2B e B2C) antes de aplicar.');
                return;
            }
            
            // Normalizar valores
            const normalizedB2B = normalizePercent(profitB2B);
            const normalizedB2C = normalizePercent(profitB2C);
            
            // Validar valores
            if (normalizedB2B === null || normalizedB2B < 0 || normalizedB2B > 1000 || isNaN(normalizedB2B)) {
                alert('Por favor, insira uma margem de lucro B2B válida (0% a 1000%).');
                return;
            }
            
            if (normalizedB2C === null || normalizedB2C < 0 || normalizedB2C > 1000 || isNaN(normalizedB2C)) {
                alert('Por favor, insira uma margem de lucro B2C válida (0% a 1000%).');
                return;
            }
            
            // Confirmar ação
            const formatPercent = (val) => {
                if (val % 1 === 0) {
                    return val.toString();
                } else {
                    return val.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 2});
                }
            };
            
            const confirmMessage = `Deseja aplicar as margens de lucro a todos os produtos?\n\n` +
                                 `Margem B2B: ${formatPercent(normalizedB2B)}%\n` +
                                 `Margem B2C: ${formatPercent(normalizedB2C)}%\n\n` +
                                 (recalculatePrices ? 'Os preços serão recalculados automaticamente.\n\n' : '') +
                                 `Esta ação afetará todos os produtos que possuem preço de custo definido.`;
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            // Desabilitar botão e mostrar loading
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Aplicando...';
            
            // Fazer requisição
            fetch('/admin/products/apply-global-margins', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    profit_margin_b2b: normalizedB2B,
                    profit_margin_b2c: normalizedB2C,
                    recalculate_prices: recalculatePrices
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    alert(data.message);
                    
                    // Recarregar a página para atualizar os valores
                    window.location.reload();
                } else {
                    alert('Erro ao aplicar margens: ' + (data.message || 'Erro desconhecido'));
                    if (data.errors && data.errors.length > 0) {
                        console.error('Erros detalhados:', data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao aplicar margens. Tente novamente.');
            })
            .finally(() => {
                // Restaurar botão
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }
    
    // Modal de Ajuste de Estoque
    const adjustStockModal = document.getElementById('adjustStockModal');
    if (adjustStockModal) {
        adjustStockModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const productId = trigger.getAttribute('data-product-id');
            const productName = trigger.getAttribute('data-product-name');
            const currentStock = trigger.getAttribute('data-current-stock');
            
            // Atualizar informações no modal
            document.getElementById('stock-product-name').textContent = productName;
            document.getElementById('stock-current-value').textContent = currentStock + ' unidades';
            
            // Atualizar action do form
            const form = document.getElementById('adjustStockForm');
            form.action = `/admin/products/${productId}/adjust-stock`;
            
            // Limpar campos
            document.getElementById('stock-quantity').value = '';
            document.getElementById('stock-notes').value = '';
            document.getElementById('stock-type').value = 'in';
        });
        
        // Submeter formulário
        document.getElementById('adjustStockForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(adjustStockModal);
                    modal.hide();
                    
                    // Mostrar mensagem de sucesso
                    alert(data.message || 'Estoque ajustado com sucesso!');
                    
                    // Recarregar página
                    window.location.reload();
                } else {
                    alert('Erro: ' + (data.message || 'Não foi possível ajustar o estoque'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao ajustar estoque. Tente novamente.');
            });
        });
    }
    
    // Modal de Edição de Descrição
    const descriptionModal = document.getElementById('descriptionModal');
    if (descriptionModal) {
        descriptionModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const productId = trigger.getAttribute('data-product-id');
            const productName = trigger.getAttribute('data-product-name');
            const productDescription = trigger.getAttribute('data-product-description');
            
            // Atualizar informações no modal
            document.getElementById('description-product-name').textContent = productName;
            document.getElementById('description-content').value = productDescription || '';
            
            // Atualizar action do form
            const form = document.getElementById('descriptionForm');
            form.action = `/admin/products/${productId}/update-description`;
        });
        
        // Submeter formulário
        document.getElementById('descriptionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(descriptionModal);
                    modal.hide();
                    
                    // Mostrar mensagem de sucesso
                    alert(data.message || 'Descrição atualizada com sucesso!');
                    
                    // Recarregar página
                    window.location.reload();
                } else {
                    alert('Erro: ' + (data.message || 'Não foi possível atualizar a descrição'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao atualizar descrição. Tente novamente.');
            });
        });
    }
    
    // Destacar produto quando vier da busca inteligente
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    
    if (highlightId) {
        const productRow = document.getElementById(`product-${highlightId}`);
        if (productRow) {
            // Scroll suave até o produto
            setTimeout(() => {
                productRow.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Adicionar animação de destaque
                productRow.style.backgroundColor = 'rgba(249, 115, 22, 0.2)';
                productRow.style.transform = 'scale(1.02)';
                productRow.style.transition = 'all 0.3s ease';
                productRow.style.boxShadow = '0 4px 12px rgba(249, 115, 22, 0.3)';
                
                // Remover destaque após 3 segundos
                setTimeout(() => {
                    productRow.style.backgroundColor = '';
                    productRow.style.transform = '';
                    productRow.style.boxShadow = '';
                }, 3000);
            }, 500);
        }
    }
});
</script>
@endpush
@endsection

@push('scripts')
<script>
(function(){
    'use strict';
    // Cache simples por produto
    window._inlineVarCache = window._inlineVarCache || {};

    // Delegação: abre/fecha e faz lazy-load
    document.addEventListener('click', async function(e) {
        const btn = e.target.closest('.toggle-inline-variations');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        const productId = btn.getAttribute('data-product-id');
        if (!productId) return;

        const row = document.getElementById(`inline-variations-row-${productId}`);
        const container = document.getElementById(`inline-variations-container-${productId}`);
        const loadMoreBtn = document.getElementById(`inline-variations-load-more-${productId}`);

        // Fechar outras linhas abertas para manter o DOM leve
        document.querySelectorAll('tr[id^="inline-variations-row-"]').forEach(r => {
            if (r.id !== `inline-variations-row-${productId}`) r.classList.add('d-none');
        });

        const isHidden = row.classList.contains('d-none');
        row.classList.toggle('d-none');
        if (!isHidden) return; // estava aberto e foi fechado

        // Buscar dados se necessário
        if (!window._inlineVarCache[productId]) {
            try {
                container.innerHTML = '<div class="text-muted small">Carregando...</div>';
                const res = await fetch(`/admin/products/${productId}/variations`);
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Erro ao carregar');
                window._inlineVarCache[productId] = {
                    variations: Array.isArray(data.variations) ? data.variations : [],
                    rendered: 0
                };
            } catch (err) {
                container.innerHTML = `<div class="text-danger small">${err.message}</div>`;
                return;
            }
        }

        // Primeira renderização
        renderInlineVariationsBatch(productId, 10);

        if (loadMoreBtn) {
            loadMoreBtn.onclick = () => renderInlineVariationsBatch(productId, 10);
        }
    }, true);

    function renderInlineVariationsBatch(productId, batchSize){
        const cache = window._inlineVarCache[productId];
        const container = document.getElementById(`inline-variations-container-${productId}`);
        const loadMoreBtn = document.getElementById(`inline-variations-load-more-${productId}`);
        if (!cache || !container) return;

        const start = cache.rendered;
        const end = Math.min(cache.variations.length, start + batchSize);
        if (start === 0) container.innerHTML = '';

        for (let i = start; i < end; i++) {
            const v = cache.variations[i];
            const col = document.createElement('div');
            col.className = 'col-12 col-md-6 col-lg-4';
            col.innerHTML = `
                <div class="variation-price-item mb-2 p-3" style="background:#fff;border-left:2px solid #f97316;border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div class="d-flex align-items-center gap-1 mb-2 flex-wrap">
                        ${v.color ? `<span class='badge bg-secondary-subtle text-secondary'>${v.color}</span>` : ''}
                        ${v.ram ? `<span class='badge bg-info-subtle text-info'>${v.ram}</span>` : ''}
                        ${v.storage ? `<span class='badge bg-primary-subtle text-primary'>${v.storage}</span>` : ''}
                    </div>
                    <div class="prices-grid">
                        <div class="d-flex align-items-center mb-2">
                            <span style="font-size:0.75rem;color:#6c757d;min-width:40px;font-weight:500;">Custo</span>
                            <div class="input-group input-group-sm flex-grow-1">
                                <span class="input-group-text" style="font-size:0.75rem;padding:4px 6px;background-color:#f8f9fa;border-color:#dee2e6;">R$</span>
                                <input type="text" class="form-control form-control-sm variation-cost-field" data-variation-id="${v.id}" data-product-id="${productId}" value="${(v.cost_price ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})}" style="font-size:0.8rem;padding:4px 8px;border-color:#dee2e6;" placeholder="0,00" onblur="updateVariationPrice(${v.id}, 'cost_price', this.value)">
                                <button class="btn btn-sm" type="button" onclick="updateVariationPrice(${v.id}, 'cost_price', this.closest('.input-group').querySelector('.variation-cost-field').value)" style="font-size:0.75rem;padding:4px 8px;background-color:#f97316;border-color:#f97316;color:white;" title="Salvar"><i class="bi bi-check-lg"></i></button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span style="font-size:0.75rem;color:#198754;min-width:40px;font-weight:600;">B2C</span>
                            <div class="input-group input-group-sm flex-grow-1">
                                <span class="input-group-text" style="font-size:0.75rem;padding:4px 6px;background-color:#d1e7dd;border-color:#a3cfbb;color:#0a3622;">R$</span>
                                <input type="text" class="form-control form-control-sm variation-b2c-field" data-variation-id="${v.id}" data-product-id="${productId}" value="${(v.price ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})}" style="font-size:0.8rem;padding:4px 8px;border-color:#a3cfbb;font-weight:600;color:#198754;" placeholder="0,00" onblur="updateVariationPrice(${v.id}, 'b2c_price', this.value)">
                                <button class="btn btn-sm" type="button" onclick="updateVariationPrice(${v.id}, 'b2c_price', this.closest('.input-group').querySelector('.variation-b2c-field').value)" style="font-size:0.75rem;padding:4px 8px;background-color:#f97316;border-color:#f97316;color:white;" title="Salvar"><i class="bi bi-check-lg"></i></button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span style="font-size:0.75rem;color:#0d6efd;min-width:40px;font-weight:600;">B2B</span>
                            <div class="input-group input-group-sm flex-grow-1">
                                <span class="input-group-text" style="font-size:0.75rem;padding:4px 6px;background-color:#cfe2ff;border-color:#9ec5fe;color:#052c65;">R$</span>
                                <input type="text" class="form-control form-control-sm variation-b2b-field" data-variation-id="${v.id}" data-product-id="${productId}" value="${(v.b2b_price ?? 0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})}" style="font-size:0.8rem;padding:4px 8px;border-color:#9ec5fe;font-weight:600;color:#0d6efd;" placeholder="0,00" onblur="updateVariationPrice(${v.id}, 'b2b_price', this.value)">
                                <button class="btn btn-sm" type="button" onclick="updateVariationPrice(${v.id}, 'b2b_price', this.closest('.input-group').querySelector('.variation-b2b-field').value)" style="font-size:0.75rem;padding:4px 8px;background-color:#f97316;border-color:#f97316;color:white;" title="Salvar"><i class="bi bi-check-lg"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(col);
        }

        cache.rendered = end;
        if (loadMoreBtn) {
            loadMoreBtn.classList.toggle('d-none', end >= cache.variations.length);
        }
    }
})();
</script>
@endpush
