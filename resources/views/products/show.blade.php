@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    // ==== Dados Base (nova implementação limpa) ====
    $rawVariations = $product->activeVariations ?? collect();
    $variationMatrix = $rawVariations->map(fn($v) => [
        'id' => $v->id,
        'ram' => $v->ram,
        'storage' => $v->storage,
        'color' => $v->color,
        'hex' => $v->color_hex,
        'price_display' => number_format($v->price, 2, ',', '.'),
        'price_raw' => (float) $v->price,
        'in_stock' => (bool) $v->in_stock,
        'stock' => (int) $v->stock_quantity,
        'sku' => $v->sku,
    ])->values();
    $ramAxis = $variationMatrix->pluck('ram')->filter()->unique()->values();
    $storageAxis = $variationMatrix->pluck('storage')->filter()->unique()->values();
    $colorAxis = $variationMatrix->pluck('color')->filter()->unique()->values();
    // Compat vars para layout anterior (mantém blocos existentes funcionando)
    $variationData = $variationMatrix->map(function($v){
        return [
            'ram' => $v['ram'] ?? null,
            'storage' => $v['storage'] ?? null,
            'color' => $v['color'] ?? null,
            'color_hex' => $v['hex'] ?? null,
            'price' => number_format($v['price_raw'] ?? 0, 2, ',', '.'),
            'in_stock' => (bool) ($v['in_stock'] ?? false),
            'stock_quantity' => (int) ($v['stock'] ?? 0),
            'sku' => $v['sku'] ?? null,
        ];
    })->values();
    $storageOptions = $storageAxis;
    $ramOptions = $ramAxis;
    $colorOptions = $colorAxis;
    // Pré-cálculos de menor preço por eixo (evita @php internos nos loops Blade)
    $lowestByStorage = $variationData
        ->filter(fn($v)=>!is_null($v['storage']))
        ->groupBy('storage')
        ->map(function($group){
            return $group->pluck('price')
                ->map(function($price){
                    return (float) str_replace(['.', ','], ['', '.'], $price);
                })
                ->min();
        });
    $lowestByColor = $variationData
        ->filter(fn($v)=>!is_null($v['color']))
        ->groupBy('color')
        ->map(function($group){
            return $group->pluck('price')
                ->map(function($price){
                    return (float) str_replace(['.', ','], ['', '.'], $price);
                })
                ->min();
        });
    $lowestByRam = $variationData
        ->filter(fn($v)=>!is_null($v['ram']))
        ->groupBy('ram')
        ->map(function($group){
            return $group->pluck('price')
                ->map(function($price){
                    return (float) str_replace(['.', ','], ['', '.'], $price);
                })
                ->min();
        });
@endphp

