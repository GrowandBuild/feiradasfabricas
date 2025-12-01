<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Painel Administrativo') - {{ setting('site_name', 'Feira das Fábricas') }}</title>
    @php $siteFavicon = setting('site_favicon'); $siteAppIcon = setting('site_app_icon'); @endphp
    @if($siteAppIcon)
        @php
            $appPath = public_path('storage/' . $siteAppIcon);
            $appVer = file_exists($appPath) ? filemtime($appPath) : time();
        @endphp
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $siteAppIcon) }}?_={{ $appVer }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/' . $siteAppIcon) }}?_={{ $appVer }}">
    @else
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @endif
    @if($siteFavicon)
        @php
            $favPath = public_path('storage/' . $siteFavicon);
            $favVer = file_exists($favPath) ? filemtime($favPath) : time();
        @endphp
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/' . $siteFavicon) }}?_={{ $favVer }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/' . $siteFavicon) }}?_={{ $favVer }}">
        <link rel="mask-icon" href="{{ asset('storage/' . $siteFavicon) }}?_={{ $favVer }}" color="{{ setting('theme_secondary', '#495a6d') }}">
    @else
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    @endif
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e293b;
            --primary-dark: #0f172a;
            --secondary-color: #64748b;
            --accent-color: #495a6d;
            --accent-dark: #384858;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
    
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.05"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        /* Sidebar safe-fallback: garante contraste mesmo se variáveis de tema estiverem ausentes */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-dark, #0f172a) 0%, rgba(15,23,42,0.85) 100%);
            min-height: 100vh;
            padding-top: calc(var(--admin-header-height, 72px) + 1rem);
            color: var(--text-primary, #ffffff);
        }

        /* Defaults for commonly used CSS variables that may be missing */
        :root {
            --text-primary: var(--text-primary, #ffffff);
            --text-secondary: var(--text-secondary, rgba(255,255,255,0.75));
            --border-color: var(--border-color, rgba(255,255,255,0.06));
            --card-bg: var(--card-bg, #0b1220);
            --admin-header-height: 72px;
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
            padding-top: calc(var(--admin-header-height, 72px) + 8px);
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
            /* Fixed, solid header */
            background: var(--accent-color, #ff8c00);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(2,6,23,0.12);
            border-bottom: none;
            padding: 0.9rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1100;
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
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-color, #ff8c00) 0%, var(--accent-dark, #e67e00) 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(2,6,23,0.12);
        }

        .page-heading {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-primary, #0f172a);
            line-height: 1.05;
        }

        .page-description {
            margin: 0.15rem 0 0;
            color: var(--text-secondary, #64748b);
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
            box-shadow: 0 0 0 3px rgba(73, 90, 109, 0.1);
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
            /* Ensure sidebar width on mobile is usable and covers enough area */
            .sidebar {
                width: 280px;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
            }
            .sidebar.show { transform: translateX(0); }

            /* No backdrop: sidebar will slide over content without an overlay */
        }

        .admin-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            /* Solid accent background to improve visibility on mobile */
            background: linear-gradient(135deg, var(--accent-color, #495a6d) 0%, var(--accent-dark, #384858) 100%);
            color: #fff;
            border-top: none;
            padding: 0.35rem 0;
            box-shadow: 0 -6px 18px rgba(0,0,0,0.08);
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
            color: rgba(255,255,255,0.92);
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
            color: rgba(255,255,255,0.95);
        }

        .admin-bottom-nav .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.06);
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

        /* Busca Inteligente Flutuante */
        .smart-search-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
        }

        .smart-search-trigger {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-dark) 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 16px rgba(73, 90, 109, 0.3);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .smart-search-trigger:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 24px rgba(73, 90, 109, 0.4);
        }

        .smart-search-trigger:active {
            transform: scale(0.95);
        }

        .smart-search-panel {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 420px;
            max-height: 600px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            display: none;
            flex-direction: column;
            z-index: 998;
            overflow: hidden;
            animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .smart-search-panel.active {
            display: flex;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .smart-search-header {
            padding: 20px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .smart-search-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
        }

        .smart-search-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .smart-search-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .smart-search-input-wrapper {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            background: white;
        }

        .smart-search-input-group {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--light-bg);
            border-radius: 12px;
            padding: 0 16px;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .smart-search-input-group:focus-within {
            border-color: var(--accent-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(73, 90, 109, 0.1);
        }

        .smart-search-input-group i {
            color: var(--text-secondary);
            margin-right: 12px;
        }

        .smart-search-input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 14px 0;
            font-size: 15px;
            outline: none;
            font-weight: 500;
            color: var(--text-primary);
        }

        .smart-search-input::placeholder {
            color: var(--text-secondary);
            font-weight: 400;
        }

        .smart-search-clear {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .smart-search-clear:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--danger-color);
        }

        .smart-search-results {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }

        .smart-search-results::-webkit-scrollbar {
            width: 8px;
        }

        .smart-search-results::-webkit-scrollbar-track {
            background: var(--light-bg);
            border-radius: 4px;
        }

        .smart-search-results::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        .smart-search-results::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        .smart-search-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            text-decoration: none;
            color: inherit;
            margin-bottom: 8px;
        }

        .smart-search-item:hover {
            background: var(--light-bg);
            border-color: var(--accent-color);
            transform: translateX(4px);
            text-decoration: none;
        }

        .smart-search-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--light-bg);
            flex-shrink: 0;
        }

        .smart-search-item-details {
            flex: 1;
            min-width: 0;
        }

        .smart-search-item-name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .smart-search-item-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* .smart-search-item-brand removed — brand UI deleted. Keeping comment for safety. */

        .smart-search-item-price {
            font-size: 13px;
            font-weight: 600;
            color: var(--success-color);
        }

        .smart-search-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .smart-search-empty i {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 16px;
        }

        .smart-search-empty p {
            margin: 0;
            font-size: 15px;
        }

        .smart-search-loading {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }

        .smart-search-loading i {
            font-size: 2rem;
            color: var(--accent-color);
            animation: spin 1s linear infinite;
        }

        @media (max-width: 768px) {
            .smart-search-panel {
                right: 15px;
                left: 15px;
                width: auto;
                bottom: 90px;
                max-height: 70vh;
            }

            .smart-search-fab {
                bottom: 100px;
                right: 15px;
            }

            .smart-search-trigger {
                width: 56px;
                height: 56px;
                font-size: 1.3rem;
            }
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
                        @php
                            $siteLogo = setting('site_logo') ?: 'logo-ofc.svg';
                            if (\Illuminate\Support\Str::startsWith($siteLogo, ['http', 'https'])) {
                                $siteLogoUrl = $siteLogo;
                            } else {
                                $siteLogoUrl = asset(\Illuminate\Support\Str::startsWith($siteLogo, 'storage/') ? $siteLogo : 'storage/' . $siteLogo);
                            }
                        @endphp
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" id="admin-logo-link">
                            <img id="admin-site-logo" src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', 'Feira das Fábricas') }}" 
                                 style="height: 40px; width: auto; cursor: pointer;">
                        </a>
                    </div>
                    <nav class="nav flex-column px-3">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> 
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <span>Departamentos</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <i class="bi bi-box-seam"></i> 
                            <span>Produtos</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}" style="padding-left: 2.5rem;">
                            <i class="bi bi-tag"></i> 
                            <span>Marcas</span>
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
                        <a class="nav-link {{ request()->routeIs('admin.albums.*') ? 'active' : '' }}" href="{{ route('admin.albums.index') }}">
                            <i class="bi bi-images"></i>
                            <span>Álbuns</span>
                        </a>
                        <!-- Galerias removidas -->
                        <a class="nav-link {{ request()->routeIs('admin.department-badges.*') ? 'active' : '' }}" href="{{ route('admin.department-badges.index') }}">
                            <i class="bi bi-award"></i>
                            <span>Selos</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.homepage-sections.*') ? 'active' : '' }}" href="{{ route('admin.homepage-sections.index') }}">
                            <i class="bi bi-layout-three-columns"></i>
                            <span>Sessões</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> 
                            <span>Usuários</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                            <i class="bi bi-gear"></i> 
                            <span>Configurações</span>
                        </a>
                        {{-- Links de frete removidos --}}
                    </nav>
                    
                    <!-- Frases Motivacionais e Dicas de Negócio -->
                    <div class="sidebar-quotes px-3 py-4 mt-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(73,90,109,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-lightbulb-fill" style="color: #fbbf24; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #fbbf24;">Diversidade paga:</strong> Ofereça múltiplas opções e variações. Clientes adoram escolhas!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(16,185,129,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-graph-up-arrow" style="color: #10b981; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #10b981;">Margem inteligente:</strong> Ajuste suas margens B2B e B2C para maximizar lucros sem perder competitividade.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(59,130,246,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-box-seam" style="color: #3b82f6; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #3b82f6;">Estoque = Fluxo:</strong> Monitore constantemente. Produto parado é dinheiro parado!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(168,85,247,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-images" style="color: #a855f7; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #a855f7;">Imagem vende:</strong> Fotos de qualidade aumentam conversões em até 300%. Invista nisso!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(236,72,153,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-people-fill" style="color: #ec4899; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #ec4899;">Cliente B2B é ouro:</strong> Eles compram volume. Ofereça condições especiais e fidelize!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(20,184,166,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-chat-dots-fill" style="color: #14b8a6; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #14b8a6;">Descrições claras:</strong> Especificações técnicas detalhadas reduzem devoluções e aumentam confiança.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(251,146,60,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-tags-fill" style="color: #fb923c; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #fb923c;">Categorias organizadas:</strong> Facilite a busca. Cliente que encontra rápido, compra mais!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(34,197,94,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-trophy-fill" style="color: #22c55e; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                        <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #22c55e;">Fornecedores de confiança:</strong> Nomes fortes atraem e convertem!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(147,51,234,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-phone-fill" style="color: #9333ea; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #9333ea;">Smartphones lideram:</strong> Foco em celulares de qualidade. É o produto mais vendido no setor!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(234,179,8,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-cart-check-fill" style="color: #eab308; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #eab308;">Acompanhe pedidos:</strong> Gestão eficiente = clientes satisfeitos = recompra garantida!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(6,182,212,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-cpu-fill" style="color: #06b6d4; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #06b6d4;">Tecnologia atualizada:</strong> Lançamentos recentes geram buzz e atraem early adopters!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(245,158,11,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-star-fill" style="color: #f59e0b; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #f59e0b;">Destaque produtos:</strong> Use badges e banners para promover itens estratégicos!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(99,102,241,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-percent" style="color: #6366f1; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #6366f1;">Cupons estratégicos:</strong> Descontos bem planejados impulsionam vendas em momentos certos!
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quote-item p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(139,92,246,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-rocket-takeoff-fill" style="color: #8b5cf6; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #8b5cf6;">Sucesso é constância:</strong> Atualize diariamente, analise métricas e otimize sempre!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        <!-- Mobile menu toggle -->
                                        <button id="adminSidebarToggle" class="btn btn-outline-light d-md-none me-2" title="Menu" aria-label="Abrir menu">
                                            <i class="bi bi-list"></i>
                                        </button>
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

        <!-- Smart Search Flutuante -->
        <!-- Logo upload modal (admin) -->
        <div class="modal fade" id="logoUploadModal" tabindex="-1" aria-labelledby="logoUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoUploadModalLabel">Substituir logo do site</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="logoUploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="logoFile" class="form-label">Escolher imagem</label>
                                <input class="form-control" type="file" id="logoFile" name="logo" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <img id="logoPreview" src="" alt="Pré-visualização" style="max-width:100%; display:none;" />
                            </div>
                            <div id="logoUploadAlert" class="alert d-none" role="alert"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="logoUploadSubmit" class="btn btn-primary">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    <div class="smart-search-fab">
        <button class="smart-search-trigger" id="smartSearchTrigger" title="Buscar produto">
            <i class="bi bi-search"></i>
        </button>
        
        <div class="smart-search-panel" id="smartSearchPanel">
            <div class="smart-search-header">
                <h3><i class="bi bi-lightning-charge-fill me-2"></i>Busca Inteligente</h3>
                <button class="smart-search-close" id="smartSearchClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="smart-search-input-wrapper">
                <div class="smart-search-input-group">
                    <i class="bi bi-search"></i>
                    <input 
                        type="text" 
                        class="smart-search-input" 
                        id="smartSearchInput"
                        placeholder="Digite para buscar produtos..."
                        autocomplete="off">
                    <button class="smart-search-clear" id="smartSearchClear" style="display: none;">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
            </div>
            
            <div class="smart-search-results" id="smartSearchResults">
                <div class="smart-search-empty">
                    <i class="bi bi-search"></i>
                    <p>Digite algo para buscar produtos</p>
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
                <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    <span>Departamentos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Produtos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                    <i class="bi bi-tag"></i>
                    <span>Marcas</span>
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
                <a class="nav-link {{ request()->routeIs('admin.albums.*') ? 'active' : '' }}" href="{{ route('admin.albums.index') }}">
                    <i class="bi bi-images"></i>
                    <span>Álbuns</span>
                </a>
                <!-- Galerias removidas -->
                <a class="nav-link {{ request()->routeIs('admin.department-badges.*') ? 'active' : '' }}" href="{{ route('admin.department-badges.index') }}">
                    <i class="bi bi-award"></i>
                    <span>Selos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.homepage-sections.*') ? 'active' : '' }}" href="{{ route('admin.homepage-sections.index') }}">
                    <i class="bi bi-layout-three-columns"></i>
                    <span>Sessões</span>
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
                {{-- botão para abrir seletor de tamanho da logo (mobile admin) --}}
                <a class="nav-link" href="#" id="adminBottomLogoSizeBtn" title="Ajustar tamanho da logo">
                    <i class="bi bi-aspect-ratio"></i>
                    <span>Logo</span>
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
        // Mobile sidebar toggle: show/hide sidebar (no backdrop)
        document.addEventListener('DOMContentLoaded', function(){
            var sidebar = document.querySelector('.sidebar');
            var toggle = document.getElementById('adminSidebarToggle');
            if(!sidebar || !toggle) return;

            toggle.addEventListener('click', function(e){
                e.preventDefault();
                sidebar.classList.toggle('show');
            });

            // close when a nav link is clicked (mobile UX)
            sidebar.addEventListener('click', function(e){
                var a = e.target.closest && e.target.closest('.nav-link');
                if(a && window.innerWidth <= 768){
                    sidebar.classList.remove('show');
                }
            });
        });
        // Smart Search Flutuante
        document.addEventListener('DOMContentLoaded', function() {
            const searchTrigger = document.getElementById('smartSearchTrigger');
            const searchPanel = document.getElementById('smartSearchPanel');
            const searchClose = document.getElementById('smartSearchClose');
            const searchInput = document.getElementById('smartSearchInput');
            const searchClear = document.getElementById('smartSearchClear');
            const searchResults = document.getElementById('smartSearchResults');
            
            let searchTimeout = null;
            
            // Toggle painel
            searchTrigger.addEventListener('click', function() {
                searchPanel.classList.toggle('active');
                if (searchPanel.classList.contains('active')) {
                    searchInput.focus();
                }
            });
            
            // Fechar painel
            searchClose.addEventListener('click', function() {
                searchPanel.classList.remove('active');
            });
            
            // Fechar ao clicar fora
            document.addEventListener('click', function(e) {
                if (!searchPanel.contains(e.target) && !searchTrigger.contains(e.target)) {
                    searchPanel.classList.remove('active');
                }
            });
            
            // Limpar busca
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                searchClear.style.display = 'none';
                searchResults.innerHTML = `
                    <div class="smart-search-empty">
                        <i class="bi bi-search"></i>
                        <p>Digite algo para buscar produtos</p>
                    </div>
                `;
            });
            
            // Busca em tempo real
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Mostrar/ocultar botão limpar
                searchClear.style.display = query ? 'block' : 'none';
                
                // Limpar timeout anterior
                clearTimeout(searchTimeout);
                
                if (query.length === 0) {
                    searchResults.innerHTML = `
                        <div class="smart-search-empty">
                            <i class="bi bi-search"></i>
                            <p>Digite algo para buscar produtos</p>
                        </div>
                    `;
                    return;
                }
                
                // Mostrar loading
                searchResults.innerHTML = `
                    <div class="smart-search-loading">
                        <i class="bi bi-arrow-repeat"></i>
                        <p>Buscando...</p>
                    </div>
                `;
                
                // Buscar após 300ms
                searchTimeout = setTimeout(function() {
                    performSearch(query);
                }, 300);
            });
            
            // Função de busca AJAX
            function performSearch(query) {
                fetch(`{{ route('admin.products.index') }}?search=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.products && data.products.length > 0) {
                        renderResults(data.products);
                    } else {
                        searchResults.innerHTML = `
                            <div class="smart-search-empty">
                                <i class="bi bi-inbox"></i>
                                <p>Nenhum produto encontrado</p>
                                <small style="color: var(--text-secondary); margin-top: 8px;">Tente buscar por outra palavra-chave</small>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                    searchResults.innerHTML = `
                        <div class="smart-search-empty">
                            <i class="bi bi-exclamation-triangle" style="color: var(--danger-color);"></i>
                            <p>Erro ao buscar produtos</p>
                        </div>
                    `;
                });
            }
            
            // Renderizar resultados
            function renderResults(products) {
                let html = '';
                
                products.forEach(product => {
                    const image = product.first_image || '{{ asset("images/no-image.svg") }}';
                    const price = product.price ? `R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}` : 'Preço não definido';
                    const listUrl = `{{ route('admin.products.index') }}?highlight=${product.id}#product-${product.id}`;
                    
                    // Verificar status do produto
                    const isInactive = !product.is_active;
                    const isUnavailable = product.is_unavailable;
                    const opacityStyle = (isInactive || isUnavailable) ? 'opacity: 0.6;' : '';
                    
                    // Badges de status
                    let statusBadges = '';
                    if (isInactive) {
                        statusBadges += '<span class="badge bg-secondary" style="font-size: 10px; margin-left: 4px;">INATIVO</span>';
                    }
                    if (isUnavailable) {
                        statusBadges += '<span class="badge bg-warning text-dark" style="font-size: 10px; margin-left: 4px;">INDISPONÍVEL</span>';
                    }
                    
                    html += `
                        <a href="${listUrl}" class="smart-search-item" style="${opacityStyle}">
                            <img src="${image}" 
                                 alt="${product.name}" 
                                 class="smart-search-item-image"
                                 onerror="this.src='{{ asset("images/no-image.svg") }}'">
                            <div class="smart-search-item-details">
                                <div class="smart-search-item-name" title="${product.name}">
                                    ${product.name}
                                    ${statusBadges}
                                </div>
                                <div class="smart-search-item-meta">
                                    <span class="smart-search-item-price">${price}</span>
                                </div>
                            </div>
                            <i class="bi bi-arrow-right-circle" style="color: var(--accent-color); font-size: 1.5rem;"></i>
                        </a>
                    `;
                });
                
                searchResults.innerHTML = html;
            }
        });
    </script>
    <style>
        /* Logo-size picker (admin mobile) */
        .logo-size-picker{
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            top: 56px;
            background: #fff;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 8px 30px rgba(2,6,23,0.12);
            padding: 8px 10px;
            border-radius: 8px;
            z-index: 120000;
            display: none;
            gap: 8px;
            min-width: 220px;
            max-width: calc(100vw - 32px);
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }
        .logo-size-picker button{ padding:8px 12px; border-radius:6px; border:1px solid rgba(0,0,0,0.06); background:transparent; cursor:pointer; white-space:nowrap }
        .logo-size-picker button.active{ background:var(--secondary-color); color:#fff; border-color:transparent }
        @media (max-width: 420px){
            .logo-size-picker{ gap:6px; padding:8px; min-width: 160px; }
            .logo-size-picker button{ flex: 1 1 48%; padding:8px; box-sizing: border-box; }
        }
    </style>

    <script>
        // Logo-size picker for admin bottom nav (mobile)
        document.addEventListener('DOMContentLoaded', function(){
            var trigger = document.getElementById('adminBottomLogoSizeBtn');
            if(!trigger) return;
            var imgs = document.querySelectorAll('#admin-site-logo, #admin-site-logo');

            var picker = document.createElement('div');
            picker.className = 'logo-size-picker';
            picker.setAttribute('role', 'dialog');
            picker.setAttribute('aria-hidden', 'true');
            picker.innerHTML = `
                <div style="font-weight:600;color:var(--text-dark);width:100%;text-align:center;padding-bottom:6px;">Tamanho</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;width:100%;justify-content:center;">
                    <button data-size="small">Pequena</button>
                    <button data-size="medium">Média</button>
                    <button data-size="large">Grande</button>
                    <button data-size="xlarge">Muito grande</button>
                </div>
            `;
            document.body.appendChild(picker);

            function showPicker(){
                picker.style.display = 'flex';
                picker.setAttribute('aria-hidden','false');
                setTimeout(function(){ picker.classList.add('show'); }, 10);
            }
            function hidePicker(){
                picker.setAttribute('aria-hidden','true');
                picker.classList.remove('show');
                setTimeout(function(){ picker.style.display = 'none'; }, 180);
            }

            trigger.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); if(picker.style.display === 'none') showPicker(); else hidePicker(); });

            document.addEventListener('click', function(e){ if(!picker.contains(e.target) && e.target !== trigger && !trigger.contains(e.target)) hidePicker(); });

            picker.addEventListener('click', function(e){
                var b = e.target.closest('button[data-size]');
                if(!b) return;
                var size = b.getAttribute('data-size');
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('{{ route("logo.size") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ size: size })
                }).then(function(r){ return r.json(); }).then(function(json){
                    if(json && json.success && json.size){
                        imgs.forEach(function(img){ try{ img.style.height = 'auto'; img.style.width = 'auto'; img.style.maxHeight = json.size + 'px'; img.style.maxWidth = json.size + 'px'; }catch(e){} });
                        Array.from(picker.querySelectorAll('button[data-size]')).forEach(function(x){ x.classList.toggle('active', x.getAttribute('data-size') === size); });
                        setTimeout(hidePicker, 180);
                    } else {
                        alert((json && json.message) ? json.message : 'Erro ao ajustar tamanho');
                    }
                }).catch(function(err){ console.error('Erro logo.size', err); alert('Erro ao ajustar tamanho'); });
            });
        });
    </script>

    <script>
        // Wrap fetch to inspect responses from settings.update and dispatch theme:updated when present
        (function(){
            const origFetch = window.fetch.bind(window);
            const settingsUrl = '{{ route("admin.settings.update") }}';
            window.fetch = function(input, init) {
                return origFetch(input, init).then(async res => {
                    try {
                        const reqUrl = (typeof input === 'string') ? input : (input && input.url ? input.url : '');
                        if (reqUrl && reqUrl.indexOf(settingsUrl) !== -1) {
                            let json = null;
                            try { json = await res.clone().json(); } catch(e) { json = null; }
                            if (json && json.theme) {
                                window.dispatchEvent(new CustomEvent('theme:updated', { detail: { theme: json.theme, slug: json.slug || null } }));
                            }
                        }
                    } catch(e) { console.error('fetch wrapper error', e); }
                    return res;
                });
            };
        })();
    </script>

    <script>
        // Listen for theme updates dispatched from settings responses
        window.addEventListener('theme:updated', function(e) {
            try {
                const detail = e && e.detail ? e.detail : {};
                const theme = detail.theme || {};
                const slug = detail.slug || null;
                const mapping = {
                    'theme_primary': '--primary-color',
                    'theme_secondary': '--accent-color',
                    'theme_accent': '--accent-color',
                    'theme_dark_bg': '--primary-dark',
                    'theme_text_light': '--text-primary',
                    'theme_text_dark': '--text-secondary',
                    'theme_success': '--success-color',
                    'theme_warning': '--warning-color',
                    'theme_danger': '--danger-color',
                    'theme_border': '--border-color'
                };
                Object.keys(theme).forEach(k => {
                    const cssVar = mapping[k] || null;
                    if (cssVar) {
                        document.documentElement.style.setProperty(cssVar, theme[k]);
                    }
                });

                // Persist theme to session for public pages
                fetch('{{ route("admin.settings.session-theme") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ theme: theme, slug: slug })
                }).catch(err => console.error('Erro ao persistir tema na sessão:', err));

            } catch (err) { console.error('Erro ao aplicar tema:', err); }
        });
    </script>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const adminLogoLink = document.getElementById('admin-logo-link');
            const adminSiteLogo = document.getElementById('admin-site-logo');
            const logoModalEl = document.getElementById('logoUploadModal');
            const logoFile = document.getElementById('logoFile');
            const logoPreview = document.getElementById('logoPreview');
            const logoUploadSubmit = document.getElementById('logoUploadSubmit');
            const logoUploadAlert = document.getElementById('logoUploadAlert');

            if (!adminLogoLink || !adminSiteLogo || !logoModalEl) return;

            const logoModal = new bootstrap.Modal(logoModalEl);

            adminLogoLink.addEventListener('click', function (e) {
                e.preventDefault();
                logoUploadAlert.classList.add('d-none');
                logoPreview.style.display = 'none';
                logoPreview.src = '';
                logoFile.value = '';
                logoModal.show();
            });

            logoFile && logoFile.addEventListener('change', function (e) {
                const file = this.files && this.files[0];
                if (!file) { logoPreview.style.display = 'none'; return; }
                const reader = new FileReader();
                reader.onload = function (ev) {
                    logoPreview.src = ev.target.result;
                    logoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });

            logoUploadSubmit && logoUploadSubmit.addEventListener('click', function () {
                const file = logoFile.files && logoFile.files[0];
                if (!file) {
                    showAlert('Selecione uma imagem para enviar.', 'danger');
                    return;
                }

                const formData = new FormData();
                formData.append('logo', file);

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch("{{ route('admin.settings.upload-logo') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(({ status, body }) => {
                    if (status === 200 && body.success) {
                        const newUrl = body.url + '?v=' + Date.now();
                        adminSiteLogo.src = newUrl;
                        showAlert(body.message || 'Logo atualizada.', 'success');
                        setTimeout(() => {
                            logoModal.hide();
                        }, 800);
                    } else if (status === 422 && body.errors) {
                        const messages = Object.values(body.errors).flat().join(' ');
                        showAlert(messages || 'Erros de validação.', 'danger');
                    } else {
                        showAlert(body.message || 'Erro ao enviar a imagem.', 'danger');
                    }
                })
                .catch(err => {
                    console.error('Upload logo error', err);
                    showAlert('Erro ao enviar a imagem.', 'danger');
                });
            });

            function showAlert(message, type) {
                logoUploadAlert.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-info');
                logoUploadAlert.classList.add('alert-' + (type === 'success' ? 'success' : (type === 'danger' ? 'danger' : 'info')));
                logoUploadAlert.textContent = message;
            }
        });
    </script>
</body>
</html>
