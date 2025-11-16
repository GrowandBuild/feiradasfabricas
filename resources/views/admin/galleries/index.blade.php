@extends('admin.layouts.app')

@section('title', 'Galerias')
@section('page-icon', 'bi bi-images')
@section('page-title', 'Galerias')
@section('page-description', 'Gerencie as galerias de fotos exibidas no site')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-images me-2"></i>
            <strong>Lista de Galerias</strong>
        </div>
        <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nova Galeria
        </a>
    </div>
    <div class="card-body">
        <form class="row g-2 mb-3" method="get">
            <div class="col-md-6">
                <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Buscar por título ou descrição">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Todos os status</option>
                    <option value="published" @selected(request('status')==='published')>Publicado</option>
                    <option value="draft" @selected(request('status')==='draft')>Rascunho</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" type="submit">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
            </div>
        </form>

        @if($galleries->count() === 0)
            <div class="alert alert-info">
                Nenhuma galeria encontrada.
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Capa</th>
                            <th>Título</th>
                            <th>Status</th>
                            <th>Imagens</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($galleries as $gallery)
                            <tr id="gallery-{{ $gallery->id }}">
                                <td>{{ $gallery->id }}</td>
                                <td style="width:120px;">
                                    <div class="ratio ratio-16x9" style="border-radius: 8px; overflow:hidden;">
                                        <img src="{{ $gallery->cover_url ?? asset('images/no-image.svg') }}" class="w-100 h-100" style="object-fit:cover;"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $gallery->title ?: '(Sem título)' }}</div>
                                    <small class="text-muted">/galeria/{{ $gallery->slug }}</small>
                                </td>
                                <td>
                                    @if($gallery->is_published)
                                        <span class="badge bg-success">Publicado</span>
                                    @else
                                        <span class="badge bg-secondary">Rascunho</span>
                                    @endif
                                </td>
                                <td>{{ $gallery->images()->count() }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.galleries.toggle-publish', $gallery) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-warning" type="submit" title="Publicar/Despublicar">
                                                <i class="bi bi-toggle-{{ $gallery->is_published ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="post" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta galeria?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $galleries->withQueryString()->links() }}
        @endif
    </div>
    </div>
@endsection
