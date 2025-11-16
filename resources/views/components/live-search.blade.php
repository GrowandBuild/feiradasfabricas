{{-- Componente Live Search - Busca Instantânea --}}
<div class="live-search-wrapper">
    <div class="live-search-container">
        <form action="{{ route('products') }}" method="GET" id="liveSearchForm" class="live-search-form">
            <input 
                type="text" 
                class="form-control live-search-input" 
                name="q" 
                id="liveSearchInput"
                placeholder="Procurar na Feira das Fabricas" 
                value="{{ request('q') }}"
                autocomplete="off"
                aria-label="Buscar produtos"
            >
            <button type="submit" class="live-search-submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
        
        {{-- Dropdown de resultados --}}
        <div class="live-search-results" id="liveSearchResults" style="display: none;">
            <div class="live-search-loading" id="liveSearchLoading" style="display: none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <span class="ms-2">Buscando produtos...</span>
            </div>
            
            <div class="live-search-content" id="liveSearchContent">
                {{-- Resultados serão inseridos aqui via JavaScript --}}
            </div>
            
            <div class="live-search-footer" id="liveSearchFooter">
                <a href="{{ route('products') }}" class="text-decoration-none" id="liveSearchFooterLink">
                    Ver todos os resultados
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.live-search-wrapper {
    max-width: 600px;
    margin: 0 auto;
    position: relative !important;
    z-index: 1000;
}

.live-search-container {
    position: relative !important;
    width: 100%;
    overflow: visible !important;
}

.live-search-form {
    display: flex;
    position: relative;
    background: white;
    border-radius: 50px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06) !important;
    overflow: visible !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none !important;
    gap: 0;
}

.live-search-form:focus-within {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15), 0 2px 6px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-1px);
}

.live-search-input {
    flex: 1;
    border: none !important;
    padding: 16px 28px;
    background: transparent;
    font-size: 15px;
    font-weight: 500;
    outline: none;
    color: #1e293b;
    font-family: 'Inter', sans-serif;
    border-radius: 50px 0 0 50px !important;
}

.live-search-input:focus {
    box-shadow: none;
    border: none;
}

.live-search-input::placeholder {
    color: #94a3b8;
    font-weight: 400;
}

.live-search-submit {
    background: #FF9900 !important;
    color: white;
    border: none !important;
    padding: 16px 28px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border-radius: 0 50px 50px 0 !important;
    box-shadow: none !important;
    min-width: 60px;
}

.live-search-submit:hover {
    background: #FFAA00 !important;
    transform: none;
    box-shadow: none !important;
}

.live-search-submit:active {
    background: #E68800 !important;
    transform: scale(0.98);
}

.live-search-submit i {
    font-size: 18px;
    color: white;
}

/* Dropdown de resultados */
.live-search-results {
    position: absolute !important;
    top: calc(100% + 10px) !important;
    left: 0 !important;
    right: 0 !important;
    background: white !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    z-index: 9999 !important;
    max-height: 500px;
    overflow-y: auto;
    border: none !important;
    margin-top: 0;
    padding: 12px;
    backdrop-filter: blur(10px);
    /* Evita bloquear cliques na página quando o dropdown estiver visível */
    pointer-events: none;
}

.live-search-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem;
    color: #64748b;
    font-weight: 500;
    font-size: 0.9rem;
}

.live-search-loading .spinner-border {
    width: 1.2rem;
    height: 1.2rem;
    border-width: 2px;
    border-color: #FF9900;
    border-right-color: transparent;
}

.live-search-content {
    padding: 0.5rem;
    /* Reabilita interação apenas no conteúdo */
    pointer-events: auto;
}

.live-search-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-radius: 12px !important;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    color: inherit;
    border: none;
    margin-bottom: 4px;
    background: transparent;
}

.live-search-item:last-child {
    margin-bottom: 0;
}

