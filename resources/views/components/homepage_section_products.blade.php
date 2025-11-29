@php
    /**
     * Expects $section (App\Models\HomepageSection)
     */
    $products = $section->getProducts();
@endphp

@if($products && $products->count() > 0)
<section class="section-gray" data-debug-homepage-section="{{ $section->id }}" style="outline: 4px dashed rgba(255,0,0,0.20); position: relative; z-index: 2147483647; display: block !important; visibility: visible !important; opacity: 1 !important;">
    <div style="position: absolute; left: 8px; top: 8px; background: rgba(255,0,0,0.85); color: #fff; padding: 4px 8px; font-size: 12px; border-radius: 4px; z-index:10000;">DEBUG: {{ $section->title }} (#{{ $section->id }})</div>
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
    <div id="debug-force-banner" style="position:fixed;left:12px;right:12px;top:12px;padding:10px 14px;background:rgba(255,69,69,0.95);color:#fff;border-radius:6px;z-index:2147483647;box-shadow:0 6px 20px rgba(0,0,0,0.25);font-weight:600;display:none;">DEBUG VISIBILITY: sessões forçadas (remova este banner depois)</div>
</section>
@endif

<script>
document.addEventListener('DOMContentLoaded', function(){
    try {
        // Show and pin a small banner so user knows debug-forced visibility is active
        var banner = document.getElementById('debug-force-banner');
        if (banner) {
            banner.style.display = 'block';
        }

        // Force styles for all debug homepage sections to defeat hiding CSS/JS
        var nodes = document.querySelectorAll('[data-debug-homepage-section]');
        if (!nodes || nodes.length === 0) return;

        nodes.forEach(function(el, idx){
            try {
                // Strong inline important rules
                el.style.setProperty('display','block','important');
                el.style.setProperty('visibility','visible','important');
                el.style.setProperty('opacity','1','important');
                el.style.setProperty('z-index','2147483647','important');
                el.style.setProperty('max-height','none','important');
                el.style.setProperty('height','auto','important');

                // Also make parents visible if some parent is hiding it
                var p = el.parentElement;
                var safeLimit = 6;
                while(p && safeLimit-- > 0) {
                    try {
                        p.style.setProperty('display','block','important');
                        p.style.setProperty('visibility','visible','important');
                        p.style.setProperty('opacity','1','important');
                        p.style.setProperty('height','auto','important');
                    } catch(e) { /* ignore */ }
                    p = p.parentElement;
                }

                // Scroll the first one into view
                if (idx === 0) {
                    setTimeout(function(){
                        try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(e){}
                        // temporary highlight so it's obvious
                        var prev = el.style.boxShadow;
                        el.style.boxShadow = '0 0 0 8px rgba(255,0,0,0.20)';
                        setTimeout(function(){ el.style.boxShadow = prev; }, 5000);
                    }, 200);
                }
            } catch(e) { /* ignore per-element errors */ }
        });
    } catch(e) { /* ignore overall errors */ }
});
</script>
