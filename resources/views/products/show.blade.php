@extends('layouts.app')

@section('title', $product->name)

@section('content')

<div class="pdp-container container py-3 py-md-5">
    <!-- Botão discreto para ir ao frete -->
    <a href="#shipping-calculator" class="shipping-quick-link-wrapper" title="Calcular frete">
        <span class="shipping-quick-link">
            <i class="bi bi-truck"></i>
            <span class="shipping-quick-text">Frete</span>
        </span>
        <span class="shipping-quick-hint">
            <span class="hint-text">
                <i class="bi bi-search"></i>
                <span class="typing-text">Selecione seu bairro</span>
                <span class="hint-example">ex: Residencial Paraíso</span>
            </span>
        </span>
    </a>

    <!-- Mobile Header: Title and Price First -->
    <div class="mobile-header-section">
        <div class="product-title-section">
            <h1 class="product-title">{{ $product->name }}</h1>
        </div>
    </div>

    <!-- Product Layout -->
    <div class="product-layout-ml">
        <!-- Left Column: Product Gallery -->
        <div class="product-gallery-ml">
        <!-- Thumbnails Column -->
        <div class="gallery-thumbnails-ml">
            <div class="thumbnails-list-ml">
                @php $images = $product->all_images ?? []; @endphp
                @forelse($images as $index => $image)
                    <div class="thumbnail-item-ml {{ $index === 0 ? 'active' : '' }}" 
                         data-image="{{ $image }}" 
                         data-index="{{ $index }}">
                        <img src="{{ $image }}"
                             alt="{{ $product->name }} - Imagem {{ $index + 1 }}"
                             class="thumbnail-img-ml"
                             onerror="this.src='{{ asset('images/no-image.svg') }}'">
                    </div>
                @empty
                    <div class="thumbnail-item-ml active" 
                         data-image="{{ asset('images/no-image.svg') }}" 
                         data-index="0">
                        <img src="{{ asset('images/no-image.svg') }}"
                             alt="{{ $product->name }} - Imagem 1"
                             class="thumbnail-img-ml">
                    </div>
                @endforelse
            </div>
        </div>
            
            <!-- Main Image Area -->
            <div class="gallery-main-ml">
                <div class="main-image-wrapper-ml {{ $product->is_unavailable ? 'unavailable' : '' }}" id="main-image-wrapper">
                    @if($product->is_unavailable)
                        <div class="unavailable-overlay-ml">
                            <span class="unavailable-badge-ml">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Indisponível no momento
                            </span>
                        </div>
                    @endif

                    <div class="zoom-lens-ml" id="zoom-lens"></div>
                    <img id="main-product-image" 
                         src="{{ $product->first_image }}" 
                         alt="{{ $product->name }}" 
                         class="main-image-ml"
                         onerror="this.src='{{ asset('images/no-image.svg') }}'">
                    
                    @if($product->hasMultipleImages())
                        <div class="image-counter-ml" id="imageCounter">
                            <span class="counter-text-ml">
                                <i class="fas fa-images me-1"></i>
                                <span id="current-image">1</span>/<span id="total-images">{{ $product->getImageCount() }}</span>
                            </span>
                        </div>
                        
                        <button type="button" class="gallery-nav-ml gallery-nav-prev-ml" id="prev-image">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="gallery-nav-ml gallery-nav-next-ml" id="next-image">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Product Details and Purchase -->
        <div class="product-details-ml slide-in-right">
            <div class="product-title-section d-none d-md-block">
                <h1 class="product-title">{{ $product->name }}</h1>
            </div>
            
            <div class="product-price-section d-none d-md-block">
                <div class="price-display-ml">
                    <span class="price-value-ml" id="product-price-display">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                    @if(!is_null($product->compare_price) && $product->compare_price > $product->price)
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <small class="text-muted compare-price-ml text-decoration-line-through">R$ {{ number_format($product->compare_price, 2, ',', '.') }}</small>
                            @php
                                $discountPercent = round((($product->compare_price - $product->price) / max($product->compare_price, 1)) * 100);
                            @endphp
                            <span class="discount-badge-ml">-{{ $discountPercent }}%</span>
                        </div>
                    @endif
                </div>
                <div class="price-subtitle">Preço à vista</div>
            </div>

            <!-- Purchase Section -->
            <div class="purchase-section-ml">
                <div class="purchase-card-ml">
                    <!-- Stock Status -->
                    <div class="stock-section">
                        <span class="stock-badge stock-available" id="stock-status" style="display: none;">
                            <i class="fas fa-check-circle me-1"></i>
                            <span id="stock-text">Em estoque</span>
                        </span>
                        <span class="stock-badge stock-unavailable" id="stock-unavailable" style="display: none;">
                            <i class="fas fa-times-circle me-1"></i>
                            Fora de estoque
                        </span>
                        @if(!$product->has_variations)
                            @if($product->stock_quantity > 0)
                                <span class="stock-badge stock-available">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Em estoque ({{ $product->stock_quantity }} unidades)
                                </span>
                            @else
                                <span class="stock-badge stock-unavailable">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Fora de estoque
                                </span>
                            @endif
                        @endif
                    </div>

                    <!-- SKU Display -->
                    <div class="sku-section">
                        <span class="sku-label">SKU:</span>
                        <span class="sku-value" id="product-sku-display">{{ $product->sku ?? 'Indisponível' }}</span>
                    </div>

                    <!-- Product Variations -->
                    @if(isset($product) && $product && $product->has_variations && isset($attributes) && $attributes instanceof \Illuminate\Database\Eloquent\Collection && $attributes->count() > 0)
                        <x-product-variations :product="$product" :productAttributes="$attributes" />
                    @endif

                    <!-- Quantity and Add to Cart -->
                    <div class="add-to-cart-section">
                        @if(!$product->is_unavailable)
                            <!-- Mobile Layout: Price + Quantity Side by Side -->
                            <div class="mobile-price-quantity-row d-md-none">
                                <div class="mobile-price-display">
                                    <span class="price-value-ml mobile-price">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                </div>
                                <div class="mobile-quantity-control">
                                    <div class="quantity-input-group">
                                        <button class="quantity-btn-ml" type="button" data-action="decrease" data-product-id="{{ $product->id }}">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input-ml" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" data-product-id="{{ $product->id }}" data-max-stock="{{ $product->stock_quantity }}">
                                        <button class="quantity-btn-ml" type="button" data-action="increase" data-product-id="{{ $product->id }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Desktop Layout -->
                            <div class="quantity-row d-none d-md-block">
                                <div class="quantity-control">
                                    <label for="quantity-{{ $product->id }}" class="quantity-label">Quantidade:</label>
                                    <div class="quantity-input-group">
                                        <button class="quantity-btn-ml" type="button" data-action="decrease" data-product-id="{{ $product->id }}">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input-ml" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" data-product-id="{{ $product->id }}" data-max-stock="{{ $product->stock_quantity }}">
                                        <button class="quantity-btn-ml" type="button" data-action="increase" data-product-id="{{ $product->id }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button class="btn-add-to-cart-ml" type="button" data-product-id="{{ $product->id }}" id="add-to-cart-btn">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span id="add-to-cart-text">Adicionar ao Carrinho</span>
                            </button>
                            <input type="hidden" id="selected-variation-id" name="variation_id" value="">
                        @else
                            <button class="btn-unavailable-ml" type="button" disabled>
                                <i class="bi bi-x-circle me-2"></i>
                                Indisponível no momento
                            </button>
                        @endif

                    <!-- Favorite Button -->
                        @auth('customer')
                            @php
                                $isFavorite = auth('customer')->user()->favorites()->where('product_id', $product->id)->exists();
                            @endphp
                            <button class="btn-favorite-ml btn-toggle-favorite mt-2" 
                                    type="button" 
                                    data-product-id="{{ $product->id }}"
                                    data-product-slug="{{ $product->slug }}"
                                    data-is-favorite="{{ $isFavorite ? 'true' : 'false' }}">
                                <i class="{{ $isFavorite ? 'bi bi-heart-fill' : 'bi bi-heart' }} me-2"></i>
                                <span class="favorite-text">{{ $isFavorite ? 'Remover dos Desejos' : 'Adicionar aos Desejos' }}</span>
                        </button>
                        @elseauth('admin')
                            @php
                                $isFavorite = auth('admin')->user()->favorites()->where('product_id', $product->id)->exists();
                            @endphp
                            <button class="btn-favorite-ml btn-toggle-favorite mt-2" 
                                    type="button" 
                                    data-product-id="{{ $product->id }}"
                                    data-product-slug="{{ $product->slug }}"
                                    data-is-favorite="{{ $isFavorite ? 'true' : 'false' }}">
                                <i class="{{ $isFavorite ? 'bi bi-heart-fill' : 'bi bi-heart' }} me-2"></i>
                                <span class="favorite-text">{{ $isFavorite ? 'Remover dos Desejos' : 'Adicionar aos Desejos' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn-favorite-ml mt-2 text-decoration-none">
                                <i class="bi bi-heart me-2"></i>
                                <span>Fazer login para favoritar</span>
                            </a>
                        @endauth
                    </div>

                </div>
            </div>
        </div>

        <!-- Additional Info Section -->
        <div class="product-info-ml slide-in-right">
            <!-- Trust Badges Section -->
            <div class="trust-badges-section">
                <div class="trust-badges-container-ml">
                    <div class="trust-badge-item-ml">
                        <div class="trust-icon-ml">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="trust-content-ml">
                            <h6 class="trust-title-ml">Garantia</h6>
                            <p class="trust-text-ml">Até 90 dias</p>
                        </div>
                    </div>
                    <div class="trust-badge-item-ml">
                        <div class="trust-icon-ml">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="trust-content-ml">
                            <h6 class="trust-title-ml">Frete Grátis</h6>
                            <p class="trust-text-ml">Em todo o site</p>
                        </div>
                    </div>
                    <div class="trust-badge-item-ml">
                        <div class="trust-icon-ml">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="trust-content-ml">
                            <h6 class="trust-title-ml">Troca Fácil</h6>
                            <p class="trust-text-ml">7 dias para devolução</p>
                        </div>
                    </div>
                    <div class="trust-badge-item-ml">
                        <div class="trust-icon-ml">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <div class="trust-content-ml">
                            <h6 class="trust-title-ml">Segurança</h6>
                            <p class="trust-text-ml">Compra protegida</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Calculator Section -->
            <div class="shipping-section-ml" id="shipping-calculator">
                @include('partials.shipping-calculator')
            </div>
        </div>
    </div>

    <!-- Seção de Feedbacks -->
    @include('components.product-feedbacks', ['product' => $product])

    <!-- Produtos Relacionados -->
    @if($relatedProducts->count() > 0)
        <div class="related-products-section">
                <h2 class="related-products-title">Produtos Relacionados</h2>
            <div class="row">
                    @php $linkDept = $currentDepartmentSlug ?? request()->get('department') ?? null; @endphp
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="product-card-modern">
                                <div class="product-card-image-container">
                                    @if($relatedProduct->first_image)
                                        <img src="{{ $relatedProduct->first_image }}" 
                                             alt="{{ $relatedProduct->name }}" 
                                             class="product-card-image">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="product-card-body">
                                    <h6 class="product-card-title">{{ Str::limit($relatedProduct->name, 60) }}</h6>
                                    <div class="product-card-price">
                                        @if($relatedProduct->sale_price && $relatedProduct->sale_price < $relatedProduct->price)
                                            <span class="product-card-price-current">R$ {{ number_format($relatedProduct->sale_price, 2, ',', '.') }}</span>
                                            <span class="product-card-price-original">R$ {{ number_format($relatedProduct->price, 2, ',', '.') }}</span>
                                        @else
                                            <span class="product-card-price-current">R$ {{ number_format($relatedProduct->price, 2, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="product-card-footer">
                                    <a href="{{ route('product', $relatedProduct->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}" 
                                       class="product-card-btn">Ver detalhes</a>
                                </div>
                            </div>
                        </div>
@endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    
    /* Container principal */
    .pdp-container.container {
        width: 100%;
        max-width: var(--site-container-max-width, 1320px);
        padding-left: var(--site-container-padding, 1.5rem);
        padding-right: var(--site-container-padding, 1.5rem);
        margin-left: auto;
        margin-right: auto;
        box-sizing: border-box;
    }
    
    /* Mobile Header Section */
    .mobile-header-section {
        padding-top: 1rem;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .mobile-header-section .product-title {
        font-size: 1.4rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 0;
        color: var(--text-dark);
    }

    /* Mobile Price + Quantity Layout */
    .mobile-price-quantity-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        margin-bottom: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1) 0%, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.05) 100%);
        border-radius: 12px;
        align-items: center;
            border: 2px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.2);
    }

    .mobile-price-display {
        justify-self: start;
    }

    .mobile-price {
            font-size: 1.75rem;
            font-weight: 800;
        color: var(--secondary-color);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .mobile-quantity-control {
        justify-self: end;
    }

    .mobile-quantity-control .quantity-input-group {
        display: flex;
        align-items: center;
        background: white;
        border: 2px solid var(--secondary-color);
        border-radius: 8px;
        overflow: hidden;
    }

    .mobile-quantity-control .quantity-btn-ml {
        background: var(--secondary-color);
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mobile-quantity-control .quantity-btn-ml:hover {
        background: var(--accent-color, #495a6d);
    }

    .mobile-quantity-control .quantity-input-ml {
        border: none;
        width: 45px;
        text-align: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .btn-add-to-cart-ml {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border: none;
            padding: 1.125rem 1.5rem;
        border-radius: 12px;
            font-weight: 700;
            font-size: 1.125rem;
        width: 100%;
        transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            bottom: 0;
            z-index: 100;
        }

        .btn-add-to-cart-ml:hover,
        .btn-add-to-cart-ml:active {
            background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), black 10%) 100%);
        transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.4);
        }

    /* Botão discreto para ir ao frete - Topo da página - COMPACTO - TUDO NA MESMA LINHA - TODOS CLICÁVEIS */
    .shipping-quick-link-wrapper {
        position: sticky;
        top: 80px;
        z-index: 500;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
        pointer-events: all;
        flex-wrap: nowrap !important;
        text-decoration: none !important;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .shipping-quick-link-wrapper:hover {
        transform: translateY(-2px);
    }

    .shipping-quick-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color, #ff6b35), black 10%) 100%);
        color: #ffffff;
        border-radius: 50px;
        text-decoration: none;
        font-size: 0.8125rem;
        font-weight: 700;
        box-shadow: 0 3px 12px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.35),
                    0 1px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }

    .shipping-quick-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s ease;
    }

    .shipping-quick-link:hover::before {
        left: 100%;
    }

    .shipping-quick-link:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 6px 20px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.45),
                    0 2px 8px rgba(0, 0, 0, 0.15);
        color: #ffffff;
        text-decoration: none;
    }

    .shipping-quick-link:active {
        transform: translateY(0) scale(1);
    }

    .shipping-quick-link i {
        font-size: 1.125rem;
        animation: truck-bounce 2s ease-in-out infinite;
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.2));
    }

    @keyframes truck-bounce {
        0%, 100% {
            transform: translateX(0) rotate(0deg);
        }
        25% {
            transform: translateX(-3px) rotate(-5deg);
        }
        75% {
            transform: translateX(3px) rotate(5deg);
        }
    }

    .shipping-quick-text {
        font-size: 0.875rem;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    /* Texto animado ao lado do badge - COMPACTO E COM BUSCA - TUDO NA MESMA LINHA - CLICÁVEL */
    .shipping-quick-hint {
        display: inline-flex !important;
        flex-direction: row !important;
        pointer-events: none;
        max-width: none;
        align-items: center !important;
        transition: all 0.3s ease;
    }
    
    .shipping-quick-link-wrapper:hover .shipping-quick-hint {
        transform: translateX(-3px);
    }

    .hint-text {
        font-size: 0.9375rem !important;
        font-weight: 700 !important;
        color: var(--text-dark, #333);
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 0.5rem;
        white-space: nowrap !important;
        line-height: 1.3;
        position: relative;
    }

    .hint-text i {
        font-size: 1.125rem !important;
        color: var(--secondary-color, #ff6b35);
        animation: search-pulse 2s ease-in-out infinite;
    }

    /* Animação de digitação para o texto */
    .hint-text::after {
        content: '';
        position: absolute;
        right: -2px;
        width: 2px;
        height: 1em;
        background: var(--secondary-color, #ff6b35);
        animation: blink-cursor 1s step-end infinite;
    }

    .hint-text .typing-text {
        display: inline-block;
        overflow: hidden;
        border-right: 2px solid var(--secondary-color, #ff6b35);
        white-space: nowrap;
        animation: typing 2.5s steps(20, end), blink-cursor 0.75s step-end infinite;
        animation-fill-mode: both;
    }

    .hint-example {
        font-size: 0.8125rem !important;
        color: var(--secondary-color, #ff6b35);
        font-weight: 600 !important;
        font-style: italic;
        margin-left: 0.5rem;
        opacity: 0.95;
        display: inline !important;
        white-space: nowrap !important;
        animation: fade-in-text 1s ease-in 2s both;
    }

    @keyframes typing {
        from {
            width: 0;
        }
        to {
            width: 100%;
        }
    }

    @keyframes blink-cursor {
        from, to {
            border-color: transparent;
        }
        50% {
            border-color: var(--secondary-color, #ff6b35);
        }
    }

    @keyframes fade-in-text {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 0.95;
            transform: translateX(0);
        }
    }

    @keyframes slide-in-right {
        from {
            opacity: 0;
            transform: translateX(-15px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse-gentle {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    @keyframes search-pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.15);
            opacity: 0.8;
        }
    }

    /* Mobile - ajustes responsivos */
    @media (max-width: 768px) {
        .shipping-quick-link-wrapper {
            top: 70px;
            margin-bottom: 0.5rem;
            gap: 0.5rem;
            justify-content: center;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
        }
        
        .shipping-quick-hint {
            display: inline-flex !important;
            flex-direction: row !important;
        }

        .shipping-quick-link {
            padding: 0.5rem 0.875rem;
            font-size: 0.75rem;
            box-shadow: 0 2px 8px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.3);
        }

        .shipping-quick-link i {
            font-size: 1rem;
        }

        .shipping-quick-text {
            font-size: 0.75rem;
        }

        .shipping-quick-hint {
            max-width: 100%;
            width: 100%;
            justify-content: center;
        }

        .hint-text {
            font-size: 0.8125rem !important;
            justify-content: center;
        }
        
        .hint-text i {
            font-size: 0.9375rem !important;
        }

        .hint-example {
            font-size: 0.75rem !important;
        }
    }

    @media (max-width: 480px) {
        .shipping-quick-link-wrapper {
            top: 65px;
            margin-bottom: 0.375rem;
            gap: 0.375rem;
        }

        .shipping-quick-link {
            padding: 0.4375rem 0.75rem;
            font-size: 0.6875rem;
        }

        .shipping-quick-link i {
            font-size: 0.9375rem;
        }

        .shipping-quick-text {
            font-size: 0.6875rem;
        }

        .hint-text {
            font-size: 0.75rem !important;
        }
        
        .hint-text i {
            font-size: 0.875rem !important;
        }

        .hint-example {
            font-size: 0.6875rem !important;
        }
    }

    /* Desktop - melhor posicionamento */
    @media (min-width: 769px) {
        .shipping-quick-link-wrapper {
            justify-content: flex-end;
            align-items: center;
        }

        .shipping-quick-hint {
            text-align: right;
        }
    }

    /* Layout usando Grid - Distribuição equilibrada */
    .product-layout-ml {
        display: grid;
        grid-template-columns: 4fr 3fr 2.5fr;
        gap: var(--grid-gap, 1.5rem);
        align-items: start;
            width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        }
        
    .product-layout-ml > * {
        min-width: 0;
            max-width: 100%;
        overflow: hidden;
        box-sizing: border-box;
    }

    /* Product Gallery */
    .product-gallery-ml {
        display: flex;
        gap: 0.75rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 2px 10px rgba(0, 0, 0, 0.06);
        padding: 0.25rem;
        overflow: hidden;
            width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    /* Responsive Layout - Mantém alinhamento com container */
    @media (max-width: 992px) {
        .product-layout-ml {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* Thumbnails Column */
    .gallery-thumbnails-ml {
        flex: 0 0 auto;
        background: rgba(248, 249, 250, 0.5);
        padding: 0.25rem;
    }

    .thumbnails-list-ml {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 500px;
        overflow-y: auto;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        align-items: center;
        justify-content: flex-start;
        padding: 0.5rem 0;
    }

    .thumbnails-list-ml::-webkit-scrollbar {
        width: 4px;
    }

    .thumbnails-list-ml::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    .thumbnails-list-ml::-webkit-scrollbar-thumb {
        background: var(--secondary-color);
        border-radius: 2px;
    }

    .thumbnail-item-ml {
        flex: 0 0 auto;
        width: 60px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background: #f8f9fa;
        margin: 0 auto;
    }

    .thumbnail-item-ml::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1) 0%, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 4px;
    }

    .thumbnail-item-ml:hover {
        border-color: var(--secondary-color);
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.25);
    }

    .thumbnail-item-ml:hover::before {
        opacity: 1;
    }

    .thumbnail-item-ml.active {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 2px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.2);
    }

    .thumbnail-item-ml.active::before {
        opacity: 1;
    }

    .thumbnail-img-ml {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Main Image Area */
    .gallery-main-ml {
        flex: 1;
        min-width: 0;
        padding: 0.25rem;
        display: flex;
        align-items: center;
        overflow: hidden;
    }

    .main-image-wrapper-ml {
        position: relative;
        width: 100%;
        max-width: 100%;
        height: auto;
        min-height: 300px;
        max-height: 500px;
        aspect-ratio: 3 / 4;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%),
                        linear-gradient(-45deg, #f8f9fa 25%, transparent 25%),
                        linear-gradient(45deg, transparent 75%, #f8f9fa 75%),
                        linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        cursor: zoom-in;
        overflow: hidden;
        border-radius: 16px;
        padding: 0.25rem;
        box-sizing: border-box;
    }
    
    @media (max-width: 1400px) {
        .main-image-wrapper-ml {
            aspect-ratio: 3 / 4;
            max-height: 450px;
        }
    }
    
    @media (max-width: 1200px) {
        .main-image-wrapper-ml {
            aspect-ratio: 3 / 4;
            max-height: 400px;
        }
    }
        transition: all 0.3s ease;
    }

    .main-image-wrapper-ml.unavailable {
        opacity: 0.6;
        pointer-events: none;
    }

    .main-image-wrapper-ml.zoomed {
        cursor: zoom-out;
    }

    .unavailable-overlay-ml {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 16px;
        z-index: 10;
    }

    .unavailable-badge-ml {
        background: #ffc107;
        color: #212529;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
    }

    .zoom-lens-ml {
        position: absolute;
        border: 2px solid var(--secondary-color);
        border-radius: 50%;
        width: 150px;
        height: 150px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
        box-shadow: 0 0 0 4px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1);
        z-index: 15;
    }

    .main-image-wrapper-ml.zoomed:hover .zoom-lens-ml {
        opacity: 1;
    }

    .main-image-ml {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 4px 20px rgba(0, 0, 0, 0.1));
    }

    .main-image-wrapper-ml.zoomed .main-image-ml {
        transform: scale(2);
        transform-origin: center;
    }

    /* Image Counter */
    .image-counter-ml {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        z-index: 5;
        backdrop-filter: blur(10px);
    }

    .counter-text-ml {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Gallery Navigation */
    .gallery-nav-ml {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 5;
        color: #333;
    }

    .gallery-nav-ml:hover {
        background: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .gallery-nav-prev-ml {
        left: 1rem;
    }

    .gallery-nav-next-ml {
        right: 1rem;
    }

    /* Product Trust Badges Section */
    .trust-badges-section {
        margin-top: 0.75rem;
    }

    .trust-badges-container-ml {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.875rem;
    }

    .trust-badge-item-ml {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 1rem;
        background: white;
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 1px solid #e8eaed;
        width: 100%;
        box-sizing: border-box;
    }

    .trust-badge-item-ml:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .trust-icon-ml {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .trust-content-ml {
        flex: 1;
        min-width: 0;
    }

    .trust-title-ml {
        font-size: 0.95rem;
        font-weight: 600;
        color: #333;
        margin: 0;
        line-height: 1.3;
    }

    .trust-text-ml {
        font-size: 0.875rem;
        color: #666;
        margin: 0;
        line-height: 1.3;
    }

    /* Purchase Section */
    .purchase-section-ml {
        margin-top: 1rem;
    }

    .purchase-card-ml {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .purchase-card-ml:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    /* Product Info Section */
    .product-info-ml {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .product-highlights-ml {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .highlight-item-ml {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e8eaed;
        transition: all 0.3s ease;
    }

    .highlight-item-ml:last-child {
        border-bottom: none;
    }

    .highlight-item-ml:hover {
        transform: translateX(4px);
    }

    .highlight-item-ml i {
        color: var(--secondary-color);
        font-size: 1.2rem;
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1);
        border-radius: 50%;
    }

    .highlight-text {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .highlight-title {
        font-weight: 600;
        color: #333;
        font-size: 0.95rem;
    }

    .highlight-subtitle {
        color: #666;
        font-size: 0.85rem;
        line-height: 1.3;
    }
    /* Product Details */
    .product-details-ml {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 0 0.5rem;
        overflow: hidden;
        word-wrap: break-word;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    /* Product Info Section - Compacto */
    .product-info-ml {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        overflow: hidden;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    /* Shipping Section - DESTAQUE MÁXIMO - Usuários querem saber de frete! */
    .shipping-section-ml {
        position: relative;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        animation: slideInUp 0.5s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .shipping-section-ml::before {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color), var(--secondary-color));
        border-radius: 16px;
        z-index: -1;
        opacity: 0.3;
        animation: glow-pulse 3s ease-in-out infinite;
    }

    @keyframes glow-pulse {
        0%, 100% {
            opacity: 0.3;
            transform: scale(1);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.02);
        }
    }
        font-size: 1rem;
    }
    
    .shipping-section-ml .card,
    .shipping-section-ml .shipping-widget {
        background: linear-gradient(135deg, #ffffff 0%, #fff9f7 50%, #ffffff 100%);
        border: 3px solid var(--secondary-color) !important;
        border-radius: 16px !important;
        box-shadow: 0 8px 24px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.25),
                    0 4px 12px rgba(0, 0, 0, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }

    .shipping-section-ml .card:hover,
    .shipping-section-ml .shipping-widget:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.35),
                    0 6px 16px rgba(0, 0, 0, 0.15) !important;
    }

    .shipping-section-ml .card::after {
        content: '🚚';
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 2rem;
        opacity: 0.1;
        pointer-events: none;
    }
        margin-bottom: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .shipping-section-ml .card-body {
        padding: 1.25rem;
    }
    
    .shipping-section-ml h6,
    .shipping-section-ml .card-body > h6 {
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        color: var(--secondary-color) !important;
        margin-bottom: 1.25rem !important;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1) 0%, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.05) 100%);
        border-radius: 12px;
        border-left: 4px solid var(--secondary-color);
        margin-left: -1rem;
        margin-right: -1rem;
        margin-top: -1rem;
    }

    .shipping-section-ml h6 i,
    .shipping-section-ml .card-body > h6 i {
        font-size: 1.75rem !important;
        color: var(--secondary-color) !important;
        animation: bounce-infinite 2s ease-in-out infinite;
    }

    @keyframes bounce-infinite {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }
        font-size: 1.1rem;
        margin-bottom: 1rem;
        font-weight: 600;
        color: var(--secondary-color, #3483fa);
    }
    
    .shipping-section-ml .nav-tabs {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 0.5rem;
        margin-bottom: 1.5rem !important;
        border: 2px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.2);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
        border-bottom: 2px solid rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1);
    }
    
    .shipping-section-ml .nav-link {
        font-weight: 700 !important;
        font-size: 1rem !important;
        padding: 0.875rem 1.25rem !important;
        border-radius: 10px !important;
        transition: all 0.3s ease !important;
        border: 2px solid transparent !important;
        color: var(--text-dark) !important;
        background: transparent !important;
        font-size: 0.95rem;
        padding: 0.6rem 1rem;
        color: #666;
        border: none;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .shipping-section-ml .nav-link:hover {
        color: var(--secondary-color, #3483fa);
        border-bottom-color: rgba(var(--secondary-color-rgb, 52, 131, 250), 0.3);
    }
    
    .shipping-section-ml .nav-link.active {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%) !important;
        color: #ffffff !important;
        border-color: var(--secondary-color) !important;
        box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.4) !important;
        transform: scale(1.05);
        color: var(--secondary-color, #3483fa);
        background: rgba(var(--secondary-color-rgb, 52, 131, 250), 0.05);
        border-bottom-color: var(--secondary-color, #3483fa);
        font-weight: 600;
    }
    
    .shipping-section-ml .form-label {
        font-weight: 700 !important;
        font-size: 1.125rem !important;
        color: var(--text-dark) !important;
        margin-bottom: 0.75rem !important;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: rgba(var(--secondary-color-rgb, 255, 107, 53), 0.08);
        border-radius: 8px;
        border-left: 3px solid var(--secondary-color);
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }
    
    .shipping-section-ml .form-control,
    .shipping-section-ml input,
    .shipping-section-ml textarea {
        font-size: 0.95rem;
        padding: 0.65rem 0.875rem;
        border-color: #ced4da;
        transition: all 0.3s ease;
    }
    
    .shipping-section-ml .form-control:focus {
        border-color: var(--secondary-color) !important;
        box-shadow: 0 0 0 4px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.2) !important;
        transform: translateY(-2px);
        border-color: var(--secondary-color, #3483fa);
        box-shadow: 0 0 0 0.2rem rgba(var(--secondary-color-rgb, 52, 131, 250), 0.25);
    }
    
    .shipping-section-ml .form-control:disabled {
        background-color: #f8f9fa;
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Ocultar CEP e Quantidade na aba Entrega Local */
    #local-pane .row.g-2.align-items-end {
        display: none !important;
    }
    
    /* Botão de verificar disponibilidade - MOSTRAR E DESTACAR */
    #btn-calc-frete-local {
        display: block !important;
        animation: pulse-button 2s ease-in-out infinite;
    }

    @keyframes pulse-button {
        0%, 100% {
            box-shadow: 0 6px 20px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.4);
        }
        50% {
            box-shadow: 0 8px 28px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.6),
                        0 0 0 8px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1);
        }
    }
    
    .shipping-section-ml .form-control-lg {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
    
    .shipping-section-ml .btn {
        font-size: 0.95rem;
        padding: 0.75rem 1.25rem;
        transition: all 0.3s ease;
    }
    
    .shipping-section-ml .btn-lg {
        font-size: 1rem;
        padding: 0.875rem 1.5rem;
        font-weight: 600;
    }
    
    .shipping-section-ml .btn-secondary,
    .shipping-section-ml #btn-calc-frete-local {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 1.125rem !important;
        padding: 1.125rem 1.5rem !important;
        border-radius: 12px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        box-shadow: 0 6px 20px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.4) !important;
        transition: all 0.3s ease !important;
        position: relative;
        overflow: hidden;
    }

    .shipping-section-ml .btn-secondary::before,
    .shipping-section-ml #btn-calc-frete-local::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .shipping-section-ml .btn-secondary:hover::before,
    .shipping-section-ml #btn-calc-frete-local:hover::before {
        width: 300px;
        height: 300px;
    }
        background: var(--secondary-color, #3483fa);
        border-color: var(--secondary-color, #3483fa);
        color: white;
    }
    
    .shipping-section-ml .btn-secondary:hover,
    .shipping-section-ml #btn-calc-frete-local:hover {
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), black 10%) 100%) !important;
        transform: translateY(-3px) scale(1.02) !important;
        box-shadow: 0 8px 24px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.5) !important;
        color: #ffffff !important;
    }
        background: color-mix(in srgb, var(--secondary-color, #3483fa) 90%, black);
        border-color: color-mix(in srgb, var(--secondary-color, #3483fa) 90%, black);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.4);
    }
    
    .shipping-section-ml .alert {
        border-radius: 12px !important;
        padding: 1rem 1.25rem !important;
        font-size: 1rem !important;
        border-left: 4px solid !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        font-size: 0.9rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        line-height: 1.5;
        border-radius: 8px;
    }
    
    .shipping-section-ml .alert-info {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
        color: #1976d2 !important;
        border-color: #2196f3 !important;
    }

    .shipping-section-ml .alert-success {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%) !important;
        color: #2e7d32 !important;
        border-color: #4caf50 !important;
        border: 3px solid #4caf50 !important;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3) !important;
    }
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1) 0%, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.05) 100%);
        border-color: rgba(var(--secondary-color-rgb, 52, 131, 250), 0.2);
        color: color-mix(in srgb, var(--secondary-color, #3483fa) 80%, black);
    }
    
    .shipping-section-ml .mb-3 {
        margin-bottom: 1rem;
    }
    
    .shipping-section-ml small {
        font-size: 0.85rem;
    }
    
    /* Animação chamativa para o buscador de região */
    @keyframes pulse-border {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(var(--secondary-color-rgb, 52, 131, 250), 0.4);
            border-color: #ced4da;
        }
        50% {
            box-shadow: 0 0 0 4px rgba(var(--secondary-color-rgb, 52, 131, 250), 0);
            border-color: var(--secondary-color, #3483fa);
        }
    }
    
    .shipping-section-ml #region-search-local {
        animation: pulse-border 2s ease-in-out infinite;
        transition: all 0.3s ease;
    }
    
    .shipping-section-ml #region-search-local:focus {
        animation: none;
        border-color: var(--secondary-color, #3483fa);
        box-shadow: 0 0 0 0.2rem rgba(var(--secondary-color-rgb, 52, 131, 250), 0.25);
    }
    
    .shipping-section-ml #region-search-local:hover {
        border-color: var(--secondary-color, #3483fa);
    }
    
    /* Garantir que elementos internos respeitem limites */
    .product-info-ml > *,
    .shipping-section-ml,
    .trust-badges-section {
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }
    
    .shipping-section-ml .card,
    .shipping-section-ml .card-body,
    .shipping-section-ml .form-control,
    .shipping-section-ml input,
    .shipping-section-ml textarea {
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }
    
    .trust-badges-container-ml {
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .product-details-ml * {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .product-title-section {
        order: 1;
        margin-bottom: 0.75rem;
    }

    .product-title {
        font-size: 1.75rem;
        font-weight: 400;
        color: #333;
        margin: 0;
        line-height: 1.3;
    }

    .product-price-section {
        order: 2 !important;
        padding: 0 0 1.5rem 0 !important;
        margin-bottom: 1.5rem !important;
        border-bottom: 1px solid #e6e6e6 !important;
        position: relative !important;
    }
    
    /* Garantir que o preço não apareça em outros lugares */
    .product-price-section:not(.d-none) {
        display: block !important;
    }
    
    /* Garantir que o preço apareça APENAS na seção correta (após o título) */
    @media (min-width: 768px) {
        /* O preço só deve aparecer dentro de .product-details-ml como primeiro filho após o título */
        .product-details-ml > .product-price-section.d-none.d-md-block {
            display: block !important;
            order: 2 !important;
        }
        
        /* Esconder qualquer preço duplicado que apareça em outros lugares */
        .purchase-section-ml ~ .product-price-section,
        .purchase-section-ml .product-price-section,
        .add-to-cart-section ~ .product-price-section,
        .add-to-cart-section .product-price-section,
        .add-to-cart-section ~ .price-display-ml,
        .btn-favorite-ml ~ .product-price-section,
        .btn-favorite-ml ~ .price-display-ml,
        .btn-favorite-ml ~ .price-value-ml,
        .purchase-card-ml > .product-price-section,
        .purchase-card-ml > .price-display-ml {
            display: none !important;
        }
        
        /* Se houver algum título + preço após os botões, esconder */
        .btn-favorite-ml ~ h1,
        .btn-favorite-ml ~ h2,
        .btn-favorite-ml ~ h3,
        .btn-favorite-ml ~ .product-title + .product-price-section,
        .add-to-cart-section ~ h1 + .price-value-ml {
            display: none !important;
        }
    }

    .price-display-ml {
        display: flex;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    
    /* Garantir que price-display-ml só apareça dentro de product-price-section no lugar correto */
    @media (min-width: 768px) {
        .price-display-ml:not(.product-price-section .price-display-ml) {
            display: none !important;
        }
        
        /* Garantir que price-display-ml dentro de product-price-section apareça */
        .product-price-section.d-none.d-md-block .price-display-ml {
            display: flex !important;
        }
        
        /* Esconder qualquer price-display-ml que apareça fora do lugar correto */
        .purchase-section-ml .price-display-ml,
        .add-to-cart-section .price-display-ml,
        .btn-favorite-ml ~ .price-display-ml,
        .purchase-card-ml .price-display-ml {
            display: none !important;
        }
    }

    .price-value-ml {
        font-size: 2.5rem;
        font-weight: 400;
        color: var(--secondary-color);
    }

    .compare-price-ml {
        font-size: 1.1rem;
        color: #999;
        text-decoration: line-through;
    }

    .discount-badge-ml {
        background: var(--secondary-color);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .price-subtitle {
        font-size: 0.9rem;
        color: #666;
    }


    .product-highlights-ml {
        order: 4;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        border: 1px solid #e8eaed;
        position: relative;
        overflow: hidden;
    }

    .product-highlights-ml::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        border-radius: 12px 12px 0 0;
    }

    .highlight-item-ml {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.95rem;
        color: #333;
        padding: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 10px;
        border: 1px solid #e8eaed;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .highlight-item-ml::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, rgba(32, 201, 151, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .highlight-item-ml:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: #28a745;
    }

    .highlight-item-ml:hover::before {
        opacity: 1;
    }

    .highlight-item-ml i {
        color: #28a745;
        font-size: 1.25rem;
        flex-shrink: 0;
        width: 24px;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .highlight-text {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        position: relative;
        z-index: 1;
    }

    .highlight-title {
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .highlight-subtitle {
        font-size: 0.8rem;
        color: #666;
        line-height: 1.3;
    }

    /* Right Column - Purchase Actions */
    .purchase-actions-ml {
        position: sticky;
        top: 20px;
    }

    .purchase-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e8eaed;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 10px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .purchase-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        border-radius: 20px 20px 0 0;
    }

    .purchase-card:hover {
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12), 0 4px 20px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .stock-section {
        padding: 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        text-align: center;
        border: 1px solid #e8eaed;
        position: relative;
        overflow: hidden;
    }

    .stock-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        border-radius: 12px 12px 0 0;
    }

    .stock-section.unavailable::before {
        background: linear-gradient(90deg, #dc3545 0%, #fd7e14 100%);
    }

    .stock-badge {
        font-size: 0.95rem;
        font-weight: 600;
        padding: 0.75rem 1.25rem;
        border-radius: 25px;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .stock-available {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #b8daff;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
    }

    .stock-unavailable {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 1px solid #f8d7da;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
    }

    .sku-section {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #666;
    }

    .sku-label {
        font-weight: 600;
    }

    .sku-value {
        font-family: monospace;
        background: #f5f5f5;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .add-to-cart-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .quantity-row {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .quantity-control {
        flex: 1;
    }

    .quantity-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .quantity-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
    }

    .quantity-btn-ml {
        background: #f5f5f5;
        border: none;
        padding: 0.75rem;
        cursor: pointer;
        transition: background 0.2s ease;
        color: #666;
    }

    .quantity-btn-ml:hover {
        background: #e0e0e0;
    }

    .quantity-input-ml {
        border: none;
        text-align: center;
        width: 60px;
        padding: 0.75rem;
        font-size: 1rem;
        font-weight: 500;
    }

    .btn-add-to-cart-ml {

.btn-add-to-cart-ml::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-add-to-cart-ml:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(var(--accent-color, #495a6d), 0.4);
}

.btn-add-to-cart-ml:hover::before {
    left: 100%;
}

.btn-add-to-cart-ml:active {
    transform: translateY(0);
}

.btn-unavailable-ml {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    border: none;
    padding: 1.25rem;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: not-allowed;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2);
    opacity: 0.8;
}
        border: none;
        padding: 1.25rem;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: not-allowed;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2);
        opacity: 0.8;
    }

    
    .shipping-section-ml .form-label,
    .shipping-section-ml .form-text,
    .shipping-section-ml small,
    .shipping-section-ml .alert,
    .shipping-section-ml p,
    .shipping-section-ml span,
    .shipping-section-ml label {
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
        line-height: 1.4;
    }
    
    .shipping-section-ml .btn {
        white-space: normal;
        word-wrap: break-word;
    }
    
    .shipping-section-ml .btn-lg {
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
    }

    .shipping-section-ml input,
    .shipping-section-ml textarea,
    .shipping-section-ml select,
    .shipping-section-ml button,
    .shipping-section-ml .form-control,
    .shipping-section-ml .btn {
        box-sizing: border-box;
    }

    .shipping-section {
        border-top: 1px solid #e6e6e6;
        padding-top: 1.5rem;
    }

    .shipping-widget-ml {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .shipping-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .shipping-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .shipping-subtitle {
        font-size: 0.75rem;
        color: #666;
    }

    .shipping-warning {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 0.5rem;
        font-size: 0.8rem;
        color: #856404;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .shipping-inputs {
        display: flex;
        gap: 0.75rem;
    }

    .shipping-input-group {
        flex: 1;
    }

    .shipping-quantity-group {
        width: 80px;
    }

    .shipping-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .shipping-input, .shipping-quantity-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .btn-calculate-shipping-ml {
        background: white;
        color: var(--secondary-color);
        border: 2px solid var(--secondary-color);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }

    .btn-calculate-shipping-ml:hover {
        background: var(--accent-color, #495a6d);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(var(--accent-color, #495a6d), 0.3);
    }

    .btn-calculate-shipping-ml:active {
        transform: translateY(-1px);
    }

    .shipping-actions {
        display: flex;
        justify-content: center;
    }

    .btn-group-shipping {
        display: flex;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .btn-shipping-option {
        background: white;
        border: none;
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-shipping-option.active {
        background: #3483fa;
        color: white;
    }

    .shipping-result, .shipping-selected {
        font-size: 0.85rem;
        color: #333;
    }

    .favorite-section {
        margin-top: 0.75rem;
    }

    .btn-favorite-ml {
        background: white;
        color: var(--secondary-color);
        border: 1px solid var(--secondary-color);
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-favorite-ml:hover {
        background: var(--accent-color, #495a6d);
        color: white;
    }

        cursor: default;
    }

    .trust-badge-item-ml:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-color: var(--secondary-color);
    }

    .trust-icon-ml {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.8rem;
    }

    .trust-content-ml {
        flex: 1;
    }

    .trust-title-ml {
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
        margin: 0;
        line-height: 1.2;
    }

    .trust-text-ml {
        font-size: 0.7rem;
        color: #666;
        margin: 0;
        line-height: 1.2;
    }

    .protection-section {
        padding-top: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .protection-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .protection-icon {
        background: var(--secondary-color);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .protection-text {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .protection-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
    }

    .protection-subtitle {
        font-size: 0.8rem;
        color: #666;
    }

    /* Enhanced Mobile Responsiveness */
    @media (max-width: 1024px) {
        .product-layout-ml {
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .purchase-actions-ml {
            grid-column: span 2;
            position: static;
        }

        .main-image-wrapper-ml {
            height: 400px;
        }
    }

    @media (max-width: 768px) {
        /* Container mobile com padding adequado */
        .pdp-container.container {
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: 0.75rem;
            padding-bottom: 80px; /* Espaço para bottom nav */
        }

        .product-layout-ml {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
            width: 100%;
            max-width: 100%;
        }

        .product-gallery-ml {
            width: 100%;
            max-width: 100%;
            flex-direction: column;
            height: auto;
            min-height: 400px;
            border-radius: 12px;
            padding: 0.5rem;
            margin-bottom: 0;
        }

        .gallery-thumbnails-ml {
            width: 100%;
            padding: 0.5rem;
            order: 2;
        }

        .thumbnails-list-ml {
            flex-direction: row;
            max-height: none;
            max-width: 100%;
            gap: 0.5rem;
            overflow-x: auto;
            justify-content: flex-start;
            padding: 0;
        }

        .thumbnail-item-ml {
            width: 60px;
            height: 60px;
        }

        .gallery-main-ml {
            flex: 1;
            padding: 0.5rem;
            align-items: center;
            order: 1;
        }

        .main-image-wrapper-ml {
            height: 400px;
            width: 100%;
            border-radius: 12px;
        }

        .gallery-main-ml {
            padding: 0.75rem;
        }

        .product-details-ml {
            width: 100%;
            max-width: 100%;
            order: 3;
            padding: 1rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .product-info-ml {
            width: 100%;
            max-width: 100%;
            order: 4;
            padding: 1rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-top: 0.75rem;
        }

        .product-title {
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.3;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .price-value-ml {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.25rem;
        }

        /* Remover estilos de preço no mobile - preço só aparece no mobile dentro de mobile-price-quantity-row */
        .product-price-section.d-none.d-md-block {
            display: none !important; /* Garantir que não aparece no mobile */
        }

        .price-display-ml {
            text-align: center;
        }

        .trust-badges-container-ml {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .trust-badge-item-ml {
            padding: 0.75rem;
            flex-direction: column;
            text-align: center;
            gap: 0.75rem;
        }

        .trust-icon-ml {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .trust-title-ml {
            font-size: 0.8rem;
        }

        .trust-text-ml {
            font-size: 0.7rem;
        }

        .protection-section {
            border-top: 1px solid #e6e6e6;
            padding-top: 1rem;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .protection-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: rgba(248, 249, 250, 0.5);
            border-radius: 8px;
        }

        .protection-icon {
            background: var(--secondary-color);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.7rem;
        }

        .protection-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .protection-subtitle {
            font-size: 0.6rem;
            color: #666;
            margin: 0;
        }

    }

    @media (max-width: 480px) {
        .product-layout-ml {
            gap: 0.75rem;
        }

        .purchase-card {
            padding: 1rem;
            border-radius: 12px;
        }

        .product-card-modern:hover {
            transform: translateY(-4px);
        }
    }

    .gallery-nav {
        width: 35px;
        height: 35px;
        font-size: 0.8rem;
    }

    .image-counter {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    /* Touch Optimizations */
    @media (hover: none) and (pointer: coarse) {

        .thumbnail-item-ml:hover {
            transform: none;
            border-color: transparent;
        }

        .product-card-modern:hover {
            transform: none;
        }

        .btn-add-to-cart-ml:hover {
            transform: none;
        }

        .btn-calculate-shipping-ml:hover {
            background: white;
            color: #3483fa;
        }

        .btn-favorite-ml:hover {
            background: white;
            color: #3483fa;
        }


        .thumbnail-item-ml:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }

        .product-card-modern:active {
            transform: scale(0.98);
            transition: transform 0.1s ease;
        }


        .quantity-btn-ml {
            min-height: 48px;
            min-width: 48px;
        }

        .gallery-nav {
            min-height: 44px;
            min-width: 44px;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {

        .btn-add-to-cart-ml {
            border: 2px solid #000;
        }

        .purchase-card {
            border-width: 2px;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }

        .fade-in,
        .slide-in-left,
        .slide-in-right,
        .pulse-animation {
            animation: none;
            opacity: 1;
            transform: none;
        }
    }

    /* Enhanced Related Products Section - Dentro do container */
    .related-products-section {
        margin-top: 4rem;
        padding: var(--section-padding, 3rem) 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 20px;
        position: relative;
        width: 100%;
        box-sizing: border-box;
    }

    .related-products-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        border-radius: 20px 20px 0 0;
    }

    .related-products-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .related-products-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        border-radius: 2px;
    }

    .product-card-modern {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e8eaed;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .product-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .product-card-modern:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        border-color: var(--secondary-color);
    }

    .product-card-modern:hover::before {
        transform: scaleX(1);
    }

    .product-card-image-container {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%),
                        linear-gradient(-45deg, #f8f9fa 25%, transparent 25%),
                        linear-gradient(45deg, transparent 75%, #f8f9fa 75%),
                        linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    }

    .product-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card-modern:hover .product-card-image {
        transform: scale(1.1);
    }

    .product-card-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1rem;
        line-height: 1.4;
        height: 2.8rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-card-price {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .product-card-price-current {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--secondary-color);
    }

    .product-card-price-original {
        font-size: 0.9rem;
        color: #999;
        text-decoration: line-through;
    }

    .product-card-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #e8eaed;
    }

    .product-card-btn {
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-align: center;
        text-decoration: none;
        display: block;
    }

    .product-card-btn:hover {
        background: linear-gradient(135deg, color-mix(in srgb, var(--secondary-color) 80%, black) 0%, color-mix(in srgb, var(--secondary-color) 60%, black) 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.3);
        color: white;
        text-decoration: none;
    }

    /* Micro-interactions and Animations */
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .slide-in-left {
        animation: slideInLeft 0.8s ease-out;
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .slide-in-right {
        animation: slideInRight 0.8s ease-out;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--secondary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .success-checkmark {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #28a745;
        position: relative;
        animation: successPop 0.6s ease-out;
    }

    .success-checkmark::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 6px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    @keyframes successPop {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .quantity-btn-ml:active {
        transform: scale(0.95);
        transition: transform 0.1s ease;
    }

    .btn-calculate-shipping-ml:active {
        transform: scale(0.98);
        transition: transform 0.1s ease;
    }

    .btn-favorite-ml:active {
        transform: scale(0.98);
        transition: transform 0.1s ease;
    }

    .btn-favorite-ml.favorited {
        background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);
        border-color: #e91e63;
        color: white;
    }

    .btn-favorite-ml.favorited i {
        animation: heartBeat 1.3s ease-in-out;
    }

    @keyframes heartBeat {
        0% { transform: scale(1); }
        14% { transform: scale(1.3); }
        28% { transform: scale(1); }
        42% { transform: scale(1.3); }
        70% { transform: scale(1); }
    }

    .thumbnail-item-ml.loading {
        pointer-events: none;
        opacity: 0.6;
    }

    .thumbnail-item-ml.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid var(--secondary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* Hide old layout elements */
    .product-layout,
    .product-column,
    .thumbnails-area,
    .image-area,
    .summary-area,
    .info-area {
        display: none;
    }

    /* Gallery navigation */
    .gallery-nav {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .gallery-nav:hover {
        background: white;
        transform: scale(1.1);
    }

    .image-counter {
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.8rem;
    }
</style>
@endsection

@section('scripts')
<script id="pdp-config" type="application/json">
{
    "images": {!! json_encode($product->all_images ?? []) !!},
    "product": {
        "id": {{ $product->id }},
        "slug": "{{ $product->slug }}",
        "name": {!! json_encode($product->name) !!},
        "price": {{ $product->price ?? 0 }},
        "price_fmt": {!! json_encode(number_format($product->price ?? 0, 2, ',', '.')) !!},
        "has_variations": {{ $product->has_variations ? 'true' : 'false' }},
        "variations": {!! json_encode($product->has_variations ? $product->variations->map(function($v) {
            return [
                'id' => $v->id,
                'sku' => $v->sku,
                'name' => $v->formatted_name,
                'price' => $v->price,
                'stock_quantity' => $v->stock_quantity,
                'in_stock' => $v->in_stock,
                'images' => $v->all_images,
                'attribute_value_ids' => $v->attributeValues->pluck('id')->toArray()
            ];
        }) : []) !!}
    },
    "routes": {
        "shippingQuote": "{{ route('shipping.quote') }}",
        "shippingQuoteRegional": "{{ route('shipping.quote.regional') }}",
        "shippingRegionalAreas": "{{ route('shipping.regional.areas') }}",
        "shippingRegionalPrice": "{{ route('shipping.regional.price') }}",
        "shippingSelect": "{{ route('shipping.select') }}"
    },
    "csrf": "{{ csrf_token() }}"
}
</script>

<script>
    // Scroll suave para a seção de frete
document.addEventListener('DOMContentLoaded', function() {
        const shippingLink = document.querySelector('.shipping-quick-link');
        const shippingSection = document.getElementById('shipping-calculator');
    
        if (shippingLink && shippingSection) {
            shippingLink.addEventListener('click', function(e) {
            e.preventDefault();
            
                // Scroll suave
                shippingSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Destacar a seção por um momento
                setTimeout(() => {
                    shippingSection.style.transition = 'all 0.5s ease';
                    shippingSection.style.transform = 'scale(1.02)';
                    shippingSection.style.boxShadow = '0 8px 32px rgba(255, 107, 53, 0.4)';
                    
                    setTimeout(() => {
                        shippingSection.style.transform = 'scale(1)';
                        shippingSection.style.boxShadow = '';
                    }, 500);
                }, 300);
            });
        }
    });

    // Wishlist (Favoritos) - Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteBtns = document.querySelectorAll('.btn-toggle-favorite');
        
        if (favoriteBtns.length === 0) return;

        favoriteBtns.forEach(function(favoriteBtn) {
            favoriteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = this.dataset.productId;
                const productSlug = this.dataset.productSlug;
                const isFavorite = this.dataset.isFavorite === 'true';

                const button = this;
                const originalHTML = button.innerHTML;
                const favoriteText = button.querySelector('.favorite-text');
                const favoriteIcon = button.querySelector('i');

                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i><span class="favorite-text">Processando...</span>';

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                    console.error('CSRF token não encontrado');
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                alert('Erro: Token de segurança não encontrado. Recarregue a página.');
                return;
            }
            
                fetch(`/produto/${productSlug}/toggle-favorito`, {
                method: 'POST',
                headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (response.status === 401 || response.status === 403) {
                        return response.json().then(data => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.href = '{{ route("login") }}';
                            }
                            throw new Error(data.message || 'Não autorizado');
                        });
                    }
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Erro na requisição');
                        });
                    }
                return response.json();
            })
                .then(data => {
                    console.log('Success data:', data);
                    if (data.success) {
                        // Atualizar estado do botão
                        button.dataset.isFavorite = data.added ? 'true' : 'false';
                        
                        if (data.added) {
                            button.classList.add('active');
                            button.innerHTML = '<i class="bi bi-heart-fill me-2"></i><span class="favorite-text">Remover dos Desejos</span>';
                        } else {
                            button.classList.remove('active');
                            button.innerHTML = '<i class="bi bi-heart me-2"></i><span class="favorite-text">Adicionar aos Desejos</span>';
                        }

                        // Atualizar contador no header
                        if (typeof updateWishlistCount === 'function') {
                            updateWishlistCount(data.count);
                        }

                        // Mostrar mensagem de sucesso
                        if (typeof showToast === 'function') {
                            showToast(data.message, data.added ? 'success' : 'info');
                        }
                } else {
                        const msg = data.message || 'Erro ao atualizar lista de desejos.';
                        if (typeof showToast === 'function') {
                            showToast(msg, 'error');
                        } else {
                            alert(msg);
                        }
                        button.innerHTML = originalHTML;
                    }
                    button.disabled = false;
                })
                .catch(error => {
                    console.error('Erro completo:', error);
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                    const errorMsg = error.message || 'Erro ao atualizar lista de desejos. Tente novamente.';
                    if (typeof showToast === 'function') {
                        showToast(errorMsg, 'error');
                    } else {
                        alert(errorMsg);
                    }
            });
        });
    });
    
        // Função para atualizar contador no header
        window.updateWishlistCount = function(count) {
            // Atualizar badges no mobile e desktop
            document.querySelectorAll('.quick-action-badge, .header-icon .quick-action-badge').forEach(badge => {
                const parent = badge.closest('a[href*="wishlist"]');
                if (parent) {
                    if (count > 0) {
                        if (!badge.textContent || badge.textContent.trim() === '') {
                            badge.textContent = count;
                            badge.style.display = 'flex';
                        } else {
                            badge.textContent = count;
                        }
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
        };

        // Função para mostrar toast
        function showToast(message, type = 'success') {
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                info: '#6c757d'
            };
            
            const toast = document.createElement('div');
            toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${colors[type] || colors.success}; color: white; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10000; animation: slideInRight 0.3s ease; max-width: 300px;`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
</script>

<style>
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
</style>

<script src="{{ asset('js/pdp.js') }}?v={{ time() }}"></script>

{{-- CÓDIGO REMOVIDO - Já gerenciado pelo AddToCart em pdp.js que envia variation_id corretamente --}}
{{-- O código antigo estava causando duplicação: adicionava produto base + variação --}}
@endsection