<div class="pdp-container container-fluid py-3 py-md-5">
    <!-- Mobile Header: Title and Price First -->
    <div class="mobile-header-section" style="display: block !important; visibility: visible !important;">
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
                        <div class="d-flex align-items-center gap-2">
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
                    <div class="stock-section" id="variation-stock-display" style="display: none;">
                        <span class="stock-badge" id="variation-stock-badge"></span>
                    </div>
                    @unless($product->hasVariations())
                        <div class="stock-section">
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
                        </div>
                    @endunless

                    <!-- SKU Display -->
                    <div id="variation-sku-display" class="sku-section" style="display: none;">
                        <span class="sku-label">SKU:</span>
                        <span class="sku-value" id="selected-variation-sku"></span>
                    </div>
                    @if(!$product->hasVariations())
                        <div class="sku-section">
                            <span class="sku-label">SKU:</span>
                            <span class="sku-value">{{ $product->sku ?? 'Indisponível' }}</span>
                        </div>
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
                                        <input type="number" class="quantity-input-ml" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" data-product-id="{{ $product->id }}">
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
                                        <input type="number" class="quantity-input-ml" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" data-product-id="{{ $product->id }}">
                                        <button class="quantity-btn-ml" type="button" data-action="increase" data-product-id="{{ $product->id }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button class="btn-add-to-cart-ml" type="button" data-product-id="{{ $product->id }}">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Adicionar ao Carrinho
                            </button>
                        @else
                            <button class="btn-unavailable-ml" type="button" disabled>
                                <i class="bi bi-x-circle me-2"></i>
                                Indisponível no momento
                            </button>
                        @endif
                    </div>

                    <!-- Favorite Button -->
                    <div class="favorite-section">
                        <button class="btn-favorite-ml" type="button">
                            <i class="far fa-heart me-2"></i>
                            Favoritar produto
                        </button>
                    </div>

                    <!-- Variation System -->
                    @if($product->hasVariations())
                        <div class="variation-system-section">
                            <div class="variation-selector-group-ml">
                                @if($storageOptions->count() > 0)
                                    <div class="variation-selector-ml mb-3">
                                        <h6 class="variation-label-ml">Armazenamento:</h6>
                                        <div class="variation-options-ml" id="storage-options">
                                            @foreach($storageOptions as $storage)
                                                <label class="variation-option-ml" data-variation-type="storage" data-value="{{ $storage }}">
                                                    <input type="radio" name="storage" value="{{ $storage }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <span class="variation-option-content-ml">
                                                        <span class="variation-option-title-ml">{{ $storage }}</span>
                                                        @if(isset($lowestByStorage[$storage]))
                                                            <span class="variation-option-price-ml">R$ {{ number_format($lowestByStorage[$storage], 2, ',', '.') }}</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($colorOptions->count() > 0)
                                    <div class="variation-selector-ml mb-3">
                                        <h6 class="variation-label-ml">Cor:</h6>
                                        <div class="variation-options-ml" id="color-options">
                                            @foreach($colorOptions as $color)
                                                @php $colorHex = optional($variationData->firstWhere('color', $color))['color_hex']; @endphp
                                                <label class="variation-option-ml" data-variation-type="color" data-value="{{ $color }}">
                                                    <input type="radio" name="color" value="{{ $color }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <span class="variation-option-content-ml">
                                                        <span class="variation-option-title-ml d-flex align-items-center gap-2">
                                                            <span class="swatch-ml" style="background: {{ $colorHex ?? '#f1f5f9' }};"></span>
                                                            <span>{{ $color }}</span>
                                                        </span>
                                                        @if(isset($lowestByColor[$color]))
                                                            <span class="variation-option-price-ml">R$ {{ number_format($lowestByColor[$color], 2, ',', '.') }}</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($ramOptions->count() > 0)
                                    <div class="variation-selector-ml mb-3">
                                        <h6 class="variation-label-ml">RAM:</h6>
                                        <div class="variation-options-ml" id="ram-options">
                                            @foreach($ramOptions as $ram)
                                                <label class="variation-option-ml" data-variation-type="ram" data-value="{{ $ram }}">
                                                    <input type="radio" name="ram" value="{{ $ram }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <span class="variation-option-content-ml">
                                                        <span class="variation-option-title-ml">{{ $ram }}</span>
                                                        @if(isset($lowestByRam[$ram]))
                                                            <span class="variation-option-price-ml">R$ {{ number_format($lowestByRam[$ram], 2, ',', '.') }}</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div id="variation-unavailable-message" class="alert alert-warning py-2 px-3 d-flex align-items-center gap-2" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Combinação indisponível. Escolha outra opção.</span>
                            </div>
                        </div>
                    @endif
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
        </div>
    </div>

    <!-- Produtos Relacionados -->
    @if($relatedProducts->count() > 0)
        <div class="related-products-section">
            <div class="container">
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
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    /* Mobile Header Section */
    .mobile-header-section {
        padding: 1rem 1rem 0.5rem 1rem;
        background: white;
        border-bottom: 1px solid #e8eaed;
        display: block !important;
        visibility: visible !important;
    }

    .mobile-header-section .product-title {
        font-size: 1.4rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 0;
        color: #333;
        display: block !important;
    }

    /* Mobile Price + Quantity Layout */
    .mobile-price-quantity-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 12px;
        align-items: center;
    }

    .mobile-price-display {
        justify-self: start;
    }

    .mobile-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--secondary-color);
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
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 0.875rem;
        border-radius: 12px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-add-to-cart-ml:hover {
        background: var(--accent-color, #495a6d);
        transform: translateY(-2px);
    }

    /* Mercado Livre Inspired Layout with Dynamic Colors */
    .product-layout-ml {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Refactored Product Gallery */
    .product-gallery-ml {
        flex: 0 0 auto;
        width: 600px;
        display: flex;
        gap: 0.75rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 2px 10px rgba(0, 0, 0, 0.06);
        padding: 0.25rem;
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
        padding: 0.25rem;
        display: flex;
        align-items: center;
    }

    .main-image-wrapper-ml {
        position: relative;
        width: 100%;
        height: 550px;
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
    .product-trust-section-ml {
        margin-top: 1rem;
        padding: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .trust-badges-container-ml {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1rem;
        background: rgba(248, 249, 250, 0.5);
        border-radius: 12px;
    }

    .trust-badge-item-ml {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid #e8eaed;
    }

    .trust-badge-item-ml:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .trust-icon-ml {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .trust-content-ml {
        flex: 1;
    }

    .trust-title-ml {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        margin: 0;
        line-height: 1.2;
    }

    .trust-text-ml {
        font-size: 0.8rem;
        color: #666;
        margin: 0;
        line-height: 1.2;
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
    .product-details-ml {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 0;
    }

    .product-title-section {
        order: 1;
    }

    .product-title {
        font-size: 1.75rem;
        font-weight: 400;
        color: #333;
        margin: 0;
        line-height: 1.3;
    }

    .product-price-section {
        order: 2;
        padding: 1.5rem 0;
        border-bottom: 1px solid #e6e6e6;
    }

    .price-display-ml {
        display: flex;
        align-items: baseline;
        gap: 1rem;
        margin-bottom: 0.5rem;
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

    .product-variations-ml {
        order: 3;
        padding: 1.5rem 0;
        border-bottom: 1px solid #e6e6e6;
    }

    .variation-selector-group-ml {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .variation-selector-ml {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
        background: #fafbfc;
        border-radius: 12px;
        border: 1px solid #e8eaed;
        transition: all 0.3s ease;
    }

    .variation-selector-ml:hover {
        border-color: var(--secondary-color);
        box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.08);
    }

    .variation-label-ml {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .variation-options-ml {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .variation-option-ml {
        border: 2px solid #e1e5e9;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        min-width: 140px;
        position: relative;
        overflow: hidden;
        pointer-events: auto !important;
        z-index: 1 !important;
    }

    .variation-option-ml input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .variation-option-ml.selected {
        border-color: var(--secondary-color);
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1) 0%, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.05) 100%);
        box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.2);
        transform: translateY(-2px);
        position: relative;
    }

    .variation-option-ml.selected::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--secondary-color);
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        z-index: 2;
    }

    .variation-option-ml::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1) 0%, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 10px;
    }

    .variation-option-ml:hover {
        border-color: var(--secondary-color);
        box-shadow: 0 8px 25px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.2);
        transform: translateY(-2px);
    }

    .variation-option-ml:hover::before {
        opacity: 1;
    }

    .variation-option-ml.active {
        border-color: var(--secondary-color);
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color) 80%, black) 100%);
        color: white;
        box-shadow: 0 8px 25px rgba(var(--secondary-color-rgb, 52, 131, 250), 0.3);
        transform: translateY(-1px);
    }

    .variation-option-ml.active::before {
        opacity: 0;
    }

    .variation-option-ml input {
        display: none;
    }

    .variation-option-content-ml {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
        position: relative;
        z-index: 1;
    }

    .variation-option-title-ml {
        font-weight: 600;
        font-size: 0.95rem;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: color 0.3s ease;
    }

    .variation-option-ml.active .variation-option-title-ml {
        color: white;
    }

    .swatch-ml {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid rgba(0, 0, 0, 0.1);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
    }

    .swatch-ml::after {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        border: 2px solid transparent;
        transition: border-color 0.3s ease;
    }

    .variation-option-ml.active .swatch-ml::after {
        border-color: rgba(255, 255, 255, 0.6);
    }

    .variation-option-price-ml {
        font-size: 0.85rem;
        color: var(--secondary-color);
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .variation-option-ml.active .variation-option-price-ml {
        color: rgba(255, 255, 255, 0.9);
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

    /* Variation System Section */
    .variation-system-section {
        margin-top: 1rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 1px solid #e8eaed;
    }

    .variation-system-section .variation-label-ml {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .variation-system-section .variation-options-ml {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.5rem;
    }

    .variation-system-section .variation-option-ml {
        padding: 0.5rem;
        border: 1px solid #e8eaed;
        border-radius: 6px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .variation-system-section .variation-option-ml:hover {
        border-color: var(--secondary-color);
        transform: translateY(-1px);
    }

    .variation-system-section .variation-option-ml input[type="radio"]:checked + .variation-option-content-ml {
        color: var(--secondary-color);
        font-weight: 600;
    }

    .variation-system-section .variation-option-ml input[type="radio"]:checked ~ .variation-option-content-ml {
        background: rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1);
    }

    .variation-system-section .variation-option-title-ml {
        font-size: 0.85rem;
        line-height: 1.2;
    }

    .variation-system-section .variation-option-price-ml {
        font-size: 0.75rem;
        color: #666;
        margin-top: 0.25rem;
    }

    .variation-system-section .swatch-ml {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        gap: 0.5rem;
        padding: 0.5rem;
        background: white;
        border-radius: 6px;
        border: 1px solid #e8eaed;
        transition: all 0.3s ease;
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
        .product-layout-ml {
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .product-gallery-ml {
            width: 100%;
            max-width: 100%;
            flex-direction: column;
            height: auto;
            min-height: 400px;
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
            height: 350px;
            width: 100%;
        }

        .product-details-ml {
            width: 100%;
            max-width: 100%;
            order: 3;
        }

        .product-info-ml {
            width: 100%;
            max-width: 100%;
            order: 4;
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

        .product-price-section {
            background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.05) 0%, rgba(var(--secondary-color-rgb, 52, 131, 250), 0.02) 100%);
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid rgba(var(--secondary-color-rgb, 52, 131, 250), 0.1);
            margin-bottom: 1rem;
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
            padding-top: 1rem !important;
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.75rem !important;
        }

        .protection-item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            gap: 0.5rem !important;
            padding: 0.75rem !important;
            background: rgba(248, 249, 250, 0.5) !important;
            border-radius: 8px !important;
        }

        .protection-icon {
            background: var(--secondary-color) !important;
            color: white !important;
            width: 28px !important;
            height: 28px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            font-size: 0.7rem !important;
        }

        .protection-title {
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            color: #333 !important;
            margin: 0 !important;
        }

        .protection-subtitle {
            font-size: 0.6rem !important;
            color: #666 !important;
            margin: 0 !important;
        }

        .variation-options-ml {
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.25rem;
        }

        .variation-option-ml {
            padding: 0.5rem;
            font-size: 0.8rem;
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
        .variation-option-ml:hover {
            transform: none;
        }

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

        /* Touch feedback */
        .variation-option-ml:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }

        .thumbnail-item-ml:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }

        .product-card-modern:active {
            transform: scale(0.98);
            transition: transform 0.1s ease;
        }

        /* Larger touch targets */
        .variation-option-ml {
            min-height: 48px;
            min-width: 48px;
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
        .variation-option-ml {
            border-width: 2px;
        }

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

    /* Enhanced Related Products Section */
    .related-products-section {
        margin-top: 4rem;
        padding: 3rem 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 20px;
        position: relative;
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
        display: none !important;
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
    "variationColorImages": {!! json_encode($product->variation_images_urls ?? []) !!},
    "hasVariations": {{ $product->hasVariations() ? 'true' : 'false' }},
    "variationData": {!! json_encode($variationData ?? []) !!},
    "product": {
        "id": {{ $product->id }},
        "slug": "{{ $product->slug }}",
        "name": {!! json_encode($product->name) !!},
        "price": {{ $product->price ?? 0 }},
        "price_fmt": {!! json_encode(number_format($product->price ?? 0, 2, ',', '.')) !!}
    },
    "routes": {
        "productVariation": "{{ route('product.variation', $product->slug) }}",
        "shippingQuote": "{{ route('shipping.quote') }}",
        "shippingSelect": "{{ route('shipping.select') }}"
    },
    "csrf": "{{ csrf_token() }}"
}
</script>

<script src="{{ asset('js/pdp.js') }}"></script>

<!-- Scripts legados removidos - Sistema profissional implementado -->
                    
                    <button id="viewCart" style="
                        flex: 1;
                        padding: 0.75rem 1.5rem;
                        border: none;
                        background: linear-gradient(135deg, #495a6d 0%, #2c3e50 100%);
                        color: white;
                        border-radius: 8px;
                        cursor: pointer;
                        font-weight: 500;
                        transition: all 0.2s ease;
                    " onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                        Ver Carrinho
                    </button>
                </div>
                
                <button id="closeModal" style="
                    position: absolute;
                    top: 1rem;
                    right: 1rem;
                    width: 30px;
                    height: 30px;
                    border: none;
                    background: #f8f9fa;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #6c757d;
                    font-size: 18px;
                    transition: all 0.2s ease;
                " onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f8f9fa'">
                    ×
                </button>
            </div>
        `;
        
        // Adicionar ao body
        document.body.appendChild(modal);
        
        // Adicionar eventos
        document.getElementById('closeModal').addEventListener('click', () => {
            modal.remove();
        });
        
        document.getElementById('continueShopping').addEventListener('click', () => {
            modal.remove();
        });
        
        document.getElementById('viewCart').addEventListener('click', () => {
            // Redirecionar para o carrinho (URL correta em português)
            window.location.href = '/carrinho';
        });
        
        // Fechar ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        // Auto-fechar após 10 segundos
        setTimeout(() => {
            if (document.body.contains(modal)) {
                modal.remove();
            }
        }, 10000);
    }
    
    // Adicionar CSS para animações
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes successPop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
    
    // Função para REALMENTE adicionar ao carrinho
    async function addToCart(productId, quantity, variations) {
        try {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            // Encontrar o variation_id correto baseado nas seleções
            const variationId = findVariationId(variations);
            if (variationId) {
                formData.append('variation_id', variationId);
            }
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const response = await fetch('/carrinho/adicionar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Erro ao adicionar ao carrinho');
            }
            
            const result = await response.json();
            
        } catch (error) {
            alert('Erro ao adicionar produto ao carrinho. Tente novamente.');
        }
    }
    
    // Função para encontrar o ID da variação baseado nas seleções
    function findVariationId(variations) {
        // Obter dados do pdp-config
        const config = JSON.parse(document.getElementById('pdp-config').textContent);
        const allVariations = config.variations || [];
        
        // Encontrar variação que corresponde às seleções
        const matchingVariation = allVariations.find(variation => {
            return variation.color === variations.color &&
                   variation.ram === variations.ram &&
                   variation.storage === variations.storage;
        });
        
        return matchingVariation ? matchingVariation.id : null;
    }
    
    });
</script>
@endsection
