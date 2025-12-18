@extends('admin.layouts.app')

@section('title', 'PDV - Ponto de Venda')
@section('page-title', 'PDV - Ponto de Venda')
@section('page-icon', 'bi bi-cash-register')
@section('page-description', 'Sistema de caixa para loja física')

@section('content')
<div class="pdv-container">
    <div class="row g-3">
        <!-- Coluna Esquerda: Busca e Produtos -->
        <div class="col-lg-8">
            <!-- Busca de Produtos -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-primary text-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="product-search" 
                               placeholder="Digite o nome ou SKU do produto..."
                               autofocus>
                        <button class="btn btn-primary" type="button" onclick="searchProducts()">
                            <i class="bi bi-search me-1"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resultados da Busca -->
            <div class="card mb-3" id="search-results-card" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Resultados da Busca</h6>
                </div>
                <div class="card-body">
                    <div id="search-results" class="row g-2">
                        <!-- Produtos serão inseridos aqui via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Carrinho de Venda -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-cart me-2"></i>Carrinho de Venda
                    </h6>
                </div>
                <div class="card-body">
                    <div id="cart-items" class="cart-items">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                            <p class="mt-3 mb-0">Carrinho vazio</p>
                            <small>Busque e adicione produtos para começar</small>
                        </div>
                    </div>

                    <!-- Totais -->
                    <div id="cart-totals" class="cart-totals" style="display: none;">
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Subtotal:</strong>
                            <span id="cart-subtotal">R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Desconto:</strong>
                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="cart-discount" 
                                       value="0" 
                                       step="0.01" 
                                       min="0"
                                       onchange="updateCartTotals()">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0"><strong>Total:</strong></h5>
                            <h5 class="mb-0 text-primary" id="cart-total">R$ 0,00</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Finalização -->
        <div class="col-lg-4">
            <!-- Informações do Cliente (Opcional) -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Cliente (Opcional)</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="customer-search" class="form-label small">Buscar Cliente</label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               id="customer-search" 
                               placeholder="Nome ou email...">
                        <input type="hidden" id="customer-id">
                        <div id="customer-info" class="mt-2" style="display: none;">
                            <div class="alert alert-info py-2 mb-0">
                                <small id="customer-name"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Forma de Pagamento -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Forma de Pagamento</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <select class="form-select" id="payment-method" onchange="toggleInstallments()">
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao_debito">Cartão de Débito</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="pix">PIX</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3" id="installments-field" style="display: none;">
                        <label for="installments" class="form-label small">Parcelas</label>
                        <select class="form-select form-select-sm" id="installments">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ $i }}x</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Observações -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>Observações</h6>
                </div>
                <div class="card-body">
                    <textarea class="form-control form-control-sm" 
                              id="sale-notes" 
                              rows="3" 
                              placeholder="Observações sobre a venda..."></textarea>
                </div>
            </div>

            <!-- Botão Finalizar Venda -->
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-success btn-lg w-100" 
                            id="finalize-sale-btn" 
                            onclick="finalizeSale()"
                            disabled>
                        <i class="bi bi-check-circle me-2"></i>
                        Finalizar Venda
                    </button>
                    <button class="btn btn-outline-secondary btn-sm w-100 mt-2" 
                            onclick="clearCart()">
                        <i class="bi bi-x-circle me-1"></i>
                        Limpar Carrinho
                    </button>
                </div>
            </div>

            <!-- Últimas Vendas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Últimas Vendas</h6>
                </div>
                <div class="card-body">
                    <div id="recent-sales" class="list-group list-group-flush">
                        <div class="text-center text-muted py-3">
                            <small>Nenhuma venda recente</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .pdv-container {
        padding: 1rem 0;
    }

    .product-card-pdv {
        border: 2px solid #e8eaed;
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .product-card-pdv:hover {
        border-color: #ff6b35;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .product-card-pdv .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .product-card-pdv .btn-primary:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
    }

    .product-card-pdv img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }

    .cart-item {
        border-bottom: 1px solid #e8eaed;
        padding: 1rem 0;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quantity-input {
        width: 60px;
        text-align: center;
    }

    .btn-quantity {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
let cart = [];
let customerId = null;

// Buscar produtos
function searchProducts() {
    const query = document.getElementById('product-search').value.trim();
    
    if (!query) {
        document.getElementById('search-results-card').style.display = 'none';
        return;
    }

    fetch(`{{ route('admin.pdv.search-products') }}?q=${encodeURIComponent(query)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayProducts(data.products);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao buscar produtos', 'error');
    });
}

// Exibir produtos
function displayProducts(products) {
    const container = document.getElementById('search-results');
    const card = document.getElementById('search-results-card');
    
    if (products.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted py-3">Nenhum produto encontrado</div>';
        card.style.display = 'block';
        return;
    }

    container.innerHTML = products.map((product, index) => `
        <div class="col-md-6">
            <div class="product-card-pdv">
                <div class="d-flex gap-3">
                    <img src="${product.image || '/images/no-image.svg'}" alt="${product.name}" onerror="this.src='/images/no-image.svg'">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${product.name}</h6>
                        <small class="text-muted d-block">SKU: ${product.sku}</small>
                        <div class="mt-2">
                            <strong class="text-primary">R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}</strong>
                            <small class="text-muted d-block">Estoque: ${product.available_stock}</small>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2 w-100" onclick="addProductToCart(${index})" type="button">
                            <i class="bi bi-cart-plus me-1"></i> Adicionar ao Carrinho
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Armazenar produtos globalmente para acesso
    window.searchResults = products;
    
    card.style.display = 'block';
}

// Adicionar produto ao carrinho
function addProductToCart(index) {
    if (!window.searchResults || !window.searchResults[index]) {
        showToast('Produto não encontrado', 'error');
        return;
    }
    
    const product = window.searchResults[index];
    addToCart(product);
}

// Adicionar ao carrinho
function addToCart(product, variation = null) {
    const existingItem = cart.find(item => 
        item.product_id === product.id && 
        item.variation_id === (variation ? variation.id : null)
    );

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        const selectedVariation = variation || (product.has_variations && product.variations && product.variations.length > 0 ? product.variations[0] : null);
        const price = selectedVariation ? selectedVariation.price : product.price;
        
        cart.push({
            product_id: product.id,
            variation_id: selectedVariation ? selectedVariation.id : null,
            product_name: product.name,
            product_sku: product.sku,
            quantity: 1,
            unit_price: parseFloat(price),
            discount: 0,
        });
    }

    updateCartDisplay();
    updateCartTotals();
    showToast('Produto adicionado ao carrinho', 'success');
}

// Remover do carrinho
function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
    updateCartTotals();
}

