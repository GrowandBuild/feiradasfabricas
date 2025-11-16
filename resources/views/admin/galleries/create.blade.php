@extends('admin.layouts.app')

@section('title', 'Nova Galeria')
@section('page-icon', 'bi bi-images')
@section('page-title', 'Nova Galeria')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                Informações
            </div>
            <div class="card-body">
                <form action="{{ route('admin.galleries.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagem de capa (opcional)</label>
                        <input type="file" name="cover_image" accept="image/*" class="form-control">
                        @error('cover_image')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" checked>
                        <label class="form-check-label" for="is_published">Publicado</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Salvar e continuar</button>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
