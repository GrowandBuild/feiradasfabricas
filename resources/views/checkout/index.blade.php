@extends('layouts.app')

@section('title', 'Checkout - Finalizar Compra')

@section('styles')
<style>
    /* Design inspirado no Shopee com cores dinâmicas do site */
    .checkout-page {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e0e7ff 100%);
        min-height: 100vh;
        padding: 0;
        padding-bottom: 80px; /* Espaço para bottom nav */
    }

    /* Card principal - estilo Shopee */
    .checkout-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: none;
        margin-bottom: 0.75rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .checkout-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    /* Header do card */
    .checkout-header {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        color: var(--text-light);
        padding: 1.25rem 1.5rem;
        border-bottom: none;
        position: relative;
        overflow: hidden;
    }

    .checkout-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        50% { transform: translate(-50%, -50%) rotate(180deg); }
    }

    .checkout-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .checkout-title i {
        font-size: 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem;
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    /* Seções do formulário */
    .checkout-section {
        background: #ffffff;
        margin-bottom: 0.75rem;
        border-radius: 0;
    }

    .checkout-section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-dark);
        padding: 1.25rem 1.5rem;
        margin: 0;
        border-bottom: 2px solid #f0f0f0;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .checkout-section-title i {
        color: var(--secondary-color);
        font-size: 1.25rem;
    }

    .checkout-section-content {
        padding: 1.25rem;
    }

    /* Labels e inputs - estilo Shopee */
    .form-label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.9375rem;
    }

    .form-label .text-danger {
        color: var(--danger-color) !important;
    }

    .form-control {
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        font-size: 0.9375rem;
        background: #ffffff;
        color: var(--text-dark);
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 2px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #999;
        opacity: 1;
    }

    /* Botão principal - estilo Shopee */
    .btn-checkout {
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-color) 100%);
        color: #ffffff;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 1rem;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-checkout:hover,
    .btn-checkout:active {
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), black 10%) 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-checkout:disabled {
        background: #e0e0e0;
        color: #999;
        cursor: not-allowed;
        transform: none;
    }

    /* Resumo do pedido - estilo Shopee */
    .order-summary {
        background: #ffffff;
        border: none;
        border-radius: 0;
        padding: 0;
    }

    .order-summary-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid #f0f0f0;
        background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        color: var(--text-light);
    }

    .order-summary-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-light);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .order-summary-title i {
        font-size: 1.25rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem;
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    .order-summary-content {
        padding: 1.25rem;
    }

    .order-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
        background: #ffffff;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .order-item:hover {
        background: #f9f9f9;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    .order-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .order-item-image {
        width: 80px;
        height: 80px;
        min-width: 80px;
        border-radius: 8px;
        overflow: hidden;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .order-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-item-image .no-image {
        color: #999;
        font-size: 1.5rem;
    }

    .order-item-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .order-item-name {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9375rem;
        line-height: 1.4;
    }

    .order-item-attributes {
        font-size: 0.8125rem;
        color: var(--secondary-color);
        font-weight: 500;
        background: rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-block;
        width: fit-content;
    }

    .order-item-quantity {
        font-size: 0.8125rem;
        color: #666;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #f0f0f0;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        width: fit-content;
        margin-top: 0.25rem;
    }

    .order-item-quantity::before {
        content: '✕';
        font-weight: 600;
    }

    .order-item-price {
        font-weight: 700;
        color: var(--secondary-color);
        font-size: 1.125rem;
        white-space: nowrap;
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.875rem 1rem;
        font-size: 0.9375rem;
        color: var(--text-dark);
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .summary-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }

    .summary-item:first-child {
        margin-top: 0.5rem;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 800;
        font-size: 1.25rem;
        border-top: 3px solid var(--secondary-color);
        padding: 1.25rem 1rem;
        margin-top: 1rem;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1) 0%, rgba(var(--primary-color-rgb, 15, 23, 42), 0.05) 100%);
        border-radius: 12px;
        color: var(--text-dark);
    }

    .summary-total .total-value {
        color: var(--secondary-color);
        font-size: 1.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Métodos de pagamento - estilo Shopee */
    .payment-method {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .payment-method:hover {
        border-color: var(--secondary-color);
        background: #fff9f7;
    }

    .payment-method.selected {
        border-color: var(--secondary-color);
        background: #fff9f7;
        box-shadow: 0 0 0 2px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1);
    }

    .payment-method input[type="radio"] {
        margin: 0;
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--secondary-color);
    }

    .payment-method label {
        margin: 0;
        cursor: pointer;
        flex: 1;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        color: var(--text-dark);
        font-size: 0.9375rem;
    }

    .payment-method-icon {
        font-size: 1.5rem;
        color: var(--secondary-color);
    }

    /* Alerts e mensagens */
    .alert {
        border-radius: 4px;
        border: none;
        padding: 0.875rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }

    .alert-info {
        background: #e3f2fd;
        color: #1976d2;
        border-left: 4px solid #2196f3;
    }

    /* Botão voltar */
    .btn-back {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        color: var(--text-dark);
        padding: 0.75rem 1.25rem;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-back:hover {
        background: #f5f5f5;
        border-color: var(--secondary-color);
        color: var(--secondary-color);
    }

    /* Shipping widget */
    .shipping-widget {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
    }

    .shipping-widget .list-group-item {
        cursor: pointer;
        border: none;
        border-bottom: 1px solid #f0f0f0;
        padding: 1rem 1.25rem;
        transition: background 0.2s ease;
    }

    .shipping-widget .list-group-item:last-child {
        border-bottom: none;
    }

    .shipping-widget .list-group-item:hover {
        background: #f9f9f9;
    }

    .shipping-widget .list-group-item[aria-checked="true"] {
        background: #fff9f7;
        border-left: 4px solid var(--secondary-color);
    }

    /* Texto do frete */
    #shipping-detail-text {
        font-size: 0.8125rem;
        color: #666;
        margin-top: 0.5rem;
        padding-left: 0.25rem;
    }

    /* Responsividade Mobile - estilo Shopee */
    @media (max-width: 992px) {
        .checkout-page {
            padding-bottom: 70px;
        }

        /* Layout mobile - resumo primeiro */
        .checkout-row-mobile .col-lg-8 {
            order: 2;
            margin-top: 0.75rem;
        }

        .checkout-row-mobile .col-lg-4 {
            order: 1;
        }
    }

    @media (max-width: 768px) {
        .checkout-page {
            padding: 0;
            padding-bottom: 70px;
        }

        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .checkout-card {
            margin-bottom: 0.5rem;
        }

        .checkout-header {
            padding: 0.875rem 1rem;
        }

        .checkout-title {
            font-size: 1rem;
        }

        .checkout-section-title {
            font-size: 0.9375rem;
            padding: 0.875rem 1rem;
        }

        .checkout-section-content {
            padding: 1rem;
        }

        .form-label {
            font-size: 0.875rem;
        }

        .form-control {
            padding: 0.6875rem 0.875rem;
            font-size: 0.875rem;
        }

        .payment-method {
            padding: 0.875rem 1rem;
            margin-bottom: 0.625rem;
        }

        .payment-method label {
            font-size: 0.875rem;
        }

        .payment-method-icon {
            font-size: 1.25rem;
        }

        .btn-checkout {
            padding: 1rem 1.25rem;
            font-size: 0.9375rem;
            border-radius: 0;
            position: sticky;
            bottom: 0;
            z-index: 100;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
        }

        .order-summary-header {
            padding: 0.875rem 1rem;
        }

        .order-summary-content {
            padding: 1rem;
        }

        .order-item {
            padding: 0.875rem 0;
        }

        .order-item-name {
            font-size: 0.875rem;
        }

        .order-item-price {
            font-size: 0.875rem;
        }

        .summary-item {
            font-size: 0.875rem;
            padding: 0.625rem 0;
        }

        .summary-total {
            font-size: 1rem;
            padding-top: 0.875rem;
        }

        .summary-total .total-value {
            font-size: 1.125rem;
        }

        .row .col-md-6,
        .row .col-md-3,
        .row .col-md-4,
        .row .col-md-8 {
            margin-bottom: 0.75rem;
        }

        /* Garantir full width em mobile */
        .row .col-md-6,
        .row .col-md-3,
        .row .col-md-4,
        .row .col-md-8 {
            width: 100%;
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .checkout-header {
            padding: 0.75rem;
        }

        .checkout-title {
            font-size: 0.9375rem;
        }

        .checkout-section-title {
            font-size: 0.875rem;
            padding: 0.75rem;
        }

        .checkout-section-content {
            padding: 0.875rem;
        }

        .form-label {
            font-size: 0.8125rem;
        }

        .form-control {
            padding: 0.625rem 0.75rem;
            font-size: 0.8125rem;
        }

        .payment-method {
            padding: 0.75rem;
        }

        .payment-method label {
            font-size: 0.8125rem;
        }

        .btn-checkout {
            padding: 0.9375rem 1rem;
            font-size: 0.875rem;
        }

        .order-summary-header {
            padding: 0.75rem;
        }

        .order-summary-content {
            padding: 0.875rem;
        }

        .summary-item {
            font-size: 0.8125rem;
        }

        .summary-total {
            font-size: 0.9375rem;
        }

        .summary-total .total-value {
            font-size: 1rem;
        }
    }

    /* Desktop - layout lado a lado */
    @media (min-width: 993px) {
        .checkout-page {
            padding: 1.5rem 0;
        }

        .checkout-card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .checkout-section {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .order-summary {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
    }

    /* Ajustes de espaçamento */
    h5 {
        margin-bottom: 1rem;
        font-size: 1rem;
        font-weight: 600;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="checkout-page">
    <div class="container">
        <div class="row checkout-row-mobile">
            <div class="col-lg-8">
                <div class="checkout-card">
                    <div class="checkout-header">
                        <h1 class="checkout-title">
                            <i class="fas fa-credit-card"></i>
                            Finalizar Compra
                        </h1>
                    </div>
                    
                    <div class="checkout-section-content">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('checkout.store') }}">
                            @csrf
                            
                            <!-- Dados Pessoais -->
                            <div class="checkout-section">
                                <h5 class="checkout-section-title">Dados Pessoais</h5>
                                <div class="checkout-section-content">
                                    <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome Completo *</label>
                                    <input type="text" 
                                           class="form-control @error('customer_name') is-invalid @enderror" 
                                           name="customer_name" 
                                           value="{{ old('customer_name', Auth::guard('customer')->user()->name ?? '') }}" 
                                           required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" 
                                           class="form-control @error('customer_email') is-invalid @enderror" 
                                           name="customer_email" 
                                           value="{{ old('customer_email', Auth::guard('customer')->user()->email ?? '') }}" 
                                           required>
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone *</label>
                                    <input type="tel" 
                                           class="form-control @error('customer_phone') is-invalid @enderror" 
                                           name="customer_phone" 
                                           value="{{ old('customer_phone', Auth::guard('customer')->user()->phone ?? '') }}" 
                                           required>
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CPF</label>
                                    <input type="text" 
                                           class="form-control @error('customer_cpf') is-invalid @enderror" 
                                           name="customer_cpf" 
                                           value="{{ old('customer_cpf') }}" 
                                           placeholder="000.000.000-00">
                                    @error('customer_cpf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                                </div>
                            </div>
                            
                            <!-- Endereço de Entrega -->
                            @php
                                $hasRegionalShipping = !empty($shippingSelection) && !empty($shippingSelection['shipping_type']) && $shippingSelection['shipping_type'] === 'regional';
                            @endphp
                            <div class="checkout-section">
                                <h5 class="checkout-section-title">
                                    <i class="bi bi-geo-alt me-2"></i>Endereço de Entrega
                                    @if($hasRegionalShipping)
                                        <span class="text-danger">*</span>
                                    @endif
                                </h5>
                                <div class="checkout-section-content">
                            
                            @if($hasRegionalShipping && !empty($shippingSelection['region_name']))
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Região selecionada:</strong> {{ $shippingSelection['region_name'] }}
                            </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">
                                        Rua/Avenida/Logradouro
                                        @if($hasRegionalShipping) <span class="text-danger">*</span> @endif
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('shipping_street') is-invalid @enderror" 
                                           name="shipping_street" 
                                           value="{{ old('shipping_street') }}" 
                                           placeholder="Ex: Rua das Flores"
                                           @if($hasRegionalShipping) required @endif>
                                    @error('shipping_street')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        Número
                                        @if($hasRegionalShipping) <span class="text-danger">*</span> @endif
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('shipping_number') is-invalid @enderror" 
                                           name="shipping_number" 
                                           value="{{ old('shipping_number') }}" 
                                           placeholder="Ex: 123"
                                           @if($hasRegionalShipping) required @endif>
                                    @error('shipping_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Complemento</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_complement') is-invalid @enderror" 
                                           name="shipping_complement" 
                                           value="{{ old('shipping_complement') }}" 
                                           placeholder="Ex: Apt 101, Bloco A">
                                    @error('shipping_complement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Bairro/Região
                                        @if($hasRegionalShipping) <span class="text-danger">*</span> @endif
                                        <button type="button" 
                                                class="btn btn-sm btn-link p-0 ms-2" 
                                                id="edit-neighborhood-btn"
                                                style="font-size: 0.85rem; text-decoration: none;">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    </label>
                                    <div id="neighborhood-display" style="display: block;">
                                        <div class="input-group">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="neighborhood-display-input"
                                                   value="{{ old('shipping_neighborhood', $shippingSelection['region_name'] ?? '') }}" 
                                                   readonly
                                                   style="background-color: #f8f9fa;">
                                            <input type="hidden" 
                                                   name="shipping_neighborhood" 
                                                   id="shipping_neighborhood"
                                                   value="{{ old('shipping_neighborhood', $shippingSelection['region_name'] ?? '') }}">
                                        </div>
                                    </div>
                                    <div id="neighborhood-edit" style="display: none;">
                                        <div class="position-relative">
                                            <input type="text" 
                                                   class="form-control @error('shipping_neighborhood') is-invalid @enderror" 
                                                   id="neighborhood-search-input"
                                                   placeholder="Buscar bairro ou região..."
                                                   autocomplete="off">
                                            <div class="position-absolute top-100 start-0 w-100 bg-white border rounded-bottom shadow-lg mt-1" 
                                                 id="neighborhood-suggestions" 
                                                 style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto;">
                                            </div>
                                            <input type="hidden" id="selected-region-id" value="{{ $shippingSelection['region_id'] ?? '' }}">
                                        </div>
                                        <button type="button" 
                                                class="btn btn-sm btn-secondary mt-2" 
                                                id="cancel-edit-neighborhood">
                                            Cancelar
                                        </button>
                                    </div>
                                    @error('shipping_neighborhood')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Cidade
                                        @if($hasRegionalShipping) <span class="text-danger">*</span> @endif
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('shipping_city') is-invalid @enderror" 
                                           name="shipping_city" 
                                           value="{{ old('shipping_city') }}" 
                                           placeholder="Ex: Goiânia"
                                           @if($hasRegionalShipping) required @endif>
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">
                                        Estado (UF)
                                        @if($hasRegionalShipping) <span class="text-danger">*</span> @endif
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('shipping_state') is-invalid @enderror" 
                                           name="shipping_state" 
                                           value="{{ old('shipping_state') }}" 
                                           placeholder="Ex: GO"
                                           maxlength="2"
                                           @if($hasRegionalShipping) required @endif>
                                    @error('shipping_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">
                                        CEP
                                        @if($hasRegionalShipping) <span class="text-danger">*</span> @endif
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('shipping_zip') is-invalid @enderror" 
                                           name="shipping_zip" 
                                           value="{{ old('shipping_zip') }}" 
                                           placeholder="00000-000"
                                           @if($hasRegionalShipping) required @endif>
                                    @error('shipping_zip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Campo antigo mantido para compatibilidade -->
                            <input type="hidden" name="shipping_address" value="{{ old('shipping_address') }}">

                            <!-- Método de Pagamento -->
                            <div class="checkout-section">
                                <h5 class="checkout-section-title">Método de Pagamento</h5>
                                <div class="checkout-section-content">
                                    <div class="payment-method" onclick="selectPayment('credit_card')">
                                        <input type="radio" name="payment_method" value="credit_card" id="credit_card" required>
                                        <label for="credit_card">
                                            <i class="fas fa-credit-card payment-method-icon"></i>
                                            <span>Cartão de Crédito</span>
                                        </label>
                                    </div>
                                    <div class="payment-method" onclick="selectPayment('pix')">
                                        <input type="radio" name="payment_method" value="pix" id="pix" required>
                                        <label for="pix">
                                            <i class="fas fa-qrcode payment-method-icon"></i>
                                            <span>PIX</span>
                                        </label>
                                    </div>
                                    <div class="payment-method" onclick="selectPayment('boleto')">
                                        <input type="radio" name="payment_method" value="boleto" id="boleto" required>
                                        <label for="boleto">
                                            <i class="fas fa-barcode payment-method-icon"></i>
                                            <span>Boleto Bancário</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-checkout">
                                <i class="fas fa-check me-2"></i>
                                Finalizar Pedido
                            </button>
                        </form>

                        <div class="mt-4">
                            <div class="shipping-widget card border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0"><i class="bi bi-truck me-2"></i> Calcular frete do carrinho</h6>
                                        <small class="text-muted">via Melhor Envio</small>
                                    </div>
                                    <div class="row g-2 align-items-end">
                                        <div class="col-8">
                                            <label class="form-label" for="cep-checkout">CEP de destino</label>
                                            <input type="text" class="form-control" id="cep-checkout" placeholder="00000-000" inputmode="numeric" maxlength="9">
                                        </div>
                                        <div class="col-4 d-grid">
                                            <button class="btn btn-outline-primary" id="btn-calc-frete-cart">
                                                <span class="label-default"><i class="bi bi-calculator me-2"></i>Calcular</span>
                                                <span class="label-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Calculando...</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-3" id="cart-frete-resultado" style="display:none"></div>
                                    <div class="mt-2" id="cart-frete-selecionado" style="display:none"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-card order-summary">
                    <div class="order-summary-header">
                        <h5 class="order-summary-title">Resumo do Pedido</h5>
                    </div>
                    
                    <div class="order-summary-content">
                        @foreach($cartItems as $item)
                            <div class="order-item">
                                <div class="order-item-image">
                                    @php
                                        $productImage = $item->display_image ?? asset('images/no-image.svg');
                                    @endphp
                                    <img src="{{ $productImage }}" 
                                         alt="{{ $item->display_name }}" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                         loading="lazy">
                                    <div class="no-image" style="display: none;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                </div>
                                <div class="order-item-info">
                                    <div class="order-item-name">{{ $item->display_name }}</div>
                                    @if($item->variation)
                                        <div class="order-item-attributes">
                                            <i class="fas fa-palette me-1"></i>{{ $item->variation->attributes_string }}
                                        </div>
                                    @endif
                                    <div class="order-item-quantity">{{ $item->quantity }}</div>
                                </div>
                                <div class="order-item-price">
                                    <span>R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                    <small style="font-size: 0.75rem; color: #999; font-weight: 400;">
                                        R$ {{ number_format($item->price, 2, ',', '.') }} cada
                                    </small>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="summary-item">
                            <span>Subtotal:</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="summary-item">
                            <span>Frete:</span>
                            <span>
                                @if(!empty($shippingSelection))
                                    <span id="summary-shipping">R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                                @else
                                    <span id="summary-shipping">—</span>
                                @endif
                            </span>
                        </div>
                        @if(!empty($shippingSelection))
                            <div class="small text-muted mb-2" id="shipping-detail-text">
                                <i class="bi bi-truck me-1"></i>
                                {{ $shippingSelection['service'] ?? ($shippingSelection['region_name'] ? 'Entrega Local - ' . $shippingSelection['region_name'] : 'Frete selecionado') }}
                                @if(!empty($shippingSelection['delivery_days']))
                                    • {{ $shippingSelection['delivery_days'] }} dia(s) úteis
                                @endif
                                @if(!empty($shippingSelection['cep']) && $shippingSelection['cep'] !== '00000000')
                                    • CEP {{ substr($shippingSelection['cep'],0,5) }}-{{ substr($shippingSelection['cep'],5) }}
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning py-2 px-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Selecione o frete para ver o total final. Use o campo acima para calcular.
                            </div>
                        @endif
                        <div class="summary-total">
                            <span>Total:</span>
                            <span class="total-value" id="total">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="order-summary-content" style="border-top: 1px solid #f0f0f0; padding-top: 1rem;">
                        <a href="{{ route('cart.index') }}" class="btn-back w-100 text-center">
                            <i class="fas fa-arrow-left"></i>
                            Voltar ao Carrinho
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectPayment(method) {
    // Remover seleção anterior
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Selecionar novo método
    document.getElementById(method).closest('.payment-method').classList.add('selected');
    document.getElementById(method).checked = true;
}
// Checkout: cálculo de frete do carrinho
document.addEventListener('DOMContentLoaded', function(){
    const cepInput = document.getElementById('cep-checkout');
    if (cepInput) {
        cepInput.addEventListener('input', (e) => {
            let v = (e.target.value || '').replace(/\D/g, '').slice(0,8);
            if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
            e.target.value = v;
        });
    }
    const btn = document.getElementById('btn-calc-frete-cart');
    btn?.addEventListener('click', async (ev) => {
        ev.preventDefault();
        const raw = (cepInput?.value || '').replace(/\D/g, '');
        if (raw.length !== 8) { return; }
        await calcularFreteCarrinho(raw);
    });
});

async function calcularFreteCarrinho(cep) {
    const btn = document.getElementById('btn-calc-frete-cart');
    const box = document.getElementById('cart-frete-resultado');
    if (!btn || !box) return;
    btn.disabled = true;
    btn.querySelector('.label-default')?.classList.add('d-none');
    btn.querySelector('.label-loading')?.classList.remove('d-none');
    box.style.display = 'none'; box.innerHTML = '';
    try {
        const resp = await fetch('{{ route("shipping.quote.cart") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ cep })
        });
        const data = await resp.json();
        if (!resp.ok || !data.success) {
            box.innerHTML = `<div class="alert alert-danger">${data.message || 'Falha ao calcular frete'}</div>`;
            box.style.display = 'block';
            return;
        }
        renderCartQuotes(data.quotes || [], cep);
    } catch(e) {
        box.innerHTML = `<div class="alert alert-danger">Erro de conexão. Tente novamente.</div>`;
        box.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.querySelector('.label-default')?.classList.remove('d-none');
        btn.querySelector('.label-loading')?.classList.add('d-none');
    }
}

function renderCartQuotes(quotes, cep) {
    const box = document.getElementById('cart-frete-resultado');
    if (!box) return;
    const withPrice = Array.isArray(quotes) ? quotes.filter(q => typeof q.price === 'number') : [];
    if (withPrice.length === 0) {
        box.innerHTML = `<div class="alert alert-warning">Nenhuma opção disponível para o CEP informado.</div>`;
        box.style.display = 'block';
        return;
    }
    const cheapest = withPrice.reduce((acc, q) => acc && acc.price <= q.price ? acc : q, withPrice[0]);
    const items = withPrice.map((q, idx) => {
        const preco = typeof q.price === 'number' ? q.price : null;
        const precoFmt = preco !== null ? `R$ ${preco.toFixed(2).replace('.', ',')}` : '—';
        let service = q.service || 'Serviço';
        if (service.startsWith('.')) service = 'Jadlog ' + service;
        const prazo = (q.delivery_days != null) ? `${q.delivery_days} dia(s)` : '';
        const isCheapest = cheapest && q === cheapest;
        return `
        <div class="list-group-item d-flex justify-content-between align-items-start" role="radio" aria-checked="${idx===0?'true':'false'}" tabindex="0" data-index="${idx}">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-1">
                    <input type="radio" name="shipping_cart" class="form-check-input me-2" ${idx===0?'checked':''} value="${service}" data-price="${preco ?? ''}" data-days="${q.delivery_days ?? ''}" data-service-id="${q.service_id ?? ''}" data-company="${q.company ?? ''}">
                    <span class="fw-semibold">${service}</span>
                </div>
                <div class="small text-muted">${prazo}</div>
                ${isCheapest?'<span class="badge bg-success mt-1">Mais barato</span>':''}
            </div>
            <div class="text-end"><div class="fw-bold">${precoFmt}</div></div>
        </div>`;
    }).join('');
    box.innerHTML = `<div class="list-group list-group-flush border rounded">${items}</div>`;
    box.style.display = 'block';
    attachCartFreteEvents(cep);
    // Salvar a primeira opção por padrão
    const first = box.querySelector('input[name="shipping_cart"]');
    if (first) saveCartSelection(first, cep);
}

function attachCartFreteEvents(cep){
    document.querySelectorAll('#cart-frete-resultado .list-group-item').forEach(opt => {
        opt.addEventListener('click', () => {
            const radio = opt.querySelector('input[type="radio"]');
            if (radio) { radio.checked = true; saveCartSelection(radio, cep); }
        });
        opt.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const radio = opt.querySelector('input[type="radio"]');
                if (radio) { radio.checked = true; saveCartSelection(radio, cep); }
            }
        });
    });
}

async function saveCartSelection(radio, cep) {
    const price = parseFloat(radio.getAttribute('data-price') || '0') || 0;
    const days = parseInt(radio.getAttribute('data-days') || '0', 10) || null;
    const serviceId = parseInt(radio.getAttribute('data-service-id') || '0', 10) || null;
    const company = radio.getAttribute('data-company') || null;
    try {
        await fetch('{{ route("shipping.select") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ service: radio.value, price, delivery_days: days, service_id: serviceId, company, cep })
        });
        // Atualizar resumo e total
        const summaryShip = document.getElementById('summary-shipping');
        if (summaryShip) summaryShip.textContent = 'R$ ' + price.toFixed(2).replace('.', ',');
        const totalEl = document.getElementById('total');
        if (totalEl) {
            const subtotalText = '{{ number_format($subtotal, 2, ',', '.') }}'.replace('.', '').replace(',', '.');
            const subtotal = parseFloat(subtotalText);
            totalEl.textContent = 'R$ ' + (subtotal + price).toFixed(2).replace('.', ',');
        }
        const selBox = document.getElementById('cart-frete-selecionado');
        if (selBox) {
            selBox.innerHTML = `<div class="alert alert-primary py-2 px-3">Frete selecionado: <strong>${radio.value}</strong> — R$ ${price.toFixed(2).replace('.', ',')} ${days?`<small class="ms-1 text-muted">${days} dia(s)</small>`:''}</div>`;
            selBox.style.display = 'block';
        }
    } catch(e) {}
}

// Sistema de edição de bairro/região no checkout
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-neighborhood-btn');
    const cancelBtn = document.getElementById('cancel-edit-neighborhood');
    const displayDiv = document.getElementById('neighborhood-display');
    const editDiv = document.getElementById('neighborhood-edit');
    const searchInput = document.getElementById('neighborhood-search-input');
    const suggestionsBox = document.getElementById('neighborhood-suggestions');
    const hiddenInput = document.getElementById('shipping_neighborhood');
    const displayInput = document.getElementById('neighborhood-display-input');
    const regionIdInput = document.getElementById('selected-region-id');
    
    let regionsList = [];
    let searchTimeout = null;
    
    // Carregar lista de regiões
    async function loadRegions() {
        try {
            const response = await fetch('/shipping/regional-areas', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (data.success && data.regions) {
                regionsList = data.regions;
            }
        } catch (error) {
            console.error('Erro ao carregar regiões:', error);
        }
    }
    
    loadRegions();
    
    // Mostrar campo de edição
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            displayDiv.style.display = 'none';
            editDiv.style.display = 'block';
            searchInput.focus();
        });
    }
    
    // Cancelar edição
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            displayDiv.style.display = 'block';
            editDiv.style.display = 'none';
            searchInput.value = '';
            suggestionsBox.style.display = 'none';
        });
    }
    
    // Buscar regiões enquanto digita
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestionsBox.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                showSuggestions(query.toLowerCase());
            }, 300);
        });
        
        // Fechar sugestões ao clicar fora
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });
    }
    
    // Mostrar sugestões
    function showSuggestions(query) {
        if (!regionsList || regionsList.length === 0) {
            return;
        }
        
        const matches = regionsList
            .filter(region => region.name.toLowerCase().includes(query))
            .slice(0, 10);
        
        if (matches.length === 0) {
            suggestionsBox.innerHTML = '<div class="p-3 text-muted text-center">Nenhuma região encontrada</div>';
            suggestionsBox.style.display = 'block';
            return;
        }
        
        suggestionsBox.innerHTML = matches.map(region => `
            <div class="suggestion-item p-2 border-bottom" 
                 style="cursor: pointer; transition: background 0.2s;"
                 data-region-id="${region.id}"
                 data-region-name="${region.name}"
                 onmouseover="this.style.background='#e7f3ff'"
                 onmouseout="this.style.background='white'">
                <strong>${region.name}</strong>
            </div>
        `).join('');
        
        suggestionsBox.style.display = 'block';
        
        // Adicionar listeners de clique
        suggestionsBox.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const regionId = this.dataset.regionId;
                const regionName = this.dataset.regionName;
                selectRegion(regionId, regionName);
            });
        });
    }
    
    // Selecionar região e atualizar frete
    async function selectRegion(regionId, regionName) {
        // Atualizar campos
        hiddenInput.value = regionName;
        displayInput.value = regionName;
        regionIdInput.value = regionId;
        
        // Fechar sugestões
        suggestionsBox.style.display = 'none';
        searchInput.value = '';
        
        // Buscar novo preço do frete
        try {
            const configEl = document.getElementById('pdp-config');
            const config = configEl ? JSON.parse(configEl.textContent) : {};
            const productId = config.product?.id;
            
            if (!productId) {
                // Tentar pegar do primeiro item do carrinho
                const firstItem = document.querySelector('[data-product-id]');
                if (firstItem) {
                    productId = firstItem.dataset.productId;
                }
            }
            
            if (productId) {
                const response = await fetch('/shipping/regional-price', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        region_id: regionId,
                        quantity: 1
                    })
                });
                
                const data = await response.json();
                
                if (data.success && data.region) {
                    // Atualizar campos do formulário
                    hiddenInput.value = regionName;
                    displayInput.value = regionName;
                    
                    // Salvar na sessão
                    await saveShippingToSession(data.region, regionId, regionName);
                    
                    // Atualizar resumo com preço
                    await updateShippingSummary(data.region.price, regionName);
                    
                    // Mostrar sucesso
                    showSuccessMessage('Região atualizada! Frete recalculado.');
                }
            }
        } catch (error) {
            console.error('Erro ao atualizar frete:', error);
        }
        
        // Voltar para modo de exibição
        displayDiv.style.display = 'block';
        editDiv.style.display = 'none';
    }
    
    // Salvar frete na sessão
    async function saveShippingToSession(region, regionId, regionName) {
        try {
            const response = await fetch('/shipping/select', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    service: `Entrega Local - ${regionName}`,
                    price: region.price || 0,
                    cep: '00000000',
                    delivery_days: region.delivery_days_min || null,
                    region_id: regionId,
                    region_name: regionName,
                    shipping_type: 'regional'
                })
            });
            
            const result = await response.json();
            if (result.success) {
                console.log('✅ Frete atualizado na sessão:', regionName);
                return result.selection;
            }
        } catch (error) {
            console.error('❌ Erro ao salvar frete:', error);
        }
        return null;
    }
    
    // Atualizar resumo do pedido via AJAX
    async function updateShippingSummary(shippingPrice, regionName = null) {
        // Atualizar visualmente primeiro
        const shippingEl = document.getElementById('summary-shipping');
        const totalEl = document.getElementById('total');
        
        if (shippingEl) {
            shippingEl.textContent = 'R$ ' + shippingPrice.toFixed(2).replace('.', ',');
        }
        
        if (totalEl) {
            // Pegar subtotal do elemento
            const subtotalEls = document.querySelectorAll('.summary-item');
            let subtotal = 0;
            
            subtotalEls.forEach(el => {
                const text = el.textContent;
                if (text.includes('Subtotal:')) {
                    const subtotalMatch = text.match(/R\$\s*([\d.,]+)/);
                    if (subtotalMatch) {
                        subtotal = parseFloat(subtotalMatch[1].replace(/\./g, '').replace(',', '.'));
                    }
                }
            });
            
            const newTotal = subtotal + shippingPrice;
            totalEl.textContent = 'R$ ' + newTotal.toFixed(2).replace('.', ',');
        }
        
        // Buscar valores atualizados do servidor via AJAX
        try {
            const response = await fetch('{{ route("checkout.index") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success) {
                    // Atualizar valores com dados do servidor
                    const shippingEl = document.getElementById('summary-shipping');
                    const totalEl = document.getElementById('total');
                    
                    if (shippingEl && data.formatted) {
                        shippingEl.textContent = data.formatted.shipping;
                    }
                    
                    if (totalEl && data.formatted) {
                        totalEl.textContent = data.formatted.total;
                    }
                }
            }
        } catch (error) {
            console.error('Erro ao atualizar resumo:', error);
            // Em caso de erro, pelo menos os valores visuais já foram atualizados
        }
    }
    
    // Mostrar mensagem de sucesso
    function showSuccessMessage(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = `
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.checkout-card .p-4').insertBefore(alert, document.querySelector('.checkout-card .p-4').firstChild);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
});
</script>
@endsection
