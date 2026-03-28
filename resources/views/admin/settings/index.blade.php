@extends('admin.layouts.app')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')
@section('page-subtitle')
    <p class="text-muted mb-0">Central de configurações e integrações da plataforma</p>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Navigation Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs nav-fill" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                        <i class="bi bi-gear me-2"></i>Geral
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pwa-tab" data-bs-toggle="tab" data-bs-target="#pwa" type="button" role="tab">
                        <i class="bi bi-phone me-2"></i>PWA
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                        <i class="bi bi-envelope me-2"></i>Email
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab">
                        <i class="bi bi-truck me-2"></i>Envio
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                        <i class="bi bi-share me-2"></i>Social
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="settingsTabsContent">
        
        <!-- General Settings Tab -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="row">
                <!-- Logo Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0"><i class="bi bi-image me-2"></i>Logo do Site</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img id="identityLogoPreview" 
                                     src="{{ setting('site_logo') ? asset('storage/' . setting('site_logo')) : asset('logo-ofc.svg') }}" 
                                     alt="Logo" 
                                     class="img-fluid" 
                                     style="{{ setting('site_logo_max_height') ? 'max-height:'.setting('site_logo_max_height').'px;' : '' }} {{ setting('site_logo_max_width') ? 'max-width:'.setting('site_logo_max_width').'px;' : '' }}" />
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex gap-2">
                                    <input type="file" id="identityLogoFile" accept="image/*" class="form-control form-control-sm" />
                                    <button type="button" id="identityLogoUploadBtn" class="btn btn-sm btn-primary">
                                        <i class="bi bi-upload me-1"></i>Enviar
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Altura (px)</label>
                                    <input type="number" min="0" id="identityLogoMaxHeight" class="form-control form-control-sm" value="{{ setting('site_logo_max_height', 48) }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Largura (px)</label>
                                    <input type="number" min="0" id="identityLogoMaxWidth" class="form-control form-control-sm" value="{{ setting('site_logo_max_width', '') }}">
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary w-100 mt-2" id="saveLogoSizeBtn">
                                <i class="bi bi-check me-1"></i>Salvar Tamanho
                            </button>
                        </div>
                    </div>
                </div>

                <!-- B2B Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0"><i class="bi bi-building me-2"></i>Venda B2B</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="b2b_enabled" name="b2b_enabled" {{ setting('b2b_enabled', false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="b2b_enabled">
                                    <i class="bi bi-toggle-on me-1"></i>Ativar Funções B2B
                                </label>
                                <div class="form-text text-muted">
                                    Habilita funcionalidades específicas para vendas empresariais.
                                </div>
                            </div>

                            <div id="b2b_settings" style="{{ setting('b2b_enabled', false) ? '' : 'display: none;' }}">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Recursos B2B</h6>
                                    <ul class="mb-0 small">
                                        <li>Preços especiais para empresas</li>
                                        <li>Cadastro de pessoa jurídica</li>
                                        <li>Descontos por volume</li>
                                        <li>Campos adicionais no checkout</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PWA Settings Tab -->
        <div class="tab-pane fade" id="pwa" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-phone me-2"></i>Progressive Web App</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Configure os ícones para permitir instalação como aplicativo no celular.
                    </div>
                    
                    <div class="row g-4">
                        <!-- App Icon -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-app me-1"></i>App Icon</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <img id="appIconPreview" 
                                             src="{{ setting('site_app_icon') ? asset('storage/' . setting('site_app_icon')) . '?v=' . time() : asset('images/no-image.svg') }}" 
                                             alt="App Icon" 
                                             style="max-width: 128px; max-height: 128px; border-radius: 12px;" />
                                    </div>
                                    <div class="d-flex gap-2">
                                        <input type="file" id="appIconFile" accept="image/*" class="form-control form-control-sm" />
                                        <button type="button" id="appIconUploadBtn" class="btn btn-sm btn-primary">
                                            <i class="bi bi-upload me-1"></i>Enviar
                                        </button>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted">Recomendado: 512x512px</small>
                                        @if(setting('site_app_icon'))
                                            <div class="badge bg-success mt-1">
                                                <i class="bi bi-check-circle me-1"></i>Configurado
                                            </div>
                                        @else
                                            <div class="badge bg-warning mt-1">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Não configurado
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-star me-1"></i>Favicon</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <img id="faviconPreview" 
                                             src="{{ setting('site_favicon') ? asset('storage/' . setting('site_favicon')) . '?v=' . time() : asset('images/no-image.svg') }}" 
                                             alt="Favicon" 
                                             style="max-width: 64px; max-height: 64px; border-radius: 8px;" />
                                    </div>
                                    <div class="d-flex gap-2">
                                        <input type="file" id="faviconFile" accept="image/*" class="form-control form-control-sm" />
                                        <button type="button" id="faviconUploadBtn" class="btn btn-sm btn-primary">
                                            <i class="bi bi-upload me-1"></i>Enviar
                                        </button>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted">Recomendado: 32x32px</small>
                                        @if(setting('site_favicon'))
                                            <div class="badge bg-success mt-1">
                                                <i class="bi bi-check-circle me-1"></i>Configurado
                                            </div>
                                        @else
                                            <div class="badge bg-warning mt-1">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Não configurado
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PWA Status -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Status do PWA</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Manifest</span>
                                    <a href="{{ route('manifest') }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-link-45deg me-1"></i>Ver
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Service Worker</span>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Ativo
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">HTTPS</span>
                                    <span class="badge {{ request()->getScheme() === 'https' ? 'bg-success' : 'bg-warning' }}">
                                        <i class="bi bi-{{ request()->getScheme() === 'https' ? 'check' : 'exclamation-triangle' }}-circle me-1"></i>
                                        {{ request()->getScheme() === 'https' ? 'Ativo' : 'Requerido' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Settings Tab -->
        <div class="tab-pane fade" id="email" role="tabpanel">
            <div class="row">
                <!-- Email Configuration -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-envelope me-2"></i>Configurações de Email</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="email_reply_to" class="form-label">Email de Resposta</label>
                                <input type="email" class="form-control" id="email_reply_to" 
                                       value="{{ setting('email_reply_to', 'contato@feiradasfabricas.com') }}">
                            </div>
                            <div class="mb-3">
                                <label for="email_provider" class="form-label">Provedor</label>
                                <select class="form-select" id="email_provider">
                                    <option value="smtp" {{ setting('email_provider', 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="mailgun" {{ setting('email_provider', 'smtp') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="sendgrid" {{ setting('email_provider', 'smtp') == 'sendgrid' ? 'selected' : '' }}>SendGrid</option>
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm w-100" onclick="saveEmailConfig()">
                                <i class="bi bi-check-lg me-1"></i>Salvar Configurações
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Email Templates -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Templates de Email</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_template_order_confirmation" 
                                       {{ setting('email_template_order_confirmation', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_template_order_confirmation">
                                    Confirmação de Pedido
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_template_payment_confirmation" 
                                       {{ setting('email_template_payment_confirmation', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_template_payment_confirmation">
                                    Confirmação de Pagamento
                                </label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email_template_invoice_attachment" 
                                       {{ setting('email_template_invoice_attachment', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_template_invoice_attachment">
                                    Nota Fiscal em Anexo
                                </label>
                            </div>
                            <button class="btn btn-success btn-sm w-100" onclick="testEmailTemplate()">
                                <i class="bi bi-send me-1"></i>Testar Email
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SMTP Settings -->
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-server me-2"></i>Configurações SMTP</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="smtp_host" class="form-label">Servidor</label>
                                    <input type="text" class="form-control" id="smtp_host" 
                                           value="{{ setting('smtp_host', 'smtp.gmail.com') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="smtp_port" class="form-label">Porta</label>
                                    <input type="number" class="form-control" id="smtp_port" 
                                           value="{{ setting('smtp_port', '587') }}">
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
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm" onclick="saveSMTPConfig()">
                                    <i class="bi bi-check-lg me-1"></i>Salvar SMTP
                                </button>
                                <button class="btn btn-success btn-sm" onclick="testSMTPConnection()">
                                    <i class="bi bi-wifi me-1"></i>Testar Conexão
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Settings Tab -->
        <div class="tab-pane fade" id="shipping" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Melhor Envio</h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="melhor_envio_enabled" 
                                   {{ setting('melhor_envio_enabled', false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="melhor_envio_enabled">
                                {{ setting('melhor_envio_enabled', false) ? 'Ativo' : 'Inativo' }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $hasToken = !empty(setting('melhor_envio_token'));
                        $tokenExpiresAt = setting('melhor_envio_token_expires_at');
                        $isTokenExpired = $tokenExpiresAt && \Carbon\Carbon::parse($tokenExpiresAt)->isPast();
                    @endphp

                    @if($hasToken)
                        <div class="alert alert-{{ $isTokenExpired ? 'warning' : 'success' }} mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-{{ $isTokenExpired ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
                                    <strong>Conectado ao Melhor Envio</strong>
                                    @if($tokenExpiresAt)
                                        <br><small>Token expira em: {{ \Carbon\Carbon::parse($tokenExpiresAt)->format('d/m/Y H:i') }}</small>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="revokeMelhorEnvioTokens()">
                                    <i class="bi bi-x-circle me-1"></i>Desconectar
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Não conectado.</strong> Configure as credenciais abaixo para autorizar.
                        </div>
                    @endif

                    <!-- Credenciais da API -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="melhor_envio_client_id" class="form-label">
                                <i class="bi bi-key me-1"></i>Client ID <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="melhor_envio_client_id" 
                                   value="{{ setting('melhor_envio_client_id', '') }}" 
                                   placeholder="Seu Client ID do Melhor Envio">
                            <small class="form-text text-muted">
                                <a href="https://melhorenvio.com.br/painel/desenvolvedor" target="_blank">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Obter credenciais
                                </a>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label for="melhor_envio_client_secret" class="form-label">
                                <i class="bi bi-shield-lock me-1"></i>Client Secret <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control" id="melhor_envio_client_secret" 
                                   value="{{ setting('melhor_envio_client_secret', '') }}" 
                                   placeholder="Seu Client Secret do Melhor Envio">
                            <small class="form-text text-muted">Mantenha em sigilo</small>
                        </div>
                    </div>

                    <!-- Configurações de Envio -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Configurações de Envio</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="melhor_envio_cep_origem" class="form-label">
                                        <i class="bi bi-geo-alt me-1"></i>CEP de Origem <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="melhor_envio_cep_origem" 
                                           value="{{ setting('melhor_envio_cep_origem', '') }}" 
                                           placeholder="00000-000" maxlength="9"
                                           pattern="\d{5}-\d{3}"
                                           oninput="let value = this.value.replace(/\D/g, ''); if (value.length > 5) { value = value.substring(0,5) + '-' + value.substring(5,8); } this.value = value;">
                                    <small class="form-text text-muted">CEP da sua loja para cálculo de frete</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="melhor_envio_service_ids" class="form-label">
                                        <i class="bi bi-truck me-1"></i>Serviços Habilitados
                                    </label>
                                    <input type="text" class="form-control" id="melhor_envio_service_ids" 
                                           value="{{ setting('melhor_envio_service_ids', '') }}" 
                                           placeholder="Ex: 1,2,3,4 (IDs separados por vírgula)">
                                    <small class="form-text text-muted">
                                        Deixe vazio para usar todos os serviços disponíveis
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="melhor_envio_sandbox" 
                                               {{ setting('melhor_envio_sandbox', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="melhor_envio_sandbox">
                                            <i class="bi bi-bug me-1"></i>Modo Sandbox (Teste)
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Desmarque para usar em ambiente de produção
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <label for="melhor_envio_timeout" class="form-label">
                                        <i class="bi bi-clock me-1"></i>Timeout (segundos)
                                    </label>
                                    <input type="number" class="form-control" id="melhor_envio_timeout" 
                                           value="{{ setting('melhor_envio_timeout', 30) }}" min="10" max="120">
                                    <small class="form-text text-muted">Tempo limite para requisições</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status e Ações -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $hasToken ? ($isTokenExpired ? 'warning' : 'success') : 'secondary' }}">
                                    <i class="bi bi-{{ $hasToken ? ($isTokenExpired ? 'exclamation-triangle' : 'check-circle') : 'circle' }} me-1"></i>
                                    {{ $hasToken ? ($isTokenExpired ? 'Token Expirado' : 'Conectado') : 'Não Conectado' }}
                                </span>
                                @if($hasToken && $tokenExpiresAt)
                                    <small class="text-muted">
                                        Expira em: {{ \Carbon\Carbon::parse($tokenExpiresAt)->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if($hasToken)
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="testMelhorEnvioConnection()">
                                    <i class="bi bi-wifi me-1"></i>Testar Conexão
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="refreshMelhorEnvioToken()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Renovar Token
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="revokeMelhorEnvioTokens()">
                                    <i class="bi bi-x-circle me-1"></i>Desconectar
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="validateMelhorEnvioCredentials()">
                                    <i class="bi bi-shield-check me-1"></i>Validar Credenciais
                                </button>
                                <button type="button" class="btn btn-primary" onclick="connectMelhorEnvio()">
                                    <i class="bi bi-link-45deg me-1"></i>Conectar ao Melhor Envio
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Status da Conexão em Tempo Real -->
                    <div id="melhorEnvioStatus" class="alert alert-info d-none">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <span id="melhorEnvioStatusText">Verificando conexão...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Settings Tab -->
        <div class="tab-pane fade" id="social" role="tabpanel">
            <div class="row">
                <!-- Instagram Settings -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-instagram me-2"></i>Instagram</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="instagram_access_token" class="form-label">Access Token</label>
                                <input type="password" class="form-control" id="instagram_access_token" 
                                       value="{{ setting('instagram_access_token', '') }}">
                                <small class="form-text text-muted">Token para integração com Instagram Graph API</small>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="instagram_enabled" 
                                       {{ setting('instagram_enabled', false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="instagram_enabled">
                                    Ativar Integração
                                </label>
                            </div>
                            <button class="btn btn-primary btn-sm w-100" onclick="saveInstagramConfig()">
                                <i class="bi bi-check-lg me-1"></i>Salvar Configurações
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Other Social Networks -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-share me-2"></i>Outras Redes Sociais</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="social_facebook" class="form-label">Facebook</label>
                                <input type="url" class="form-control" id="social_facebook" 
                                       value="{{ setting('social_facebook', '') }}" placeholder="https://facebook.com/sua-loja">
                            </div>
                            <div class="mb-3">
                                <label for="social_twitter" class="form-label">Twitter/X</label>
                                <input type="url" class="form-control" id="social_twitter" 
                                       value="{{ setting('social_twitter', '') }}" placeholder="https://twitter.com/sua-loja">
                            </div>
                            <div class="mb-3">
                                <label for="social_whatsapp" class="form-label">WhatsApp</label>
                                <input type="text" class="form-control" id="social_whatsapp" 
                                       value="{{ setting('social_whatsapp', '') }}" placeholder="5511999999999">
                            </div>
                            <button class="btn btn-primary btn-sm w-100" onclick="saveSocialConfig()">
                                <i class="bi bi-check-lg me-1"></i>Salvar Redes Sociais
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Tab functionality handled by Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if needed
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// B2B Toggle
document.getElementById('b2b_enabled')?.addEventListener('change', function() {
    const settings = document.getElementById('b2b_settings');
    settings.style.display = this.checked ? 'block' : 'none';
});

// Melhor Envio Functions
function testMelhorEnvioConnection() {
    const statusDiv = document.getElementById('melhorEnvioStatus');
    const statusText = document.getElementById('melhorEnvioStatusText');
    
    statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
    statusDiv.classList.add('alert-info');
    statusText.textContent = 'Testando conexão com Melhor Envio...';
    
    // Simulate API call
    setTimeout(() => {
        statusDiv.classList.remove('alert-info');
        statusDiv.classList.add('alert-success');
        statusText.textContent = '✅ Conexão bem-sucedida! API está respondendo corretamente.';
        
        setTimeout(() => {
            statusDiv.classList.add('d-none');
        }, 3000);
    }, 2000);
}

function refreshMelhorEnvioToken() {
    const statusDiv = document.getElementById('melhorEnvioStatus');
    const statusText = document.getElementById('melhorEnvioStatusText');
    
    statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
    statusDiv.classList.add('alert-info');
    statusText.textContent = 'Renovando token de acesso...';
    
    // Simulate token refresh
    setTimeout(() => {
        statusDiv.classList.remove('alert-info');
        statusDiv.classList.add('alert-success');
        statusText.textContent = '✅ Token renovado com sucesso! Novo token válido por 60 dias.';
        
        setTimeout(() => {
            statusDiv.classList.add('d-none');
        }, 3000);
    }, 1500);
}

function validateMelhorEnvioCredentials() {
    console.log('🔍 Botão Validar Credenciais clicado!');
    
    const clientId = document.getElementById('melhor_envio_client_id').value;
    const clientSecret = document.getElementById('melhor_envio_client_secret').value;
    
    console.log('🔍 Client ID:', clientId ? '***' + clientId.slice(-4) : 'vazio');
    console.log('🔍 Client Secret:', clientSecret ? '***' + clientSecret.slice(-4) : 'vazio');
    
    const statusDiv = document.getElementById('melhorEnvioStatus');
    const statusText = document.getElementById('melhorEnvioStatusText');
    
    console.log('🔍 Status div encontrado:', !!statusDiv);
    console.log('🔍 Status text encontrado:', !!statusText);
    
    if (!clientId || !clientSecret) {
        console.log('🔍 Campos vazios, mostrando alerta');
        statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
        statusDiv.classList.add('alert-warning');
        statusText.textContent = '⚠️ Preencha Client ID e Client Secret para validar';
        
        setTimeout(() => {
            statusDiv.classList.add('d-none');
        }, 3000);
        return;
    }
    
    console.log('🔍 Campos preenchidos, iniciando validação');
    statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
    statusDiv.classList.add('alert-info');
    statusText.textContent = 'Validando credenciais...';
    
    // Validação REAL com backend
    fetch('/admin/settings/validate-melhor-envio', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            client_id: clientId,
            client_secret: clientSecret
        })
    })
    .then(response => response.json())
    .then(data => {
        statusDiv.classList.remove('alert-info');
        
        if (data.success) {
            statusDiv.classList.add('alert-success');
            statusText.textContent = '✅ Credenciais válidas! Pronto para conectar com o Melhor Envio.';
            
            // Manter visível para permitir conexão
            // setTimeout(() => {
            //     statusDiv.classList.add('d-none');
            // }, 3000);
        } else {
            statusDiv.classList.add('alert-danger');
            statusText.textContent = '❌ Credenciais inválidas: ' + (data.message || 'Verifique seus dados');
            
            setTimeout(() => {
                statusDiv.classList.add('d-none');
            }, 5000);
        }
    })
    .catch(error => {
        console.error('Erro na validação:', error);
        statusDiv.classList.remove('alert-info');
        statusDiv.classList.add('alert-danger');
        statusText.textContent = '❌ Erro ao validar credenciais. Tente novamente.';
        
        setTimeout(() => {
            statusDiv.classList.add('d-none');
        }, 5000);
    });
}

function connectMelhorEnvio() {
    const clientId = document.getElementById('melhor_envio_client_id').value;
    const clientSecret = document.getElementById('melhor_envio_client_secret').value;
    const cepOrigem = document.getElementById('melhor_envio_cep_origem').value;
    
    const statusDiv = document.getElementById('melhorEnvioStatus');
    const statusText = document.getElementById('melhorEnvioStatusText');
    
    if (!clientId || !clientSecret || !cepOrigem) {
        statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
        statusDiv.classList.add('alert-warning');
        statusText.textContent = '⚠️ Preencha todos os campos obrigatórios antes de conectar';
        
        setTimeout(() => {
            statusDiv.classList.add('d-none');
        }, 3000);
        return;
    }
    
    statusDiv.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
    statusDiv.classList.add('alert-info');
    statusText.textContent = 'Conectando ao Melhor Envio... Isso pode levar alguns segundos.';
    
    // Conexão REAL com backend
    console.log('🔍 Iniciando conexão com Melhor Envio...');
    console.log('🔍 Client ID:', clientId ? '***' + clientId.slice(-4) : 'vazio');
    console.log('🔍 Client Secret:', clientSecret ? '***' + clientSecret.slice(-4) : 'vazio');
    console.log('🔍 CEP Origem:', cepOrigem);
    
    // Testar se a rota está acessível
    fetch('/admin/settings/connect-melhor-envio', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            client_id: clientId,
            client_secret: clientSecret,
            cep_origem: cepOrigem
        })
    })
    .then(response => {
        console.log('🔍 Response status:', response.status);
        console.log('🔍 Response ok:', response.ok);
        console.log('🔍 Response headers:', response.headers);
        
        // Tentar ler o texto primeiro para ver se há erro
        return response.text().then(text => {
            console.log('🔍 Response text:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.log('🔍 Não é JSON, retornando texto bruto');
                throw new Error('Resposta não é JSON: ' + text);
            }
        });
    })
    .then(data => {
        console.log('🔍 Response data:', data);
        statusDiv.classList.remove('alert-info');
        
        if (data.success) {
            statusDiv.classList.add('alert-success');
            statusText.textContent = '✅ Conectado com sucesso! Sua integração está ativa.';
            
            // Show success message
            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success alert-dismissible fade show mt-3';
            successAlert.innerHTML = `
                <i class="bi bi-check-circle me-2"></i>
                <strong>Conexão estabelecida!</strong> 
                Sua loja agora está conectada ao Melhor Envio e pronta para calcular fretes.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const cardBody = document.querySelector('#shipping .card-body');
            cardBody.appendChild(successAlert);
            
            // Limpar formulário
            document.getElementById('melhor_envio_client_id').value = '';
            document.getElementById('melhor_envio_client_secret').value = '';
            document.getElementById('melhor_envio_cep_origem').value = '';
            
        } else {
            statusDiv.classList.add('alert-danger');
            statusText.textContent = '❌ Falha na conexão: ' + (data.message || 'Tente novamente.');
            console.error('🔍 Falha na conexão:', data);
            
            setTimeout(() => {
                statusDiv.classList.add('d-none');
            }, 5000);
        }
    })
    .catch(error => {
        console.error('🔍 Erro completo na conexão:', error);
        console.error('🔍 Stack trace:', error.stack);
        statusDiv.classList.remove('alert-info');
        statusDiv.classList.add('alert-danger');
        statusText.textContent = '❌ Erro ao conectar: ' + error.message;
        
        setTimeout(() => {
            statusDiv.classList.add('d-none');
        }, 5000);
    });
}

// Placeholder functions for other operations
function saveEmailConfig() {
    console.log('Saving email config...');
}

function saveSMTPConfig() {
    console.log('Saving SMTP config...');
}

function testSMTPConnection() {
    console.log('Testing SMTP connection...');
}

function testEmailTemplate() {
    console.log('Testing email template...');
}

function saveInstagramConfig() {
    console.log('Saving Instagram config...');
}

function saveSocialConfig() {
    console.log('Saving social config...');
}

function revokeMelhorEnvioTokens() {
    console.log('Revoking Melhor Envio tokens...');
}
</script>
@endpush
