@auth('admin')
    <!-- Smart Search Flutuante (visível apenas para Admin logado) -->
    <style>
        /* Wizard step animation */
        .qp-step { transition: opacity .28s ease, transform .28s ease; }
        .qp-step[style*="display: none"] { opacity: 0; transform: translateY(8px); pointer-events: none; }
        .qp-step[style*="display: "] { opacity: 1; transform: translateY(0); }
        /* Full-form mode: show all steps stacked and reveal save button */
        .qp-full .qp-step { display: block !important; opacity: 1 !important; transform: none !important; }
        .qp-full .ss-footer .ss-btn.ss-btn-primary { display: inline-block !important; }
        .qp-full .qp-wizard-nav { display: none !important; }
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
        /* Floating circular toggle placed above the FAB */
        .fab-top-toggle { position: absolute; left: 50%; transform: translateX(-50%); bottom: calc(100% + 10px); width:48px; height:48px; border-radius:50%; border:none; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, color-mix(in srgb, var(--secondary-color), black 12%), var(--secondary-color)); color:#fff; box-shadow: 0 8px 22px rgba(2,6,23,0.32); }
        .fab-top-toggle i { font-size:18px; }
        @media (max-width:640px){ .fab-top-toggle { width:44px; height:44px; bottom: calc(100% + 8px); } }
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
        /* .smart-search-item-brand removed — brand UI deleted. Keeping comment for safety. */
        .smart-search-item-price { font-size: 13px; font-weight: 600; color: #10b981; }
        .smart-search-empty, .smart-search-loading { text-align: center; padding: 40px 20px; color: #64748b; }
    /* Quick overlays inside panel */
    /* Overlays must cover the whole viewport and center their modal content. */
    .ss-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: none; align-items: center; justify-content: center; z-index: 3000; pointer-events: auto; }
    .ss-overlay.active { display: flex; }
    /* Quick-create categories UI */
    .qp-cat-wrapper { position: relative; }
    .qp-cat-chips { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px; }
    .qp-cat-chip { background:#f1f5f9; border:1px solid rgba(15,23,42,0.06); padding:6px 8px; border-radius:999px; display:inline-flex; gap:8px; align-items:center; font-size:13px; }
    .qp-cat-chip button { border: none; background: transparent; color: #64748b; cursor: pointer; padding:0; margin:0; }
    .qp-cat-dropdown .qp-cat-row { padding:8px 10px; cursor:pointer; border-bottom:1px solid #f1f5f9; }
    .qp-cat-dropdown .qp-cat-row:hover { background:#f8fafc; }
    /* Combobox department styles */
    .qp-dept-combobox { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(15,23,42,0.08); background: #fff; box-shadow: 0 6px 18px rgba(2,6,23,0.04); font-weight:600; padding-left:46px; }
    .qp-dept-combobox:focus { outline: none; box-shadow: 0 8px 20px rgba(99,102,241,0.12); border-color: var(--secondary-color); }
    .qp-dept-list { background:#fff; border:1px solid rgba(15,23,42,0.06); border-radius:8px; max-height:280px; overflow:auto; box-shadow:0 12px 30px rgba(2,6,23,0.12); }
    .qp-dept-item { padding:10px 12px; cursor:pointer; border-bottom:1px solid rgba(15,23,42,0.03); font-weight:600; color:#0f172a; }
    .qp-dept-item:hover, .qp-dept-item.qp-dept-highlight { background: linear-gradient(90deg, rgba(99,102,241,0.06), rgba(99,102,241,0.03)); }
    .qp-dept-item small { display:block; font-weight:400; color:#64748b; font-size:12px; }
    .qp-dept-swatch { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:28px; height:28px; border-radius:8px; box-shadow:0 6px 14px rgba(2,6,23,0.08); border:1px solid rgba(255,255,255,0.4); }
    /* Department select (prominent, modern) */
    .qp-dept-select { display: inline-block; min-width: 320px; max-width: 520px; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(15,23,42,0.08); background: linear-gradient(180deg, #fff, #fbfdff); box-shadow: 0 8px 20px rgba(2,6,23,0.06); font-size: 1rem; font-weight: 600; color: #0f172a; }
    .qp-dept-select:focus { outline: none; box-shadow: 0 6px 18px rgba(99,102,241,0.12); border-color: var(--secondary-color); }
    .qp-dept-select option[disabled] { color: #94a3b8; }
    .visually-hidden { position: absolute !important; height: 1px; width: 1px; overflow: hidden; clip: rect(1px, 1px, 1px, 1px); white-space: nowrap; }
    /* Quick-create price preview */
    .qp-price-preview { background: linear-gradient(90deg,#f8fafc, #ffffff); border: 1px solid rgba(148,163,184,0.12); padding: 10px 12px; border-radius: 8px; display:flex; gap:12px; align-items:center; }
    .qp-price-row { display:flex; flex-direction:column; }
    .qp-price-label { font-size:12px; color:#475569; font-weight:600; }
    .qp-price-value { font-size:16px; font-weight:700; color:var(--success-color, #10b981); }
    .qp-price-badge { font-size:11px; color:#334155; background:#eef2ff; padding:4px 8px; border-radius:999px; border:1px solid rgba(99,102,241,0.08); }
    /* Modal: centered, scrollable body and responsive max width
       Increased default size to be much larger on desktop while remaining responsive.
    */
    .ss-modal {
        max-width: none;
        width: 96vw;
        padding: 18px 22px;
        height: 92vh;
        display: flex;
        flex-direction: column;
        border-radius: 14px;
        position: relative;
        background: var(--modal-surface, #0b1220);
        border: 1px solid var(--modal-border, rgba(255,255,255,0.04));
        backdrop-filter: blur(6px) saturate(110%);
        box-shadow: 0 12px 40px rgba(2,6,23,0.36);
        color: var(--modal-text-color, #fff);
        overflow: hidden;
    }
    .ss-modal-center { align-self: center; }
    .ss-modal .ss-header { padding: 10px 12px; font-weight: 600; background: #0f172a; color: #fff; display: flex; align-items: center; justify-content: space-between; gap:8px; }
    .ss-modal .ss-body { padding: 12px; overflow: auto; flex: 1 1 auto; max-height: none; }
    .ss-modal .ss-footer { padding: 8px 12px; display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid #e2e8f0; flex-shrink: 0; background: transparent; }
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

        /* Mobile bottom-nav variant for smart-search: convert FAB to a compact bottom bar
           and make the panel open above it. Keeps admin tools reachable while improving
           mobile ergonomics. */
        @media (max-width: 640px) {
            .smart-search-fab {
                /* center a compact pill rather than spanning full width */
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                bottom: env(safe-area-inset-bottom,12px) !important;
                display: inline-flex !important;
                flex-direction: row !important;
                justify-content: center;
                align-items: center;
                gap: 10px;
                --ss-nav-height: 64px;
                padding: 8px 12px;
                background: var(--primary-color, #0f172a); /* use dynamic primary color */
                color: var(--text-light, #fff);
                box-shadow: 0 8px 30px rgba(2,6,23,0.18);
                border-radius: 999px;
                z-index: 1200;
                max-width: calc(100% - 48px);
            }
            .smart-search-fab > button { margin: 0; }
            .smart-search-trigger { width: 56px; height: 56px; border-radius: 999px; font-size: 1.2rem; }
            .departments-trigger, .sections-trigger, .theme-trigger { display: inline-flex; width: 44px; height: 44px; border-radius: 10px; }
            /* Place the panel above the bottom-nav and reduce width to fit mobile */
            .smart-search-panel {
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                bottom: calc(var(--ss-nav-height,56px) + env(safe-area-inset-bottom,12px) + 12px) !important;
                width: min(920px, calc(100% - 32px)) !important;
                max-height: 62vh !important;
                border-radius: 12px !important;
            }
            /* Ensure overlays and other fixed panels don't get hidden behind nav */
            .theme-panel, .departments-panel, .sections-panel { bottom: calc(56px + env(safe-area-inset-bottom,12px) + 14px); left: 12px; right: 12px; }
        }

        /* Desktop: center a compact bottom-nav variant so admin can use the same UX on larger screens.
           The FAB becomes a centered pill; the panel opens above it and is constrained to a sensible max-width. */
        @media (min-width: 641px) {
            .smart-search-fab {
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                bottom: 18px !important;
                display: inline-flex !important;
                flex-direction: row !important;
                justify-content: center;
                align-items: center;
                gap: 12px;
                --ss-nav-height: 64px;
                padding: 8px 16px;
                background: var(--primary-color, #0f172a); /* use dynamic primary color */
                color: var(--text-light, #fff);
                box-shadow: 0 8px 28px rgba(2,6,23,0.12);
                border-radius: 999px;
                z-index: 1200;
                max-width: 920px;
            }
            .smart-search-fab > button { margin: 0; }
            .smart-search-trigger { width: 56px; height: 56px; border-radius: 999px; font-size: 1.2rem; }
            .departments-trigger, .sections-trigger, .theme-trigger { display: inline-flex; width: 44px; height: 44px; border-radius: 10px; }
            .smart-search-panel {
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                bottom: calc(var(--ss-nav-height,56px) + 18px + 12px) !important;
                width: min(920px, calc(100% - 96px)) !important;
                max-height: 70vh !important;
                border-radius: 12px !important;
            }
            .theme-panel, .departments-panel, .sections-panel {
                left: 50% !important;
                right: auto !important;
                transform: translateX(-50%);
                bottom: calc(56px + 18px + 14px) !important;
                width: min(980px, calc(100% - 96px));
            }
        }
    </style>

    <div id="adminFab" class="smart-search-fab admin-only {{ session('admin_view_as_user') ? 'fab-hidden' : 'fab-visible' }}">
        {{-- Toggle flutuante acima do FAB (somente admin) --}}
        <button type="button" class="fab-top-toggle admin-toggle-view-as-user" aria-pressed="{{ session('admin_view_as_user') ? 'true' : 'false' }}" title="Alternar ver como usuário" style="position:absolute; left:50%; transform:translateX(-50%); bottom: calc(100% + 10px); width:48px; height:48px; border-radius:50%; border:none; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, color-mix(in srgb, var(--secondary-color), black 12%), var(--secondary-color)); color:#fff; box-shadow: 0 8px 22px rgba(2,6,23,0.32);">
            @if(session('admin_view_as_user'))
                <i class="fas fa-eye"></i>
            @else
                <i class="fas fa-eye-slash"></i>
            @endif
        </button>
        <!-- Botão: Gerenciador curto de Produtos (sacola) - abre criação rápida -->
        <button class="departments-trigger" id="productsManagerTrigger" title="Gerar Produto Rápido">
            <i class="bi bi-bag-fill"></i>
        </button>
        <!-- Botão Gerenciar Departamentos removido para evitar erros/complexidade -->
        <!-- painel removido: id="departmentsTrigger" -->
        <!-- Botão Gerenciar Seções (acima da paleta) -->
        <button class="sections-trigger" id="sectionsTrigger" title="Sessões">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </button>
        <!-- Botão Produtos (quick-create) removido conforme solicitado -->

        <!-- Botão de Tema (pincel) -->
        <button class="theme-trigger" id="themeTrigger" title="Cores do site">
            <i class="bi bi-palette-fill"></i>
        </button>
        @auth('admin')
            <button class="departments-trigger" id="adminMobileLogoSizeBtn" title="Ajustar tamanho da logo">
                <i class="bi bi-aspect-ratio"></i>
            </button>
        @endauth
        <button class="smart-search-trigger" id="smartSearchTrigger" title="Buscar produto">
            <i class="bi bi-search"></i>
        </button>

        <div class="smart-search-panel" id="smartSearchPanel" role="dialog" aria-label="Painel de busca rápida">
            <div class="smart-search-header">
                <h3>Buscar produtos</h3>
                <button class="smart-search-close" id="smartSearchClose" aria-label="Fechar busca">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="smart-search-input-wrapper">
                <div class="smart-search-input-group">
                    <input id="smartSearchInput" class="smart-search-input" type="search" placeholder="Procurar produto..." aria-label="Pesquisar produto" />
                    <button id="smartSearchClear" class="smart-search-clear" title="Limpar" aria-label="Limpar busca" style="display:none;"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
            <div class="smart-search-results" id="smartSearchResults">
                <div class="smart-search-empty">
                    <i class="bi bi-search"></i>
                    <p>Digite algo para buscar produtos</p>
                </div>
            </div>

                <script>
                    // Ensure modal uses site's CSS variables even on browsers without color-mix
                    (function(){
                        function hexToRgb(hex){
                            if(!hex) return null;
                            hex = hex.replace('#','').trim();
                            if (hex.length === 3) hex = hex.split('').map(h=>h+h).join('');
                            if (hex.length !== 6) return null;
                            const r = parseInt(hex.substring(0,2),16);
                            const g = parseInt(hex.substring(2,4),16);
                            const b = parseInt(hex.substring(4,6),16);
                            return r+','+g+','+b;
                        }

                        function applyThemeToModal(){
                            try{
                                const overlay = document.getElementById('ssQuickProductOverlay');
                                if (!overlay) return;

                                // traverse from overlay up to root to find page-specific CSS variables
                                function findVarOnAncestors(el, varName){
                                    let node = el;
                                    while(node){
                                        try{
                                            const s = getComputedStyle(node);
                                            const v = s.getPropertyValue(varName);
                                            if (v && v.trim() !== '') return v.trim();
                                        }catch(e){ /* ignore */ }
                                        node = node.parentElement;
                                    }
                                    // fallback to body and documentElement
                                    try{ const b = getComputedStyle(document.body).getPropertyValue(varName); if (b && b.trim() !== '') return b.trim(); }catch(e){}
                                    try{ const r = getComputedStyle(document.documentElement).getPropertyValue(varName); if (r && r.trim() !== '') return r.trim(); }catch(e){}
                                    return null;
                                }

                                const primary = findVarOnAncestors(overlay, '--primary-color') || '#0b1220';
                                const primaryDark = findVarOnAncestors(overlay, '--primary-dark') || primary;
                                const secondary = findVarOnAncestors(overlay, '--secondary-color') || findVarOnAncestors(overlay, '--accent-color') || '#ff7a3a';
                                const cardBg = findVarOnAncestors(overlay, '--card-bg') || '#0b1220';

                                // helper convert and luminance
                                const primaryRgb = hexToRgb(primary.replace(/\s/g,'')) || '';
                                const secRgb = hexToRgb(secondary.replace(/\s/g,'')) || null;
                                function luminanceFromRgb(rgbStr){ if(!rgbStr) return 0; const parts = rgbStr.split(',').map(n=>parseInt(n,10)/255); for(let i=0;i<parts.length;i++){ const v = parts[i]; parts[i] = v <= 0.03928 ? v/12.92 : Math.pow((v+0.055)/1.055,2.4); } return 0.2126*parts[0] + 0.7152*parts[1] + 0.0722*parts[2]; }

                                // choose modal surface and text for page-specific theme
                                let modalSurface = '';
                                let modalText = '#ffffff';
                                let modalBorder = 'rgba(0,0,0,0.06)';
                                if(primaryRgb){
                                    modalSurface = `rgba(${primaryRgb}, 0.96)`;
                                    const lum = luminanceFromRgb(primaryRgb);
                                    modalText = lum > 0.65 ? '#071022' : '#ffffff';
                                    modalBorder = secRgb ? `rgba(${secRgb},0.22)` : `rgba(${primaryRgb},0.14)`;
                                } else if(secRgb){
                                    modalSurface = `rgba(${secRgb}, 0.12)`;
                                    modalText = '#ffffff';
                                    modalBorder = `rgba(${secRgb},0.22)`;
                                } else {
                                    modalSurface = cardBg;
                                    modalText = 'var(--text-primary, #0f172a)';
                                }

                                // expose computed vars on overlay
                                overlay.style.setProperty('--qp-primary', primary);
                                overlay.style.setProperty('--qp-dark', primaryDark);
                                overlay.style.setProperty('--qp-secondary', secondary);
                                overlay.style.setProperty('--qp-card-bg', cardBg);
                                if(primaryRgb) overlay.style.setProperty('--qp-primary-rgb', primaryRgb);
                                if(secRgb) overlay.style.setProperty('--qp-secondary-rgb', secRgb);
                                overlay.style.setProperty('--modal-surface', modalSurface);
                                overlay.style.setProperty('--modal-text-color', modalText);
                                overlay.style.setProperty('--modal-border', modalBorder);
                            }catch(e){ console.debug && console.debug('applyThemeToModal failed', e); }
                        }

                        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', applyThemeToModal); else applyThemeToModal();
                    })();
                </script>
        </div>
        
        <!-- Painel de Departamentos (ANTIGO) - comentado para substituição por versão mais simples -->
        <!--
        <div class="departments-panel" id="departmentsPanel"> ... (OLD PANEL REMOVED) ... </div>
        -->

        <!-- Painel de Departamentos removido (HTML e controls) -->
        <!-- O painel simples e seu conteúdo foram removidos para evitar erros e reduzir complexidade.
             Se necessário, restaure a partir do histórico de commits. -->

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

        <!-- Painel de Seções -->
        <div class="sections-panel" id="sectionsPanel">
            <div class="sp-header" style="display:flex; align-items:center; gap:8px; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-collection me-2"></i>
                    <strong>Sessões</strong>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <button class="sp-btn sp-btn-secondary" id="sectionsRefresh" title="Atualizar" style="padding:6px 10px;">⭮</button>
                    <button class="smart-search-close" id="sectionsClose" aria-label="Fechar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="sp-body">
                <div id="sectionsUnsupported" style="display:none; color:#64748b; font-size: 14px;">
                    Abra este painel na página do departamento para ver as sessões disponíveis.
                </div>
                <ul class="sp-list" id="sectionsList"></ul>
                <div id="sectionsEmpty" class="smart-search-empty" style="display:none; padding:12px; color:#64748b;">
                    Nenhuma sessão encontrada para este departamento.
                </div>
            </div>
            <!-- Quick-create de marca removido -->
            <!-- Confirmação de troca de seção -->
            <!-- sections confirm overlay moved below to avoid nesting -->
        </div>
    </div>

    <!-- Quick-create product overlay moved outside the search panel to avoid layout/overflow conflicts -->
    <style>
        /* Modern, colorful quick-create modal styles using site theme vars */
        #ssQuickProductOverlay {
            /* prefer site variables if present */
            --qp-primary: var(--primary-color, #06b6d4);
            --qp-dark: var(--primary-dark, #7c3aed);
            --qp-secondary: var(--secondary-color, #ff7a3a); /* used for action buttons */
            --qp-accent: var(--accent-color, #10b981);
            --qp-card-bg: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            --qp-surface: #ffffff;
            --qp-muted: #64748b;
            --qp-radius: 12px;
        }

        #ssQuickProductOverlay .ss-modal {
            /* Allow larger modal while keeping reasonable limits */
            max-width: 1200px;
            width: calc(100vw - 80px);
            height: 80vh;
            box-sizing: border-box;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-radius: var(--qp-radius);
            /* Modal surface: prefer explicit --modal-surface set by JS */
            background: var(--modal-surface, rgba(255,255,255,0.04));
            color: var(--modal-text-color, var(--text-primary, #0f172a));
            backdrop-filter: blur(10px) saturate(120%);
            -webkit-backdrop-filter: blur(10px) saturate(120%);
            box-shadow: 0 30px 80px rgba(2,6,23,0.28), 0 6px 32px rgba(2,6,23,0.12) inset;
            border: 1px solid var(--modal-border, rgba(0,0,0,0.06));
            position: relative;
        }

        /* Outer neon glow using secondary color if available */
        #ssQuickProductOverlay::before {
            content: '';
            position: absolute; inset: -6px; border-radius: calc(var(--qp-radius) + 6px);
            background: transparent; z-index: -2;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18);
        }
        #ssQuickProductOverlay::after {
            content: '';
            position: absolute; inset: -1px; border-radius: calc(var(--qp-radius) + 1px);
            z-index: -1; pointer-events: none;
            background: linear-gradient(90deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
            box-shadow: 0 6px 30px rgba(0,0,0,0.12);
        }

        /* Header uses the site's primary gradient (keeps identity) */
        #ssQuickProductOverlay .ss-header {
            flex: 0 0 auto;
            padding: 12px 16px;
            display:flex; align-items:center; justify-content:space-between;
            background: linear-gradient(90deg, var(--qp-primary), var(--qp-dark));
            color: #fff;
            border-top-left-radius: calc(var(--qp-radius) - 2px);
            border-top-right-radius: calc(var(--qp-radius) - 2px);
            gap: 12px;
        }

        /* Tabs as modern pills with icons */
        #ssQuickProductOverlay .ss-tab {
            background: transparent;
            color: rgba(255,255,255,0.95);
            border: none;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight:600;
            cursor: pointer;
            transition: all .18s ease;
            box-shadow: none;
            opacity: 0.95;
        }
        #ssQuickProductOverlay .ss-tab:hover { transform: translateY(-2px); opacity:1; }
        #ssQuickProductOverlay .ss-tab.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
            box-shadow: 0 6px 20px rgba(2,6,23,0.12);
        }

        /* Body panels */
        #ssQuickProductOverlay .qp-tab-panel { flex: 1 1 auto; display: block; overflow: hidden; }
        #ssQuickProductOverlay .qp-tab-panel > .ss-body { height: 100% !important; overflow: auto !important; box-sizing: border-box; padding: 18px; }
        #ssQuickProductOverlay .ss-body { flex: 1 1 auto; overflow: auto !important; padding-bottom: 120px; }

        /* Make form controls friendlier */
        #ssQuickProductOverlay .form-control, #ssQuickProductOverlay .form-select, #ssQuickProductOverlay textarea {
            border-radius: 10px; border: 1px solid rgba(15,23,42,0.06);
            padding: 10px 12px; box-shadow: none; background: #fff;
        }
        #ssQuickProductOverlay label.form-label { font-weight:700; color:#0f172a; }
        #ssQuickProductOverlay .text-muted { color: var(--qp-muted); }

        /* Footer positioned inside the overlay modal (absolute inside modal) */
        #ssQuickProductOverlay .ss-footer {
            position: absolute; left: 18px; right: 18px; bottom: 18px; z-index: 60;
            background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(248,251,255,0.98));
            padding: 12px 16px; display:flex; gap:8px; justify-content:flex-end; align-items:center; border-radius: 10px;
            box-shadow: 0 6px 24px rgba(2,6,23,0.18);
        }
        /* Compact footer appearance (dark / flat) */
        #ssQuickProductOverlay.qp-compact .ss-footer {
            background: rgba(6,8,12,0.86);
            color: var(--modal-text-color, #e6eef8);
            box-shadow: 0 6px 20px rgba(2,6,23,0.28);
            border-radius: 8px;
        }
        #ssQuickProductOverlay.qp-compact .ss-footer .ss-btn { box-shadow: none; }

        /* Primary button — uses site secondary/accent (keeps CTA consistent) */
        #ssQuickProductOverlay .ss-btn.ss-btn-primary {
            background: linear-gradient(90deg,var(--qp-secondary),var(--qp-accent));
            color: #ffffff; border: none; padding: 10px 16px; border-radius: 10px; font-weight:700; box-shadow: 0 10px 26px rgba(15,23,42,0.12);
            display:inline-flex; gap:8px; align-items:center;
        }
        #ssQuickProductOverlay .ss-btn.ss-btn-secondary {
            background: transparent; border: 1px solid rgba(255,255,255,0.14); color: #fff; padding:8px 12px; border-radius:8px;
        }

        /* Floating actions: circular colorful buttons */
        #ssQuickProductOverlay .qp-floating-actions { display: none; }
        #ssQuickProductOverlay.active .qp-floating-actions { display: flex; position: fixed; right: 28px; bottom: 28px; gap: 12px; z-index: 4000; }
        #ssQuickProductOverlay .qp-floating-actions .ss-btn { box-shadow: 0 12px 30px rgba(2,6,23,0.12); border-radius: 999px; padding:12px 14px; }

        /* Category chips (use secondary color) */
        .qp-cat-chips { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px; }
        .qp-cat-chips .qp-chip { padding:6px 10px; border-radius:999px; background: linear-gradient(90deg,var(--qp-secondary),var(--qp-accent)); color:#08112a; font-weight:600; box-shadow:0 8px 20px rgba(15,23,42,0.06); }

        /* Price badges */
        .qp-price-badge { display:inline-block; padding:6px 10px; border-radius:999px; background: linear-gradient(90deg,#06b6d4,#7c3aed); color:#fff; font-weight:700; font-size:0.85rem; }

        /* Small responsive tweaks */
        @media (max-width: 640px) {
            /* Make modal responsive: allow max dimensions but avoid forcing fixed height (prevents fullscreen) */
            #ssQuickProductOverlay .ss-modal { max-width: 96vw; max-height: 78vh; }
            #ssQuickProductOverlay .ss-body { padding: 12px; }
            #ssQuickProductOverlay .ss-header { padding: 10px; }
        }

        /* Subtle animations */
        @keyframes qp-fade-in-up { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }
        #ssQuickProductOverlay .ss-modal { animation: qp-fade-in-up .28s cubic-bezier(.2,.9,.2,1); }
        .ss-tab .bi { margin-right:8px; transform: translateY(0); transition: transform .18s ease; }
        .ss-tab:hover .bi { transform: translateY(-3px); }
        /* Next button micro-move */
        .qp-next-btn { transition: transform .12s ease, box-shadow .12s ease; }
        .qp-next-btn:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(15,23,42,0.09); }
        /* Compact header (icon-only) */
        .ss-header-compact .ss-tab { padding:8px; width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; background:transparent; border:1px solid transparent; transition: transform .12s ease, background .12s ease, box-shadow .12s ease; }
        .ss-header-compact .ss-tab i { font-size:18px; }
        .ss-header-compact .ss-tab.active { background: linear-gradient(90deg, rgba(var(--qp-secondary-rgb,255,122,58),0.12), rgba(var(--qp-accent-rgb,124,58,237),0.10)); box-shadow: 0 8px 28px rgba(var(--qp-secondary-rgb,255,122,58),0.12); transform: translateY(-2px); }
        .ss-btn-ghost { background:transparent; border:1px solid rgba(255,255,255,0.04); padding:8px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; height:40px; width:40px; }
        /* Performance hints: prefer transform/opacity; avoid expensive filters on animated elements */
        .ss-modal, .ss-header, .ss-body { will-change: transform, opacity; }

        /* Make the header of this overlay visually invisible while keeping buttons functional */
        #ssQuickProductOverlay .ss-header {
            background: transparent !important;
            box-shadow: none !important;
            border-bottom: none !important;
            color: var(--modal-text-color, #e6eef8) !important;
            padding: 8px 12px !important;
        }
        /* Ensure the modal top corners remain visible without a colored header bar */
        #ssQuickProductOverlay .ss-modal { overflow: visible; }
        /* Full-screen modal variant */
        #ssQuickProductOverlay .ss-modal.ss-modal-full {
            position: fixed !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: none !important;
            max-height: none !important;
            margin: 0 !important;
            border-radius: 0 !important;
            padding: 18px !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: none !important;
            background: linear-gradient(180deg, rgba(6,8,12,0.96), rgba(4,6,10,0.94));
        }
        /* Fullscreen when applied on the overlay (more robust) */
        #ssQuickProductOverlay.qp-fullscreen .ss-modal {
            position: fixed !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: none !important;
            max-height: none !important;
            margin: 0 !important;
            border-radius: 0 !important;
            padding: 18px !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: none !important;
            background: linear-gradient(180deg, rgba(6,8,12,0.96), rgba(4,6,10,0.94));
            z-index: 3050;
        }
    </style>

    <div class="ss-overlay" id="ssQuickProductOverlay" aria-modal="true" role="dialog">
        <div class="ss-modal ss-modal-center" role="document">
            <div class="ss-header ss-header-compact" style="display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; gap:8px; align-items:center;">
                    <button class="ss-tab active" data-tab="create" id="qpTabCreate" aria-label="Criar" title="Criar"><i class="bi bi-plus-circle" aria-hidden="true"></i><span class="visually-hidden">Criar</span></button>
                    <button class="ss-tab" data-tab="manage" id="pmTabManage" aria-label="Gerenciador" title="Gerenciador"><i class="bi bi-gear-fill" aria-hidden="true"></i><span class="visually-hidden">Gerenciador</span></button>
                    <button class="ss-tab" data-tab="latest" id="pmTabLatest" aria-label="Últimos" title="Últimos"><i class="bi bi-clock-history" aria-hidden="true"></i><span class="visually-hidden">Últimos</span></button>
                </div>
                <div style="display:flex; gap:8px; align-items:center;">
                    <button class="sp-btn sp-btn-ghost" id="qpToggleFullForm" title="Mostrar tudo" type="button" aria-label="Expandir"><i class="bi bi-layout-text-window-reverse" aria-hidden="true"></i></button>
                    <button class="ss-btn ss-btn-ghost" id="ssQuickProductClose" type="button" aria-label="Fechar" title="Fechar"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
                </div>
            </div>
            <div class="qp-tab-panel" data-panel="create">
            <form id="qpForm" onsubmit="return false;">
                <div class="ss-body">
                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <div style="display:flex; gap:8px; align-items:center; position:relative;">
                            <div style="position:relative; width:100%;">
                                <div class="qp-dept-swatch" id="qpDeptSwatch" title="Departamento" style="display:none;"></div>
                                <input id="qpDeptCombo" class="form-control qp-dept-combobox" type="text" placeholder="Abrir lista de departamentos..." aria-label="Selecionar departamento" autocomplete="off" role="button" readonly onclick="toggleDepartmentsDropdown(); return false;" />
                                <div id="qpDeptList" class="qp-dept-list" style="display:none; position:absolute; left:0; right:0; z-index:40;" role="listbox"></div>
                            </div>
                            <select id="qpDepartment" name="department_id" class="form-select qp-dept-select visually-hidden" aria-hidden="true" style="display:none;">
                                <option value="">— Selecione o departamento —</option>
                            </select>
                            <button type="button" id="qpDeptHelp" class="ss-btn ss-btn-secondary" title="Ajuda">?</button>
                        </div>
                        <small class="text-muted">Escolha o departamento principal deste produto. Pode ser alterado depois.</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nome do produto</label>
                        <input type="text" id="qpName" class="form-control" placeholder="Nome do produto" />
                    </div>
                    <div class="mb-2 d-flex gap-2">
                        <div style="flex:1">
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
                        <div id="qpCategoriesWrapper" class="qp-cat-wrapper">
                            <div id="qpCatChips" class="qp-cat-chips" aria-hidden="false"></div>
                            <input id="qpCatSearchInput" class="form-control" type="search" placeholder="Buscar/Adicionar categoria..." aria-label="Pesquisar categorias" autocomplete="off" />
                            <div id="qpCatDropdown" class="qp-cat-dropdown" style="display:none; max-height:200px; overflow:auto; margin-top:6px; border:1px solid #e6edf3; border-radius:8px; background:#fff; box-shadow:0 8px 18px rgba(15,23,42,0.06);"></div>
                            <select id="qpCategories" name="categories[]" class="form-select visually-hidden" multiple style="display:none; min-height:80px;"></select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Breve descrição</label>
                        <textarea id="qpShortDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Descrição completa (HTML opcional)</label>
                        <textarea id="qpDescription" class="form-control" rows="6"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Preço</label>
                        <input type="number" id="qpPrice" class="form-control" placeholder="Preço" step="0.01" min="0" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Preço de comparação</label>
                        <input type="number" id="qpComparePrice" class="form-control" placeholder="Preço de comparação" step="0.01" min="0" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Preço de custo</label>
                        <input type="number" id="qpCostPrice" class="form-control" placeholder="Preço de custo" step="0.01" min="0" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tipo de produto</label>
                        <select id="qpProductType" class="form-select">
                            <option value="physical">Físico</option>
                            <option value="service">Serviço</option>
                        </select>
                        <small class="text-muted">Escolha "Serviço" para produtos que não possuem estoque.</small>
                    </div>
                    <div class="mb-2 d-flex gap-3 align-items-center" style="align-items:center">
                        <label class="form-label" style="min-width:120px; margin-bottom:0;">Canais de venda</label>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <label style="font-weight:500; font-size:0.95rem;"><input type="checkbox" id="qpSellB2C" checked style="margin-right:6px;" /> Vender em B2C</label>
                            <label style="font-weight:500; font-size:0.95rem;"><input type="checkbox" id="qpSellB2B" checked style="margin-right:6px;" /> Vender em B2B</label>
                        </div>
                        <div style="margin-left:auto; display:flex; align-items:center; gap:8px;">
                            <label style="font-size:0.9rem; color:#475569;"><input type="checkbox" id="qpUseMargins" style="margin-right:6px;" /> Calcular preços a partir do custo</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div id="qpPricePreview" class="qp-price-preview" style="display:none;">
                            <div style="display:flex; flex-direction:column; gap:6px;">
                                <div style="display:flex; gap:8px; align-items:center;"><span class="qp-price-badge">B2B</span><div class="qp-price-row"><span class="qp-price-label">Preço B2B</span><span id="qpPriceB2bValue" class="qp-price-value">R$ 0,00</span></div></div>
                                <div style="display:flex; gap:8px; align-items:center;"><span class="qp-price-badge">B2C</span><div class="qp-price-row"><span class="qp-price-label">Preço B2C</span><span id="qpPriceB2cValue" class="qp-price-value">R$ 0,00</span></div></div>
                            </div>
                            <div style="margin-left:auto; font-size:12px; color:#64748b;">Calculado</div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Imagens</label>
                        <input type="file" id="qpImages" class="form-control" multiple accept="image/*" />
                        <div id="qpPreview" class="d-flex gap-2 mt-2"></div>
                    </div>
                    <div class="mb-2 d-flex gap-2" id="qpStockBlock">
                        <div style="flex:1">
                            <label class="form-label">Estoque</label>
                            <input type="number" id="qpStock" class="form-control" placeholder="Quantidade em estoque" min="0" />
                        </div>
                        <div style="width:160px">
                            <label class="form-label">Estoque mínimo</label>
                            <input type="number" id="qpMinStock" class="form-control" placeholder="Estoque mínimo" min="0" />
                        </div>
                    </div>
                    </div>
                    <script>
                        // Reorganize qp form into left (upload) / right (fields) layout and apply neon classes
                        (function(){
                            function domReady(fn){ if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn); else fn(); }
                            domReady(function(){
                                try{
                                    const form = document.getElementById('qpForm');
                                    const body = form && form.querySelector('.ss-body');
                                    if (!body) return;
                                    // create layout containers
                                    const layout = document.createElement('div'); layout.className = 'qp-layout';
                                    const left = document.createElement('div'); left.className = 'qp-left';
                                    const right = document.createElement('div'); right.className = 'qp-right';

                                    // find images block and stock block to move left
                                    const imgInput = document.getElementById('qpImages');
                                    const imgBlock = imgInput ? imgInput.closest('.mb-2') : null;
                                    const stockBlock = document.getElementById('qpStockBlock');

                                    // create a nicer upload card using existing input
                                    if (imgBlock) {
                                        const uploadCard = document.createElement('div'); uploadCard.className = 'qp-upload-card';
                                        const lab = document.createElement('div'); lab.className = 'qp-upload-label';
                                        lab.innerHTML = '<span class="qp-upload-plus">+</span><div>Upload Product Images</div>';
                                        uploadCard.appendChild(lab);
                                        // move original input into card (hide duplicate label)
                                        const fileInput = imgBlock.querySelector('input[type=file]');
                                        if (fileInput) {
                                            uploadCard.appendChild(fileInput);
                                        }
                                        left.appendChild(uploadCard);
                                        // move preview under upload
                                        const preview = document.getElementById('qpPreview');
                                        if (preview) left.appendChild(preview);
                                    }

                                    // move stock to left as small info
                                    if (stockBlock) left.appendChild(stockBlock);

                                    // now move remaining children into right
                                    Array.from(body.children).forEach(ch => {
                                        if (ch === imgBlock || ch === stockBlock) return; // already moved
                                        // skip footer
                                        if (ch.classList && ch.classList.contains('ss-footer')) return;
                                        right.appendChild(ch);
                                    });

                                    // clear body and append layout
                                    body.innerHTML = '';
                                    layout.appendChild(left); layout.appendChild(right);
                                    body.appendChild(layout);

                                    // apply neon classes to inputs
                                    right.querySelectorAll('input.form-control, textarea.form-control, select.form-select').forEach(i=>{ i.classList.add('neon'); });

                                    // style save/cancel buttons
                                    const save = document.getElementById('ssQuickProductSave');
                                    const cancel = document.getElementById('ssQuickProductCancel');
                                    if (save) save.classList.add('neon-primary');
                                    if (cancel) cancel.classList.add('neon-secondary');

                                    // wire clicking uploadCard to open file input if file input moved
                                    const uploadCard = left.querySelector('.qp-upload-card');
                                    if (uploadCard) uploadCard.addEventListener('click', function(){ const f = uploadCard.querySelector('input[type=file]'); if (f) f.click(); });
                                }catch(e){ console.debug && console.debug('qp layout fail', e); }
                            });
                        })();
                    </script>
                </div>
                <style>
                    /* Frosted / Neon product modal layout (full redesign)
                       - Removed stray comment rendering outside of <style>
                       - Enlarged modal, increased contrast, stronger neon + animated border
                    */
                        /* Layout */
                        #ssQuickProductOverlay .qp-layout { display:grid; grid-template-columns: minmax(300px, 420px) 1fr; gap:28px; align-items:start; }
                        #ssQuickProductOverlay .qp-left { display:flex; flex-direction:column; gap:14px; }
                        #ssQuickProductOverlay .qp-right { display:flex; flex-direction:column; gap:12px; }

                        /* Modal shell - larger, darker, frosted with animated neon border */
                        #ssQuickProductOverlay .ss-modal {
                            max-width: none;
                            width: 100vw;
                            max-width: none;
                            padding: 22px 26px;
                            height: 100vh;
                            border-radius: 14px;
                            position: relative;
                            background: linear-gradient(180deg, rgba(12,14,20,0.86), rgba(8,10,14,0.80));
                            border: 1px solid rgba(255,255,255,0.04);
                            backdrop-filter: blur(10px) saturate(120%);
                            box-shadow: 0 18px 60px rgba(2,6,23,0.45);
                            overflow: visible;
                        }
                        /* animated soft, blurred halo */
                        /* Halo disabled: remove ::after entirely for cleaner look */
                        #ssQuickProductOverlay .ss-modal::after{ display:none !important; }

                        /* Static animated border highlight using opacity pulse (lighter on GPU) */
                        #ssQuickProductOverlay .ss-modal::before{
                            content:""; position:absolute; inset:-1.5px; border-radius:15px; z-index:-1;
                            background: linear-gradient(90deg, rgba(var(--qp-secondary-rgb,255,122,58),0.32) 0%, rgba(var(--qp-accent-rgb,124,58,237),0.28) 50%, rgba(var(--qp-secondary-rgb,255,122,58),0.18) 100%);
                            filter: blur(8px); pointer-events:none; opacity:0.9; transition: opacity .3s ease;
                            animation: qpBorderPulse 5s ease-in-out infinite alternate;
                        }
                        @keyframes qpBorderPulse{0%{opacity:0.7}50%{opacity:1}100%{opacity:0.7}}

                        /* Upload card */
                        .qp-upload-card { min-height:300px; border-radius:12px; border:2px dashed rgba(var(--qp-secondary-rgb, 255,122,58),0.62); display:flex; align-items:center; justify-content:center; background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); color:var(--modal-text-color, #e6eef8); position:relative; cursor:pointer; }
                        .qp-upload-card .qp-upload-label { text-align:center; opacity:0.95; }
                        .qp-upload-card .qp-upload-plus { font-size:28px; display:block; margin-bottom:8px; color:rgba(var(--qp-secondary-rgb,255,122,58),0.98); text-shadow:0 6px 18px rgba(var(--qp-secondary-rgb,255,122,58),0.09); }
                        .qp-upload-card input[type=file] { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; }

                        /* Neon inputs */
                        #ssQuickProductOverlay .form-control.neon {
                            background: rgba(6,8,12,0.62); border:1px solid rgba(var(--qp-secondary-rgb,255,122,58),0.34); color:var(--modal-text-color, #e6eef8);
                            box-shadow: 0 10px 30px rgba(var(--qp-secondary-rgb,255,122,58),0.10) inset, 0 16px 48px rgba(2,6,23,0.36);
                            border-radius:10px; transition: box-shadow .18s ease, transform .12s ease;
                            padding:10px 12px;
                        }
                        /* Compact / flat variant for a tight modern layout */
                        #ssQuickProductOverlay.qp-compact .qp-layout { grid-template-columns: minmax(220px, 320px) 1fr; gap:16px; }
                        #ssQuickProductOverlay.qp-compact .qp-upload-card { min-height:180px; border-radius:8px; border-width:1px; background: transparent; box-shadow: none; }
                        #ssQuickProductOverlay.qp-compact .qp-upload-card .qp-upload-plus { font-size:20px; }
                        #ssQuickProductOverlay.qp-compact .ss-header, #ssQuickProductOverlay.qp-compact .ss-footer { padding:8px 10px; }
                        #ssQuickProductOverlay.qp-compact .form-control, #ssQuickProductOverlay.qp-compact .form-select, #ssQuickProductOverlay.qp-compact textarea { padding:8px 10px; border-radius:8px; }
                        #ssQuickProductOverlay.qp-compact .ss-modal { border-radius:8px; padding:12px 14px; }
                        #ssQuickProductOverlay .form-control.neon:focus { outline:none; box-shadow: 0 22px 70px rgba(var(--qp-secondary-rgb,255,122,58),0.32); transform: translateY(-2px); }
                        #ssQuickProductOverlay textarea.neon { min-height:120px; }

                        /* Labels lighter */
                        #ssQuickProductOverlay label.form-label { color: rgba(230,238,248,0.9); }

                        /* Toggle look */
                        .qp-toggle { width:48px; height:26px; border-radius:26px; background: rgba(255,255,255,0.08); position:relative; display:inline-block; vertical-align:middle; }
                        .qp-toggle .qp-knob { position:absolute; top:3px; left:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:left .12s ease, background .12s ease; }
                        .qp-toggle.on { background: linear-gradient(90deg, rgba(var(--qp-secondary-rgb,255,122,58),0.9), rgba(var(--qp-accent-rgb,124,58,237),0.9)); }
                        .qp-toggle.on .qp-knob { left:25px; background:#fff; }

                        /* Buttons */
                        .ss-btn.neon-primary { background: var(--qp-secondary, #ff7a3a); color: var(--modal-button-text, #fff); border-radius:10px; padding:12px 20px; box-shadow: 0 18px 48px rgba(var(--qp-secondary-rgb,255,122,58),0.22); border: none; transition: transform .12s ease, filter .12s ease; }
                        .ss-btn.neon-primary:hover{ transform: translateY(-2px); filter: brightness(1.06); }
                        .ss-btn.neon-secondary { background: transparent; border:1px solid rgba(255,255,255,0.06); color:var(--modal-text-color); border-radius:10px; padding:10px 16px; }

                        /* Small form grid on right — two columns */
                        .qp-right .two-col { display:grid; grid-template-columns: 1fr 180px; gap:12px; align-items:center; }
                        .qp-right .field-row { display:flex; gap:12px; align-items:center; }

                        /* Make chips glow subtly */
                        .qp-cat-chips .qp-chip { background: rgba(var(--qp-secondary-rgb,255,122,58),0.14); color:var(--modal-text-color); border:1px solid rgba(var(--qp-secondary-rgb,255,122,58),0.26); box-shadow: 0 8px 30px rgba(var(--qp-secondary-rgb,255,122,58),0.06); }
                        /* small visual polish for scrollbar inside modal body */
                        #ssQuickProductOverlay .ss-body::-webkit-scrollbar{ width:10px; }
                        #ssQuickProductOverlay .ss-body::-webkit-scrollbar-thumb{ background:linear-gradient(180deg, rgba(var(--qp-secondary-rgb,255,122,58),0.28), rgba(var(--qp-accent-rgb,124,58,237),0.26)); border-radius:8px; }
                    </style>
                    <script>
                        // Toggle full-screen modal when clicking the expand button
                        (function(){
                            document.addEventListener('DOMContentLoaded', function(){
                                try{
                                    const btn = document.getElementById('qpToggleFullForm');
                                    const overlay = document.getElementById('ssQuickProductOverlay');
                                    const modal = overlay && overlay.querySelector('.ss-modal');
                                    if(!btn || !overlay) return;
                                    btn.addEventListener('click', function(e){
                                        e.preventDefault();
                                        const isFull = overlay.classList.toggle('qp-fullscreen');
                                        document.body.style.overflow = isFull ? 'hidden' : '';
                                        btn.title = isFull ? 'Sair do modo tela cheia' : 'Mostrar tudo';
                                    });
                                    // Exit fullscreen on ESC
                                    document.addEventListener('keydown', function(ev){
                                        if(ev.key === 'Escape' && overlay.classList.contains('qp-fullscreen')){
                                            overlay.classList.remove('qp-fullscreen');
                                            document.body.style.overflow = '';
                                            btn.title = 'Mostrar tudo';
                                        }
                                    });
                                }catch(err){ console.debug && console.debug('qp full toggle err', err); }
                            });
                        })();
                    </script>
            </div>
        </div>
        <!-- Floating actions (duplicate of modal footer) kept visible while overlay is active -->
        <div class="qp-floating-actions" id="qpFloatingActions" style="display:none;">
            <button class="ss-btn ss-btn-secondary" id="qpFloatingCancel" type="button">Cancelar</button>
            <button class="ss-btn ss-btn-primary" id="qpFloatingSave" type="button">Salvar Produto</button>
        </div>
    </div>

    <!-- Sections confirm overlay (moved out of panel) -->
    <div class="ss-overlay" id="spConfirmOverlay" aria-modal="true" role="dialog" style="display:none; align-items:center; justify-content:center; z-index:1500;">
        <div class="ss-modal" style="max-width:520px;">
            <div class="ss-header">
                <span>Substituir sessão</span>
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
                        Também desativar todos os produtos da sessão anterior neste departamento (exige confirmação abaixo).
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple toast system to replace blocking alert() calls
            (function initToasts(){
                const container = document.createElement('div');
                container.id = 'ssToastContainer';
                container.style.position = 'fixed';
                container.style.right = '20px';
                container.style.top = '20px';
                container.style.zIndex = '5000';
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.gap = '8px';
                document.body.appendChild(container);

                window.ssShowToast = function(message, type = 'info', timeout = 4500){
                    try {
                        const t = document.createElement('div');
                        t.className = 'ss-toast ss-toast-' + (type || 'info');
                        t.style.minWidth = '220px';
                        t.style.maxWidth = '420px';
                        t.style.padding = '10px 14px';
                        t.style.borderRadius = '10px';
                        t.style.boxShadow = '0 8px 20px rgba(0,0,0,0.2)';
                        t.style.color = '#fff';
                        t.style.fontWeight = '600';
                        t.style.fontSize = '13px';
                        t.style.opacity = '0';
                        t.style.transition = 'opacity .2s ease, transform .25s ease';
                        if (type === 'error') t.style.background = '#ef4444';
                        else if (type === 'success') t.style.background = '#10b981';
                        else if (type === 'warning') t.style.background = '#f59e0b';
                        else t.style.background = '#111827';
                        t.textContent = message;
                        container.appendChild(t);
                        // entrance
                        requestAnimationFrame(() => { t.style.opacity = '1'; t.style.transform = 'translateY(0)'; });
                        const hide = () => {
                            t.style.opacity = '0';
                            setTimeout(() => { try { t.remove(); } catch(e){} }, 220);
                        };
                        if (timeout && timeout > 0) setTimeout(hide, timeout);
                        // click to dismiss
                        t.addEventListener('click', hide);
                        return t;
                    } catch(e){ console.error('toast error', e); }
                };
            })();
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
            const productsTriggerBtn = document.getElementById('productsTrigger');
            const qpOverlay = document.getElementById('ssQuickProductOverlay');
            const productsManagerTrigger = document.getElementById('productsManagerTrigger');
            const qpNameField = document.getElementById('qpName');
            const qpSkuField = document.getElementById('qpSku');
            // Categories data and selection for quick-create
            let qpCategoriesData = [];
            let qpSelectedCategories = new Set();
            // Ensure overlay is attached to document.body to avoid clipping/overflow/transform containment issues
            try {
                if (qpOverlay) {
                    // If overlay is not direct child of body, move it to body to ensure fixed positioning works
                    if (qpOverlay.parentElement && qpOverlay.parentElement !== document.body) {
                        document.body.appendChild(qpOverlay);
                    }
                    // Force essential inline styles so CSS overriding is less likely to hide it accidentally
                    qpOverlay.style.position = qpOverlay.style.position || 'fixed';
                    qpOverlay.style.inset = qpOverlay.style.inset || '0';
                    qpOverlay.style.left = qpOverlay.style.left || '0';
                    qpOverlay.style.top = qpOverlay.style.top || '0';
                    qpOverlay.style.display = qpOverlay.style.display || 'none';
                    qpOverlay.style.alignItems = qpOverlay.style.alignItems || 'center';
                    qpOverlay.style.justifyContent = qpOverlay.style.justifyContent || 'center';
                    qpOverlay.style.zIndex = qpOverlay.style.zIndex || '3000';
                    qpOverlay.style.pointerEvents = qpOverlay.style.pointerEvents || 'auto';
                    const modalEl = qpOverlay.querySelector('.ss-modal');
                    if (modalEl) {
                        // Remove JS-imposed maxWidth so CSS/fullscreen class controls sizing
                        modalEl.style.maxWidth = '';
                        modalEl.style.margin = modalEl.style.margin || '0 auto';
                        // Add fullscreen class by default so modal uses fullscreen layout when opened
                        if (qpOverlay && !qpOverlay.classList.contains('qp-fullscreen')) {
                            qpOverlay.classList.add('qp-fullscreen');
                        }
                        // apply compact flat variant by default
                        if (qpOverlay && !qpOverlay.classList.contains('qp-compact')) {
                            qpOverlay.classList.add('qp-compact');
                        }
                        // Ensure there is a visible footer inside the modal — create if missing
                        try {
                            let footer = modalEl.querySelector('.ss-footer');
                            if (!footer) {
                                footer = document.createElement('div');
                                footer.className = 'ss-footer';
                                footer.innerHTML = '<button class="ss-btn ss-btn-secondary" id="ssQuickProductCancel" type="button">Cancelar</button> <button class="ss-btn ss-btn-danger" id="ssQuickProductRemove" type="button">Remover</button> <button class="ss-btn ss-btn-primary" id="ssQuickProductSave" type="button">Salvar Produto</button>';
                                modalEl.appendChild(footer);
                            } else {
                                // ensure footer buttons have expected IDs
                                if (!footer.querySelector('#ssQuickProductSave')) footer.insertAdjacentHTML('beforeend',' <button class="ss-btn ss-btn-primary" id="ssQuickProductSave" type="button">Salvar Produto</button>');
                                if (!footer.querySelector('#ssQuickProductCancel')) footer.insertAdjacentHTML('afterbegin','<button class="ss-btn ss-btn-secondary" id="ssQuickProductCancel" type="button">Cancelar</button>');
                            }
                            // also ensure floating actions mirror footer and are hidden
                            const floating = document.getElementById('qpFloatingActions');
                            if (floating) floating.style.display = 'none';
                        } catch(e) { console.debug && console.debug('ensure footer failed', e); }
                    }
                }
            } catch(e) { console.debug && console.debug('overlay placement guard failed', e); }

            // Ensure products quick-create shortcut is visible
            try { if (productsTriggerBtn) productsTriggerBtn.style.display = ''; } catch(e) {}
            // Bag shortcut should open quick-create as well
            try {
                productsManagerTrigger?.addEventListener('click', function(e){
                    e && e.stopPropagation();
                    if (typeof openQuickProduct === 'function') openQuickProduct(); else if (qpOverlay) { qpOverlay.style.display = 'flex'; qpOverlay.classList && qpOverlay.classList.add('active'); }
                    // Abrir diretamente na aba de criação para fluxo rápido
                    setTimeout(function(){ const btn = document.querySelector('.ss-tab[data-tab="create"]'); if (btn) btn.click(); try { qpNameField && qpNameField.focus(); } catch(e){} }, 60);
                });
            } catch(e) {}

            // Product Manager removed — use quick-create only
            // Quick-product modal controls
            const qpClose = document.getElementById('ssQuickProductClose');
            const qpCancel = document.getElementById('ssQuickProductCancel');
            const qpSave = document.getElementById('ssQuickProductSave');
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
                    refs.picker?.addEventListener('input', () => setThemeValue(key, refs.picker.value, 'picker'));
                }
                if (refs.hex) {
                    refs.hex?.addEventListener('input', () => refs.hex.classList.remove('is-invalid'));
                    refs.hex?.addEventListener('blur', () => setThemeValue(key, refs.hex.value, 'hex'));
                    refs.hex?.addEventListener('keydown', (ev) => {
                        if (ev.key === 'Enter') {
                            ev.preventDefault();
                            setThemeValue(key, refs.hex.value, 'hex');
                        }
                    });
                }
            });

            // === Quick-create form wizard (step-by-step) ===
            (function initQuickProductWizard(){
                try {
                    const form = document.getElementById('qpForm');
                    if (!form) return;
                    const footer = form.querySelector('.ss-footer');
                    const saveBtn = document.getElementById('ssQuickProductSave');
                    const cancelBtn = document.getElementById('ssQuickProductCancel');

                    // Define steps by selectors (order matters)
                    const steps = [
                        ['#qpDeptCombo','#qpDeptSwatch','#qpDepartment','#qpDeptHelp','#qpName','#qpSku','#qpActive'],
                        ['#qpCategoriesWrapper','#qpCatSearchInput','#qpShortDesc','#qpDescription'],
                        ['#qpPrice','#qpComparePrice','#qpCostPrice','#qpProductType','#qpSellB2C','#qpSellB2B','#qpUseMargins','#qpPricePreview'],
                        ['#qpImages','#qpPreview','#qpStock','#qpMinStock']
                    ];

                    // Create step containers
                    const body = form.querySelector('.ss-body');
                    if (!body) return;
                    // Move existing children into step wrappers based on selector matching
                    const stepEls = [];
                    steps.forEach((selList, idx) => {
                        const stepWrap = document.createElement('div');
                        stepWrap.className = 'qp-step';
                        stepWrap.dataset.step = idx+1;
                        stepWrap.style.display = idx === 0 ? '' : 'none';
                        stepWrap.style.minHeight = '180px';
                        stepWrap.style.paddingBottom = '8px';
                        stepEls.push(stepWrap);
                    });

                    // Helper to find parent node of element and move it
                    const moveElementToStep = (el, stepWrap) => {
                        if (!el) return;
                        // find nearest ancestor that is direct child of .ss-body (e.g., .mb-2 or similar)
                        let candidate = el;
                        while (candidate && candidate.parentElement && candidate.parentElement !== body) {
                            candidate = candidate.parentElement;
                        }
                        if (candidate && candidate.parentElement === body) {
                            stepWrap.appendChild(candidate);
                        } else {
                            // fallback: append the element itself
                            stepWrap.appendChild(el.cloneNode(true));
                        }
                    };

                    // For each selector in steps, try to find element and move it
                    steps.forEach((selList, idx) => {
                        selList.forEach(sel => {
                            try {
                                const el = document.querySelector(sel);
                                if (el) moveElementToStep(el, stepEls[idx]);
                            } catch(e){}
                        });
                    });

                    // If some children of body are not yet moved (extras), put them in last step
                    const remaining = Array.from(body.children).filter(ch => !stepEls.includes(ch));
                    // Clear body
                    body.innerHTML = '';
                    // Append step wrappers that have content; if empty, append original remaining into first step
                    stepEls.forEach((wrap, idx) => {
                        if (wrap.children.length > 0) {
                            body.appendChild(wrap);
                        }
                    });
                    if (body.children.length === 0) {
                        // fallback: restore original children
                        remaining.forEach(r => body.appendChild(r));
                    } else {
                        // append any leftover controls into last step
                        const last = body.querySelector('.qp-step:last-child');
                        remaining.forEach(r => last.appendChild(r));
                    }

                    // Create navigation controls
                    const nav = document.createElement('div');
                    nav.className = 'qp-wizard-nav';
                    nav.style.display = 'flex';
                    nav.style.gap = '8px';
                    nav.style.alignItems = 'center';
                    nav.style.marginLeft = 'auto';

                    const backBtn = document.createElement('button');
                    backBtn.type = 'button';
                    backBtn.className = 'ss-btn ss-btn-secondary';
                    backBtn.id = 'qpWizardBack';
                    backBtn.textContent = 'Voltar';
                    backBtn.style.display = 'none';

                    const nextBtn = document.createElement('button');
                    nextBtn.type = 'button';
                    nextBtn.className = 'ss-btn ss-btn-primary';
                    nextBtn.id = 'qpWizardNext';
                    nextBtn.textContent = 'Próximo';

                    // Ensure Save button is only visible on last step
                    if (saveBtn) saveBtn.style.display = 'none';

                    nav.appendChild(backBtn);
                    nav.appendChild(nextBtn);

                    // Insert nav into footer before Cancel/Save
                    if (footer) {
                        // hide original cancel/save temporarily and append nav
                        footer.insertBefore(nav, footer.lastElementChild);
                    }

                    let currentStep = 1;
                    const totalSteps = body.querySelectorAll('.qp-step').length || 1;

                    const showStep = (n) => {
                        const all = body.querySelectorAll('.qp-step');
                        all.forEach((s, i) => s.style.display = (i === n-1) ? '' : 'none');
                        currentStep = n;
                        backBtn.style.display = (n > 1) ? '' : 'none';
                        nextBtn.style.display = (n < totalSteps) ? '' : 'none';
                        if (saveBtn) saveBtn.style.display = (n === totalSteps) ? '' : 'none';
                        // focus first input in step
                        try { const first = body.querySelector('.qp-step[data-step="'+n+'"] input, .qp-step[data-step="'+n+'"] textarea, .qp-step[data-step="'+n+'"] select'); if (first) first.focus(); } catch(e){}
                    };

                    // Validation per step before moving next
                    const validators = [];
                    // Step 1: department (or name) required
                    validators[1] = function(){
                        const name = document.getElementById('qpName');
                        const dept = document.getElementById('qpDepartment');
                        if (!name || !name.value || name.value.trim().length < 2) {
                            name && name.classList.add('is-invalid');
                            window.ssShowToast('Preencha o nome do produto (mínimo 2 caracteres).', 'warning');
                            return false;
                        }
                        if (dept && dept.value === '') {
                            window.ssShowToast('Selecione um departamento.', 'warning');
                            return false;
                        }
                        // clean
                        name && name.classList.remove('is-invalid');
                        return true;
                    };
                    // Step 2: either short description or categories
                    validators[2] = function(){
                        const catSel = document.getElementById('qpCategories');
                        const shortDesc = document.getElementById('qpShortDesc');
                        const haveCat = catSel && catSel.options && catSel.options.length > 0 && Array.from(catSel.options).some(o=>o.selected);
                        if ((!shortDesc || shortDesc.value.trim().length === 0) && !haveCat) {
                            window.ssShowToast('Adicione uma breve descrição ou selecione ao menos uma categoria.', 'warning');
                            return false;
                        }
                        return true;
                    };
                    // Step 3: price required for physical products
                    validators[3] = function(){
                        const price = document.getElementById('qpPrice');
                        const type = document.getElementById('qpProductType');
                        if (type && type.value === 'physical') {
                            if (!price || price.value === '' || Number(price.value) <= 0) {
                                window.ssShowToast('Informe um preço válido para produtos físicos.', 'warning');
                                price && price.classList.add('is-invalid');
                                return false;
                            }
                        }
                        price && price.classList.remove('is-invalid');
                        return true;
                    };

                    backBtn.addEventListener('click', () => { if (currentStep > 1) showStep(currentStep-1); });
                    nextBtn.addEventListener('click', () => {
                        // run validation for current step
                        const valid = (typeof validators[currentStep] === 'function') ? validators[currentStep]() : true;
                        if (!valid) return;
                        if (currentStep < totalSteps) showStep(currentStep+1);
                    });

                    // Initialize
                    showStep(1);

                    // When modal opens, ensure it starts at first step and reacts to 'Mostrar tudo'
                    const observer = new MutationObserver(function(m){
                        if (qpOverlay && qpOverlay.style.display !== 'none' && qpOverlay.classList.contains('active')) {
                            showStep(1);
                            // reset full-form
                            if (qpOverlay.classList.contains('qp-full-mode')) {
                                qpOverlay.classList.remove('qp-full-mode');
                            }
                        }
                    });
                    observer.observe(qpOverlay || document.body, { attributes: true, attributeFilter: ['style', 'class'] });

                    // Toggle full-form (mostrar tudo)
                    const qpToggleFull = document.getElementById('qpToggleFullForm');
                    qpToggleFull && qpToggleFull.addEventListener('click', function(){
                        const modal = qpOverlay && qpOverlay.querySelector('.ss-modal');
                        if (!modal) return;
                        // toggle a class on modal to show all steps stacked
                        if (modal.classList.contains('qp-full')) {
                            modal.classList.remove('qp-full');
                            // ensure wizard header nav visible
                        } else {
                            modal.classList.add('qp-full');
                        }
                        // toggle save/nav visibility
                        const saveBtn = document.getElementById('ssQuickProductSave');
                        const navEl = modal.querySelector('.qp-wizard-nav');
                        if (modal.classList.contains('qp-full')) {
                            if (saveBtn) saveBtn.style.display = '';
                            if (navEl) navEl.style.display = 'none';
                        } else {
                            if (saveBtn) saveBtn.style.display = 'none';
                            if (navEl) navEl.style.display = '';
                        }
                    });

                } catch(e){ console.debug && console.debug('qp wizard init failed', e); }
            })();

            presetButtons.forEach(btn => {
                btn?.addEventListener('click', () => {
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

            let availableBrands = [];
            let departmentsLoaded = false;
            let departmentsData = [];
            let dpPendingToggle = null;
            // For quick-create combobox
            let departmentsForQuick = [];
            let qpDeptHighlighted = -1;
            let qpDeptWired = false;

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


            function generateSkuFromName(name){
                const base = (slugify(name || '') || '').toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0,8) || 'PRD';
                const rnd = Math.random().toString(36).slice(2,6).toUpperCase();
                return base + '-' + rnd;
            }

            // Pricing helpers: defaults mirror server defaults (can be adjusted later)
            const DEFAULT_MARGIN_B2B = 10.0;
            const DEFAULT_MARGIN_B2C = 20.0;

            function computePricesFromCost(cost, marginB2b = DEFAULT_MARGIN_B2B, marginB2c = DEFAULT_MARGIN_B2C){
                const c = parseFloat(cost) || 0;
                const b2b = +(Math.round((c * (1 + (parseFloat(marginB2b) || 0)/100)) * 100) / 100).toFixed(2);
                const b2c = +(Math.round((c * (1 + (parseFloat(marginB2c) || 0)/100)) * 100) / 100).toFixed(2);
                return { b2b, b2c };
            }

            // Wire quick-create cost -> computed prices when requested
            const qpCostInput = document.getElementById('qpCostPrice');
            const qpUseMarginsCheckbox = document.getElementById('qpUseMargins');
            const qpSellB2BCheckbox = document.getElementById('qpSellB2B');
            const qpSellB2CCheckbox = document.getElementById('qpSellB2C');
            function maybeApplyMarginsPreview(){
                try {
                    if (!qpUseMarginsCheckbox || !qpCostInput) return;
                    const use = qpUseMarginsCheckbox.checked;
                    const costVal = parseFloat(qpCostInput.value || 0);
                    if (!use || !costVal) return;
                    const prices = computePricesFromCost(costVal, DEFAULT_MARGIN_B2B, DEFAULT_MARGIN_B2C);
                    // Show preview in the price input if empty or if user hasn't set manual price
                    const priceEl = document.getElementById('qpPrice');
                    if (priceEl && (!priceEl.dataset.manual || priceEl.dataset.manual !== 'true')) {
                        priceEl.value = prices.b2c;
                    }
                    // update pretty preview UI
                    updatePricePreview(prices.b2b, prices.b2c);
                } catch(e) { console.debug && console.debug('maybeApplyMarginsPreview failed', e); }
            }

            function formatCurrencyBRL(val){
                try {
                    const n = Number(val) || 0;
                    return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                } catch(e){ return 'R$ 0,00'; }
            }

            function updatePricePreview(b2b, b2c){
                const preview = document.getElementById('qpPricePreview');
                const b2bEl = document.getElementById('qpPriceB2bValue');
                const b2cEl = document.getElementById('qpPriceB2cValue');
                if (!preview || !b2bEl || !b2cEl) return;
                b2bEl.textContent = formatCurrencyBRL(b2b);
                b2cEl.textContent = formatCurrencyBRL(b2c);
                preview.style.display = 'flex';
            }

            // hide preview when margins disabled or cost empty
            function hidePricePreview(){
                const preview = document.getElementById('qpPricePreview');
                if (preview) preview.style.display = 'none';
            }

            qpCostInput?.addEventListener('blur', function(){
                // if useMargins is active, update preview or hide
                if (qpUseMarginsCheckbox?.checked && (this.value || '').trim()) {
                    maybeApplyMarginsPreview();
                } else {
                    hidePricePreview();
                }
            });
            qpUseMarginsCheckbox?.addEventListener('change', function(){
                if (this.checked && (qpCostInput?.value || '').trim()) {
                    maybeApplyMarginsPreview();
                } else {
                    hidePricePreview();
                }
            });
            qpCostInput?.addEventListener('input', maybeApplyMarginsPreview);
            qpUseMarginsCheckbox?.addEventListener('change', maybeApplyMarginsPreview);

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
                themeTrigger?.addEventListener('click', function(){
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
                departmentsTrigger?.addEventListener('click', function(){
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
                return fetch(`/admin/departments/inline-snapshot`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Não foi possível carregar os departamentos.');
                        departmentsData = Array.isArray(data.departments) ? data.departments : [];
                        renderDepartmentsList(departmentsData);
                        departmentsLoaded = true;
                    })
                    .catch(err => {
                        console.error('Departamentos:', err);
                        if (dpLoading) {
                            dpLoading.textContent = 'Erro ao carregar departamentos.';
                            dpLoading.style.display = 'none';
                        }
                        if (dpEmpty) dpEmpty.style.display = 'block';
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

            // ----- Simple Departments Panel (new, minimal) -----
            function initSimpleDepartmentsPanel(){
                const panel = document.getElementById('departmentsPanelSimple');
                if (!panel) return Promise.resolve();
                const loading = document.getElementById('dpSimpleLoading');
                const empty = document.getElementById('dpSimpleEmpty');
                const list = document.getElementById('departmentsSimpleList');
                if (loading) { loading.style.display = 'block'; loading.textContent = 'Carregando departamentos...'; }
                if (empty) empty.style.display = 'none';
                if (list) list.innerHTML = '';

                return fetch('/admin/departments/inline-snapshot', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(data => {
                        const items = Array.isArray(data.departments) ? data.departments : [];
                        renderSimpleDepartmentsList(items);
                        return items;
                    })
                    .catch(err => {
                        console.error('Simple Departments:', err);
                        if (loading) { loading.textContent = 'Erro ao carregar departamentos.'; loading.style.display = 'none'; }
                        if (empty) empty.style.display = 'block';
                    })
                    .finally(() => { if (loading) setTimeout(() => { loading.style.display = 'none'; }, 200); });
            }

            function renderSimpleDepartmentsList(items){
                const list = document.getElementById('departmentsSimpleList');
                if (!list) return;
                list.innerHTML = '';
                const arr = Array.isArray(items) ? items : [];
                if (!arr.length) {
                    const empty = document.getElementById('dpSimpleEmpty'); if (empty) empty.style.display = 'block';
                    return;
                }
                arr.forEach(d => {
                    const li = document.createElement('li');
                    li.style.display = 'flex'; li.style.alignItems = 'center'; li.style.justifyContent = 'space-between'; li.style.padding = '8px'; li.style.borderBottom = '1px solid #eef2f6';
                    const left = document.createElement('div'); left.style.display = 'flex'; left.style.alignItems = 'center'; left.style.gap = '10px';
                    const sw = document.createElement('div'); sw.style.width='28px'; sw.style.height='28px'; sw.style.borderRadius='6px'; sw.style.background = d.color || '#667eea'; sw.style.flex='0 0 28px';
                    const name = document.createElement('div'); name.style.fontWeight='600'; name.textContent = d.name || '(sem nome)';
                    left.appendChild(sw); left.appendChild(name);
                    const right = document.createElement('div'); right.style.display='flex'; right.style.gap='8px'; right.style.alignItems='center';
                    const count = document.createElement('span'); count.style.fontSize='12px'; count.style.color='#64748b'; count.textContent = (d.products_count || 0) + ' prod.';
                    const view = document.createElement('a'); view.href = d.id ? (`/admin/departments/${d.id}/edit`) : '#'; view.className='sp-btn sp-btn-secondary'; view.textContent='Editar'; view.style.padding='6px 8px';
                    right.appendChild(count); right.appendChild(view);
                    li.appendChild(left); li.appendChild(right);
                    list.appendChild(li);
                });
            }

            // Bind simple panel toggle to the existing trigger if present
            (function wireSimpleDepartmentsPanel(){
                console.debug && console.debug('wireSimpleDepartmentsPanel: init');
                const trigger = document.getElementById('departmentsTrigger');
                const simplePanel = document.getElementById('departmentsPanelSimple');
                const simpleClose = document.getElementById('departmentsSimpleClose');
                const addBtn = document.getElementById('dpSimpleAdd');
                const saveBtn = document.getElementById('departmentsSimpleSave');
                const msgEl = document.getElementById('dpSimpleMsg');
                if (!trigger || !simplePanel) return;
                trigger.addEventListener('click', function(e){
                    console.debug && console.debug('wireSimpleDepartmentsPanel: trigger clicked');
                    e && e.stopPropagation();
                    const willOpen = simplePanel.style.display === 'none' || !simplePanel.classList.contains('active');
                    if (willOpen) {
                        simplePanel.style.display = 'flex'; simplePanel.classList.add('active'); initSimpleDepartmentsPanel();
                    } else {
                        simplePanel.style.display = 'none'; simplePanel.classList.remove('active');
                    }
                });
                if (simpleClose) simpleClose.addEventListener('click', () => { simplePanel.style.display = 'none'; simplePanel.classList.remove('active'); });

                if (addBtn) addBtn.addEventListener('click', function(){
                    const name = (document.getElementById('dpSimpleNewName')?.value || '').trim();
                    if (!name) { if (msgEl) msgEl.textContent = 'Informe o nome do departamento.'; return; }
                    const slug = (document.getElementById('dpSimpleNewSlug')?.value || '').trim() || slugify(name);
                    const color = (document.getElementById('dpSimpleNewColor')?.value || '#667eea').trim();
                    const description = (document.getElementById('dpSimpleNewDescription')?.value || '').trim();
                    const list = document.getElementById('departmentsSimpleList');
                    // append preview-only item
                    const item = { id: null, name, slug, color, description, is_active: true, products_count: 0 };
                    // add to top of list
                    const existing = window._simpleDepartmentsCache = window._simpleDepartmentsCache || [];
                    existing.push(item);
                    renderSimpleDepartmentsList(existing.concat());
                    // clear inputs
                    document.getElementById('dpSimpleNewName').value = '';
                    document.getElementById('dpSimpleNewSlug').value = '';
                    document.getElementById('dpSimpleNewDescription').value = '';
                    if (msgEl) { msgEl.textContent = 'Departamento adicionado (temporário). Clique em Salvar.'; }
                });

                if (saveBtn) saveBtn.addEventListener('click', function(){
                    const items = [];
                    // prefer cache if populated
                    const cached = window._simpleDepartmentsCache || [];
                    if (cached.length) {
                        cached.forEach((d, idx) => {
                            items.push({ id: d.id, name: d.name, slug: d.slug, icon: d.icon || null, color: d.color, description: d.description || null, is_active: !!d.is_active, sort_order: idx });
                        });
                    } else {
                        // no cache: read list items from DOM (fallback)
                        const list = document.getElementById('departmentsSimpleList');
                        if (!list) return;
                        Array.from(list.children).forEach((li, idx) => {
                            const nameEl = li.querySelector('div > div:nth-child(2)');
                            const name = nameEl ? nameEl.textContent.trim() : 'Departamento';
                            items.push({ id: null, name, slug: slugify(name), icon: null, color: '#667eea', description: null, is_active: true, sort_order: idx });
                        });
                    }
                    if (!items.length) { if (msgEl) msgEl.textContent = 'Nada para salvar.'; return; }
                    msgEl && (msgEl.textContent = 'Salvando...');
                    fetch('/admin/departments/inline-sync', { method: 'PUT', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ departments: items }) })
                        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                        .then(data => {
                            if (!data.success) throw new Error(data.message || 'Erro ao salvar');
                            window._simpleDepartmentsCache = (Array.isArray(data.departments) ? data.departments : []);
                            renderSimpleDepartmentsList(window._simpleDepartmentsCache);
                            msgEl && (msgEl.textContent = 'Salvo com sucesso!');
                            setTimeout(() => { document.getElementById('departmentsPanelSimple').style.display = 'none'; }, 800);
                        })
                        .catch(err => { console.error('Save departments', err); msgEl && (msgEl.textContent = 'Erro ao salvar: ' + (err.message || '')); });
                });
            })();

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
                dpConfirmOverlay.style.display = 'flex';
                dpConfirmOverlay.classList.add('active');
            }

            function closeDpConfirm(cancelled){
                if (dpConfirmOverlay) {
                    dpConfirmOverlay.classList.remove('active');
                    dpConfirmOverlay.style.display = 'none';
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
                        credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ departments: cleaned })
                    })
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
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
                // 1) global var set by other scripts
                try {
                    if (window.CurrentDepartmentSlug) return String(window.CurrentDepartmentSlug);
                } catch(e){}

                // 2) common data attribute on body or main element
                try {
                    const el1 = document.querySelector('[data-department-slug]');
                    if (el1) return String(el1.getAttribute('data-department-slug')) || null;
                    const el2 = document.body && document.body.getAttribute && (document.body.getAttribute('data-department') || document.body.getAttribute('data-department-slug'));
                    if (el2) return String(el2);
                } catch(e){}

                // 3) meta tag (some templates may inject)
                try {
                    const meta = document.querySelector('meta[name="department-slug"]') || document.querySelector('meta[property="department:slug"]');
                    if (meta && meta.content) return String(meta.content);
                } catch(e){}

                // 4) quick-create select / visible inputs used by the theme
                try {
                    const sel = document.getElementById('qpDepartment') || document.getElementById('qpDeptCombo');
                    if (sel) {
                        const val = sel.value || sel.getAttribute('data-value') || sel.getAttribute('data-slug');
                        if (val) return String(val);
                    }
                } catch(e){}

                // 5) attempt to extract from URL (several common patterns)
                try {
                    const path = window.location.pathname || '';
                    const patterns = [/\/departamento\/([^\/\?#]+)/i, /\/departamentos\/([^\/\?#]+)/i, /\/department\/([^\/\?#]+)/i];
                    for (const p of patterns) {
                        const m = path.match(p);
                        if (m && m[1]) return decodeURIComponent(m[1]);
                    }
                } catch(e){}

                return null;
            }
            function onDepartmentPage(){ return !!detectDepartmentSlug(); }
            function getCurrentSectionsConfig(){
                // Fonte principal: variável global definida na página Eletrônicos
                const raw = window.DepartmentSectionsConfig || [];
                if (Array.isArray(raw)) return raw;
                try { const parsed = JSON.parse(raw); return Array.isArray(parsed) ? parsed : []; } catch(e){ return []; }
            }
            function fetchBrands(){
                const rawDept = detectDepartmentSlug() || 'eletronicos';
                const dept = rawDept;
                const targetUrl = `/admin/products/brands-list?department=${encodeURIComponent(dept)}`;

                // Debug: log attempts so we can see what the client requested
                console.debug && console.debug('fetchBrands: trying', targetUrl);

                // Try to fetch brands scoped to the department; if empty, try fallbacks
                return fetch(targetUrl, { headers: { 'Accept': 'application/json' }})
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        console.debug && console.debug('fetchBrands response for', dept, data);
                        let brandsPayload = [];
                        if (Array.isArray(data.brands)) {
                            brandsPayload = data.brands;
                        } else if (data.brands && typeof data.brands === 'object') {
                            brandsPayload = Object.values(data.brands);
                        }

                        availableBrands = (brandsPayload || []).map(b => (b ?? '').toString().trim()).filter(Boolean);
                        if (availableBrands && availableBrands.length) {
                            populateBrandsSelect();
                            return;
                        }

                        // Fallback 1: try department by numeric id if the slug resolution failed
                        return fetch('/admin/departments/inline-snapshot', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                            .then(r2 => { if (!r2.ok) throw new Error('HTTP ' + r2.status); return r2.json(); })
                            .then(deptData => {
                                const list = Array.isArray(deptData.departments) ? deptData.departments : [];
                                const norm = (s) => (String(s || '')).toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                                const wanted = norm(dept);
                                const found = list.find(d => norm(d.slug) === wanted || norm(d.name) === wanted || String(d.id) === String(dept));
                                if (found && found.id) {
                                    const tryUrl = `/admin/products/brands-list?department=${encodeURIComponent(found.id)}`;
                                    console.debug && console.debug('fetchBrands: trying by id fallback', tryUrl);
                                    return fetch(tryUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                                        .then(r3 => { if (!r3.ok) throw new Error('HTTP ' + r3.status); return r3.json(); })
                                        .then(data => {
                                        const list = Array.isArray(data.departments) ? data.departments : (data || {}).departments || [];
                                        // normalize list for both select and combobox
                                        departmentsForQuick = Array.isArray(list) ? list.map(d => ({
                                            id: d.id,
                                            name: d.name || d.title || String(d.id),
                                            slug: d.slug || null,
                                            color: d.color || d.theme_primary || '#667eea',
                                            products_count: d.products_count || d.productsCount || d.products || 0
                                        })) : [];
                                        // Try to extract brands payload from this response (if present)
                                        let bp = [];
                                        if (Array.isArray(data.brands)) bp = data.brands;
                                        else if (data.brands && typeof data.brands === 'object') bp = Object.values(data.brands);
                                        availableBrands = (bp || []).map(b => (b ?? '').toString().trim()).filter(Boolean);
                                        if (availableBrands && availableBrands.length) {
                                            populateBrandsSelect();
                                            return;
                                        }
                                        // else continue to next fallback
                                        })
                                        .catch(err => console.debug && console.debug('fetchBrands id-fallback failed', err));
                                }
                                // Fallback 2: try without department param (global/all)
                                return fetch('/admin/products/brands-list', { headers: { 'Accept': 'application/json' }})
                                    .then(r4 => { if (!r4.ok) throw new Error('HTTP ' + r4.status); return r4.json(); })
                                    .then(data4 => {
                                        let bp = [];
                                        if (Array.isArray(data4.brands)) bp = data4.brands;
                                        else if (data4.brands && typeof data4.brands === 'object') bp = Object.values(data4.brands);
                                        availableBrands = (bp || []).map(b => (b ?? '').toString().trim()).filter(Boolean);
                                        populateBrandsSelect();
                                    })
                                    .catch(err => { console.error('fetchBrands final fallback failed', err); availableBrands = []; populateBrandsSelect(); });
                            })
                            .catch(err => {
                                // If dept snapshot failed, still try the no-department call
                                console.debug && console.debug('fetchBrands: dept snapshot failed', err);
                                return fetch('/admin/products/brands-list', { headers: { 'Accept': 'application/json' }})
                                    .then(r5 => { if (!r5.ok) throw new Error('HTTP ' + r5.status); return r5.json(); })
                                    .then(data5 => {
                                        let bp = [];
                                        if (Array.isArray(data5.brands)) bp = data5.brands;
                                        else if (data5.brands && typeof data5.brands === 'object') bp = Object.values(data5.brands);
                                        availableBrands = (bp || []).map(b => (b ?? '').toString().trim()).filter(Boolean);
                                        populateBrandsSelect();
                                    })
                                    .catch(e => { console.error('fetchBrands ultimate fallback failed', e); availableBrands = []; populateBrandsSelect(); });
                            });
                    })
                    .catch(err => { console.error('fetchBrands error', err); availableBrands = []; populateBrandsSelect(); });
            }

            function populateBrandsSelect(){
                // Brands UI removed; no-op
            }

            // When a brand is chosen manually, hide the 'no brands' warning
            if (spNewBrandSelect) {
                spNewBrandSelect?.addEventListener('change', function(){
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
                // Quick-create brand functionality removed — simply validate and close overlay for compatibility.
                if (!name) { window.ssShowToast && ssShowToast('Informe o nome.', 'warning'); return; }
                closeSpCreateBrand();
            });
            function renderSectionsList(){
                const arr = Array.isArray(window.DepartmentSectionsConfig) ? window.DepartmentSectionsConfig : getCurrentSectionsConfig();
                // If not on a department page, show a hint
                if (!onDepartmentPage()) {
                    sectionsUnsupported.style.display = 'block';
                    sectionsUnsupported.textContent = 'Abra este painel em uma página de departamento para ver as sessões disponíveis.';
                } else {
                    sectionsUnsupported.style.display = 'none';
                }

                sectionsList.innerHTML = '';
                const emptyEl = document.getElementById('sectionsEmpty');
                if (!arr || !arr.length) {
                    if (emptyEl) emptyEl.style.display = 'block';
                    return;
                }
                if (emptyEl) emptyEl.style.display = 'none';

                arr.forEach((sec) => {
                    const li = document.createElement('li');
                    li.className = 'sp-item';
                    const title = sec.title || (sec.reference ? String(sec.reference) : (sec.type || 'Sessão'));
                    const type = (sec.type || 'dynamic');
                    const ref = sec.reference || '';
                    const enabled = sec.enabled !== false;
                    // Edit link: always open the homepage sections index to avoid 404s
                    const homepageEditUrl = '/admin/homepage-sections';

                    const deptSlug = detectDepartmentSlug() || '';
                    const deptEditUrl = deptSlug ? `/admin/departments/${encodeURIComponent(deptSlug)}/edit` : '/admin/departments';

                    li.innerHTML = `
                        <div style="display:flex; align-items:center; gap:12px; width:100%;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="width:10px; height:10px; border-radius:999px; display:inline-block; background:${enabled? '#10b981' : '#94a3b8'}; box-shadow:0 2px 6px rgba(2,6,23,0.06);"></span>
                            </div>
                            <div style="flex:1">
                                <div style="font-weight:700; color: var(--text-dark);">${escapeHtml(title)}</div>
                                <div style="font-size:12px; color:#64748b; margin-top:4px;">${escapeHtml(type)} ${ref?('&middot; ' + escapeHtml(String(ref))):''}</div>
                            </div>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <a href="${homepageEditUrl}" class="sp-btn sp-btn-secondary" target="_blank" rel="noopener" title="Editar sessão (Homepage)"><i class="bi bi-pencil"></i></a>
                                <a href="${deptEditUrl}" class="sp-btn sp-btn-secondary" target="_blank" rel="noopener" title="Editar departamento"><i class="bi bi-diagram-3-fill"></i></a>
                            </div>
                        </div>
                    `;
                    sectionsList.appendChild(li);
                });
            }
            function moveItem(el, dir){
                if (!el) return;
                if (dir < 0 && el.previousElementSibling) el.parentNode.insertBefore(el, el.previousElementSibling);
                if (dir > 0 && el.nextElementSibling) el.parentNode.insertBefore(el.nextElementSibling, el);
            }
            function initSectionsPanel(){
                const dept = detectDepartmentSlug() || 'eletronicos';
                const target = `/admin/homepage-sections?department=${encodeURIComponent(dept)}&as=json`;
                fetch(target, { headers: { 'Accept': 'application/json' }})
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.success && Array.isArray(data.sections)) {
                            window.DepartmentSectionsConfig = data.sections.map(s => ({
                                id: s.id,
                                type: 'dynamic', // homepage sections are treated as dynamic sessions
                                reference: s.department_id ? String(s.department_id) : null,
                                title: s.title || '',
                                enabled: s.enabled !== false,
                                metadata: { homepage_section_id: s.id },
                            }));
                        } else {
                            window.DepartmentSectionsConfig = [];
                        }
                        renderSectionsList();
                    })
                    .catch(() => {
                        window.DepartmentSectionsConfig = [];
                        renderSectionsList();
                    });
            }

            // Wire refresh button for manual refetch
            const sectionsRefresh = document.getElementById('sectionsRefresh');
            sectionsRefresh?.addEventListener('click', function(){
                try { sectionsRefresh.disabled = true; sectionsRefresh.textContent = '…'; } catch(e){}
                initSectionsPanel();
                setTimeout(()=>{ try { sectionsRefresh.disabled = false; sectionsRefresh.textContent = '⭮'; } catch(e){} }, 600);
            });
            sectionsList?.addEventListener('click', function(e){
                const item = e.target.closest('.sp-item');
                if (!item) return;
                if (e.target.closest('.sp-up')) moveItem(item, -1);
                if (e.target.closest('.sp-down')) moveItem(item, +1);
                if (e.target.closest('.sp-remove')) item.remove();
            });
            // Confirmação ao trocar a seção existente
            let spPendingChange = null; // {itemEl, oldBrand, newBrand}
            // sections change handling for brand selection removed (brand selection UI was removed)
            spAdd?.addEventListener('click', function(){
                const title = (spNewTitle?.value || '').trim();
                const label = title || 'Nova seção';
                const current = getCurrentSectionsConfig();
                current.push({ type: 'dynamic', reference: null, title: label, enabled: true });
                window.DepartmentSectionsConfig = current;
                if (spNewTitle) spNewTitle.value = '';
                renderSectionsList();
            });
            // Overlay de confirmação para troca de seção
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
                spConfirmOverlay.style.display = 'flex';
                spConfirmOverlay.classList.add('active');
            }
            function closeSpConfirm(){
                if (spConfirmOverlay) {
                    spConfirmOverlay.classList.remove('active');
                    spConfirmOverlay.style.display = 'none';
                }
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
                    // Hide any matching section by comparing type+reference (legacy brand select removed)
                    const items = sectionsList.querySelectorAll('.sp-item');
                    items.forEach(li => {
                        if (li === itemEl) return;
                        const t = (li.querySelector('.sp-type')?.value || '').trim().toLowerCase();
                        const ref = (li.querySelector('.sp-reference')?.value || '').trim().toLowerCase();
                        const combined = `${t}:${ref}`;
                        if (combined === (oldBrand || '').toString().trim().toLowerCase() || ref === (oldBrand || '').toString().trim().toLowerCase()) {
                            const chk = li.querySelector('.sp-enabled');
                            if (chk) chk.checked = false;
                        }
                    });
                }
                const dept = detectDepartmentSlug() || 'eletronicos';
                // Ação extra: desativar produtos da sessão anterior
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
                // Bulk toggle by brand removed — skip server request and proceed as successful.
                try {
                    proceedAfter();
                } catch(e) { proceedAfter(); }
            });
            function applySectionsToPage(cfg){
                if (!onDepartmentPage()) return;
                if (!Array.isArray(cfg)) return;
                // 1) Renomear títulos e esconder/mostrar
                cfg.forEach(sec => {
                    // Use reference for matching (brands are stored as reference when type === 'brand')
                    const refKey = (sec.reference || '').trim().toLowerCase();
                    if (!refKey) return;
                    const sectionEl = document.querySelector(`[data-brand-section="${refKey}"]`);
                    if (!sectionEl) return;
                    // toggle visibility
                    sectionEl.style.display = (sec.enabled === false) ? 'none' : '';
                    // set title
                    const titleEl = sectionEl.querySelector('.js-section-title');
                    if (titleEl && (sec.title || sec.reference)) {
                        titleEl.textContent = sec.title || ('Produtos ' + sec.reference);
                    }
                });
                // 2) Reordenar DOM dentro do bloco de seções, preservando o restante da página
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
                    type: s.type || 'dynamic',
                    reference: (s.reference || '') || null,
                    reference_id: (s.reference_id && Number.isInteger(s.reference_id)) ? s.reference_id : null,
                    title: s.title || (s.reference ? ('Produtos ' + s.reference) : ''),
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
            searchTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                // debug
                try { console.debug && console.debug('handler: smartSearchTrigger clicked'); } catch(e) {}
                searchPanel.classList.toggle('active');
                // Ensure quick-product overlay is fully closed when opening the search
                try { console.debug && console.debug('smartSearchTrigger: closing quick product if open'); } catch(e) {}
                if (typeof closeQuickProduct === 'function') {
                    try { closeQuickProduct(); } catch(err) { /* noop */ }
                } else if (qpOverlay) {
                    qpOverlay.style.display = 'none';
                    qpOverlay.classList && qpOverlay.classList.remove('active');
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
            document.getElementById('ssRenameClose')?.addEventListener('click', closeRename);
            document.getElementById('ssRenameCancel')?.addEventListener('click', closeRename);
            document.getElementById('ssRenameSave')?.addEventListener('click', function(){
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
            document.getElementById('ssImageClose')?.addEventListener('click', closeImage);
            document.getElementById('ssImageCancel')?.addEventListener('click', closeImage);
            document.getElementById('ssImageSave')?.addEventListener('click', function(){
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
            document.getElementById('ssImageRemove')?.addEventListener('click', function(){
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

            
            // Tab switching
            document.querySelectorAll('.ss-tab').forEach(btn => {
                btn?.addEventListener('click', function(){
                    document.querySelectorAll('.ss-tab').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const panel = btn.dataset.tab;
                    document.querySelectorAll('.qp-tab-panel').forEach(p => p.style.display = (p.dataset.panel === panel) ? '' : 'none');
                });
            });


            // SKU automatico: gerar a partir do nome enquanto o usuário não editar o campo SKU
            try {
                if (qpSkuField) qpSkuField.dataset.manual = (qpSkuField.value || '').trim() ? 'true' : 'false';
                qpSkuField?.addEventListener('input', function(){ this.dataset.manual = 'true'; });
                qpNameField?.addEventListener('input', function(){
                    try {
                        if (!qpSkuField) return;
                        if (qpSkuField.dataset.manual === 'true') return; // usuario editou manualmente
                        const nameVal = (this.value || '').trim();
                        qpSkuField.value = generateSkuFromName(nameVal);
                    } catch(e) {}
                });
            } catch(e) {}

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
                // repopulate selects
                loadQuickFormOptions();
            }

            function openQuickProduct(){
                try { console.debug && console.debug('openQuickProduct called'); } catch(e) {}
                if (!qpOverlay) return;
                qpOverlay.style.display = 'flex';
                qpOverlay.classList && qpOverlay.classList.add('active');
                // Show floating action bar (if present) when opening quick-create
                try { const flo = document.getElementById('qpFloatingActions'); if (flo) flo.style.display = 'flex'; } catch(e) {}
                loadQuickFormOptions();
                setTimeout(()=>{ 
                    const deptEl = document.getElementById('qpDepartment');
                    const nameEl = document.getElementById('qpName');
                    // Focus department first for better flow; fallback to name
                    const deptInput = document.getElementById('qpDeptCombo');
                    if (deptInput) {
                        try { deptInput.focus(); } catch(e) { if (nameEl) nameEl.focus(); }
                    } else if (deptEl && deptEl.options && deptEl.options.length > 1) {
                        try { deptEl.focus(); } catch(e) { if (nameEl) nameEl.focus(); }
                    } else if (nameEl) {
                        nameEl.focus();
                    }
                    // reset sku manual flag when opening
                    if (qpSkuField) qpSkuField.dataset.manual = (qpSkuField.value || '').trim() ? 'true' : 'false';
                    // if name exists and sku not manually set, generate sku
                    if (qpNameField && qpSkuField && qpSkuField.dataset.manual !== 'true') {
                        const generated = generateSkuFromName(qpNameField.value || '');
                        qpSkuField.value = generated;
                    }
                    // Toggle stock fields according to product type (quick-create)
                    try {
                        const pt = document.getElementById('qpProductType');
                        const block = document.getElementById('qpStockBlock');
                        const stock = document.getElementById('qpStock');
                        const minStock = document.getElementById('qpMinStock');
                        if (pt && block) {
                            const applyToggle = function(){
                                try {
                                    if ((pt.value || 'physical') === 'service') {
                                        block.style.display = 'none';
                                        if (stock) stock.value = '';
                                        if (minStock) minStock.value = '';
                                    } else {
                                        block.style.display = '';
                                    }
                                } catch(e) { console.debug && console.debug('applyToggle error', e); }
                            };
                            pt.removeEventListener('change', applyToggle);
                            pt.addEventListener('change', applyToggle);
                            // apply now
                            applyToggle();
                        }
                    } catch(e) { console.debug && console.debug('qp product_type toggle failed', e); }
                },60);
            }
            function closeQuickProduct(){
                try { console.debug && console.debug('closeQuickProduct called'); } catch(e) {}
                if (!qpOverlay) return;
                qpOverlay.style.display = 'none';
                qpOverlay.classList && qpOverlay.classList.remove('active');
                // Ensure floating actions hidden when overlay closes
                try { const flo = document.getElementById('qpFloatingActions'); if (flo) flo.style.display = 'none'; } catch(e) {}
            }
            productsTriggerBtn?.addEventListener('click', function(e){ e.stopPropagation(); try { console.debug && console.debug('productsTrigger clicked'); } catch(e){}; openQuickProduct(); });
            // Fallback delegated listener: garante abertura mesmo se o listener direto não for registrado
            document.addEventListener('click', function(e){
                try {
                    const el = e.target.closest && e.target.closest('#productsTrigger');
                    if (!el) return;
                    e.stopPropagation();
                    try { console.debug && console.debug('delegated productsTrigger clicked'); } catch(e){}
                    openQuickProduct();
                } catch(err) { /* silent */ }
            });
            qpClose?.addEventListener('click', closeQuickProduct);
            qpCancel?.addEventListener('click', () => { closeQuickProduct(); });

            // Wire floating action buttons (duplicate of footer) to reuse existing handlers
            try {
                const fpSave = document.getElementById('qpFloatingSave');
                const fpCancel = document.getElementById('qpFloatingCancel');
                if (fpSave) fpSave.addEventListener('click', function(){ try { if (typeof qpSave?.click === 'function') qpSave.click(); else if (qpSave) qpSave.dispatchEvent(new Event('click')); } catch(e){} });
                if (fpCancel) fpCancel.addEventListener('click', function(){ try { if (typeof qpCancel === 'function') { qpCancel(); } else if (qpCancel && typeof qpCancel.click === 'function') qpCancel.click(); else if (qpCancel) qpCancel.dispatchEvent(new Event('click')); } catch(e){} });
            } catch(e) {}

            // From quick-create header, open the product manager overlay (so tabs are reachable)
            // From quick-create header, switch to the product manager tab inside the same modal
            document.getElementById('openPmManagerFromQuick')?.addEventListener('click', function(){
                try {
                    openQuickProduct();
                    setTimeout(function(){
                        const btn = document.querySelector('.ss-tab[data-tab="manage"]');
                        if (btn) btn.click();
                        try { pmSearchInput && pmSearchInput.focus(); } catch(e){}
                    }, 80);
                } catch(e){ console.debug && console.debug('openPmManagerFromQuick failed', e); }
            });

            // Load brands and categories
            function loadQuickFormOptions(){
                const catsEl = document.getElementById('qpCategories');
                const deptEl = document.getElementById('qpDepartment');

                // Fetch departments for the department selector
                if (deptEl) {
                    fetch('/admin/departments/inline-snapshot', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                        .then(data => {
                            const list = Array.isArray(data.departments) ? data.departments : (data || []).departments || [];
                            // Clear and populate
                            // normalize list for both select and combobox
                            departmentsForQuick = Array.isArray(list) ? list.map(d => ({ id: d.id, name: d.name || d.title || String(d.id), slug: d.slug || null })) : [];
                            deptEl.innerHTML = '<option value="">— Selecione o departamento —</option>';
                            if (Array.isArray(departmentsForQuick) && departmentsForQuick.length) {
                                departmentsForQuick.forEach(d => {
                                    try {
                                        const opt = document.createElement('option');
                                        opt.value = d.id || '';
                                        opt.textContent = d.name || String(d.id || '');
                                        deptEl.appendChild(opt);
                                    } catch(e){}
                                });
                            }
                            // render combobox list if present
                            renderDeptList('');
                            // wire combobox handlers once
                            try { wireDeptCombo(); } catch(e){}
                        })
                        .catch(err => { console.debug && console.debug('fetch departments failed', err); });
                }
                const searchInput = document.getElementById('qpCatSearchInput');
                const dropdown = document.getElementById('qpCatDropdown');
                const chips = document.getElementById('qpCatChips');
                // fetch categories list (expects array or { categories: [] })
                fetch('/admin/categories/list', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                    .then(r => {
                        if (r.ok) return r.json();
                        if (r.status === 404) {
                            // try public fallback
                            return fetch('/categories/json', { headers: { 'Accept': 'application/json' } }).then(r2 => r2.ok ? r2.json() : { categories: [] });
                        }
                        throw new Error('HTTP ' + r.status);
                    })
                    .then(data => {
                        console.debug && console.debug('qp categories fetched', data);
                        const list = Array.isArray(data) ? data : (data.categories || []);
                        qpCategoriesData = (list || []).map(c => ({ id: c.id || c.value || String(c.id||''), name: c.name || c.title || String(c.id || '') }));
                        // populate hidden select for compatibility
                        if (catsEl) {
                            catsEl.innerHTML = '';
                            qpCategoriesData.forEach(cat => {
                                const opt = document.createElement('option');
                                opt.value = cat.id;
                                opt.textContent = cat.name;
                                catsEl.appendChild(opt);
                            });
                        }
                        renderQpCategoriesDropdown('');
                        // clear previous selection if any
                        if (chips) chips.innerHTML = '';
                        qpSelectedCategories = new Set();
                    })
                    .catch(err => { console.error('Categorias quick-create falha', err); qpCategoriesData = []; renderQpCategoriesDropdown(''); });

                // wire search input to filter dropdown
                (function wireCatSearch(){
                    const si = document.getElementById('qpCatSearchInput');
                    if (!si) return;
                    si.addEventListener('input', function(){ renderQpCategoriesDropdown(this.value || ''); });
                    si.addEventListener('focus', function(){ renderQpCategoriesDropdown(this.value || ''); });
                    // Enter to create when no match; basic keyboard support
                    si.addEventListener('keydown', function(e){
                        const dd = document.getElementById('qpCatDropdown');
                        const q = (this.value || '').trim();
                        if (e.key === 'Enter'){
                            e.preventDefault();
                            // if no matched items, create new
                            const matched = qpCategoriesData.filter(c => (c.name || '').toLowerCase().indexOf(q.toLowerCase()) !== -1);
                            if (!matched.length && q.length) {
                                // create and select
                                createQpCategory(q).catch(err => alert('Erro ao criar categoria: ' + (err.message||err)));
                                return;
                            }
                            // otherwise, if exactly one match, toggle it
                            if (matched.length === 1) {
                                toggleQpCategory(matched[0].id, matched[0].name);
                            }
                        }
                        if (e.key === 'Escape') {
                            if (dd) dd.style.display = 'none';
                        }
                    });
                })();
            }

            function renderQpCategoriesDropdown(filter){
                const dropdown = document.getElementById('qpCatDropdown');
                const si = document.getElementById('qpCatSearchInput');
                const chips = document.getElementById('qpCatChips');
                const hidden = document.getElementById('qpCategories');
                if (!dropdown) return;
                const q = String(filter || (si && si.value) || '').trim().toLowerCase();
                dropdown.innerHTML = '';
                const matched = qpCategoriesData.filter(c => !q || c.name.toLowerCase().indexOf(q) !== -1).slice(0, 200);
                if (!matched.length) {
                    const empty = document.createElement('div');
                    empty.className = 'qp-cat-row';
                    empty.style.color = '#64748b';
                    empty.textContent = q ? 'Nenhuma categoria encontrada' : 'Carregando categorias...';
                    dropdown.appendChild(empty);
                    dropdown.style.display = 'block';
                    return;
                }
                matched.forEach(cat => {
                    const row = document.createElement('div');
                    row.className = 'qp-cat-row';
                    row.setAttribute('data-id', cat.id);
                    row.textContent = cat.name;
                    row.addEventListener('click', function(){ toggleQpCategory(cat.id, cat.name); });
                    dropdown.appendChild(row);
                });
                dropdown.style.display = 'block';
                // render chips from selected
                if (chips) {
                    chips.innerHTML = '';
                    Array.from(qpSelectedCategories).forEach(id => {
                        const cat = qpCategoriesData.find(c => String(c.id) === String(id));
                        const label = (cat && cat.name) ? cat.name : String(id);
                        const chip = document.createElement('span');
                        chip.className = 'qp-cat-chip';
                        chip.innerHTML = `<span>${escapeHtml(label)}</span><button type=\"button\" aria-label=\"Remover ${escapeHtml(label)}\">&times;</button>`;
                        chip.querySelector('button').addEventListener('click', function(){ removeQpCategory(id); });
                        chips.appendChild(chip);
                    });
                }
                // keep hidden select in sync
                if (hidden) {
                    Array.from(hidden.options).forEach(o => { o.selected = qpSelectedCategories.has(String(o.value)); });
                }
            }

            /* Department combobox renderer and wiring */
            function renderDeptList(filter){
                const listEl = document.getElementById('qpDeptList');
                const combo = document.getElementById('qpDeptCombo');
                if (!listEl || !combo) return;
                const q = String(filter || combo.value || '').trim().toLowerCase();
                listEl.innerHTML = '';
                const matched = (departmentsForQuick || []).filter(d => !q || (d.name||'').toLowerCase().indexOf(q) !== -1).slice(0, 200);
                if (!matched.length) {
                    const empty = document.createElement('div');
                    empty.className = 'qp-dept-item';
                    empty.style.color = '#64748b';
                    empty.textContent = q ? 'Nenhum departamento encontrado' : 'Carregando departamentos...';
                    listEl.appendChild(empty);
                    listEl.style.display = 'block';
                    qpDeptHighlighted = -1;
                    return;
                }
                matched.forEach((d, idx) => {
                    const row = document.createElement('div');
                    row.className = 'qp-dept-item';
                    row.id = 'qp-dept-item-' + String(d.id);
                    row.setAttribute('data-id', d.id);
                    row.setAttribute('data-idx', String(idx));
                    const count = d.products_count ? `<small>${escapeHtml(String(d.products_count))} produto(s)</small>` : '';
                    const sw = d.color ? `<span style="display:inline-block; width:12px; height:12px; border-radius:3px; margin-right:8px; vertical-align:middle; background:${escapeHtml(d.color)}; box-shadow:0 2px 6px rgba(2,6,23,0.1);"></span>` : '';
                    row.innerHTML = `<div>${sw}<span style="vertical-align:middle;">${escapeHtml(d.name || String(d.id || ''))}</span> ${d.slug ? (' <small>' + escapeHtml(d.slug) + '</small>') : ''}${count}</div>`;
                    row.setAttribute('role','option');
                    row.addEventListener('click', function(){ selectDept(d.id, d.name, d.color); });
                    listEl.appendChild(row);
                });
                qpDeptHighlighted = -1;
                listEl.style.display = 'block';
            }

            function selectDept(id, name, color){
                const combo = document.getElementById('qpDeptCombo');
                const hidden = document.getElementById('qpDepartment');
                const sw = document.getElementById('qpDeptSwatch');
                if (combo) combo.value = name || '';
                if (sw) {
                    if (color) {
                        sw.style.background = color;
                        sw.style.display = 'block';
                        sw.title = name || '';
                    } else {
                        sw.style.display = 'none';
                    }
                }
                if (hidden) {
                    // ensure option exists
                    let opt = Array.from(hidden.options).find(o => String(o.value) === String(id));
                    if (!opt) {
                        opt = document.createElement('option');
                        opt.value = id; opt.textContent = name || String(id);
                        hidden.appendChild(opt);
                    }
                    hidden.value = id;
                }
                const listEl = document.getElementById('qpDeptList');
                if (listEl) { listEl.style.display = 'none'; }
                const comboEl = document.getElementById('qpDeptCombo');
                if (comboEl) comboEl.setAttribute('aria-expanded', 'false');
            }

            function wireDeptCombo(){
                if (qpDeptWired) return; qpDeptWired = true;
                const combo = document.getElementById('qpDeptCombo');
                const listEl = document.getElementById('qpDeptList');
                if (!combo || !listEl) return;
                combo.addEventListener('input', function(){ renderDeptList(this.value || ''); });
                combo.addEventListener('focus', function(){ renderDeptList(this.value || ''); });
                combo.addEventListener('blur', function(){ setTimeout(()=>{ listEl.style.display = 'none'; }, 180); });
                combo.addEventListener('keydown', function(e){
                    const items = Array.from(listEl.querySelectorAll('.qp-dept-item'));
                    if (!items.length) return;
                    if (e.key === 'ArrowDown') { e.preventDefault(); qpDeptHighlighted = Math.min(qpDeptHighlighted + 1, items.length - 1); highlightDept(items); }
                    else if (e.key === 'ArrowUp') { e.preventDefault(); qpDeptHighlighted = Math.max(qpDeptHighlighted - 1, 0); highlightDept(items); }
                    else if (e.key === 'Enter') { e.preventDefault(); if (qpDeptHighlighted >= 0 && items[qpDeptHighlighted]) { const id = items[qpDeptHighlighted].dataset.id; const name = items[qpDeptHighlighted].querySelector('span')?.textContent?.trim() || items[qpDeptHighlighted].textContent.trim(); selectDept(id, name, (departmentsForQuick.find(d=>String(d.id)===String(id))||{}).color); } }
                    else if (e.key === 'Escape') { listEl.style.display = 'none'; combo.setAttribute('aria-expanded', 'false'); }
                });
                function highlightDept(items){
                    items.forEach((it, i) => {
                        const id = it.id || '';
                        const active = i === qpDeptHighlighted;
                        it.classList.toggle('qp-dept-highlight', active);
                        if (active) {
                            it.scrollIntoView({ block: 'nearest' });
                            // set aria-activedescendant on the combobox
                            combo.setAttribute('aria-activedescendant', id);
                        }
                    });
                }
            }

            function toggleQpCategory(id, name){
                const sid = String(id);
                if (qpSelectedCategories.has(sid)) {
                    qpSelectedCategories.delete(sid);
                } else {
                    qpSelectedCategories.add(sid);
                }
                renderQpCategoriesDropdown(document.getElementById('qpCatSearchInput')?.value || '');
                // keep dropdown visible briefly
                const dd = document.getElementById('qpCatDropdown'); if (dd) dd.style.display = 'block';
                // focus back to search
                document.getElementById('qpCatSearchInput')?.focus();
            }

            function removeQpCategory(id){
                qpSelectedCategories.delete(String(id));
                renderQpCategoriesDropdown(document.getElementById('qpCatSearchInput')?.value || '');
            }

            // Create a new category via public endpoint and select it
            function createQpCategory(name){
                if (!name || !name.trim()) return Promise.reject(new Error('Nome inválido'));
                const payload = { name: name.trim() };
                const dd = document.getElementById('qpCatDropdown');
                if (dd) { dd.innerHTML = '<div class="qp-cat-row" style="color:#64748b">Criando categoria…</div>'; dd.style.display = 'block'; }
                return fetch('/categories/json', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (!data || !data.success) throw new Error((data && data.message) || 'Erro ao criar categoria');
                    const cat = data.category || (data.categories && data.categories[0]) || { id: data.id || null, name: name };
                    // normalize and append
                    const entry = { id: cat.id || String(Date.now()), name: cat.name || name };
                    qpCategoriesData = qpCategoriesData || [];
                    // avoid duplicates
                    if (!qpCategoriesData.find(c => String(c.id) === String(entry.id))) qpCategoriesData.push(entry);
                    qpSelectedCategories.add(String(entry.id));
                    // sync hidden select
                    const hidden = document.getElementById('qpCategories');
                    if (hidden) {
                        const opt = Array.from(hidden.options).find(o => String(o.value) === String(entry.id));
                        if (!opt) {
                            const newOpt = document.createElement('option'); newOpt.value = entry.id; newOpt.textContent = entry.name; newOpt.selected = true; hidden.appendChild(newOpt);
                        } else {
                            opt.selected = true;
                        }
                    }
                    renderQpCategoriesDropdown('');
                    document.getElementById('qpCatSearchInput')?.focus();
                    return entry;
                })
                .catch(err => {
                    console.error('createQpCategory failed', err);
                    throw err;
                });
            }

            // Gather form data and submit (use FormData if images present)
                    qpSave?.addEventListener('click', function(){
                try {
                    const name = (document.getElementById('qpName')?.value || '').trim();
                    if (!name) { window.ssShowToast && ssShowToast('Informe o nome do produto.', 'warning'); document.querySelector('[data-panel="general"] input#qpName')?.focus(); return; }
                    const payload = {};
                    payload.name = name;
                    payload.sku = (document.getElementById('qpSku')?.value || '').trim() || null;
                    payload.product_type = document.getElementById('qpProductType')?.value || 'physical';
                    payload.is_active = document.getElementById('qpActive')?.checked ? 1 : 0;
                    payload.short_description = (document.getElementById('qpShortDesc')?.value || '').trim() || null;
                    // Ensure description exists (server requires it). Prefer full description, then short, then name.
                    let desc = (document.getElementById('qpDescription')?.value || '').trim();
                    if (!desc) desc = payload.short_description || payload.name || '';
                    payload.description = desc || null;
                    // read categories from the enhanced selector (qpSelectedCategories) or fallback to hidden select
                    payload.categories = Array.from(qpSelectedCategories).map(id => Number(id)).filter(Boolean);
                    // department (optional): include department_id if selected
                    const deptSel = document.getElementById('qpDepartment');
                    payload.department_id = deptSel && deptSel.value ? (isNaN(Number(deptSel.value)) ? deptSel.value : Number(deptSel.value)) : null;
                    // Client-side validation: categories required by server
                    if (!payload.categories || payload.categories.length === 0) {
                        window.ssShowToast && ssShowToast('Escolha pelo menos uma categoria antes de salvar (o servidor exige ao menos 1).', 'warning');
                        return;
                    }
                    // Price handling: if user requested margin-based calculation, compute B2B/B2C from cost
                    const rawCost = document.getElementById('qpCostPrice')?.value;
                    const useMargins = document.getElementById('qpUseMargins')?.checked;
                    if (useMargins && rawCost) {
                        const computed = computePricesFromCost(parseFloat(rawCost || 0), DEFAULT_MARGIN_B2B, DEFAULT_MARGIN_B2C);
                        payload.price = computed.b2c;
                        payload.b2b_price = computed.b2b;
                        payload.cost_price = parseFloat(rawCost);
                    } else {
                        payload.price = document.getElementById('qpPrice')?.value ? parseFloat(document.getElementById('qpPrice').value) : null;
                        payload.b2b_price = document.getElementById('qpB2bPrice')?.value ? parseFloat(document.getElementById('qpB2bPrice').value) : null;
                        payload.cost_price = document.getElementById('qpCostPrice')?.value ? parseFloat(document.getElementById('qpCostPrice').value) : null;
                    }
                    payload.compare_price = document.getElementById('qpComparePrice')?.value ? parseFloat(document.getElementById('qpComparePrice').value) : null;
                    // server expects `stock_quantity` and `min_stock`
                    payload.stock_quantity = document.getElementById('qpStock')?.value ? parseInt(document.getElementById('qpStock').value,10) : 0;
                    payload.min_stock = document.getElementById('qpMinStock')?.value ? parseInt(document.getElementById('qpMinStock').value,10) : 0;
                    payload.barcode = (document.getElementById('qpBarcode')?.value || '').trim() || null;
                    payload.weight = document.getElementById('qpWeight')?.value ? parseFloat(document.getElementById('qpWeight').value) : null;
                    payload.length = document.getElementById('qpLength')?.value ? parseFloat(document.getElementById('qpLength').value) : null;
                    payload.width = document.getElementById('qpWidth')?.value ? parseFloat(document.getElementById('qpWidth').value) : null;
                    payload.height = document.getElementById('qpHeight')?.value ? parseFloat(document.getElementById('qpHeight').value) : null;
                    payload.slug = (document.getElementById('qpSlug')?.value || '').trim() || null;
                    payload.seo_title = (document.getElementById('qpSeoTitle')?.value || '').trim() || null;
                    payload.seo_description = (document.getElementById('qpSeoDescription')?.value || '').trim() || null;


                    // Include sell channel flags
                    payload.sell_b2b = document.getElementById('qpSellB2B')?.checked ? 1 : 0;
                    payload.sell_b2c = document.getElementById('qpSellB2C')?.checked ? 1 : 0;

                    const files = qpImagesInput?.files || [];
                    let useForm = (files && files.length > 0);
                    // Ensure price exists (server requires price)
                    if (!payload.price && payload.price !== 0) payload.price = 0;

                    // helper: slugify and random suffix for SKU generation
                    function slugify(str){ return (str||'').toString().toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,''); }
                    function randomSuffix(len){ const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; let s=''; for(let i=0;i<len;i++) s+=chars[Math.floor(Math.random()*chars.length)]; return s; }

                    // Ensure there's a SKU (server requires unique). If empty, generate one from name.
                    if (!payload.sku) {
                        const base = slugify(payload.name || 'prod');
                        payload.sku = (base ? base.toUpperCase().slice(0,50) : 'PROD') + '-' + randomSuffix(4);
                    }

                    // Submit with retry logic on duplicate SKU conflict
                    function submitProduct(payloadObj, fileList, attempt){
                        attempt = attempt || 0;
                        const MAX_ATTEMPTS = 3;
                        if (fileList && fileList.length > 0) {
                            const fd = new FormData();
                            Object.keys(payloadObj).forEach(k => {
                                const val = payloadObj[k];
                                if (Array.isArray(val)) {
                                    val.forEach(v => { if (v === null || typeof v === 'undefined') return; fd.append(k + '[]', String(v)); });
                                } else if (val !== null && val !== undefined) {
                                    fd.append(k, String(val));
                                }
                            });
                            Array.from(fileList).slice(0,10).forEach(f => fd.append('images[]', f));
                            return fetch('/admin/products', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: fd })
                                .then(r => r.json().catch(()=>({ success:false, message: 'Resposta inválida do servidor' })))
                                .then(data => {
                                    if (data && data.success) return handleCreateResponse(data);
                                    const msg = data && (data.message || (data.errors && Object.values(data.errors).flat().join('\n')) ) || 'Erro ao criar produto';
                                    // detect duplicate sku error
                                    if (attempt < MAX_ATTEMPTS && /duplicate entry|products_sku_unique|1062/i.test(msg)){
                                        // generate new sku and retry
                                        payloadObj.sku = (slugify(payloadObj.sku||payloadObj.name||'PROD').toUpperCase().slice(0,40)) + '-' + randomSuffix(3);
                                        if (attempt === 0) window.ssShowToast && ssShowToast('SKU em uso. Tentando um SKU alternativo automaticamente...', 'warning');
                                        return submitProduct(payloadObj, fileList, attempt+1);
                                    }
                                    // otherwise show message
                                    window.ssShowToast ? ssShowToast(msg, 'error') : alert(msg);
                                })
                                .catch(err => { window.ssShowToast && ssShowToast(err.message || 'Erro ao criar produto', 'error'); });
                        } else {
                            return fetch('/admin/products', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify(payloadObj) })
                                .then(r => r.json().catch(()=>({ success:false, message: 'Resposta inválida do servidor' })))
                                .then(data => {
                                    if (data && data.success) return handleCreateResponse(data);
                                    const msg = data && (data.message || (data.errors && Object.values(data.errors).flat().join('\n')) ) || 'Erro ao criar produto';
                                    if (attempt < MAX_ATTEMPTS && /duplicate entry|products_sku_unique|1062/i.test(msg)){
                                        payloadObj.sku = (slugify(payloadObj.sku||payloadObj.name||'PROD').toUpperCase().slice(0,40)) + '-' + randomSuffix(3);
                                        if (attempt === 0) window.ssShowToast && ssShowToast('SKU em uso. Tentando um SKU alternativo automaticamente...', 'warning');
                                        return submitProduct(payloadObj, fileList, attempt+1);
                                    }
                                    window.ssShowToast ? ssShowToast(msg, 'error') : alert(msg);
                                })
                                .catch(err => { window.ssShowToast && ssShowToast(err.message || 'Erro ao criar produto', 'error'); });
                        }
                    }

                    // start submission
                    submitProduct(Object.assign({}, payload), files, 0);
                } catch(ex) { window.ssShowToast ? ssShowToast(ex.message || 'Erro inesperado', 'error') : alert(ex.message || 'Erro inesperado'); }
            });

            function handleCreateResponse(data){
                if (!data || !data.success) {
                    const msg = data && (data.message || (data.errors && Object.values(data.errors).flat().join('\n'))) || 'Erro ao criar produto';
                    window.ssShowToast ? ssShowToast(msg, 'error') : alert(msg);
                    return;
                }
                closeQuickProduct(); clearQuickForm();
                const successMsg = data.product && data.product.id ? ('Produto criado com sucesso (ID ' + data.product.id + ').') : 'Produto criado com sucesso.';
                window.ssShowToast ? ssShowToast(successMsg, 'success') : alert(successMsg);
                // Optionally reload available data (brand list removed)
            }

            // === Recriação rápida do atalho Produtos (fallback/override) ===
            (function quickProductRecreate(){
                try {
                    const btn = document.getElementById('productsTrigger');
                    const overlay = document.getElementById('ssQuickProductOverlay');
                    const form = document.getElementById('qpForm');
                    const nameInput = document.getElementById('qpName');
                    const saveBtn = document.getElementById('ssQuickProductSave');
                    const cancelBtn = document.getElementById('ssQuickProductCancel');
                    const closeBtn = document.getElementById('ssQuickProductClose');

                    if (!btn || !overlay || !form) return;

                    // Ensure overlay is hidden by default
                    overlay.style.display = overlay.style.display || 'none';

                    function openNew() {
                        overlay.style.display = 'flex';
                        overlay.classList && overlay.classList.add('active');
                        // reset small form area
                        try { nameInput && nameInput.focus(); } catch(e){}
                        // load categories if present
                        const cats = document.getElementById('qpCategories');
                        if (cats && !cats.children.length) {
                            fetch('/admin/categories/list', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                                .then(r => {
                                    if (r.ok) return r.json();
                                    if (r.status === 404) return fetch('/categories/json', { headers: { 'Accept': 'application/json' } }).then(r2 => r2.ok ? r2.json() : { categories: [] });
                                    throw new Error('HTTP ' + r.status);
                                })
                                .then(data => {
                                    console.debug && console.debug('quick fallback categories fetched', data);
                                    const list = Array.isArray(data) ? data : (data.categories || []);
                                    cats.innerHTML = '';
                                    qpCategoriesData = (list || []).map(c => ({ id: c.id || c.value || String(c.id||''), name: c.name || c.title || String(c.id || '') }));
                                    qpCategoriesData.forEach(cat => {
                                        const opt = document.createElement('option'); opt.value = cat.id || ''; opt.textContent = cat.name || String(cat.id || ''); cats.appendChild(opt);
                                    });
                                    // render enhanced UI too (chips/dropdown)
                                    try { renderQpCategoriesDropdown(''); } catch(e) {}
                                }).catch(err => { console.debug && console.debug('quick fallback categories fetch failed', err); });
                        }
                    }

                    function closeNew(){
                        overlay.classList && overlay.classList.remove('active');
                        overlay.style.display = 'none';
                    }

                    // Attach handlers (use click assignment to avoid duplicate addEventListener piling)
                    btn.onclick = function(e){ e && e.stopPropagation(); openNew(); };
                    closeBtn && (closeBtn.onclick = function(){ closeNew(); });
                    cancelBtn && (cancelBtn.onclick = function(){ closeNew(); });

                    // click outside modal to close
                    overlay.addEventListener('click', function(ev){
                        if (ev.target === overlay) closeNew();
                    });

                    // minimal save handler
                    saveBtn && (saveBtn.onclick = function(){
                        try {
                            const name = (nameInput?.value || '').trim();
                            if (!name) { window.ssShowToast && ssShowToast('Informe o nome do produto.', 'warning'); nameInput && nameInput.focus(); return; }
                            const payload = { name };
                            payload.product_type = document.getElementById('qpProductType')?.value || 'physical';
                            // collect categories if any
                            const cats = document.getElementById('qpCategories');
                            if (cats) payload.categories = Array.from(cats.selectedOptions || []).map(o => Number(o.value)).filter(Boolean);
                            // department for quick fallback
                            const dept = document.getElementById('qpDepartment');
                            if (dept && dept.value) payload.department_id = isNaN(Number(dept.value)) ? dept.value : Number(dept.value);
                            // stock fields required by server: send zeros when empty
                            payload.stock_quantity = document.getElementById('qpStock')?.value ? parseInt(document.getElementById('qpStock').value,10) : 0;
                            payload.min_stock = document.getElementById('qpMinStock')?.value ? parseInt(document.getElementById('qpMinStock').value,10) : 0;
                            // require at least one category (server validation)
                            if (!payload.categories || payload.categories.length === 0) {
                                window.ssShowToast && ssShowToast('Escolha pelo menos uma categoria antes de salvar.', 'warning');
                                return;
                            }
                            // ensure a description exists
                            const shortDesc = (document.getElementById('qpShortDesc')?.value || '').trim();
                            payload.description = (document.getElementById('qpDescription')?.value || '').trim() || shortDesc || payload.name;
                            // basic price + channel flags + optional margin calc
                            const rawCost = document.getElementById('qpCostPrice')?.value;
                            const useMargins = document.getElementById('qpUseMargins')?.checked;
                            if (useMargins && rawCost) {
                                const computed = computePricesFromCost(parseFloat(rawCost || 0), DEFAULT_MARGIN_B2B, DEFAULT_MARGIN_B2C);
                                payload.price = computed.b2c;
                                payload.b2b_price = computed.b2b;
                                payload.cost_price = parseFloat(rawCost);
                            } else {
                                const priceEl = document.getElementById('qpPrice');
                                if (priceEl && priceEl.value) payload.price = parseFloat(priceEl.value);
                            }
                            payload.sell_b2b = document.getElementById('qpSellB2B')?.checked ? 1 : 0;
                            payload.sell_b2c = document.getElementById('qpSellB2C')?.checked ? 1 : 0;

                            fetch('/admin/products', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: JSON.stringify(payload) })
                                .then(r => r.json())
                                .then(function(data){
                                    try { handleCreateResponse(data); } catch(e) { window.ssShowToast ? ssShowToast('Produto criado (resposta inesperada)', 'success') : alert('Produto criado (resposta inesperada)'); }
                                })
                                .catch(err => { window.ssShowToast ? ssShowToast(err.message || 'Erro ao criar produto', 'error') : alert(err.message || 'Erro ao criar produto'); });
                        } catch(e) { window.ssShowToast ? ssShowToast(e.message || 'Erro', 'error') : alert(e.message || 'Erro'); }
                    });

                } catch(e) { console.error('quickProductRecreate failed', e); }
            })();
        });
    </script>

    <script>
        // Extra safety: se ocorrer um erro que interrompa outros scripts, este handler
        // tenta reconectar os FABs importantes e permite abrir o painel manualmente.
        (function(){
            function safeWire() {
                try {
                    const wireOne = (btnId, panelId, initFn) => {
                        const btn = document.getElementById(btnId);
                        const panel = document.getElementById(panelId);
                        if (!btn || !panel) return false;
                        // prevent double-binding
                        if (btn.__ss_wired) return true;
                        btn.addEventListener('click', function(e){
                            e && e.preventDefault && e.preventDefault();
                            const willOpen = !panel.classList.contains('active');
                            if (willOpen) {
                                panel.classList.add('active');
                                try { if (typeof initFn === 'function') initFn(); } catch(err) { console.debug && console.debug('initFn failed', err); }
                            } else {
                                panel.classList.remove('active');
                            }
                        });
                        btn.__ss_wired = true;
                        return true;
                    };

                    // Try to wire departments specifically (most important for current bug)
                    wireOne('departmentsTrigger', 'departmentsPanel', function(){ try { if (typeof initDepartmentsPanel === 'function') initDepartmentsPanel(); } catch(e){} });
                    wireOne('sectionsTrigger', 'sectionsPanel');
                    wireOne('themeTrigger', 'themePanel');
                    wireOne('smartSearchTrigger', 'smartSearchPanel');
                } catch(err) { console.debug && console.debug('safeWire error', err); }
            }

            // Run after DOM ready
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(safeWire, 50);
            } else {
                document.addEventListener('DOMContentLoaded', function(){ setTimeout(safeWire, 50); });
            }

            // Also attempt to recover when an uncaught error occurs (so wiring still happens)
            window.addEventListener('error', function(){ setTimeout(safeWire, 50); });
            window.addEventListener('unhandledrejection', function(){ setTimeout(safeWire, 50); });
        })();
    </script>
    <script>
        // Ensure sections panel loads when it becomes active regardless of how it was opened
        (function(){
            try {
                const panel = document.getElementById('sectionsPanel');
                if (!panel) return;
                // Debounce guard
                let _t = null;
                const ensure = () => {
                    try {
                        if (typeof initSectionsPanel === 'function') initSectionsPanel();
                    } catch(e) { console.debug && console.debug('ensure initSectionsPanel failed', e); }
                };
                const obs = new MutationObserver((mutations) => {
                    for (const m of mutations) {
                        if (m.attributeName === 'class') {
                            const has = panel.classList && panel.classList.contains('active');
                            if (has) {
                                if (_t) clearTimeout(_t);
                                _t = setTimeout(() => { ensure(); _t = null; }, 80);
                            }
                        }
                    }
                });
                obs.observe(panel, { attributes: true, attributeFilter: ['class'] });
                // Also try once on load if panel already active
                if (panel.classList && panel.classList.contains('active')) setTimeout(ensure, 50);
            } catch(e) { console.debug && console.debug('sectionsPanel observer failed', e); }
        })();
    </script>
    <script>
    // Robust wiring for simple departments panel (runs even if earlier scripts errored)
    (function(){
        function safeWire(){
            try{
                console.debug && console.debug('safeWire: init');
                const trigger = document.getElementById('departmentsTrigger');
                const panel = document.getElementById('departmentsPanelSimple');
                const closeBtn = document.getElementById('departmentsSimpleClose');
                if (!trigger) { console.debug && console.debug('safeWire: no trigger element'); return; }
                if (!panel) { console.debug && console.debug('safeWire: no panel element'); return; }
                // prevent duplicate wiring
                if (trigger.dataset._wiredSimple) return; trigger.dataset._wiredSimple = '1';
                trigger.addEventListener('click', function(e){
                    try{
                        e && e.stopPropagation();
                        const open = panel.classList && panel.classList.contains('active');
                        if (open) { panel.style.display = 'none'; panel.classList && panel.classList.remove('active'); return; }
                        panel.style.display = 'flex'; panel.classList && panel.classList.add('active');
                        // fetch snapshot and render
                        fetch('/admin/departments/inline-snapshot', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                            .then(data => {
                                const items = Array.isArray(data.departments) ? data.departments : [];
                                // render simple list
                                const list = document.getElementById('departmentsSimpleList'); if (!list) return;
                                list.innerHTML = '';
                                items.forEach(d => {
                                    const li = document.createElement('li'); li.style.display='flex'; li.style.justifyContent='space-between'; li.style.padding='8px'; li.style.borderBottom='1px solid #eef2f6';
                                    const left = document.createElement('div'); left.style.display='flex'; left.style.gap='10px'; left.style.alignItems='center';
                                    const sw = document.createElement('div'); sw.style.width='28px'; sw.style.height='28px'; sw.style.borderRadius='6px'; sw.style.background = d.color || '#667eea';
                                    const name = document.createElement('div'); name.style.fontWeight='600'; name.textContent = d.name || '(sem nome)';
                                    left.appendChild(sw); left.appendChild(name);
                                    const right = document.createElement('div'); right.style.display='flex'; right.style.gap='8px';
                                    const view = document.createElement('a'); view.href = d.id ? (`/admin/departments/${d.id}/edit`) : '#'; view.className='sp-btn sp-btn-secondary'; view.textContent='Editar'; view.style.padding='6px 8px';
                                    right.appendChild(view);
                                    li.appendChild(left); li.appendChild(right); list.appendChild(li);
                                });
                            })
                            .catch(err => { console.error('safeWire fetch error', err); const m = document.getElementById('dpSimpleMsg'); if(m) m.textContent = 'Erro ao carregar departamentos.'; });
                    } catch(e){ console.error('trigger click error', e); }
                });
                if (closeBtn) closeBtn.addEventListener('click', function(){ panel.style.display='none'; panel.classList && panel.classList.remove('active'); });
                console.debug && console.debug('safeWire: wired');
            } catch(e){ console.error('safeWire error', e); }
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', safeWire); else safeWire();
    })();
    </script>
    <script>
        // Final safety: ensure quick-open handlers actually open overlays.
        (function(){
            function safeOpenQuickProduct(e){
                try { e && e.stopPropagation(); } catch(e){}
                try {
                    if (typeof openQuickProduct === 'function') {
                        openQuickProduct();
                        return;
                    }
                } catch(err) {
                    console.debug && console.debug('openQuickProduct threw', err);
                }

                // Fallback: open overlay manually
                try {
                    const qpOverlay = document.getElementById('ssQuickProductOverlay');
                    if (!qpOverlay) return;
                    qpOverlay.style.display = 'flex';
                    qpOverlay.classList && qpOverlay.classList.add('active');
                    // ensure it's attached to body
                    if (qpOverlay.parentElement !== document.body) document.body.appendChild(qpOverlay);
                    // try to load options if available
                    if (typeof loadQuickFormOptions === 'function') {
                        try { loadQuickFormOptions(); } catch(e){}
                    }
                    // focus department combobox or name
                    setTimeout(function(){
                        const dept = document.getElementById('qpDeptCombo') || document.getElementById('qpDepartment');
                        const name = document.getElementById('qpName');
                        try { if (dept) dept.focus(); else if (name) name.focus(); } catch(e){}
                    }, 50);
                } catch(e){ console.error('safeOpenQuickProduct failed', e); }
            }

            function attachSafe(id, handler){
                try {
                    const btn = document.getElementById(id);
                    if (!btn) return;
                    btn.removeEventListener('click', handler);
                    btn.addEventListener('click', handler);
                } catch(e){}
            }

            // Attach to known FAB ids
            document.addEventListener('DOMContentLoaded', function(){
                attachSafe('productsTrigger', safeOpenQuickProduct);
                attachSafe('productsManagerTrigger', safeOpenQuickProduct);
                // also ensure overlay close buttons wired
                const qpClose = document.getElementById('ssQuickProductClose');
                if (qpClose) qpClose.addEventListener('click', function(){ try { document.getElementById('ssQuickProductOverlay').style.display='none'; document.getElementById('ssQuickProductOverlay').classList.remove('active'); } catch(e){} });
            });
        })();
    </script>
    <script>
        // Product Manager: tabs, search and "latest" loader
        document.addEventListener('DOMContentLoaded', function(){
            const pmTabManage = document.getElementById('pmTabManage');
            const pmTabLatest = document.getElementById('pmTabLatest');
            const pmManagePanel = document.getElementById('pmManagePanel');
            const pmLatestPanel = document.getElementById('pmLatestPanel');
            const pmSearchInput = document.getElementById('pmSearchInput');
            const pmResults = document.getElementById('pmResults');
            const pmLatestResults = document.getElementById('pmLatestResults');
            const pmManagerClose = document.getElementById('pmManagerClose');
            const pmManagerCancel = document.getElementById('pmManagerCancel');

            const CSRF_TOKEN = (typeof CSRF !== 'undefined') ? CSRF : (document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');

            function switchPmTab(tab){
                if (tab === 'manage'){
                    pmManagePanel.style.display = '';
                    pmLatestPanel.style.display = 'none';
                    pmTabManage.classList.add('active'); pmTabLatest.classList.remove('active');
                } else {
                    pmManagePanel.style.display = 'none';
                    pmLatestPanel.style.display = '';
                    pmTabManage.classList.remove('active'); pmTabLatest.classList.add('active');
                    loadPmLatest();
                }
            }

            pmTabManage?.addEventListener('click', () => switchPmTab('manage'));
            pmTabLatest?.addEventListener('click', () => switchPmTab('latest'));

            function renderPmItem(prod){
                const el = document.createElement('div');
                el.className = 'pm-item d-flex align-items-center';
                el.style.padding = '10px';
                el.style.borderBottom = '1px solid #eef2f7';
                el.dataset.id = prod.id;
                const img = prod.first_image || '{{ asset('images/no-image.svg') }}';
                const price = prod.price ? `R$ ${parseFloat(prod.price).toFixed(2).replace('.',',')}` : '—';
                el.innerHTML = `
                    <div style="width:64px; height:64px; flex:0 0 64px; margin-right:12px;"><img src="${img}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;" onerror="this.src='{{ asset('images/no-image.svg') }}'"></div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px">
                            <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-weight:600;">${escapeHtml(prod.name || prod.title || ('ID ' + prod.id))}</div>
                            <div style="color:#10b981; font-weight:700;">${price}</div>
                        </div>
                        <div style="margin-top:6px; display:flex; gap:8px;">
                            <a class="btn btn-sm btn-outline-secondary" href="/admin/products/${prod.id}/edit" target="_blank">Editar</a>
                            <button class="btn btn-sm btn-primary pm-select-btn" title="Selecionar produto">Selecionar</button>
                            <button class="btn btn-sm btn-danger pm-delete-btn">Remover</button>
                            <a class="btn btn-sm btn-secondary" href="/admin/products/${prod.id}" target="_blank">Ver</a>
                        </div>
                    </div>
                `;
                // attach delete handler
                el.querySelector('.pm-delete-btn')?.addEventListener('click', function(){
                    if (!confirm('Deseja remover este produto? Essa ação pode ser irreversível.')) return;
                    const id = prod.id;
                    fetch(`/admin/products/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }})
                        .then(r => r.json())
                        .then(data => {
                            if (!data || !data.success) throw new Error(data && data.message ? data.message : 'Erro ao remover produto');
                            el.remove();
                            window.ssShowToast && ssShowToast('Produto removido', 'success');
                        }).catch(err => { window.ssShowToast ? ssShowToast(err.message || 'Erro', 'error') : alert(err.message || 'Erro'); });
                });
                return el;
            }

            function escapeHtml(str){ return (str||'').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

            let pmSearchTimer = null;
            pmSearchInput?.addEventListener('input', function(){
                clearTimeout(pmSearchTimer);
                pmSearchTimer = setTimeout(()=> { loadPmResults(this.value.trim()); }, 250);
            });

            function loadPmResults(q){
                if (!pmResults) return;
                pmResults.innerHTML = '<div style="padding:16px;color:#64748b">Buscando...</div>';
                const url = `{{ route('admin.products.index') }}?search=${encodeURIComponent(q)}&per_page=50`;
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With':'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => {
                        const list = (data && data.products) ? data.products : (Array.isArray(data) ? data : []);
                        if (!list.length) {
                            pmResults.innerHTML = '<div style="padding:16px;color:#64748b">Nenhum produto encontrado.</div>';
                            return;
                        }
                        pmResults.innerHTML = '';
                        list.forEach(p => pmResults.appendChild(renderPmItem(p)));
                    })
                    .catch(err => { pmResults.innerHTML = '<div style="padding:16px;color:#ef4444">Erro ao buscar produtos</div>'; console.error(err); });
            }

            function loadPmLatest(){
                if (!pmLatestResults) return;
                pmLatestResults.innerHTML = '<div style="padding:16px;color:#64748b">Carregando últimos produtos...</div>';
                // try a couple of fallbacks for endpoint
                const tryUrls = [
                    `{{ route('admin.products.index') }}?recent=1&per_page=20`,
                    `{{ route('admin.products.index') }}?order=created_at_desc&per_page=20`,
                    `{{ route('admin.products.index') }}?per_page=20`
                ];
                function tryFetch(i){
                    if (i >= tryUrls.length) { pmLatestResults.innerHTML = '<div style="padding:16px;color:#64748b">Não foi possível carregar os últimos produtos.</div>'; return; }
                    fetch(tryUrls[i], { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(data => {
                            const list = (data && data.products) ? data.products : (Array.isArray(data) ? data : []);
                            if (!list.length && i < tryUrls.length-1) return tryFetch(i+1);
                            if (!list.length) { pmLatestResults.innerHTML = '<div style="padding:16px;color:#64748b">Nenhum produto recente encontrado.</div>'; return; }
                            pmLatestResults.innerHTML = '';
                            list.forEach(p => pmLatestResults.appendChild(renderPmItem(p)));
                        }).catch(() => tryFetch(i+1));
                }
                tryFetch(0);
            }

            // initial state: show create tab by default
            document.querySelectorAll('.ss-tab').forEach(b=>b.classList.remove('active'));
            document.querySelector('.ss-tab[data-tab="create"]')?.classList.add('active');
            document.querySelectorAll('.qp-tab-panel').forEach(p => p.style.display = (p.dataset.panel === 'create') ? '' : 'none');

            // wire close/cancel: switch back to create tab (do not close the whole modal)
            pmManagerClose?.addEventListener('click', function(){
                try {
                    document.querySelectorAll('.ss-tab').forEach(b=>b.classList.remove('active'));
                    document.querySelector('.ss-tab[data-tab="create"]')?.classList.add('active');
                    document.querySelectorAll('.qp-tab-panel').forEach(p => p.style.display = (p.dataset.panel === 'create') ? '' : 'none');
                } catch(e) { console.debug && console.debug('pmManagerClose switch failed', e); }
            });
            pmManagerCancel?.addEventListener('click', function(){
                try {
                    document.querySelectorAll('.ss-tab').forEach(b=>b.classList.remove('active'));
                    document.querySelector('.ss-tab[data-tab="create"]')?.classList.add('active');
                    document.querySelectorAll('.qp-tab-panel').forEach(p => p.style.display = (p.dataset.panel === 'create') ? '' : 'none');
                } catch(e) { console.debug && console.debug('pmManagerCancel switch failed', e); }
            });

            // when the quick modal changes visibility, autofocus search if manager tab active
            const overlay = document.getElementById('ssQuickProductOverlay');
            if (overlay) {
                const obs = new MutationObserver(function(m){
                    try {
                        if (overlay.style.display !== 'none') {
                            const active = document.querySelector('.ss-tab.active')?.dataset.tab;
                            if (active === 'manage') setTimeout(()=> pmSearchInput && pmSearchInput.focus(), 80);
                        }
                    } catch(e){}
                });
                obs.observe(overlay, { attributes: true, attributeFilter: ['style', 'class'] });
            }
        });
    </script>
    <script>
        // Defensive fallbacks: ensure FAB shortcuts still open their panels
        document.addEventListener('DOMContentLoaded', function(){
            try {
                // Global error capture to aid debugging (will show toast and console log)
                window.addEventListener('error', function(ev){
                    try { console.error('Global JS error:', ev.message, ev.filename + ':' + ev.lineno, ev.error); } catch(e){}
                    if (window.ssShowToast) ssShowToast('Erro de script detectado. Veja console para detalhes.', 'error', 8000);
                });
                window.addEventListener('unhandledrejection', function(ev){
                    try { console.error('Unhandled promise rejection:', ev.reason); } catch(e){}
                    if (window.ssShowToast) ssShowToast('Erro assíncrono detectado. Veja console para detalhes.', 'error', 8000);
                });

                const safeToggle = (btnId, panelId, asOverlay, preferFn) => {
                    const btn = document.getElementById(btnId);
                    const panel = document.getElementById(panelId);
                    if (!btn) {
                        console.debug && console.debug('safeToggle: button not found', btnId);
                        return;
                    }
                    btn.addEventListener('click', function(e){
                        e && e.stopPropagation();
                        try {
                            if (typeof preferFn === 'function') {
                                // try preferred handler first (e.g. openQuickProduct)
                                try { preferFn(); return; } catch(err) { console.debug && console.debug('preferFn failed', err); }
                            }
                            if (panel) {
                                if (panel.classList) panel.classList.toggle('active');
                                if (asOverlay) {
                                    // toggle inline display for overlays
                                    try { panel.style.display = (panel.style.display === 'flex' || panel.classList.contains('active')) ? 'none' : 'flex'; } catch(e) { panel.style.display = panel.style.display === 'flex' ? 'none' : 'flex'; }
                                }
                            }
                        } catch(err){ console.debug && console.debug('safeToggle click handler error', err); }
                    });
                };

                safeToggle('smartSearchTrigger', 'smartSearchPanel');
                safeToggle('productsTrigger', 'ssQuickProductOverlay', true, function(){ if (typeof openQuickProduct === 'function') { try { openQuickProduct(); } catch(e){ console.debug('openQuickProduct threw', e); throw e; } } else { throw new Error('openQuickProduct not defined'); } });
                safeToggle('productsManagerTrigger', 'ssQuickProductOverlay', true, function(){
                    try {
                        // open quick modal and switch to create tab (prefer creation flow)
                        if (typeof openQuickProduct === 'function') openQuickProduct();
                        else { const qp = document.getElementById('ssQuickProductOverlay'); if (qp) { qp.style.display = 'flex'; qp.classList && qp.classList.add('active'); } }
                        setTimeout(()=>{
                            const btn = document.querySelector('.ss-tab[data-tab="create"]'); if (btn) btn.click();
                            try { const name = document.getElementById('qpName'); if (name) name.focus(); } catch(e){}
                        }, 60);
                    } catch(e) { console.debug && console.debug('open pmCreate (tab) failed', e); }
                });
                safeToggle('departmentsTrigger', 'departmentsPanel');
                safeToggle('sectionsTrigger', 'sectionsPanel');
                safeToggle('themeTrigger', 'themePanel');
            } catch(e){ console.debug && console.debug('defensive FAB wiring failed', e); }
        });
    </script>
    </script>
    <script>
        // When header dropdown dispatches department:selected, update quick-create / smart-search department selection
        window.addEventListener('department:selected', function(e){
            try {
                var detail = (e && e.detail) ? e.detail : {};
                var id = detail.id || detail.slug || '';
                var name = detail.name || '';
                var color = detail.color || '';

                // If the panel has a select element, set its value and trigger change
                var deptSel = document.getElementById('qpDepartment');
                if (deptSel) {
                    try { deptSel.value = id || ''; } catch(err){}
                    try { deptSel.dispatchEvent(new Event('change', { bubbles: true })); } catch(err){}
                }

                // Update the visible combo/input used as trigger
                var combo = document.getElementById('qpDeptCombo');
                if (combo) {
                    try { combo.value = name || id || ''; } catch(err){}
                }

                // If the quick-create specific selectDept function exists, call it to keep internal state
                try { if (typeof selectDept === 'function') selectDept(id, name, color); } catch(err){}
            } catch(err) { console.debug && console.debug('department:selected handler error', err); }
        });
    </script>
@endauth
    <script>
        // Fallback delegado: garante que os FABs abram seus painéis mesmo que algum listener anterior não tenha sido registrado.
        document.addEventListener('click', function(e){
            try {
                const map = [
                    { btnId: 'departmentsTrigger', panelId: 'departmentsPanel' },
                    { btnId: 'sectionsTrigger', panelId: 'sectionsPanel' },
                    { btnId: 'themeTrigger', panelId: 'themePanel' },
                    { btnId: 'smartSearchTrigger', panelId: 'smartSearchPanel' }
                ];
                for (const m of map) {
                    const el = e.target.closest && e.target.closest('#' + m.btnId);
                    if (!el) continue;
                    e.stopPropagation(); e.preventDefault && e.preventDefault();
                    const panel = document.getElementById(m.panelId);
                    if (!panel) continue;
                    if (panel.classList) panel.classList.toggle('active');
                }
            } catch(err) {
                console.debug && console.debug('FAB fallback click handler error', err);
            }
        }, true);
    </script>
