@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="#identity" class="list-group-item list-group-item-action active">Identidade Visual</a>
                <a href="#general" class="list-group-item list-group-item-action">Configurações Gerais</a>
                <a href="#integrations" class="list-group-item list-group-item-action">Integrações</a>
            </div>
        </div>
        <div class="col-md-9">
            <div id="settings-alert" style="display:none; margin-bottom:12px;"></div>

            <div id="identity" class="card mb-3">
                <div class="card-header">Identidade Visual</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nome do site</label>
                        <input type="text" id="site_name" class="form-control" value="{{ setting('site_name', config('app.name', 'Feira')) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição (meta)</label>
                        <textarea id="site_description" class="form-control" rows="3">{{ setting('site_description', '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo do site</label>
                        <div class="d-flex align-items-center gap-3">
                            @php $logo = setting('site_logo'); @endphp
                            <img id="preview_site_logo" src="{{ $logo ? asset('storage/' . $logo) : asset('logo-ofc.svg') }}" alt="Logo" style="height:64px; object-fit:contain; background:#fff; padding:8px; border:1px solid #e9ecef;">
                            <div>
                                <button type="button" class="btn btn-secondary" id="openLogoModal">Substituir (abrir modal)</button>
                                <div class="form-text">Também é possível alterar por departamento nas páginas de departamento.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Favicon</label>
                        <div class="d-flex gap-3 align-items-center">
                            @php $favicon = setting('site_favicon'); @endphp
                            <img id="preview_favicon" src="{{ $favicon ? asset('storage/' . $favicon) : asset('favicon_io/favicon-32x32.png') }}" alt="Favicon" style="height:48px; width:48px; object-fit:contain; background:#fff; padding:6px; border:1px solid #e9ecef;">
                            <div>
                                <input type="file" id="faviconInput" accept="image/*,.ico" class="form-control-file">
                                <small class="form-text text-muted">Tamanhos recomendados: 32x32, 48x48. Tipos: png, ico, svg, webp.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">App Icon (ícone para instalação / homescreen)</label>
                        <div class="d-flex gap-3 align-items-center">
                            @php $appIcon = setting('site_app_icon'); @endphp
                            <img id="preview_app_icon" src="{{ $appIcon ? asset('storage/' . $appIcon) : asset('favicon_io/apple-touch-icon.png') }}" alt="App Icon" style="height:72px; width:72px; object-fit:contain; background:#fff; padding:6px; border:1px solid #e9ecef;">
                            <div>
                                <input type="file" id="appIconInput" accept="image/*" class="form-control-file">
                                <small class="form-text text-muted">Envie um PNG quadrado (recomendado 512x512). Será usado como apple-touch-icon e para instalação PWA.</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button id="saveIdentity" class="btn btn-primary">Salvar Identidade</button>
                        <button id="resetIdentity" class="btn btn-outline-secondary">Cancelar</button>
                    </div>
                </div>
            </div>

            <div id="general" class="card mb-3" style="display:none;">
                <div class="card-header">Configurações Gerais</div>
                <div class="card-body">
                    <p>Outras configurações podem ser editadas aqui em breve.</p>
                </div>
            </div>

            <div id="integrations" class="card mb-3" style="display:none;">
                <div class="card-header">Integrações</div>
                <div class="card-body">
                    <p>OAuth e provedores aparecem aqui.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Tabs
    document.querySelectorAll('.list-group a').forEach(function(a){
        a.addEventListener('click', function(e){
            e.preventDefault();
            document.querySelectorAll('.list-group a').forEach(x=>x.classList.remove('active'));
            a.classList.add('active');
            document.querySelectorAll('#identity, #general, #integrations').forEach(s=>s.style.display='none');
            const id = a.getAttribute('href').substring(1);
            document.getElementById(id).style.display = '';
        });
    });

    const csrftoken = '{{ csrf_token() }}';

    // Open existing modal if available
    document.getElementById('openLogoModal')?.addEventListener('click', function(){
        const btn = document.getElementById('admin-logo-link');
        if (btn) { btn.click(); }
        else {
            // fallback: show file picker to update logo inline
            alert('Modal de logo não encontrado. Use o formulário de departamento ou carregue manualmente.');
        }
    });

    // Save site name/description via PUT JSON
    document.getElementById('saveIdentity').addEventListener('click', function(){
        const payload = {
            site_name: document.getElementById('site_name').value,
            site_description: document.getElementById('site_description').value
        };
        fetch('{{ route('admin.settings.update') }}', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrftoken, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        }).then(r => r.json()).then(data => {
            if (data && data.success) {
                showAlert('Identidade salva.', 'success');
                setTimeout(()=> location.reload(), 700);
            } else {
                showAlert(data.message || 'Erro ao salvar', 'danger');
            }
        }).catch(err => showAlert(err.message || 'Erro de rede', 'danger'));
    });

    function showAlert(msg, type){
        const cont = document.getElementById('settings-alert');
        cont.style.display = '';
        cont.className = 'alert alert-' + (type === 'success' ? 'success' : (type === 'danger' ? 'danger' : 'secondary'));
        cont.textContent = msg;
        setTimeout(()=>{ cont.style.display = 'none'; }, 5000);
    }

    // Favicon upload
    const favInput = document.getElementById('faviconInput');
    favInput?.addEventListener('change', function(){
        const file = this.files && this.files[0];
        if (!file) return;
        const fd = new FormData();
        fd.append('favicon', file);
        fetch('{{ route('admin.settings.upload-favicon') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrftoken },
            body: fd,
            credentials: 'same-origin'
        }).then(r => r.json()).then(data => {
            if (data && data.success) {
                const img = document.getElementById('preview_favicon');
                img.src = data.url + '?_=' + Date.now();
                showAlert('Favicon atualizado.', 'success');
            } else if (data && data.errors) {
                showAlert(Object.values(data.errors).flat().join('; '), 'danger');
            } else {
                showAlert(data.message || 'Erro ao enviar favicon', 'danger');
            }
        }).catch(e => showAlert('Erro de rede ao enviar favicon', 'danger'));
    });

    // App Icon upload
    const appIconInput = document.getElementById('appIconInput');
    appIconInput?.addEventListener('change', function(){
        const file = this.files && this.files[0];
        if (!file) return;
        const fd = new FormData();
        fd.append('app_icon', file);
        fetch('{{ route('admin.settings.upload-app-icon') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrftoken },
            body: fd,
            credentials: 'same-origin'
        }).then(r => r.json()).then(data => {
            if (data && data.success) {
                const img = document.getElementById('preview_app_icon');
                img.src = data.url + '?_=' + Date.now();
                showAlert('App icon atualizado.', 'success');
            } else if (data && data.errors) {
                showAlert(Object.values(data.errors).flat().join('; '), 'danger');
            } else {
                showAlert(data.message || 'Erro ao enviar app icon', 'danger');
            }
        }).catch(e => showAlert('Erro de rede ao enviar app icon', 'danger'));
    });
});
</script>
@endpush

