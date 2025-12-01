@extends('layouts.app')

@section('title', 'Feira das Fábricas - Home')

@section('styles')
<style>
    /* Elegant Dark Blue Theme */
    :root {
        --elegant-dark: #0f172a;
        --elegant-blue: #1e293b;
        --elegant-light: #334155;
        --elegant-white: #ffffff;
        --elegant-gray: #f8fafc;
        --elegant-text: #1e293b;
        --elegant-text-light: #64748b;
        /* Acento deste template herda da cor secundária global */
        --elegant-accent: var(--secondary-color);
    }

    body {
        background-color: var(--elegant-white);
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    }

    /* Hero Section - Elegant */
    .hero-section {
        min-height: 50vh;
        position: relative;
        overflow: hidden;
        padding: 0;
    }

    /* Hero Banner Full */
    .hero-banner-full {
        width: 100%;
        height: 50vh;
        position: relative;
        overflow: hidden;
    }

    .hero-banner-image {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        /* end of hero-banner-title styles */
    }

    .elegant-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px color-mix(in srgb, var(--elegant-accent), transparent 60%);
        background: linear-gradient(135deg, color-mix(in srgb, var(--elegant-accent), white 10%) 0%, var(--elegant-accent) 100%);
        color: white;
    }

    .elegant-btn-outline {
        background: transparent;
        color: var(--elegant-white);
        border: 2px solid var(--elegant-white);
        padding: 13px 33px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .elegant-btn-outline:hover {
        background: var(--elegant-accent);
        color: white;
        border-color: var(--elegant-accent);
        transform: translateY(-3px);
    }

    /* Banner Hero Prominent */
    .hero-banner-section {
        background: var(--elegant-white);
        padding: 80px 0;
        position: relative;
    }

    /* Default hero background + overlay (ensure default banner visible) */
    .hero-default-bg {
        background: linear-gradient(135deg, var(--elegant-dark) 0%, var(--elegant-blue) 50%, var(--elegant-light) 100%);
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
    }

    .hero-banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(15,23,42,0.7) 0%, rgba(30,41,59,0.5) 50%, rgba(51,65,85,0.3) 100%);
        display: flex;
        align-items: center;
    }

    .hero-content-row { height: 50vh; min-height: 350px; }

    .hero-banner-content { color: white; z-index: 2; position: relative; }

    .hero-banner-title {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0.8rem;
        line-height: 1.1;
        background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .banner-card {
        background: var(--elegant-white);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(30, 58, 138, 0.15);
        transition: all 0.3s ease;
        border: 1px solid rgba(30, 58, 138, 0.1);
    }

    .banner-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 70px rgba(30, 58, 138, 0.2);
    }

    .banner-image {
        height: 400px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .banner-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(30, 58, 138, 0.9));
        color: white;
        padding: 40px;
    }

    .banner-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .banner-description {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 25px;
    }

    /* Sections */
    .section-elegant {
        padding: 10px 0;
        background: var(--elegant-white);
    }

    .section-dark {
        background: var(--elegant-dark);
        color: white;
        padding: 30px 0;
    }

    .section-gray {
        background: var(--elegant-gray);
        padding: 30px 0;
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--elegant-dark);
        margin-bottom: 8px;
        text-align: center;
    }

    .section-title-white {
        color: white;
    }

    .section-subtitle {
        font-size: 0.85rem;
        color: var(--elegant-text-light);
        text-align: center;
        margin-bottom: 15px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .section-subtitle-white {
        color: rgba(255,255,255,0.8);
    }

    /* Cards */
    .elegant-card {
        background: var(--elegant-white);
        border-radius: 8px;
        padding: 12px;
        /* subtle shadow using primary color */
        box-shadow: 0 3px 12px color-mix(in srgb, var(--primary-color, #1e293b), transparent 85%);
        transition: all 0.3s ease;
        border: 1px solid color-mix(in srgb, var(--primary-color, #1e293b), transparent 88%);
        height: 100%;
        /* Keep a sensible minimum height so cards don't collapse when most elements are hidden */
        min-height: 150px;
        position: relative;
    }

    /* Button positioning helpers for category cards */
    /* Default: bottom position uses normal flow so it doesn't overlap the card content when not intended */
    .elegant-card .btn-outline-primary { position: static; left: auto; width: 100%; z-index: 1; }
    /* Ensure bottom-positioned cards place the button at the end of the card content */
    .elegant-card.btn-pos-bottom { display: flex; flex-direction: column; }
    .elegant-card.btn-pos-bottom .btn-outline-primary { margin-top: auto; position: static; }
    /* Top: place the button as an absolute overlay at the top of the card */
    .elegant-card.btn-pos-top .btn-outline-primary { position: absolute; left: 50%; width: calc(100% - 32px); top: 12px; z-index: 3; --btn-transform: translateX(-50%); transform: var(--btn-transform); }
    /* Center: absolute and centered in the middle of the card */
    .elegant-card.btn-pos-center .btn-outline-primary { position: absolute; left: 50%; width: calc(100% - 32px); top: 50%; z-index: 3; --btn-transform: translate(-50%, -50%); transform: var(--btn-transform); }
    /* Left: pin the button to the left edge but inset to match site container */
    .elegant-card.btn-pos-left .btn-outline-primary { position: absolute; left: 16px; width: calc(100% - 48px); top: 50%; transform: translateY(-50%); z-index: 3; }
    /* Right: pin the button to the right edge but inset to match site container */
    .elegant-card.btn-pos-right .btn-outline-primary { position: absolute; right: 16px; width: calc(100% - 48px); top: 50%; transform: translateY(-50%); z-index: 3; }

    /* Improved cover handling */
    .elegant-card.has-cover {
        color: #fff;
        position: relative;
        overflow: hidden;
        background-color: transparent;
        /* Covers often look better a bit taller */
        min-height: 180px;
    }

    .elegant-card.has-cover::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.35) 0%, rgba(0,0,0,0.45) 60%);
        pointer-events: none;
        z-index: 1;
    }

    .elegant-card.has-cover .card-icon,
    .elegant-card.has-cover .card-title,
    .elegant-card.has-cover .card-text,
    .elegant-card.has-cover .btn-outline-primary {
        position: relative;
        z-index: 2;
        color: #fff !important;
    }

    .elegant-card .card-icon img.category-image-display {
        width: 64px !important;
        height: 64px !important;
        border-radius: 50%;
        box-shadow: 0 6px 18px rgba(0,0,0,0.35);
        border: 3px solid rgba(255,255,255,0.85);
    }

    .elegant-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px color-mix(in srgb, var(--primary-color, #1e293b), transparent 70%);
    }

    .card-icon {
        width: 40px;
        height: 40px;
        /* Solid primary color for clarity (no noisy gradient) */
        background: var(--primary-color, var(--elegant-blue));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        color: white;
        font-size: 1.1rem;
        box-shadow: 0 6px 18px color-mix(in srgb, var(--primary-color, var(--elegant-blue)), transparent 70%);
    }

    .card-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--elegant-dark);
        margin-bottom: 6px;
        text-align: center;
    }

    .card-text {
        color: var(--elegant-text-light);
        text-align: center;
        margin-bottom: 10px;
        font-size: 0.8rem;
        line-height: 1.3;
    }

    /* Botão compacto para cards de categoria - agora preenchido com a cor primaria */
    .elegant-card .btn-outline-primary {
        padding: 8px 16px;
        font-size: 0.85rem;
        border-radius: 6px;
        border: none;
        background: var(--primary-color);
        color: #fff;
        font-weight: 600;
        transition: all 0.22s ease;
        box-shadow: 0 6px 18px color-mix(in srgb, var(--primary-color, #1e293b), transparent 70%);
    }

    .elegant-card .btn-outline-primary:hover {
        background: linear-gradient(135deg, var(--primary-color, var(--elegant-blue)) 0%, var(--secondary-color, var(--elegant-dark)) 100%);
        color: #fff;
        transform: var(--btn-transform, translateX(0)) translateY(-3px);
    }

    /* Product Cards */
    .product-card {
        background: var(--elegant-white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.04);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .product-image {
        height: 160px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        aspect-ratio: 1;
        flex-shrink: 0;
        border-radius: 12px 12px 0 0;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.2s ease;
    }
    
    /* Efeito sutil para imagens */
    .product-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.05);
        z-index: 1;
        pointer-events: none;
    }
    
    .product-image img {
        position: relative;
        z-index: 2;
    }

    .product-card:hover .product-image img {
        transform: scale(1.02);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--elegant-blue);
        color: white;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
        z-index: 10;
    }

    .product-info {
        padding: 16px;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
        background: linear-gradient(180deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.8) 100%);
    }

    .product-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
        line-height: 1.3;
        flex-grow: 1;
        letter-spacing: -0.025em;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: 800;
        color: #059669;
        margin-bottom: 8px;
        flex-shrink: 0;
        letter-spacing: -0.025em;
    }

    .product-btn {
        background: #1f2937;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        width: 100%;
        flex-shrink: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(31, 41, 55, 0.2);
        text-decoration: none;
    }
    
    .product-btn:hover {
        background: var(--elegant-blue);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    /* Align product buttons when card button position is left/right (inset by container spacing)
       Use a CSS variable so it's easy to tweak the inset globally. */
    :root { --card-btn-inset: 24px; }
    .product-card.btn-pos-left .product-info { position: relative; }
    .product-card.btn-pos-left .product-info .product-btn {
        position: absolute;
        left: var(--card-btn-inset);
        width: calc(100% - (var(--card-btn-inset) * 2 + 0px));
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
    }

    .product-card.btn-pos-right .product-info { position: relative; }
    .product-card.btn-pos-right .product-info .product-btn {
        position: absolute;
        right: var(--card-btn-inset);
        width: calc(100% - (var(--card-btn-inset) * 2 + 0px));
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
    }

    /* On small screens keep buttons in normal flow to avoid overlap */
    @media (max-width: 576px) {
        .product-card.btn-pos-left .product-info .product-btn,
        .product-card.btn-pos-right .product-info .product-btn {
            position: static;
            width: 100%;
            transform: none;
            top: auto;
            left: auto;
            right: auto;
        }
    }

    /* Botão de Editar Admin */
    .product-info .d-flex {
        align-items: center;
        flex-shrink: 0;
    }

    .product-info .btn-outline-primary {
        border: 1px solid var(--elegant-blue);
        color: var(--elegant-blue);
        padding: 8px 12px;
        font-size: 0.8rem;
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        background: rgba(30, 58, 138, 0.05);
    }

    .product-info .btn-outline-primary:hover {
        background: linear-gradient(135deg, var(--elegant-blue) 0%, #1e40af 100%);
        border-color: var(--elegant-blue);
        color: white;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.3);
    }

    @media (max-width: 576px) {
        .product-info .d-flex {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .product-info .product-btn {
            flex: 1 1 100% !important;
        }

        .product-info .btn-outline-primary {
            width: 100%;
        }
    }

    /* Carrossel de Produtos */
    #productsCarousel {
        position: relative;
    }

    #productsCarousel .carousel-control-prev,
    #productsCarousel .carousel-control-next {
        width: 50px;
        height: 50px;
        background: var(--elegant-blue);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
        transition: all 0.3s ease;
        z-index: 5;
    }

    #productsCarousel .carousel-control-prev {
        left: -25px;
    }

    #productsCarousel .carousel-control-next {
        right: -25px;
    }

    #productsCarousel .carousel-control-prev:hover,
    #productsCarousel .carousel-control-next:hover {
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
    }

    #productsCarousel .carousel-control-prev-icon,
    #productsCarousel .carousel-control-next-icon {
        width: 20px;
        height: 20px;
    }

    @media (max-width: 576px) {
        #productsCarousel .carousel-control-prev,
        #productsCarousel .carousel-control-next {
            width: 38px;
            height: 38px;
            left: auto;
            right: auto;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
        }

        #productsCarousel .carousel-control-prev {
            left: 6px;
        }

        #productsCarousel .carousel-control-next {
            right: 6px;
        }

        #productsCarousel .carousel-control-prev-icon,
        #productsCarousel .carousel-control-next-icon {
            width: 16px;
            height: 16px;
        }
    }

    /* Indicadores do carrossel */
    #productsCarousel .carousel-indicators {
        bottom: -30px;
        margin-bottom: 0;
    }

    #productsCarousel .carousel-indicators button {
        background-color: var(--elegant-blue);
        border-radius: 50%;
        width: 12px;
        height: 12px;
        margin: 0 5px;
    }

    #productsCarousel .carousel-indicators button.active {
        background-color: var(--elegant-dark);
    }

    /* Carrossel Apple */
    #appleCarousel {
        position: relative;
    }

    #appleCarousel .carousel-control-prev,
    #appleCarousel .carousel-control-next {
        width: 50px;
        height: 50px;
        background: #000;
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
        transition: all 0.3s ease;
        z-index: 5;
    }

    #appleCarousel .carousel-control-prev {
        left: -25px;
    }

    #appleCarousel .carousel-control-next {
        right: -25px;
    }

    #appleCarousel .carousel-control-prev:hover,
    #appleCarousel .carousel-control-next:hover {
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
        background: #333;
    }

    #appleCarousel .carousel-control-prev-icon,
    #appleCarousel .carousel-control-next-icon {
        width: 20px;
        height: 20px;
    }

    @media (max-width: 576px) {
        #appleCarousel .carousel-control-prev,
        #appleCarousel .carousel-control-next {
            width: 38px;
            height: 38px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
        }

        #appleCarousel .carousel-control-prev {
            left: 6px;
        }

        #appleCarousel .carousel-control-next {
            right: 6px;
        }

        #appleCarousel .carousel-control-prev-icon,
        #appleCarousel .carousel-control-next-icon {
            width: 16px;
            height: 16px;
        }
    }

    /* Ajustes para demais carrosséis de marcas */
    @media (max-width: 576px) {
        #samsungCarousel .carousel-control-prev,
        #samsungCarousel .carousel-control-next,
        #xiaomiCarousel .carousel-control-prev,
        #xiaomiCarousel .carousel-control-next,
        #motorolaCarousel .carousel-control-prev,
        #motorolaCarousel .carousel-control-next,
        #infinixCarousel .carousel-control-prev,
        #infinixCarousel .carousel-control-next,
        #jblCarousel .carousel-control-prev,
        #jblCarousel .carousel-control-next,
        #oppoCarousel .carousel-control-prev,
        #oppoCarousel .carousel-control-next,
        #realmeCarousel .carousel-control-prev,
        #realmeCarousel .carousel-control-next,
        #tecnoCarousel .carousel-control-prev,
        #tecnoCarousel .carousel-control-next {
            width: 38px;
            height: 38px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
            z-index: 5;
        }

        #samsungCarousel .carousel-control-prev,
        #xiaomiCarousel .carousel-control-prev,
        #motorolaCarousel .carousel-control-prev,
        #infinixCarousel .carousel-control-prev,
        #jblCarousel .carousel-control-prev,
        #oppoCarousel .carousel-control-prev,
        #realmeCarousel .carousel-control-prev,
        #tecnoCarousel .carousel-control-prev {
            left: 6px;
        }

        #samsungCarousel .carousel-control-next,
        #xiaomiCarousel .carousel-control-next,
        #motorolaCarousel .carousel-control-next,
        #infinixCarousel .carousel-control-next,
        #jblCarousel .carousel-control-next,
        #oppoCarousel .carousel-control-next,
        #realmeCarousel .carousel-control-next,
        #tecnoCarousel .carousel-control-next {
            right: 6px;
        }

        #samsungCarousel .carousel-control-prev-icon,
        #samsungCarousel .carousel-control-next-icon,
        #xiaomiCarousel .carousel-control-prev-icon,
        #xiaomiCarousel .carousel-control-next-icon,
        #motorolaCarousel .carousel-control-prev-icon,
        #motorolaCarousel .carousel-control-next-icon,
        #infinixCarousel .carousel-control-prev-icon,
        #infinixCarousel .carousel-control-next-icon,
        #jblCarousel .carousel-control-prev-icon,
        #jblCarousel .carousel-control-next-icon,
        #oppoCarousel .carousel-control-prev-icon,
        #oppoCarousel .carousel-control-next-icon,
        #realmeCarousel .carousel-control-prev-icon,
        #realmeCarousel .carousel-control-next-icon,
        #tecnoCarousel .carousel-control-prev-icon,
        #tecnoCarousel .carousel-control-next-icon {
            width: 16px;
            height: 16px;
        }
    }

    /* Indicadores do carrossel Apple */
    #appleCarousel .carousel-indicators {
        bottom: -30px;
        margin-bottom: 0;
    }

    #appleCarousel .carousel-indicators button {
        background-color: #ccc;
        border-radius: 50%;
        width: 12px;
        height: 12px;
        margin: 0 5px;
    }

    #appleCarousel .carousel-indicators button.active {
        background-color: #000;
    }

    /* Card Apple especial */
    .apple-card {
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .apple-card:hover {
        border-color: rgba(0, 0, 0, 0.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .apple-badge {
        background: #000 !important;
        color: white !important;
    }

    /* Carrosséis de Marcas */
    #samsungCarousel,
    #xiaomiCarousel,
    #motorolaCarousel {
        position: relative;
    }

    #samsungCarousel .carousel-control-prev,
    #samsungCarousel .carousel-control-next,
    #xiaomiCarousel .carousel-control-prev,
    #xiaomiCarousel .carousel-control-next,
    #motorolaCarousel .carousel-control-prev,
    #motorolaCarousel .carousel-control-next {
        width: 45px;
        height: 45px;
        background: var(--elegant-blue);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
        transition: all 0.2s ease;
    }

    #samsungCarousel .carousel-control-prev,
    #xiaomiCarousel .carousel-control-prev,
    #motorolaCarousel .carousel-control-prev {
        left: -22px;
    }

    #samsungCarousel .carousel-control-next,
    #xiaomiCarousel .carousel-control-next,
    #motorolaCarousel .carousel-control-next {
        right: -22px;
    }

    #samsungCarousel .carousel-control-prev:hover,
    #samsungCarousel .carousel-control-next:hover,
    #xiaomiCarousel .carousel-control-prev:hover,
    #xiaomiCarousel .carousel-control-next:hover,
    #motorolaCarousel .carousel-control-prev:hover,
    #motorolaCarousel .carousel-control-next:hover {
        opacity: 1;
        transform: translateY(-50%) scale(1.05);
    }

    #samsungCarousel .carousel-control-prev-icon,
    #samsungCarousel .carousel-control-next-icon,
    #xiaomiCarousel .carousel-control-prev-icon,
    #xiaomiCarousel .carousel-control-next-icon,
    #motorolaCarousel .carousel-control-prev-icon,
    #motorolaCarousel .carousel-control-next-icon {
        width: 18px;
        height: 18px;
    }

    /* Indicadores das marcas */
    #samsungCarousel .carousel-indicators,
    #xiaomiCarousel .carousel-indicators,
    #motorolaCarousel .carousel-indicators {
        bottom: -30px;
        margin-bottom: 0;
    }

    #samsungCarousel .carousel-indicators button,
    #xiaomiCarousel .carousel-indicators button,
    #motorolaCarousel .carousel-indicators button {
        background-color: var(--elegant-blue);
        border-radius: 50%;
        width: 10px;
        height: 10px;
        margin: 0 4px;
    }

    #samsungCarousel .carousel-indicators button.active,
    #xiaomiCarousel .carousel-indicators button.active,
    #motorolaCarousel .carousel-indicators button.active {
        background-color: var(--elegant-dark);
    }

    /* Cards de marca */
    .brand-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .brand-card:hover {
        border-color: rgba(30, 58, 138, 0.2);
        box-shadow: 0 4px 16px rgba(30, 58, 138, 0.08);
    }

    .brand-badge {
        background: var(--elegant-blue) !important;
        color: white !important;
    }

    /* Carrosséis das Marcas Adicionais */
    #infinixCarousel,
    #jblCarousel,
    #oppoCarousel,
    #realmeCarousel,
    #tecnoCarousel {
        position: relative;
    }

    #infinixCarousel .carousel-control-prev,
    #infinixCarousel .carousel-control-next,
    #jblCarousel .carousel-control-prev,
    #jblCarousel .carousel-control-next,
    #oppoCarousel .carousel-control-prev,
    #oppoCarousel .carousel-control-next,
    #realmeCarousel .carousel-control-prev,
    #realmeCarousel .carousel-control-next,
    #tecnoCarousel .carousel-control-prev,
    #tecnoCarousel .carousel-control-next {
        width: 45px;
        height: 45px;
        background: var(--elegant-blue);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.8;
        transition: all 0.2s ease;
    }

    #infinixCarousel .carousel-control-prev,
    #jblCarousel .carousel-control-prev,
    #oppoCarousel .carousel-control-prev,
    #realmeCarousel .carousel-control-prev,
    #tecnoCarousel .carousel-control-prev {
        left: -22px;
    }

    #infinixCarousel .carousel-control-next,
    #jblCarousel .carousel-control-next,
    #oppoCarousel .carousel-control-next,
    #realmeCarousel .carousel-control-next,
    #tecnoCarousel .carousel-control-next {
        right: -22px;
    }

    #infinixCarousel .carousel-control-prev:hover,
    #infinixCarousel .carousel-control-next:hover,
    #jblCarousel .carousel-control-prev:hover,
    #jblCarousel .carousel-control-next:hover,
    #oppoCarousel .carousel-control-prev:hover,
    #oppoCarousel .carousel-control-next:hover,
    #realmeCarousel .carousel-control-prev:hover,
    #realmeCarousel .carousel-control-next:hover,
    #tecnoCarousel .carousel-control-prev:hover,
    #tecnoCarousel .carousel-control-next:hover {
        opacity: 1;
        transform: translateY(-50%) scale(1.05);
    }

    #infinixCarousel .carousel-control-prev-icon,
    #infinixCarousel .carousel-control-next-icon,
    #jblCarousel .carousel-control-prev-icon,
    #jblCarousel .carousel-control-next-icon,
    #oppoCarousel .carousel-control-prev-icon,
    #oppoCarousel .carousel-control-next-icon,
    #realmeCarousel .carousel-control-prev-icon,
    #realmeCarousel .carousel-control-next-icon,
    #tecnoCarousel .carousel-control-prev-icon,
    #tecnoCarousel .carousel-control-next-icon {
        width: 18px;
        height: 18px;
    }

    /* Indicadores das marcas adicionais */
    #infinixCarousel .carousel-indicators,
    #jblCarousel .carousel-indicators,
    #oppoCarousel .carousel-indicators,
    #realmeCarousel .carousel-indicators,
    #tecnoCarousel .carousel-indicators {
        bottom: -30px;
        margin-bottom: 20px;
    }

    #infinixCarousel .carousel-indicators button,
    #jblCarousel .carousel-indicators button,
    #oppoCarousel .carousel-indicators button,
    #realmeCarousel .carousel-indicators button,
    #tecnoCarousel .carousel-indicators button {
        background-color: var(--elegant-blue);
        border-radius: 50%;
        width: 10px;
        height: 10px;
        margin: 0 4px;
    }

    #infinixCarousel .carousel-indicators button.active,
    #jblCarousel .carousel-indicators button.active,
    #oppoCarousel .carousel-indicators button.active,
    #realmeCarousel .carousel-indicators button.active,
    #tecnoCarousel .carousel-indicators button.active {
        background-color: var(--elegant-dark);
    }

    /* Estado vazio */
    .empty-state {
        padding: 40px 20px;
    }

    .empty-state i {
        opacity: 0.5;
    }

    .empty-state h4 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .empty-state p {
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    /* B2B Section */
    .b2b-section {
        background: linear-gradient(135deg, var(--elegant-dark) 0%, var(--elegant-blue) 50%, var(--elegant-light) 100%);
        color: white;
        padding: 60px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 8px 32px rgba(15, 23, 42, 0.3);
    }

    .b2b-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .b2b-description {
        font-size: 1.2rem;
        margin-bottom: 30px;
        opacity: 0.9;
    }

    .b2b-btn {
        background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%);
        color: white;
        border: none;
        padding: 15px 35px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px color-mix(in srgb, var(--secondary-color), transparent 70%);
    }

    .b2b-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px color-mix(in srgb, var(--secondary-color), transparent 60%);
        background: linear-gradient(135deg, color-mix(in srgb, var(--secondary-color), white 10%) 0%, var(--secondary-color) 100%);
        color: white;
    }


    /* Responsive Geral */
    @media (max-width: 768px) {
        
        .section-title {
            font-size: 2rem;
        }
        
        .banner-image {
            height: 250px;
        }
        
        .banner-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 480px) {
    }

    /* Selos de Marcas (Department Badges) */
    .badges-wrapper {
        position: relative;
        overflow: hidden;
        width: 100%;
        overscroll-behavior: contain;
    }

    .badges-container {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
        gap: 15px;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 10px 0;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior-x: contain;
        scroll-snap-type: x proximity;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .badges-container::-webkit-scrollbar {
        display: none;
    }

    .badge-item {
        padding: 10px;
        transition: transform 0.3s ease;
        /* prevent items from stretching into non-square shapes on small screens */
        flex: 0 0 auto;
        min-width: 80px;
        max-width: 120px;
    }

    .badge-item:hover {
        transform: translateY(-5px);
    }

    .badge-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .badge-circle {
        width: 100%;
        max-width: 100px;
        border-radius: 50%;
        margin: 0 auto 10px;
        border: 2px solid rgba(30, 58, 138, 0.1);
        overflow: hidden;
        background: var(--elegant-white);
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
        position: relative;
    }

    /* Ensure a perfect square across all browsers (fallback for older mobile browsers)
       using the padding-top trick and absolutely positioned image. This avoids ovals
       when aspect-ratio is not supported. */
    .badge-circle::before {
        content: '';
        display: block;
        padding-top: 100%;
    }

    .badge-item:hover .badge-circle {
        border-color: var(--elegant-accent);
        box-shadow: 0 4px 12px rgba(255, 153, 0, 0.2);
        transform: scale(1.05);
    }

    .badge-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .badge-title {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--elegant-text);
        margin: 0;
        text-align: center;
        line-height: 1.3;
    }

    .badge-item:hover .badge-title {
        color: var(--elegant-accent);
    }

    /* Responsive para Selos */
    @media (max-width: 991px) {
        .badges-container {
            gap: 12px;
        }
        .badge-item {
            max-width: 100px;
        }
        .badge-circle {
            max-width: 80px;
        }
        .badge-title {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 768px) {
        .badges-container {
            gap: 10px;
            justify-content: flex-start;
            padding: 10px 6px;
            scroll-snap-type: x mandatory;
        }
        .badge-item {
            max-width: 90px;
        }
        .badge-circle {
            max-width: 70px;
        }
        .badge-title {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 480px) {
        .badges-container {
            gap: 8px;
        }
        .badge-item {
            max-width: 80px;
        }
        .badge-circle {
            max-width: 60px;
        }
        .badge-title {
            font-size: 0.65rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section com Banner Oficial Ocupando Todo o Espaço (unificado via include) -->
<div class="hero-section no-padding">
    @php
        use App\Helpers\BannerHelper;
        $hasHero = BannerHelper::getBannersForDisplay($department->id ?? null, 'hero', 1)->count() > 0;
    @endphp

    @if($hasHero)
        @include('components.banner-slider', ['departmentId' => $department->id ?? null, 'position' => 'hero', 'limit' => 5])
    @else
        <!-- Banner Padrão quando não há banners -->
        <div class="hero-banner-full">
            <div class="hero-banner-image hero-default-bg">
                <div class="hero-banner-overlay">
                    <div class="container">
                        <div class="row align-items-center hero-content-row">
                            <div class="col-lg-8">
                                <div class="hero-banner-content">
                                    <h1 class="hero-banner-title">Feira das Fábricas</h1>
                                    <p class="hero-banner-subtitle">
                                        O melhor em eletrônicos e tecnologia para sua empresa e para você.
                                    </p>
                                    <div class="hero-banner-actions">
                                        <a href="{{ route('products') }}" class="btn hero-btn-primary">
                                            <i class="fas fa-shopping-bag me-2"></i>
                                            Ver Produtos
                                        </a>
                                        <a href="{{ route('contact') }}" class="btn hero-btn-secondary">
                                            <i class="fas fa-phone me-2"></i>
                                            Contato
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@php
    // Show global sections and sections specific to this department
    $deptId = $department->id ?? null;
    $homepageSections = \App\Models\HomepageSection::where('enabled', true)
        ->where(function($q) use ($deptId) {
            $q->whereNull('department_id');
            if ($deptId) {
                $q->orWhere('department_id', $deptId);
            }
        })
        ->orderBy('position')
        ->get();
@endphp

<!-- Categorias (moved here so categories appear above homepage sections) -->
@if($categories && $categories->count() > 0)
<section class="section-elegant" style="padding: 20px 0;">
    <div class="container">
        <h2 class="section-title" style="font-size: 1.6rem; margin-bottom: 5px;">Nossas Categorias</h2>
        <p class="section-subtitle" style="margin-bottom: 12px;">
            Explore nossa ampla gama de produtos em diferentes categorias
        </p>
        <div class="row g-2">
            @foreach($categories->take(4) as $category)
                <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                    @php
                        $catCoverUrl = null;
                        if (isset($category->cover) && $category->cover) {
                            if (\Illuminate\Support\Str::startsWith($category->cover, ['http://', 'https://'])) {
                                $catCoverUrl = $category->cover;
                            } else {
                                $catCoverUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($category->cover);
                            }
                        }
                        // visibility flags (defaults to true when column absent)
                        $showAvatar = $category->show_avatar ?? true;
                        $showCover = $category->show_cover ?? true;
                        $showTitle = $category->show_title ?? true;
                        $showDescription = $category->show_description ?? true;
                        $showButton = $category->show_button ?? true;
                    @endphp
                    <div class="elegant-card btn-pos-{{ $category->button_position ?? 'bottom' }} @if($catCoverUrl && $showCover) has-cover @endif" @if($catCoverUrl && $showCover) style="background-image: url('{{ $catCoverUrl }}'); background-size: cover; background-position: center;" data-cover-url="{{ $catCoverUrl }}" @else data-cover-url="" @endif data-button-position="{{ $category->button_position ?? 'bottom' }}">
                        <div class="card-icon" @if(!$showAvatar) style="display:none;" @endif>
                            @php
                                $iconClass = $category->icon_class ?? 'fas fa-laptop';
                                $catImageUrl = null;
                                if ($category->image) {
                                    if (\Illuminate\Support\Str::startsWith($category->image, ['http://', 'https://'])) {
                                        $catImageUrl = $category->image;
                                    } else {
                                        $catImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($category->image);
                                    }
                                }
                            @endphp
                            @if($showAvatar && $catImageUrl)
                                <img src="{{ $catImageUrl ?: asset('images/no-image.svg') }}" alt="{{ $category->name }}" class="category-image-display" data-category-id="{{ $category->id }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;cursor:pointer;" @auth('admin') title="Clique para editar imagem" @endauth loading="lazy" decoding="async">
                            @endif
                            @if($showAvatar && !$catImageUrl)
                                <i class="category-icon-display {{ $iconClass }}" data-category-id="{{ $category->id }}" @auth('admin') title="Clique para alterar ícone" style="cursor:pointer;" @endauth></i>
                            @else
                                {{-- If avatar is disabled, do not show icon --}}
                                @if(!$showAvatar)
                                    <i class="category-icon-display {{ $iconClass }}" data-category-id="{{ $category->id }}" hidden></i>
                                @endif
                            @endif
                        </div>
                        @if($showTitle)
                        <h5 class="card-title">
                            @auth('admin')
                                <span class="js-edit-category-title" data-category-id="{{ $category->id }}" data-current-title="{{ $category->name }}" style="cursor: pointer;">{{ $category->name }}</span>
                            @else
                                {{ $category->name }}
                            @endauth
                        </h5>
                        @endif
                        @if($showDescription)
                        <p class="card-text">
                            @auth('admin')
                                <span class="js-edit-category-desc" data-category-id="{{ $category->id }}" data-current-desc="{{ $category->description }}" style="cursor: pointer;">{{ $category->description ?? 'Produtos de alta qualidade para sua empresa.' }}</span>
                            @else
                                {{ $category->description ?? 'Produtos de alta qualidade para sua empresa.' }}
                            @endauth
                        </p>
                        @endif
                        @if($showButton)
                        <a href="{{ route('products') }}?category={{ $category->slug }}" 
                           class="btn btn-outline-primary w-100">
                            Explorar Categoria
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@foreach($homepageSections as $section)
    @include('components.homepage_section_products', ['section' => $section])
@endforeach

<!-- Selos de Marcas (Department Badges) -->
@if($departmentBadges && $departmentBadges->count() > 0)
<section class="section-elegant" style="padding: 30px 0; background: var(--elegant-white);">
    <div class="container">
    <div class="badges-wrapper">
            <div class="badges-container">
                @foreach($departmentBadges as $badge)
                    <div class="badge-item text-center" data-badge-item>
                            @if($badge->link)
                                <a href="{{ $badge->link }}" class="badge-link" title="{{ $badge->title }}">
                            @else
                                <div class="badge-link" title="{{ $badge->title }}">
                            @endif
                                <div class="badge-circle">
                                    <img src="{{ $badge->image_url }}" 
                                         alt="{{ $badge->title }}" 
                                         class="badge-image @auth('admin') js-change-badge-image @endauth"
                                         @auth('admin') data-badge-id="{{ $badge->id }}" @endauth
                                         onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                </div>
                                <p class="badge-title">
                                    @auth('admin')
                                        <span class="js-rename-badge" data-badge-id="{{ $badge->id }}" data-current-title="{{ $badge->title }}" style="cursor: pointer;">{{ $badge->title }}</span>
                                    @else
                                        {{ $badge->title }}
                                    @endauth
                                </p>
                            @if($badge->link)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@auth('admin')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Delegated click: rename badge title
    document.addEventListener('click', function(e){
        const target = e.target.closest('.js-rename-badge');
        if (!target) return;
        e.preventDefault();
        const id = target.getAttribute('data-badge-id');
        const current = target.getAttribute('data-current-title') || target.textContent.trim();
        const title = prompt('Novo título do selo:', current);
        if (!title || title.trim().length === 0) return;
        fetch(`/admin/department-badges/${id}/update-title`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ title: title.trim() })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Erro ao renomear selo');
            target.textContent = data.badge.title;
            target.setAttribute('data-current-title', data.badge.title);
        })
        .catch(err => alert(err.message));
    });

    // Delegated click: change badge image
    document.addEventListener('click', function(e){
        const img = e.target.closest('.js-change-badge-image');
        if (!img) return;
        e.preventDefault();
        const id = img.getAttribute('data-badge-id');
        // Create file input on the fly to keep DOM light
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        document.body.appendChild(fileInput);
        fileInput.addEventListener('change', function(){
            const file = fileInput.files && fileInput.files[0];
            if (!file) { document.body.removeChild(fileInput); return; }
            const fd = new FormData();
            fd.append('image', file);
            fetch(`/admin/department-badges/${id}/update-image`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Erro ao atualizar imagem do selo');
                img.src = data.badge.image_url;
            })
            .catch(err => alert(err.message))
            .finally(() => { document.body.removeChild(fileInput); });
        });
        fileInput.click();
    });
});
</script>
@endpush
@endauth

