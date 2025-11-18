@auth('admin')
    <!-- Smart Search Flutuante (visível apenas para Admin logado) -->
    <style>
        .smart-search-fab { position: fixed; bottom: 30px; right: 30px; z-index: 999; }
        .smart-search-trigger {
            width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer;
            background: linear-gradient(135deg, #ff8c00 0%, #e67e00 100%); color: #fff;
            box-shadow: 0 8px 16px rgba(255, 140, 0, 0.3); display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .smart-search-trigger:hover { transform: scale(1.1); box-shadow: 0 12px 24px rgba(255,140,0,.4); }
        .smart-search-trigger:active { transform: scale(0.95); }

        .smart-search-panel {
            position: fixed; bottom: 100px; right: 30px; width: 420px; max-height: 600px; overflow: hidden;
            background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.3);
            display: none; flex-direction: column; z-index: 998; animation: ss-slideUp .3s cubic-bezier(.4,0,.2,1);
        }
        .smart-search-panel.active { display: flex; }
        @keyframes ss-slideUp { from { opacity:0; transform: translateY(20px);} to { opacity:1; transform: translateY(0);} }
        .smart-search-header { padding: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; }
        .smart-search-header h3 { margin: 0; font-size: 1.1rem; font-weight: 600; color: #fff; }
        .smart-search-close { width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(255,255,255,.2); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .smart-search-input-wrapper { padding: 16px; border-bottom: 1px solid #e2e8f0; background: #fff; }
        .smart-search-input-group { display: flex; align-items: center; gap: 12px; background: #f8fafc; border-radius: 12px; padding: 0 16px; border: 2px solid transparent; }
        .smart-search-input-group:focus-within { border-color: #ff8c00; background: #fff; box-shadow: 0 0 0 4px rgba(255,140,0,.1); }
        .smart-search-input { flex: 1; border: none; outline: none; padding: 14px 0; font-size: 15px; font-weight: 500; background: transparent; }
        .smart-search-clear { background: none; border: none; color: #64748b; cursor: pointer; padding: 4px 8px; border-radius: 4px; }
        .smart-search-results { flex: 1; overflow-y: auto; padding: 8px; }
    .smart-search-item { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px; text-decoration: none; color: inherit; border: 2px solid transparent; transition: all .2s ease; margin-bottom: 8px; }
        .smart-search-item:hover { background: #f8fafc; border-color: #ff8c00; transform: translateX(4px); text-decoration: none; }
    .smart-search-item-image { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; background: #f8fafc; flex-shrink: 0; cursor: pointer; }
    .smart-search-item-name { font-weight: 600; font-size: 14px; color: #1e293b; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .smart-search-item-name .js-rename-product { cursor: pointer; color: #1e293b; text-decoration: underline; text-underline-offset: 2px; }
        .smart-search-item-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .smart-search-item-brand { font-size: 12px; padding: 2px 8px; background: #1e293b; color: #fff; border-radius: 4px; font-weight: 500; }
        .smart-search-item-price { font-size: 13px; font-weight: 600; color: #10b981; }
        .smart-search-empty, .smart-search-loading { text-align: center; padding: 40px 20px; color: #64748b; }
    /* Quick overlays inside panel */
    .ss-overlay { position: absolute; inset: 0; background: rgba(0,0,0,.25); display: none; align-items: center; justify-content: center; z-index: 1000; }
    .ss-overlay.active { display: flex; }
    .ss-modal { width: 92%; max-width: 420px; background: #fff; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,.3); overflow: hidden; }
    .ss-modal .ss-header { padding: 12px 16px; font-weight: 600; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: space-between; }
    .ss-modal .ss-body { padding: 16px; }
    .ss-modal .ss-footer { padding: 12px 16px; display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid #e2e8f0; }
    .ss-btn { border: none; border-radius: 8px; padding: 8px 12px; font-weight: 600; cursor: pointer; }
    .ss-btn-primary { background: linear-gradient(135deg, #ff8c00 0%, #e67e00 100%); color: #fff; }
    .ss-btn-secondary { background: #e5e7eb; color: #111827; }
    .ss-btn-danger { background: #ef4444; color: #fff; }
        @media (max-width: 768px) {
            .smart-search-panel { right: 15px; left: 15px; width: auto; bottom: 90px; max-height: 70vh; }
            .smart-search-fab { bottom: 100px; right: 15px; }
            .smart-search-trigger { width: 56px; height: 56px; font-size: 1.3rem; }
        }
    </style>

    <div class="smart-search-fab">
        <button class="smart-search-trigger" id="smartSearchTrigger" title="Buscar produto">
            <i class="bi bi-search"></i>
        </button>

        <div class="smart-search-panel" id="smartSearchPanel">
            <div class="smart-search-header">
                <h3><i class="bi bi-lightning-charge-fill me-2"></i>Busca Inteligente</h3>
                <button class="smart-search-close" id="smartSearchClose" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="smart-search-input-wrapper">
                <div class="smart-search-input-group">
                    <i class="bi bi-search"></i>
                    <input type="text" class="smart-search-input" id="smartSearchInput" placeholder="Digite para buscar produtos..." autocomplete="off">
                    <button class="smart-search-clear" id="smartSearchClear" style="display:none;">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
            </div>

            <div class="smart-search-results" id="smartSearchResults">
                <div class="smart-search-empty">
                    <i class="bi bi-search"></i>
                    <p>Digite algo para buscar produtos</p>
                </div>
            </div>

            <!-- Overlays: Renomear e Trocar Imagem -->
            <div class="ss-overlay" id="ssRenameOverlay" aria-modal="true" role="dialog">
                <div class="ss-modal">
                    <div class="ss-header">
                        <span>Renomear produto</span>
                        <button class="ss-btn ss-btn-secondary" id="ssRenameClose">Fechar</button>
                    </div>
                    <div class="ss-body">
                        <input type="text" id="ssRenameInput" class="form-control" placeholder="Novo nome do produto" />
                        <input type="hidden" id="ssRenameProductId" />
                    </div>
                    <div class="ss-footer">
                        <button class="ss-btn ss-btn-secondary" id="ssRenameCancel">Cancelar</button>
                        <button class="ss-btn ss-btn-primary" id="ssRenameSave">Salvar</button>
                    </div>
                </div>
            </div>
            <div class="ss-overlay" id="ssImageOverlay" aria-modal="true" role="dialog">
                <div class="ss-modal">
                    <div class="ss-header">
                        <span>Trocar imagem destaque</span>
                        <button class="ss-btn ss-btn-secondary" id="ssImageClose">Fechar</button>
                    </div>
                    <div class="ss-body">
                        <input type="file" id="ssImageFile" accept="image/*" class="form-control" />
                        <input type="hidden" id="ssImageProductId" />
                        <small class="text-muted d-block mt-2">Formatos: jpeg, png, jpg, gif, webp, avif. Máx 10MB.</small>
                    </div>
                    <div class="ss-footer">
                        <button class="ss-btn ss-btn-danger" id="ssImageRemove">Remover imagem</button>
                        <button class="ss-btn ss-btn-secondary" id="ssImageCancel">Cancelar</button>
                        <button class="ss-btn ss-btn-primary" id="ssImageSave">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchTrigger = document.getElementById('smartSearchTrigger');
            const searchPanel = document.getElementById('smartSearchPanel');
            const searchClose = document.getElementById('smartSearchClose');
            const searchInput = document.getElementById('smartSearchInput');
            const searchClear = document.getElementById('smartSearchClear');
            const searchResults = document.getElementById('smartSearchResults');

            if (!searchTrigger || !searchPanel) return;

            let searchTimeout = null;

            searchTrigger.addEventListener('click', function() {
                searchPanel.classList.toggle('active');
                if (searchPanel.classList.contains('active')) {
                    setTimeout(() => searchInput && searchInput.focus(), 0);
                }
            });
            searchClose.addEventListener('click', function() { searchPanel.classList.remove('active'); });
            document.addEventListener('click', function(e) {
                if (!searchPanel.contains(e.target) && !searchTrigger.contains(e.target)) {
                    searchPanel.classList.remove('active');
                }
            });
            searchClear.addEventListener('click', function() {
                if (!searchInput) return;
                searchInput.value = '';
                searchClear.style.display = 'none';
                searchResults.innerHTML = `
                    <div class="smart-search-empty">
                        <i class="bi bi-search"></i>
                        <p>Digite algo para buscar produtos</p>
                    </div>
                `;
            });

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    searchClear.style.display = query ? 'block' : 'none';
                    clearTimeout(searchTimeout);
                    if (query.length === 0) {
                        searchResults.innerHTML = `
                            <div class="smart-search-empty">
                                <i class="bi bi-search"></i>
                                <p>Digite algo para buscar produtos</p>
                            </div>
                        `;
                        return;
                    }
                    searchResults.innerHTML = `
                        <div class="smart-search-loading">
                            <i class="bi bi-arrow-repeat"></i>
                            <p>Buscando...</p>
                        </div>
                    `;
                    searchTimeout = setTimeout(function() { performSearch(query); }, 300);
                });
            }

            function performSearch(query) {
                fetch(`{{ route('admin.products.index') }}?search=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.products && data.products.length) {
                        renderResults(data.products);
                    } else {
                        searchResults.innerHTML = `
                            <div class="smart-search-empty">
                                <i class="bi bi-inbox"></i>
                                <p>Nenhum produto encontrado</p>
                                <small style="color:#64748b; margin-top:8px;">Tente outra palavra-chave</small>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    console.error('Erro na busca:', err);
                    searchResults.innerHTML = `
                        <div class="smart-search-empty">
                            <i class="bi bi-exclamation-triangle" style="color:#ef4444;"></i>
                            <p>Erro ao buscar produtos</p>
                        </div>
                    `;
                });
            }

            function renderResults(products) {
                let html = '';
                products.forEach(product => {
                    const image = product.first_image || '{{ asset('images/no-image.svg') }}';
                    const brand = product.brand || 'Sem marca';
                    const price = product.price ? `R$ ${parseFloat(product.price).toFixed(2).replace('.', ',')}` : 'Preço não definido';
                    const listUrl = `{{ route('admin.products.index') }}?highlight=${product.id}#product-${product.id}`;

                    const isInactive = !product.is_active;
                    const isUnavailable = product.is_unavailable;
                    const opacityStyle = (isInactive || isUnavailable) ? 'opacity: 0.6;' : '';

                    let statusBadges = '';
                    if (isInactive) statusBadges += '<span class="badge bg-secondary" style="font-size:10px; margin-left:4px;">INATIVO</span>';
                    if (isUnavailable) statusBadges += '<span class="badge bg-warning text-dark" style="font-size:10px; margin-left:4px;">INDISPONÍVEL</span>';

                    html += `
                        <a href="${listUrl}" id="smart-result-${product.id}" class="smart-search-item" style="${opacityStyle}">
                            <img src="${image}" alt="${product.name}" class="smart-search-item-image js-change-image" data-product-id="${product.id}" onerror="this.src='{{ asset('images/no-image.svg') }}'">
                            <div class="smart-search-item-details">
                                <div class="smart-search-item-name" title="${product.name}"><span class="js-rename-product" data-product-id="${product.id}" data-current-name="${product.name}">${product.name}</span> ${statusBadges}</div>
                                <div class="smart-search-item-meta">
                                    <span class="smart-search-item-brand">${brand}</span>
                                    <span class="smart-search-item-price">${price}</span>
                                </div>
                            </div>
                            <i class="bi bi-arrow-right-circle" style="color:#ff8c00; font-size:1.5rem;"></i>
                        </a>
                    `;
                });
                searchResults.innerHTML = html;
            }

            // Utilitários
            const CSRF = '{{ csrf_token() }}';
            const overlayRename = document.getElementById('ssRenameOverlay');
            const overlayImage = document.getElementById('ssImageOverlay');
            const renameInput = document.getElementById('ssRenameInput');
            const renameProductId = document.getElementById('ssRenameProductId');
            const imageFile = document.getElementById('ssImageFile');
            const imageProductId = document.getElementById('ssImageProductId');

            function openRename(id, currentName){
                renameProductId.value = id;
                renameInput.value = currentName || '';
                overlayRename.classList.add('active');
                setTimeout(()=> renameInput.focus(), 50);
            }
            function closeRename(){ overlayRename.classList.remove('active'); }
            function openImage(id){
                imageProductId.value = id;
                imageFile.value = '';
                overlayImage.classList.add('active');
            }
            function closeImage(){ overlayImage.classList.remove('active'); }

            // Delegação: clique no título para renomear
            document.addEventListener('click', function(e){
                const btn = e.target.closest('.js-rename-product');
                if (!btn) return;
                e.preventDefault();
                e.stopPropagation();
                const id = btn.getAttribute('data-product-id');
                const current = btn.getAttribute('data-current-name') || btn.textContent.trim();
                openRename(id, current);
            });

            // Delegação: clique na imagem para trocar
            document.addEventListener('click', function(e){
                const img = e.target.closest('.js-change-image');
                if (!img) return;
                e.preventDefault();
                e.stopPropagation();
                const id = img.getAttribute('data-product-id');
                openImage(id);
            });

            // Ações Rename
            document.getElementById('ssRenameClose').addEventListener('click', closeRename);
            document.getElementById('ssRenameCancel').addEventListener('click', closeRename);
            document.getElementById('ssRenameSave').addEventListener('click', function(){
                const id = renameProductId.value;
                const name = renameInput.value.trim();
                if (!id || !name) { alert('Informe um nome válido.'); return; }
                fetch(`/admin/products/${id}/update-name`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ name })
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Erro ao renomear');
                    // Atualiza item na lista
                    const row = document.getElementById(`smart-result-${id}`);
                    if (row) {
                        const nameEl = row.querySelector('.js-rename-product');
                        if (nameEl) {
                            nameEl.textContent = data.product.name;
                            nameEl.setAttribute('data-current-name', data.product.name);
                        }
                    }
                    closeRename();
                })
                .catch(err => { alert(err.message); });
            });

            // Ações Imagem
            document.getElementById('ssImageClose').addEventListener('click', closeImage);
            document.getElementById('ssImageCancel').addEventListener('click', closeImage);
            document.getElementById('ssImageSave').addEventListener('click', function(){
                const id = imageProductId.value;
                const file = imageFile.files && imageFile.files[0];
                if (!id) return;
                if (!file) { alert('Escolha um arquivo de imagem.'); return; }
                const fd = new FormData();
                fd.append('featured_image', file);
                fetch(`/admin/products/${id}/update-images`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: fd
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Erro ao atualizar imagem');
                    const row = document.getElementById(`smart-result-${id}`);
                    const imgEl = row ? row.querySelector('img.smart-search-item-image') : null;
                    if (imgEl && Array.isArray(data.images) && data.images.length) {
                        // O primeiro da lista é destaque (pode vir URL absoluta)
                        imgEl.src = data.images[0];
                    }
                    closeImage();
                })
                .catch(err => { alert(err.message); });
            });
            document.getElementById('ssImageRemove').addEventListener('click', function(){
                const id = imageProductId.value;
                if (!id) return;
                const fd = new FormData();
                fd.append('remove_featured_image', '1');
                fd.append('all_images_removed', '0');
                fetch(`/admin/products/${id}/update-images`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: fd
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Erro ao remover imagem');
                    const row = document.getElementById(`smart-result-${id}`);
                    const imgEl = row ? row.querySelector('img.smart-search-item-image') : null;
                    if (imgEl) {
                        imgEl.src = `{{ asset('images/no-image.svg') }}`;
                    }
                    closeImage();
                })
                .catch(err => { alert(err.message); });
            });
        });
    </script>
@endauth