// Atualizar quantidade
function updateQuantity(index, change) {
    cart[index].quantity = Math.max(1, cart[index].quantity + change);
    updateCartDisplay();
    updateCartTotals();
}

// Atualizar exibição do carrinho
function updateCartDisplay() {
    const container = document.getElementById('cart-items');
    const totals = document.getElementById('cart-totals');
    const finalizeBtn = document.getElementById('finalize-sale-btn');

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">Carrinho vazio</p>
                <small>Busque e adicione produtos para começar</small>
            </div>
        `;
        totals.style.display = 'none';
        finalizeBtn.disabled = true;
        return;
    }

    container.innerHTML = cart.map((item, index) => `
        <div class="cart-item">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${item.product_name}</h6>
                    <small class="text-muted">SKU: ${item.product_sku}</small>
                    <div class="mt-2">
                        <strong>R$ ${item.unit_price.toFixed(2).replace('.', ',')}</strong>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})" title="Remover">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="cart-item-controls mt-2">
                <button class="btn btn-sm btn-outline-secondary btn-quantity" onclick="updateQuantity(${index}, -1)">
                    <i class="bi bi-dash"></i>
                </button>
                <input type="number" 
                       class="form-control form-control-sm quantity-input" 
                       value="${item.quantity}" 
                       min="1"
                       onchange="cart[${index}].quantity = parseInt(this.value) || 1; updateCartDisplay(); updateCartTotals();">
                <button class="btn btn-sm btn-outline-secondary btn-quantity" onclick="updateQuantity(${index}, 1)">
                    <i class="bi bi-plus"></i>
                </button>
                <div class="ms-auto">
                    <strong>R$ ${(item.unit_price * item.quantity).toFixed(2).replace('.', ',')}</strong>
                </div>
            </div>
        </div>
    `).join('');

    totals.style.display = 'block';
    finalizeBtn.disabled = false;
}

// Atualizar totais
function updateCartTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('cart-discount').value) || 0;
    const total = Math.max(0, subtotal - discount);

    document.getElementById('cart-subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    document.getElementById('cart-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
}

// Toggle parcelas
function toggleInstallments() {
    const method = document.getElementById('payment-method').value;
    const installmentsField = document.getElementById('installments-field');
    
    if (method === 'cartao_credito') {
        installmentsField.style.display = 'block';
    } else {
        installmentsField.style.display = 'none';
    }
}

// Limpar carrinho
function clearCart() {
    if (confirm('Tem certeza que deseja limpar o carrinho?')) {
        cart = [];
        updateCartDisplay();
        updateCartTotals();
        document.getElementById('product-search').value = '';
        document.getElementById('search-results-card').style.display = 'none';
    }
}

// Finalizar venda
function finalizeSale() {
    if (cart.length === 0) {
        showToast('Adicione produtos ao carrinho', 'error');
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('cart-discount').value) || 0;
    const total = Math.max(0, subtotal - discount);

    const saleData = {
        items: cart,
        subtotal: subtotal,
        discount: discount,
        total: total,
        payment_method: document.getElementById('payment-method').value,
        installments: document.getElementById('payment-method').value === 'cartao_credito' 
            ? parseInt(document.getElementById('installments').value) 
            : 1,
        customer_id: customerId,
        notes: document.getElementById('sale-notes').value,
    };

    const btn = document.getElementById('finalize-sale-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';

    fetch('{{ route("admin.pdv.create-sale") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify(saleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Se requer confirmação de pagamento (cartão no modo manual)
            if (data.requires_confirmation) {
                showPaymentConfirmationModal(data.sale, data.instructions);
            } else {
                showToast('Venda registrada com sucesso!', 'success');
                clearCart();
                loadRecentSales();
            }
        } else {
            showToast(data.message || 'Erro ao finalizar venda', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao finalizar venda', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Finalizar Venda';
    });
}

// Modal de confirmação de pagamento
function showPaymentConfirmationModal(sale, instructions) {
    const modal = document.createElement('div');
    modal.className = 'modal fade show';
    modal.style.display = 'block';
    modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-credit-card me-2"></i>Confirmar Pagamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closePaymentModal()"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Instruções:</strong><br>
                        ${instructions}
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment-reference" class="form-label">
                            <i class="bi bi-receipt me-1"></i> Referência do Pagamento (Opcional)
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="payment-reference" 
                               placeholder="Ex: NSU, Código de Autorização, etc.">
                        <small class="form-text text-muted">
                            Informe o NSU, código de autorização ou outra referência da maquininha
                        </small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg" onclick="confirmPayment(${sale.id})">
                            <i class="bi bi-check-circle me-2"></i>Confirmar Pagamento
                        </button>
                        <button class="btn btn-outline-danger" onclick="cancelPayment(${sale.id})">
                            <i class="bi bi-x-circle me-2"></i>Cancelar Venda
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
}

function closePaymentModal() {
    const modal = document.querySelector('.modal.show');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
}

// Confirmar pagamento
function confirmPayment(saleId) {
    const reference = document.getElementById('payment-reference')?.value || '';
    
    fetch(`{{ url('admin/pdv/sales') }}/${saleId}/confirm-payment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            confirmed: true,
            reference: reference
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Pagamento confirmado com sucesso!', 'success');
            closePaymentModal();
            clearCart();
            loadRecentSales();
        } else {
            showToast(data.message || 'Erro ao confirmar pagamento', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao confirmar pagamento', 'error');
    });
}

// Cancelar pagamento
function cancelPayment(saleId) {
    if (confirm('Tem certeza que deseja cancelar esta venda?')) {
        // Aqui você pode implementar cancelamento se necessário
        closePaymentModal();
        showToast('Venda cancelada', 'info');
    }
}

// Carregar vendas recentes
function loadRecentSales() {
    // Implementar depois
}

// Busca ao pressionar Enter
document.getElementById('product-search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchProducts();
    }
});

// Toast notification
function showToast(message, type = 'success') {
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        info: '#6c757d'
    };
    
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${colors[type] || colors.success}; color: white; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10000; animation: slideInRight 0.3s ease; max-width: 300px;`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection

