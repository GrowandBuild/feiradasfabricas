<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Feira das Fábricas' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .order-info h3 {
            margin: 0 0 15px 0;
            color: #667eea;
        }
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #333;
            font-size: 16px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .items-table th {
            background-color: #667eea;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .total-section {
            background-color: #667eea;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-section h3 {
            margin: 0 0 15px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }
        .total-final {
            font-size: 20px;
            font-weight: 600;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            padding-top: 10px;
            margin-top: 15px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        .footer h4 {
            color: #667eea;
            margin: 0 0 15px 0;
        }
        .contact-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .contact-item {
            text-align: center;
        }
        .contact-item strong {
            color: #667eea;
            display: block;
            margin-bottom: 5px;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #5a6fd8;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-shipped {
            background-color: #cce7ff;
            color: #004085;
        }
        .status-delivered {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .tracking-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .tracking-code {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: 600;
            color: #004085;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            border: 2px dashed #004085;
            text-align: center;
            margin: 10px 0;
        }
        @media (max-width: 600px) {
            .order-details,
            .contact-info {
                grid-template-columns: 1fr;
            }
            .items-table {
                font-size: 14px;
            }
            .items-table th,
            .items-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $company_name ?? 'Feira das Fábricas' }}</h1>
            <p>Sua loja de eletrônicos de confiança</p>
        </div>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="footer">
            <h4>Obrigado pela sua preferência!</h4>
            
            <div class="contact-info">
                <div class="contact-item">
                    <strong>Email</strong>
                    {{ $company_email ?? 'contato@feiradasfabricas.com' }}
                </div>
                <div class="contact-item">
                    <strong>Telefone</strong>
                    {{ $company_phone ?? '(11) 99999-9999' }}
                </div>
            </div>

            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
                <a href="#">WhatsApp</a>
            </div>

            <p style="margin-top: 20px; font-size: 12px; color: #666;">
                Este é um email automático. Por favor, não responda diretamente a esta mensagem.
            </p>
        </div>
    </div>
</body>
</html>
