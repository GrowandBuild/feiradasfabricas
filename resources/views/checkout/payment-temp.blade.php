@extends('layouts.app')

@section('title', 'Pagamento Cartão de Crédito - Finalizar Compra')

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

    .payment-form-container {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #f59e0b;
        border-radius: 20px;
        padding: 3rem;
        margin: 2rem 0;
        position: relative;
        overflow: hidden;
    }

    .payment-form-container::before {
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

    .payment-form-header {
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .payment-form-header h5 {
        color: #1e40af;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .payment-form-header p {
        color: #1e40af;
        font-size: 0.9rem;
        margin: 0;
    }

    .payment-form {
        position: relative;
        z-index: 1;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-control:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        transform: translateY(-1px);
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .form-row {
        display: flex;
        gap: 1rem;
    }

    .form-row .form-group {
        flex: 1;
    }

    .payment-info {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #10b981;
        border-radius: 16px;
        padding: 2rem;
        margin: 2rem 0;
        position: relative;
        z-index: 1;
    }

    .payment-info h6 {
        color: #065f46;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .payment-info ul {
        color: #047857;
        padding-left: 1.5rem;
        line-height: 1.8;
        margin: 0;
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
        position: relative;
        z-index: 1;
    }

    .btn-pay {
        background: linear-gradient(135deg, #1e40af 0%, #f59e0b 100%);
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-pay:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(30, 64, 175, 0.4);
    }

    .btn-pay:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-pay:active {
        transform: translateY(0);
    }

    .btn-pay::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-pay:hover::before {
        left: 100%;
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

    .card-icons {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .card-icon {
        width: 40px;
        height: 25px;
        background: #e2e8f0;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #64748b;
    }

    @media (max-width: 768px) {
        .payment-content {
            padding: 2rem 1.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-pay {
            width: 100%;
            max-width: 300px;
        }
        
        .form-row {
            flex-direction: column;
            gap: 0;
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
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h1 class="payment-title">
                        Pagamento via Cartão de Crédito
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

                    <div class="payment-form-container">
                        <div class="payment-form-header">
                            <h5><i class="fas fa-credit-card me-2"></i>Dados do Cartão</h5>
                            <p>Preencha os dados do seu cartão de crédito</p>
                        </div>
                        
                        <div class="payment-form">
                            <form id="paymentForm">
                                <div class="form-group">
                                    <label for="cardNumber">Número do Cartão</label>
                                    <input type="text" id="cardNumber" name="cardNumber" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19">
                                    <div class="card-icons">
                                        <div class="card-icon">VISA</div>
                                        <div class="card-icon">MC</div>
                                        <div class="card-icon">AMEX</div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cardholderName">Nome no Cartão</label>
                                    <input type="text" id="cardholderName" name="cardholderName" class="form-control" placeholder="Nome como aparece no cartão">
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="expiryDate">Validade</label>
                                        <input type="text" id="expiryDate" name="expiryDate" class="form-control" placeholder="MM/AA" maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label for="cvv">CVV</label>
                                        <input type="text" id="cvv" name="cvv" class="form-control" placeholder="000" maxlength="4">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="installments">Parcelas</label>
                                    <select id="installments" name="installments" class="form-control">
                                        <option value="1">1x de R$ {{ number_format($tempOrderData['total_amount'], 2, ',', '.') }} sem juros</option>
                                        <option value="2">2x de R$ {{ number_format($tempOrderData['total_amount'] / 2, 2, ',', '.') }} sem juros</option>
                                        <option value="3">3x de R$ {{ number_format($tempOrderData['total_amount'] / 3, 2, ',', '.') }} sem juros</option>
                                        <option value="4">4x de R$ {{ number_format($tempOrderData['total_amount'] / 4, 2, ',', '.') }} sem juros</option>
                                        <option value="5">5x de R$ {{ number_format($tempOrderData['total_amount'] / 5, 2, ',', '.') }} sem juros</option>
                                        <option value="6">6x de R$ {{ number_format($tempOrderData['total_amount'] / 6, 2, ',', '.') }} sem juros</option>
                                    </select>
                                </div>
                                
                                <div class="action-buttons">
                                    <button type="submit" class="btn-pay" id="payBtn">
                                        <i class="fas fa-lock me-2"></i>
                                        <span id="payText">Pagar Agora</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="payment-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Informações Importantes:</h6>
                        <ul>
                            <li>Seus dados são protegidos com criptografia SSL</li>
                            <li>Processamento seguro pelo Mercado Pago</li>
                            <li>Você será redirecionado após o pagamento</li>
                            <li>Receberá confirmação por email</li>
                        </ul>
                    </div>
                    
                    <div class="status-check" id="statusCheck">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="statusMessage">Pagamento confirmado!</span>
                    </div>

                    <div class="loading" id="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Processando pagamento...
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
// Máscaras para os campos
document.addEventListener('DOMContentLoaded', function() {
    const cardNumber = document.getElementById('cardNumber');
    const expiryDate = document.getElementById('expiryDate');
    const cvv = document.getElementById('cvv');
    
    // Máscara para número do cartão
    cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        e.target.value = value;
    });
    
    // Máscara para data de validade
    expiryDate.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });
    
    // Máscara para CVV
    cvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });
});

// Processar pagamento
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const payBtn = document.getElementById('payBtn');
    const payText = document.getElementById('payText');
    const loading = document.getElementById('loading');
    const errorMessage = document.getElementById('errorMessage');
    const statusCheck = document.getElementById('statusCheck');
    const statusMessage = document.getElementById('statusMessage');
    
    // Validações básicas
    const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
    const cardholderName = document.getElementById('cardholderName').value;
    const expiryDate = document.getElementById('expiryDate').value;
    const cvv = document.getElementById('cvv').value;
    
    if (!cardNumber || cardNumber.length < 13) {
        showError('Por favor, digite um número de cartão válido');
        return;
    }
    
    if (!cardholderName.trim()) {
        showError('Por favor, digite o nome do portador do cartão');
        return;
    }
    
    if (!expiryDate || expiryDate.length !== 5) {
        showError('Por favor, digite uma data de validade válida (MM/AA)');
        return;
    }
    
    if (!cvv || cvv.length < 3) {
        showError('Por favor, digite um CVV válido');
        return;
    }
    
    // Mostrar loading
    loading.classList.add('show');
    payBtn.disabled = true;
    payText.textContent = 'Processando...';
    errorMessage.classList.remove('show');
    
    // Simular processamento (aqui você integraria com o Mercado Pago)
    setTimeout(() => {
        // Simular sucesso (90% das vezes)
        if (Math.random() > 0.1) {
            loading.classList.remove('show');
            statusMessage.textContent = 'Pagamento aprovado com sucesso!';
            statusCheck.classList.add('show');
            statusCheck.className = 'status-check show status-success';
            
            // Redirecionar após 2 segundos
            setTimeout(() => {
                window.location.href = '{{ route("checkout.success", ["orderNumber" => "TEMP-" . uniqid()]) }}';
            }, 2000);
        } else {
            // Simular erro
            loading.classList.remove('show');
            payBtn.disabled = false;
            payText.textContent = 'Pagar Agora';
            showError('Erro no processamento do pagamento. Tente novamente.');
        }
    }, 3000);
});

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = message;
    errorMessage.classList.add('show');
    
    // Remover erro após 5 segundos
    setTimeout(() => {
        errorMessage.classList.remove('show');
    }, 5000);
}
</script>
@endsection