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
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="btn-admin-edit-floating position-absolute" 
                               title="Editar Produto"
                               style="top: 8px; right: 8px; z-index: 10; width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 2px solid var(--secondary-color, #ff6b35); color: var(--secondary-color, #ff6b35); display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endauth
                        <div class="product-image">
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
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
    /* Botão de edição flutuante para admin nos cards de produtos */
    .btn-admin-edit-floating {
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    .product-card:hover .btn-admin-edit-floating {
        opacity: 1;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.4);
    }

    .btn-admin-edit-floating:hover {
        background: var(--secondary-color, #ff6b35) !important;
        color: #ffffff !important;
        transform: scale(1.15);
        box-shadow: 0 6px 16px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.5);
    }

    @media (max-width: 768px) {
        .btn-admin-edit-floating {
            width: 32px !important;
            height: 32px !important;
            top: 6px !important;
            right: 6px !important;
        }
    }
</style>
@endpush
@endif
