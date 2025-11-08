<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel Administrativo') - Feira das Fábricas</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo-ofc.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e293b;
            --primary-dark: #0f172a;
            --secondary-color: #64748b;
            --accent-color: #ff8c00;
            --accent-dark: #e67e00;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --border-radius: 0.75rem;
            --border-radius-sm: 0.5rem;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            box-shadow: var(--shadow-md);
            position: relative;
            z-index: 10;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.05"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 0.875rem 1.25rem;
            margin: 0.25rem 0.75rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
            font-weight: 500;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.15);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }

        .sidebar-brand {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: opacity 0.2s ease;
        }

        .sidebar-brand:hover {
            color: white;
            text-decoration: none;
            opacity: 0.9;
        }

        .sidebar-brand img {
            transition: transform 0.2s ease;
        }

        .sidebar-brand:hover img {
            transform: scale(1.05);
        }

        .main-content {
            background-color: var(--light-bg);
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .card {
            border: none;
            box-shadow: var(--shadow-sm);
            border-radius: var(--border-radius);
            background-color: var(--card-bg);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-body {
            padding: 1.5rem;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stats-card-accent {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-dark) 100%);
            color: white;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .stats-card .card-body {
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .stats-card .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stats-card .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .stats-card .stats-icon {
            font-size: 2.5rem;
            opacity: 0.3;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }

        .navbar-admin {
            background: var(--card-bg);
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border-color);
            padding: 0.9rem 0;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .admin-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .page-header-wrap {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .page-header-wrap .page-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.1) 0%, rgba(148, 163, 184, 0.2) 100%);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .page-heading {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .page-description {
            margin: 0.15rem 0 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .page-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .admin-header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn-go-store {
            border: 1px solid var(--border-color);
            background-color: transparent;
            color: var(--text-secondary);
            font-weight: 600;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.9rem;
        }

        .btn-go-store:hover {
            color: white;
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        .admin-user-chip {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.12);
        }

        .admin-user-chip .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: var(--accent-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .admin-user-chip .admin-user-details {
            line-height: 1.1;
        }

        .admin-user-chip .admin-user-details .name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .admin-user-chip .admin-user-details .role {
            font-size: 0.7rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .btn {
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: var(--accent-dark);
            border-color: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-primary {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            transform: translateY(-1px);
        }

        .form-control, .form-select {
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--light-bg);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--text-primary);
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background-color: rgba(37, 99, 235, 0.02);
        }

        .badge {
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            border-radius: var(--border-radius-sm);
        }

        .alert {
            border-radius: var(--border-radius-sm);
            border: none;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .alert-info {
            background-color: rgba(6, 182, 212, 0.1);
            color: var(--info-color);
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .page-header-accent {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-dark) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .page-subtitle {
            opacity: 0.9;
            margin: 0.5rem 0 0;
            font-size: 1.1rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding-bottom: 90px;
            }
        }

        .admin-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 0.35rem 0;
            z-index: 999;
            display: none;
        }

        .admin-bottom-nav .nav {
            position: relative;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: center;
            gap: 0.35rem;
            overflow-x: auto;
            overflow-y: hidden;
            padding: 0 0.75rem;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x proximity;
        }

        .admin-bottom-nav .nav::-webkit-scrollbar {
            display: none;
        }

        .admin-bottom-nav .nav[data-admin-loop="true"] {
            scroll-behavior: smooth;
        }


        .admin-bottom-nav .nav {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .admin-bottom-nav .nav-link {
            flex: 0 0 auto;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            transition: all 0.2s ease;
            scroll-snap-align: center;
            min-width: 4.5rem;
        }

        .admin-bottom-nav .nav-link i {
            font-size: 1.1rem;
        }

        .admin-bottom-nav .nav-link.active {
            color: var(--accent-color);
            background: rgba(255, 140, 0, 0.12);
        }

        @media (max-width: 992px) {
            .page-heading {
                font-size: 1.45rem;
            }

            .page-description {
                font-size: 0.85rem;
            }

            .admin-header-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .navbar-admin {
                padding: 0.75rem 0;
            }

            .page-header-wrap {
                gap: 0.65rem;
            }

            .page-header-wrap .page-icon {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }

            .page-heading {
                font-size: 1.35rem;
            }

            .page-description {
                font-size: 0.8rem;
            }

            .admin-header-actions {
                gap: 0.6rem;
            }

            .btn-go-store {
                padding: 0.4rem 0.75rem;
                font-size: 0.85rem;
            }

            .admin-user-chip {
                padding: 0.35rem 0.6rem;
                gap: 0.5rem;
                background: rgba(148, 163, 184, 0.18);
            }

            .admin-user-chip .avatar {
                width: 34px;
                height: 34px;
                font-size: 1rem;
            }

            .admin-bottom-nav {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .admin-header-top {
                gap: 0.75rem;
            }

            .admin-header-actions {
                flex-wrap: nowrap;
                justify-content: space-between;
            }

            .admin-user-chip {
                flex: 1 1 auto;
                justify-content: flex-start;
            }

            .admin-user-chip .admin-user-details {
                display: none;
            }
        }

        /* CORREÇÃO GLOBAL: Garantir que backdrops fiquem SEMPRE atrás dos modais */
        .modal-backdrop {
            z-index: 1040 !important;
            position: fixed !important;
        }

        .modal-backdrop.show {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
            position: fixed !important;
        }

        .modal.show {
            z-index: 1050 !important;
            display: block !important;
        }

        .modal-dialog {
            z-index: 1055 !important;
            position: relative !important;
        }

        .modal-content {
            z-index: 1056 !important;
            position: relative !important;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="sidebar-header">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                            <img src="{{ asset('logo-ofc.svg') }}" alt="Feira das Fábricas" 
                                 style="height: 40px; width: auto;">
                        </a>
                    </div>
                    <nav class="nav flex-column px-3">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> 
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <i class="bi bi-box-seam"></i> 
                            <span>Produtos</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                            <i class="bi bi-tags"></i> 
                            <span>Categorias</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="bi bi-cart-check"></i> 
                            <span>Pedidos</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                            <i class="bi bi-people"></i> 
                            <span>Clientes</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                            <i class="bi bi-ticket-perforated"></i> 
                            <span>Cupons</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                            <i class="bi bi-image"></i>
                            <span>Banners</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.department-badges.*') ? 'active' : '' }}" href="{{ route('admin.department-badges.index') }}">
                            <i class="bi bi-award"></i>
                            <span>Selos de Marcas</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> 
                            <span>Usuários</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="bi bi-gear"></i> 
                            <span>Configurações</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Top Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-admin">
                        <div class="container-fluid px-4">
                                <div class="d-flex flex-column gap-2 w-100">
                                <div class="admin-header-top">
                                    <div class="page-header-wrap">
                                        <div class="page-icon">
                                            <i class="@yield('page-icon', 'bi bi-speedometer2')"></i>
                                        </div>
                                        <div>
                                            <div class="page-meta">
                                                <h1 class="page-heading">@yield('page-title', 'Dashboard')</h1>
                                                @yield('page-breadcrumb')
                                            </div>
                                            @php
                                                $pageSubtitle = trim($__env->yieldContent('page-subtitle'));
                                                $pageDescription = trim($__env->yieldContent('page-description'));
                                            @endphp
                                            @if(!empty($pageDescription))
                                                <p class="page-description">{!! $pageDescription !!}</p>
                                            @elseif(!empty($pageSubtitle))
                                                <p class="page-description">{!! $pageSubtitle !!}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="admin-header-actions">
                                        @if(request()->routeIs('admin.dashboard'))
                                            <a href="{{ route('home') }}" class="btn btn-go-store">
                                                <i class="bi bi-arrow-return-left"></i>
                                                Voltar para o site
                                            </a>
                                        @endif
                                        <div class="admin-user-chip">
                                            <div class="avatar">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div class="admin-user-details">
                                                <div class="name">{{ auth('admin')->user()->name }}</div>
                                                <div class="role">Administrador</div>
                                            </div>
                                        </div>
                                        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Sair">
                                                <i class="bi bi-box-arrow-right"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                                        <i class="bi bi-check-circle me-2"></i>
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                                        <i class="bi bi-exclamation-circle me-2"></i>
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </nav>

                    <!-- Page Content -->
                    <div class="container-fluid p-4 fade-in">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="admin-bottom-nav d-md-none" aria-label="Navegação Administrativa">
        <div class="container-fluid px-3">
            <div class="nav" data-admin-loop="true" data-admin-loop-width>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Produtos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i>
                    <span>Categorias</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <i class="bi bi-cart-check"></i>
                    <span>Pedidos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                    <i class="bi bi-people"></i>
                    <span>Clientes</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                    <i class="bi bi-image"></i>
                    <span>Banners</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.department-badges.*') ? 'active' : '' }}" href="{{ route('admin.department-badges.index') }}">
                    <i class="bi bi-award"></i>
                    <span>Selos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                    <i class="bi bi-ticket-perforated"></i>
                    <span>Cupons</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-person-badge"></i>
                    <span>Usuários</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                    <i class="bi bi-gear"></i>
                    <span>Config.</span>
                </a>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Modais renderizados diretamente no body para ficarem acima do backdrop -->
    @stack('modals')
    
    @stack('scripts')
    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loopContainers = document.querySelectorAll('.admin-bottom-nav [data-admin-loop="true"]');

            loopContainers.forEach(function (container) {
                const items = Array.from(container.querySelectorAll('.nav-link'));
                if (items.length < 2) {
                    return;
                }

                const originalWidth = container.scrollWidth;

                items.forEach(function (item) {
                    const clone = item.cloneNode(true);
                    clone.setAttribute('data-admin-clone', 'true');
                    container.appendChild(clone);
                });

                let isAdjusting = false;

                container.addEventListener('scroll', function () {
                    if (isAdjusting) {
                        return;
                    }

                    if (container.scrollLeft >= originalWidth) {
                        isAdjusting = true;
                        const previousBehavior = container.style.scrollBehavior;
                        container.style.scrollBehavior = 'auto';
                        container.scrollLeft -= originalWidth;
                        requestAnimationFrame(function () {
                            container.style.scrollBehavior = previousBehavior;
                            isAdjusting = false;
                        });
                    } else if (container.scrollLeft <= 0) {
                        isAdjusting = true;
                        const previousBehavior = container.style.scrollBehavior;
                        container.style.scrollBehavior = 'auto';
                        container.scrollLeft += originalWidth;
                        requestAnimationFrame(function () {
                            container.style.scrollBehavior = previousBehavior;
                            isAdjusting = false;
                        });
                    }
                });

                container.style.scrollBehavior = 'auto';
                container.scrollLeft = 1;
                requestAnimationFrame(function () {
                    container.style.scrollBehavior = '';
                });
            });
        });
    </script>
</body>
</html>
