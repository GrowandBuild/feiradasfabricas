@extends('admin.layouts.app')

@section('title', 'Editar Cupom')
@section('page-title', 'Editar Cupom')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-pencil"></i> Editar Cupom</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Código do Cupom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $coupon->code) }}" 
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
                                       id="name" name="name" value="{{ old('name', $coupon->name) }}" 
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
                                  placeholder="Descrição opcional do cupom">{{ old('description', $coupon->description) }}</textarea>
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
                                    <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Porcentagem</option>
                                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
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
                                           id="value" name="value" value="{{ old('value', $coupon->value) }}" 
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
                                           id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount) }}" 
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
                                       id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" 
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
                                    <option value="all" {{ old('customer_type', $coupon->customer_type) === 'all' ? 'selected' : '' }}>Todos</option>
                                    <option value="b2c" {{ old('customer_type', $coupon->customer_type) === 'b2c' ? 'selected' : '' }}>B2C</option>
                                    <option value="b2b" {{ old('customer_type', $coupon->customer_type) === 'b2b' ? 'selected' : '' }}>B2B</option>
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
                                       id="starts_at" name="starts_at" 
                                       value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
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
                                       id="expires_at" name="expires_at" 
                                       value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
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
                                   {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Cupom ativo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informações do Cupom</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Criado em:</strong>
                    <br><small class="text-muted">{{ $coupon->created_at->format('d/m/Y H:i') }}</small>
                </div>
                
                <div class="mb-3">
                    <strong>Última atualização:</strong>
                    <br><small class="text-muted">{{ $coupon->updated_at->format('d/m/Y H:i') }}</small>
                </div>
                
                <div class="mb-3">
                    <strong>Usos atuais:</strong>
                    <br><span class="badge bg-secondary">{{ $coupon->usages->count() }} / {{ $coupon->usage_limit ?: '∞' }}</span>
                </div>
                
                @if($coupon->usages->count() > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Este cupom já foi utilizado {{ $coupon->usages->count() }} vez(es). 
                        Alterações podem afetar clientes que já usaram o cupom.
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Dicas</h6>
            </div>
            <div class="card-body">
                <h6>Alterações de Código</h6>
                <p class="small text-muted">Cuidado ao alterar o código de um cupom que já foi usado. Clientes podem ter o código salvo.</p>
                
                <h6>Valor do Desconto</h6>
                <p class="small text-muted">Para cupons percentuais, o valor não pode ser maior que 100%.</p>
                
                <h6>Data de Expiração</h6>
                <p class="small text-muted">Cupons expirados não podem ser usados, mesmo que estejam ativos.</p>
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