@endsection
    @extends('admin.layouts.app')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')
@section('page-subtitle')
    <p class="text-muted mb-0">Central de integração de APIs e configurações gerais</p>
@endsection

@section('content')
<div class="row">
    <!-- Central de APIs -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-plug me-2" style="color: var(--accent-color);"></i>
                <h5 class="card-title mb-0">Central de Integração de APIs</h5>
            </div>
            <div class="card-body">
                <!-- Tabs de APIs -->
                <ul class="nav nav-tabs mb-4" id="apiTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                            <i class="bi bi-credit-card me-2"></i>Pagamentos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="logistica-tab" data-bs-toggle="tab" data-bs-target="#logistica" type="button" role="tab">
                            <i class="bi bi-truck me-2"></i>Logística
                        </button>
                    </li>
                    
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="bi bi-gear me-2"></i>Gerais
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fiscal-tab" data-bs-toggle="tab" data-bs-target="#fiscal" type="button" role="tab">
                            <i class="bi bi-receipt me-2"></i>SEFAZ/NFe
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                            <i class="bi bi-envelope me-2"></i>Emails Automáticos
                        </button>
                    </li>
                </ul>

                <!-- Conteúdo das Tabs -->
                <div class="tab-content" id="apiTabsContent">
                    <!-- Tab Pagamentos -->
                    <div class="tab-pane fade show active" id="payment" role="tabpanel">
                        <div class="row">
                            <!-- Stripe -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-credit-card text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Stripe</h6>
                                                <small class="text-muted">Gateway de pagamento internacional</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="stripe_enabled" 
                                                       {{ setting('stripe_enabled', false) ? 'checked' : '' }}
                                                       onchange="updateStatusText('stripe_enabled')">
                                                <label class="form-check-label" for="stripe_enabled" id="stripe_enabled_label">
                                                    {{ setting('stripe_enabled', false) ? 'Ativo' : 'Inativo' }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_public_key" class="form-label">Chave Pública</label>
                                            <input type="text" class="form-control" id="stripe_public_key" 
                                                   value="{{ setting('stripe_public_key', '') }}" placeholder="pk_test_...">
                                        </div>
                                        <div class="mb-3">
                                            <label for="stripe_secret_key" class="form-label">Chave Secreta</label>
                                            <input type="password" class="form-control" id="stripe_secret_key" 
                                                   value="{{ setting('stripe_secret_key', '') }}" placeholder="sk_test_...">
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="savePaymentConfig('stripe')">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm ms-2" onclick="testPaymentConnection('stripe')">
                                            <i class="bi bi-wifi me-1"></i>Testar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- PagSeguro -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-shield-check text-success fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">PagSeguro</h6>
                                                <small class="text-muted">Gateway de pagamento brasileiro</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="pagseguro_enabled" 
                                                       {{ setting('pagseguro_enabled', false) ? 'checked' : '' }}
                                                       onchange="updateStatusText('pagseguro_enabled')">
                                                <label class="form-check-label" for="pagseguro_enabled" id="pagseguro_enabled_label">
                                                    {{ setting('pagseguro_enabled', false) ? 'Ativo' : 'Inativo' }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pagseguro_email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="pagseguro_email" 
                                                   value="{{ setting('pagseguro_email', '') }}" placeholder="seu@email.com">
                                        </div>
                                        <div class="mb-3">
                                            <label for="pagseguro_token" class="form-label">Token</label>
                                            <input type="password" class="form-control" id="pagseguro_token" 
                                                   value="{{ setting('pagseguro_token', '') }}" placeholder="Token do PagSeguro">
                                        </div>
                                        <div class="mb-3">
                                            <label for="pagseguro_sandbox" class="form-label">Ambiente</label>
                                            <select class="form-select" id="pagseguro_sandbox">
                                                <option value="1" {{ setting('pagseguro_sandbox', true) ? 'selected' : '' }}>Sandbox (Teste)</option>
                                                <option value="0" {{ !setting('pagseguro_sandbox', true) ? 'selected' : '' }}>Produção</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-success btn-sm" onclick="savePaymentConfig('pagseguro')">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm ms-2" onclick="testPaymentConnection('pagseguro')">
                                            <i class="bi bi-wifi me-1"></i>Testar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- PayPal -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-paypal text-info fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">PayPal</h6>
                                                <small class="text-muted">Gateway de pagamento global</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="paypal_enabled" 
                                                       {{ setting('paypal_enabled', false) ? 'checked' : '' }}
                                                       onchange="updateStatusText('paypal_enabled')">
                                                <label class="form-check-label" for="paypal_enabled" id="paypal_enabled_label">
                                                    {{ setting('paypal_enabled', false) ? 'Ativo' : 'Inativo' }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="paypal_client_id" class="form-label">Client ID</label>
                                            <input type="text" class="form-control" id="paypal_client_id" 
                                                   value="{{ setting('paypal_client_id', '') }}" placeholder="Client ID do PayPal">
                                        </div>
                                        <div class="mb-3">
                                            <label for="paypal_client_secret" class="form-label">Client Secret</label>
                                            <input type="password" class="form-control" id="paypal_client_secret" 
                                                   value="{{ setting('paypal_client_secret', '') }}" placeholder="Client Secret do PayPal">
                                        </div>
                                        <div class="mb-3">
                                            <label for="paypal_sandbox" class="form-label">Ambiente</label>
                                            <select class="form-select" id="paypal_sandbox">
                                                <option value="1" {{ setting('paypal_sandbox', true) ? 'selected' : '' }}>Sandbox (Teste)</option>
                                                <option value="0" {{ !setting('paypal_sandbox', true) ? 'selected' : '' }}>Produção</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-info btn-sm" onclick="savePaymentConfig('paypal')">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm ms-2" onclick="testPaymentConnection('paypal')">
                                            <i class="bi bi-wifi me-1"></i>Testar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Mercado Pago -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-currency-exchange text-warning fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Mercado Pago</h6>
                                                <small class="text-muted">Gateway de pagamento latino-americano</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="mercadopago_enabled" 
                                                       {{ setting('mercadopago_enabled', false) ? 'checked' : '' }}
                                                       onchange="updateStatusText('mercadopago_enabled')">
                                                <label class="form-check-label" for="mercadopago_enabled" id="mercadopago_enabled_label">
                                                    {{ setting('mercadopago_enabled', false) ? 'Ativo' : 'Inativo' }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="mercadopago_public_key" class="form-label">Chave Pública</label>
                                            <input type="text" class="form-control" id="mercadopago_public_key" 
                                                   value="{{ setting('mercadopago_public_key', '') }}" placeholder="APP_USR-...">
                                        </div>
                                        <div class="mb-3">
                                            <label for="mercadopago_access_token" class="form-label">Access Token</label>
                                            <input type="password" class="form-control" id="mercadopago_access_token" 
                                                   value="{{ setting('mercadopago_access_token', '') }}" placeholder="Access Token do Mercado Pago">
                                        </div>
                                        <div class="mb-3">
                                            <label for="mercadopago_sandbox" class="form-label">Ambiente</label>
                                            <select class="form-select" id="mercadopago_sandbox">
                                                <option value="1" {{ setting('mercadopago_sandbox', true) ? 'selected' : '' }}>Sandbox (Teste)</option>
                                                <option value="0" {{ !setting('mercadopago_sandbox', true) ? 'selected' : '' }}>Produção</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-warning btn-sm" onclick="savePaymentConfig('mercadopago')">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm ms-2" onclick="testPaymentConnection('mercadopago')">
                                            <i class="bi bi-wifi me-1"></i>Testar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Logística (apenas configurações; frete segue desativado no front) -->
                    <div class="tab-pane fade" id="logistica" role="tabpanel">
                        <div class="row">
                            <!-- Melhor Envio -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-box2-heart text-secondary fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Melhor Envio</h6>
                                                <small class="text-muted">Credenciais e autorização OAuth (somente painel)</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="melhor_envio_enabled" 
                                                       {{ setting('melhor_envio_enabled', false) ? 'checked' : '' }}
                                                       onchange="updateStatusText('melhor_envio_enabled')">
                                                <label class="form-check-label" for="melhor_envio_enabled" id="melhor_envio_enabled_label">
                                                    {{ setting('melhor_envio_enabled', false) ? 'Ativo' : 'Inativo' }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="melhor_envio_client_id" class="form-label">Client ID</label>
                                            <input type="text" class="form-control" id="melhor_envio_client_id" 
                                                   value="{{ setting('melhor_envio_client_id', '') }}" placeholder="Client ID do Melhor Envio">
                                        </div>
                                        <div class="mb-3">
                                            <label for="melhor_envio_client_secret" class="form-label">Client Secret</label>
                                            <input type="password" class="form-control" id="melhor_envio_client_secret" 
                                                   value="{{ setting('melhor_envio_client_secret', '') }}" placeholder="Client Secret do Melhor Envio">
                                        </div>
                                        <div class="mb-3">
                                            <label for="melhor_envio_sandbox" class="form-label">Ambiente</label>
                                            <select class="form-select" id="melhor_envio_sandbox">
                                                <option value="1" {{ setting('melhor_envio_sandbox', true) ? 'selected' : '' }}>Sandbox (Teste)</option>
                                                <option value="0" {{ !setting('melhor_envio_sandbox', true) ? 'selected' : '' }}>Produção</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="melhor_envio_cep_origem" class="form-label">CEP de Origem (opcional)</label>
                                            <input type="text" class="form-control" id="melhor_envio_cep_origem" 
                                                   value="{{ setting('melhor_envio_cep_origem', '') }}" placeholder="00000-000">
                                        </div>
                                        <div class="mb-3">
                                            <label for="melhor_envio_service_ids" class="form-label">Serviços (IDs separados por vírgula, opcional)</label>
                                            <input type="text" class="form-control" id="melhor_envio_service_ids" 
                                                   value="{{ is_array(setting('melhor_envio_service_ids')) ? implode(',', setting('melhor_envio_service_ids')) : (setting('melhor_envio_service_ids','')) }}" placeholder="1,2,3">
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="melhor_envio_declared_mode" class="form-label">Modo Valor Declarado</label>
                                                @php $declaredMode = setting('melhor_envio_declared_mode','none'); @endphp
                                                <select id="melhor_envio_declared_mode" class="form-select">
                                                    <option value="none" {{ $declaredMode==='none' ? 'selected' : '' }}>Sem seguro (mínimo)</option>
                                                    <option value="cost" {{ $declaredMode==='cost' ? 'selected' : '' }}>Custo</option>
                                                    <option value="cap" {{ $declaredMode==='cap' ? 'selected' : '' }}>Teto</option>
                                                    <option value="full" {{ $declaredMode==='full' ? 'selected' : '' }}>Preço cheio</option>
                                                </select>
                                                <div class="form-text">Dica: use "Sem seguro" ou "Teto" baixo para fretes mais baratos. "Preço cheio" encarece bastante.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="melhor_envio_declared_cap" class="form-label">Teto Valor Declarado (R$)</label>
                                                <input type="number" step="0.01" min="0" class="form-control" id="melhor_envio_declared_cap" value="{{ setting('melhor_envio_declared_cap',80) }}">
                                                <div class="form-text">Usado quando modo = Teto</div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            @php $hasToken = !empty(setting('melhor_envio_token', '')); @endphp
                                            @if($hasToken)
                                                <div class="alert alert-success py-2">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Conectado via OAuth. <small>Token salvo com segurança.</small>
                                                </div>
                                            @else
                                                <div class="alert alert-warning py-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    Não conectado. Autorize para obter o token de acesso.
                                                </div>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button class="btn btn-secondary btn-sm" onclick="saveDeliveryConfig('melhor_envio')">
                                                <i class="bi bi-check-lg me-1"></i>Salvar
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" onclick="testDeliveryConnection('melhor_envio')">
                                                <i class="bi bi-wifi me-1"></i>Testar
                                            </button>
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.settings.melhor-envio.authorize') }}">
                                                <i class="bi bi-shield-lock me-1"></i>Conectar (OAuth)
                                            </a>
                                            @if($hasToken)
                                                <button class="btn btn-outline-danger btn-sm" onclick="revokeMelhorEnvioTokens()">
                                                    <i class="bi bi-x-circle me-1"></i>Desconectar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Tab Configurações Gerais -->
                    <div class="tab-pane fade" id="general" role="tabpanel">
                        <div class="row">
                            <!-- Configurações do Site -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-globe me-2"></i>Configurações do Site</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Nome do Site</label>
                                            <input type="text" class="form-control" id="site_name" 
                                                   value="{{ setting('site_name', 'Feira das Fábricas') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="site_email" class="form-label">Email Principal</label>
                                            <input type="email" class="form-control" id="site_email" 
                                                   value="{{ setting('site_email', '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="site_phone" class="form-label">Telefone</label>
                                            <input type="text" class="form-control" id="site_phone" 
                                                   value="{{ setting('site_phone', '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="site_address" class="form-label">Endereço</label>
                                            <textarea class="form-control" id="site_address" rows="3">{{ setting('site_address', '') }}</textarea>
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveGeneralConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações de Estoque -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i>Configurações de Estoque</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="stock_alert_threshold" class="form-label">Limite de Alerta de Estoque</label>
                                            <input type="number" class="form-control" id="stock_alert_threshold" 
                                                   value="{{ setting('stock_alert_threshold', 10) }}" min="1">
                                        </div>
                                        <div class="mb-3">
                                            <label for="auto_stock_management" class="form-label">Gerenciamento Automático</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="auto_stock_management" 
                                                       {{ setting('auto_stock_management', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="auto_stock_management">
                                                    Ativar
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stock_reserve_time" class="form-label">Tempo de Reserva (horas)</label>
                                            <input type="number" class="form-control" id="stock_reserve_time" 
                                                   value="{{ setting('stock_reserve_time', 24) }}" min="1" max="168">
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveStockConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações de Notificações -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-bell me-2"></i>Notificações</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="email_notifications" class="form-label">Notificações por Email</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_notifications" 
                                                       {{ setting('email_notifications', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_notifications">
                                                    Ativar
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sms_notifications" class="form-label">Notificações por SMS</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                                       {{ setting('sms_notifications', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sms_notifications">
                                                    Ativar
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notification_email" class="form-label">Email para Notificações</label>
                                            <input type="email" class="form-control" id="notification_email" 
                                                   value="{{ setting('notification_email', '') }}">
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveNotificationConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações de Segurança -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-shield-check me-2"></i>Segurança</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="two_factor_auth" class="form-label">Autenticação de Dois Fatores</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="two_factor_auth" 
                                                       {{ setting('two_factor_auth', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="two_factor_auth">
                                                    Ativar
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="session_timeout" class="form-label">Timeout de Sessão (minutos)</label>
                                            <input type="number" class="form-control" id="session_timeout" 
                                                   value="{{ setting('session_timeout', 120) }}" min="15" max="480">
                                        </div>
                                        <div class="mb-3">
                                            <label for="max_login_attempts" class="form-label">Máximo de Tentativas de Login</label>
                                            <input type="number" class="form-control" id="max_login_attempts" 
                                                   value="{{ setting('max_login_attempts', 5) }}" min="3" max="10">
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveSecurityConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab SEFAZ/NFe -->
                    <div class="tab-pane fade" id="fiscal" role="tabpanel">
                        <div class="row">
                            <!-- Configurações SEFAZ -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-receipt me-2"></i>Configurações SEFAZ</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="sefaz_enabled" class="form-label">Ativar Notas Fiscais</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sefaz_enabled" 
                                                       {{ setting('sefaz_enabled', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sefaz_enabled">
                                                    Habilitar emissão de NFe/NFCe
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_ambiente" class="form-label">Ambiente</label>
                                            <select class="form-select" id="sefaz_ambiente">
                                                <option value="1" {{ setting('sefaz_ambiente', '1') == '1' ? 'selected' : '' }}>Produção</option>
                                                <option value="2" {{ setting('sefaz_ambiente', '1') == '2' ? 'selected' : '' }}>Homologação</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_cnpj" class="form-label">CNPJ da Empresa</label>
                                            <input type="text" class="form-control" id="sefaz_cnpj" 
                                                   value="{{ setting('sefaz_cnpj', '') }}" placeholder="00.000.000/0000-00">
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_ie" class="form-label">Inscrição Estadual</label>
                                            <input type="text" class="form-control" id="sefaz_ie" 
                                                   value="{{ setting('sefaz_ie', '') }}" placeholder="ISENTO ou número da IE">
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_razao_social" class="form-label">Razão Social</label>
                                            <input type="text" class="form-control" id="sefaz_razao_social" 
                                                   value="{{ setting('sefaz_razao_social', '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_nome_fantasia" class="form-label">Nome Fantasia</label>
                                            <input type="text" class="form-control" id="sefaz_nome_fantasia" 
                                                   value="{{ setting('sefaz_nome_fantasia', '') }}">
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveFiscalConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar Configurações
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificado Digital -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-shield-lock me-2"></i>Certificado Digital</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="sefaz_certificado_tipo" class="form-label">Tipo de Certificado</label>
                                            <select class="form-select" id="sefaz_certificado_tipo">
                                                <option value="A1" {{ setting('sefaz_certificado_tipo', 'A1') == 'A1' ? 'selected' : '' }}>A1 (Arquivo .pfx/.p12)</option>
                                                <option value="A3" {{ setting('sefaz_certificado_tipo', 'A1') == 'A3' ? 'selected' : '' }}>A3 (Token/Cartão)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_certificado_arquivo" class="form-label">Arquivo do Certificado (.pfx/.p12)</label>
                                            <input type="file" class="form-control" id="sefaz_certificado_arquivo" accept=".pfx,.p12">
                                            <div class="form-text">Arquivo do certificado digital A1</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_certificado_senha" class="form-label">Senha do Certificado</label>
                                            <input type="password" class="form-control" id="sefaz_certificado_senha" 
                                                   value="{{ setting('sefaz_certificado_senha', '') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_serie" class="form-label">Série da Nota Fiscal</label>
                                            <input type="number" class="form-control" id="sefaz_serie" 
                                                   value="{{ setting('sefaz_serie', '1') }}" min="1" max="999">
                                        </div>
                                        <div class="mb-3">
                                            <label for="sefaz_numero_inicial" class="form-label">Número Inicial</label>
                                            <input type="number" class="form-control" id="sefaz_numero_inicial" 
                                                   value="{{ setting('sefaz_numero_inicial', '1') }}" min="1">
                                        </div>
                                        <button class="btn btn-success btn-sm" onclick="testFiscalConnection()">
                                            <i class="bi bi-wifi me-1"></i>Testar Conexão SEFAZ
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações de Emissão -->
                            <div class="col-12 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-gear me-2"></i>Configurações de Emissão</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="sefaz_auto_emitir" class="form-label">Emissão Automática</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="sefaz_auto_emitir" 
                                                           {{ setting('sefaz_auto_emitir', false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sefaz_auto_emitir">
                                                        Emitir NFe automaticamente
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="sefaz_tipo_nota" class="form-label">Tipo de Nota</label>
                                                <select class="form-select" id="sefaz_tipo_nota">
                                                    <option value="NFe" {{ setting('sefaz_tipo_nota', 'NFCe') == 'NFe' ? 'selected' : '' }}>NFe</option>
                                                    <option value="NFCe" {{ setting('sefaz_tipo_nota', 'NFCe') == 'NFCe' ? 'selected' : '' }}>NFCe</option>
                                                    <option value="auto" {{ setting('sefaz_tipo_nota', 'NFCe') == 'auto' ? 'selected' : '' }}>Automático</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="sefaz_cfop" class="form-label">CFOP Padrão</label>
                                                <input type="text" class="form-control" id="sefaz_cfop" 
                                                       value="{{ setting('sefaz_cfop', '5102') }}" placeholder="5102">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="sefaz_cst" class="form-label">CST Padrão</label>
                                                <input type="text" class="form-control" id="sefaz_cst" 
                                                       value="{{ setting('sefaz_cst', '00') }}" placeholder="00">
                                            </div>
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveEmissionConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar Configurações
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Emails Automáticos -->
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <div class="row">
                            <!-- Configurações de Email -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-envelope me-2"></i>Configurações de Email</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="email_enabled" class="form-label">Ativar Emails Automáticos</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_enabled" 
                                                       {{ setting('email_enabled', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_enabled">
                                                    Habilitar envio automático
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_from_name" class="form-label">Nome do Remetente</label>
                                            <input type="text" class="form-control" id="email_from_name" 
                                                   value="{{ setting('email_from_name', 'Feira das Fábricas') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_from_address" class="form-label">Email do Remetente</label>
                                            <input type="email" class="form-control" id="email_from_address" 
                                                   value="{{ setting('email_from_address', 'noreply@feiradasfabricas.com') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_reply_to" class="form-label">Email de Resposta</label>
                                            <input type="email" class="form-control" id="email_reply_to" 
                                                   value="{{ setting('email_reply_to', 'contato@feiradasfabricas.com') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_provider" class="form-label">Provedor de Email</label>
                                            <select class="form-select" id="email_provider">
                                                <option value="smtp" {{ setting('email_provider', 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                                <option value="mailgun" {{ setting('email_provider', 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                                <option value="sendgrid" {{ setting('email_provider', 'smtp') == 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveEmailConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar Configurações
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Templates de Email -->
                            <div class="col-lg-6 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Templates de Email</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="email_template_order_confirmation" class="form-label">Template: Confirmação de Pedido</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_template_order_confirmation" 
                                                       {{ setting('email_template_order_confirmation', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_template_order_confirmation">
                                                    Enviar confirmação
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_template_payment_confirmation" class="form-label">Template: Confirmação de Pagamento</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_template_payment_confirmation" 
                                                       {{ setting('email_template_payment_confirmation', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_template_payment_confirmation">
                                                    Enviar confirmação
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email_template_invoice_attachment" class="form-label">Template: Nota Fiscal em Anexo</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="email_template_invoice_attachment" 
                                                       {{ setting('email_template_invoice_attachment', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="email_template_invoice_attachment">
                                                    Enviar NFe em anexo
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button class="btn btn-success btn-sm" onclick="testEmailTemplate()">
                                            <i class="bi bi-send me-1"></i>Testar Email
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações SMTP -->
                            <div class="col-12 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><i class="bi bi-server me-2"></i>Configurações SMTP</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="smtp_host" class="form-label">Servidor SMTP</label>
                                                <input type="text" class="form-control" id="smtp_host" 
                                                       value="{{ setting('smtp_host', 'smtp.gmail.com') }}" placeholder="smtp.gmail.com">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="smtp_port" class="form-label">Porta</label>
                                                <input type="number" class="form-control" id="smtp_port" 
                                                       value="{{ setting('smtp_port', '587') }}" placeholder="587">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="smtp_encryption" class="form-label">Criptografia</label>
                                                <select class="form-select" id="smtp_encryption">
                                                    <option value="tls" {{ setting('smtp_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                                    <option value="ssl" {{ setting('smtp_encryption', 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                    <option value="none" {{ setting('smtp_encryption', 'tls') == 'none' ? 'selected' : '' }}>Nenhuma</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="smtp_username" class="form-label">Usuário</label>
                                                <input type="text" class="form-control" id="smtp_username" 
                                                       value="{{ setting('smtp_username', '') }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="smtp_password" class="form-label">Senha</label>
                                                <input type="password" class="form-control" id="smtp_password" 
                                                       value="{{ setting('smtp_password', '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="smtp_timeout" class="form-label">Timeout (segundos)</label>
                                                <input type="number" class="form-control" id="smtp_timeout" 
                                                       value="{{ setting('smtp_timeout', '30') }}" min="10" max="120">
                                            </div>
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="saveSMTPConfig()">
                                            <i class="bi bi-check-lg me-1"></i>Salvar SMTP
                                        </button>
                                        <button class="btn btn-success btn-sm ms-2" onclick="testSMTPConnection()">
                                            <i class="bi bi-wifi me-1"></i>Testar Conexão
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Status da Conexão -->
<div class="modal fade" id="connectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status da Conexão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="connectionStatus"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Função para salvar configurações de pagamento
function savePaymentConfig(provider) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    // Adicionar campos específicos do provider
    const fields = getPaymentFields(provider);
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? element.checked : element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Configurações salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        showAlert('Erro ao salvar configurações: ' + error.message, 'danger');
    });
}


// Função para salvar configurações gerais
function saveGeneralConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['site_name', 'site_email', 'site_phone', 'site_address'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Configurações salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        showAlert('Erro ao salvar configurações: ' + error.message, 'danger');
    });
}

// Função para salvar configurações de estoque
function saveStockConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['stock_alert_threshold', 'auto_stock_management', 'stock_reserve_time'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? element.checked : element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Configurações salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        showAlert('Erro ao salvar configurações: ' + error.message, 'danger');
    });
}

// Função para salvar configurações de notificação
function saveNotificationConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['email_notifications', 'sms_notifications', 'notification_email'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? element.checked : element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Configurações salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        showAlert('Erro ao salvar configurações: ' + error.message, 'danger');
    });
}

// Função para salvar configurações de segurança
function saveSecurityConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['two_factor_auth', 'session_timeout', 'max_login_attempts'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? element.checked : element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('Configurações salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        showAlert('Erro ao salvar configurações: ' + error.message, 'danger');
    });
}

// Função para testar conexão de pagamento
function testPaymentConnection(provider) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('provider', provider);
    formData.append('action', 'test_connection');

    fetch('{{ route("admin.settings.test-connection") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showConnectionModal(data);
    })
    .catch(error => {
        showConnectionModal({
            success: false,
            message: 'Erro ao testar conexão: ' + error.message
        });
    });
}

// Função para testar conexão de entrega
function testDeliveryConnection(provider) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('provider', provider);
    formData.append('action', 'test_connection');

    fetch('{{ route("admin.settings.test-connection") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showConnectionModal(data);
    })
    .catch(error => {
        showConnectionModal({
            success: false,
            message: 'Erro ao testar conexão: ' + error.message
        });
    });
}

// Função para autorizar Melhor Envio via OAuth
function revokeMelhorEnvioTokens() {
    if (!confirm('Deseja remover os tokens salvos do Melhor Envio?')) return;
    fetch('{{ route("admin.settings.melhor-envio.revoke") }}', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        showAlert(data.message || 'Operação concluída.', data.success ? 'success' : 'danger');
        if (data.success) {
            setTimeout(() => window.location.reload(), 1000);
        }
    })
    .catch(err => showAlert('Erro ao desconectar: ' + err.message, 'danger'));
}

// Função para mostrar modal de conexão
function showConnectionModal(data) {
    const modal = new bootstrap.Modal(document.getElementById('connectionModal'));
    const statusDiv = document.getElementById('connectionStatus');
    
    if (data.success) {
        statusDiv.innerHTML = `
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Conexão bem-sucedida!</strong><br>
                ${data.message || 'API conectada com sucesso.'}
            </div>
        `;
    } else {
        statusDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-x-circle me-2"></i>
                <strong>Falha na conexão!</strong><br>
                ${data.message || 'Não foi possível conectar com a API.'}
            </div>
        `;
    }
    
    modal.show();
}

// Função para mostrar alertas
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Funções auxiliares para obter campos
function getPaymentFields(provider) {
    const fields = {
        'stripe': ['stripe_enabled', 'stripe_public_key', 'stripe_secret_key'],
        'pagseguro': ['pagseguro_enabled', 'pagseguro_email', 'pagseguro_token', 'pagseguro_sandbox'],
        'paypal': ['paypal_enabled', 'paypal_client_id', 'paypal_client_secret', 'paypal_sandbox'],
        'mercadopago': ['mercadopago_enabled', 'mercadopago_public_key', 'mercadopago_access_token', 'mercadopago_sandbox']
    };
    return fields[provider] || [];
}

// Funções de entrega (apenas config no painel)
function saveDeliveryConfig(provider) {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');

    const fields = getDeliveryFields(provider);
    fields.forEach(field => {
        const el = document.getElementById(field);
        if (el) {
            formData.append(field, el.type === 'checkbox' ? el.checked : el.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP '+r.status);
        return r.json();
    })
    .then(data => showAlert(data.message || 'Configurações salvas!', data.success ? 'success' : 'danger'))
    .catch(err => showAlert('Erro ao salvar: ' + err.message, 'danger'));
}

function getDeliveryFields(provider) {
    const fields = {
        'melhor_envio': ['melhor_envio_enabled','melhor_envio_client_id','melhor_envio_client_secret','melhor_envio_sandbox','melhor_envio_cep_origem','melhor_envio_service_ids','melhor_envio_declared_mode','melhor_envio_declared_cap']
    };
    return fields[provider] || [];
}

// Função para atualizar texto do status
function updateStatusText(checkboxId) {
    const checkbox = document.getElementById(checkboxId);
    const label = document.getElementById(checkboxId + '_label');
    
    if (checkbox && label) {
        label.textContent = checkbox.checked ? 'Ativo' : 'Inativo';
    }
}

// Funções para SEFAZ/NFe
function saveFiscalConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['sefaz_enabled', 'sefaz_ambiente', 'sefaz_cnpj', 'sefaz_ie', 'sefaz_razao_social', 'sefaz_nome_fantasia'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? element.checked : element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Configurações fiscais salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao salvar configurações fiscais', 'danger');
    });
}

function saveEmissionConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['sefaz_auto_emitir', 'sefaz_tipo_nota', 'sefaz_cfop', 'sefaz_cst'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? element.checked : element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Configurações de emissão salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao salvar configurações de emissão', 'danger');
    });
}

function testFiscalConnection() {
    showAlert('Testando conexão com SEFAZ...', 'info');
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    formData.append('test_connection', 'true');

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Conexão com SEFAZ realizada com sucesso!', 'success');
        } else {
            showAlert('Erro na conexão SEFAZ: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao testar conexão SEFAZ', 'danger');
    });
}

// Funções para Emails Automáticos
function saveEmailConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['email_enabled', 'email_from_name', 'email_from_address', 'email_reply_to', 'email_provider'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? element.checked : element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Configurações de email salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao salvar configurações de email', 'danger');
    });
}

function saveSMTPConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password', 'smtp_timeout'];
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.value);
        }
    });

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Configurações SMTP salvas com sucesso!', 'success');
        } else {
            showAlert('Erro ao salvar configurações: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao salvar configurações SMTP', 'danger');
    });
}

function testSMTPConnection() {
    showAlert('Testando conexão SMTP...', 'info');
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    formData.append('test_smtp', 'true');

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Conexão SMTP realizada com sucesso!', 'success');
        } else {
            showAlert('Erro na conexão SMTP: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao testar conexão SMTP', 'danger');
    });
}

function testEmailTemplate() {
    showAlert('Enviando email de teste...', 'info');
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    formData.append('test_email', 'true');

    fetch('{{ route("admin.settings.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Email de teste enviado com sucesso!', 'success');
        } else {
            showAlert('Erro ao enviar email: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao enviar email de teste', 'danger');
    });
}

// Função para mostrar alertas
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Remove o alerta após 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Máscaras para campos
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CNPJ
    const cnpjInput = document.getElementById('jadlog_cnpj');
    if (cnpjInput) {
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }

    // Máscara para CNPJ SEFAZ
    const sefazCnpjInput = document.getElementById('sefaz_cnpj');
    if (sefazCnpjInput) {
        sefazCnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }
});

// Ativar aba correta se vier do callback OAuth
@if(session('active_tab'))
    document.addEventListener('DOMContentLoaded', function() {
        const activeTab = '{{ session("active_tab") }}';
        if (activeTab) {
            const tabButton = document.querySelector(`#${activeTab}-tab`);
            if (tabButton) {
                const tab = new bootstrap.Tab(tabButton);
                tab.show();
            }
        }
    });
@endif
</script>
@endsection
