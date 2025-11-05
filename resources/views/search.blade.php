@extends('layouts.app')

@section('title', 'Busca - Feira das Fábricas')

@section('content')
<div class="amazon-search-page">
    <!-- Header Estilo Amazon -->
    <header class="amazon-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="amazon-logo">
                        <a href="/">
                            <span class="logo-text">Feira das Fábricas</span>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="amazon-search-bar">
                        <form class="search-form" id="searchForm">
                            <div class="search-wrapper">
                                <select class="search-category" id="searchCategory">
                                    <option value="all">Todas as categorias</option>
                                    <option value="smartphones">Smartphones</option>
                                    <option value="acessorios">Acessórios</option>
                                    <option value="tablets">Tablets</option>
                                </select>
                                <input 
                                    type="text" 
                                    class="search-input" 
                                    id="searchInput"
                                    placeholder="Buscar produtos..."
                                    autocomplete="off"
                                >
                                <button type="submit" class="search-button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="header-actions">
                        <a href="#" class="header-link">
                            <i class="fas fa-user"></i>
                            <span>Conta</span>
                        </a>
                        <a href="#" class="header-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Carrinho</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section Elegante -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        <i class="fas fa-crown"></i>
                        Encontre os Melhores Produtos
                    </h1>
                    <p class="hero-subtitle">
                        Descubra uma seleção exclusiva de produtos de alta qualidade
                        com preços competitivos e entrega rápida
                    </p>
                    <div class="hero-features">
                        <div class="feature-item">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Entrega Rápida</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Garantia Total</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-star"></i>
                            <span>Produtos Premium</span>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-card">
                        <i class="fas fa-shopping-bag"></i>
                        <h3>Feira das Fábricas</h3>
                        <p>Sua loja de confiança</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar de Filtros -->
                <aside class="col-md-3 sidebar">
                    <div class="filters-section">
                        <h3>Filtrar por</h3>
                        
                        <!-- Filtro de Marca -->
                        <div class="filter-group">
                            <h4>Marca</h4>
                            <div class="filter-options">
                                <label class="filter-option">
                                    <input type="checkbox" value="Apple" name="brand">
                                    <span class="checkmark"></span>
                                    Apple
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" value="Samsung" name="brand">
                                    <span class="checkmark"></span>
                                    Samsung
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" value="Xiaomi" name="brand">
                                    <span class="checkmark"></span>
                                    Xiaomi
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" value="Motorola" name="brand">
                                    <span class="checkmark"></span>
                                    Motorola
                                </label>
                            </div>
                        </div>
                        
                        <!-- Filtro de Preço -->
                        <div class="filter-group">
                            <h4>Preço</h4>
                            <div class="price-range">
                                <input type="range" class="price-slider" min="0" max="10000" value="5000" id="priceSlider">
                                <div class="price-labels">
                                    <span>R$ 0</span>
                                    <span>R$ 10.000</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtro de Avaliação -->
                        <div class="filter-group">
                            <h4>Avaliação</h4>
                            <div class="rating-filters">
                                <label class="filter-option">
                                    <input type="checkbox" value="5" name="rating">
                                    <span class="checkmark"></span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <span>& acima</span>
                                    </div>
                                </label>
                                <label class="filter-option">
                                    <input type="checkbox" value="4" name="rating">
                                    <span class="checkmark"></span>
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <span>& acima</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>
                
                <!-- Área de Resultados -->
                <section class="col-md-9 results-section">
                    <!-- Cabeçalho dos Resultados -->
                    <div class="results-header">
                        <div class="results-info">
                            <h2 id="resultsTitle">Resultados da busca</h2>
                            <span id="resultsCount" class="results-count">Digite para buscar</span>
                        </div>
                        <div class="sort-options">
                            <select id="sortSelect" class="sort-select">
                                <option value="relevance">Mais relevantes</option>
                                <option value="price_asc">Menor preço</option>
                                <option value="price_desc">Maior preço</option>
                                <option value="name">Nome A-Z</option>
                                <option value="rating">Melhor avaliados</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Produtos -->
                    <div class="products-grid" id="productsGrid">
                        <div class="welcome-message">
                            <i class="fas fa-search"></i>
                            <h3>Encontre o que você procura</h3>
                            <p>Use a barra de busca acima para encontrar produtos</p>
                        </div>
                    </div>
                    
                    <!-- Paginação -->
                    <div class="pagination-container" id="paginationContainer" style="display: none;">
                        <nav aria-label="Paginação">
                            <ul class="pagination justify-content-center">
                                <!-- Paginação será inserida aqui -->
                            </ul>
                        </nav>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>

<style>
/* Reset e Base */
* {
    box-sizing: border-box;
}

body {
    font-family: 'Amazon Ember', Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #eaeded;
    color: #0f1111;
}

