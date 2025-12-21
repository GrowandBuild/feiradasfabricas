@extends('admin.layouts.app')

@section('title', 'Atributos de Produtos')
@section('page-title', 'Gerenciar Atributos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Atributos de Produtos</h4>
        <p class="text-muted mb-0">Gerencie atributos globais reutilizáveis (Cor, Tamanho, etc.)</p>
    </div>
    <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Atributo
    </a>
</div>

<!-- Lista de Atributos -->
<div class="card">
    <div class="card-body">
        @if($attributes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Valores</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attributes as $attribute)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $attribute->name }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $attribute->slug }}</small>
                                </td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            'color' => 'Cor',
                                            'size' => 'Tamanho',
                                            'text' => 'Texto',
                                            'number' => 'Número',
                                            'image' => 'Imagem'
                                        ];
                                        $typeColors = [
                                            'color' => 'info',
                                            'size' => 'primary',
                                            'text' => 'secondary',
                                            'number' => 'warning',
                                            'image' => 'success'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$attribute->type] ?? 'secondary' }}">
                                        {{ $typeLabels[$attribute->type] ?? ucfirst($attribute->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $attribute->values_count }} valores</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $attribute->sort_order ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $attribute->is_active ? 'success' : 'danger' }}">
                                        {{ $attribute->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.attributes.show', $attribute) }}" 
                                           class="btn btn-outline-info btn-sm" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.attributes.edit', $attribute) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.attributes.destroy', $attribute) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este atributo?\n\nAtenção: Atributos usados em variações não podem ser excluídos.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir">
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

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $attributes->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tags" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum atributo encontrado</h5>
                <p class="text-muted">Comece criando seu primeiro atributo (ex: Cor, Tamanho).</p>
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Atributo
                </a>
            </div>
        @endif
    </div>
</div>
@endsection



