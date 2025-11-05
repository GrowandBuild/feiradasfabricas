@extends('layouts.app')

@section('title', $product->name)

@section('content')
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
                    <div class="main-image-wrapper position-relative">
                        <img id="main-product-image" 
                             src="{{ $product->first_image }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid rounded shadow-sm main-image"
                             style="max-height: 500px; object-fit: contain; width: 100%; cursor: pointer; background-color: #f8f9fa;"
                             onerror="this.src='{{ asset('images/no-image.svg') }}'">
                        
                        <!-- Contador de imagens -->
                        @if($product->hasMultipleImages())
                            <div class="image-counter position-absolute top-0 end-0 m-2">
                                <span class="badge bg-dark bg-opacity-75">
                                    <i class="fas fa-images me-1"></i>
                                    <span id="current-image">1</span>/{{ $product->getImageCount() }}
                                </span>
                            </div>
                        @endif

                        <!-- Setas de navegação (se houver múltiplas imagens) -->
                        @if($product->hasMultipleImages())
                            <button type="button" class="btn btn-light btn-sm position-absolute top-50 start-0 translate-middle-y ms-2 gallery-nav" 
                                    id="prev-image" onclick="changeImage(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" class="btn btn-light btn-sm position-absolute top-50 end-0 translate-middle-y me-2 gallery-nav" 
                                    id="next-image" onclick="changeImage(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Miniaturas -->
                @if($product->hasMultipleImages())
                    <div class="thumbnails-container">
                        <div class="thumbnails-wrapper d-flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: thin;">
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
                @else
                    <!-- Placeholder quando não há múltiplas imagens -->
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                         style="height: 80px;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informações do Produto -->
        <div class="col-md-6">
            <div class="product-details">
                <h1 class="h2 mb-3">{{ $product->name }}</h1>
                
                @if($product->brand)
                    <p class="text-muted mb-2">
                        <strong>Marca:</strong> {{ $product->brand }}
                    </p>
                @endif

                @if($product->sku)
                    <p class="text-muted mb-2">
                        <strong>SKU:</strong> {{ $product->sku }}
                    </p>
                @endif

                <div class="price-section mb-4">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <div class="d-flex align-items-center">
                            <span class="h3 text-primary me-3">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</span>
                            <span class="text-muted text-decoration-line-through me-2">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            <span class="badge bg-danger">
                                {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                            </span>
                        </div>
                    @else
                        <span class="h3 text-primary">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                    @endif
                </div>

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
                    <x-add-to-cart 
                        :product="$product" 
                        :showQuantity="true"
                        buttonText="Adicionar ao Carrinho"
                        buttonClass="btn btn-primary btn-lg me-2" />
                    
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
        .gallery-nav {
            opacity: 1;
            width: 35px;
            height: 35px;
        }
        
        .thumbnail-img {
            width: 60px !important;
            height: 60px !important;
        }
        
        .main-image {
            max-height: 400px !important;
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
</style>
@endsection

@section('scripts')
@stack('scripts')
<script>
    // Variáveis globais para a galeria
    let currentImageIndex = 0;
    const productImages = @json($product->all_images);
    const totalImages = productImages.length;

    // Função para definir a imagem principal
    function setMainImage(imageSrc, imageNumber) {
        const mainImage = document.getElementById('main-product-image');
        const currentImageSpan = document.getElementById('current-image');
        const thumbnails = document.querySelectorAll('.thumbnail-img');
        
        // Atualizar imagem principal
        mainImage.src = imageSrc;
        
        // Atualizar contador
        if (currentImageSpan) {
            currentImageSpan.textContent = imageNumber;
        }
        
        // Atualizar índice atual
        currentImageIndex = imageNumber - 1;
        
        // Atualizar thumbnails ativas
        thumbnails.forEach((thumb, index) => {
            thumb.classList.remove('active');
            if (index === currentImageIndex) {
                thumb.classList.add('active');
            }
        });
        
        // Efeito de fade na troca de imagem
        mainImage.style.opacity = '0.7';
        setTimeout(() => {
            mainImage.style.opacity = '1';
        }, 150);
    }

    // Função para navegar entre imagens
    function changeImage(direction) {
        if (totalImages <= 1) return;
        
        currentImageIndex += direction;
        
        // Loop circular
        if (currentImageIndex >= totalImages) {
            currentImageIndex = 0;
        } else if (currentImageIndex < 0) {
            currentImageIndex = totalImages - 1;
        }
        
        // Definir nova imagem
        setMainImage(productImages[currentImageIndex], currentImageIndex + 1);
        
        // Scroll automático para a thumbnail ativa
        scrollToActiveThumbnail();
    }

    // Função para fazer scroll automático para a thumbnail ativa
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

    // Navegação por teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        }
    });

    // Zoom na imagem principal (clique duplo)
    document.getElementById('main-product-image').addEventListener('dblclick', function() {
        if (this.style.transform === 'scale(2)') {
            this.style.transform = 'scale(1)';
            this.style.cursor = 'pointer';
        } else {
            this.style.transform = 'scale(2)';
            this.style.cursor = 'zoom-out';
        }
    });

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar se há múltiplas imagens
        if (totalImages <= 1) {
            // Ocultar elementos de navegação se não há múltiplas imagens
            const navButtons = document.querySelectorAll('.gallery-nav');
            const imageCounter = document.querySelector('.image-counter');
            
            navButtons.forEach(btn => btn.style.display = 'none');
            if (imageCounter) imageCounter.style.display = 'none';
        }
    });
</script>
@endsection
