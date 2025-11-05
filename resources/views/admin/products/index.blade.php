@extends('admin.layouts.app')

@section('title', 'Produtos')
@section('page-title', 'Gerenciar Produtos')
@section('page-subtitle')
    <p class="text-muted mb-0">Gerencie todos os produtos da loja</p>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="rounded-circle p-3 me-3" style="background-color: rgba(249, 115, 22, 0.1);">
            <i class="bi bi-box-seam" style="font-size: 1.5rem; color: var(--accent-color);"></i>
        </div>
        <div>
            <h4 class="mb-0">Produtos</h4>
            <p class="text-muted mb-0">{{ $products->total() }} produtos cadastrados</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.import') }}" class="btn btn-outline-primary">
            <i class="bi bi-upload me-1"></i> Importar
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Produto
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center">
        <i class="bi bi-funnel me-2" style="color: var(--accent-color);"></i>
        <h6 class="mb-0">Filtros e Busca</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-search me-1"></i>Buscar
                </label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Nome, SKU ou descrição">
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-award me-1"></i>Marca
                </label>
                <select name="brand" class="form-select">
                    <option value="">Todas</option>
                    @php
                        $brands = \App\Models\Product::distinct()->pluck('brand')->filter()->sort();
                    @endphp
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                            {{ $brand }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-tags me-1"></i>Categoria
                </label>
                <select name="category" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-toggle-on me-1"></i>Status
                </label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">
                    <i class="bi bi-boxes me-1"></i>Estoque
                </label>
                <select name="stock_status" class="form-select">
                    <option value="">Todos</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Estoque Baixo</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Sem Estoque</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        
        @if(request()->hasAny(['search', 'brand', 'category', 'status', 'stock_status']))
            <div class="mt-3 d-flex align-items-center">
                <span class="text-muted me-2">Filtros ativos:</span>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Limpar filtros
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Lista de Produtos -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">
                                <i class="bi bi-image text-muted"></i>
                            </th>
                            <th>Produto</th>
                            <th>Marca</th>
                            <th>SKU</th>
                            <th>Preço</th>
                            <th class="text-center">Estoque</th>
                            <th>Categorias</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td class="text-center">
                                    @if($product->first_image)
                                        <img src="{{ $product->first_image }}" 
                                             alt="{{ $product->name }}" 
                                             class="rounded shadow-sm" 
                                             style="width: 60px; height: 60px; object-fit: cover;"
                                             onerror="this.onerror=null; this.src='{{ asset('images/no-image.svg') }}';">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="bi bi-image" style="font-size: 1.5rem; color: #fd7e14;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $product->name }}</h6>
                                            @if($product->is_featured)
                                                <div class="mt-1">
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-star-fill me-1"></i>Destaque
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($product->brand)
                                        <span class="badge bg-primary">
                                            <i class="bi bi-award me-1"></i>{{ $product->brand }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $product->sku }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold text-success">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </div>
                                    @if($product->b2b_price)
                                        <small class="text-muted">
                                            B2B: R$ {{ number_format($product->b2b_price, 2, ',', '.') }}
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-semibold">{{ $product->current_stock }}</span>
                                        @if($product->isLowStock())
                                            <span class="badge bg-danger mt-1">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Baixo
                                            </span>
                                        @elseif($product->current_stock == 0)
                                            <span class="badge bg-secondary mt-1">
                                                <i class="bi bi-x-circle me-1"></i>Sem Estoque
                                            </span>
                                        @else
                                            <span class="badge bg-success mt-1">
                                                <i class="bi bi-check-circle me-1"></i>OK
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @forelse($product->categories as $category)
                                        <span class="badge bg-light text-dark me-1 mb-1">
                                            <i class="bi bi-tag me-1"></i>{{ $category->name }}
                                        </span>
                                    @empty
                                        <span class="text-muted">Sem categoria</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                        <i class="bi bi-{{ $product->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                        {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.products.show', $product) }}" 
                                           class="btn btn-outline-info" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
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

            <!-- Paginação -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="mb-3 mb-md-0">
                    <p class="text-muted mb-0">
                        Mostrando <strong>{{ $products->firstItem() ?? 0 }}</strong> a <strong>{{ $products->lastItem() ?? 0 }}</strong> de <strong>{{ $products->total() }}</strong> resultados
                    </p>
                </div>
                <div>
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                     style="width: 100px; height: 100px;">
                    <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-muted mb-3">Nenhum produto encontrado</h5>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'brand', 'category', 'status', 'stock_status']))
                        Nenhum produto corresponde aos filtros aplicados.
                    @else
                        Comece criando seu primeiro produto para a loja.
                    @endif
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    @if(request()->hasAny(['search', 'brand', 'category', 'status', 'stock_status']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Limpar Filtros
                        </a>
                    @endif
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Criar Produto
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
