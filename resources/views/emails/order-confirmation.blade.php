@extends('emails.layout')

@section('content')
<h2>Olá, {{ $customer->first_name }}!</h2>

<p>Recebemos o seu pedido e queremos agradecer pela sua confiança em nossa loja!</p>

<div class="order-info">
    <h3>📦 Detalhes do Pedido</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Número do Pedido</span>
            <span class="detail-value"><strong>#{{ $order->order_number }}</strong></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Data do Pedido</span>
            <span class="detail-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge status-pending">Pendente</span>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Forma de Pagamento</span>
            <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
        </div>
    </div>
</div>

<h3>🛍️ Itens do Pedido</h3>
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
    <h3>💰 Resumo Financeiro</h3>
    
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

<h3>📋 Próximos Passos</h3>
<ol>
    <li><strong>Confirmação de Pagamento:</strong> Após a confirmação do pagamento, você receberá outro email.</li>
    <li><strong>Preparação:</strong> Seu pedido será preparado com cuidado.</li>
    <li><strong>Envio:</strong> Você receberá um email com o código de rastreamento.</li>
    <li><strong>Entrega:</strong> Seu pedido será entregue no endereço informado.</li>
</ol>

@if($order->notes)
<div class="order-info">
    <h3>📝 Observações do Cliente</h3>
    <p style="margin: 0;">{{ $order->notes }}</p>
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('orders.show', $order) }}" class="button">Acompanhar Pedido</a>
</div>

<p><strong>Dúvidas?</strong> Entre em contato conosco através do email {{ $company_email ?? 'contato@feiradasfabricas.com' }} ou pelo telefone {{ $company_phone ?? '(11) 99999-9999' }}.</p>

<p>Agradecemos pela sua preferência e esperamos que você aproveite sua compra!</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection
