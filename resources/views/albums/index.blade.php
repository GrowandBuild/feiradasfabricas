@extends('layouts.app')

@section('title', 'Álbuns')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Álbuns</h1>

    @if(auth()->guard('admin')->check())
        <div class="card mb-3 p-3">
            <h5 class="mb-2">Criar Álbum Rápido (admin)</h5>
            <form id="quick-create-album" action="{{ route('albums.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-auto" style="flex:1">
                        <label class="form-label small">Título</label>
                        <input name="title" class="form-control form-control-sm" required />
                    </div>
                    <div class="col-auto">
                        <label class="form-label small">Capa</label>
                        <input type="file" name="cover" accept="image/*" class="form-control form-control-sm" />
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary btn-sm">Criar</button>
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
        <div class="alert alert-secondary">Nenhum álbum publicado.</div>
    @else
        <div class="row g-3">
            @foreach($albums as $album)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('albums.show', $album->slug) }}" class="text-decoration-none text-dark">
                    <div class="card h-100 position-relative">
                        @if(auth()->guard('admin')->check() && !$album->is_published)
                            <span class="badge bg-warning text-dark position-absolute" style="z-index:10; right:8px; top:8px;">Rascunho</span>
                        @endif
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
