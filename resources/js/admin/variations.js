// Extracted from resources/views/admin/products/modals/variations.blade.php
// This file contains the variation modal logic. It is loaded as a Vite module
// but exposes necessary functions on `window` for compatibility with inline handlers.

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
            let button = event.relatedTarget;
            if (!button) {
                button = document.querySelector('[data-bs-target="#variationsModal"][data-product-id]') || null;
            }

            let productId = null;
            let productName = '';
            if (button) {
                productId = button.getAttribute('data-product-id');
                productName = button.getAttribute('data-product-name') || '';
            }
            if (!productId) {
                const hidden = document.getElementById('variationsProductId');
                if (hidden && hidden.value) productId = hidden.value;
            }

            document.getElementById('variationsModalLabel').innerHTML =
                '<i class="bi bi-list-ul me-2"></i>Variações' + (productName ? ' - ' + productName : '');
            if (productId) document.getElementById('variationsProductId').value = productId;

            const selectedAttrCheckboxes = Array.from(document.querySelectorAll('.dept-attr-checkbox:checked'));
            const selectedTypes = selectedAttrCheckboxes.map(cb => cb.dataset.type).filter(Boolean);
            const uniqueTypes = Array.from(new Set(selectedTypes));

            if (uniqueTypes.length > 0) {
                loadVariations(productId, uniqueTypes);
            } else {
                loadVariations(productId);
            }
        });

        variationsModal.addEventListener('hidden.bs.modal', function() {
            const colorsList = document.getElementById('colorsList');
            const ramsList = document.getElementById('ramsList');
            const storagesList = document.getElementById('storagesList');
            const stockList = document.getElementById('stockList');

            if (colorsList) colorsList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';
            if (ramsList) ramsList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';
            if (storagesList) storagesList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';
            if (stockList) stockList.innerHTML = '<p class="text-muted text-center">Carregando...</p>';

            const newColor = document.getElementById('newColor');
            const newRam = document.getElementById('newRam');
            const newStorage = document.getElementById('newStorage');

            if (newColor) newColor.value = '';
            if (newRam) newRam.value = '';
            if (newStorage) newStorage.value = '';

            const firstTab = document.querySelector('#variationsTabs .nav-link');
            if (firstTab) {
                firstTab.click();
            }
        });
    }
});

function loadVariations(productId, onlyTypes = null) {
    fetch(`/admin/products/${productId}/variations`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                variationProductImages = data.product_images || [];
                variationProductImagesUrls = data.product_images_urls || [];
                variationColorImagesMap = data.color_images || {};
                variationColorImagesUrlsMap = data.color_images_urls || {};
                variationColorHexMap = data.color_hex_map || {};
                productMarginB2C = data.margins?.b2c ?? 20;
                productMarginB2B = data.margins?.b2b ?? 10;
                renderVariations(data, onlyTypes);
                renderStock(data);
                // Emit event so other UI parts (dept attributes panel) can sync
                try { emitVariationsUpdated(productId); } catch (e) {}
            } else {
                alert('Erro ao carregar variações: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar variações');
        });
}

// Emit a custom event when variations changed so other components can listen
function emitVariationsUpdated(productId) {
    try {
        const ev = new CustomEvent('variations:updated', { detail: { productId: productId } });
        document.dispatchEvent(ev);
    } catch (e) {
        console.debug && console.debug('emitVariationsUpdated failed', e);
    }
}
window.emitVariationsUpdated = emitVariationsUpdated;

