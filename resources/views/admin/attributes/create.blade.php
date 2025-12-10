@extends('admin.layouts.app')

@section('title', 'Criar Atributo')
@section('page-title', 'Criar Novo Atributo')
@section('page-icon', 'bi-sliders')

@section('content')
<style>
    .attribute-form-modern {
        background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.98));
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .form-section {
        padding: 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title i {
        color: #495a6d;
        font-size: 1.2rem;
    }

    .form-label-modern {
        font-weight: 600;
        color: #334155;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label-modern .required {
        color: #ef4444;
    }

    .form-control-modern {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
    }

    .form-control-modern:focus {
        border-color: #495a6d;
        box-shadow: 0 0 0 4px rgba(73, 90, 109, 0.1);
        outline: none;
        background: #ffffff;
    }

    .form-control-modern::placeholder {
        color: #94a3b8;
    }

    .form-select-modern {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        cursor: pointer;
    }

    .form-select-modern:focus {
        border-color: #495a6d;
        box-shadow: 0 0 0 4px rgba(73, 90, 109, 0.1);
        outline: none;
    }

    .value-item-modern {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.75rem;
        margin-bottom: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .value-item-modern:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transform: translateY(-3px);
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
    }

    .value-item-modern:last-child {
        margin-bottom: 0;
    }

    .value-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .value-item-number {
        font-weight: 700;
        color: #495a6d;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .value-item-number::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #495a6d;
        border-radius: 50%;
    }

    .btn-remove-value {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-remove-value:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .color-input-group {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .color-picker-input {
        width: 70px;
        height: 56px;
        border: 3px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .color-picker-input:hover {
        border-color: #495a6d;
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .color-picker-input:active {
        transform: scale(0.95);
    }

    .color-hex-input {
        flex: 1;
    }

    .btn-add-value-modern {
        background: linear-gradient(135deg, #495a6d 0%, #334155 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 1rem 2rem;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 6px 20px rgba(73, 90, 109, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-add-value-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-add-value-modern:hover::before {
        left: 100%;
    }

    .btn-add-value-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(73, 90, 109, 0.4);
        background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
    }

    .btn-add-value-modern:active {
        transform: translateY(-1px);
    }

    .btn-submit-modern {
        background: linear-gradient(135deg, #495a6d 0%, #334155 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.875rem 2rem;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 4px 16px rgba(73, 90, 109, 0.25);
    }

    .btn-submit-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(73, 90, 109, 0.35);
    }

    .btn-cancel-modern {
        background: white;
        color: #64748b;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.875rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel-modern:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .info-card-modern {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #bae6fd;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .info-card-modern h6 {
        color: #0369a1;
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card-modern p {
        color: #0c4a6e;
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.6;
    }

    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .type-badge.color { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
    .type-badge.size { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; }
    .type-badge.text { background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 100%); color: #6b21a8; }
    .type-badge.number { background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); color: #9f1239; }
    .type-badge.image { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
        margin-bottom: 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1.5rem;
        display: block;
    }

    .empty-state p {
        font-size: 1rem;
        margin: 0;
        font-weight: 500;
        line-height: 1.6;
    }

    .form-check-input:checked {
        background-color: #495a6d;
        border-color: #495a6d;
    }

    .form-check-input:focus {
        border-color: #495a6d;
        box-shadow: 0 0 0 0.25rem rgba(73, 90, 109, 0.25);
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 6px 20px rgba(73, 90, 109, 0.3);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 8px 30px rgba(73, 90, 109, 0.5);
        }
    }

    .btn-add-value-modern:focus {
        outline: 3px solid rgba(73, 90, 109, 0.3);
        outline-offset: 2px;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .value-item-modern {
        animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-add-value-modern:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .invalid-feedback::before {
        content: '⚠';
        font-size: 1rem;
    }

    .form-control-modern.is-invalid {
        border-color: #ef4444;
        background-color: #fef2f2;
    }

    .form-control-modern.is-invalid:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .debug-panel {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #1e293b;
        color: #fff;
        padding: 1rem;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        max-width: 400px;
        max-height: 500px;
        overflow-y: auto;
        z-index: 9999;
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        display: none;
    }

    .debug-panel.active {
        display: block;
    }

    .debug-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .debug-panel-header h6 {
        margin: 0;
        color: #60a5fa;
        font-weight: 700;
    }

    .debug-panel-content {
        color: #cbd5e1;
        line-height: 1.6;
    }

    .debug-panel-content .debug-item {
        margin-bottom: 0.5rem;
        padding: 0.5rem;
        background: rgba(255,255,255,0.05);
        border-radius: 4px;
    }

    .debug-panel-content .debug-label {
        color: #94a3b8;
        font-weight: 600;
    }

    .debug-panel-content .debug-value {
        color: #fff;
        word-break: break-all;
    }

    .debug-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: #495a6d;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.3s;
    }

    .debug-toggle:hover {
        background: #334155;
        transform: scale(1.1);
    }

    .debug-panel.active ~ .debug-toggle {
        display: none;
    }
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="attribute-form-modern">
            <form action="{{ route('admin.attributes.store') }}" method="POST" id="attribute-form">
                @csrf
                
                <!-- Informações Básicas -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="bi bi-info-circle"></i>
                        <span>Informações Básicas</span>
                    </div>

                    <div class="mb-4">
                        <label for="name" class="form-label-modern">
                            Nome do Atributo <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control-modern @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="Ex: Cor, Tamanho, Numeração" 
                               required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> Este nome será usado em todos os produtos que usarem este atributo.
                        </small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label-modern">
                                Tipo de Atributo <span class="required">*</span>
                            </label>
                            <select class="form-select-modern @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="">Selecione o tipo</option>
                                <option value="color" {{ old('type') === 'color' ? 'selected' : '' }}>Cor (Swatches coloridos)</option>
                                <option value="size" {{ old('type') === 'size' ? 'selected' : '' }}>Tamanho (Botões)</option>
                                <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Texto (Botões de texto)</option>
                                <option value="number" {{ old('type') === 'number' ? 'selected' : '' }}>Número (Dropdown)</option>
                                <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Imagem (Swatches com imagens)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="type-badge-container" class="mt-2"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="sort_order" class="form-label-modern">
                                Ordem de Exibição
                            </label>
                            <input type="number" 
                                   class="form-control-modern @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', 0) }}" 
                                   min="0">
                            @error('sort_order')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-sort-numeric-down"></i> Menor número aparece primeiro
                            </small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            <label class="form-check-label ms-3" for="is_active" style="font-weight: 600; cursor: pointer;">
                                Atributo Ativo
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1 ms-5">
                            Atributos inativos não aparecerão nas opções de criação de variações
                        </small>
                    </div>
                </div>

                <!-- Valores do Atributo -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="bi bi-list-ul"></i>
                        <span>Valores do Atributo</span>
                    </div>

                    <div class="info-card-modern">
                        <h6>
                            <i class="bi bi-lightbulb"></i>
                            Como adicionar valores
                        </h6>
                        <p>
                            Adicione todos os valores possíveis para este atributo. Por exemplo, para "Cor": Vermelho, Azul, Verde. 
                            Para "Tamanho": P, M, G, GG. Você pode adicionar quantos valores precisar.
                        </p>
                    </div>

                    <div id="values-container">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p><strong>Nenhum valor adicionado ainda.</strong><br>Selecione o tipo do atributo acima e clique no botão abaixo para adicionar valores.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column align-items-center mt-4 mb-3">
                        <div class="text-center mb-3">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-arrow-down"></i> Clique no botão abaixo para adicionar valores
                            </div>
                        </div>
                        <button type="button" class="btn-add-value-modern" id="add-value-btn" style="min-width: 280px; justify-content: center; padding: 1.25rem 2rem; font-size: 1.1rem;">
                            <i class="bi bi-plus-circle-fill" style="font-size: 1.4rem;"></i>
                            <span>Adicionar Primeiro Valor</span>
                        </button>
                        <small class="text-muted mt-2">
                            <i class="bi bi-info-circle"></i> Você pode adicionar quantos valores precisar
                        </small>
                    </div>
                </div>

                <!-- Ações -->
                <div class="form-section" style="background: #f8fafc;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            Campos marcados com <span class="required">*</span> são obrigatórios
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.attributes.index') }}" class="btn-cancel-modern">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn-submit-modern">
                                <i class="bi bi-check-circle"></i> Criar Atributo
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-modern mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informações
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Tipos de Atributos</h6>
                    <div class="d-flex flex-column gap-2">
                        <div>
                            <strong>Cor:</strong> Exibido como swatches coloridos no frontend
                        </div>
                        <div>
                            <strong>Tamanho:</strong> Exibido como botões (P, M, G)
                        </div>
                        <div>
                            <strong>Texto:</strong> Exibido como botões de texto
                        </div>
                        <div>
                            <strong>Número:</strong> Exibido como dropdown
                        </div>
                        <div>
                            <strong>Imagem:</strong> Exibido como swatches com imagens
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-modern">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-question-circle"></i> Dicas
                </h6>
            </div>
            <div class="card-body">
                <ul class="mb-0 small">
                    <li class="mb-2">Atributos são reutilizáveis em múltiplos produtos</li>
                    <li class="mb-2">Valores podem ser editados depois</li>
                    <li class="mb-2">Use "Valor de Exibição" para nomes mais amigáveis</li>
                    <li>Para cores, sempre preencha o código hexadecimal</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Debug Panel -->
<button type="button" class="debug-toggle" id="debug-toggle" title="Mostrar Debug">
    <i class="bi bi-bug"></i>
</button>
<div class="debug-panel" id="debug-panel">
    <div class="debug-panel-header">
        <h6><i class="bi bi-bug"></i> Debug Console</h6>
        <button type="button" class="btn btn-sm btn-outline-light" id="debug-close">
            <i class="bi bi-x"></i>
        </button>
    </div>
    <div class="debug-panel-content" id="debug-content">
        <div class="debug-item">
            <div class="debug-label">Status:</div>
            <div class="debug-value">Aguardando ação...</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('values-container');
    const addBtn = document.getElementById('add-value-btn');
    const typeSelect = document.getElementById('type');
    const form = document.getElementById('attribute-form');
    const debugPanel = document.getElementById('debug-panel');
    const debugContent = document.getElementById('debug-content');
    const debugToggle = document.getElementById('debug-toggle');
    let valueIndex = 0;

    // Função para adicionar log no debug panel
    function debugLog(message, data = null) {
        console.log(message, data || '');
        const timestamp = new Date().toLocaleTimeString();
        const logItem = document.createElement('div');
        logItem.className = 'debug-item';
        logItem.innerHTML = `
            <div class="debug-label">[${timestamp}] ${message}</div>
            ${data ? `<div class="debug-value">${JSON.stringify(data, null, 2)}</div>` : ''}
        `;
        debugContent.appendChild(logItem);
        debugContent.scrollTop = debugContent.scrollHeight;
    }

    // Toggle debug panel
    debugToggle.addEventListener('click', function() {
        debugPanel.classList.add('active');
    });

    document.getElementById('debug-close').addEventListener('click', function() {
        debugPanel.classList.remove('active');
    });

    debugLog('Sistema inicializado');

    // Atualizar badge de tipo
    function updateTypeBadge() {
        const badgeContainer = document.getElementById('type-badge-container');
        const type = typeSelect.value;
        const badges = {
            color: '<span class="type-badge color"><i class="bi bi-palette"></i> Cor</span>',
            size: '<span class="type-badge size"><i class="bi bi-rulers"></i> Tamanho</span>',
            text: '<span class="type-badge text"><i class="bi bi-type"></i> Texto</span>',
            number: '<span class="type-badge number"><i class="bi bi-123"></i> Número</span>',
            image: '<span class="type-badge image"><i class="bi bi-image"></i> Imagem</span>'
        };
        badgeContainer.innerHTML = type ? badges[type] || '' : '';
    }

    typeSelect.addEventListener('change', function() {
        updateTypeBadge();
        // Se já tiver valores, não fazer nada
        if (container.querySelector('.value-item-modern')) return;
        
        // Se mudou o tipo e não tem valores, atualizar mensagem e destacar botão
        if (this.value && container.querySelector('.empty-state')) {
            const emptyState = container.querySelector('.empty-state p');
            if (emptyState) {
                emptyState.innerHTML = '<strong>Tipo selecionado!</strong><br>Agora clique no botão abaixo para adicionar valores.';
            }
            // Destacar o botão com animação
            const btn = document.getElementById('add-value-btn');
            if (btn) {
                btn.style.animation = 'pulse 2s infinite';
                setTimeout(() => {
                    btn.style.animation = '';
                }, 4000);
            }
        }
    });
    updateTypeBadge();

    function addValueItem(data = {}) {
        const index = valueIndex++;
        const isColor = typeSelect.value === 'color';
        const isImage = typeSelect.value === 'image';
        
        debugLog(`Criando item de valor #${index + 1}`, {
            tipo: typeSelect.value,
            isColor: isColor,
            isImage: isImage,
            data_inicial: data
        });
        
        // Remover empty state se existir
        const emptyState = container.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
            debugLog('Empty state removido');
        }
        
        const html = `
            <div class="value-item-modern" data-index="${index}">
                <div class="value-item-header">
                    <div class="value-item-number">Valor #${index + 1}</div>
                    <button type="button" class="btn-remove-value remove-value" data-index="${index}">
                        <i class="bi bi-trash"></i> Remover
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-modern">
                            <i class="bi bi-tag"></i> Valor <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control-modern" 
                               name="values[${index}][value]" 
                               value="${data.value || ''}" 
                               placeholder="Ex: Vermelho, P, 38" 
                               required
                               autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-modern">
                            <i class="bi bi-eye"></i> Valor de Exibição
                        </label>
                        <input type="text" 
                               class="form-control-modern" 
                               name="values[${index}][display_value]" 
                               value="${data.display_value || ''}" 
                               placeholder="Ex: Pequeno (para P)"
                               autocomplete="off">
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> Opcional. Se vazio, usa o valor acima
                        </small>
                    </div>
                </div>
                
                ${isColor ? `
                <div class="mt-4">
                    <label class="form-label-modern">
                        <i class="bi bi-palette-fill"></i> Cor (Hex) <span class="required">*</span>
                    </label>
                    <div class="color-input-group">
                        <input type="color" 
                               class="color-picker-input" 
                               id="color_picker_${index}"
                               value="${data.color_hex || '#000000'}"
                               title="Selecione a cor">
                        <input type="text" 
                               class="form-control-modern color-hex-input" 
                               name="values[${index}][color_hex]" 
                               id="color_hex_${index}"
                               value="${data.color_hex || '#000000'}" 
                               placeholder="#000000" 
                               pattern="^#[0-9A-Fa-f]{6}$" 
                               required
                               maxlength="7"
                               autocomplete="off">
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle"></i> Clique no quadrado colorido ou digite o código hexadecimal
                    </small>
                </div>
                ` : ''}
                
                ${isImage ? `
                <div class="mt-4">
                    <label class="form-label-modern">
                        <i class="bi bi-image"></i> URL da Imagem
                    </label>
                    <input type="url" 
                           class="form-control-modern" 
                           name="values[${index}][image_url]" 
                           value="${data.image_url || ''}" 
                           placeholder="https://exemplo.com/imagem.jpg"
                           autocomplete="off">
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-link-45deg"></i> Cole a URL completa da imagem
                    </small>
                </div>
                ` : ''}
                
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label-modern">
                            <i class="bi bi-sort-numeric-down"></i> Ordem de Exibição
                        </label>
                        <input type="number" 
                               class="form-control-modern" 
                               name="values[${index}][sort_order]" 
                               value="${data.sort_order ?? index}" 
                               min="0"
                               autocomplete="off">
                        <small class="text-muted d-block mt-1">
                            Menor número aparece primeiro
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        
        // Sincronizar color picker com input text
        if (isColor) {
            const colorPicker = document.getElementById(`color_picker_${index}`);
            const hexInput = document.getElementById(`color_hex_${index}`);
            
            colorPicker.addEventListener('input', function() {
                hexInput.value = this.value;
            });
            
            hexInput.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                    colorPicker.value = this.value;
                }
            });
        }
    }

    addBtn.addEventListener('click', function() {
        debugLog('🔵 Botão "Adicionar Valor" clicado', {
            tipo_selecionado: typeSelect.value,
            valores_existentes: container.querySelectorAll('.value-item-modern').length
        });

        if (typeSelect.value === '') {
            debugLog('❌ Erro: Tipo não selecionado');
            // Adicionar feedback visual
            typeSelect.style.borderColor = '#ef4444';
            typeSelect.focus();
            setTimeout(() => {
                typeSelect.style.borderColor = '';
            }, 2000);
            
            // Mostrar mensagem mais amigável
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-2';
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle"></i> Por favor, selecione o tipo do atributo primeiro.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            typeSelect.parentElement.appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 3000);
            return;
        }
        
        debugLog('✅ Adicionando novo valor...');
        addValueItem();
        
        debugLog('✅ Valor adicionado', {
            total_valores: container.querySelectorAll('.value-item-modern').length
        });
        
        // Scroll suave para o novo item
        setTimeout(() => {
            const lastItem = container.querySelector('.value-item-modern:last-child');
            if (lastItem) {
                lastItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 100);
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-value')) {
            const btn = e.target.closest('.remove-value');
            const index = btn.dataset.index;
            const item = container.querySelector(`[data-index="${index}"]`);
            if (item && confirm('Tem certeza que deseja remover este valor?')) {
                item.remove();
                // Se não tiver mais valores, mostrar empty state
                if (container.children.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p><strong>Nenhum valor adicionado ainda.</strong><br>Selecione o tipo do atributo acima e clique no botão abaixo para adicionar valores.</p>
                        </div>
                    `;
                }
            }
        }
    });

    // Validação antes de enviar
    form.addEventListener('submit', function(e) {
        debugLog('🔵 Form submit iniciado');
        console.log('🔵 [DEBUG] Form submit iniciado');
        
        const values = container.querySelectorAll('.value-item-modern');
        debugLog('Valores encontrados no formulário', { total: values.length });
        console.log('🔵 [DEBUG] Valores encontrados:', values.length);
        
        // Coletar dados do formulário para debug
        const formData = new FormData(form);
        const formDataObj = {};
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
        }
        debugLog('Dados do formulário (FormData)', formDataObj);
        console.log('🔵 [DEBUG] Dados do formulário:', formDataObj);
        
        // Coletar valores para debug
        const valuesData = [];
        values.forEach((item, idx) => {
            const valueInput = item.querySelector('input[name*="[value]"]');
            const displayValueInput = item.querySelector('input[name*="[display_value]"]');
            const colorHexInput = item.querySelector('input[name*="[color_hex]"]');
            const imageUrlInput = item.querySelector('input[name*="[image_url]"]');
            const sortOrderInput = item.querySelector('input[name*="[sort_order]"]');
            
            const valueData = {
                index: idx,
                value: valueInput ? valueInput.value : 'NÃO ENCONTRADO',
                display_value: displayValueInput ? (displayValueInput.value || '(vazio - será usado o value)') : 'NÃO ENCONTRADO',
                color_hex: colorHexInput ? colorHexInput.value : 'N/A',
                image_url: imageUrlInput ? imageUrlInput.value : 'N/A',
                sort_order: sortOrderInput ? sortOrderInput.value : idx
            };
            valuesData.push(valueData);
        });
        debugLog('Dados detalhados dos valores', valuesData);
        console.log('🔵 [DEBUG] Dados dos valores:', valuesData);
        
        // Verificar se display_value é necessário
        const hasEmptyDisplayValues = valuesData.some(v => !v.display_value || v.display_value === '(vazio - será usado o value)');
        if (hasEmptyDisplayValues) {
            debugLog('ℹ️ INFO: Alguns valores não têm display_value. Isso é OK - o sistema usará o "value" automaticamente.');
        }
        
        // Remover validações anteriores
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            if (el.parentElement) el.remove();
        });
        
        if (values.length === 0) {
            debugLog('❌ ERRO: Nenhum valor adicionado');
            console.log('❌ [DEBUG] Erro: Nenhum valor adicionado');
            e.preventDefault();
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger mt-3';
            alertDiv.innerHTML = '<i class="bi bi-exclamation-circle"></i> Por favor, adicione pelo menos um valor para este atributo.';
            addBtn.parentElement.insertBefore(alertDiv, addBtn);
            addBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => alertDiv.remove(), 5000);
            return false;
        }

        // Validar campos obrigatórios
        let hasErrors = false;
        let firstError = null;
        
        values.forEach((item, idx) => {
            const valueInput = item.querySelector('input[name*="[value]"]');
            const colorHexInput = item.querySelector('input[name*="[color_hex]"]');
            
            console.log(`🔵 [DEBUG] Validando valor #${idx + 1}:`, {
                value: valueInput ? valueInput.value : 'NÃO ENCONTRADO',
                color_hex: colorHexInput ? colorHexInput.value : 'N/A'
            });
            
            if (!valueInput || !valueInput.value.trim()) {
                debugLog(`❌ ERRO no valor #${idx + 1}: campo "valor" está vazio`);
                console.log(`❌ [DEBUG] Erro no valor #${idx + 1}: campo "valor" vazio`);
                if (valueInput) {
                    valueInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'Este campo é obrigatório';
                    valueInput.parentElement.appendChild(errorDiv);
                    if (!firstError) firstError = valueInput;
                }
                hasErrors = true;
            }
            
            if (typeSelect.value === 'color' && colorHexInput) {
                if (!colorHexInput.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                    debugLog(`❌ ERRO no valor #${idx + 1}: color_hex inválido`, { valor: colorHexInput.value });
                    console.log(`❌ [DEBUG] Erro no valor #${idx + 1}: color_hex inválido: "${colorHexInput.value}"`);
                    colorHexInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    errorDiv.textContent = 'Digite um código hexadecimal válido (ex: #FF0000)';
                    colorHexInput.parentElement.appendChild(errorDiv);
                    if (!firstError) firstError = colorHexInput;
                    hasErrors = true;
                }
            }
        });

        if (hasErrors) {
            debugLog('❌ Formulário tem erros, não será enviado');
            console.log('❌ [DEBUG] Formulário tem erros, não será enviado');
            e.preventDefault();
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            return false;
        }
        
        debugLog('✅ Validação passou, enviando formulário...');
        console.log('✅ [DEBUG] Validação passou, enviando formulário...');
        
        // Mostrar loading no botão
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Criando...';
        
        debugLog('🔵 Formulário sendo enviado ao servidor...');
        console.log('🔵 [DEBUG] Formulário sendo enviado...');
    });
});
</script>
@endsection
