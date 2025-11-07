<!-- Modal de Gerenciamento de Variações -->
<div class="modal fade" id="variationsModal" tabindex="-1" aria-labelledby="variationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
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

<!-- Modal Gerenciamento de Imagens por Cor -->
<div class="modal fade" id="colorImagesModal" tabindex="-1" aria-labelledby="colorImagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="colorImagesModalLabel">
                    <i class="bi bi-image me-2"></i>Imagens por Cor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Selecione quais imagens do produto devem aparecer quando esta cor for escolhida na loja.
                </p>
                <div id="colorImagesEmptyState" class="d-none"></div>
                <div id="colorImagesGrid" class="row"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Fechar
                </button>
                <button type="button" class="btn btn-primary" id="saveColorImagesBtn" onclick="saveColorImages()">
                    <i class="bi bi-check-circle me-1"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .variation-image-card {
        position: relative;
        border: 2px solid transparent;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }

    .variation-image-card:hover {
        transform: translateY(-2px);
    }

    .variation-image-card.selected {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .variation-image-card .selection-overlay {
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(13, 110, 253, 0.9);
        color: #fff;
        border-radius: 999px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        opacity: 0;
        transform: scale(0.6);
        transition: opacity 0.15s ease, transform 0.15s ease;
        pointer-events: none;
    }

    .variation-image-card.selected .selection-overlay {
        opacity: 1;
        transform: scale(1);
    }

    .color-dot {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid rgba(148, 163, 184, 0.6);
        background: #f1f5f9;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
    }
</style>

@push('scripts')
<script>
// Sistema de Gerenciamento de Variações
let variationProductImages = [];
let variationProductImagesUrls = [];
let variationColorImagesMap = {};
let variationColorImagesUrlsMap = {};
let variationColorHexMap = {};
let currentColorBeingEdited = null;
let productMarginB2C = 20;
let productMarginB2B = 10;

function formatCurrencyValue(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }
    const numberValue = typeof value === 'number' ? value : parseFloat(value);
    if (isNaN(numberValue)) {
        return '';
    }
    return numberValue.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function parseCurrencyValue(value) {
    if (!value && value !== 0) {
        return null;
    }

    let cleanValue = value.toString().trim();
    cleanValue = cleanValue.replace(/[^0-9,.-]/g, '');

    if (cleanValue === '' || cleanValue === ',' || cleanValue === '.') {
        return null;
    }

    const commaCount = (cleanValue.match(/,/g) || []).length;
    const dotCount = (cleanValue.match(/\./g) || []).length;

    if (commaCount > 1 || dotCount > 1) {
        cleanValue = cleanValue.replace(/\./g, '');
        cleanValue = cleanValue.replace(/,/g, '.');
    } else if (commaCount === 1 && dotCount === 0) {
        cleanValue = cleanValue.replace(/,/g, '.');
    } else if (dotCount === 1 && commaCount === 0) {
        cleanValue = cleanValue.replace(/\./g, '.');
    } else if (commaCount === 1 && dotCount === 1) {
        const commaIndex = cleanValue.indexOf(',');
        const dotIndex = cleanValue.indexOf('.');
        if (commaIndex > dotIndex) {
            cleanValue = cleanValue.replace(/\./g, '');
            cleanValue = cleanValue.replace(/,/g, '.');
        } else {
            cleanValue = cleanValue.replace(/,/g, '');
        }
    } else {
        cleanValue = cleanValue.replace(/\./g, '');
    }

    const parsed = parseFloat(cleanValue);
    return isNaN(parsed) ? null : parsed;
}

function calculatePriceFromCost(cost) {
    if (cost === null || cost === undefined) {
        return {
            price: null,
            b2b: null
        };
    }

    const price = cost * (1 + (productMarginB2C / 100));
    const b2bPrice = cost * (1 + (productMarginB2B / 100));

    return {
        price: parseFloat(price.toFixed(2)),
        b2b: parseFloat(b2bPrice.toFixed(2))
    };
}

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
                variationProductImages = data.product_images || [];
                variationProductImagesUrls = data.product_images_urls || [];
                variationColorImagesMap = data.color_images || {};
                variationColorImagesUrlsMap = data.color_images_urls || {};
                variationColorHexMap = data.color_hex_map || {};
                variationColorHexMap = Object.keys(variationColorHexMap).reduce((acc, key) => {
                    const value = variationColorHexMap[key];
                    acc[key] = value;
                    const sanitized = key.replace(/[^a-zA-Z0-9]/g, '_');
                    acc[sanitized] = value;
                    return acc;
                }, {});
                productMarginB2C = data.margins?.b2c ?? 20;
                productMarginB2B = data.margins?.b2b ?? 10;
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
            applyExistingColorHex(color.name, colorItem);
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

function createColorItem(color, productId) {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded';
    const safeColorName = color.name.replace(/'/g, "\\'");
    const colorKey = color.name.replace(/[^a-zA-Z0-9]/g, '_');
    const hasImages = Array.isArray(variationColorImagesMap[color.name]) && variationColorImagesMap[color.name].length > 0;
    div.innerHTML = `
        <div class="d-flex align-items-center gap-2 flex-grow-1">
            <span class="color-dot" data-color-key="${colorKey}"></span>
            <div class="flex-grow-1">
                <span class="d-block fw-semibold">${color.name}</span>
                <div class="d-flex gap-2 mt-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="openColorImagesModal('${safeColorName}')" title="Gerenciar imagens">
                        <i class="bi bi-image"></i>
                    </button>
                    <div class="input-group input-group-sm" style="width: 140px;">
                        <span class="input-group-text"><i class="bi bi-palette"></i></span>
                        <input type="color" class="form-control form-control-color color-picker" value="#ffffff" title="Selecionar cor" data-color-name="${safeColorName}" data-color-key="${colorKey}" onchange="handleColorPickerChange('${safeColorName}', '${colorKey}', this.value)">
                        <button class="btn btn-outline-secondary" type="button" onclick="clearColorHex('${safeColorName}', '${colorKey}')" title="Limpar cor">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-${color.enabled ? 'success' : 'secondary'}">${color.count} variações</span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteVariationValue('color', '${safeColorName}', ${color.count})" title="Excluir cor e suas variações">
                <i class="bi bi-trash"></i>
            </button>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" 
                   ${color.enabled ? 'checked' : ''} 
                   onchange="toggleVariationType(${productId}, 'color', '${color.name.replace(/'/g, "\\'")}', this.checked)">
            </div>
        </div>
    `;
    return div;
}

function handleColorPickerChange(colorName, colorKey, hexValue) {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId || !colorName) {
        return;
    }

    const normalizedHex = hexValue && hexValue.startsWith('#') ? hexValue : `#${hexValue}`;

    fetch(`/admin/products/${productId}/variations/color-hex`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            color: colorName,
            hex: normalizedHex
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            variationColorHexMap[colorName] = data.hex;
            variationColorHexMap[colorKey] = data.hex;
            updateColorDot(colorKey, data.hex);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível atualizar a cor.');
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar cor:', error);
        showVariationMessage('error', 'Erro ao atualizar cor.');
    });
}

