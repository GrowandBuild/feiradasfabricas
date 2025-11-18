@extends('layouts.app')

@section('title', 'Álbuns')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Álbuns</h1>

    @if($albums->count() === 0)
        <div class="alert alert-secondary">Nenhum álbum publicado.</div>
    @else
        <div class="row g-3">
            @foreach($albums as $album)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('albums.show', $album->slug) }}" class="text-decoration-none text-dark">
                    <div class="card h-100">
                        <div class="ratio ratio-1x1">
                            <img src="{{ $album->cover_url ?? asset('images/no-image.svg') }}" class="w-100 h-100" style="object-fit:cover;" alt="{{ $album->title }}" loading="lazy">
                        </div>
                        <div class="card-body py-2">
                            <div class="fw-semibold">{{ $album->title }}</div>
                            @if($album->description)
                                <small class="text-muted d-block text-truncate" title="{{ $album->description }}">{{ $album->description }}</small>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $albums->links() }}
        </div>
    @endif
</div>
@endsection
