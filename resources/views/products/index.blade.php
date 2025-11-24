@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">Nossos Produtos</h1>
            <p class="text-muted">Encontre os melhores produtos para suas necessidades</p>
        </div>
    </div>

    <div class="row">
        <!-- Filtros Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <!-- Busca -->
                    <form method="GET" action="{{ route('products') }}" class="mb-4">
                        <div class="mb-3">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nome do produto...">
                        </div>

                        <!-- Filtro por Categoria -->
                        @if($categories->count() > 0)
                            <div class="mb-3">
                                <label for="category" class="form-label">Categoria</label>
                                <select class="form-select" id="category" name="category">
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

                        <!-- Marca filter removed -->

                        <!-- Ordenação -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Ordenar por</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nome A-Z</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Menor Preço</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais Recentes</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('products') }}" class="btn btn-outline-secondary">Limpar Filtros</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="col-lg-9">
            <!-- Resultados -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted small">
                        Exibindo {{ $products->count() }} de {{ $products->total() }} produtos
                    </span>
                </div>
                
                @if(request()->hasAny(['category', 'search']))
                    <div class="active-filters">
                        <span class="text-muted me-2">Filtros ativos:</span>
                        @if(request('category'))
                            @php $categoryName = $categories->firstWhere('slug', request('category'))->name ?? request('category'); @endphp
                            <span class="badge bg-primary me-1">
                                {{ $categoryName }}
                                <a href="{{ route('products', request()->except('category')) }}" class="text-white ms-1">×</a>
                            </span>
                        @endif
                        {{-- brand removed --}}
                        @if(request('search'))
                            <span class="badge bg-primary me-1">
                                "{{ request('search') }}"
                                <a href="{{ route('products', request()->except('search')) }}" class="text-white ms-1">×</a>
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            @if($products->count() > 0)
                <!-- Grid de Produtos -->
                <div class="row">
                    @php $linkDept = $currentDepartmentSlug ?? request()->get('department') ?? null; @endphp
                    @foreach($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 product-card {{ $product->is_unavailable ? 'product-unavailable' : '' }}" 
                                 style="{{ $product->is_unavailable ? 'opacity: 0.6;' : '' }}">
                                <div class="card-img-top-container position-relative" style="height: 250px; overflow: hidden;">
                             @if($product->first_image)
                                <img src="{{ $product->first_image }}" 
                                    alt="{{ $product->name }}" 
                                    class="card-img-top @auth('admin') js-change-image @endauth"
                                    @auth('admin') data-product-id="{{ $product->id }}" style="height: 100%; object-fit: cover; cursor: pointer;" @else style="height: 100%; object-fit: cover;" @endauth
                                    onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                    @else
                                <img src="{{ asset('images/no-image.svg') }}" 
                                    alt="{{ $product->name }}" 
                                    class="card-img-top @auth('admin') js-change-image @endauth"
                                    @auth('admin') data-product-id="{{ $product->id }}" style="height: 100%; object-fit: cover; cursor: pointer;" @else style="height: 100%; object-fit: cover;" @endauth>
                                    @endif
                                    
                                    @if($product->is_unavailable)
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                                             style="background: rgba(0,0,0,0.5); z-index: 2;">
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                Indisponível no momento
                                            </span>
                                        </div>
                                    @endif
                                    
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <div class="position-absolute top-0 end-0 m-2" style="z-index: {{ $product->is_unavailable ? '1' : '3' }};">
                                            <span class="badge bg-danger">
                                                {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Brand display removed -->
                                    
                                    <h6 class="card-title">{{ Str::limit($product->name, 60) }}</h6>
                                    
                                    <div class="mt-auto">
                                        <div class="price-section mb-2">
                                            @if($product->sale_price && $product->sale_price < $product->price)
                                                <div class="d-flex align-items-center">
                                                    <span class="h6 text-primary mb-0">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</span>
                                                    <small class="text-muted text-decoration-line-through ms-2">R$ {{ number_format($product->price, 2, ',', '.') }}</small>
                                                </div>
                                            @else
                                                <span class="h6 text-primary">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="stock-status mb-2">
                                            @if($product->stock_quantity > 0)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Em estoque
                                                </small>
                                            @else
                                                <small class="text-danger">
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    Fora de estoque
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                                     <a href="{{ route('product', $product->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}" 
                                                         class="btn btn-outline-primary btn-sm">
                                            Ver Detalhes
                                        </a>
                                        
                                        @if(!$product->is_unavailable)
                                            <x-add-to-cart 
                                                :product="$product" 
                                                :showQuantity="false"
                                                buttonText="Adicionar ao Carrinho"
                                                buttonClass="btn btn-primary btn-sm" />
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="bi bi-x-circle me-1"></i>Indisponível
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginação -->
                @if($products->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            @else
                <!-- Nenhum produto encontrado -->
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>Nenhum produto encontrado</h4>
                    <p class="text-muted">Tente ajustar os filtros ou fazer uma nova busca.</p>
                    <a href="{{ route('products') }}" class="btn btn-primary">Ver Todos os Produtos</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #e9ecef;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .card-img-top-container {
        background: #f8f9fa;
    }
    
    .active-filters .badge {
        font-size: 0.75rem;
    }
    
    .active-filters a {
        text-decoration: none;
        color: white !important;
        font-weight: bold;
    }
    
    .active-filters a:hover {
        opacity: 0.8;
    }
    
    .price-section .text-decoration-line-through {
        font-size: 0.85rem;
    }
    
    .stock-status small {
        font-size: 0.75rem;
    }
    
    .card-footer {
        border-top: 1px solid #e9ecef;
    }
    
    .form-select, .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
    }
    
    .btn {
        border-radius: 0.375rem;
    }
    
    /* Estilos personalizados para paginação */
    .pagination {
        margin-bottom: 0;
        gap: 0.5rem;
    }
    
    .pagination .page-link {
        color: var(--primary-color);
        border: 1px solid var(--border-color);
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        background: white;
        min-width: 40px;
        text-align: center;
    }
    
    .pagination .page-link:hover {
        color: white;
        background: var(--accent-color);
        border-color: var(--accent-color);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .pagination .page-item.active .page-link {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: white;
        font-weight: 600;
    }
    
    .pagination .page-item.disabled .page-link {
        color: var(--text-muted);
        background: #f8f9fa;
        border-color: var(--border-color);
        cursor: not-allowed;
        opacity: 0.5;
    }
    
    .pagination .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
    }
    
    /* Responsividade da paginação */
    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.8rem;
            min-width: 35px;
        }
    }

    /* Responsividade Mobile */
    @media (max-width: 768px) {
        .container.py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        /* Filtros mobile - colapsar ou mover para cima */
        .col-lg-3 {
            order: 2;
            margin-bottom: 1.5rem;
        }

        .col-lg-9 {
            order: 1;
        }

        .card {
            border-radius: 0.5rem;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }

        .card-header h5 {
            font-size: 1rem;
            margin: 0;
        }

        .card-body {
            padding: 1rem;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            padding: 0.6rem 0.75rem;
            font-size: 0.9rem;
        }

        .btn {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* Grid de produtos mobile */
        .col-lg-4.col-md-6 {
            margin-bottom: 1rem;
        }

        .product-card {
            margin-bottom: 0;
        }

        .card-img-top-container {
            height: 200px !important;
        }

        .card-body {
            padding: 1rem;
        }

        .card-title {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .card-footer {
            padding: 0.75rem 1rem;
        }

        .card-footer .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        /* Resultados e filtros ativos */
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 0.75rem;
        }

        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .active-filters .badge {
            font-size: 0.7rem;
            padding: 0.35rem 0.6rem;
        }
    }

    @media (max-width: 480px) {
        h1.h2 {
            font-size: 1.5rem;
        }

        .text-muted {
            font-size: 0.85rem;
        }

        .col-lg-3 {
            margin-bottom: 1rem;
        }

        .card-header {
            padding: 0.6rem 0.75rem;
        }

        .card-body {
            padding: 0.75rem;
        }

        .form-control,
        .form-select {
            padding: 0.5rem 0.6rem;
            font-size: 0.85rem;
        }

        .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .card-img-top-container {
            height: 180px !important;
        }

        .card-title {
            font-size: 0.9rem;
        }

        .price-section .h6 {
            font-size: 1rem;
        }

        .price-section small {
            font-size: 0.75rem;
        }

        .stock-status small {
            font-size: 0.7rem;
        }
    }
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
    });
</script>
@endsection
