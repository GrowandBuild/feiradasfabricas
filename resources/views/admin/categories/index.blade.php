@extends('admin.layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Gerenciar Categorias')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Categorias</h4>
        <p class="text-muted mb-0">Gerencie todas as categorias de produtos</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nova Categoria
    </a>
</div>

<!-- Lista de Categorias -->
<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Produtos</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" 
                                             alt="{{ $category->name }}" 
                                             class="rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-tag text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $category->name }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $category->slug }}</small>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($category->description, 50) ?: 'Sem descrição' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category->products_count }} produtos</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $category->sort_order ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                        {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.show', $category) }}" 
                                           class="btn btn-outline-info btn-sm" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?\n\nAtenção: Categorias com produtos associados não podem ser excluídas.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Excluir"
                                                    {{ $category->products_count > 0 ? 'disabled' : '' }}>
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
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tags" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhuma categoria encontrada</h5>
                <p class="text-muted">Comece criando sua primeira categoria.</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Categoria
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