function clearColorHex(colorName, colorKey) {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId || !colorName) {
        return;
    }

    fetch(`/admin/products/${productId}/variations/color-hex`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            color: colorName,
            hex: null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            delete variationColorHexMap[colorName];
            delete variationColorHexMap[colorKey];
            updateColorDot(colorKey, null);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível limpar a cor.');
        }
    })
    .catch(error => {
        console.error('Erro ao limpar cor:', error);
        showVariationMessage('error', 'Erro ao limpar cor.');
    });
}

function updateColorDot(colorKey, hex) {
    const colorDot = document.querySelector(`.color-dot[data-color-key="${colorKey}"]`);
    const colorPicker = document.querySelector(`.color-picker[data-color-key="${colorKey}"]`);

    const finalHex = hex || '#f1f5f9';

    if (colorDot) {
        colorDot.style.background = finalHex;
    }
    if (colorPicker) {
        colorPicker.value = hex || '#ffffff';
    }
}

function applyExistingColorHex(colorName, container) {
    const colorKey = colorName.replace(/[^a-zA-Z0-9]/g, '_');
    const storedHex = variationColorHexMap[colorName] || variationColorHexMap[colorName.trim()] || variationColorHexMap[colorName.toLowerCase()];
    const colorDot = container.querySelector(`.color-dot[data-color-key="${colorKey}"]`);
    const colorPicker = container.querySelector(`.color-picker[data-color-key="${colorKey}"]`);

    if (colorDot) {
        colorDot.style.background = storedHex || '#f1f5f9';
    }
    if (colorPicker) {
        colorPicker.value = storedHex || '#ffffff';
    }

    if (storedHex) {
        variationColorHexMap[colorName] = storedHex;
        variationColorHexMap[colorKey] = storedHex;
    }
}

function createRamItem(ram, productId) {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded';
    const safeRamName = ram.name.replace(/'/g, "\\'");
    div.innerHTML = `
        <span class="flex-grow-1">${ram.name}</span>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-${ram.enabled ? 'success' : 'secondary'}">${ram.count} variações</span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteVariationValue('ram', '${safeRamName}', ${ram.count})" title="Excluir RAM e suas variações">
                <i class="bi bi-trash"></i>
            </button>
        </div>
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
    const safeStorageName = storage.name.replace(/'/g, "\\'");
    div.innerHTML = `
        <span class="flex-grow-1">${storage.name}</span>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-${storage.enabled ? 'success' : 'secondary'}">${storage.count} variações</span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteVariationValue('storage', '${safeStorageName}', ${storage.count})" title="Excluir armazenamento e suas variações">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" 
                   ${storage.enabled ? 'checked' : ''} 
                   onchange="toggleVariationType(${productId}, 'storage', '${storage.name.replace(/'/g, "\\'")}', this.checked)">
        </div>
    `;
    return div;
}

function confirmDeleteVariationValue(type, value, count) {
    if (!type || !value) {
        return;
    }

    const productId = document.getElementById('variationsProductId').value;
    if (!productId) {
        showVariationMessage('error', 'Erro: produto não identificado.');
        return;
    }

    const decodedValue = value.replace(/\\'/g, "'");
    const message = count > 0
        ? `Isso removerá ${count} variação(ões) associada(s) a "${decodedValue}". Continuar?`
        : `Remover "${decodedValue}"?`;

    if (!confirm(message)) {
        return;
    }

    fetch(`/admin/products/${productId}/variations/value`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type, value: decodedValue })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showVariationMessage('success', data.message || 'Variações removidas com sucesso.');
            loadVariations(productId);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível remover as variações.');
        }
    })
    .catch(error => {
        console.error('Erro ao remover variações:', error);
        showVariationMessage('error', 'Erro ao remover variações.');
    });
}

function renderStock(data) {
    const stockContainer = document.getElementById('stockList');
    if (!stockContainer) {
        return;
    }

    stockContainer.innerHTML = '';
    const variationsArray = Array.isArray(data.variations) ? data.variations : Object.values(data.variations || {});
    const activeVariations = variationsArray.filter(variation => variation.is_active);

    if (activeVariations.length > 0) {
        activeVariations.forEach(variation => {
            const stockItem = createStockItem(variation, data.productId);
            stockContainer.appendChild(stockItem);
            initializeVariationPriceFields(stockItem);
        });
    } else {
        stockContainer.innerHTML = '<p class="text-muted text-center">Nenhuma variação ativa encontrada</p>';
    }
}

function createStockItem(variation, productId) {
    const div = document.createElement('div');
    div.className = 'variation-stock-card border rounded-3 mb-2';
    div.setAttribute('data-variation-id', variation.id);
    div.innerHTML = `
        <div class="variation-stock-header d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex flex-column">
                <span class="fw-semibold">${variation.name || variation.sku}</span>
                <small class="text-muted">SKU: ${variation.sku}</small>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    ${variation.color ? `<span class="badge rounded-pill bg-secondary-subtle text-secondary">${variation.color}</span>` : ''}
                    ${variation.ram ? `<span class="badge rounded-pill bg-info-subtle text-info">${variation.ram}</span>` : ''}
                    ${variation.storage ? `<span class="badge rounded-pill bg-primary-subtle text-primary">${variation.storage}</span>` : ''}
                    ${!variation.is_active ? '<span class="badge rounded-pill bg-dark-subtle text-dark">Inativa</span>' : ''}
            </div>
        </div>
            <div class="d-flex align-items-center gap-2">
                <div class="text-center">
                    <small class="text-muted">Estoque</small>
                    <input type="number" class="form-control form-control-sm text-center stock-input" data-variation-id="${variation.id}" value="${variation.stock_quantity || 0}" min="0" step="1" style="width: 90px;">
            </div>
                <div>
                    <small class="text-muted">Status</small>
                    <div>
                        <select class="form-select form-select-sm stock-status" data-variation-id="${variation.id}" style="width: 140px;">
                            <option value="1" ${variation.in_stock ? 'selected' : ''}>Em estoque</option>
                            <option value="0" ${!variation.in_stock ? 'selected' : ''}>Sem estoque</option>
                </select>
                    </div>
                </div>
                <button type="button"
                        class="btn btn-outline-danger btn-sm variation-delete"
                        data-variation-id="${variation.id}"
                        title="Excluir variação">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="variation-stock-prices row gx-2 mt-3">
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-1">Custo</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                    <input type="text" class="form-control variation-cost" data-variation-id="${variation.id}" value="${variation.cost_price ?? ''}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-1">Preço B2C</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-cart"></i></span>
                    <input type="text" class="form-control variation-price" data-variation-id="${variation.id}" value="${variation.price ?? ''}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-1">Preço B2B</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input type="text" class="form-control variation-b2b" data-variation-id="${variation.id}" value="${variation.b2b_price ?? ''}">
                </div>
            </div>
        </div>
    `;
    return div;
}

function toggleVariationType(productId, type, value, enabled) {
    if (!productId || !type || !value) {
        showVariationMessage('error', 'Erro: Dados inválidos');
        return;
    }
    
    // Desabilitar o toggle enquanto processa
    const toggle = event.target;
    const originalState = toggle.checked;
    toggle.disabled = true;
    
    fetch(`/admin/products/${productId}/variations/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type, value, enabled })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        toggle.disabled = false;
        if (data.success) {
            // Feedback visual
            const message = data.message || 'Variação atualizada com sucesso!';
            showVariationMessage('success', message);
            // Recarregar variações para atualizar o estado
            loadVariations(productId);
        } else {
            // Reverter o toggle se falhou
            toggle.checked = !enabled;
            showVariationMessage('error', 'Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        toggle.disabled = false;
        // Reverter o toggle se falhou
        toggle.checked = !enabled;
        showVariationMessage('error', 'Erro ao atualizar variação. Verifique sua conexão.');
    });
}

