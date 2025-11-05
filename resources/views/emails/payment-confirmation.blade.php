@extends('emails.layout')

@section('content')
<h2>🎉 Pagamento Confirmado!</h2>

<p>Olá, {{ $customer->first_name }}! Temos uma ótima notícia: seu pagamento foi confirmado com sucesso!</p>

<div class="order-info">
    <h3>✅ Confirmação de Pagamento</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Número do Pedido</span>
            <span class="detail-value"><strong>#{{ $order->order_number }}</strong></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Data do Pagamento</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Valor Pago</span>
            <span class="detail-value" style="color: #28a745; font-weight: 600;">
                <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Forma de Pagamento</span>
            <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
        </div>
    </div>
</div>

<h3>📦 Seu Pedido Está Sendo Preparado</h3>
<p>Agora que o pagamento foi confirmado, nossa equipe já está trabalhando para preparar seu pedido com muito cuidado!</p>

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
    <h3>💰 Valor Total Pago</h3>
    
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
    <h3>🚚 Endereço de Entrega</h3>
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

<h3>⏰ Cronograma de Entrega</h3>
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <span style="background-color: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">✓</span>
        <span><strong>Pagamento Confirmado</strong> - {{ now()->format('d/m/Y H:i') }}</span>
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <span style="background-color: #ffc107; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">📦</span>
        <span><strong>Preparação do Pedido</strong> - Em andamento</span>
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <span style="background-color: #6c757d; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">🚚</span>
        <span><strong>Envio</strong> - Aguardando</span>
    </div>
    <div style="display: flex; align-items: center;">
        <span style="background-color: #6c757d; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">🏠</span>
        <span><strong>Entrega</strong> - Aguardando</span>
    </div>
</div>

<h3>📱 Acompanhe seu Pedido</h3>
<p>Você pode acompanhar o status do seu pedido a qualquer momento através do nosso site.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('orders.show', $order) }}" class="button">Acompanhar Pedido</a>
</div>

<h3>📋 Próximos Passos</h3>
<ul>
    <li><strong>Preparação:</strong> Nossa equipe está preparando seu pedido com muito cuidado</li>
    <li><strong>Envio:</strong> Em breve você receberá um email com o código de rastreamento</li>
    <li><strong>Entrega:</strong> Seu pedido será entregue no endereço informado</li>
</ul>

<div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #004085; margin-top: 0;">💡 Dica Importante</h4>
    <p style="margin-bottom: 0;">
        Mantenha este email para referência futura. Ele contém todas as informações importantes sobre seu pedido.
    </p>
</div>

<p><strong>Dúvidas?</strong> Nossa equipe de atendimento está pronta para ajudar! Entre em contato através do email {{ $company_email ?? 'contato@feiradasfabricas.com' }} ou pelo telefone {{ $company_phone ?? '(11) 99999-9999' }}.</p>

<p>Obrigado pela sua compra e pela confiança em nossa loja!</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection
