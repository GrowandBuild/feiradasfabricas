@extends('admin.layouts.app')

@section('title', 'Feedbacks de Produtos')
@section('page-title', 'Gerenciar Feedbacks')
@section('page-icon', 'bi bi-chat-heart')
@section('page-description', 'Gerencie os feedbacks dos clientes sobre os produtos.')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('admin.feedbacks.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Feedback
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.feedbacks.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="product_id" class="form-label">Produto</label>
                <select name="product_id" id="product_id" class="form-select">
                    <option value="">Todos os produtos</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todos os status</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovados</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendentes</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Feedbacks -->
<div class="card">
    <div class="card-body">
        @if($feedbacks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Autor</th>
                            <th>Conteúdo</th>
                            <th>Imagem</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feedbacks as $feedback)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.products.show', $feedback->product_id) }}" class="text-decoration-none">
                                        {{ $feedback->product->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($feedback->customer)
                                        <span class="badge bg-info">{{ $feedback->author_name }}</span>
                                        <small class="text-muted d-block">Cliente</small>
                                    @elseif($feedback->admin)
                                        <span class="badge bg-primary">{{ $feedback->author_name }}</span>
                                        <small class="text-muted d-block">Admin</small>
                                    @else
                                        <span class="text-muted">Anônimo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($feedback->text)
                                        <p class="mb-0">{{ Str::limit($feedback->text, 100) }}</p>
                                    @else
                                        <span class="text-muted">Sem texto</span>
                                    @endif
                                </td>
                                <td>
                                    @if($feedback->image)
                                        <img src="{{ $feedback->image_url }}" alt="Feedback" class="img-thumbnail" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">Sem imagem</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $feedback->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    @if($feedback->is_approved)
                                        <span class="badge bg-success">Aprovado</span>
                                    @else
                                        <span class="badge bg-warning">Pendente</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.feedbacks.show', $feedback) }}" class="btn btn-outline-info" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.feedbacks.edit', $feedback) }}" class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.feedbacks.toggle-approval', $feedback) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-{{ $feedback->is_approved ? 'warning' : 'success' }}" title="{{ $feedback->is_approved ? 'Desaprovar' : 'Aprovar' }}">
                                                <i class="bi bi-{{ $feedback->is_approved ? 'x-circle' : 'check-circle' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.feedbacks.destroy', $feedback) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este feedback?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $feedbacks->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Nenhum feedback encontrado.</p>
            </div>
        @endif
    </div>
</div>
@endsection

