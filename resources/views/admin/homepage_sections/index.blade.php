@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Sessões da Página</h3>
        <a href="{{ route('admin.homepage-sections.create') }}" class="btn btn-primary">Nova Sessão</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Departamento</th>
                <th>Produtos</th>
                <th>Limite</th>
                <th>Ordem</th>
                <th>Ativo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->title }}</td>
                    <td>{{ $s->department ? $s->department->name : '-' }}</td>
                    <td>{{ is_array($s->product_ids) ? implode(',', array_slice($s->product_ids,0,5)) : '-' }}</td>
                    <td>{{ $s->limit }}</td>
                    <td>{{ $s->position }}</td>
                    <td>{{ $s->enabled ? 'Sim' : 'Não' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.homepage-sections.edit', $s) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('admin.homepage-sections.destroy', $s) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Remover sessão?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Remover</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
