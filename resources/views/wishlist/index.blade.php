@extends('layouts.app')

@section('title', 'Lista de Desejos')

@section('content')
<div class="container py-4 wishlist-page">
    <div class="wishlist-header mb-4">
        <h1 class="wishlist-title mb-2">
            <i class="bi bi-heart-fill me-2" style="color: var(--secondary-color, #ff6b35);"></i>
            Lista de Desejos
        </h1>
        <p class="wishlist-subtitle text-muted">
            Seus produtos favoritos em um só lugar
        </p>
    </div>

    @if($favorites->count() === 0)
        <div class="wishlist-empty text-center py-5">
            <div class="empty-icon mb-3">
                <i class="bi bi-heart" style="font-size: 4rem; color: #ddd;"></i>
            </div>
            <h3 class="mb-2">Sua lista de desejos está vazia</h3>
            <p class="text-muted mb-4">Adicione produtos que você gosta à sua lista de desejos para encontrá-los facilmente depois.</p>
            <a href="{{ route('products') }}" class="btn btn-primary" style="background: var(--secondary-color); border-color: var(--secondary-color);">
                <i class="bi bi-arrow-left me-2"></i>Continuar Comprando
            </a>
        </div>
    @else
        <div class="wishlist-stats mb-4">
            <div class="alert alert-info d-flex align-items-center" style="background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1) 0%, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.05) 100%); border: 2px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.2); border-radius: 12px;">
                <i class="bi bi-info-circle me-2" style="color: var(--secondary-color, #ff6b35); font-size: 1.25rem;"></i>
                <span>
                    <strong>{{ $favorites->total() }}</strong> {{ $favorites->total() === 1 ? 'produto' : 'produtos' }} na sua lista de desejos
                </span>
            </div>
        </div>

        <div class="wishlist-grid row g-3 g-md-4">
            @foreach($favorites as $favorite)
                @php $product = $favorite->product; @endphp
                @if($product)
                <div class="col-6 col-md-4 col-lg-3 wishlist-item">
                    <div class="product-card-wishlist card h-100 position-relative border-0 shadow-sm">
                        <button type="button" 
                                class="btn-remove-wishlist position-absolute" 
                                data-product-id="{{ $product->id }}"
                                data-product-slug="{{ $product->slug }}"
                                title="Remover da lista de desejos"
                                style="top: 8px; right: 8px; z-index: 10; width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid var(--danger-color, #dc3545); color: var(--danger-color, #dc3545); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                            <i class="bi bi-heart-fill"></i>
                        </button>

                        <a href="{{ route('product', $product->slug) }}" class="text-decoration-none text-dark">
                            <div class="product-image-wishlist position-relative" style="aspect-ratio: 1/1; overflow: hidden; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <img src="{{ $product->first_image }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-100 h-100" 
                                     style="object-fit: cover; transition: transform 0.3s ease;"
                                     loading="lazy">
                                @if($product->is_featured)
                                    <div class="product-badge position-absolute" style="top: 8px; left: 8px; background: var(--secondary-color, #ff6b35); color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; z-index: 5;">
                                        Destaque
                                    </div>
                                @endif
                            </div>
                            <div class="card-body py-3">
                                <h6 class="product-title-wishlist mb-2" style="font-weight: 600; color: var(--text-dark); min-height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $product->name }}
                                </h6>
                                <div class="product-price-wishlist mb-3" style="font-size: 1.25rem; font-weight: 700; color: var(--success-color, #28a745);">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                    @if($product->compare_price && $product->compare_price > $product->price)
                                        <small class="text-muted text-decoration-line-through d-block" style="font-size: 0.875rem; font-weight: 400;">
                                            R$ {{ number_format($product->compare_price, 2, ',', '.') }}
                                        </small>
                                    @endif
                                </div>
                                <a href="{{ route('product', $product->slug) }}" class="btn btn-primary w-100" style="background: var(--secondary-color); border-color: var(--secondary-color);">
                                    <i class="bi bi-eye me-1"></i>Ver Detalhes
                                </a>
                            </div>
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $favorites->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Página de Lista de Desejos */
    .wishlist-page {
        max-width: var(--site-container-max-width, 1320px);
        margin: 0 auto;
    }

    .wishlist-header {
        text-align: center;
        padding: 2rem 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.05) 0%, #ffffff 100%);
        border-radius: 12px;
        border: 2px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1);
        margin-bottom: 2rem;
    }

    .wishlist-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .wishlist-subtitle {
        font-size: 1.125rem;
        margin-bottom: 0;
    }

    .wishlist-empty {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 4rem 2rem;
        border: 2px dashed #dee2e6;
    }

    .wishlist-empty h3 {
        color: var(--text-dark);
        font-weight: 700;
    }

    .product-card-wishlist {
        border-radius: 12px !important;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .product-card-wishlist:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    }

    .product-card-wishlist:hover .product-image-wishlist img {
        transform: scale(1.1);
    }

    .btn-remove-wishlist {
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .product-card-wishlist:hover .btn-remove-wishlist {
        opacity: 1;
        transform: scale(1.1);
    }

    .btn-remove-wishlist:hover {
        background: var(--danger-color, #dc3545) !important;
        color: #ffffff !important;
        transform: scale(1.15);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .wishlist-item {
        animation: fadeInUp 0.5s ease;
        animation-fill-mode: both;
    }

    .wishlist-item:nth-child(1) { animation-delay: 0.1s; }
    .wishlist-item:nth-child(2) { animation-delay: 0.2s; }
    .wishlist-item:nth-child(3) { animation-delay: 0.3s; }
    .wishlist-item:nth-child(4) { animation-delay: 0.4s; }
    .wishlist-item:nth-child(5) { animation-delay: 0.1s; }
    .wishlist-item:nth-child(6) { animation-delay: 0.2s; }
    .wishlist-item:nth-child(7) { animation-delay: 0.3s; }
    .wishlist-item:nth-child(8) { animation-delay: 0.4s; }

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

    /* Paginação */
    .pagination {
        --bs-pagination-color: var(--secondary-color, #ff6b35);
        --bs-pagination-hover-color: var(--secondary-color, #ff6b35);
        --bs-pagination-focus-color: var(--secondary-color, #ff6b35);
        --bs-pagination-active-bg: var(--secondary-color, #ff6b35);
        --bs-pagination-active-border-color: var(--secondary-color, #ff6b35);
    }

    @media (max-width: 768px) {
        .wishlist-title {
            font-size: 1.75rem;
        }

        .wishlist-subtitle {
            font-size: 1rem;
        }

        .wishlist-header {
            padding: 1.5rem 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remover da lista de desejos
        document.querySelectorAll('.btn-remove-wishlist').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = this.dataset.productId;
                const productSlug = this.dataset.productSlug;
                
                if (!confirm('Remover este produto da lista de desejos?')) {
                    return;
                }

                const button = this;
                button.disabled = true;
                button.innerHTML = '<i class="bi bi-hourglass-split"></i>';

                fetch(`/produto/${productSlug}/desfavoritar`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remover o card
                        button.closest('.wishlist-item').style.animation = 'fadeOut 0.3s ease';
                        setTimeout(() => {
                            button.closest('.wishlist-item').remove();
                            
                            // Atualizar contador no header
                            if (typeof updateWishlistCount === 'function') {
                                updateWishlistCount(data.count);
                            }
                            
                            // Verificar se ficou vazio
                            const grid = document.querySelector('.wishlist-grid');
                            if (grid && grid.children.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    } else {
                        alert(data.message || 'Erro ao remover produto.');
                        button.disabled = false;
                        button.innerHTML = '<i class="bi bi-heart-fill"></i>';
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao remover produto.');
                    button.disabled = false;
                    button.innerHTML = '<i class="bi bi-heart-fill"></i>';
                });
            });
        });
    });

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.9);
        }
    }
</script>
@endpush
@endsection

