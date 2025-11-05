@props([
    'departmentId' => null,
    'position' => 'hero',
    'limit' => 5,
    'showDots' => true,
    'showArrows' => true,
    'autoplay' => true,
    'interval' => 5000
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
<div class="banner-slider" 
     data-autoplay="{{ $autoplay ? 'true' : 'false' }}" 
     data-interval="{{ $interval }}"
     data-show-dots="{{ $showDots ? 'true' : 'false' }}"
     data-show-arrows="{{ $showArrows ? 'true' : 'false' }}">
    
    <div class="banner-slides-wrapper">
        @foreach($banners as $index => $banner)
            @php
                $desktopImageUrl = BannerHelper::getBannerImageUrl($banner);
                $mobileImageUrl = BannerHelper::getBannerImageUrl($banner, true);
                $hasImage = ($desktopImageUrl && $banner->image) || ($mobileImageUrl && $banner->mobile_image);
            @endphp
            
            <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" 
                 data-slide="{{ $index }}">
            
                @if($banner->link)
                    <a href="{{ $banner->link }}" class="banner-link">
                @endif
                
                <div class="banner-image-container" 
                     style="@if(!$desktopImageUrl && !$mobileImageUrl) background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); @endif">
                    @if($desktopImageUrl && $banner->image)
                        <img src="{{ $desktopImageUrl }}" 
                             alt="{{ $banner->title ?? 'Banner' }}"
                             class="banner-image desktop-image"
                             loading="lazy"
                             onerror="this.style.display='none'; this.parentElement.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)';">
                    @endif
                    
                    @if($mobileImageUrl && $banner->mobile_image)
                        <img src="{{ $mobileImageUrl }}" 
                             alt="{{ $banner->title ?? 'Banner' }}"
                             class="banner-image mobile-image"
                             loading="lazy"
                             onerror="this.style.display='none';">
                    @endif
                
                    @if(!empty($banner->title) || !empty($banner->description))
                        @php
                            // Estilos do Título
                            $titleColor = $banner->text_color ?? '#ffffff';
                            $titleSize = $banner->text_size ?? '2.5rem';
                            $titleAlign = $banner->text_align ?? 'center';
                            $titleVerticalAlign = $banner->text_vertical_align ?? 'bottom';
                            $titleWeight = $banner->text_font_weight ?? '700';
                            
                            // Estilos da Descrição
                            $descColor = $banner->description_color ?? '#ffffff';
                            $descSize = $banner->description_size ?? '1.2rem';
                            $descAlign = $banner->description_align ?? 'center';
                            $descVerticalAlign = $banner->description_vertical_align ?? 'bottom';
                            
                            // Determinar posição vertical do overlay
                            $verticalPosition = match($titleVerticalAlign) {
                                'top' => 'top: 0; bottom: auto; transform: none;',
                                'center' => 'top: 50%; bottom: auto; transform: translateY(-50%);',
                                'bottom' => 'bottom: 0; top: auto; transform: none;',
                                default => 'bottom: 0; top: auto; transform: none;'
                            };
                        @endphp
                        <div class="banner-overlay" style="{{ $verticalPosition }}">
                            <div class="banner-content" style="text-align: {{ $titleAlign }};">
                                @if(!empty($banner->title))
                                    <h2 class="banner-title" 
                                        style="color: {{ $titleColor }} !important; 
                                               font-size: {{ $titleSize }} !important; 
                                               font-weight: {{ $titleWeight }} !important; 
                                               text-align: {{ $titleAlign }} !important;">
                                        {{ $banner->title }}
                                    </h2>
                                @endif
                                
                                @if(!empty($banner->description))
                                    <p class="banner-description" 
                                       style="color: {{ $descColor }} !important; 
                                              font-size: {{ $descSize }} !important; 
                                              text-align: {{ $descAlign }} !important;">
                                        {{ $banner->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                @if($banner->link)
                    </a>
                @endif
            </div>
        @endforeach
    </div>
    
    @if($showArrows && $banners->count() > 1)
        <button class="banner-arrow banner-arrow-prev" onclick="changeSlide(-1)" aria-label="Slide anterior">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="banner-arrow banner-arrow-next" onclick="changeSlide(1)" aria-label="Proximo slide">
            <i class="fas fa-chevron-right"></i>
        </button>
    @endif
    
    @if($showDots && $banners->count() > 1)
        <div class="banner-indicators">
            @foreach($banners as $idx => $b)
                <button class="banner-indicator {{ $idx === 0 ? 'active' : '' }}" 
                        onclick="goToSlide({{ $idx }})"
                        data-slide="{{ $idx }}"
                        aria-label="Ir para slide {{ $idx + 1 }}">
                </button>
            @endforeach
        </div>
    @endif
</div>

<style>
.banner-slider {
    position: relative;
    width: 100%;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.banner-slides-wrapper {
    position: relative;
    width: 100%;
    height: 500px;
    min-height: 500px;
}

.banner-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.6s ease-in-out;
    z-index: 1;
}

.banner-slide.active {
    opacity: 1;
    z-index: 2;
}

.banner-image-container {
    position: relative;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.banner-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.mobile-image {
    display: none;
}

.banner-link {
    display: block;
    width: 100%;
    height: 100%;
    text-decoration: none;
    color: inherit;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 3;
    cursor: pointer;
}

.banner-overlay {
    position: absolute;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
    padding: 3rem 2rem 2rem;
    z-index: 4;
    width: 100%;
}

.banner-overlay[style*="top: 0"] {
    background: linear-gradient(rgba(0, 0, 0, 0.7), transparent);
    padding: 2rem 2rem 3rem;
}

.banner-overlay[style*="top: 50%"] {
    background: rgba(0, 0, 0, 0.6);
    padding: 2rem;
}

.banner-content {
    position: relative;
    z-index: 5;
    max-width: 800px;
    margin: 0 auto;
}

.banner-title {
    margin: 0 0 1rem 0;
    padding: 0;
    line-height: 1.2;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
}

.banner-description {
    margin: 0;
    padding: 0;
    line-height: 1.6;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.8);
}

.banner-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(15, 23, 42, 0.8);
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.25rem;
    color: white;
    transition: all 0.3s ease;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.banner-arrow:hover {
    background: rgba(15, 23, 42, 1);
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

.banner-arrow-prev {
    left: 20px;
}

.banner-arrow-next {
    right: 20px;
}

.banner-indicators {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.banner-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    background: rgba(255, 255, 255, 0.3);
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}

.banner-indicator.active,
.banner-indicator:hover {
    background: white;
    transform: scale(1.3);
}

@media (max-width: 768px) {
    .banner-slides-wrapper {
        height: 400px;
        min-height: 400px;
    }
    
    .banner-overlay {
        padding: 2rem 1.5rem 1.5rem;
    }
    
    .banner-title {
        font-size: 1.8rem;
    }
    
    .banner-description {
        font-size: 1rem;
    }
    
    .banner-arrow {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .banner-arrow-prev {
        left: 10px;
    }
    
    .banner-arrow-next {
        right: 10px;
    }
    
    .desktop-image {
        display: none;
    }
    
    .mobile-image {
        display: block;
    }
}

@media (max-width: 480px) {
    .banner-slides-wrapper {
        height: 300px;
        min-height: 300px;
    }
    
    .banner-overlay {
        padding: 1.5rem 1rem 1rem;
    }
    
    .banner-title {
        font-size: 1.5rem;
    }
    
    .banner-description {
        font-size: 0.9rem;
    }
    
    .banner-arrow {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
    
    .banner-indicators {
        bottom: 15px;
    }
    
    .banner-indicator {
        width: 10px;
        height: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.banner-slider');
    if (!slider) return;
    
    const slides = slider.querySelectorAll('.banner-slide');
    const indicators = slider.querySelectorAll('.banner-indicator');
    const autoplay = slider.dataset.autoplay === 'true';
    const interval = parseInt(slider.dataset.interval) || 5000;
    let currentSlide = 0;
    let slideInterval;
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        if (slides[index]) {
            slides[index].classList.add('active');
        }
        if (indicators[index]) {
            indicators[index].classList.add('active');
        }
        
        currentSlide = index;
    }
    
    function nextSlide() {
        const totalSlides = slides.length;
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }
    
    function prevSlide() {
        const totalSlides = slides.length;
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }
    
    function goToSlide(index) {
        if (index >= 0 && index < slides.length) {
            showSlide(index);
        }
    }
    
    function startAutoplay() {
        if (autoplay && slides.length > 1) {
            slideInterval = setInterval(nextSlide, interval);
        }
    }
    
    function stopAutoplay() {
        clearInterval(slideInterval);
    }
    
    window.changeSlide = function(direction) {
        if (direction > 0) {
            nextSlide();
        } else {
            prevSlide();
        }
    };
    
    window.goToSlide = goToSlide;
    
    if (slides.length > 0) {
        showSlide(0);
        startAutoplay();
        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);
    }
});
</script>
@endif
