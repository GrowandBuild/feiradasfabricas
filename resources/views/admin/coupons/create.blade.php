@extends('admin.layouts.app')

@section('title', 'Novo Cupom')
@section('page-title', 'Criar Novo Cupom')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Informações do Cupom</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.coupons.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Código do Cupom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="Ex: DESCONTO10" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Código único que os clientes usarão para aplicar o desconto.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome do Cupom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Ex: Desconto de 10%" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Descrição opcional do cupom">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipo de Desconto <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Porcentagem</option>
                                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="value" class="form-label">Valor <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                           id="value" name="value" value="{{ old('value') }}" 
                                           step="0.01" min="0" required>
                                    <span class="input-group-text" id="value-suffix">%</span>
                                </div>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_amount" class="form-label">Valor Mínimo</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                           id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount') }}" 
                                           step="0.01" min="0">
                                </div>
                                @error('minimum_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Valor mínimo do pedido para usar o cupom.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">Limite de Uso</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" 
                                       min="1" placeholder="Deixe vazio para ilimitado">
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Número máximo de vezes que o cupom pode ser usado.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_type" class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                                <select class="form-select @error('customer_type') is-invalid @enderror" 
                                        id="customer_type" name="customer_type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="all" {{ old('customer_type') === 'all' ? 'selected' : '' }}>Todos</option>
                                    <option value="b2c" {{ old('customer_type') === 'b2c' ? 'selected' : '' }}>B2C</option>
                                    <option value="b2b" {{ old('customer_type') === 'b2b' ? 'selected' : '' }}>B2B</option>
                                </select>
                                @error('customer_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="starts_at" class="form-label">Data de Início</label>
                                <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                                       id="starts_at" name="starts_at" value="{{ old('starts_at') }}">
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para ativar imediatamente.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">Data de Expiração</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para não expirar.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Cupom ativo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Cupom
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Dicas</h6>
            </div>
            <div class="card-body">
                <h6>Código do Cupom</h6>
                <p class="small text-muted">Use códigos únicos e fáceis de lembrar. Evite espaços e caracteres especiais.</p>
                
                <h6>Tipo de Desconto</h6>
                <p class="small text-muted">
                    <strong>Porcentagem:</strong> Desconto baseado em % do valor total<br>
                    <strong>Valor Fixo:</strong> Desconto de valor fixo em reais
                </p>
                
                <h6>Valor Mínimo</h6>
                <p class="small text-muted">Define o valor mínimo que o pedido deve ter para o cupom ser aplicado.</p>
                
                <h6>Limite de Uso</h6>
                <p class="small text-muted">Controle quantas vezes o cupom pode ser usado. Deixe vazio para ilimitado.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueSuffix = document.getElementById('value-suffix');
    
    function updateValueSuffix() {
        if (typeSelect.value === 'percentage') {
            valueSuffix.textContent = '%';
        } else if (typeSelect.value === 'fixed') {
            valueSuffix.textContent = 'R$';
        }
    }
    
    typeSelect.addEventListener('change', updateValueSuffix);
    updateValueSuffix(); // Initialize on page load
});
</script>
@endsection