// Centralized sync function to mark department attribute checkboxes
window.syncDeptAttributesWithVariations = function(productId) {
    if (!productId) return;
    fetch(`/admin/products/${productId}/variations`, { headers: { 'Accept': 'application/json' } })
        .then(resp => resp.json())
        .then(vdata => {
            if (vdata && vdata.attribute_groups) {
                const lookup = {};
                Object.keys(vdata.attribute_groups).forEach(type => {
                    lookup[type] = new Set((vdata.attribute_groups[type] || []).map(i => (i.name || '').toString().toLowerCase()));
                });

                document.querySelectorAll('.dept-attr-checkbox').forEach(cb => {
                    const type = (cb.dataset.type || '').toString();
                    const val = (cb.dataset.value || '').toString().toLowerCase();
                    if (lookup[type] && lookup[type].has(val)) {
                        cb.classList.add('variation-exists');
                        try { cb.disabled = true; cb.setAttribute('aria-disabled','true'); } catch(e) {}
                        const label = cb.nextElementSibling || cb.closest('label') || null;
                        if (label && !label.querySelector('.badge-created')) {
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-success ms-2 badge-created';
                            badge.style.fontSize = '0.7em';
                            badge.textContent = 'Criada';
                            label.appendChild(badge);
                        }
                    }
                });
            }
        }).catch(e => console.debug && console.debug('syncDeptAttributesWithVariations failed', e));
};

function renderVariations(data, onlyTypes = null) {
    const tabsEl = document.getElementById('variationsTabs');
    const tabContentEl = document.getElementById('variationsTabContent');

    if (!tabsEl || !tabContentEl) return;

    const groups = data.attribute_groups || {};
    const preferredOrder = ['color','ram','storage'];
    let availableKeys = Object.keys(groups || {});
    if (Array.isArray(onlyTypes) && onlyTypes.length > 0) {
        const wanted = onlyTypes.map(s => String(s).toLowerCase());
        availableKeys = availableKeys.filter(k => wanted.includes(String(k).toLowerCase()));
    }

    const keys = availableKeys.sort((a,b) => {
        const ai = preferredOrder.indexOf(a) === -1 ? 99 : preferredOrder.indexOf(a);
        const bi = preferredOrder.indexOf(b) === -1 ? 99 : preferredOrder.indexOf(b);
        if (ai === bi) return a.localeCompare(b);
        return ai - bi;
    });

    tabsEl.innerHTML = '';
    tabContentEl.innerHTML = '';

    keys.forEach((type, idx) => {
        const tabId = `${type}-tab`;
        const paneId = `${type}-pane`;

        const li = document.createElement('li');
        li.className = 'nav-item';
        li.setAttribute('role','presentation');
        li.innerHTML = `<button class="nav-link ${idx===0? 'active':''}" id="${tabId}" data-bs-toggle="tab" data-bs-target="#${paneId}" type="button" role="tab">${type.charAt(0).toUpperCase()+type.slice(1)}</button>`;
        tabsEl.appendChild(li);

        const pane = document.createElement('div');
        pane.className = `tab-pane fade ${idx===0? 'show active':''}`;
        pane.id = paneId;
        pane.setAttribute('role','tabpanel');

        const inputId = `new${type.charAt(0).toUpperCase() + type.slice(1)}`;
        pane.innerHTML = `
            <div class="mb-3">
                <label class="form-label fw-bold">Adicionar novo ${type}</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="${inputId}" placeholder="Ex: valor">
                    <button class="btn btn-primary" type="button" onclick="addNewVariationType(document.getElementById('variationsProductId').value, '${type}')">
                        <i class="bi bi-plus-circle me-1"></i>Adicionar
                    </button>
                </div>
            </div>
            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                <label class="form-label fw-bold mb-3">${type.charAt(0).toUpperCase()+type.slice(1)}s Disponíveis</label>
                <div id="${type}List">
                    <p class="text-muted text-center">Carregando...</p>
                </div>
            </div>
        `;

        tabContentEl.appendChild(pane);
    });

    const stockLi = document.createElement('li');
    stockLi.className = 'nav-item';
    stockLi.setAttribute('role','presentation');
    stockLi.innerHTML = `<button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab"><i class="bi bi-box-seam me-1"></i>Estoque</button>`;
    tabsEl.appendChild(stockLi);

    if (!document.getElementById('stock')) {
        const stockPane = document.createElement('div');
        stockPane.className = 'tab-pane fade';
        stockPane.id = 'stock';
        stockPane.setAttribute('role','tabpanel');
        stockPane.innerHTML = `<div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;"><label class="form-label fw-bold mb-3">Variações e Estoque</label><div id="stockList"><p class="text-muted text-center">Carregando...</p></div></div>`;
        tabContentEl.appendChild(stockPane);
    }

    keys.forEach(type => {
        const listEl = document.getElementById(`${type}List`);
        listEl.innerHTML = '';
        const items = (data.attribute_groups && data.attribute_groups[type]) ? data.attribute_groups[type] : [];
        if (!items || items.length === 0) {
            listEl.innerHTML = '<p class="text-muted text-center">Nenhum item encontrado</p>';
            return;
        }

        items.forEach(item => {
            let node;
            if (type === 'color') {
                node = createColorItem(item, data.productId);
                applyExistingColorHex(item.name, node);
            } else if (type === 'ram') {
                node = createRamItem(item, data.productId);
            } else if (type === 'storage') {
                node = createStorageItem(item, data.productId);
            } else {
                node = createAttributeItem(type, item, data.productId);
            }
            listEl.appendChild(node);
        });
    });
}

