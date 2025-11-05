@extends('layouts.app')

@section('title', 'Feira das Fábricas - Sua Loja Online Completa')

@section('styles')
<style>
    /* CSS Otimizado - Performance First */
    :root {
        --primary: #0f172a;
        --secondary: #ec4899;
        --dark: #111827;
        --gray: #6b7280;
        --white: #ffffff;
        --header-dark: #0f172a;
    }

    body {
        background: #f8fafc;
        font-family: system-ui, -apple-system, sans-serif;
        line-height: 1.6;
    }

    /* Hero Section Simplificado */
    .hero-section {
        min-height: 70vh;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        text-align: center;
        padding: 3rem 0;
    }

    .hero-content {
        padding: 2rem 0;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }

    .btn-primary {
        background: var(--secondary);
        color: white;
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-primary:hover {
        background: #d946ef;
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: transparent;
        color: white;
        border: 2px solid white;
        padding: 0.9rem 1.9rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-secondary:hover {
        background: rgba(255,255,255,0.1);
        color: white;
        text-decoration: none;
    }

    /* Sections Simplificadas */
    .section {
        padding: 4rem 0;
    }

    .section-light {
        background: #f8fafc;
    }

    .section-white {
        background: white;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
        text-align: center;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: var(--gray);
        text-align: center;
        margin-bottom: 3rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Department Cards Simplificados */
    .department-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .department-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .department-icon {
        font-size: 4rem;
        color: white;
    }

    .department-overlay {
        padding: 2rem;
        color: var(--dark);
    }

    .department-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--dark);
    }

    .department-description {
        font-size: 1rem;
        color: var(--gray);
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .department-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        text-align: center;
        padding: 0.5rem;
        background: var(--header-dark);
        border-radius: 8px;
    }

    .stat-number {
        font-size: 1.2rem;
        font-weight: 700;
        color: white;
        display: block;
    }

    .stat-label {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.9);
        text-transform: uppercase;
    }

    .department-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: block;
        text-align: center;
        width: 100%;
    }

    .department-btn:hover {
        background: #1e293b;
        color: white;
        text-decoration: none;
    }

    /* Feature Cards Simplificados */
    .feature-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        height: 100%;
        border: 1px solid #e5e7eb;
    }

    .feature-icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }

    .feature-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
    }

    .feature-description {
        color: var(--gray);
        line-height: 1.6;
    }

    /* B2B Section Simplificado */
    .b2b-section {
        background: var(--dark);
        color: white;
        padding: 3rem 2rem;
        border-radius: 12px;
        text-align: center;
    }

    .b2b-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: white;
    }

    .b2b-description {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .b2b-btn {
        background: var(--secondary);
        color: white;
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .b2b-btn:hover {
        background: #d946ef;
        color: white;
        text-decoration: none;
    }

    /* Responsividade Simplificada */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-primary,
        .btn-secondary {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }
        
        .department-stats {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .section-title {
            font-size: 2rem;
        }
        
        .b2b-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 480px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1rem;
        }
        
        .btn-primary,
        .btn-secondary {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }
        
        .department-stats {
            grid-template-columns: 1fr;
        }
        
        .department-overlay {
            padding: 1.5rem;
        }
        
        .feature-card {
            padding: 1.5rem;
        }
        
        .b2b-section {
            padding: 2rem 1.5rem;
        }
    }
</style>
@endsection

@section('content')

<!-- Banner Principal -->
<x-banner-slider 
    :departmentId="null" 
    position="hero" 
    :limit="1" 
    :showDots="false" 
    :showArrows="false" 
    :autoplay="true" 
    :interval="5000" />

<!-- Hero Section (Fallback se não houver banner) -->
@php
    use App\Helpers\BannerHelper;
    $heroBanners = BannerHelper::getGlobalBanners('hero', 1);
@endphp
@if($heroBanners->count() == 0)
<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Feira das Fábricas</h1>
            <p class="hero-subtitle">
                Sua loja online completa com produtos de qualidade para todas as suas necessidades. 
                Eletrônicos, vestuário e muito mais em um só lugar!
            </p>
            <div class="hero-actions">
                <a href="{{ route('department.index', 'eletronicos') }}" class="btn-primary">
                    <i class="fas fa-laptop me-2"></i>
                    Ver Eletrônicos
                </a>
                <a href="{{ route('department.list') }}" class="btn-secondary">
                    <i class="fas fa-th-large me-2"></i>
                    Todos os Departamentos
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Departamentos -->
<section class="section section-light">
    <div class="container">
        <h2 class="section-title">Nossos Departamentos</h2>
        <p class="section-subtitle">
            Explore nossa ampla variedade de produtos organizados por departamentos especializados
        </p>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="department-card">
                    <div class="department-image" style="background-image: url('{{ asset('images/eletronicos.jpg') }}'); background-size: cover; background-position: center;">
                    </div>
                    <div class="department-overlay">
                        <h3 class="department-title">Eletrônicos</h3>
                        <p class="department-description">
                            Smartphones, tablets, notebooks, acessórios e muito mais tecnologia.
                        </p>
                        
                        <a href="{{ route('department.index', 'eletronicos') }}" class="department-btn">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Explorar Eletrônicos
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="department-card">
                    <div class="department-image" style="background-image: url('{{ asset('images/vestuario feminino.jpg') }}'); background-size: cover; background-position: center;">
                    </div>
                    <div class="department-overlay">
                        <h3 class="department-title">Vestuário Feminino</h3>
                        <p class="department-description">
                            Moda feminina com elegância e estilo. Roupas, calçados e acessórios exclusivos.
                        </p>
                        
                        <a href="{{ route('department.index', 'vestuario-feminino') }}" class="department-btn">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Explorar Feminino
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="department-card">
                    <div class="department-image" style="background-image: url('{{ asset('images/vestuario masculino.jpg') }}'); background-size: cover; background-position: center;">
                    </div>
                    <div class="department-overlay">
                        <h3 class="department-title">Vestuário Masculino</h3>
                        <p class="department-description">
                            Estilo masculino com conforto e qualidade. Roupas, calçados e acessórios para o homem moderno.
                        </p>
                        
                        <a href="{{ route('department.index', 'vestuario-masculino') }}" class="department-btn">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Explorar Masculino
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Por que escolher a Feira das Fábricas -->
<section class="section section-white">
    <div class="container">
        <h2 class="section-title">Por que escolher a Feira das Fábricas?</h2>
        <p class="section-subtitle">
            Somos mais que uma loja online. Somos seu parceiro de confiança para todas as suas necessidades.
        </p>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <i class="fas fa-shipping-fast feature-icon"></i>
                    <h3 class="feature-title">Entrega Rápida</h3>
                    <p class="feature-description">
                        Entregamos seus produtos com rapidez e segurança em todo o Brasil.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3 class="feature-title">Garantia de Qualidade</h3>
                    <p class="feature-description">
                        Todos os nossos produtos passam por rigoroso controle de qualidade.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <i class="fas fa-headset feature-icon"></i>
                    <h3 class="feature-title">Suporte 24/7</h3>
                    <p class="feature-description">
                        Nossa equipe está sempre disponível para ajudar você.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <i class="fas fa-tags feature-icon"></i>
                    <h3 class="feature-title">Preços Competitivos</h3>
                    <p class="feature-description">
                        Os melhores preços do mercado com condições especiais para empresas.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <i class="fas fa-store feature-icon"></i>
                    <h3 class="feature-title">Variedade de Produtos</h3>
                    <p class="feature-description">
                        Milhares de produtos em diferentes categorias para todas as suas necessidades.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <i class="fas fa-credit-card feature-icon"></i>
                    <h3 class="feature-title">Pagamento Seguro</h3>
                    <p class="feature-description">
                        Processamos seus pagamentos com total segurança e privacidade.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- B2B Section -->
<section class="section section-light">
    <div class="container">
        <div class="b2b-section">
            <div class="b2b-content">
                <h2 class="b2b-title">Conta B2B</h2>
                <p class="b2b-description">
                    Condições especiais para empresas. Preços diferenciados, atendimento prioritário e facilidades exclusivas para seu negócio.
                </p>
                <a href="{{ route('register.b2b') }}" class="b2b-btn">
                    <i class="fas fa-building me-2"></i>
                    Criar Conta B2B
                </a>
            </div>
        </div>
    </div>
</section>
@endsection