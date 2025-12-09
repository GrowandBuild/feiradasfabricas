/**
 * PDP Professional - Sistema de Variações Impecável
 * Arquitetura moderna, performance otimizada e UX excepcional
 * 
 * @author Professional Developer
 * @version 2.0.0
 */

(function() {
    'use strict';

    /**
     * Configuration e Constants
     */
    const CONFIG = {
        // Cache selectors para performance
        SELECTORS: {
            mainImage: '#main-product-image',
            imageCounter: '#imageCounter',
            currentImage: '#current-image',
            totalImages: '#total-images',
            thumbnailsWrapper: '#thumbnailsWrapper',
            priceDisplay: '#product-price-display',
            skuDisplay: '#variation-sku-display',
            skuValue: '#selected-variation-sku',
            stockDisplay: '#variation-stock-display',
            stockBadge: '#variation-stock-badge',
            unavailableMessage: '#variation-unavailable-message',
            addToCartBtn: '.btn-add-to-cart-ml',
            variationOptions: '.variation-option-ml',
            variationInputs: '.variation-option-ml input',
            pdpConfig: '#pdp-config'
        },
        
        // Estados da aplicação
        STATE: {
            currentVariation: null,
            selectedOptions: {},
            isLoading: false,
            images: [],
            variations: [],
            colorImages: {}
        },
        
        // Performance thresholds
        THROTTLE_DELAY: 16, // 60fps
        DEBOUNCE_DELAY: 300,
        
        // Animation durations
        ANIMATION: {
            fast: 150,
            normal: 300,
            slow: 500
        }
    };

    /**
     * Utility Functions Impecáveis
     */
    const Utils = {
        /**
         * Safe query selector com null check
         */
        $(selector, context = document) {
            const element = context.querySelector(selector);
            if (!element && process.env.NODE_ENV === 'development') {
                console.warn(`Element not found: ${selector}`);
            }
            return element;
        },

        /**
         * Safe query selector all
         */
        $$(selector, context = document) {
            return Array.from(context.querySelectorAll(selector));
        },

        /**
         * Formatador de moeda brasileiro
         */
        formatCurrency(value) {
            if (value == null || value === '') return '';
            const number = typeof value === 'number' ? value : parseFloat(value);
            if (isNaN(number)) return '';
            return number.toLocaleString('pt-BR', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        },

        /**
         * Debounce profissional
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Throttle para animações suaves
         */
        throttle(func, limit) {
            let inThrottle;
            return function executedFunction(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        /**
         * Animação suave com CSS transitions
         */
        animate(element, properties, duration = CONFIG.ANIMATION.normal) {
            if (!element) return Promise.resolve();
            
            return new Promise(resolve => {
                const originalTransition = element.style.transition;
                element.style.transition = `all ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
                
                Object.assign(element.style, properties);
                
                setTimeout(() => {
                    element.style.transition = originalTransition;
                    resolve();
                }, duration);
            });
        }
    };

    /**
     * Image Gallery Professional
     */
    const ImageGallery = {
        currentIndex: 0,
        images: [],
        isZoomed: false,

        init() {
            this.loadConfiguration();
            this.setupEventListeners();
            this.render();
        },

        loadConfiguration() {
            const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
            if (!configEl) return;

            try {
                const config = JSON.parse(configEl.textContent || '{}');
                this.images = Array.isArray(config.images) ? config.images : [];
                CONFIG.STATE.colorImages = config.variationColorImages || {};
            } catch (error) {
                console.error('Invalid PDP configuration:', error);
                this.images = [];
            }
        },

        setupEventListeners() {
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') this.navigate(-1);
                if (e.key === 'ArrowRight') this.navigate(1);
            });

            // Touch gestures para mobile
            const mainImage = Utils.$(CONFIG.SELECTORS.mainImage);
            if (mainImage) {
                this.setupTouchGestures(mainImage);
                this.setupZoom(mainImage);
            }
        },

        setupTouchGestures(element) {
            let startX = 0;
            let currentX = 0;
            let isDragging = false;

            element.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
            });

            element.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                currentX = e.touches[0].clientX;
            });

            element.addEventListener('touchend', () => {
                if (!isDragging) return;
                const diff = startX - currentX;
                
                if (Math.abs(diff) > 50) {
                    this.navigate(diff > 0 ? 1 : -1);
                }
                
                isDragging = false;
            });
        },

        setupZoom(element) {
            element.addEventListener('dblclick', () => {
                this.isZoomed = !this.isZoomed;
                Utils.animate(element, {
                    transform: this.isZoomed ? 'scale(2)' : 'scale(1)',
                    cursor: this.isZoomed ? 'zoom-out' : 'zoom-in'
                });
            });
        },

        render() {
            this.renderThumbnails();
            this.setMainImage(0);
            this.updateCounter();
        },

        renderThumbnails() {
            const wrapper = Utils.$(CONFIG.SELECTORS.thumbnailsWrapper);
            if (!wrapper) return;

            if (this.images.length === 0) {
                wrapper.innerHTML = this.getEmptyStateHTML();
                return;
            }

            const html = this.images.map((src, index) => `
                <div class="thumbnail-item ${index === 0 ? 'active' : ''}" 
                     data-index="${index}">
                    <img src="${src}" 
                         alt="Produto - Imagem ${index + 1}"
                         class="thumbnail-img"
                         onerror="this.src='/images/no-image.svg'">
                </div>
            `).join('');

            wrapper.innerHTML = html;
            this.attachThumbnailEvents();
        },

        attachThumbnailEvents() {
            const wrapper = Utils.$(CONFIG.SELECTORS.thumbnailsWrapper);
            if (!wrapper) return;

            wrapper.addEventListener('click', (e) => {
                const thumbnail = e.target.closest('.thumbnail-item');
                if (thumbnail) {
                    const index = parseInt(thumbnail.dataset.index);
                    this.setMainImage(index);
                }
            });
        },

        setMainImage(index) {
            if (index < 0 || index >= this.images.length) return;
            
            this.currentIndex = index;
            const mainImage = Utils.$(CONFIG.SELECTORS.mainImage);
            
            if (mainImage && this.images[index]) {
                // Smooth transition
                Utils.animate(mainImage, { opacity: 0.7 }, CONFIG.ANIMATION.fast)
                    .then(() => {
                        mainImage.src = this.images[index];
                        return Utils.animate(mainImage, { opacity: 1 }, CONFIG.ANIMATION.fast);
                    });
            }

            this.updateActiveThumbnail();
            this.updateCounter();
        },

        updateActiveThumbnail() {
            Utils.$$('.thumbnail-item').forEach((thumb, index) => {
                thumb.classList.toggle('active', index === this.currentIndex);
            });

            // Scroll to active thumbnail
            const activeThumb = Utils.$('.thumbnail-item.active');
            if (activeThumb) {
                activeThumb.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'nearest', 
                    inline: 'center' 
                });
            }
        },

        updateCounter() {
            const currentSpan = Utils.$(CONFIG.SELECTORS.currentImage);
            const totalSpan = Utils.$(CONFIG.SELECTORS.totalImages);
            const counter = Utils.$(CONFIG.SELECTORS.imageCounter);

            if (currentSpan) currentSpan.textContent = this.currentIndex + 1;
            if (totalSpan) totalSpan.textContent = this.images.length;
            
            if (counter) {
                const shouldShow = this.images.length > 1;
                counter.style.display = shouldShow ? 'block' : 'none';
            }
        },

        navigate(direction) {
            if (this.images.length <= 1) return;
            
            let newIndex = this.currentIndex + direction;
            if (newIndex >= this.images.length) newIndex = 0;
            if (newIndex < 0) newIndex = this.images.length - 1;
            
            this.setMainImage(newIndex);
        },

        applyColorImages(color) {
            if (!color || !CONFIG.STATE.colorImages[color]) {
                this.images = this.getOriginalImages();
            } else {
                this.images = CONFIG.STATE.colorImages[color];
            }
            
            this.render();
        },

        getOriginalImages() {
            const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
            if (!configEl) return [];
            
            try {
                const config = JSON.parse(configEl.textContent || '{}');
                return Array.isArray(config.images) ? config.images : [];
            } catch {
                return [];
            }
        },

        getEmptyStateHTML() {
            return `
                <div class="text-center p-4">
                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Nenhuma imagem disponível</p>
                </div>
            `;
        }
    };

    /**
     * Variation System Professional
     */
    const VariationSystem = {
        init() {
            this.loadConfiguration();
            this.setupEventListeners();
            this.initializeSelections();
        },

        loadConfiguration() {
            const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
            if (!configEl) return;

            try {
                const config = JSON.parse(configEl.textContent || '{}');
                CONFIG.STATE.variations = Array.isArray(config.variationData) 
                    ? config.variationData 
                    : [];
            } catch (error) {
                console.error('Invalid variations configuration:', error);
                CONFIG.STATE.variations = [];
            }
        },

        setupEventListeners() {
            // Delegated events para performance
            document.addEventListener('change', (e) => {
                if (e.target.matches(CONFIG.SELECTORS.variationInputs)) {
                    this.handleOptionChange(e.target);
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target.closest(CONFIG.SELECTORS.variationOptions)) {
                    this.handleOptionClick(e.target.closest(CONFIG.SELECTORS.variationOptions));
                }
            });
        },

        handleOptionChange(input) {
            const type = input.getAttribute('name');
            const value = input.value;
            
            CONFIG.STATE.selectedOptions[type] = value;
            this.updateAvailability();
            this.updateVariation();
        },

        handleOptionClick(option) {
            const input = option.querySelector('input');
            if (input && !input.disabled && !input.checked) {
                input.checked = true;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },

        initializeSelections() {
            Utils.$$(CONFIG.SELECTORS.variationInputs).forEach(input => {
                if (input.checked) {
                    CONFIG.STATE.selectedOptions[input.name] = input.value;
                }
            });

            this.updateAvailability();
        },

        updateAvailability() {
            const selected = CONFIG.STATE.selectedOptions;
            
            ['ram', 'storage', 'color'].forEach(type => {
                const options = Utils.$$(`label[data-variation-type="${type}"]`);
                
                options.forEach(option => {
                    const input = option.querySelector('input');
                    if (!input) return;

                    const value = input.value;
                    const isAvailable = this.isCombinationAvailable(
                        type === 'ram' ? value : selected.ram,
                        type === 'storage' ? value : selected.storage,
                        type === 'color' ? value : selected.color
                    );

                    input.disabled = !isAvailable;
                    option.classList.toggle('disabled', !isAvailable);

                    // Auto-select primeira opção disponível
                    if (!input.checked && isAvailable && !options.some(o => o.querySelector('input:checked'))) {
                        input.checked = true;
                        CONFIG.STATE.selectedOptions[type] = value;
                    }
                });
            });

            this.updateVisualStates();
        },

        updateVisualStates() {
            Utils.$$(CONFIG.SELECTORS.variationOptions).forEach(option => {
                const input = option.querySelector('input');
                const isActive = input && input.checked && !input.disabled;
                option.classList.toggle('active', isActive);
            });

            this.applyColorSwatches();
        },

        applyColorSwatches() {
            Utils.$$('.variation-option-ml[data-variation-type="color"]').forEach(option => {
                const value = option.getAttribute('data-value');
                const swatch = option.querySelector('.swatch-ml');
                
                if (swatch && value) {
                    const variation = CONFIG.STATE.variations.find(v => v.color === value);
                    const hex = variation?.color_hex || '#f1f5f9';
                    swatch.style.background = hex;
                }
            });
        },

        isCombinationAvailable(ram, storage, color) {
            return CONFIG.STATE.variations.some(variation => {
                if (!variation.in_stock || variation.stock_quantity <= 0) return false;
                
                const matchesRam = !ram || variation.ram === ram;
                const matchesStorage = !storage || variation.storage === storage;
                const matchesColor = !color || variation.color === color;
                
                return matchesRam && matchesStorage && matchesColor;
            });
        },

        async updateVariation() {
            const selected = CONFIG.STATE.selectedOptions;
            const hasCompleteSelection = Object.values(selected).filter(v => v).length >= 2;
            
            if (!hasCompleteSelection) {
                this.hideUnavailableMessage();
                return;
            }

            const isAvailable = this.isCombinationAvailable(
                selected.ram, 
                selected.storage, 
                selected.color
            );

            if (!isAvailable) {
                this.showUnavailableMessage();
                this.disableAddToCart();
                return;
            }

            this.hideUnavailableMessage();
            await this.fetchVariationDetails();
        },

        async fetchVariationDetails() {
            const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
            if (!configEl) return;

            try {
                const config = JSON.parse(configEl.textContent || '{}');
                const url = new URL(config.routes?.productVariation || '/produto/{slug}/variacao', window.location.origin);
                
                // Adicionar parâmetros
                Object.entries(CONFIG.STATE.selectedOptions).forEach(([key, value]) => {
                    if (value) url.searchParams.append(key, value);
                });

                CONFIG.STATE.isLoading = true;
                const response = await fetch(url.toString());
                const data = await response.json();

                if (data.success && data.variation) {
                    this.updateVariationDisplay(data.variation);
                    this.enableAddToCart(data.variation.id);
                    ImageGallery.applyColorImages(CONFIG.STATE.selectedOptions.color);
                } else {
                    this.showUnavailableMessage();
                    this.disableAddToCart();
                }
            } catch (error) {
                console.error('Error fetching variation:', error);
                this.showUnavailableMessage();
                this.disableAddToCart();
            } finally {
                CONFIG.STATE.isLoading = false;
            }
        },

        updateVariationDisplay(variation) {
            // Update price
            const priceDisplay = Utils.$(CONFIG.SELECTORS.priceDisplay);
            if (priceDisplay && variation.price) {
                priceDisplay.textContent = `R$ ${variation.price}`;
            }

            // Update SKU
            const skuDisplay = Utils.$(CONFIG.SELECTORS.skuDisplay);
            const skuValue = Utils.$(CONFIG.SELECTORS.skuValue);
            if (skuDisplay && skuValue && variation.sku) {
                skuValue.textContent = variation.sku;
                skuDisplay.style.display = 'block';
            }

            // Update stock
            this.updateStockDisplay(variation);
        },

        updateStockDisplay(variation) {
            const stockDisplay = Utils.$(CONFIG.SELECTORS.stockDisplay);
            const stockBadge = Utils.$(CONFIG.SELECTORS.stockBadge);
            
            if (!stockDisplay || !stockBadge) return;

            const inStock = variation.in_stock && variation.stock_quantity > 0;
            
            if (inStock) {
                stockBadge.className = 'badge bg-success';
                stockBadge.innerHTML = `
                    <i class="fas fa-check-circle me-1"></i>
                    Em estoque (${variation.stock_quantity} unidades)
                `;
                this.enableAddToCart();
            } else {
                stockBadge.className = 'badge bg-danger';
                stockBadge.innerHTML = `
                    <i class="fas fa-times-circle me-1"></i>
                    Fora de estoque
                `;
                this.disableAddToCart();
            }
            
            stockDisplay.style.display = 'block';
        },

        showUnavailableMessage() {
            const message = Utils.$(CONFIG.SELECTORS.unavailableMessage);
            if (message) {
                message.style.display = 'flex';
            }
        },

        hideUnavailableMessage() {
            const message = Utils.$(CONFIG.SELECTORS.unavailableMessage);
            if (message) {
                message.style.display = 'none';
            }
        },

        enableAddToCart(variationId) {
            const btn = Utils.$(CONFIG.SELECTORS.addToCartBtn);
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('disabled');
                if (variationId) {
                    btn.setAttribute('data-variation-id', variationId);
                }
            }
        },

        disableAddToCart() {
            const btn = Utils.$(CONFIG.SELECTORS.addToCartBtn);
            if (btn) {
                btn.disabled = true;
                btn.classList.add('disabled');
                btn.removeAttribute('data-variation-id');
            }
        }
    };

    /**
     * Shipping System Professional
     */
    const ShippingSystem = {
        init() {
            this.setupEventListeners();
        },

        setupEventListeners() {
            const cepInput = Utils.$('#cep-destino');
            const calcBtn = Utils.$('#btn-calc-frete');
            
            if (cepInput) {
                cepInput.addEventListener('input', this.handleCEPInput.bind(this));
            }
            
            if (calcBtn) {
                calcBtn.addEventListener('click', this.calculateShipping.bind(this));
            }
        },

        handleCEPInput(e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 8);
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }
            e.target.value = value;
        },

        async calculateShipping() {
            const cepInput = Utils.$('#cep-destino');
            const qtyInput = Utils.$('#qty-shipping');
            const btn = Utils.$('#btn-calc-frete');
            const resultBox = Utils.$('#frete-resultado');

            if (!cepInput || !btn || !resultBox) return;

            const cep = cepInput.value.replace(/\D/g, '');
            const qty = parseInt(qtyInput?.value || '1', 10) || 1;

            if (cep.length !== 8) {
                this.showShippingMessage('Informe um CEP válido com 8 dígitos.', 'warning');
                return;
            }

            // Loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Calculando...';
            resultBox.style.display = 'none';

            try {
                const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
                const config = JSON.parse(configEl?.textContent || '{}');
                
                const response = await fetch(config.routes?.shippingQuote || '/frete/calcular', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrf || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: config.product?.id,
                        cep: cep,
                        quantity: qty
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.renderShippingQuotes(data.quotes || []);
                } else {
                    this.showShippingMessage(data.message || 'Erro ao calcular frete', 'danger');
                }
            } catch (error) {
                console.error('Shipping calculation error:', error);
                this.showShippingMessage('Falha na conexão. Tente novamente.', 'danger');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-truck me-2"></i> Calcular Frete';
            }
        },

        renderShippingQuotes(quotes) {
            const resultBox = Utils.$('#frete-resultado');
            if (!resultBox) return;

            if (quotes.length === 0) {
                this.showShippingMessage('Nenhuma opção de frete disponível.', 'warning');
                return;
            }

            const html = quotes.map((quote, index) => `
                <div class="shipping-option ${index === 0 ? 'option-cheapest' : ''}" data-index="${index}">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <input type="radio" name="shipping_service" class="form-check-input me-2" 
                                       ${index === 0 ? 'checked' : ''} value="${quote.service}">
                                <span class="fw-semibold">${quote.service}</span>
                            </div>
                            <div class="small text-muted">
                                ${quote.delivery_days ? `${quote.delivery_days} dia(s) úteis` : ''}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-primary">
                                R$ ${quote.price.toFixed(2).replace('.', ',')}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            resultBox.innerHTML = `<div class="border rounded">${html}</div>`;
            resultBox.style.display = 'block';
        },

        showShippingMessage(message, type) {
            const resultBox = Utils.$('#frete-resultado');
            if (!resultBox) return;

            resultBox.innerHTML = `
                <div class="alert alert-${type} m-3">
                    <i class="fas fa-info-circle me-2"></i>
                    ${message}
                </div>
            `;
            resultBox.style.display = 'block';
        }
    };

    /**
     * Quantity Control Professional
     */
    const QuantityControl = {
        init() {
            this.setupEventListeners();
            this.syncQuantities();
        },

        setupEventListeners() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('.quantity-btn-ml[data-action="decrease"]')) {
                    this.decreaseQuantity(e.target);
                } else if (e.target.matches('.quantity-btn-ml[data-action="increase"]')) {
                    this.increaseQuantity(e.target);
                }
            });

            document.addEventListener('change', (e) => {
                if (e.target.matches('.quantity-input-ml')) {
                    this.validateQuantity(e.target);
                }
            });
        },

        decreaseQuantity(btn) {
            const input = this.getSiblingInput(btn);
            if (input) {
                const current = parseInt(input.value) || 1;
                const min = parseInt(input.min) || 1;
                const newValue = Math.max(min, current - 1);
                input.value = newValue;
                this.syncQuantities();
            }
        },

        increaseQuantity(btn) {
            const input = this.getSiblingInput(btn);
            if (input) {
                const current = parseInt(input.value) || 1;
                const max = parseInt(input.max) || 999;
                const newValue = Math.min(max, current + 1);
                input.value = newValue;
                this.syncQuantities();
            }
        },

        validateQuantity(input) {
            const value = parseInt(input.value) || 1;
            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || 999;
            
            input.value = Math.max(min, Math.min(max, value));
            this.syncQuantities();
        },

        getSiblingInput(btn) {
            const parent = btn.closest('.quantity-input-group');
            return parent ? parent.querySelector('.quantity-input-ml') : null;
        },

        syncQuantities() {
            const mainInput = Utils.$('.quantity-input-ml');
            const shippingInput = Utils.$('#qty-shipping');
            
            if (mainInput && shippingInput) {
                shippingInput.value = mainInput.value || '1';
            }
        }
    };

    /**
     * Application Bootstrap
     */
    const App = {
        init() {
            // Verificar se estamos na página de produto
            if (!Utils.$(CONFIG.SELECTORS.pdpConfig)) {
                return;
            }

            try {
                // Inicializar módulos
                ImageGallery.init();
                VariationSystem.init();
                ShippingSystem.init();
                QuantityControl.init();

                // Setup global error handling
                window.addEventListener('error', this.handleGlobalError.bind(this));
                
                // Performance monitoring
                this.trackPerformance();

            } catch (error) {
                console.error('PDP initialization error:', error);
                this.handleInitializationError(error);
            }
        },

        handleGlobalError(error) {
            console.error('PDP Global Error:', error);
            // Aqui você poderia enviar para um serviço de monitoramento
        },

        handleInitializationError(error) {
            const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
            if (configEl) {
                configEl.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Erro ao inicializar a página. Por favor, recarregue.
                    </div>
                `;
            }
        },

        trackPerformance() {
            if ('performance' in window) {
                window.addEventListener('load', () => {
                    setTimeout(() => {
                        const perfData = performance.getEntriesByType('navigation')[0];
                        if (perfData) {
                            console.log(`PDP Load Time: ${perfData.loadEventEnd - perfData.fetchStart}ms`);
                        }
                    }, 0);
                });
            }
        }
    };

    /**
     * Initialize quando DOM estiver pronto
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => App.init());
    } else {
        App.init();
    }

    // Expor utilidades globalmente para debugging
    if (process.env.NODE_ENV === 'development') {
        window.PDP = {
            Utils,
            ImageGallery,
            VariationSystem,
            ShippingSystem,
            QuantityControl,
            CONFIG
        };
    }

})();
