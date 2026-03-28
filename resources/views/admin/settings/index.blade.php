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
            @php
                // Usar o mesmo serviço do controller dedicado
                $melhorEnvioService = app(\App\Services\MelhorEnvioService::class);
                $isConfigured = $melhorEnvioService->isConfigured();
                $isConnected = $melhorEnvioService->isConnected();
                $hasToken = !empty(setting('melhor_envio_token'));
                $tokenExpiresAt = setting('melhor_envio_token_expires_at');
                $isTokenExpired = $tokenExpiresAt && \Carbon\Carbon::parse($tokenExpiresAt)->isPast();
            @endphp

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

            <!-- Alerta de configuração -->
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Configuração do Melhor Envio:</strong><br>
                <ul class="mb-2 mt-2">
                    <li><strong>Callback URL:</strong> <code>https://rosybrown-jackal-637541.hostingersite.com/admin/melhor-envio/callback</code></li>
                    <li><strong>Painel Produção:</strong> <a href="https://melhorenvio.com.br/painel/desenvolvedor" target="_blank">melhorenvio.com.br/painel/desenvolvedor <i class="bi bi-box-arrow-up-right"></i></a></li>
                    <li><strong>Painel Sandbox:</strong> <a href="https://sandbox.melhorenvio.com.br/integracoes/area-dev" target="_blank">sandbox.melhorenvio.com.br/integracoes/area-dev <i class="bi bi-box-arrow-up-right"></i></a></li>
                </ul>
                <small class="text-muted">Use credenciais do ambiente correspondente (produção ou sandbox).</small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

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
                                <button class="btn btn-outline-danger" onclick="disconnectMelhorEnvio()">
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
                                    <strong>Produção:</strong> <a href="https://melhorenvio.com.br/painel/desenvolvedor" target="_blank">melhorenvio.com.br/painel/desenvolvedor <i class="bi bi-box-arrow-up-right"></i><br>
                                    <strong>Sandbox:</strong> <a href="https://sandbox.melhorenvio.com.br/integracoes/area-dev" target="_blank">sandbox.melhorenvio.com.br/integracoes/area-dev <i class="bi bi-box-arrow-up-right"></i><br>
                                    <strong>Callback URL:</strong> <code>https://rosybrown-jackal-637541.hostingersite.com/admin/melhor-envio/callback</code>
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
                                           {{ setting('melhor_envio_sandbox', false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sandbox">
                                        Modo Sandbox (Teste)
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Sandbox: https://sandbox.melhorenvio.com.br<br>
                                    Produção: https://api.melhorenvio.com.br
                                </small>
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
// Identity / Theme handlers
document.addEventListener('DOMContentLoaded', function() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Logo
    const logoFile = document.getElementById('identityLogoFile');
    const logoPreview = document.getElementById('identityLogoPreview');
    const logoUploadBtn = document.getElementById('identityLogoUploadBtn');
    if (logoFile) {
        logoFile.addEventListener('change', function() {
            const f = this.files && this.files[0];
            if (!f) return; const r = new FileReader(); r.onload = e => { logoPreview.src = e.target.result; }; r.readAsDataURL(f);
        });
    }
    logoUploadBtn && logoUploadBtn.addEventListener('click', function() {
        const f = logoFile.files && logoFile.files[0]; if (!f) { showAlert('Selecione um arquivo de logo.', 'danger'); return; }
        const fd = new FormData(); fd.append('logo', f);
        fetch('{{ route("admin.settings.upload-logo") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: fd })
            .then(r => r.json()).then(data => {
                if (data && data.success) { logoPreview.src = data.url + '?v=' + Date.now(); showAlert(data.message || 'Logo enviada.', 'success'); }
                else if (data && data.errors) showAlert(Object.values(data.errors).flat().join(' '), 'danger');
                else showAlert(data.message || 'Erro ao enviar logo.', 'danger');
            }).catch(err => { console.error(err); showAlert('Erro ao enviar logo.', 'danger'); });
    });

    // App Icon Upload
    const appIconFile = document.getElementById('appIconFile');
    const appIconPreview = document.getElementById('appIconPreview');
    const appIconUploadBtn = document.getElementById('appIconUploadBtn');
    if (appIconFile) {
        appIconFile.addEventListener('change', function() {
            const f = this.files && this.files[0];
            if (!f) return;
            const r = new FileReader();
            r.onload = e => { appIconPreview.src = e.target.result; };
            r.readAsDataURL(f);
        });
    }
    appIconUploadBtn && appIconUploadBtn.addEventListener('click', function() {
        const f = appIconFile.files && appIconFile.files[0];
        if (!f) { showAlert('Selecione um arquivo de App Icon.', 'danger'); return; }
        const fd = new FormData();
        fd.append('app_icon', f);
        fetch('{{ route("admin.settings.upload-app-icon") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.success) {
                appIconPreview.src = data.url + '?v=' + Date.now();
                showAlert(data.message || 'App Icon enviado com sucesso!', 'success');
            } else if (data && data.errors) {
                showAlert(Object.values(data.errors).flat().join(' '), 'danger');
            } else {
                showAlert(data.message || 'Erro ao enviar App Icon.', 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('Erro ao enviar App Icon.', 'danger');
        });
    });

    // Favicon Upload
    const faviconFile = document.getElementById('faviconFile');
    const faviconPreview = document.getElementById('faviconPreview');
    const faviconUploadBtn = document.getElementById('faviconUploadBtn');
    if (faviconFile) {
        faviconFile.addEventListener('change', function() {
            const f = this.files && this.files[0];
            if (!f) return;
            const r = new FileReader();
            r.onload = e => { faviconPreview.src = e.target.result; };
            r.readAsDataURL(f);
        });
    }
    faviconUploadBtn && faviconUploadBtn.addEventListener('click', function() {
        const f = faviconFile.files && faviconFile.files[0];
        if (!f) { showAlert('Selecione um arquivo de Favicon.', 'danger'); return; }
        const fd = new FormData();
        fd.append('favicon', f);
        fetch('{{ route("admin.settings.upload-favicon") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data && data.success) {
                faviconPreview.src = data.url + '?v=' + Date.now();
                showAlert(data.message || 'Favicon enviado com sucesso!', 'success');
            } else if (data && data.errors) {
                showAlert(Object.values(data.errors).flat().join(' '), 'danger');
            } else {
                showAlert(data.message || 'Erro ao enviar Favicon.', 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('Erro ao enviar Favicon.', 'danger');
        });
    });

    // Logo size save handler
    const saveLogoSizeBtn = document.getElementById('saveLogoSizeBtn');
    const logoMaxH = document.getElementById('identityLogoMaxHeight');
    const logoMaxW = document.getElementById('identityLogoMaxWidth');
    if (saveLogoSizeBtn) {
        saveLogoSizeBtn.addEventListener('click', function() {
            const h = logoMaxH && logoMaxH.value ? parseInt(logoMaxH.value, 10) : null;
            const w = logoMaxW && logoMaxW.value ? parseInt(logoMaxW.value, 10) : null;
            const fd = new FormData();
            fd.append('_token', token);
            fd.append('_method', 'PUT');
            if (h !== null) fd.append('site_logo_max_height', h);
            if (w !== null && w !== '') fd.append('site_logo_max_width', w);

            fetch('{{ route("admin.settings.update") }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => r.json()).then(data => {
                    if (data && data.success) {
                        showAlert('Tamanho da logo salvo com sucesso! O novo tamanho será aplicado em todas as páginas do site.', 'success');
                        const logoPreview = document.getElementById('identityLogoPreview');
                        if (logoPreview) {
                            if (h) logoPreview.style.maxHeight = h + 'px'; else logoPreview.style.maxHeight = '';
                            if (w) logoPreview.style.maxWidth = w + 'px'; else logoPreview.style.maxWidth = '';
                        }
                        setTimeout(function() {
                            if (confirm('Tamanho salvo! Deseja recarregar a página para ver as mudanças em todas as logos?')) {
                                window.location.reload();
                            }
                        }, 1000);
                    } else {
                        showAlert('Erro ao salvar tamanho.', 'danger');
                    }
                }).catch(err => { console.error(err); showAlert('Erro ao salvar tamanho.', 'danger'); });
        });
    }

    // B2B Toggle
    document.getElementById('b2b_enabled')?.addEventListener('change', function() {
        const settings = document.getElementById('b2b_settings');
        settings.style.display = this.checked ? 'block' : 'none';
    });
});

// Placeholder functions for save operations
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

// Melhor Envio Functions
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

// Testar credenciais
async function testCredentials() {
    const clientId = document.getElementById('client_id').value;
    const clientSecret = document.getElementById('client_secret').value;
    
    if (!clientId || !clientSecret) {
        showAlert('Preencha Client ID e Client Secret', 'danger');
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ client_id: clientId, client_secret: clientSecret })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('✅ ' + data.message, 'success');
        } else {
            showAlert('❌ ' + data.message, 'danger');
        }
    } catch (error) {
        showAlert('Erro: ' + error.message, 'danger');
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
        showAlert('Preencha todos os campos obrigatórios', 'danger');
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            showAlert('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('❌ ' + data.message, 'danger');
        }
    } catch (error) {
        showAlert('Erro: ' + error.message, 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i>Salvar Configurações';
    }
}

// Desconectar
async function disconnectMelhorEnvio() {
    if (!confirm('Tem certeza que deseja desconectar do Melhor Envio?')) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.melhor-envio.disconnect") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('✅ ' + data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('❌ ' + data.message, 'danger');
        }
    } catch (error) {
        showAlert('Erro: ' + error.message, 'danger');
    }
}

// Testar cálculo
async function testCalculate() {
    const cepOrigem = document.getElementById('test_cep_origem').value;
    const cepDestino = document.getElementById('test_cep_destino').value;
    
    if (!cepDestino) {
        showAlert('Informe o CEP de destino', 'danger');
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        showAlert('Erro: ' + error.message, 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-calculator me-1"></i>Calcular';
    }
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
</script>
@endsection
