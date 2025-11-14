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
        <div class="card-header">Operações</div>
        <div class="card-body">
            <button class="btn btn-outline-secondary btn-sm" onclick="selectAll(true)"><i class="bi bi-check2-all"></i> Ativar Todos</button>
            <button class="btn btn-outline-secondary btn-sm ms-2" onclick="selectAll(false)"><i class="bi bi-x-circle"></i> Desativar Todos</button>
            <button class="btn btn-outline-danger btn-sm ms-2" onclick="saveProviders()"><i class="bi bi-floppy"></i> Salvar Agora</button>
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
        console.log('Diagnóstico Providers', data.diagnose);
        showToast('Diagnóstico registrado no log');
    } catch(e){ alert(e.message); }
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