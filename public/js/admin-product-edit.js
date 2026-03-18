/* Admin Product Edit JS
   Expects runtime config in window.adminProductEditConfig
*/
(function(){
    'use strict';
    
    // Shared image validation (must be available to global listeners too)
    function validateImageFile(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];

        if (!file) return false;

        // Some browsers may not set type for certain clipboard images; fallback to extension
        const name = (file.name || '').toLowerCase();
        const typeOk = allowedTypes.includes(file.type) || name.endsWith('.avif');

        if (!typeOk) {
            try {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'Formato de arquivo não permitido. Use: JPG, PNG, GIF, WEBP ou AVIF', 'Erro');
                } else {
                    console.error('Formato de arquivo não permitido. Use: JPG, PNG, GIF, WEBP ou AVIF');
                }
            } catch(e) {
                console.error('Formato de arquivo não permitido. Use: JPG, PNG, GIF, WEBP ou AVIF');
            }
            return false;
        }

        if (file.size > maxSize) {
            try {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'Arquivo muito grande. Tamanho máximo: 10MB', 'Erro');
                } else {
                    console.error('Arquivo muito grande. Tamanho máximo: 10MB');
                }
            } catch(e) {
                console.error('Arquivo muito grande. Tamanho máximo: 10MB');
            }
            return false;
        }

        return true;
    }

    // expose for other scripts / debugging
    window.validateImageFile = window.validateImageFile || validateImageFile;

    // Function to get config with retry mechanism
    function getConfigWithRetry(maxRetries = 10, delay = 100) {
        return new Promise((resolve, reject) => {
            let retries = 0;
            
            function checkConfig() {
                if (window.adminProductEditConfig) {
                    resolve(window.adminProductEditConfig);
                } else if (retries >= maxRetries) {
                    console.warn('adminProductEditConfig not found after retries, using fallback');
                    resolve({});
                } else {
                    retries++;
                    setTimeout(checkConfig, delay);
                }
            }
            
            checkConfig();
        });
    }
    
    // Initialize when DOM is ready and config is available
    document.addEventListener('DOMContentLoaded', function(){
        getConfigWithRetry().then(cfg => {
            initializeProductEdit(cfg);
        });
    });
    
    function initializeProductEdit(cfg) {
        const productId = cfg.productId || null;
        const CSRF_TOKEN = cfg.csrfToken || (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');
        
        console.log('🔍 DEBUG: admin-product-edit.js initialized with config:', cfg);

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

        // Função para remover preview de nova imagem
        window.removeNewImagePreview = function(button, fileName) {
            const col = button.closest('.new-image-preview');
            if (col) {
                col.remove();
            }
            
            // Remover do input file
            const input = document.getElementById('images');
            if (input) {
                const dt = new DataTransfer();
                Array.from(input.files).forEach(f => {
                    if (f.name !== fileName) {
                        dt.items.add(f);
                    }
                });
                input.files = dt.files;
            }
        };
        
        console.log('🔍 DEBUG: admin-product-edit.js loaded successfully');
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
            console.log('🔍 Debug - imageInput:', imageInput);
            console.log('🔍 Debug - container:', container);
            
            if (imageInput && container) {
                imageInput.addEventListener('change', function(e){
                    console.log('🔍 Debug - change event fired, files:', e.target.files);
                    const files = e.target.files; if (!files || files.length === 0) return;
                    console.log('🔍 Debug - processing', files.length, 'files');
                    
                    // remove previous new-image-preview elements
                    const newImagePreviews = container.querySelectorAll('.new-image-preview');
                    newImagePreviews.forEach(p => p.remove());
                    Array.from(files).forEach((file) => {
                        if (!file.type.startsWith('image/') && !file.name.toLowerCase().endsWith('.avif')) return;
                        console.log('🔍 Debug - processing file:', file.name);
                        const reader = new FileReader();
                        reader.onload = function(ev){
                            console.log('🔍 Debug - file loaded, creating preview');
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
            } else {
                console.log('🔍 Debug - imageInput or container not found');
                console.log('🔍 Debug - imageInput exists:', !!imageInput);
                console.log('🔍 Debug - container exists:', !!container);
            }

            // Melhorar a zona de colar imagens com drag-and-drop
            const pasteZone = document.getElementById('editProductPasteZone') || document.getElementById('createProductPasteZone');
            const fileInput = document.getElementById('images');
            
            if (pasteZone && fileInput) {
                // Click para abrir file dialog
                pasteZone.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'INPUT') {
                        fileInput.click();
                    }
                });
                
                // Drag and Drop
                pasteZone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.add('dragover');
                });
                
                pasteZone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('dragover');
                });
                
                pasteZone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    this.classList.remove('dragover');
                    
                    const files = Array.from(e.dataTransfer.files).filter(file => 
                        file.type.startsWith('image/') || file.name.toLowerCase().endsWith('.avif')
                    );
                    
                    if (files.length === 0) {
                        try {
                            if (typeof showToast === 'function') {
                                showToast('error', 'Nenhuma imagem válida encontrada', 'Erro');
                            } else {
                                console.error('Nenhuma imagem válida encontrada');
                            }
                        } catch(e) {
                            console.error('Nenhuma imagem válida encontrada');
                        }
                        return;
                    }
                    
                    // Validar e adicionar arquivos
                    const validFiles = files.filter(validateImageFile);
                    if (validFiles.length === 0) return;
                    
                    const dt = new DataTransfer();
                    Array.from(fileInput.files).forEach(f => dt.items.add(f));
                    validFiles.forEach(f => dt.items.add(f));
                    fileInput.files = dt.files;
                    
                    // Disparar preview
                    fileInput.dispatchEvent(new Event('change'));
                    
                    // Feedback visual melhorado
                    this.classList.add('paste-success');
                    setTimeout(() => {
                        this.classList.remove('paste-success');
                    }, 1500);
                    
                    try {
                        if (typeof showToast === 'function') {
                            showToast('success', `${validFiles.length} imagem(ns) adicionada(s)`, 'Sucesso');
                        } else {
                            console.log(`${validFiles.length} imagem(ns) adicionada(s)`);
                        }
                    } catch(e) {
                        console.log(`${validFiles.length} imagem(ns) adicionada(s)`);
                    }
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

            // Album picker (create + variation modal)
            // - Create page uses hidden inputs existing_image_ids[]
            // - Variation modal uploads selected album image via AJAX
            (function bindAlbumPicker(){
                try {
                    const btn = document.getElementById('select-from-album-btn');
                    const modalEl = document.getElementById('albumImagesModal');
                    const container = document.getElementById('album-images-container');
                    if (!btn || !modalEl || !container) return;

                    // If already has an onclick handler registered by blade script, don't double-bind
                    if (btn.dataset.albumBound === '1') return;
                    btn.dataset.albumBound = '1';

                    const csrfToken = (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')) || CSRF_TOKEN || '';

                    function openModal() {
                        if (!window.bootstrap || !bootstrap.Modal) {
                            alert('Bootstrap não carregou nesta página. Recarregue e tente novamente.');
                            return;
                        }
                        const m = bootstrap.Modal.getOrCreateInstance(modalEl);
                        m.show();
                    }

                    function renderAlbums(albums) {
                        container.innerHTML = '';
                        if (!albums || albums.length === 0) {
                            container.innerHTML = '<div class="col-12 text-center py-4 text-muted">Nenhum álbum encontrado</div>';
                            return;
                        }

                        albums.forEach(album => {
                            if (!album.images || album.images.length === 0) return;

                            const albumCol = document.createElement('div');
                            albumCol.className = 'col-12';
                            albumCol.innerHTML = `
                                <h6 class="mb-3">
                                    <i class="bi bi-folder me-2"></i>${album.title}
                                    <span class="badge bg-secondary ms-2">${album.images.length} imagens</span>
                                </h6>
                                <div class="row g-2" id="album-${album.id}-images">
                                    ${album.images.map(image => `
                                        <div class="col-md-2 col-sm-3 col-4">
                                            <div class="position-relative album-image-item" style="cursor: pointer;"
                                                 data-image-id="${image.id}"
                                                 data-image-url="${image.url}"
                                                 data-image-path="${image.path}">
                                                <img src="${image.url}" class="img-thumbnail w-100" style="height: 80px; object-fit: cover;">
                                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 text-white" style="opacity: 0; transition: opacity 0.2s;">
                                                    <i class="bi bi-check-circle fs-4"></i>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            `;
                            container.appendChild(albumCol);
                        });

                        // click binding (event delegation)
                        container.addEventListener('click', function(e){
                            const item = e.target.closest('.album-image-item');
                            if (!item) return;
                            const imageId = item.getAttribute('data-image-id');
                            const imageUrl = item.getAttribute('data-image-url');
                            const imagePath = item.getAttribute('data-image-path');

                            // If variation images modal is open, selection should upload to variation
                            const variationModal = document.getElementById('variationImagesModal');
                            const variationIsOpen = variationModal && variationModal.classList.contains('show');
                            const variationId = document.getElementById('variation_images_id')?.value;

                            if (variationIsOpen && variationId) {
                                // Upload selected album image to variation via AJAX
                                if (!csrfToken) {
                                    alert('Token CSRF não encontrado. Recarregue a página e tente novamente.');
                                    return;
                                }

                                fetch(`/admin/products/variations/${variationId}/images`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json'
                                    },
                                    credentials: 'same-origin',
                                    body: JSON.stringify({ album_image_id: imageId })
                                })
                                .then(r => r.json().then(body => ({ ok: r.ok, status: r.status, body })))
                                .then(({ ok, body }) => {
                                    if (!ok || !body || !body.success) {
                                        const msg = body?.message || 'Erro ao adicionar imagem à variação';
                                        throw new Error(msg);
                                    }

                                    if (typeof window.showToast === 'function') {
                                        window.showToast('success', 'Imagem adicionada à variação!', 'Sucesso');
                                    }

                                    // Refresh variation images grid
                                    return fetch(`/admin/products/variations/${variationId}/images`, {
                                        headers: { 'Accept': 'application/json' },
                                        credentials: 'same-origin'
                                    }).then(r => r.json());
                                })
                                .then(data => {
                                    if (!data || !data.success) return;
                                    const grid = document.getElementById('variation-images-grid');
                                    const count = document.getElementById('variation-images-count');
                                    const empty = document.getElementById('no-variation-images');

                                    if (grid) {
                                        grid.innerHTML = '';
                                        (data.images || []).forEach((img, idx) => {
                                            const col = document.createElement('div');
                                            col.className = 'col-md-3 mb-2';
                                            col.innerHTML = `
                                                <div class="variation-image-item">
                                                    <img src="${img.url}" alt="" loading="lazy">
                                                </div>
                                            `;
                                            grid.appendChild(col);
                                        });
                                    }
                                    if (count) count.textContent = (data.images || []).length;
                                    if (empty) empty.style.display = (data.images || []).length ? 'none' : '';
                                })
                                .catch(err => {
                                    console.error(err);
                                    alert(err.message || 'Erro ao adicionar imagem');
                                });
                            } else {
                                // Create product flow: add hidden input existing_image_ids[]
                                const preselectedContainer = document.getElementById('preselected-images');
                                let preselectedList = document.getElementById('preselected-images-list');
                                if (!preselectedList) {
                                    preselectedList = document.createElement('div');
                                    preselectedList.id = 'preselected-images-list';
                                    preselectedList.className = 'd-flex flex-wrap gap-2';
                                    if (preselectedContainer) {
                                        preselectedContainer.classList.remove('d-none');
                                        preselectedContainer.appendChild(preselectedList);
                                    }
                                }

                                if (document.querySelector(`[data-album-image-id="${imageId}"]`)) {
                                    alert('Esta imagem já foi selecionada!');
                                    return;
                                }

                                const wrapper = document.createElement('div');
                                wrapper.className = 'position-relative border rounded';
                                wrapper.setAttribute('data-album-image-id', imageId);
                                wrapper.style.cssText = 'width:100px; height:100px; overflow:hidden';
                                wrapper.innerHTML = `
                                    <img src="${imageUrl}" class="w-100 h-100" style="object-fit:cover;" loading="lazy">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute remove-preselected" style="top:6px; right:6px; padding:4px 6px">Remover</button>
                                    <input type="hidden" name="existing_image_ids[]" value="${imageId}">
                                `;
                                preselectedList.appendChild(wrapper);

                                const removeBtn = wrapper.querySelector('.remove-preselected');
                                if (removeBtn) {
                                    removeBtn.addEventListener('click', function(){
                                        wrapper.remove();
                                        if (preselectedList.children.length === 0 && preselectedContainer) {
                                            preselectedContainer.classList.add('d-none');
                                        }
                                    });
                                }
                                if (typeof window.showToast === 'function') {
                                    window.showToast('success', 'Imagem selecionada com sucesso!', 'Sucesso');
                                }
                            }

                            // close modal
                            if (window.bootstrap && bootstrap.Modal) {
                                const inst = bootstrap.Modal.getInstance(modalEl);
                                if (inst) inst.hide();
                            }
                        }, { once: true });
                    }

                    function loadAlbums() {
                        container.innerHTML = '<div class="col-12 text-center py-4"><i class="bi bi-hourglass-split"></i> Carregando álbuns...</div>';
                        fetch('/admin/products/album-images', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                            .then(r => r.json())
                            .then(data => {
                                if (data && data.success) renderAlbums(data.albums);
                                else container.innerHTML = `<div class="col-12 text-center py-4 text-danger">Erro ao carregar álbuns${data && data.message ? ': ' + data.message : ''}</div>`;
                            })
                            .catch(err => {
                                console.error('Erro carregando álbuns:', err);
                                container.innerHTML = '<div class="col-12 text-center py-4 text-danger">Erro ao carregar álbuns. Verifique o console.</div>';
                            });
                    }

                    btn.addEventListener('click', function(){
                        loadAlbums();
                        openModal();
                    });
                } catch(e) {
                    console.error('Album picker fallback failed:', e);
                }
            })();

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
    }

    // Ctrl+V GLOBAL - Prático e Rápido (Melhorado)
    document.addEventListener('paste', function(e) {
        console.log('🔍 DEBUG: Paste event triggered');
        console.log('🔍 DEBUG: Current pathname:', window.location.pathname);
        console.log('🔍 DEBUG: Active element:', document.activeElement.tagName);
        
        // Verificar se está na página de produto (edit ou create)
        if (window.location.pathname.includes('/admin/products/') && 
            (window.location.pathname.includes('/edit') || window.location.pathname.includes('/create'))) {
            
            console.log('🔍 DEBUG: Correct page detected');
            
            const items = Array.from(e.clipboardData.items);
            console.log('🔍 DEBUG: Clipboard items:', items.length);
            
            const imageItems = items.filter(item => 
                item.type.indexOf('image') !== -1 || item.type === 'text/uri-list'
            );
            
            console.log('🔍 DEBUG: Image items found:', imageItems.length);
            
            if (imageItems.length === 0) {
                console.log('🔍 DEBUG: No image items, returning');
                return;
            }
            
            // Só previne o comportamento padrão se encontrar imagens
            e.preventDefault();
            console.log('🔍 DEBUG: Prevented default behavior');
            
            const input = document.getElementById('images');
            console.log('🔍 DEBUG: Images input found:', !!input);
            
            if (!input) return;
            
            const dt = new DataTransfer();
            Array.from(input.files).forEach(f => dt.items.add(f));
            
            let addedCount = 0;
            
            imageItems.forEach(item => {
                if (item.type.indexOf('image') !== -1) {
                    const file = item.getAsFile();
                    console.log('🔍 DEBUG: Processing file:', file ? file.name : 'null');
                    const validator = window.validateImageFile || validateImageFile;
                    if (file && validator(file)) {
                        dt.items.add(file);
                        addedCount++;
                        console.log('🔍 DEBUG: File added successfully');
                    }
                }
            });
            
            console.log('🔍 DEBUG: Total files added:', addedCount);
            
            if (addedCount > 0) {
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
                console.log('🔍 DEBUG: Files set and change event dispatched');
                
                // Feedback visual aprimorado com classes CSS
                const zone = document.getElementById('editProductPasteZone') || document.getElementById('createProductPasteZone');
                console.log('🔍 DEBUG: Zone found:', !!zone);
                
                if (zone) {
                    zone.classList.add('paste-success');
                    setTimeout(() => {
                        zone.classList.remove('paste-success');
                    }, 1500);
                }
                
                // Toast com fallback seguro
                try {
                    if (typeof showToast === 'function') {
                        showToast('success', `${addedCount} imagem(ns) colada(s) com Ctrl+V`, 'Sucesso');
                    } else {
                        console.log(`${addedCount} imagem(ns) colada(s) com Ctrl+V`);
                    }
                } catch(e) {
                    console.log(`${addedCount} imagem(ns) colada(s) com Ctrl+V`);
                }
            }
        } else {
            console.log('🔍 DEBUG: Not on product page, ignoring');
        }
    });
    
    console.log('🔍 DEBUG: Paste event listener registered');

})();
