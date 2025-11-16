@extends('layouts.app')

@section('title', $gallery->title . ' - Galeria')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="mb-1" style="font-weight:800">{{ $gallery->title }}</h1>
                @if($gallery->description)
                    <p class="text-muted mb-0">{!! nl2br(e($gallery->description)) !!}</p>
                @endif
            </div>
            <div>
                <a href="{{ route('gallery.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i> Voltar
                </a>
            </div>
        </div>

        @if($gallery->images->count() === 0)
            <div class="text-center py-5">
                <i class="fa-regular fa-image" style="font-size: 3rem; color: #cbd5e1"></i>
                <h5 class="mt-3 mb-1">Nenhuma imagem nesta galeria</h5>
            </div>
        @else
            <div class="row g-3">
                @foreach($gallery->images as $image)
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="#" class="d-block" data-bs-toggle="modal" data-bs-target="#lightboxModal" data-image="{{ $image->url }}" data-title="{{ $image->title }}" data-alt="{{ $image->alt_text }}">
                            <div class="ratio ratio-1x1" style="border-radius: 0.75rem; overflow: hidden;">
                                <img src="{{ $image->url }}" alt="{{ $image->alt_text ?? $image->title ?? $gallery->title }}" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Modal Lightbox -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lightboxTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <img id="lightboxImage" src="" alt="" class="w-100 h-100" style="object-fit: contain; background: #0b1220;">
                </div>
            </div>
        </div>
    </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('lightboxModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const imageUrl = trigger.getAttribute('data-image');
        const title = trigger.getAttribute('data-title') || '';
        const alt = trigger.getAttribute('data-alt') || title;
        document.getElementById('lightboxImage').src = imageUrl;
        document.getElementById('lightboxImage').alt = alt;
        document.getElementById('lightboxTitle').textContent = title;
    });
});
</script>
@endpush
@endsection
