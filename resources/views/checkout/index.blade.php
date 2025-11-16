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
        .shipping-widget {background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px;}
        .shipping-widget .list-group-item{cursor:pointer}
        .shipping-widget .list-group-item:hover{background:#f8fafc}
        .shipping-widget .list-group-item[aria-checked="true"]{background:#eef6ff; border-left: 4px solid #0d6efd;}

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

                            <!-- Endereço (opcional) -->
                            <h5 class="mb-3 mt-4">Endereço (opcional)</h5>
                            <div class="mb-3">
                                <label class="form-label">Endereço Completo</label>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                          name="shipping_address" 
                                          rows="3" 
                                          >{{ old('shipping_address') }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cidade</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_city') is-invalid @enderror" 
                                           name="shipping_city" 
                                           value="{{ old('shipping_city') }}" >
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Estado</label>
                                    <input type="text" 
                                           class="form-control @error('shipping_state') is-invalid @enderror" 
                                           name="shipping_state" 
                                           value="{{ old('shipping_state') }}" >
                                    @error('shipping_state')
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
                                <span>
                                    @if(!empty($shippingSelection))
                                        <span id="summary-shipping">R$ {{ number_format($shipping, 2, ',', '.') }}</span>
                                    @else
                                        <span id="summary-shipping">—</span>
                                    @endif
                                </span>
                            </div>
                            @if(!empty($shippingSelection))
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-truck me-1"></i>
                                    {{ $shippingSelection['service'] ?? 'Frete selecionado' }}
                                    @if(!empty($shippingSelection['delivery_days']))
                                        • {{ $shippingSelection['delivery_days'] }} dia(s) úteis
                                    @endif
                                    @if(!empty($shippingSelection['cep']))
                                        • CEP {{ substr($shippingSelection['cep'],0,5) }}-{{ substr($shippingSelection['cep'],5) }}
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning py-2 px-3">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Selecione o frete para ver o total final. Use o campo acima para calcular.
                                </div>
                            @endif
                            <div class="summary-total d-flex justify-content-between">
                                <span>Total:</span>
                                <span id="total">R$ {{ number_format($total, 2, ',', '.') }}</span>
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
    if (!Array.isArray(quotes) || quotes.length === 0) {
        box.innerHTML = `<div class="alert alert-warning">Nenhuma opção disponível para o CEP informado.</div>`;
        box.style.display = 'block';
        return;
    }
    const withPrice = quotes.filter(q => typeof q.price === 'number');
    const cheapest = withPrice.reduce((acc, q) => acc && acc.price <= q.price ? acc : q, withPrice[0]);
    const items = quotes.map((q, idx) => {
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
</script>
@endsection
