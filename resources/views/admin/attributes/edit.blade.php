@extends('admin.layouts.app')

@section('title', 'Editar Atributo')
@section('page-title', 'Editar Atributo')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Editar Atributo: {{ $attribute->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Atributo *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $attribute->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipo *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required disabled>
                                    <option value="color" {{ $attribute->type === 'color' ? 'selected' : '' }}>Cor</option>
                                    <option value="size" {{ $attribute->type === 'size' ? 'selected' : '' }}>Tamanho</option>
                                    <option value="text" {{ $attribute->type === 'text' ? 'selected' : '' }}>Texto</option>
                                    <option value="number" {{ $attribute->type === 'number' ? 'selected' : '' }}>Número</option>
                                    <option value="image" {{ $attribute->type === 'image' ? 'selected' : '' }}>Imagem</option>
                                </select>
                                <small class="text-muted">O tipo não pode ser alterado após criação.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Ordem</label>
                                <input type="number" class="form-control" 
                                       id="sort_order" name="sort_order" 
                                       value="{{ old('sort_order', $attribute->sort_order) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', $attribute->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Ativo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Gerenciar Valores -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Valores do Atributo</h5>
            </div>
            <div class="card-body">
                <div id="values-list">
                    @foreach($attribute->allValues as $value)
                        <div class="value-item mb-3 p-3 border rounded" data-value-id="{{ $value->id }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-grow-1">
                                    <strong>{{ $value->value }}</strong>
                                    @if($value->display_value && $value->display_value !== $value->value)
                                        <small class="text-muted">({{ $value->display_value }})</small>
                                    @endif
                                    @if($attribute->type === 'color' && $value->color_hex)
                                        <span class="color-preview ms-2" style="background-color: {{ $value->color_hex }};"></span>
                                    @endif
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-value" 
                                            data-value-id="{{ $value->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-value" 
                                            data-value-id="{{ $value->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Ordem: {{ $value->sort_order }}</small>
                            @if(!$value->is_active)
                                <span class="badge bg-danger ms-2">Inativo</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="add-value-btn">
                    <i class="bi bi-plus-circle"></i> Adicionar Novo Valor
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Gerencie os valores deste atributo. Valores podem ser adicionados, editados ou removidos.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar/Editar Valor -->
<div class="modal fade" id="valueModal" tabindex="-1" aria-labelledby="valueModalLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="valueModalTitle">Adicionar Valor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="valueForm">
                <div class="modal-body">
                    <input type="hidden" id="value_id" name="value_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Valor *</label>
                        <input type="text" class="form-control" id="value_value" name="value" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Valor de Exibição</label>
                        <input type="text" class="form-control" id="value_display_value" name="display_value">
                    </div>
                    
                    @if($attribute->type === 'color')
                    <div class="mb-3">
                        <label class="form-label">Cor (Hex) *</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="value_color_hex_color">
                            <input type="text" class="form-control" id="value_color_hex" name="color_hex" 
                                   pattern="^#[0-9A-Fa-f]{6}$" required>
                        </div>
                    </div>
                    @endif
                    
                    @if($attribute->type === 'image')
                    <div class="mb-3">
                        <label class="form-label">URL da Imagem</label>
                        <input type="url" class="form-control" id="value_image_url" name="image_url">
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" class="form-control" id="value_sort_order" name="sort_order" min="0" value="0">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="value_is_active" name="is_active" checked>
                        <label class="form-check-label" for="value_is_active">Ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .color-preview {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        display: inline-block;
        vertical-align: middle;
    }

    /* Corrigir z-index do modal */
    #valueModal {
        z-index: 1055 !important;
    }

    /* Garantir que o modal fique acima de tudo */
    .modal.show {
        display: block !important;
    }

    /* Remover backdrop completamente */
    .modal-backdrop {
        display: none !important;
    }

    .modal-dialog {
        margin: 1.75rem auto;
        max-width: 500px;
    }

    /* Garantir que o modal-dialog seja clicável */
    .modal-dialog {
        pointer-events: auto;
        position: relative;
    }

    .modal-content {
        pointer-events: auto;
    }

    /* Garantir que elementos dentro do modal sejam clicáveis */
    .modal-content * {
        pointer-events: auto;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('valueModal');
    const modal = new bootstrap.Modal(modalElement, {
        backdrop: false,
        keyboard: true,
        focus: true
    });
    const form = document.getElementById('valueForm');
    const addBtn = document.getElementById('add-value-btn');
    const attributeId = {{ $attribute->id }};

    // Garantir que o modal fique acima de tudo
    modalElement.style.zIndex = '1055';
    
    // Remover qualquer backdrop que possa aparecer
    modalElement.addEventListener('show.bs.modal', function() {
        // Remover backdrop se existir
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    });

    // Corrigir posicionamento quando abrir
    modalElement.addEventListener('shown.bs.modal', function() {
        // Garantir que o modal fique visível e centralizado
        modalElement.style.display = 'block';
        modalElement.style.paddingLeft = '0';
        
        // Garantir que o modal-dialog esteja centralizado
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.margin = '1.75rem auto';
        }
        
        // Remover backdrop se aparecer
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    });

    // Limpar quando fechar
    modalElement.addEventListener('hidden.bs.modal', function() {
        // Remover backdrop se existir
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        // Remover classe do body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    addBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        document.getElementById('valueModalTitle').textContent = 'Adicionar Valor';
        form.reset();
        document.getElementById('value_id').value = '';
        
        // Limpar validações anteriores
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        modal.show();
        
        // Focar no primeiro campo após o modal abrir
        setTimeout(() => {
            const firstInput = form.querySelector('input[type="text"]:not([type="hidden"])');
            if (firstInput) {
                firstInput.focus();
            }
        }, 300);
    });

    document.querySelectorAll('.edit-value').forEach(btn => {
        btn.addEventListener('click', async function() {
            const valueId = this.dataset.valueId;
            try {
                const response = await fetch(`/admin/attributes/values/${valueId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                }
                
                const data = await response.json();
                
                document.getElementById('valueModalTitle').textContent = 'Editar Valor';
                document.getElementById('value_id').value = valueId;
                document.getElementById('value_value').value = data.value.value;
                document.getElementById('value_display_value').value = data.value.display_value || '';
                @if($attribute->type === 'color')
                document.getElementById('value_color_hex').value = data.value.color_hex || '#000000';
                document.getElementById('value_color_hex_color').value = data.value.color_hex || '#000000';
                @endif
                @if($attribute->type === 'image')
                document.getElementById('value_image_url').value = data.value.image_url || '';
                @endif
                document.getElementById('value_sort_order').value = data.value.sort_order || 0;
                document.getElementById('value_is_active').checked = data.value.is_active;
                
                // Limpar validações anteriores
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                modal.show();
                
                // Focar no primeiro campo após o modal abrir
                setTimeout(() => {
                    const firstInput = form.querySelector('input[type="text"]:not([type="hidden"])');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, 300);
            } catch (error) {
                alert('Erro ao carregar valor: ' + error.message);
            }
        });
    });

    document.querySelectorAll('.delete-value').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Tem certeza que deseja excluir este valor?')) return;
            
            const valueId = this.dataset.valueId;
            try {
                const response = await fetch(`/admin/attributes/values/${valueId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    document.querySelector(`[data-value-id="${valueId}"]`).remove();
                } else {
                    alert(data.message || 'Erro ao excluir valor');
                }
            } catch (error) {
                alert('Erro ao excluir valor: ' + error.message);
            }
        });
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validação no frontend antes de enviar
        const valueInput = document.getElementById('value_value');
        const colorHexInput = document.getElementById('value_color_hex');
        
        // Remover validações anteriores
        valueInput.classList.remove('is-invalid');
        if (colorHexInput) colorHexInput.classList.remove('is-invalid');
        
        let hasErrors = false;
        
        // Validar campo "value"
        if (!valueInput.value || !valueInput.value.trim()) {
            valueInput.classList.add('is-invalid');
            hasErrors = true;
        }
        
        // Validar color_hex se for tipo color
        @if($attribute->type === 'color')
        if (!colorHexInput || !colorHexInput.value || !colorHexInput.value.match(/^#[0-9A-Fa-f]{6}$/)) {
            if (colorHexInput) colorHexInput.classList.add('is-invalid');
            hasErrors = true;
        }
        @endif
        
        if (hasErrors) {
            alert('Por favor, preencha todos os campos obrigatórios corretamente.');
            return;
        }
        
        const valueId = document.getElementById('value_id').value;
        
        // Preparar dados como JSON (não FormData) para garantir que funcione com PUT
        const data = {
            value: valueInput.value.trim(),
            display_value: document.getElementById('value_display_value').value.trim() || valueInput.value.trim(),
            sort_order: parseInt(document.getElementById('value_sort_order').value) || 0,
            is_active: document.getElementById('value_is_active').checked
        };
        
        // Adicionar color_hex se for tipo color
        @if($attribute->type === 'color')
        if (colorHexInput && colorHexInput.value) {
            data.color_hex = colorHexInput.value;
        }
        @endif
        
        // Adicionar image_url se for tipo image
        @if($attribute->type === 'image')
        const imageUrlInput = document.getElementById('value_image_url');
        if (imageUrlInput && imageUrlInput.value) {
            data.image_url = imageUrlInput.value;
        }
        @endif
        
        const url = valueId 
            ? `/admin/attributes/values/${valueId}`
            : `/admin/attributes/${attributeId}/values`;
        const method = valueId ? 'PUT' : 'POST';
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });
            
            // Verificar se a resposta é JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Resposta não é JSON. Status: ${response.status}. Resposta: ${text.substring(0, 200)}`);
            }
            
            const responseData = await response.json();
            
            if (responseData.success) {
                // Mostrar mensagem de sucesso
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Salvo!';
                submitBtn.classList.add('btn-success');
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                // Mostrar erros de validação se houver
                let errorMsg = responseData.message || 'Erro ao salvar valor';
                if (responseData.errors) {
                    const errors = Object.values(responseData.errors).flat().join('\n');
                    errorMsg += '\n\n' + errors;
                }
                alert(errorMsg);
            }
        } catch (error) {
            console.error('Erro completo:', error);
            alert('Erro ao salvar valor: ' + error.message);
        }
    });

    @if($attribute->type === 'color')
    // Sincronizar color picker
    document.getElementById('value_color_hex_color').addEventListener('input', function() {
        document.getElementById('value_color_hex').value = this.value;
    });
    document.getElementById('value_color_hex').addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
            document.getElementById('value_color_hex_color').value = this.value;
        }
    });
    @endif
});
</script>
@endsection