.live-search-item:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    transform: translateX(6px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.live-search-item-image {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px !important;
    margin-right: 14px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: transform 0.25s ease, box-shadow 0.25s ease, opacity 0.2s ease-in-out;
    display: block;
    /* Prevenir flicker - otimizações de renderização */
    image-rendering: -webkit-optimize-contrast;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    transform: translateZ(0);
    will-change: transform, opacity;
    /* Prevenir recarregamento */
    content-visibility: auto;
}

.live-search-item:hover .live-search-item-image {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.live-search-item-content {
    flex: 1;
    min-width: 0;
}

.live-search-item-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #1e293b;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    letter-spacing: -0.01em;
}

.live-search-item-description {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

.live-search-item-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.live-search-item-brand {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.live-search-item-price {
    font-weight: 700;
    font-size: 1.15rem;
    color: #FF9900;
    letter-spacing: -0.02em;
}

.live-search-no-results {
    padding: 3rem 2rem;
    text-align: center;
    color: #64748b;
}

.live-search-no-results i {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    opacity: 0.4;
    color: #94a3b8;
}

.live-search-no-results p {
    font-size: 0.95rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
    color: #475569;
}

.live-search-no-results small {
    font-size: 0.85rem;
    color: #94a3b8;
}

.live-search-footer {
    padding: 12px 16px;
    border-top: 1px solid #e2e8f0;
    text-align: center;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 0 0 16px 16px;
    margin: 0 -8px -8px -8px;
    pointer-events: auto;
}

.live-search-footer a {
    color: #FF9900;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.live-search-footer a:hover {
    color: #FFAA00;
    transform: translateX(2px);
}

/* Scrollbar personalizada */
.live-search-results::-webkit-scrollbar {
    width: 8px;
}

.live-search-results::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 0 0 16px 16px;
}

.live-search-results::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
    border: 2px solid transparent;
    background-clip: padding-box;
}

.live-search-results::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
    background-clip: padding-box;
}