/* Header Estilo Amazon */
.amazon-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    padding: 15px 0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.3);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
}

.amazon-logo a {
    text-decoration: none;
    color: #ff9900;
    font-weight: bold;
    font-size: 24px;
}

.logo-text {
    color: #ff9900;
}

.amazon-search-bar {
    padding: 0 20px;
}

.search-form {
    width: 100%;
}

.search-wrapper {
    display: flex;
    background: white;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.search-category {
    background: #f3f3f3;
    border: none;
    padding: 10px 15px;
    border-right: 1px solid #ddd;
    font-size: 14px;
    color: #0f1111;
    cursor: pointer;
}

.search-input {
    flex: 1;
    border: none;
    padding: 12px 15px;
    font-size: 16px;
    outline: none;
}

.search-button {
    background: #ff9900;
    border: none;
    padding: 10px 20px;
    color: white;
    cursor: pointer;
    transition: background 0.2s;
}

.search-button:hover {
    background: #e88a00;
}

.header-actions {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
}

.header-link {
    color: white;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 12px;
    transition: color 0.2s;
}

.header-link:hover {
    color: #ff9900;
}

.header-link i {
    font-size: 20px;
    margin-bottom: 2px;
}

/* Hero Section Elegante */
.hero-section {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(148,163,184,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    display: flex;
    align-items: center;
    gap: 60px;
    position: relative;
    z-index: 2;
}

.hero-text {
    flex: 1;
    color: white;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
    background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-title i {
    color: #ff9900;
    margin-right: 15px;
    font-size: 3rem;
}

.hero-subtitle {
    font-size: 1.3rem;
    color: #cbd5e1;
    margin-bottom: 40px;
    line-height: 1.6;
    max-width: 600px;
}

.hero-features {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #e2e8f0;
    font-size: 1.1rem;
    font-weight: 500;
}

.feature-item i {
    color: #ff9900;
    font-size: 1.5rem;
    width: 24px;
    text-align: center;
}

.hero-visual {
    flex: 0 0 300px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    color: white;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    transform: rotate(-5deg);
    transition: transform 0.3s ease;
}

.hero-card:hover {
    transform: rotate(0deg) scale(1.05);
}

.hero-card i {
    font-size: 4rem;
    color: #ff9900;
    margin-bottom: 20px;
}

.hero-card h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.hero-card p {
    color: #cbd5e1;
    font-size: 1rem;
}

/* Conteúdo Principal */
.main-content {
    padding: 20px 0;
}

/* Sidebar */
.sidebar {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-right: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: fit-content;
}

.filters-section h3 {
    color: #0f1111;
    font-size: 18px;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.filter-group {
    margin-bottom: 25px;
}

.filter-group h4 {
    color: #0f1111;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 10px;
}

.filter-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-option {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
    color: #0f1111;
}

.filter-option input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #ddd;
    border-radius: 3px;
    margin-right: 10px;
    position: relative;
    transition: all 0.2s;
}

.filter-option input[type="checkbox"]:checked + .checkmark {
    background: #ff9900;
    border-color: #ff9900;
}

.filter-option input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 12px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.price-range {
    padding: 10px 0;
}

.price-slider {
    width: 100%;
    height: 6px;
    border-radius: 3px;
    background: #ddd;
    outline: none;
    -webkit-appearance: none;
}

.price-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ff9900;
    cursor: pointer;
}

.price-labels {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.stars {
    display: flex;
    align-items: center;
    gap: 2px;
}

.stars i {
    color: #ff9900;
    font-size: 12px;
}

/* Área de Resultados */
.results-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #ddd;
}

.results-info h2 {
    font-size: 20px;
    color: #0f1111;
    margin: 0;
}

.results-count {
    font-size: 14px;
    color: #666;
}

.sort-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background: white;
}

/* Grid de Produtos */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.product-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.2s;
    cursor: pointer;
    position: relative;
}

.product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.product-image {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #f0f2f5 0%, #e4e6ea 100%);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    position: relative;
    overflow: hidden;
}

.product-image i {
    font-size: 48px;
    color: #666;
}

