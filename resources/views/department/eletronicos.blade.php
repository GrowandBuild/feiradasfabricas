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
        --elegant-accent: #ff9900;
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
        background-repeat: no-repeat;
        position: relative;
    }

    .hero-default-bg {
        background: linear-gradient(135deg, var(--elegant-dark) 0%, var(--elegant-blue) 50%, var(--elegant-light) 100%);
    }

    .hero-banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(30, 41, 59, 0.5) 50%, rgba(51, 65, 85, 0.3) 100%);
        display: flex;
        align-items: center;
    }

    .hero-banner-overlay.no-overlay {
        background: none !important;
    }

    .hero-content-row {
        height: 50vh;
        min-height: 350px;
    }

    .hero-banner-content {
        color: white;
        z-index: 2;
        position: relative;
    }

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

    .hero-banner-subtitle {
        font-size: 1rem;
        margin-bottom: 1rem;
        opacity: 0.95;
        font-weight: 300;
        line-height: 1.6;
        max-width: 600px;
    }

    .hero-banner-actions {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .hero-btn-primary {
        background: var(--elegant-accent);
        color: white;
        border: none;
        padding: 18px 40px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 153, 0, 0.3);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .hero-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(255, 153, 0, 0.4);
        background: #e88a00;
        color: white;
    }

    .hero-btn-secondary {
        background: transparent;
        color: white;
        border: 2px solid white;
        padding: 16px 38px;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .hero-btn-secondary:hover {
        background: white;
        color: var(--elegant-dark);
        border-color: white;
        transform: translateY(-3px);
    }

    /* Hero Carousel */
    .hero-carousel {
        height: 50vh;
    }

    .hero-carousel .carousel-inner {
        height: 50vh;
    }

    .hero-carousel .carousel-item {
        height: 50vh;
    }

    /* Controles do Carousel */
    .hero-control-prev,
    .hero-control-next {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .hero-control-prev:hover,
    .hero-control-next:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: var(--elegant-accent);
        transform: scale(1.1);
    }

    .hero-control-prev {
        left: 30px;
    }

    .hero-control-next {
        right: 30px;
    }

    .hero-control-prev .carousel-control-prev-icon,
    .hero-control-next .carousel-control-next-icon {
        width: 20px;
        height: 20px;
    }

    /* Responsividade para Hero Banner */
    @media (max-width: 768px) {
        .hero-section {
            min-height: 40vh;
        }
        
        .hero-banner-full {
            height: 40vh;
        }
        
        .hero-carousel {
            height: 40vh;
        }
        
        .hero-carousel .carousel-inner {
            height: 40vh;
        }
        
        .hero-carousel .carousel-item {
            height: 40vh;
        }
        
        .hero-content-row {
            height: 40vh;
            min-height: 300px;
        }
        
        .hero-banner-title {
            font-size: 2rem;
        }
        
        .hero-banner-subtitle {
            font-size: 1.1rem;
        }
        
        .hero-banner-actions {
            flex-direction: column;
            gap: 15px;
        }
        
        .hero-btn-primary,
        .hero-btn-secondary {
            padding: 15px 30px;
            font-size: 1.1rem;
            justify-content: center;
        }
        
        .hero-control-prev {
            left: 15px;
        }
        
        .hero-control-next {
            right: 15px;
        }
        
        .hero-control-prev,
        .hero-control-next {
            width: 50px;
            height: 50px;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            min-height: 35vh;
        }
        
        .hero-banner-full {
            height: 35vh;
        }
        
        .hero-carousel {
            height: 35vh;
        }
        
        .hero-carousel .carousel-inner {
            height: 35vh;
        }
        
        .hero-carousel .carousel-item {
            height: 35vh;
        }
        
        .hero-content-row {
            height: 35vh;
            min-height: 250px;
        }
        
        .hero-banner-title {
            font-size: 2rem;
        }
        
        .hero-banner-subtitle {
            font-size: 1rem;
        }
        
        .hero-btn-primary,
        .hero-btn-secondary {
            padding: 12px 25px;
            font-size: 1rem;
        }
        
        .hero-control-prev,
        .hero-control-next {
            width: 40px;
            height: 40px;
        }
        
        .hero-control-prev {
            left: 10px;
        }
        
        .hero-control-next {
            right: 10px;
        }
    }

    .elegant-btn {
        background: var(--elegant-accent);
        color: white;
        border: none;
        padding: 15px 35px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 153, 0, 0.3);
    }

    .elegant-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(255, 153, 0, 0.4);
        background: #e88a00;
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
        box-shadow: 0 3px 12px rgba(30, 58, 138, 0.06);
        transition: all 0.3s ease;
        border: 1px solid rgba(30, 58, 138, 0.05);
        height: 100%;
    }

    .elegant-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(30, 58, 138, 0.15);
    }

    .card-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--elegant-blue) 0%, var(--elegant-dark) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        color: white;
        font-size: 1.1rem;
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

    /* Botão compacto para cards de categoria */
    .elegant-card .btn-outline-primary {
        padding: 8px 16px;
        font-size: 0.85rem;
        border-radius: 6px;
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
        background: var(--elegant-accent);
        color: white;
        border: none;
        padding: 15px 35px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 153, 0, 0.3);
    }

    .b2b-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(255, 153, 0, 0.4);
        background: #e88a00;
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
        flex: 1 1 auto;
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
        aspect-ratio: 1;
        max-width: 100px;
        border-radius: 50%;
        margin: 0 auto 10px;
        border: 2px solid rgba(30, 58, 138, 0.1);
        overflow: hidden;
        background: var(--elegant-white);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(30, 58, 138, 0.08);
    }

    .badge-item:hover .badge-circle {
        border-color: var(--elegant-accent);
        box-shadow: 0 4px 12px rgba(255, 153, 0, 0.2);
        transform: scale(1.05);
    }

    .badge-image {
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
<!-- Hero Section com Banner Oficial Ocupando Todo o Espaço -->
<div class="hero-section">
    @if($heroBanners && $heroBanners->count() > 0)
        <div id="heroBannerCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($heroBanners as $index => $banner)
                    @php
                        $showTitle = $banner->show_title ?? true;
                        $showDescription = $banner->show_description ?? true;
                        $showPrimaryDesktop = $banner->show_primary_button_desktop ?? true;
                        $showPrimaryMobile = $banner->show_primary_button_mobile ?? true;
                        $showSecondaryDesktop = $banner->show_secondary_button_desktop ?? true;
                        $showSecondaryMobile = $banner->show_secondary_button_mobile ?? true;
                        $overlayEnabled = $banner->show_overlay ?? true;

                        $overlayStyle = '';
                        if ($overlayEnabled) {
                            $rawOverlayColor = $banner->overlay_color ?? 'rgba(15,23,42,0.65)';
                            if (\Illuminate\Support\Str::startsWith($rawOverlayColor, '#')) {
                                $hex = ltrim($rawOverlayColor, '#');
                                if (strlen($hex) === 3) {
                                    $hex = preg_replace('/(.)/', '$1$1', $hex);
                                }
                                $rgb = [
                                    hexdec(substr($hex, 0, 2)),
                                    hexdec(substr($hex, 2, 2)),
                                    hexdec(substr($hex, 4, 2)),
                                ];
                                $opacity = max(min($banner->overlay_opacity ?? 70, 100), 0) / 100;
                                $overlayStyle = "background: rgba({$rgb[0]}, {$rgb[1]}, {$rgb[2]}, {$opacity});";
                            } else {
                                $overlayStyle = "background: {$rawOverlayColor};";
                            }
                        }

                        $primaryButtonClasses = null;
                        if ($showPrimaryDesktop || $showPrimaryMobile) {
                            $primaryButtonClasses = 'btn hero-btn-primary align-items-center gap-2';
                            if ($showPrimaryDesktop && $showPrimaryMobile) {
                                $primaryButtonClasses .= ' d-inline-flex';
                            } elseif ($showPrimaryDesktop) {
                                $primaryButtonClasses .= ' d-none d-md-inline-flex';
                            } else {
                                $primaryButtonClasses .= ' d-inline-flex d-md-none';
                            }
                        }

                        $secondaryButtonClasses = null;
                        if ($showSecondaryDesktop || $showSecondaryMobile) {
                            $secondaryButtonClasses = 'btn hero-btn-secondary align-items-center gap-2';
                            if ($showSecondaryDesktop && $showSecondaryMobile) {
                                $secondaryButtonClasses .= ' d-inline-flex';
                            } elseif ($showSecondaryDesktop) {
                                $secondaryButtonClasses .= ' d-none d-md-inline-flex';
                            } else {
                                $secondaryButtonClasses .= ' d-inline-flex d-md-none';
                            }
                        }

                        $hasAnyButton = $primaryButtonClasses || $secondaryButtonClasses;
                    @endphp
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="hero-banner-full">
                            <div class="hero-banner-image" 
                                 style="background-image: url('{{ $banner->image ? asset('storage/' . $banner->image) : '' }}');">
                                <div class="hero-banner-overlay {{ $overlayEnabled ? '' : 'no-overlay' }}" @if($overlayEnabled && $overlayStyle) style="{{ $overlayStyle }}" @endif>
                                    <div class="container">
                                        <div class="row align-items-center hero-content-row">
                                            <div class="col-lg-8">
                                                <div class="hero-banner-content">
                                                    @if($showTitle)
                                                        <h1 class="hero-banner-title">{{ $banner->title ?: 'Feira das Fábricas' }}</h1>
                                                    @endif

                                                    @if($showDescription)
                                                        <p class="hero-banner-subtitle">
                                                            {{ $banner->description ?: 'O melhor em eletrônicos e tecnologia para sua empresa e para você.' }}
                                                        </p>
                                                    @endif

                                                    @if($hasAnyButton)
                                                        <div class="hero-banner-actions">
                                                            @if($primaryButtonClasses)
                                                                <a href="{{ route('products') }}" class="{{ $primaryButtonClasses }}">
                                                                    <i class="fas fa-shopping-bag me-2"></i>
                                                                    Ver Produtos
                                                                </a>
                                                            @endif

                                                            @if($secondaryButtonClasses)
                                                                @php
                                                                    $secondaryHref = $banner->link ?: route('contact');
                                                                    $secondaryLabel = $banner->link ? 'Saiba Mais' : 'Contato';
                                                                    $secondaryIcon = $banner->link ? 'fas fa-arrow-right' : 'fas fa-phone';
                                                                @endphp
                                                                <a href="{{ $secondaryHref }}" class="{{ $secondaryButtonClasses }}">
                                                                    <i class="{{ $secondaryIcon }} me-2"></i>
                                                                    {{ $secondaryLabel }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($heroBanners->count() > 1)
                <button class="carousel-control-prev hero-control-prev" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next hero-control-next" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            @endif
        </div>
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
                    <div class="elegant-card">
                        <div class="card-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text">
                            {{ $category->description ?? 'Produtos de alta qualidade para sua empresa.' }}
                        </p>
                        <a href="{{ route('products') }}?category={{ $category->slug }}" 
                           class="btn btn-outline-primary w-100">
                            Explorar Categoria
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
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
                                    <div class="product-card">
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
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
                                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Apple</h2>
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
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
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
                                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
@if($samsungProducts->count() > 0)
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Samsung</h2>
        <p class="section-subtitle">
            Tecnologia inovadora Samsung para seu negócio
        </p>
        
        <!-- Carrossel de Produtos Samsung -->
        <div id="samsungCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $samsungChunks = $samsungProducts->chunk(4);
                @endphp
                @foreach($samsungChunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 col-6 mb-2">
                                    <div class="product-card brand-card">
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                            <div class="product-badge brand-badge">Samsung</div>
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
                                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
            
            <!-- Controles do Carrossel Samsung -->
            @if($samsungChunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#samsungCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#samsungCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
                
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach($samsungChunks as $index => $chunk)
                        <button type="button" data-bs-target="#samsungCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos Samsung -->
<section class="section-elegant" style="padding: 15px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products', ['brand' => 'Samsung']) }}" class="btn elegant-btn">
                Ver Todos os Produtos Samsung
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Seção Produtos Xiaomi -->
@if($xiaomiProducts->count() > 0)
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Xiaomi</h2>
        <p class="section-subtitle">
            Inovação e qualidade Xiaomi para seu negócio
        </p>
        
        <!-- Carrossel de Produtos Xiaomi -->
        <div id="xiaomiCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $xiaomiChunks = $xiaomiProducts->chunk(4);
                @endphp
                @foreach($xiaomiChunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 col-6 mb-2">
                                    <div class="product-card brand-card">
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                            <div class="product-badge brand-badge">Xiaomi</div>
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
                                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
            
            <!-- Controles do Carrossel Xiaomi -->
            @if($xiaomiChunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#xiaomiCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#xiaomiCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
                
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach($xiaomiChunks as $index => $chunk)
                        <button type="button" data-bs-target="#xiaomiCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos Xiaomi -->
<section class="section-elegant" style="padding: 15px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products', ['brand' => 'Xiaomi']) }}" class="btn elegant-btn">
                Ver Todos os Produtos Xiaomi
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Seção Produtos Motorola -->
@if($motorolaProducts->count() > 0)
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Motorola</h2>
        <p class="section-subtitle">
            Tradição e inovação Motorola para seu negócio
        </p>
        
        <!-- Carrossel de Produtos Motorola -->
        <div id="motorolaCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $motorolaChunks = $motorolaProducts->chunk(4);
                @endphp
                @foreach($motorolaChunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 col-6 mb-2">
                                    <div class="product-card brand-card">
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                            <div class="product-badge brand-badge">Motorola</div>
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
                                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
            
            <!-- Controles do Carrossel Motorola -->
            @if($motorolaChunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#motorolaCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#motorolaCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
                
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach($motorolaChunks as $index => $chunk)
                        <button type="button" data-bs-target="#motorolaCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos Motorola -->
<section class="section-elegant" style="padding: 15px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products', ['brand' => 'Motorola']) }}" class="btn elegant-btn">
                Ver Todos os Produtos Motorola
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

@endif

<!-- Seção Produtos Infinix -->
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Infinix</h2>
        <p class="section-subtitle">
            Tecnologia Infinix para seu negócio
        </p>
        
        @if($infinixProducts->count() > 0)
            <!-- Carrossel de Produtos Infinix -->
            <div id="infinixCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @php
                        $infinixChunks = $infinixProducts->chunk(4);
                    @endphp
                    @foreach($infinixChunks as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $product)
                                    <div class="col-lg-3 col-md-6 col-6 mb-2">
                                        <div class="product-card brand-card">
                                            <div class="product-image">
                                                @if($product->first_image)
                                                    <img src="{{ $product->first_image }}" 
                                                         alt="{{ $product->name }}"
                                                         onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                                @else
                                                    <img src="{{ asset('images/no-image.svg') }}" 
                                                         alt="{{ $product->name }}">
                                                @endif
                                                <div class="product-badge brand-badge">Infinix</div>
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
                                                    <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
                
                <!-- Controles do Carrossel Infinix -->
                @if($infinixChunks->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#infinixCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#infinixCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                    
                    <!-- Indicadores -->
                    <div class="carousel-indicators">
                        @foreach($infinixChunks as $index => $chunk)
                            <button type="button" data-bs-target="#infinixCarousel" data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                    aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Botão Ver Todos os Produtos Infinix -->
            <section class="section-elegant" style="padding: 35px 0 15px 0;">
                <div class="container">
                    <div class="text-center">
                        <a href="{{ route('products', ['brand' => 'Infinix']) }}" class="btn elegant-btn">
                            Ver Todos os Produtos Infinix
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </section>
        @else
            <!-- Mensagem quando não há produtos -->
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum produto Infinix cadastrado ainda</h4>
                    <p class="text-muted">Em breve teremos os melhores produtos Infinix para você!</p>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Seção Produtos JBL -->
@if($jblProducts->count() > 0)
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos JBL</h2>
        <p class="section-subtitle">
            Som de qualidade JBL para seu negócio
        </p>
        
        <!-- Carrossel de Produtos JBL -->
        <div id="jblCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $jblChunks = $jblProducts->chunk(4);
                @endphp
                @foreach($jblChunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 col-6 mb-2">
                                    <div class="product-card brand-card">
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                            <div class="product-badge brand-badge">JBL</div>
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
                                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
            
            <!-- Controles do Carrossel JBL -->
            @if($jblChunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#jblCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#jblCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
                
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach($jblChunks as $index => $chunk)
                        <button type="button" data-bs-target="#jblCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos JBL -->
<section class="section-elegant" style="padding: 15px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products', ['brand' => 'JBL']) }}" class="btn elegant-btn">
                Ver Todos os Produtos JBL
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Seção Produtos Oppo -->
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Oppo</h2>
        <p class="section-subtitle">
            Inovação Oppo para seu negócio
        </p>
        
        @if($oppoProducts->count() > 0)
            <!-- Carrossel de Produtos Oppo -->
            <div id="oppoCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @php
                        $oppoChunks = $oppoProducts->chunk(4);
                    @endphp
                    @foreach($oppoChunks as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $product)
                                    <div class="col-lg-3 col-md-6 col-6 mb-2">
                                        <div class="product-card brand-card">
                                            <div class="product-image">
                                                @if($product->first_image)
                                                    <img src="{{ $product->first_image }}" 
                                                         alt="{{ $product->name }}"
                                                         onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                                @else
                                                    <img src="{{ asset('images/no-image.svg') }}" 
                                                         alt="{{ $product->name }}">
                                                @endif
                                                <div class="product-badge brand-badge">Oppo</div>
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
                                                    <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
                
                <!-- Controles do Carrossel Oppo -->
                @if($oppoChunks->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#oppoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#oppoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                    
                    <!-- Indicadores -->
                    <div class="carousel-indicators">
                        @foreach($oppoChunks as $index => $chunk)
                            <button type="button" data-bs-target="#oppoCarousel" data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                    aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Botão Ver Todos os Produtos Oppo -->
            <section class="section-elegant" style="padding: 15px 0;">
                <div class="container">
                    <div class="text-center">
                        <a href="{{ route('products', ['brand' => 'Oppo']) }}" class="btn elegant-btn">
                            Ver Todos os Produtos Oppo
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </section>
        @else
            <!-- Mensagem quando não há produtos -->
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum produto Oppo cadastrado ainda</h4>
                    <p class="text-muted">Em breve teremos os melhores produtos Oppo para você!</p>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Seção Produtos Realme -->
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Realme</h2>
        <p class="section-subtitle">
            Performance Realme para seu negócio
        </p>
        
        @if($realmeProducts->count() > 0)
            <!-- Carrossel de Produtos Realme -->
            <div id="realmeCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @php
                        $realmeChunks = $realmeProducts->chunk(4);
                    @endphp
                    @foreach($realmeChunks as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $product)
                                    <div class="col-lg-3 col-md-6 col-6 mb-2">
                                        <div class="product-card brand-card">
                                            <div class="product-image">
                                                @if($product->first_image)
                                                    <img src="{{ $product->first_image }}" 
                                                         alt="{{ $product->name }}"
                                                         onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                                @else
                                                    <img src="{{ asset('images/no-image.svg') }}" 
                                                         alt="{{ $product->name }}">
                                                @endif
                                                <div class="product-badge brand-badge">Realme</div>
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
                                                    <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
                
                <!-- Controles do Carrossel Realme -->
                @if($realmeChunks->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#realmeCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#realmeCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>
                    
                    <!-- Indicadores -->
                    <div class="carousel-indicators">
                        @foreach($realmeChunks as $index => $chunk)
                            <button type="button" data-bs-target="#realmeCarousel" data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                    aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Botão Ver Todos os Produtos Realme -->
            <section class="section-elegant" style="padding: 15px 0;">
                <div class="container">
                    <div class="text-center">
                        <a href="{{ route('products', ['brand' => 'Realme']) }}" class="btn elegant-btn">
                            Ver Todos os Produtos Realme
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </section>
        @else
            <!-- Mensagem quando não há produtos -->
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum produto Realme cadastrado ainda</h4>
                    <p class="text-muted">Em breve teremos os melhores produtos Realme para você!</p>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Seção Produtos Tecno -->
@if($tecnoProducts->count() > 0)
<section class="section-elegant">
    <div class="container">
        <h2 class="section-title">Produtos Tecno</h2>
        <p class="section-subtitle">
            Tecnologia Tecno para seu negócio
        </p>
        
        <!-- Carrossel de Produtos Tecno -->
        <div id="tecnoCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $tecnoChunks = $tecnoProducts->chunk(4);
                @endphp
                @foreach($tecnoChunks as $index => $chunk)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $product)
                                <div class="col-lg-3 col-md-6 col-6 mb-2">
                                    <div class="product-card brand-card">
                                        <div class="product-image">
                                            @if($product->first_image)
                                                <img src="{{ $product->first_image }}" 
                                                     alt="{{ $product->name }}"
                                                     onerror="this.src='{{ asset('images/no-image.svg') }}'">
                                            @else
                                                <img src="{{ asset('images/no-image.svg') }}" 
                                                     alt="{{ $product->name }}">
                                            @endif
                                            <div class="product-badge brand-badge">Tecno</div>
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
                                                <a href="{{ route('product', $product->slug) }}" class="product-btn" style="flex: 1;">
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
            
            <!-- Controles do Carrossel Tecno -->
            @if($tecnoChunks->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#tecnoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#tecnoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
                
                <!-- Indicadores -->
                <div class="carousel-indicators">
                    @foreach($tecnoChunks as $index => $chunk)
                        <button type="button" data-bs-target="#tecnoCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Botão Ver Todos os Produtos Tecno -->
<section class="section-elegant" style="padding: 15px 0;">
    <div class="container">
        <div class="text-center">
            <a href="{{ route('products', ['brand' => 'Tecno']) }}" class="btn elegant-btn">
                Ver Todos os Produtos Tecno
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
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