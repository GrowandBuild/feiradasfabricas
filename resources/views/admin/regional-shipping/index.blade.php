@extends('admin.layouts.app')

@section('title', 'Entregas Regionais')
@section('page-title', 'Gerenciar Entregas Regionais')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Entregas Regionais</h4>
        <p class="text-muted mb-0">Configure entregas locais por região e CEP</p>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload"></i> Importar Lista
        </button>
        <a href="{{ route('admin.regional-shipping.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Região
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Lista de Regiões -->
<div class="card">
    <div class="card-body">
        @if($regions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CEP/Range</th>
                            <th>Tipo de Preço</th>
                            <th>Valor</th>
                            <th>Prazo</th>
                            <th>Ordem</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regions as $region)
                            <tr>
                                <td>
                                    <strong>{{ $region->name }}</strong>
                                    @if($region->description)
                                        <br><small class="text-muted">{{ Str::limit($region->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($region->cep_start && $region->cep_end)
                                        <small>{{ $region->cep_start }} - {{ $region->cep_end }}</small>
                                    @elseif($region->cep_list)
                                        @php
                                            $cepList = json_decode($region->cep_list, true);
                                            $cepCount = is_array($cepList) ? count($cepList) : 0;
                                        @endphp
                                        <small>{{ $cepCount }} CEP(s) específico(s)</small>
                                    @else
                                        <small class="text-muted">Não configurado</small>
                                    @endif
                                </td>
                                <td>
                                    @if($region->pricing_type === 'fixed')
                                        <span class="badge bg-info">Fixo</span>
                                    @elseif($region->pricing_type === 'per_weight')
                                        <span class="badge bg-warning">Por Peso</span>
                                    @else
                                        <span class="badge bg-secondary">Por Item</span>
                                    @endif
                                </td>
                                <td>
                                    @if($region->pricing_type === 'fixed')
                                        <strong>R$ {{ number_format($region->fixed_price ?? 0, 2, ',', '.') }}</strong>
                                    @elseif($region->pricing_type === 'per_weight')
                                        <strong>R$ {{ number_format($region->price_per_kg ?? 0, 2, ',', '.') }}/kg</strong>
                                    @else
                                        <strong>R$ {{ number_format($region->price_per_item ?? 0, 2, ',', '.') }}/item</strong>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $region->delivery_time }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $region->sort_order }}</span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-active" 
                                               type="checkbox" 
                                               data-id="{{ $region->id }}"
                                               {{ $region->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            {{ $region->is_active ? 'Ativo' : 'Inativo' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.regional-shipping.edit', $region->id) }}" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.regional-shipping.destroy', $region->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja remover esta região?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Remover">
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
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Nenhuma região cadastrada ainda.</p>
                <a href="{{ route('admin.regional-shipping.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Primeira Região
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Importação em Massa -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-lg" style="max-width: 90vw; margin: 1rem auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="bi bi-upload me-2"></i>Importar Lista de Bairros/Regiões
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="importForm" action="{{ route('admin.regional-shipping.import') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Como usar:</strong> Cole a lista de bairros/regiões (um por linha). Todas as regiões criadas usarão as mesmas configurações de preço e prazo definidas abaixo.
                    </div>

                    <div class="mb-3">
                        <label for="regions_list" class="form-label">
                            Lista de Bairros/Regiões <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="regions_list" name="regions_list" rows="8" 
                                  placeholder="Cole aqui a lista de bairros, um por linha:&#10;&#10;Área Rural de Valparaíso de Goiás&#10;Chácara Lourdes Meireles&#10;Chácaras Anhangüera&#10;Chácaras Araguaia&#10;...&#10;&#10;OU com preços individuais (formato: Nome|Preço):&#10;Área Rural de Valparaíso de Goiás|15.00&#10;Chácara Lourdes Meireles|12.50&#10;..."></textarea>
                        <small class="form-text text-muted">
                            <strong>Formato 1:</strong> Apenas nomes (um por linha) - você definirá os preços individualmente na tabela abaixo.<br>
                            <strong>Formato 2:</strong> Nome|Preço (ex: "Bairro X|15.00") - preços já definidos na lista.
                        </small>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="previewListBtn">
                            <i class="bi bi-eye me-1"></i> Visualizar e Ajustar Preços
                        </button>
                    </div>

                    <!-- Tabela de Preview e Edição de Preços -->
                    <div id="previewTableContainer" style="display: none;" class="mb-3">
                        <div class="alert alert-warning mb-2">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Revise e ajuste os preços individualmente:</strong> Edite os valores na tabela abaixo antes de importar.
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                            <table class="table table-sm table-bordered table-hover mb-0" id="previewTable">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background: #f8f9fa;">
                                    <tr>
                                        <th style="width: 55%; min-width: 200px; background: #f8f9fa;">Nome da Região</th>
                                        <th style="width: 25%; min-width: 120px; background: #f8f9fa;">Preço (R$)</th>
                                        <th style="width: 20%; min-width: 80px; background: #f8f9fa;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                    <!-- Será preenchido via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted" id="previewCount">0 regiões</small>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="hidePreviewBtn">
                                <i class="bi bi-eye-slash me-1"></i> Ocultar Tabela
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="import_pricing_type" class="form-label">Tipo de Precificação <span class="text-danger">*</span></label>
                            <select class="form-select" id="import_pricing_type" name="pricing_type" required>
                                <option value="fixed" selected>Preço Fixo</option>
                                <option value="per_weight">Por Peso (R$/kg)</option>
                                <option value="per_item">Por Item (R$/unidade)</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3" id="import_default_price_field">
                            <label for="import_default_price" class="form-label">Preço Padrão (R$) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" 
                                   id="import_default_price" name="default_price" value="0" required>
                            <small class="form-text text-muted">Preço padrão para regiões sem preço definido na lista</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="import_delivery_days_min" class="form-label">Prazo Mínimo (dias) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="import_delivery_days_min" 
                                   name="delivery_days_min" value="1" min="1" max="60" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="import_delivery_days_max" class="form-label">Prazo Máximo (dias)</label>
                            <input type="number" class="form-control" id="import_delivery_days_max" 
                                   name="delivery_days_max" min="1" max="60">
                            <small class="form-text text-muted">Deixe vazio para usar apenas o mínimo</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="import_sort_order" class="form-label">Ordem de Prioridade</label>
                            <input type="number" class="form-control" id="import_sort_order" 
                                   name="sort_order" value="0" min="0">
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="import_is_active" 
                                       name="is_active" value="1" checked>
                                <label class="form-check-label" for="import_is_active">
                                    Criar regiões como Ativas
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="import_description" class="form-label">Descrição (opcional)</label>
                        <input type="text" class="form-control" id="import_description" 
                               name="description" placeholder="Descrição que será aplicada a todas as regiões">
                    </div>
                </div>
                <div class="modal-footer" style="position: sticky; bottom: 0; background: white; border-top: 2px solid #dee2e6; padding: 1rem 1.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="confirmImportBtn">
                        <i class="bi bi-upload me-1"></i> Importar Regiões (<span id="finalImportCount">0</span>)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Modal sem backdrop e adaptativo ao zoom */
#importModal {
    z-index: 1055;
}

#importModal .modal-dialog {
    max-width: min(90vw, 900px);
    width: auto;
    margin: 1rem auto;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

#importModal .modal-content {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    overflow: hidden;
}

#importModal .modal-header {
    flex-shrink: 0;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

#importModal .modal-body {
    flex: 1 1 auto;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 1.5rem;
    min-height: 0;
}

#importModal .modal-footer {
    flex-shrink: 0;
    border-top: 2px solid #dee2e6;
    padding: 1rem 1.5rem;
    background: #fff;
    margin-top: auto;
}

