@php
    use App\Helpers\BannerHelper;
    $desktopImageUrl = BannerHelper::getBannerImageUrl($banner);
    $mobileImageUrl = BannerHelper::getBannerImageUrl($banner, true);
    $isHero = ($banner->position ?? 'hero') === 'hero';
    $showTitleFlag = isset($banner->show_title) ? (bool) $banner->show_title : true;
    $showDescriptionFlag = isset($banner->show_description) ? (bool) $banner->show_description : true;
    $showOverlayFlag = isset($banner->show_overlay) ? (bool) $banner->show_overlay : true;
@endphp

@if($isHero)
<div class="banner-slide" data-banner-id="{{ $banner->id }}" data-banner-title="{{ $banner->title }}" @if(!empty($banner->link)) data-slide-link="{{ $banner->link }}" @endif>
    <div class="banner-image-container">
        @if($desktopImageUrl && $banner->image)
            <img src="{{ $desktopImageUrl }}" alt="{{ $banner->title }}" class="banner-image desktop-image" loading="lazy">
        @endif
        @if($mobileImageUrl && $banner->mobile_image)
            <img src="{{ $mobileImageUrl }}" alt="{{ $banner->title }}" class="banner-image mobile-image" loading="lazy">
        @endif

        @php
            $hasTitle = $showTitleFlag && !empty($banner->title);
            $hasDescription = $showDescriptionFlag && !empty($banner->description);
        @endphp

        @if($hasTitle || $hasDescription)
            @if($showOverlayFlag)
                <div class="banner-overlay">
                    <div class="banner-content">
                        @if($hasTitle)
                            <h2 class="banner-title">{{ $banner->title }}</h2>
                        @endif
                        @if($hasDescription)
                            <p class="banner-description">{{ $banner->description }}</p>
                        @endif
                    </div>
                </div>
            @else
                {{-- When overlay is disabled, still show title/description positioned similarly but without background --}}
                <div class="banner-content banner-content-no-overlay" style="position: absolute; left: 0; right: 0; bottom: 1.5rem; z-index: 5; text-align: center;">
                    @if($hasTitle)
                        <h2 class="banner-title">{{ $banner->title }}</h2>
                    @endif
                    @if($hasDescription)
                        <p class="banner-description">{{ $banner->description }}</p>
                    @endif
                </div>
            @endif
        @endif

        @php
            // Flags de exibição dos botões (padrões preservados)
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

        @auth('admin')
            <button type="button" class="admin-edit-banner-btn edit-banner-btn" title="Editar banner" data-banner-id="{{ $banner->id }}" data-banner-title="{{ $banner->title }}" style="position: absolute; top: 10px; right: 10px; z-index: 9999; background: rgba(255,255,255,0.9); border-radius: 6px; border: 1px solid rgba(0,0,0,0.06); padding: .45rem .5rem;">
                <i class="bi bi-pencil"></i>
            </button>
        @endauth
    </div>
</div>
@else
<div class="banner-item" data-banner-id="{{ $banner->id }}" data-banner-title="{{ $banner->title }}" @if(!empty($banner->link)) data-slide-link="{{ $banner->link }}" data-slide-link-target="_blank" @endif>
    <div class="banner-image-container">
        @if($desktopImageUrl)
            <img src="{{ $desktopImageUrl }}" alt="{{ $banner->title }}" class="banner-image" loading="lazy">
        @endif

        @php
            $hasTitle = $showTitleFlag && !empty($banner->title);
            $hasDescription = $showDescriptionFlag && !empty($banner->description);
        @endphp

        @if($showOverlayFlag && ($hasTitle || $hasDescription))
            <div class="banner-overlay">
                <div class="banner-content">
                    @if($hasTitle)
                        <h3 class="banner-title">{{ $banner->title }}</h3>
                    @endif
                    @if($hasDescription)
                        <p class="banner-description">{{ $banner->description }}</p>
                    @endif
                </div>
            </div>
        @endif

        @php
            // Flags de exibição dos botões (padrões preservados)
            $showPrimaryDesktop = isset($banner->show_primary_button_desktop) ? (bool) $banner->show_primary_button_desktop : true;
            $showPrimaryMobile = isset($banner->show_primary_button_mobile) ? (bool) $banner->show_primary_button_mobile : true;
            $showSecondaryDesktop = isset($banner->show_secondary_button_desktop) ? (bool) $banner->show_secondary_button_desktop : false;
            $showSecondaryMobile = isset($banner->show_secondary_button_mobile) ? (bool) $banner->show_secondary_button_mobile : false;
            $hasAnyCta = $showPrimaryDesktop || $showPrimaryMobile || $showSecondaryDesktop || $showSecondaryMobile;
            $hasLink = !empty($banner->link);
            $ctaHref = $hasLink ? $banner->link : '#';
        @endphp

        @if($hasAnyCta)
            <div class="container banner-ctas-outer">
                <div class="banner-ctas cta-pos-{{ $banner->cta_position ?? 'bottom' }} cta-align-{{ $banner->cta_align ?? 'center' }}">
                    @if($showPrimaryDesktop)
                    <a href="{{ $ctaHref }}" class="btn btn-primary banner-cta-desktop btn-pill {{ $hasLink ? '' : 'disabled' }}" @if(!$hasLink) tabindex="-1" aria-disabled="true" @endif>
                        <i class="bi bi-lock-fill me-2"></i>
                        Ver detalhes
                    </a>
                @endif
                @if($showSecondaryDesktop)
                    <a href="{{ $ctaHref }}" class="btn banner-cta-desktop btn-outline-cta btn-pill {{ $hasLink ? '' : 'disabled' }}" @if(!$hasLink) tabindex="-1" aria-disabled="true" @endif>
                        <i class="bi bi-telephone-fill me-2"></i>
                        Saiba mais
                    </a>
                @endif
                @if($showPrimaryMobile)
                    <a href="{{ $ctaHref }}" class="btn btn-primary banner-cta-mobile btn-pill {{ $hasLink ? '' : 'disabled' }}" @if(!$hasLink) tabindex="-1" aria-disabled="true" @endif>
                        <i class="bi bi-lock-fill me-2"></i>
                        Ver
                    </a>
                @endif
                @if($showSecondaryMobile)
                    <a href="{{ $ctaHref }}" class="btn banner-cta-mobile btn-outline-cta btn-pill {{ $hasLink ? '' : 'disabled' }}" @if(!$hasLink) tabindex="-1" aria-disabled="true" @endif>
                        <i class="bi bi-telephone-fill me-2"></i>
                        Mais
                    </a>
                @endif
                </div>
            </div>
        @endif

        @auth('admin')
            <button type="button" class="admin-edit-banner-btn edit-banner-btn" title="Editar banner" data-banner-id="{{ $banner->id }}" data-banner-title="{{ $banner->title }}" style="position: absolute; top: 10px; right: 10px; z-index: 9999; background: rgba(255,255,255,0.9); border-radius: 6px; border: 1px solid rgba(0,0,0,0.06); padding: .45rem .5rem;">
                <i class="bi bi-pencil"></i>
            </button>
        @endauth
    </div>
</div>
@endif
