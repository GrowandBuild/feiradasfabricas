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
                <div class="banner-item" data-banner-id="{{ $banner->id }}" data-banner-title="{{ $banner->title }}" @if(!empty($banner->link)) data-slide-link="{{ $banner->link }}" data-slide-link-target="_blank" @endif>
                    
                    <div class="banner-image-container">
                        <img src="{{ BannerHelper::getBannerImageUrl($banner) }}" 
                             alt="{{ $banner->title }}"
                             class="banner-image"
                             loading="lazy">
                        
                        @php
                            $showTitleFlag = isset($banner->show_title) ? (bool) $banner->show_title : true;
                            $showDescriptionFlag = isset($banner->show_description) ? (bool) $banner->show_description : true;
                            $showOverlayFlag = isset($banner->show_overlay) ? (bool) $banner->show_overlay : true;

                            $hasTitle = $showTitleFlag && !empty($banner->title);
                            $hasDescription = $showDescriptionFlag && !empty($banner->description);
                        @endphp

                            @php
                                $showPrimaryDesktop = isset($banner->show_primary_button_desktop) ? (bool) $banner->show_primary_button_desktop : true;
                                $showPrimaryMobile = isset($banner->show_primary_button_mobile) ? (bool) $banner->show_primary_button_mobile : true;
                                $showSecondaryDesktop = isset($banner->show_secondary_button_desktop) ? (bool) $banner->show_secondary_button_desktop : false;
                                $showSecondaryMobile = isset($banner->show_secondary_button_mobile) ? (bool) $banner->show_secondary_button_mobile : false;
                                $hasAnyCta = $showPrimaryDesktop || $showPrimaryMobile || $showSecondaryDesktop || $showSecondaryMobile;
                                $hasLink = !empty($banner->link);
                                $ctaHref = $hasLink ? $banner->link : '#';
                            @endphp

                            @if($hasTitle || $hasDescription)
                                @if($showOverlayFlag)
                                    <div class="banner-overlay">
                                        <div class="banner-content">
                                            @if($hasTitle)
                                                <h3 class="banner-title">{{ $banner->title }}</h3>
                                            @endif
                                            @if($hasDescription)
                                                <p class="banner-description">{{ $banner->description }}</p>
                                            @endif
                                            @if($hasAnyCta)
                                                @php
                                                    $ctaSize = $banner->cta_size ?? 'medium';
                                                    $ctaSizeClass = $ctaSize === 'small' ? 'btn-sm' : ($ctaSize === 'large' ? 'btn-lg' : '');
                                                @endphp
                                                <div class="banner-ctas cta-pos-{{ $banner->cta_position ?? 'bottom' }} cta-align-{{ $banner->cta_align ?? 'center' }} cta-size-{{ $banner->cta_size ?? 'medium' }} cta-layout-{{ $banner->cta_layout ?? 'horizontal' }}">
                                                    <div class="container banner-ctas-inner">
                                                        <div class="cta-wrapper">
                                                        @if($showPrimaryDesktop)
                                                        @if($hasLink)
                                                            <a href="{{ $ctaHref }}" class="btn btn-primary banner-cta-desktop btn-pill">
                                                                <i class="bi bi-lock-fill me-2"></i>
                                                                Ver detalhes
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn-primary banner-cta-desktop btn-pill">
                                                                <i class="bi bi-lock-fill me-2"></i>
                                                                Ver detalhes
                                                            </button>
                                                        @endif
                                                    @endif
                                                    @if($showSecondaryDesktop)
                                                        @if($hasLink)
                                                            <a href="{{ $ctaHref }}" class="btn banner-cta-desktop btn-outline-cta btn-pill">
                                                                <i class="bi bi-telephone-fill me-2"></i>
                                                                Saiba mais
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn banner-cta-desktop btn-outline-cta btn-pill">
                                                                <i class="bi bi-telephone-fill me-2"></i>
                                                                Saiba mais
                                                            </button>
                                                        @endif
                                                    @endif

                                                    @if($showPrimaryMobile)
                                                        @if($hasLink)
                                                            <a href="{{ $ctaHref }}" class="btn btn-primary banner-cta-mobile btn-pill">
                                                                <i class="bi bi-lock-fill me-2"></i>
                                                                Ver
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn btn-primary banner-cta-mobile btn-pill">
                                                                <i class="bi bi-lock-fill me-2"></i>
                                                                Ver
                                                            </button>
                                                        @endif
                                                    @endif
                                                    @if($showSecondaryMobile)
                                                        @if($hasLink)
                                                            <a href="{{ $ctaHref }}" class="btn banner-cta-mobile btn-outline-cta btn-pill">
                                                                <i class="bi bi-telephone-fill me-2"></i>
                                                                Mais
                                                            </a>
                                                        @else
                                                            <button type="button" class="btn banner-cta-mobile btn-outline-cta btn-pill">
                                                                <i class="bi bi-telephone-fill me-2"></i>
                                                                Mais
                                                            </button>
                                                        @endif
                                                    @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- show content without overlay background --}}
                                    <div class="banner-content banner-content-no-overlay" style="position:absolute; left:0; right:0; bottom:1rem; z-index:5; text-align:center;">
                                        @if($hasTitle)
                                            <h3 class="banner-title">{{ $banner->title }}</h3>
                                        @endif
                                        @if($hasDescription)
                                            <p class="banner-description">{{ $banner->description }}</p>
                                        @endif
                                        @if($hasAnyCta)
                                            <div class="banner-ctas cta-pos-{{ $banner->cta_position ?? 'bottom' }} cta-align-{{ $banner->cta_align ?? 'center' }}">
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
                                        @endif
                                    </div>
                                @endif
                            @endif
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
                    
                    {{-- slide-level link handled by delegated JS; keep inner CTA anchors as-is --}}
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

    /* CTA size variants (match banner-slider sizing) */
    .banner-ctas.cta-size-small .btn { padding: .35rem .7rem; font-size: .82rem; }
    .banner-ctas.cta-size-medium .btn { padding: .55rem 1rem; font-size: .95rem; }
    .banner-ctas.cta-size-large .btn { padding: .8rem 1.4rem; font-size: 1.02rem; }
    .banner-ctas.cta-size-xlarge .btn { padding: 1.1rem 1.8rem; font-size: 1.12rem; }
    .banner-ctas.cta-size-xxlarge .btn { padding: 1.4rem 2.0rem; font-size: 1.25rem; }
    .banner-ctas.cta-size-xxxlarge .btn { padding: 1.8rem 2.4rem; font-size: 1.45rem; }

