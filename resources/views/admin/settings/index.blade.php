@extends('admin.layouts.app')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')
@section('page-subtitle')
    <p class="text-muted mb-0">Central de integração de APIs e configurações gerais</p>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        @endpush

        @endsection
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
