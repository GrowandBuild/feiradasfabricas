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
        text-align: center;
    }

    .qr-code-container {
        background: #f8fafc;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        margin: 1.5rem 0;
    }

    .qr-code {
        width: 200px;
        height: 200px;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5e7eb;
    }

    .payment-info {
        background: #f0f4ff;
        border: 1px solid #0f172a;
        border-radius: 8px;
        padding: 1rem;
        margin: 1rem 0;
    }

    .copy-btn {
        background: #0f172a;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        margin-left: 0.5rem;
    }

    .copy-btn:hover {
        background: #1e293b;
    }

    .status-check {
        margin-top: 1rem;
        padding: 1rem;
        background: #f0fdf4;
        border: 1px solid #22c55e;
        border-radius: 8px;
        display: none;
    }

    .status-check.show {
        display: block;
    }

    .loading {
        display: none;
    }

    .loading.show {
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="payment-page">
    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h1 class="payment-title">
                    <i class="fas fa-qrcode me-2"></i>
                    Pagamento via PIX
                </h1>
            </div>
            
            <div class="payment-content">
                <h4>Pedido: {{ $order->order_number }}</h4>
                <p class="text-muted">Valor: <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></p>
                
                @if(isset($paymentDetails['payment_url']))
                    <div class="qr-code-container">
                        <div class="qr-code">
                            <img src="{{ $paymentDetails['payment_url'] }}" alt="QR Code PIX" style="max-width: 100%; max-height: 100%;">
                        </div>
                    </div>
                    
                    <div class="payment-info">
                        <p><strong>Instruções:</strong></p>
                        <p>1. Abra o app do seu banco ou carteira digital</p>
                        <p>2. Escaneie o QR Code ou copie o código PIX</p>
                        <p>3. Confirme o pagamento</p>
                        <p>4. Aguarde a confirmação automática</p>
                    </div>
                    
                    <div class="mt-3">
                        <button onclick="copyPixCode()" class="copy-btn">
                            <i class="fas fa-copy me-1"></i>
                            Copiar Código PIX
                        </button>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Erro ao gerar código PIX. Entre em contato conosco.
                    </div>
                @endif
                
                <div class="status-check" id="statusCheck">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="statusMessage">Pagamento confirmado!</span>
                </div>
                
                <div class="mt-4">
                    <button onclick="checkPaymentStatus()" class="btn btn-primary" id="checkBtn">
                        <span class="loading" id="loading">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                        </span>
                        <span id="checkText">Verificar Pagamento</span>
                    </button>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('checkout.success', $order->order_number) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let checkInterval;

function copyPixCode() {
    // Aqui você implementaria a lógica para copiar o código PIX
    alert('Código PIX copiado para a área de transferência!');
}

function checkPaymentStatus() {
    const checkBtn = document.getElementById('checkBtn');
    const loading = document.getElementById('loading');
    const checkText = document.getElementById('checkText');
    const statusCheck = document.getElementById('statusCheck');
    const statusMessage = document.getElementById('statusMessage');
    
    // Mostrar loading
    loading.classList.add('show');
    checkText.textContent = 'Verificando...';
    checkBtn.disabled = true;
    
    fetch(`{{ route('checkout.status', $order->order_number) }}`)
        .then(response => response.json())
        .then(data => {
            loading.classList.remove('show');
            checkText.textContent = 'Verificar Pagamento';
            checkBtn.disabled = false;
            
            if (data.success) {
                if (data.status === 'paid' || data.status === 'approved') {
                    statusMessage.textContent = 'Pagamento confirmado!';
                    statusCheck.classList.add('show');
                    statusCheck.style.background = '#f0fdf4';
                    statusCheck.style.borderColor = '#22c55e';
                    statusCheck.style.color = '#166534';
                    
                    // Parar verificação automática
                    if (checkInterval) {
                        clearInterval(checkInterval);
                    }
                    
                    // Redirecionar para página de sucesso após 3 segundos
                    setTimeout(() => {
                        window.location.href = `{{ route('checkout.success', $order->order_number) }}`;
                    }, 3000);
                } else if (data.status === 'pending') {
                    statusMessage.textContent = 'Aguardando pagamento...';
                    statusCheck.classList.add('show');
                    statusCheck.style.background = '#fef3c7';
                    statusCheck.style.borderColor = '#f59e0b';
                    statusCheck.style.color = '#92400e';
                } else {
                    statusMessage.textContent = `Status: ${data.status}`;
                    statusCheck.classList.add('show');
                    statusCheck.style.background = '#fee2e2';
                    statusCheck.style.borderColor = '#ef4444';
                    statusCheck.style.color = '#991b1b';
                }
            }
        })
        .catch(error => {
            loading.classList.remove('show');
            checkText.textContent = 'Verificar Pagamento';
            checkBtn.disabled = false;
            console.error('Erro:', error);
        });
}

// Verificação automática a cada 10 segundos
document.addEventListener('DOMContentLoaded', function() {
    checkInterval = setInterval(checkPaymentStatus, 10000);
});
</script>
@endsection
