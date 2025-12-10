@extends('layouts.app')

@section('title', 'Carrinho de Compras')

@section('styles')
<style>
:root {
    --primary-color: #0f172a;
    --secondary-color: #ff6b35;
    --accent-color: #0f172a;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
}

.cart-page {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
}

.cart-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1e293b 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    box-shadow: var(--shadow-lg);
}

.cart-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 2.5rem;
    margin: 0;
    color: white;
    text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}

.cart-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.cart-card:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
}

.product-image-container {
    position: relative;
    border: 2px solid var(--border-color);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.product-image-container:hover {
    border-color: var(--accent-color);
    box-shadow: var(--shadow-md);
    transform: scale(1.02);
}

.product-image {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.product-image:hover {
    transform: scale(1.05);
}

.no-image-placeholder {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 2px dashed var(--border-color);
    color: var(--text-muted);
}

.loading-image {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.quantity-controls {
    background: white;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}

.quantity-controls:focus-within {
    border-color: var(--accent-color);
    box-shadow: var(--shadow-md);
}

.quantity-btn {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: none;
    color: var(--text-dark);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 600;
}

.quantity-btn:hover {
    background: linear-gradient(135deg, var(--accent-color) 0%, #1e293b 100%);
    color: white;
    transform: scale(1.05);
}

.quantity-input {
    border: none;
    font-weight: 600;
    text-align: center;
    background: transparent;
}

.quantity-input:focus {
    box-shadow: none;
    border: none;
}

.btn-modern {
    border-radius: var(--radius-lg);
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    box-shadow: var(--shadow-md);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-danger-modern {
    background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
    color: white;
}

.btn-danger-modern:hover {
    background: linear-gradient(135deg, #dc2626 0%, var(--danger-color) 100%);
    color: white;
}

.btn-primary-modern {
    background: linear-gradient(135deg, var(--accent-color) 0%, #1e293b 100%);
    color: white;
}

.btn-primary-modern:hover {
    background: linear-gradient(135deg, #1e293b 0%, var(--accent-color) 100%);
    color: white;
}

.btn-secondary-modern {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
}

.btn-secondary-modern:hover {
    background: linear-gradient(135deg, #4b5563 0%, #6b7280 100%);
    color: white;
}

.price-text {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    color: #1f2937;
    font-size: 1.1rem;
}

.total-text {
    font-family: 'Poppins', sans-serif;
    font-weight: 800;
    color: #111827;
    font-size: 1.2rem;
}

.product-name {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.sku-text {
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    color: #6b7280;
    font-size: 0.875rem;
}

.summary-card {
    background: linear-gradient(135deg, white 0%, #f8fafc 100%);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.summary-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1e293b 100%);
    color: white;
    padding: 1.5rem;
    margin: -1px -1px 0 -1px;
}

.summary-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    margin: 0;
    font-size: 1.25rem;
    color: white;
}

.empty-cart {
    background: linear-gradient(135deg, white 0%, #f8fafc 100%);
    border: 2px dashed var(--border-color);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.empty-cart-icon {
    color: var(--text-muted);
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.7;
}

.empty-cart-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    color: var(--text-muted);
    font-size: 1.5rem;
}

.empty-cart-text {
    font-family: 'Inter', sans-serif;
    color: var(--text-muted);
    font-size: 1rem;
}

/* Responsividade Mobile */
@media (max-width: 768px) {
    .cart-page {
        padding: 0;
    }

    .cart-header {
        padding: 1.5rem 0;
        margin-bottom: 1.5rem;
        border-radius: 0;
    }

    .cart-title {
        font-size: 1.75rem;
    }

    .container.py-5 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    /* Layout mobile - resumo primeiro */
    .col-lg-8 {
        order: 2;
        margin-top: 1.5rem;
    }

    .col-lg-4 {
        order: 1;
    }

    .cart-card {
        border-radius: var(--radius-lg);
    }

    .card-body.p-4 {
        padding: 1rem !important;
    }

    /* Tabela mobile - tornar mais compacta */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        font-size: 0.9rem;
    }

    .table thead th {
        font-size: 0.85rem;
        padding: 0.75rem 0.5rem;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 1rem 0.5rem;
        vertical-align: middle;
    }

    .product-image-container {
        width: 60px !important;
        height: 60px !important;
    }

    .product-name {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .sku-text {
        font-size: 0.75rem;
    }

    .price-text {
        font-size: 1rem;
    }

    .total-text {
        font-size: 1.1rem;
    }

    .quantity-controls {
        width: 120px !important;
    }

    .quantity-btn {
        padding: 0.4rem 0.6rem;
        font-size: 0.75rem;
    }

    .quantity-input {
        padding: 0.4rem 0.5rem;
        font-size: 0.85rem;
    }

    .btn-modern {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }

    /* Resumo mobile */
    .summary-card {
        border-radius: var(--radius-lg);
    }

    .summary-header {
        padding: 1rem;
    }

    .summary-title {
        font-size: 1.1rem;
    }

    .card-body.p-4 {
        padding: 1.25rem !important;
    }

    .empty-cart {
        padding: 2rem 1.5rem;
    }

    .empty-cart-icon {
        font-size: 3rem;
    }

    .empty-cart-title {
        font-size: 1.25rem;
    }

    .empty-cart-text {
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .cart-header {
        padding: 1.25rem 0;
    }

    .cart-title {
        font-size: 1.5rem;
    }

    .cart-title i {
        font-size: 1.25rem;
    }

    .table {
        font-size: 0.85rem;
    }

    .table thead th {
        font-size: 0.75rem;
        padding: 0.6rem 0.4rem;
    }

    .table tbody td {
        padding: 0.75rem 0.4rem;
    }

    .product-image-container {
        width: 50px !important;
        height: 50px !important;
    }

    .product-name {
        font-size: 0.85rem;
    }

    .sku-text {
        font-size: 0.7rem;
    }

    .quantity-controls {
        width: 110px !important;
    }

    .quantity-btn {
        padding: 0.35rem 0.5rem;
    }

    .quantity-input {
        padding: 0.35rem 0.4rem;
        font-size: 0.8rem;
    }

    .price-text {
        font-size: 0.95rem;
    }

    .total-text {
        font-size: 1rem;
    }

    .summary-header {
        padding: 0.9rem;
    }

    .summary-title {
        font-size: 1rem;
    }

    .card-body.p-4 {
        padding: 1rem !important;
    }

    .btn-modern {
        padding: 0.6rem 0.9rem;
        font-size: 0.85rem;
    }
}
</style>
@endsection

@section('content')
<div class="cart-page">
    <div class="cart-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="cart-title">
                        <i class="fas fa-shopping-cart me-3"></i>
                        Carrinho de Compras
                    </h1>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container py-5">

    @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="cart-card">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                                        <th class="border-0 py-3" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #111827;">Produto</th>
                                        <th class="border-0 py-3" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #111827;">Preço</th>
                                        <th class="border-0 py-3" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #111827;">Quantidade</th>
                                        <th class="border-0 py-3" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #111827;">Total</th>
                                        <th class="border-0 py-3" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #111827;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    @foreach($cartItems as $item)
                                        <tr data-item-id="{{ $item->id }}" class="border-0">
                                            <td class="py-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="product-image-container me-3" style="width: 70px; height: 70px; border-radius: var(--radius-lg); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                        <img src="{{ $item->display_image }}" 
                                                             alt="{{ $item->display_name }}" 
                                                             class="product-image" 
                                                             style="width: 100%; height: 100%; object-fit: cover;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                             loading="lazy">
                                                        <div class="no-image-placeholder" style="display: none; flex-direction: column; align-items: center; justify-content: center; font-size: 12px;">
                                                            <i class="fas fa-image mb-1"></i>
                                                            <span>Sem imagem</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="product-name mb-1">
                                                            {{ $item->display_name }}
                                                            @if($item->variation)
                                                                <small class="variation-attributes d-block mt-1" style="font-size: 0.85rem; color: var(--text-muted, #64748b); font-weight: 400;">
                                                                    {{ $item->variation->attributes_string }}
                                                                </small>
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted">SKU: {{ $item->display_sku }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <span class="price-text">R$ {{ number_format($item->price, 2, ',', '.') }}</span>
                                            </td>
                                            <td class="py-4">
                                                <div class="quantity-controls d-flex" style="width: 140px;">
                                                    <button class="btn quantity-btn" 
                                                            type="button"
                                                            data-action="decrease" 
                                                            data-item-id="{{ $item->id }}">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="form-control quantity-input" 
                                                           value="{{ $item->quantity }}" 
                                                           min="1" 
                                                           max="999"
                                                           data-item-id="{{ $item->id }}">
                                                    <button class="btn quantity-btn" 
                                                            type="button"
                                                            data-action="increase" 
                                                            data-item-id="{{ $item->id }}">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <span class="total-text">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                            </td>
                                            <td class="py-4">
                                                <button class="btn btn-danger-modern btn-modern remove-item" 
                                                        type="button"
                                                        data-item-id="{{ $item->id }}"
                                                        title="Remover item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-secondary-modern btn-modern" id="clear-cart" type="button">
                        <i class="fas fa-trash me-2"></i>
                        Limpar Carrinho
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="summary-header">
                        <h5 class="summary-title">
                            <i class="fas fa-receipt me-2"></i>
                            Resumo do Pedido
                        </h5>
                    </div>
                    <div class="card-body p-4" style="background: white;">
                        <div class="d-flex justify-content-between mb-3">
                            <span style="font-family: 'Inter', sans-serif; font-weight: 500; color: #374151;">Subtotal:</span>
                            <span id="subtotal" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #1f2937; font-size: 1.1rem;">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        
                        @php
                            $shippingSelection = session('shipping_selection');
                        @endphp
                        @if($shipping > 0)
                        <div class="d-flex justify-content-between mb-3">
                            <span style="font-family: 'Inter', sans-serif; font-weight: 500; color: #374151;">
                                <i class="bi bi-truck me-1"></i>Frete:
                                @if(!empty($shippingSelection['region_name']))
                                    <small class="text-muted d-block" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                        {{ $shippingSelection['region_name'] }}
                                    </small>
                                @elseif(!empty($shippingSelection['service']))
                                    <small class="text-muted d-block" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                        {{ $shippingSelection['service'] }}
                                    </small>
                                @endif
                            </span>
                            <span id="shipping" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #1f2937; font-size: 1rem;">R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span style="font-family: 'Inter', sans-serif; font-weight: 500; color: #374151;">Desconto:</span>
                            <span style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #059669;">R$ 0,00</span>
                        </div>
                        <hr style="border-color: #e5e7eb; margin: 1.5rem 0;">
                        <div class="d-flex justify-content-between mb-4">
                            <strong style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #111827; font-size: 1.1rem;">Total:</strong>
                            <strong id="total" style="font-family: 'Poppins', sans-serif; font-weight: 800; color: #111827; font-size: 1.3rem;">R$ {{ number_format($total, 2, ',', '.') }}</strong>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="btn btn-primary-modern btn-modern w-100 mb-3">
                            <i class="fas fa-credit-card me-2"></i>
                            Finalizar Compra
                        </a>
                        
                        @guest
                            <div class="alert alert-info border-0 mb-3" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: var(--radius-lg); border: 1px solid #3b82f6;">
                                <small style="font-family: 'Inter', sans-serif; font-weight: 500; color: #1e40af;">
                                    <i class="fas fa-info-circle me-1" style="color: #3b82f6;"></i>
                                    Não está logado? Você pode finalizar a compra mesmo assim!
                                </small>
                            </div>
                            <a href="{{ route('customer.login') }}" class="btn btn-outline-primary btn-modern w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Fazer Login (Opcional)
                            </a>
                        @endguest

                        <a href="{{ route('products') }}" class="btn btn-secondary-modern btn-modern w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="empty-cart text-center py-5 px-4">
                    <i class="fas fa-shopping-cart empty-cart-icon"></i>
                    <h4 class="empty-cart-title mb-3">Seu carrinho está vazio</h4>
                    <p class="empty-cart-text mb-4">Adicione alguns produtos para começar sua compra</p>
                    <a href="{{ route('products') }}" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-shopping-bag me-2"></i>
                        Ver Produtos
                    </a>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Classe para gerenciar o carrinho
    class CartPageManager {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            this.setupImageLoading();
            // Frete removido: sem resumo remoto
        }

        setupImageLoading() {
            // Adicionar loading state para imagens
            document.querySelectorAll('.product-image').forEach(img => {
                const container = img.closest('.product-image-container');
                
                // Adicionar classe de loading
                container.classList.add('loading-image');
                
                img.addEventListener('load', () => {
                    container.classList.remove('loading-image');
                    console.log('Imagem carregada:', img.src);
                });
                
                img.addEventListener('error', () => {
                    container.classList.remove('loading-image');
                    console.error('Erro ao carregar imagem:', img.src);
                });
            });
        }

        bindEvents() {
            // Controle de quantidade
            document.querySelectorAll('.quantity-btn').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleQuantityChange(e));
            });

            // Validação de quantidade via input
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', (e) => this.validateQuantity(e));
            });

            // Remover item
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleRemoveItem(e));
            });

            // Limpar carrinho
            const clearBtn = document.getElementById('clear-cart');
            if (clearBtn) {
                clearBtn.addEventListener('click', (e) => this.handleClearCart(e));
            }
        }

        handleQuantityChange(event) {
            const button = event.currentTarget;
            const action = button.dataset.action;
            const itemId = button.dataset.itemId;
            const input = document.querySelector(`input[data-item-id="${itemId}"]`);
            
            if (!input) return;

            let newQuantity = parseInt(input.value);
            
            if (action === 'increase') {
                newQuantity = Math.min(newQuantity + 1, 999);
            } else if (action === 'decrease') {
                newQuantity = Math.max(1, newQuantity - 1);
            }
            
            input.value = newQuantity;
            this.updateQuantity(itemId, newQuantity);
        }

        validateQuantity(event) {
            const input = event.currentTarget;
            const quantity = parseInt(input.value);
            const itemId = input.dataset.itemId;
            
            if (quantity < 1) {
                input.value = 1;
            } else if (quantity > 999) {
                input.value = 999;
            }
            
            this.updateQuantity(itemId, parseInt(input.value));
        }

        handleRemoveItem(event) {
            const button = event.currentTarget;
            const itemId = button.dataset.itemId;
            
            if (confirm('Tem certeza que deseja remover este item?')) {
                this.removeItem(itemId);
            }
        }

        handleClearCart(event) {
            if (confirm('Tem certeza que deseja limpar o carrinho?')) {
                this.clearCart();
            }
        }

        async updateQuantity(itemId, quantity) {
            try {
                const response = await this.makeRequest(`{{ route('cart.update', '') }}/${itemId}`, 'PUT', {
                    quantity: quantity
                });

                if (response.success) {
                    this.updateCartInterface(response);
                } else {
                    this.showNotification('error', response.message);
                }
            } catch (error) {
                this.showNotification('error', 'Erro ao atualizar quantidade');
            }
        }

        async removeItem(itemId) {
            try {
                const response = await this.makeRequest(`{{ route('cart.remove', '') }}/${itemId}`, 'DELETE');

                if (response.success) {
                    const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.transform = 'translateX(-100%)';
                        row.style.opacity = '0';
                        
                        setTimeout(() => {
                            row.remove();
                            
                            if (document.querySelectorAll('#cart-items tr').length === 0) {
                                location.reload();
                            } else {
                                this.updateCartInterface(response);
                            }
                        }, 300);
                    }
                    
                    this.showNotification('success', response.message);
                } else {
                    this.showNotification('error', response.message);
                }
            } catch (error) {
                this.showNotification('error', 'Erro ao remover item');
            }
        }

        async clearCart() {
            try {
                const response = await this.makeRequest('{{ route("cart.clear") }}', 'DELETE');

                if (response.success) {
                    location.reload();
                } else {
                    this.showNotification('error', response.message);
                }
            } catch (error) {
                this.showNotification('error', 'Erro ao limpar carrinho');
            }
        }

        async makeRequest(url, method, data = null) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const options = {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            };

            if (data) {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }

        // Frete removido: método de resumo desativado

            async updateCartInterface(data) {
                // Atualizar contador do carrinho no header
                const cartCount = document.querySelector('.cart-count');
                if (cartCount && data.cart_count !== undefined) {
                    cartCount.textContent = data.cart_count;
                }

                // Atualizar total do item específico
                if (data.item_total !== undefined) {
                    const itemRow = document.querySelector(`tr[data-item-id="${data.item_id}"]`);
                    if (itemRow) {
                        const totalCell = itemRow.querySelector('.total-text');
                        if (totalCell) {
                            totalCell.textContent = `R$ ${data.item_total}`;
                        }
                    }
                }

                // Atualizar resumo usando dados da resposta
                if (data.subtotal !== undefined) {
                    const elSub = document.getElementById('subtotal');
                    if (elSub) elSub.textContent = `R$ ${data.subtotal}`;
                }
                if (data.total !== undefined) {
                    const elTot = document.getElementById('total');
                    if (elTot) elTot.textContent = `R$ ${data.total}`;
                }
            }

        showNotification(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
            
            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    }

    // Inicializar o gerenciador da página do carrinho
    new CartPageManager();
});
</script>
@endpush