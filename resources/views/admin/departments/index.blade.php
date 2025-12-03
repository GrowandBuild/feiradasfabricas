@extends('admin.layouts.app')

@section('title', 'Departamentos')
@section('page-title', 'Departamentos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Lista de departamentos</h5>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Criar Departamento</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
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
                                                <button type="button" class="btn btn-sm btn-outline-success apply-dept-theme" data-id="{{ $department->id }}" title="Aplicar tema deste departamento">
                                                    <i class="bi bi-palette"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary restore-dept-theme" data-id="{{ $department->id }}" title="Restaurar tema padrão deste departamento">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    function handleResponseAndApply(theme){
        if (!theme) return;
        // Normalize keys expected by the layout listener
        const payload = { theme: { theme_primary: theme.theme_primary || theme.primary || theme.themePrimary || theme.primary_color, theme_secondary: theme.theme_secondary || theme.secondary || theme.themeSecondary || theme.secondary_color } };
        window.dispatchEvent(new CustomEvent('theme:updated', { detail: payload }));
    }

    document.querySelectorAll('.apply-dept-theme').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            if (!confirm('Aplicar o tema deste departamento no painel para pré-visualização?')) return;
            fetch(`/admin/departments/${id}/restore-theme-colors`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data) handleResponseAndApply(data);
                }).catch(err => { console.error('Erro ao aplicar tema:', err); alert('Erro ao aplicar tema. Veja o console.'); });
        });
    });

    document.querySelectorAll('.restore-dept-theme').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            if (!confirm('Restaurar cores padrão do departamento e aplicar no painel?')) return;
            fetch(`/admin/departments/${id}/restore-theme-colors`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data) handleResponseAndApply(data);
                }).catch(err => { console.error('Erro ao restaurar tema:', err); alert('Erro ao restaurar tema. Veja o console.'); });
        });
    });
});
</script>
@endpush
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    function handleResponseAndApply(theme){
        if (!theme) return;
        // Normalize keys expected by the layout listener
        const payload = { theme: { theme_primary: theme.theme_primary || theme.primary || theme.themePrimary || theme.primary_color, theme_secondary: theme.theme_secondary || theme.secondary || theme.themeSecondary || theme.secondary_color } };
        window.dispatchEvent(new CustomEvent('theme:updated', { detail: payload }));
    }

    document.querySelectorAll('.apply-dept-theme').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            if (!confirm('Aplicar o tema deste departamento no painel para pré-visualização?')) return;
            fetch(`/admin/departments/${id}/restore-theme-colors`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data) handleResponseAndApply(data);
                }).catch(err => { console.error('Erro ao aplicar tema:', err); alert('Erro ao aplicar tema. Veja o console.'); });
        });
    });

    document.querySelectorAll('.restore-dept-theme').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            if (!confirm('Restaurar cores padrão do departamento e aplicar no painel?')) return;
            fetch(`/admin/departments/${id}/restore-theme-colors`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data) handleResponseAndApply(data);
                }).catch(err => { console.error('Erro ao restaurar tema:', err); alert('Erro ao restaurar tema. Veja o console.'); });
        });
    });
});
</script>
@endpush
