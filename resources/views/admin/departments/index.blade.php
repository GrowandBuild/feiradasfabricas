@extends('admin.layouts.app')

@section('title', 'Departamentos')
@section('page-title', 'Departamentos')
@section('page-subtitle')
    <p class="text-muted mb-0">Gerencie os departamentos da loja</p>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-building"></i> Lista de Departamentos
                </h5>
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Novo Departamento
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($departments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Slug</th>
                                    <th>Ícone</th>
                                    <th>Cor</th>
                                    <th>Produtos</th>
                                    <th>Status</th>
                                    <th>Ordem</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $department->icon ?? 'fas fa-folder' }} me-2" style="color: {{ $department->color }};"></i>
                                                <strong>{{ $department->name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <code>{{ $department->slug }}</code>
                                        </td>
                                        <td>
                                            <i class="{{ $department->icon ?? 'fas fa-folder' }}" style="color: {{ $department->color }};"></i>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="color-preview me-2" style="width: 20px; height: 20px; background-color: {{ $department->color }}; border-radius: 3px; border: 1px solid #ddd;"></div>
                                                <span class="text-muted">{{ $department->color }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $department->total_products }} produtos</span>
                                        </td>
                                        <td>
                                            @if($department->is_active)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $department->sort_order }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.departments.show', $department) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Ver detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.departments.edit', $department) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.departments.destroy', $department) }}" 
                                                      method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Tem certeza que deseja excluir este departamento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
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
                    <div class="d-flex justify-content-center">
                        {{ $departments->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-building fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Nenhum departamento encontrado</h4>
                        <p class="text-muted">Comece criando seu primeiro departamento.</p>
                        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Criar Departamento
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas dos Departamentos -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total de Departamentos</h6>
                        <h3 class="mb-0">{{ $departments->total() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-building fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Departamentos Ativos</h6>
                        <h3 class="mb-0">{{ $departments->where('is_active', true)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Departamentos Inativos</h6>
                        <h3 class="mb-0">{{ $departments->where('is_active', false)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-x-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total de Produtos</h6>
                        <h3 class="mb-0">{{ $departments->sum('total_products') }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
