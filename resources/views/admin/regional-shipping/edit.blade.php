@extends('admin.layouts.app')

@section('title', 'Editar Região de Entrega')
@section('page-title', 'Editar Região de Entrega')

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informações da Região</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.regional-shipping.update', $region->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Informações Básicas -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Informações Básicas</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome da Região <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $region->name) }}" 
                                       placeholder="Ex: Centro, Zona Norte, Região Metropolitana" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Ordem de Prioridade</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $region->sort_order) }}" 
                                       min="0" placeholder="0">
                                <small class="form-text text-muted">Menor número = maior prioridade</small>
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="2"
                                      placeholder="Descrição opcional da região">{{ old('description', $region->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Configuração de CEPs -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Área de Cobertura (CEP)</h6>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Configure a área de cobertura:</strong> Use range de CEPs (início e fim) OU lista de CEPs específicos separados por vírgula.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cep_start" class="form-label">CEP Inicial (Range)</label>
                                <input type="text" class="form-control @error('cep_start') is-invalid @enderror"
                                       id="cep_start" name="cep_start" value="{{ old('cep_start', $region->cep_start) }}" 
                                       placeholder="70000000" maxlength="8">
                                <small class="form-text text-muted">Apenas números (8 dígitos)</small>
                                @error('cep_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cep_end" class="form-label">CEP Final (Range)</label>
                                <input type="text" class="form-control @error('cep_end') is-invalid @enderror"
                                       id="cep_end" name="cep_end" value="{{ old('cep_end', $region->cep_end) }}" 
                                       placeholder="70999999" maxlength="8">
                                <small class="form-text text-muted">Apenas números (8 dígitos)</small>
                                @error('cep_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cep_list" class="form-label">OU Lista de CEPs Específicos</label>
                            <input type="text" class="form-control @error('cep_list') is-invalid @enderror"
                                   id="cep_list" name="cep_list" 
                                   value="{{ old('cep_list', $region->cep_list_display ?? '') }}" 
                                   placeholder="74673030, 74673040, 74673050">
                            <small class="form-text text-muted">Separe por vírgula (ex: 74673030, 74673040)</small>
                            @error('cep_list')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Configuração de Preços -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Precificação</h6>
                        
                        <div class="mb-3">
                            <label for="pricing_type" class="form-label">Tipo de Precificação <span class="text-danger">*</span></label>
                            <select class="form-select @error('pricing_type') is-invalid @enderror" 
                                    id="pricing_type" name="pricing_type" required>
                                <option value="fixed" {{ old('pricing_type', $region->pricing_type) == 'fixed' ? 'selected' : '' }}>Preço Fixo</option>
                                <option value="per_weight" {{ old('pricing_type', $region->pricing_type) == 'per_weight' ? 'selected' : '' }}>Por Peso (R$/kg)</option>
                                <option value="per_item" {{ old('pricing_type', $region->pricing_type) == 'per_item' ? 'selected' : '' }}>Por Item (R$/unidade)</option>
                            </select>
                            @error('pricing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campos dinâmicos baseados no tipo -->
                        <div id="pricing-fields">
                            <!-- Preço Fixo -->
                            <div class="row pricing-field" data-type="fixed" style="display: {{ old('pricing_type', $region->pricing_type) == 'fixed' ? 'block' : 'none' }};">
                                <div class="col-md-6 mb-3">
                                    <label for="fixed_price" class="form-label">Preço Fixo (R$) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('fixed_price') is-invalid @enderror"
                                           id="fixed_price" name="fixed_price" 
                                           value="{{ old('fixed_price', $region->fixed_price) }}" placeholder="0.00">
                                    @error('fixed_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Por Peso -->
                            <div class="row pricing-field" data-type="per_weight" style="display: {{ old('pricing_type', $region->pricing_type) == 'per_weight' ? 'block' : 'none' }};">
                                <div class="col-md-6 mb-3">
                                    <label for="price_per_kg" class="form-label">Preço por kg (R$) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('price_per_kg') is-invalid @enderror"
                                           id="price_per_kg" name="price_per_kg" 
                                           value="{{ old('price_per_kg', $region->price_per_kg) }}" placeholder="0.00">
                                    @error('price_per_kg')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Por Item -->
                            <div class="row pricing-field" data-type="per_item" style="display: {{ old('pricing_type', $region->pricing_type) == 'per_item' ? 'block' : 'none' }};">
                                <div class="col-md-6 mb-3">
                                    <label for="price_per_item" class="form-label">Preço por item (R$) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('price_per_item') is-invalid @enderror"
                                           id="price_per_item" name="price_per_item" 
                                           value="{{ old('price_per_item', $region->price_per_item) }}" placeholder="0.00">
                                    @error('price_per_item')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Limites de Preço -->
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label for="min_price" class="form-label">Preço Mínimo (R$)</label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('min_price') is-invalid @enderror"
                                       id="min_price" name="min_price" 
                                       value="{{ old('min_price', $region->min_price) }}" placeholder="0.00">
                                <small class="form-text text-muted">Valor mínimo que será cobrado</small>
                                @error('min_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_price" class="form-label">Preço Máximo (R$)</label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('max_price') is-invalid @enderror"
                                       id="max_price" name="max_price" 
                                       value="{{ old('max_price', $region->max_price) }}" placeholder="0.00">
                                <small class="form-text text-muted">Valor máximo que será cobrado</small>
                                @error('max_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Prazo de Entrega -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Prazo de Entrega</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="delivery_days_min" class="form-label">Prazo Mínimo (dias) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('delivery_days_min') is-invalid @enderror"
                                       id="delivery_days_min" name="delivery_days_min" 
                                       value="{{ old('delivery_days_min', $region->delivery_days_min) }}" min="1" max="60" required>
                                @error('delivery_days_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="delivery_days_max" class="form-label">Prazo Máximo (dias)</label>
                                <input type="number" class="form-control @error('delivery_days_max') is-invalid @enderror"
                                       id="delivery_days_max" name="delivery_days_max" 
                                       value="{{ old('delivery_days_max', $region->delivery_days_max) }}" min="1" max="60">
                                <small class="form-text text-muted">Deixe vazio para usar apenas o mínimo</small>
                                @error('delivery_days_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', $region->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Região Ativa
                            </label>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.regional-shipping.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Atualizar Região
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CEPs
    const cepInputs = ['cep_start', 'cep_end'];
    cepInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '').slice(0, 8);
                e.target.value = value;
            });
        }
    });

    // Máscara para lista de CEPs
    const cepListInput = document.getElementById('cep_list');
    if (cepListInput) {
        cepListInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d,]/g, '');
            e.target.value = value;
        });
    }

    // Mostrar/ocultar campos de precificação baseado no tipo
    const pricingType = document.getElementById('pricing_type');
    if (pricingType) {
        pricingType.addEventListener('change', function() {
            const selectedType = this.value;
            document.querySelectorAll('.pricing-field').forEach(field => {
                if (field.dataset.type === selectedType) {
                    field.style.display = 'block';
                    // Tornar obrigatório
                    const input = field.querySelector('input');
                    if (input) input.required = true;
                } else {
                    field.style.display = 'none';
                    // Remover obrigatório
                    const input = field.querySelector('input');
                    if (input) input.required = false;
                }
            });
        });
    }
});
</script>
@endpush
@endsection



