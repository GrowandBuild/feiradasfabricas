<div class="card border-0 shadow-sm mb-4 shipping-widget" id="shipping-calculator" style="display: block;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0"><i class="bi bi-truck me-2"></i> Calcule o frete</h6>
            <small class="text-muted">via Melhor Envio</small>
        </div>
        @php $sandbox = setting('melhor_envio_sandbox', true); @endphp
        @if($sandbox)
            <div class="alert alert-warning py-1 mb-2"><small><i class="bi bi-exclamation-triangle me-1"></i>Ambiente de teste (sandbox) — valores podem estar acima do real.</small></div>
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
            <button class="btn btn-outline-primary" id="btn-calc-frete">
                <span class="label-default"><i class="bi bi-calculator me-2"></i>Calcular frete</span>
                <span class="label-loading d-none"><i class="fas fa-spinner fa-spin me-2"></i>Calculando...</span>
            </button>
        </div>
        <button class="btn btn-sm btn-link text-decoration-none mt-2" id="toggle-frete-debug" type="button">Detalhes técnicos</button>
        <div class="small text-muted" id="frete-debug-panel" style="display:none;"></div>
        <div class="mt-2 d-flex justify-content-between align-items-center gap-2" id="frete-actions" style="display:none;">
            <div class="btn-group btn-group-sm" role="group" aria-label="Ordenar fretes">
                <button type="button" class="btn btn-outline-secondary active" id="sort-price" aria-pressed="true">Mais barato</button>
                <button type="button" class="btn btn-outline-secondary" id="sort-speed" aria-pressed="false">Mais rápido</button>
            </div>
            <small class="text-muted" id="economy-hint"></small>
        </div>
        <div class="mt-3" id="frete-resultado" style="display:none;"></div>
        <div class="mt-3" id="frete-selecionado" style="display:none;"></div>
    </div>
</div>
