@extends('admin.layouts.app')

@section('title', 'Novo Produto')
@section('page-title', 'Criar Novo Produto')

@section('content')
<style>
/* Estilos Modernos para Create Product - SMART SEARCH */
.form-control, .form-select {
    border: 2px solid #e5e7eb !important;
    border-radius: 12px !important;
    padding: 14px 18px !important;
    font-size: 15px !important;
    font-weight: 500 !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background: #ffffff !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    color: #1f2937 !important;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea !important;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15), 0 8px 25px rgba(102, 126, 234, 0.2) !important;
    outline: none !important;
    transform: translateY(-2px) !important;
}

.form-control:hover, .form-select:hover {
    border-color: #667eea !important;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15) !important;
}

.form-label {
    font-weight: 700 !important;
    color: #1f2937 !important;
    margin-bottom: 10px !important;
    font-size: 14px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.input-group {
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
}

.input-group-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    font-weight: 700 !important;
    color: white !important;
    padding: 14px 18px !important;
}

.input-group .form-control {
    border-left: none !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

.input-group:focus-within {
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15), 0 8px 25px rgba(102, 126, 234, 0.2) !important;
}

.input-group:focus-within .input-group-text {
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%) !important;
}

textarea.form-control {
    resize: vertical !important;
    min-height: 140px !important;
    line-height: 1.6 !important;
}

textarea.form-control:focus {
    min-height: 160px !important;
}

.form-check-input {
    width: 22px !important;
    height: 22px !important;
    border: 2px solid #d1d5db !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
}

.form-check-input:checked {
    background-color: #667eea !important;
    border-color: #667eea !important;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
}

.form-check-input:hover {
    border-color: #667eea !important;
    transform: scale(1.1) !important;
}

.form-check-label {
    font-weight: 600 !important;
    color: #4b5563 !important;
    cursor: pointer !important;
    margin-left: 10px !important;
}

.card {
    border: none !important;
    border-radius: 20px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
    background: #ffffff !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    margin-bottom: 24px !important;
}

.card:hover {
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.16) !important;
    transform: translateY(-4px) !important;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    padding: 24px 28px !important;
    border-bottom: none !important;
}

.card-header h5 {
    color: white !important;
    font-weight: 800 !important;
    font-size: 20px !important;
    margin: 0 !important;
}

.card-body {
    padding: 32px 28px !important;
}

.btn {
    border-radius: 12px !important;
    padding: 14px 28px !important;
    font-weight: 700 !important;
    font-size: 15px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: none !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
}

.btn:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2) !important;
}

.btn:active {
    transform: translateY(0) !important;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
}

.btn-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
    color: white !important;
    box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4) !important;
}

.btn-outline-secondary {
    background: transparent !important;
    border: 2px solid #e5e7eb !important;
    color: #6b7280 !important;
}

.btn-outline-secondary:hover {
    background: #f3f4f6 !important;
    border-color: #d1d5db !important;
    color: #4b5563 !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.6s ease-out !important;
}

