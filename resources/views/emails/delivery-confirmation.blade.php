@extends('emails.layout')

@section('content')
<h2>🏠 Pedido Entregue com Sucesso!</h2>

<p>Olá, {{ $customer->first_name }}! Temos uma ótima notícia: seu pedido foi entregue com sucesso!</p>

<div class="order-info">
    <h3>✅ Confirmação de Entrega</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Número do Pedido</span>
            <span class="detail-value"><strong>#{{ $order->order_number }}</strong></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Data da Entrega</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge status-delivered">Entregue</span>
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

<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #155724; margin-top: 0;">🎉 Pedido Entregue!</h4>
    <p style="margin-bottom: 0;">
        Seu pedido foi entregue com sucesso no endereço informado. 
        Esperamos que você aproveite sua compra!
    </p>
</div>

<h3>🛍️ Produtos Entregues</h3>
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
    <h3>💰 Resumo Final</h3>
    
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
        <span><strong>TOTAL PAGO:</strong></span>
        <span><strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></span>
    </div>
</div>

<div class="order-info">
    <h3>🏠 Endereço de Entrega</h3>
    <p style="margin: 0;">
        <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong><br>
        {{ $order->shipping_address }}, {{ $order->shipping_number }}<br>
        @if($order->shipping_complement)
        {{ $order->shipping_complement }}<br>
        @endif
        {{ $order->shipping_neighborhood }}<br>
        {{ $order->shipping_city }} - {{ $order->shipping_state }}<br>
        CEP: {{ $order->shipping_zip_code }}<br>
        @if($order->shipping_phone)
        📞 {{ $order->shipping_phone }}
        @endif
    </p>
</div>

<h3>✅ Jornada Completa</h3>
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <span style="background-color: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">✓</span>
        <span><strong>Pagamento Confirmado</strong> - Concluído</span>
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <span style="background-color: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">✓</span>
        <span><strong>Preparação do Pedido</strong> - Concluído</span>
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <span style="background-color: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">✓</span>
        <span><strong>Envio</strong> - Concluído</span>
    </div>
    <div style="display: flex; align-items: center;">
        <span style="background-color: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">✓</span>
        <span><strong>Entrega</strong> - {{ now()->format('d/m/Y H:i') }}</span>
    </div>
</div>

<h3>⭐ Avalie Sua Experiência</h3>
<p>Gostaríamos muito de saber sua opinião sobre nossa loja e os produtos que você recebeu!</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="#" class="button">Avaliar Compra</a>
</div>

<h3>📋 Informações Importantes</h3>
<ul>
    <li><strong>Garantia:</strong> Conserve a nota fiscal para reivindicação de garantia</li>
    <li><strong>Manual do Produto:</strong> Siga as instruções de uso dos produtos</li>
    <li><strong>Suporte Técnico:</strong> Entre em contato se precisar de ajuda</li>
    <li><strong>Devolução:</strong> Temos 7 dias para troca/devolução</li>
</ul>

<div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #004085; margin-top: 0;">💡 Dicas para seus Produtos</h4>
    <ul style="margin-bottom: 0;">
        <li>Leia o manual de instruções antes de usar</li>
        <li>Mantenha os produtos em local adequado</li>
        <li>Guarde a embalagem original para possível devolução</li>
        <li>Em caso de dúvidas, entre em contato conosco</li>
    </ul>
</div>

<h3>🔄 Quer Comprar Novamente?</h3>
<p>Encontre mais produtos incríveis em nossa loja!</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('home') }}" class="button">Ver Mais Produtos</a>
</div>

<h3>📞 Precisa de Ajuda?</h3>
<p>Nossa equipe de atendimento está sempre pronta para ajudar!</p>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="text-align: center;">
            <strong style="color: #667eea; display: block; margin-bottom: 5px;">📧 Email</strong>
            {{ $company_email ?? 'contato@feiradasfabricas.com' }}
        </div>
        <div style="text-align: center;">
            <strong style="color: #667eea; display: block; margin-bottom: 5px;">📞 Telefone</strong>
            {{ $company_phone ?? '(11) 99999-9999' }}
        </div>
    </div>
</div>

<p><strong>Obrigado pela sua compra!</strong> Foi um prazer atendê-lo e esperamos vê-lo novamente em breve!</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection
