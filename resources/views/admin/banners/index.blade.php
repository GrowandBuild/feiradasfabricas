@extends('admin.layouts.app')

@section('title', 'Banners')
@section('page-title', 'Gerenciar Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Banners</h4>
        <p class="text-muted mb-0">Gerencie os banners promocionais por departamento</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Banner
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.banners.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="department_id" class="form-label">Departamento</label>
                <select name="department_id" id="department_id" class="form-select">
                    <option value="">Todos os departamentos</option>
                    <option value="global" {{ request('department_id') === 'global' ? 'selected' : '' }}>Banners Globais</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="position" class="form-label">Posição</label>
                <select name="position" id="position" class="form-select">
                    <option value="">Todas as posições</option>
                    <option value="hero" {{ request('position') === 'hero' ? 'selected' : '' }}>Hero (Topo)</option>
                    <option value="category" {{ request('position') === 'category' ? 'selected' : '' }}>Categorias</option>
                    <option value="product" {{ request('position') === 'product' ? 'selected' : '' }}>Produtos</option>
                    <option value="footer" {{ request('position') === 'footer' ? 'selected' : '' }}>Rodapé</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Banners -->
<div class="card">
    <div class="card-body">
        @if($banners->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Título</th>
                            <th>Departamento</th>
                            <th>Posição</th>
                            <th>Público-Alvo</th>
                            <th>Ordem</th>
                            <th>Período</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banners as $banner)
                            <tr>
                                <td>
                                    @if($banner->image)
                                        <img src="{{ asset('storage/' . $banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             class="rounded" 
                                             style="width: 100px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 100px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $banner->title }}</strong>
                                    </div>
                                    @if($banner->description)
                                        <small class="text-muted">{{ Str::limit($banner->description, 50) }}</small>
                                    @endif
                                    @if($banner->link)
                                        <div>
                                            <small class="text-primary">
                                                <i class="bi bi-link-45deg"></i> {{ Str::limit($banner->link, 40) }}
                                            </small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($banner->department)
                                        <span class="badge bg-primary">
                                            <i class="bi bi-building"></i> {{ $banner->department->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-globe"></i> Global
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        @switch($banner->position)
                                            @case('hero')
                                                Topo (Hero)
                                                @break
                                            @case('category')
                                                Categorias
                                                @break
                                            @case('product')
                                                Produtos
                                                @break
                                            @case('footer')
                                                Rodapé
                                                @break
                                            @default
                                                {{ ucfirst($banner->position) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        @switch($banner->target_audience)
                                            @case('all')
                                                Todos
                                                @break
                                            @case('b2c')
                                                B2C
                                                @break
                                            @case('b2b')
                                                B2B
                                                @break
                                            @default
                                                {{ strtoupper($banner->target_audience) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $banner->sort_order ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($banner->starts_at || $banner->expires_at)
                                        <small>
                                            @if($banner->starts_at)
                                                <div><i class="bi bi-calendar-check"></i> {{ $banner->starts_at->format('d/m/Y') }}</div>
                                            @endif
                                            @if($banner->expires_at)
                                                <div><i class="bi bi-calendar-x"></i> {{ $banner->expires_at->format('d/m/Y') }}</div>
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">Sempre ativo</small>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.banners.toggle-active', $banner) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $banner->is_active ? 'success' : 'secondary' }}" title="Alterar status">
                                            @if($banner->is_active)
                                                <i class="bi bi-toggle-on"></i> Ativo
                                            @else
                                                <i class="bi bi-toggle-off"></i> Inativo
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este banner?')">
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
                {{ $banners->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum banner encontrado</h5>
                <p class="text-muted">Comece criando seu primeiro banner promocional.</p>
                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Banner
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

