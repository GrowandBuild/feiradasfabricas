@extends('admin.layouts.app')

@section('title', 'Editar Álbum')
@section('page-title', 'Editar Álbum')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.albums.update', $album) }}" method="post" enctype="multipart/form-data" class="card mb-4">
    @csrf
    @method('PUT')
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $album->title) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $album->slug) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $album->description) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Capa</label>
                <input type="file" name="cover" class="form-control" accept="image/*">
                <div class="mt-2">
                    <div class="ratio ratio-1x1 border rounded" style="width:100px; overflow:hidden;">
                        @if($album->cover_url)
                            <img src="{{ $album->cover_url }}" class="w-100 h-100" style="object-fit:cover;"/>
                        @else
                            <img src="{{ asset('images/no-image.svg') }}" class="w-100 h-100" style="object-fit:cover;"/>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Adicionar imagens</label>
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" name="is_published" id="is_published" {{ old('is_published', $album->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_published">Publicado</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.albums.index') }}" class="btn btn-outline-secondary">Voltar</a>
        <button class="btn btn-primary">Salvar</button>
    </div>
</form>

<div class="card">
    <div class="card-header">Imagens do Álbum</div>
    <div class="card-body">
        @if($album->images->count() === 0)
            <div class="text-muted">Nenhuma imagem adicionada.</div>
        @else
            <div class="row g-3">
                @foreach($album->images as $image)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="border rounded p-1 position-relative">
                        <img src="{{ Storage::url($image->path) }}" class="w-100" style="aspect-ratio:1/1; object-fit:cover;"/>
                        <form action="{{ route('admin.albums.images.destroy', [$album, $image]) }}" method="post" class="position-absolute top-0 end-0 m-1">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Remover" onclick="return confirm('Remover imagem?');">
                                <i class="bi bi-x"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