function createAttributeItem(type, item, productId) {
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 border rounded';
    const safeName = (item.name || '').replace(/'/g, "\\'");
    let hex = null;
    try {
        if (item && (item.hex || item.color_hex)) {
            hex = (item.hex || item.color_hex || '').toString().trim();
            if (hex !== '' && hex.indexOf('#') !== 0) hex = '#' + hex;
        }
    } catch (e) { hex = null; }

    const swatchHtml = hex ? `<span class="me-2" style="display:inline-block;width:18px;height:14px;background:${hex};border:1px solid #ddd;vertical-align:middle;border-radius:3px;"></span>` : '';

    div.innerHTML = `
        <span class="flex-grow-1">${swatchHtml}<span>${item.name}</span></span>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-${item.enabled ? 'success' : 'secondary'}">${item.count} variações</span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteVariationValue('${type}', '${safeName}', ${item.count})" title="Excluir e suas variações">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" ${item.enabled ? 'checked' : ''} onchange="toggleVariationType(${productId}, '${type}', '${(item.name||'').replace(/'/g, "\\'")}', this.checked, this)">
        </div>
    `;
    return div;
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
                     onchange="toggleVariationType(${productId}, 'color', '${color.name.replace(/'/g, "\\'")}', this.checked, this)">
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
        try { colorPicker.value = hex || '#ffffff'; } catch (e) {}
    }
}

function applyExistingColorHex(colorName, container) {
    if (!colorName || !container) return;
    const trimmed = String(colorName).trim();
    const sanitized = trimmed.replace(/[^a-zA-Z0-9]/g, '_');
    const candidates = [trimmed, trimmed.toLowerCase(), trimmed.toUpperCase(), sanitized, sanitized.toLowerCase(), sanitized.toUpperCase()];
    let storedHex = null;
    for (const k of candidates) {
        if (variationColorHexMap && Object.prototype.hasOwnProperty.call(variationColorHexMap, k)) {
            storedHex = variationColorHexMap[k];
            break;
        }
    }

    const colorDot = container.querySelector(`.color-dot[data-color-key="${sanitized}"]`);
    const colorPicker = container.querySelector(`.color-picker[data-color-key="${sanitized}"]`);

    const finalHex = storedHex || null;
    if (colorDot) {
        colorDot.style.background = finalHex || '#f1f5f9';
    }
    if (colorPicker) {
        try { colorPicker.value = finalHex || '#ffffff'; } catch (e) {}
    }

    if (storedHex) {
        variationColorHexMap[trimmed] = storedHex;
        variationColorHexMap[trimmed.toLowerCase()] = storedHex;
        variationColorHexMap[sanitized] = storedHex;
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
                     onchange="toggleVariationType(${productId}, 'ram', '${ram.name.replace(/'/g, "\\'")}', this.checked, this)">
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
                     onchange="toggleVariationType(${productId}, 'storage', '${storage.name.replace(/'/g, "\\'")}', this.checked, this)">
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
    let colorName = null;
    try {
        if (variation.attributes && typeof variation.attributes === 'string') {
            try {
                variation.attributes = JSON.parse(variation.attributes);
            } catch (e) {
            }
        }

        if (variation.color) {
            colorName = variation.color;
        } else if (variation.attributes) {
            const attrs = variation.attributes;
            if (typeof attrs === 'object' && attrs !== null) {
                if (attrs.color) {
                    if (typeof attrs.color === 'string') colorName = attrs.color;
                    else if (typeof attrs.color === 'object' && attrs.color !== null) {
                        colorName = attrs.color.value || attrs.color.name || null;
                        if (!colorName) {
                            for (const k in attrs.color) {
                                if (Object.prototype.hasOwnProperty.call(attrs.color, k)) {
                                    const v = attrs.color[k];
                                    if (typeof v === 'string' || typeof v === 'number') { colorName = String(v); break; }
                                }
                            }
                        }
                    }
                }
            }
        }
    } catch (e) { colorName = null; }

    const lookupCandidates = [];
    if (colorName) {
        const trimmed = String(colorName).trim();
        const sanitized = trimmed.replace(/[^a-zA-Z0-9]/g, '_');
        lookupCandidates.push(trimmed, trimmed.toLowerCase(), trimmed.toUpperCase(), sanitized, sanitized.toLowerCase());
    }
    if (variation.color_hex) {
        lookupCandidates.push(variation.color_hex, ('#' + String(variation.color_hex).replace(/^#/, '')));
    }

    let resolvedHex = null;
    for (const k of lookupCandidates) {
        if (!k) continue;
        if (variationColorHexMap && Object.prototype.hasOwnProperty.call(variationColorHexMap, k)) {
            resolvedHex = variationColorHexMap[k];
            break;
        }
    }
    if (!resolvedHex) resolvedHex = '#f1f5f9';

    function luminance(hex) {
        if (!hex) return 1;
        const h = String(hex).replace('#','');
        if (h.length < 6) return 1;
        const r = parseInt(h.substring(0,2),16)/255;
        const g = parseInt(h.substring(2,4),16)/255;
        const b = parseInt(h.substring(4,6),16)/255;
        return 0.2126*r + 0.7152*g + 0.0722*b;
    }
    const border = luminance(resolvedHex) > 0.85 ? 'rgba(0,0,0,0.25)' : 'rgba(148,163,184,0.4)';

    let attrsDisplay = [];
    try {
        let attrs = {};
        if (variation.attributes) {
            if (typeof variation.attributes === 'object') {
                attrs = variation.attributes;
            } else if (typeof variation.attributes === 'string') {
                try {
                    const parsed = JSON.parse(variation.attributes);
                    if (parsed) {
                        if (Array.isArray(parsed)) {
                            parsed.forEach(item => { if (item && typeof item === 'object') Object.assign(attrs, item); });
                        } else if (parsed && typeof parsed === 'object') {
                            attrs = parsed;
                        }
                    }
                } catch (e) {
                }
            }
        }

        if ((!attrs || Object.keys(attrs).length === 0) && variation.name && typeof variation.name === 'string') {
            try {
                const parsedName = JSON.parse(variation.name);
                if (parsedName) {
                    if (Array.isArray(parsedName)) {
                        parsedName.forEach(item => { if (item && typeof item === 'object') Object.assign(attrs, item); });
                    } else if (parsedName && typeof parsedName === 'object') {
                        attrs = parsedName;
                    }
                }
            } catch (e) {
            }
        }

        const order = ['color','cor','size','tamanho','ram','storage','memoria'];
        const used = new Set();
        order.forEach(k => {
            if (attrs && Object.prototype.hasOwnProperty.call(attrs, k) && attrs[k]) {
                const v = (typeof attrs[k] === 'object') ? (attrs[k].value || attrs[k].name || JSON.stringify(attrs[k])) : attrs[k];
                attrsDisplay.push(String(v));
                used.add(k);
            }
        });
        if (attrs) {
            Object.keys(attrs).forEach(k => {
                if (used.has(k)) return;
                const v = attrs[k];
                if (v === null || v === undefined) return;
                const val = (typeof v === 'object') ? (v.value || v.name || JSON.stringify(v)) : v;
                if (String(val).trim() !== '') attrsDisplay.push(String(val));
            });
        }
    } catch (e) { attrsDisplay = []; }

    let nameFromAttrs = '';
    if (attrsDisplay.length > 0) {
        nameFromAttrs = attrsDisplay.join(' / ');
    } else if (variation.name && typeof variation.name === 'string') {
        const t = variation.name.trim();
        if (t.startsWith('{') || t.startsWith('[')) {
            try {
                const parsed = JSON.parse(t);
                let tmp = [];
                if (Array.isArray(parsed)) {
                    parsed.forEach(item => {
                        if (item && typeof item === 'object') {
                            Object.keys(item).forEach(k => { if (item[k]) tmp.push(String(item[k])); });
                        } else if (item) tmp.push(String(item));
                    });
                } else if (parsed && typeof parsed === 'object') {
                    Object.keys(parsed).forEach(k => { if (parsed[k]) tmp.push(String(parsed[k])); });
                }
                if (tmp.length > 0) nameFromAttrs = tmp.join(' / ');
            } catch (e) {
            }
        }
    }

    const displayName = (nameFromAttrs && nameFromAttrs.length > 0) ? nameFromAttrs : (variation.name || variation.sku || '');

    div.innerHTML = `
        <div class="variation-stock-header d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex flex-column">
                ${colorName ? (`<div class="mb-2 d-flex align-items-center gap-2"><span class="color-dot" style="width:20px;height:20px;border-radius:50%;background:${resolvedHex};border:1px solid ${border};display:inline-block;"></span><strong style="font-size:0.95rem;">${colorName}${resolvedHex ? ` <small class="text-muted ms-2" style="font-family:monospace;">(${resolvedHex})</small>` : ''}</strong></div>`) : ''}
                <span class="fw-semibold">[${displayName}] ${resolvedHex ? `<small class="text-muted ms-2" style="font-family:monospace;">(${resolvedHex})</small>` : ''}</span>
                <small class="text-muted d-block">SKU: ${variation.sku}</small>
                <div class="d-flex flex-wrap gap-1 mt-1">
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

function toggleVariationType(productId, type, value, enabled, el) {
    if (!productId || !type || !value) {
        showVariationMessage('error', 'Erro: Dados inválidos');
        return;
    }

    const toggle = el || null;
    const originalState = toggle ? toggle.checked : !enabled;
    if (toggle) toggle.disabled = true;

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
        if (toggle) toggle.disabled = false;
        if (data.success) {
            const message = data.message || 'Variação atualizada com sucesso!';
            showVariationMessage('success', message);
            loadVariations(productId);
        } else {
            if (toggle) toggle.checked = !enabled;
            showVariationMessage('error', 'Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        if (toggle) toggle.disabled = false;
        if (toggle) toggle.checked = !enabled;
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
        const statusSelect = document.querySelector(`.stock-status[data-variation-id=\"${variationId}\"]`);
        const costInput = document.querySelector(`.variation-cost[data-variation-id=\"${variationId}\"]`);
        const priceInput = document.querySelector(`.variation-price[data-variation-id=\"${variationId}\"]`);
        const b2bInput = document.querySelector(`.variation-b2b[data-variation-id=\"${variationId}\"]`);
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
    
    let saveBtn = null;
    let originalText = '';
    if (event && event.target) {
        saveBtn = event.target;
        originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
    }
    
    const timeoutId = setTimeout(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        showVariationMessage('error', '⏱️ A requisição está demorando muito. Verifique sua conexão.');
    }, 60000);
    
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

function saveAndCloseModal() {
    const productId = document.getElementById('variationsProductId').value;
    
    const activeTab = document.querySelector('#variationsTabs .nav-link.active');
    if (activeTab && activeTab.getAttribute('data-bs-target') === '#stock') {
        const stockInputs = document.querySelectorAll('.stock-input[data-variation-id]');
        if (stockInputs.length > 0) {
            updateAllStock().then(data => {
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
                    if (modal) {
                        modal.hide();
                    }
                }, 1500);
            });
        } else {
            const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
            if (modal) {
                modal.hide();
            }
        }
    } else {
        const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
        if (modal) {
            modal.hide();
        }
    }
}

function showVariationMessage(type, message) {
    const existingMessage = document.getElementById('variationMessage');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.id = 'variationMessage';
    messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    messageDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(messageDiv);
    
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
                if (b2bInput) priceInput.value = '';
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
            
            if (!id) {
                console.error('ID da variação não encontrado no atributo data-variation-id');
                showVariationMessage('error', 'Erro: ID da variação não encontrado.');
                return;
            }
            
            confirmAndDeleteVariation(id, card);
        });
    }
}

function confirmAndDeleteVariation(variationId, cardElement) {
    if (!variationId) {
        showVariationMessage('error', 'ID da variação não encontrado.');
        return;
    }

    const id = parseInt(variationId, 10);
    if (isNaN(id) || id <= 0) {
        console.error('ID da variação inválido:', variationId);
        showVariationMessage('error', 'ID da variação inválido.');
        return;
    }
    
    console.log('Excluindo variação com ID:', id);

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

    fetch(`/admin/products/${productId}/variations/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();
        
        if (!response.ok) {
            showVariationMessage('error', data.message || 'Não foi possível remover a variação.');
            return;
        }
        
        if (data.success) {
            showVariationMessage('success', data.message || 'Variação removida com sucesso.');
            loadVariations(productId);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível remover a variação.');
        }
    })
    .catch(error => {
        console.error('Erro ao excluir variação:', error);
        showVariationMessage('error', 'Erro ao remover variação. Tente novamente.');
    })
    .finally(() => {
        if (cardElement) {
            cardElement.classList.remove(loaderClass);
        }
    });
}

function deleteInactiveVariations() {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId) {
        showVariationMessage('error', 'Erro: produto não identificado.');
        return;
    }

    if (!confirm('⚠️ ATENÇÃO: Esta ação é IRREVERSÍVEL!\n\nTem certeza que deseja excluir TODAS as variações desativadas deste produto?\n\nEsta ação não pode ser desfeita.')) {
        return;
    }

    fetch(`/admin/products/${productId}/variations/inactive/delete-all`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();
        
        if (!response.ok) {
            showVariationMessage('error', data.message || 'Não foi possível remover as variações desativadas.');
            return;
        }
        
        if (data.success) {
            showVariationMessage('success', data.message || 'Variações desativadas removidas com sucesso.');
            loadVariations(productId);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível remover as variações desativadas.');
        }
    })
    .catch(error => {
        console.error('Erro ao excluir variações desativadas:', error);
        showVariationMessage('error', 'Erro ao remover variações desativadas. Tente novamente.');
    });
}

// --- New UI bindings for redesigned modal ---
function applyListFilters() {
    const search = (document.getElementById('variationSearch') && document.getElementById('variationSearch').value || '').toString().toLowerCase().trim();
    const filterType = (document.getElementById('filterType') && document.getElementById('filterType').value) || '';
    const onlyActive = !!(document.getElementById('onlyActiveToggle') && document.getElementById('onlyActiveToggle').checked);

    const panes = ['colors','rams','storages','stock'];
    panes.forEach(pane => {
        const container = document.getElementById(pane + 'List');
        if (!container) return;
        const children = Array.from(container.children || []);
        children.forEach(child => {
            const text = (child.innerText || '').toString().toLowerCase();
            let visible = true;
            if (search && text.indexOf(search) === -1) visible = false;
            if (filterType && pane !== filterType) visible = false;
            if (onlyActive) {
                if (text.indexOf('inativa') !== -1 || text.indexOf('inativo') !== -1) visible = false;
            }
            child.style.display = visible ? '' : 'none';
        });
    });
}

function addGenericVariation() {
    const productIdEl = document.getElementById('variationsProductId');
    const productId = productIdEl ? productIdEl.value : null;
    if (!productId) return showVariationMessage('error', 'Produto não identificado.');

    const type = (document.getElementById('filterType') && document.getElementById('filterType').value) || '';
    if (!type) return showVariationMessage('error', 'Selecione o tipo (Cores / RAM / Armazenamento) no filtro.');

    const input = document.getElementById('newVariationValue');
    if (!input) return showVariationMessage('error', 'Campo de entrada não encontrado.');

    const value = (input.value || '').toString().trim();
    if (!value) return showVariationMessage('error', 'Por favor, insira um valor.');

    const btn = document.getElementById('addGenericBtn');
    const original = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Adicionando...'; }

    fetch(`/admin/products/${productId}/variations/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type, value })
    })
    .then(resp => resp.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.innerHTML = original; }
        if (data.success) {
            input.value = '';
            showVariationMessage('success', data.message || 'Valor adicionado com sucesso.');
            loadVariations(productId);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível adicionar o valor.');
        }
    })
    .catch(err => {
        console.error(err);
        if (btn) { btn.disabled = false; btn.innerHTML = original; }
        showVariationMessage('error', 'Erro ao adicionar valor.');
    });
}

