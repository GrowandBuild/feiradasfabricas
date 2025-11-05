<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #{{ $order->order_number }} - Feira das Fábricas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
        body {
            background: white;
            font-size: 12px;
        }
        .invoice-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .invoice-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Botão Imprimir -->
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Imprimir Pedido
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Fechar
            </button>
        </div>

        <!-- Cabeçalho -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-6">
                    <h2 class="mb-0" style="color: #667eea;">Feira das Fábricas</h2>
                    <p class="mb-0">Sua loja de eletrônicos</p>
                </div>
                <div class="col-6 text-end">
                    <h3 class="mb-2">PEDIDO</h3>
                    <h4 class="mb-0">#{{ $order->order_number }}</h4>
                    <p class="mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Informações do Cliente e Status -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="invoice-box">
                    <h6 class="mb-3"><strong>CLIENTE</strong></h6>
                    @if($order->customer)
                        <p class="mb-1"><strong>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong></p>
                        @if($order->customer->company_name)
                            <p class="mb-1">{{ $order->customer->company_name }}</p>
                        @endif
                        <p class="mb-1">{{ $order->customer->email }}</p>
                        @if($order->customer->phone)
                            <p class="mb-0">{{ $order->customer->phone }}</p>
                        @endif
                    @else
                        <p class="mb-1"><strong>Cliente não encontrado</strong></p>
                        <p class="mb-1">Dados do cliente indisponíveis</p>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="invoice-box">
                    <h6 class="mb-3"><strong>STATUS DO PEDIDO</strong></h6>
                    @php
                        $statusLabels = [
                            'pending' => 'Pendente',
                            'processing' => 'Em Processamento',
                            'shipped' => 'Enviado',
                            'delivered' => 'Entregue',
                            'cancelled' => 'Cancelado'
                        ];
                        $paymentLabels = [
                            'pending' => 'Pendente',
                            'paid' => 'Pago',
                            'partial' => 'Parcial',
                            'refunded' => 'Reembolsado',
                            'failed' => 'Falhou'
                        ];
                    @endphp
                    <p class="mb-1"><strong>Status:</strong> {{ $statusLabels[$order->status] ?? $order->status }}</p>
                    <p class="mb-1"><strong>Pagamento:</strong> {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</p>
                    <p class="mb-0"><strong>Método:</strong> {{ $order->payment_method ?: 'Não informado' }}</p>
                </div>
            </div>
        </div>

        <!-- Endereços -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="invoice-box">
                    <h6 class="mb-3"><strong>ENDEREÇO DE ENTREGA</strong></h6>
                    <p class="mb-1">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                    @if($order->shipping_company)
                        <p class="mb-1">{{ $order->shipping_company }}</p>
                    @endif
                    <p class="mb-1">{{ $order->shipping_address }}, {{ $order->shipping_number }}</p>
                    @if($order->shipping_complement)
                        <p class="mb-1">{{ $order->shipping_complement }}</p>
                    @endif
                    <p class="mb-1">{{ $order->shipping_neighborhood }}</p>
                    <p class="mb-1">{{ $order->shipping_city }} - {{ $order->shipping_state }}</p>
                    <p class="mb-0">CEP: {{ $order->shipping_zip_code }}</p>
                </div>
            </div>
            <div class="col-6">
                <div class="invoice-box">
                    <h6 class="mb-3"><strong>ENDEREÇO DE COBRANÇA</strong></h6>
                    <p class="mb-1">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</p>
                    @if($order->billing_company)
                        <p class="mb-1">{{ $order->billing_company }}</p>
                    @endif
                    <p class="mb-1">{{ $order->billing_address }}, {{ $order->billing_number }}</p>
                    @if($order->billing_complement)
                        <p class="mb-1">{{ $order->billing_complement }}</p>
                    @endif
                    <p class="mb-1">{{ $order->billing_neighborhood }}</p>
                    <p class="mb-1">{{ $order->billing_city }} - {{ $order->billing_state }}</p>
                    <p class="mb-0">CEP: {{ $order->billing_zip_code }}</p>
                </div>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="mb-4">
            <h6 class="mb-3"><strong>ITENS DO PEDIDO</strong></h6>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th>SKU</th>
                        <th class="text-center">Qtd.</th>
                        <th class="text-end">Preço Unit.</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                {{ $item->product_name }}
                            </td>
                            <td>{{ $item->product_sku }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td class="text-end">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                        <td class="text-end"><strong>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</strong></td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="4" class="text-end"><strong>Desconto:</strong></td>
                        <td class="text-end text-success"><strong>- R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</strong></td>
                    </tr>
                    @endif
                    @if($order->shipping_amount > 0)
                    <tr>
                        <td colspan="4" class="text-end"><strong>Frete:</strong></td>
                        <td class="text-end"><strong>R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</strong></td>
                    </tr>
                    @endif
                    @if($order->tax_amount > 0)
                    <tr>
                        <td colspan="4" class="text-end"><strong>Impostos:</strong></td>
                        <td class="text-end"><strong>R$ {{ number_format($order->tax_amount, 2, ',', '.') }}</strong></td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                        <td class="text-end"><strong style="font-size: 1.2em;">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Observações -->
        @if($order->notes)
        <div class="invoice-box">
            <h6 class="mb-2"><strong>OBSERVAÇÕES DO CLIENTE</strong></h6>
            <p class="mb-0">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Rodapé -->
        <div class="text-center mt-5 pt-4" style="border-top: 1px solid #ddd;">
            <p class="mb-0"><small>Este é um documento eletrônico gerado pelo sistema Feira das Fábricas</small></p>
            <p class="mb-0"><small>Data de impressão: {{ now()->format('d/m/Y H:i') }}</small></p>
        </div>
    </div>

    <script>
        // Auto-print quando carregar (opcional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

