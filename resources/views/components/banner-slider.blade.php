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
        // Primeiro tenta buscar banners globais
        $banners = BannerHelper::getGlobalBanners($position, $limit);
        
        // Se não há banners globais e estamos na home, busca todos os banners ativos da posição
        if ($banners->count() == 0 && $position === 'hero') {
            $banners = \App\Models\Banner::active()
                ->byPosition($position)
                ->orderBy('sort_order')
                ->limit($limit ?? 5)
                ->get();
        }
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
              data-slide="{{ $index }}"
              data-banner-id="{{ $banner->id }}"
              data-banner-title="{{ $banner->title }}"
              @if(!empty($banner->link)) data-slide-link="{{ $banner->link }}" @endif>
                
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
                                {{-- CTA buttons (respect flags). Primary uses banner->link if present. --}}
                                @php
                                    $showPrimaryDesktop = isset($banner->show_primary_button_desktop) ? (bool) $banner->show_primary_button_desktop : true;
                                    $showPrimaryMobile = isset($banner->show_primary_button_mobile) ? (bool) $banner->show_primary_button_mobile : true;
                                    $showSecondaryDesktop = isset($banner->show_secondary_button_desktop) ? (bool) $banner->show_secondary_button_desktop : false;
                                    $showSecondaryMobile = isset($banner->show_secondary_button_mobile) ? (bool) $banner->show_secondary_button_mobile : false;
                                    $hasAnyCta = $showPrimaryDesktop || $showPrimaryMobile || $showSecondaryDesktop || $showSecondaryMobile;
                                    $hasLink = !empty($banner->link);
                                    $ctaHref = $hasLink ? $banner->link : '#';
                                @endphp
                                @if($hasAnyCta)
                                    @php
                                        $ctaSize = $banner->cta_size ?? 'medium';
                                        $ctaSizeClass = $ctaSize === 'small' ? 'btn-sm' : ($ctaSize === 'large' ? 'btn-lg' : '');
                                    @endphp
                                    <div class="container banner-ctas-outer">
                                        <div class="banner-ctas cta-pos-{{ $banner->cta_position ?? 'bottom' }} cta-align-{{ $banner->cta_align ?? 'center' }} cta-size-{{ $banner->cta_size ?? 'medium' }} cta-layout-{{ $banner->cta_layout ?? 'horizontal' }}">
                                            <div class="banner-ctas-inner">
                                                <div class="cta-wrapper">
                                        @if($showPrimaryDesktop)
                                                    @if($hasLink)
                                                        <a href="{{ $ctaHref }}" class="btn btn-primary banner-cta-desktop btn-pill {{ $ctaSizeClass }}">
                                                            <i class="bi bi-lock-fill me-2"></i>
                                                            Ver detalhes
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-primary banner-cta-desktop btn-pill {{ $ctaSizeClass }}">
                                                            <i class="bi bi-lock-fill me-2"></i>
                                                            Ver detalhes
                                                        </button>
                                                    @endif
                                                @endif
                                        @if($showSecondaryDesktop)
                                            @if($hasLink)
                                                <a href="{{ $ctaHref }}" class="btn banner-cta-desktop btn-outline-cta btn-pill {{ $ctaSizeClass }}">
                                                    <i class="bi bi-telephone-fill me-2"></i>
                                                    Saiba mais
                                                </a>
                                            @else
                                                <button type="button" class="btn banner-cta-desktop btn-outline-cta btn-pill {{ $ctaSizeClass }}">
                                                    <i class="bi bi-telephone-fill me-2"></i>
                                                    Saiba mais
                                                </button>
                                            @endif
                                        @endif

                                        @if($showPrimaryMobile)
                                                @if($hasLink)
                                                    <a href="{{ $ctaHref }}" class="btn btn-primary banner-cta-mobile btn-pill {{ $ctaSizeClass }}">
                                                    <i class="bi bi-lock-fill me-2"></i>
                                                    Ver
                                                </a>
                                            @else
                                                    <button type="button" class="btn btn-primary banner-cta-mobile btn-pill {{ $ctaSizeClass }}">
                                                    <i class="bi bi-lock-fill me-2"></i>
                                                    Ver
                                                </button>
                                            @endif
                                        @endif
                                        @if($showSecondaryMobile)
                                            @if($hasLink)
                                                <a href="{{ $ctaHref }}" class="btn banner-cta-mobile btn-outline-cta btn-pill {{ $ctaSizeClass }}">
                                                    <i class="bi bi-telephone-fill me-2"></i>
                                                    Mais
                                                </a>
                                            @else
                                                <button type="button" class="btn banner-cta-mobile btn-outline-cta btn-pill {{ $ctaSizeClass }}">
                                                    <i class="bi bi-telephone-fill me-2"></i>
                                                    Mais
                                                </button>
                                            @endif
                                        @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                
                    @php
                        // Flags de exibição vindas do model (podem ser nulas em dados antigos — assumir true por compatibilidade)
                        $showTitleFlag = isset($banner->show_title) ? (bool) $banner->show_title : true;
                        $showDescriptionFlag = isset($banner->show_description) ? (bool) $banner->show_description : true;
                        $showOverlayFlag = isset($banner->show_overlay) ? (bool) $banner->show_overlay : true;

                        // Conteúdo efetivamente disponível
                        $hasTitle = $showTitleFlag && !empty($banner->title);
                        $hasDescription = $showDescriptionFlag && !empty($banner->description);

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

                    @if($hasTitle || $hasDescription)
                        @if($showOverlayFlag)
                            <div class="banner-overlay" style="{{ $verticalPosition }}">
                                <div class="banner-content" style="text-align: {{ $titleAlign }};">
                                    @if($hasTitle)
                                        <h2 class="banner-title" 
                                            style="color: {{ $titleColor }} !important; 
                                                   font-size: {{ $titleSize }} !important; 
                                                   font-weight: {{ $titleWeight }} !important; 
                                                   text-align: {{ $titleAlign }} !important;">
                                            {{ $banner->title }}
                                        </h2>
                                    @endif

                                    @if($hasDescription)
                                        <p class="banner-description" 
                                           style="color: {{ $descColor }} !important; 
                                                  font-size: {{ $descSize }} !important; 
                                                  text-align: {{ $descAlign }} !important;">
                                            {{ $banner->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Show title/description without overlay background when overlay is disabled --}}
                            <div class="banner-content banner-content-no-overlay" style="position: absolute; left: 0; right: 0; {{ $verticalPosition }}; z-index: 5; text-align: {{ $titleAlign }};">
                                @if($hasTitle)
                                    <h2 class="banner-title" 
                                        style="color: {{ $titleColor }} !important; 
                                               font-size: {{ $titleSize }} !important; 
                                               font-weight: {{ $titleWeight }} !important; 
                                               text-align: {{ $titleAlign }} !important;">
                                        {{ $banner->title }}
                                    </h2>
                                @endif

                                @if($hasDescription)
                                    <p class="banner-description" 
                                       style="color: {{ $descColor }} !important; 
                                              font-size: {{ $descSize }} !important; 
                                              text-align: {{ $descAlign }} !important;">
                                        {{ $banner->description }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
                
                {{-- slide-level link handled by JS via data-slide-link to avoid nested anchors --}}

                @auth('admin')
                    <button type="button" class="admin-edit-banner-btn edit-banner-btn" 
                            title="Editar banner"
                            data-banner-id="{{ $banner->id }}"
                            data-banner-title="{{ $banner->title }}"
                            style="position: absolute; top: 10px; right: 10px; z-index: 9999; background: rgba(255,255,255,0.9); border-radius: 6px; border: 1px solid rgba(0,0,0,0.06); padding: .45rem .5rem;">
                        <i class="bi bi-pencil"></i>
                    </button>
                @endauth
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
.banner-slider { --site-container-max-width: 1440px; --site-container-padding: 1rem; --banner-cta-offset: calc((100% - var(--site-container-max-width)) / 2 + var(--site-container-padding)); --banner-cta-vertical-min: 1.25rem; }
.banner-slider {
    position: relative;
    width: 100%;
    overflow: hidden;
    /* Ensure the slider itself has no contrasting background that can peek
       through rounded corners on some devices. */
    background-color: transparent !important;
    border-radius: 0 !important;
    /* Remove subtle framing/outline for a flat banner appearance */
    box-shadow: none;
    border: none;
    /* slightly reduced global bottom spacing; hero-specific override below */
    margin-bottom: 1.5rem;
}

/* When used inside a hero-section we don't want extra bottom spacing */
.hero-section > .banner-slider { margin-bottom: 0 !important; }

.banner-slides-wrapper {
    position: relative;
    width: 100%;
    /* Use viewport relative height to match hero areas; allow smaller min-height for small screens */
    height: 50vh;
    min-height: 300px;
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
     /* Clip child images to the same rounded corners as the slider to avoid
         a thin visible seam caused by subpixel rendering at the edges. */
    overflow: hidden;
    border-radius: 0 !important;
}

.banner-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border: none;
    /* Remove rounding so banners render with square corners */
    border-radius: 0 !important;
    background-clip: padding-box;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    transform: translateZ(0) scale(1.002);
}