.product-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ff9900;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.product-title {
    font-size: 16px;
    font-weight: 500;
    color: #0f1111;
    margin-bottom: 8px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-brand {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}

.product-rating {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.product-stars {
    color: #ff9900;
    margin-right: 5px;
}

.product-rating-count {
    font-size: 12px;
    color: #666;
}

.product-price {
    font-size: 18px;
    font-weight: bold;
    color: #b12704;
    margin-bottom: 10px;
}

.product-price-original {
    font-size: 14px;
    color: #666;
    text-decoration: line-through;
    margin-left: 8px;
}

.product-actions {
    display: flex;
    gap: 8px;
}

.btn-amazon {
    background: #ff9900;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
    flex: 1;
}

.btn-amazon:hover {
    background: #e88a00;
}

.btn-secondary-amazon {
    background: white;
    color: #0f1111;
    border: 1px solid #ddd;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary-amazon:hover {
    background: #f7f7f7;
    border-color: #999;
}

/* Mensagens */
.welcome-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.welcome-message i {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 20px;
}

.welcome-message h3 {
    color: #0f1111;
    margin-bottom: 10px;
}

.loading-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.loading-message i {
    animation: spin 1s linear infinite;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.error-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 20px;
    color: #b12704;
    background: #fdf2f2;
    border-radius: 8px;
}

/* Paginação */
.pagination-container {
    margin-top: 40px;
    text-align: center;
}

.pagination {
    display: inline-flex;
    gap: 5px;
}

.page-link {
    color: #0f1111;
    border: 1px solid #ddd;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s;
}

.page-link:hover {
    background: #f7f7f7;
    border-color: #999;
}

.page-link.active {
    background: #ff9900;
    color: white;
    border-color: #ff9900;
}

/* Responsivo */
@media (max-width: 768px) {
    .amazon-header .row {
        flex-direction: column;
        gap: 15px;
    }
    
    .amazon-search-bar {
        padding: 0;
    }
    
    .hero-section {
        padding: 60px 0;
    }
    
    .hero-content {
        flex-direction: column;
        gap: 40px;
        text-align: center;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-features {
        justify-content: center;
    }
    
    .hero-visual {
        flex: none;
    }
    
    .hero-card {
        transform: none;
    }
    
    .hero-card:hover {
        transform: scale(1.05);
    }
    
    .sidebar {
        margin-right: 0;
        margin-bottom: 20px;
    }
    
    .results-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .header-actions {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 40px 0;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .hero-features {
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }
    
    .feature-item {
        font-size: 1rem;
    }
    
    .hero-card {
        padding: 30px 20px;
    }
    
    .hero-card i {
        font-size: 3rem;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .main-content {
        padding: 10px 0;
    }
    
    .sidebar, .results-section {
        padding: 15px;
    }
}
</style>

<script>
class AmazonSearch {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.searchForm = document.getElementById('searchForm');
        this.searchCategory = document.getElementById('searchCategory');
        this.sortSelect = document.getElementById('sortSelect');
        this.productsGrid = document.getElementById('productsGrid');
        this.resultsTitle = document.getElementById('resultsTitle');
        this.resultsCount = document.getElementById('resultsCount');
        this.paginationContainer = document.getElementById('paginationContainer');
        
        this.currentQuery = '';
        this.currentFilters = {};
        this.currentSort = 'relevance';
        this.currentPage = 1;
        this.searchTimeout = null;
        
        this.init();
    }
    
    init() {
        console.log('🛒 Inicializando busca estilo Amazon...');
        
        // Event listeners
        this.searchForm.addEventListener('submit', this.handleSearch.bind(this));
        this.searchInput.addEventListener('input', this.handleInput.bind(this));
        this.sortSelect.addEventListener('change', this.handleSort.bind(this));
        
        // Filtros
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', this.handleFilter.bind(this));
        });
        
        document.getElementById('priceSlider').addEventListener('input', this.handlePriceFilter.bind(this));
        
        console.log('✅ Busca Amazon inicializada!');
    }
    
    handleInput(e) {
        const query = e.target.value.trim();
        this.currentQuery = query;
        
        clearTimeout(this.searchTimeout);
        
        if (query.length < 2) {
            this.showWelcome();
            return;
        }
        
        this.searchTimeout = setTimeout(() => {
            this.performSearch();
        }, 500);
    }
    
    handleSearch(e) {
        e.preventDefault();
        this.performSearch();
    }
    
    handleSort(e) {
        this.currentSort = e.target.value;
        this.performSearch();
    }
    
    handleFilter(e) {
        const name = e.target.name;
        const value = e.target.value;
        
        if (!this.currentFilters[name]) {
            this.currentFilters[name] = [];
        }
        
        if (e.target.checked) {
            this.currentFilters[name].push(value);
        } else {
            this.currentFilters[name] = this.currentFilters[name].filter(v => v !== value);
        }
        
        this.performSearch();
    }
    
    handlePriceFilter(e) {
        const maxPrice = e.target.value;
        this.currentFilters.max_price = maxPrice;
        this.performSearch();
    }
    
    async performSearch() {
        if (!this.currentQuery) {
            this.showWelcome();
            return;
        }
        
        console.log('🔍 Buscando:', this.currentQuery);
        
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                q: this.currentQuery,
                sort: this.currentSort,
                per_page: 12,
                page: this.currentPage
            });
            
            // Adicionar filtros
            Object.keys(this.currentFilters).forEach(key => {
                if (this.currentFilters[key].length > 0) {
                    params.append(key, this.currentFilters[key].join(','));
                }
            });
            
            const response = await fetch(`/api/search?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            this.displayResults(data);
            
        } catch (error) {
            console.error('Erro na busca:', error);
            this.showError(error.message);
        }
    }
    
    displayResults(data) {
        if (!data.products || !data.products.data || data.products.data.length === 0) {
            this.showNoResults();
            return;
        }
        
        const products = data.products.data;
        this.resultsTitle.textContent = `Resultados para "${this.currentQuery}"`;
        this.resultsCount.textContent = `${products.length} produtos encontrados`;
        
        const html = products.map(product => this.createProductCard(product)).join('');
        this.productsGrid.innerHTML = html;
        
        // Mostrar paginação se necessário
        if (data.products.last_page > 1) {
            this.showPagination(data.products);
        } else {
            this.paginationContainer.style.display = 'none';
        }
    }
    
    createProductCard(product) {
        const originalPrice = parseFloat(product.price) * 1.2; // Simular preço original
        const discount = Math.round(((originalPrice - parseFloat(product.price)) / originalPrice) * 100);
        
        return `
            <div class="product-card" onclick="window.open('/products/${product.id}', '_blank')">
                ${discount > 0 ? `<div class="product-badge">-${discount}%</div>` : ''}
                <div class="product-image">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="product-title">${product.name}</div>
                <div class="product-brand">${product.brand}</div>
                <div class="product-rating">
                    <div class="product-stars">
                        ${this.generateStars(Math.random() * 2 + 3)}
                    </div>
                    <span class="product-rating-count">(${Math.floor(Math.random() * 1000) + 50})</span>
                </div>
                <div class="product-price">
                    R$ ${parseFloat(product.price).toFixed(2)}
                    ${discount > 0 ? `<span class="product-price-original">R$ ${originalPrice.toFixed(2)}</span>` : ''}
                </div>
                <div class="product-actions">
                    <button class="btn-amazon">Adicionar ao carrinho</button>
                    <button class="btn-secondary-amazon">Lista de desejos</button>
                </div>
            </div>
        `;
    }
    
    generateStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        let stars = '';
        
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star"></i>';
        }
        
        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        }
        
        const emptyStars = 5 - Math.ceil(rating);
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star"></i>';
        }
        
        return stars;
    }
    
    showPagination(pagination) {
        this.paginationContainer.style.display = 'block';
        
        let html = '';
        
        // Botão anterior
        if (pagination.current_page > 1) {
            html += `<a href="#" class="page-link" onclick="amazonSearch.goToPage(${pagination.current_page - 1})">‹</a>`;
        }
        
        // Páginas
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<span class="page-link active">${i}</span>`;
            } else {
                html += `<a href="#" class="page-link" onclick="amazonSearch.goToPage(${i})">${i}</a>`;
            }
        }
        
        // Botão próximo
        if (pagination.current_page < pagination.last_page) {
            html += `<a href="#" class="page-link" onclick="amazonSearch.goToPage(${pagination.current_page + 1})">›</a>`;
        }
        
        this.paginationContainer.querySelector('.pagination').innerHTML = html;
    }
    
    goToPage(page) {
        this.currentPage = page;
        this.performSearch();
    }
    
    showWelcome() {
        this.productsGrid.innerHTML = `
            <div class="welcome-message">
                <i class="fas fa-search"></i>
                <h3>Encontre o que você procura</h3>
                <p>Use a barra de busca acima para encontrar produtos</p>
            </div>
        `;
        this.resultsTitle.textContent = 'Resultados da busca';
        this.resultsCount.textContent = 'Digite para buscar';
        this.paginationContainer.style.display = 'none';
    }
    
    showLoading() {
        this.productsGrid.innerHTML = `
            <div class="loading-message">
                <i class="fas fa-spinner"></i>
                Buscando produtos...
            </div>
        `;
    }
    
    showNoResults() {
        this.productsGrid.innerHTML = `
            <div class="error-message">
                <i class="fas fa-search"></i>
                <h3>Nenhum produto encontrado</h3>
                <p>Tente ajustar os filtros ou usar termos diferentes</p>
            </div>
        `;
        this.resultsTitle.textContent = `Resultados para "${this.currentQuery}"`;
        this.resultsCount.textContent = '0 produtos encontrados';
        this.paginationContainer.style.display = 'none';
    }
    
    showError(message) {
        this.productsGrid.innerHTML = `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Erro na busca</h3>
                <p>${message}</p>
            </div>
        `;
    }
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    window.amazonSearch = new AmazonSearch();
});
</script>
@endsection