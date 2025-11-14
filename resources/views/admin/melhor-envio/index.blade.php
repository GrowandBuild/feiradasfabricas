@extends('admin.layouts.app')

@section('title', 'Melhor Envio')
@section('page-icon', 'bi bi-truck')
@section('page-title', 'Melhor Envio')
@section('page-description')
Configuração simples do frete via Melhor Envio. Informe um Token (recomendado) ou, opcionalmente, Client ID/Secret. Escolha Sandbox para testes.
@endsection

@section('content')
<div class="row g-4">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span>Configurações</span>
        <span class="badge {{ $enabled ? 'bg-success' : 'bg-secondary' }}">{{ $enabled ? 'Ativo' : 'Inativo' }}</span>
      </div>
      <div class="card-body">
        <form id="me-form">
          @csrf
          <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox" id="me_enabled" name="melhor_envio_enabled" {{ $enabled ? 'checked' : '' }}>
            <label class="form-check-label" for="me_enabled">Ativar Melhor Envio</label>
          </div>

          <div class="mb-3">
            <label for="me_token" class="form-label">Token (Bearer)</label>
            <input type="text" class="form-control" id="me_token" name="melhor_envio_token" value="{{ $token }}" placeholder="Cole seu token aqui">
            <div class="form-text">Recomendado. Você pode gerar e colar um Token de acesso. Alternativamente, use Client ID/Secret (aba avançada).</div>
          </div>

          <div class="row g-3">
            <div class="col-sm-6">
              <label for="me_sandbox" class="form-label">Ambiente</label>
              <select class="form-select" id="me_sandbox" name="melhor_envio_sandbox">
                <option value="1" {{ $sandbox ? 'selected' : '' }}>Sandbox (Teste)</option>
                <option value="0" {{ !$sandbox ? 'selected' : '' }}>Produção</option>
              </select>
            </div>
            <div class="col-sm-6">
              <label for="me_cep_origem" class="form-label">CEP de Origem</label>
              <input type="text" class="form-control" id="me_cep_origem" name="correios_cep_origem" value="{{ $cep_origem }}" placeholder="Ex.: 01001-000" maxlength="9">
              <div class="form-text">Utilizado como origem para as cotações.</div>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label for="me_services" class="form-label">Serviços (IDs) opcionais</label>
            <input type="text" class="form-control" id="me_services" name="melhor_envio_service_ids" value="{{ $service_ids }}" placeholder="Ex.: 1,2,3,4,17">
            <div class="form-text">Deixe em branco para usar os padrões. Separe por vírgulas.</div>
          </div>

          <div class="border rounded p-3 mb-3 bg-light">
            <h6 class="mb-2">Padrões de Peso e Dimensões (fallback)</h6>
            <div class="row g-3">
              <div class="col-6 col-md-3">
                <label class="form-label">Peso (kg)</label>
                <input type="number" step="0.01" min="0.01" class="form-control" name="shipping_default_weight" value="{{ $default_weight }}">
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Comprimento (cm)</label>
                <input type="number" min="1" class="form-control" name="shipping_default_length" value="{{ $default_length }}">
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Altura (cm)</label>
                <input type="number" min="1" class="form-control" name="shipping_default_height" value="{{ $default_height }}">
              </div>
              <div class="col-6 col-md-3">
                <label class="form-label">Largura (cm)</label>
                <input type="number" min="1" class="form-control" name="shipping_default_width" value="{{ $default_width }}">
              </div>
            </div>
            <div class="form-text mt-2">Usados automaticamente quando o produto não tiver valores próprios definidos. Ajudam a evitar falha na cotação.</div>
          </div>

          <details class="mb-3">
            <summary class="mb-2"><strong>Avançado (opcional) – Client ID/Secret</strong></summary>
            <div class="row g-3">
              <div class="col-sm-6">
                <label for="me_client_id" class="form-label">Client ID</label>
                <input type="text" class="form-control" id="me_client_id" name="melhor_envio_client_id" value="{{ $client_id }}">
              </div>
              <div class="col-sm-6">
                <label for="me_client_secret" class="form-label">Client Secret</label>
                <input type="password" class="form-control" id="me_client_secret" name="melhor_envio_client_secret" value="{{ $client_secret }}">
              </div>
            </div>
          </details>

          <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" id="btnSave">
              <i class="bi bi-save"></i> Salvar
            </button>
            <a href="{{ route('admin.melhor-envio.authorize') }}" class="btn btn-outline-secondary">
              <i class="bi bi-key"></i> Autorizar conta
            </a>
            <button type="button" class="btn btn-outline-primary" id="btnTest">
              <span class="spinner-border spinner-border-sm me-2 d-none" id="testSpinner"></span>
              <i class="bi bi-plug"></i> Testar conexão
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card">
      <div class="card-header">Como funciona</div>
      <div class="card-body">
        <ul class="mb-3">
          <li>Ative o provider e informe um <strong>Token</strong> válido.</li>
          <li>Escolha o <strong>Ambiente</strong> (Sandbox para teste).</li>
          <li>Defina um <strong>CEP de Origem</strong> válido.</li>
          <li>Opcional: especifique <strong>IDs de Serviços</strong> (ou deixe padrão).</li>
        </ul>
        <p class="small text-muted mb-0">Dica: após salvar, use a calculadora de frete em uma página de produto e digite um CEP para ver as opções retornadas pelo Melhor Envio.</p>
      </div>
    </div>
    <div class="card mt-3">
      <div class="card-header">Resultado do teste</div>
      <div class="card-body">
        <div id="testResult" class="small text-muted">Nenhum teste executado ainda.</div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
  const form = document.getElementById('me-form');
  const btnSave = document.getElementById('btnSave');
  const btnTest = document.getElementById('btnTest');
  const testSpinner = document.getElementById('testSpinner');
  const testResult = document.getElementById('testResult');
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  // mask CEP
  const cep = document.getElementById('me_cep_origem');
  if(cep){
    cep.addEventListener('input', ()=>{
      cep.value = cep.value.replace(/[^0-9]/g,'').slice(0,8).replace(/(\d{5})(\d{0,3})/, (m,a,b)=> b?`${a}-${b}`:a);
    });
  }

  btnSave.addEventListener('click', async ()=>{
    const fd = new FormData(form);
    // Switch fields send 'on' – convert to boolean
    fd.set('melhor_envio_enabled', document.getElementById('me_enabled').checked ? '1' : '0');

    try{
      const res = await fetch("{{ route('admin.melhor-envio.save') }}", {
        method: 'POST',
        headers: { 
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: fd
      });
      const j = await res.json().catch(()=>({success:false,message:'Erro ao salvar'}));
      if(j.success){
        showToast('Configurações salvas com sucesso.', 'success');
      } else {
        showToast(j.message||'Falha ao salvar', 'danger');
      }
    }catch(e){
      showToast('Erro ao salvar: '+e.message, 'danger');
    }
  });

  btnTest.addEventListener('click', async ()=>{
    testSpinner.classList.remove('d-none');
    testResult.textContent = 'Testando...';
    try{
      const res = await fetch("{{ route('admin.melhor-envio.test') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' }
      });
      const j = await res.json();
      if(j.success){
        showToast(j.message||'Conexão OK', 'success');
        const acc = j.account? `Conta: ${j.account.name||'-'} (${j.account.email||'-'})` : '';
        testResult.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> ${j.message}</span><br>${acc}`;
      } else {
        showToast(j.message||'Falha no teste', 'danger');
        testResult.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle"></i> ${j.message||'Falha no teste'}</span>`;
      }
    }catch(e){
      showToast('Erro no teste: '+e.message, 'danger');
      testResult.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle"></i> Erro no teste: ${e.message}</span>`;
    }finally{
      testSpinner.classList.add('d-none');
    }
  });

  function showToast(msg, type){
    const el = document.createElement('div');
    el.className = `alert alert-${type} mt-3`;
    el.innerHTML = `<i class="bi bi-info-circle me-2"></i>${msg}`;
    document.querySelector('.container-fluid.p-4').prepend(el);
    setTimeout(()=> el.remove(), 4000);
  }
})();
</script>
@endsection
