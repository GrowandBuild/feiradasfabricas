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
                                <button type="button" 
                                        class="btn btn-primary btn-sm edit-banner-btn" 
                                        title="Editar Banner"
                                        data-banner-id="{{ $mainBanner->id }}"
                                        data-banner-title="{{ $mainBanner->title }}">
                                    <i class="bi bi-pencil"></i> Editar Banner
                                </button>
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

@include('admin.banners.modal-edit')

@section('styles')
<style>
    /* Garantir que o modal fique acima de tudo */
    .modal-backdrop.show {
        z-index: 9998 !important;
        opacity: 0.5;
    }
    
    #editBannerModal {
        z-index: 9999 !important;
    }
    
    #editBannerModal.show {
        display: block !important;
    }
    
    #editBannerModal .modal-dialog {
        max-width: 90%;
        z-index: 10000 !important;
        margin: 1.75rem auto;
    }
    
    #editBannerModal .modal-content {
        position: relative;
        z-index: 10001 !important;
    }
    
    #editBannerModal .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    #editBannerModal .form-control-color {
        width: 50px;
        height: 40px;
    }
    
    #editBannerModal .nav-tabs .nav-link {
        color: #495057;
    }
    
    #editBannerModal .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
</style>
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

// Script para o modal de edição de banner
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editBannerModal'));
    const modalBody = document.getElementById('editBannerModalBody');
    const toastEl = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');
    const toast = new bootstrap.Toast(toastEl);

    // Event listener para botões de editar
    document.querySelectorAll('.edit-banner-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bannerId = this.dataset.bannerId;
            const bannerTitle = this.dataset.bannerTitle;
            
            // Atualizar título do modal
            document.getElementById('editBannerModalLabel').innerHTML = 
                `<i class="bi bi-pencil"></i> Editar Banner: ${bannerTitle}`;
            
            // Mostrar loading
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-3 text-muted">Carregando formulário...</p>
                </div>
            `;
            
            // Abrir modal
            editModal.show();
            
            // Carregar formulário via AJAX
            fetch(`{{ route('admin.banners.edit', ':id') }}`.replace(':id', bannerId), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar formulário');
                }
                return response.text();
            })
            .then(html => {
                modalBody.innerHTML = html;
                
                // Inicializar abas do Bootstrap se existirem
                const tabElements = modalBody.querySelectorAll('[data-bs-toggle="tab"]');
                if (tabElements.length > 0) {
                    tabElements.forEach(tab => {
                        tab.addEventListener('click', function(e) {
                            e.preventDefault();
                            const target = this.getAttribute('data-bs-target');
                            const tabPane = modalBody.querySelector(target);
                            if (tabPane) {
                                // Remover active de todas as abas
                                modalBody.querySelectorAll('.nav-link').forEach(link => {
                                    link.classList.remove('active');
                                });
                                modalBody.querySelectorAll('.tab-pane').forEach(pane => {
                                    pane.classList.remove('active', 'show');
                                });
                                // Adicionar active na aba clicada
                                this.classList.add('active');
                                tabPane.classList.add('active', 'show');
                            }
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Erro ao carregar formulário:</strong> ${error.message}
                    </div>
                `;
            });
        });
    });

    // Event listener para submit do formulário (usando delegação de eventos)
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'banner-edit-form') {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Desabilitar botão e mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
            
            // Enviar via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw { data, status: response.status };
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso
                    toastMessage.innerHTML = data.message;
                    toastEl.querySelector('.toast-header i').className = 'bi bi-check-circle-fill text-success me-2';
                    toast.show();
                    
                    // Fechar modal
                    editModal.hide();
                    
                    // Recarregar página após 1 segundo para atualizar
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Mostrar erros
                    let errorHtml = '<ul class="mb-0">';
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            data.errors[key].forEach(error => {
                                errorHtml += `<li>${error}</li>`;
                            });
                        });
                    } else {
                        errorHtml += `<li>${data.message || 'Erro desconhecido'}</li>`;
                    }
                    errorHtml += '</ul>';
                    
                    // Remover erros anteriores se existirem
                    const existingError = form.querySelector('.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Mostrar erros no topo do formulário
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                    errorDiv.innerHTML = `
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro ao atualizar banner:</strong>
                        ${errorHtml}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.insertBefore(errorDiv, form.firstChild);
                    
                    // Scroll para o topo do formulário
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Reabilitar botão
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                
                // Se for erro de validação (tem data.errors)
                if (error.data && error.data.errors) {
                    // Mostrar erros de validação
                    let errorHtml = '<ul class="mb-0">';
                    Object.keys(error.data.errors).forEach(key => {
                        error.data.errors[key].forEach(err => {
                            errorHtml += `<li>${err}</li>`;
                        });
                    });
                    errorHtml += '</ul>';
                    
                    // Remover erros anteriores se existirem
                    const existingError = form.querySelector('.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Mostrar erros no topo do formulário
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                    errorDiv.innerHTML = `
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro de validação:</strong>
                        ${errorHtml}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.insertBefore(errorDiv, form.firstChild);
                } else {
                    // Erro genérico
                    // Remover erros anteriores se existirem
                    const existingError = form.querySelector('.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // Mostrar erro no formulário
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                    errorDiv.innerHTML = `
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro ao salvar banner:</strong>
                        <ul class="mb-0"><li>${error.data?.message || error.message || 'Erro ao salvar banner. Tente novamente.'}</li></ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    form.insertBefore(errorDiv, form.firstChild);
                }
                
                // Scroll para o topo do formulário
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Reabilitar botão
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    });

    // Limpar conteúdo do modal ao fechar
    editModal._element.addEventListener('hidden.bs.modal', function() {
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-3 text-muted">Carregando formulário...</p>
            </div>
        `;
    });
});
</script>
@endsection
