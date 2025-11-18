@extends('admin.layouts.app')

@section('title', 'Álbuns')
@section('page-title', 'Álbuns')
@section('page-description', 'Gerencie coleções de imagens simples')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <strong>Lista de Álbuns</strong>
    <a href="{{ route('admin.albums.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Novo Álbum</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($albums->count() === 0)
    <div class="alert alert-secondary">Nenhum álbum encontrado.</div>
@else
<table class="table align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Capa</th>
            <th>Título</th>
            <th>Publicado</th>
            <th>Imagens</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    @foreach($albums as $album)
        <tr>
            <td>{{ $album->id }}</td>
            <td style="width:90px">
                <div class="ratio ratio-1x1 border rounded" style="width:80px; overflow:hidden;">
                    @if($album->cover_url)
                        <img src="{{ $album->cover_url }}" class="w-100 h-100" style="object-fit:cover;"/>
                    @else
                        <img src="{{ asset('images/no-image.svg') }}" class="w-100 h-100" style="object-fit:cover;"/>
                    @endif
                </div>
            </td>
            <td>
                <div class="fw-semibold">{{ $album->title }}</div>
                <small class="text-muted">/albuns/{{ $album->slug }}</small>
            </td>
            <td>
                @if($album->is_published)
                    <span class="badge bg-success">Sim</span>
                @else
                    <span class="badge bg-secondary">Não</span>
                @endif
            </td>
            <td>{{ $album->images()->count() }}</td>
            <td>
                <a href="{{ route('admin.albums.edit', $album) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                <form action="{{ route('admin.albums.destroy', $album) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir este álbum?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Excluir</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $albums->withQueryString()->links() }}
@endif
@endsection
