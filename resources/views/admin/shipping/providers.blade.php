@extends('admin.layouts.app')

@section('title', 'Status dos Providers de Frete')

@section('content')
<div class="container-fluid py-4">
    <div class="admin-header-top mb-4">
        <div class="page-header-wrap">
            <div class="page-icon"><i class="bi bi-truck"></i></div>
            <div>
                <h1 class="page-heading">Providers de Frete</h1>
                <p class="page-description">Visualize e altere rapidamente quais motores de frete estão ativos. Overrides prevalecem sobre o config padrão.</p>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span>Status Atual</span>
                <span class="badge bg-primary" id="active_count_badge"></span>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="clearCache()"><i class="bi bi-arrow-clockwise"></i> Limpar Cache</button>
                <button class="btn btn-sm btn-outline-info" onclick="runDiagnose()"><i class="bi bi-activity"></i> Diagnóstico</button>
                <button class="btn btn-sm btn-primary" onclick="saveProviders()"><i class="bi bi-save"></i> Salvar Alterações</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th>Status</th>
                            <th>Fonte</th>
                            <th>Config Padrão</th>
                            <th>Override</th>
                            <th>Ativar?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($providers as $p)
                        <tr id="row_{{ $p['key'] }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $p['icon'] }}"></i>
                                    <strong>{{ $p['label'] }}</strong>
                                    <code class="text-muted" style="font-size:0.65rem">{{ $p['key'] }}</code>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $p['enabled'] ? 'bg-success' : 'bg-secondary' }}" id="status_badge_{{ $p['key'] }}">{{ $p['enabled'] ? 'Ativo' : 'Inativo' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $p['source'] }}</span>
                            </td>
                            <td>{{ $p['config_default'] ? 'true' : 'false' }}</td>
                            <td>{{ $p['override_value'] === null ? '-' : ($p['override_value'] ? '1' : '0') }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggle_{{ $p['key'] }}" {{ $p['enabled'] ? 'checked' : '' }} onchange="updateRowStatus('{{ $p['key'] }}')">
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <small class="text-muted">Se desativar todos os providers não haverá cotação de frete. O cache será invalidado automaticamente ao salvar.</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Operações</span>
            <div>
                <button class="btn btn-outline-warning btn-sm" onclick="opcacheReset()"><i class="bi bi-lightning"></i> Reset OpCache</button>
                <button class="btn btn-outline-primary btn-sm" onclick="toggleQuickTest()"><i class="bi bi-bug"></i> Teste Rápido Melhor Envio</button>
            </div>
        </div>
        <div class="card-body">
            <button class="btn btn-outline-secondary btn-sm" onclick="selectAll(true)"><i class="bi bi-check2-all"></i> Ativar Todos</button>
            <button class="btn btn-outline-secondary btn-sm ms-2" onclick="selectAll(false)"><i class="bi bi-x-circle"></i> Desativar Todos</button>
            <button class="btn btn-outline-danger btn-sm ms-2" onclick="saveProviders()"><i class="bi bi-floppy"></i> Salvar Agora</button>
            <div id="quick_test" class="mt-3" style="display:none">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label">CEP Origem</label>
                        <input id="qt_from" type="text" class="form-control" placeholder="ex.: 01001000" value="{{ preg_replace('/[^0-9]/','', (string) setting('correios_cep_origem')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">CEP Destino</label>
                        <input id="qt_to" type="text" class="form-control" placeholder="ex.: 20040002" value="">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Peso (kg)</label>
                        <input id="qt_weight" type="number" step="0.01" class="form-control" value="{{ config('shipping.defaults.fallback_weight',1.0) }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Comp (cm)</label>
                        <input id="qt_length" type="number" class="form-control" value="{{ config('shipping.defaults.length',20) }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Alt (cm)</label>
                        <input id="qt_height" type="number" class="form-control" value="{{ config('shipping.defaults.height',20) }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Larg (cm)</label>
                        <input id="qt_width" type="number" class="form-control" value="{{ config('shipping.defaults.width',20) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Seguro (R$)</label>
                        <input id="qt_insurance" type="number" step="0.01" class="form-control" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Serviços</label>
                        <input id="qt_services" type="text" class="form-control" placeholder="1,2,3,4,17" value="{{ (string) setting('melhor_envio_service_ids') }}">
                    </div>
                </div>
                <div class="mt-2">
                    <button class="btn btn-sm btn-success" onclick="runQuickTest()"><i class="bi bi-play"></i> Executar Teste</button>
                </div>
                <div id="qt_summary" class="mt-3"></div>
                <pre id="qt_output" class="mt-2 bg-dark text-light p-3" style="white-space:pre-wrap; max-height:300px; overflow:auto;"></pre>
            </div>
        </div>
    </div>
</div>

<script>
function collectProviders(){
    const rows = @json(array_keys($providers));
    const result = {};
    rows.forEach(k => { result[k] = document.getElementById('toggle_'+k).checked ? 1 : 0; });
    return result;
}
function updateRowStatus(key){
    const badge = document.getElementById('status_badge_'+key);
    const active = document.getElementById('toggle_'+key).checked;
    badge.textContent = active ? 'Ativo' : 'Inativo';
    badge.className = 'badge rounded-pill '+(active ? 'bg-success' : 'bg-secondary');
    updateActiveCount();
}
function selectAll(state){
    @foreach($providers as $p)
    document.getElementById('toggle_{{ $p['key'] }}').checked = state;
    updateRowStatus('{{ $p['key'] }}');
    @endforeach
}
async function saveProviders(){
    const payload = { providers: collectProviders() };
    try {
        const resp = await fetch("{{ route('admin.shipping-providers.save') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });
        const data = await resp.json();
        if(!data.ok){ throw new Error(data.message || 'Falha ao salvar'); }
        showToast('Providers atualizados');
    } catch(e){
        alert(e.message);
    }
}
async function clearCache(){
    try {
        const resp = await fetch("{{ route('admin.shipping-providers.clear-cache') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }
        });
        const data = await resp.json();
        if(!data.ok) throw new Error(data.message || 'Falha ao limpar cache');
        showToast('Cache limpo');
    } catch(e){
        alert(e.message);
    }
}
async function runDiagnose(){
    try {
        const resp = await fetch("{{ route('admin.shipping-providers.diagnose') }}", { headers:{'Accept':'application/json'} });
        const data = await resp.json();
        if(!data.ok) throw new Error('Falha no diagnóstico');
        console.log('Diagnóstico Providers', data);
        showToast('Diagnóstico registrado no log');
    } catch(e){ alert(e.message); }
}
async function opcacheReset(){
    try {
        const resp = await fetch("{{ route('admin.shipping-providers.opcache-reset') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }
        });
        const data = await resp.json();
        if(!data.ok) throw new Error(data.message || 'Falha ao resetar OpCache');
        showToast('OpCache reset acionado');
    } catch(e){ alert(e.message); }
}
function toggleQuickTest(){
    const el = document.getElementById('quick_test');
    if(!el) return; el.style.display = el.style.display==='none' ? 'block' : 'none';
}
async function runQuickTest(){
    const payload = {
        from_cep: document.getElementById('qt_from').value,
        to_cep: document.getElementById('qt_to').value,
        weight: document.getElementById('qt_weight').value,
        length: document.getElementById('qt_length').value,
        height: document.getElementById('qt_height').value,
        width: document.getElementById('qt_width').value,
        insurance_value: document.getElementById('qt_insurance').value,
        services: document.getElementById('qt_services').value,
    };
    const out = document.getElementById('qt_output');
    const sum = document.getElementById('qt_summary');
    out.textContent = 'Executando teste...';
    sum.innerHTML = '';
    try {
        const resp = await fetch("{{ route('admin.shipping-providers.test-me') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });
        const data = await resp.json();
        out.textContent = JSON.stringify(data, null, 2);
        renderQuickTestSummary(data);
        showToast('Teste concluído');
    } catch(e){
        out.textContent = 'Erro: '+e.message;
    }
}

function renderQuickTestSummary(data){
    const sum = document.getElementById('qt_summary');
    if(!sum) return;
    const probe = Array.isArray(data?.probe) ? data.probe : [];
    const count = typeof data?.count === 'number' ? data.count : 0;
    const firstErr = data?.first_error || null;

    const statusBadge = (st, ctype) => {
        if (st == null) return '<span class="badge bg-secondary">n/a</span>';
        if (st >= 200 && st < 300) {
            const isJson = (ctype||'').toLowerCase().includes('json');
            return `<span class="badge ${isJson?'bg-success':'bg-warning text-dark'}">${st} ${isJson?'JSON':'OK'}</span>`;
        }
        if (st >= 400) return `<span class="badge bg-danger">${st}</span>`;
        return `<span class="badge bg-secondary">${st}</span>`;
    };

    let rows = probe.map(p => {
        const host = (p.host||'').replace(/</g,'&lt;');
        const ip = (p.dns_ip||'').replace(/</g,'&lt;');
        const ctype = (p.content_type||'').replace(/</g,'&lt;');
        const body = (p.body_snippet||'').toString().slice(0,120).replace(/</g,'&lt;');
        const dnsCount = Array.isArray(p.dns_records) ? p.dns_records.length : 0;
        const err = (p.error||'').replace(/</g,'&lt;');
        const meBadge = (p.me_post_status==null) ? '<span class="badge bg-secondary">n/a</span>' : ((p.me_post_status>=200&&p.me_post_status<300) ? '<span class="badge bg-success">'+p.me_post_status+'</span>' : '<span class="badge bg-danger">'+p.me_post_status+'</span>');
        const meBody = (p.me_body_snippet||'').toString().slice(0,80).replace(/</g,'&lt;');
        return `<tr>
            <td><code>${host}</code></td>
            <td>${ip || '-'}</td>
            <td>${statusBadge(p.get_status, ctype)}</td>
            <td><small>${ctype||'-'}</small></td>
            <td><small>${body|| (err?('Erro: '+err):'-')}</small></td>
            <td><small>${dnsCount>0? dnsCount+' DNS' : '-'}</small></td>
        </tr>
        <tr class="table-light">
            <td colspan="2" class="text-end"><small>POST /api/v2/me/shipment/calculate</small></td>
            <td>${meBadge}</td>
            <td colspan="3"><small>${meBody || (p.me_error?('Erro: '+(p.me_error||'')):'-')}</small></td>
        </tr>`;
    }).join('');
    if (!rows) rows = '<tr><td colspan="6" class="text-muted">Sem dados de probe</td></tr>';

    const quotesBadge = count>0
        ? `<span class="badge bg-success">${count} opção(ões)</span>`
        : `<span class="badge bg-danger">sem opções</span>`;
    const errorBadge = firstErr ? `<span class="badge bg-danger">${(firstErr||'').toString().slice(0,80)}</span>` : '';

    sum.innerHTML = `
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead><tr>
                    <th>Host</th><th>DNS IP</th><th>GET /shipment/calculate</th><th>Content-Type</th><th>Snippet/Erro</th><th>DNS</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
        <div class="mt-2">
            <strong>Resultado:</strong> ${quotesBadge} ${errorBadge}
        </div>
    `;
}
function showToast(msg){
    const div = document.createElement('div');
    div.className='position-fixed top-0 end-0 p-3';
    div.style.zIndex=2000;
    div.innerHTML = `<div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>`;
    document.body.appendChild(div);
    setTimeout(()=>div.remove(),3500);
}
function updateActiveCount(){
    const keys = @json(array_keys($providers));
    let count = 0; keys.forEach(k => { if(document.getElementById('toggle_'+k).checked) count++; });
    document.getElementById('active_count_badge').textContent = count + ' ativo(s)';
}
updateActiveCount();
</script>
@endsection