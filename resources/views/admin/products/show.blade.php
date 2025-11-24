@extends('admin.layouts.app')

@section('title', 'Detalhes do Produto')
@section('page-title', 'Detalhes do Produto')

@section('content')
<!-- Cabeçalho -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $product->name }}</h4>
        <p class="text-muted mb-0">SKU: {{ $product->sku }}</p>
    </div>
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar Produto
        </a>
    </div>
</div>

<div class="row">
    <!-- Coluna Principal -->
    <div class="col-lg-8">
        <!-- Informações Básicas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações Básicas</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold text-muted">Nome do Produto</label>
                        <p>{{ $product->name }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold text-muted">SKU</label>
                        <p><code>{{ $product->sku }}</code></p>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold text-muted">Status</label>
                        <p>
                            <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                            @if($product->is_featured)
                                <span class="badge bg-warning">Destaque</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Marca removida do painel de detalhes -->
                    <div class="col-md-4">
                        <label class="fw-bold text-muted">Modelo</label>
                        <p>{{ $product->model ?: 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold text-muted">Slug</label>
                        <p><small class="text-muted">{{ $product->slug }}</small></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-muted">Descrição</label>
                    <p>{{ $product->description }}</p>
                </div>

                @if($product->short_description)
                <div class="mb-3">
                    <label class="fw-bold text-muted">Descrição Curta</label>
                    <p>{{ $product->short_description }}</p>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <label class="fw-bold text-muted">Categorias</label>
                        <p>
                            @forelse($product->categories as $category)
                                <span class="badge bg-light text-dark me-1">{{ $category->name }}</span>
                            @empty
                                <span class="text-muted">Sem categoria</span>
                            @endforelse
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galeria de Imagens -->
        @if($product->images && count($product->images) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-images"></i> Galeria de Imagens ({{ count($product->images) }})</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($product->images as $image)
                    <div class="col-md-3">
                        <img src="{{ asset('storage/' . $image) }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid rounded border"
                             style="width: 100%; height: 200px; object-fit: cover;">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Especificações Técnicas -->
        @if($product->specifications)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Especificações Técnicas</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    @foreach($product->specifications as $key => $value)
                    <tr>
                        <td class="fw-bold" style="width: 30%;">{{ ucfirst($key) }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        @endif

        <!-- Histórico de Estoque -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Histórico de Estoque</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                    <i class="bi bi-plus-circle"></i> Ajustar Estoque
                </button>
            </div>
            <div class="card-body">
                @if($product->inventoryLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Anterior</th>
                                    <th>Alteração</th>
                                    <th>Depois</th>
                                    <th>Responsável</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->inventoryLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $typeClass = match($log->type) {
                                                'in' => 'success',
                                                'out' => 'danger',
                                                'adjustment' => 'warning',
                                                default => 'secondary'
                                            };
                                            $typeName = match($log->type) {
                                                'in' => 'Entrada',
                                                'out' => 'Saída',
                                                'adjustment' => 'Ajuste',
                                                default => 'Outro'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $typeClass }}">{{ $typeName }}</span>
                                    </td>
                                    <td>{{ $log->quantity_before }}</td>
                                    <td class="fw-bold {{ $log->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                                    </td>
                                    <td>{{ $log->quantity_after }}</td>
                                    <td>{{ $log->admin->name ?? 'Sistema' }}</td>
                                    <td><small>{{ $log->notes }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Nenhuma movimentação de estoque registrada.</p>
                @endif
            </div>
        </div>

        <!-- Histórico de Vendas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cart-check"></i> Histórico de Vendas</h5>
            </div>
            <div class="card-body">
                @if($product->orderItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Qtd</th>
                                    <th>Preço Unit.</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->orderItems as $item)
                                <tr>
                                    <td><code>#{{ $item->order->id }}</code></td>
                                    <td>{{ $item->order->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $item->order->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($item->order->status) {
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst($item->order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <strong>Total Vendido:</strong> {{ $product->orderItems->sum('quantity') }} unidades<br>
                        <strong>Faturamento Total:</strong> R$ {{ number_format($product->orderItems->sum('subtotal'), 2, ',', '.') }}
                    </div>
                @else
                    <p class="text-muted mb-0">Nenhuma venda registrada para este produto.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Coluna Lateral -->
    <div class="col-lg-4">
        <!-- Card de Preços -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Preços</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold text-muted">Preço de Venda</label>
                    <h3 class="text-success mb-0">R$ {{ number_format($product->price, 2, ',', '.') }}</h3>
                </div>
                @if($product->b2b_price)
                <div class="mb-3">
                    <label class="fw-bold text-muted">Preço B2B</label>
                    <h4 class="text-info mb-0">R$ {{ number_format($product->b2b_price, 2, ',', '.') }}</h4>
                </div>
                @endif
                @if($product->cost_price)
                <div class="mb-3">
                    <label class="fw-bold text-muted">Preço de Custo</label>
                    <p class="mb-0">R$ {{ number_format($product->cost_price, 2, ',', '.') }}</p>
                    @if($product->cost_price > 0)
                    <small class="text-muted">
                        Margem: {{ number_format((($product->price - $product->cost_price) / $product->cost_price) * 100, 1) }}%
                    </small>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Card de Estoque -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-box-seam"></i> Estoque</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold text-muted">Quantidade Atual</label>
                    <h3 class="mb-0">
                        {{ $product->current_stock }}
                        @if($product->isLowStock())
                            <span class="badge bg-danger ms-2">Baixo</span>
                        @elseif($product->current_stock == 0)
                            <span class="badge bg-secondary ms-2">Sem Estoque</span>
                        @else
                            <span class="badge bg-success ms-2">OK</span>
                        @endif
                    </h3>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Estoque Mínimo</label>
                    <p class="mb-0">{{ $product->min_stock }} unidades</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Gerenciar Estoque</label>
                    <p class="mb-0">
                        <span class="badge bg-{{ $product->manage_stock ? 'success' : 'secondary' }}">
                            {{ $product->manage_stock ? 'Sim' : 'Não' }}
                        </span>
                    </p>
                </div>
                <div class="mb-0">
                    <label class="fw-bold text-muted">Em Estoque</label>
                    <p class="mb-0">
                        <span class="badge bg-{{ $product->in_stock ? 'success' : 'danger' }}">
                            {{ $product->in_stock ? 'Sim' : 'Não' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Card de Dimensões -->
        @if($product->weight || $product->length || $product->width || $product->height)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-rulers"></i> Dimensões</h5>
            </div>
            <div class="card-body">
                @if($product->weight)
                <div class="mb-2">
                    <label class="fw-bold text-muted">Peso</label>
                    <p class="mb-0">{{ $product->weight }} kg</p>
                </div>
                @endif
                @if($product->length || $product->width || $product->height)
                <div>
                    <label class="fw-bold text-muted">Dimensões (C x L x A)</label>
                    <p class="mb-0">
                        {{ $product->length ?: '0' }} x 
                        {{ $product->width ?: '0' }} x 
                        {{ $product->height ?: '0' }} cm
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Card de Metadados -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar"></i> Informações do Sistema</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="fw-bold text-muted">Criado em</label>
                    <p class="mb-0">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="fw-bold text-muted">Atualizado em</label>
                    <p class="mb-0">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ajuste de Estoque -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.products.adjust-stock', $product) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajustar Estoque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estoque Atual</label>
                        <p class="text-muted">{{ $product->current_stock }} unidades</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Ajuste</label>
                        <select name="type" class="form-select" required>
                            <option value="in">Entrada (adicionar)</option>
                            <option value="out">Saída (remover)</option>
                            <option value="adjustment">Ajuste Manual</option>
                        </select>
                        <small class="text-muted">
                            <strong>Entrada:</strong> adiciona ao estoque atual<br>
                            <strong>Saída:</strong> remove do estoque atual<br>
                            <strong>Ajuste:</strong> define um novo valor absoluto
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantity" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Ajuste</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