function openColorImagesModal(colorName) {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId || !colorName) {
        showVariationMessage('error', 'Erro: informações insuficientes para carregar imagens.');
        return;
    }

    currentColorBeingEdited = colorName;

    const modalTitle = document.getElementById('colorImagesModalLabel');
    if (modalTitle) {
        modalTitle.innerHTML = `<i class="bi bi-image me-2"></i>Imagens - ${colorName}`;
    }

    const grid = document.getElementById('colorImagesGrid');
    const emptyState = document.getElementById('colorImagesEmptyState');
    const saveBtn = document.getElementById('saveColorImagesBtn');

    if (!variationProductImages || variationProductImages.length === 0) {
        if (grid) grid.innerHTML = '';
        if (grid) grid.classList.add('d-none');
        if (emptyState) {
            emptyState.classList.remove('d-none');
            emptyState.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-info-circle me-1"></i>Adicione imagens ao produto para vinculá-las às cores.</div>';
        }
        if (saveBtn) saveBtn.disabled = true;
    } else {
        if (grid) {
            grid.classList.remove('d-none');
            const selectedForColor = variationColorImagesMap[colorName] || [];
            const selectedLookup = Object.fromEntries(selectedForColor.map(value => [value, true]));

            grid.innerHTML = variationProductImages.map((imagePath, index) => {
                const imageUrl = variationProductImagesUrls[index] || '';
                const isChecked = !!selectedLookup[imagePath];
                return `
                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                        <div class="card variation-image-card h-100 ${isChecked ? 'selected' : ''}" data-image-card>
                            <div class="position-relative">
                                <img src="${imageUrl}" class="card-img-top" alt="Imagem da variação" style="height: 120px; object-fit: cover;">
                                <div class="selection-overlay"><i class="bi bi-check-lg"></i></div>
                                <div class="form-check position-absolute top-0 end-0 m-2 bg-white rounded-pill px-2">
                                    <input class="form-check-input color-image-checkbox" type="checkbox" value="${imagePath}" ${isChecked ? 'checked' : ''}>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            grid.querySelectorAll('.color-image-checkbox').forEach(checkbox => {
                const card = checkbox.closest('[data-image-card]');
                if (!card) {
                    return;
                }

                checkbox.addEventListener('change', function() {
                    card.classList.toggle('selected', this.checked);
                });
            });
        }

        if (emptyState) emptyState.classList.add('d-none');
        if (saveBtn) saveBtn.disabled = false;
    }

    const modalElement = document.getElementById('colorImagesModal');
    if (modalElement) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }
}

function saveColorImages() {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId || !currentColorBeingEdited) {
        showVariationMessage('error', 'Erro: nenhuma cor selecionada.');
        return;
    }

    const modalElement = document.getElementById('colorImagesModal');
    const saveBtn = document.getElementById('saveColorImagesBtn');
    const checkboxes = modalElement ? modalElement.querySelectorAll('.color-image-checkbox') : [];
    const selectedImages = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando';
    }

    fetch(`/admin/products/${productId}/variations/color-images`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            color: currentColorBeingEdited,
            images: selectedImages
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            variationColorImagesMap = data.color_images || {};
            variationColorImagesUrlsMap = data.color_images_urls || {};

            showVariationMessage('success', data.message || 'Imagens atualizadas com sucesso!');

            // Recarregar lista de variações para exibir indicadores atualizados
            loadVariations(productId);

            setTimeout(() => {
                if (modalElement) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modal.hide();
                }
            }, 300);
        } else {
            showVariationMessage('error', data.message || 'Erro ao salvar imagens da cor.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showVariationMessage('error', 'Erro ao salvar imagens da cor.');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salvar';
        }
    });
}

function addNewVariationType(productId, type) {
    if (!productId || !type) {
        showVariationMessage('error', 'Erro: Dados inválidos');
        return;
    }
    
    const inputId = `new${type.charAt(0).toUpperCase() + type.slice(1)}`;
    const input = document.getElementById(inputId);
    if (!input) {
        showVariationMessage('error', 'Erro: Campo de entrada não encontrado');
        return;
    }
    
    const value = input.value.trim();
    
    if (!value) {
        showVariationMessage('error', 'Por favor, insira um valor');
        return;
    }
    
    // Desabilitar input e botão enquanto processa
    const addBtn = input.nextElementSibling;
    const originalBtnText = addBtn ? addBtn.innerHTML : '';
    if (addBtn) {
        addBtn.disabled = true;
        addBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Adicionando...';
    }
    input.disabled = true;
    
    fetch(`/admin/products/${productId}/variations/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type, value })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        input.disabled = false;
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = originalBtnText;
        }
        
        if (data.success) {
            input.value = '';
            showVariationMessage('success', data.message || 'Variação adicionada com sucesso!');
            // Recarregar variações para mostrar o novo item
            loadVariations(productId);
        } else {
            showVariationMessage('error', 'Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        input.disabled = false;
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = originalBtnText;
        }
        showVariationMessage('error', 'Erro ao adicionar variação. Verifique sua conexão.');
    });
}

function updateAllStock(event) {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId) {
        showVariationMessage('error', 'Erro: ID do produto não encontrado');
        return Promise.resolve({ success: false });
    }
    
    const stockInputs = document.querySelectorAll('.stock-input[data-variation-id]');
    const updates = [];
    
    stockInputs.forEach(input => {
        const variationId = input.getAttribute('data-variation-id');
        const stockQuantity = parseInt(input.value) || 0;
        const statusSelect = document.querySelector(`.stock-status[data-variation-id="${variationId}"]`);
        const costInput = document.querySelector(`.variation-cost[data-variation-id="${variationId}"]`);
        const priceInput = document.querySelector(`.variation-price[data-variation-id="${variationId}"]`);
        const b2bInput = document.querySelector(`.variation-b2b[data-variation-id="${variationId}"]`);
        const inStock = statusSelect ? statusSelect.value === '1' : true;
        
        updates.push({
            variation_id: variationId,
            stock_quantity: stockQuantity,
            in_stock: inStock,
            cost_price: costInput ? parseCurrencyValue(costInput.value) : null,
            price: priceInput ? parseCurrencyValue(priceInput.value) : null,
            b2b_price: b2bInput ? parseCurrencyValue(b2bInput.value) : null
        });
    });
    
    if (updates.length === 0) {
        if (event && event.target) {
            showVariationMessage('error', 'Nenhuma variação para atualizar');
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
    
    // Timeout para evitar requisições muito longas
    const timeoutId = setTimeout(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        showVariationMessage('error', '⏱️ A requisição está demorando muito. Verifique sua conexão.');
    }, 60000); // 60 segundos
    
    return fetch(`/admin/products/${productId}/variations/update-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ updates })
    })
    .then(response => {
        clearTimeout(timeoutId);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showVariationMessage('success', `✅ Estoque de ${data.updated} variação(ões) atualizado(s) com sucesso!`);
            // Recarregar variações para atualizar o estado
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
        clearTimeout(timeoutId);
        console.error('Erro:', error);
        showVariationMessage('error', '❌ Erro ao atualizar estoque. Verifique sua conexão.');
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

function initializeVariationPriceFields(stockItem) {
    if (!stockItem) {
        return;
    }

    const variationId = stockItem.getAttribute('data-variation-id');
    const costInput = stockItem.querySelector('.variation-cost');
    const priceInput = stockItem.querySelector('.variation-price');
    const b2bInput = stockItem.querySelector('.variation-b2b');
    const deleteBtn = stockItem.querySelector('.variation-delete');

    const inputs = [costInput, priceInput, b2bInput];

    inputs.forEach(input => {
        if (!input) {
            return;
        }
        const parsed = parseCurrencyValue(input.value);
        input.value = parsed !== null ? formatCurrencyValue(parsed) : '';
        input.addEventListener('focus', () => input.select());
    });

    if (costInput) {
        costInput.addEventListener('blur', function() {
            const parsedCost = parseCurrencyValue(this.value);
            if (parsedCost !== null) {
                this.value = formatCurrencyValue(parsedCost);
                const calculated = calculatePriceFromCost(parsedCost);
                if (priceInput) {
                    priceInput.value = formatCurrencyValue(calculated.price);
                }
                if (b2bInput) {
                    b2bInput.value = formatCurrencyValue(calculated.b2b);
                }
            } else {
                this.value = '';
                if (priceInput) priceInput.value = '';
                if (b2bInput) b2bInput.value = '';
            }
        });
    }

    [priceInput, b2bInput].forEach(input => {
        if (!input) {
            return;
        }
        input.addEventListener('blur', function() {
            const parsed = parseCurrencyValue(this.value);
            this.value = parsed !== null ? formatCurrencyValue(parsed) : '';
        });
    });

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const id = this.getAttribute('data-variation-id');
            const card = this.closest('.variation-stock-card');
            confirmAndDeleteVariation(id, card);
        });
    }
}

function confirmAndDeleteVariation(variationId, cardElement) {
    if (!variationId) {
        return;
    }

    if (!confirm('Tem certeza que deseja remover esta variação? Esta ação não pode ser desfeita.')) {
        return;
    }

    const productId = document.getElementById('variationsProductId').value;
    if (!productId) {
        showVariationMessage('error', 'Erro: produto não identificado.');
        return;
    }

    const loaderClass = 'opacity-50';
    if (cardElement) {
        cardElement.classList.add(loaderClass);
    }

    fetch(`/admin/products/${productId}/variations/${variationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showVariationMessage('success', data.message || 'Variação removida com sucesso.');
            loadVariations(productId);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível remover a variação.');
        }
    })
    .catch(error => {
        console.error('Erro ao excluir variação:', error);
        showVariationMessage('error', 'Erro ao remover variação.');
    })
    .finally(() => {
        if (cardElement) {
            cardElement.classList.remove(loaderClass);
        }
    });
}
</script>

<style>
    .variation-stock-card {
        padding: 1rem;
        background: #fff;
        transition: box-shadow 0.15s ease;
    }

    .variation-stock-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .variation-stock-header small {
        font-size: 0.75rem;
    }

    .variation-stock-prices .form-control {
        font-weight: 500;
    }
</style>
@endpush