function setupModalUIBindings() {
    document.addEventListener('click', function(e) {
        const target = e.target;
        if (!target) return;
    });

    const refreshBtn = document.getElementById('refreshVariationsBtn');
    if (refreshBtn) refreshBtn.addEventListener('click', function() {
        const pid = document.getElementById('variationsProductId').value;
        if (pid) loadVariations(pid);
    });

    const addBtn = document.getElementById('addGenericBtn');
    if (addBtn) addBtn.addEventListener('click', addGenericVariation);

    const openMgrBtn = document.getElementById('openVariationsManagerBtn');
    if (openMgrBtn) openMgrBtn.addEventListener('click', function() {
        const pid = document.getElementById('variationsProductId').value;
        if (pid) loadVariations(pid);
        const firstTab = document.querySelector('#variationsTabs .nav-link');
        if (firstTab) firstTab.click();
    });

    const bulkDeleteBtn = document.getElementById('bulkDeleteInactiveBtn');
    if (bulkDeleteBtn) bulkDeleteBtn.addEventListener('click', function() { deleteInactiveVariations(); });

    const bulkAddBtn = document.getElementById('bulkAddSelectedBtn');
    if (bulkAddBtn) bulkAddBtn.addEventListener('click', function() { showVariationMessage('error', 'Adicionar em massa ainda não implementado nesta versão.'); });

    const searchInput = document.getElementById('variationSearch');
    if (searchInput) {
        let t = null;
        searchInput.addEventListener('input', function() {
            if (t) clearTimeout(t);
            t = setTimeout(() => { applyListFilters(); }, 200);
        });
    }

    const filterSelect = document.getElementById('filterType');
    if (filterSelect) filterSelect.addEventListener('change', function() {
        const val = this.value;
        if (val) {
            const tabBtn = document.getElementById(val + '-tab');
            if (tabBtn) tabBtn.click();
        }
        applyListFilters();
    });

    const onlyActive = document.getElementById('onlyActiveToggle');
    if (onlyActive) onlyActive.addEventListener('change', applyListFilters);
}

// initialize bindings once after DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        try { setupModalUIBindings(); } catch (e) { console.debug && console.debug('setupModalUIBindings failed', e); }
    });
} else {
    try { setupModalUIBindings(); } catch (e) { console.debug && console.debug('setupModalUIBindings failed', e); }
}

// Expose commonly-used functions to global scope for compatibility with inline handlers
const exported = [
    'addNewVariationType','toggleVariationType','clearColorHex','handleColorPickerChange',
    'openColorImagesModal','saveColorImages','saveAndCloseModal','deleteInactiveVariations',
    'confirmDeleteVariationValue','updateAllStock','loadVariations','addGenericVariation','applyListFilters','setupModalUIBindings'
];

for (const name of exported) {
    try {
        if (typeof window !== 'undefined' && typeof eval(name) === 'function') {
            window[name] = eval(name);
        }
    } catch (e) {
        // ignore
    }
}

export default {};
