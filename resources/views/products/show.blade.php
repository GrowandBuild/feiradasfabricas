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

<div class="pdp-container container-fluid py-5">
    <!-- Breadcrumb -->
    <div class="product-layout">
        <div class="product-column thumbnails-area">
            <div class="thumbnails-container">
                <div id="thumbnailsWrapper" class="thumbnails-wrapper d-flex flex-column">
                    @php($images = $product->all_images ?? [])
                    @forelse($images as $index => $image)
                        <div class="thumbnail-item">
                            <img src="{{ $image }}"
                                 alt="{{ $product->name }} - Imagem {{ $index + 1 }}"
                                 class="thumbnail-img rounded border {{ $index === 0 ? 'active' : '' }}"
                                 onclick="setMainImage('{{ $image }}', {{ $index + 1 }})"
                                 onmouseenter="setMainImage('{{ $image }}', {{ $index + 1 }})"
                                 onmouseover="this.style.transform='scale(1.05)'"
                                 onmouseout="this.style.transform='scale(1)'"
                                 onerror="this.src='{{ asset('images/no-image.svg') }}'">
                        </div>
                    @empty
                        <div class="thumbnail-item">
                            <img src="{{ asset('images/no-image.svg') }}"
                                 alt="{{ $product->name }} - Imagem 1"
                                 class="thumbnail-img rounded border active">
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="product-column image-area">
            <div class="main-image-container">
                    <div class="main-image-wrapper position-relative" style="{{ $product->is_unavailable ? 'opacity: 0.6;' : '' }}">
                        @if($product->is_unavailable)
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                             style="background: rgba(0,0,0,0.3); z-index: 10; border-radius: 12px;">
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
                         style="max-height: 520px; object-fit: contain; width: 100%; cursor: pointer; background-color: #f8f9fa;"
                             onerror="this.src='{{ asset('images/no-image.svg') }}'">
                        
                    <div class="image-counter position-absolute top-0 end-0 m-2 {{ $product->hasMultipleImages() ? '' : 'd-none' }}" id="imageCounter">
                                <span class="badge bg-dark bg-opacity-75">
                                    <i class="fas fa-images me-1"></i>
                            <span id="current-image">1</span>/<span id="total-images">{{ max($product->getImageCount(), 1) }}</span>
                                </span>
                            </div>

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
                </div>

        <div class="product-column summary-area">
            <div class="product-details">
                <div class="purchase-summary mb-4">
                    <div class="price-card mb-3">
                        <div class="price-section">
                            <span class="h3 price-value" id="product-price-display">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            <span class="sub-price">Preço à vista</span>
                                </div>
                        {{-- Frete removido --}}
                        <div class="stock-line" id="variation-stock-display" style="display: none;">
                            <span class="badge bg-success" id="variation-stock-badge"></span>
                        </div>
                        @unless($product->hasVariations())
                            <div class="stock-line">
                                @if($product->stock_quantity > 0)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Em estoque ({{ $product->stock_quantity }} unidades)
                                    </span>
                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>
                                        Fora de estoque
                                    </span>
                @endif
                            </div>
                        @endunless
                        <div id="variation-sku-display" class="sku-line" style="display: none;">
                            <i class="bi bi-upc-scan"></i> <span id="selected-variation-sku"></span>
            </div>
        </div>

                    <div class="info-highlights mb-4">
                        <div class="highlight-item"><i class="bi bi-shield-check"></i> Garantia de 90 dias</div>
                        <div class="highlight-item"><i class="bi bi-arrow-repeat"></i> Troca fácil em até 7 dias</div>
                    </div>
                </div>

                @if($product->hasVariations())
                    <div class="product-variations mb-4">
                        <div class="variation-selector-group">
                            @if($storageOptions->count() > 0)
                                <div class="variation-selector mb-3">
                                    <h6 class="variation-label">Armazenamento:</h6>
                                    <div class="variation-options" id="storage-options">
                                        @foreach($storageOptions as $storage)
                                            <label class="variation-option" data-variation-type="storage" data-value="{{ $storage }}">
                                                <input type="radio" name="storage" value="{{ $storage }}" {{ $loop->first ? 'checked' : '' }}>
                                                <span class="variation-option-content">
                                                    <span class="variation-option-title">{{ $storage }}</span>
                                                    @if(isset($lowestByStorage[$storage]))
                                                        <span class="variation-option-price">R$ {{ number_format($lowestByStorage[$storage], 2, ',', '.') }}</span>
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
                                            @php($colorHex = optional($variationData->firstWhere('color', $color))['color_hex'])
                                            <label class="variation-option" data-variation-type="color" data-value="{{ $color }}">
                                                <input type="radio" name="color" value="{{ $color }}" {{ $loop->first ? 'checked' : '' }}>
                                                <span class="variation-option-content">
                                                    <span class="variation-option-title d-flex align-items-center gap-2">
                                                        <span class="swatch" style="background: {{ $colorHex ?? '#f1f5f9' }};"></span>
                                                        <span>{{ $color }}</span>
                                                    </span>
                                                    @if(isset($lowestByColor[$color]))
                                                        <span class="variation-option-price">R$ {{ number_format($lowestByColor[$color], 2, ',', '.') }}</span>
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
                                            <label class="variation-option" data-variation-type="ram" data-value="{{ $ram }}">
                                                <input type="radio" name="ram" value="{{ $ram }}" {{ $loop->first ? 'checked' : '' }}>
                                                <span class="variation-option-content">
                                                    <span class="variation-option-title">{{ $ram }}</span>
                                                    @if(isset($lowestByRam[$ram]))
                                                        <span class="variation-option-price">R$ {{ number_format($lowestByRam[$ram], 2, ',', '.') }}</span>
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

                <div class="buy-actions mb-4">
                    @if(!$product->is_unavailable)
                        <x-add-to-cart 
                            :product="$product" 
                            :showQuantity="true"
                            buttonText="Adicionar ao Carrinho"
                            buttonClass="btn btn-primary btn-lg w-100" />
                        @else
                        <button class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="bi bi-x-circle me-2"></i>
                            Indisponível no momento
                        </button>
                        @endif
                    </div>

                <!-- Shipping Calculator Widget -->
                <div class="card border-0 shadow-sm mb-4 shipping-widget" id="shipping-calculator" style="display: block;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="bi bi-truck me-2"></i> Calcule o frete</h6>
                            <small class="text-muted">via Melhor Envio</small>
                        </div>
                        @php($sandbox = setting('melhor_envio_sandbox', true))
                        @if($sandbox)
                            <div class="alert alert-warning py-1 mb-2"><small><i class="bi bi-exclamation-triangle me-1"></i>Ambiente de teste (sandbox) — valores podem estar acima do real.</small></div>
                        @endif
                        <div class="row g-2 align-items-end">
                            <div class="col-8">
                                <label for="cep-destino" class="form-label">CEP de destino</label>
                                <input type="text" class="form-control" id="cep-destino" placeholder="00000-000" inputmode="numeric" maxlength="9">
                                <div class="form-text">Apenas números (ex.: 74673-030)</div>
                            </div>
                            <div class="col-4">
                                <label for="qty-shipping" class="form-label">Qtd</label>
                                <input type="number" class="form-control" id="qty-shipping" min="1" value="1">
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button class="btn btn-outline-primary" id="btn-calc-frete">
                                <span class="label-default"><i class="bi bi-calculator me-2"></i>Calcular frete</span>
                                <span class="label-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Calculando...</span>
                            </button>
                        </div>
                        <button class="btn btn-sm btn-link text-decoration-none mt-2" id="toggle-frete-debug" type="button">Detalhes técnicos</button>
                        <div class="small text-muted" id="frete-debug-panel" style="display:none;"></div>
                        <div class="mt-2 d-flex justify-content-between align-items-center gap-2" id="frete-actions" style="display:none;">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Ordenar fretes">
                                <button type="button" class="btn btn-outline-secondary active" id="sort-price" aria-pressed="true">Mais barato</button>
                                <button type="button" class="btn btn-outline-secondary" id="sort-speed" aria-pressed="false">Mais rápido</button>
                            </div>
                            <small class="text-muted" id="economy-hint"></small>
                        </div>
                        <div class="mt-3" id="frete-resultado" style="display:none;"></div>
                        <div class="mt-3" id="frete-selecionado" style="display:none;"></div>
                    </div>
                </div>

                <button class="btn btn-outline-secondary w-100" style="border-color:#ff9900; color:#ff9900;">
                    <i class="far fa-heart me-2"></i>
                    Favoritar produto
                </button>
            </div>
        </div>

        <div class="product-column info-area">
            <div class="summary-grid">
                <div class="summary-card">
                    <span class="title"><i class="bi bi-tag"></i> SKU</span>
                    <span class="subtitle">{{ $product->sku ?? 'Indisponível' }}</span>
                </div>
                
                <div class="summary-card">
                    <span class="title"><i class="bi bi-shield-check"></i> Proteção extra</span>
                    <span class="subtitle">Garantia e suporte em 90 dias</span>
                </div>
                <div class="summary-card">
                    <span class="title"><i class="bi bi-arrow-repeat"></i> Política de troca</span>
                    <span class="subtitle">Devolução facilitada em até 7 dias</span>
                </div>
            </div>

                @if($product->categories->count() > 0)
                <div class="product-meta-list mb-3">
                    <strong class="d-block mb-2">Categorias</strong>
                        @foreach($product->categories as $category)
                            <span class="badge bg-secondary me-1">{{ $category->name }}</span>
                        @endforeach
                    </div>
                @endif

                @if($product->description)
                <div class="product-meta-list mb-3">
                    <h5 class="mb-2">Descrição</h5>
                    <p class="text-muted mb-0">{{ $product->description }}</p>
                    </div>
                @endif

            <div class="info-grid">
                <div class="info-card">
                    <span class="title"><i class="bi bi-box-seam"></i> O que vem na caixa</span>
                    <span class="subtitle">Produto lacrado + cabo USB + guia rápido.</span>
                </div>
                <div class="info-card">
                    <span class="title"><i class="bi bi-credit-card"></i> Pagamento</span>
                    <span class="subtitle">Parcele em até 12x sem juros. Desconto à vista no Pix.</span>
            </div>
                <div class="info-card">
                    <span class="title"><i class="bi bi-shield-lock"></i> Segurança</span>
                    <span class="subtitle">Checkout protegido e criptografado.</span>
                </div>
                <div class="info-card">
                    <span class="title"><i class="bi bi-chat-dots"></i> Suporte</span>
                    <span class="subtitle">Atendimento humano via WhatsApp e e-mail.</span>
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
                                       class="btn btn-outline-primary w-100 btn-sm">Ver detalhes</a>
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
    .product-layout {
        display: grid;
        grid-template-areas: "thumbs image summary info";
        grid-template-columns: 90px minmax(0, 1fr) minmax(0, 0.85fr) minmax(0, 0.7fr);
        gap: 1.5rem;
        align-items: start;
    }

    .thumbnails-area { grid-area: thumbs; }
    .image-area { grid-area: image; }
    .summary-area { grid-area: summary; }
    .info-area { grid-area: info; }

    @media (max-width: 1400px) {
        .product-layout {
            grid-template-columns: 80px minmax(0, 1fr) minmax(0, 0.9fr) minmax(0, 0.75fr);
            gap: 1.25rem;
        }
    }

    @media (max-width: 1200px) {
        .product-layout {
            grid-template-columns: 70px minmax(0, 1.05fr) minmax(0, 0.95fr);
            grid-template-areas:
                "thumbs image summary"
                "thumbs info info";
        }
    }

    @media (max-width: 992px) {
        .product-layout {
            grid-template-columns: 1fr;
            grid-template-areas:
                "image"
                "thumbs"
                "summary"
                "info";
            gap: 1.25rem;
        }

        .product-column {
            gap: 1rem;
        }

        .image-area,
        .thumbnails-area {
            position: static;
        }

        .main-image-container {
            padding: 1rem;
        }

        .thumbnails-area {
            flex-direction: row;
            align-items: center;
            height: auto;
            max-height: none;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 0.5rem 0;
            gap: 0.75rem;
            scroll-snap-type: x proximity;
            -webkit-overflow-scrolling: touch;
        }

        .thumbnails-area::-webkit-scrollbar {
            height: 6px;
            width: auto;
        }

        .thumbnails-area::-webkit-scrollbar-thumb {
            background: #cbd5f5;
        }

        .thumbnail-item {
            flex: 0 0 auto;
            width: 72px;
            min-height: auto;
            scroll-snap-align: start;
        }

        .thumbnail-img {
            height: 72px;
        }

        .summary-area,
        .info-area {
            position: static;
        }

        .summary-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    .product-column {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .thumbnails-area {
        padding-right: 4px;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    @media (max-width: 992px) {
        .thumbnails-area {
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            padding-right: 0;
            max-height: none;
            height: auto;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .thumbnail-item {
            flex: 0 0 auto;
            width: 68px;
            min-height: auto;
        }
    }

    .summary-area {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        position: relative;
    }

    .info-area {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        position: relative;
    }

    .image-area {
        position: relative;
    }

    .info-highlights {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 1rem;
    }

    .highlight-item {
        background: #ffffff;
        border-radius: 14px;
        padding: 0.9rem 1rem;
        box-shadow: 0 10px 28px rgba(148, 163, 184, 0.12);
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 600;
        color: #1f2937;
    }

    .highlight-item i {
        color: #ff9900;
        font-size: 1.1rem;
    }

    @media (min-width: 992px) {
        .thumbnails-area {
            position: sticky;
            top: 110px;
            height: 520px;
            overflow-y: auto;
        }

        .image-area {
            position: sticky;
            top: 100px;
        }
    }

    .thumbnails-area::-webkit-scrollbar {
        width: 6px;
    }

    .thumbnails-area::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 999px;
    }

    .thumbnails-area::-webkit-scrollbar-thumb {
        background: #cbd5f5;
        border-radius: 999px;
    }
    
    .main-image-container {
        border-radius: 12px;
        background: #ffffff;
        padding: 1.25rem;
        box-shadow: 0 20px 45px rgba(148, 163, 184, 0.18);
        flex: 1;
        width: 100%;
    }
    
    .main-image-wrapper {
        border-radius: 12px;
        background: #fff;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
        width: 100%;
        max-width: 100%;
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
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        width: 78px;
    }

    .thumbnail-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.15);
    }

    .thumbnail-img {
        width: 100%;
        height: 70px;
        object-fit: contain;
        padding: 0.4rem;
        background: #f8fafc;
        border-radius: 12px;
        transition: transform 0.2s ease, border 0.2s ease, box-shadow 0.2s ease;
    }
    
    .thumbnail-img.active {
        border: 2px solid #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }

    .price-value {
        color: #ff9900;
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

        .product-layout {
            gap: 1rem;
        }

        .breadcrumb {
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .breadcrumb-item {
            font-size: 0.85rem;
        }

        .col-md-6 {
            margin-bottom: 1.5rem;
        }

        .main-image-container {
            padding: 1rem;
            box-shadow: 0 12px 30px rgba(148, 163, 184, 0.12);
        }

        .main-image-wrapper {
            min-height: 230px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
        }

        .main-image {
            max-height: 320px !important;
        }

        .gallery-nav {
            opacity: 1;
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .thumbnails-area {
            justify-content: flex-start;
            gap: 0.65rem;
        }
        
        .thumbnail-img {
            width: 60px !important;
            height: 60px !important;
            padding: 0.35rem;
        }

        .thumbnails-wrapper {
            gap: 0.5rem;
        }

        .image-counter {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

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

        .variation-options {
            gap: 0.5rem;
        }

        .variation-option {
            flex: 1 1 calc(50% - 0.5rem);
            min-width: calc(50% - 0.5rem);
        }

        .price-section {
            margin-bottom: 1.5rem;
        }

        .price-section .h3 {
            font-size: 1.75rem;
            justify-content: center;
        }

        .stock-status {
            margin-bottom: 1.5rem;
        }

        .stock-status .badge {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .price-card {
            padding: 1.25rem;
            box-shadow: 0 12px 28px rgba(148, 163, 184, 0.12);
        }

        .purchase-summary {
            text-align: center;
        }

        .stock-line {
            justify-content: center;
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

        .info-highlights {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .summary-grid,
        .info-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .summary-card,
        .info-card {
            text-align: center;
        }

        .product-meta-list {
            padding: 1.1rem;
        }

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

        .main-image-container {
            padding: 0.85rem;
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
            padding: 0.3rem;
        }

        .product-details h1.h2 {
            font-size: 1.35rem;
        }

        .price-section .h3 {
            font-size: 1.75rem;
        }

        .action-buttons .btn {
            padding: 0.7rem 0.9rem;
            font-size: 0.9rem;
        }

        .variation-option {
            flex: 1 1 100%;
            min-width: 100%;
        }

        .product-card .card-img-top-container {
            height: 160px !important;
        }

        .summary-card,
        .info-card {
            padding: 1rem 1.05rem;
        }

        .highlight-item {
            padding: 0.85rem 0.95rem;
        }
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
        padding: 0.55rem 0.95rem;
        min-width: 120px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #fff;
        position: relative;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
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

    .variation-option-content .swatch {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 1px solid rgba(148, 163, 184, 0.6);
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
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
        background: rgba(37, 99, 235, 0.08);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.16);
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

    .product-meta-list {
        background: #f8fafc;
        border-radius: 14px;
        padding: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
    }

    .secondary-info .info-section {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.15);
        margin-bottom: 0.75rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .secondary-info .info-section h6 {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #1f2937;
    }

    .action-buttons .btn {
        padding: 0.9rem 1.2rem;
        font-weight: 600;
        border-radius: 12px;
    }

    .summary-grid, .info-grid {
        display: grid;
        gap: 0.85rem;
    }

    .summary-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .info-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .summary-card, .info-card {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        padding: 1.1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .summary-card i, .info-card i {
        color: #ff9900;
    }

    .summary-card span.title, .info-card span.title {
        font-weight: 600;
        color: #1f2937;
    }

    .summary-card span.subtitle, .info-card span.subtitle {
        color: #64748b;
        font-size: 0.88rem;
    }

    /* Shipping widget enhancements */
    .shipping-widget {background: #ffffff; border: 1px solid rgba(148,163,184,0.25); border-radius: 14px;}
    .shipping-widget .list-group-item {cursor: pointer; transition: background .15s, box-shadow .15s, border-color .15s;}
    .shipping-widget .list-group-item:hover {background: #f8fafc; box-shadow: 0 4px 12px rgba(0,0,0,0.06);}
    .shipping-widget .list-group-item[aria-checked="true"], .shipping-widget .list-group-item:has(input[type=radio]:checked) {background: #eef6ff; border-left: 4px solid #0d6efd;}
    .shipping-widget .price-display {font-size: 0.95rem;}
    .shipping-widget .option-cheapest .price-display {color: #0d6efd;}
    .shipping-widget .option-fastest .service-name::after {content: '⚡'; margin-left: 4px; font-size: .9rem;}
    #frete-actions .btn-group .btn.active {background:#0d6efd; color:#fff;}
    #frete-selecionado .alert {border-radius: 10px;}
    .shipping-widget .badge {font-size: .65rem; font-weight:600; letter-spacing:.5px;}
    @media (max-width: 576px){
        .shipping-widget .list-group-item {padding: .75rem .85rem;}
        #frete-actions {flex-direction: column; align-items: flex-start;}
        #frete-actions small{margin-top:.25rem;}
    }

    .product-variations {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 16px;
        padding: 1.5rem;
        background: rgba(248, 250, 252, 0.9);
    }

    .price-section .h3 {
        font-size: 2.4rem;
        font-weight: 700;
        color: #ff9900;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .price-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        margin-bottom: 1.5rem;
        box-shadow: 0 18px 45px rgba(148, 163, 184, 0.16);
    }

    .stock-line {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        color: #0f766e;
        font-size: 0.95rem;
    }

    .stock-line span.badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        border-radius: 999px;
    }
</style>
@endsection

@section('scripts')
@stack('scripts')
<script>
    // ==== PDP Boot Diagnostics ====
    try { console.log('[PDP] script boot start'); } catch(e){}
    window.__PDP_BOOT = {started:true, galleryInit:false, variationInit:false, errors:[]};
    // Variáveis globais para a galeria
    let currentImageIndex = 0;
    const baseProductImages = @json($product->all_images);
    const variationColorImages = @json($product->variation_images_urls ?? []);
    const fallbackImage = "{{ asset('images/no-image.svg') }}";
    const activeVariationsData = @json($variationData);
    const colorHexMap = activeVariationsData.reduce((acc, variation) => {
        if (variation.color && variation.color_hex) {
            acc[variation.color] = variation.color_hex;
            const key = variation.color.replace(/[^a-zA-Z0-9]/g, '_');
            acc[key] = variation.color_hex;
        }
        return acc;
    }, {});
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
    // Expõe globalmente para evitar ReferenceError em handlers injetados
    window.setMainImage = setMainImage;

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

        const safeProductName = @json($product->name);
        let html = '';
        for (let i=0;i<productImages.length;i++) {
            const img = productImages[i];
            const altTxt = safeProductName + ' - Imagem ' + (i+1);
            html += '<div class="thumbnail-item">'
                + '<img src="' + img + '" alt="' + altTxt.replace(/"/g,'&quot;') + '"'
                + ' class="thumbnail-img rounded border ' + (i===0? 'active':'') + '"'
                + ' data-index="' + i + '"'
                + '>'
                + '</div>';
        }
        wrapper.innerHTML = html;

        setMainImage(productImages[0], 1);
        // Delegação de eventos para robustez
        const delegate = function(e){
            const target = e.target.closest('.thumbnail-img');
            if(!target) return;
            const idx = parseInt(target.getAttribute('data-index'),10) || 0;
            if(window.setMainImage){ window.setMainImage(productImages[idx], idx+1); }
        };
        wrapper.removeEventListener('click', delegate);
        wrapper.removeEventListener('mouseenter', delegate, true);
        wrapper.addEventListener('click', delegate);
        wrapper.addEventListener('mouseenter', delegate, true);
        window.__PDP_BOOT.galleryInit = true;
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
        try { renderThumbnails(productImages); } catch(err){ window.__PDP_BOOT.errors.push('gallery:'+err.message); console.error('[PDP] gallery init error', err); }

        // Pré-seleção por querystring (?color=...&storage=...&ram=...)
        const params = new URLSearchParams(window.location.search);
        const qsColor = params.get('color');
        const qsStorage = params.get('storage');
        const qsRam = params.get('ram');

        if (typeof initVariationSelectors === 'function') {
            try { initVariationSelectors(); window.__PDP_BOOT.variationInit = true; } catch(err){ window.__PDP_BOOT.errors.push('variation:'+err.message); console.error('[PDP] variation init error', err); }
            // Aplicar valores da query após inicializar os seletores
            if (qsStorage) setOptionSelected('storage', qsStorage);
            if (qsColor) setOptionSelected('color', qsColor);
            if (qsRam) setOptionSelected('ram', qsRam);
            // Sincronizar disponibilidade e atualizar variação/imagens
            if (typeof syncVariationOptionAvailability === 'function') {
                syncVariationOptionAvailability();
            }
            if (typeof applyColorImages === 'function') {
                applyColorImages(getSelectedValue('color'));
            }
            if (typeof updateVariation === 'function') {
                updateVariation();
            }
        }

        // Sincronizar quantidade do widget de frete com o input principal (se existir)
        const qtyInput = document.getElementById('quantity-{{ $product->id }}');
        const qtyShipping = document.getElementById('qty-shipping');
        if (qtyInput && qtyShipping) {
            qtyShipping.value = qtyInput.value || '1';
            qtyInput.addEventListener('change', () => {
                qtyShipping.value = qtyInput.value || '1';
            });
        }
        // Máscara simples para CEP
        const cepInput = document.getElementById('cep-destino');
        if (cepInput) {
            cepInput.addEventListener('input', (e) => {
                let v = (e.target.value || '').replace(/\D/g, '').slice(0,8);
                if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
                e.target.value = v;
            });
        }
        // Acionador do cálculo
        const btnCalc = document.getElementById('btn-calc-frete');
        const resultBox = document.getElementById('frete-resultado');
        if (btnCalc && resultBox) {
            btnCalc.addEventListener('click', async () => {
                const cep = (cepInput?.value || '').replace(/\D/g, '');
                const qty = parseInt(qtyShipping?.value || '1', 10) || 1;
                if (cep.length !== 8) {
                    showFreteMessage('Informe um CEP válido com 8 dígitos.', 'warning');
                    return;
                }
                await calcularFrete(cep, qty);
            });
        }
    });

    async function calcularFrete(cep, qty) {
        const btn = document.getElementById('btn-calc-frete');
        const resultBox = document.getElementById('frete-resultado');
        if (!btn || !resultBox) return;

        // UI loading
        btn.disabled = true;
        btn.querySelector('.label-default')?.classList.add('d-none');
        btn.querySelector('.label-loading')?.classList.remove('d-none');
        resultBox.style.display = 'none';
        resultBox.innerHTML = '';

        try {
            const resp = await fetch('{{ route("shipping.quote") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    cep: cep,
                    quantity: qty
                })
            });
            const data = await resp.json();
            if (!resp.ok || !data.success) {
                const msg = data.message || `Erro ao calcular frete (HTTP ${resp.status})`;
                showFreteMessage(msg, 'danger');
                return;
            }
            renderQuotes(data.quotes || []);
            if (data.debug) renderDebug(data.debug);
        } catch (err) {
            showFreteMessage('Falha na conexão. Tente novamente.', 'danger');
        } finally {
            btn.disabled = false;
            btn.querySelector('.label-default')?.classList.remove('d-none');
            btn.querySelector('.label-loading')?.classList.add('d-none');
        }
    }

    function renderQuotes(quotes) {
        const resultBox = document.getElementById('frete-resultado');
        if (!resultBox) return;
        const withPrice = Array.isArray(quotes) ? quotes.filter(q => typeof q.price === 'number') : [];
        if (withPrice.length === 0) {
            showFreteMessage('Nenhuma opção de frete disponível para o CEP informado.', 'warning');
            return;
        }
        // Determinar cheapest & fastest entre as que possuem preço
        const cheapest = withPrice.reduce((acc, q) => acc && acc.price <= q.price ? acc : q, withPrice[0]);
        const withDays = withPrice.filter(q => typeof q.delivery_days === 'number');
        const fastest = withDays.reduce((acc, q) => acc && acc.delivery_days <= q.delivery_days ? acc : q, withDays[0]);
        const maxPrice = withPrice.reduce((acc, q) => q.price > acc ? q.price : acc, 0);
        const economyHint = maxPrice && cheapest ? `Economize até R$ ${(maxPrice - cheapest.price).toFixed(2).replace('.',',')}` : '';
        const econEl = document.getElementById('economy-hint');
        if (econEl) econEl.textContent = economyHint;
        document.getElementById('frete-actions')?.style.display = 'flex';

            const itens = withPrice.map((q, idx) => {
            const preco = typeof q.price === 'number' ? q.price : null;
            const precoFmt = preco !== null ? `R$ ${preco.toFixed(2).replace('.', ',')}` : '—';
            const prazo = (q.delivery_days != null) ? `${q.delivery_days} dia(s) úteis` : '';
            let service = q.service || 'Serviço';
            if (service.startsWith('.')) service = 'Jadlog ' + service;
            const company = q.company ? ` • ${q.company}` : '';
            const isCheapest = cheapest && q === cheapest;
            const isFastest = fastest && q === fastest;
            const arrivalDate = (q.delivery_days != null) ? formatArrival(q.delivery_days) : '';
            const badges = `
                ${isCheapest ? '<span class="badge bg-success me-1">Mais barato</span>' : ''}
                ${isFastest ? '<span class="badge bg-info text-dark me-1">Mais rápido</span>' : ''}
            `;
            return `
                <div class="shipping-option list-group-item ${isCheapest ? 'option-cheapest' : ''} ${isFastest ? 'option-fastest' : ''}" role="radio" aria-checked="${idx===0?'true':'false'}" tabindex="0" data-index="${idx}">
                    <div class="d-flex justify-content-between align-items-start w-100">
                        <div class="flex-grow-1 me-2">
                            <div class="d-flex align-items-center mb-1">
                                <input type="radio" name="shipping_service" class="form-check-input me-2" ${idx===0?'checked':''} value="${service}" data-price="${preco ?? ''}" data-days="${q.delivery_days ?? ''}" data-service-id="${q.service_id ?? ''}" data-company="${q.company ?? ''}">
                                <span class="fw-semibold service-name">${service}</span>
                            </div>
                            <div class="small text-muted">
                                ${prazo} ${arrivalDate ? ' • Chegada estimada ' + arrivalDate : ''}
                            </div>
                            <div class="mt-1">${badges}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold price-display">${precoFmt}</div>
                        </div>
                    </div>
                </div>`;
        }).join('');

        resultBox.innerHTML = `<div class="list-group list-group-flush border rounded">${itens}</div>`;
        resultBox.style.display = 'block';
        attachShippingOptionEvents();
        updateSelectedSummary();
    }

    function renderDebug(d) {
        const panel = document.getElementById('frete-debug-panel');
        if (!panel) return;
        panel.innerHTML = `
            <div class="border rounded p-2 bg-light">
                <div><strong>Modo declarado:</strong> ${d.declared_mode}</div>
                <div><strong>Valor declarado:</strong> R$ ${Number(d.declared_value).toFixed(2).replace('.',',')} ${d.declared_mode==='cap' ? `(teto R$ ${Number(d.declared_cap).toFixed(2).replace('.',',')})` : ''}</div>
                <div><strong>Peso real total:</strong> ${d.weight_real_kg_total} kg</div>
                <div><strong>Peso volumétrico:</strong> ${d.weight_volumetric_kg_total} kg</div>
                <div><strong>Peso usado:</strong> ${d.weight_used_kg} kg</div>
                <div><strong>Dimensões (cm):</strong> ${d.dimensions_cm.length} × ${d.dimensions_cm.width} × ${d.dimensions_cm.height}</div>
                <div><strong>Camadas empilhadas:</strong> ${d.stack_layers}</div>
                <div><strong>Quantidade:</strong> ${d.quantity}</div>
                <div><strong>Ambiente:</strong> ${d.environment}</div>
            </div>`;
    }

    document.getElementById('toggle-frete-debug')?.addEventListener('click', () => {
        const panel = document.getElementById('frete-debug-panel');
        if (!panel) return;
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    });

    function showFreteMessage(message, type) {
        const resultBox = document.getElementById('frete-resultado');
        if (!resultBox) return;
        resultBox.innerHTML = `<div class="alert alert-${type}"><i class="bi bi-info-circle me-2"></i>${message}</div>`;
        resultBox.style.display = 'block';
    }

    function formatArrival(days) {
        if (!Number.isFinite(days)) return '';
        const d = new Date();
        d.setDate(d.getDate() + days);
        return d.toLocaleDateString('pt-BR', { day:'2-digit', month:'2-digit' });
    }

    function attachShippingOptionEvents() {
        document.querySelectorAll('.shipping-option').forEach(opt => {
            opt.addEventListener('click', () => selectShippingOption(opt));
            opt.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault(); selectShippingOption(opt);
                }
            });
        });
        document.getElementById('sort-price')?.addEventListener('click', () => sortShipping('price'));
        document.getElementById('sort-speed')?.addEventListener('click', () => sortShipping('speed'));
    }

    async function selectShippingOption(opt) {
        const radio = opt.querySelector('input[type="radio"]');
        if (!radio) return;
        radio.checked = true;
        document.querySelectorAll('.shipping-option').forEach(o => o.setAttribute('aria-checked','false'));
        opt.setAttribute('aria-checked','true');
        updateSelectedSummary();
        // Persistir seleção no backend
        const cep = (document.getElementById('cep-destino')?.value || '').replace(/\D/g, '');
        const qty = parseInt(document.getElementById('qty-shipping')?.value || '1', 10) || 1;
        try {
            await fetch('{{ route("shipping.select") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    service: radio.value,
                    price: parseFloat(radio.getAttribute('data-price') || '0') || 0,
                    delivery_days: parseInt(radio.getAttribute('data-days') || '0', 10) || null,
                    service_id: parseInt(radio.getAttribute('data-service-id') || '0', 10) || null,
                    company: radio.getAttribute('data-company') || null,
                    cep: cep,
                    product_id: {{ $product->id }},
                    quantity: qty
                })
            });
        } catch (e) {
            console.warn('Falha ao salvar seleção de frete.', e);
        }
    }

    async function updateSelectedSummary() {
        const selectedRadio = document.querySelector('input[name="shipping_service"]:checked');
        const summaryBox = document.getElementById('frete-selecionado');
        if (!selectedRadio || !summaryBox) return;
        const service = selectedRadio.value;
        const price = selectedRadio.getAttribute('data-price');
        const days = selectedRadio.getAttribute('data-days');
        if (!service) { summaryBox.style.display='none'; return; }
        summaryBox.innerHTML = `
            <div class="alert alert-primary d-flex align-items-center justify-content-between py-2 px-3">
                <div>
                    <strong>${service}</strong> — ${price?('R$ '+Number(price).toFixed(2).replace('.',',')):'Preço indisponível'}
                    ${days?`<small class="ms-2 text-muted">${days} dia(s)</small>`:''}
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-alterar-frete">Alterar</button>
            </div>`;
        summaryBox.style.display = 'block';
        document.getElementById('btn-alterar-frete')?.addEventListener('click', () => {
            document.getElementById('frete-resultado')?.scrollIntoView({behavior:'smooth'});
        });

        // Persistir seleção atual (inclui caso seja a primeira opção após cálculo)
        const cep = (document.getElementById('cep-destino')?.value || '').replace(/\D/g, '');
        const qty = parseInt(document.getElementById('qty-shipping')?.value || '1', 10) || 1;
        try {
            await fetch('{{ route("shipping.select") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    service: service,
                    price: parseFloat(price || '0') || 0,
                    delivery_days: parseInt(days || '0', 10) || null,
                    service_id: parseInt(selectedRadio.getAttribute('data-service-id') || '0', 10) || null,
                    company: selectedRadio.getAttribute('data-company') || null,
                    cep: cep,
                    product_id: {{ $product->id }},
                    quantity: qty
                })
            });
        } catch (e) {
            console.warn('Falha ao salvar seleção de frete.', e);
        }
    }

    function sortShipping(mode) {
        const resultBox = document.getElementById('frete-resultado');
        if (!resultBox) return;
        const options = Array.from(resultBox.querySelectorAll('.shipping-option'));
        options.sort((a,b) => {
            const ra = a.querySelector('input');
            const rb = b.querySelector('input');
            if (!ra || !rb) return 0;
            if (mode==='price') {
                return (parseFloat(ra.getAttribute('data-price')||'99999') - parseFloat(rb.getAttribute('data-price')||'99999'));
            } else {
                return (parseInt(ra.getAttribute('data-days')||'999') - parseInt(rb.getAttribute('data-days')||'999'));
            }
        });
        const container = resultBox.querySelector('.list-group');
        if (container) {
            options.forEach(o => container.appendChild(o));
        }
        // Toggle active states
        const btnPrice = document.getElementById('sort-price');
        const btnSpeed = document.getElementById('sort-speed');
        if (mode==='price') {
            btnPrice.classList.add('active'); btnSpeed.classList.remove('active');
            btnPrice.setAttribute('aria-pressed','true'); btnSpeed.setAttribute('aria-pressed','false');
        } else {
            btnSpeed.classList.add('active'); btnPrice.classList.remove('active');
            btnSpeed.setAttribute('aria-pressed','true'); btnPrice.setAttribute('aria-pressed','false');
        }
    }

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
            option.classList.toggle('active', !!(input && input.checked && !input.disabled));
        });
    }

    function setOptionSelected(type, value) {
        if (!value) {
            return;
        }
        document.querySelectorAll(`input[name="${type}"]`).forEach(input => {
            if (input.value === value && !input.disabled) {
                input.checked = true;
            }
        });
    }

    function isCombinationAvailable(ram, storage, color) {
        return activeVariationsData.some(variation => {
            if (!variation.in_stock || variation.stock_quantity <= 0) {
                return false;
            }

            const matchesRam = !ram || variation.ram === ram;
            const matchesStorage = !storage || variation.storage === storage;
            const matchesColor = !color || variation.color === color;

            return matchesRam && matchesStorage && matchesColor;
        });
    }

    function applyColorSwatches() {
        document.querySelectorAll('.variation-option[data-variation-type="color"]').forEach(option => {
            const value = option.getAttribute('data-value');
            const key = value ? value.replace(/[^a-zA-Z0-9]/g, '_') : '';
            const swatch = option.querySelector('.swatch');
            if (!swatch) {
                return;
            }
            const hex = colorHexMap[value] || colorHexMap[key] || '#f1f5f9';
            swatch.style.background = hex;
        });
    }

    function syncVariationOptionAvailability() {
        const selected = {
            ram: getSelectedValue('ram') || null,
            storage: getSelectedValue('storage') || null,
            color: getSelectedValue('color') || null,
        };

        ['storage', 'color', 'ram'].forEach(type => {
            const options = Array.from(document.querySelectorAll(`label[data-variation-type="${type}"]`));
            if (!options.length) {
                return;
            }

            let firstAvailable = null;

            options.forEach(option => {
                const input = option.querySelector('input');
                const value = input.value;
                const available = isCombinationAvailable(
                    type === 'ram' ? value : selected.ram,
                    type === 'storage' ? value : selected.storage,
                    type === 'color' ? value : selected.color
                );

                input.disabled = !available;
                option.classList.toggle('disabled', !available);

                if (available && !firstAvailable) {
                    firstAvailable = input;
                }

                if (!available && input.checked) {
                    input.checked = false;
                }
            });

            const hasChecked = options.some(option => {
                const input = option.querySelector('input');
                return input.checked && !input.disabled;
            });

            if (!hasChecked && firstAvailable) {
                firstAvailable.checked = true;
                selected[type] = firstAvailable.value;
            }
        });

        refreshActiveVariationOptions();
        applyColorSwatches();
    }

    function initVariationSelectors() {
        // Dispara atualização quando o input muda (via clique ou programaticamente)
        document.querySelectorAll('.variation-option input').forEach(input => {
            input.addEventListener('change', () => {
                syncVariationOptionAvailability();
                applyColorImages(getSelectedValue('color'));
                updateVariation();
            });
        });

        // Permite clicar no "card" inteiro da opção para selecionar
        document.querySelectorAll('label.variation-option').forEach(label => {
            label.addEventListener('click', (e) => {
                const input = label.querySelector('input');
                if (!input || input.disabled) {
                    return;
                }
                if (!input.checked) {
                    input.checked = true;
                    // Garante que os listeners de change sejam disparados
                    const ev = new Event('change', { bubbles: true });
                    input.dispatchEvent(ev);
                }
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

        const combinationExists = isCombinationAvailable(ram, storage, color);

        applyColorImages(color);

        if (!combinationExists) {
            if (unavailableMessage) {
                unavailableMessage.style.display = 'flex';
            }
            setAddToCartDisabled(true);
            selectedVariationId = null;

            const priceDisplay = document.getElementById('product-price-display');
            if (priceDisplay) {
                priceDisplay.textContent = 'R$ {{ number_format($product->price, 2, ',', '.') }}';
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
                            stockBadge.className = 'badge bg-success';
                            stockBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Em estoque (' + data.variation.stock_quantity + ' unidades)';
                            stockDisplay.style.display = 'block';
                            setAddToCartDisabled(false);
                        } else {
                            stockBadge.className = 'badge bg-danger';
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

                    // Frete removido: sem recálculo de envio
                    // Recalcular frete automaticamente se o CEP já foi informado
                    try {
                        const cepInput = document.getElementById('cep-destino');
                        const qtyShipping = document.getElementById('qty-shipping');
                        const cep = (cepInput?.value || '').replace(/\D/g, '').slice(0,8);
                        const qty = parseInt(qtyShipping?.value || '1', 10) || 1;
                        if (cep && cep.length === 8 && typeof calcularFrete === 'function') {
                            calcularFrete(cep, qty);
                        }
                    } catch (e) {
                        console.warn('Não foi possível recalcular o frete após mudar a variação.', e);
                    }
                } else {
                    selectedVariationId = null;
                    setAddToCartDisabled(true);
                }
            })
            .catch(error => {
                console.error('Erro ao buscar variação:', error);
            });
    }

    initVariationSelectors();
    @endif
</script>
@if(request()->has('debug') && filter_var(request('debug'), FILTER_VALIDATE_BOOLEAN))
<script>
    // UI Diagnostics - only when ?debug=1
    (function(){
        const dbgBtn = document.createElement('button');
        dbgBtn.textContent = 'UI Debug ON';
        dbgBtn.style.cssText = 'position:fixed;bottom:16px;right:16px;z-index:2147483647;padding:8px 12px;border-radius:8px;background:#111;color:#fff;border:none;box-shadow:0 6px 20px rgba(0,0,0,.25);pointer-events:auto;';
        document.body.appendChild(dbgBtn);
        let enabled = false;
        let handler = null;
        function format(el){
            if(!el) return 'null';
            const cs = window.getComputedStyle(el);
            return `${el.tagName.toLowerCase()}${el.id?('#'+el.id):''}${el.className?('.'+el.className.toString().replace(/\s+/g,'.')):''} | z:${cs.zIndex} | pe:${cs.pointerEvents} | pos:${cs.position}`;
        }
        function probe(e){
            const x = e.clientX, y = e.clientY;
            const top = document.elementFromPoint(x,y);
            console.groupCollapsed('UI click probe @', x, y);
            console.log('elementFromPoint:', top, format(top));
            console.log('target:', e.target, format(e.target));
            const path = e.composedPath ? e.composedPath() : [];
            if(path.length){
                console.log('path:');
                path.slice(0,8).forEach((n,i)=>console.log(i, format(n)));
            }
            const lsr = document.querySelector('.live-search-results');
            if(lsr){
                const cs = getComputedStyle(lsr);
                console.log('live-search-results:', format(lsr), 'display:', cs.display, 'visibility:', cs.visibility);
                console.log('live-search-content pe:', getComputedStyle(document.querySelector('.live-search-content')||lsr).pointerEvents);
            }
            console.groupEnd();
            // Visual pin
            const pin = document.createElement('div');
            pin.style.cssText = 'position:fixed;width:10px;height:10px;border-radius:50%;background:#ff3b30;box-shadow:0 0 0 4px rgba(255,59,48,.3);transform:translate(-50%,-50%);z-index:2147483647;pointer-events:none;';
            pin.style.left = x+'px'; pin.style.top = y+'px';
            document.body.appendChild(pin);
            setTimeout(()=>pin.remove(), 800);
        }
        dbgBtn.addEventListener('click', ()=>{
            enabled = !enabled;
            dbgBtn.textContent = enabled ? 'UI Debug: Capturando (clique em qualquer lugar)' : 'UI Debug ON';
            if(enabled){
                handler = probe; document.addEventListener('click', handler, true);
            } else if(handler){
                document.removeEventListener('click', handler, true); handler = null;
            }
        });
        // Utilitário: força esconder o dropdown de busca
        window.forceCloseLiveSearch = function(){
            const el = document.getElementById('liveSearchResults'); if(el){ el.style.display='none'; console.log('Live Search fechado'); }
        }
        console.info('UI Debug habilitado. Use window.forceCloseLiveSearch() para fechar o dropdown de busca.');
        console.info('Boot flags:', window.__PDP_BOOT);
    })();
</script>
@endif
@endsection