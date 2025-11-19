@auth('admin')
    <!-- Smart Search Flutuante (visível apenas para Admin logado) -->
    <style>
    .smart-search-fab { position: fixed; bottom: 30px; right: 30px; z-index: 1101; display: flex; flex-direction: column; gap: 12px; align-items: flex-end; }
        .smart-search-trigger {
            width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer;
            background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%); color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .smart-search-trigger:hover { transform: scale(1.1); box-shadow: 0 12px 24px rgba(0,0,0,.28); }
        .smart-search-trigger:active { transform: scale(0.95); }

        .departments-trigger, .sections-trigger, .theme-trigger {
            width: 52px; height: 52px; border-radius: 50%; border: none; cursor: pointer;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-bg) 100%); color: #fff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25); display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .departments-trigger:hover, .sections-trigger:hover, .theme-trigger:hover { transform: scale(1.08); box-shadow: 0 12px 24px rgba(0,0,0,.35); }
        .departments-trigger:active, .sections-trigger:active, .theme-trigger:active { transform: scale(0.95); }

        .smart-search-panel {
            position: fixed; bottom: 90px; right: 30px; width: 420px; max-height: 600px; overflow: hidden;
            background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.3);
            display: none; flex-direction: column; z-index: 1100; animation: ss-slideUp .3s cubic-bezier(.4,0,.2,1);
        }
    .smart-search-panel.active { display: flex; }
        @keyframes ss-slideUp { from { opacity:0; transform: translateY(20px);} to { opacity:1; transform: translateY(0);} }
        .smart-search-header { padding: 20px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; }
        .smart-search-header h3 { margin: 0; font-size: 1.1rem; font-weight: 600; color: #fff; }
        .smart-search-close { width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(255,255,255,.2); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .smart-search-input-wrapper { padding: 16px; border-bottom: 1px solid #e2e8f0; background: #fff; }
        .smart-search-input-group { display: flex; align-items: center; gap: 12px; background: #f8fafc; border-radius: 12px; padding: 0 16px; border: 2px solid transparent; }
    .smart-search-input-group:focus-within { border-color: var(--secondary-color); background: #fff; box-shadow: 0 0 0 4px color-mix(in srgb, var(--secondary-color), transparent 85%); }
        .smart-search-input { flex: 1; border: none; outline: none; padding: 14px 0; font-size: 15px; font-weight: 500; background: transparent; }
        .smart-search-clear { background: none; border: none; color: #64748b; cursor: pointer; padding: 4px 8px; border-radius: 4px; }
        .smart-search-results { flex: 1; overflow-y: auto; padding: 8px; }
    .smart-search-item { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px; text-decoration: none; color: inherit; border: 2px solid transparent; transition: all .2s ease; margin-bottom: 8px; }
    .smart-search-item:hover { background: #f8fafc; border-color: var(--secondary-color); transform: translateX(4px); text-decoration: none; }
    .smart-search-item-image { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; background: #f8fafc; flex-shrink: 0; cursor: pointer; }
    .smart-search-item-name { font-weight: 600; font-size: 14px; color: #1e293b; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .smart-search-item-name .js-rename-product { cursor: pointer; color: #1e293b; text-decoration: underline; text-underline-offset: 2px; }
        .smart-search-item-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .smart-search-item-brand { font-size: 12px; padding: 2px 8px; background: #1e293b; color: #fff; border-radius: 4px; font-weight: 500; }
        .smart-search-item-price { font-size: 13px; font-weight: 600; color: #10b981; }
        .smart-search-empty, .smart-search-loading { text-align: center; padding: 40px 20px; color: #64748b; }
    /* Quick overlays inside panel */
    /* Overlays must cover the whole viewport. Use fixed so they're not clipped by the FAB container. */
    .ss-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.32); display: none; align-items: center; justify-content: center; z-index: 1500; }
    .ss-overlay.active { display: flex; }
    /* Modal: compact header/footer and scrollable body to avoid large top/bottom gaps */
    .ss-modal { width: 92%; max-width: 420px; background: #fff; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,.3); overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; }
    .ss-modal .ss-header { padding: 10px 12px; font-weight: 600; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: space-between; gap:8px; }
    .ss-modal .ss-body { padding: 12px; overflow: auto; flex: 1 1 auto; max-height: calc(85vh - 96px); }
    .ss-modal .ss-footer { padding: 8px 12px; display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid #e2e8f0; flex-shrink: 0; }
    /* Tab buttons inside modal header - compact */
    .ss-tab { padding: 6px 10px; font-size: 0.85rem; border-radius: 8px; background: #f3f4f6; color: #0f172a; border: none; cursor: pointer; }
    .ss-tab.active { background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%); color: #fff; }
    .ss-btn { border: none; border-radius: 8px; padding: 8px 12px; font-weight: 600; cursor: pointer; }
    .ss-btn-primary { background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%); color: #fff; }
    .ss-btn-secondary { background: #e5e7eb; color: #111827; }
    .ss-btn-danger { background: #ef4444; color: #fff; }
        /* Painel de Tema */
    .theme-panel { position: fixed; bottom: 90px; right: 30px; width: 360px; max-height: 520px; overflow: hidden; background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.3); display: none; flex-direction: column; z-index: 1100; animation: ss-slideUp .3s cubic-bezier(.4,0,.2,1); }
        .theme-panel.active { display: flex; }
        .theme-panel .tp-header { padding: 16px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-bg) 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; }
        .theme-panel .tp-body { padding: 14px 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .theme-panel .tp-footer { padding: 12px 16px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #e2e8f0; }
        .tp-label { font-size: 12px; color: #334155; margin-bottom: 6px; font-weight: 600; }
        .tp-field { display: flex; flex-direction: column; }
        .tp-color { width: 100%; height: 40px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0; }
        .tp-btn { border: none; border-radius: 8px; padding: 8px 12px; font-weight: 600; cursor: pointer; }
    .tp-btn-primary { background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%); color: #fff; }
        .tp-btn-secondary { background: #e5e7eb; color: #111827; }
        .tp-btn-danger { background: #ef4444; color: #fff; margin-right: auto; }

    /* Painel de Departamentos */
    .departments-panel { position: fixed; bottom: 90px; right: 30px; width: 420px; max-height: 560px; overflow: hidden; background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.3); display: none; flex-direction: column; z-index: 1100; animation: ss-slideUp .3s cubic-bezier(.4,0,.2,1); }
    .departments-panel.active { display: flex; }
    .departments-panel .dp-header { padding: 14px 16px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-bg) 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; }
    .departments-panel .dp-body { padding: 12px 14px; overflow-y: auto; }
    .departments-panel .dp-footer { padding: 12px 14px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #e2e8f0; }
    .dp-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 12px; }
    .dp-item { display: grid; grid-template-columns: 24px 1fr; gap: 10px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; background: #fff; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); }
    .dp-handle { cursor: grab; color: #64748b; }
    .dp-name-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .dp-name-row .dp-toggle { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; }
    .dp-meta-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 8px; }
    .dp-meta-row .dp-color { width: 100%; height: 36px; padding: 0; }
    .dp-description { resize: vertical; min-height: 55px; font-size: 13px; margin-bottom: 10px; }
    .dp-info-row { display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 12px; color: #475569; }
    .dp-actions { display: flex; align-items: center; gap: 6px; }
    .dp-quick-add { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px; }
    .dp-quick-add .dp-full { grid-column: 1 / -1; }
    .dp-quick-add input[type="color"] { width: 100%; height: 40px; padding: 0; }
    #dpLoading { text-align: center; padding: 20px 0; color: #64748b; }
    #dpEmpty { text-align: center; padding: 16px; color: #94a3b8; font-size: 13px; display: none; }

        /* Painel de Tema */
    .theme-panel { position: fixed; bottom: 90px; right: 30px; width: 420px; max-height: 560px; overflow: hidden; background: #fff; border-radius: 20px; box-shadow: 0 22px 60px rgba(15,23,42,0.32); display: none; flex-direction: column; z-index: 1100; animation: ss-slideUp .3s cubic-bezier(.4,0,.2,1); }
        .theme-panel.active { display: flex; }
        .theme-panel .tp-header { padding: 18px 20px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-bg) 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; }
        .theme-panel .tp-body { padding: 16px 20px 0; overflow-y: auto; display: flex; flex-direction: column; gap: 18px; }
        .theme-panel .tp-footer { padding: 16px 20px; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; background:#f8fafc; }
        .tp-intro { background: linear-gradient(135deg, rgba(15,23,42,0.04) 0%, rgba(15,23,42,0.08) 100%); border: 1px solid rgba(148,163,184,0.25); border-radius: 14px; padding: 14px 16px; font-size: 13px; color: #475569; display: flex; gap: 12px; align-items: flex-start; }
        .tp-intro i { color: var(--secondary-color); font-size: 1.2rem; }
        .tp-preview { border: 1px solid rgba(148,163,184,0.2); border-radius: 14px; padding: 16px; background: #f8fafc; display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 14px; font-size: 12px; color: #1e293b; }
        .tp-preview-header { background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-bg) 100%); border-radius: 12px; padding: 16px; color: var(--text-light); display: flex; align-items: center; justify-content: space-between; }
        .tp-preview-header span { font-weight: 600; }
        .tp-preview-search { display: flex; gap: 10px; align-items: center; }
        .tp-preview-search .bar { flex: 1; background: #fff; border-radius: 999px; height: 32px; border: 2px solid color-mix(in srgb, var(--secondary-color), transparent 50%); box-shadow: inset 0 1px 3px rgba(15,23,42,0.08); }
        .tp-preview-actions { display:flex; gap:10px; flex-wrap:wrap; }
        .tp-preview-actions .btn-outline { border: 2px solid var(--secondary-color); color: var(--secondary-color); padding: 6px 12px; border-radius: 999px; font-weight:600; background:#fff; }
        .tp-preview-actions .btn-solid { background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 14%) 100%); color:#fff; padding: 6px 14px; border-radius:999px; font-weight:600; box-shadow:0 8px 18px rgba(255,107,53,0.28); }
        .tp-preview-card { background:#fff; border-radius:12px; padding:12px; border: 1px solid rgba(148,163,184,0.15); box-shadow:0 4px 14px rgba(15,23,42,0.08); display:flex; flex-direction:column; gap:8px; }
        .tp-preview-card .tag { align-self:flex-start; padding:3px 10px; border-radius:999px; background:var(--accent-color); color:var(--text-light); font-weight:600; font-size:11px; }
        .tp-preview-card .title { font-weight:600; font-size:13px; color: var(--text-dark); }
        .tp-preview-card .price { font-weight:700; color: var(--success-color); }
        .tp-fields { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px; }
        .tp-field { background:#fff; border:1px solid rgba(148,163,184,0.2); border-radius:14px; padding:12px 14px; display:flex; flex-direction:column; gap:10px; box-shadow:0 6px 16px rgba(15,23,42,0.05); }
        .tp-label { font-size:12px; font-weight:700; color:#1e293b; text-transform:uppercase; letter-spacing:0.04em; }
        .tp-field small { color:#64748b; font-size:11px; line-height:1.4; }
        .tp-input-group { display:flex; gap:10px; align-items:center; }
        .tp-color { width:44px; height:44px; border-radius:12px; border:1px solid rgba(148,163,184,0.3); padding:0; flex-shrink:0; }
    .tp-hex { flex:1; border:1px solid rgba(148,163,184,0.4); border-radius:10px; padding:8px 10px; font-size:13px; font-weight:600; font-family:'Inter', sans-serif; letter-spacing:0.03em; transition:border-color .15s ease, box-shadow .15s ease; }
        .tp-hex:focus { outline:2px solid color-mix(in srgb, var(--secondary-color), transparent 70%); }
    .tp-hex.is-invalid { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,0.18); }
        .tp-presets { display:flex; flex-direction:column; gap:10px; }
        .tp-presets-title { font-size:12px; font-weight:700; color:#1e293b; text-transform:uppercase; letter-spacing:0.05em; }
        .tp-preset-group { display:flex; gap:10px; flex-wrap:wrap; }
        .tp-preset-btn { border:none; border-radius:12px; padding:10px 12px; background:#f1f5f9; display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; color:#1e293b; box-shadow:0 4px 12px rgba(15,23,42,0.08); transition:transform .15s ease, box-shadow .15s ease; }
        .tp-preset-btn:hover { transform:translateY(-2px); box-shadow:0 8px 18px rgba(15,23,42,0.12); }
        .tp-swatch { width:38px; height:38px; border-radius:10px; border:1px solid rgba(148,163,184,0.3); position:relative; overflow:hidden; }
        .tp-swatch::after { content:''; position:absolute; inset:0; background:var(--swatch-gradient, transparent); }
        .tp-btn { border: none; border-radius: 10px; padding: 10px 16px; font-weight: 700; cursor: pointer; letter-spacing:0.03em; text-transform:uppercase; }
        .tp-btn-primary { background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%); color: #fff; box-shadow:0 10px 22px rgba(255,107,53,0.28); }
        .tp-btn-secondary { background: #e2e8f0; color: #0f172a; }
        .tp-btn-danger { background: #ef4444; color: #fff; margin-right: auto; }
        .tp-btn:focus-visible { outline:3px solid color-mix(in srgb, var(--secondary-color), transparent 60%); outline-offset:2px; }
        .sp-btn { border: none; border-radius: 8px; padding: 6px 10px; font-weight: 600; cursor: pointer; }
        .sp-btn-primary { background: linear-gradient(135deg, var(--secondary-color) 0%, color-mix(in srgb, var(--secondary-color), white 12%) 100%); color: #fff; }
        .sp-btn-secondary { background: #e5e7eb; color: #111827; }
        .sp-btn-danger { background: #ef4444; color: #fff; }

    .sections-panel { position: fixed; bottom: 90px; right: 30px; width: 420px; max-height: 520px; overflow: hidden; background: #fff; border-radius: 18px; box-shadow: 0 22px 60px rgba(15,23,42,0.32); display: none; flex-direction: column; z-index: 1100; animation: ss-slideUp .3s cubic-bezier(.4,0,.2,1); }
        .sections-panel.active { display: flex; }
        .sections-panel .sp-header { padding: 16px 18px; background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-bg) 100%); color: #fff; display: flex; align-items: center; justify-content: space-between; }
        .sections-panel .sp-body { padding: 16px 18px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; }
        .sections-panel .sp-footer { padding: 14px 18px; display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
        .sp-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 12px; }
        .sp-item { display: grid; grid-template-columns: auto 1fr auto; gap: 12px; padding: 14px; border-radius: 14px; border: 1px solid rgba(148,163,184,0.22); background: #fff; box-shadow: 0 10px 28px rgba(15,23,42,0.08); }
        .sp-handle { cursor: grab; color: #64748b; font-size: 1.1rem; padding-top: 4px; }
        .sp-item .form-control, .sp-item .form-select { font-size: 0.85rem; }
        .sp-actions { display: inline-flex; gap: 6px; }
        .sp-actions .sp-btn { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
        .sp-item small { display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 0.8rem; color: #475569; }
        .sp-toggle { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #475569; }
        .sp-toggle input { margin: 0; }
        .sp-body .mt-2.d-flex { gap: 10px !important; }
        .sp-body .mt-2 select { flex: 1; }
        .sp-body .mt-2 input { flex: 1.2; }
        .sp-body .mt-2 button { flex-shrink: 0; }

        @media (max-width: 768px) {
            .smart-search-panel { right: 15px; left: 15px; width: auto; bottom: 80px; max-height: 70vh; }
            .smart-search-fab { bottom: 30px; right: 15px; }
            .smart-search-trigger { width: 56px; height: 56px; font-size: 1.3rem; }
            .theme-panel { right: 15px; left: 15px; width: auto; bottom: 80px; max-height: 65vh; }
            .departments-panel { right: 15px; left: 15px; width: auto; bottom: 80px; max-height: 65vh; }
            .sections-panel { right: 15px; left: 15px; width: auto; bottom: 80px; max-height: 65vh; }
            .sp-item { grid-template-columns: 1fr; }
            .sp-handle { order: -1; justify-self: flex-end; }
            .sp-toggle { justify-content: flex-end; }
            .sections-panel .sp-body { padding: 14px; }
            .sections-panel .sp-footer { flex-wrap: wrap; justify-content: center; gap: 10px; }
        }
    </style>

    <div class="smart-search-fab">
        <!-- Botão Gerenciar Departamentos (mais alto) -->
        <button class="departments-trigger" id="departmentsTrigger" title="Departamentos">
            <i class="bi bi-diagram-3-fill"></i>
        </button>
        <!-- Botão Gerenciar Seções (acima da paleta) -->
        <button class="sections-trigger" id="sectionsTrigger" title="Sessões de marcas">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </button>
        <!-- Botão Produtos (quick-create) -->
        <button class="sections-trigger" id="productsTrigger" title="Produtos (criar rápido)">
            <i class="bi bi-box-seam"></i>
        </button>
        <!-- Botão de Tema (pincel) -->
        <button class="theme-trigger" id="themeTrigger" title="Cores do site">
            <i class="bi bi-palette-fill"></i>
        </button>
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
                        <label class="form-label mb-1">Enviar arquivo</label>
                        <input type="file" id="ssImageFile" accept="image/*" class="form-control mb-3" />
                        <div class="text-center text-muted my-2">ou</div>
                        <label class="form-label mb-1">Usar link (URL)</label>
                        <input type="url" id="ssImageUrl" placeholder="https://exemplo.com/imagem.jpg" class="form-control" />
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

        <!-- Quick Create Product Modal (Tabbed) -->
        <div class="ss-overlay" id="ssQuickProductOverlay" aria-modal="true" role="dialog" style="display:none;">
            <div class="ss-modal" style="max-width:900px; width:96%;">
                <div class="ss-header" style="display:flex; align-items:center; gap:12px;">
                    <div style="flex:1; display:flex; gap:8px; align-items:center;">
                        <strong>Criar produto rápido</strong>
                        <nav style="display:flex; gap:6px; margin-left:8px;">
                            <button type="button" class="ss-btn ss-tab active" data-tab="general">Geral</button>
                            <button type="button" class="ss-btn ss-tab" data-tab="pricing">Preço</button>
                            <button type="button" class="ss-btn ss-tab" data-tab="inventory">Estoque</button>
                            <button type="button" class="ss-btn ss-tab" data-tab="images">Imagens</button>
                            <button type="button" class="ss-btn ss-tab" data-tab="shipping">Envio</button>
                            <button type="button" class="ss-btn ss-tab" data-tab="seo">SEO</button>
                            <button type="button" class="ss-btn ss-tab" data-tab="attrs">Atributos</button>
                        </nav>
                    </div>
                    <button class="ss-btn ss-btn-secondary" id="ssQuickProductClose">Fechar</button>
                </div>
                <div class="ss-body">
                    <form id="qpForm" onsubmit="return false;">
                        <div class="qp-tabs">
                            <div class="qp-tab-panel" data-panel="general">
                                <div class="mb-2">
                                    <label class="form-label">Nome do produto</label>
                                    <input type="text" id="qpName" class="form-control" placeholder="Nome do produto" />
                                </div>
                                <div class="mb-2 d-flex gap-2">
                                    <div style="flex:1">
                                        <label class="form-label">Marca</label>
                                        <select id="qpBrand" class="form-select"><option value="">Selecione a marca</option></select>
                                    </div>
                                    <div style="width:180px">
                                        <label class="form-label">SKU</label>
                                        <input type="text" id="qpSku" class="form-control" placeholder="SKU" />
                                    </div>
                                    <div style="width:120px">
                                        <label class="form-label">Ativo</label>
                                        <div><input type="checkbox" id="qpActive" checked /> Ativo</div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Categorias</label>
                                    <select id="qpCategories" class="form-select" multiple style="min-height:80px;"></select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Breve descrição</label>
                                    <textarea id="qpShortDesc" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Descrição completa (HTML opcional)</label>
                                    <textarea id="qpDescription" class="form-control" rows="6"></textarea>
                                </div>
                            </div>

                            <div class="qp-tab-panel" data-panel="pricing" style="display:none;">
                                <div class="d-flex gap-2 mb-2">
                                    <div style="flex:1">
                                        <label class="form-label">Preço (R$)</label>
                                        <input type="number" id="qpPrice" class="form-control" step="0.01" placeholder="0.00" />
                                    </div>
                                    <div style="width:160px">
                                        <label class="form-label">Preço Promocional</label>
                                        <input type="number" id="qpComparePrice" class="form-control" step="0.01" placeholder="0.00" />
                                    </div>
                                    <div style="width:160px">
                                        <label class="form-label">Custo</label>
                                        <input type="number" id="qpCostPrice" class="form-control" step="0.01" placeholder="0.00" />
                                    </div>
                                </div>
                            </div>

                            <div class="qp-tab-panel" data-panel="inventory" style="display:none;">
                                <div class="d-flex gap-2 mb-2">
                                    <div style="width:140px">
                                        <label class="form-label">Estoque</label>
                                        <input type="number" id="qpStock" class="form-control" value="0" />
                                    </div>
                                    <div style="width:140px">
                                        <label class="form-label">Min stock</label>
                                        <input type="number" id="qpMinStock" class="form-control" value="0" />
                                    </div>
                                    <div style="width:160px">
                                        <label class="form-label">Código de Barras</label>
                                        <input type="text" id="qpBarcode" class="form-control" placeholder="GTIN / EAN" />
                                    </div>
                                </div>
                            </div>

                            <div class="qp-tab-panel" data-panel="images" style="display:none;">
                                <div class="mb-2">
                                    <label class="form-label">Imagens (arraste ou selecione)</label>
                                    <input type="file" id="qpImages" class="form-control" accept="image/*" multiple />
                                    <small class="text-muted">Envie até 10 imagens. O primeiro arquivo será a imagem destaque.</small>
                                </div>
                                <div id="qpPreview" style="display:flex; gap:8px; flex-wrap:wrap; margin-top:8px;"></div>
                            </div>

                            <div class="qp-tab-panel" data-panel="shipping" style="display:none;">
                                <div class="d-flex gap-2 mb-2">
                                    <div style="width:160px">
                                        <label class="form-label">Peso (kg)</label>
                                        <input type="number" id="qpWeight" class="form-control" step="0.01" placeholder="0.00" />
                                    </div>
                                    <div style="width:160px">
                                        <label class="form-label">Comprimento (cm)</label>
                                        <input type="number" id="qpLength" class="form-control" step="0.1" placeholder="0.0" />
                                    </div>
                                    <div style="width:160px">
                                        <label class="form-label">Largura (cm)</label>
                                        <input type="number" id="qpWidth" class="form-control" step="0.1" placeholder="0.0" />
                                    </div>
                                    <div style="width:160px">
                                        <label class="form-label">Altura (cm)</label>
                                        <input type="number" id="qpHeight" class="form-control" step="0.1" placeholder="0.0" />
                                    </div>
                                </div>
                            </div>

                            <div class="qp-tab-panel" data-panel="seo" style="display:none;">
                                <div class="mb-2">
                                    <label class="form-label">Slug (opcional)</label>
                                    <input type="text" id="qpSlug" class="form-control" placeholder="slug-do-produto" />
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Título SEO</label>
                                    <input type="text" id="qpSeoTitle" class="form-control" />
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Descrição SEO</label>
                                    <textarea id="qpSeoDescription" class="form-control" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="qp-tab-panel" data-panel="attrs" style="display:none;">
                                <div id="qpAttributesList" style="display:flex; flex-direction:column; gap:8px;">
                                    <!-- attribute rows inserted here -->
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <input type="text" id="qpAttrKey" class="form-control" placeholder="Nome do atributo (ex: Cor)" />
                                    <input type="text" id="qpAttrValue" class="form-control" placeholder="Valor (ex: Vermelho)" />
                                    <button type="button" id="qpAddAttr" class="ss-btn ss-btn-primary">Adicionar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="ss-footer">
                    <button class="ss-btn ss-btn-secondary" id="ssQuickProductCancel">Cancelar</button>
                    <button class="ss-btn ss-btn-primary" id="ssQuickProductSave">Criar produto</button>
                </div>
            </div>
        </div>
        
        <!-- Painel de Departamentos -->
        <div class="departments-panel" id="departmentsPanel">
            <div class="dp-header">
                <span><i class="bi bi-diagram-3 me-2"></i> Departamentos</span>
                <button class="smart-search-close" id="departmentsClose" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="dp-body">
                <div id="dpLoading">Carregando departamentos...</div>
                <div id="dpEmpty">Nenhum departamento cadastrado ainda.</div>
                <ul class="dp-list" id="departmentsList"></ul>
                <div class="dp-quick-add">
                    <input type="text" id="dpNewName" class="form-control dp-full" placeholder="Nome do novo departamento">
                    <input type="text" id="dpNewSlug" class="form-control" placeholder="Slug (opcional)">
                    <input type="text" id="dpNewIcon" class="form-control" placeholder="Classe do ícone (ex: fas fa-store)">
                    <input type="color" id="dpNewColor" class="form-control form-control-color" value="#667eea" title="Cor destaque">
                    <textarea id="dpNewDescription" class="form-control dp-full" placeholder="Descrição (opcional)" rows="2"></textarea>
                    <button class="sp-btn sp-btn-secondary dp-full" id="dpAdd" type="button">Adicionar departamento</button>
                </div>
            </div>
            <div class="dp-footer">
                <button class="sp-btn sp-btn-secondary" id="departmentsCancel" type="button">Fechar</button>
                <button class="sp-btn sp-btn-primary" id="departmentsSave" type="button">Salvar alterações</button>
            </div>
            <div class="ss-overlay" id="dpConfirmOverlay" aria-modal="true" role="dialog">
                <div class="ss-modal" style="max-width:520px;">
                    <div class="ss-header">
                        <span>Desativar departamento</span>
                        <button class="ss-btn ss-btn-secondary" id="dpConfirmClose" type="button">Fechar</button>
                    </div>
                    <div class="ss-body">
                        <div id="dpConfirmText" class="mb-3" style="color:#334155; font-size:14px;"></div>
                        <div id="dpConfirmDanger" class="border rounded p-2" style="border-color:#fecaca; background:#fef2f2;">
                            <div style="font-size:13px; color:#7f1d1d;" class="mb-2">Para confirmar, digite exatamente: <strong id="dpConfirmPhrase"></strong></div>
                            <input type="text" id="dpConfirmInput" class="form-control" placeholder="Digite a frase de confirmação" />
                        </div>
                        <small class="text-muted d-block mt-2">Os produtos continuarão disponíveis e poderão ser encontrados via busca.</small>
                    </div>
                    <div class="ss-footer">
                        <button class="ss-btn ss-btn-secondary" id="dpConfirmCancel" type="button">Cancelar</button>
                        <button class="ss-btn ss-btn-primary" id="dpConfirmApply" type="button">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Painel de Tema -->
        <div class="theme-panel" id="themePanel">
            <div class="tp-header">
                <span><i class="bi bi-palette me-2"></i> Cores do Site</span>
                <button class="smart-search-close" id="themeClose" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="tp-body">
                <div class="tp-intro">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <strong>Personalize a identidade visual em poucos cliques.</strong>
                        <div>As alterações aparecem instantaneamente na pré-visualização abaixo. Quando estiver satisfeito, clique em <strong>Salvar</strong> para aplicar para todos os visitantes.</div>
                    </div>
                </div>
                <div class="tp-preview" aria-live="polite">
                    <div class="tp-preview-header">
                        <span>Topo do site</span>
                        <div class="tp-preview-search">
                            <div class="bar" title="Área da busca"></div>
                            <div class="tp-preview-actions">
                                <div class="btn-outline">Categorias</div>
                                <div class="btn-solid">Buscar</div>
                            </div>
                        </div>
                    </div>
                    <div class="tp-preview-card">
                        <span class="tag">Oferta</span>
                        <span class="title">Produto destaque com cor de texto escuro</span>
                        <span class="price">R$ 1.299,00</span>
                        <div style="display:flex; gap:8px;">
                            <div class="btn-solid" style="padding:8px 14px; border-radius:8px; font-size:11px;">Ver detalhes</div>
                            <div class="btn-outline" style="padding:8px 14px; border-radius:8px; font-size:11px;">Adicionar</div>
                        </div>
                    </div>
                </div>
                <div class="tp-fields">
                    <div class="tp-field" data-key="theme_primary">
                        <span class="tp-label">Primária</span>
                        <div class="tp-input-group">
                            <input type="color" id="tpPrimary" class="tp-color" value="{{ setting('theme_primary', '#0f172a') }}" data-key="theme_primary">
                            <input type="text" id="tpPrimaryHex" class="tp-hex" value="{{ setting('theme_primary', '#0f172a') }}" data-key="theme_primary" aria-label="Código hexadecimal da cor primária">
                        </div>
                        <small>Aplicada no cabeçalho, títulos, ícones principais e áreas de destaque.</small>
                    </div>
                    <div class="tp-field" data-key="theme_secondary">
                        <span class="tp-label">Secundária</span>
                        <div class="tp-input-group">
                            <input type="color" id="tpSecondary" class="tp-color" value="{{ setting('theme_secondary', '#ff6b35') }}" data-key="theme_secondary">
                            <input type="text" id="tpSecondaryHex" class="tp-hex" value="{{ setting('theme_secondary', '#ff6b35') }}" data-key="theme_secondary" aria-label="Código hexadecimal da cor secundária">
                        </div>
                        <small>Destaque para botões, chamada para ação e badges.</small>
                    </div>
                    <div class="tp-field" data-key="theme_accent">
                        <span class="tp-label">Acento</span>
                        <div class="tp-input-group">
                            <input type="color" id="tpAccent" class="tp-color" value="{{ setting('theme_accent', '#0f172a') }}" data-key="theme_accent">
                            <input type="text" id="tpAccentHex" class="tp-hex" value="{{ setting('theme_accent', '#0f172a') }}" data-key="theme_accent" aria-label="Código hexadecimal do acento">
                        </div>
                        <small>Usada em etiquetas, indicadores e elementos menores.</small>
                    </div>
                    <div class="tp-field" data-key="theme_dark_bg">
                        <span class="tp-label">Fundo Escuro</span>
                        <div class="tp-input-group">
                            <input type="color" id="tpDarkBg" class="tp-color" value="{{ setting('theme_dark_bg', '#1e293b') }}" data-key="theme_dark_bg">
                            <input type="text" id="tpDarkBgHex" class="tp-hex" value="{{ setting('theme_dark_bg', '#1e293b') }}" data-key="theme_dark_bg" aria-label="Código hexadecimal do fundo escuro">
                        </div>
                        <small>Bases de banners e rodapés escuros.</small>
                    </div>
                    <div class="tp-field" data-key="theme_text_light">
                        <span class="tp-label">Texto Claro</span>
                        <div class="tp-input-group">
                            <input type="color" id="tpTextLight" class="tp-color" value="{{ setting('theme_text_light', '#f8fafc') }}" data-key="theme_text_light">
                            <input type="text" id="tpTextLightHex" class="tp-hex" value="{{ setting('theme_text_light', '#f8fafc') }}" data-key="theme_text_light" aria-label="Código hexadecimal do texto claro">
                        </div>
                        <small>Textos que aparecem sobre fundos escuros.</small>
                    </div>
                    <div class="tp-field" data-key="theme_text_dark">
                        <span class="tp-label">Texto Escuro</span>
                        <div class="tp-input-group">
                            <input type="color" id="tpTextDark" class="tp-color" value="{{ setting('theme_text_dark', '#1e293b') }}" data-key="theme_text_dark">
                            <input type="text" id="tpTextDarkHex" class="tp-hex" value="{{ setting('theme_text_dark', '#1e293b') }}" data-key="theme_text_dark" aria-label="Código hexadecimal do texto escuro">
                        </div>
                        <small>Cor padrão das tipografias em áreas claras.</small>
                    </div>
                </div>
                <div class="tp-presets">
                    <span class="tp-presets-title">Sugestões rápidas</span>
                    <div class="tp-preset-group">
                        <button type="button" class="tp-preset-btn" data-primary="#0f172a" data-secondary="#ff6b35" data-accent="#0f172a" data-dark="#1e293b" data-light="#f8fafc" data-text="#1e293b" style="--swatch-gradient: linear-gradient(135deg, #0f172a 0%, #ff6b35 100%);">
                            <div class="tp-swatch"></div>
                            Clássico Feira
                        </button>
                        <button type="button" class="tp-preset-btn" data-primary="#0b3d2c" data-secondary="#1abc9c" data-accent="#0b3d2c" data-dark="#052e1f" data-light="#f6fffb" data-text="#0b1f16" style="--swatch-gradient: linear-gradient(135deg, #0b3d2c 0%, #1abc9c 100%);">
                            <div class="tp-swatch"></div>
                            Verde Premium
                        </button>
                        <button type="button" class="tp-preset-btn" data-primary="#212948" data-secondary="#6366f1" data-accent="#4338ca" data-dark="#111827" data-light="#f5f3ff" data-text="#1e1b4b" style="--swatch-gradient: linear-gradient(135deg, #212948 0%, #6366f1 100%);">
                            <div class="tp-swatch"></div>
                            Noturno Tech
                        </button>
                        <button type="button" class="tp-preset-btn" data-primary="#4a1d1f" data-secondary="#f97316" data-accent="#b45309" data-dark="#2d1415" data-light="#fff7ed" data-text="#431407" style="--swatch-gradient: linear-gradient(135deg, #4a1d1f 0%, #f97316 100%);">
                            <div class="tp-swatch"></div>
                            Terracota
                        </button>
                    </div>
                </div>
            </div>
            <div class="tp-footer">
                <button class="tp-btn tp-btn-danger" id="themeReset">Restaurar</button>
                <button class="tp-btn tp-btn-secondary" id="themeCancel">Cancelar</button>
                <button class="tp-btn tp-btn-primary" id="themeSave">Salvar</button>
            </div>
        </div>

        <!-- Painel de Seções (Marcas) -->
        <div class="sections-panel" id="sectionsPanel">
            <div class="sp-header">
                <span><i class="bi bi-collection me-2"></i> Sessões de Marcas</span>
                <button class="smart-search-close" id="sectionsClose" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="sp-body">
                <div id="sectionsUnsupported" style="display:none; color:#64748b; font-size: 14px;">
                    Abra este painel na página do departamento Eletrônicos para gerenciar as sessões.
                </div>
                <ul class="sp-list" id="sectionsList"></ul>
                <div class="mt-2 d-flex gap-2 align-items-stretch">
                    <div style="display:flex; gap:6px; align-items:center;">
                        <select id="spNewBrandSelect" class="form-select" style="min-width: 160px;">
                            <option value="">Selecione a marca…</option>
                        </select>
                        <button type="button" id="spCreateBrandBtn" class="sp-btn sp-btn-primary" title="Criar marca">Criar marca</button>
                    </div>
                    <input type="text" id="spNewTitle" class="form-control" placeholder="Título da seção (opcional)">
                    <button class="sp-btn sp-btn-secondary" id="spAdd">Adicionar</button>
                </div>
                <small id="spBrandsWarning" class="d-block mt-2" style="color:#d97706; display:none;">Nenhuma marca encontrada para este departamento.</small>
                <small class="d-block mt-2" style="color:#64748b;">Dica: Use as setas para ordenar; desative para ocultar a seção. Salve para persistir.</small>
            </div>
            <div class="sp-footer">
                <button class="sp-btn sp-btn-secondary" id="sectionsCancel">Cancelar</button>
                <button class="sp-btn sp-btn-primary" id="sectionsSave">Salvar</button>
            </div>
            <!-- Overlay/modal para criar marca rapidamente -->
            <div class="ss-overlay" id="spCreateBrandOverlay" aria-modal="true" role="dialog" style="display:none;">
                <div class="ss-modal">
                    <div class="ss-header">
                        <span>Criar marca</span>
                        <button class="ss-btn ss-btn-secondary" id="spCreateBrandClose">Fechar</button>
                    </div>
                    <div class="ss-body">
                        <label class="form-label">Nome da marca</label>
                        <input type="text" id="spCreateBrandName" class="form-control mb-2" placeholder="Ex: Acme">
                        <label class="form-label">Slug (opcional)</label>
                        <input type="text" id="spCreateBrandSlug" class="form-control mb-2" placeholder="acme">
                        <label class="form-label">Logo (URL opcional)</label>
                        <input type="url" id="spCreateBrandLogo" class="form-control" placeholder="https://...">
                    </div>
                    <div class="ss-footer">
                        <button class="ss-btn ss-btn-secondary" id="spCreateBrandCancel">Cancelar</button>
                        <button class="ss-btn ss-btn-primary" id="spCreateBrandSave">Criar</button>
                    </div>
                </div>
            </div>
            <!-- Confirmação de troca de marca -->
            <div class="ss-overlay" id="spConfirmOverlay" aria-modal="true" role="dialog">
                <div class="ss-modal" style="max-width:520px;">
                    <div class="ss-header">
                        <span>Substituir sessão de marca</span>
                        <button class="ss-btn ss-btn-secondary" id="spConfirmClose">Fechar</button>
                    </div>
                    <div class="ss-body">
                        <div id="spConfirmText" class="mb-3" style="color:#334155; font-size:14px;"></div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="spConfirmAction" id="spActionReplace" value="replace" checked>
                            <label class="form-check-label" for="spActionReplace">
                                Substituir apenas a sessão (recomendado): não altera nenhum produto.
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="spConfirmAction" id="spActionHideOld" value="hide_old">
                            <label class="form-check-label" for="spActionHideOld">
                                Ocultar seção antiga se existir outra igual (não destrutivo).
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="spConfirmAction" id="spActionDeactivateProducts" value="deactivate_products">
                            <label class="form-check-label" for="spActionDeactivateProducts">
                                Também desativar todos os produtos da marca antiga neste departamento (exige confirmação abaixo).
                            </label>
                        </div>
                        <div id="spDangerBox" class="border rounded p-2" style="display:none; border-color:#fecaca; background:#fef2f2;">
                            <div style="font-size:13px; color:#7f1d1d;" class="mb-2">Para confirmar, digite exatamente: <strong id="spConfirmPhrase"></strong></div>
                            <input type="text" id="spConfirmInput" class="form-control" placeholder="Digite a frase de confirmação" />
                        </div>
                    </div>
                    <div class="ss-footer">
                        <button class="ss-btn ss-btn-secondary" id="spConfirmCancel">Cancelar</button>
                        <button class="ss-btn ss-btn-primary" id="spConfirmApply">Aplicar</button>
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
            
            // Tema UI
            const themeTrigger = document.getElementById('themeTrigger');
            const themePanel = document.getElementById('themePanel');
            const themeClose = document.getElementById('themeClose');
            const themeSave = document.getElementById('themeSave');
            const themeCancel = document.getElementById('themeCancel');
            const themeReset = document.getElementById('themeReset');
            // Seções UI
            const sectionsTrigger = document.getElementById('sectionsTrigger');
            const sectionsPanel = document.getElementById('sectionsPanel');
            const sectionsClose = document.getElementById('sectionsClose');
            const sectionsCancel = document.getElementById('sectionsCancel');
            const sectionsSave = document.getElementById('sectionsSave');
            const sectionsList = document.getElementById('sectionsList');
            const sectionsUnsupported = document.getElementById('sectionsUnsupported');
            const spAdd = document.getElementById('spAdd');
            const spNewBrandSelect = document.getElementById('spNewBrandSelect');
            const spNewTitle = document.getElementById('spNewTitle');

            const departmentsTrigger = document.getElementById('departmentsTrigger');
            const departmentsPanel = document.getElementById('departmentsPanel');
            const departmentsClose = document.getElementById('departmentsClose');
            const departmentsCancel = document.getElementById('departmentsCancel');
            const departmentsSave = document.getElementById('departmentsSave');
            const departmentsList = document.getElementById('departmentsList');
            const dpLoading = document.getElementById('dpLoading');
            const dpEmpty = document.getElementById('dpEmpty');
            const dpAdd = document.getElementById('dpAdd');
            const dpNewName = document.getElementById('dpNewName');
            const dpNewSlug = document.getElementById('dpNewSlug');
            const dpNewIcon = document.getElementById('dpNewIcon');
            const dpNewColor = document.getElementById('dpNewColor');
            const dpNewDescription = document.getElementById('dpNewDescription');
            const dpConfirmOverlay = document.getElementById('dpConfirmOverlay');
            const dpConfirmClose = document.getElementById('dpConfirmClose');
            const dpConfirmCancel = document.getElementById('dpConfirmCancel');
            const dpConfirmApply = document.getElementById('dpConfirmApply');
            const dpConfirmText = document.getElementById('dpConfirmText');
            const dpConfirmPhraseEl = document.getElementById('dpConfirmPhrase');
            const dpConfirmInput = document.getElementById('dpConfirmInput');

            @php
                // Resolve theme defaults preferring department-specific settings when available
                $deptSlugForJs = $currentDepartmentSlug ?? null;
                $td_primary = $deptSlugForJs ? setting('dept_'.$deptSlugForJs.'_theme_primary', setting('theme_primary', '#0f172a')) : setting('theme_primary', '#0f172a');
                $td_secondary = $deptSlugForJs ? setting('dept_'.$deptSlugForJs.'_theme_secondary', setting('theme_secondary', '#ff6b35')) : setting('theme_secondary', '#ff6b35');
                $td_accent = $deptSlugForJs ? setting('dept_'.$deptSlugForJs.'_theme_accent', setting('theme_accent', '#0f172a')) : setting('theme_accent', '#0f172a');
                $td_dark = $deptSlugForJs ? setting('dept_'.$deptSlugForJs.'_theme_dark_bg', setting('theme_dark_bg', '#1e293b')) : setting('theme_dark_bg', '#1e293b');
                $td_text_light = $deptSlugForJs ? setting('dept_'.$deptSlugForJs.'_theme_text_light', setting('theme_text_light', '#f8fafc')) : setting('theme_text_light', '#f8fafc');
                $td_text_dark = $deptSlugForJs ? setting('dept_'.$deptSlugForJs.'_theme_text_dark', setting('theme_text_dark', '#1e293b')) : setting('theme_text_dark', '#1e293b');
            @endphp
            const themeDefaultsRaw = {
                theme_primary: '{{ $td_primary }}',
                theme_secondary: '{{ $td_secondary }}',
                theme_accent: '{{ $td_accent }}',
                theme_dark_bg: '{{ $td_dark }}',
                theme_text_light: '{{ $td_text_light }}',
                theme_text_dark: '{{ $td_text_dark }}',
            };
            const normalizeHex = (value) => {
                if (value === null || value === undefined) return null;
                let hex = String(value).trim();
                if (!hex.length) return null;
                if (hex[0] !== '#') hex = '#' + hex;
                hex = hex.replace(/[^#a-fA-F0-9]/g, '');
                if (hex.length === 4) {
                    hex = '#' + hex[1] + hex[1] + hex[2] + hex[2] + hex[3] + hex[3];
                }
                if (hex.length !== 7) return null;
                return hex.toUpperCase();
            };
            const themeDefaults = {};
            Object.keys(themeDefaultsRaw).forEach(key => {
                themeDefaults[key] = normalizeHex(themeDefaultsRaw[key]) || '#0F172A';
            });
            let themeState = { ...themeDefaults };
            const colorFields = {
                theme_primary: { picker: document.getElementById('tpPrimary'), hex: document.getElementById('tpPrimaryHex') },
                theme_secondary: { picker: document.getElementById('tpSecondary'), hex: document.getElementById('tpSecondaryHex') },
                theme_accent: { picker: document.getElementById('tpAccent'), hex: document.getElementById('tpAccentHex') },
                theme_dark_bg: { picker: document.getElementById('tpDarkBg'), hex: document.getElementById('tpDarkBgHex') },
                theme_text_light: { picker: document.getElementById('tpTextLight'), hex: document.getElementById('tpTextLightHex') },
                theme_text_dark: { picker: document.getElementById('tpTextDark'), hex: document.getElementById('tpTextDarkHex') },
            };
            const presetButtons = document.querySelectorAll('.tp-preset-btn');
            let suspendThemeBroadcast = false;

            const getThemePayload = () => ({
                primary: themeState.theme_primary,
                secondary: themeState.theme_secondary,
                accent: themeState.theme_accent,
                dark_bg: themeState.theme_dark_bg,
                text_light: themeState.theme_text_light,
                text_dark: themeState.theme_text_dark,
            });
            const broadcastTheme = () => {
                applyThemeLocally(getThemePayload());
            };
            const flagInvalidHex = (key) => {
                const hexInput = colorFields[key]?.hex;
                if (!hexInput) return;
                hexInput.classList.add('is-invalid');
                setTimeout(() => {
                    hexInput.classList.remove('is-invalid');
                    hexInput.value = themeState[key];
                }, 1200);
            };
            const setThemeValue = (key, value, source = 'update') => {
                const normalized = normalizeHex(value);
                if (!normalized) {
                    if (source === 'hex') {
                        flagInvalidHex(key);
                    }
                    return;
                }
                themeState[key] = normalized;
                const refs = colorFields[key] || {};
                if (refs.picker && source !== 'picker') refs.picker.value = normalized;
                if (refs.hex && source !== 'hex') {
                    refs.hex.value = normalized;
                    refs.hex.classList.remove('is-invalid');
                }
                if (!suspendThemeBroadcast) {
                    broadcastTheme();
                }
            };
            const setThemeValues = (values) => {
                suspendThemeBroadcast = true;
                Object.keys(colorFields).forEach(key => {
                    if (values[key]) {
                        setThemeValue(key, values[key], 'bulk');
                    }
                });
                suspendThemeBroadcast = false;
                broadcastTheme();
            };

            setThemeValues(themeState);

            Object.entries(colorFields).forEach(([key, refs]) => {
                if (refs.picker) {
                    refs.picker.addEventListener('input', () => setThemeValue(key, refs.picker.value, 'picker'));
                }
                if (refs.hex) {
                    refs.hex.addEventListener('input', () => refs.hex.classList.remove('is-invalid'));
                    refs.hex.addEventListener('blur', () => setThemeValue(key, refs.hex.value, 'hex'));
                    refs.hex.addEventListener('keydown', (ev) => {
                        if (ev.key === 'Enter') {
                            ev.preventDefault();
                            setThemeValue(key, refs.hex.value, 'hex');
                        }
                    });
                }
            });

            presetButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const preset = {
                        theme_primary: btn.dataset.primary,
                        theme_secondary: btn.dataset.secondary,
                        theme_accent: btn.dataset.accent,
                        theme_dark_bg: btn.dataset.dark,
                        theme_text_light: btn.dataset.light,
                        theme_text_dark: btn.dataset.text,
                    };
                    setThemeValues(preset);
                });
            });

            let availableBrands = null; // null sinaliza que ainda não carregamos ou houve falha
            let departmentsLoaded = false;
            let departmentsData = [];
            let dpPendingToggle = null;

            function slugify(str) {
                return (str || '')
                    .toString()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '')
                    .replace(/-{2,}/g, '-')
                    .slice(0, 80);
            }

            function escapeHtml(str) {
                return (str ?? '').toString()
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            if (!searchTrigger || !searchPanel) return;

            let searchTimeout = null;

            // Abertura/fechamento do painel de tema
            if (themeTrigger && themePanel) {
                themeTrigger.addEventListener('click', function(){
                    themePanel.classList.toggle('active');
                });
                themeClose && themeClose.addEventListener('click', () => themePanel.classList.remove('active'));
                themeCancel && themeCancel.addEventListener('click', () => themePanel.classList.remove('active'));
                document.addEventListener('click', function(e){
                    if (themePanel.classList.contains('active') && !themePanel.contains(e.target) && !themeTrigger.contains(e.target)) {
                        themePanel.classList.remove('active');
                    }
                });
            }

            // Abertura/fechamento do painel de departamentos
            if (departmentsTrigger && departmentsPanel) {
                departmentsTrigger.addEventListener('click', function(){
                    const willOpen = !departmentsPanel.classList.contains('active');
                    if (willOpen) {
                        departmentsPanel.classList.add('active');
                        initDepartmentsPanel();
                    } else {
                        departmentsPanel.classList.remove('active');
                    }
                });
                departmentsClose && departmentsClose.addEventListener('click', () => departmentsPanel.classList.remove('active'));
                departmentsCancel && departmentsCancel.addEventListener('click', () => departmentsPanel.classList.remove('active'));
                document.addEventListener('click', function(e){
                    if (departmentsPanel.classList.contains('active') && !departmentsPanel.contains(e.target) && !departmentsTrigger.contains(e.target)) {
                        departmentsPanel.classList.remove('active');
                    }
                });
            }

            function initDepartmentsPanel(){
                if (!departmentsPanel) return Promise.resolve();
                if (departmentsLoaded) {
                    toggleDepartmentsEmpty();
                    return Promise.resolve();
                }
                if (dpLoading) {
                    dpLoading.style.display = 'block';
                    dpLoading.textContent = 'Carregando departamentos...';
                }
                if (dpEmpty) dpEmpty.style.display = 'none';
                return fetch(`/admin/departments/inline-snapshot`, { headers: { 'Accept': 'application/json' }})
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Não foi possível carregar os departamentos.');
                        departmentsData = Array.isArray(data.departments) ? data.departments : [];
                        renderDepartmentsList(departmentsData);
                        departmentsLoaded = true;
                    })
                    .catch(err => {
                        console.error('Departamentos:', err);
                        if (dpLoading) dpLoading.textContent = 'Erro ao carregar departamentos.';
                    })
                    .finally(() => {
                        if (dpLoading) {
                            setTimeout(() => { dpLoading.style.display = 'none'; }, 250);
                        }
                    });
            }

            function renderDepartmentsList(items){
                if (!departmentsList) return;
                departmentsList.innerHTML = '';
                const arr = Array.isArray(items) ? items : [];
                arr.forEach(item => appendDepartmentItem(item));
                toggleDepartmentsEmpty();
                departmentsData = arr.slice();
            }

            function appendDepartmentItem(dept){
                if (!departmentsList) return;
                const li = document.createElement('li');
                const productsCount = Number(dept?.products_count ?? 0);
                const isActive = dept?.is_active !== false;
                // Prefer department-specific theme_primary when available, fallback to dept.color
                const desiredColor = ((dept && (dept.theme_primary || dept.color)) || '#667eea').trim();
                const color = desiredColor && desiredColor.startsWith('#') ? desiredColor : '#667eea';
                const slugValue = dept?.slug ? dept.slug : slugify(dept?.name || '');
                li.className = 'dp-item';
                li.dataset.id = dept?.id ? String(dept.id) : '';
                li.dataset.products = String(productsCount);
                li.dataset.url = dept?.url || '';
                li.dataset.slug = slugValue || '';
                li.innerHTML = `
                    <div class="dp-handle" title="Arrastar"><i class="bi bi-list"></i></div>
                    <div>
                        <div class="dp-name-row">
                            <input type="text" class="form-control form-control-sm dp-name" value="${escapeHtml(dept?.name || '')}" placeholder="Nome exibido">
                            <label class="dp-toggle">
                                <input type="checkbox" class="form-check-input dp-active" ${isActive ? 'checked' : ''} data-prev="${isActive ? '1' : '0'}"> ativo
                            </label>
                        </div>
                        <div class="dp-meta-row">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Slug</span>
                                <input type="text" class="form-control dp-slug" value="${escapeHtml(slugValue || '')}" data-manual="${dept?.slug ? 'true' : 'false'}">
                            </div>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control form-control-sm dp-icon" placeholder="fa-solid fa-store" value="${escapeHtml(dept?.icon || '')}">
                                <input type="color" class="dp-color form-control form-control-color" value="${escapeHtml(color)}" title="Cor destaque">
                            </div>
                        </div>
                        <textarea class="form-control form-control-sm dp-description" placeholder="Descrição (opcional)">${escapeHtml(dept?.description || '')}</textarea>
                        <div class="dp-info-row">
                            <small>Produtos ativos: <strong>${productsCount}</strong></small>
                            <div class="dp-actions">
                                <button type="button" class="sp-btn sp-btn-secondary dp-up" title="Subir"><i class="bi bi-arrow-up"></i></button>
                                <button type="button" class="sp-btn sp-btn-secondary dp-down" title="Descer"><i class="bi bi-arrow-down"></i></button>
                                ${dept?.id ? `<a href="${escapeHtml(dept?.url || '')}" class="sp-btn sp-btn-secondary dp-view" target="_blank" rel="noopener">Ver página</a>` : `<button type="button" class="sp-btn sp-btn-danger dp-remove" title="Remover rascunho"><i class="bi bi-trash"></i></button>`}
                            </div>
                        </div>
                    </div>
                `;
                departmentsList.appendChild(li);
                // When color input changes in the admin panel, update :root primary color for instant preview
                const colorInput = li.querySelector('.dp-color');
                if (colorInput) {
                    colorInput.addEventListener('input', function() {
                        try {
                            const val = (this.value || '').trim();
                            if (val && val[0] !== '#') return;
                            // Apply preview globally (this mirrors the theme preview behavior)
                            document.documentElement.style.setProperty('--primary-color', val);
                        } catch(e) {}
                    });
                }
            }

            function toggleDepartmentsEmpty(){
                if (!dpEmpty || !departmentsList) return;
                dpEmpty.style.display = departmentsList.children.length ? 'none' : 'block';
            }

            function readDepartmentsList(){
                if (!departmentsList) return [];
                return Array.from(departmentsList.querySelectorAll('.dp-item')).map((item, index) => {
                    const name = item.querySelector('.dp-name')?.value?.trim() || '';
                    const slug = item.querySelector('.dp-slug')?.value?.trim() || '';
                    const icon = item.querySelector('.dp-icon')?.value?.trim() || '';
                    const color = item.querySelector('.dp-color')?.value?.trim() || '';
                    const description = item.querySelector('.dp-description')?.value?.trim() || '';
                    const checkbox = item.querySelector('.dp-active');
                    return {
                        id: item.dataset.id ? Number(item.dataset.id) : null,
                        name,
                        slug,
                        icon,
                        color,
                        description,
                        is_active: checkbox ? checkbox.checked : true,
                        products_count: parseInt(item.dataset.products || '0', 10) || 0,
                        url: item.dataset.url || '',
                        sort_order: index,
                    };
                });
            }

            function syncDepartmentsDataFromDOM(){
                departmentsData = readDepartmentsList();
            }

            function handleDpActiveToggle(checkbox){
                const item = checkbox.closest('.dp-item');
                if (!item) return;
                const wasActive = checkbox.dataset.prev === '1';
                const nowActive = checkbox.checked;
                if (nowActive) {
                    checkbox.dataset.prev = '1';
                    syncDepartmentsDataFromDOM();
                    return;
                }
                if (!wasActive) {
                    syncDepartmentsDataFromDOM();
                    return;
                }
                const productsCount = parseInt(item.dataset.products || '0', 10) || 0;
                if (productsCount <= 0) {
                    checkbox.dataset.prev = '0';
                    syncDepartmentsDataFromDOM();
                    return;
                }
                dpPendingToggle = { checkbox, item, productsCount };
                openDpConfirm(item, productsCount);
            }

            function openDpConfirm(item, productsCount){
                if (!dpConfirmOverlay) return;
                const name = (item.querySelector('.dp-name')?.value || '').trim() || 'Departamento';
                const slugBase = (item.querySelector('.dp-slug')?.value || '').trim() || slugify(name);
                const phrase = `DESATIVAR ${slugBase.toUpperCase()}`;
                dpConfirmText.textContent = `O departamento "${name}" possui ${productsCount} produto(s) ativo(s). Desativar vai removê-lo das vitrines e navegação principal.`;
                dpConfirmPhraseEl.textContent = phrase;
                dpConfirmInput.value = '';
                dpConfirmOverlay.classList.add('active');
            }

            function closeDpConfirm(cancelled){
                if (dpConfirmOverlay) {
                    dpConfirmOverlay.classList.remove('active');
                }
                if (cancelled && dpPendingToggle?.checkbox) {
                    dpPendingToggle.checkbox.checked = true;
                }
                dpPendingToggle = null;
            }

            dpNewSlug && (dpNewSlug.dataset.manual = 'false');
            dpNewName?.addEventListener('input', function(){
                if (dpNewSlug && dpNewSlug.dataset.manual !== 'true') {
                    dpNewSlug.value = slugify(this.value);
                }
            });
            dpNewSlug?.addEventListener('input', function(){
                this.dataset.manual = this.value.trim().length ? 'true' : 'false';
            });

            dpAdd?.addEventListener('click', function(){
                const name = dpNewName?.value?.trim();
                if (!name) { alert('Informe o nome do novo departamento.'); return; }
                const slugBase = dpNewSlug?.value?.trim();
                const slug = slugify(slugBase || name);
                if (!slug) { alert('Slug inválido. Ajuste o nome ou o slug manualmente.'); return; }
                let color = (dpNewColor?.value || '#667eea').trim();
                if (!color.startsWith('#') || color.length < 4) {
                    color = '#667eea';
                }
                const icon = dpNewIcon?.value?.trim() || '';
                const description = dpNewDescription?.value?.trim() || '';
                const newDept = {
                    id: null,
                    name,
                    slug,
                    icon,
                    color,
                    description,
                    is_active: true,
                    products_count: 0,
                    url: ''
                };
                departmentsData.push(newDept);
                appendDepartmentItem(newDept);
                toggleDepartmentsEmpty();
                syncDepartmentsDataFromDOM();
                if (dpNewName) dpNewName.value = '';
                if (dpNewSlug) { dpNewSlug.value = ''; dpNewSlug.dataset.manual = 'false'; }
                if (dpNewIcon) dpNewIcon.value = '';
                if (dpNewColor) dpNewColor.value = '#667eea';
                if (dpNewDescription) dpNewDescription.value = '';
                dpNewName?.focus();
            });

            departmentsList?.addEventListener('click', function(e){
                const item = e.target.closest('.dp-item');
                if (!item) return;
                if (e.target.closest('.dp-up')) {
                    moveItem(item, -1);
                    syncDepartmentsDataFromDOM();
                } else if (e.target.closest('.dp-down')) {
                    moveItem(item, +1);
                    syncDepartmentsDataFromDOM();
                } else if (e.target.closest('.dp-remove')) {
                    item.remove();
                    syncDepartmentsDataFromDOM();
                    toggleDepartmentsEmpty();
                }
            });

            departmentsList?.addEventListener('input', function(e){
                const nameInput = e.target.closest('.dp-name');
                if (nameInput) {
                    const item = nameInput.closest('.dp-item');
                    const slugInput = item?.querySelector('.dp-slug');
                    if (slugInput && slugInput.dataset.manual !== 'true') {
                        slugInput.value = slugify(nameInput.value);
                    }
                    return;
                }
                const slugInput = e.target.closest('.dp-slug');
                if (slugInput) {
                    slugInput.dataset.manual = slugInput.value.trim().length ? 'true' : 'false';
                }
            });

            departmentsList?.addEventListener('change', function(e){
                const checkbox = e.target.closest('.dp-active');
                if (checkbox) {
                    handleDpActiveToggle(checkbox);
                }
            });

            dpConfirmClose?.addEventListener('click', () => closeDpConfirm(true));
            dpConfirmCancel?.addEventListener('click', () => closeDpConfirm(true));
            dpConfirmApply?.addEventListener('click', function(){
                if (!dpPendingToggle) {
                    closeDpConfirm(false);
                    return;
                }
                const phrase = (dpConfirmPhraseEl?.textContent || '').trim();
                if ((dpConfirmInput?.value || '').trim().toUpperCase() !== phrase) {
                    alert('Confirmação inválida. Digite exatamente a frase exibida.');
                    return;
                }
                if (dpPendingToggle.checkbox) {
                    dpPendingToggle.checkbox.dataset.prev = '0';
                }
                closeDpConfirm(false);
                syncDepartmentsDataFromDOM();
            });

            departmentsSave?.addEventListener('click', function(){
                try {
                    const list = readDepartmentsList();
                    if (!list.length) {
                        alert('Adicione pelo menos um departamento antes de salvar.');
                        return;
                    }
                    const cleaned = list.map((dept, idx) => {
                        const name = (dept.name || '').trim();
                        if (!name) throw new Error('Informe o nome de todos os departamentos.');
                        const slug = slugify(dept.slug || name);
                        if (!slug) throw new Error('Slug inválido encontrado. Ajuste antes de salvar.');
                        let color = (dept.color || '#667eea').trim();
                        if (!color.startsWith('#') || color.length < 4) {
                            color = '#667eea';
                        }
                        return {
                            id: dept.id,
                            name,
                            slug,
                            icon: dept.icon || null,
                            color,
                            description: dept.description || null,
                            is_active: !!dept.is_active,
                            sort_order: idx,
                        };
                    });
                    const slugSet = new Set();
                    cleaned.forEach(item => {
                        if (slugSet.has(item.slug)) {
                            throw new Error('Existem departamentos com slugs duplicados. Ajuste antes de salvar.');
                        }
                        slugSet.add(item.slug);
                    });
                    fetch(`/admin/departments/inline-sync`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ departments: cleaned })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Erro ao atualizar departamentos.');
                        departmentsData = Array.isArray(data.departments) ? data.departments : [];
                        departmentsLoaded = true;
                        renderDepartmentsList(departmentsData);
                        departmentsPanel.classList.remove('active');
                    })
                    .catch(err => alert(err.message));
                } catch (validationError) {
                    alert(validationError.message || 'Verifique os campos dos departamentos antes de salvar.');
                }
            });

            // Abertura/fechamento do painel de seções
            if (sectionsTrigger && sectionsPanel) {
                sectionsTrigger.addEventListener('click', function(){
                    initSectionsPanel();
                    sectionsPanel.classList.toggle('active');
                });
                sectionsClose && sectionsClose.addEventListener('click', () => sectionsPanel.classList.remove('active'));
                sectionsCancel && sectionsCancel.addEventListener('click', () => sectionsPanel.classList.remove('active'));
                document.addEventListener('click', function(e){
                    if (sectionsPanel.classList.contains('active') && !sectionsPanel.contains(e.target) && !sectionsTrigger.contains(e.target)) {
                        sectionsPanel.classList.remove('active');
                    }
                });
            }

            function applyThemeLocally(values){
                const root = document.documentElement;
                if (values.primary) root.style.setProperty('--primary-color', values.primary);
                if (values.secondary) root.style.setProperty('--secondary-color', values.secondary);
                if (values.accent) root.style.setProperty('--accent-color', values.accent);
                if (values.dark_bg) root.style.setProperty('--dark-bg', values.dark_bg);
                if (values.text_light) root.style.setProperty('--text-light', values.text_light);
                if (values.text_dark) root.style.setProperty('--text-dark', values.text_dark);
                // Atualiza meta theme-color para mobile
                const metaTheme = document.querySelector('meta[name="theme-color"]');
                if (metaTheme && values.secondary) metaTheme.setAttribute('content', values.secondary);
            }

            function getCurrentDepartmentSlug() {
                if (window.CurrentDepartmentSlug) return String(window.CurrentDepartmentSlug);
                try {
                    const m = window.location.pathname.match(/\/departamento\/([^\/?#]+)/i);
                    return m && m[1] ? decodeURIComponent(m[1]) : null;
                } catch(e){ return null; }
            }
            if (themeSave) {
                themeSave.addEventListener('click', function(){
                    const payload = { ...themeState };
                    applyThemeLocally(getThemePayload());
                    const deptSlug = getCurrentDepartmentSlug();
                    let settingsPayload = {};
                    if (deptSlug) {
                        Object.entries(payload).forEach(([key, value]) => {
                            settingsPayload[`dept_${deptSlug}_${key}`] = value;
                        });
                    } else {
                        settingsPayload = payload;
                    }
                    fetch(`/admin/settings`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(settingsPayload)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Erro ao salvar tema');
                        themePanel.classList.remove('active');
                    })
                    .catch(err => alert(err.message));
                });
            }

            if (themeReset) {
                themeReset.addEventListener('click', function(){
                    themeState = { ...themeDefaults };
                    setThemeValues(themeState);
                    const deptSlug = getCurrentDepartmentSlug();
                    let settingsPayload = {};
                    if (deptSlug) {
                        Object.entries(themeDefaults).forEach(([key, value]) => {
                            settingsPayload[`dept_${deptSlug}_${key}`] = value;
                        });
                    } else {
                        settingsPayload = themeDefaults;
                    }
                    fetch(`/admin/settings`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(settingsPayload)
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Erro ao restaurar tema');
                        themePanel.classList.remove('active');
                    })
                    .catch(err => alert(err.message));
                });
            }

            // Seções: estado e helpers (generalizado por departamento)
            function detectDepartmentSlug(){
                if (window.CurrentDepartmentSlug) return String(window.CurrentDepartmentSlug);
                try {
                    const m = window.location.pathname.match(/\/departamento\/([^\/?#]+)/i);
                    return m && m[1] ? decodeURIComponent(m[1]) : null;
                } catch(e){ return null; }
            }
            function onDepartmentPage(){ return !!detectDepartmentSlug(); }
            function getCurrentSectionsConfig(){
                // Fonte principal: variável global definida na página Eletrônicos
                const raw = window.DepartmentSectionsConfig || [];
                if (Array.isArray(raw)) return raw;
                try { const parsed = JSON.parse(raw); return Array.isArray(parsed) ? parsed : []; } catch(e){ return []; }
            }
            function fetchBrands(){
                const dept = detectDepartmentSlug() || 'eletronicos';
                const targetUrl = `/admin/products/brands-list?department=${encodeURIComponent(dept)}`;
                // Try to fetch brands scoped to the department; if empty, try a fallback without department param
                return fetch(targetUrl, { headers: { 'Accept': 'application/json' }})
                    .then(r => r.json())
                    .then(data => {
                        let brandsPayload = [];
                        if (Array.isArray(data.brands)) {
                            brandsPayload = data.brands;
                        } else if (data.brands && typeof data.brands === 'object') {
                            brandsPayload = Object.values(data.brands);
                        }

                        availableBrands = (brandsPayload || []).map(b => (b ?? '').toString().trim()).filter(Boolean);
                        if ((!availableBrands || !availableBrands.length)) {
                            // fallback: try without department param
                            return fetch('/admin/products/brands-list', { headers: { 'Accept': 'application/json' }})
                                .then(r2 => r2.json())
                                .then(data2 => {
                                    let bp = [];
                                    if (Array.isArray(data2.brands)) bp = data2.brands;
                                    else if (data2.brands && typeof data2.brands === 'object') bp = Object.values(data2.brands);
                                    availableBrands = (bp || []).map(b => (b ?? '').toString().trim()).filter(Boolean);
                                    populateBrandsSelect();
                                })
                                .catch(() => { availableBrands = availableBrands || []; populateBrandsSelect(); });
                        }
                        populateBrandsSelect();
                    })
                    .catch(err => { console.error('fetchBrands error', err); availableBrands = []; populateBrandsSelect(); });
            }

            function populateBrandsSelect(){
                if (!spNewBrandSelect) return;
                const baseOption = '<option value="">Selecione a marca…</option>';
                if (Array.isArray(availableBrands) && availableBrands.length) {
                    const optionsHtml = baseOption + availableBrands.map(b => `<option value="${escapeHtml(b)}">${escapeHtml(b)}</option>`).join('');
                    spNewBrandSelect.innerHTML = optionsHtml;
                    // Also update quick-create brand select if present
                    const qpBrandSelect = document.getElementById('qpBrand');
                    if (qpBrandSelect) qpBrandSelect.innerHTML = optionsHtml;
                    const warn = document.getElementById('spBrandsWarning'); if (warn) warn.style.display = 'none';
                } else {
                    spNewBrandSelect.innerHTML = baseOption;
                    const qpBrandSelect = document.getElementById('qpBrand');
                    if (qpBrandSelect) qpBrandSelect.innerHTML = baseOption;
                    const warn = document.getElementById('spBrandsWarning'); if (warn) warn.style.display = 'block';
                }
            }

            // When a brand is chosen manually, hide the 'no brands' warning
            if (spNewBrandSelect) {
                spNewBrandSelect.addEventListener('change', function(){
                    const warn = document.getElementById('spBrandsWarning');
                    if (!warn) return;
                    if (this.value && this.value.trim()) warn.style.display = 'none';
                    else {
                        // if select has options > 1 it means there are brands available
                        warn.style.display = this.options && this.options.length > 1 ? 'none' : 'block';
                    }
                });
            }

            // Quick-create brand modal handling
            const spCreateBrandBtn = document.getElementById('spCreateBrandBtn');
            const spCreateBrandOverlay = document.getElementById('spCreateBrandOverlay');
            const spCreateBrandClose = document.getElementById('spCreateBrandClose');
            const spCreateBrandCancel = document.getElementById('spCreateBrandCancel');
            const spCreateBrandSave = document.getElementById('spCreateBrandSave');
            const spCreateBrandName = document.getElementById('spCreateBrandName');
            const spCreateBrandSlug = document.getElementById('spCreateBrandSlug');
            const spCreateBrandLogo = document.getElementById('spCreateBrandLogo');

            function openSpCreateBrand(){
                if (!spCreateBrandOverlay) return;
                spCreateBrandName.value = '';
                spCreateBrandSlug.value = '';
                spCreateBrandLogo.value = '';
                spCreateBrandOverlay.style.display = 'flex';
                setTimeout(() => spCreateBrandName.focus(), 50);
            }
            function closeSpCreateBrand(){
                if (!spCreateBrandOverlay) return;
                spCreateBrandOverlay.style.display = 'none';
            }

            if (spCreateBrandBtn) {
                spCreateBrandBtn.addEventListener('click', function(){ openSpCreateBrand(); });
            }
            spCreateBrandClose?.addEventListener('click', closeSpCreateBrand);
            spCreateBrandCancel?.addEventListener('click', closeSpCreateBrand);

            spCreateBrandSave?.addEventListener('click', function(){
                const name = (spCreateBrandName?.value || '').trim();
                const slug = (spCreateBrandSlug?.value || '').trim();
                const logo = (spCreateBrandLogo?.value || '').trim();
                if (!name) { alert('Informe o nome da marca.'); return; }
                const payload = { name };
                if (slug) payload.department = slug; // reuse department param optionally for scoping
                if (logo) payload.logo = logo;

                fetch('/admin/brands/inline-create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (!data || !data.success) throw new Error(data?.message || 'Erro ao criar marca');
                    // Add to available brands and select it
                    availableBrands = availableBrands || [];
                    if (!availableBrands.find(b => b.toLowerCase() === data.brand.toLowerCase())) {
                        availableBrands.push(data.brand);
                    }
                    populateBrandsSelect();
                    spNewBrandSelect.value = data.brand;
                    // Also set the quick-create product brand select if present
                    const qpBrandSelect = document.getElementById('qpBrand');
                    if (qpBrandSelect) qpBrandSelect.value = data.brand;
                    closeSpCreateBrand();
                })
                .catch(err => { alert(err.message || 'Erro ao criar marca'); });
            });
            function renderSectionsList(){
                const arr = getCurrentSectionsConfig();
                // If not on a department page, show a hint but still render any available configuration
                if (!onDepartmentPage()) {
                    sectionsUnsupported.style.display = 'block';
                    sectionsUnsupported.textContent = 'Abra este painel em uma página de departamento para gerenciar as sessões.';
                } else {
                    sectionsUnsupported.style.display = 'none';
                }
                // continue to render sections from available configuration even when not on-department
                sectionsList.innerHTML = '';
                const hasLookup = Array.isArray(availableBrands) && availableBrands.length > 0;
                const sanitizedBrands = hasLookup ? availableBrands.map(b => (b ?? '').toString().trim()).filter(Boolean) : [];
                arr.forEach((sec, idx) => {
                    const li = document.createElement('li');
                    li.className = 'sp-item';
                    const curr = (sec.brand || '').toString().trim();
                    li.innerHTML = `
                        <div class="sp-handle" title="Arrastar"><i class="bi bi-list"></i></div>
                        <div>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="text" class="form-control form-control-sm sp-title" value="${sec.title || ('Produtos ' + curr)}" placeholder="Título">
                                <div class="sp-actions">
                                    <button type="button" class="sp-btn sp-btn-secondary sp-up" title="Subir"><i class="bi bi-arrow-up"></i></button>
                                    <button type="button" class="sp-btn sp-btn-secondary sp-down" title="Descer"><i class="bi bi-arrow-down"></i></button>
                                    <button type="button" class="sp-btn sp-btn-danger sp-remove" title="Remover"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <small>Marca:
                                <select class="form-select form-select-sm sp-brand-select" style="display:inline-block; width: 180px; margin-left:6px;">
                                    ${(() => {
                                        const normalizedCurr = curr.toLowerCase();
                                        const exists = hasLookup ? sanitizedBrands.some(b => b.toLowerCase() === normalizedCurr) : true;

                                        if (!hasLookup) {
                                            return curr ? `<option value="${curr}" selected>${curr}</option>` : '';
                                        }

                                        const missingOpt = (!exists && curr)
                                            ? `<option value="${curr}" selected>${curr} (inativa)</option>`
                                            : '';

                                        const opts = sanitizedBrands.map(b => {
                                            const sel = normalizedCurr === b.toLowerCase() ? 'selected' : '';
                                            return `<option value="${b}" ${sel}>${b}</option>`;
                                        }).join('');

                                        return missingOpt + opts;
                                    })()}
                                </select>
                            </small>
                        </div>
                        <label class="sp-toggle">
                            <input type="checkbox" class="form-check-input sp-enabled" ${sec.enabled === false ? '' : 'checked'}> visível
                        </label>
                    `;
                    sectionsList.appendChild(li);
                });
            }
            function readSectionsList(){
                const items = sectionsList.querySelectorAll('.sp-item');
                const arr = [];
                items.forEach(it => {
                    arr.push({
                        brand: it.querySelector('.sp-brand-select')?.value?.trim() || '',
                        title: it.querySelector('.sp-title')?.value?.trim() || '',
                        enabled: it.querySelector('.sp-enabled')?.checked !== false
                    });
                });
                return arr.filter(x => x.brand);
            }
            function moveItem(el, dir){
                if (!el) return;
                if (dir < 0 && el.previousElementSibling) el.parentNode.insertBefore(el, el.previousElementSibling);
                if (dir > 0 && el.nextElementSibling) el.parentNode.insertBefore(el.nextElementSibling, el);
            }
            function initSectionsPanel(){
                // When opening the Sections panel in a department page, prefer loading persisted DB sections
                const dept = detectDepartmentSlug() || 'eletronicos';
                fetchBrands().then(() => {
                    // Try to fetch saved sections from server for this department
                    fetch(`/admin/departments/${encodeURIComponent(dept)}/sections`, { headers: { 'Accept': 'application/json' }})
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.success && Array.isArray(data.sections)) {
                                // Convert to the legacy config shape
                                window.DepartmentSectionsConfig = data.sections.map(s => ({
                                    brand: s.type === 'brand' ? (s.reference || '') : '',
                                    title: s.title || (s.reference ? ('Produtos ' + s.reference) : ''),
                                    enabled: s.enabled !== false,
                                    type: s.type,
                                    reference: s.reference,
                                    id: s.id,
                                }));
                                renderSectionsList();
                                return;
                            }
                            // fallback to client config
                            renderSectionsList();
                        })
                        .catch(() => {
                            // fallback when server not available
                            renderSectionsList();
                        });
                });
            }
            sectionsList?.addEventListener('click', function(e){
                const item = e.target.closest('.sp-item');
                if (!item) return;
                if (e.target.closest('.sp-up')) moveItem(item, -1);
                if (e.target.closest('.sp-down')) moveItem(item, +1);
                if (e.target.closest('.sp-remove')) item.remove();
            });
            // Confirmação ao trocar a marca de uma seção existente
            let spPendingChange = null; // {itemEl, oldBrand, newBrand}
            sectionsList?.addEventListener('change', function(e){
                const select = e.target.closest('.sp-brand-select');
                if (!select) return;
                const item = e.target.closest('.sp-item');
                const oldBrand = (select.getAttribute('data-prev') || '').trim();
                const newBrand = (select.value || '').trim();
                if (!oldBrand) {
                    // primeira vez: apenas anotar prev e sair
                    select.setAttribute('data-prev', newBrand);
                    return;
                }
                if (oldBrand.toLowerCase() === newBrand.toLowerCase()) return;
                // Abrir confirmação
                spPendingChange = { itemEl: item, oldBrand, newBrand, select };
                openSpConfirm(oldBrand, newBrand);
            });
            spAdd?.addEventListener('click', function(){
                const brand = (spNewBrandSelect?.value || '').trim();
                const title = (spNewTitle?.value || '').trim();
                if (!brand) { alert('Informe a marca.'); return; }
                if (Array.isArray(availableBrands) && availableBrands.length && !availableBrands.find(b => String(b).toLowerCase() === brand.toLowerCase())) {
                    alert('Marca inválida. Selecione uma marca existente.');
                    return;
                }

                const current = getCurrentSectionsConfig();
                current.push({ brand, title: title || ('Produtos ' + brand), enabled: true });
                window.DepartmentSectionsConfig = current;
                spNewBrandSelect.value = '';
                spNewTitle.value = '';
                renderSectionsList();
            });
            spNewBrandSelect?.addEventListener('change', function(){
                if (!spNewTitle.value && this.value) {
                    spNewTitle.value = 'Produtos ' + this.value;
                }
            });
            // Overlay de confirmação para troca de marca
            const spConfirmOverlay = document.getElementById('spConfirmOverlay');
            const spConfirmClose = document.getElementById('spConfirmClose');
            const spConfirmCancel = document.getElementById('spConfirmCancel');
            const spConfirmApply = document.getElementById('spConfirmApply');
            const spConfirmText = document.getElementById('spConfirmText');
            const spDangerBox = document.getElementById('spDangerBox');
            const spConfirmPhraseEl = document.getElementById('spConfirmPhrase');
            const spConfirmInput = document.getElementById('spConfirmInput');
            const spActionReplace = document.getElementById('spActionReplace');
            const spActionHideOld = document.getElementById('spActionHideOld');
            const spActionDeactivateProducts = document.getElementById('spActionDeactivateProducts');

            function openSpConfirm(oldBrand, newBrand){
                if (!spConfirmOverlay) return;
                spConfirmText.innerHTML = `Você está substituindo a sessão <strong>${oldBrand}</strong> por <strong>${newBrand}</strong>. O que deseja fazer?`;
                spActionReplace.checked = true;
                spDangerBox.style.display = 'none';
                const phrase = `DESATIVAR ${oldBrand.toUpperCase()}`;
                spConfirmPhraseEl.textContent = phrase;
                spConfirmInput.value = '';
                spConfirmOverlay.classList.add('active');
            }
            function closeSpConfirm(){
                spConfirmOverlay?.classList.remove('active');
                // Reverter select se não aplicou
                if (spPendingChange?.select) {
                    spPendingChange.select.value = spPendingChange.oldBrand;
                }
                spPendingChange = null;
            }
            // Mostrar/esconder caixa de perigo
            [spActionReplace, spActionHideOld, spActionDeactivateProducts].forEach(r => {
                r?.addEventListener('change', function(){
                    spDangerBox.style.display = spActionDeactivateProducts.checked ? 'block' : 'none';
                });
            });
            spConfirmClose?.addEventListener('click', closeSpConfirm);
            spConfirmCancel?.addEventListener('click', closeSpConfirm);
            spConfirmApply?.addEventListener('click', function(){
                if (!spPendingChange) { closeSpConfirm(); return; }
                const { itemEl, oldBrand, newBrand, select } = spPendingChange;
                // Aplicar troca no item atual
                select.setAttribute('data-prev', newBrand);
                // Ajustar título se estiver vazio ou igual ao padrão anterior
                const titleInput = itemEl.querySelector('.sp-title');
                if (titleInput && (!titleInput.value || titleInput.value === `Produtos ${oldBrand}`)) {
                    titleInput.value = `Produtos ${newBrand}`;
                }
                // Ação extra: ocultar seção antiga duplicada, se existir
                if (spActionHideOld?.checked) {
                    const items = sectionsList.querySelectorAll('.sp-item');
                    items.forEach(li => {
                        if (li === itemEl) return;
                        const sel = li.querySelector('.sp-brand-select');
                        if (sel && (sel.value||'').trim().toLowerCase() === oldBrand.toLowerCase()) {
                            const chk = li.querySelector('.sp-enabled');
                            if (chk) chk.checked = false;
                        }
                    });
                }
                const dept = detectDepartmentSlug() || 'eletronicos';
                // Ação extra: desativar produtos da marca antiga
                const doDeactivate = spActionDeactivateProducts?.checked === true;
                const proceedAfter = () => {
                    spPendingChange = null;
                    spConfirmOverlay?.classList.remove('active');
                };
                if (!doDeactivate) { proceedAfter(); return; }
                // Validar frase
                const needed = (document.getElementById('spConfirmPhrase')?.textContent||'').trim();
                if ((spConfirmInput?.value||'').trim().toUpperCase() !== needed) {
                    alert('Confirmação inválida. Digite exatamente a frase exibida.');
                    return;
                }
                fetch('/admin/products/bulk-toggle-by-brand', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ department: dept, brand: oldBrand, active: false })
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Erro ao desativar produtos da marca antiga');
                    proceedAfter();
                })
                .catch(err => { alert(err.message); proceedAfter(); });
            });
            function applySectionsToPage(cfg){
                if (!onDepartmentPage()) return;
                if (!Array.isArray(cfg)) return;
                // 1) Renomear títulos e esconder/mostrar
                cfg.forEach(sec => {
                    const brandKey = (sec.brand || '').trim().toLowerCase();
                    if (!brandKey) return;
                    const sectionEl = document.querySelector(`[data-brand-section="${brandKey}"]`);
                    if (!sectionEl) return;
                    // toggle visibility
                    sectionEl.style.display = (sec.enabled === false) ? 'none' : '';
                    // set title
                    const titleEl = sectionEl.querySelector('.js-section-title');
                    if (titleEl && (sec.title || sec.brand)) {
                        titleEl.textContent = sec.title || ('Produtos ' + sec.brand);
                    }
                });
                // 2) Reordenar DOM dentro do bloco de seções de marcas, preservando o restante da página
                const existing = Array.from(document.querySelectorAll('[data-brand-section]'));
                if (!existing.length) return;
                const parent = existing[0].parentNode;
                // Encontrar o primeiro elemento desejado
                let firstPlaced = false;
                let lastPlaced = null;
                cfg.forEach(sec => {
                    const brandKey = (sec.brand || '').trim().toLowerCase();
                    const el = document.querySelector(`[data-brand-section="${brandKey}"]`);
                    if (!el) return;
                    if (!firstPlaced) {
                        // mover para o topo do grupo de seções (antes do primeiro existente)
                        if (el !== existing[0]) parent.insertBefore(el, existing[0]);
                        lastPlaced = el;
                        firstPlaced = true;
                    } else {
                        // inserir após o último já posicionado
                        if (lastPlaced.nextSibling !== el) parent.insertBefore(el, lastPlaced.nextSibling);
                        lastPlaced = el;
                    }
                });
            }
            sectionsSave?.addEventListener('click', function(){
                const arr = readSectionsList();
                const dept = detectDepartmentSlug() || 'eletronicos';
                // Normalize to server shape
                const payload = { sections: arr.map((s, idx) => ({
                    type: s.brand ? 'brand' : (s.type || 'brand'),
                    reference: s.brand || s.reference || '',
                    reference_id: s.reference_id || null,
                    title: s.title || ('Produtos ' + (s.brand || s.reference || '')),
                    enabled: s.enabled !== false,
                    sort_order: idx,
                    metadata: s.metadata || null,
                })) };

                fetch(`/admin/departments/${encodeURIComponent(dept)}/sections`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Erro ao salvar seções');
                    // Reload sections from server
                    initSectionsPanel();
                    sectionsPanel.classList.remove('active');
                })
                .catch(err => alert(err.message));
            });
            // Aplicar configuração ao carregar a página Eletrônicos (se houver)
            const initialCfg = getCurrentSectionsConfig();
            if (initialCfg && initialCfg.length && onDepartmentPage()) {
                // Evita bloquear render, aplica logo após paint
                setTimeout(() => applySectionsToPage(initialCfg), 50);
            }
            searchTrigger.addEventListener('click', function() {
                searchPanel.classList.toggle('active');
                if (searchPanel.classList.contains('active')) {
                    setTimeout(() => searchInput && searchInput.focus(), 0);
                }
            });
            searchClose.addEventListener('click', function() { searchPanel.classList.remove('active'); });
            document.addEventListener('click', function(e) {
                if (searchPanel.classList.contains('active') && !searchPanel.contains(e.target) && !searchTrigger.contains(e.target)) {
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
                            <i class="bi bi-arrow-right-circle" style="color: var(--secondary-color); font-size:1.5rem;"></i>
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
            const imageUrl = document.getElementById('ssImageUrl');
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
                if (imageUrl) imageUrl.value = '';
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
                const url = imageUrl ? imageUrl.value.trim() : '';
                if (!id) return;
                if (file) {
                    const fd = new FormData();
                    fd.append('featured_image', file);
                    fetch(`/admin/products/${id}/update-images`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: fd
                    })
                    .then(r => r.json())
                    .then(data => updateImageInList(id, data))
                    .catch(err => { alert(err.message); });
                } else if (url) {
                    fetch(`/admin/products/${id}/update-images`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: JSON.stringify({ featured_image_url: url })
                    })
                    .then(r => r.json())
                    .then(data => updateImageInList(id, data))
                    .catch(err => { alert(err.message); });
                } else {
                    alert('Envie um arquivo ou informe uma URL de imagem.');
                }
            });
            function updateImageInList(id, data){
                if (!data.success) throw new Error(data.message || 'Erro ao atualizar imagem');
                const row = document.getElementById(`smart-result-${id}`);
                const imgEl = row ? row.querySelector('img.smart-search-item-image') : null;
                if (imgEl && Array.isArray(data.images) && data.images.length) {
                    imgEl.src = data.images[0];
                }
                closeImage();
            }
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

            // Quick Create Product modal wiring (tabbed, images, attributes, FormData submit)
            const productsTriggerBtn = document.getElementById('productsTrigger');
            const qpOverlay = document.getElementById('ssQuickProductOverlay');
            const qpClose = document.getElementById('ssQuickProductClose');
            const qpCancel = document.getElementById('ssQuickProductCancel');
            const qpSave = document.getElementById('ssQuickProductSave');

            // Tab switching
            document.querySelectorAll('.ss-tab').forEach(btn => {
                btn.addEventListener('click', function(){
                    document.querySelectorAll('.ss-tab').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const panel = btn.dataset.tab;
                    document.querySelectorAll('.qp-tab-panel').forEach(p => p.style.display = (p.dataset.panel === panel) ? '' : 'none');
                });
            });

            // Simple attribute rows
            const qpAttributesList = document.getElementById('qpAttributesList');
            const qpAddAttrBtn = document.getElementById('qpAddAttr');
            const qpAttrKey = document.getElementById('qpAttrKey');
            const qpAttrValue = document.getElementById('qpAttrValue');
            function addAttributeRow(key, value){
                if (!qpAttributesList) return;
                const wrap = document.createElement('div');
                wrap.style.display = 'flex'; wrap.style.gap = '8px'; wrap.style.alignItems = 'center';
                const k = document.createElement('input'); k.type = 'text'; k.className = 'form-control'; k.value = key || ''; k.placeholder = 'Nome';
                const v = document.createElement('input'); v.type = 'text'; v.className = 'form-control'; v.value = value || ''; v.placeholder = 'Valor';
                const rem = document.createElement('button'); rem.type = 'button'; rem.className = 'ss-btn ss-btn-secondary'; rem.textContent = 'Remover';
                rem.addEventListener('click', () => wrap.remove());
                wrap.appendChild(k); wrap.appendChild(v); wrap.appendChild(rem);
                qpAttributesList.appendChild(wrap);
            }
            qpAddAttrBtn?.addEventListener('click', function(){
                const key = (qpAttrKey?.value || '').trim();
                const val = (qpAttrValue?.value || '').trim();
                if (!key || !val) { alert('Informe nome e valor do atributo.'); return; }
                addAttributeRow(key, val);
                qpAttrKey.value = ''; qpAttrValue.value = '';
            });

            // Image preview
            const qpImagesInput = document.getElementById('qpImages');
            const qpPreview = document.getElementById('qpPreview');
            function renderImagePreviews(files){
                if (!qpPreview) return;
                qpPreview.innerHTML = '';
                Array.from(files || []).slice(0,10).forEach(f => {
                    const url = URL.createObjectURL(f);
                    const img = document.createElement('img'); img.src = url; img.style.width = '96px'; img.style.height = '96px'; img.style.objectFit = 'cover'; img.style.borderRadius = '6px';
                    qpPreview.appendChild(img);
                });
            }
            qpImagesInput?.addEventListener('change', function(){ renderImagePreviews(this.files); });

            function clearQuickForm(){
                const form = document.getElementById('qpForm');
                if (!form) return; form.reset();
                if (qpPreview) qpPreview.innerHTML = '';
                if (qpAttributesList) qpAttributesList.innerHTML = '';
                // repopulate selects
                loadQuickFormOptions();
            }

            function openQuickProduct(){ if (!qpOverlay) return; qpOverlay.style.display = 'flex'; loadQuickFormOptions(); setTimeout(()=>document.getElementById('qpName')?.focus(),60); }
            function closeQuickProduct(){ if (!qpOverlay) return; qpOverlay.style.display = 'none'; }
            productsTriggerBtn?.addEventListener('click', () => openQuickProduct());
            qpClose?.addEventListener('click', closeQuickProduct);
            qpCancel?.addEventListener('click', () => { closeQuickProduct(); });

            // Load brands and categories
            function loadQuickFormOptions(){
                // brands - always attempt a fresh fetch scoped to department (fallback to global)
                const brandEl = document.getElementById('qpBrand');
                if (brandEl) {
                    // Prefer department slug from current page path (if any), fallback to ?department= in query string
                    const detectedDept = (typeof detectDepartmentSlug === 'function') ? (detectDepartmentSlug() || '') : '';
                    const qDept = (new URLSearchParams(window.location.search)).get('department') || '';
                    const dept = detectedDept || qDept || '';
                    const url = dept ? `/admin/products/brands-list?department=${encodeURIComponent(dept)}` : '/admin/products/brands-list';
                    fetch(url, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.json())
                        .then(data => {
                            let list = [];
                            if (Array.isArray(data.brands)) list = data.brands;
                            else if (data.brands && typeof data.brands === 'object') list = Object.values(data.brands);
                            list = (list || []).map(b => (b ?? '').toString().trim()).filter(Boolean);
                            const base = '<option value="">Selecione a marca</option>';
                            brandEl.innerHTML = base + (list.length ? list.map(b => `<option value="${escapeHtml(b)}">${escapeHtml(b)}</option>`).join('') : '');
                        })
                        .catch(err => {
                            console.error('brands-list fetch failed', err);
                            brandEl.innerHTML = '<option value="">Selecione a marca</option>';
                        });
                }

                // categories
                const catsEl = document.getElementById('qpCategories');
                fetch('/admin/categories/list', { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        const list = Array.isArray(data) ? data : (data.categories || []);
                        if (!catsEl) return;
                        catsEl.innerHTML = '';
                        list.forEach(cat => {
                            const opt = document.createElement('option');
                            opt.value = cat.id || cat.value || '';
                            opt.textContent = cat.name || cat.title || String(cat.id || opt.value);
                            catsEl.appendChild(opt);
                        });
                    })
                    .catch(err => console.error('Categorias quick-create falha', err));
            }

            // Gather form data and submit (use FormData if images present)
            qpSave?.addEventListener('click', function(){
                try {
                    const name = (document.getElementById('qpName')?.value || '').trim();
                    if (!name) { alert('Informe o nome do produto.'); document.querySelector('[data-panel="general"] input#qpName')?.focus(); return; }
                    const payload = {};
                    payload.name = name;
                    payload.brand = (document.getElementById('qpBrand')?.value || '').trim() || null;
                    payload.sku = (document.getElementById('qpSku')?.value || '').trim() || null;
                    payload.is_active = document.getElementById('qpActive')?.checked ? 1 : 0;
                    payload.short_description = (document.getElementById('qpShortDesc')?.value || '').trim() || null;
                    payload.description = (document.getElementById('qpDescription')?.value || '').trim() || null;
                    payload.categories = Array.from(document.getElementById('qpCategories')?.selectedOptions || []).map(o => Number(o.value)).filter(Boolean);
                    payload.price = document.getElementById('qpPrice')?.value ? parseFloat(document.getElementById('qpPrice').value) : null;
                    payload.compare_price = document.getElementById('qpComparePrice')?.value ? parseFloat(document.getElementById('qpComparePrice').value) : null;
                    payload.cost_price = document.getElementById('qpCostPrice')?.value ? parseFloat(document.getElementById('qpCostPrice').value) : null;
                    payload.stock = document.getElementById('qpStock')?.value ? parseInt(document.getElementById('qpStock').value,10) : 0;
                    payload.min_stock = document.getElementById('qpMinStock')?.value ? parseInt(document.getElementById('qpMinStock').value,10) : 0;
                    payload.barcode = (document.getElementById('qpBarcode')?.value || '').trim() || null;
                    payload.weight = document.getElementById('qpWeight')?.value ? parseFloat(document.getElementById('qpWeight').value) : null;
                    payload.length = document.getElementById('qpLength')?.value ? parseFloat(document.getElementById('qpLength').value) : null;
                    payload.width = document.getElementById('qpWidth')?.value ? parseFloat(document.getElementById('qpWidth').value) : null;
                    payload.height = document.getElementById('qpHeight')?.value ? parseFloat(document.getElementById('qpHeight').value) : null;
                    payload.slug = (document.getElementById('qpSlug')?.value || '').trim() || null;
                    payload.seo_title = (document.getElementById('qpSeoTitle')?.value || '').trim() || null;
                    payload.seo_description = (document.getElementById('qpSeoDescription')?.value || '').trim() || null;

                    // attributes
                    const attrs = [];
                    Array.from(qpAttributesList?.children || []).forEach(row => {
                        const k = row.querySelector('input:nth-child(1)')?.value?.trim();
                        const v = row.querySelector('input:nth-child(2)')?.value?.trim();
                        if (k && v) attrs.push({ name: k, value: v });
                    });
                    if (attrs.length) payload.attributes = attrs;

                    const files = qpImagesInput?.files || [];
                    let useForm = (files && files.length > 0);

                    if (useForm) {
                        const fd = new FormData();
                        Object.keys(payload).forEach(k => {
                            const val = payload[k];
                            if (Array.isArray(val)) {
                                fd.append(k, JSON.stringify(val));
                            } else if (val !== null && val !== undefined) {
                                fd.append(k, String(val));
                            }
                        });
                        Array.from(files).slice(0,10).forEach((f, idx) => fd.append('images[]', f));
                        // send
                        fetch('/admin/products', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: fd })
                            .then(r => r.json())
                            .then(handleCreateResponse)
                            .catch(err => alert(err.message || 'Erro ao criar produto'));
                    } else {
                        // JSON submit
                        fetch('/admin/products', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify(payload) })
                            .then(r => r.json())
                            .then(handleCreateResponse)
                            .catch(err => alert(err.message || 'Erro ao criar produto'));
                    }
                } catch(ex) { alert(ex.message || 'Erro inesperado'); }
            });

            function handleCreateResponse(data){
                if (!data || !data.success) {
                    const msg = data && (data.message || (data.errors && Object.values(data.errors).flat().join('\n'))) || 'Erro ao criar produto';
                    alert(msg);
                    return;
                }
                closeQuickProduct(); clearQuickForm();
                if (data.product && data.product.id) alert('Produto criado com sucesso (ID ' + data.product.id + ').'); else alert('Produto criado com sucesso.');
                // Optionally reload available data
                fetchBrands();
            }
        });
    </script>
@endauth
