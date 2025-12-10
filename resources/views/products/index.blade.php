@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="products-page-container">
    <!-- Header Section -->
    <div class="products-header">
        <div class="container">
            <div class="products-header-content">
                <div class="products-title-section">
                    <h1 class="products-main-title">
                        <i class="bi bi-grid me-2"></i>
                        Nossos Produtos
                    </h1>
                    <p class="products-subtitle">Encontre os melhores produtos para suas necessidades</p>
                </div>
                
                @if(request()->hasAny(['category', 'search']))
                    <div class="active-filters-bar">
                        <span class="filters-label">Filtros ativos:</span>
                        @if(request('category'))
                            @php $categoryName = $categories->firstWhere('slug', request('category'))->name ?? request('category'); @endphp
                            <span class="filter-badge">
                                {{ $categoryName }}
                                <a href="{{ route('products', request()->except('category')) }}" class="filter-remove">×</a>
                            </span>
                        @endif
                        @if(request('search'))
                            <span class="filter-badge">
                                "{{ request('search') }}"
                                <a href="{{ route('products', request()->except('search')) }}" class="filter-remove">×</a>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container products-content">
        <div class="products-layout">
            <!-- Filtros Sidebar -->
            <aside class="products-filters">
                <div class="filters-card">
                    <div class="filters-header">
                        <i class="bi bi-funnel me-2"></i>
                        <h5 class="filters-title">Filtros</h5>
                    </div>
                    <div class="filters-body">
                        <form method="GET" action="{{ route('products') }}" id="filtersForm">
                            <!-- Busca -->
                            <div class="filter-group">
                                <label for="search" class="filter-label">
                                    <i class="bi bi-search me-1"></i>
                                    Buscar Produto
                                </label>
                                <input type="text" 
                                       class="filter-input" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Nome do produto...">
                            </div>

                            <!-- Filtro por Categoria -->
                            @if($categories->count() > 0)
                                <div class="filter-group">
                                    <label for="category" class="filter-label">
                                        <i class="bi bi-tags me-1"></i>
                                        Categoria
                                    </label>
                                    <select class="filter-select" id="category" name="category">
                                        <option value="">Todas as categorias</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->slug }}" 
                                                    {{ request('category') == $category->slug ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Ordenação -->
                            <div class="filter-group">
                                <label for="sort" class="filter-label">
                                    <i class="bi bi-sort-down me-1"></i>
                                    Ordenar por
                                </label>
                                <select class="filter-select" id="sort" name="sort">
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nome A-Z</option>
                                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Menor Preço</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais Recentes</option>
                                </select>
                            </div>

                            <div class="filters-actions">
                                <button type="submit" class="btn-filter-primary">
                                    <i class="bi bi-funnel-fill me-2"></i>
                                    Aplicar Filtros
                                </button>
                                <a href="{{ route('products') }}" class="btn-filter-secondary">
                                    <i class="bi bi-x-circle me-2"></i>
                                    Limpar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Lista de Produtos -->
            <main class="products-list-section">
                <!-- Resultados Header -->
                <div class="results-header">
                    <div class="results-count">
                        <i class="bi bi-box-seam me-2"></i>
                        <span>Exibindo <strong>{{ $products->count() }}</strong> de <strong>{{ $products->total() }}</strong> produtos</span>
                    </div>
                </div>

                @if($products->count() > 0)
                    <!-- Grid de Produtos -->
                    <div class="products-grid">
                        @php $linkDept = $currentDepartmentSlug ?? request()->get('department') ?? null; @endphp
                        @foreach($products as $product)
                            <div class="product-card-item">
                                <div class="product-card-modern {{ $product->is_unavailable ? 'product-unavailable' : '' }}">
                                    <!-- Product Image -->
                                    <div class="product-image-container">
                                        <a href="{{ route('product', $product->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}" class="product-image-link">
                                            @php
                                                // Coletar todas as imagens únicas (produto + variações)
                                                $allImages = [];
                                                if ($product->first_image) {
                                                    $allImages[] = $product->first_image;
                                                }
                                                
                                                // Adicionar imagens das variações que têm imagens próprias
                                                if ($product->has_variations && $product->variations) {
                                                    foreach ($product->variations as $variation) {
                                                        if ($variation->images && is_array($variation->images) && !empty($variation->images)) {
                                                            foreach ($variation->images as $img) {
                                                                $imgUrl = strpos($img, 'http') === 0 ? $img : '/storage/' . ltrim($img, '/');
                                                                if (!in_array($imgUrl, $allImages)) {
                                                                    $allImages[] = $imgUrl;
                                                                }
                                                            }
                                                        } elseif ($variation->first_image && !in_array($variation->first_image, $allImages)) {
                                                            $allImages[] = $variation->first_image;
                                                        }
                                                    }
                                                }
                                                
                                                // Garantir pelo menos uma imagem
                                                if (empty($allImages)) {
                                                    $allImages[] = asset('images/no-image.svg');
                                                }
                                            @endphp
                                            
                                            <div class="product-image-carousel" data-product-id="{{ $product->id }}">
                                                @foreach($allImages as $index => $img)
                                                    <img src="{{ $img }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="product-image product-carousel-image {{ $index === 0 ? 'active' : '' }} @auth('admin') js-change-image @endauth"
                                                         @auth('admin') data-product-id="{{ $product->id }}" @endauth
                                                         loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                                         decoding="async"
                                                         data-index="{{ $index }}"
                                                         onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                                @endforeach
                                            </div>
                                            
                                            @if($product->is_unavailable)
                                                <div class="product-unavailable-overlay">
                                                    <span class="unavailable-badge">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                        Indisponível
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            @if($product->sale_price && $product->sale_price < $product->price)
                                                <div class="product-discount-badge">
                                                    <span>{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF</span>
                                                </div>
                                            @endif
                                        </a>
                                        
                                        @auth('admin')
                                            <div class="admin-actions-product" style="position: absolute; top: 8px; right: 8px; z-index: 10; display: flex; gap: 6px;">
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="btn-admin-edit-product"
                                                   title="Editar Produto"
                                                   style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid var(--secondary-color, #ff6b35); color: var(--secondary-color, #ff6b35); display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn-admin-delete-product"
                                                        title="Excluir Produto"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        data-product-slug="{{ $product->slug }}"
                                                        style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid var(--danger-color, #dc3545); color: var(--danger-color, #dc3545); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease; cursor: pointer;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endauth
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class="product-info">
                                        <a href="{{ route('product', $product->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}" class="product-title-link">
                                            <h3 class="product-title">{{ Str::limit($product->name, 60) }}</h3>
                                        </a>
                                        
                                        <div class="product-price-info">
                                            @if($product->sale_price && $product->sale_price < $product->price)
                                                <div class="price-row">
                                                    <span class="price-current">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</span>
                                                    <span class="price-original">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                                </div>
                                            @else
                                                <span class="price-current">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="product-status">
                                            @if($product->stock_quantity > 0)
                                                <span class="status-badge status-in-stock">
                                                    <i class="bi bi-check-circle-fill me-1"></i>
                                                    Em estoque
                                                </span>
                                            @else
                                                <span class="status-badge status-out-stock">
                                                    <i class="bi bi-x-circle-fill me-1"></i>
                                                    Fora de estoque
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Product Actions -->
                                    <div class="product-actions">
                                        <a href="{{ route('product', $product->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}" 
                                           class="btn-product-view">
                                            <i class="bi bi-eye me-2"></i>
                                            Ver Detalhes
                                        </a>
                                        
                                        @if(!$product->is_unavailable)
                                            <x-add-to-cart 
                                                :product="$product" 
                                                :showQuantity="false"
                                                buttonText="Adicionar"
                                                buttonClass="btn-product-add" />
                                        @else
                                            <button class="btn-product-disabled" disabled>
                                                <i class="bi bi-x-circle me-2"></i>
                                                Indisponível
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    @if($products->hasPages())
                        <div class="products-pagination">
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                @else
                    <!-- Nenhum produto encontrado -->
                    <div class="products-empty">
                        <div class="empty-icon">
                            <i class="bi bi-search"></i>
                        </div>
                        <h3 class="empty-title">Nenhum produto encontrado</h3>
                        <p class="empty-text">Tente ajustar os filtros ou fazer uma nova busca.</p>
                        <a href="{{ route('products') }}" class="btn-empty-action">
                            <i class="bi bi-arrow-left me-2"></i>
                            Ver Todos os Produtos
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Container Principal */
    .products-page-container {
        background: #f5f5f5;
        min-height: calc(100vh - 200px);
        padding-bottom: 2rem;
    }

    /* Header Section */
    .products-header {
        background: linear-gradient(135deg, var(--primary-color, #0f172a) 0%, var(--dark-bg, #1e293b) 100%);
        padding: 2rem 0;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .products-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .products-title-section {
        flex: 1;
    }

    .products-main-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-light, #ffffff);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .products-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        margin: 0;
    }

    .active-filters-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .filters-label {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        font-size: 0.9rem;
    }

    .filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--secondary-color, #ff6b35);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
    }

    .filter-remove {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.2rem;
        line-height: 1;
        transition: transform 0.2s;
    }

    .filter-remove:hover {
        transform: scale(1.2);
        color: white;
    }

    /* Content Layout */
    .products-content {
        padding-top: 1rem;
    }

    .products-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        align-items: start;
    }

    /* Filters Sidebar */
    .products-filters {
        position: sticky;
        top: 100px;
    }

    .filters-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid var(--border-color, #e2e8f0);
    }

    .filters-header {
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color), black 15%) 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
    }

    .filters-title {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .filters-body {
        padding: 1.5rem;
    }

    .filter-group {
        margin-bottom: 1.5rem;
    }

    .filter-label {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: var(--text-dark, #1e293b);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: white;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: var(--secondary-color, #ff6b35);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .filters-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
    }

    .btn-filter-primary,
    .btn-filter-secondary {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-filter-primary {
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color), black 15%) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .btn-filter-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
        color: white;
    }

    .btn-filter-secondary {
        background: white;
        color: var(--text-dark, #1e293b);
        border: 2px solid var(--border-color, #e2e8f0);
    }

    .btn-filter-secondary:hover {
        background: var(--border-color, #e2e8f0);
        color: var(--text-dark, #1e293b);
    }

    /* Products List Section */
    .products-list-section {
        flex: 1;
    }

    .results-header {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .results-count {
        display: flex;
        align-items: center;
        color: var(--text-dark, #1e293b);
        font-size: 0.9rem;
    }

    .results-count strong {
        color: var(--secondary-color, #ff6b35);
        font-weight: 600;
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.5rem;
    }

    /* Product Card */
    .product-card-item {
        animation: fadeInUp 0.4s ease-out;
        animation-fill-mode: both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card-modern {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: var(--secondary-color, #ff6b35);
    }

    .product-card-modern.product-unavailable {
        opacity: 0.7;
    }

    .product-image-container {
        position: relative;
        width: 100%;
        padding-top: 100%;
        background: #f8f9fa;
        overflow: hidden;
    }

    .product-image-link {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block;
    }

    .product-image-carousel {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .product-carousel-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 1.2s ease-in-out;
        z-index: 1;
    }

    .product-carousel-image.active {
        opacity: 1;
        z-index: 2;
    }

    .product-carousel-image:first-child {
        position: absolute;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .product-card-modern:hover .product-image {
        transform: scale(1.05);
    }

    .product-unavailable-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .unavailable-badge {
        background: var(--warning-color, #f59e0b);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    .product-discount-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, var(--danger-color, #ef4444) 0%, #dc2626 100%);
        color: white;
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.75rem;
        z-index: 3;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
    }

    .admin-actions-product {
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .product-card-modern:hover .admin-actions-product {
        opacity: 1;
    }

    .btn-admin-edit-product,
    .btn-admin-delete-product {
        position: relative;
        background: white;
        color: var(--secondary-color, #ff6b35);
        border: 2px solid var(--secondary-color, #ff6b35);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        opacity: 0.8;
    }

    .btn-admin-delete-product {
        color: var(--danger-color, #dc3545);
        border-color: var(--danger-color, #dc3545);
    }

    .product-card-modern:hover .btn-admin-edit-product,
    .product-card-modern:hover .btn-admin-delete-product {
        opacity: 1;
        transform: scale(1.1);
    }

    .btn-admin-edit-product:hover {
        background: var(--secondary-color, #ff6b35);
        color: white;
        transform: scale(1.15);
        box-shadow: 0 6px 16px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.5);
    }

    .btn-admin-delete-product:hover {
        background: var(--danger-color, #dc3545);
        color: white;
        transform: scale(1.15);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.5);
    }

    .product-info {
        padding: 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title-link {
        text-decoration: none;
        color: inherit;
        margin-bottom: 0.75rem;
    }

    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark, #1e293b);
        margin: 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.2s;
    }

    .product-title-link:hover .product-title {
        color: var(--secondary-color, #ff6b35);
    }

    .product-price-info {
        margin-bottom: 0.75rem;
    }

    .price-row {
        display: flex;
        align-items: baseline;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .price-current {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--secondary-color, #ff6b35);
    }

    .price-original {
        font-size: 0.9rem;
        color: var(--text-muted, #64748b);
        text-decoration: line-through;
    }

    .product-status {
        margin-bottom: 1rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-in-stock {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .status-out-stock {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .product-actions {
        padding: 0 1.25rem 1.25rem;
        display: flex;
        gap: 0.75rem;
    }

    .btn-product-view,
    .btn-product-add,
    .btn-product-disabled {
        flex: 1;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-product-view {
        background: white;
        color: var(--text-dark, #1e293b);
        border: 2px solid var(--border-color, #e2e8f0);
    }

    .btn-product-view:hover {
        background: var(--border-color, #e2e8f0);
        color: var(--text-dark, #1e293b);
    }

    .btn-product-add {
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color), black 15%) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .btn-product-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
        color: white;
    }

    .btn-product-disabled {
        background: var(--border-color, #e2e8f0);
        color: var(--text-muted, #64748b);
        cursor: not-allowed;
    }

    /* Empty State */
    .products-empty {
        background: white;
        border-radius: 16px;
        padding: 4rem 2rem;
        text-align: center;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--text-muted, #64748b);
        margin-bottom: 1.5rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-dark, #1e293b);
        margin-bottom: 0.75rem;
    }

    .empty-text {
        color: var(--text-muted, #64748b);
        margin-bottom: 2rem;
    }

    .btn-empty-action {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color), black 15%) 100%);
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .btn-empty-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
        color: white;
    }

    /* Pagination */
    .products-pagination {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
    }

    .products-pagination .pagination {
        gap: 0.5rem;
    }

    .products-pagination .page-link {
        color: var(--text-dark, #1e293b);
        border: 2px solid var(--border-color, #e2e8f0);
        padding: 0.625rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        background: white;
        min-width: 44px;
        text-align: center;
    }

    .products-pagination .page-link:hover {
        color: white;
        background: var(--secondary-color, #ff6b35);
        border-color: var(--secondary-color, #ff6b35);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .products-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color), black 15%) 100%);
        border-color: var(--secondary-color, #ff6b35);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .products-pagination .page-item.disabled .page-link {
        color: var(--text-muted, #64748b);
        background: var(--border-color, #e2e8f0);
        border-color: var(--border-color, #e2e8f0);
        cursor: not-allowed;
        opacity: 0.5;
    }

    /* Responsividade */
    @media (max-width: 992px) {
        .products-layout {
            grid-template-columns: 1fr;
        }

        .products-filters {
            position: static;
            order: 2;
        }

        .products-list-section {
            order: 1;
        }

        .products-header-content {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 768px) {
        .products-header {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }

        .products-main-title {
            font-size: 1.5rem;
        }

        .products-subtitle {
            font-size: 0.9rem;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .filters-card {
            border-radius: 12px;
        }

        .filters-body {
            padding: 1.25rem;
        }

        .filter-group {
            margin-bottom: 1.25rem;
        }

        .results-header {
            padding: 0.875rem 1rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .product-info {
            padding: 1rem;
        }

        .product-actions {
            padding: 0 1rem 1rem;
            flex-direction: column;
        }

        .btn-product-view,
        .btn-product-add {
            width: 100%;
        }

        .price-current {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .product-image-container {
            padding-top: 100%;
        }

        .product-title {
            font-size: 0.875rem;
        }

        .price-current {
            font-size: 1.125rem;
        }

        .btn-product-view,
        .btn-product-add {
            padding: 0.625rem 0.75rem;
            font-size: 0.8rem;
        }

        .products-empty {
            padding: 2.5rem 1.5rem;
        }

        .empty-icon {
            font-size: 3rem;
        }

        .empty-title {
            font-size: 1.25rem;
        }
    }

    /* Stagger animation for product cards */
    .product-card-item:nth-child(1) { animation-delay: 0.05s; }
    .product-card-item:nth-child(2) { animation-delay: 0.1s; }
    .product-card-item:nth-child(3) { animation-delay: 0.15s; }
    .product-card-item:nth-child(4) { animation-delay: 0.2s; }
    .product-card-item:nth-child(5) { animation-delay: 0.25s; }
    .product-card-item:nth-child(6) { animation-delay: 0.3s; }
    .product-card-item:nth-child(7) { animation-delay: 0.35s; }
    .product-card-item:nth-child(8) { animation-delay: 0.4s; }
    .product-card-item:nth-child(n+9) { animation-delay: 0.45s; }
</style>
@endsection

@section('scripts')
@stack('scripts')
<script>
    // Auto-submit form when filters change
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('#category, #sort');
        selects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
        
        // Carrossel automático de imagens de variações
        initVariationCarousels();
    });
    
    // Carrossel automático de imagens de variações
    function initVariationCarousels() {
        const carousels = document.querySelectorAll('.product-image-carousel');
        
        carousels.forEach(function(carousel) {
            const images = carousel.querySelectorAll('.product-carousel-image');
            
            // Só ativar se tiver mais de uma imagem
            if (images.length <= 1) {
                return;
            }
            
            let currentIndex = 0;
            const totalImages = images.length;
            
            // Intervalo de troca (3 segundos)
            const intervalTime = 3000;
            
            function showNextImage() {
                // Remover classe active da imagem atual
                images[currentIndex].classList.remove('active');
                
                // Avançar para próxima imagem
                currentIndex = (currentIndex + 1) % totalImages;
                
                // Adicionar classe active na nova imagem
                images[currentIndex].classList.add('active');
            }
            
            // Pausar ao passar o mouse
            let carouselInterval;
            
            function startCarousel() {
                carouselInterval = setInterval(showNextImage, intervalTime);
            }
            
            function stopCarousel() {
                if (carouselInterval) {
                    clearInterval(carouselInterval);
                }
            }
            
            // Inicializar carrossel
            startCarousel();
            
            // Pausar ao passar o mouse
            carousel.addEventListener('mouseenter', stopCarousel);
            carousel.addEventListener('mouseleave', startCarousel);
        });
    }
    
    // Botão de excluir produto
    document.querySelectorAll('.btn-admin-delete-product').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productSlug = this.dataset.productSlug;
            
            if (!confirm('Tem certeza que deseja excluir o produto "' + productName + '"?\n\nEsta ação não pode ser desfeita!')) {
                return;
            }
            
            // Criar formulário para DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/products/' + productSlug;
            
            // Adicionar CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
            form.appendChild(csrfInput);
            
            // Adicionar método DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            // Enviar formulário
            document.body.appendChild(form);
            form.submit();
        });
    });
</script>
@endsection
