@extends('admin.layouts.app')

@section('title', 'Pedido ' . $order->order_number)
@section('page-title', 'Detalhes do Pedido')

@section('content')
<div class="row">
    <!-- Informações do Pedido -->
    <div class="col-lg-8">
        <!-- Cabeçalho do Pedido -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-check"></i> Pedido #{{ $order->order_number }}
                    </h5>
                    <div>
                        <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-light btn-sm" target="_blank">
                            <i class="bi bi-printer"></i> Imprimir
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Data do Pedido:</strong></p>
                        <p class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Forma de Pagamento:</strong></p>
                        <p class="text-muted">{{ $order->payment_method ?: 'Não informado' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-box"></i> Itens do Pedido</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>SKU</th>
                                <th>Preço Unit.</th>
                                <th>Qtd.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->first_image)
                                                <img src="{{ asset('storage/' . $item->product->first_image) }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     class="rounded me-2" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $item->product_name }}</strong>
                                                @if($item->product_variation)
                                                    <div class="mt-1">
                                                        @if($item->product_variation->attributes)
                                                            @foreach($item->product_variation->attributes as $attribute => $value)
                                                                <span class="badge bg-light text-dark me-1" style="font-size: 0.7rem;">
                                                                    {{ ucfirst($attribute) }}: {{ $value }}
                                                                </span>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $item->product_sku }}</code></td>
                                    <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td><strong>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td><strong>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</strong></td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Desconto:</strong></td>
                                <td><strong class="text-success">- R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</strong></td>
                            </tr>
                            @endif
                            @if($order->shipping_amount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Frete:</strong></td>
                                <td><strong>R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</strong></td>
                            </tr>
                            @endif
                            @if($order->tax_amount > 0)
                            <tr>
                                <td colspan="4" class="text-end"><strong>Impostos:</strong></td>
                                <td><strong>R$ {{ number_format($order->tax_amount, 2, ',', '.') }}</strong></td>
                            </tr>
                            @endif
                            <tr class="table-primary">
                                <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                <td><strong class="fs-5">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informações de Frete Selecionado -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-truck"></i> Frete Selecionado</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Serviço:</strong></p>
                        <p class="text-muted">{{ $order->shipping_service ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Transportadora:</strong></p>
                        <p class="text-muted">{{ $order->shipping_company ?? '—' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Prazo:</strong></p>
                        <p class="text-muted">
                            @if($order->shipping_delivery_days)
                                {{ $order->shipping_delivery_days }} dia(s) úteis
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Valor do Frete:</strong></p>
                        <p class="text-muted">R$ {{ number_format($order->shipping_amount ?? 0, 2, ',', '.') }}</p>
                    </div>
                </div>
                @if($order->shipping_zip_code)
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>CEP de Destino:</strong></p>
                        <p class="text-muted">{{ $order->shipping_zip_code }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Cupons Aplicados -->
        @if($order->couponUsages && $order->couponUsages->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-ticket-perforated"></i> Cupons Aplicados</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($order->couponUsages as $couponUsage)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-success">{{ $couponUsage->coupon->code }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $couponUsage->coupon->description ?? 'Desconto aplicado' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success fs-6">
                                            -R$ {{ number_format($couponUsage->discount_amount, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Endereço (apenas cobrança) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-credit-card"></i> Endereço de Cobrança</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</strong></p>
                        @if($order->billing_company)
                            <p class="mb-1">{{ $order->billing_company }}</p>
                        @endif
                        <p class="mb-1">{{ $order->billing_address }}, {{ $order->billing_number }}</p>
                        @if($order->billing_complement)
                            <p class="mb-1">{{ $order->billing_complement }}</p>
                        @endif
                        <p class="mb-1">{{ $order->billing_neighborhood }}</p>
                        <p class="mb-1">{{ $order->billing_city }} - {{ $order->billing_state }}</p>
                        {{-- CEP removido da visualização --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Observações -->
        @if($order->notes || $order->admin_notes)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-chat-text"></i> Observações</h6>
            </div>
            <div class="card-body">
                @if($order->notes)
                    <div class="mb-3">
                        <strong>Observações do Cliente:</strong>
                        <p class="text-muted mb-0">{{ $order->notes }}</p>
                    </div>
                @endif
                @if($order->admin_notes)
                    <div>
                        <strong>Notas Internas:</strong>
                        <p class="text-muted mb-0">{{ $order->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar com Cliente e Status -->
    <div class="col-lg-4">
        <!-- Cliente -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person"></i> Cliente</h6>
            </div>
            <div class="card-body">
                @if($order->customer)
                    <p class="mb-1"><strong>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong></p>
                    @if($order->customer->company_name)
                        <p class="mb-1">{{ $order->customer->company_name }}</p>
                    @endif
                    <p class="mb-1"><i class="bi bi-envelope"></i> {{ $order->customer->email }}</p>
                    @if($order->customer->phone)
                        <p class="mb-1"><i class="bi bi-telephone"></i> {{ $order->customer->phone }}</p>
                    @endif
                @else
                    <p class="mb-1 text-muted"><strong>Cliente não encontrado</strong></p>
                    <p class="mb-1 text-muted">Dados do cliente indisponíveis</p>
                @endif
                @if($order->customer)
                    <p class="mb-0">
                        <span class="badge bg-{{ $order->customer->type === 'B2B' ? 'info' : 'secondary' }}">
                            {{ $order->customer->type }}
                        </span>
                    </p>
                @endif
                @if($order->customer)
                    <hr>
                    <a href="{{ route('admin.customers.show', $order->customer) }}" class="btn btn-outline-primary btn-sm w-100">
                        Ver Perfil do Cliente
                    </a>
                @endif
            </div>
        </div>

        <!-- Atualizar Status do Pedido -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-truck"></i> Status do Pedido</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Status Atual:</label>
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $statusLabels = [
                                'pending' => 'Pendente',
                                'processing' => 'Processando',
                                'shipped' => 'Enviado',
                                'delivered' => 'Entregue',
                                'cancelled' => 'Cancelado'
                            ];
                        @endphp
                        <div>
                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} fs-6">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alterar Status:</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processando</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Enviado</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Entregue</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observações:</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Adicionar nota sobre a atualização..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Atualizar Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Atualizar Status do Pagamento -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-credit-card"></i> Status do Pagamento</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Status Atual:</label>
                        @php
                            $paymentColors = [
                                'pending' => 'warning',
                                'paid' => 'success',
                                'partial' => 'info',
                                'refunded' => 'secondary',
                                'failed' => 'danger'
                            ];
                            $paymentLabels = [
                                'pending' => 'Pendente',
                                'paid' => 'Pago',
                                'partial' => 'Parcial',
                                'refunded' => 'Reembolsado',
                                'failed' => 'Falhou'
                            ];
                        @endphp
                        <div>
                            <span class="badge bg-{{ $paymentColors[$order->payment_status] ?? 'secondary' }} fs-6">
                                {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alterar Status:</label>
                        <select name="payment_status" class="form-select" required>
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Pago</option>
                            <option value="partial" {{ $order->payment_status === 'partial' ? 'selected' : '' }}>Parcial</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Falhou</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Atualizar Pagamento
                    </button>
                </form>
            </div>
        </div>

        <!-- Voltar -->
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-left"></i> Voltar para Pedidos
        </a>
    </div>
</div>
@endsection

