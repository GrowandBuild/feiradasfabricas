@php
    /**
     * Expects $section (App\Models\HomepageSection)
     */
    $products = $section->getProducts();
@endphp

@if($products && $products->count() > 0)
    <section class="section-gray">
        <div class="container">
        <h2 class="section-title">{{ $section->title }}</h2>
        <p class="section-subtitle">Seleção especial selecionada no painel</p>
        <div class="row g-2">
            @foreach($products as $product)
                <div class="col-lg-3 col-md-6 col-6 mb-2">
                    <div class="product-card position-relative">
                        @auth('admin')
                            <div class="admin-actions-floating position-absolute" style="top: 8px; right: 8px; z-index: 10; display: flex; gap: 6px;">
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="btn-admin-edit-floating" 
                                   title="Editar Produto"
                                   style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid var(--secondary-color, #ff6b35); color: var(--secondary-color, #ff6b35); display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn-admin-delete-floating"
                                        title="Excluir Produto"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-product-slug="{{ $product->slug }}"
                                        style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid var(--danger-color, #dc3545); color: var(--danger-color, #dc3545); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease; cursor: pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        @endauth
                        <div class="product-image product-image-carousel" data-product-id="{{ $product->id }}">
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
                            
                            @foreach($allImages as $index => $img)
                                <img src="{{ $img }}" 
                                     alt="{{ $product->name }}" 
                                     class="product-carousel-image {{ $index === 0 ? 'active' : '' }}"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                     decoding="async"
                                     data-index="{{ $index }}">
                            @endforeach
                            
                            @if($product->is_featured)
                                <div class="product-badge">Destaque</div>
                            @endif
                        </div>
                        <div class="product-info">
                            <h6 class="product-title">{{ $product->name }}</h6>
                            <div class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('styles')
<style>
    /* Botões de ação flutuantes para admin nos cards de produtos */
    .admin-actions-floating {
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .product-card:hover .admin-actions-floating {
        opacity: 1;
    }

    .btn-admin-edit-floating,
    .btn-admin-delete-floating {
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .product-card:hover .btn-admin-edit-floating,
    .product-card:hover .btn-admin-delete-floating {
        opacity: 1;
        transform: scale(1.1);
    }

    .btn-admin-edit-floating:hover {
        background: var(--secondary-color, #ff6b35) !important;
        color: #ffffff !important;
        transform: scale(1.15);
        box-shadow: 0 6px 16px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.5);
    }

    .btn-admin-delete-floating:hover {
        background: var(--danger-color, #dc3545) !important;
        color: #ffffff !important;
        transform: scale(1.15);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.5);
    }

    /* Carrossel de imagens de variações */
    .product-image-carousel {
        position: relative;
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
        transition: opacity 1s ease-in-out;
        z-index: 1;
    }

    .product-carousel-image.active {
        opacity: 1;
        z-index: 2;
    }

    .product-carousel-image:first-child {
        position: relative;
    }

    @media (max-width: 768px) {
        .admin-actions-floating {
            top: 6px !important;
            right: 6px !important;
            gap: 4px !important;
        }
        .btn-admin-edit-floating,
        .btn-admin-delete-floating {
            width: 32px !important;
            height: 32px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
            
            // Iniciar carrossel
            startCarousel();
            
            // Pausar ao passar o mouse
            carousel.addEventListener('mouseenter', stopCarousel);
            carousel.addEventListener('mouseleave', startCarousel);
        });
    }
    
    // Inicializar carrosséis
    initVariationCarousels();
    
    // Reinicializar se novos produtos forem carregados dinamicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initVariationCarousels();
            }
        });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Botão de excluir produto
    document.querySelectorAll('.btn-admin-delete-floating').forEach(function(btn) {
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
});
</script>
@endpush
@endif
