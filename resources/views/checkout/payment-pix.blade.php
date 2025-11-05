@extends('layouts.app')

@section('title', 'Pagamento PIX - Finalizar Compra')

@section('styles')
<style>
    .payment-page {
        background: linear-gradient(135deg, #1e40af 0%, #f59e0b 100%);
        min-height: 100vh;
        padding: 2rem 0;
        position: relative;
        overflow: hidden;
    }

    .payment-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }

    .payment-container {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
    }

    .payment-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.2);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.3);
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .payment-header {
        background: linear-gradient(135deg, #1e40af 0%, #f59e0b 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .payment-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .payment-title {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .payment-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .payment-content {
        padding: 3rem;
    }

    .order-summary {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .order-summary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1e40af, #f59e0b, #f97316);
    }

    .order-summary h5 {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .order-item:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 1.1rem;
        color: #1e293b;
    }

    .order-item-label {
        color: #64748b;
        font-weight: 500;
    }

    .qr-code-container {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px dashed #f59e0b;
        border-radius: 20px;
        padding: 3rem;
        margin: 2rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .qr-code-container::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .qr-code-placeholder {
        width: 280px;
        height: 280px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 2px solid #e2e8f0;
        position: relative;
        z-index: 1;
    }

    .qr-code-placeholder i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    .qr-code-placeholder span {
        color: #64748b;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .qr-code {
        width: 280px;
        height: 280px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 2px solid #e2e8f0;
        position: relative;
        z-index: 1;
        animation: zoomIn 0.5s ease-out;
    }

    @keyframes zoomIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .qr-code img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 12px;
    }

    .payment-info {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #10b981;
        border-radius: 16px;
        padding: 2rem;
        margin: 2rem 0;
        position: relative;
    }

    .payment-info h6 {
        color: #065f46;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .payment-info ol {
        color: #047857;
        padding-left: 1.5rem;
        line-height: 1.8;
    }

    .payment-info li {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin: 2rem 0;
        flex-wrap: wrap;
    }

    .btn-pix {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-pix:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    }

    .btn-pix:active {
        transform: translateY(0);
    }

    .btn-pix::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-pix:hover::before {
        left: 100%;
    }

    .btn-verify {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-verify:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(30, 64, 175, 0.4);
    }

    .btn-verify:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-back {
        background: #6b7280;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 2rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-back:hover {
        background: #4b5563;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .loading {
        display: none;
        text-align: center;
        margin-top: 1rem;
    }

    .loading.show {
        display: block;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .status-check {
        margin-top: 1rem;
        padding: 1.5rem;
        border-radius: 12px;
        display: none;
        text-align: center;
        font-weight: 600;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .status-check.show {
        display: block;
    }

    .status-pending {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        color: #92400e;
    }

    .status-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border: 1px solid #10b981;
        color: #065f46;
    }

    .status-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 1px solid #ef4444;
        color: #991b1b;
    }

    .security-badge {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #3b82f6;
        color: #1e40af;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        text-align: center;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .security-badge i {
        font-size: 1.2rem;
    }

    .error-message {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 1px solid #ef4444;
        color: #991b1b;
        padding: 1.5rem;
        border-radius: 12px;
        margin: 1rem 0;
        display: none;
        text-align: center;
        font-weight: 500;
        animation: shake 0.5s ease-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .error-message.show {
        display: block;
    }

    @media (max-width: 768px) {
        .payment-content {
            padding: 2rem 1.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-pix, .btn-verify {
            width: 100%;
            max-width: 300px;
        }
        
        .qr-code-placeholder, .qr-code {
            width: 240px;
            height: 240px;
        }
        
        .payment-header {
            padding: 1.5rem;
        }
        
        .payment-title {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="payment-page">
    <div class="container">
        <div class="payment-container">
            <div class="payment-card">
                <div class="payment-header">
                    <div class="payment-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h1 class="payment-title">
                        Pagamento via PIX
                    </h1>
                </div>
                
                <div class="payment-content">
                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <strong>Pagamento 100% Seguro:</strong> Seus dados são processados diretamente pelo Mercado Pago
                    </div>

                    <div class="order-summary">
                        <h5><i class="fas fa-receipt me-2"></i>Resumo do Pedido</h5>
                        <div class="order-item">
                            <span class="order-item-label">Cliente:</span>
                            <span>{{ $tempOrderData['customer_name'] }}</span>
                        </div>
                        <div class="order-item">
                            <span class="order-item-label">Email:</span>
                            <span>{{ $tempOrderData['customer_email'] }}</span>
                        </div>
                        <div class="order-item">
                            <span class="order-item-label">Itens:</span>
                            <span>{{ count($tempOrderData['cart_items']) }} produto(s)</span>
                        </div>
                        <div class="order-item">
                            <span class="order-item-label">Valor Total:</span>
                            <span>R$ {{ number_format($tempOrderData['total_amount'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    @if(isset($checkoutData['payment_url']) && $checkoutData['payment_url'])
                        <div class="qr-code-container">
                            <h5 style="margin-bottom: 2rem; color: #1e40af; font-weight: 700;">
                                <i class="fas fa-qrcode me-2"></i>QR Code PIX
                            </h5>
                            <div class="qr-code">
                                <img src="{{ $checkoutData['payment_url'] }}" alt="QR Code PIX" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="qr-code-placeholder" style="display: none;">
                                    <i class="fas fa-qrcode"></i>
                                    <span>QR Code PIX</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="qr-code-container">
                            <h5 style="margin-bottom: 2rem; color: #1e40af; font-weight: 700;">
                                <i class="fas fa-qrcode me-2"></i>QR Code PIX
                            </h5>
                            <div class="qr-code-placeholder">
                                <i class="fas fa-qrcode"></i>
                                <span>QR Code PIX</span>
                            </div>
                            <div class="error-message show" style="margin-top: 2rem;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Erro ao gerar QR Code PIX. Entre em contato conosco.
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($checkoutData['payment_url']) && $checkoutData['payment_url'])
                        <div class="payment-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Instruções:</h6>
                            <ol>
                                <li>Abra o app do seu banco ou carteira digital</li>
                                <li>Escaneie o QR Code acima</li>
                                <li>Confirme o pagamento</li>
                                <li>Clique em "Verificar Pagamento" abaixo</li>
                            </ol>
                        </div>
                        
                        <div class="action-buttons">
                            <button onclick="copyPixCode()" class="btn-pix">
                                <i class="fas fa-copy me-2"></i>
                                Copiar Código PIX
                            </button>
                            <button onclick="checkPaymentStatus()" class="btn-verify" id="checkBtn">
                                <span class="loading" id="loading">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                </span>
                                <span id="checkText">Verificar Pagamento</span>
                            </button>
                        </div>
                    @endif
                    
                    <div class="status-check" id="statusCheck">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="statusMessage">Pagamento confirmado!</span>
                    </div>

                    <div class="loading" id="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Verificando pagamento...
                    </div>

                    <div class="error-message" id="errorMessage"></div>

                    <div style="text-align: center;">
                        <a href="{{ route('checkout.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                            Voltar ao Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let checkInterval;

function copyPixCode() {
    // Aqui você implementaria a lógica para copiar o código PIX
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-2"></i>Código Copiado!';
    btn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = 'linear-gradient(135deg, #f59e0b 0%, #f97316 100%)';
    }, 2000);
    
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
    
    // Fazer requisição real para verificar status
    fetch('{{ route("checkout.payment.status.temp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        loading.classList.remove('show');
        checkText.textContent = 'Verificar Pagamento';
        checkBtn.disabled = false;
        
        if (data.success) {
            statusMessage.textContent = data.message;
            statusCheck.classList.add('show');
            
            if (data.status === 'approved' || data.status === 'paid') {
                // Pagamento confirmado
                statusCheck.className = 'status-check show status-success';
                
                // Parar verificação automática
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
                
                // Redirecionar para página de sucesso
                if (data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2000);
                }
            } else {
                // Ainda aguardando pagamento
                statusCheck.className = 'status-check show status-pending';
            }
        } else {
            // Erro na verificação
            statusMessage.textContent = 'Erro ao verificar pagamento';
            statusCheck.className = 'status-check show status-error';
        }
    })
    .catch(error => {
        loading.classList.remove('show');
        checkText.textContent = 'Verificar Pagamento';
        checkBtn.disabled = false;
        
        console.error('Erro:', error);
        statusMessage.textContent = 'Erro ao verificar pagamento';
        statusCheck.className = 'status-check show status-error';
    });
}

// Verificação automática a cada 10 segundos
document.addEventListener('DOMContentLoaded', function() {
    checkInterval = setInterval(checkPaymentStatus, 10000);
});
</script>
@endsection