/* Tabela de preview com scroll */
#previewTableContainer {
    margin-bottom: 1rem;
}

#previewTableContainer .table-responsive {
    max-height: 400px;
    overflow-y: auto !important;
    overflow-x: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    display: block;
}

#previewTable {
    margin-bottom: 0;
}

#previewTable thead {
    position: sticky;
    top: 0;
    background: #f8f9fa;
    z-index: 10;
}

/* Textarea menor */
#regions_list {
    max-height: 200px;
    font-size: 13px;
    line-height: 1.4;
}

/* Adaptação ao zoom */
@media (max-width: 768px) {
    #importModal .modal-dialog {
        max-width: 95vw;
        margin: 0.5rem auto;
        max-height: 98vh;
    }
    
    #importModal .modal-dialog {
        max-height: 95vh;
    }
    
    #importModal .modal-content {
        max-height: 95vh;
    }
    
    #importModal .modal-body {
        padding: 1rem;
        overflow-y: auto;
    }
    
    #importModal .modal-footer {
        padding: 0.75rem 1rem;
    }
    
    #previewTableContainer .table-responsive {
        max-height: 300px;
    }
    
    #importModal textarea {
        font-size: 12px;
        max-height: 150px;
    }
    
    #previewTableContainer .table-responsive {
        max-height: 250px;
    }
}

/* Garantir que o modal não tenha overlay escuro */
.modal-backdrop {
    display: none !important;
}

