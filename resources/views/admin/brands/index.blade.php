@extends('admin.layouts.app')

@section('title', 'Marcas')
@section('page-title', 'Gerenciar Marcas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Marcas</h4>
        <p class="text-muted mb-0">Gerencie todas as marcas de produtos</p>
    </div>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nova Marca
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar por nome</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="{{ request('search') }}" placeholder="Digite o nome da marca">
            </div>
            <div class="col-md-3">
                <label for="is_active" class="form-label">Status</label>
                <select class="form-select" id="is_active" name="is_active">
                    <option value="">Todos</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Marcas -->
<div class="card">
    <div class="card-body">
        @if($brands->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Produtos</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($brands as $brand)
                            <tr>
                                <td>
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" 
                                             alt="{{ $brand->name }}" 
                                             style="width: 30px; height: 30px; object-fit: cover;" 
                                             class="me-2 rounded">
                                    @endif
                                    <div>
                                        <strong>{{ $brand->name }}</strong>
                                        @if($brand->description)
                                            <br><small class="text-muted">{{ Str::limit($brand->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <code class="small">{{ $brand->slug }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $brand->products()->count() }}</span>
                                </td>
                                <td>{{ $brand->sort_order }}</td>
                                <td>
                                    @if($brand->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.brands.show', $brand) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.brands.edit', $brand) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.brands.toggle-active', $brand) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm {{ $brand->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $brand->is_active ? 'Desativar' : 'Ativar' }}">
                                                <i class="bi {{ $brand->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                            </button>
                                        </form>
                                        @if($brand->products()->count() === 0)
                                            <form action="{{ route('admin.brands.destroy', $brand) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir esta marca?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $brands->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tag display-1 text-muted mb-3"></i>
                <h5>Nenhuma marca encontrada</h5>
                <p class="text-muted">Comece criando sua primeira marca.</p>
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Primeira Marca
                </a>
            </div>
        @endif
    </div>
</div>
@endsection