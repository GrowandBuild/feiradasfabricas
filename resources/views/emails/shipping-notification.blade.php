@extends('emails.layout')

@section('content')
<h2>🚚 Seu pedido foi enviado!</h2>

<p>Olá, {{ $customer->first_name }}! Temos uma ótima notícia: seu pedido foi enviado e está a caminho!</p>

<div class="order-info">
    <h3>📦 Informações de Envio</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Número do Pedido</span>
            <span class="detail-value"><strong>#{{ $order->order_number }}</strong></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Data do Envio</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge status-shipped">Enviado</span>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Previsão de Entrega</span>
            <span class="detail-value">{{ now()->addDays(3)->format('d/m/Y') }}</span>
        </div>
    </div>
</div>

@if($tracking_code)
<div class="tracking-info">
    <h3>🔍 Código de Rastreamento</h3>
    <p>Use o código abaixo para acompanhar seu pedido:</p>
    <div class="tracking-code">{{ $tracking_code }}</div>
    <p style="text-align: center; margin-top: 15px;">
        <a href="https://www2.correios.com.br/sistemas/rastreamento/" target="_blank" class="button">
            Rastrear Pedido
        </a>
    </p>
</div>
@endif

<h3>🛍️ Itens Enviados</h3>
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
    <h3>💰 Resumo do Pedido</h3>
    
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

<h3>⏰ Status Atualizado</h3>
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
        <span style="background-color: #007bff; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">🚚</span>
        <span><strong>Envio</strong> - {{ now()->format('d/m/Y H:i') }}</span>
    </div>
    <div style="display: flex; align-items: center;">
        <span style="background-color: #ffc107; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">⏳</span>
        <span><strong>Entrega</strong> - Prevista para {{ now()->addDays(3)->format('d/m/Y') }}</span>
    </div>
</div>

<h3>📱 Acompanhe seu Pedido</h3>
<p>Você pode acompanhar o status do seu pedido a qualquer momento através do nosso site ou usando o código de rastreamento acima.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('orders.show', $order) }}" class="button">Acompanhar Pedido</a>
</div>

<h3>📋 Informações Importantes</h3>
<ul>
    <li><strong>Previsão de Entrega:</strong> {{ now()->addDays(3)->format('d/m/Y') }} (prazo estimado)</li>
    <li><strong>Horário de Entrega:</strong> Das 8h às 18h</li>
    <li><strong>Responsável pela Entrega:</strong> Aguarde contato do entregador</li>
    <li><strong>Documento Necessário:</strong> RG ou CPF do destinatário</li>
</ul>

<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #856404; margin-top: 0;">⚠️ Dicas para Receber seu Pedido</h4>
    <ul style="margin-bottom: 0;">
        <li>Mantenha o telefone próximo para receber ligações do entregador</li>
        <li>Tenha um documento de identificação em mãos</li>
        <li>Verifique se há alguém em casa para receber o pedido</li>
        <li>Em caso de ausência, o entregador tentará uma nova entrega</li>
    </ul>
</div>

<div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #004085; margin-top: 0;">💡 Dica Importante</h4>
    <p style="margin-bottom: 0;">
        Guarde este email com o código de rastreamento. Ele será útil para acompanhar seu pedido até a entrega.
    </p>
</div>

<p><strong>Dúvidas sobre a entrega?</strong> Entre em contato conosco através do email {{ $company_email ?? 'contato@feiradasfabricas.com' }} ou pelo telefone {{ $company_phone ?? '(11) 99999-9999' }}.</p>

<p>Obrigado pela sua compra e pela confiança em nossa loja!</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection
