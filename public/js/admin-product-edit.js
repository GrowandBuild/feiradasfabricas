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
