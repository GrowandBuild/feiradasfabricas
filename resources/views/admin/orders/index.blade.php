@extends('admin.layouts.app')

@section('title', 'Pedidos')
@section('page-title', 'Gerenciar Pedidos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Pedidos</h4>
        <p class="text-muted mb-0">Gerencie todos os pedidos da loja</p>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Total de Pedidos</h6>
                        <div class="stats-number">{{ \App\Models\Order::count() }}</div>
                    </div>
                    <i class="bi bi-cart-check" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Pendentes</h6>
                        <div class="stats-number">{{ \App\Models\Order::where('status', 'pending')->count() }}</div>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Em Processamento</h6>
                        <div class="stats-number">{{ \App\Models\Order::where('status', 'processing')->count() }}</div>
                    </div>
                    <i class="bi bi-gear" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Entregues</h6>
                        <div class="stats-number">{{ \App\Models\Order::where('status', 'delivered')->count() }}</div>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nº pedido, cliente, email">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processando</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Enviado</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Pagamento</label>
                <select name="payment_status" class="form-select">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Pago</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Falhou</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipo Cliente</label>
                <select name="customer_type" class="form-select">
                    <option value="">Todos</option>
                    <option value="B2C" {{ request('customer_type') === 'B2C' ? 'selected' : '' }}>B2C</option>
                    <option value="B2B" {{ request('customer_type') === 'B2B' ? 'selected' : '' }}>B2B</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">De</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label">Até</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Pedidos -->
<div class="card">
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Itens</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pagamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>
                                <td>
                                    @if($order->customer)
                                        <div>
                                            <strong>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong>
                                            @if($order->customer->type === 'B2B')
                                                <span class="badge bg-info ms-1">B2B</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $order->customer->email }}</small>
                                        @if($order->customer->company_name)
                                            <div><small class="text-muted">{{ $order->customer->company_name }}</small></div>
                                        @endif
                                    @else
                                        <div>
                                            <strong>Cliente não encontrado</strong>
                                        </div>
                                        <small class="text-muted">Dados do cliente indisponíveis</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->orderItems->count() }} itens</span>
                                </td>
                                <td>
                                    <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pendente',
                                            'processing' => 'Processando',
                                            'shipped' => 'Enviado',
                                            'delivered' => 'Entregue',
                                            'cancelled' => 'Cancelado'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $paymentColors = [
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'partial' => 'info',
                                            'refunded' => 'secondary',
                                            'failed' => 'danger'
                                        ];
                                        $paymentLabels = [
                                            'pending' => 'Pendente',
                                            'paid' => 'Pago',
                                            'partial' => 'Parcial',
                                            'refunded' => 'Reembolsado',
                                            'failed' => 'Falhou'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $paymentColors[$order->payment_status] ?? 'secondary' }}">
                                        {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="btn btn-outline-info btn-sm" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.print', $order) }}" 
                                           class="btn btn-outline-secondary btn-sm" title="Imprimir" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum pedido encontrado</h5>
                <p class="text-muted">Os pedidos aparecerão aqui quando os clientes fizerem compras.</p>
            </div>
        @endif
    </div>
</div>
@endsection

