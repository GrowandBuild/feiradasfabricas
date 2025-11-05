@extends('layouts.app')

@section('title', 'Pedido Realizado com Sucesso')

@section('styles')
<style>
    .success-page {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        min-height: 100vh;
        padding: 4rem 0;
    }

    .success-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        max-width: 800px;
        margin: 0 auto;
    }

    .success-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
    }

    .success-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    .success-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .success-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .order-details {
        padding: 2rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .detail-label {
        font-weight: 600;
        color: #374151;
    }

    .detail-value {
        color: #111827;
        text-align: right;
    }

    .order-items {
        margin-top: 2rem;
    }

    .item-row {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 1rem;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .item-sku {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .item-quantity {
        font-weight: 600;
        color: #374151;
        margin-right: 1rem;
    }

    .item-total {
        font-weight: 700;
        color: #111827;
        font-size: 1.1rem;
    }

    .total-section {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 12px;
        margin-top: 2rem;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .final-total {
        font-size: 1.3rem;
        font-weight: 800;
        border-top: 2px solid #e5e7eb;
        padding-top: 1rem;
        margin-top: 1rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-success {
        background: #10b981;
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.3s ease;
    }

    .btn-success:hover {
        background: #059669;
        color: white;
    }

    .btn-outline {
        background: transparent;
        color: #374151;
        border: 2px solid #e5e7eb;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-outline:hover {
        background: #f8fafc;
        border-color: #d1d5db;
        color: #374151;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
</style>
@endsection

@section('content')
<div class="success-page">
    <div class="container">
        <div class="success-card">
            <div class="success-header">
                <i class="fas fa-check-circle success-icon"></i>
                <h1 class="success-title">Pedido Realizado!</h1>
                <p class="success-subtitle">
                    Seu pedido foi processado com sucesso. Você receberá um email de confirmação em breve.
                </p>
            </div>
            
            <div class="order-details">
                <div class="status-badge status-pending">
                    <i class="fas fa-clock me-2"></i>
                    Status: Pendente
                </div>
                
                <h3 class="mb-3">Detalhes do Pedido</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Número do Pedido:</span>
                    <span class="detail-value">{{ $order->order_number }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Data:</span>
                    <span class="detail-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Cliente:</span>
                    <span class="detail-value">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Telefone:</span>
                    <span class="detail-value">{{ $order->shipping_phone }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Endereço:</span>
                    <span class="detail-value">{{ $order->shipping_address }}, {{ $order->shipping_city }}/{{ $order->shipping_state }} - {{ $order->shipping_zip_code }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Método de Pagamento:</span>
                    <span class="detail-value">
                        @switch($order->payment_method)
                            @case('credit_card')
                                <i class="fas fa-credit-card me-1"></i>Cartão de Crédito
                                @break
                            @case('pix')
                                <i class="fas fa-qrcode me-1"></i>PIX
                                @break
                            @case('boleto')
                                <i class="fas fa-barcode me-1"></i>Boleto Bancário
                                @break
                        @endswitch
                    </span>
                </div>

                <div class="order-items">
                    <h4 class="mb-3">Itens do Pedido</h4>
                    
                    @foreach($order->orderItems as $item)
                        <div class="item-row">
                            <img src="{{ $item->product->first_image }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="item-image"
                                 onerror="this.style.display='none'">
                            
                            <div class="item-details">
                                <div class="item-name">{{ $item->product->name }}</div>
                                <div class="item-sku">SKU: {{ $item->product->sku }}</div>
                            </div>
                            
                            <div class="item-quantity">Qtd: {{ $item->quantity }}</div>
                            <div class="item-total">R$ {{ number_format($item->total, 2, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="total-section">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>Frete:</span>
                        <span>Grátis</span>
                    </div>
                    <div class="total-row final-total">
                        <span>Total:</span>
                        <span>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('home') }}" class="btn-success">
                        <i class="fas fa-home me-2"></i>
                        Voltar ao Início
                    </a>
                    
                    <a href="{{ route('products') }}" class="btn-outline">
                        <i class="fas fa-shopping-bag me-2"></i>
                        Continuar Comprando
                    </a>
                </div>

                @if(Auth::guard('customer')->check())
                    <div class="mt-4 text-center">
                        <a href="{{ route('orders.show', $order) }}" class="btn-outline">
                            <i class="fas fa-eye me-2"></i>
                            Ver Pedido Completo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