/* Make the container that wraps CTAs the positioned ancestor so CTAs align with page content */
.banner-image-container > .banner-ctas-outer { position: absolute; inset: 0; }

/* Keep the CTA outer as a block-level element but avoid forcing full-height
   which caused layout regressions in some contexts. Let the positioned ancestor
   define the visual height naturally. */
.banner-ctas-outer { display: block; }

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
        height: 40vh;
        min-height: 300px;
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

/* Mobile: remove inner container lateral padding so CTAs can use full width
   (fixes the 14px left/right padding the inspector shows). Keep scoped to
   mobile so desktop spacing remains controlled by the site container variable.) */
@media (max-width: 768px) {
    .banner-ctas .banner-ctas-inner { padding-left: 0 !important; padding-right: 0 !important; }
}

@media (max-width: 480px) {
    .banner-slides-wrapper {
        height: 35vh;
        min-height: 220px;
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


.banner-ctas { display:block; position:absolute; left:0; right:0; z-index:6; }
.banner-ctas-inner { display:grid; grid-template-columns: 1fr auto 1fr; align-items:center; width:100%; max-width:var(--site-container-max-width); margin:0 auto; padding:0 var(--site-container-padding); box-sizing:border-box; }

@media (min-width: 769px) {
    /* Remove extra lateral padding that prevents CTAs from reaching the page gutter.
       We keep this scoped only to larger screens so mobile spacing remains unchanged. */
    .banner-ctas.cta-align-left .banner-ctas-inner { padding-left: 0 !important; }
    .banner-ctas.cta-align-right .banner-ctas-inner { padding-right: 0 !important; }
}
/* Align CTA wrapper to the container inner edge by calculating the page side gutter
    ( (100% - max-width)/2 + container padding ). This aligns CTAs with the logo/cards.
    Uses CSS variables so the site container can override values for global harmony. */
.banner-ctas.cta-align-left .banner-ctas-inner .cta-wrapper { margin-left: max(calc((100% - var(--site-container-max-width)) / 2 + var(--site-container-padding)), 0px); }
.banner-ctas.cta-align-right .banner-ctas-inner .cta-wrapper { margin-right: max(calc((100% - var(--site-container-max-width)) / 2 + var(--site-container-padding)), 0px); }
.banner-ctas-inner .cta-wrapper { grid-column: 2; display:flex; gap:.6rem; align-items:center; max-width: var(--site-container-max-width); box-sizing: border-box; }
.banner-ctas.cta-align-left .banner-ctas-inner .cta-wrapper { grid-column: 1; justify-self: start; }
.banner-ctas.cta-align-center .banner-ctas-inner .cta-wrapper { grid-column: 2; justify-self: center; }
.banner-ctas.cta-align-right .banner-ctas-inner .cta-wrapper { grid-column: 3; justify-self: end; }

/* Size variants applied via wrapper classes to avoid Bootstrap override issues */
.banner-ctas.cta-size-small .btn { padding: .35rem .7rem; font-size: .82rem; }
.banner-ctas.cta-size-medium .btn { padding: .55rem 1rem; font-size: .95rem; }
.banner-ctas.cta-size-large .btn { padding: .8rem 1.4rem; font-size: 1.02rem; }

/* Extra size variants requested by admin UI: progressively larger paddings/font-size */
.banner-ctas.cta-size-xlarge .btn { padding: 1.1rem 1.8rem; font-size: 1.12rem; }
.banner-ctas.cta-size-xxlarge .btn { padding: 1.4rem 2.0rem; font-size: 1.25rem; }
.banner-ctas.cta-size-xxxlarge .btn { padding: 1.8rem 2.4rem; font-size: 1.45rem; }

.banner-ctas .btn, .banner-ctas-inner .btn { /* fallback */ padding: .55rem 1rem; font-size: .95rem; }

/* vertical position: ensure a sensible minimum distance from top/bottom so
    CTAs don't sit flush against the banner edges. We use a small CSS variable
    to allow easy tuning. */
.banner-ctas.cta-pos-top { top: max(1.5rem, var(--banner-cta-vertical-min, 1.25rem)); bottom: auto; }
.banner-ctas.cta-pos-center { top: 50%; bottom: auto; transform: translateY(-50%); }
.banner-ctas.cta-pos-bottom { bottom: max(calc(1.5rem + 28px), var(--banner-cta-vertical-min, 1.25rem)); top: auto; }

/* lateral alignment: use container-like centering and justify-content to align inside max-width */
.banner-ctas.cta-align-left { justify-content: flex-start; }
.banner-ctas.cta-align-center { justify-content: center; }
.banner-ctas.cta-align-right { justify-content: flex-end; }

/* vertical layout: make the CTA wrapper a vertical flex column so buttons stack */
.banner-ctas.cta-layout-vertical .banner-ctas-inner .cta-wrapper {
    display: flex;
    flex-direction: column;
    gap: .5rem;
    align-items: flex-start; /* default; overridden below for right/center */
}
.banner-ctas.cta-layout-vertical .banner-ctas-inner .cta-wrapper .btn {
    display: inline-flex;
    width: auto;
}
.banner-ctas.cta-layout-vertical.cta-align-left .banner-ctas-inner .cta-wrapper {
    align-items: flex-start;
    grid-column: 1;
    justify-self: start;
}
.banner-ctas.cta-layout-vertical.cta-align-center .banner-ctas-inner .cta-wrapper {
    align-items: center;
    grid-column: 2;
    justify-self: center;
}
.banner-ctas.cta-layout-vertical.cta-align-right .banner-ctas-inner .cta-wrapper {
    align-items: flex-end;
    grid-column: 3;
    justify-self: end;
}
.banner-ctas.cta-layout-vertical.cta-pos-bottom { bottom: calc(1.5rem + 28px); }
.banner-ctas .btn { padding: .55rem 1rem; font-size: .95rem; }
.banner-ctas .banner-cta-mobile { display: none !important; }
.banner-cta-desktop { display: inline-block; }
 .btn-pill { border-radius: 999px; padding: .6rem 1.2rem; font-weight: 600; }
 .btn-outline-cta { background: transparent; border: 2px solid var(--secondary-color); color: var(--secondary-color); }
 .btn-outline-cta:hover { background: color-mix(in srgb, var(--secondary-color), white 12%); color: var(--text-dark); }

/* Make mobile outlined CTAs filled for better visibility on small screens */
.banner-cta-mobile.btn-outline-cta {
    background: var(--secondary-color) !important;
    color: #fff !important;
    border-color: transparent !important;
    box-shadow: none !important;
}
.banner-cta-mobile.btn-outline-cta i { color: rgba(255,255,255,0.95) !important; }
/* Make desktop outlined CTAs filled as well (match mobile) */
.banner-cta-desktop.btn-outline-cta {
    background: var(--secondary-color) !important;
    color: #fff !important;
    border-color: transparent !important;
    box-shadow: none !important;
}
.banner-cta-desktop.btn-outline-cta i { color: rgba(255,255,255,0.95) !important; }
/* On hover revert to outlined appearance (transparent background + colored border) */
.banner-cta-mobile.btn-outline-cta:hover,
.banner-cta-desktop.btn-outline-cta:hover {
    background: transparent !important;
    color: var(--secondary-color) !important;
    border-color: var(--secondary-color) !important;
    box-shadow: none !important;
}
.banner-cta-mobile.btn-outline-cta:hover i,
.banner-cta-desktop.btn-outline-cta:hover i {
    color: var(--secondary-color) !important;
}
/* ensure banner link doesn't visually block CTAs */
.banner-link { background: transparent; }
@media (max-width: 768px) {
    .banner-ctas .banner-cta-desktop { display: none !important; }
    .banner-ctas .banner-cta-mobile { display: inline-block !important; }
    /* On mobile prefer center; but keep small lateral offsets if explicitly chosen */
    .banner-ctas { justify-content: center; left: 0; right: 0; }
    .banner-ctas.cta-align-left { left: .75rem; right: auto; }
    .banner-ctas.cta-align-right { right: .75rem; left: auto; }
}

/* Fix / Debug: ensure right-aligned CTAs take the right grid column and margin
   (higher specificity in case other CSS overrides are present). Remove or
   simplify later if this proves sufficient in production. */
.banner-ctas.cta-align-left .banner-ctas-inner .cta-wrapper {
    grid-column: 1 !important;
    justify-self: start !important;
    margin-left: max(calc((100% - var(--site-container-max-width)) / 2 + var(--site-container-padding)), 0px) !important;
}
.banner-ctas.cta-align-right .banner-ctas-inner .cta-wrapper {
    grid-column: 3 !important;
    justify-self: end !important;
    margin-right: max(calc((100% - var(--site-container-max-width)) / 2 + var(--site-container-padding)), 0px) !important;
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

    // Click-to-navigate for slides (avoid nested anchors). If a slide has data-slide-link,
    // navigate when the user clicks the slide but NOT when clicking CTAs or admin buttons.
    slider.addEventListener('click', function(e) {
        const target = e.target;
        const slide = target.closest('.banner-slide');
        if (!slide) return;
        const link = slide.dataset.slideLink;
        if (!link) return;

        // If click originated inside a CTA, an anchor, or the admin edit button, do nothing.
        if (target.closest('.banner-ctas') || target.closest('a') || target.closest('button') || target.closest('.admin-edit-banner-btn')) {
            return;
        }

        // otherwise navigate same window
        window.location.href = link;
    });
});
</script>
@endif

<script>
    // Align banner CTAs with the header logo precisely.
    (function(){
        function alignCtasWithLogo(){
            var sliders = document.querySelectorAll('.banner-slider');
            if(!sliders || sliders.length === 0) return;

            sliders.forEach(function(slider){
                var sliderRect = slider.getBoundingClientRect();
                var banner = slider.querySelector('.banner-image-container');
                if(!banner) return;

                var ctas = slider.querySelectorAll('.banner-ctas');
                ctas.forEach(function(ctasEl){
                    var inner = ctasEl.querySelector('.banner-ctas-inner');
                    var wrapper = ctasEl.querySelector('.cta-wrapper');
                    if(!inner || !wrapper) return;

                    // Reset any inline margin we set previously
                    wrapper.style.marginLeft = '';
                    wrapper.style.marginRight = '';

                    // Read CSS variables used for container alignment and optional adjust
                    var computed = getComputedStyle(slider);
                    var maxW = parseFloat(computed.getPropertyValue('--site-container-max-width')) || 1140;
                    var pad = parseFloat(computed.getPropertyValue('--site-container-padding')) || 16;
                    var adjust = parseFloat(computed.getPropertyValue('--banner-cta-adjust')) || 8; // pixels to shift inward

                    // Compute pixel offset equivalent to: calc((100% - var(--site-container-max-width)) / 2 + var(--site-container-padding))
                    var gutter = Math.max(0, (sliderRect.width - maxW) / 2 + pad);

                    // account for any inner padding the grid may have (e.g. .banner-ctas-inner { padding-left: 56px })
                    var innerStyle = getComputedStyle(inner);
                    var innerPadLeft = parseFloat(innerStyle.paddingLeft) || 0;
                    var innerPadRight = parseFloat(innerStyle.paddingRight) || 0;

                    // Ensure CTA wrapper does not overflow the slider (leave 8px padding)
                    var maxAllowed = Math.max(0, sliderRect.width - wrapper.offsetWidth - 8);

                    // For left-aligned CTAs: align to the page/container gutter (minus optional adjust and inner padding)
                    if(ctasEl.classList.contains('cta-align-left')){
                        var desiredLeft = Math.min(maxAllowed, Math.round(Math.max(0, gutter - adjust - innerPadLeft)));
                        if(desiredLeft > 0) wrapper.style.marginLeft = desiredLeft + 'px';
                    }

                    // For right-aligned CTAs: mirror the same gutter on the right (minus optional adjust and inner padding)
                    if(ctasEl.classList.contains('cta-align-right')){
                        var desiredRight = Math.min(maxAllowed, Math.round(Math.max(0, gutter - adjust - innerPadRight)));
                        if(desiredRight > 0) wrapper.style.marginRight = desiredRight + 'px';
                    }
                });
            });
        }

        // Run on DOM ready and on resize (debounced)
        document.addEventListener('DOMContentLoaded', alignCtasWithLogo);
        var resizeTimer;
        window.addEventListener('resize', function(){ clearTimeout(resizeTimer); resizeTimer = setTimeout(alignCtasWithLogo, 120); });
        // Also align after images/fonts load
        window.addEventListener('load', function(){ setTimeout(alignCtasWithLogo, 50); });
    })();
</script>

<script>
    // Debug helper: log any banners that have 'cta-align-right' and show computed values
    (function(){
        function debugCtas(){
            var banners = document.querySelectorAll('.banner-slider');
            banners.forEach(function(slider){
                var ctas = slider.querySelectorAll('.banner-ctas.cta-align-right');
                ctas.forEach(function(ctasEl, idx){
                    var wrapper = ctasEl.querySelector('.cta-wrapper');
                    if(!wrapper) return;
                    var cs = window.getComputedStyle(wrapper);
                    console.info('[banner-debug] cta-align-right found', {slider: slider, index: idx, gridColumn: cs.gridColumn, justifySelf: cs.justifySelf, marginRight: cs.marginRight, marginLeft: cs.marginLeft});
                });
            });
        }

        document.addEventListener('DOMContentLoaded', debugCtas);
        window.addEventListener('load', function(){ setTimeout(debugCtas, 60); });
        var t;
        window.addEventListener('resize', function(){ clearTimeout(t); t = setTimeout(debugCtas, 150); });
    })();
</script>
