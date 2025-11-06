<!-- Modal de Gerenciamento de Variações -->
<div class="modal fade" id="variationsModal" tabindex="-1" aria-labelledby="variationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variationsModalLabel">
                    <i class="bi bi-list-ul me-2"></i>Gerenciar Variações
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="variationsProductId" value="">
                
                <!-- Abas -->
                <ul class="nav nav-tabs mb-4" id="variationsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors" type="button" role="tab">
                            <i class="bi bi-palette me-1"></i>Cores
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rams-tab" data-bs-toggle="tab" data-bs-target="#rams" type="button" role="tab">
                            <i class="bi bi-memory me-1"></i>RAM
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="storages-tab" data-bs-toggle="tab" data-bs-target="#storages" type="button" role="tab">
                            <i class="bi bi-hdd me-1"></i>Armazenamento
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab">
                            <i class="bi bi-box-seam me-1"></i>Estoque
                        </button>
                    </li>
                </ul>
                
                <!-- Conteúdo das Abas -->
                <div class="tab-content" id="variationsTabContent">
                    <!-- Aba Cores -->
                    <div class="tab-pane fade show active" id="colors" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adicionar Nova Cor</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newColor" placeholder="Ex: Preto, Branco, Azul">
                                <button class="btn btn-primary" type="button" onclick="addNewVariationType(document.getElementById('variationsProductId').value, 'color')">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar
                                </button>
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">Cores Disponíveis</label>
                            <div id="colorsList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba RAM -->
                    <div class="tab-pane fade" id="rams" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adicionar Nova RAM</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newRam" placeholder="Ex: 4GB, 8GB, 16GB">
                                <button class="btn btn-primary" type="button" onclick="addNewVariationType(document.getElementById('variationsProductId').value, 'ram')">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar
                                </button>
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">RAMs Disponíveis</label>
                            <div id="ramsList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba Armazenamento -->
                    <div class="tab-pane fade" id="storages" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adicionar Novo Armazenamento</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newStorage" placeholder="Ex: 128GB, 256GB, 512GB">
                                <button class="btn btn-primary" type="button" onclick="addNewVariationType(document.getElementById('variationsProductId').value, 'storage')">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar
                                </button>
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">Armazenamentos Disponíveis</label>
                            <div id="storagesList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba Estoque -->
                    <div class="tab-pane fade" id="stock" role="tabpanel">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">Gerenciar Estoque por Variação</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateAllStock()">
                                    <i class="bi bi-check-all me-1"></i>Salvar Todos
                                </button>
                            </div>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Edite o estoque de cada variação abaixo. Clique em "Salvar Todos" para aplicar todas as alterações.
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">Variações e Estoque</label>
                            <div id="stockList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="saveAndCloseModal()">
                    <i class="bi bi-check-circle me-1"></i>Salvar e Fechar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Sistema de Gerenciamento de Variações
document.addEventListener('DOMContentLoaded', function() {
    const variationsModal = document.getElementById('variationsModal');
    if (variationsModal) {
        variationsModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            
            document.getElementById('variationsModalLabel').innerHTML = 
                '<i class="bi bi-list-ul me-2"></i>Variações - ' + productName;
            document.getElementById('variationsProductId').value = productId;
            
            loadVariations(productId);
        });
        
        // Limpar conteúdo ao fechar o modal
        variationsModal.addEventListener('hidden.bs.modal', function() {
            // Limpar listas
            const colorsList = document.getElementById('colorsList');
            const ramsList = document.getElementById('ramsList');
            const storagesList = document.getElementById('storagesList');
            const stockList = document.getElementById('stockList');
            
            if (colorsList) colorsList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';
            if (ramsList) ramsList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';
            if (storagesList) storagesList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';
            if (stockList) stockList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';
            
            // Limpar inputs
            const newColor = document.getElementById('newColor');
            const newRam = document.getElementById('newRam');
            const newStorage = document.getElementById('newStorage');
            
            if (newColor) newColor.value = '';
            if (newRam) newRam.value = '';
            if (newStorage) newStorage.value = '';
            
            // Resetar para primeira aba
            const firstTab = document.querySelector('#variationsTabs .nav-link');
            if (firstTab) {
                firstTab.click();
            }
        });
    }
});

function loadVariations(productId) {
    fetch(`/admin/products/${productId}/variations`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderVariations(data);
                renderStock(data);
            } else {
                alert('Erro ao carregar variações: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar variações');
        });
}

function renderVariations(data) {
    // Renderizar cores
    const colorsContainer = document.getElementById('colorsList');
    colorsContainer.innerHTML = '';
    if (data.colors && data.colors.length > 0) {
        data.colors.forEach(color => {
            const colorItem = createColorItem(color, data.productId);
            colorsContainer.appendChild(colorItem);
        });
    } else {
        colorsContainer.innerHTML = '<p class="text-muted text-center">Nenhuma cor encontrada</p>';
    }
    
    // Renderizar RAMs
    const ramsContainer = document.getElementById('ramsList');
    ramsContainer.innerHTML = '';
    if (data.rams && data.rams.length > 0) {
        data.rams.forEach(ram => {
            const ramItem = createRamItem(ram, data.productId);
            ramsContainer.appendChild(ramItem);
        });
    } else {
        ramsContainer.innerHTML = '<p class="text-muted text-center">Nenhuma RAM encontrada</p>';
    }
    
    // Renderizar Armazenamentos
    const storagesContainer = document.getElementById('storagesList');
    storagesContainer.innerHTML = '';
    if (data.storages && data.storages.length > 0) {
        data.storages.forEach(storage => {
            const storageItem = createStorageItem(storage, data.productId);
            storagesContainer.appendChild(storageItem);
        });
    } else {
        storagesContainer.innerHTML = '<p class="text-muted text-center">Nenhum armazenamento encontrado</p>';
    }
}

function renderStock(data) {
    const stockContainer = document.getElementById('stockList');
    stockContainer.innerHTML = '';
    
    if (data.variations && data.variations.length > 0) {
        data.variations.forEach(variation => {
            const stockItem = createStockItem(variation, data.productId);
            stockContainer.appendChild(stockItem);
        });
    } else {
        stockContainer.innerHTML = '<p class="text-muted text-center">Nenhuma variação encontrada</p>';
    }
}

function createColorItem(color, productId) {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded';
    div.innerHTML = `
        <span class="flex-grow-1">${color.name}</span>
        <span class="badge bg-${color.enabled ? 'success' : 'secondary'} me-2">${color.count} variações</span>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" 
                   ${color.enabled ? 'checked' : ''} 
                   onchange="toggleVariationType(${productId}, 'color', '${color.name.replace(/'/g, "\\'")}', this.checked)">
        </div>
    `;
    return div;
}

function createRamItem(ram, productId) {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded';
    div.innerHTML = `
        <span class="flex-grow-1">${ram.name}</span>
        <span class="badge bg-${ram.enabled ? 'success' : 'secondary'} me-2">${ram.count} variações</span>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" 
                   ${ram.enabled ? 'checked' : ''} 
                   onchange="toggleVariationType(${productId}, 'ram', '${ram.name.replace(/'/g, "\\'")}', this.checked)">
        </div>
    `;
    return div;
}

function createStorageItem(storage, productId) {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded';
    div.innerHTML = `
        <span class="flex-grow-1">${storage.name}</span>
        <span class="badge bg-${storage.enabled ? 'success' : 'secondary'} me-2">${storage.count} variações</span>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" 
                   ${storage.enabled ? 'checked' : ''} 
                   onchange="toggleVariationType(${productId}, 'storage', '${storage.name.replace(/'/g, "\\'")}', this.checked)">
        </div>
    `;
    return div;
}

function createStockItem(variation, productId) {
    const div = document.createElement('div');
    div.className = 'mb-3 p-3 border rounded';
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="flex-grow-1">
                <strong>${variation.name || variation.sku}</strong>
                <div class="small text-muted">SKU: ${variation.sku}</div>
                ${variation.ram ? `<span class="badge bg-info me-1">${variation.ram}</span>` : ''}
                ${variation.storage ? `<span class="badge bg-primary me-1">${variation.storage}</span>` : ''}
                ${variation.color ? `<span class="badge bg-secondary me-1">${variation.color}</span>` : ''}
            </div>
            <span class="badge bg-${variation.is_active ? 'success' : 'secondary'}">
                ${variation.is_active ? 'Ativo' : 'Inativo'}
            </span>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label small">Estoque Atual</label>
                <input type="number" 
                       class="form-control form-control-sm stock-input" 
                       data-variation-id="${variation.id}"
                       value="${variation.stock_quantity || 0}"
                       min="0"
                       step="1">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Status</label>
                <select class="form-select form-select-sm stock-status" 
                        data-variation-id="${variation.id}">
                    <option value="1" ${variation.in_stock ? 'selected' : ''}>Em Estoque</option>
                    <option value="0" ${!variation.in_stock ? 'selected' : ''}>Fora de Estoque</option>
                </select>
            </div>
        </div>
    `;
    return div;
}

function toggleVariationType(productId, type, value, enabled) {
    fetch(`/admin/products/${productId}/variations/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type, value, enabled })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Feedback visual
            const message = data.message || 'Variação atualizada com sucesso!';
            showVariationMessage('success', message);
            loadVariations(productId);
        } else {
            showVariationMessage('error', 'Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showVariationMessage('error', 'Erro ao atualizar variação');
    });
}

