@extends('admin.layouts.app')

@section('title', 'Editar Galeria')
@section('page-icon', 'bi bi-images')
@section('page-title', 'Editar Galeria')
@section('page-description', 'Atualize informações, capa e gerencie as imagens desta galeria')

@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Informações da Galeria</span>
                <form action="{{ route('admin.galleries.toggle-publish', $gallery) }}" method="post" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-sm {{ $gallery->is_published ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        <i class="bi bi-toggle-{{ $gallery->is_published ? 'on' : 'off' }} me-1"></i>
                        {{ $gallery->is_published ? 'Despublicar' : 'Publicar' }}
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.galleries.update', $gallery) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $gallery->title) }}">
                        @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description', $gallery->description) }}</textarea>
                        @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Capa</label>
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-6">
                                <div class="ratio ratio-16x9" style="border-radius: 10px; overflow:hidden; background:#f1f5f9;">
                                    <img src="{{ $gallery->cover_url ?? asset('images/no-image.svg') }}" class="w-100 h-100" style="object-fit:cover;" alt="Capa da galeria">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="file" name="cover_image" accept="image/*" class="form-control mb-2">
                                @error('cover_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                @if($gallery->cover_image)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" name="remove_cover" id="remove_cover">
                                        <label class="form-check-label" for="remove_cover">Remover capa atual</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" @checked(old('is_published', $gallery->is_published))>
                        <label class="form-check-label" for="is_published">Publicado</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-check2 me-1"></i>Salvar alterações
                        </button>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Upload de Imagens
            </div>
            <div class="card-body">
                <form action="{{ route('admin.galleries.images.upload', $gallery) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="images[]" accept="image/*" class="form-control" multiple required>
                        <small class="text-muted">Formatos: jpeg, png, jpg, gif, webp. Máx. 10MB cada.</small>
                        @error('images.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-upload me-1"></i>Enviar
                    </button>
                </form>
                <hr class="my-4">
                <form action="{{ route('admin.galleries.images.add-url', $gallery) }}" method="post">
                    @csrf
                    <label class="form-label">Adicionar por link (URL)</label>
                    <div class="input-group">
                        <input type="url" name="image_url" class="form-control" placeholder="https://exemplo.com/imagem.jpg" required>
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-link-45deg me-1"></i>Adicionar
                        </button>
                    </div>
                    @error('image_url')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <small class="text-muted">Aceita JPEG, PNG, GIF, WEBP até 10MB.</small>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Imagens ({{ $gallery->images->count() }})</span>
                @if($gallery->images->count() > 0)
                <form action="{{ route('admin.galleries.images.reorder', $gallery) }}" method="post">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                        <i class="bi bi-list-ol me-1"></i>Salvar ordem
                    </button>
                </form>
                @endif
            </div>
            <div class="card-body">
                @if($gallery->images->count() === 0)
                    <div class="text-muted">Nenhuma imagem enviada ainda.</div>
                @else
                    <form action="{{ route('admin.galleries.images.reorder', $gallery) }}" method="post">
                        @csrf
                        <div class="row g-3">
                            @foreach($gallery->images as $image)
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-3 p-2 border rounded shadow-sm">
                                        <div style="width: 90px;">
                                            <div class="ratio ratio-1x1" style="border-radius: 8px; overflow:hidden;">
                                                <img src="{{ $image->url }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $image->alt_text }}">
                                            </div>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-5">
                                                    <label class="form-label mb-0 small text-muted">Título</label>
                                                    <input type="text" class="form-control form-control-sm" value="{{ $image->title }}" disabled>
                                                </div>
                                                <div class="col-3">
                                                    <label class="form-label mb-0 small text-muted">Ordem</label>
                                                    <input type="number" class="form-control form-control-sm" name="orders[{{ $image->id }}]" value="{{ $image->sort_order }}">
                                                </div>
                                                <div class="col-4 text-end">
                                                    <form action="{{ route('admin.galleries.images.destroy', [$gallery, $image]) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir esta imagem?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-list-ol me-1"></i>Salvar ordem
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@extends('admin.layouts.app')

@section('title', 'Editar Galeria')
@section('page-icon', 'bi bi-images')
@section('page-title', 'Editar Galeria')
@section('page-subtitle', $gallery->title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Informações</div>
            <div class="card-body">
                <form action="{{ route('admin.galleries.update', $gallery) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $gallery->title) }}" required>
                        @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $gallery->description) }}</textarea>
                        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Imagem de capa</label>
                            <input type="file" name="cover_image" accept="image/*" class="form-control">
                            @error('cover_image')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">Prévia</div>
                            <div class="ratio ratio-16x9" style="border-radius: 8px; overflow:hidden; background:#f1f5f9;">
                                <img src="{{ $gallery->cover_url ?? asset('images/no-image.svg') }}" class="w-100 h-100" style="object-fit:cover;">
                            </div>
                            @if($gallery->cover_image)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_cover" name="remove_cover">
                                    <label class="form-check-label" for="remove_cover">Remover capa atual</label>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-check form-switch my-3">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" @checked($gallery->is_published)>
                        <label class="form-check-label" for="is_published">Publicado</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>Salvar</button>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Imagens</span>
                <form action="{{ route('admin.galleries.images.upload', $gallery) }}" method="post" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                    @csrf
                    <input type="file" name="images[]" accept="image/*" multiple class="form-control" required>
                    <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-upload me-1"></i>Enviar</button>
                </form>
            </div>
            <div class="card-body">
                @if($gallery->images->count() === 0)
                    <div class="alert alert-info mb-0">Nenhuma imagem enviada ainda.</div>
                @else
                    <form action="{{ route('admin.galleries.images.reorder', $gallery) }}" method="post">
                        @csrf
                        <div class="row g-3">
                            @foreach($gallery->images as $img)
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <div class="border rounded p-2 h-100 d-flex flex-column">
                                        <div class="ratio ratio-16x9 mb-2" style="border-radius: 8px; overflow:hidden;">
                                            <img src="{{ $img->url }}" class="w-100 h-100" style="object-fit:cover;">
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="me-2" style="width: 110px;">
                                                <label class="form-label mb-1 small">Ordem</label>
                                                <input type="number" class="form-control form-control-sm" name="orders[{{ $img->id }}]" value="{{ $img->sort_order }}">
                                            </div>
                                            <form action="{{ route('admin.galleries.images.destroy', [$gallery, $img]) }}" method="post" onsubmit="return confirm('Remover esta imagem?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-arrow-repeat me-1"></i>Salvar ordem</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
