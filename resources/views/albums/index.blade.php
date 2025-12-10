@extends('layouts.app')

@section('title', 'Álbuns')

@section('content')
<div class="container py-4 albums-page">
    <h1 class="albums-page-title mb-4">Álbuns</h1>

    @if(auth()->guard('admin')->check())
        <div class="card admin-quick-create-album mb-4 p-3 shadow-sm border-0">
            <h5 class="mb-3 fw-bold">
                <i class="bi bi-plus-circle me-2"></i>Criar Álbum Rápido (admin)
            </h5>
            <form id="quick-create-album" action="{{ route('albums.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label small fw-semibold">Título</label>
                        <input name="title" class="form-control" required placeholder="Nome do álbum" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold">Capa</label>
                        <input type="file" name="cover" accept="image/*" class="form-control" />
                    </div>
                    <div class="col-12 col-md-3">
                        <button class="btn btn-primary w-100" style="background: var(--secondary-color); border-color: var(--secondary-color);">
                            <i class="bi bi-plus-lg me-1"></i>Criar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <script>
            (function(){
                const form = document.getElementById('quick-create-album');
                if (!form) return;
                form.addEventListener('submit', function(ev){
                    ev.preventDefault();
                    const fd = new FormData(form);
                    const submit = form.querySelector('button');
                    submit.setAttribute('disabled','disabled');
                    fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: fd
                    }).then(r => r.json()).then(json => {
                        submit.removeAttribute('disabled');
                        if (json.success && json.album && json.album.slug) {
                            // redirect to album page
                            window.location = '/albuns/' + json.album.slug;
                        } else {
                            alert('Erro ao criar álbum');
                        }
                    }).catch(err => { submit.removeAttribute('disabled'); alert('Erro na requisição'); });
                });
            })();
        </script>
    @endif

    @if($albums->count() === 0)
        <div class="alert alert-secondary text-center py-4">
            <i class="bi bi-images fs-3 d-block mb-2"></i>
            Nenhum álbum publicado.
        </div>
    @else
        <div class="row g-3 g-md-4 albums-grid">
            @foreach($albums as $album)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('albums.show', $album->slug) }}" class="album-card-link text-decoration-none">
                    <div class="album-card card h-100 position-relative border-0 shadow-sm">
                        @if(auth()->guard('admin')->check() && !$album->is_published)
                            <span class="badge bg-warning text-dark position-absolute album-draft-badge" style="z-index:10; right:8px; top:8px;">
                                <i class="bi bi-file-earmark me-1"></i>Rascunho
                            </span>
                        @endif
                        <div class="album-card-image ratio ratio-1x1 overflow-hidden position-relative">
                            <img src="{{ $album->cover_url ?? asset('images/no-image.svg') }}" 
                                 class="w-100 h-100" 
                                 style="object-fit:cover; transition: transform 0.3s ease;" 
                                 alt="{{ $album->title }}" 
                                 loading="lazy">
                            <div class="album-card-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-eye-fill text-white fs-3 opacity-0" style="transition: opacity 0.3s ease;"></i>
                            </div>
                        </div>
                        <div class="card-body py-3">
                            <div class="fw-semibold album-title mb-1">{{ $album->title }}</div>
                            @if($album->description)
                                <small class="text-muted d-block text-truncate" title="{{ $album->description }}">{{ $album->description }}</small>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $albums->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    /* Página de Álbuns - Design Moderno */
    .albums-page {
        max-width: var(--site-container-max-width, 1320px);
        margin: 0 auto;
    }

    .albums-page-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-dark);
        text-align: center;
        margin-bottom: 2rem !important;
    }

    /* Card de criação rápida (admin) */
    .admin-quick-create-album {
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.05) 0%, #ffffff 100%);
        border: 2px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.2) !important;
        border-radius: 12px;
    }

    .admin-quick-create-album h5 {
        color: var(--secondary-color, #ff6b35);
    }

    .admin-quick-create-album .form-control:focus {
        border-color: var(--secondary-color, #ff6b35);
        box-shadow: 0 0 0 0.2rem rgba(var(--secondary-color-rgb, 255, 107, 53), 0.25);
    }

    /* Cards de álbuns */
    .album-card-link {
        display: block;
        transition: transform 0.3s ease;
    }

    .album-card-link:hover {
        transform: translateY(-5px);
        text-decoration: none;
    }

    .album-card {
        border-radius: 12px !important;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #ffffff;
    }

    .album-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    }

    .album-card-image {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px 12px 0 0;
    }

    .album-card-link:hover .album-card-image img {
        transform: scale(1.1);
    }

    .album-card-overlay {
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 12px 12px 0 0;
    }

    .album-card-link:hover .album-card-overlay {
        opacity: 1;
    }

    .album-card-link:hover .album-card-overlay i {
        opacity: 1;
    }

    .album-title {
        color: var(--text-dark);
        transition: color 0.3s ease;
    }

    .album-card-link:hover .album-title {
        color: var(--secondary-color, #ff6b35);
    }

    .album-draft-badge {
        border-radius: 6px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Paginação */
    .pagination {
        --bs-pagination-color: var(--secondary-color, #ff6b35);
        --bs-pagination-hover-color: var(--secondary-color, #ff6b35);
        --bs-pagination-focus-color: var(--secondary-color, #ff6b35);
        --bs-pagination-active-bg: var(--secondary-color, #ff6b35);
        --bs-pagination-active-border-color: var(--secondary-color, #ff6b35);
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .albums-page-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem !important;
        }

        .admin-quick-create-album {
            margin-bottom: 1.5rem !important;
        }

        .albums-grid {
            gap: 0.75rem !important;
        }
    }

    @media (max-width: 576px) {
        .albums-page-title {
            font-size: 1.25rem;
        }
    }
</style>
@endpush
@endsection
