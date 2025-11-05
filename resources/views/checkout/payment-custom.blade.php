@extends('layouts.app')

@section('title', 'Pagamento - Finalizar Compra')

@section('styles')
<style>
    .payment-page {
        background: #f8fafc;
        min-height: 100vh;
        padding: 2rem 0;
    }

    .payment-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        max-width: 600px;
        margin: 0 auto;
    }

    .payment-header {
        background: #111827;
        color: white;
        padding: 1.5rem;
        text-align: center;
    }

    .payment-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .payment-content {
        padding: 2rem;
    }

    .order-summary {
        background: #f8fafc;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e5e7eb;
    }

    .payment-form {
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #374151;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out;
    }

    .form-control:focus {
        outline: none;
        border-color: #0f172a;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
    }

    .row {
        display: flex;
        gap: 1rem;
    }

    .col-6 {
        flex: 1;
    }

    .col-4 {
        flex: 0 0 33.333333%;
    }

    .col-8 {
        flex: 0 0 66.666667%;
    }

    .btn-pay {
        width: 100%;
        background: #0f172a;
        color: white;
        border: none;
        padding: 1rem;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }

    .btn-pay:hover:not(:disabled) {
        background: #1e293b;
    }

    .btn-pay:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .loading {
        display: none;
        text-align: center;
        margin-top: 1rem;
    }

    .loading.show {
        display: block;
    }

    .error-message {
        background: #fee2e2;
        border: 1px solid #ef4444;
        color: #991b1b;
        padding: 1rem;
        border-radius: 6px;
        margin-top: 1rem;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    .success-message {
        background: #f0fdf4;
        border: 1px solid #22c55e;
        color: #166534;
        padding: 1rem;
        border-radius: 6px;
        margin-top: 1rem;
        display: none;
    }

    .success-message.show {
        display: block;
    }

    .back-btn {
        background: #6b7280;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin-top: 1rem;
    }

    .back-btn:hover {
        background: #4b5563;
    }
</style>
@endsection

@section('content')
<div class="payment-page">
    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h1 class="payment-title">
                    <i class="fas fa-credit-card me-2"></i>
                    Finalizar Pagamento
                </h1>
            </div>
            
            <div class="payment-content">
                <div class="order-summary">
                    <h5>Resumo do Pedido</h5>
                    <p><strong>Número:</strong> {{ $order->order_number }}</p>
                    <p><strong>Valor Total:</strong> R$ {{ number_format($order->total_amount, 2, ',', '.') }}</p>
                    <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                </div>

                <form id="paymentForm" class="payment-form">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">Dados do Cartão</label>
                        <div id="cardNumber" class="form-control" style="height: 40px;"></div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">Data de Vencimento</label>
                                <div id="expirationDate" class="form-control" style="height: 40px;"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="form-label">CVV</label>
                                <div id="securityCode" class="form-control" style="height: 40px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nome do Portador</label>
                        <input type="text" id="cardholderName" class="form-control" placeholder="Nome como está no cartão" required>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">CPF</label>
                                <input type="text" id="identificationNumber" class="form-control" placeholder="000.000.000-00" required>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" id="email" class="form-control" value="{{ $order->customer_email }}" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitButton" class="btn-pay">
                        <i class="fas fa-lock me-2"></i>
                        Pagar R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                    </button>
                </form>

                <div class="loading" id="loading">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Processando pagamento...
                </div>

                <div class="error-message" id="errorMessage"></div>
                <div class="success-message" id="successMessage"></div>

                <a href="{{ route('checkout.success', $order->order_number) }}" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- SDK do Mercado Pago -->
<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
// Configuração do Mercado Pago
const mp = new MercadoPago('{{ $paymentDetails["checkout_data"]["public_key"] }}', {
    locale: 'pt-BR'
});

// Elementos do formulário
const cardNumberElement = mp.fields.create('cardNumber', {
    placeholder: "Número do cartão"
}).mount('cardNumber');

const expirationDateElement = mp.fields.create('expirationDate', {
    placeholder: "MM/AA"
}).mount('expirationDate');

const securityCodeElement = mp.fields.create('securityCode', {
    placeholder: "CVV"
}).mount('securityCode');

// Elementos da interface
const form = document.getElementById('paymentForm');
const submitButton = document.getElementById('submitButton');
const loading = document.getElementById('loading');
const errorMessage = document.getElementById('errorMessage');
const successMessage = document.getElementById('successMessage');

// Submissão do formulário
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Mostrar loading
    submitButton.disabled = true;
    loading.classList.add('show');
    errorMessage.classList.remove('show');
    successMessage.classList.remove('show');

    try {
        // Criar token do cartão
        const token = await mp.fields.createCardToken({
            cardholderName: document.getElementById('cardholderName').value,
            identificationType: 'CPF',
            identificationNumber: document.getElementById('identificationNumber').value.replace(/\D/g, ''),
            email: document.getElementById('email').value
        });

        // Processar pagamento
        const response = await fetch('{{ route("checkout.payment.process", $order->order_number) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                token: token.id,
                payment_method_id: token.payment_method_id
            })
        });

        const result = await response.json();

        if (result.success) {
            successMessage.textContent = 'Pagamento processado com sucesso! Redirecionando...';
            successMessage.classList.add('show');
            
            // Redirecionar para página de sucesso
            setTimeout(() => {
                window.location.href = result.redirect_url;
            }, 2000);
        } else {
            errorMessage.textContent = result.error || 'Erro ao processar pagamento';
            errorMessage.classList.add('show');
        }
    } catch (error) {
        console.error('Erro:', error);
        errorMessage.textContent = 'Erro ao processar pagamento. Tente novamente.';
        errorMessage.classList.add('show');
    } finally {
        // Esconder loading
        submitButton.disabled = false;
        loading.classList.remove('show');
    }
});

// Máscara para CPF
document.getElementById('identificationNumber').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = value;
});
</script>
@endsection

