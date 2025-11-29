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
                    <div class="product-card">
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
@endif
