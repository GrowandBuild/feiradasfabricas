@php
    /**
     * Partial: banner-universal
     * Props (passed via @include):
     *  - departmentId (int|null)
     *  - position (string) default 'hero'
     *  - limit (int|null) default null
     *  - style (string|null) 'slider'|'static' - if null, inferred from position
     *  - class (string|null)
     *  - showDots, showArrows, autoplay, interval (for slider)
     */
    use App\Helpers\BannerHelper;

    $position = $position ?? 'hero';
    $limit = $limit ?? null;
    $style = $style ?? null;
    $class = $class ?? '';
    $showDots = $showDots ?? true;
    $showArrows = $showArrows ?? true;
    $autoplay = $autoplay ?? true;
    $interval = $interval ?? 5000;

    $banners = BannerHelper::getBannersForDisplay($departmentId ?? null, $position, $limit);
    // infer style if not provided
    if (!$style) {
        $style = $position === 'hero' ? 'slider' : 'static';
    }
@endphp

@if($banners->count() > 0)
    @if($style === 'slider')
        @include('components.banner-slider', [
            'departmentId' => $departmentId ?? null,
            'position' => $position,
            'limit' => $limit ?? $banners->count(),
            'showDots' => $showDots,
            'showArrows' => $showArrows,
            'autoplay' => $autoplay,
            'interval' => $interval,
            'class' => $class,
        ])
    @else
        @include('components.banner-static', [
            'departmentId' => $departmentId ?? null,
            'position' => $position,
            'limit' => $limit ?? $banners->count(),
            'class' => $class,
        ])
    @endif
@endif
