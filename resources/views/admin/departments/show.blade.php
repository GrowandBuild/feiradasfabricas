@extends('admin.layouts.app')

@section('title', 'Departamento: ' . $department->name)
@section('page-title', $department->name)
@section('page-subtitle')
    <p class="text-muted mb-0">Detalhes do departamento</p>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Informações Básicas -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Informações do Departamento
                </h5>
                <div>
                    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Nome</h6>
                        <p class="text-muted">{{ $department->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Slug</h6>
                        <p class="text-muted"><code>{{ $department->slug }}</code></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Ícone</h6>
                        <p class="text-muted">
                            <i class="{{ $department->icon ?? 'fas fa-folder' }}" style="color: {{ $department->color }}; font-size: 1.5rem;"></i>
                            <span class="ms-2">{{ $department->icon ?? 'Nenhum ícone definido' }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Cor Principal</h6>
                        <p class="text-muted">
                            <div class="color-preview me-2" style="width: 30px; height: 30px; background-color: {{ $department->color }}; border-radius: 5px; border: 1px solid #ddd; display: inline-block;"></div>
                            <span>{{ $department->color }}</span>
                        </p>
                    </div>
                </div>

                @if($department->description)
                    <div class="row">
                        <div class="col-12">
                            <h6>Descrição</h6>
                            <p class="text-muted">{{ $department->description }}</p>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <h6>Status</h6>
                        <p class="text-muted">
                            @if($department->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <h6>Ordem de Exibição</h6>
                        <p class="text-muted">{{ $department->sort_order }}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>Criado em</h6>
                        <p class="text-muted">{{ $department->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produtos do Departamento -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box"></i> Produtos ({{ $department->products->count() }})
                </h5>
                <a href="{{ route('admin.products.create', ['department_id' => $department->id]) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Novo Produto
                </a>
            </div>
            <div class="card-body">
                @if($department->products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>SKU</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->products->take(10) as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($product->first_image)
                                                    <img src="{{ $product->first_image }}" alt="{{ $product->name }}" 
                                                         class="me-2" style="width: 30px; height: 30px; object-fit: cover; border-radius: 3px;">
                                                @endif
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    @if($product->is_featured)
                                                        <span class="badge bg-warning ms-1">Destaque</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td><code>{{ $product->sku }}</code></td>
                                        <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                        <td>{{ $product->stock_quantity }}</td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($department->products->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.products.index', ['department_id' => $department->id]) }}" class="btn btn-outline-primary">
                                Ver Todos os {{ $department->products->count() }} Produtos
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-box fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum produto encontrado</h5>
                        <p class="text-muted">Este departamento ainda não possui produtos.</p>
                        <a href="{{ route('admin.products.create', ['department_id' => $department->id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Adicionar Primeiro Produto
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Categorias do Departamento -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tags"></i> Categorias ({{ $department->categories->count() }})
                </h5>
                <a href="{{ route('admin.categories.create', ['department_id' => $department->id]) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Nova Categoria
                </a>
            </div>
            <div class="card-body">
                @if($department->categories->count() > 0)
                    <div class="row">
                        @foreach($department->categories as $category)
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $category->name }}</h6>
                                        @if($category->description)
                                            <p class="card-text text-muted small">{{ $category->description }}</p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $category->products()->count() }} produtos</small>
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma categoria encontrada</h5>
                        <p class="text-muted">Este departamento ainda não possui categorias.</p>
                        <a href="{{ route('admin.categories.create', ['department_id' => $department->id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Adicionar Primeira Categoria
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Estatísticas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart"></i> Estatísticas
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3 class="text-primary">{{ $department->products->count() }}</h3>
                        <small class="text-muted">Produtos</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="text-info">{{ $department->categories->count() }}</h3>
                        <small class="text-muted">Categorias</small>
                    </div>
                </div>
                
                <hr>
                
                <h6>Produtos por Status</h6>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Ativos</span>
                        <span class="badge bg-success">{{ $department->products->where('is_active', true)->count() }}</span>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Inativos</span>
                        <span class="badge bg-secondary">{{ $department->products->where('is_active', false)->count() }}</span>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>Em Destaque</span>
                        <span class="badge bg-warning">{{ $department->products->where('is_featured', true)->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning"></i> Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.create', ['department_id' => $department->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Produto
                    </a>
                    <a href="{{ route('admin.categories.create', ['department_id' => $department->id]) }}" class="btn btn-info">
                        <i class="bi bi-tags"></i> Nova Categoria
                    </a>
                    <a href="{{ route('department.index', $department->slug) }}" class="btn btn-success" target="_blank">
                        <i class="bi bi-eye"></i> Ver no Site
                    </a>
                </div>
            </div>
        </div>

        <!-- Informações Técnicas -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-gear"></i> Informações Técnicas
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>ID:</strong> <code>{{ $department->id }}</code>
                </div>
                <div class="mb-2">
                    <strong>Slug:</strong> <code>{{ $department->slug }}</code>
                </div>
                <div class="mb-2">
                    <strong>Criado em:</strong> {{ $department->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="mb-2">
                    <strong>Atualizado em:</strong> {{ $department->updated_at->format('d/m/Y H:i') }}
                </div>
                @if($department->settings)
                    <div class="mb-2">
                        <strong>Configurações:</strong>
                        <pre class="small text-muted">{{ json_encode($department->settings, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
