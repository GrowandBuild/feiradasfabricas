@props(['product', 'showQuantity' => true, 'buttonText' => 'Adicionar ao Carrinho', 'buttonClass' => 'btn btn-primary-modern btn-modern'])

@if(!$product)
    <div class="alert alert-danger border-0" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-radius: var(--radius-lg); font-family: 'Inter', sans-serif;">Erro: Produto não encontrado</div>
@else
<div class="add-to-cart-component" data-product-id="{{ $product->id }}">
    @if($showQuantity)
        <div class="row mb-4">
            <div class="col-6">
                <label for="quantity-{{ $product->id }}" class="form-label" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #111827;">Quantidade:</label>
                <div class="quantity-controls d-flex" style="width: 100%;">
                    <button class="btn quantity-btn" 
                            type="button"
                            data-action="decrease" 
                            data-product-id="{{ $product->id }}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" 
                           class="form-control quantity-input" 
                           id="quantity-{{ $product->id }}"
                           value="1" 
                           min="1" 
                           max="{{ $product->stock_quantity }}"
                           data-product-id="{{ $product->id }}">
                    <button class="btn quantity-btn" 
                            type="button"
                            data-action="increase" 
                            data-product-id="{{ $product->id }}">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-6">
                <label class="form-label" style="font-family: 'Poppins', sans-serif; font-weight: 600; color: #111827;">Estoque:</label>
                <div class="form-control-plaintext">
                    @if($product->in_stock)
                        <span style="font-family: 'Inter', sans-serif; font-weight: 500; color: #059669;">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ $product->stock_quantity }} disponível(is)
                        </span>
                    @else
                        <span style="font-family: 'Inter', sans-serif; font-weight: 500; color: #dc2626;">
                            <i class="fas fa-times-circle me-1"></i>
                            Indisponível
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="d-grid">
        <button class="{{ $buttonClass }} add-to-cart-btn" 
                type="button"
                data-product-id="{{ $product->id }}"
                {{ !$product->in_stock ? 'disabled' : '' }}
                style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            <i class="fas fa-shopping-cart me-2"></i>
            {{ $product->in_stock ? $buttonText : 'Produto Indisponível' }}
        </button>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Classe para gerenciar o estado do carrinho
    class CartManager {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
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

            // Adicionar ao carrinho
            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleAddToCart(e));
            });
        }

        handleQuantityChange(event) {
            const button = event.currentTarget;
            const action = button.dataset.action;
            const productId = button.dataset.productId;
            const input = document.querySelector(`input[data-product-id="${productId}"]`);
            
            if (!input) return;

            let newQuantity = parseInt(input.value);
            const maxQuantity = parseInt(input.getAttribute('max'));
            
            if (action === 'increase') {
                newQuantity = Math.min(newQuantity + 1, maxQuantity);
            } else if (action === 'decrease') {
                newQuantity = Math.max(1, newQuantity - 1);
            }
            
            input.value = newQuantity;
        }

        validateQuantity(event) {
            const input = event.currentTarget;
            const quantity = parseInt(input.value);
            const maxQuantity = parseInt(input.getAttribute('max'));
            
            if (quantity < 1) {
                input.value = 1;
            } else if (quantity > maxQuantity) {
                input.value = maxQuantity;
            }
        }

        handleAddToCart(event) {
            const button = event.currentTarget;
            
            // Prevenir múltiplos cliques
            if (button.disabled) return;

            const productId = button.dataset.productId;
            const quantityInput = document.querySelector(`input[data-product-id="${productId}"]`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            
            this.addToCart(productId, quantity, button);
        }

        async addToCart(productId, quantity, button) {
            // Salvar estado original do botão
            const originalState = {
                content: button.innerHTML,
                classes: button.className,
                disabled: button.disabled
            };

            try {
                // Atualizar UI para estado de loading
                this.setLoadingState(button);

                // Fazer requisição
                const response = await this.makeRequest(productId, quantity);
                
                if (response.success) {
                    this.setSuccessState(button, originalState);
                    this.updateCartCount();
                    this.showNotification('success', response.message);
                } else {
                    this.setErrorState(button, originalState, response.message);
                }
            } catch (error) {
                this.setErrorState(button, originalState, error.message);
            }
        }

        setLoadingState(button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adicionando...';
            button.classList.remove('btn-primary', 'btn-success', 'btn-danger');
            button.classList.add('btn-secondary');
        }

        setSuccessState(button, originalState) {
            button.innerHTML = '<i class="fas fa-check me-2"></i>Adicionado!';
            button.classList.remove('btn-secondary');
            button.classList.add('btn-success');

            // Resetar após 2 segundos
            setTimeout(() => {
                this.resetButton(button, originalState);
            }, 2000);
        }

        setErrorState(button, originalState, message) {
            button.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Erro';
            button.classList.remove('btn-secondary');
            button.classList.add('btn-danger');
            
            this.showNotification('error', message);

            // Resetar após 3 segundos
            setTimeout(() => {
                this.resetButton(button, originalState);
            }, 3000);
        }

        resetButton(button, originalState) {
            button.innerHTML = originalState.content;
            button.className = originalState.classes;
            button.disabled = originalState.disabled;
        }

        async makeRequest(productId, quantity) {
            const url = '{{ route("cart.add") }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Verificar se há variation_id selecionada
            const variationId = document.querySelector('.add-to-cart-component[data-variation-id]')?.getAttribute('data-variation-id') || 
                                 document.querySelector('[data-product-id][data-variation-id]')?.getAttribute('data-variation-id') || null;

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    variation_id: variationId
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }

        async updateCartCount() {
            try {
                const response = await fetch('{{ route("cart.count") }}');
                const data = await response.json();
                
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.count;
                }
            } catch (error) {
                console.error('Erro ao atualizar contador:', error);
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
            
            // Remover notificação após 5 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }
    }

    // Inicializar o gerenciador de carrinho
    new CartManager();
});
</script>
@endpush