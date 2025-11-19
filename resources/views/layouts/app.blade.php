@php
    $deptSlug = isset($currentDepartmentSlug) ? $currentDepartmentSlug : null;
    $dept_setting = function($key, $default = null) use ($deptSlug) {
        if ($deptSlug) {
            $deptKey = 'dept_' . $deptSlug . '_' . $key;
            $val = setting($deptKey);
            if ($val !== null && $val !== '') return $val;
        }
        return setting($key, $default);
    };
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Feira das Fábricas')</title>
    
    <meta name="theme-color" content="{{ $dept_setting('theme_secondary', '#ff9900') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (para a lupa da Busca Inteligente) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        
        :root {
            --primary-color: {{ $dept_setting('theme_primary', '#0f172a') }};
            --secondary-color: {{ $dept_setting('theme_secondary', '#ff6b35') }};
            --accent-color: {{ $dept_setting('theme_accent', '#0f172a') }};
            --dark-bg: {{ $dept_setting('theme_dark_bg', '#1e293b') }};
            --text-light: {{ $dept_setting('theme_text_light', '#f8fafc') }};
            --text-dark: {{ $dept_setting('theme_text_dark', '#1e293b') }};
            --header-height: 72px; /* adjust if your header height differs */
            /* map elegant variables used in department templates to theme variables */
            --elegant-accent: var(--secondary-color);
            --elegant-dark: var(--text-dark);
            --text-muted: #64748b;
            --elegant-blue: #334155;
            --success-color: {{ $dept_setting('theme_success', '#10b981') }};
            --warning-color: {{ $dept_setting('theme_warning', '#f59e0b') }};
            --danger-color: {{ $dept_setting('theme_danger', '#ef4444') }};
            --border-color: {{ $dept_setting('theme_border', '#e2e8f0') }};
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

            /* Global compact scale overrides: reduce base font-size and shrink large paddings/sizes
               to make the site appear less scaled (everything a bit smaller). Tweak as needed. */
            html {
                font-size: 14px; /* default 16px -> smaller overall scale */
            }

            /* Header / Navbar compact */
            .navbar-custom {
                padding: 0.35rem 0 !important;
            }

            .navbar-brand {
                font-size: 1.4rem !important;
            }

            .logo-img {
                height: 30px !important;
            }

            /* Search bar smaller */
            .search-bar input {
                padding: 12px 18px !important;
                font-size: 14px !important;
            }

            .search-bar button {
                padding: 12px 18px !important;
            }

            /* (banner behavior restored to the original rules further below) */

            /* Reduce badge / brand logo sizes */
            .brand-logo, .badge-circle {
                width: 80px !important;
                height: 80px !important;
            }

            /* Product card compact */
            .product-image {
                height: 140px !important; /* was 160px */
            }

            .product-title {
                font-size: 0.85rem !important;
            }

            .product-price {
                font-size: 1rem !important;
            }

            .product-btn {
                padding: 8px 12px !important;
                font-size: 0.75rem !important;
                border-radius: 6px !important;
            }

            /* Compact quantity controls */
            .quantity-btn {
                padding: 4px 8px !important;
                min-width: 30px !important;
                height: 32px !important;
            }

            .quantity-input {
                min-width: 44px !important;
                width: 44px !important;
                padding: 4px 8px !important;
                font-size: 0.95rem !important;
            }

            /* Header icon size */
            .header-icon {
                font-size: 1.1rem !important;
                padding: 6px !important;
            }

            /* Footer compact */
            .footer {
                padding: 2rem 0 0.75rem !important;
            }

        * {
            box-sizing: border-box;
        }

        html, body {
            overflow-x: hidden;
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

        .mobile-header {
            display: none;
            width: 100%;
        }

        .mobile-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 0.75rem;
        }

        .mobile-search-wrapper {
            display: none;
            width: 100%;
        }

        .desktop-search-wrapper {
            display: block;
        }

        .mobile-logo {
            display: none;
        }

        .mobile-quick-actions {
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
            background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%);
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
            background: linear-gradient(135deg, color-mix(in srgb, var(--secondary-color), white 10%) 0%, var(--secondary-color) 100%);
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

        .mobile-menu-button {
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: transparent;
            color: white;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .mobile-menu-button:hover,
        .mobile-menu-button:focus {
            background: rgba(255, 255, 255, 0.12);
            outline: none;
            transform: translateY(-1px);
        }

        .mobile-quick-actions {
            display: none;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .mobile-quick-actions .quick-action {
            color: white;
            font-size: 1.35rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .mobile-quick-actions .quick-action:hover {
            color: var(--secondary-color);
            transform: translateY(-1px);
        }

        .mobile-quick-actions .quick-action-total {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }

        .mobile-quick-actions .quick-action-badge {
            position: absolute;
            top: -0.25rem;
            right: -0.55rem;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: var(--secondary-color);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.25rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);
        }


        .hero-section {
            background: var(--dark-bg);
            color: white;
            padding: 2rem 0;
            min-height: 70vh;
        }

        /* When we render a full-bleed hero banner (slider) we remove the
           extra padding and let the banner component control height so the
           image touches the next section. Apply this by adding class
           `no-padding` to the `.hero-section` element where appropriate. */
        .hero-section.no-padding {
            padding: 0;
            min-height: auto;
            background: transparent;
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
            background-color: color-mix(in srgb, var(--secondary-color), white 18%);
        }

        .b2b-section {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            /* remove noisy secondary-based shadow; use subtle primary-based glow */
            box-shadow: 0 10px 30px color-mix(in srgb, var(--primary-color), transparent 80%);
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

            .mobile-header {
                display: flex !important;
                flex-direction: column;
                width: 100%;
            }

            .mobile-top-bar {
                margin-bottom: 0.75rem;
            }

            .mobile-menu-button {
                display: flex !important;
            }

            .mobile-search-wrapper {
                display: block !important;
                width: 100%;
            }

            .mobile-search-wrapper .live-search-wrapper {
                max-width: 100%;
                margin: 0;
            }

            .desktop-search-wrapper {
                display: none !important;
            }

            .header-icons {
                display: none !important;
            }

            .mobile-quick-actions {
                display: flex !important;
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

            .b2b-btn {
                background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%);
                color: white;
                border: none;
                padding: 12px 30px;
                font-size: 1.0rem;
                font-weight: 700;
                border-radius: 999px;
                transition: all 0.22s ease;
                box-shadow: 0 8px 20px color-mix(in srgb, var(--secondary-color), transparent 72%);
            }

            .b2b-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 30px color-mix(in srgb, var(--secondary-color), transparent 60%);
                filter: brightness(0.95);
                color: white;
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
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .quantity-controls:focus-within {
            border-color: var(--accent-color);
            box-shadow: var(--shadow-md);
        }

        .quantity-btn {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            color: var(--text-dark);
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 700;
            padding: 6px 10px;
            min-width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:hover {
            background: linear-gradient(135deg, var(--accent-color) 0%, #1e293b 100%);
            color: white;
            transform: scale(1.05);
        }

        .quantity-input {
            border: none;
            font-weight: 700;
            text-align: center;
            background: transparent;
            padding: 6px 10px;
            min-width: 56px; /* give space to show the number clearly */
            width: 56px;
            font-size: 1rem;
            line-height: 1;
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
        /* Override common bootstrap and custom button styles to respect theme variables */
        .btn-primary, .btn-primary-modern, .navbar-custom .btn, .btn-cta {
            background: linear-gradient(135deg, var(--primary-color) 0%, color-mix(in srgb, var(--primary-color), var(--dark-bg) 30%));
            border-color: transparent;
            color: #fff !important;
        }
        .btn-primary:focus, .btn-primary:active, .btn-primary:hover,
        .btn-cta:hover {
            filter: brightness(0.95);
            transform: translateY(-1px);
        }
        /* Treat .btn-success as secondary CTA so it matches theme secondary color in front-end CTAs */
        .btn-success {
            background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), var(--dark-bg) 30%));
            border-color: transparent;
            color: #fff !important;
        }
        .btn-success:hover, .btn-success:focus {
            filter: brightness(0.95);
        }
        /* Small utilities used in panels and theme tool */
        .tp-btn-secondary, .ss-btn-secondary, .tp-btn-secondary, .tp-btn-danger, .ss-btn-danger {
            background: transparent; /* let specific classes below override */
        }
        .tp-btn-secondary, .ss-btn-secondary { background: var(--border-color); color: var(--text-dark); }
        .tp-btn-danger, .ss-btn-danger { background: var(--danger-color); color: #fff; }
        .smart-search-item-brand { background: var(--primary-color); }
        /* Ensure badges and small accents use secondary or accent colors */
        .badge, .badge-pill { background: var(--secondary-color); color: #fff; }
        /* Ensure hero buttons follow theme (override inline or other hero-specific vars) */
        .hero-btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, color-mix(in srgb, var(--primary-color), var(--dark-bg) 30%)) !important;
            box-shadow: 0 8px 25px color-mix(in srgb, var(--primary-color), transparent 70%) !important;
            color: #fff !important;
        }
        .hero-btn-primary:hover {
            background: linear-gradient(135deg, color-mix(in srgb, var(--primary-color), white 10%) 0%, var(--primary-color) 100%) !important;
            box-shadow: 0 12px 35px color-mix(in srgb, var(--primary-color), transparent 60%) !important;
        }
        .hero-btn-secondary {
            background: transparent !important;
            color: var(--text-light) !important;
            border-color: color-mix(in srgb, var(--secondary-color), white 40%) !important;
        }
        .hero-btn-secondary:hover {
            background: var(--secondary-color) !important;
            color: #fff !important;
        }
        /* Ensure product listing cards use the department secondary color for primary CTAs
           This overrides department-local styles like .product-btn and keeps changes centralized */
        .product-card .product-btn,
        .product-info .product-btn,
        .product-card .product-info .product-btn {
            background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), var(--dark-bg) 30%)) !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 6px 18px color-mix(in srgb, var(--secondary-color), transparent 70%) !important;
        }
        .product-card .product-btn:hover,
        .product-info .product-btn:hover,
        .product-card .product-info .product-btn:hover {
            background: linear-gradient(135deg, color-mix(in srgb, var(--secondary-color), white 10%) 0%, var(--secondary-color) 100%) !important;
            transform: translateY(-2px) !important;
            filter: brightness(0.98) !important;
        }
        /* Ensure admin modals / department panels don't grow beyond the viewport and are scrollable.
           This prevents the panel from being obscured by the sticky header and improves UX on small screens. */
        .modal-dialog {
            margin: calc(var(--header-height) * 0.4) auto; /* modal starts below header proportionally */
            max-width: 960px;
            width: 100%;
        }

        .modal-content {
            max-height: calc(100vh - var(--header-height) - 40px);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-body {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            max-height: calc(100vh - var(--header-height) - 160px); /* leave room for header and modal footer */
        }

        /* Offcanvas panels (Bootstrap) similar behavior */
        /* push offcanvas further below the sticky header so it opens lower on the page */
        .offcanvas {
            max-height: calc(100vh - var(--header-height) - 8px) !important;
            top: calc(var(--header-height) + 8px) !important; /* open below sticky header */
            overflow: hidden;
        }

        .offcanvas .offcanvas-body {
            overflow-y: auto;
            max-height: calc(100vh - 160px) !important;
        }
        /* Visual dropzone for file uploads */
        .dropzone {
            border: 2px dashed var(--border-color);
            background: rgba(255,255,255,0.02);
            padding: 0.5rem;
            transition: box-shadow 0.15s ease, border-color 0.15s ease, transform 0.12s ease;
        }
        .dropzone input[type="file"] {
            background: transparent;
            border: none;
            box-shadow: none;
        }
        .dropzone.dragover {
            border-color: var(--secondary-color);
            box-shadow: 0 8px 24px color-mix(in srgb, var(--secondary-color), transparent 72%);
            transform: translateY(-2px);
            background: color-mix(in srgb, var(--secondary-color), white 96%);
        }
    </style>
    
    <!-- Overrides dinâmicos de tema (carregados de settings, por departamento se disponível) -->
    @php
    $deptSlug = isset($currentDepartmentSlug) ? $currentDepartmentSlug : null;
    $dept_setting = function($key, $default = null) use ($deptSlug) {
            if ($deptSlug) {
                $deptKey = 'dept_' . $deptSlug . '_' . $key;
                $val = setting($deptKey);
                if ($val !== null && $val !== '') return $val;
            }
            return setting($key, $default);
        }
    @endphp
    <style>
        :root {
            --primary-color: {{ $dept_setting('theme_primary', '#0f172a') }};
            --secondary-color: {{ $dept_setting('theme_secondary', '#ff6b35') }};
            --accent-color: {{ $dept_setting('theme_accent', '#0f172a') }};
            --dark-bg: {{ $dept_setting('theme_dark_bg', '#1e293b') }};
            --text-light: {{ $dept_setting('theme_text_light', '#f8fafc') }};
            --text-dark: {{ $dept_setting('theme_text_dark', '#1e293b') }};
            --success-color: {{ $dept_setting('theme_success', '#10b981') }};
            --warning-color: {{ $dept_setting('theme_warning', '#f59e0b') }};
            --danger-color: {{ $dept_setting('theme_danger', '#ef4444') }};
            --border-color: {{ $dept_setting('theme_border', '#e2e8f0') }};
        }
    </style>
    
    @yield('styles')
    @stack('head')
</head>
<body>
@php
    $customerUser = \Illuminate\Support\Facades\Auth::guard('customer')->user();
    $cartItemsCollection = collect();

    if ($customerUser) {
        $cartItemsCollection = \App\Models\CartItem::forCustomer($customerUser->id)->get(['quantity', 'price']);
    } else {
        $cartSessionId = session('cart_session_id');
        if ($cartSessionId) {
            $cartItemsCollection = \App\Models\CartItem::forSession($cartSessionId)->get(['quantity', 'price']);
        }
    }

    $cartCount = $cartItemsCollection->sum('quantity');
    $cartTotalValue = $cartItemsCollection->reduce(function ($carry, $item) {
        return $carry + ($item->quantity * $item->price);
    }, 0);

    $cartTotalValueFormatted = number_format($cartTotalValue, 2, ',', '.');
@endphp
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

                <!-- Mobile layout -->
                <div class="mobile-header">
                    <div class="mobile-top-bar">
                        <a class="mobile-logo" href="{{ route('home') }}">
                            <img src="{{ asset('logo-ofc.svg') }}" alt="Feira das Fábricas" class="logo-img">
                        </a>
                        <button class="mobile-menu-button" type="button" aria-label="Abrir menu">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                    <div class="mobile-search-wrapper">
                        @include('components.live-search')
                    </div>
                    <div class="mobile-quick-actions">
                        <a href="{{ route('home') }}" class="quick-action" title="Início">
                            <i class="fas fa-store"></i>
                        </a>
                        <a href="{{ route('albums.index') }}" class="quick-action" title="Álbuns de Imagens">
                            <i class="fas fa-image"></i>
                        </a>
                        <a href="#" class="quick-action" title="Notificações">
                            <i class="fas fa-bell"></i>
                        </a>
                        <a href="{{ route('contact') }}" class="quick-action" title="Suporte">
                            <i class="fas fa-headphones"></i>
                        </a>
                        @auth('customer')
                            <a href="{{ route('orders.index') }}" class="quick-action" title="Minha Conta">
                                <i class="fas fa-user"></i>
                            </a>
                        @elseauth('admin')
                            <a href="{{ route('admin.dashboard') }}" class="quick-action" title="Painel Admin">
                                <i class="fas fa-user-shield"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="quick-action" title="Entrar">
                                <i class="fas fa-user"></i>
                            </a>
                        @endauth
                        <div class="quick-action quick-action-total" title="Subtotal do carrinho">
                            {{ 'R$ ' . $cartTotalValueFormatted }}
                        </div>
                        <a href="{{ route('cart.index') }}" class="quick-action quick-action-cart" title="Carrinho">
                            <i class="fas fa-shopping-basket"></i>
                            <span class="quick-action-badge">{{ $cartCount }}</span>
                        </a>
                    </div>
                </div>

                <!-- Header Icons -->
                <div class="header-icons ms-auto">
                    <a href="#" class="header-icon" title="Loja">
                        <i class="fas fa-store"></i>
                    </a>
                    <a href="{{ route('albums.index') }}" class="header-icon" title="Álbuns de Imagens">
                        <i class="fas fa-image"></i>
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
                    {{-- Botão temporário de "Remover Frete" removido após limpeza geral de frete/CEP e integração admin com Melhor Envio --}}
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

    {{-- Shipping Offcanvas removido --}}

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
    
        @auth('admin')
        <!-- Modal Global: Trocar imagem do produto (upload ou URL) -->
        <div class="modal fade" id="quickImageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--primary-color); color: #fff;">
                        <h5 class="modal-title">Trocar imagem do produto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <input type="hidden" id="qiProductId" />

                            <div class="mb-3 d-flex align-items-start gap-3">
                                <div style="width:120px; height:80px; flex:0 0 120px;">
                                    <img id="qiPreview" src="{{ asset('images/no-image.svg') }}" alt="Preview" style="width:100%; height:100%; object-fit:cover; border-radius:6px; border:1px solid var(--border-color);" />
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-semibold">Atualize a imagem do produto</p>
                                    <p class="mb-2 text-muted small">Cole (Ctrl+V), arraste & solte ou use o botão abaixo para escolher um arquivo. Como alternativa, informe uma URL.</p>
                                    <div class="mb-2">
                                        <label class="form-label mb-1">Enviar arquivo <span class="text-muted small">(arraste/cole)</span></label>
                                        <input type="file" id="qiFile" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="text-center text-muted my-2">ou</div>

                            <div class="mb-3">
                                    <label class="form-label">Usar link (URL)</label>
                                    <input type="url" id="qiUrl" class="form-control" placeholder="https://exemplo.com/imagem.jpg">
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Formatos: jpeg, png, jpg, gif, webp, avif. Máx 10MB.</small>
                                <small class="text-muted">Dica: após colar, verifique o preview antes de salvar.</small>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" id="qiRemoveBtn">Remover</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="qiSaveBtn" style="background: var(--secondary-color); border-color: var(--secondary-color);">Salvar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function(){
                const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let qiModal, qiCurrentImgEl = null;
                document.addEventListener('click', function(e){
                    const img = e.target.closest('.js-change-image');
                    if (!img) return;
                    const pid = img.getAttribute('data-product-id');
                    if (!pid) return;
                    e.preventDefault();
                    e.stopPropagation();
                    qiCurrentImgEl = img;
                    document.getElementById('qiProductId').value = pid;
                    document.getElementById('qiFile').value = '';
                    const urlInput = document.getElementById('qiUrl'); if (urlInput) urlInput.value = '';
                    if (!qiModal) { qiModal = new bootstrap.Modal(document.getElementById('quickImageModal')); }
                    qiModal.show();
                }, true);

                function updateTargetImage(newSrc){
                    // Atualiza preview interno do modal
                    const preview = document.getElementById('qiPreview');
                    if (preview) preview.src = newSrc || `{{ asset('images/no-image.svg') }}`;
                    // Atualiza a imagem alvo na lista (preview em tela) apenas se tivermos referência
                    if (qiCurrentImgEl && newSrc) {
                        qiCurrentImgEl.src = newSrc;
                    }
                }

                function postUpdateImages(productId, body, isJson){
                    return fetch(`/admin/products/${productId}/update-images`, {
                        method: 'POST',
                        headers: isJson ? { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } : { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body
                    }).then(r => r.json());
                }

                document.getElementById('qiSaveBtn').addEventListener('click', function(){
                    const pid = document.getElementById('qiProductId').value;
                    const file = document.getElementById('qiFile').files[0];
                    const url = document.getElementById('qiUrl').value.trim();
                    if (!pid) return;
                    if (file) {
                        const fd = new FormData();
                        fd.append('featured_image', file);
                        postUpdateImages(pid, fd, false)
                            .then(data => {
                                if (!data.success) throw new Error(data.message || 'Erro ao atualizar imagem');
                                if (Array.isArray(data.images) && data.images.length) updateTargetImage(data.images[0]);
                                qiModal && qiModal.hide();
                            })
                            .catch(err => alert(err.message));
                    } else if (url) {
                        postUpdateImages(pid, JSON.stringify({ featured_image_url: url }), true)
                            .then(data => {
                                if (!data.success) throw new Error(data.message || 'Erro ao atualizar imagem via URL');
                                if (Array.isArray(data.images) && data.images.length) updateTargetImage(data.images[0]);
                                qiModal && qiModal.hide();
                            })
                            .catch(err => alert(err.message));
                    } else {
                        alert('Envie um arquivo ou informe uma URL.');
                    }
                });

                document.getElementById('qiRemoveBtn').addEventListener('click', function(){
                    const pid = document.getElementById('qiProductId').value;
                    if (!pid) return;
                    const fd = new FormData();
                    fd.append('remove_featured_image', '1');
                    fd.append('all_images_removed', '0');
                    postUpdateImages(pid, fd, false)
                        .then(data => {
                            if (!data.success) throw new Error(data.message || 'Erro ao remover imagem');
                            if (qiCurrentImgEl) qiCurrentImgEl.src = `{{ asset('images/no-image.svg') }}`;
                            qiModal && qiModal.hide();
                        })
                        .catch(err => alert(err.message));
                });

                // Allow paste (Ctrl+V) of image files or image URLs into the modal
                const qiModalEl = document.getElementById('quickImageModal');
                function qiPasteHandler(e){
                    try {
                        const clipboard = e.clipboardData || window.clipboardData;
                        if (!clipboard) return;
                        // First, prefer file items (images pasted from clipboard)
                        const items = clipboard.items;
                        if (items && items.length) {
                            for (let i = 0; i < items.length; i++) {
                                const it = items[i];
                                if (it.kind === 'file' && it.type && it.type.indexOf('image/') === 0) {
                                    const blob = it.getAsFile();
                                    const ext = (blob.type || 'image/png').split('/').pop();
                                    const file = new File([blob], `pasted-image.${ext}`, { type: blob.type });
                                    const dt = new DataTransfer();
                                    dt.items.add(file);
                                    const fileInput = document.getElementById('qiFile');
                                    if (fileInput) fileInput.files = dt.files;
                                    const urlInput = document.getElementById('qiUrl'); if (urlInput) urlInput.value = '';
                                    updateTargetImage(URL.createObjectURL(file));
                                    return;
                                }
                            }
                        }
                        // Fallback: if clipboard contains a text URL to an image, use it
                        const text = clipboard.getData ? (clipboard.getData('text') || clipboard.getData('Text')) : null;
                        if (text && /\.(jpe?g|png|gif|webp|avif|svg)(\?|$)/i.test(text.trim())) {
                            const urlInput = document.getElementById('qiUrl');
                            if (urlInput) urlInput.value = text.trim();
                            updateTargetImage(text.trim());
                        }
                    } catch(err) {
                        console.error('paste handler error', err);
                    }
                }

                if (qiModalEl) {
                    qiModalEl.addEventListener('shown.bs.modal', function(){ 
                        document.addEventListener('paste', qiPasteHandler);
                        // set preview to current product image (if available)
                        const preview = document.getElementById('qiPreview');
                        try {
                            if (preview) preview.src = (qiCurrentImgEl && qiCurrentImgEl.src) ? qiCurrentImgEl.src : `{{ asset('images/no-image.svg') }}`;
                        } catch(e) {
                            console.error('Erro ao setar preview do modal', e);
                        }
                    });
                    qiModalEl.addEventListener('hidden.bs.modal', function(){ 
                        document.removeEventListener('paste', qiPasteHandler);
                        // reset preview to placeholder to reduce confusion
                        const preview = document.getElementById('qiPreview');
                        if (preview) preview.src = `{{ asset('images/no-image.svg') }}`;
                    });
                }
            })();
        </script>
        @endauth
    
        {{-- Global image paste & drop support: attach paste/drag handlers to any file inputs that accept images --}}
        <script>
            (function(){
                document.addEventListener('DOMContentLoaded', function(){
                    function makePasteHandler(fileInput){
                        return function(e){
                            try {
                                const clipboard = e.clipboardData || window.clipboardData;
                                if (!clipboard) return;
                                const items = clipboard.items;
                                if (items && items.length) {
                                    for (let i = 0; i < items.length; i++) {
                                        const it = items[i];
                                        if (it.kind === 'file' && it.type && it.type.indexOf('image/') === 0) {
                                            const blob = it.getAsFile();
                                            const ext = (blob.type || 'image/png').split('/').pop();
                                            const file = new File([blob], `pasted-image.${ext}`, { type: blob.type });
                                            const dt = new DataTransfer(); dt.items.add(file);
                                            fileInput.files = dt.files;
                                            // dispatch change so existing handlers (previews) run
                                            try { fileInput.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) { }
                                            // update preview if present in the same container
                                            const container = fileInput.closest('.modal, .image-upload, form') || document;
                                            const preview = container.querySelector('img[data-upload-preview], img.preview');
                                            if (preview) preview.src = URL.createObjectURL(file);
                                            const urlInput = container.querySelector('input[type="url"]'); if (urlInput) urlInput.value = '';
                                            return;
                                        }
                                    }
                                }
                                const text = clipboard.getData ? (clipboard.getData('text') || clipboard.getData('Text')) : null;
                                if (text && /\.(jpe?g|png|gif|webp|avif|svg)(\?|$)/i.test(text.trim())) {
                                    const url = text.trim();
                                    const container = fileInput.closest('.modal, .image-upload, form') || document;
                                    const urlInput = container.querySelector('input[type="url"]'); if (urlInput) urlInput.value = url;
                                    const preview = container.querySelector('img[data-upload-preview], img.preview');
                                    if (preview) preview.src = url;
                                }
                            } catch(err) {
                                console.error('paste handler error', err);
                            }
                        };
                    }

                    const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
                    fileInputs.forEach(function(fi){
                        const modal = fi.closest('.modal');
                        const handler = makePasteHandler(fi);
                        if (modal) {
                            modal.addEventListener('shown.bs.modal', function(){ document.addEventListener('paste', handler); });
                            modal.addEventListener('hidden.bs.modal', function(){ document.removeEventListener('paste', handler); });
                        } else {
                            fi.addEventListener('focus', function(){ document.addEventListener('paste', handler); });
                            fi.addEventListener('blur', function(){ document.removeEventListener('paste', handler); });
                        }

                        // basic drag & drop support on the input parent
                        const dropZone = fi.closest('.dropzone') || fi.parentElement;
                        if (dropZone) {
                            dropZone.addEventListener('dragover', function(e){ e.preventDefault(); dropZone.classList.add('dragover'); });
                            dropZone.addEventListener('dragleave', function(){ dropZone.classList.remove('dragover'); });
                            dropZone.addEventListener('drop', function(e){
                                e.preventDefault(); dropZone.classList.remove('dragover');
                                const files = e.dataTransfer.files;
                                if (files && files.length) {
                                const f = files[0];
                                if (f.type && f.type.indexOf('image/') === 0) {
                                    const dt = new DataTransfer(); dt.items.add(f); fi.files = dt.files;
                                    // trigger change so previews are created by page scripts
                                    try { fi.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) { }
                                    const preview = dropZone.querySelector('img[data-upload-preview], img.preview');
                                    if (preview) preview.src = URL.createObjectURL(f);
                                    const urlInput = dropZone.querySelector('input[type="url"]'); if (urlInput) urlInput.value = '';
                                }
                            }
                            });
                        }
                    });
                });
            })();
        </script>

        {{-- Busca Inteligente Flutuante para Admin (global) --}}
        @include('partials.smart-search')
        
        @auth('admin')
            {{-- Include banner edit modal globally for admins so they can edit from front-end view --}}
            @includeIf('admin.banners.modal-edit')

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Reuse the same modal used in admin pages (#editBannerModal)
                const editModalEl = document.getElementById('editBannerModal');
                if (!editModalEl) return;
                const editModal = new bootstrap.Modal(editModalEl);
                const modalBody = document.getElementById('editBannerModalBody');

                function openBannerEditor(bannerId, bannerTitle){
                    if (!bannerId) return;
                    document.getElementById('editBannerModalLabel').innerHTML = `<i class="bi bi-pencil"></i> Editar Banner: ${bannerTitle || ''}`;
                    modalBody.innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-3 text-muted">Carregando formulário...</p>
                        </div>
                    `;
                    editModal.show();

                    fetch(`{{ route('admin.banners.edit', ':id') }}`.replace(':id', bannerId), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(r => {
                        if (!r.ok) throw new Error('Erro ao carregar formulário');
                        return r.text();
                    }).then(html => {
                        modalBody.innerHTML = html;
                    }).catch(err => {
                        console.error(err);
                        modalBody.innerHTML = `<div class="alert alert-danger">Erro ao carregar formulário: ${err.message}</div>`;
                    });
                }

                document.body.addEventListener('click', function(e){
                    const btn = e.target.closest('.edit-banner-btn');
                    if (!btn) return;
                    e.preventDefault();
                    const id = btn.dataset.bannerId;
                    const title = btn.dataset.bannerTitle || '';
                    openBannerEditor(id, title);
                });

                // Also allow left-clicking the banner wrapper itself to open the editor.
                // We avoid interfering with normal links, buttons or modifier-key clicks so
                // admins can still navigate normally when needed.
                document.body.addEventListener('click', function(e){
                    const wrapper = e.target.closest('[data-banner-id]');
                    if (!wrapper) return;
                    // If the click was on the small edit button, it's already handled above.
                    if (e.target.closest('.edit-banner-btn')) return;
                    // Don't hijack clicks on interactive elements (links, buttons, inputs, labels)
                    if (e.target.closest('a, button, input, textarea, select, label')) return;
                    // Respect modifier keys (allow ctrl/cmd/shift clicks to act as usual)
                    if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                    // Prevent default navigation and open the editor for this banner
                    e.preventDefault();
                    e.stopPropagation();
                    const id = wrapper.dataset.bannerId;
                    const title = wrapper.dataset.bannerTitle || '';
                    openBannerEditor(id, title);
                });
            });
            </script>

        <script>
            // Delegated handler for banner edit form submit so injected form (via innerHTML)
            // is submitted via AJAX and does not navigate away.
            document.addEventListener('submit', function(e){
                const form = e.target.closest('#banner-edit-form');
                if (!form) return;
                // only handle when form is inside the edit modal
                if (!document.getElementById('editBannerModal')) return;
                e.preventDefault();
                (async function(){
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalHtml = submitBtn ? submitBtn.innerHTML : null;
                    try {
                        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...'; }
                        // remove any previous ajax error block
                        const prev = form.querySelector('.ajax-errors'); if (prev) prev.remove();
                        const fd = new FormData(form);
                        const resp = await fetch(form.action, {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json, text/html'
                            },
                            credentials: 'same-origin'
                        });

                        const contentType = (resp.headers.get('content-type') || '');
                        function showToast(msg){
                            const toastEl = document.getElementById('toast-notification');
                            const toastMessage = document.getElementById('toast-message');
                            if (toastMessage) toastMessage.textContent = msg;
                            if (toastEl) { const t = new bootstrap.Toast(toastEl); t.show(); }
                            else console.log(msg);
                        }

                        function renderErrors(errors){
                            const container = document.createElement('div');
                            container.className = 'alert alert-danger ajax-errors';
                            let html = '<strong><i class="bi bi-exclamation-triangle"></i> Erros:</strong><ul class="mb-0 mt-2">';
                            for (const key in errors){
                                if (!errors.hasOwnProperty(key)) continue;
                                const arr = errors[key];
                                for (const m of arr){ html += `<li>${m}</li>`; }
                            }
                            html += '</ul>';
                            container.innerHTML = html;
                            form.insertBefore(container, form.firstChild);
                            const firstKey = Object.keys(errors)[0];
                            if (firstKey){
                                const field = form.querySelector(`[name="${firstKey}"]`);
                                if (field) try{ field.focus(); }catch(e){}
                            }
                        }

                        if (contentType.includes('application/json')){
                            const json = await resp.json();
                            if (json && json.success) {
                                showToast(json.message || 'Banner atualizado com sucesso!');
                                const modalEl = document.getElementById('editBannerModal');
                                if (modalEl){ const bs = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); bs.hide(); }
                                try { window.dispatchEvent(new CustomEvent('banner:updated', { detail: { id: json.id || null } } )); } catch(e){}
                            } else if (json && json.errors) {
                                renderErrors(json.errors || {});
                            } else {
                                showToast('Resposta inesperada do servidor.');
                            }
                        } else if (contentType.includes('text/html')){
                            const text = await resp.text();
                            if (/banner atualizado/i.test(text) || /Banner atualizado/i.test(text)){
                                showToast('Banner atualizado com sucesso!');
                                const modalEl = document.getElementById('editBannerModal');
                                if (modalEl){ const bs = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); bs.hide(); }
                                try { window.dispatchEvent(new CustomEvent('banner:updated', { detail: { id: null } } )); } catch(e){}
                            } else {
                                // server returned HTML (probably the same form with errors) -> replace modal body
                                const modalBody = document.getElementById('editBannerModalBody');
                                if (modalBody) modalBody.innerHTML = text;
                            }
                        } else if (resp.status === 422) {
                            const json = await resp.json();
                            renderErrors(json.errors || {});
                        } else {
                            const text = await resp.text();
                            if (/banner atualizado/i.test(text)){
                                showToast('Banner atualizado com sucesso!');
                                const modalEl = document.getElementById('editBannerModal');
                                if (modalEl){ const bs = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); bs.hide(); }
                                try { window.dispatchEvent(new CustomEvent('banner:updated', { detail: { id: null } } )); } catch(e){}
                            } else {
                                alert('Erro ao salvar banner. Veja o console para mais detalhes.');
                                console.error('Resposta inesperada ao salvar banner:', resp, text);
                            }
                        }
                    } catch(err){
                        console.error('Erro AJAX ao salvar banner', err);
                        alert('Erro ao salvar banner: ' + (err.message || err));
                    } finally {
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalHtml; }
                    }
                })();
            }, true);
        </script>
        <script>
            // Quando um banner for atualizado, solicitar o fragmento renderizado e substituir
            // todas as ocorrências no DOM com data-banner-id="{id}"
            window.addEventListener('banner:updated', function(e){
                try {
                    const id = (e && e.detail && e.detail.id) ? e.detail.id : null;
                    if (!id) return;
                    const url = `/admin/banners/${id}/fragment`;
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
                        .then(r => {
                            if (!r.ok) throw new Error('Erro ao buscar fragmento do banner');
                            return r.text();
                        })
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newEl = doc.body.firstElementChild;
                            if (!newEl) return;
                            const selector = `[data-banner-id="${id}"]`;
                            const els = Array.from(document.querySelectorAll(selector));
                            els.forEach(el => {
                                el.replaceWith(newEl.cloneNode(true));
                            });
                        }).catch(err => console.error('Erro ao atualizar banner no DOM', err));
                } catch (err) { console.error('banner:updated handler error', err); }
            });
        </script>
        @endauth
</body>
</html>
