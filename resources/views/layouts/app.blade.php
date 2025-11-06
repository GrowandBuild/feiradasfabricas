<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Feira das Fábricas')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0f172a;
            --secondary-color: #ff6b35;
            --accent-color: #0f172a;
            --dark-bg: #1e293b;
            --text-light: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --elegant-blue: #334155;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: var(--text-dark);
            line-height: 1.6;
            font-weight: 400;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            line-height: 1.3;
            color: var(--text-dark);
        }

        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-bg) 100%);
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            color: white !important;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            color: var(--secondary-color) !important;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .logo-img {
            height: 35px;
            width: auto;
        }

        .search-bar {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        .mobile-search-wrapper {
            display: none;
            flex: 1;
            max-width: 200px;
        }

        .desktop-search-wrapper {
            display: block;
        }

        .mobile-header-top {
            display: none;
            width: 100%;
        }

        .mobile-logo {
            display: none;
        }

        .navbar-content {
            display: flex;
            align-items: center;
            width: 100%;
            flex-wrap: wrap;
        }

        .search-bar form {
            display: flex;
            position: relative;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .search-bar form:focus-within {
            box-shadow: var(--shadow-xl);
            transform: translateY(-2px);
            border-color: var(--accent-color);
        }

        .search-bar input {
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

        .search-bar input::placeholder {
            color: var(--text-muted);
            font-weight: 400;
        }

        .search-bar button {
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

        .search-bar button:hover {
            background: linear-gradient(135deg, #ff8c42 0%, var(--secondary-color) 100%);
            transform: scale(1.02);
            box-shadow: var(--shadow-lg);
        }

        .search-bar button i {
            font-size: 16px;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-icon {
            color: white;
            font-size: 1.3rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 8px;
            border-radius: var(--radius-md);
            position: relative;
        }

        .header-icon:hover {
            color: var(--secondary-color);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .header-icon:active {
            transform: translateY(0);
        }

        /* Ocultar contador do carrinho */
        .header-icon .badge,
        .header-icon .cart-badge,
        .header-icon .cart-count {
            display: none !important;
        }


        .hero-section {
            background: var(--dark-bg);
            color: white;
            padding: 2rem 0;
            min-height: 70vh;
        }

        .hero-banner {
            background: #374151;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .btn-primary-custom {
            background-color: var(--secondary-color);
            border: none;
            color: var(--text-dark);
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
        }

        .btn-primary-custom:hover {
            background-color: #f59e0b;
        }

        .b2b-section {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }

        .brands-section {
            background: white;
            padding: 2rem 0;
        }

        .brand-logo {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.8rem;
            border: 2px solid #d1d5db;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .brand-logo:hover {
            border-color: var(--primary-color);
        }

        .brand-logo img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .brands-section h2,
        .brands-section p {
            color: #374151 !important;
        }

        .brands-section h6 {
            color: #374151 !important;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .footer {
            background-color: var(--text-dark);
            color: white;
            padding: 3rem 0 1rem;
        }

        @media (max-width: 768px) {
            .logo-img {
                height: 30px;
            }
            
            .hero-section {
                padding: 2rem 0;
            }
            
            .hero-banner {
                padding: 1.5rem;
            }
            
            .header-icons {
                gap: 0.5rem;
            }

            /* Mobile: Logo ao lado do search, search menor */
            .navbar-brand {
                display: none !important;
            }

            .mobile-logo {
                display: flex !important;
                font-size: 1.2rem;
                margin-right: 1rem !important;
            }

            .mobile-header-top {
                display: flex !important;
                flex-direction: row;
                align-items: center;
                margin-bottom: 0.5rem;
                width: 100%;
            }

            .mobile-search-wrapper {
                display: block !important;
                flex: 1;
                max-width: none;
                min-width: 0;
            }

            .mobile-search-wrapper .live-search-wrapper {
                max-width: 100%;
                margin: 0;
            }

            .desktop-search-wrapper {
                display: none !important;
            }

            .search-bar {
                max-width: 100%;
            }

            .search-bar form {
                border-radius: var(--radius-md);
            }

            .search-bar input {
                padding: 10px 16px;
                font-size: 14px;
            }

            .search-bar button {
                padding: 10px 16px;
            }

            .search-bar button i {
                font-size: 14px;
            }

            .navbar-content {
                flex-direction: column;
                align-items: stretch;
            }

            .header-icons {
                justify-content: center;
                margin-top: 0.5rem;
                padding-top: 0.5rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            /* Footer Mobile */
            .footer {
                padding: 2rem 0 1rem;
            }

            .footer .row > div {
                margin-bottom: 1.5rem;
            }

            .footer h5, .footer h6 {
                font-size: 1rem;
                margin-bottom: 0.75rem;
            }

            .footer ul {
                margin-bottom: 0;
            }

            .footer ul li {
                margin-bottom: 0.5rem;
            }

            .footer .text-end {
                text-align: center !important;
                margin-top: 1rem;
            }

            /* Container mobile */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            /* Espaçamentos gerais mobile */
            main {
                padding: 0;
            }

            .py-5 {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }

            .mb-4 {
                margin-bottom: 1.5rem !important;
            }

            .mb-5 {
                margin-bottom: 2rem !important;
            }
        }

        @media (max-width: 480px) {
            .logo-img {
                height: 28px;
            }

            .header-icon {
                font-size: 1.1rem;
                padding: 6px;
            }

            .footer {
                padding: 1.5rem 0 1rem;
            }

            .footer h5 {
                font-size: 0.95rem;
            }

            .footer h6 {
                font-size: 0.85rem;
            }

            .footer ul li a {
                font-size: 0.85rem;
            }

            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .py-5 {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }
        }

        /* Estilos para o componente add-to-cart */
        .quantity-controls {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .quantity-controls:focus-within {
            border-color: var(--accent-color);
            box-shadow: var(--shadow-md);
        }

        .quantity-btn {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            color: var(--text-dark);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            padding: 8px 12px;
        }

        .quantity-btn:hover {
            background: linear-gradient(135deg, var(--accent-color) 0%, #1e293b 100%);
            color: white;
            transform: scale(1.05);
        }

        .quantity-input {
            border: none;
            font-weight: 600;
            text-align: center;
            background: transparent;
            padding: 8px 12px;
        }

        .quantity-input:focus {
            box-shadow: none;
            border: none;
        }

        .btn-modern {
            border-radius: var(--radius-lg);
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: var(--shadow-md);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--accent-color) 0%, #1e293b 100%);
            color: white;
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #1e293b 0%, var(--accent-color) 100%);
            color: white;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('logo-ofc.svg') }}" alt="Feira das Fábricas" class="logo-img">
            </a>

            <!-- Navbar content (sempre aberto) -->
            <div class="navbar-content">
                <!-- Live Search Component (Desktop - centralizado) -->
                <div class="desktop-search-wrapper mx-auto">
                    @include('components.live-search')
                </div>

                <!-- Mobile: Logo and Search wrapper -->
                <div class="mobile-header-top">
                    <a class="navbar-brand mobile-logo" href="{{ route('home') }}">
                        <img src="{{ asset('logo-ofc.svg') }}" alt="Feira das Fábricas" class="logo-img">
                    </a>
                    <!-- Live Search Component (Mobile - ao lado da logo) -->
                    <div class="mobile-search-wrapper">
                        @include('components.live-search')
                    </div>
                </div>

                <!-- Header Icons -->
                <div class="header-icons ms-auto">
                    <a href="#" class="header-icon" title="Loja">
                        <i class="fas fa-store"></i>
                    </a>
                    <a href="#" class="header-icon" title="Favoritos">
                        <i class="fas fa-heart"></i>
                    </a>
                    <a href="#" class="header-icon" title="Notificações">
                        <i class="fas fa-bell"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="header-icon" title="Suporte">
                        <i class="fas fa-headphones"></i>
                    </a>
                    <a href="#" class="header-icon" title="Histórico">
                        <i class="fas fa-history"></i>
                    </a>
                    <a href="#" class="header-icon" title="Comparar">
                        <i class="fas fa-balance-scale"></i>
                    </a>
                    <a href="#" class="header-icon" title="Lista de Desejos">
                        <i class="fas fa-list"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="header-icon" title="Carrinho">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    @auth('customer')
                        @php
                            $user = Auth::guard('customer')->user();
                            $isAdmin = false;
                        @endphp
                        <div class="dropdown">
                            <a href="#" class="header-icon dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" title="Minha Conta">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">
                                    {{ $user->display_name ?? $user->name }}
                                </h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Minha Conta</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="fas fa-shopping-bag me-2"></i>Meus Pedidos</a></li>
                                @if($user->type === 'b2b')
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-building me-2"></i>Área B2B</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('customer.logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @elseauth('admin')
                        @php
                            $user = Auth::guard('admin')->user();
                            $isAdmin = true;
                        @endphp
                        <div class="dropdown">
                            <a href="#" class="header-icon dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" title="Minha Conta">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">
                                    {{ $user->name }}
                                    <span class="badge bg-warning text-dark ms-1">Admin</span>
                                </h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Painel Admin</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.products.index') }}"><i class="fas fa-box me-2"></i>Produtos</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="fas fa-shopping-cart me-2"></i>Pedidos</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.customers.index') }}"><i class="fas fa-users me-2"></i>Clientes</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="header-icon" title="Login">
                            <i class="fas fa-user"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Feira das Fábricas</h5>
                    <p>O melhor em eletrônicos e tecnologia para sua empresa e para você.</p>
                </div>
                <div class="col-md-2">
                    <h6>Produtos</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Smartphones</a></li>
                        <li><a href="#" class="text-light">Áudio</a></li>
                        <li><a href="#" class="text-light">Acessórios</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Empresa</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Sobre Nós</a></li>
                        <li><a href="{{ route('contact') }}" class="text-light">Contato</a></li>
                        <li><a href="#" class="text-light">Trabalhe Conosco</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Suporte</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Central de Ajuda</a></li>
                        <li><a href="#" class="text-light">Política de Troca</a></li>
                        <li><a href="#" class="text-light">Garantia</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6>Conta</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('register') }}" class="text-light">Criar Conta</a></li>
                        <li><a href="{{ route('register.b2b') }}" class="text-light">Conta B2B</a></li>
                        <li><a href="{{ route('login') }}" class="text-light">Login</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Feira das Fábricas. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart Script Simplificado -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Migrar carrinho apenas se necessário
            @auth('customer')
                fetch('{{ route("cart.migrate") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .catch(error => console.error('Erro ao migrar carrinho:', error));
            @endauth
        });
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>