/* Mobile */
@media (max-width: 768px) {
    .live-search-wrapper {
        max-width: 100%;
    }

    .live-search-form {
        border-radius: 12px !important;
    }

    .live-search-input {
        padding: 10px 16px;
        font-size: 14px;
        border-radius: 12px 0 0 12px !important;
    }

    .live-search-submit {
        padding: 10px 16px;
        min-width: 50px;
        border-radius: 0 12px 12px 0 !important;
    }

    .live-search-submit i {
        font-size: 14px;
    }
    
    .live-search-item-image {
        width: 60px;
        height: 60px;
    }
    
    .live-search-item-name {
        font-size: 0.9rem;
    }
    
    .live-search-item-price {
        font-size: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Live Search inicializado');
    
    const searchInput = document.getElementById('liveSearchInput');
    const searchResults = document.getElementById('liveSearchResults');
    const searchContent = document.getElementById('liveSearchContent');
    const searchLoading = document.getElementById('liveSearchLoading');
    const searchForm = document.getElementById('liveSearchForm');
    
    if (!searchInput || !searchResults || !searchContent) {
        console.error('❌ Elementos do Live Search não encontrados!');
        return;
    }
    
    console.log('✅ Elementos encontrados:', {
        input: !!searchInput,
        results: !!searchResults,
        content: !!searchContent
    });
    
    let searchTimeout;
    let currentRequest = null;
    
    // Função para buscar produtos
    function searchProducts(query) {
        // Cancelar requisição anterior se existir
        if (currentRequest) {
            currentRequest.abort();
        }
        
        if (!query || query.length < 1) {
            searchResults.style.display = 'none';
            return;
        }
        
        // Mostrar loading
        searchLoading.style.display = 'flex';
        searchContent.innerHTML = '';
        searchResults.style.display = 'block';
        
        // Fazer requisição AJAX
        const url = new URL('/api/search/live', window.location.origin);
        url.searchParams.append('q', query);
    // Definir limite dinâmico: desktop 12, mobile 8
    const isMobile = window.matchMedia('(max-width: 768px)').matches;
    url.searchParams.append('limit', isMobile ? '8' : '12');
        
        const controller = new AbortController();
        currentRequest = controller;
        
        console.log('🔍 Buscando:', query, 'URL:', url.toString());
        
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            signal: controller.signal
        })
        .then(response => {
            console.log('📥 Resposta recebida:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📦 Dados recebidos:', data);
            currentRequest = null;
            searchLoading.style.display = 'none';
            
            if (data.products && data.products.length > 0) {
                console.log('✅ Produtos encontrados:', data.products.length);
                displayResults(data.products);
            } else {
                console.log('❌ Nenhum produto encontrado');
                displayNoResults();
            }
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                console.error('❌ Erro na busca:', error);
                currentRequest = null;
                searchLoading.style.display = 'none';
                displayError();
            }
        });
    }
    
    // Exibir resultados
    function displayResults(products) {
        const html = products.map(product => {
            const image = product.cover_image || product.image || (product.images && product.images.length > 0 
                ? product.images[0] 
                : '/images/no-image.png');
            
            const description = product.short_description || product.description || '';
            const shortDescription = description.length > 60 
                ? description.substring(0, 60) + '...' 
                : description;
            
            const price = formatPrice(product.price);
            // Construir URL do produto
            let productUrl = product.variant_url || (function(){
                let base = '/produto/';
                if (product.slug) { return base + product.slug; }
                return base + product.id;
            })();
            const title = product.display_name || product.name;
            
            return `
                <a href="${productUrl}" class="live-search-item">
                <img src="${image}" alt="${escapeHtml(title)}" class="live-search-item-image" 
                    data-fallback="{{ asset('images/no-image.png') }}">
                    <div class="live-search-item-content">
                        <div class="live-search-item-name">${escapeHtml(title)}</div>
                        ${shortDescription ? `<div class="live-search-item-description">${escapeHtml(shortDescription)}</div>` : ''}
                        <div class="live-search-item-footer">
                            ${product.brand ? `<span class="live-search-item-brand">${escapeHtml(product.brand)}</span>` : ''}
                            <span class="live-search-item-price">${price}</span>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
        
    searchContent.innerHTML = html;
    // Atualizar link do rodapé com query e total
    updateFooterLink(products.length);
        
        // Prevenir flicker: adicionar event listeners após inserir HTML
        const images = searchContent.querySelectorAll('.live-search-item-image');
        images.forEach(img => {
            const fallbackSrc = img.getAttribute('data-fallback') || '{{ asset('images/no-image.png') }}';
            let errorHandled = false;
            let imageLoaded = false;
            
            // Adicionar listener de erro uma única vez
            img.addEventListener('error', function() {
                if (!errorHandled && this.src !== fallbackSrc) {
                    errorHandled = true;
                    this.onerror = null; // Prevenir loop infinito
                    this.src = fallbackSrc;
                }
            }, { once: true });
            
            // Adicionar listener de load para estabilizar
            img.addEventListener('load', function() {
                if (!imageLoaded) {
                    imageLoaded = true;
                    // Forçar repaint para estabilizar
                    this.style.opacity = '1';
                }
            }, { once: true });
            
            // Definir opacity inicial para prevenir flicker
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.2s ease-in-out';
            
            // Se a imagem já está carregada (cache), mostrar imediatamente
            if (img.complete && img.naturalHeight !== 0) {
                img.style.opacity = '1';
                imageLoaded = true;
            }
        });
    }
    
    // Exibir mensagem de nenhum resultado
    function displayNoResults() {
        searchContent.innerHTML = `
            <div class="live-search-no-results">
                <i class="fas fa-search"></i>
                <p>Nenhum produto encontrado</p>
                <small>Tente buscar com outros termos</small>
            </div>
        `;
        updateFooterLink(0);
    }
    
    // Exibir mensagem de erro
    function displayError() {
        searchContent.innerHTML = `
            <div class="live-search-no-results">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Erro ao buscar produtos</p>
                <small>Tente novamente em alguns instantes</small>
            </div>
        `;
    }
    
    // Formatar preço
    function formatPrice(price) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(price);
    }
    
    // Escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Event listener no input
    function updateFooterLink(total){
        const footerLink = document.getElementById('liveSearchFooterLink');
        if(!footerLink) return;
        const q = searchInput.value.trim();
        const url = new URL('{{ route('products') }}', window.location.origin);
        if(q.length >= 2){ url.searchParams.set('q', q); }
        footerLink.href = url.toString();
        footerLink.innerHTML = total > 0
            ? `Ver todos os resultados (${total}) <i class="fas fa-arrow-right ms-1"></i>`
            : `Ver todos os resultados <i class="fas fa-arrow-right ms-1"></i>`;
    }

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        console.log('⌨️ Input digitado:', query);
        
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            console.log('⏱️ Executando busca após debounce...');
            searchProducts(query);
        }, 300); // Debounce de 300ms
    });
    
    // Event listener para debug
    searchInput.addEventListener('focus', function() {
        console.log('👁️ Input focado');
        const query = searchInput.value.trim();
        if (query.length >= 1) {
            searchResults.style.display = 'block';
            updateFooterLink(searchContent.querySelectorAll('.live-search-item').length);
        }
    });
    
    // Fechar dropdown ao clicar fora
    document.addEventListener('click', function(e) {
        const isClickInside = searchInput.contains(e.target) || 
                              searchResults.contains(e.target) ||
                              searchForm.contains(e.target);
        
        if (!isClickInside) {
            searchResults.style.display = 'none';
        }
    });

    // Se clicar no "vazio" do dropdown (fora dos itens/links), fecha também
    searchResults.addEventListener('click', function(e) {
        const clickedItem = e.target.closest('.live-search-item');
        const clickedFooter = e.target.closest('.live-search-footer');
        if (!clickedItem && !clickedFooter) {
            searchResults.style.display = 'none';
        }
    });

    // Ao rolar a página, fecha o dropdown para evitar sobrepor o conteúdo
    window.addEventListener('scroll', function() {
        if (searchResults.style.display !== 'none') {
            searchResults.style.display = 'none';
        }
    }, { passive: true });
    
    // Fechar dropdown ao pressionar Escape
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchResults.style.display = 'none';
            searchInput.blur();
        }
    });
    
    // Manter dropdown aberto ao focar no input
    searchInput.addEventListener('focus', function() {
        const query = searchInput.value.trim();
        if (query.length >= 1) {
            searchResults.style.display = 'block';
        }
    });
});
</script>
