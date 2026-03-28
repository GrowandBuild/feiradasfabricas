@extends('admin.layouts.app')

@section('title', 'Configurações - Melhor Envio')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="bi bi-truck me-2"></i>Melhor Envio
                    </h1>
                    <p class="text-muted mb-0">Configure a integração com o Melhor Envio para cálculo de frete</p>
                </div>
                <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-{{ $isConnected ? 'success' : ($isConfigured ? 'warning' : 'secondary') }} bg-opacity-10 p-3 rounded">
                                <i class="bi bi-{{ $isConnected ? 'check-circle-fill' : ($isConfigured ? 'exclamation-circle-fill' : 'circle') }} text-{{ $isConnected ? 'success' : ($isConfigured ? 'warning' : 'secondary') }} fs-2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">Status da Conexão</h5>
                            <p class="mb-0 text-muted">
                                @if($isConnected)
                                    <span class="text-success">✅ Conectado e autorizado</span>
                                @elseif($isConfigured)
                                    <span class="text-warning">⚠️ Configurado mas não autorizado</span>
                                    <br><small>Clique em "Autorizar" para conectar com o Melhor Envio</small>
                                @else
                                    <span class="text-secondary">❌ Não configurado</span>
                                    <br><small>Preencha as credenciais abaixo para começar</small>
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            @if($isConnected)
                                <button class="btn btn-outline-danger" onclick="disconnect()">
                                    <i class="bi bi-x-circle me-1"></i>Desconectar
                                </button>
                            @elseif($isConfigured)
                                <a href="{{ route('admin.melhor-envio.authorize') }}" class="btn btn-primary">
                                    <i class="bi bi-link-45deg me-1"></i>Autorizar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Configurações</h5>
                </div>
                <div class="card-body">
                    <form id="melhorEnvioForm">
                        @csrf
                        
                        <!-- Credenciais -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Credenciais da API</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">
                                    Client ID <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="client_id" 
                                       name="client_id"
                                       value="{{ setting('melhor_envio_client_id', '') }}"
                                       placeholder="Seu Client ID">
                                <small class="form-text text-muted">
                                    Obtenha em <a href="https://melhorenvio.com.br/painel/desenvolvedor" target="_blank">melhorenvio.com.br/painel/desenvolvedor <i class="bi bi-box-arrow-up-right"></i></a>
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="client_secret" class="form-label">
                                    Client Secret <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="client_secret" 
                                       name="client_secret"
                                       value="{{ setting('melhor_envio_client_secret', '') }}"
                                       placeholder="Seu Client Secret">
                                <small class="form-text text-muted">Mantenha em sigilo</small>
                            </div>
                        </div>

                        <!-- CEP Origem -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="cep_origem" class="form-label">
                                    CEP de Origem <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="cep_origem" 
                                       name="cep_origem"
                                       value="{{ setting('melhor_envio_cep_origem', '') }}"
                                       placeholder="00000-000"
                                       maxlength="9">
                                <small class="form-text text-muted">CEP da sua loja para cálculo de frete</small>
                            </div>
                            <div class="col-md-6">
                                <label for="sandbox" class="form-label">Ambiente</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="sandbox" 
                                           name="sandbox"
                                           {{ setting('melhor_envio_sandbox', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sandbox">
                                        Modo Sandbox (Teste)
                                    </label>
                                </div>
                                <small class="form-text text-muted">Desmarque para produção</small>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-info" onclick="testCredentials()">
                                <i class="bi bi-shield-check me-1"></i>Testar Credenciais
                            </button>
                            <button type="button" class="btn btn-primary" onclick="saveSettings()">
                                <i class="bi bi-save me-1"></i>Salvar Configurações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Teste de Cálculo (só aparece se conectado) -->
            @if($isConnected)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Testar Cálculo de Frete</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">CEP Origem</label>
                            <input type="text" class="form-control" id="test_cep_origem" 
                                   value="{{ setting('melhor_envio_cep_origem', '') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CEP Destino</label>
                            <input type="text" class="form-control" id="test_cep_destino" 
                                   placeholder="00000-000" maxlength="9">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-outline-primary w-100" onclick="testCalculate()">
                                <i class="bi bi-calculator me-1"></i>Calcular
                            </button>
                        </div>
                    </div>
                    <div id="calculationResult" class="mt-3 d-none">
                        <!-- Resultado aparece aqui -->
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast para notificações -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert">
        <div class="toast-header">
            <i class="bi bi-info-circle me-2"></i>
            <strong class="me-auto">Melhor Envio</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            <!-- Mensagem -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Máscara para CEP
function maskCep(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 5) {
        value = value.substring(0, 5) + '-' + value.substring(5, 8);
    }
    input.value = value;
}

document.getElementById('cep_origem')?.addEventListener('input', function() {
    maskCep(this);
});

document.getElementById('test_cep_destino')?.addEventListener('input', function() {
    maskCep(this);
});

// Mostrar toast
function showToast(message, type = 'info') {
    const toast = document.getElementById('liveToast');
    const toastBody = document.getElementById('toastMessage');
    toastBody.textContent = message;
    toast.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-warning');
    toast.classList.add(type === 'success' ? 'text-bg-success' : type === 'error' ? 'text-bg-danger' : 'text-bg-info');
    bootstrap.Toast.getOrCreateInstance(toast).show();
}

// Testar credenciais
async function testCredentials() {
    const clientId = document.getElementById('client_id').value;
    const clientSecret = document.getElementById('client_secret').value;
    
    if (!clientId || !clientSecret) {
        showToast('Preencha Client ID e Client Secret', 'error');
        return;
    }
    
    const btn = document.querySelector('button[onclick="testCredentials()"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Testando...';
    
    try {
        const response = await fetch('{{ route("admin.melhor-envio.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ client_id: clientId, client_secret: clientSecret })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
        } else {
            showToast('❌ ' + data.message, 'error');
        }
    } catch (error) {
        showToast('Erro: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Testar Credenciais';
    }
}

// Salvar configurações
async function saveSettings() {
    const clientId = document.getElementById('client_id').value;
    const clientSecret = document.getElementById('client_secret').value;
    const cepOrigem = document.getElementById('cep_origem').value;
    const sandbox = document.getElementById('sandbox').checked;
    
    if (!clientId || !clientSecret || !cepOrigem) {
        showToast('Preencha todos os campos obrigatórios', 'error');
        return;
    }
    
    const btn = document.querySelector('button[onclick="saveSettings()"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando...';
    
    try {
        const response = await fetch('{{ route("admin.melhor-envio.connect") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                client_id: clientId,
                client_secret: clientSecret,
                cep_origem: cepOrigem,
                sandbox: sandbox
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('❌ ' + data.message, 'error');
        }
    } catch (error) {
        showToast('Erro: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i>Salvar Configurações';
    }
}

// Desconectar
async function disconnect() {
    if (!confirm('Tem certeza que deseja desconectar do Melhor Envio?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.melhor-envio.disconnect") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('❌ ' + data.message, 'error');
        }
    } catch (error) {
        showToast('Erro: ' + error.message, 'error');
    }
}

// Testar cálculo
async function testCalculate() {
    const cepOrigem = document.getElementById('test_cep_origem').value;
    const cepDestino = document.getElementById('test_cep_destino').value;
    
    if (!cepDestino) {
        showToast('Informe o CEP de destino', 'error');
        return;
    }
    
    const btn = document.querySelector('button[onclick="testCalculate()"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Calculando...';
    
    try {
        const response = await fetch('{{ route("admin.melhor-envio.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                cep_origem: cepOrigem,
                cep_destino: cepDestino,
                products: [{
                    id: 'test-1',
                    width: 11,
                    height: 2,
                    length: 16,
                    weight: 0.3,
                    price: 50.00,
                    quantity: 1
                }]
            })
        });
        
        const data = await response.json();
        const resultDiv = document.getElementById('calculationResult');
        
        if (data.success && data.services) {
            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Serviço</th><th>Preço</th><th>Prazo</th></tr></thead><tbody>';
            
            data.services.forEach(service => {
                html += `<tr>
                    <td>${service.name}</td>
                    <td>R$ ${service.price}</td>
                    <td>${service.delivery_time ? service.delivery_time + ' dias' : '-'}</td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            resultDiv.innerHTML = html;
            resultDiv.classList.remove('d-none');
        } else {
            resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            resultDiv.classList.remove('d-none');
        }
    } catch (error) {
        showToast('Erro: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-calculator me-1"></i>Calcular';
    }
}
</script>
@endpush
