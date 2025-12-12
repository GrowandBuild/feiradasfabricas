@extends('admin.layouts.app')

@section('title', 'Detalhes do Feedback')
@section('page-title', 'Detalhes do Feedback')
@section('page-icon', 'bi bi-chat-heart')
@section('page-description', 'Visualize os detalhes do feedback.')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informações do Feedback</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Produto:</strong><br>
                    <a href="{{ route('admin.products.show', $feedback->product_id) }}" class="text-decoration-none">
                        {{ $feedback->product->name }}
                    </a>
                </div>

                <div class="mb-3">
                    <strong>Autor:</strong><br>
                    @if($feedback->customer)
                        <span class="badge bg-info">{{ $feedback->author_name }}</span>
                        <small class="text-muted">(Cliente)</small>
                    @elseif($feedback->admin)
                        <span class="badge bg-primary">{{ $feedback->author_name }}</span>
                        <small class="text-muted">(Admin)</small>
                    @else
                        <span class="text-muted">Anônimo</span>
                    @endif
                </div>

                @if($feedback->text)
                    <div class="mb-3">
                        <strong>Texto:</strong>
                        <p class="mt-2">{{ $feedback->text }}</p>
                    </div>
                @endif

                @if($feedback->image)
                    <div class="mb-3">
                        <strong>Imagem:</strong><br>
                        <img src="{{ $feedback->image_url }}" alt="Feedback" class="img-fluid rounded mt-2" style="max-width: 100%; max-height: 500px; object-fit: contain;">
                    </div>
                @endif

                <div class="mb-3">
                    <strong>Status:</strong><br>
                    @if($feedback->is_approved)
                        <span class="badge bg-success">Aprovado</span>
                    @else
                        <span class="badge bg-warning">Pendente</span>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>Data de Criação:</strong><br>
                    {{ $feedback->created_at->format('d/m/Y H:i:s') }}
                </div>

                <div class="mb-3">
                    <strong>Última Atualização:</strong><br>
                    {{ $feedback->updated_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ações</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.feedbacks.edit', $feedback) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    
                    <form action="{{ route('admin.feedbacks.toggle-approval', $feedback) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $feedback->is_approved ? 'warning' : 'success' }} w-100">
                            <i class="bi bi-{{ $feedback->is_approved ? 'x-circle' : 'check-circle' }}"></i>
                            {{ $feedback->is_approved ? 'Desaprovar' : 'Aprovar' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.feedbacks.destroy', $feedback) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este feedback?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </form>

                    <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

