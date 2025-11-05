@extends('admin.layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Gerenciar Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Clientes</h4>
        <p class="text-muted mb-0">Gerencie todos os clientes da loja</p>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Total de Clientes</h6>
                        <div class="stats-number">{{ \App\Models\Customer::count() }}</div>
                    </div>
                    <i class="bi bi-people" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">B2B</h6>
                        <div class="stats-number">{{ \App\Models\Customer::where('type', 'B2B')->count() }}</div>
                    </div>
                    <i class="bi bi-building" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Ativos</h6>
                        <div class="stats-number">{{ \App\Models\Customer::where('is_active', true)->count() }}</div>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">B2B Pendentes</h6>
                        <div class="stats-number">{{ \App\Models\Customer::where('type', 'B2B')->where('b2b_status', 'pending')->count() }}</div>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nome, email, empresa, CNPJ">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select">
                    <option value="">Todos</option>
                    <option value="B2C" {{ request('type') === 'B2C' ? 'selected' : '' }}>B2C</option>
                    <option value="B2B" {{ request('type') === 'B2B' ? 'selected' : '' }}>B2B</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status B2B</label>
                <select name="b2b_status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('b2b_status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="approved" {{ request('b2b_status') === 'approved' ? 'selected' : '' }}>Aprovado</option>
                    <option value="rejected" {{ request('b2b_status') === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Ativo</label>
                <select name="is_active" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Sim</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Não</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Clientes -->
<div class="card">
    <div class="card-body">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Empresa</th>
                            <th>Pedidos</th>
                            <th>Status B2B</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                    @if($customer->phone)
                                        <div><small class="text-muted"><i class="bi bi-telephone"></i> {{ $customer->phone }}</small></div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $customer->type === 'B2B' ? 'info' : 'secondary' }}">
                                        {{ $customer->type }}
                                    </span>
                                </td>
                                <td>
                                    @if($customer->company_name)
                                        <div><strong>{{ $customer->company_name }}</strong></div>
                                        @if($customer->cnpj)
                                            <small class="text-muted">CNPJ: {{ $customer->cnpj }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $customer->orders_count }} pedidos</span>
                                </td>
                                <td>
                                    @if($customer->type === 'B2B')
                                        @php
                                            $b2bColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $b2bLabels = [
                                                'pending' => 'Pendente',
                                                'approved' => 'Aprovado',
                                                'rejected' => 'Rejeitado'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $b2bColors[$customer->b2b_status] ?? 'secondary' }}">
                                            {{ $b2bLabels[$customer->b2b_status] ?? $customer->b2b_status }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                                        {{ $customer->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.customers.show', $customer) }}" 
                                           class="btn btn-outline-info btn-sm" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($customer->type === 'B2B')
                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                                title="Status B2B" data-bs-toggle="modal" 
                                                data-bs-target="#b2bModal{{ $customer->id }}">
                                            <i class="bi bi-building-gear"></i>
                                        </button>
                                        @endif
                                        <form action="{{ route('admin.customers.toggle-active', $customer) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-{{ $customer->is_active ? 'danger' : 'success' }} btn-sm" 
                                                    title="{{ $customer->is_active ? 'Desativar' : 'Ativar' }}"
                                                    onclick="return confirm('Tem certeza que deseja {{ $customer->is_active ? 'desativar' : 'ativar' }} este cliente?')">
                                                <i class="bi bi-{{ $customer->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal B2B Status -->
                            @if($customer->type === 'B2B')
                            <div class="modal fade" id="b2bModal{{ $customer->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Status B2B - {{ $customer->company_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.customers.update-b2b-status', $customer) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="b2b_status" class="form-select" required>
                                                        <option value="pending" {{ $customer->b2b_status === 'pending' ? 'selected' : '' }}>Pendente</option>
                                                        <option value="approved" {{ $customer->b2b_status === 'approved' ? 'selected' : '' }}>Aprovado</option>
                                                        <option value="rejected" {{ $customer->b2b_status === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Observações</label>
                                                    <textarea name="b2b_notes" class="form-control" rows="3" 
                                                              placeholder="Adicionar observações sobre o status B2B...">{{ $customer->b2b_notes }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Atualizar Status</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum cliente encontrado</h5>
                <p class="text-muted">Os clientes aparecerão aqui quando se cadastrarem na loja.</p>
            </div>
        @endif
    </div>
</div>
@endsection
