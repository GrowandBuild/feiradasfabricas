@extends('admin.layouts.app')

@section('title', 'Atributos')
@section('page-title', 'Atributos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gerenciar Atributos</h5>
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary btn-sm">Criar Atributo</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Chave</th>
                            <th>Departamento</th>
                            <th>Valores</th>
                            <th>Ativo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attributes as $attr)
                            <tr>
                                <td>{{ $attr->name }}</td>
                                <td>{{ $attr->key }}</td>
                                <td>{{ $attr->department->name ?? '—' }}</td>
                                <td>{{ $attr->values->count() }}</td>
                                <td>{{ $attr->is_active ? 'Sim' : 'Não' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.attributes.show', $attr) }}" class="btn btn-sm btn-outline-secondary">Valores</a>
                                    <a href="{{ route('admin.attributes.edit', $attr) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form action="{{ route('admin.attributes.destroy', $attr) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Excluir atributo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $attributes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
