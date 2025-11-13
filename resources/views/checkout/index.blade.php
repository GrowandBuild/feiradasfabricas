@extends('layouts.app')

@section('title', 'Checkout - Finalizar Compra')

@section('styles')
<style>
    .checkout-page {
        background: #f8fafc;
        min-height: 100vh;
        padding: 2rem 0;
    }

    .checkout-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .checkout-header {
        background: #111827;
        color: white;
        padding: 1.5rem;
    }

    .checkout-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #0f172a;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
    }

    .btn-checkout {
        background: #0f172a;
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    .btn-checkout:hover {
        background: #1e293b;
        color: white;
    }

    .order-summary {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.5rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .summary-total {
        font-weight: 700;
        font-size: 1.2rem;
        border-top: 2px solid #e5e7eb;
        padding-top: 0.5rem;
        margin-top: 1rem;
    }

    .payment-method {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method:hover {
        border-color: #0f172a;
    }

    .payment-method.selected {
        border-color: #0f172a;
        background: #f0f4ff;
    }

    .payment-method input[type="radio"] {
        margin-right: 0.5rem;
    }

    /* Responsividade Mobile */
    @media (max-width: 768px) {
        .checkout-page {
            padding: 1rem 0;
        }

        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Layout mobile - resumo primeiro */
        .col-lg-8 {
            order: 2;
            margin-top: 1.5rem;
        }

        .col-lg-4 {
            order: 1;
        }

        .checkout-card {
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .checkout-header {
            padding: 1.25rem;
        }

        .checkout-title {
            font-size: 1.25rem;
        }

        .p-4 {
            padding: 1.25rem !important;
        }

        h5 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.7rem 0.75rem;
            font-size: 0.9rem;
        }

        .row .col-md-6,
        .row .col-md-3 {
            margin-bottom: 1rem;
        }

        .payment-method {
            padding: 0.9rem;
            margin-bottom: 0.75rem;
        }

        .payment-method label {
            font-size: 0.9rem;
        }

        .btn-checkout {
            padding: 0.9rem 1.5rem;
            font-size: 1rem;
        }

        .order-summary {
            padding: 1.25rem;
        }

        .summary-item {
            font-size: 0.9rem;
        }

        .summary-total {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .checkout-page {
            padding: 0.75rem 0;
        }

        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .checkout-header {
            padding: 1rem;
        }

        .checkout-title {
            font-size: 1.1rem;
        }

        .p-4 {
            padding: 1rem !important;
        }

        h5 {
            font-size: 1rem;
        }

        .form-label {
            font-size: 0.85rem;
        }

        .form-control {
            padding: 0.6rem 0.65rem;
            font-size: 0.85rem;
        }

        .payment-method {
            padding: 0.75rem;
        }

        .payment-method label {
            font-size: 0.85rem;
        }

        .btn-checkout {
            padding: 0.8rem 1.25rem;
            font-size: 0.95rem;
        }

        .order-summary {
            padding: 1rem;
        }

        .summary-item {
            font-size: 0.85rem;
        }

        .summary-total {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="checkout-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-card">
                    <div class="checkout-header">
                        <h1 class="checkout-title">
                            <i class="fas fa-credit-card me-2"></i>
                            Finalizar Compra
                        </h1>
                    </div>
                    
                    <div class="p-4">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('checkout.store') }}">
                            @csrf
                            
                            <!-- Dados Pessoais -->
                            <h5 class="mb-3">Dados Pessoais</h5>
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

                            <!-- Endereço de Entrega -->
                            <h5 class="mb-3 mt-4">Endereço de Entrega</h5>
                            <div class="mb-3">
                                <label class="form-label">Endereço Completo *</label>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                          name="shipping_address" 
                                          rows="3" 
                                          required>{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cidade *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_city') is-invalid @enderror" 
                                           name="shipping_city" 
                                           value="{{ old('shipping_city') }}" 
                                           required>
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Estado *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_state') is-invalid @enderror" 
                                           name="shipping_state" 
                                           value="{{ old('shipping_state') }}" 
                                           required>
                                    @error('shipping_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">CEP *</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_zip') is-invalid @enderror" 
                                           name="shipping_zip" 
                                           value="{{ old('shipping_zip') }}" 
                                           required>
                                    @error('shipping_zip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Método de Pagamento -->
                            <h5 class="mb-3 mt-4">Método de Pagamento</h5>
                            <div class="payment-method" onclick="selectPayment('credit_card')">
                                <input type="radio" name="payment_method" value="credit_card" id="credit_card" required>
                                <label for="credit_card">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Cartão de Crédito
                                </label>
                            </div>
                            <div class="payment-method" onclick="selectPayment('pix')">
                                <input type="radio" name="payment_method" value="pix" id="pix" required>
                                <label for="pix">
                                    <i class="fas fa-qrcode me-2"></i>
                                    PIX
                                </label>
                            </div>
                            <div class="payment-method" onclick="selectPayment('boleto')">
                                <input type="radio" name="payment_method" value="boleto" id="boleto" required>
                                <label for="boleto">
                                    <i class="fas fa-barcode me-2"></i>
                                    Boleto Bancário
                                </label>
                            </div>

                            <button type="submit" class="btn-checkout mt-4">
                                <i class="fas fa-check me-2"></i>
                                Finalizar Pedido
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-card">
                    <div class="p-4">
                        <h5 class="mb-3">Resumo do Pedido</h5>
                        
                        <div class="order-summary">
                            @foreach($cartItems as $item)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                    <span>R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                            
                            <hr>
                            
                            <div class="summary-item">
                                <span>Subtotal:</span>
                                <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="summary-item">
                                <span>Frete:</span>
                                <span class="text-end">
                                    <div id="shipping-amount">R$ 0,00</div>
                                    <div id="shipping-method" class="small text-muted"></div>
                                </span>
                            </div>
                            @php
                                $shippingItems = [];
                                foreach ($cartItems as $ci) {
                                    $qty = max(1, (int) $ci->quantity);
                                    for ($i = 0; $i < $qty; $i++) {
                                        $shippingItems[] = [
                                            'weight' => (float) ($ci->product->weight ?? 0.3),
                                            'length' => (float) ($ci->product->length ?? 20),
                                            'height' => (float) ($ci->product->height ?? 20),
                                            'width'  => (float) ($ci->product->width ?? 20),
                                            'value'  => (float) ($ci->price ?? $ci->product->price ?? 0),
                                        ];
                                    }
                                }
                            @endphp
                            <div class="mb-2">
                                <x-shipping-calculator :items="$shippingItems" />
                            </div>
                            <div class="summary-total d-flex justify-content-between">
                                <span>Total:</span>
                                <span id="total">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-3">
                            <i class="fas fa-arrow-left me-2"></i>
                            Voltar ao Carrinho
                        </a>
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
// Update monetary summary when shipping is selected or on load
document.addEventListener('DOMContentLoaded', function(){
    function refreshSummary(){
        fetch('/api/cart/summary').then(r=>r.json()).then(j=>{
            if(!j||!j.success) return;
            const s=j.summary||{};
            const sel=j.selection||{};
            const elShip=document.getElementById('shipping-amount');
            const elTot=document.getElementById('total');
            const elMethod=document.getElementById('shipping-method');
            if(elShip&&s.shipping){ elShip.textContent='R$ '+s.shipping; }
            if(elTot&&s.total){ elTot.textContent='R$ '+s.total; }
            if(elMethod && sel && sel.service_name){
                const prov = (sel.provider||'');
                const provName = prov.charAt(0).toUpperCase()+prov.slice(1);
                elMethod.textContent = `${sel.service_name} · ${provName}`;
            }
        }).catch(()=>{});
    }
    refreshSummary();
    window.addEventListener('shipping:selected', refreshSummary);
});
</script>
@endsection
