<style>
    /* Esconder botões de ordenação quando aba Local estiver ativa */
    #local-pane.active ~ * #frete-actions,
    #local-pane.active ~ #frete-actions {
        display: none !important;
    }
    
    /* Mostrar apenas quando Correios estiver ativo E houver resultados */
    #correios-pane.active ~ * #frete-actions,
    #correios-pane.active ~ #frete-actions {
        display: none; /* Será controlado via JavaScript */
    }
    
    /* Por padrão, esconder */
    #frete-actions {
        display: none !important;
    }
</style>

<div class="card border-0 shadow-sm mb-4 shipping-widget" id="shipping-calculator" style="display: block;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="bi bi-truck-fill me-2"></i> 
                <strong style="font-size: 1.5rem; color: var(--secondary-color);">🚚 CALCULE O FRETE</strong>
                <span style="font-size: 0.875rem; color: var(--secondary-color); margin-left: 0.5rem; font-weight: 600;">
                    ⚡ Rápido e Fácil
                </span>
            </h6>
        </div>

        <!-- Abas de seleção de tipo de frete -->
        <ul class="nav nav-tabs mb-3" id="shipping-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="local-tab" data-bs-toggle="tab" data-bs-target="#local-pane" 
                        type="button" role="tab" aria-controls="local-pane" aria-selected="true">
                    <i class="bi bi-geo-alt me-1"></i> Entrega Local
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="correios-tab" data-bs-toggle="tab" data-bs-target="#correios-pane" 
                        type="button" role="tab" aria-controls="correios-pane" aria-selected="false">
                    <i class="bi bi-envelope me-1"></i> Correios
                </button>
            </li>
        </ul>

        <!-- Conteúdo das abas -->
        <div class="tab-content" id="shipping-tab-content">
            <!-- Aba Entrega Local/Regional -->
            <div class="tab-pane fade show active" id="local-pane" role="tabpanel" aria-labelledby="local-tab">
                <div class="alert alert-info py-2 mb-3">
                    <small class="d-block" style="word-wrap: break-word; line-height: 1.4;">
                        <i class="bi bi-info-circle me-1"></i>Entrega realizada pela própria loja na sua região.
                    </small>
                </div>

                <!-- Busca de Bairro/Região -->
                <div class="mb-3">
                    <label for="region-search-local" class="form-label fw-semibold d-block mb-2">
                        <i class="bi bi-search me-1"></i>Buscar seu bairro ou região
                    </label>
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="region-search-local" 
                               placeholder="Digite o nome do seu bairro ou região..."
                               autocomplete="off"
                               style="width: 100%; box-sizing: border-box;">
                        <div class="position-absolute top-100 start-0 w-100 bg-white border rounded-bottom shadow-lg mt-1" 
                             id="region-suggestions" 
                             style="display: none; z-index: 1050; max-height: 300px; overflow-y: auto;">
                            <!-- Sugestões serão inseridas aqui via JavaScript -->
                        </div>
                    </div>
                    <small class="form-text text-muted d-block mt-1" style="word-wrap: break-word;">Ou informe seu CEP abaixo</small>
                </div>

                <!-- Informação da Região Selecionada - DESTAQUE MÁXIMO -->
                <div id="selected-region-info" class="alert alert-success mb-3" style="display: none; border: 3px solid #4caf50 !important; background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%) !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <strong style="font-size: 1.125rem; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="bi bi-check-circle-fill" style="font-size: 1.5rem; color: #2e7d32;"></i>
                                Região selecionada:
                            </strong>
                            <span id="selected-region-name" class="fw-bold" style="font-size: 1.25rem; color: #1b5e20; display: block; margin-top: 0.5rem;"></span>
                            <div class="mt-2">
                                <small style="font-size: 1rem; font-weight: 600; color: #2e7d32;">
                                    <i class="bi bi-clock-fill me-1"></i>Prazo: <span id="selected-region-delivery"></span>
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="h3 mb-0 text-success fw-bold" id="selected-region-price" style="font-size: 2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">R$ 0,00</div>
                            <small style="font-size: 0.875rem; font-weight: 600; color: #2e7d32;">Frete</small>
                        </div>
                    </div>
                </div>

                <!-- CEP e Quantidade (alternativa) - Desabilitados na Entrega Local -->
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-8">
                        <label for="cep-destino-local" class="form-label">
                            <i class="bi bi-geo-alt me-1"></i>Ou informe seu CEP
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="cep-destino-local" 
                               placeholder="00000-000" 
                               inputmode="numeric" 
                               maxlength="9"
                               disabled>
                    </div>
                    <div class="col-4">
                        <label for="qty-shipping-local" class="form-label">Quantidade</label>
                        <input type="number" 
                               class="form-control" 
                               id="qty-shipping-local" 
                               min="1" 
                               value="1"
                               disabled>
                    </div>
                </div>

                <!-- Botão de Calcular -->
                <div class="d-grid">
                    <button class="btn btn-secondary btn-lg" id="btn-calc-frete-local" data-shipping-type="local" style="background: var(--secondary-color); border-color: var(--secondary-color);">
                        <span class="label-default">
                            <i class="bi bi-calculator me-2"></i>Verificar disponibilidade e preço
                        </span>
                        <span class="label-loading d-none">
                            <i class="fas fa-spinner fa-spin me-2"></i>Verificando...
                        </span>
                    </button>
                </div>

                <!-- Mensagem de ajuda -->
                <div class="mt-3">
                    <small class="text-muted d-block text-center" style="line-height: 1.4; word-wrap: break-word; padding: 0 0.5rem;">
                        <i class="bi bi-lightbulb me-1"></i>
                        Encontre seu bairro acima ou informe seu CEP para verificar se fazemos entrega na sua região
                    </small>
                </div>
            </div>

            <!-- Aba Correios (Melhor Envio) -->
            <div class="tab-pane fade" id="correios-pane" role="tabpanel" aria-labelledby="correios-tab">
                @php $sandbox = setting('melhor_envio_sandbox', true); @endphp
                @if($sandbox)
                    <div class="alert alert-warning py-1 mb-2">
                        <small><i class="bi bi-exclamation-triangle me-1"></i>Ambiente de teste (sandbox) — valores podem estar acima do real.</small>
                    </div>
                @endif
                <div class="row g-2 align-items-end">
                    <div class="col-8">
                        <label for="cep-destino" class="form-label">CEP de destino</label>
                        <input type="text" class="form-control" id="cep-destino" placeholder="00000-000" inputmode="numeric" maxlength="9">
                        <div class="form-text">Apenas números (ex.: 74673-030)</div>
                    </div>
                    <div class="col-4">
                        <label for="qty-shipping" class="form-label">Qtd</label>
                        <input type="number" class="form-control" id="qty-shipping" min="1" value="1">
                    </div>
                </div>
                <div class="d-grid mt-3">
                    <button class="btn btn-outline-primary" id="btn-calc-frete" data-shipping-type="correios">
                        <span class="label-default"><i class="bi bi-calculator me-2"></i>Calcular frete</span>
                        <span class="label-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Calculando...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Debug e ações (apenas para Correios) -->
        <button class="btn btn-sm btn-link text-decoration-none mt-2" id="toggle-frete-debug" type="button" style="display: none;">Detalhes técnicos</button>
        <div class="small text-muted" id="frete-debug-panel" style="display:none;"></div>
        <div class="mt-2 d-flex justify-content-between align-items-center gap-2" id="frete-actions" style="display:none !important;" data-shipping-type="correios">
            <div class="btn-group btn-group-sm" role="group" aria-label="Ordenar fretes">
                <button type="button" class="btn btn-outline-secondary active" id="sort-price" aria-pressed="true">Mais barato</button>
                <button type="button" class="btn btn-outline-secondary" id="sort-speed" aria-pressed="false">Mais rápido</button>
            </div>
            <small class="text-muted" id="economy-hint"></small>
        </div>
        
        <!-- Resultados (compartilhado) -->
        <div class="mt-3" id="frete-resultado" style="display:none;"></div>
        <div class="mt-3" id="frete-selecionado" style="display:none;"></div>
    </div>
</div>
