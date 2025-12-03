@extends('admin.layouts.app')

@section('title', 'Editar Atributo')
@section('page-title', 'Editar Atributo')

@section('content')
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $attribute->name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chave (opcional)</label>
                        <input type="text" name="key" class="form-control" value="{{ old('key', $attribute->key) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departamento (opcional)</label>
                        <select name="department_id" class="form-select">
                            <option value="">— Nenhum —</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ old('department_id', $attribute->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ $attribute->is_active ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Ativo</label>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
