@extends('admin.layouts.app')

@section('title', 'Detalhes do Cupom')
@section('page-title', 'Detalhes do Cupom')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Informações do Cupom -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-ticket-perforated"></i> Informações do Cupom</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary">{{ $coupon->name }}</h5>
                        <p class="text-muted">{{ $coupon->description }}</p>
                        
                        <div class="mb-3">
                            <strong>Código:</strong>
                            <code class="bg-light px-2 py-1 rounded ms-2">{{ $coupon->code }}</code>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Tipo:</strong>
                            <span class="badge bg-{{ $coupon->type === 'percentage' ? 'info' : 'warning' }} ms-2">
                                {{ $coupon->type === 'percentage' ? 'Porcentagem' : 'Valor Fixo' }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Valor:</strong>
                            @if($coupon->type === 'percentage')
                                <span class="text-primary fw-bold">{{ $coupon->value }}%</span>
                            @else
                                <span class="text-primary fw-bold">R$ {{ number_format($coupon->value, 2, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Status:</strong>
                            @if($coupon->is_active)
                                @if($coupon->expires_at && $coupon->expires_at < now())
                                    <span class="badge bg-danger ms-2">Expirado</span>
                                @elseif($coupon->starts_at && $coupon->starts_at > now())
                                    <span class="badge bg-warning ms-2">Aguardando</span>
                                @else
                                    <span class="badge bg-success ms-2">Ativo</span>
                                @endif
                            @else
                                <span class="badge bg-secondary ms-2">Inativo</span>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <strong>Uso:</strong>
                            <span class="badge bg-secondary ms-2">
                                {{ $coupon->usages->count() }} / {{ $coupon->usage_limit ?: '∞' }}
                            </span>
                        </div>
                        
                        @if($coupon->minimum_amount)
                            <div class="mb-3">
                                <strong>Valor Mínimo:</strong>
                                <span class="text-muted ms-2">R$ {{ number_format($coupon->minimum_amount, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <strong>Tipo de Cliente:</strong>
                            <span class="badge bg-info ms-2">
                                {{ ucfirst($coupon->customer_type) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        @if($coupon->starts_at)
                            <div class="mb-3">
                                <strong>Início:</strong>
                                <span class="text-muted ms-2">{{ $coupon->starts_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        
                        @if($coupon->expires_at)
                            <div class="mb-3">
                                <strong>Expiração:</strong>
                                <span class="text-muted ms-2">{{ $coupon->expires_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Criado em:</strong>
                            <span class="text-muted ms-2">{{ $coupon->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Atualizado em:</strong>
                            <span class="text-muted ms-2">{{ $coupon->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de Uso -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-clock-history"></i> Histórico de Uso</h6>
            </div>
            <div class="card-body">
                @if($coupon->usages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Pedido</th>
                                    <th>Desconto</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupon->usages as $usage)
                                    <tr>
                                        <td>
                                            @if($usage->customer)
                                                <strong>{{ $usage->customer->first_name }} {{ $usage->customer->last_name }}</strong>
                                                <br><small class="text-muted">{{ $usage->customer->email }}</small>
                                            @else
                                                <span class="text-muted">Cliente não encontrado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($usage->order)
                                                <a href="{{ route('admin.orders.show', $usage->order) }}" class="text-decoration-none">
                                                    #{{ $usage->order->order_number }}
                                                </a>
                                            @else
                                                <span class="text-muted">Pedido não encontrado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                -R$ {{ number_format($usage->discount_amount, 2, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $usage->used_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-ticket-perforated display-4 text-muted"></i>
                        <h5 class="text-muted mt-3">Nenhum uso registrado</h5>
                        <p class="text-muted">Este cupom ainda não foi utilizado por nenhum cliente.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Ações -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Ações</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar Cupom
                    </a>
                    
                    <form action="{{ route('admin.coupons.toggle-active', $coupon) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $coupon->is_active ? 'secondary' : 'success' }} w-100">
                            <i class="bi bi-{{ $coupon->is_active ? 'pause' : 'play' }}"></i> 
                            {{ $coupon->is_active ? 'Desativar' : 'Ativar' }} Cupom
                        </button>
                    </form>
                    
                    @if($coupon->usages->count() === 0)
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" 
                              onsubmit="return confirm('Tem certeza que deseja excluir este cupom?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Excluir Cupom
                            </button>
                        </form>
                    @else
                        <button class="btn btn-danger w-100" disabled title="Não é possível excluir um cupom que já foi utilizado">
                            <i class="bi bi-trash"></i> Excluir Cupom
                        </button>
                    @endif
                    
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar à Lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Estatísticas</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $coupon->usages->count() }}</h4>
                        <small class="text-muted">Usos</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">
                            R$ {{ number_format($coupon->usages->sum('discount_amount'), 2, ',', '.') }}
                        </h4>
                        <small class="text-muted">Desconto Total</small>
                    </div>
                </div>
                
                @if($coupon->usage_limit)
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <small>Progresso</small>
                            <small>{{ $coupon->usages->count() }}/{{ $coupon->usage_limit }}</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" 
                                 style="width: {{ ($coupon->usages->count() / $coupon->usage_limit) * 100 }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
