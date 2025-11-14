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
            position: sticky;
            top: 0;
            z-index: 10;
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
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
            box-shadow: 0 8px 16px rgba(255, 140, 0, 0.3);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .smart-search-trigger:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 24px rgba(255, 140, 0, 0.4);
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
            box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1);
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

        .smart-search-item-brand {
            font-size: 12px;
            padding: 2px 8px;
            background: var(--primary-color);
            color: white;
            border-radius: 4px;
            font-weight: 500;
        }

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
                        <a class="nav-link {{ request()->routeIs('admin.melhor-envio.index') ? 'active' : '' }}" href="{{ route('admin.melhor-envio.index') }}">
                            <i class="bi bi-truck"></i>
                            <span>Melhor Envio</span>
                        </a>
                        <a class="nav-link ps-5 {{ request()->routeIs('admin.melhor-envio.services') ? 'active' : '' }}" href="{{ route('admin.melhor-envio.services') }}">
                            <i class="bi bi-list-ul"></i>
                            <span>Serviços</span>
                        </a>
                    </nav>
                    
                    <!-- Frases Motivacionais e Dicas de Negócio -->
                    <div class="sidebar-quotes px-3 py-4 mt-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(255,140,0,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-lightbulb-fill" style="color: #fbbf24; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #fbbf24;">Diversidade paga:</strong> Ofereça múltiplas marcas e variações. Clientes adoram opções!
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
                        
                        <div class="quote-item mb-3 p-3" style="background: rgba(255,255,255,0.08); border-radius: 0.75rem; border-left: 3px solid rgba(239,68,68,0.8);">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-lightning-charge-fill" style="color: #ef4444; font-size: 1.2rem; flex-shrink: 0;"></i>
                                <div>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.95); font-size: 0.85rem; line-height: 1.5; font-weight: 500;">
                                        <strong style="color: #ef4444;">Rapidez no envio:</strong> Entrega rápida é diferencial competitivo. Organize sua logística!
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
                                        <strong style="color: #22c55e;">Marcas de confiança:</strong> Apple, Samsung, Xiaomi... Nomes fortes atraem e convertem!
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
                    const brand = product.brand || 'Sem marca';
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
                                    <span class="smart-search-item-brand">${brand}</span>
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
