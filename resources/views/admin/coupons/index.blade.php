@extends('admin.layouts.app')

@section('title', 'Cupons')
@section('page-title', 'Gerenciar Cupons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Cupons de Desconto</h4>
        <p class="text-muted mb-0">Gerencie todos os cupons de desconto</p>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Cupom
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Código ou nome do cupom">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirados</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="type" class="form-label">Tipo</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Todos</option>
                    <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Porcentagem</option>
                    <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Cupons -->
<div class="card">
    <div class="card-body">
        @if($coupons->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Uso</th>
                            <th>Status</th>
                            <th>Válido Até</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                                </td>
                                <td>
                                    <strong>{{ $coupon->name }}</strong>
                                    @if($coupon->description)
                                        <br><small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $coupon->type === 'percentage' ? 'info' : 'warning' }}">
                                        {{ $coupon->type === 'percentage' ? 'Porcentagem' : 'Valor Fixo' }}
                                    </span>
                                </td>
                                <td>
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        R$ {{ number_format($coupon->value, 2, ',', '.') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $coupon->usages_count ?? 0 }} / {{ $coupon->usage_limit ?: '∞' }}
                                    </span>
                                </td>
                                <td>
                                    @if($coupon->is_active)
                                        @if($coupon->expires_at && $coupon->expires_at < now())
                                            <span class="badge bg-danger">Expirado</span>
                                        @elseif($coupon->starts_at && $coupon->starts_at > now())
                                            <span class="badge bg-warning">Aguardando</span>
                                        @else
                                            <span class="badge bg-success">Ativo</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->expires_at)
                                        {{ $coupon->expires_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Sem expiração</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                           class="btn btn-outline-warning btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este cupom?')">
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
                {{ $coupons->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-ticket-perforated display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Nenhum cupom encontrado</h4>
                <p class="text-muted">Comece criando seu primeiro cupom de desconto.</p>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Primeiro Cupom
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
