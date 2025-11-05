@props([
    'departmentId' => null,
    'position' => 'category',
    'limit' => 3,
    'class' => ''
])

@php
    use App\Helpers\BannerHelper;
    
    if ($departmentId) {
        $banners = BannerHelper::getBannersForDisplay($departmentId, $position, $limit);
    } else {
        $banners = BannerHelper::getGlobalBanners($position, $limit);
    }
@endphp

@if($banners->count() > 0)
<div class="banner-static {{ $class }}">
    <div class="row g-3">
        @foreach($banners as $banner)
            <div class="col-md-{{ 12 / $banners->count() }}">
                <div class="banner-item">
                    @if($banner->link)
                        <a href="{{ $banner->link }}" class="banner-link" target="_blank">
                    @endif
                    
                    <div class="banner-image-container">
                        <img src="{{ BannerHelper::getBannerImageUrl($banner) }}" 
                             alt="{{ $banner->title }}"
                             class="banner-image"
                             loading="lazy">
                        
                        @if($banner->title || $banner->description)
                            <div class="banner-overlay">
                                <div class="banner-content">
                                    @if($banner->title)
                                        <h3 class="banner-title">{{ $banner->title }}</h3>
                                    @endif
                                    @if($banner->description)
                                        <p class="banner-description">{{ $banner->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($banner->link)
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
.banner-static {
    margin: 2rem 0;
}

.banner-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.banner-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.banner-image-container {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.banner-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.banner-item:hover .banner-image {
    transform: scale(1.05);
}

.banner-link {
    display: block;
    width: 100%;
    height: 100%;
    text-decoration: none;
    color: inherit;
}

.banner-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
    padding: 1rem;
    color: white;
}

.banner-content {
    text-align: center;
}

.banner-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

.banner-description {
    font-size: 0.9rem;
    opacity: 0.9;
    margin: 0;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Responsivo */
@media (max-width: 768px) {
    .banner-static .row {
        flex-direction: column;
    }
    
    .banner-static .col-md-12,
    .banner-static .col-md-6,
    .banner-static .col-md-4 {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .banner-image-container {
        height: 150px;
    }
    
    .banner-title {
        font-size: 1rem;
    }
    
    .banner-description {
        font-size: 0.8rem;
    }
}
</style>
@endif
