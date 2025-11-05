@extends('admin.layouts.app')

@section('title', 'Selos de Marcas')
@section('page-title', 'Gerenciar Selos de Marcas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Selos de Marcas</h4>
        <p class="text-muted mb-0">Gerencie os selos/logos de marcas por departamento</p>
    </div>
    <a href="{{ route('admin.department-badges.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Selo
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.department-badges.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="department_id" class="form-label">Departamento</label>
                <select name="department_id" id="department_id" class="form-select">
                    <option value="">Todos os departamentos</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('admin.department-badges.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Mensagens de sucesso/erro -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle"></i> Erro:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Lista de Selos -->
<div class="card">
    <div class="card-body">
        @if($badges->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Título</th>
                            <th>Departamento</th>
                            <th>Link</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($badges as $badge)
                            <tr>
                                <td>
                                    <img src="{{ $badge->image_url }}" 
                                         alt="{{ $badge->title }}" 
                                         class="rounded-circle" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong>{{ $badge->title }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $badge->department->name }}</span>
                                </td>
                                <td>
                                    @if($badge->link)
                                        <a href="{{ $badge->link }}" target="_blank" class="text-decoration-none">
                                            <i class="bi bi-link-45deg"></i> Ver link
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $badge->sort_order }}</span>
                                </td>
                                <td>
                                    @if($badge->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.department-badges.edit', $badge) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.department-badges.toggle-active', $badge) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-{{ $badge->is_active ? 'warning' : 'success' }}" 
                                                    title="{{ $badge->is_active ? 'Desativar' : 'Ativar' }}">
                                                <i class="bi bi-{{ $badge->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.department-badges.destroy', $badge) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este selo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Excluir">
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
            <div class="mt-4">
                {{ $badges->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Nenhum selo encontrado.</p>
                <a href="{{ route('admin.department-badges.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Primeiro Selo
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

