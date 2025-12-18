@extends('cashier.layout')

@section('content')
<div class="row g-4">
    <!-- Coluna Esquerda: Busca e Produtos -->
    <div class="col-lg-8">
        <!-- Busca de Produtos -->
        <div class="card-modern mb-4 animate-slide-in">
            <div class="card-body p-4">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-primary text-white" style="border-radius: 10px 0 0 10px;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control input-modern" 
                           id="product-search" 
                           placeholder="Digite o nome ou SKU do produto e pressione Enter..."
                           autofocus
                           style="border-left: none; border-radius: 0 10px 10px 0;">
                </div>
            </div>
        </div>

        <!-- Resultados da Busca -->
        <div class="card-modern mb-4" id="search-results-card" style="display: none;">
            <div class="card-header-modern">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Produtos Encontrados</h5>
            </div>
            <div class="card-body p-3">
                <div id="search-results" class="row g-3">
                    <!-- Produtos serão inseridos aqui -->
                </div>
            </div>
        </div>

        <!-- Carrinho de Venda -->
        <div class="card-modern">
            <div class="card-header-modern">
                <h5 class="mb-0"><i class="bi bi-cart me-2"></i>Carrinho de Venda</h5>
            </div>
            <div class="card-body p-4">
                <div id="cart-items">
                    <div class="empty-state">
                        <i class="bi bi-cart-x text-muted"></i>
                        <p class="mt-3 mb-0 fw-bold">Carrinho vazio</p>
                        <small class="text-muted">Busque e adicione produtos para começar</small>
                    </div>
                </div>

                <!-- Totais -->
                <div id="cart-totals" style="display: none;">
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Subtotal:</span>
                        <span class="fs-5 fw-bold" id="cart-subtotal">R$ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Desconto:</span>
                        <div class="input-group" style="max-width: 200px;">
                            <span class="input-group-text">R$</span>
                            <input type="number" 
                                   class="form-control input-modern" 
                                   id="cart-discount" 
                                   value="0" 
                                   step="0.01" 
                                   min="0"
                                   onchange="updateCartTotals()">
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="total-display">
                        <small class="opacity-75">TOTAL A PAGAR</small>
                        <h2 id="cart-total" class="mt-2">R$ 0,00</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Finalização -->
    <div class="col-lg-4">
        <!-- Forma de Pagamento -->
        <div class="card-modern mb-4">
            <div class="card-header-modern">
                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Forma de Pagamento</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <select class="form-select input-modern" id="payment-method" onchange="toggleInstallments()">
                        <option value="dinheiro">💵 Dinheiro</option>
                        <option value="cartao_debito">💳 Cartão de Débito</option>
                        <option value="cartao_credito">💳 Cartão de Crédito</option>
                        <option value="pix">📱 PIX</option>
                        <option value="cheque">📄 Cheque</option>
                    </select>
                </div>
                <div class="mb-3" id="installments-field" style="display: none;">
                    <label class="form-label small">Parcelas</label>
                    <select class="form-select input-modern" id="installments">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $i }}x</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <div class="card-modern mb-4">
            <div class="card-header-modern">
                <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>Observações</h5>
            </div>
            <div class="card-body p-4">
                <textarea class="form-control input-modern" 
                          id="sale-notes" 
                          rows="3" 
                          placeholder="Observações sobre a venda (opcional)..."></textarea>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="card-modern">
            <div class="card-body p-4">
                <button class="btn btn-success-modern btn-modern w-100 mb-3" 
                        id="finalize-sale-btn" 
                        onclick="finalizeSale()"
                        disabled
                        style="font-size: 1.1rem; padding: 1rem;">
                    <i class="bi bi-check-circle me-2"></i>
                    Finalizar Venda
                </button>
                <button class="btn btn-outline-danger btn-modern w-100" 
                        onclick="clearCart()">
                    <i class="bi bi-x-circle me-1"></i>
                    Limpar Carrinho
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];

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
        container.innerHTML = '<div class="col-12 text-center text-muted py-4">Nenhum produto encontrado</div>';
        card.style.display = 'block';
        return;
    }

    container.innerHTML = products.map((product, index) => `
        <div class="col-md-6">
            <div class="product-card" onclick="addProductToCart(${index})">
                <div class="d-flex gap-3">
                    <img src="${product.image || '/images/no-image.svg'}" 
                         alt="${product.name}" 
                         onerror="this.src='/images/no-image.svg'"
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">${product.name}</h6>
                        <small class="text-muted d-block">SKU: ${product.sku}</small>
                        <div class="mt-2">
                            <strong class="text-primary fs-5">R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}</strong>
                            <small class="text-muted d-block">Estoque: ${product.available_stock}</small>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2 w-100" onclick="event.stopPropagation(); addProductToCart(${index})" type="button">
                            <i class="bi bi-cart-plus me-1"></i> Adicionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
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
    showToast('Produto adicionado!', 'success');
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
            <div class="empty-state">
                <i class="bi bi-cart-x text-muted"></i>
                <p class="mt-3 mb-0 fw-bold">Carrinho vazio</p>
                <small class="text-muted">Busque e adicione produtos para começar</small>
            </div>
        `;
        totals.style.display = 'none';
        finalizeBtn.disabled = true;
        return;
    }

    container.innerHTML = cart.map((item, index) => `
        <div class="cart-item-modern animate-slide-in">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">${item.product_name}</h6>
                    <small class="text-muted">SKU: ${item.product_sku}</small>
                    <div class="mt-2">
                        <strong class="text-primary fs-5">R$ ${item.unit_price.toFixed(2).replace('.', ',')}</strong>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})" title="Remover">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="quantity-control">
                <button class="quantity-btn" onclick="updateQuantity(${index}, -1)">
                    <i class="bi bi-dash"></i>
                </button>
                <input type="number" 
                       class="form-control text-center" 
                       value="${item.quantity}" 
                       min="1"
                       style="max-width: 80px;"
                       onchange="cart[${index}].quantity = parseInt(this.value) || 1; updateCartDisplay(); updateCartTotals();">
                <button class="quantity-btn" onclick="updateQuantity(${index}, 1)">
                    <i class="bi bi-plus"></i>
                </button>
                <div class="ms-auto">
                    <strong class="fs-5">R$ ${(item.unit_price * item.quantity).toFixed(2).replace('.', ',')}</strong>
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
        customer_id: null,
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
            if (data.requires_confirmation) {
                showPaymentConfirmationModal(data.sale, data.instructions);
            } else {
                showToast('Venda registrada com sucesso!', 'success');
                clearCart();
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
            <div class="modal-content card-modern">
                <div class="card-header-modern">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Confirmar Pagamento</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closePaymentModal()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Instruções:</strong><br>
                        ${instructions}
                    </div>
                    <div class="mb-3">
                        <label for="payment-reference" class="form-label">
                            <i class="bi bi-receipt me-1"></i> Referência (NSU, Autorização)
                        </label>
                        <input type="text" class="form-control input-modern" id="payment-reference" placeholder="Opcional">
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success-modern btn-modern" onclick="confirmPayment(${sale.id})">
                            <i class="bi bi-check-circle me-2"></i>Confirmar Pagamento
                        </button>
                        <button class="btn btn-outline-danger btn-modern" onclick="closePaymentModal()">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
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
            showToast('Pagamento confirmado!', 'success');
            closePaymentModal();
            clearCart();
        } else {
            showToast(data.message || 'Erro ao confirmar pagamento', 'error');
        }
    })
    .catch(error => {
        showToast('Erro ao confirmar pagamento', 'error');
    });
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
        success: '#10b981',
        error: '#ef4444',
        info: '#3b82f6'
    };
    
    const toast = document.createElement('div');
    toast.className = 'toast-custom';
    toast.style.cssText = `background: ${colors[type] || colors.success}; color: white; padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);`;
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'} me-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection


