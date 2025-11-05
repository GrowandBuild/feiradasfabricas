@extends('emails.layout')

@section('content')
<h2>🧾 Nota Fiscal Emitida</h2>

<p>Olá, {{ $customer->first_name }}! Sua nota fiscal foi emitida e está em anexo neste email.</p>

<div class="order-info">
    <h3>📋 Informações da Nota Fiscal</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Número do Pedido</span>
            <span class="detail-value"><strong>#{{ $order->order_number }}</strong></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Data de Emissão</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge status-confirmed">Autorizada</span>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Valor Total</span>
            <span class="detail-value" style="color: #28a745; font-weight: 600;">
                <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong>
            </span>
        </div>
    </div>
</div>

<div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #004085; margin-top: 0;">📎 Nota Fiscal em Anexo</h4>
    <p style="margin-bottom: 0;">
        A nota fiscal do seu pedido está em anexo a este email em formato PDF. 
        Guarde este documento para fins de garantia e declaração de imposto de renda.
    </p>
</div>

<h3>🛍️ Itens da Nota Fiscal</h3>
<table class="items-table">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Preço Unit.</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>
                <strong>{{ $item->product_name }}</strong>
                @if($item->product_sku)
                <br><small style="color: #666;">SKU: {{ $item->product_sku }}</small>
                @endif
            </td>
            <td>{{ $item->quantity }}</td>
            <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
            <td><strong>R$ {{ number_format($item->total, 2, ',', '.') }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="total-section">
    <h3>💰 Resumo Fiscal</h3>
    
    <div class="total-row">
        <span>Subtotal:</span>
        <span>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
    </div>
    
    @if($order->discount_amount > 0)
    <div class="total-row">
        <span>Desconto:</span>
        <span style="color: #28a745;">- R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</span>
    </div>
    @endif
    
    @if($order->shipping_amount > 0)
    <div class="total-row">
        <span>Frete:</span>
        <span>R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
    </div>
    @endif
    
    @if($order->tax_amount > 0)
    <div class="total-row">
        <span>Impostos:</span>
        <span>R$ {{ number_format($order->tax_amount, 2, ',', '.') }}</span>
    </div>
    @endif
    
    <div class="total-row total-final">
        <span><strong>TOTAL:</strong></span>
        <span><strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></span>
    </div>
</div>

<div class="order-info">
    <h3>🏠 Dados de Cobrança</h3>
    <p style="margin: 0;">
        <strong>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</strong><br>
        @if($order->billing_company)
        {{ $order->billing_company }}<br>
        @endif
        {{ $order->billing_address }}, {{ $order->billing_number }}<br>
        @if($order->billing_complement)
        {{ $order->billing_complement }}<br>
        @endif
        {{ $order->billing_neighborhood }}<br>
        {{ $order->billing_city }} - {{ $order->billing_state }}<br>
        CEP: {{ $order->billing_zip_code }}
    </p>
</div>

<h3>📋 Informações Importantes</h3>
<ul>
    <li><strong>Documento Fiscal:</strong> Nota Fiscal Eletrônica (NF-e) emitida pela SEFAZ</li>
    <li><strong>Validade:</strong> Documento válido para fins fiscais e contábeis</li>
    <li><strong>Garantia:</strong> Guarde para reivindicação de garantia dos produtos</li>
    <li><strong>Imposto de Renda:</strong> Pode ser utilizada na declaração anual</li>
</ul>

<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #155724; margin-top: 0;">✅ Nota Fiscal Autorizada</h4>
    <p style="margin-bottom: 0;">
        Esta nota fiscal foi autorizada pela Receita Federal e está disponível 
        para consulta no portal da SEFAZ do seu estado.
    </p>
</div>

<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #856404; margin-top: 0;">💡 Dicas Importantes</h4>
    <ul style="margin-bottom: 0;">
        <li>Imprima e guarde a nota fiscal em local seguro</li>
        <li>Para produtos com garantia, mantenha a nota fiscal</li>
        <li>Em caso de devolução, apresente este documento</li>
        <li>Conserve por pelo menos 5 anos para fins fiscais</li>
    </ul>
</div>

<h3>🔍 Consulta da Nota Fiscal</h3>
<p>Você pode consultar sua nota fiscal no portal da SEFAZ do seu estado usando a chave de acesso que consta no documento.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('orders.show', $order) }}" class="button">Ver Detalhes do Pedido</a>
</div>

<h3>📱 Acompanhe seu Pedido</h3>
<p>Continue acompanhando o status do seu pedido através do nosso site.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('orders.show', $order) }}" class="button">Acompanhar Pedido</a>
</div>

<p><strong>Dúvidas sobre a nota fiscal?</strong> Entre em contato conosco através do email {{ $company_email ?? 'contato@feiradasfabricas.com' }} ou pelo telefone {{ $company_phone ?? '(11) 99999-9999' }}.</p>

<p>Obrigado pela sua compra e pela confiança em nossa loja!</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection
