@extends('admin.layouts.app')

@section('title', 'Configurações')
@section('page-title', 'Configurações do Sistema')
@section('page-subtitle')
    <p class="text-muted mb-0">Central de integração de APIs e configurações gerais</p>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0"><i class="bi bi-palette me-2"></i>Identidade / Tema</h6>
                    <small class="text-muted">Logo, favicon, app icon e cores do tema</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Logo do Site</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="file" id="identityLogoFile" accept="image/*" class="form-control form-control-sm" />
                                <button type="button" id="identityLogoUploadBtn" class="btn btn-sm btn-primary">Enviar</button>
                            </div>
                            <div class="mt-2">
                                <img id="identityLogoPreview" src="{{ setting('site_logo') ? asset('storage/' . setting('site_logo')) : asset('logo-ofc.svg') }}" alt="Logo" style="{{ setting('site_logo_max_height') ? 'max-height:'.setting('site_logo_max_height').'px;' : '' }} {{ setting('site_logo_max_width') ? 'max-width:'.setting('site_logo_max_width').'px;' : '' }}" />
                            </div>
                            <div class="row mt-2 gx-2 gy-2">
                                <div class="col-auto">
                                    <label class="form-label small mb-1">Max Altura (px)</label>
                                    <input type="number" min="0" id="identityLogoMaxHeight" class="form-control form-control-sm" value="{{ setting('site_logo_max_height', 48) }}">
                                </div>
                                <div class="col-auto">
                                    <label class="form-label small mb-1">Max Largura (px)</label>
                                    <input type="number" min="0" id="identityLogoMaxWidth" class="form-control form-control-sm" value="{{ setting('site_logo_max_width', '') }}">
                                </div>
                                <div class="col-auto align-self-end">
                                    <button type="button" class="btn btn-sm btn-secondary" id="saveLogoSizeBtn">Salvar tamanho</button>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>

        {{-- Seção PWA - Configuração de Ícones --}}
        <div class="col-12 mt-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-phone me-2"></i>PWA - Progressive Web App</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">
                        Configure os ícones para que seu site possa ser instalado como aplicativo no celular. 
                        O botão de instalação aparecerá automaticamente quando todos os requisitos estiverem configurados.
                    </p>
                    
                    <div class="row g-4">
                        {{-- App Icon --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-app me-1"></i>App Icon (PWA)
                                <small class="text-muted d-block">Ícone principal para instalação (mínimo 192x192px)</small>
                            </label>
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex gap-2 mb-3">
                                        <input type="file" id="appIconFile" accept="image/png,image/jpeg,image/jpg,image/webp" class="form-control form-control-sm" />
                                        <button type="button" id="appIconUploadBtn" class="btn btn-sm btn-primary">
                                            <i class="bi bi-upload me-1"></i>Enviar
                                        </button>
                                    </div>
                                    <div class="text-center p-3 bg-light rounded">
                                        <img id="appIconPreview" 
                                             src="{{ setting('site_app_icon') ? asset('storage/' . setting('site_app_icon')) . '?v=' . time() : asset('images/no-image.svg') }}" 
                                             alt="App Icon" 
                                             style="max-width: 128px; max-height: 128px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" />
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Recomendado: 512x512px</small>
                                            @if(setting('site_app_icon'))
                                                <span class="badge bg-success mt-2">
                                                    <i class="bi bi-check-circle me-1"></i>Configurado
                                                </span>
                                            @else
                                                <span class="badge bg-warning mt-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Não configurado
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Favicon --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-star me-1"></i>Favicon
                                <small class="text-muted d-block">Ícone exibido na aba do navegador</small>
                            </label>
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex gap-2 mb-3">
                                        <input type="file" id="faviconFile" accept="image/png,image/ico,image/x-icon,image/svg+xml" class="form-control form-control-sm" />
                                        <button type="button" id="faviconUploadBtn" class="btn btn-sm btn-primary">
                                            <i class="bi bi-upload me-1"></i>Enviar
                                        </button>
                                    </div>
                                    <div class="text-center p-3 bg-light rounded">
                                        <img id="faviconPreview" 
                                             src="{{ setting('site_favicon') ? asset('storage/' . setting('site_favicon')) . '?v=' . time() : asset('images/no-image.svg') }}" 
                                             alt="Favicon" 
                                             style="max-width: 64px; max-height: 64px; border-radius: 8px;" />
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Recomendado: 32x32px ou 64x64px</small>
                                            @if(setting('site_favicon'))
                                                <span class="badge bg-success mt-2">
                                                    <i class="bi bi-check-circle me-1"></i>Configurado
                                                </span>
                                            @else
                                                <span class="badge bg-warning mt-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Não configurado
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status do PWA --}}
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Status do PWA</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Manifest</small>
                                <a href="{{ route('manifest') }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-link-45deg me-1"></i>Ver Manifest
                                </a>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Service Worker</small>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Ativo
                                </span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">HTTPS</small>
                                <span class="badge {{ request()->getScheme() === 'https' ? 'bg-success' : 'bg-warning' }}">
                                    <i class="bi bi-{{ request()->getScheme() === 'https' ? 'check' : 'exclamation-triangle' }}-circle me-1"></i>
                                    {{ request()->getScheme() === 'https' ? 'Ativo' : 'Requerido' }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-lightbulb me-1"></i>
                                <strong>Dica:</strong> Para o botão de instalação aparecer, você precisa: 
                                HTTPS ativo, manifest.json válido, service worker registrado e ícones configurados.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
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

                            <!-- Melhor Envio - Configurações de Logística -->
                            <div class="col-12 mb-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="card-title mb-0"><i class="bi bi-truck me-2"></i>Melhor Envio</h6>
                                            <small class="text-muted">Configurações de cálculo e envio de fretes</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="melhor_envio_enabled" 
                                                   {{ setting('melhor_envio_enabled', false) ? 'checked' : '' }}
                                                   onchange="updateStatusText('melhor_envio_enabled')">
                                            <label class="form-check-label" for="melhor_envio_enabled" id="melhor_envio_enabled_label">
                                                {{ setting('melhor_envio_enabled', false) ? 'Ativo' : 'Inativo' }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $hasToken = !empty(setting('melhor_envio_token'));
                                            $tokenExpiresAt = setting('melhor_envio_token_expires_at');
                                            $isTokenExpired = $tokenExpiresAt && \Carbon\Carbon::parse($tokenExpiresAt)->isPast();
                                        @endphp

                                        <!-- Status da Conexão -->
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
                                                <strong>Não conectado.</strong> Configure as credenciais abaixo e clique em "Conectar ao Melhor Envio" para autorizar.
                                            </div>
                                        @endif

                                        <!-- Credenciais OAuth -->
                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3">
                                                <label for="melhor_envio_client_id" class="form-label">
                                                    Client ID <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="melhor_envio_client_id" 
                                                       value="{{ setting('melhor_envio_client_id', '') }}" 
                                                       placeholder="Seu Client ID do Melhor Envio">
                                                <small class="form-text text-muted">
                                                    Obtenha em: <a href="https://melhorenvio.com.br/painel/desenvolvedor" target="_blank">melhorenvio.com.br/painel/desenvolvedor</a>
                                                </small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="melhor_envio_client_secret" class="form-label">
                                                    Client Secret <span class="text-danger">*</span>
                                                </label>
                                                <input type="password" class="form-control" id="melhor_envio_client_secret" 
                                                       value="{{ setting('melhor_envio_client_secret', '') }}" 
                                                       placeholder="Seu Client Secret do Melhor Envio">
                                                <small class="form-text text-muted">Mantenha em sigilo</small>
                                            </div>
                                        </div>

                                        <!-- Ambiente e CEP de Origem -->
                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3">
                                                <label for="melhor_envio_sandbox" class="form-label">Ambiente</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="melhor_envio_sandbox" 
                                                           {{ setting('melhor_envio_sandbox', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="melhor_envio_sandbox">
                                                        Modo Sandbox (Teste)
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Desmarque para usar em produção
                                                </small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="melhor_envio_cep_origem" class="form-label">
                                                    CEP de Origem <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="melhor_envio_cep_origem" 
                                                       value="{{ setting('melhor_envio_cep_origem', '') }}" 
                                                       placeholder="00000-000" maxlength="9">
                                                <small class="form-text text-muted">CEP da sua loja (apenas números)</small>
                                            </div>
                                        </div>

                                        <!-- Serviços de Entrega -->
                                        <div class="mb-3">
                                            <label for="melhor_envio_service_ids" class="form-label">Serviços de Entrega</label>
                                            <input type="text" class="form-control" id="melhor_envio_service_ids" 
                                                   value="{{ setting('melhor_envio_service_ids', '') }}" 
                                                   placeholder="Ex: 1,2,3,4 (IDs separados por vírgula)">
                                            <small class="form-text text-muted">
                                                IDs dos serviços habilitados. Deixe vazio para usar todos. 
                                                <a href="https://melhorenvio.com.br/documentacao/api" target="_blank">Ver documentação</a>
                                            </small>
                                        </div>

                                        <!-- Configurações Avançadas -->
                                        <div class="accordion mb-3" id="melhorEnvioAdvanced">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#melhorEnvioAdvancedCollapse">
                                                        <i class="bi bi-gear me-2"></i>Configurações Avançadas
                                                    </button>
                                                </h2>
                                                <div id="melhorEnvioAdvancedCollapse" class="accordion-collapse collapse" data-bs-parent="#melhorEnvioAdvanced">
                                                    <div class="accordion-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="melhor_envio_declared_mode" class="form-label">Modo de Declaração</label>
                                                                <select class="form-select" id="melhor_envio_declared_mode">
                                                                    <option value="declared" {{ setting('melhor_envio_declared_mode', 'declared') == 'declared' ? 'selected' : '' }}>Declarado</option>
                                                                    <option value="not_declared" {{ setting('melhor_envio_declared_mode') == 'not_declared' ? 'selected' : '' }}>Não Declarado</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="melhor_envio_declared_cap" class="form-label">Capacidade de Declaração (R$)</label>
                                                                <input type="number" class="form-control" id="melhor_envio_declared_cap" 
                                                                       value="{{ setting('melhor_envio_declared_cap', '') }}" 
                                                                       placeholder="0.00" step="0.01" min="0">
                                                                <small class="form-text text-muted">Valor máximo para declaração</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Botões de Ação -->
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button type="button" class="btn btn-primary" onclick="saveDeliveryConfig('melhor_envio')">
                                                <i class="bi bi-save me-1"></i>Salvar Configurações
                                            </button>
                                            @if(!empty(setting('melhor_envio_client_id')) && !empty(setting('melhor_envio_client_secret')))
                                                @if(!$hasToken)
                                                    <a href="{{ route('admin.settings.melhor-envio.authorize') }}" class="btn btn-success">
                                                        <i class="bi bi-link-45deg me-1"></i>Conectar ao Melhor Envio
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-outline-primary" onclick="testDeliveryConnection('melhor_envio')">
                                                    <i class="bi bi-wifi me-1"></i>Testar Conexão
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Informações de Ajuda -->
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <h6 class="mb-2"><i class="bi bi-question-circle me-2"></i>Como configurar:</h6>
                                            <ol class="mb-0 small">
                                                <li>Acesse <a href="https://melhorenvio.com.br/painel/desenvolvedor" target="_blank">melhorenvio.com.br/painel/desenvolvedor</a></li>
                                                <li>Crie uma aplicação e obtenha o <strong>Client ID</strong> e <strong>Client Secret</strong></li>
                                                <li>Preencha os campos acima e clique em "Salvar Configurações"</li>
                                                <li>Clique em "Conectar ao Melhor Envio" para autorizar o acesso</li>
                                                <li>Configure o CEP de origem da sua loja</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loja Física / PDV - Sincronização -->
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between bg-primary text-white">
                        <div>
                            <h6 class="card-title mb-0"><i class="bi bi-shop me-2"></i>Loja Física / PDV</h6>
                            <small class="text-white-50">Sincronização entre e-commerce e loja física</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enable_physical_store_sync" 
                                   {{ setting('enable_physical_store_sync', false) ? 'checked' : '' }}
                                   onchange="togglePhysicalStoreSync()"
                                   style="transform: scale(1.3);">
                            <label class="form-check-label text-white" for="enable_physical_store_sync">
                                <strong>Ativar Sincronização</strong>
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="physicalStoreConfig" style="{{ !setting('enable_physical_store_sync', false) ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Importante:</strong> Ao ativar a sincronização, o estoque será unificado entre e-commerce e loja física. 
                            Você pode desativar a qualquer momento sem perder dados.
                        </div>

                        <div class="row g-3">
                            <!-- Nome da Loja Física -->
                            <div class="col-md-6">
                                <label for="physical_store_name" class="form-label">
                                    <i class="bi bi-shop me-1"></i> Nome da Loja Física
                                </label>
                                <input type="text" class="form-control" id="physical_store_name" 
                                       value="{{ setting('physical_store_name', '') }}" 
                                       placeholder="Ex: Loja Centro">
                                <small class="form-text text-muted">Nome identificador da loja física</small>
                            </div>

                            <!-- Tempo de Reserva de Estoque -->
                            <div class="col-md-6">
                                <label for="inventory_reservation_time" class="form-label">
                                    <i class="bi bi-clock me-1"></i> Tempo de Reserva (minutos)
                                </label>
                                <input type="number" class="form-control" id="inventory_reservation_time" 
                                       value="{{ setting('inventory_reservation_time', 15) }}" 
                                       min="5" max="60">
                                <small class="form-text text-muted">Tempo que o estoque fica reservado no carrinho</small>
                            </div>

                            <!-- Intervalo de Sincronização -->
                            <div class="col-md-6">
                                <label for="auto_sync_interval" class="form-label">
                                    <i class="bi bi-arrow-repeat me-1"></i> Intervalo de Sincronização (minutos)
                                </label>
                                <input type="number" class="form-control" id="auto_sync_interval" 
                                       value="{{ setting('auto_sync_interval', 5) }}" 
                                       min="1" max="60">
                                <small class="form-text text-muted">Intervalo para sincronização automática</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Opções de Sincronização -->
                        <h6 class="mb-3"><i class="bi bi-sliders me-2"></i>Opções de Sincronização</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="sync_inventory" 
                                                   {{ setting('sync_inventory', false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sync_inventory">
                                                <strong><i class="bi bi-box-seam me-1"></i> Sincronizar Estoque</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            Estoque unificado entre e-commerce e loja física
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="sync_sales" 
                                                   {{ setting('sync_sales', false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sync_sales">
                                                <strong><i class="bi bi-cart-check me-1"></i> Sincronizar Vendas</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            Vendas da loja física aparecem no e-commerce
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="sync_coupons" 
                                                   {{ setting('sync_coupons', false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sync_coupons">
                                                <strong><i class="bi bi-ticket-perforated me-1"></i> Sincronizar Cupons</strong>
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            Cupons válidos em ambos os canais
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status da Sincronização -->
                        <div class="mt-4 p-3 bg-light rounded" id="syncStatus">
                            <h6 class="mb-2"><i class="bi bi-activity me-2"></i>Status da Sincronização</h6>
                            <div id="syncStatusContent">
                                <p class="text-muted mb-0">Carregando...</p>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="mt-4 d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="savePhysicalStoreConfig()">
                                <i class="bi bi-save me-1"></i> Salvar Configurações
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="checkSyncStatus()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Verificar Status
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
                        // Atualizar logo no header do admin também (se estiver visível)
                        setTimeout(function() {
                            var adminLogos = document.querySelectorAll('#admin-site-logo, #siteLogoImage, .mobile-logo img.logo-img, .logo-img');
                            adminLogos.forEach(function(logo) {
                                if (h) {
                                    logo.style.setProperty('max-height', h + 'px', 'important');
                                    logo.setAttribute('style', logo.getAttribute('style') + ' max-height:' + h + 'px !important;');
                                }
                                if (w) {
                                    logo.style.setProperty('max-width', w + 'px', 'important');
                                    logo.setAttribute('style', logo.getAttribute('style') + ' max-width:' + w + 'px !important;');
                                }
                                logo.style.setProperty('height', 'auto', 'important');
                                logo.style.setProperty('width', 'auto', 'important');
                            });
                            
                            // Forçar reload da página após 1 segundo para garantir que o tamanho seja aplicado em todas as páginas
                            setTimeout(function() {
                                if (confirm('Tamanho salvo! Deseja recarregar a página para ver as mudanças em todas as logos?')) {
                                    window.location.reload();
                                }
                            }, 1000);
                        }, 200);
                    } else {
                        showAlert('Erro ao salvar tamanho.', 'danger');
                    }
                }).catch(err => { console.error(err); showAlert('Erro ao salvar tamanho.', 'danger'); });
        });
    }

    
});

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

    // Máscara para CEP Melhor Envio
    const melhorEnvioCepInput = document.getElementById('melhor_envio_cep_origem');
    if (melhorEnvioCepInput) {
        melhorEnvioCepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
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

// ============================================
// FUNÇÕES PARA LOJA FÍSICA / PDV
// ============================================

// Toggle do master switch
function togglePhysicalStoreSync() {
    const checkbox = document.getElementById('enable_physical_store_sync');
    const configDiv = document.getElementById('physicalStoreConfig');
    
    if (checkbox.checked) {
        configDiv.style.opacity = '1';
        configDiv.style.pointerEvents = 'auto';
    } else {
        configDiv.style.opacity = '0.5';
        configDiv.style.pointerEvents = 'none';
    }
    
    // Salvar imediatamente
    savePhysicalStoreConfig();
}

// Salvar configurações da loja física
function savePhysicalStoreConfig() {
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    
    const fields = [
        'enable_physical_store_sync',
        'physical_store_name',
        'sync_inventory',
        'sync_sales',
        'sync_coupons',
        'inventory_reservation_time',
        'auto_sync_interval'
    ];
    
    fields.forEach(field => {
        const element = document.getElementById(field);
        if (element) {
            formData.append(field, element.type === 'checkbox' ? (element.checked ? '1' : '0') : element.value);
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Configurações da loja física salvas com sucesso!', 'success');
            checkSyncStatus(); // Atualizar status
        } else {
            showAlert('Erro ao salvar configurações: ' + (data.message || 'Erro desconhecido'), 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('Erro ao salvar configurações.', 'danger');
    });
}

// Verificar status da sincronização
function checkSyncStatus() {
    const statusDiv = document.getElementById('syncStatusContent');
    if (!statusDiv) return;
    
    statusDiv.innerHTML = '<p class="text-muted mb-0"><i class="bi bi-hourglass-split me-1"></i> Verificando...</p>';
    
    // Usar o SyncService via uma rota ou endpoint
    fetch('/admin/settings/sync-status', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            const status = data.status;
            let html = '';
            
            if (!status.enabled) {
                html = '<div class="alert alert-secondary mb-0"><i class="bi bi-pause-circle me-2"></i>Sincronização desativada</div>';
            } else {
                html = `
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="card border ${status.inventory_sync ? 'border-success' : 'border-secondary'}">
                                <div class="card-body p-2">
                                    <small class="d-block"><strong>Estoque:</strong></small>
                                    <span class="badge ${status.inventory_sync ? 'bg-success' : 'bg-secondary'}">
                                        ${status.inventory_sync ? 'Ativo' : 'Inativo'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border ${status.sales_sync ? 'border-success' : 'border-secondary'}">
                                <div class="card-body p-2">
                                    <small class="d-block"><strong>Vendas:</strong></small>
                                    <span class="badge ${status.sales_sync ? 'bg-success' : 'bg-secondary'}">
                                        ${status.sales_sync ? 'Ativo' : 'Inativo'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border ${status.coupons_sync ? 'border-success' : 'border-secondary'}">
                                <div class="card-body p-2">
                                    <small class="d-block"><strong>Cupons:</strong></small>
                                    <span class="badge ${status.coupons_sync ? 'bg-success' : 'bg-secondary'}">
                                        ${status.coupons_sync ? 'Ativo' : 'Inativo'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${status.pending_syncs > 0 ? `<div class="alert alert-warning mt-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i>${status.pending_syncs} sincronizações pendentes</div>` : ''}
                    ${status.recent_failures > 0 ? `<div class="alert alert-danger mt-2 mb-0"><i class="bi bi-x-circle me-1"></i>${status.recent_failures} falhas recentes</div>` : ''}
                `;
            }
            
            statusDiv.innerHTML = html;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        statusDiv.innerHTML = '<p class="text-danger mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Erro ao verificar status</p>';
    });
}

// Carregar status ao abrir a página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('syncStatus')) {
        setTimeout(checkSyncStatus, 500);
    }
});
</script>
@endsection
