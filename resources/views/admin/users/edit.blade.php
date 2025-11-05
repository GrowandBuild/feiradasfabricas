@extends('admin.layouts.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')
@section('page-subtitle')
    <p class="text-muted mb-0">Editar usuário administrador</p>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-gear"></i> Editar Usuário: {{ $user->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Informações Básicas -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-person"></i> Informações Básicas
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nome Completo *</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Senha -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-shield-lock"></i> Segurança
                            </h6>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                Deixe os campos de senha em branco para manter a senha atual.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Nova Senha</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            <small class="form-text text-muted">Mínimo 8 caracteres (deixe em branco para manter atual)</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation">
                        </div>
                    </div>

                    <!-- Role e Permissões -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-shield-check"></i> Role e Permissões
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" 
                                    name="role" 
                                    required>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ old('role', $user->role) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-4">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Usuário ativo
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Usuário -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-info-circle"></i> Informações Adicionais
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Criado em</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Último login</label>
                            <input type="text" class="form-control" 
                                   value="{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}" 
                                   readonly>
                        </div>
                    </div>

                    <!-- Permissões -->
                    <div class="row mb-4" id="permissions-section">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-key"></i> Permissões Específicas
                            </h6>
                            <p class="text-muted mb-3">
                                <small>
                                    <i class="bi bi-info-circle"></i> 
                                    Super Administradores têm todas as permissões automaticamente.
                                </small>
                            </p>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                @foreach($permissions as $permission => $description)
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" 
                                                   type="checkbox" 
                                                   id="permission_{{ $loop->index }}" 
                                                   name="permissions[]" 
                                                   value="{{ $permission }}"
                                                   {{ in_array($permission, old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $loop->index }}">
                                                {{ $description }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Atualizar Usuário
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const permissionsSection = document.getElementById('permissions-section');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    function togglePermissionsSection() {
        const isSuperAdmin = roleSelect.value === 'super_admin';
        permissionsSection.style.display = isSuperAdmin ? 'none' : 'block';
        
        if (isSuperAdmin) {
            // Desmarcar todas as permissões para super admin
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    }

    // Event listener para mudança de role
    roleSelect.addEventListener('change', togglePermissionsSection);
    
    // Executar na carga inicial
    togglePermissionsSection();
});
</script>
@endsection
