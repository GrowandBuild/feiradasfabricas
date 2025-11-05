@extends('admin.layouts.app')

@section('title', 'Visualizar Categoria')
@section('page-title', 'Detalhes da Categoria')

@section('content')
<div class="row">
    <!-- Informações da Categoria -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-tag"></i> Informações da Categoria
                </h5>
            </div>
            <div class="card-body">
                <!-- Imagem -->
                @if($category->image)
                <div class="text-center mb-3">
                    <img src="{{ asset('storage/' . $category->image) }}" 
                         alt="{{ $category->name }}" 
                         class="img-fluid rounded"
                         style="max-height: 300px;">
                </div>
                @else
                <div class="text-center mb-3 bg-light rounded py-5">
                    <i class="bi bi-tag" style="font-size: 4rem; color: #ccc;"></i>
                </div>
                @endif

                <!-- Informações -->
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Nome:</th>
                            <td>{{ $category->name }}</td>
                        </tr>
                        <tr>
                            <th>Slug:</th>
                            <td><code>{{ $category->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                    {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Ordem:</th>
                            <td><span class="badge bg-secondary">{{ $category->sort_order ?? 0 }}</span></td>
                        </tr>
                        <tr>
                            <th>Produtos:</th>
                            <td>
                                <span class="badge bg-info">{{ $category->products->count() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Criada em:</th>
                            <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Atualizada em:</th>
                            <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>

                @if($category->description)
                <div class="mt-3">
                    <h6>Descrição:</h6>
                    <p class="text-muted">{{ $category->description }}</p>
                </div>
                @endif

                <!-- Ações -->
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Categoria
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos da Categoria -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-box"></i> Produtos nesta Categoria
                    <span class="badge bg-info ms-2">{{ $category->products->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($category->products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Imagem</th>
                                    <th>Produto</th>
                                    <th>SKU</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->products as $product)
                                    <tr>
                                        <td>
                                            @if($product->first_image)
                                                <img src="{{ asset('storage/' . $product->first_image) }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="rounded" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                @if($product->is_featured)
                                                    <span class="badge bg-warning ms-1">Destaque</span>
                                                @endif
                                            </div>
                                            @if($product->brand)
                                                <small class="text-muted">{{ $product->brand }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $product->sku }}</code>
                                        </td>
                                        <td>
                                            <strong>R$ {{ number_format($product->price, 2, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $product->current_stock }}</span>
                                                @if($product->current_stock == 0)
                                                    <span class="badge bg-secondary">Sem Estoque</span>
                                                @elseif($product->isLowStock())
                                                    <span class="badge bg-danger">Baixo</span>
                                                @else
                                                    <span class="badge bg-success">OK</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                                {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.show', $product) }}" 
                                                   class="btn btn-outline-info btn-sm" title="Ver">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" 
                                                   class="btn btn-outline-primary btn-sm" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box" style="font-size: 3rem; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">Nenhum produto nesta categoria</h5>
                        <p class="text-muted">Adicione produtos a esta categoria através da página de edição de produtos.</p>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                            <i class="bi bi-box"></i> Ver Produtos
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