body.modal-open {
    overflow: auto !important;
    padding-right: 0 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle ativo/inativo
    document.querySelectorAll('.toggle-active').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const isActive = this.checked;
            
            fetch(`/admin/regional-shipping/${id}/toggle-active`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const label = this.nextElementSibling;
                    label.textContent = data.is_active ? 'Ativo' : 'Inativo';
                } else {
                    this.checked = !isActive; // Reverter
                    alert('Erro ao alterar status: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                this.checked = !isActive; // Reverter
                console.error('Error:', error);
                alert('Erro ao alterar status');
            });
        });
    });

    // Preview da lista e edição de preços
    const previewListBtn = document.getElementById('previewListBtn');
    const hidePreviewBtn = document.getElementById('hidePreviewBtn');
    const previewTableContainer = document.getElementById('previewTableContainer');
    const previewTableBody = document.getElementById('previewTableBody');
    const regionsListTextarea = document.getElementById('regions_list');
    let regionsData = [];

    function parseRegionsList() {
        const text = regionsListTextarea.value.trim();
        if (!text) {
            alert('Por favor, cole a lista de bairros primeiro.');
            return;
        }

        const lines = text.split('\n').filter(line => line.trim().length > 0);
        regionsData = [];

        lines.forEach((line, index) => {
            const trimmed = line.trim();
            if (!trimmed) return;

            // Verificar se tem preço no formato "Nome|Preço"
            const parts = trimmed.split('|');
            const name = parts[0].trim();
            let price = null;

            if (parts.length > 1) {
                // Tentar extrair preço
                const priceStr = parts[1].trim().replace(/[^\d,.-]/g, '').replace(',', '.');
                price = parseFloat(priceStr);
                if (isNaN(price)) price = null;
            }

            if (name) {
                regionsData.push({
                    name: name,
                    price: price,
                    index: index
                });
            }
        });

        renderPreviewTable();
    }

    function renderPreviewTable() {
        if (regionsData.length === 0) {
            previewTableContainer.style.display = 'none';
            return;
        }

        const defaultPrice = parseFloat(document.getElementById('import_default_price')?.value || '0') || 0;
        const pricingType = document.getElementById('import_pricing_type')?.value || 'fixed';

        previewTableBody.innerHTML = '';
        regionsData.forEach((region, idx) => {
            const row = document.createElement('tr');
            const priceValue = region.price !== null ? region.price : defaultPrice;
            
            row.innerHTML = `
                <td>
                    <input type="hidden" name="regions[${idx}][name]" value="${region.name.replace(/"/g, '&quot;')}">
                    <strong>${region.name}</strong>
                </td>
                <td>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           class="form-control form-control-sm region-price-input" 
                           name="regions[${idx}][price]" 
                           value="${priceValue.toFixed(2)}"
                           data-index="${idx}"
                           required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-region-btn" data-index="${idx}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            previewTableBody.appendChild(row);
        });

        // Atualizar contador
        document.getElementById('previewCount').textContent = `${regionsData.length} região(ões)`;

        // Adicionar listeners para remover
        document.querySelectorAll('.remove-region-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                regionsData = regionsData.filter((_, i) => i !== index);
                renderPreviewTable();
            });
        });

        previewTableContainer.style.display = 'block';
    }

    if (previewListBtn) {
        previewListBtn.addEventListener('click', parseRegionsList);
    }

    if (hidePreviewBtn) {
        hidePreviewBtn.addEventListener('click', function() {
            previewTableContainer.style.display = 'none';
        });
    }

    // Atualizar preços quando o preço padrão mudar
    const defaultPriceInput = document.getElementById('import_default_price');
    if (defaultPriceInput) {
        defaultPriceInput.addEventListener('input', function() {
            if (regionsData.length > 0) {
                const defaultPrice = parseFloat(this.value) || 0;
                document.querySelectorAll('.region-price-input').forEach(input => {
                    const region = regionsData[parseInt(input.dataset.index)];
                    if (region && region.price === null) {
                        input.value = defaultPrice.toFixed(2);
                    }
                });
            }
        });
    }

    // Garantir que não há backdrop ao abrir o modal
    const importModal = document.getElementById('importModal');
    if (importModal) {
        importModal.addEventListener('show.bs.modal', function() {
            // Remover qualquer backdrop existente
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Garantir que o body não tenha overflow hidden
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0';
        });
    }

    // Formulário de importação
    const importForm = document.getElementById('importForm');
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Se há preview table, usar os dados dela
            if (regionsData.length > 0) {
                // Coletar preços da tabela
                const priceInputs = document.querySelectorAll('.region-price-input');
                priceInputs.forEach(input => {
                    const idx = parseInt(input.dataset.index);
                    if (regionsData[idx]) {
                        regionsData[idx].price = parseFloat(input.value) || 0;
                    }
                });

                // Validar preços
                const invalidRegions = regionsData.filter(r => !r.price || r.price <= 0);
                if (invalidRegions.length > 0) {
                    alert(`Por favor, defina um preço válido para todas as regiões. ${invalidRegions.length} região(ões) sem preço.`);
                    return;
                }

                if (!confirm(`Deseja criar ${regionsData.length} região(ões) com os preços definidos?`)) {
                    return;
                }

                // Criar input hidden com dados JSON
                let existingInput = document.getElementById('regions_data_json');
                if (existingInput) existingInput.remove();
                
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = 'regions_data_json';
                hiddenInput.name = 'regions_data';
                hiddenInput.value = JSON.stringify(regionsData);
                this.appendChild(hiddenInput);
            } else {
                // Fallback: usar lista de texto
                const regionsList = document.getElementById('regions_list').value.trim();
                if (!regionsList) {
                    alert('Por favor, cole a lista de bairros/regiões e clique em "Visualizar e Ajustar Preços".');
                    return;
                }
            }

            // Desabilitar botão e mostrar loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Importando...';

            // Enviar formulário
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Sucesso! ${data.created} região(ões) criada(s).`);
                    window.location.reload();
                } else {
                    alert('Erro ao importar: ' + (data.message || 'Erro desconhecido'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao importar regiões. Tente novamente.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});
</script>
@endpush
@endsection

