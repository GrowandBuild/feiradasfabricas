@extends('layouts.app')

@section('title', 'Meus Pedidos')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-shopping-bag me-2"></i>
                Meus Pedidos
            </h1>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('orders.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Número do pedido ou observações">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos os status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Pendente
                                    </option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                                        Processando
                                    </option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>
                                        Enviado
                                    </option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                                        Entregue
                                    </option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        Cancelado
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        Limpar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                        <th>Pagamento</th>
                                        <th>Total</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>#{{ $order->order_number }}</strong>
                                                    @if($order->notes)
                                                        <br>
                                                        <small class="text-muted">{{ Str::limit($order->notes, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $order->created_at->format('d/m/Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->getStatusColor() }}">
                                                    {{ $order->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                                                    {{ $order->getPaymentStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('orders.show', $order) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if(in_array($order->status, ['pending', 'processing']))
                                                        <button class="btn btn-outline-danger btn-sm cancel-order" 
                                                                data-order-id="{{ $order->id }}"
                                                                title="Cancelar pedido">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($order->status === 'delivered')
                                                        <button class="btn btn-outline-success btn-sm reorder" 
                                                                data-order-id="{{ $order->id }}"
                                                                title="Reordenar">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Nenhum pedido encontrado</h4>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['search', 'status']))
                                Tente ajustar os filtros de busca
                            @else
                                Você ainda não fez nenhum pedido
                            @endif
                        </p>
                        <a href="{{ route('products') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Ver Produtos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Loading Modal -->
@if(false)
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mb-0">Processando...</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    
    // Cancelar pedido
    document.querySelectorAll('.cancel-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            cancelOrder(orderId);
        });
    });

    // Reordenar
    document.querySelectorAll('.reorder').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            reorderOrder(orderId);
        });
    });

    function cancelOrder(orderId) {
        if (!confirm('Tem certeza que deseja cancelar este pedido?')) {
            return;
        }
        
        loadingModal.show();
        
        fetch(`/pedidos/${orderId}/cancelar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            loadingModal.hide();
            showAlert('error', 'Erro ao cancelar pedido');
        });
    }

    function reorderOrder(orderId) {
        loadingModal.show();
        
        fetch(`/pedidos/${orderId}/reordenar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            
            if (data.success) {
                showAlert('success', data.message);
                if (data.redirect_url) {
                    setTimeout(() => window.location.href = data.redirect_url, 1500);
                }
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            loadingModal.hide();
            showAlert('error', 'Erro ao reordenar');
        });
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.querySelector('.container');
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>
@endpush
