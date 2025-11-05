@extends('admin.layouts.app')

@section('title', 'Detalhes do Usuário')
@section('page-title', 'Detalhes do Usuário')
@section('page-subtitle')
    <p class="text-muted mb-0">Visualizar informações do usuário administrador</p>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person"></i> {{ $user->name }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Informações Básicas -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-person"></i> Informações Básicas
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome Completo</label>
                            <div class="form-control-plaintext">{{ $user->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="form-control-plaintext">{{ $user->email }}</div>
                        </div>
                    </div>
                </div>

                <!-- Role e Status -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-shield-check"></i> Role e Status
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role</label>
                            <div>
                                <span class="badge bg-{{ $user->role === 'super_admin' ? 'danger' : 'primary' }} fs-6">
                                    {{ $user->role === 'super_admin' ? 'Super Administrador' : 'Administrador' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if($user->is_active)
                                    <span class="badge bg-success fs-6">Ativo</span>
                                @else
                                    <span class="badge bg-danger fs-6">Inativo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Último Login</label>
                            <div class="form-control-plaintext">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    <small class="text-muted d-block">{{ $user->last_login_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissões -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-key"></i> Permissões
                        </h6>
                    </div>
                    <div class="col-12">
                        @if($user->isSuperAdmin())
                            <div class="alert alert-success">
                                <i class="bi bi-shield-check"></i>
                                <strong>Super Administrador:</strong> Este usuário tem todas as permissões do sistema.
                            </div>
                        @else
                            <div class="row">
                                @if(empty($user->permissions))
                                    <div class="col-12">
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            Este usuário não possui permissões específicas.
                                        </div>
                                    </div>
                                @else
                                    @foreach($user->permissions as $permission)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle text-success me-2"></i>
                                                <span>{{ $permission }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informações do Sistema -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle"></i> Informações do Sistema
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Criado em</label>
                            <div class="form-control-plaintext">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                                <small class="text-muted d-block">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Última atualização</label>
                            <div class="form-control-plaintext">
                                {{ $user->updated_at->format('d/m/Y H:i') }}
                                <small class="text-muted d-block">{{ $user->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Avatar -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-image"></i> Avatar
                </h6>
            </div>
            <div class="card-body text-center">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 120px; height: 120px; font-size: 3rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h5>{{ $user->name }}</h5>
                <p class="text-muted">{{ $user->email }}</p>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up"></i> Estatísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $user->activityLogs()->count() }}</h4>
                            <small class="text-muted">Atividades</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $user->inventoryLogs()->count() }}</h4>
                        <small class="text-muted">Logs de Estoque</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-gear"></i> Ações
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Usuário
                    </a>
                    
                    @if($user->id !== auth('admin')->id())
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} w-100"
                                    onclick="return confirm('Tem certeza?')">
                                <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                {{ $user->is_active ? 'Desativar' : 'Ativar' }} Usuário
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="btn btn-outline-secondary w-100"
                                    onclick="return confirm('Resetar senha para \"password123\"?')">
                                <i class="bi bi-key"></i> Resetar Senha
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Tem certeza que deseja deletar este usuário?')">
                                <i class="bi bi-trash"></i> Deletar Usuário
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar à Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
