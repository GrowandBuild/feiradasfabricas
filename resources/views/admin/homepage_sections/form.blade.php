@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>{{ $section && $section->id ? 'Editar Sessão' : 'Nova Sessão' }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $section && $section->id ? route('admin.homepage-sections.update', $section) : route('admin.homepage-sections.store') }}" method="POST">
        @csrf
        @if($section && $section->id)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $section->title) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Departamento (opcional)</label>
            <select name="department_id" class="form-select">
                <option value="">-- Nenhum --</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ old('department_id', $section->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Produtos (opcional, seleciona manualmente)</label>
            <select name="product_ids[]" class="form-select" multiple size="8">
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ is_array(old('product_ids', $section->product_ids ?? [])) && in_array($p->id, old('product_ids', $section->product_ids ?? [])) ? 'selected' : '' }}>{{ $p->id }} - {{ $p->name }}</option>
                @endforeach
            </select>
            <small class="text-muted">Se nenhum produto for selecionado, serão usados produtos do departamento (se definido).</small>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Limite</label>
                <input type="number" name="limit" class="form-control" value="{{ old('limit', $section->limit ?? 4) }}" min="1" max="50">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Ordem (posição)</label>
                <input type="number" name="position" class="form-control" value="{{ old('position', $section->position ?? 0) }}">
            </div>
            <div class="col-md-3 mb-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="enabled" class="form-check-input" id="enabled" {{ old('enabled', $section->enabled ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="enabled">Ativa</label>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary">Salvar</button>
            <a href="{{ route('admin.homepage-sections.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

@endsection
