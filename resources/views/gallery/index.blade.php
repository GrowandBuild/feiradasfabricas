@extends('layouts.app')

@section('title', 'Galeria de Fotos - Feira das Fábricas')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="mb-1" style="font-weight:800">Galeria</h1>
                <p class="text-muted mb-0">Explore momentos, produtos e bastidores da Feira das Fábricas</p>
            </div>
        </div>

        @if($galleries->count() === 0)
            <div class="text-center py-5">
                <i class="fa-regular fa-images" style="font-size: 3rem; color: #cbd5e1"></i>
                <h5 class="mt-3 mb-1">Nenhuma galeria publicada ainda</h5>
                <p class="text-muted">Volte em breve para ver novidades.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach($galleries as $gallery)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <a href="{{ route('gallery.show', $gallery->slug) }}" class="text-decoration-none">
                            <div class="card h-100 shadow-sm border-0" style="overflow:hidden; border-radius: 1rem;">
                                <div class="ratio ratio-16x9">
                                    <img src="{{ $gallery->cover_url ?? asset('images/no-image.svg') }}" alt="{{ $gallery->title }}" class="w-100 h-100" style="object-fit:cover;">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1" style="color:#0f172a; font-weight:700;">{{ $gallery->title ?: 'Galeria' }}</h5>
                                    <p class="text-muted small mb-2">{{ $gallery->images_count }} {{ Str::plural('foto', $gallery->images_count) }}</p>
                                    @if($gallery->description)
                                        <p class="text-muted" style="font-size: 0.95rem;">{{ Str::limit(strip_tags($gallery->description), 100) }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
