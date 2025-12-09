/* Admin Product Edit JS
   Expects runtime config in window.adminProductEditConfig
*/
(function(){
    'use strict';
    const cfg = window.adminProductEditConfig || {};
    const productId = cfg.productId || null;
    const CSRF_TOKEN = cfg.csrfToken || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');

    // Utilities
    function normalizePrice(value) {
        if (!value && value !== 0) return null;
        let cleanValue = value.toString().trim();
        cleanValue = cleanValue.replace(/[^0-9,.-]/g, '');
        cleanValue = cleanValue.replace('\u00a0', '').replace('\u00a0', '');
        if (cleanValue === '' || cleanValue === ',') return null;
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

    function formatCurrency(value) {
        if (value === null || value === undefined || value === '') return '';
        const numberValue = typeof value === 'number' ? value : parseFloat(value);
        if (isNaN(numberValue)) return '';
        return numberValue.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Update cost price from server (keeps existing behavior)
    function updateCostPriceFromServer(costPrice) {
        if (!productId || !costPrice || costPrice <= 0) return;
        const costPriceInput = document.getElementById('cost_price');
        const priceInput = document.getElementById('price');
        const b2bPriceInput = document.getElementById('b2b_price');
        const loaderClass = 'is-loading';
        if (costPriceInput) { costPriceInput.classList.add(loaderClass); costPriceInput.disabled = true; }

        fetch(`/admin/products/${productId}/update-cost-price`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ cost_price: costPrice })
        }).then(r => r.json()).then(data => {
            if (data && data.success && data.product) {
                const { cost_price, b2c_price, b2b_price } = data.product;
                if (costPriceInput) costPriceInput.value = cost_price ?? formatCurrency(costPrice);
                if (priceInput && b2c_price) priceInput.value = b2c_price;
                if (b2bPriceInput && b2b_price) b2bPriceInput.value = b2b_price;
                if (costPriceInput) {
                    costPriceInput.classList.add('border-success');
                    setTimeout(() => costPriceInput.classList.remove('border-success'), 2000);
                }
            } else {
                throw new Error((data && data.message) || 'Erro ao atualizar preços');
            }
        }).catch(err => {
            console.error('Erro ao atualizar custo:', err);
            alert('Erro ao atualizar preços com base no custo. Tente novamente.');
        }).finally(() => { if (costPriceInput) { costPriceInput.disabled = false; costPriceInput.classList.remove(loaderClass); } });
    }

    // Main DOM ready handler
    document.addEventListener('DOMContentLoaded', function(){
        try {
            // Price inputs
            const priceInput = document.getElementById('price');
            const b2bPriceInput = document.getElementById('b2b_price');
            const costPriceInput = document.getElementById('cost_price');

            // B2B auto update when price changes
            if (priceInput && b2bPriceInput) {
                priceInput.addEventListener('input', function(){
                    const normalized = normalizePrice(priceInput.value);
                    if (normalized !== null) {
                        const newB2BPrice = normalized * 0.9;
                        b2bPriceInput.value = formatCurrency(newB2BPrice);
                    }
                });
            }

            // Image upload preview (additional simple support to complement imageManager/alpine)
            const imageInput = document.getElementById('images');
            const container = document.getElementById('images-container');
            if (imageInput && container) {
                imageInput.addEventListener('change', function(e){
                    const files = e.target.files; if (!files || files.length === 0) return;
                    // remove previous new-image-preview elements
                    const newImagePreviews = container.querySelectorAll('.new-image-preview');
                    newImagePreviews.forEach(p => p.remove());
                    Array.from(files).forEach((file) => {
                        if (!file.type.startsWith('image/') && !file.name.toLowerCase().endsWith('.avif')) return;
                        const reader = new FileReader();
                        reader.onload = function(ev){
                            const col = document.createElement('div');
                            col.className = 'col-md-3 mb-2 new-image-preview';
                            col.setAttribute('data-file-name', file.name);
                            col.innerHTML = `
                                <div class="position-relative">
                                    <img src="${ev.target.result}" class="img-thumbnail" style="width:100%; height:100px; object-fit:cover; cursor:pointer;" alt="Preview">
                                    <span class="badge bg-success position-absolute top-0 start-0 badge-circle badge-circle-sm m-2">Nova</span>
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeNewImagePreview(this, '${file.name}')"><i class="bi bi-x"></i></button>
                                </div>
                            `;
                            container.appendChild(col);
                        };

            // Carrega e renderiza variações existentes no modal
            window.loadVariations = window.loadVariations || function(pid){
                const target = document.getElementById('variationsList');
                const effectiveId = pid || productId;
                if (!target || !effectiveId) return;

                target.innerHTML = '<div class="text-muted">Carregando variações...</div>';

                fetch(`/admin/products/${effectiveId}/variations`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    try {
                        const list = Array.isArray(data) ? data : (data.variations || []);
                        if (!list || list.length === 0) {
                            target.innerHTML = '<p class="text-muted mb-0">Nenhuma variação cadastrada para este produto.</p>';
                            return;
                        }

                        let html = '';
                        html += '<div class="table-responsive">';
                        html += '<table class="table table-sm align-middle">';
                        html += '<thead><tr>' +
                            '<th>Atributos</th>' +
                            '<th>SKU</th>' +
                            '<th class="text-end">Preço</th>' +
                            '<th class="text-end">Estoque</th>' +
                            '<th class="text-center">Ativo</th>' +
                            '<th class="text-center">Ações</th>' +
                            '</tr></thead><tbody>';

                        list.forEach(v => {
                            const attrs = [];
                            if (v.color) attrs.push('Cor: ' + v.color);
                            if (v.ram) attrs.push('RAM: ' + v.ram);
                            if (v.storage) attrs.push('Armazenamento: ' + v.storage);
                            const attrsLabel = attrs.length ? attrs.join(' · ') : (v.attributes_label || '-');

                            const sku = v.sku || '';
                            const priceRaw = typeof v.price !== 'undefined' ? v.price : '';
                            const priceFormatted = priceRaw ? formatCurrency(priceRaw) : '';
                            const stock = typeof v.stock_quantity !== 'undefined' ? v.stock_quantity : '';
                            const active = (v.is_active === true || v.is_active === 1 || v.is_active === '1');

                            html += '<tr>' +
                                '<td>' + attrsLabel + '</td>' +
                                '<td><input type="text" class="form-control form-control-sm text-center variation-sku" data-variation-id="' + (v.id || '') + '" value="' + (sku || '') + '" placeholder="SKU"></td>' +
                                '<td class="text-end"><input type="text" class="form-control form-control-sm text-end variation-price" data-variation-id="' + (v.id || '') + '" value="' + priceFormatted + '" placeholder="0,00" data-raw="' + priceRaw + '"></td>' +
                                '<td class="text-end"><input type="number" class="form-control form-control-sm text-end variation-stock" data-variation-id="' + (v.id || '') + '" value="' + (stock !== '' ? stock : '') + '" placeholder="0"></td>' +
                                '<td class="text-center"><div class="form-check form-switch d-inline-block"><input class="form-check-input variation-active" type="checkbox" data-variation-id="' + (v.id || '') + '" ' + (active ? 'checked' : '') + '></div></td>' +
                                '<td class="text-center"><button class="btn btn-sm btn-outline-danger me-1 delete-variation" data-variation-id="' + (v.id || '') + '" title="Excluir"><i class="bi bi-trash"></i></button><button class="btn btn-sm btn-outline-secondary toggle-variation" data-variation-id="' + (v.id || '') + '" title="' + (active ? 'Desativar' : 'Ativar') + '"><i class="bi bi-' + (active ? 'pause' : 'play') + '"></i></button></td>' +
                                '</tr>';
                        });

                        html += '</tbody></table></div>';
                        target.innerHTML = html;
                    } catch (e) {
                        console.error('Erro ao renderizar variações', e);
                        target.innerHTML = '<p class="text-danger">Erro ao exibir variações. Veja o console.</p>';
                    }
                })
                .catch(err => {
                    console.error('Erro ao carregar variações', err);
                    target.innerHTML = '<p class="text-danger">Erro ao carregar variações. Veja o console.</p>';
                });
            };
                        reader.readAsDataURL(file);
                    });
                });
            }

            // Toast helper
            window.showToast = window.showToast || function(type, message, title){
                try {
                    const containerId = 'toastContainer';
                    let container = document.getElementById(containerId);
                    if (!container) { container = document.createElement('div'); container.id = containerId; container.className = 'position-fixed top-0 end-0 p-3'; container.style.zIndex = 10850; document.body.appendChild(container); }
                    const toastId = 'toast-' + Date.now();
                    const toastEl = document.createElement('div');
                    toastEl.id = toastId;
                    toastEl.className = 'toast align-items-center text-bg-' + (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'danger')) + ' border-0';
                    toastEl.setAttribute('role','alert'); toastEl.setAttribute('aria-live','assertive'); toastEl.setAttribute('aria-atomic','true'); toastEl.style.minWidth = '250px';
                    toastEl.innerHTML = `<div class="d-flex"><div class="toast-body">${title ? '<strong>'+title+'</strong><br/>' : ''}${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
                    container.appendChild(toastEl);
                    const bToast = new bootstrap.Toast(toastEl, { delay: 4500 }); bToast.show();
                    toastEl.addEventListener('hidden.bs.toast', function(){ try { toastEl.remove(); } catch(e){} });
                } catch(e) { console.error('showToast failed', e); alert(message); }
            };

            // AJAX Save
            (function bindAjaxSave(){
                const ajaxSaveBtn = document.getElementById('ajaxSaveBtn');
                const form = document.querySelector('form');
                const csrf = CSRF_TOKEN || '';
                if (!ajaxSaveBtn || !form) return;
                ajaxSaveBtn.addEventListener('click', function(){
                    ajaxSaveBtn.disabled = true; ajaxSaveBtn.textContent = 'Salvando...';
                    const fd = new FormData(form); if (!fd.has('_method')) fd.append('_method','PUT');
                    fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: fd, credentials: 'same-origin' })
                        .then(res => res.json().then(body => ({ status: res.status, body })))
                        .then(r => {
                            if (r.status >= 200 && r.status < 300 && r.body) { window.showToast('success', 'Produto atualizado com sucesso!', 'Sucesso'); setTimeout(()=>location.reload(), 700); }
                            else { console.error('Save error', r.body); window.showToast('error', 'Erro ao salvar. Veja console para detalhes.', 'Erro'); }
                        }).catch(err => { console.error(err); window.showToast('error', 'Erro de rede ao salvar.', 'Rede'); })
                        .finally(()=>{ ajaxSaveBtn.disabled = false; ajaxSaveBtn.textContent = 'Salvar alterações'; });
                });
            })();

            // Quick toggle active
            (function bindQuickToggle(){
                const quickToggle = document.getElementById('quickToggleActive');
                if (!quickToggle || !productId) return;
                quickToggle.addEventListener('click', function(){
                    const newState = cfg.productIsActive ? '0' : '1';
                    quickToggle.disabled = true; quickToggle.textContent = 'Aguarde...';
                    fetch(`/admin/products/${productId}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, body: JSON.stringify({ _method: 'PUT', is_active: newState }) })
                    .then(r => r.json()).then(data => {
                        if (data && data.success) {
                            const badge = document.getElementById('statusBadge');
                            if (badge) { if (newState == '1') { badge.className = 'badge bg-success'; badge.textContent = 'Ativo'; } else { badge.className = 'badge bg-danger'; badge.textContent = 'Inativo'; } }
                            quickToggle.textContent = newState == '1' ? 'Desativar' : 'Ativar';
                        } else { window.showToast('error', 'Erro ao alternar estado', 'Erro'); }
                    }).catch(err => { console.error(err); alert('Erro ao alternar estado'); }).finally(()=>{ quickToggle.disabled = false; });
                });
            })();

            // Open variations modal button
            (function bindOpenVariations(){
                const openBtn = document.getElementById('openVariationsManager');
                if (!openBtn) return;
                openBtn.addEventListener('click', function(){
                    const modalEl = document.getElementById('variationsModal');
                    if (modalEl) {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                        try {
                            window.loadVariations && window.loadVariations(productId);
                        } catch(e) {
                            console.debug && console.debug('loadVariations failed', e);
                        }
                    } else {
                        window.showToast('warning', 'Gerenciador de variações indisponível', 'Indisponível');
                    }
                });
            })();

            // Edição inline na tabela de variações
            (function bindInlineVariationEdits(){
                function saveVariationField(variationId, field, value) {
                    const payload = { field: field, value: value };
                    fetch(`/admin/products/${productId}/variations/${variationId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            window.showToast('success', 'Variação atualizada com sucesso', 'Sucesso');
                        } else {
                            window.showToast('error', data.message || 'Erro ao atualizar variação', 'Erro');
                        }
                    })
                    .catch(err => {
                        console.error('Erro ao salvar variação', err);
                        window.showToast('error', 'Erro ao salvar variação. Veja o console.', 'Erro');
                    });
                }

                function handlePriceInput(input) {
                    let raw = input.value.replace(/[^\d,]/g, '');
                    const parts = raw.split(',');
                    if (parts.length > 2) raw = parts[0] + ',' + parts.slice(1).join('');
                    if (parts.length === 2 && parts[1].length > 2) raw = parts[0] + ',' + parts[1].slice(0,2);
                    input.value = raw;
                    const numeric = parseFloat(raw.replace(',', '.')) || 0;
                    input.setAttribute('data-raw', numeric);
                }

                // Delegated events para inputs dinâmicos
                document.getElementById('variationsList')?.addEventListener('input', function(e){
                    const input = e.target;
                    const variationId = input.getAttribute('data-variation-id');
                    if (!variationId) return;
                    if (input.classList.contains('variation-price')) {
                        handlePriceInput(input);
                    } else if (input.classList.contains('variation-stock')) {
                        // apenas garantir que é número
                    }
                });

                document.getElementById('variationsList')?.addEventListener('change', function(e){
                    const input = e.target;
                    const variationId = input.getAttribute('data-variation-id');
                    if (!variationId) return;
                    let field = null, value = null;
                    if (input.classList.contains('variation-price')) {
                        field = 'price';
                        value = parseFloat(input.getAttribute('data-raw')) || 0;
                    } else if (input.classList.contains('variation-stock')) {
                        field = 'stock_quantity';
                        value = parseInt(input.value, 10) || 0;
                    } else if (input.classList.contains('variation-sku')) {
                        field = 'sku';
                        value = input.value.trim();
                    } else if (input.classList.contains('variation-active')) {
                        field = 'is_active';
                        value = input.checked ? 1 : 0;
                    }
                    if (field !== null) {
                        saveVariationField(variationId, field, value);
                    }
                });

                // Delegated events para botões de ação (excluir/toggle)
                document.getElementById('variationsList')?.addEventListener('click', function(e){
                    const btn = e.target.closest('.delete-variation, .toggle-variation');
                    if (!btn) return;
                    const variationId = btn.getAttribute('data-variation-id');
                    if (!variationId) return;
                    if (btn.classList.contains('delete-variation')) {
                        if (!confirm('Tem certeza que deseja excluir esta variação?')) return;
                        fetch(`/admin/products/${productId}/variations/${variationId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                window.showToast('success', 'Variação excluída com sucesso', 'Sucesso');
                                window.loadVariations(productId);
                            } else {
                                window.showToast('error', data.message || 'Erro ao excluir variação', 'Erro');
                            }
                        })
                        .catch(err => {
                            console.error('Erro ao excluir variação', err);
                            window.showToast('error', 'Erro ao excluir variação. Veja o console.', 'Erro');
                        });
                    } else if (btn.classList.contains('toggle-variation')) {
                        const currentlyActive = btn.getAttribute('title')?.includes('Desativar');
                        const newActive = currentlyActive ? 0 : 1;
                        saveVariationField(variationId, 'is_active', newActive);
                    }
                });
            })();

            // Aba Visual: gerar variações por atributos
            (function bindVisualVariationsGenerator(){
                const deptSelect = document.getElementById('visualDepartmentSelect');
                const attrContainer = document.getElementById('visualAttributesContainer');
                const generateBtn = document.getElementById('visualGenerateBtn');
                const previewBtn = document.getElementById('visualPreviewBtn');
                const spinner = document.getElementById('visualSpinner');
                const resultDiv = document.getElementById('visualResult');

                if (!deptSelect || !attrContainer || !generateBtn || !previewBtn || !spinner || !resultDiv) return;

                // Carregar departamentos no select
                fetch('/admin/departments?simple=1', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(depts => {
                    deptSelect.innerHTML = '<option value="">Selecione um departamento...</option>';
                    if (Array.isArray(depts)) {
                        depts.forEach(d => {
                            deptSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                        });
                    }
                })
                .catch(() => {
                    deptSelect.innerHTML = '<option value="">Erro ao carregar departamentos</option>';
                });

                // Ao selecionar departamento: carregar atributos
                deptSelect.addEventListener('change', function(){
                    const deptId = this.value;
                    if (!deptId) {
                        attrContainer.innerHTML = '<div class="text-muted small">Selecione um departamento para carregar os atributos.</div>';
                        generateBtn.disabled = true;
                        previewBtn.disabled = true;
                        return;
                    }
                    attrContainer.innerHTML = '<div class="text-muted small">Carregando atributos...</div>';
                    fetch(`/admin/attributes?department_id=${deptId}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        const attrs = Array.isArray(data) ? data : (data.data || []);
                        if (!attrs.length) {
                            attrContainer.innerHTML = '<div class="text-muted small">Nenhum atributo encontrado para este departamento.</div>';
                            generateBtn.disabled = true;
                            previewBtn.disabled = true;
                            return;
                        }
                        let html = '';
                        attrs.forEach(attr => {
                            html += `<div class="mb-3"><label class="form-label small fw-bold">${attr.name}</label><div class="d-flex flex-wrap gap-2">`;
                            if (Array.isArray(attr.values) && attr.values.length) {
                                attr.values.forEach(val => {
                                    const valId = val.id || val.value;
                                    const valLabel = val.value || val;
                                    html += `<label class="form-check form-check-inline"><input type="checkbox" class="form-check-input" data-attr="${attr.key}" data-value="${valLabel}" value="${valId}"> <span class="form-check-label">${valLabel}</span></label>`;
                                });
                            } else {
                                html += '<span class="text-muted small">Nenhum valor cadastrado</span>';
                            }
                            html += '</div></div>';
                        });
                        attrContainer.innerHTML = html;
                        generateBtn.disabled = false;
                        previewBtn.disabled = false;
                    })
                    .catch(() => {
                        attrContainer.innerHTML = '<div class="text-danger small">Erro ao carregar atributos.</div>';
                        generateBtn.disabled = true;
                        previewBtn.disabled = true;
                    });
                });

                // Gerar combinações (preview ou save)
                function generateCombinations(save = false) {
                    const checkboxes = attrContainer.querySelectorAll('input[type="checkbox"]:checked');
                    if (!checkboxes.length) {
                        resultDiv.innerHTML = '<div class="alert alert-warning py-2">Selecione ao menos um valor de atributo.</div>';
                        return;
                    }
                    const attrsMap = {};
                    checkboxes.forEach(cb => {
                        const key = cb.getAttribute('data-attr');
                        const value = cb.getAttribute('data-value');
                        if (!attrsMap[key]) attrsMap[key] = [];
                        attrsMap[key].push(value);
                    });
                    // Cartesian product
                    const keys = Object.keys(attrsMap);
                    const combos = keys.reduce((acc, key) => {
                        const values = attrsMap[key];
                        if (!acc.length) return values.map(v => ({ [key]: v }));
                        const newAcc = [];
                        acc.forEach((combo) => {
                            values.forEach(v => {
                                newAcc.push({ ...combo, [key]: v });
                            });
                        });
                        return newAcc;
                    }, []);
                    if (save) {
                        spinner.classList.remove('d-none');
                        generateBtn.disabled = true;
                        previewBtn.disabled = true;
                        fetch(`/admin/products/${productId}/variations/bulk-add`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ combinations: combos })
                        })
                        .then(r => r.json())
                        .then(data => {
                            spinner.classList.add('d-none');
                            generateBtn.disabled = false;
                            previewBtn.disabled = false;
                            if (data.success) {
                                resultDiv.innerHTML = '<div class="alert alert-success py-2">' + (data.message || 'Variações geradas com sucesso!') + '</div>';
                                // Recarregar aba Existentes
                                window.loadVariations && window.loadVariations(productId);
                            } else {
                                resultDiv.innerHTML = '<div class="alert alert-danger py-2">' + (data.message || 'Erro ao gerar variações.') + '</div>';
                            }
                        })
                        .catch(err => {
                            spinner.classList.add('d-none');
                            generateBtn.disabled = false;
                            previewBtn.disabled = false;
                            console.error(err);
                            resultDiv.innerHTML = '<div class="alert alert-danger py-2">Erro ao gerar variações. Veja o console.</div>';
                        });
                    } else {
                        // Preview
                        let html = '<div class="alert alert-info py-2">' + combos.length + ' combinações serão geradas:</div><div class="small">';
                        combos.slice(0, 20).forEach(c => {
                            const parts = keys.map(k => `${k}: ${c[k]}`);
                            html += '<div>- ' + parts.join(' · ') + '</div>';
                        });
                        if (combos.length > 20) html += '<div>... e mais ' + (combos.length - 20) + '</div>';
                        html += '</div>';
                        resultDiv.innerHTML = html;
                    }
                }

                previewBtn.addEventListener('click', () => generateCombinations(false));
                generateBtn.addEventListener('click', () => generateCombinations(true));
            })();
            try {
                const pt = document.getElementById('product_type');
                const stockRow = document.getElementById('stockFieldsRow');
                const stockQty = document.getElementById('stock_quantity');
                const minStock = document.getElementById('min_stock');
                const apply = function(){
                    if (!pt || !stockRow) return;
                    if ((pt.value || 'physical') === 'service') {
                        stockRow.style.display = 'none'; if (stockQty) { stockQty.required = false; stockQty.value = '' } if (minStock) { minStock.required = false; minStock.value = '' }
                    } else { stockRow.style.display = ''; if (stockQty) stockQty.required = true; if (minStock) minStock.required = true; }
                };
                pt && pt.addEventListener('change', apply);
                apply();
            } catch(e){ console.debug && console.debug('product_type toggle failed', e); }

            // Dept attribute module: fetch and render
            (function deptAttributesModule(){
                try {
                    const deptAttributesPanel = document.getElementById('deptAttributesPanel');
                    if (!deptAttributesPanel) return;
                    let currentDepartment = cfg.productDepartmentId || null;

                    function renderAttributes(data) {
                        if (!deptAttributesPanel) return;
                        deptAttributesPanel.innerHTML = '';
                        if (!data || !data.attributes || data.attributes.length === 0) { deptAttributesPanel.innerHTML = '<p class="text-muted">Nenhum atributo encontrado para este departamento.</p>'; return; }
                        data.attributes.forEach(attr => {
                            const group = document.createElement('div'); group.className = 'mb-3';
                            const title = document.createElement('label'); title.className = 'form-label fw-semibold'; title.textContent = attr.name || attr.key; group.appendChild(title);
                            const wrap = document.createElement('div'); wrap.className = 'd-flex flex-wrap gap-2';
                            attr.values.forEach(v => {
                                const id = `dept-attr-${attr.key}-${v.value}`.replace(/[^a-zA-Z0-9-_]/g, '_');
                                const div = document.createElement('div'); div.className = 'form-check'; div.style.minWidth = '160px';
                                const input = document.createElement('input'); input.type = 'checkbox'; input.className = 'form-check-input dept-attr-checkbox'; input.id = id; input.dataset.type = attr.key; input.dataset.value = v.value;
                                const label = document.createElement('label'); label.className = 'form-check-label'; label.htmlFor = id;
                                if (attr.key === 'color' && v.hex) { label.innerHTML = `<span class="me-2" style="display:inline-block;width:18px;height:14px;background:${v.hex};border:1px solid #ddd;vertical-align:middle;"></span> ${v.value}`; } else { label.textContent = v.value; }
                                div.appendChild(input); div.appendChild(label);
                                if (v.value_id && attr.attribute_id) {
                                    const actBadge = document.createElement('span'); actBadge.setAttribute('role','button'); actBadge.className = 'dept-attr-action-badge ms-2 ' + (v.is_active ? 'bg-success text-white' : 'bg-secondary text-white'); actBadge.textContent = v.is_active ? 'Ativo' : 'Inativo'; actBadge.title = v.is_active ? 'Desativar valor' : 'Ativar valor'; actBadge.style.padding = '0.25rem 0.5rem'; actBadge.style.fontSize = '0.75rem'; actBadge.style.borderRadius = '0.25rem'; actBadge.style.cursor = 'pointer';
                                    actBadge.addEventListener('click', function(){ try{ actBadge.setAttribute('aria-busy','true'); actBadge.style.opacity='0.6'; actBadge.style.pointerEvents='none'; }catch(e){}; const shouldActivate = !v.is_active; const payload = { value: v.value }; if (shouldActivate) payload.is_active = true; fetch(`/admin/attributes/${attr.attribute_id}/values/${v.value_id}`, { method: 'PATCH', headers: { 'Content-Type':'application/json','X-CSRF-TOKEN': CSRF_TOKEN,'Accept':'application/json' }, body: JSON.stringify(payload) }).then(()=>{ fetchAndRenderForDepartment(currentDepartment); }).catch(err=>{ console.error('Erro ao alternar ativo do valor:', err); fetchAndRenderForDepartment(currentDepartment); }).finally(()=>{ try{ actBadge.removeAttribute('aria-busy'); actBadge.style.opacity=''; actBadge.style.pointerEvents=''; }catch(e){} }); });
                                    div.appendChild(actBadge);
                                }
                                wrap.appendChild(div);
                            });
                            group.appendChild(wrap); deptAttributesPanel.appendChild(group);
                        });
                        // actions
                        const actions = document.createElement('div'); actions.className = 'd-flex gap-2 mt-2';
                        const addBtn = document.createElement('button'); addBtn.type='button'; addBtn.className='btn btn-sm btn-primary'; addBtn.textContent='Adicionar valores selecionados como variações'; addBtn.addEventListener('click', applySelectedAttributes);
                        const openModalBtn = document.createElement('button'); openModalBtn.type='button'; openModalBtn.className='btn btn-sm btn-outline-secondary'; openModalBtn.textContent='Abrir Gerenciador de Variações'; openModalBtn.addEventListener('click', function(){ const modalEl = document.getElementById('variationsModal'); if (modalEl) { const modal = new bootstrap.Modal(modalEl); try { const prodIdInput = document.getElementById('variationsProductId'); if (prodIdInput) prodIdInput.value = productId; const btn = document.querySelector('[data-bs-target="#variationsModal"]'); if (btn) btn.setAttribute('data-product-id', productId); } catch(e){} modal.show(); } });
                        const refreshBtn = document.createElement('button'); refreshBtn.type='button'; refreshBtn.className='btn btn-sm btn-outline-info'; refreshBtn.textContent='Atualizar atributos'; refreshBtn.addEventListener('click', function(){ fetchAndRenderForDepartment(currentDepartment); });
                        actions.appendChild(addBtn); actions.appendChild(refreshBtn); actions.appendChild(openModalBtn); deptAttributesPanel.appendChild(actions);
                        try { if (window.syncDeptAttributesWithVariations && productId) window.syncDeptAttributesWithVariations(productId); } catch(e) { console.debug && console.debug('sync call failed', e); }
                    }

                    function applySelectedAttributes(){
                        const checkboxes = Array.from(document.querySelectorAll('.dept-attr-checkbox')).filter(cb => cb.checked);
                        if (checkboxes.length === 0) { alert('Selecione ao menos um valor para adicionar.'); return; }
                        if (!confirm(`Adicionar ${checkboxes.length} valor(es) selecionado(s) como variações deste produto?`)) return;
                        const groups = {}; checkboxes.forEach(cb => { const type = cb.dataset.type; const value = cb.dataset.value; if (!groups[type]) groups[type]=[]; groups[type].push(value); });
                        const keys = Object.keys(groups); const arrays = keys.map(k => groups[k].map(v => ({ key: k, value: v })));
                        function cartesianProduct(arr){ return arr.reduce((a,b) => a.flatMap(d => b.map(e => d.concat([e]))), [[]]); }
                        function slugify(str){ return String(str||'').toLowerCase().normalize('NFD').replace(/[^\w\s-]/g,'').replace(/\s+/g,'_').replace(/[^a-z0-9_-]/g,'').replace(/^_+|_+$/g,''); }
                        const combos = cartesianProduct(arrays).map(combo => { const attrs = {}; combo.forEach(c => { attrs[slugify(c.key)] = c.value; }); return attrs; });
                        if (combos.length > 300) { if (!confirm(`Serão criadas ${combos.length} variações. Continuar?`)) return; }
                        fetch(`/admin/products/${productId}/variations/bulk-add`, { method: 'POST', headers: { 'Content-Type':'application/json','X-CSRF-TOKEN': CSRF_TOKEN,'Accept':'application/json' }, body: JSON.stringify({ combos }) }).then(r => r.json()).then(data => {
                            if (data && data.success) {
                                alert(`Operação concluída. ${data.created} nova(s) variação(ões) criada(s).`);
                                const variationsModalEl = document.getElementById('variationsModal'); if (variationsModalEl && bootstrap.Modal.getInstance(variationsModalEl)) { const prodIdInput = document.getElementById('variationsProductId'); if (prodIdInput) loadVariations(prodIdInput.value); }
                                try { if (typeof fetchAndRenderForDepartment === 'function') fetchAndRenderForDepartment(currentDepartment); fetch(`/admin/products/${productId}/variations`, { headers: { 'Accept':'application/json' } }).then(resp => resp.json()).then(vdata => {
                                    if (vdata && vdata.attribute_groups) {
                                        const lookup = {}; Object.keys(vdata.attribute_groups).forEach(type => { lookup[type] = new Set((vdata.attribute_groups[type] || []).map(i => (i.name||'').toString().toLowerCase())); });
                                        document.querySelectorAll('.dept-attr-checkbox').forEach(cb => { const type = (cb.dataset.type||'').toString(); const val = (cb.dataset.value||'').toString().toLowerCase(); if (lookup[type] && lookup[type].has(val)) { cb.classList.add('variation-exists'); try { cb.disabled = true; cb.setAttribute('aria-disabled','true'); } catch(e){} const label = cb.nextElementSibling || cb.closest('label') || null; if (label && !label.querySelector('.badge-created')) { const badge = document.createElement('span'); badge.className = 'badge bg-success ms-2 badge-created'; badge.style.fontSize = '0.7em'; badge.textContent = 'Criada'; badge.setAttribute('title', `Criada em ${(new Date()).toLocaleString()}`); label.appendChild(badge); } } });
                                    }
                                }).catch(e=>console.debug && console.debug('Erro ao sincronizar variações:', e)); } catch(e){ console.debug && console.debug('Erro na pós-sincronização de atributos:', e); }
                            } else { console.error('bulk-add failed', data); alert('Erro ao criar variações em lote. Veja console para detalhes.'); }
                        }).catch(err => { console.error(err); alert('Erro ao criar variações. Veja console para detalhes.'); });
                    }

                    function fetchAndRenderForDepartment(dept){ if (!dept) { deptAttributesPanel.innerHTML = '<p class="text-muted">Produto sem departamento definido. Atribua um departamento para obter sugestões.</p>'; return; } deptAttributesPanel.innerHTML = '<p class="text-muted">Carregando atributos do departamento...</p>'; fetch(`/admin/attributes/list?department=${dept}`, { headers: { 'Accept':'application/json' } }).then(r => r.json()).then(data => { renderAttributes(data); }).catch(err => { console.error('Erro ao carregar atributos do departamento:', err); deptAttributesPanel.innerHTML = '<p class="text-muted text-danger">Erro ao carregar atributos. Verifique o console.</p>'; }); }

                    fetchAndRenderForDepartment(currentDepartment);

                    // listen for external events
                    document.addEventListener('variations:updated', function(e){ try { const pid = (e && e.detail && e.detail.productId) ? e.detail.productId : productId; if (window.syncDeptAttributesWithVariations) window.syncDeptAttributesWithVariations(pid); } catch(err) { console.debug && console.debug('variations:updated handler failed', err); } });

                    // bind department change selectors
                    function bindDepartmentChange(){ const selectors = [document.querySelector('select[name="department_id"]'), document.getElementById('qpDepartment'), document.getElementById('qpDeptCombo'), document.getElementById('department_id')]; selectors.forEach(el => { if (!el) return; const handler = function(){ let val = el.value || null; if (el.id === 'qpDeptCombo') { const sel = document.getElementById('qpDepartment'); if (sel && sel.value) val = sel.value; } if (val === null || val === '') { currentDepartment = null; fetchAndRenderForDepartment(null); return; } if (val == currentDepartment) return; currentDepartment = val; fetchAndRenderForDepartment(currentDepartment); }; el.addEventListener('change', handler); el.addEventListener('input', handler); }); }
                    bindDepartmentChange();

                    (function startDeptPoll(){ let last = currentDepartment; setInterval(function(){ const candidates = [document.querySelector('select[name="department_id"]'), document.getElementById('qpDepartment'), document.getElementById('qpDeptCombo'), document.getElementById('department_id')]; let found = null; for (const el of candidates) { if (!el) continue; let v = el.value || null; if (el.id === 'qpDeptCombo') { const sel = document.getElementById('qpDepartment'); if (sel && sel.value) v = sel.value; } if (v) { found = v; break; } } if ((found || null) !== last) { last = found || null; currentDepartment = last; fetchAndRenderForDepartment(currentDepartment); } }, 800); })();

                } catch(e){ console.error('Erro no módulo de atributos do departamento:', e); }
            })();

            // Advanced fields toggle
            try {
                const adv = document.getElementById('advancedFields');
                const btn = document.getElementById('toggleAdvancedFieldsBtn');
                const key = 'prodEditAdvancedVisible';
                const saved = localStorage.getItem(key);
                const visible = saved === null ? false : (saved === '1');
                function setVisible(v){ if (!adv) return; adv.style.display = v ? '' : 'none'; if (btn) btn.innerHTML = v ? '<i class="bi bi-arrows-collapse me-1"></i> Ocultar' : '<i class="bi bi-arrows-expand me-1"></i> Mostrar'; localStorage.setItem(key, v ? '1' : '0'); }
                if (adv) setVisible(visible); if (btn) btn.addEventListener('click', function(){ setVisible(!(adv.style.display === '' || adv.style.display === 'block')); });
            } catch(e){ console.debug && console.debug('toggle advanced failed', e); }

        } catch(e){ console.error('Erro inicializando admin-product-edit.js', e); }
    });

})();
