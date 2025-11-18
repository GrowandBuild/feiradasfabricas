@extends('admin.layouts.app')

@section('title', 'Novo Álbum')
@section('page-title', 'Novo Álbum')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.albums.store') }}" method="post" enctype="multipart/form-data" class="card">
    @csrf
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Slug (opcional)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="sera-gerado-pelo-titulo">
            </div>
            <div class="col-12">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Capa (opcional)</label>
                <input type="file" name="cover" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
                <label class="form-label">Imagens (múltiplas)</label>
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" name="is_published" id="is_published" {{ old('is_published') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_published">Publicar álbum</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.albums.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Criar Álbum</button>
    </div>
</form>
@endsection