function addNewVariationType(productId, type) {
    const inputId = `new${type.charAt(0).toUpperCase() + type.slice(1)}`;
    const input = document.getElementById(inputId);
    const value = input.value.trim();
    
    if (!value) {
        alert('Por favor, insira um valor');
        return;
    }
    
    fetch(`/admin/products/${productId}/variations/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type, value })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            showVariationMessage('success', data.message || 'Variação adicionada com sucesso!');
            loadVariations(productId);
        } else {
            showVariationMessage('error', 'Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showVariationMessage('error', 'Erro ao adicionar variação');
    });
}

function updateAllStock(event) {
    const productId = document.getElementById('variationsProductId').value;
    const stockInputs = document.querySelectorAll('.stock-input[data-variation-id]');
    const updates = [];
    
    stockInputs.forEach(input => {
        const variationId = input.getAttribute('data-variation-id');
        const stockQuantity = parseInt(input.value) || 0;
        const statusSelect = document.querySelector(`.stock-status[data-variation-id="${variationId}"]`);
        const inStock = statusSelect ? statusSelect.value === '1' : true;
        
        updates.push({
            variation_id: variationId,
            stock_quantity: stockQuantity,
            in_stock: inStock
        });
    });
    
    if (updates.length === 0) {
        if (event && event.target) {
            alert('Nenhuma variação para atualizar');
        }
        return Promise.resolve({ success: false, message: 'Nenhuma variação para atualizar' });
    }
    
    // Mostrar loading no botão se houver evento
    let saveBtn = null;
    let originalText = '';
    if (event && event.target) {
        saveBtn = event.target;
        originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
    }
    
    return fetch(`/admin/products/${productId}/variations/update-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ updates })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showVariationMessage('success', `✅ Estoque de ${data.updated} variação(ões) atualizado(s) com sucesso!`);
            loadVariations(productId);
        } else {
            showVariationMessage('error', '❌ Erro: ' + (data.message || 'Erro desconhecido'));
        }
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        return data;
    })
    .catch(error => {
        console.error('Erro:', error);
        showVariationMessage('error', '❌ Erro ao atualizar estoque');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        return { success: false, error: error };
    });
}

// Função para salvar e fechar o modal
function saveAndCloseModal() {
    const productId = document.getElementById('variationsProductId').value;
    
    // Verificar se estamos na aba de estoque e há alterações pendentes
    const activeTab = document.querySelector('#variationsTabs .nav-link.active');
    if (activeTab && activeTab.getAttribute('data-bs-target') === '#stock') {
        // Se estiver na aba de estoque, salvar todas as alterações primeiro
        const stockInputs = document.querySelectorAll('.stock-input[data-variation-id]');
        if (stockInputs.length > 0) {
            // Salvar estoque e depois fechar
            updateAllStock().then(data => {
                // Aguardar um pouco para mostrar a mensagem e depois fechar
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
                    if (modal) {
                        modal.hide();
                    }
                }, 1500);
            });
        } else {
            // Não há alterações, apenas fechar
            const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
            if (modal) {
                modal.hide();
            }
        }
    } else {
        // Para outras abas, as alterações já foram salvas automaticamente
        // Apenas fechar o modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
        if (modal) {
            modal.hide();
        }
    }
}

// Função para exibir mensagens de feedback
function showVariationMessage(type, message) {
    // Remover mensagem anterior se existir
    const existingMessage = document.getElementById('variationMessage');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Criar nova mensagem
    const messageDiv = document.createElement('div');
    messageDiv.id = 'variationMessage';
    messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    messageDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(messageDiv);
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        if (messageDiv && messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}
</script>
@endpush

