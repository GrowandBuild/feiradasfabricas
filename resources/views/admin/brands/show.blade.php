@extends('admin.layouts.app')

@section('title', 'Detalhes da Marca')
@section('page-title', 'Marca: ' . $brand->name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Informações da Marca -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informações da Marca</h5>
                <div>
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-width: 150px; max-height: 150px; object-fit: contain;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto"
                                 style="width: 150px; height: 150px;">
                                <i class="bi bi-tag display-4 text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <dl class="row">
                            <dt class="col-sm-3">Nome:</dt>
                            <dd class="col-sm-9">{{ $brand->name }}</dd>

                            <dt class="col-sm-3">Slug:</dt>
                            <dd class="col-sm-9"><code>{{ $brand->slug }}</code></dd>

                            <dt class="col-sm-3">Status:</dt>
                            <dd class="col-sm-9">
                                @if($brand->is_active)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">Ordem:</dt>
                            <dd class="col-sm-9">{{ $brand->sort_order }}</dd>

                            <dt class="col-sm-3">Criada em:</dt>
                            <dd class="col-sm-9">{{ $brand->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-3">Última atualização:</dt>
                            <dd class="col-sm-9">{{ $brand->updated_at->format('d/m/Y H:i') }}</dd>
                        </dl>

                        @if($brand->description)
                            <div class="mt-3">
                                <h6>Descrição:</h6>
                                <p class="text-muted">{{ $brand->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Produtos Associados -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Produtos Associados ({{ $brand->products->count() }})</h5>
                <a href="{{ route('admin.products.create') }}?brand_id={{ $brand->id }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-plus-circle"></i> Novo Produto
                </a>
            </div>
            <div class="card-body">
                @if($brand->products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>SKU</th>
                                    <th>Preço</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brand->products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->short_description)
                                                    <br><small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td><code class="small">{{ $product->sku }}</code></td>
                                        <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $product) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Ver produto">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Editar produto">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($brand->products->count() >= 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.products.index', ['brand' => $brand->id]) }}" class="btn btn-outline-primary">
                                <i class="bi bi-list"></i> Ver Todos os Produtos
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
                        <h6>Nenhum produto associado</h6>
                        <p class="text-muted mb-3">Esta marca ainda não possui produtos associados.</p>
                        <a href="{{ route('admin.products.create') }}?brand_id={{ $brand->id }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Criar Primeiro Produto
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Estatísticas -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Estatísticas</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="stats-number">{{ $brand->products()->count() }}</div>
                        <div class="stats-label">Produtos</div>
                    </div>
                    <div class="col-6">
                        <div class="stats-number">{{ $brand->products()->where('is_active', true)->count() }}</div>
                        <div class="stats-label">Ativos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Ações Rápidas</h6>
            </div>
            <div class="card-body d-grid gap-2">
                <form action="{{ route('admin.brands.toggle-active', $brand) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-{{ $brand->is_active ? 'warning' : 'success' }} w-100">
                        <i class="bi {{ $brand->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                        {{ $brand->is_active ? 'Desativar' : 'Ativar' }} Marca
                    </button>
                </form>

                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Editar Marca
                </a>

                <a href="{{ route('admin.products.create') }}?brand_id={{ $brand->id }}" class="btn btn-outline-success">
                    <i class="bi bi-plus-circle"></i> Novo Produto
                </a>

                @if($brand->products()->count() === 0)
                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST"
                          onsubmit="return confirm('Tem certeza que deseja excluir esta marca?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i> Excluir Marca
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar à Lista
                </a>
            </div>
        </div>
    </div>
</div>
@endsection