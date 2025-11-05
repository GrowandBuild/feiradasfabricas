@extends('layouts.app')

@section('title', 'Exemplos de Uso de Banners')

@section('content')
<div class="container my-5">
    <h1>Exemplos de Uso de Banners por Departamento</h1>
    
    <!-- Banner Slider para Home (Global) -->
    <section class="my-5">
        <h2>Banner Slider Global (Home)</h2>
        <x-banner-slider 
            :departmentId="null" 
            position="hero" 
            :limit="3" 
            :showDots="true" 
            :showArrows="true" 
            :autoplay="true" 
            :interval="5000" />
    </section>

    <!-- Banner Slider para Departamento de Eletrônicos -->
    <section class="my-5">
        <h2>Banner Slider - Departamento de Eletrônicos</h2>
        @php
            $eletronicos = \App\Models\Department::where('slug', 'eletronicos')->first();
        @endphp
        @if($eletronicos)
            <x-banner-slider 
                :departmentId="$eletronicos->id" 
                position="hero" 
                :limit="4" 
                :showDots="true" 
                :showArrows="true" 
                :autoplay="true" 
                :interval="4000" />
        @endif
    </section>

    <!-- Banners Estáticos Globais -->
    <section class="my-5">
        <h2>Banners Estáticos Globais</h2>
        <x-banner-static 
            :departmentId="null" 
            position="category" 
            :limit="3" 
            class="my-4" />
    </section>

    <!-- Banners Estáticos para Departamento de Casa -->
    <section class="my-5">
        <h2>Banners Estáticos - Departamento de Casa</h2>
        @php
            $casa = \App\Models\Department::where('slug', 'casa')->first();
        @endphp
        @if($casa)
            <x-banner-static 
                :departmentId="$casa->id" 
                position="category" 
                :limit="2" 
                class="my-4" />
        @endif
    </section>

    <!-- Exemplo de uso direto com BannerHelper -->
    <section class="my-5">
        <h2>Uso Direto com BannerHelper</h2>
        @php
            use App\Helpers\BannerHelper;
            
            // Banners para departamento específico
            $departmentId = $eletronicos ? $eletronicos->id : null;
            $banners = BannerHelper::getBannersForDisplay($departmentId, 'product', 4);
        @endphp
        
        @if($banners->count() > 0)
            <div class="row g-3">
                @foreach($banners as $banner)
                    <div class="col-md-3">
                        <div class="card">
                            <img src="{{ BannerHelper::getBannerImageUrl($banner) }}" 
                                 class="card-img-top" 
                                 alt="{{ $banner->title }}"
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">{{ $banner->title }}</h5>
                                @if($banner->description)
                                    <p class="card-text">{{ $banner->description }}</p>
                                @endif
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" class="btn btn-primary">Ver Mais</a>
                                @endif
                                <div class="mt-2">
                                    <small class="text-muted">
                                        @if($banner->department)
                                            <span class="badge bg-primary">{{ $banner->department->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Global</span>
                                        @endif
                                        <span class="badge bg-info ms-1">{{ ucfirst($banner->position) }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Nenhum banner encontrado para esta posição.
            </div>
        @endif
    </section>

    <!-- Estatísticas de Banners -->
    <section class="my-5">
        <h2>Estatísticas de Banners</h2>
        @php
            $stats = BannerHelper::getBannerStats();
        @endphp
        
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">{{ $stats['total'] }}</h5>
                        <p class="card-text">Total de Banners</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">{{ $stats['active'] }}</h5>
                        <p class="card-text">Banners Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">{{ $stats['global'] }}</h5>
                        <p class="card-text">Banners Globais</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">{{ $stats['by_department'] }}</h5>
                        <p class="card-text">Por Departamento</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banners por posição -->
        <div class="mt-4">
            <h4>Banners por Posição</h4>
            <div class="row g-2">
                @foreach($stats['by_position'] as $position => $count)
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body p-2">
                                <h6 class="card-title">{{ ucfirst($position) }}</h6>
                                <p class="card-text mb-0">{{ $count }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endsection
