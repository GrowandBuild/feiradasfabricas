@extends('layouts.app')

@section('title', $product->name)

@section('content')
@php
    $variations = $product->activeVariations;
    $variationData = $variations->map(function ($variation) {
        return [
            'id' => $variation->id,
            'ram' => $variation->ram,
            'storage' => $variation->storage,
            'color' => $variation->color,
            'in_stock' => (bool) $variation->in_stock,
            'stock_quantity' => (int) $variation->stock_quantity,
            'price' => number_format($variation->price, 2, ',', '.'),
            'b2b_price' => $variation->b2b_price ? number_format($variation->b2b_price, 2, ',', '.') : null,
        ];
    })->values();

    $ramOptions = $variationData->pluck('ram')->filter()->unique()->values();
    $storageOptions = $variationData->pluck('storage')->filter()->unique()->values();
    $colorOptions = $variationData->pluck('color')->filter()->unique()->values();
@endphp
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products') }}">Produtos</a></li>
            @if($product->categories->count() > 0)
                <li class="breadcrumb-item">
                    <a href="{{ route('products', ['category' => $product->categories->first()->slug]) }}">
                        {{ $product->categories->first()->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Galeria de Imagens do Produto -->
        <div class="col-md-6 mb-4">
            <div class="product-gallery">
                <!-- Imagem Principal -->
                <div class="main-image-container mb-3">
                    <div class="main-image-wrapper position-relative" style="{{ $product->is_unavailable ? 'opacity: 0.6;' : '' }}">
                        @if($product->is_unavailable)
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                                 style="background: rgba(0,0,0,0.3); z-index: 10; border-radius: 8px;">
                                <span class="badge bg-warning text-dark fs-5 px-4 py-3">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Indisponível no momento
                                </span>
                            </div>
                        @endif
                        <img id="main-product-image" 
                             src="{{ $product->first_image }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid rounded shadow-sm main-image"
                             style="max-height: 500px; object-fit: contain; width: 100%; cursor: pointer; background-color: #f8f9fa;"
                             onerror="this.src='{{ asset('images/no-image.svg') }}'">
                        
                        <!-- Contador de imagens -->
                        <div class="image-counter position-absolute top-0 end-0 m-2 {{ $product->hasMultipleImages() ? '' : 'd-none' }}" id="imageCounter">
                                <span class="badge bg-dark bg-opacity-75">
                                    <i class="fas fa-images me-1"></i>
                                <span id="current-image">1</span>/<span id="total-images">{{ max($product->getImageCount(), 1) }}</span>
                                </span>
                            </div>

                        <!-- Setas de navegação (se houver múltiplas imagens) -->
                        <button type="button" class="btn btn-light btn-sm position-absolute top-50 start-0 translate-middle-y ms-2 gallery-nav {{ $product->hasMultipleImages() ? '' : 'd-none' }}" 
                                    id="prev-image" onclick="changeImage(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        <button type="button" class="btn btn-light btn-sm position-absolute top-50 end-0 translate-middle-y me-2 gallery-nav {{ $product->hasMultipleImages() ? '' : 'd-none' }}" 
                                    id="next-image" onclick="changeImage(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                    </div>
                </div>

                <!-- Miniaturas -->
                    <div class="thumbnails-container">
                    <div class="thumbnails-wrapper d-flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: thin;" id="thumbnailsWrapper">
                            @foreach($product->all_images as $index => $image)
                                <div class="thumbnail-item flex-shrink-0">
                                    <img src="{{ $image }}" 
                                         alt="{{ $product->name }} - Imagem {{ $index + 1 }}"
                                         class="thumbnail-img rounded border {{ $index === 0 ? 'active' : '' }}"
                                         style="width: 80px; height: 80px; object-fit: contain; cursor: pointer; background-color: #f8f9fa;"
                                         onclick="setMainImage('{{ $image }}', {{ $index + 1 }})"
                                         onmouseover="this.style.transform='scale(1.1)'"
                                         onmouseout="this.style.transform='scale(1)'"
                                         onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                </div>
                            @endforeach
                        </div>
                    </div>
            </div>
        </div>

        <!-- Informações do Produto -->
        <div class="col-md-6">
            <div class="product-details">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <h1 class="h2 mb-0">{{ $product->name }}</h1>
                    @if($product->is_unavailable)
                        <span class="badge bg-warning text-dark" style="font-size: 0.875rem;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Indisponível no momento
                        </span>
                    @endif
                </div>
                
                @if($product->brand)
                    <p class="text-muted mb-2">
                        <strong>Marca:</strong> {{ $product->brand }}
                    </p>
                @endif
                
                @if($product->supplier)
                    <p class="text-muted mb-2">
                        <strong>Fornecedor:</strong> {{ $product->supplier }}
                    </p>
                @endif

                @if($product->sku && !$product->hasVariations())
                    <p class="text-muted mb-2">
                        <strong>SKU:</strong> {{ $product->sku }}
                    </p>
                @endif

                @if($product->hasVariations())
                    <!-- Seletores de Variações -->
                    <div class="product-variations mb-4">
                        <div class="variation-selector-group">
                            @if($storageOptions->count() > 0)
                                <div class="variation-selector mb-3">
                                    <h6 class="variation-label">Armazenamento:</h6>
                                    <div class="variation-options" id="storage-options">
                                        @foreach($storageOptions as $storage)
                                            @php
                                                $lowestPrice = $variationData->where('storage', $storage)
                                                    ->pluck('price')
                                                    ->map(function($price) {
                                                        return (float) str_replace(['.', ','], ['', '.'], $price);
                                                    })
                                                    ->min();
                                            @endphp
                                            <label class="variation-option" data-variation-type="storage" data-value="{{ $storage }}">
                                                <input type="radio" name="storage" value="{{ $storage }}" {{ $loop->first ? 'checked' : '' }}>
                                                <span class="variation-option-content">
                                                    <span class="variation-option-title">{{ $storage }}</span>
                                                    @if(!is_null($lowestPrice))
                                                        <span class="variation-option-price">R$ {{ number_format($lowestPrice, 2, ',', '.') }}</span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($colorOptions->count() > 0)
                                <div class="variation-selector mb-3">
                                    <h6 class="variation-label">Cor:</h6>
                                    <div class="variation-options" id="color-options">
                                        @foreach($colorOptions as $color)
                                            @php
                                                $lowestPrice = $variationData->where('color', $color)
                                                    ->pluck('price')
                                                    ->map(function($price) {
                                                        return (float) str_replace(['.', ','], ['', '.'], $price);
                                                    })
                                                    ->min();
                                            @endphp
                                            <label class="variation-option" data-variation-type="color" data-value="{{ $color }}">
                                                <input type="radio" name="color" value="{{ $color }}" {{ $loop->first ? 'checked' : '' }}>
                                                <span class="variation-option-content">
                                                    <span class="variation-option-title">{{ $color }}</span>
                                                    @if(!is_null($lowestPrice))
                                                        <span class="variation-option-price">R$ {{ number_format($lowestPrice, 2, ',', '.') }}</span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($ramOptions->count() > 0)
                                <div class="variation-selector mb-3">
                                    <h6 class="variation-label">RAM:</h6>
                                    <div class="variation-options" id="ram-options">
                                        @foreach($ramOptions as $ram)
                                            @php
                                                $lowestPrice = $variationData->where('ram', $ram)
                                                    ->pluck('price')
                                                    ->map(function($price) {
                                                        return (float) str_replace(['.', ','], ['', '.'], $price);
                                                    })
                                                    ->min();
                                            @endphp
                                            <label class="variation-option" data-variation-type="ram" data-value="{{ $ram }}">
                                                <input type="radio" name="ram" value="{{ $ram }}" {{ $loop->first ? 'checked' : '' }}>
                                                <span class="variation-option-content">
                                                    <span class="variation-option-title">{{ $ram }}</span>
                                                    @if(!is_null($lowestPrice))
                                                        <span class="variation-option-price">R$ {{ number_format($lowestPrice, 2, ',', '.') }}</span>
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

                        <!-- SKU da Variação Selecionada -->
                        <div id="variation-sku-display" class="mb-2" style="display: none;">
                            <p class="text-muted mb-0">
                                <strong>SKU:</strong> <span id="selected-variation-sku"></span>
                            </p>
                        </div>

                        <!-- Status de Estoque da Variação -->
                        <div id="variation-stock-display" class="mb-3" style="display: none;">
                            <span id="variation-stock-badge" class="badge fs-6"></span>
                        </div>
                    </div>
                @endif

                <div class="price-section mb-4">
                    <span id="product-price-display" class="h3 text-primary">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                </div>

                @if(!$product->hasVariations())
                    <div class="stock-status mb-4">
                        @if($product->stock_quantity > 0)
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle me-1"></i>
                                Em estoque ({{ $product->stock_quantity }} unidades)
                            </span>
                        @else
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-times-circle me-1"></i>
                                Fora de estoque
                            </span>
                        @endif
                    </div>
                @endif

                @if($product->categories->count() > 0)
                    <div class="categories mb-4">
                        <strong>Categorias:</strong>
                        @foreach($product->categories as $category)
                            <span class="badge bg-secondary me-1">{{ $category->name }}</span>
                        @endforeach
                    </div>
                @endif

                @if($product->description)
                    <div class="description mb-4">
                        <h5>Descrição</h5>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                @endif

                <div class="action-buttons">
                    @if(!$product->is_unavailable)
                        <x-add-to-cart 
                            :product="$product" 
                            :showQuantity="true"
                            buttonText="Adicionar ao Carrinho"
                            buttonClass="btn btn-primary btn-lg me-2" />
                    @else
                        <button class="btn btn-secondary btn-lg me-2" disabled>
                            <i class="bi bi-x-circle me-2"></i>
                            Indisponível no momento
                        </button>
                    @endif
                    
                    <button class="btn btn-outline-secondary btn-lg">
                        <i class="far fa-heart me-2"></i>
                        Favoritar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos Relacionados -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Produtos Relacionados</h3>
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 product-card">
                                <div class="card-img-top-container" style="height: 200px; overflow: hidden;">
                                    @if($relatedProduct->first_image)
                                        <img src="{{ $relatedProduct->first_image }}" 
                                             alt="{{ $relatedProduct->name }}" 
                                             class="card-img-top"
                                             style="height: 100%; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ Str::limit($relatedProduct->name, 50) }}</h6>
                                    <p class="text-muted small mb-2">{{ $relatedProduct->brand }}</p>
                                    <div class="mt-auto">
                                        @if($relatedProduct->sale_price && $relatedProduct->sale_price < $relatedProduct->price)
                                            <div class="d-flex align-items-center">
                                                <span class="text-primary fw-bold">R$ {{ number_format($relatedProduct->sale_price, 2, ',', '.') }}</span>
                                                <small class="text-muted text-decoration-line-through ms-2">R$ {{ number_format($relatedProduct->price, 2, ',', '.') }}</small>
                                            </div>
                                        @else
                                            <span class="text-primary fw-bold">R$ {{ number_format($relatedProduct->price, 2, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="{{ route('product', $relatedProduct->slug) }}" 
                                       class="btn btn-outline-primary btn-sm w-100">
                                        Ver Detalhes
                                    </a>
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
    /* Estilos da Galeria */
    .product-gallery {
        position: relative;
    }
    
    .main-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
    }
    
    .main-image {
        transition: transform 0.3s ease, opacity 0.3s ease;
        width: 100%;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .main-image:hover {
        transform: scale(1.02);
    }
    
    .main-image-wrapper {
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    
    .gallery-nav {
        opacity: 0;
        transition: opacity 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .main-image-container:hover .gallery-nav {
        opacity: 1;
    }
    
    .gallery-nav:hover {
        background: rgba(255, 255, 255, 1);
        transform: scale(1.1);
    }
    
    .thumbnails-container {
        margin-top: 15px;
    }
    
    .thumbnails-wrapper {
        gap: 8px;
        padding: 5px 0;
    }
    
    .thumbnail-item {
        position: relative;
    }
    
    .thumbnail-img {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 8px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .thumbnail-img:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }
    
    .thumbnail-img.active {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .variation-select option.variation-option-disabled {
        color: #9ca3af;
    }
    
    .thumbnail-item {
        background-color: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 80px;
    }
    
    .image-counter {
        z-index: 10;
    }
    
    /* Estilos dos cards de produtos */
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .breadcrumb {
        background: none;
        padding: 0;
    }
    
    .breadcrumb-item a {
        color: #007bff;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .container.py-5 {
            padding-top: 1.5rem !important;
            padding-bottom: 1.5rem !important;
        }

        .breadcrumb {
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .breadcrumb-item {
            font-size: 0.85rem;
        }

        /* Galeria mobile */
        .col-md-6 {
            margin-bottom: 1.5rem;
        }

        .main-image-wrapper {
            min-height: 250px;
        }

        .main-image {
            max-height: 350px !important;
        }

        .gallery-nav {
            opacity: 1;
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }
        
        .thumbnail-img {
            width: 60px !important;
            height: 60px !important;
        }

        .thumbnails-wrapper {
            gap: 0.5rem;
        }

        .image-counter {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Informações do produto mobile */
        .product-details h1.h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .product-details .text-muted {
            font-size: 0.9rem;
        }

        .product-variations {
            margin-bottom: 1.5rem;
        }

        .product-variations .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .product-variations .form-select {
            padding: 0.6rem 0.75rem;
            font-size: 0.9rem;
        }

        .price-section {
            margin-bottom: 1.5rem;
        }

        .price-section .h3 {
            font-size: 1.75rem;
        }

        .stock-status {
            margin-bottom: 1.5rem;
        }

        .stock-status .badge {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .categories {
            margin-bottom: 1.5rem;
        }

        .categories .badge {
            font-size: 0.8rem;
            padding: 0.35rem 0.6rem;
        }

        .description {
            margin-bottom: 1.5rem;
        }

        .description h5 {
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }

        .description p {
            font-size: 0.9rem;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .action-buttons .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        /* Produtos relacionados mobile */
        .row.mt-5 {
            margin-top: 2rem !important;
        }

        .row.mt-5 h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .col-lg-3.col-md-4.col-sm-6 {
            margin-bottom: 1rem;
        }

        .product-card .card-img-top-container {
            height: 180px !important;
        }

        .product-card .card-title {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .breadcrumb {
            font-size: 0.75rem;
        }

        .main-image-wrapper {
            min-height: 220px;
        }

        .main-image {
            max-height: 300px !important;
        }

        .gallery-nav {
            width: 28px;
            height: 28px;
        }

        .thumbnail-img {
            width: 50px !important;
            height: 50px !important;
        }

        .product-details h1.h2 {
            font-size: 1.35rem;
        }

        .price-section .h3 {
            font-size: 1.5rem;
        }

        .action-buttons .btn {
            padding: 0.7rem 0.9rem;
            font-size: 0.9rem;
        }

        .product-card .card-img-top-container {
            height: 160px !important;
        }
    }
    
    /* Scrollbar customizada para miniaturas */
    .thumbnails-wrapper::-webkit-scrollbar {
        height: 4px;
    }
    
    .thumbnails-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }
    
    .thumbnails-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }
    
    .thumbnails-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .variation-selector-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .variation-selector {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .variation-label {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .variation-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .variation-option {
        border: 1px solid #d1d5db;
        border-radius: 0.75rem;
        padding: 0.5rem 0.85rem;
        min-width: 110px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #fff;
        position: relative;
    }

    .variation-option input {
        display: none;
    }

    .variation-option-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        align-items: flex-start;
    }

    .variation-option-title {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .variation-option-price {
        font-size: 0.8rem;
        color: #1f2937;
        font-weight: 500;
    }

    .variation-option:hover {
        border-color: #6366f1;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.1);
    }

    .variation-option.active {
        border-color: #0d6efd;
        background: rgba(13, 110, 253, 0.08);
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15);
    }

    .variation-option.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
        background: #f3f4f6;
    }

    @media (max-width: 576px) {
        .variation-option {
            min-width: calc(50% - 0.75rem);
        }
    }
</style>
@endsection

@section('scripts')
@stack('scripts')
<script>
    // Variáveis globais para a galeria
    let currentImageIndex = 0;
    const baseProductImages = @json($product->all_images);
    const variationColorImages = @json($product->variation_images_urls ?? []);
    const fallbackImage = "{{ asset('images/no-image.svg') }}";
    const activeVariationsData = @json($variationData);
    let productImages = Array.isArray(baseProductImages) && baseProductImages.length ? [...baseProductImages] : [fallbackImage];
    let totalImages = productImages.length;

    function setMainImage(imageSrc, imageNumber = 1) {
        const mainImage = document.getElementById('main-product-image');
        const currentImageSpan = document.getElementById('current-image');
        const thumbnails = document.querySelectorAll('.thumbnail-img');
        
        if (mainImage) {
        mainImage.src = imageSrc;
            mainImage.style.opacity = '0.7';
            setTimeout(() => {
                mainImage.style.opacity = '1';
            }, 150);
        }
        
        currentImageIndex = Math.max(0, Math.min(productImages.length - 1, imageNumber - 1));

        if (currentImageSpan) {
            currentImageSpan.textContent = imageNumber;
        }
        
        thumbnails.forEach((thumb, index) => {
            thumb.classList.toggle('active', index === currentImageIndex);
        });

        updateImageCounter(imageNumber, totalImages);
    }

    function changeImage(direction) {
        if (totalImages <= 1) {
            return;
        }
        
        currentImageIndex += direction;
        
        if (currentImageIndex >= totalImages) {
            currentImageIndex = 0;
        } else if (currentImageIndex < 0) {
            currentImageIndex = totalImages - 1;
        }
        
        setMainImage(productImages[currentImageIndex], currentImageIndex + 1);
        scrollToActiveThumbnail();
    }

    function scrollToActiveThumbnail() {
        const activeThumbnail = document.querySelector('.thumbnail-img.active');
        if (activeThumbnail) {
            activeThumbnail.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });
        }
    }

    function updateImageCounter(current, total) {
        const counter = document.getElementById('imageCounter');
        const totalSpan = document.getElementById('total-images');
        const currentSpan = document.getElementById('current-image');
            const navButtons = document.querySelectorAll('.gallery-nav');

        if (totalSpan) {
            totalSpan.textContent = total;
        }
        if (currentSpan) {
            currentSpan.textContent = current;
        }

        const shouldShowControls = total > 1;

        if (counter) {
            counter.classList.toggle('d-none', !shouldShowControls);
        }

        navButtons.forEach(btn => {
            btn.classList.toggle('d-none', !shouldShowControls);
        });
    }

    function renderThumbnails(images) {
        const wrapper = document.getElementById('thumbnailsWrapper');
        if (!wrapper) {
            return;
        }

        if (!Array.isArray(images) || images.length === 0) {
            wrapper.innerHTML = `
                <div class="bg-light rounded d-flex align-items-center justify-content-center w-100" style="height: 80px;">
                    <i class="fas fa-image text-muted"></i>
                </div>
            `;
            productImages = [fallbackImage];
            totalImages = 1;
            setMainImage(fallbackImage, 1);
            return;
        }

        productImages = [...images];
        totalImages = productImages.length;
            currentImageIndex = 0;

        wrapper.innerHTML = productImages.map((image, index) => {
            const safeImage = image.replace(/'/g, "\\'");
            return `
                <div class="thumbnail-item flex-shrink-0">
                    <img src="${image}" 
                         alt="{{ $product->name }} - Imagem ${index + 1 }}"
                         class="thumbnail-img rounded border ${index === 0 ? 'active' : ''}"
                         style="width: 80px; height: 80px; object-fit: contain; cursor: pointer; background-color: #f8f9fa;"
                         onclick="setMainImage('${safeImage}', ${index + 1})"
                         onmouseover="this.style.transform='scale(1.1)'"
                         onmouseout="this.style.transform='scale(1)'"
                         onerror="this.src='${fallbackImage}'">
                </div>
            `;
        }).join('');

        setMainImage(productImages[0], 1);
    }

    function applyColorImages(color) {
        const normalizedColor = color || '';
        let imagesToRender = baseProductImages;

        if (normalizedColor && Array.isArray(variationColorImages[normalizedColor]) && variationColorImages[normalizedColor].length > 0) {
            imagesToRender = variationColorImages[normalizedColor];
        }

        if (!Array.isArray(imagesToRender) || imagesToRender.length === 0) {
            imagesToRender = [fallbackImage];
        }

        renderThumbnails(imagesToRender);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        }
    });

    const mainImageElement = document.getElementById('main-product-image');
    if (mainImageElement) {
        mainImageElement.addEventListener('dblclick', function() {
        if (this.style.transform === 'scale(2)') {
            this.style.transform = 'scale(1)';
            this.style.cursor = 'pointer';
        } else {
            this.style.transform = 'scale(2)';
            this.style.cursor = 'zoom-out';
        }
    });
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderThumbnails(productImages);

        if (typeof initVariationSelectors === 'function') {
        initVariationSelectors();
        }
    });

    // Sistema de variações de produtos
    @if($product->hasVariations())
    const productSlug = '{{ $product->slug }}';
    let selectedVariationId = null;

    function setAddToCartDisabled(disabled) {
                    const addToCartBtn = document.querySelector('.add-to-cart-component [data-product-id]');
                        const addToCartComponent = document.querySelector('.add-to-cart-component');

        if (addToCartBtn) {
            if (typeof addToCartBtn.disabled !== 'undefined') {
                addToCartBtn.disabled = disabled;
            }
            addToCartBtn.classList.toggle('disabled', disabled);
            addToCartBtn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        }

        if (addToCartComponent) {
            if (disabled) {
                addToCartComponent.setAttribute('data-variation-id', '');
            }
        }
    }

    function getSelectedValue(type) {
        const input = document.querySelector(`input[name="${type}"]:checked`);
        return input ? input.value : '';
    }

    function refreshActiveVariationOptions() {
        document.querySelectorAll('.variation-option').forEach(option => {
            const input = option.querySelector('input');
            option.classList.toggle('active', input && input.checked && !input.disabled);
        });
    }

    function setOptionSelected(type, value) {
        if (!value) {
            return;
        }
        const inputs = document.querySelectorAll(`input[name="${type}"]`);
        inputs.forEach(input => {
            if (input.value === value && !input.disabled) {
                input.checked = true;
            }
        });
    }

    function isCombinationAvailable(ram, storage, color) {
        return activeVariationsData.some(variation => {
            if (variation.in_stock !== true || variation.stock_quantity <= 0) {
                return false;
            }

            const matchesRam = !ram || variation.ram === ram;
            const matchesStorage = !storage || variation.storage === storage;
            const matchesColor = !color || variation.color === color;

            return matchesRam && matchesStorage && matchesColor;
        });
    }

    function syncVariationOptionAvailability() {
        const selected = {
            ram: getSelectedValue('ram') || null,
            storage: getSelectedValue('storage') || null,
            color: getSelectedValue('color') || null,
        };

        const types = ['storage', 'color', 'ram'];

        types.forEach(type => {
            const options = Array.from(document.querySelectorAll(`label[data-variation-type="${type}"]`));
            if (!options.length) {
                return;
            }

            let firstAvailableInput = null;

            options.forEach(option => {
                const input = option.querySelector('input');
                const value = input.value;

                const variationsForValue = activeVariationsData.filter(variation => {
                    if (variation.in_stock !== true || variation.stock_quantity <= 0) {
                        return false;
                    }
                    return variation[type] === value;
                });

                const hasVariations = variationsForValue.length > 0;

                if (hasVariations && !firstAvailableInput) {
                    firstAvailableInput = input;
                }

                input.disabled = !hasVariations;
                option.classList.toggle('disabled', !hasVariations);

                if (!hasVariations && input.checked) {
                    input.checked = false;
                    selected[type] = null;
                }
            });

            if (!selected[type] && firstAvailableInput) {
                firstAvailableInput.checked = true;
                selected[type] = firstAvailableInput.value;
            }
        });

        refreshActiveVariationOptions();
    }

    function initVariationSelectors() {
        const optionInputs = document.querySelectorAll('.variation-option input');
        optionInputs.forEach(input => {
            input.addEventListener('change', () => {
                syncVariationOptionAvailability();
                applyColorImages(getSelectedValue('color'));
                updateVariation();
            });
        });

        syncVariationOptionAvailability();
        applyColorImages(getSelectedValue('color'));
        updateVariation();
    }

    function updateVariation() {
        const ram = getSelectedValue('ram');
        const storage = getSelectedValue('storage');
        const color = getSelectedValue('color');
        const unavailableMessage = document.getElementById('variation-unavailable-message');

        const combinationAvailable = isCombinationAvailable(ram, storage, color);

        applyColorImages(color);

        if (!combinationAvailable) {
            const fallback = (function() {
                const prioritized = [
                    variation => (!storage || variation.storage === storage) && (!ram || variation.ram === ram) && (!color || variation.color === color),
                    variation => (!storage || variation.storage === storage) && (!ram || variation.ram === ram),
                    variation => (!storage || variation.storage === storage),
                    variation => (!color || variation.color === color),
                    variation => (!ram || variation.ram === ram),
                    () => true,
                ];

                for (const predicate of prioritized) {
                    const match = activeVariationsData.find(variation => {
                        if (variation.in_stock !== true || variation.stock_quantity <= 0) {
                            return false;
                        }
                        return predicate(variation);
                    });

                    if (match) {
                        return match;
                    }
                }
                return null;
            })();

            if (fallback) {
                if (fallback.storage) setOptionSelected('storage', fallback.storage);
                if (fallback.color) setOptionSelected('color', fallback.color);
                if (fallback.ram) setOptionSelected('ram', fallback.ram);

                syncVariationOptionAvailability();
                updateVariation();
                return;
            }

            if (unavailableMessage) {
                unavailableMessage.style.display = 'flex';
            }
            setAddToCartDisabled(true);
            selectedVariationId = null;

            const priceDisplay = document.getElementById('product-price-display');
            if (priceDisplay) {
                priceDisplay.textContent = 'R$ {{ number_format($product->price, 2, ",", ".") }}';
            }

            const skuDisplay = document.getElementById('variation-sku-display');
            if (skuDisplay) {
                skuDisplay.style.display = 'none';
            }

            const stockDisplay = document.getElementById('variation-stock-display');
            if (stockDisplay) {
                stockDisplay.style.display = 'none';
            }

            return;
        }

        if (unavailableMessage) {
            unavailableMessage.style.display = 'none';
        }

        const url = new URL('{{ route("product.variation", $product->slug) }}', window.location.origin);
        if (ram) url.searchParams.append('ram', ram);
        if (storage) url.searchParams.append('storage', storage);
        if (color) url.searchParams.append('color', color);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.variation) {
                    selectedVariationId = data.variation.id;

                    const priceDisplay = document.getElementById('product-price-display');
                    if (priceDisplay && data.variation.price) {
                        priceDisplay.textContent = 'R$ ' + data.variation.price;
                    }

                    const skuDisplay = document.getElementById('variation-sku-display');
                    const skuSpan = document.getElementById('selected-variation-sku');
                    if (skuDisplay && skuSpan) {
                        skuSpan.textContent = data.variation.sku;
                        skuDisplay.style.display = 'block';
                    }

                    const stockDisplay = document.getElementById('variation-stock-display');
                    const stockBadge = document.getElementById('variation-stock-badge');
                    if (stockDisplay && stockBadge) {
                        if (data.variation.in_stock && data.variation.stock_quantity > 0) {
                            stockBadge.className = 'badge bg-success fs-6';
                            stockBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Em estoque (' + data.variation.stock_quantity + ' unidades)';
                            stockDisplay.style.display = 'block';
                            setAddToCartDisabled(false);
                        } else {
                            stockBadge.className = 'badge bg-danger fs-6';
                            stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> Fora de estoque';
                            stockDisplay.style.display = 'block';
                            setAddToCartDisabled(true);
                            if (unavailableMessage) {
                                unavailableMessage.style.display = 'flex';
                            }
                        }
                    }

                    const addToCartBtn = document.querySelector('.add-to-cart-component [data-product-id]');
                    if (addToCartBtn) {
                        addToCartBtn.setAttribute('data-variation-id', data.variation.id);
                        const addToCartComponent = document.querySelector('.add-to-cart-component');
                        if (addToCartComponent) {
                            addToCartComponent.setAttribute('data-variation-id', data.variation.id);
                        }
                    }
                } else {
                    selectedVariationId = null;

                    const priceDisplay = document.getElementById('product-price-display');
                    if (priceDisplay) {
                        priceDisplay.textContent = 'R$ {{ number_format($product->price, 2, ",", ".") }}';
                    }

                    const skuDisplay = document.getElementById('variation-sku-display');
                    if (skuDisplay) {
                        skuDisplay.style.display = 'none';
                    }

                    const stockDisplay = document.getElementById('variation-stock-display');
                    if (stockDisplay) {
                        stockDisplay.style.display = 'none';
                    }

                    if (unavailableMessage) {
                        unavailableMessage.style.display = 'flex';
                    }

                    setAddToCartDisabled(true);
                }
            })
            .catch(error => {
                console.error('Erro ao buscar variação:', error);
            });
    }
    @endif
</script>
@endsection