@media (max-width: 768px) {
    .form-control, .form-select {
        padding: 16px 18px !important;
        font-size: 16px !important;
    }
    
    .card-body {
        padding: 24px !important;
    }
    
    .btn {
        padding: 16px 24px !important;
        font-size: 15px !important;
    }
}
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-3"></i>Criar Novo Produto
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Informações Básicas -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-tag me-2"></i>Nome do Produto *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Digite o nome do produto">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sku" class="form-label">
                                    <i class="bi bi-upc me-2"></i>SKU *
                                </label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                       id="sku" name="sku" value="{{ old('sku') }}" required
                                       placeholder="Ex: PROD-001">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-text-paragraph me-2"></i>Descrição *
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required
                                  placeholder="Descreva detalhes importantes sobre o produto...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Preços e Estoque -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    <i class="bi bi-currency-dollar me-2"></i>Preço (B2C) *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price') }}" required
                                           placeholder="0.00">
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="b2b_price" class="form-label">
                                    <i class="bi bi-briefcase me-2"></i>Preço (B2B)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('b2b_price') is-invalid @enderror" 
                                           id="b2b_price" name="b2b_price" value="{{ old('b2b_price') }}"
                                           placeholder="0.00">
                                </div>
                                @error('b2b_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">
                                    <i class="bi bi-receipt me-2"></i>Preço de Custo
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror" 
                                           id="cost_price" name="cost_price" value="{{ old('cost_price') }}"
                                           placeholder="0.00">
                                </div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">
                                    <i class="bi bi-box-seam me-2"></i>Quantidade em Estoque *
                                </label>
                                <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                                       placeholder="0">
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_stock" class="form-label">Estoque Mínimo *</label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror" 
                                       id="min_stock" name="min_stock" value="{{ old('min_stock', 10) }}" required>
                                @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Categorias -->
                    <div class="mb-3">
                        <label class="form-label">Categorias *</label>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="categories[]" value="{{ $category->id }}" 
                                               id="category_{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('categories')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Marca -->
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Marca do Produto</label>
                        <select class="form-select @error('brand_id') is-invalid @enderror"
                                id="brand_id" name="brand_id">
                            <option value="">— Nenhuma marca selecionada —</option>
                            @php
                                $brands = \App\Models\Brand::active()->orderBy('sort_order')->orderBy('name')->get();
                            @endphp
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                        {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach

                                            <div class="mb-3">
                                                <label for="department_id" class="form-label">Departamento</label>
                                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                                    <option value="">— Nenhum departamento selecionado —</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted">Selecione o departamento do produto (opcional)</small>
                                                @error('department_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                        </select>
                        <small class="form-text text-muted">Selecione a marca do produto (opcional)</small>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Imagens -->
                    <div class="mb-3">
                        <label for="images" class="form-label">Imagens do Produto</label>
                        <input type="file" class="form-control @error('images') is-invalid @enderror" 
                               id="images" name="images[]" multiple accept="image/*">
                        @if(!empty($preselectedImages ?? []))
                            <div class="mt-2" id="preselected-images">
                                <div class="small text-muted mb-2">Imagens selecionadas do álbum:</div>
                                <div class="d-flex flex-wrap gap-2" id="preselected-images-list">
                                    @foreach($preselectedImages as $img)
                                        <div class="position-relative border rounded" data-album-image-id="{{ $img['id'] }}" style="width:100px; height:100px; overflow:hidden">
                                            <img src="{{ $img['url'] }}" class="w-100 h-100" style="object-fit:cover;" loading="lazy">
                                            <button type="button" class="btn btn-sm btn-outline-danger position-absolute remove-preselected" style="top:6px; right:6px; padding:4px 6px">Remover</button>
                                            <input type="hidden" name="existing_image_ids[]" value="{{ $img['id'] }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div id="preselected-images" class="d-none"></div>
                        @endif
                        <div class="form-text">Você pode selecionar múltiplas imagens. Formatos aceitos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)</div>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informações Adicionais (Marca removida) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="model" class="form-label">Modelo</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model') }}">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dimensões -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="weight" class="form-label">Peso (kg)</label>
                                <input type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror" 
                                       id="weight" name="weight" value="{{ old('weight') }}">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="length" class="form-label">Comprimento (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('length') is-invalid @enderror" 
                                       id="length" name="length" value="{{ old('length') }}">
                                @error('length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="width" class="form-label">Largura (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('width') is-invalid @enderror" 
                                       id="width" name="width" value="{{ old('width') }}">
                                @error('width')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="height" class="form-label">Altura (cm)</label>
                                <input type="number" step="0.01" class="form-control @error('height') is-invalid @enderror" 
                                       id="height" name="height" value="{{ old('height') }}">
                                @error('height')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Dicas</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Informações Importantes</h6>
                    <ul class="mb-0">
                        <li>O SKU deve ser único para cada produto</li>
                        <li>Selecione pelo menos uma categoria</li>
                        <li>O preço B2B é opcional</li>
                        <li>As imagens devem ter boa qualidade</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // remove preselected album image before form submit
            document.querySelectorAll('.remove-preselected').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    const wrapper = btn.closest('[data-album-image-id]');
                    if (!wrapper) return;
                    // remove the hidden input and the preview
                    const input = wrapper.querySelector('input[name="existing_image_ids[]"]');
                    if (input) input.remove();
                    wrapper.remove();
                    // if list becomes empty hide container
                    const list = document.getElementById('preselected-images-list');
                    if (list && list.children.length === 0) {
                        const container = document.getElementById('preselected-images');
                        if (container) container.classList.add('d-none');
                    }
                });
            });
        });
    </script>
    @endpush
