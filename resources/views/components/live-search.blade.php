{{-- Componente Live Search - Busca Instantânea --}}
<div class="live-search-wrapper position-relative">
    <div class="search-bar">
        <form action="{{ route('search') }}" method="GET" id="liveSearchForm" class="live-search-form">
            <input 
                type="text" 
                class="form-control live-search-input" 
                name="q" 
                id="liveSearchInput"
                placeholder="Procurar na Feira das Fábricas" 
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
            
            <div class="live-search-footer">
                <a href="{{ route('search') }}" class="text-decoration-none">
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
}

.live-search-form {
    display: flex;
    position: relative;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    overflow: visible;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
}

.live-search-form:focus-within {
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
    border-color: var(--accent-color);
}

.live-search-input {
    flex: 1;
    border: none;
    padding: 16px 24px;
    background: transparent;
    font-size: 15px;
    font-weight: 500;
    outline: none;
    color: var(--text-dark);
    font-family: 'Inter', sans-serif;
}

.live-search-input::placeholder {
    color: var(--text-muted);
    font-weight: 400;
}

.live-search-submit {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #ff8c42 100%);
    color: white;
    border: none;
    padding: 16px 24px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-md);
    font-weight: 600;
}

.live-search-submit:hover {
    background: linear-gradient(135deg, #ff8c42 0%, var(--secondary-color) 100%);
    transform: scale(1.02);
    box-shadow: var(--shadow-lg);
}

.live-search-submit i {
    font-size: 16px;
}

/* Dropdown de resultados */
.live-search-results {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    z-index: 1000;
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
}

.live-search-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: var(--text-muted);
}

.live-search-content {
    padding: 0.5rem;
}

.live-search-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid var(--border-color);
}

.live-search-item:last-child {
    border-bottom: none;
}

.live-search-item:hover {
    background: #f8fafc;
    transform: translateX(4px);
}

.live-search-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--radius-md);
    margin-right: 1rem;
    flex-shrink: 0;
    background: #f1f5f9;
}

.live-search-item-content {
    flex: 1;
    min-width: 0;
}

.live-search-item-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.live-search-item-description {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.live-search-item-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.live-search-item-brand {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 500;
}

.live-search-item-price {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--secondary-color);
}

.live-search-no-results {
    padding: 2rem;
    text-align: center;
    color: var(--text-muted);
}

.live-search-no-results i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}

.live-search-footer {
    padding: 1rem;
    border-top: 1px solid var(--border-color);
    text-align: center;
    background: #f8fafc;
}

.live-search-footer a {
    color: var(--secondary-color);
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.live-search-footer a:hover {
    color: var(--accent-color);
}

/* Scrollbar personalizada */
.live-search-results::-webkit-scrollbar {
    width: 6px;
}

.live-search-results::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}

.live-search-results::-webkit-scrollbar-thumb {
    background: var(--text-muted);
    border-radius: 3px;
}

.live-search-results::-webkit-scrollbar-thumb:hover {
    background: var(--accent-color);
}

/* Mobile */
@media (max-width: 768px) {
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
    const searchInput = document.getElementById('liveSearchInput');
    const searchResults = document.getElementById('liveSearchResults');
    const searchContent = document.getElementById('liveSearchContent');
    const searchLoading = document.getElementById('liveSearchLoading');
    const searchForm = document.getElementById('liveSearchForm');
    
    let searchTimeout;
    let currentRequest = null;
    
    // Função para buscar produtos
    function searchProducts(query) {
        // Cancelar requisição anterior se existir
        if (currentRequest) {
            currentRequest.abort();
        }
        
        if (!query || query.length < 2) {
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
        url.searchParams.append('limit', '8');
        
        const controller = new AbortController();
        currentRequest = controller;
        
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: controller.signal
        })
        .then(response => response.json())
        .then(data => {
            currentRequest = null;
            searchLoading.style.display = 'none';
            
            if (data.products && data.products.length > 0) {
                displayResults(data.products);
            } else {
                displayNoResults();
            }
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                console.error('Erro na busca:', error);
                currentRequest = null;
                searchLoading.style.display = 'none';
                displayError();
            }
        });
    }
    
    // Exibir resultados
    function displayResults(products) {
        const html = products.map(product => {
            const image = product.images && product.images.length > 0 
                ? product.images[0] 
                : '/images/no-image.png';
            
            const description = product.short_description || product.description || '';
            const shortDescription = description.length > 60 
                ? description.substring(0, 60) + '...' 
                : description;
            
            const price = formatPrice(product.price);
            // Construir URL do produto
            let productUrl = '/produto/';
            if (product.slug) {
                productUrl += product.slug;
            } else {
                productUrl += product.id;
            }
            
            return `
                <a href="${productUrl}" class="live-search-item">
                    <img src="${image}" alt="${escapeHtml(product.name)}" class="live-search-item-image" 
                         onerror="this.src='{{ asset('images/no-image.png') }}'">
                    <div class="live-search-item-content">
                        <div class="live-search-item-name">${escapeHtml(product.name)}</div>
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
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(() => {
            searchProducts(query);
        }, 300); // Debounce de 300ms
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
        if (query.length >= 2) {
            searchResults.style.display = 'block';
        }
    });
});
</script>
