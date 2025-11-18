@extends('layouts.app')

@section('title', $album->title)

@section('content')
<div class="container py-4">
    <a href="{{ route('albums.index') }}" class="btn btn-outline-secondary btn-sm mb-3">Voltar</a>

    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="ratio ratio-1x1" style="width:80px">
            <img src="{{ $album->cover_url ?? asset('images/no-image.svg') }}" class="w-100 h-100 rounded" style="object-fit:cover;" loading="lazy">
        </div>
        <div>
            <h1 class="h4 m-0">{{ $album->title }}</h1>
            @if($album->description)
                <div class="text-muted">{{ $album->description }}</div>
            @endif
        </div>
    </div>

    @if($album->images->count() === 0)
        <div class="alert alert-secondary">Nenhuma imagem neste álbum.</div>
    @else
        <div class="row g-3">
            @foreach($album->images as $image)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ Storage::url($image->path) }}" target="_blank" rel="noopener" class="d-block border rounded overflow-hidden">
                    <img src="{{ Storage::url($image->path) }}" class="w-100" style="aspect-ratio:1/1; object-fit:cover;" alt="{{ $image->alt ?? $album->title }}" loading="lazy">
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
