@extends('layouts.app')

@section('title', 'Detalhes do Pedido #' . $order->order_number)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-shopping-bag me-2"></i>
                    Pedido #{{ $order->order_number }}
                </h1>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voltar aos Pedidos
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informações do Pedido -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informações do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Número do Pedido:</strong> {{ $order->order_number }}</p>
                            <p><strong>Data do Pedido:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $order->getStatusColor() }}">
                                    {{ $order->getStatusLabel() }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status do Pagamento:</strong> 
                                <span class="badge bg-{{ $order->getPaymentStatusColor() }}">
                                    {{ $order->getPaymentStatusLabel() }}
                                </span>
                            </p>
                            <p><strong>Status da Entrega:</strong> 
                                <span class="badge bg-{{ $order->getShippingStatusColor() }}">
                                    {{ $order->getShippingStatusLabel() }}
                                </span>
                            </p>
                            <p><strong>Método de Pagamento:</strong> {{ $order->getPaymentMethodLabel() }}</p>
                        </div>
                    </div>
                    
                    @if($order->notes)
                        <hr>
                        <p><strong>Observações:</strong></p>
                        <p class="text-muted">{{ $order->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Itens do Pedido</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço Unit.</th>
                                    <th>Quantidade</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->images && count($item->product->images) > 0)
                                                    <img src="{{ asset('storage/' . $item->product->images[0]) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <img src="{{ asset('images/no-image.png') }}" 
                                                         alt="Sem imagem" 
                                                         class="me-3" 
                                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                    <small class="text-muted">
                                                        SKU: {{ $item->product->sku }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>R$ {{ number_format($item->total, 2, ',', '.') }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo e Ações -->
        <div class="col-lg-4">
            <!-- Resumo Financeiro -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Resumo Financeiro</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <span>R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto:</span>
                        <span class="text-success">- R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Impostos:</span>
                        <span>R$ {{ number_format($order->tax_amount, 2, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-0">
                        <strong>Total:</strong>
                        <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Endereço de Entrega -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Endereço de Entrega</h5>
                </div>
                <div class="card-body">
                    <address class="mb-0">
                        <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong><br>
                        @if($order->shipping_company)
                            {{ $order->shipping_company }}<br>
                        @endif
                        {{ $order->shipping_address }}, {{ $order->shipping_number }}<br>
                        @if($order->shipping_complement)
                            {{ $order->shipping_complement }}<br>
                        @endif
                        {{ $order->shipping_neighborhood }}<br>
                        {{ $order->shipping_city }} - {{ $order->shipping_state }}<br>
                        CEP: {{ $order->shipping_zip_code }}<br>
                        <i class="fas fa-phone me-1"></i> {{ $order->shipping_phone }}
                    </address>
                </div>
            </div>

            <!-- Endereço de Cobrança (se diferente) -->
            @if($order->billing_address !== $order->shipping_address)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Endereço de Cobrança</h5>
                    </div>
                    <div class="card-body">
                        <address class="mb-0">
                            <strong>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</strong><br>
                            @if($order->billing_company)
                                {{ $order->billing_company }}<br>
                            @endif
                            {{ $order->billing_address }}, {{ $order->billing_number }}<br>
                            @if($order->billing_complement)
                                {{ $order->billing_complement }}<br>
                            @endif
                            {{ $order->billing_neighborhood }}<br>
                            {{ $order->billing_city }} - {{ $order->billing_state }}<br>
                            CEP: {{ $order->billing_zip_code }}
                        </address>
                    </div>
                </div>
            @endif

            <!-- Ações -->
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Ações</h6>
                    
                    @if(in_array($order->status, ['pending', 'processing']))
                        <button class="btn btn-outline-danger w-100 mb-2" id="cancel-order">
                            <i class="fas fa-times me-2"></i>
                            Cancelar Pedido
                        </button>
                    @endif
                    
                    @if($order->status === 'delivered')
                        <button class="btn btn-outline-success w-100 mb-2" id="reorder">
                            <i class="fas fa-redo me-2"></i>
                            Reordenar
                        </button>
                    @endif
                    
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar aos Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    
    // Cancelar pedido
    document.getElementById('cancel-order')?.addEventListener('click', function() {
        if (confirm('Tem certeza que deseja cancelar este pedido?')) {
            cancelOrder();
        }
    });

    // Reordenar
    document.getElementById('reorder')?.addEventListener('click', function() {
        reorderOrder();
    });

    function cancelOrder() {
        loadingModal.show();
        
        fetch(`{{ route('orders.cancel', $order) }}`, {
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

    function reorderOrder() {
        loadingModal.show();
        
        fetch(`{{ route('orders.reorder', $order) }}`, {
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
