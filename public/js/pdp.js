/**
 * PDP Professional - Página de Detalhes do Produto
 * Sistema de galeria de imagens, controle de quantidade, cálculo de frete e adicionar ao carrinho
 * 
 * @author Professional Developer
 * @version 2.1.0
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
            addToCartBtn: '.btn-add-to-cart-ml',
            pdpConfig: '#pdp-config'
        },
        
        // Estados da aplicação
        STATE: {
            isLoading: false,
            images: []
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
            if (!element && typeof process !== 'undefined' && process.env && process.env.NODE_ENV === 'development') {
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

            // Configurar botões de navegação
            this.setupNavigationButtons();

            // Touch gestures para mobile
            const mainImage = Utils.$(CONFIG.SELECTORS.mainImage);
            if (mainImage) {
                this.setupTouchGestures(mainImage);
                this.setupZoom(mainImage);
            }
        },

        setupNavigationButtons() {
            // Remover listeners antigos se existirem
            const prevBtn = document.getElementById('prev-image');
            const nextBtn = document.getElementById('next-image');
            
            if (prevBtn) {
                // Clonar o botão para remover listeners antigos
                const newPrevBtn = prevBtn.cloneNode(true);
                prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
                newPrevBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.navigate(-1);
                });
            }
            
            if (nextBtn) {
                // Clonar o botão para remover listeners antigos
                const newNextBtn = nextBtn.cloneNode(true);
                nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
                newNextBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.navigate(1);
                });
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
            // Resetar para a primeira imagem quando renderizar
            if (this.images.length > 0) {
                this.currentIndex = 0;
                this.setMainImage(0);
            }
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
     * Shipping System Professional
     */
    const ShippingSystem = {
        regionsList: [],
        selectedRegion: null,
        searchTimeout: null,

        init() {
            console.log('ShippingSystem.init() chamado');
            this.setupEventListeners();
            this.setupTabs();
            // Carregar regiões primeiro, depois configurar busca
            this.loadRegionsList().then(() => {
                console.log('Regiões carregadas, configurando busca...');
                this.setupRegionSearch();
            }).catch(error => {
                console.error('Erro ao carregar regiões:', error);
                // Mesmo assim, configurar busca para tentar recarregar depois
                this.setupRegionSearch();
            });
        },

        async loadRegionsList() {
            try {
                console.log('🔄 Carregando lista de regiões...');
                const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
                if (!configEl) {
                    console.error('❌ Elemento de configuração não encontrado');
                    this.regionsList = [];
                    return;
                }
                
                const config = JSON.parse(configEl?.textContent || '{}');
                const route = config.routes?.shippingRegionalAreas || '/shipping/regional-areas';
                console.log('🌐 Fazendo requisição para:', route);
                
                const response = await fetch(route, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                console.log('📡 Resposta recebida:', response.status, response.statusText);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('📦 Dados recebidos:', data);
                
                if (data.success && data.regions) {
                    this.regionsList = data.regions;
                    console.log('✅ Regiões carregadas:', this.regionsList.length);
                    if (this.regionsList.length > 0) {
                        console.log('📝 Primeiras regiões:', this.regionsList.slice(0, 3).map(r => r.name));
                        // Selecionar uma região aleatória por padrão
                        this.selectRandomRegion();
                    }
                } else {
                    console.warn('⚠️ Nenhuma região retornada ou resposta inválida:', data);
                    this.regionsList = [];
                }
            } catch (error) {
                console.error('❌ Erro ao carregar regiões:', error);
                this.regionsList = [];
            }
        },

        setupRegionSearch() {
            const searchInput = Utils.$('#region-search-local');
            const suggestionsBox = Utils.$('#region-suggestions');
            
            if (!searchInput) {
                console.error('❌ Campo de busca não encontrado: #region-search-local');
                return;
            }
            
            if (!suggestionsBox) {
                console.error('❌ Container de sugestões não encontrado: #region-suggestions');
                return;
            }

            console.log('✅ Configurando busca regional. Total de regiões:', this.regionsList?.length || 0);
            console.log('✅ Campo de busca encontrado:', searchInput);
            console.log('✅ Container de sugestões encontrado:', suggestionsBox);

            // Buscar enquanto digita
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                console.log('🔍 Usuário digitou:', query, '| Tamanho:', query.length);
                
                clearTimeout(this.searchTimeout);
                
                if (query.length < 2) {
                    console.log('⏸️ Query muito curta, escondendo sugestões');
                    suggestionsBox.style.display = 'none';
                    this.clearSelectedRegion();
                    return;
                }

                console.log('⏳ Aguardando 300ms antes de buscar...');
                this.searchTimeout = setTimeout(() => {
                    console.log('🔎 Iniciando busca por:', query);
                    console.log('📋 Regiões disponíveis:', this.regionsList?.length || 0);
                    
                    if (!this.regionsList || this.regionsList.length === 0) {
                        console.warn('⚠️ Lista de regiões vazia. Tentando recarregar...');
                        this.loadRegionsList().then(() => {
                            console.log('✅ Regiões recarregadas, mostrando sugestões...');
                            this.showSuggestions(query.toLowerCase(), suggestionsBox);
                        });
                    } else {
                        console.log('✅ Mostrando sugestões com', this.regionsList.length, 'regiões disponíveis');
                        this.showSuggestions(query.toLowerCase(), suggestionsBox);
                    }
                }, 300);
            });

            // Fechar sugestões ao clicar fora
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });

            // Selecionar com Enter ou setas
            searchInput.addEventListener('keydown', (e) => {
                const suggestions = Array.from(suggestionsBox.querySelectorAll('.suggestion-item'));
                const currentIndex = suggestions.findIndex(s => s.classList.contains('highlighted'));
                
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const highlighted = suggestionsBox.querySelector('.suggestion-item.highlighted');
                    const firstSuggestion = highlighted || suggestions[0];
                    if (firstSuggestion) {
                        firstSuggestion.click();
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = currentIndex < suggestions.length - 1 ? currentIndex + 1 : 0;
                    suggestions.forEach(s => s.classList.remove('highlighted'));
                    if (suggestions[nextIndex]) {
                        suggestions[nextIndex].classList.add('highlighted');
                        suggestions[nextIndex].style.background = '#e7f3ff';
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : suggestions.length - 1;
                    suggestions.forEach(s => s.classList.remove('highlighted'));
                    if (suggestions[prevIndex]) {
                        suggestions[prevIndex].classList.add('highlighted');
                        suggestions[prevIndex].style.background = '#e7f3ff';
                    }
                } else if (e.key === 'Escape') {
                    suggestionsBox.style.display = 'none';
                }
            });
        },

        /**
         * Calcula a similaridade entre duas strings usando Levenshtein distance
         */
        calculateSimilarity(str1, str2) {
            const s1 = str1.toLowerCase().trim();
            const s2 = str2.toLowerCase().trim();
            
            // Se for exatamente igual, retorna 1
            if (s1 === s2) return 1;
            
            // Se uma contém a outra, retorna alta similaridade
            if (s1.includes(s2) || s2.includes(s1)) {
                return 0.9;
            }
            
            // Calcular distância de Levenshtein
            const maxLen = Math.max(s1.length, s2.length);
            if (maxLen === 0) return 1;
            
            const distance = this.levenshteinDistance(s1, s2);
            const similarity = 1 - (distance / maxLen);
            
            // Bonus se palavras individuais correspondem
            const words1 = s1.split(/\s+/).filter(w => w.length > 2);
            const words2 = s2.split(/\s+/).filter(w => w.length > 2);
            let wordMatches = 0;
            
            words1.forEach(w1 => {
                words2.forEach(w2 => {
                    if (w1 === w2 || w1.includes(w2) || w2.includes(w1)) {
                        wordMatches++;
                    }
                });
            });
            
            if (wordMatches > 0) {
                const wordBonus = Math.min(0.3, wordMatches * 0.1);
                return Math.min(1, similarity + wordBonus);
            }
            
            return similarity;
        },

        /**
         * Calcula a distância de Levenshtein entre duas strings
         */
        levenshteinDistance(str1, str2) {
            const matrix = [];
            const len1 = str1.length;
            const len2 = str2.length;

            for (let i = 0; i <= len2; i++) {
                matrix[i] = [i];
            }

            for (let j = 0; j <= len1; j++) {
                matrix[0][j] = j;
            }

            for (let i = 1; i <= len2; i++) {
                for (let j = 1; j <= len1; j++) {
                    if (str2.charAt(i - 1) === str1.charAt(j - 1)) {
                        matrix[i][j] = matrix[i - 1][j - 1];
                    } else {
                        matrix[i][j] = Math.min(
                            matrix[i - 1][j - 1] + 1,
                            matrix[i][j - 1] + 1,
                            matrix[i - 1][j] + 1
                        );
                    }
                }
            }

            return matrix[len2][len1];
        },

        showSuggestions(query, container) {
            const queryLower = query.toLowerCase().trim();
            
            if (!this.regionsList || this.regionsList.length === 0) {
                console.warn('Lista de regiões vazia ao mostrar sugestões');
                container.innerHTML = `
                    <div class="p-3 text-muted text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>Nenhuma região disponível
                        <div class="mt-2 small">Carregando regiões...</div>
                    </div>
                `;
                container.style.display = 'block';
                return;
            }
            
            console.log(`Buscando "${queryLower}" em ${this.regionsList.length} regiões`);
            
            // Calcular similaridade para todas as regiões
            const regionsWithScore = this.regionsList.map(region => ({
                region: region,
                score: this.calculateSimilarity(queryLower, region.name.toLowerCase())
            }));

            // Filtrar e ordenar por similaridade (maior score primeiro)
            const matches = regionsWithScore
                .filter(item => item.score > 0.3) // Mínimo de 30% de similaridade
                .sort((a, b) => b.score - a.score) // Ordenar por score decrescente
                .slice(0, 10) // Limitar a 10 resultados
                .map(item => item.region);

            console.log('🎯 Matches encontrados:', matches.length);
            if (matches.length > 0) {
                console.log('✅ Primeiros matches:', matches.slice(0, 3).map(m => m.name));
            }

            if (matches.length === 0) {
                console.log('❌ Nenhum match encontrado');
                container.innerHTML = `
                    <div class="p-3 text-muted text-center">
                        <i class="bi bi-search me-2"></i>Nenhuma região encontrada
                        <div class="mt-2 small">
                            <i class="bi bi-lightbulb me-1"></i>
                            Tente buscar por palavras-chave como "Parque", "Centro", "Vila", etc.
                        </div>
                    </div>
                `;
                container.style.display = 'block';
                return;
            }

            // Encontrar o score de cada match para destacar os melhores
            const matchesWithScore = matches.map(region => {
                const score = this.calculateSimilarity(queryLower, region.name.toLowerCase());
                return { region, score };
            });

            container.innerHTML = matchesWithScore.map(({ region, score }) => {
                // Destacar se a similaridade for muito alta (quase exata)
                const isHighMatch = score >= 0.8;
                const highlightClass = isHighMatch ? 'border-start border-primary border-3' : '';
                
                return `
                    <div class="suggestion-item p-3 border-bottom cursor-pointer ${highlightClass}" 
                         data-region-id="${region.id}"
                         data-region-name="${region.name}"
                         style="cursor: pointer; transition: all 0.2s;"
                         onmouseover="this.style.background='#f8f9fa'; this.style.transform='translateX(2px)'"
                         onmouseout="this.style.background='white'; this.style.transform='translateX(0)'">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">
                                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>${region.name}
                                    ${isHighMatch ? '<span class="badge bg-success ms-2">Correto</span>' : ''}
                                </div>
                                ${region.description ? `<small class="text-muted d-block mt-1">${region.description}</small>` : ''}
                                ${region.delivery_time ? `<small class="text-muted d-block mt-1"><i class="bi bi-clock me-1"></i>${region.delivery_time}</small>` : ''}
                            </div>
                            ${score < 0.9 ? `<small class="text-muted ms-2" title="Similaridade: ${Math.round(score * 100)}%">~${Math.round(score * 100)}%</small>` : ''}
                        </div>
                    </div>
                `;
            }).join('');

            // Adicionar listeners
            container.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', () => {
                    this.selectRegion({
                        id: parseInt(item.dataset.regionId),
                        name: item.dataset.regionName
                    });
                    container.style.display = 'none';
                });
                
                // Remover highlight ao passar o mouse
                item.addEventListener('mouseenter', () => {
                    container.querySelectorAll('.suggestion-item').forEach(s => {
                        s.classList.remove('highlighted');
                        if (!s.matches(':hover')) {
                            s.style.background = '';
                        }
                    });
                });
            });

            // Garantir que o container esteja visível e posicionado corretamente
            container.style.display = 'block';
            
            console.log('Sugestões exibidas:', matches.length);
        },

        async selectRegion(region) {
            this.selectedRegion = region;
            const searchInput = Utils.$('#region-search-local');
            if (searchInput) {
                searchInput.value = region.name;
            }

            // Buscar preço e salvar na sessão
            await this.fetchRegionPrice(region.id);
        },

        async fetchRegionPrice(regionId) {
            try {
                const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
                const config = JSON.parse(configEl?.textContent || '{}');
                const qtyInput = Utils.$('#qty-shipping-local');
                const quantity = parseInt(qtyInput?.value || '1', 10) || 1;

                const route = config.routes?.shippingRegionalPrice || '/shipping/regional-price';
                const response = await fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrf || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: config.product?.id,
                        region_id: regionId,
                        quantity: quantity
                    })
                });

                const data = await response.json();
                
                if (data.success && data.region) {
                    this.showSelectedRegionInfo(data.region);
                    
                    // Salvar frete local na sessão para sincronizar com checkout
                    await this.saveRegionalShippingToSession(data.region);
                } else {
                    this.clearSelectedRegion();
                    const resultBox = Utils.$('#frete-resultado');
                    if (resultBox) {
                        resultBox.innerHTML = `
                            <div class="alert alert-warning m-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                ${data.message || 'Erro ao buscar preço'}
                            </div>
                        `;
                        resultBox.style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Erro ao buscar preço:', error);
                this.clearSelectedRegion();
            }
        },

        showSelectedRegionInfo(region) {
            const infoBox = Utils.$('#selected-region-info');
            const nameEl = Utils.$('#selected-region-name');
            const priceEl = Utils.$('#selected-region-price');
            const deliveryEl = Utils.$('#selected-region-delivery');

            if (infoBox) {
                if (nameEl) nameEl.textContent = region.name;
                if (priceEl) priceEl.textContent = `R$ ${region.price.toFixed(2).replace('.', ',')}`;
                if (deliveryEl) deliveryEl.textContent = region.delivery_time || 'A combinar';
                
                infoBox.style.display = 'block';
            }
        },

        clearSelectedRegion() {
            this.selectedRegion = null;
            const infoBox = Utils.$('#selected-region-info');
            if (infoBox) {
                infoBox.style.display = 'none';
            }
        },

        async selectRandomRegion() {
            if (!this.regionsList || this.regionsList.length === 0) {
                return;
            }
            
            // Selecionar uma região aleatória
            const randomIndex = Math.floor(Math.random() * this.regionsList.length);
            const randomRegion = this.regionsList[randomIndex];
            
            console.log('🎲 Região aleatória selecionada:', randomRegion.name);
            
            // Selecionar a região
            this.selectedRegion = randomRegion;
            
            // Preencher o campo de busca
            const searchInput = Utils.$('#region-search-local');
            if (searchInput) {
                searchInput.value = randomRegion.name;
            }
            
            // Buscar o preço e prazo via API
            if (randomRegion.id) {
                await this.fetchRegionPrice(randomRegion.id);
            } else {
                // Se não tiver ID, tentar mostrar com dados da região
                this.showSelectedRegionInfo(randomRegion);
            }
        },

        async saveRegionalShippingToSession(region) {
            try {
                const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
                const config = JSON.parse(configEl?.textContent || '{}');
                const qtyInput = Utils.$('#qty-shipping-local') || Utils.$('.quantity-input-ml');
                const quantity = parseInt(qtyInput?.value || '1', 10) || 1;
                
                // Extrair dias de entrega (pode ser string ou número)
                let deliveryDays = null;
                if (region.delivery_days_min !== undefined) {
                    deliveryDays = region.delivery_days_min;
                } else if (region.delivery_time) {
                    // Tentar extrair número de "1 dia útil" ou similar
                    const match = region.delivery_time.toString().match(/(\d+)/);
                    if (match) {
                        deliveryDays = parseInt(match[1], 10);
                    }
                }
                
                // Salvar seleção de frete regional na sessão
                const response = await fetch('/shipping/select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrf || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        service: `Entrega Local - ${region.name}`,
                        price: region.price || 0,
                        cep: '00000000', // CEP genérico para entrega local (não é usado)
                        delivery_days: deliveryDays,
                        product_id: config.product?.id,
                        quantity: quantity,
                        region_id: region.id,
                        region_name: region.name
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    console.log('✅ Frete regional salvo na sessão:', region.name, 'R$', region.price);
                } else {
                    console.warn('⚠️ Erro ao salvar frete regional:', result.message);
                }
            } catch (error) {
                console.error('❌ Erro ao salvar frete regional na sessão:', error);
            }
        },

        setupTabs() {
            // Sincronizar CEP entre as abas
            const cepCorreios = Utils.$('#cep-destino');
            const cepLocal = Utils.$('#cep-destino-local');
            const qtyCorreios = Utils.$('#qty-shipping');
            const qtyLocal = Utils.$('#qty-shipping-local');
            const localTab = Utils.$('#local-tab');
            const correiosTab = Utils.$('#correios-tab');
            const localPane = Utils.$('#local-pane');
            const correiosPane = Utils.$('#correios-pane');
            const actionsBox = Utils.$('#frete-actions');

            // Função para esconder botões de ordenação
            const hideSortButtons = () => {
                if (actionsBox) {
                    actionsBox.style.display = 'none';
                }
            };

            // Função para mostrar botões apenas se for Correios e houver resultados
            const showSortButtonsIfNeeded = () => {
                if (actionsBox) {
                    const isLocalActive = localPane && localPane.classList.contains('active') && localPane.classList.contains('show');
                    const isCorreiosActive = correiosPane && correiosPane.classList.contains('active') && correiosPane.classList.contains('show');
                    
                    if (isLocalActive) {
                        actionsBox.style.display = 'none';
                    } else if (isCorreiosActive) {
                        const resultBox = Utils.$('#frete-resultado');
                        if (resultBox && resultBox.style.display !== 'none' && resultBox.children.length > 0) {
                            actionsBox.style.display = 'flex';
                        } else {
                            actionsBox.style.display = 'none';
                        }
                    } else {
                        actionsBox.style.display = 'none';
                    }
                }
            };

            // Esconder por padrão
            hideSortButtons();

            // Listener para mudança de abas usando Bootstrap
            const tabElements = [localTab, correiosTab];
            tabElements.forEach(tab => {
                if (tab) {
                    tab.addEventListener('shown.bs.tab', () => {
                        setTimeout(() => showSortButtonsIfNeeded(), 100);
                    });
                }
            });

            // Verificar aba inicial após um pequeno delay
            setTimeout(() => showSortButtonsIfNeeded(), 200);

            if (cepCorreios && cepLocal) {
                cepCorreios.addEventListener('input', () => {
                    if (cepLocal) cepLocal.value = cepCorreios.value;
                });
                cepLocal.addEventListener('input', () => {
                    if (cepCorreios) cepCorreios.value = cepLocal.value;
                });
            }

            if (qtyCorreios && qtyLocal) {
                qtyCorreios.addEventListener('input', () => {
                    if (qtyLocal) qtyLocal.value = qtyCorreios.value;
                });
                qtyLocal.addEventListener('input', () => {
                    if (qtyCorreios) qtyCorreios.value = qtyLocal.value;
                    // Recalcular preço se houver região selecionada
                    if (this.selectedRegion) {
                        this.fetchRegionPrice(this.selectedRegion.id);
                    }
                });
            }
            
            // Verificar aba inicial
            showSortButtonsIfNeeded();
        },

        setupEventListeners() {
            const cepInput = Utils.$('#cep-destino');
            const cepInputLocal = Utils.$('#cep-destino-local');
            const calcBtn = Utils.$('#btn-calc-frete');
            const calcBtnLocal = Utils.$('#btn-calc-frete-local');
            
            if (cepInput) {
                cepInput.addEventListener('input', this.handleCEPInput.bind(this));
            }
            if (cepInputLocal) {
                cepInputLocal.addEventListener('input', this.handleCEPInput.bind(this));
            }
            
            if (calcBtn) {
                calcBtn.addEventListener('click', () => this.calculateShipping('correios'));
            }
            if (calcBtnLocal) {
                calcBtnLocal.addEventListener('click', () => this.calculateShipping('local'));
            }
        },

        handleCEPInput(e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 8);
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }
            e.target.value = value;
        },

        async calculateShipping(type = 'correios') {
            const isLocal = type === 'local';
            const cepInput = isLocal ? Utils.$('#cep-destino-local') : Utils.$('#cep-destino');
            const qtyInput = isLocal ? Utils.$('#qty-shipping-local') : Utils.$('#qty-shipping');
            const btn = isLocal ? Utils.$('#btn-calc-frete-local') : Utils.$('#btn-calc-frete');
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
            const btnDefault = btn.innerHTML;
            btn.innerHTML = '<span class="label-loading"><i class="fas fa-spinner fa-spin me-2"></i>Calculando...</span>';
            resultBox.style.display = 'none';

            try {
                const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
                const config = JSON.parse(configEl?.textContent || '{}');
                
                // Escolher endpoint baseado no tipo
                const endpoint = isLocal 
                    ? (config.routes?.shippingQuoteRegional || '/frete/calcular-regional')
                    : (config.routes?.shippingQuote || '/frete/calcular');
                
                const response = await fetch(endpoint, {
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
                    this.renderShippingQuotes(data.quotes || [], type);
                } else {
                    this.showShippingMessage(data.message || 'Erro ao calcular frete', 'danger');
                }
            } catch (error) {
                console.error('Shipping calculation error:', error);
                this.showShippingMessage('Falha na conexão. Tente novamente.', 'danger');
            } finally {
                btn.disabled = false;
                btn.innerHTML = btnDefault;
            }
        },

        renderShippingQuotes(quotes, type = 'correios') {
            const resultBox = Utils.$('#frete-resultado');
            if (!resultBox) return;

            if (quotes.length === 0) {
                this.showShippingMessage('Nenhuma opção de frete disponível.', 'warning');
                return;
            }

            const isLocal = type === 'local';
            const typeLabel = isLocal ? 'local' : 'correios';
            
            // Esconder botões de ordenação se for Entrega Local
            const actionsBox = Utils.$('#frete-actions');
            if (actionsBox) {
                if (isLocal) {
                    actionsBox.style.display = 'none';
                } else {
                    // Só mostrar se houver mais de uma opção E se a aba Correios estiver ativa
                    const correiosPane = Utils.$('#correios-pane');
                    const isCorreiosActive = correiosPane && correiosPane.classList.contains('active') && correiosPane.classList.contains('show');
                    if (isCorreiosActive && quotes.length > 1) {
                        actionsBox.style.display = 'flex';
                    } else {
                        actionsBox.style.display = 'none';
                    }
                }
            }

            const html = quotes.map((quote, index) => {
                const deliveryTime = quote.delivery_time || (quote.delivery_days ? `${quote.delivery_days} dia(s) úteis` : '');
                const icon = isLocal ? 'bi-geo-alt' : 'bi-envelope';
                
                return `
                    <div class="shipping-option ${index === 0 ? 'option-cheapest' : ''}" 
                         data-index="${index}" 
                         data-type="${typeLabel}"
                         data-service-id="${quote.service_id || ''}"
                         data-price="${quote.price}"
                         data-service="${quote.service}">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <input type="radio" name="shipping_service" class="form-check-input me-2" 
                                           ${index === 0 ? 'checked' : ''} 
                                           value="${quote.service_id || quote.service}"
                                           data-type="${typeLabel}">
                                    <i class="bi ${icon} me-2"></i>
                                    <span class="fw-semibold">${quote.service}</span>
                                </div>
                                <div class="small text-muted">
                                    ${deliveryTime}
                                    ${quote.company ? ` • ${quote.company}` : ''}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary">
                                    R$ ${quote.price.toFixed(2).replace('.', ',')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            resultBox.innerHTML = `<div class="border rounded">${html}</div>`;
            resultBox.style.display = 'block';

            // Adicionar listeners para seleção
            this.setupQuoteSelection();
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
     * Add to Cart Professional
     */
    const AddToCart = {
        init() {
            // Aguardar DOM estar pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
            } else {
                this.setupEventListeners();
            }
        },

        setupEventListeners() {
            // Usar querySelectorAll para pegar TODOS os botões
            const buttons = document.querySelectorAll('.btn-add-to-cart-ml');
            
            buttons.forEach((button) => {
                // Remover listeners anteriores se existirem
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                // Adicionar novo listener
                newButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleAddToCart(newButton);
                });
            });
        },

        async handleAddToCart(button) {
            const productId = button.dataset.productId;
            if (!productId) {
                console.error('Product ID not found');
                return;
            }

            // Obter quantidade do input
            const quantityInput = Utils.$(`#quantity-${productId}`) || Utils.$('.quantity-input-ml');
            const quantity = parseInt(quantityInput?.value || '1', 10) || 1;

            // Desabilitar botão durante requisição
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Adicionando...';

            try {
                const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
                const config = JSON.parse(configEl?.textContent || '{}');
                
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', quantity);
                
                // Adicionar variation_id se selecionada
                const variationId = document.getElementById('selected-variation-id')?.value;
                if (variationId) {
                    formData.append('variation_id', variationId);
                }
                
                const response = await fetch('/carrinho/adicionar', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': config.csrf || '',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Sucesso
                    button.innerHTML = '<i class="fas fa-check me-2"></i> Adicionado!';
                    button.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
                    
                    // Atualizar contador do carrinho se existir
                    AddToCart.updateCartCount(result.cart_count || 0);
                    
                    // Mostrar modal de sucesso
                    AddToCart.showSuccessModal(result);
                    
                    // Restaurar botão após 2 segundos
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                        button.style.background = '';
                    }, 2000);
                } else {
                    throw new Error(result.message || 'Erro ao adicionar ao carrinho');
                }
            } catch (error) {
                console.error('Add to cart error:', error);
                button.disabled = false;
                button.innerHTML = originalText;
                alert(error.message || 'Erro ao adicionar produto ao carrinho. Tente novamente.');
            }
        },

        updateCartCount(count) {
            // Atualizar contador do carrinho em qualquer elemento que tenha a classe
            document.querySelectorAll('.cart-count, .cart-badge, [data-cart-count]').forEach(el => {
                el.textContent = count;
                el.style.display = count > 0 ? 'inline-block' : 'none';
            });
        },

        showSuccessModal(result) {
            // Criar modal de sucesso
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            `;
            
            modal.innerHTML = `
                <div style="
                    background: white;
                    border-radius: 12px;
                    padding: 2rem;
                    max-width: 400px;
                    width: 90%;
                    text-align: center;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                    animation: slideUp 0.3s ease;
                ">
                    <div style="font-size: 48px; color: #28a745; margin-bottom: 1rem;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem; color: #333;">Produto adicionado!</h3>
                    <p style="color: #666; margin-bottom: 1.5rem;">${result.message || 'Produto adicionado ao carrinho com sucesso.'}</p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button id="continueShopping" style="
                            padding: 0.75rem 1.5rem;
                            border: 1px solid #ddd;
                            background: white;
                            border-radius: 8px;
                            cursor: pointer;
                            font-weight: 500;
                        ">Continuar Comprando</button>
                        <button id="viewCart" style="
                            padding: 0.75rem 1.5rem;
                            border: none;
                            background: linear-gradient(135deg, #495a6d 0%, #2c3e50 100%);
                            color: white;
                            border-radius: 8px;
                            cursor: pointer;
                            font-weight: 500;
                        ">Ver Carrinho</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Event listeners
            modal.querySelector('#continueShopping').addEventListener('click', () => {
                modal.remove();
            });
            
            modal.querySelector('#viewCart').addEventListener('click', () => {
                window.location.href = '/carrinho';
            });
            
            // Fechar ao clicar fora
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
            
            // Auto-fechar após 5 segundos
            setTimeout(() => {
                if (document.body.contains(modal)) {
                    modal.remove();
                }
            }, 5000);
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
                ShippingSystem.init();
                QuantityControl.init();
                AddToCart.init();
                VariationSelector.init();

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
            // Não substituir o conteúdo do script JSON, apenas logar o erro
            console.error('PDP initialization error:', error);
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

    /**
     * Variation Selector Professional
     */
    const VariationSelector = {
        selectedAttributes: {},
        productConfig: null,
        currentVariation: null,

        init() {
            const configEl = Utils.$(CONFIG.SELECTORS.pdpConfig);
            if (!configEl) return;

            try {
                // Verificar se o conteúdo é JSON válido (não HTML)
                const content = configEl.textContent || '';
                if (content.trim().startsWith('<') || content.includes('<div')) {
                    console.error('Variation selector: Config element contains HTML instead of JSON');
                    return;
                }
                
                const config = JSON.parse(content);
                this.productConfig = config.product || {};
                
                if (!this.productConfig.has_variations) return;

                this.setupEventListeners();
                this.loadInitialVariation();
            } catch (error) {
                console.error('Variation selector init error:', error);
                console.error('Config content:', configEl.textContent?.substring(0, 200));
            }
        },

        setupEventListeners() {
            // Color swatches
            document.querySelectorAll('.color-swatch').forEach(swatch => {
                swatch.addEventListener('click', (e) => {
                    if (swatch.disabled) return;
                    this.handleAttributeSelect(e.currentTarget);
                });
            });

            // Size buttons
            document.querySelectorAll('.size-button').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (btn.disabled) return;
                    this.handleAttributeSelect(e.currentTarget);
                });
            });

            // Text buttons
            document.querySelectorAll('.text-button').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (btn.disabled) return;
                    this.handleAttributeSelect(e.currentTarget);
                });
            });

            // Image swatches
            document.querySelectorAll('.image-swatch').forEach(swatch => {
                swatch.addEventListener('click', (e) => {
                    if (swatch.disabled) return;
                    this.handleAttributeSelect(e.currentTarget);
                });
            });

            // Select dropdowns
            document.querySelectorAll('.variation-select').forEach(select => {
                select.addEventListener('change', (e) => {
                    this.handleSelectChange(e.currentTarget);
                });
            });
        },

        handleAttributeSelect(element) {
            const attributeId = element.dataset.attributeId;
            const valueId = element.dataset.valueId;

            if (!attributeId || !valueId) return;

            // Remover seleção anterior do mesmo atributo
            document.querySelectorAll(`[data-attribute-id="${attributeId}"]`).forEach(el => {
                el.classList.remove('selected');
            });

            // Adicionar seleção atual
            element.classList.add('selected');
            this.selectedAttributes[attributeId] = valueId;

            // Atualizar texto do atributo selecionado
            const label = document.querySelector(`.selected-value-text[data-attribute-id="${attributeId}"]`);
            if (label) {
                const valueText = element.title || element.textContent.trim();
                label.textContent = valueText;
            }

            this.updateVariation();
        },

        handleSelectChange(select) {
            const attributeId = select.dataset.attributeId;
            const valueId = select.value;

            if (!attributeId || !valueId) {
                delete this.selectedAttributes[attributeId];
                this.updateVariation();
                return;
            }

            this.selectedAttributes[attributeId] = valueId;
            this.updateVariation();
        },

        updateVariation() {
            const allAttributesSelected = Object.keys(this.selectedAttributes).length === 
                                         document.querySelectorAll('.variation-attribute-group').length;

            if (!allAttributesSelected) {
                this.clearVariationInfo();
                return;
            }

            // Buscar variação correspondente
            const valueIds = Object.values(this.selectedAttributes).map(id => parseInt(id)).sort();
            const variation = this.findVariationByAttributes(valueIds);

            if (variation) {
                this.currentVariation = variation;
                this.updateProductInfo(variation);
            } else {
                this.clearVariationInfo();
            }
        },

        findVariationByAttributes(valueIds) {
            if (!this.productConfig.variations) return null;

            return this.productConfig.variations.find(v => {
                const vIds = v.attribute_value_ids.sort();
                return vIds.length === valueIds.length && 
                       vIds.every((id, i) => id === valueIds[i]);
            });
        },

        updateProductInfo(variation) {
            // Atualizar preço
            const priceDisplay = Utils.$(CONFIG.SELECTORS.priceDisplay);
            if (priceDisplay) {
                priceDisplay.textContent = `R$ ${Utils.formatCurrency(variation.price)}`;
            }

            // Atualizar SKU
            const skuDisplay = Utils.$('#product-sku-display');
            if (skuDisplay) {
                skuDisplay.textContent = variation.sku || '';
            }

            // Atualizar estoque
            this.updateStockStatus(variation);

            // Atualizar quantidade máxima
            const quantityInputs = document.querySelectorAll('.quantity-input-ml');
            quantityInputs.forEach(input => {
                input.setAttribute('max', variation.stock_quantity);
                input.setAttribute('data-max-stock', variation.stock_quantity);
                if (parseInt(input.value) > variation.stock_quantity) {
                    input.value = variation.stock_quantity;
                }
            });

            // Atualizar variation_id hidden input
            const variationInput = Utils.$('#selected-variation-id');
            if (variationInput) {
                variationInput.value = variation.id;
            }

            // Atualizar imagens se a variação tiver imagens específicas
            if (variation.images && variation.images.length > 0) {
                ImageGallery.images = variation.images;
                ImageGallery.currentIndex = 0;
                ImageGallery.render();
                // Reconfigurar event listeners dos botões de navegação
                ImageGallery.setupNavigationButtons();
            }

            // Mostrar info da variação
            const infoEl = Utils.$('.selected-variation-info');
            if (infoEl) {
                infoEl.style.display = 'block';
                const nameEl = infoEl.querySelector('.variation-name');
                const skuEl = infoEl.querySelector('.variation-sku');
                if (nameEl) nameEl.textContent = variation.name;
                if (skuEl) skuEl.textContent = `SKU: ${variation.sku}`;
            }
        },

        updateStockStatus(variation) {
            const stockAvailable = Utils.$('#stock-status');
            const stockUnavailable = Utils.$('#stock-unavailable');
            const stockText = Utils.$('#stock-text');

            if (variation.in_stock && variation.stock_quantity > 0) {
                if (stockAvailable) {
                    stockAvailable.style.display = 'inline-block';
                    if (stockText) {
                        stockText.textContent = `Em estoque (${variation.stock_quantity} unidades)`;
                    }
                }
                if (stockUnavailable) stockUnavailable.style.display = 'none';
            } else {
                if (stockAvailable) stockAvailable.style.display = 'none';
                if (stockUnavailable) stockUnavailable.style.display = 'inline-block';
            }
        },

        clearVariationInfo() {
            this.currentVariation = null;
            const variationInput = Utils.$('#selected-variation-id');
            if (variationInput) variationInput.value = '';

            const infoEl = Utils.$('.selected-variation-info');
            if (infoEl) infoEl.style.display = 'none';
        },

        loadInitialVariation() {
            // Tentar carregar variação padrão
            if (this.productConfig.variations && this.productConfig.variations.length > 0) {
                const defaultVariation = this.productConfig.variations.find(v => v.is_default) || 
                                        this.productConfig.variations[0];
                
                if (defaultVariation && defaultVariation.attribute_value_ids) {
                    // Selecionar valores da variação padrão
                    defaultVariation.attribute_value_ids.forEach(valueId => {
                        const element = document.querySelector(`[data-value-id="${valueId}"]`);
                        if (element && !element.disabled) {
                            if (element.tagName === 'SELECT') {
                                element.value = valueId;
                                this.handleSelectChange(element);
                            } else {
                                this.handleAttributeSelect(element);
                            }
                        }
                    });
                }
            }
        }
    };

    // Inicializar VariationSelector
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => VariationSelector.init());
    } else {
        VariationSelector.init();
    }

    // Expor utilidades globalmente para debugging
    if (typeof process !== 'undefined' && process.env && process.env.NODE_ENV === 'development') {
        window.PDP = {
            Utils,
            ImageGallery,
            ShippingSystem,
            QuantityControl,
            AddToCart,
            VariationSelector,
            CONFIG
        };
    }

})();