.banner-ctas { display:block; position:absolute; left:0; right:0; z-index:6; }
.banner-ctas-inner { display:grid; grid-template-columns: 1fr auto 1fr; align-items:center; width:100%; max-width:var(--site-container-max-width, 1140px); margin:0 auto; padding:0 var(--site-container-padding, 1rem); box-sizing:border-box; }
.banner-ctas-inner .cta-wrapper { grid-column: 2; display:flex; gap:.5rem; align-items:center; max-width: var(--site-container-max-width, 1140px); box-sizing: border-box; }
.banner-ctas.cta-align-left .banner-ctas-inner .cta-wrapper { grid-column: 1; justify-self: start; margin-left: max(calc((100% - var(--site-container-max-width, 1140px)) / 2 + var(--site-container-padding, 1rem)), 0px); }
.banner-ctas.cta-align-right .banner-ctas-inner .cta-wrapper { margin-right: max(calc((100% - var(--site-container-max-width, 1140px)) / 2 + var(--site-container-padding, 1rem)), 0px); }
.banner-ctas.cta-align-center .banner-ctas-inner .cta-wrapper { grid-column: 2; justify-self: center; }
.banner-ctas.cta-align-right .banner-ctas-inner .cta-wrapper { grid-column: 3; justify-self: end; }

/* Size variants applied via wrapper classes to avoid Bootstrap override issues */
.banner-ctas.cta-size-small .btn { padding: .35rem .6rem; font-size: .82rem; }
.banner-ctas.cta-size-medium .btn { padding: .45rem .9rem; font-size: .9rem; }
.banner-ctas.cta-size-large .btn { padding: .7rem 1.2rem; font-size: 1rem; }

.banner-ctas .btn, .banner-ctas-inner .btn { /* fallback */ padding: .45rem .9rem; font-size: .9rem; }

/* vertical position */
.banner-ctas.cta-pos-top { top: 0.8rem; bottom: auto; }
.banner-ctas.cta-pos-center { top: 50%; bottom: auto; }
.banner-ctas.cta-pos-bottom { bottom: 1rem; top: auto; }

/* lateral alignment: align inside centered max-width container */
.banner-ctas.cta-align-left { justify-content: flex-start; }
.banner-ctas.cta-align-center { justify-content: center; }
.banner-ctas.cta-align-right { justify-content: flex-end; }

/* vertical layout stack for static banners */
.banner-ctas.cta-layout-vertical { flex-direction: column; gap: .4rem; }
.banner-ctas.cta-layout-vertical.cta-align-left { align-items: flex-start; justify-content: flex-start; }
.banner-ctas.cta-layout-vertical.cta-align-right { align-items: flex-end; justify-content: flex-end; }
.banner-ctas.cta-layout-vertical.cta-pos-bottom { bottom: 1rem; }
.banner-cta-mobile { display: none; }
.banner-link { background: transparent; }
@media (max-width: 768px) {
    .banner-cta-desktop { display: none; }
    .banner-cta-mobile { display: inline-block; }
    .banner-ctas { left: 0; right: 0; justify-content: center; }
    .banner-ctas.cta-align-left { left: .75rem; right: auto; }
    .banner-ctas.cta-align-right { right: .75rem; left: auto; }
}
</style>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.banner-static').forEach(function(wrapper) {
        wrapper.addEventListener('click', function(e) {
            const target = e.target;
            const item = target.closest('.banner-item');
            if (!item) return;
            const link = item.dataset.slideLink;
            if (!link) return;

            // ignore clicks on CTAs, anchors, buttons or admin edit
            if (target.closest('.banner-ctas') || target.closest('a') || target.closest('button') || target.closest('.admin-edit-banner-btn')) {
                return;
            }

            const targetAttr = item.dataset.slideLinkTarget || '_self';
            window.open(link, targetAttr);
        });
    });
});
</script>