<!-- Categorias -->
@if($categories && $categories->count() > 0)
<section class="section-elegant" style="padding: 20px 0;">
    <div class="container">
        <h2 class="section-title" style="font-size: 1.6rem; margin-bottom: 5px;">Nossas Categorias</h2>
        <p class="section-subtitle" style="margin-bottom: 12px;">
            Explore nossa ampla gama de produtos em diferentes categorias
        </p>
        <div class="row g-2">
            @foreach($categories->take(4) as $category)
                <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                    @php
                        $catCoverUrl = null;
                        if (isset($category->cover) && $category->cover) {
                            if (\Illuminate\Support\Str::startsWith($category->cover, ['http://', 'https://'])) {
                                $catCoverUrl = $category->cover;
                            } else {
                                $catCoverUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($category->cover);
                            }
                        }
                        // visibility flags (defaults to true when column absent)
                        $showAvatar = $category->show_avatar ?? true;
                        $showCover = $category->show_cover ?? true;
                        $showTitle = $category->show_title ?? true;
                        $showDescription = $category->show_description ?? true;
                        $showButton = $category->show_button ?? true;
                    @endphp
                    <div class="elegant-card btn-pos-{{ $category->button_position ?? 'bottom' }} @if($catCoverUrl && $showCover) has-cover @endif" @if($catCoverUrl && $showCover) style="background-image: url('{{ $catCoverUrl }}'); background-size: cover; background-position: center;" data-cover-url="{{ $catCoverUrl }}" @else data-cover-url="" @endif data-button-position="{{ $category->button_position ?? 'bottom' }}">
                        <div class="card-icon" @if(!$showAvatar) style="display:none;" @endif>
                            @php
                                $iconClass = $category->icon_class ?? 'fas fa-laptop';
                                $catImageUrl = null;
                                if ($category->image) {
                                    if (\Illuminate\Support\Str::startsWith($category->image, ['http://', 'https://'])) {
                                        $catImageUrl = $category->image;
                                    } else {
                                        $catImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($category->image);
                                    }
                                }
                            @endphp
                            @if($showAvatar && $catImageUrl)
                                <img src="{{ $catImageUrl ?: asset('images/no-image.svg') }}" alt="{{ $category->name }}" class="category-image-display" data-category-id="{{ $category->id }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;cursor:pointer;" @auth('admin') title="Clique para editar imagem" @endauth loading="lazy" decoding="async">
                            @endif
                            @if($showAvatar && !$catImageUrl)
                                <i class="category-icon-display {{ $iconClass }}" data-category-id="{{ $category->id }}" @auth('admin') title="Clique para alterar ícone" style="cursor:pointer;" @endauth></i>
                            @else
                                {{-- If avatar is disabled, do not show icon --}}
                                @if(!$showAvatar)
                                    <i class="category-icon-display {{ $iconClass }}" data-category-id="{{ $category->id }}" hidden></i>
                                @endif
                            @endif
                        </div>
                        @if($showTitle)
                        <h5 class="card-title">
                            @auth('admin')
                                <span class="js-edit-category-title" data-category-id="{{ $category->id }}" data-current-title="{{ $category->name }}" style="cursor: pointer;">{{ $category->name }}</span>
                            @else
                                {{ $category->name }}
                            @endauth
                        </h5>
                        @endif
                        @if($showDescription)
                        <p class="card-text">
                            @auth('admin')
                                <span class="js-edit-category-desc" data-category-id="{{ $category->id }}" data-current-desc="{{ $category->description }}" style="cursor: pointer;">{{ $category->description ?? 'Produtos de alta qualidade para sua empresa.' }}</span>
                            @else
                                {{ $category->description ?? 'Produtos de alta qualidade para sua empresa.' }}
                            @endauth
                        </p>
                        @endif
                        @if($showButton)
                        <a href="{{ route('products') }}?category={{ $category->slug }}" 
                           class="btn btn-outline-primary w-100">
                            Explorar Categoria
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Produtos em Destaque -->
@if($featuredProducts && $featuredProducts->count() > 0)
<section class="section-gray">
    <div class="container">
        <h2 class="section-title">Produtos em Destaque</h2>
        <p class="section-subtitle">
            Seleção especial de produtos premium para seu negócio
        </p>
        <!-- Carrossel de Produtos -->
        <div id="productsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $products = $featuredProducts->take(12);
                    $chunks = $products->chunk(4);
                @endphp
                @foreach($chunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 col-6 mb-2">
                                    <div class="product-card" @auth('admin') data-product-id="{{ $product->id }}" @endauth>
                                        <div class="product-image" @auth('admin') title="Trocar imagem (upload ou link)" style="cursor: pointer;" @endauth>
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     class="@auth('admin') js-change-image @endauth"
                                                     @auth('admin') data-product-id="{{ $product->id }}" @endauth
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}"
                                                     class="@auth('admin') js-change-image @endauth"
                                                     @auth('admin') data-product-id="{{ $product->id }}" @endauth>
                                            @endif
                                            @if($product->is_featured)
                                                <div class="product-badge">Destaque</div>
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <h6 class="product-title">{{ $product->name }}</h6>
                                            <div class="product-price">
                                                R$ {{ number_format($product->price, 2, ',', '.') }}
                                                @if($product->b2b_price)
                                                    <small class="text-muted d-block">B2B: R$ {{ number_format($product->b2b_price, 2, ',', '.') }}</small>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('product', $product->slug) }}?department={{ $department->slug }}" class="product-btn" style="flex: 1;">
                                                    <i class="fas fa-shopping-cart me-2"></i>
                                                    Ver Detalhes
                                                </a>
                                                @auth('admin')
                                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="Editar Produto"
                                                       style="flex-shrink: 0; min-width: 44px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Controles do Carrossel -->
            @if($chunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#productsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
                
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach($chunks as $index => $chunk)
                        <button type="button" data-bs-target="#productsCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos -->
<section class="section-elegant" style="padding: 20px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products') }}" class="btn elegant-btn">
                Ver Todos os Produtos
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Seção Produtos Apple -->
@if($appleProducts->count() > 0)
<section class="section-elegant" data-brand-section="apple">
    <div class="container">
        <h2 class="section-title js-section-title">Produtos Apple</h2>
        <p class="section-subtitle">
            A melhor tecnologia Apple para seu negócio
        </p>
        
        <!-- Carrossel de Produtos Apple -->
        <div id="appleCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $appleChunks = $appleProducts->chunk(4);
                @endphp
                @foreach($appleChunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 col-6 mb-2">
                                    <div class="product-card apple-card">
                                        <div class="product-image" @auth('admin') title="Trocar imagem (upload ou link)" style="cursor: pointer;" @endauth>
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     class="@auth('admin') js-change-image @endauth" @auth('admin') data-product-id="{{ $product->id }}" @endauth
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}" class="@auth('admin') js-change-image @endauth" @auth('admin') data-product-id="{{ $product->id }}" @endauth>
                                            @endif
                                            <div class="product-badge apple-badge">Apple</div>
                                        </div>
                                        <div class="product-info">
                                            <h6 class="product-title">{{ $product->name }}</h6>
                                            <div class="product-price">
                                                R$ {{ number_format($product->price, 2, ',', '.') }}
                                                @if($product->b2b_price)
                                                    <small class="text-muted d-block">B2B: R$ {{ number_format($product->b2b_price, 2, ',', '.') }}</small>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('product', $product->slug) }}?department={{ $department->slug }}" class="product-btn" style="flex: 1;">
                                                    <i class="fas fa-shopping-cart me-2"></i>
                                                    Ver Detalhes
                                                </a>
                                                @auth('admin')
                                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                                       class="btn btn-outline-primary btn-sm" 
                                                       title="Editar Produto"
                                                       style="flex-shrink: 0; min-width: 44px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Controles do Carrossel Apple -->
            @if($appleChunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#appleCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#appleCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
                
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach($appleChunks as $index => $chunk)
                        <button type="button" data-bs-target="#appleCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos Apple -->
<section class="section-elegant" style="padding: 20px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products', ['brand' => 'Apple']) }}" class="btn elegant-btn">
                Ver Todos os Produtos Apple
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Seção Produtos Samsung -->
@if(false)
    {{-- Samsung brand section removed per request. --}}
@endif

<!-- Seção Produtos Xiaomi -->
@if(false)
    {{-- Xiaomi brand section removed per request. --}}
@endif

<!-- Seção Produtos Motorola -->
@if(false)
    {{-- Motorola brand section removed per request. --}}
@endif

@endif

<!-- Infinix section removed per request -->

<!-- Seção Produtos JBL -->
@if(false)
    {{-- JBL brand section removed per request. --}}
@endif

<!-- Seção Produtos Oppo (removed) -->
@if(false)
    {{-- Oppo brand section removed per request. --}}
@endif

<!-- Seção Produtos Realme (removed) -->
@if(false)
    {{-- Realme brand section removed per request. --}}
@endif

<!-- Seção Produtos Tecno (removed) -->
@if(false)
    {{-- Tecno brand section removed per request. --}}
@endif

<!-- B2B Section -->
<section class="section-elegant">
    <div class="container">
        <div class="b2b-section">
            <h2 class="b2b-title">Conta B2B</h2>
            <p class="b2b-description">
                Condições especiais para empresas. Preços diferenciados e atendimento prioritário.
            </p>
            <a href="{{ route('register.b2b') }}" class="btn b2b-btn">
                <i class="fas fa-building me-2"></i>
                Criar Conta B2B
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Expor configuração inicial de seções para o painel global (smart-search)
window.DepartmentSectionsConfig = (function(){
    try {
        const raw = @json(setting('dept_eletronicos_sections'));
        if (Array.isArray(raw)) return raw;
        if (typeof raw === 'string' && raw && raw.trim().length) {
            try { return JSON.parse(raw); } catch(e) { /* ignore */ }
        }
    } catch(e) {}
    return [
        { brand: 'Apple', title: 'Produtos Apple', enabled: true },
        { brand: 'Samsung', title: 'Produtos Samsung', enabled: true },
        { brand: 'Xiaomi', title: 'Produtos Xiaomi', enabled: true },
        { brand: 'Motorola', title: 'Produtos Motorola', enabled: true },
        { brand: 'Infinix', title: 'Produtos Infinix', enabled: true },
        { brand: 'JBL', title: 'Produtos JBL', enabled: true },
        { brand: 'Oppo', title: 'Produtos Oppo', enabled: true },
        { brand: 'Realme', title: 'Produtos Realme', enabled: true },
        { brand: 'Tecno', title: 'Produtos Tecno', enabled: true }
    ];
})();
// Informar o slug atual do departamento (para filtragem de marcas)
window.CurrentDepartmentSlug = 'eletronicos';
</script>
@endpush
@auth('admin')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Rename category inline
    document.addEventListener('click', function(e){
        const el = e.target.closest('.js-edit-category-title');
        if (!el) return;
        e.preventDefault();
        const id = el.getAttribute('data-category-id');
        const current = el.getAttribute('data-current-title') || el.textContent.trim();
        const name = prompt('Novo nome da categoria:', current);
        if (!name || name.trim().length === 0) return;
        fetch(`/admin/categories/${id}/quick-update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ name: name.trim() })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Erro ao atualizar categoria');
            el.textContent = data.category.name;
            el.setAttribute('data-current-title', data.category.name);
        })
        .catch(err => alert(err.message || 'Erro'));
    });

    // Edit description inline
    document.addEventListener('click', function(e){
        const el = e.target.closest('.js-edit-category-desc');
        if (!el) return;
        e.preventDefault();
        const id = el.getAttribute('data-category-id');
        const current = el.getAttribute('data-current-desc') || el.textContent.trim();
        const desc = prompt('Nova descrição da categoria (deixe vazio para remover):', current);
        if (desc === null) return;
        fetch(`/admin/categories/${id}/quick-update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ description: desc })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error(data.message || 'Erro ao atualizar descrição');
            el.textContent = data.category.description || 'Produtos de alta qualidade para sua empresa.';
            el.setAttribute('data-current-desc', data.category.description || '');
        })
        .catch(err => alert(err.message || 'Erro'));
    });

    // Icon editing now handled via modal (opens the same modal as image)
});
</script>
@endpush
@endauth

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrappers = document.querySelectorAll('[data-badge-loop]');

        wrappers.forEach(function (wrapper) {
            const scroller = wrapper.querySelector('.badges-container');
            if (!scroller) {
                return;
            }

            const items = Array.from(scroller.querySelectorAll('[data-badge-item]'));
            if (items.length < 2) {
                return;
            }

            const originalWidth = scroller.scrollWidth;

            items.forEach(function (item) {
                const clone = item.cloneNode(true);
                clone.setAttribute('data-badge-clone', 'true');
                scroller.appendChild(clone);
            });

            let isAdjusting = false;

            scroller.addEventListener('scroll', function () {
                if (isAdjusting) {
                    return;
                }

            if (scroller.scrollLeft >= originalWidth) {
                    isAdjusting = true;
                const previousBehavior = scroller.style.scrollBehavior;
                scroller.style.scrollBehavior = 'auto';
                scroller.scrollLeft -= originalWidth;
                requestAnimationFrame(function () {
                    scroller.style.scrollBehavior = previousBehavior;
                    isAdjusting = false;
                });
                } else if (scroller.scrollLeft <= 0) {
                    isAdjusting = true;
                const previousBehavior = scroller.style.scrollBehavior;
                scroller.style.scrollBehavior = 'auto';
                scroller.scrollLeft += originalWidth;
                requestAnimationFrame(function () {
                    scroller.style.scrollBehavior = previousBehavior;
                    isAdjusting = false;
                });
                }
            });

        scroller.style.scrollBehavior = 'auto';
        scroller.scrollLeft = 1;
        requestAnimationFrame(function () {
            scroller.style.scrollBehavior = '';
        });
        });
    });
