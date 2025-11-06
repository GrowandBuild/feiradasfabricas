@extends('layouts.app')

@section('title', 'Departamentos - Feira das Fábricas')

@section('styles')
<style>
    /* CSS Simples e Rápido - Cores do Header */
    :root {
        --header-blue: #1e3a8a;
        --header-dark: #1e40af;
        --accent-orange: #f97316;
        --text-dark: #1f2937;
        --text-gray: #6b7280;
        --bg-light: #f8fafc;
    }

    /* Header Simples */
    .premium-header {
        background: var(--header-blue);
        padding: 60px 0;
        text-align: center;
    }

    .premium-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .premium-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Seção Principal */
    .premium-section {
        padding: 60px 0;
        background: var(--bg-light);
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-dark);
        text-align: center;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        color: var(--text-gray);
        text-align: center;
        margin-bottom: 3rem;
    }

    /* Cards Simples */
    .department-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .department-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .department-header {
        background: var(--header-dark);
        padding: 2rem;
        text-align: center;
    }

    .department-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .department-icon i {
        font-size: 1.5rem;
        color: white;
    }

    .department-title {
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .department-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
    }

    .department-content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .department-description {
        color: var(--text-gray);
        line-height: 1.5;
        margin-bottom: 1.5rem;
        flex: 1;
    }

    .department-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        background: #f1f5f9;
        border-radius: 8px;
    }

    .stat-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--header-blue);
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-gray);
        text-transform: uppercase;
    }

    .department-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: auto;
    }

    .department-btn {
        flex: 1;
        background: var(--header-blue);
        color: white;
        border: none;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: background 0.2s ease;
    }

    .department-btn:hover {
        background: var(--header-dark);
        color: white;
        text-decoration: none;
    }

    .department-btn-secondary {
        flex: 1;
        background: transparent;
        color: var(--header-blue);
        border: 2px solid var(--header-blue);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease;
    }

    .department-btn-secondary:hover {
        background: var(--header-blue);
        color: white;
        text-decoration: none;
    }

    .status-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: var(--accent-orange);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .premium-header {
            padding: 2rem 0;
        }

        .premium-title {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }

        .premium-subtitle {
            font-size: 0.95rem;
        }
        
        .premium-section {
            padding: 2rem 0;
        }

        .section-title {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }

        .section-subtitle {
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }
        
        .department-card {
            margin-bottom: 1.5rem;
        }

        .department-header {
            padding: 1.25rem;
        }

        .department-icon {
            width: 50px;
            height: 50px;
        }

        .department-icon i {
            font-size: 1.25rem;
        }

        .department-title {
            font-size: 1.1rem;
        }

        .department-subtitle {
            font-size: 0.85rem;
        }
        
        .department-content {
            padding: 1rem;
        }

        .department-description {
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
        }
        
        .department-stats {
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .stat-item {
            padding: 0.75rem;
        }

        .stat-number {
            font-size: 1.1rem;
        }

        .stat-label {
            font-size: 0.75rem;
        }
        
        .department-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .department-btn,
        .department-btn-secondary {
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
        }

        .status-badge {
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.2rem 0.6rem;
            font-size: 0.7rem;
        }
    }

    @media (max-width: 480px) {
        .premium-header {
            padding: 1.5rem 0;
        }

        .premium-title {
            font-size: 1.5rem;
        }

        .premium-subtitle {
            font-size: 0.85rem;
        }

        .premium-section {
            padding: 1.5rem 0;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .section-subtitle {
            font-size: 0.85rem;
        }

        .department-header {
            padding: 1rem;
        }

        .department-icon {
            width: 45px;
            height: 45px;
        }

        .department-icon i {
            font-size: 1.1rem;
        }

        .department-title {
            font-size: 1rem;
        }

        .department-subtitle {
            font-size: 0.8rem;
        }

        .department-content {
            padding: 0.9rem;
        }

        .department-description {
            font-size: 0.85rem;
        }

        .department-stats {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .stat-item {
            padding: 0.6rem;
        }

        .stat-number {
            font-size: 1rem;
        }

        .stat-label {
            font-size: 0.7rem;
        }

        .department-btn,
        .department-btn-secondary {
            padding: 0.65rem 0.9rem;
            font-size: 0.85rem;
        }

        .status-badge {
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.15rem 0.5rem;
            font-size: 0.65rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Header Simples - Cores do Site -->
<div class="premium-header">
    <div class="container">
        <div class="premium-header-content">
            <h1 class="premium-title">Departamentos Especializados</h1>
            <p class="premium-subtitle">
                Explore nossa seleção de produtos organizados por especialidades. 
                Cada departamento oferece o melhor em tecnologia e qualidade.
            </p>
        </div>
    </div>
</div>

<!-- Seção de Departamentos -->
<section class="premium-section">
    <div class="container">
        <div class="premium-section-content">
            <h2 class="section-title">Nossos Departamentos</h2>
            <p class="section-subtitle">
                Descubra produtos de alta qualidade organizados por especialidades técnicas
            </p>
        
        @if($departments->count() > 0)
            <div class="row">
                @foreach($departments as $department)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="department-card">
                            <div class="status-badge">
                                {{ $department->is_active ? 'ATIVO' : 'INATIVO' }}
                            </div>
                            
                            <div class="department-header">
                                <div class="department-icon">
                                    <i class="{{ $department->icon ?? 'fas fa-building' }}"></i>
                                </div>
                                <h3 class="department-title">{{ $department->name }}</h3>
                                <p class="department-subtitle">{{ $department->subtitle ?? 'Departamento Especializado' }}</p>
                            </div>
                            
                            <div class="department-content">
                                <p class="department-description">
                                    {{ $department->description }}
                                </p>
                                
                                <div class="department-stats">
                                    <div class="stat-item">
                                        <div class="stat-number">{{ rand(50, 200) }}</div>
                                        <div class="stat-label">Produtos</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">{{ rand(10, 50) }}</div>
                                        <div class="stat-label">Marcas</div>
                                    </div>
                                </div>
                                
                                <div class="department-actions">
                                    <a href="{{ route('department.index', $department->slug) }}" class="department-btn">
                                        <i class="fas fa-eye"></i> Ver Produtos
                                    </a>
                                    <a href="{{ route('department.index', $department->slug) }}" class="department-btn-secondary">
                                        <i class="fas fa-info"></i> Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-12 text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum departamento disponível</h4>
                    <p class="text-muted">Os departamentos serão exibidos aqui quando estiverem disponíveis.</p>
                </div>
            </div>
        @endif
        </div>
    </div>
</section>

<!-- Seção de Recursos -->
<section class="premium-section" style="background: white;">
    <div class="container">
        <div class="premium-section-content">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title">Por que escolher nossos departamentos?</h2>
                    <p class="section-subtitle">
                        Organizamos nossos produtos em departamentos especializados para facilitar sua busca.
                    </p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <div style="width: 60px; height: 60px; background: var(--header-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="fas fa-search fa-lg text-white"></i>
                                </div>
                                <h5 style="color: var(--text-dark); font-weight: 600; margin-bottom: 0.5rem;">Navegação Fácil</h5>
                                <p style="color: var(--text-gray);">Encontre produtos rapidamente organizados por categoria</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <div style="width: 60px; height: 60px; background: var(--header-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="fas fa-star fa-lg text-white"></i>
                                </div>
                                <h5 style="color: var(--text-dark); font-weight: 600; margin-bottom: 0.5rem;">Qualidade Garantida</h5>
                                <p style="color: var(--text-gray);">Produtos cuidadosamente selecionados para máxima qualidade</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="text-center">
                                <div style="width: 60px; height: 60px; background: var(--header-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="fas fa-headset fa-lg text-white"></i>
                                </div>
                                <h5 style="color: var(--text-dark); font-weight: 600; margin-bottom: 0.5rem;">Suporte Técnico</h5>
                                <p style="color: var(--text-gray);">Equipe especializada em cada área de produtos</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection