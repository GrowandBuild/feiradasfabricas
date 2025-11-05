@extends('admin.layouts.app')

@section('title', 'Cliente ' . $customer->first_name . ' ' . $customer->last_name)
@section('page-title', 'Detalhes do Cliente')

@section('content')
<div class="row">
    <!-- Informações do Cliente -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person"></i> {{ $customer->first_name }} {{ $customer->last_name }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Nome:</th>
                            <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        @if($customer->phone)
                        <tr>
                            <th>Telefone:</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Tipo:</th>
                            <td>
                                <span class="badge bg-{{ $customer->type === 'B2B' ? 'info' : 'secondary' }}">
                                    {{ $customer->type }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                                    {{ $customer->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                        </tr>
                        @if($customer->type === 'B2B')
                        <tr>
                            <th>Status B2B:</th>
                            <td>
                                @php
                                    $b2bColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $b2bLabels = [
                                        'pending' => 'Pendente',
                                        'approved' => 'Aprovado',
                                        'rejected' => 'Rejeitado'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $b2bColors[$customer->b2b_status] ?? 'secondary' }}">
                                    {{ $b2bLabels[$customer->b2b_status] ?? $customer->b2b_status }}
                                </span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Cadastrado em:</th>
                            <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última atualização:</th>
                            <td>{{ $customer->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Ações -->
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Cliente
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Informações B2B -->
        @if($customer->type === 'B2B')
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-building"></i> Informações B2B</h6>
            </div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Empresa:</th>
                            <td>{{ $customer->company_name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>CNPJ:</th>
                            <td>{{ $customer->cnpj ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Inscrição Estadual:</th>
                            <td>{{ $customer->ie ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pessoa de Contato:</th>
                            <td>{{ $customer->contact_person ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Departamento:</th>
                            <td>{{ $customer->department ?: '-' }}</td>
                        </tr>
                        @if($customer->credit_limit)
                        <tr>
                            <th>Limite de Crédito:</th>
                            <td>R$ {{ number_format($customer->credit_limit, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                @if($customer->b2b_notes)
                <div class="mt-3">
                    <h6>Observações B2B:</h6>
                    <p class="text-muted">{{ $customer->b2b_notes }}</p>
                </div>
                @endif

                <!-- Ações B2B -->
                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#b2bModal">
                        <i class="bi bi-building-gear"></i> Alterar Status B2B
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Endereço -->
        @if($customer->address)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Endereço</h6>
            </div>
            <div class="card-body">
                <p class="mb-1">{{ $customer->address }}, {{ $customer->number }}</p>
                @if($customer->complement)
                    <p class="mb-1">{{ $customer->complement }}</p>
                @endif
                <p class="mb-1">{{ $customer->neighborhood }}</p>
                <p class="mb-1">{{ $customer->city }} - {{ $customer->state }}</p>
                <p class="mb-0">CEP: {{ $customer->zip_code }}</p>
                @if($customer->country)
                    <p class="mb-0">{{ $customer->country }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Pedidos do Cliente -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-check"></i> Pedidos do Cliente
                        <span class="badge bg-info ms-2">{{ $customer->orders->count() }}</span>
                    </h5>
                    <a href="{{ route('admin.customers.orders', $customer) }}" class="btn btn-outline-primary btn-sm">
                        Ver Todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nº Pedido</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Pagamento</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->order_number }}</strong>
                                        </td>
                                        <td>
                                            <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
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
                                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$order->status] ?? $order->status }}
                                            </span>
                                        </td>
                                        <td>
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
                                            <span class="badge bg-{{ $paymentColors[$order->payment_status] ?? 'secondary' }}">
                                                {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="btn btn-outline-info btn-sm" title="Ver">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">Nenhum pedido encontrado</h5>
                        <p class="text-muted">Este cliente ainda não fez nenhum pedido.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal B2B Status -->
@if($customer->type === 'B2B')
<div class="modal fade" id="b2bModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar Status B2B</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.customers.update-b2b-status', $customer) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="b2b_status" class="form-select" required>
                            <option value="pending" {{ $customer->b2b_status === 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="approved" {{ $customer->b2b_status === 'approved' ? 'selected' : '' }}>Aprovado</option>
                            <option value="rejected" {{ $customer->b2b_status === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea name="b2b_notes" class="form-control" rows="3" 
                                  placeholder="Adicionar observações sobre o status B2B...">{{ $customer->b2b_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
