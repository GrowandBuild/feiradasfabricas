@extends('admin.layouts.app')

@section('title', 'Gerenciamento de Usuários')
@section('page-title', 'Gerenciamento de Usuários')
@section('page-subtitle')
    <p class="text-muted mb-0">Gerencie usuários administradores, roles e permissões</p>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people"></i> Usuários Administradores
                    </h5>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Novo Usuário
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-12">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Buscar por nome ou email..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="role">
                                    <option value="">Todas as roles</option>
                                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>
                                        Super Administrador
                                    </option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>
                                        Administrador
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="is_active">
                                    <option value="">Todos os status</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                                        Ativos
                                    </option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                                        Inativos
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabela de usuários -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Role</th>
                                <th>Permissões</th>
                                <th>Último Login</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                @if($user->avatar)
                                                    <img src="{{ Storage::url($user->avatar) }}" 
                                                         alt="{{ $user->name }}" class="rounded-circle" 
                                                         style="width: 40px; height: 40px;">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'super_admin' ? 'danger' : 'primary' }}">
                                            {{ $user->role === 'super_admin' ? 'Super Admin' : 'Admin' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->isSuperAdmin())
                                            <span class="badge bg-success">Todas</span>
                                        @else
                                            <span class="badge bg-secondary">{{ count($user->permissions ?? []) }} permissões</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->last_login_at)
                                            <div>{{ $user->last_login_at->format('d/m/Y H:i') }}</div>
                                            <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Nunca</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-outline-info btn-sm" title="Visualizar">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-outline-primary btn-sm" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            @if($user->id !== auth('admin')->id())
                                                <form action="{{ route('admin.users.toggle-status', $user) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} btn-sm"
                                                            title="{{ $user->is_active ? 'Desativar' : 'Ativar' }}"
                                                            onclick="return confirm('Tem certeza?')">
                                                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.users.reset-password', $user) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-outline-secondary btn-sm" 
                                                            title="Resetar Senha"
                                                            onclick="return confirm('Resetar senha para \"password123\"?')">
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.users.destroy', $user) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            title="Deletar"
                                                            onclick="return confirm('Tem certeza que deseja deletar este usuário?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-people display-4"></i>
                                            <p class="mt-2">Nenhum usuário encontrado</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($users->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total de Usuários</h6>
                        <h3>{{ $users->total() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people display-4"></i>
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
                        <h6 class="card-title">Usuários Ativos</h6>
                        <h3>{{ $users->where('is_active', true)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Super Admins</h6>
                        <h3>{{ $users->where('role', 'super_admin')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-shield-check display-4"></i>
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
                        <h6 class="card-title">Admins</h6>
                        <h3>{{ $users->where('role', 'admin')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-person-gear display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
