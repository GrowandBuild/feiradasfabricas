@extends('layouts.app')

@section('title', 'Vestuário Masculino - Feira das Fábricas')

@section('styles')
<style>
    /* Elegant Theme - Vestuário Masculino */
    :root {
        --elegant-dark: #0f172a;
        --elegant-blue: #1e293b;
        --elegant-light: #334155;
        --elegant-white: #ffffff;
        --elegant-gray: #f8fafc;
        --elegant-text: #1e293b;
        --elegant-text-light: #64748b;
        --elegant-accent: #2196f3;
    }

    body {
        background-color: var(--elegant-white);
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    }

    /* Hero Section - Masculino */
    .hero-section {
        min-height: 50vh;
        position: relative;
        overflow: hidden;
        padding: 0;
    }

    .hero-banner-full {
        width: 100%;
        height: 50vh;
        position: relative;
        overflow: hidden;
    }

    .hero-banner-image {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }

    .hero-default-bg {
        background: linear-gradient(135deg, #2196f3 0%, #1976d2 50%, #1565c0 100%);
    }

    .hero-banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(33, 150, 243, 0.7) 0%, rgba(25, 118, 210, 0.5) 50%, rgba(21, 101, 192, 0.3) 100%);
        display: flex;
        align-items: center;
    }

    .hero-content-row {
        height: 50vh;
        min-height: 350px;
    }

    .hero-banner-content {
        color: white;
        z-index: 2;
        position: relative;
    }

    .hero-banner-title {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0.8rem;
        line-height: 1.1;
        background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .hero-banner-subtitle {
        font-size: 1rem;
        margin-bottom: 1rem;
        opacity: 0.95;
        font-weight: 300;
        line-height: 1.6;
        max-width: 600px;
    }

    .hero-banner-actions {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .hero-btn-primary {
        background: var(--elegant-accent);
        color: white;
        border: none;
        padding: 18px 40px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .hero-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(33, 150, 243, 0.4);
        background: #1976d2;
        color: white;
    }

    .hero-btn-secondary {
        background: transparent;
        color: white;
        border: 2px solid white;
        padding: 16px 38px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .hero-btn-secondary:hover {
        background: white;
        color: var(--elegant-accent);
        border-color: white;
        transform: translateY(-3px);
    }

    /* Sections */
    .section-elegant {
        padding: 10px 0;
        background: var(--elegant-white);
    }

    .section-gray {
        background: var(--elegant-gray);
        padding: 30px 0;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--elegant-dark);
        margin-bottom: 8px;
        text-align: center;
    }

    .section-subtitle {
        font-size: 0.9rem;
        color: var(--elegant-text-light);
        text-align: center;
        margin-bottom: 20px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Product Cards */
    .product-card {
        background: var(--elegant-white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(33, 150, 243, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(33, 150, 243, 0.04);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.12);
    }

    .product-image {
        height: 160px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        aspect-ratio: 1;
        flex-shrink: 0;
        border-radius: 12px 12px 0 0;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.2s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.02);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--elegant-accent);
        color: white;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2);
        z-index: 10;
    }

    .product-info {
        padding: 16px;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
        background: linear-gradient(180deg, rgba(255,255,255,0.9) 0%, rgba(227,242,253,0.8) 100%);
    }

    .product-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        line-height: 1.3;
        flex-grow: 1;
        letter-spacing: -0.025em;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: 800;
        color: #059669;
        margin-bottom: 8px;
        flex-shrink: 0;
        letter-spacing: -0.025em;
    }

    .product-btn {
        background: var(--elegant-accent);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        width: 100%;
        flex-shrink: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2);
        text-decoration: none;
    }
    
    .product-btn:hover {
        background: #1976d2;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }

    /* Carrossel de Produtos */
    #productsCarousel {
        position: relative;
    }

    #productsCarousel .carousel-control-prev,
    #productsCarousel .carousel-control-next {
        width: 50px;
        height: 50px;
        background: var(--elegant-accent);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
        transition: all 0.3s ease;
    }

    #productsCarousel .carousel-control-prev {
        left: -25px;
    }

    #productsCarousel .carousel-control-next {
        right: -25px;
    }

    #productsCarousel .carousel-control-prev:hover,
    #productsCarousel .carousel-control-next:hover {
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
    }

    /* Seções específicas masculinas */
    .category-section {
        margin-bottom: 50px;
    }

    .category-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .category-icon {
        font-size: 3rem;
        color: var(--elegant-accent);
        margin-bottom: 15px;
    }

    .category-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--elegant-dark);
        margin-bottom: 10px;
    }

    .category-subtitle {
        font-size: 1.1rem;
        color: var(--elegant-text-light);
        max-width: 500px;
        margin: 0 auto;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .hero-banner-title {
            font-size: 2rem;
        }
        
        .hero-banner-subtitle {
            font-size: 1.1rem;
        }
        
        .hero-banner-actions {
            flex-direction: column;
            gap: 15px;
        }
        
        .hero-btn-primary,
        .hero-btn-secondary {
            padding: 15px 30px;
            font-size: 1.1rem;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .hero-banner-title {
            font-size: 2rem;
        }
        
        .hero-banner-subtitle {
            font-size: 1rem;
        }
        
        .hero-btn-primary,
        .hero-btn-secondary {
            padding: 12px 25px;
            font-size: 1rem;
        }
    }
</style>
@endsection

@push('scripts')
<script>
// Informar o slug atual do departamento e carregar configuração de seções correspondente
window.CurrentDepartmentSlug = 'vestuario-masculino';
window.DepartmentSectionsConfig = (function(){
    try {
        const raw = @json(setting('dept_vestuario-masculino_sections'));
        if (Array.isArray(raw)) return raw;
        if (typeof raw === 'string' && raw.trim().length) { try { return JSON.parse(raw); } catch(e){} }
    } catch(e) {}
    return [];
})();
</script>
@endpush

@section('content')
<!-- Hero Section (unificado via partial banner-universal) -->
<div class="hero-section no-padding">
    @php
        use App\Helpers\BannerHelper;
        $hasHero = BannerHelper::getBannersForDisplay($department->id ?? null, 'hero', 1)->count() > 0;
    @endphp

    @if($hasHero)
        @include('partials.banner-universal', ['departmentId' => $department->id ?? null, 'position' => 'hero', 'limit' => 5])
    @else
        <div class="hero-banner-full">
            <div class="hero-banner-image hero-default-bg">
                <div class="hero-banner-overlay">
                    <div class="container">
                        <div class="row align-items-center hero-content-row">
                            <div class="col-lg-8">
                                <div class="hero-banner-content">
                                    <h1 class="hero-banner-title">
                                        <i class="fas fa-male me-3"></i>
                                        Vestuário Masculino
                                    </h1>
                                    <p class="hero-banner-subtitle">
                                        Estilo masculino com conforto e qualidade. Roupas, calçados e acessórios para o homem moderno.
                                    </p>
                                    <div class="hero-banner-actions">
                                        <a href="{{ route('products') }}?department=vestuario-masculino" class="btn hero-btn-primary">
                                            <i class="fas fa-shopping-bag me-2"></i>
                                            Ver Produtos
                                        </a>
                                        <a href="{{ route('contact') }}" class="btn hero-btn-secondary">
                                            <i class="fas fa-phone me-2"></i>
                                            Contato
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Produtos em Destaque -->
@if($featuredProducts && $featuredProducts->count() > 0)
<section class="section-gray">
    <div class="container">
        <h2 class="section-title">Produtos em Destaque</h2>
        <p class="section-subtitle">
            Seleção especial de produtos masculinos para seu estilo único
        </p>
        <!-- Carrossel de Produtos -->
        <div id="productsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $chunks = $featuredProducts->chunk(4);
                @endphp
                @foreach($chunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <div class="product-card">
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                            @if($product->is_featured)
                                                <div class="product-badge">Destaque</div>
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <h6 class="product-title">{{ $product->name }}</h6>
                                            <div class="product-price">
                                                R$ {{ number_format($product->price, 2, ',', '.') }}
                                                @if($product->b2b_price)
                                                    <small class="text-muted d-block">B2B: R$ {{ number_format($product->b2b_price, 2, ',', '.') }}</small>
                                                @endif
                                            </div>
                                            <a href="{{ route('product', $product->slug) }}?department={{ $department->slug }}" class="product-btn">
                                                <i class="fas fa-shopping-cart me-2"></i>
                                                Ver Detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Controles do Carrossel -->
            @if($chunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#productsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos -->
<section class="section-elegant" style="padding: 20px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products') }}?department=vestuario-masculino" class="btn" style="background: var(--elegant-accent); color: white; padding: 15px 35px; font-size: 1.1rem; font-weight: 600; border-radius: 50px; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);">
                Ver Todos os Produtos Masculinos
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Seção Roupas Masculinas -->
<section class="section-elegant">
    <div class="container">
        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-user-tie category-icon"></i>
                <h2 class="category-title">Roupas Masculinas</h2>
                <p class="category-subtitle">
                    Descubra nossa coleção de roupas masculinas com estilo, conforto e qualidade
                </p>
            </div>
            
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-male fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Coleção Masculina em Expansão</h4>
                    <p class="text-muted">Estamos preparando uma seleção especial de roupas masculinas para você!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção Calçados Masculinos -->
<section class="section-gray">
    <div class="container">
        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-shoe-prints category-icon"></i>
                <h2 class="category-title">Calçados Masculinos</h2>
                <p class="category-subtitle">
                    Conforto e estilo para seus pés com nossa linha de calçados masculinos
                </p>
            </div>
            
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Calçados Masculinos em Breve</h4>
                    <p class="text-muted">Estamos preparando uma seleção especial de calçados masculinos para você!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Seção Acessórios Masculinos -->
<section class="section-elegant">
    <div class="container">
        <div class="category-section">
            <div class="category-header">
                <i class="fas fa-watch category-icon"></i>
                <h2 class="category-title">Acessórios Masculinos</h2>
                <p class="category-subtitle">
                    Complete seu look com nossos acessórios exclusivos para homens
                </p>
            </div>
            
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-gem fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Acessórios Masculinos em Breve</h4>
                    <p class="text-muted">Estamos preparando uma seleção especial de acessórios masculinos para você!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- B2B Section -->
<section class="section-gray">
    <div class="container">
        <div class="b2b-section" style="background: linear-gradient(135deg, var(--elegant-dark) 0%, var(--elegant-blue) 50%, var(--elegant-light) 100%); color: white; padding: 60px; border-radius: 20px; text-align: center; box-shadow: 0 8px 32px rgba(15, 23, 42, 0.3);">
            <h2 class="b2b-title">Conta B2B</h2>
            <p class="b2b-description">
                Condições especiais para empresas. Preços diferenciados e atendimento prioritário.
            </p>
            <a href="{{ route('register.b2b') }}" class="btn b2b-btn" style="background: var(--elegant-accent); color: white; border: none; padding: 15px 35px; font-size: 1.1rem; font-weight: 600; border-radius: 50px; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);">
                <i class="fas fa-building me-2"></i>
                Criar Conta B2B
            </a>
        </div>
    </div>
</section>
@endsection
