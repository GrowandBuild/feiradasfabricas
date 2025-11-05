@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle')
    <p class="text-muted mb-0">Visão geral do sistema</p>
@endsection

@section('content')
<div class="row">
    <!-- Banner Principal -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-image me-2" style="color: var(--accent-color);"></i>
                    <h5 class="card-title mb-0">Banner da Página Principal</h5>
                </div>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-gear"></i> Gerenciar Banners
                </a>
            </div>
            <div class="card-body">
                @php
                    $mainBanner = \App\Models\Banner::where('position', 'hero')
                        ->whereNull('department_id')
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->first();
                @endphp
                
                @if($mainBanner)
                    <div class="row">
                        <div class="col-md-4">
                            <div class="banner-preview">
                                @if(str_starts_with($mainBanner->image, 'http'))
                                    <img src="{{ $mainBanner->image }}" 
                                         alt="{{ $mainBanner->title }}" 
                                         class="img-fluid rounded"
                                         style="max-height: 200px; width: 100%; object-fit: cover;">
                                @else
                                    <img src="{{ asset('storage/' . $mainBanner->image) }}" 
                                         alt="{{ $mainBanner->title }}" 
                                         class="img-fluid rounded"
                                         style="max-height: 200px; width: 100%; object-fit: cover;">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="mb-2">{{ $mainBanner->title }}</h6>
                            @if($mainBanner->description)
                                <p class="text-muted mb-2">{{ $mainBanner->description }}</p>
                            @endif
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.banners.edit', $mainBanner) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Editar Banner
                                </a>
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-plus"></i> Novo Banner
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-2 text-muted">Nenhum banner principal configurado</h6>
                        <p class="text-muted mb-3">Configure o banner que aparece na página inicial do site</p>
                        <a href="{{ route('admin.banners.create') }}?position=hero&department_id=&target_audience=all" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Criar Banner Principal
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Estatísticas Principais -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Total de Pedidos</h6>
                        <h2 class="stats-number">{{ number_format($stats['total_orders']) }}</h2>
                        <small class="text-white-50">Este mês</small>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Receita Total</h6>
                        <h2 class="stats-number">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</h2>
                        <small class="text-white-50">Este mês</small>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Total de Clientes</h6>
                        <h2 class="stats-number">{{ number_format($stats['total_customers']) }}</h2>
                        <small class="text-white-50">Cadastrados</small>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Total de Produtos</h6>
                        <h2 class="stats-number">{{ number_format($stats['total_products']) }}</h2>
                        <small class="text-white-50">Em estoque</small>
                    </div>
                    <div class="stats-icon">
                        <i class="bi bi-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Alertas -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2" style="color: var(--accent-color);"></i>
                <h5 class="card-title mb-0">Alertas</h5>
            </div>
            <div class="card-body">
                @if($stats['pending_orders'] > 0)
                    <div class="alert alert-warning border-0 d-flex align-items-center">
                        <i class="bi bi-clock me-3"></i>
                        <div>
                            <strong>{{ $stats['pending_orders'] }}</strong> pedidos pendentes
                            <small class="d-block text-muted">Requerem atenção</small>
                        </div>
                    </div>
                @endif
                @if($stats['low_stock_products'] > 0)
                    <div class="alert alert-danger border-0 d-flex align-items-center">
                        <i class="bi bi-exclamation-circle me-3"></i>
                        <div>
                            <strong>{{ $stats['low_stock_products'] }}</strong> produtos com estoque baixo
                            <small class="d-block text-muted">Reabastecimento necessário</small>
                        </div>
                    </div>
                @endif
                @if($stats['pending_orders'] == 0 && $stats['low_stock_products'] == 0)
                    <div class="alert alert-success border-0 d-flex align-items-center">
                        <i class="bi bi-check-circle me-3"></i>
                        <div>
                            Nenhum alerta no momento
                            <small class="d-block text-muted">Sistema funcionando normalmente</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Gráfico de Vendas -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-graph-up me-2" style="color: var(--accent-color);"></i>
                <h5 class="card-title mb-0">Vendas por Mês</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Produtos -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-trophy"></i> Top Produtos Vendidos</h5>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Vendas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts->take(5) as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $product->sales_count }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Nenhum produto vendido ainda.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Pedidos Recentes -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Pedidos Recentes</h5>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders->take(5) as $order)
                                    <tr>
                                        <td>#{{ $order->order_number }}</td>
                                        <td>{{ $order->customer ? $order->customer->display_name : 'Cliente não encontrado' }}</td>
                                        <td>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'delivered' ? 'success' : 'info') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Nenhum pedido ainda.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Distribuição de Clientes -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-pie-chart"></i> Distribuição de Clientes</h5>
            </div>
            <div class="card-body">
                <canvas id="customersChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Status dos Pedidos -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-bar-chart"></i> Status dos Pedidos</h5>
            </div>
            <div class="card-body">
                <canvas id="ordersChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Gráfico de Vendas
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesData = @json($salesData);
const salesLabels = @json($salesLabels);

new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [{
            label: 'Vendas (R$)',
            data: salesData,
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Clientes
const customersCtx = document.getElementById('customersChart').getContext('2d');
new Chart(customersCtx, {
    type: 'doughnut',
    data: {
        labels: ['B2C', 'B2B'],
        datasets: [{
            data: [{{ $customersByType['b2c'] }}, {{ $customersByType['b2b'] }}],
            backgroundColor: ['#667eea', '#764ba2']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Gráfico de Status dos Pedidos
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
const ordersData = @json($ordersByStatus);
const ordersLabels = Object.keys(ordersData).map(key => key.charAt(0).toUpperCase() + key.slice(1));
const ordersValues = Object.values(ordersData);

new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: ordersLabels,
        datasets: [{
            label: 'Quantidade',
            data: ordersValues,
            backgroundColor: ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection
