@extends('admin.layouts.app')

@section('title', 'Criar Atributo')
@section('page-title', 'Criar Atributo')

@section('content')
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.attributes.store') }}" method="POST">
                    @csrf
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
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chave (opcional)</label>
                        <input type="text" name="key" class="form-control" value="{{ old('key') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departamento (opcional)</label>
                        <select name="department_id" class="form-select">
                            <option value="">— Nenhum —</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                        <label for="is_active" class="form-check-label">Ativo</label>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button class="btn btn-primary">Criar Atributo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