</script>
@endpush
@auth('admin')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Build a Bootstrap modal once and reuse
        const modalHtml = `
        <div class="modal fade" id="quickImageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Trocar imagem do produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Enviar arquivo</label>
                            <input type="file" id="qiImageFile" accept="image/*" class="form-control" />
                        </div>
                        <div class="text-center text-muted">ou</div>
                        <div class="mt-3">
                            <label class="form-label">Usar link (URL)</label>
                            <input type="url" id="qiImageUrl" placeholder="https://exemplo.com/imagem.jpg" class="form-control" />
                        </div>
                        <input type="hidden" id="qiProductId" />
                        <small class="text-muted d-block mt-2">Formatos: jpeg, png, jpg, gif, webp, avif. Máx 10MB.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" id="qiImageRemove">Remover imagem</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="qiImageSave">Salvar</button>
                    </div>
                </div>
            </div>
        </div>`;
        if (!document.getElementById('quickImageModal')) {
                document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
        const qiModalEl = document.getElementById('quickImageModal');
        const qiModal = new bootstrap.Modal(qiModalEl);
        const qiFile = document.getElementById('qiImageFile');
        const qiUrl = document.getElementById('qiImageUrl');
        const qiProductId = document.getElementById('qiProductId');
        let qiTargetImg = null;

        function openQuick(productId, imgEl){
                qiProductId.value = productId;
                qiFile.value = '';
                qiUrl.value = '';
                qiTargetImg = imgEl;
                qiModal.show();
        }

        // Delegate click on product card image
        document.addEventListener('click', function(e){
                const img = e.target.closest('.js-quick-change-image');
                if (!img) return;
                const card = img.closest('[data-product-id]');
                if (!card) return;
                e.preventDefault();
                e.stopPropagation();
                openQuick(card.getAttribute('data-product-id'), img);
        }, true);

        document.getElementById('qiImageSave').addEventListener('click', function(){
                const id = qiProductId.value;
                const file = qiFile.files && qiFile.files[0];
                const url = qiUrl.value.trim();
                if (!id) return;
                if (file) {
                        const fd = new FormData();
                        fd.append('featured_image', file);
                        fetch(`/admin/products/${id}/update-images`, { method:'POST', headers:{ 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' }, body: fd })
                            .then(r=>r.json()).then(data=>applyResult(id, data)).catch(err=>alert(err.message));
                } else if (url) {
                        fetch(`/admin/products/${id}/update-images`, { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' }, body: JSON.stringify({ featured_image_url: url }) })
                            .then(r=>r.json()).then(data=>applyResult(id, data)).catch(err=>alert(err.message));
                } else {
                        alert('Envie um arquivo ou informe uma URL.');
                }
        });

        document.getElementById('qiImageRemove').addEventListener('click', function(){
                const id = qiProductId.value;
                if (!id) return;
                const fd = new FormData();
                fd.append('remove_featured_image', '1');
                fetch(`/admin/products/${id}/update-images`, { method:'POST', headers:{ 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' }, body: fd })
                    .then(r=>r.json()).then(data=>applyResult(id, data)).catch(err=>alert(err.message));
        });

        function applyResult(id, data){
                if (!data.success) { alert(data.message || 'Erro ao atualizar imagem'); return; }
                if (qiTargetImg && Array.isArray(data.images) && data.images.length) {
                        qiTargetImg.src = data.images[0];
                }
                qiModal.hide();
        }
});
</script>
@endpush
@endauth
@auth('admin')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Build modal for categories (upload / URL / copy / remove)
    const catModalHtml = `
    <div class="modal fade" id="quickCategoryImageModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Imagem da Categoria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
                    <div class="modal-body">
                        <div class="text-center mb-2">
                                <div id="qcCoverPreview" style="width:100%;height:120px;background-size:cover;background-position:center;border-radius:8px;border:1px solid #e9ecef;overflow:hidden;"></div>
                                <div class="mt-2"><img id="qcPreview" src="" style="width:64px;height:64px;object-fit:cover;border-radius:8px;" onerror="this.src='{{ asset('images/no-image.svg') }}'"></div>
                        </div>

                        <!-- Simple visibility toggles -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Opções de exibição</label>
                            <div class="d-flex flex-column small">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="qcShowAvatar" checked>
                                    <span class="form-check-label">Mostrar avatar</span>
                                </label>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="qcShowCover" checked>
                                    <span class="form-check-label">Mostrar capa</span>
                                </label>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="qcShowTitle" checked>
                                    <span class="form-check-label">Mostrar título</span>
                                </label>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="qcShowDescription" checked>
                                    <span class="form-check-label">Mostrar descrição</span>
                                </label>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="qcShowButton" checked>
                                    <span class="form-check-label">Mostrar botão</span>
                                </label>
                                <div class="mt-2">
                                    <label class="form-label small mb-1">Posição do botão</label>
                                    <select id="qcButtonPosition" class="form-select form-select-sm">
                                        <option value="top">Cima</option>
                                        <option value="center">Meio</option>
                                        <option value="bottom" selected>Baixo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Colar URL da imagem (avatar)</label>
                            <input type="url" id="qcUrl" placeholder="https://..." class="form-control" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Colar URL da imagem de fundo (capa)</label>
                            <input type="url" id="qcCoverUrl" placeholder="https://..." class="form-control" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Classe do ícone (ex: fas fa-laptop)</label>
                            <input type="text" id="qcIconClass" placeholder="fas fa-laptop" class="form-control" />
                        </div>
                        <input type="hidden" id="qcCategoryId" />
          </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" id="qcCopyUrl">Copiar URL</button>
                        <button type="button" class="btn btn-danger" id="qcRemove">Remover Avatar</button>
                        <button type="button" class="btn btn-warning" id="qcRemoveCover">Remover Fundo</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="qcSave">Salvar</button>
                    </div>
        </div>
      </div>
    </div>`;

    if (!document.getElementById('quickCategoryImageModal')) {
        document.body.insertAdjacentHTML('beforeend', catModalHtml);
    }

    const qcModalEl = document.getElementById('quickCategoryImageModal');
    const qcModal = new bootstrap.Modal(qcModalEl);
    const qcUrl = document.getElementById('qcUrl');
    const qcCoverUrl = document.getElementById('qcCoverUrl');
    const qcIconClass = document.getElementById('qcIconClass');
    const qcPreview = document.getElementById('qcPreview');
    const qcCategoryId = document.getElementById('qcCategoryId');
    // New toggle controls (visibility options)
    const qcShowAvatar = document.getElementById('qcShowAvatar');
    const qcShowCover = document.getElementById('qcShowCover');
    const qcShowTitle = document.getElementById('qcShowTitle');
    const qcShowDescription = document.getElementById('qcShowDescription');
    const qcShowButton = document.getElementById('qcShowButton');
    const qcButtonPosition = document.getElementById('qcButtonPosition');

    function prefillVisibilityTogglesFromCard(card){
        if (!card) return;
        // avatar present?
        const avatar = card.querySelector('.category-image-display');
        if (qcShowAvatar) qcShowAvatar.checked = !!avatar;
        // cover present?
        const cover = card.getAttribute('data-cover-url');
        if (qcShowCover) qcShowCover.checked = !!(cover && cover.length);
        // title: consider element absence or hidden via style
        const titleEl = card.querySelector('.card-title');
        if (qcShowTitle) qcShowTitle.checked = titleEl ? (getComputedStyle(titleEl).display !== 'none') : false;
        // description: consider element absence or hidden via style
        const descEl = card.querySelector('.card-text');
        if (qcShowDescription) qcShowDescription.checked = descEl ? (getComputedStyle(descEl).display !== 'none') : false;
        // button (search for primary button inside card)
        const btnEl = card.querySelector('.btn') || card.querySelector('a.btn');
        if (qcShowButton) qcShowButton.checked = !!(btnEl && getComputedStyle(btnEl).display !== 'none');
        // button position (read data attribute or class)
        if (qcButtonPosition) {
            const pos = card.getAttribute('data-button-position') || Array.from(card.classList).find(c=>c.indexOf('btn-pos-')===0)?.replace('btn-pos-','') || 'bottom';
            qcButtonPosition.value = pos;
        }
    }

    // Update preview when typing/pasting a URL
    qcUrl.addEventListener('input', function(){
        const val = qcUrl.value.trim();
        if (!val) return;
        // quick sanity: only update if starts with http
        if (/^https?:\/\//i.test(val)) {
            qcPreview.src = val;
        }
    });

    // Update cover preview when typing/pasting a cover URL
    const qcCoverPreview = document.getElementById('qcCoverPreview');
    qcCoverUrl.addEventListener('input', function(){
        const val = qcCoverUrl.value.trim();
        if (!val) { qcCoverPreview.style.backgroundImage = ''; return; }
        if (/^https?:\/\//i.test(val)) {
            qcCoverPreview.style.backgroundImage = `url('${val}')`;
        }
    });

    // Open modal when clicking the category IMAGE only (icon click edits icon class)
    document.addEventListener('click', function(e){
        const el = e.target.closest('.category-image-display');
        if (!el) return;
        e.preventDefault();
        const id = el.getAttribute('data-category-id');
        qcCategoryId.value = id;
        qcUrl.value = '';
        qcIconClass.value = '';
        qcCoverUrl.value = '';
        // find current image in card
        const card = el.closest('.elegant-card');
        let imgSrc = '';
        if (card) {
            const img = card.querySelector('.category-image-display');
            if (img) imgSrc = img.src;
        }
        qcPreview.src = imgSrc || '{{ asset('images/no-image.svg') }}';
        // set cover preview from card if present
        const cardForCover = el.closest('.elegant-card');
        const existingCover = cardForCover ? cardForCover.getAttribute('data-cover-url') : '';
        if (existingCover) qcCoverPreview.style.backgroundImage = `url('${existingCover}')`; else qcCoverPreview.style.backgroundImage = '';
        // prefill toggles
        prefillVisibilityTogglesFromCard(cardForCover || el.closest('.elegant-card'));
        qcModal.show();
    });

    // Open modal when clicking the icon as well (so admin uses a single modal for icon/image)
    document.addEventListener('click', function(e){
        const el = e.target.closest('.category-icon-display');
        if (!el) return;
        e.preventDefault();
        const id = el.getAttribute('data-category-id');
        qcCategoryId.value = id;
        qcUrl.value = '';
        qcCoverUrl.value = '';
        // prefill icon class from element's classes (exclude 'category-icon-display')
        const classes = Array.from(el.classList).filter(c => c !== 'category-icon-display').join(' ');
        qcIconClass.value = classes;
        // preview a placeholder when editing icon
        qcPreview.src = '{{ asset('images/no-image.svg') }}';
        qcCoverPreview.style.backgroundImage = '';
        // try prefilling toggles from card if available
        const cardFromIcon = el.closest('.elegant-card');
        prefillVisibilityTogglesFromCard(cardFromIcon);
        qcModal.show();
    });

    // Open modal when clicking anywhere on the category card (except links/buttons)
    document.addEventListener('click', function(e){
        const card = e.target.closest('.elegant-card');
        if (!card) return;
        // ignore clicks on links or buttons inside the card (like "Explorar Categoria")
        if (e.target.closest('a') || e.target.closest('button')) return;
        // find an element with data-category-id inside the card
        const inner = card.querySelector('[data-category-id]');
        if (!inner) return;
        e.preventDefault();
        const id = inner.getAttribute('data-category-id');
        qcCategoryId.value = id;
        qcUrl.value = '';
        qcIconClass.value = '';
        // prefill cover url if present on the card
        qcCoverUrl.value = card.getAttribute('data-cover-url') || '';
        // use existing avatar image if present
        const img = card.querySelector('.category-image-display');
        qcPreview.src = (img && img.src) ? img.src : '{{ asset('images/no-image.svg') }}';
        // also set cover preview
        const cardCover = card.getAttribute('data-cover-url') || '';
        qcCoverPreview.style.backgroundImage = cardCover ? `url('${cardCover}')` : '';
        // prefill toggles
        prefillVisibilityTogglesFromCard(card);
        qcModal.show();
    });

    // Copy URL (prefer explicit URL field, fallback to preview src)
    document.getElementById('qcCopyUrl').addEventListener('click', function(){
        const toCopy = qcUrl.value.trim() || qcPreview.src || '';
        if (!toCopy) return alert('Nenhuma URL para copiar');
        navigator.clipboard.writeText(toCopy).then(()=>{
            alert('URL copiada para a área de transferência');
        }).catch(()=>alert('Não foi possível copiar'));
    });

    // Remove image
    document.getElementById('qcRemove').addEventListener('click', function(){
        const id = qcCategoryId.value;
        if (!id) return;
        if (!confirm('Remover imagem da categoria?')) return;
        fetch(`/admin/categories/${id}/remove-image`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(r=>r.json())
                .then(data=>{
            if (!data.success) throw new Error(data.message||'Erro');
            // update card UI: remove image element, show/hide icon and wrapper based on toggle
            const imgEl = document.querySelector('.elegant-card .category-image-display[data-category-id="'+id+'"]') || document.querySelector('.category-image-display[data-category-id="'+id+'"]');
            if (imgEl) imgEl.remove();
            const icon = document.querySelector('.category-icon-display[data-category-id="'+id+'"]');
            const wrapper = icon ? icon.closest('.card-icon') : document.querySelector('.elegant-card [data-category-id="'+id+'"]')?.closest('.elegant-card')?.querySelector('.card-icon');
            // show or hide icon/wrapper depending on the current modal toggle (if toggle exists)
            const shouldShowIcon = (qcShowAvatar ? qcShowAvatar.checked : true);
            if (icon) {
                if (shouldShowIcon) icon.removeAttribute('hidden'); else icon.setAttribute('hidden', '');
            }
            if (wrapper) {
                wrapper.style.display = shouldShowIcon ? '' : 'none';
            }
            qcPreview.src = data.category.image_url || '{{ asset('images/no-image.svg') }}';
        })
        .catch(err=>alert(err.message||'Erro'));
    });

    // Remove cover (background)
    document.getElementById('qcRemoveCover').addEventListener('click', function(){
        const id = qcCategoryId.value;
        if (!id) return;
        if (!confirm('Remover imagem de fundo (capa) da categoria?')) return;
        fetch(`/admin/categories/${id}/remove-cover`, {
            method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(r=>r.json())
        .then(data=>{
            if (!data.success) throw new Error(data.message||'Erro');
            // find the specific card by category id
            const specific = Array.from(document.querySelectorAll('.elegant-card')).find(c=>{
                const img = c.querySelector('[data-category-id]');
                return img && img.getAttribute('data-category-id') == id;
            });
            if (specific) {
                specific.style.backgroundImage = '';
                specific.removeAttribute('data-cover-url');
            }
            qcCoverUrl.value = '';
            if (typeof qcCoverPreview !== 'undefined' && qcCoverPreview) qcCoverPreview.style.backgroundImage = '';
        })
        .catch(err=>alert(err.message||'Erro'));
    });

    // Save (use URL or update icon class)
    document.getElementById('qcSave').addEventListener('click', function(){
        const id = qcCategoryId.value;
        if (!id) return;
        const url = qcUrl.value.trim();
        const cover = qcCoverUrl.value.trim();
        const iconClass = (qcIconClass && qcIconClass.value) ? qcIconClass.value.trim() : '';
        // visibility flags from modal toggles
        const showAvatar = (qcShowAvatar ? qcShowAvatar.checked : true);
        const showCover = (qcShowCover ? qcShowCover.checked : true);
        const showTitle = (qcShowTitle ? qcShowTitle.checked : true);
        const showDescription = (qcShowDescription ? qcShowDescription.checked : true);
        const showButton = (qcShowButton ? qcShowButton.checked : true);

        function doIconUpdate(){
            return fetch(`/admin/categories/${id}/quick-update`, {
                method: 'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
                body: JSON.stringify({ icon_class: iconClass || null })
            }).then(r=>r.json());
        }

        const tasks = [];

        if (url) {
            const t = fetch(`/admin/categories/${id}/update-image`, {
                method: 'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' }, body: JSON.stringify({ image_url: url })
            }).then(r=>r.json()).then(data=>{
                if (!data.success) throw new Error(data.message||'Erro');
                const card = document.querySelector('.elegant-card [data-category-id="'+id+'"]')?.closest('.elegant-card');
                if (card) {
                    const existing = card.querySelector('.category-image-display');
                    if (existing) existing.src = data.category.image_url;
                    else {
                        const img = document.createElement('img');
                        img.src = data.category.image_url;
                        img.alt = '';
                        img.className = 'category-image-display';
                        img.setAttribute('data-category-id', id);
                        img.style.width = '40px'; img.style.height='40px'; img.style.borderRadius='50%'; img.style.objectFit='cover'; img.style.cursor='pointer';
                        const wrapper = card.querySelector('.card-icon');
                        if (wrapper) {
                            const icon = wrapper.querySelector('.category-icon-display');
                            if (icon) icon.setAttribute('hidden','');
                            wrapper.insertAdjacentElement('afterbegin', img);
                        }
                    }
                }
                qcPreview.src = data.category.image_url;
                return data;
            });
            tasks.push(t);
        }

        if (cover) {
            const t = fetch(`/admin/categories/${id}/update-cover`, {
                method: 'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' }, body: JSON.stringify({ cover_url: cover })
            }).then(r=>r.json()).then(data=>{
                if (!data.success) throw new Error(data.message||'Erro');
                const card = Array.from(document.querySelectorAll('.elegant-card')).find(c=>{
                    const img = c.querySelector('[data-category-id]');
                    return img && img.getAttribute('data-category-id') == id;
                });
                const coverUrl = (data && data.cover_url) ? data.cover_url : (data && data.category && data.category.cover_url ? data.category.cover_url : null);
                if (card && coverUrl) {
                    card.style.backgroundImage = `url('${coverUrl}')`;
                    card.setAttribute('data-cover-url', coverUrl);
                }
                if (typeof qcCoverPreview !== 'undefined' && qcCoverPreview) qcCoverPreview.style.backgroundImage = coverUrl ? `url('${coverUrl}')` : '';
                return data;
            });
            tasks.push(t);
        }

        if (iconClass) {
            tasks.push(doIconUpdate().then(iconResp=>{
                if (!iconResp.success) throw new Error(iconResp.message||'Erro ao atualizar ícone');
                const iconEl = document.querySelector('.category-icon-display[data-category-id="'+id+'"]');
                const card = document.querySelector('.elegant-card [data-category-id="'+id+'"]')?.closest('.elegant-card');
                if (iconEl) {
                    iconEl.className = 'category-icon-display ' + (iconResp.category.icon_class || 'fas fa-laptop');
                    if (card && card.querySelector('.category-image-display')) iconEl.setAttribute('hidden','');
                }
                return iconResp;
            }));
        }

        // Visibility update: call quick-update to persist visibility flags and update DOM immediately
        function doVisibilityUpdate(){
            return fetch(`/admin/categories/${id}/quick-update`, {
                method: 'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
                body: JSON.stringify({
                        show_avatar: !!showAvatar,
                        show_cover: !!showCover,
                        show_title: !!showTitle,
                        show_description: !!showDescription,
                        show_button: !!showButton,
                        button_position: (qcButtonPosition ? qcButtonPosition.value : 'bottom')
                    })
                }).then(r=>r.json()).then(resp=>{
                    if (!resp.success) console.warn('quick-update did not return success for visibility flags:', resp && resp.message);
                    // prefer server-provided flags when available
                    const flags = (resp && resp.category) ? {
                        show_avatar: typeof resp.category.show_avatar !== 'undefined' ? !!resp.category.show_avatar : !!showAvatar,
                        show_cover: typeof resp.category.show_cover !== 'undefined' ? !!resp.category.show_cover : !!showCover,
                        show_title: typeof resp.category.show_title !== 'undefined' ? !!resp.category.show_title : !!showTitle,
                        show_description: typeof resp.category.show_description !== 'undefined' ? !!resp.category.show_description : !!showDescription,
                        show_button: typeof resp.category.show_button !== 'undefined' ? !!resp.category.show_button : !!showButton,
                    } : {
                        show_avatar: !!showAvatar,
                        show_cover: !!showCover,
                        show_title: !!showTitle,
                        show_description: !!showDescription,
                        show_button: !!showButton,
                    };

                    // update DOM according to flags
                    const card = Array.from(document.querySelectorAll('.elegant-card')).find(c=>{
                        const img = c.querySelector('[data-category-id]');
                        return img && img.getAttribute('data-category-id') == id;
                    });
                    if (!card) return resp;

                    // avatar/icon handling
                    const avatarEl = card.querySelector('.category-image-display');
                    const iconEl = card.querySelector('.category-icon-display');
                    const wrapperEl = card.querySelector('.card-icon');
                    if (!flags.show_avatar) {
                        if (avatarEl) avatarEl.remove();
                        if (iconEl) iconEl.setAttribute('hidden', '');
                        if (wrapperEl) wrapperEl.style.display = 'none';
                    } else {
                        if (wrapperEl) wrapperEl.style.display = '';
                        if (!avatarEl && qcPreview && qcPreview.src) {
                            const img = document.createElement('img');
                            img.src = qcPreview.src;
                            img.alt = '';
                            img.className = 'category-image-display';
                            img.setAttribute('data-category-id', id);
                            img.style.width = '40px'; img.style.height='40px'; img.style.borderRadius='50%'; img.style.objectFit='cover'; img.style.cursor='pointer';
                            const wrapper = card.querySelector('.card-icon');
                            if (wrapper) {
                                if (iconEl) iconEl.setAttribute('hidden','');
                                wrapper.insertAdjacentElement('afterbegin', img);
                            }
                        }
                    }

                    // cover
                    if (!flags.show_cover) {
                        card.style.backgroundImage = '';
                        card.removeAttribute('data-cover-url');
                    } else {
                        const coverUrl = card.getAttribute('data-cover-url') || '';
                        if (coverUrl) card.style.backgroundImage = `url('${coverUrl}')`;
                    }

                    // title
                    const titleEl = card.querySelector('.card-title');
                    if (titleEl) titleEl.style.display = flags.show_title ? '' : 'none';
                    // description
                    const descEl = card.querySelector('.card-text');
                    if (descEl) descEl.style.display = flags.show_description ? '' : 'none';
                    // button
                    const btnEl = card.querySelector('a.btn') || card.querySelector('.btn');
                    if (btnEl) btnEl.style.display = flags.show_button ? '' : 'none';

                    // button position: update card class and data attribute when server returns position
                    const serverPos = (resp && resp.category && typeof resp.category.button_position !== 'undefined') ? (resp.category.button_position || 'bottom') : (qcButtonPosition ? qcButtonPosition.value : 'bottom');
                    // normalize
                    const pos = ['top','center','bottom'].includes(serverPos) ? serverPos : 'bottom';
                    // set data attribute
                    card.setAttribute('data-button-position', pos);
                    // update class list: remove any btn-pos-* then add desired
                    card.classList.remove('btn-pos-top','btn-pos-center','btn-pos-bottom');
                    card.classList.add('btn-pos-'+pos);
                    return resp;
                });
        }

        // Always push visibility update so UI reflects changes immediately
        tasks.push(doVisibilityUpdate());

        if (tasks.length === 0) {
            alert('Informe a URL da imagem, a URL da capa ou altere a classe do ícone.');
            return;
        }

        Promise.all(tasks).then(()=>{
            qcModal.hide();
        }).catch(err=>{
            alert(err.message||'Erro');
        });
    });
});
</script>
@endpush
@endauth