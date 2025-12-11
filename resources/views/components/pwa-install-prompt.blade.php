<!-- PWA Install Prompt -->
<div id="pwa-install-prompt" style="display: none;">
    <div class="pwa-install-banner">
        <div class="pwa-install-content">
            <div class="pwa-install-icon">
                <i class="bi bi-download"></i>
            </div>
            <div class="pwa-install-text">
                <strong>Instalar App</strong>
                <small>{{ setting('site_name', 'Feira das Fábricas') }}</small>
            </div>
            <div class="pwa-install-actions">
                <button id="pwa-install-button" class="btn btn-sm btn-primary">
                    <i class="bi bi-download me-1"></i>
                    Instalar
                </button>
                <button id="pwa-install-dismiss" class="btn btn-sm btn-link text-white" aria-label="Fechar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#pwa-install-prompt {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99998;
    padding: 0;
    animation: slideUpPWA 0.3s ease-out;
}

.pwa-install-banner {
    background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, var(--primary-color, #0f172a) 100%);
    color: white;
    padding: 12px 16px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.pwa-install-content {
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: 1200px;
    margin: 0 auto;
}

.pwa-install-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.pwa-install-text {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.pwa-install-text strong {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.2;
}

.pwa-install-text small {
    font-size: 12px;
    opacity: 0.9;
    line-height: 1.2;
}

.pwa-install-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

#pwa-install-button {
    white-space: nowrap;
    padding: 6px 16px;
    font-weight: 600;
    border-radius: 6px;
}

#pwa-install-dismiss {
    padding: 4px 8px;
    color: white;
    opacity: 0.8;
    min-width: auto;
}

#pwa-install-dismiss:hover {
    opacity: 1;
    color: white;
}

@keyframes slideUpPWA {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Desktop: Banner no topo */
@media (min-width: 769px) {
    #pwa-install-prompt {
        bottom: auto;
        top: 0;
        animation: slideDownPWA 0.3s ease-out;
    }
    
    .pwa-install-banner {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
}

@keyframes slideDownPWA {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Mobile: Ajustar padding para não sobrepor conteúdo */
@media (max-width: 768px) {
    body.pwa-prompt-visible {
        padding-bottom: 80px;
    }
}
</style>

<script>
(function() {
    let deferredPrompt = null;
    const promptElement = document.getElementById('pwa-install-prompt');
    const installButton = document.getElementById('pwa-install-button');
    const dismissButton = document.getElementById('pwa-install-dismiss');
    
    if (!promptElement) return;

    // Verificar se já foi instalado ou rejeitado
    const installPromptDismissed = localStorage.getItem('pwa-install-dismissed');
    const installPromptInstalled = localStorage.getItem('pwa-installed');
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                         window.navigator.standalone || 
                         document.referrer.includes('android-app://');

    // Não mostrar se já instalado ou se foi rejeitado nas últimas 30 dias
    if (isStandalone || installPromptInstalled === 'true') {
        return;
    }

    if (installPromptDismissed) {
        const daysSinceDismiss = (Date.now() - parseInt(installPromptDismissed)) / (1000 * 60 * 60 * 24);
        if (daysSinceDismiss < 30) {
            return; // Não mostrar novamente por 30 dias
        }
    }

    // Debug: Verificar requisitos do PWA
    function checkPWARequirements() {
        const checks = {
            manifest: document.querySelector('link[rel="manifest"]') !== null,
            serviceWorker: 'serviceWorker' in navigator,
            https: location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1',
            icons: true // Será verificado pelo manifest
        };
        
        console.log('🔍 PWA Requirements Check:', checks);
        
        // Verificar manifest
        const manifestLink = document.querySelector('link[rel="manifest"]');
        if (manifestLink) {
            fetch(manifestLink.href)
                .then(res => res.json())
                .then(manifest => {
                    console.log('✅ Manifest válido:', manifest);
                    console.log('📱 Ícones no manifest:', manifest.icons?.length || 0);
                    if (!manifest.icons || manifest.icons.length === 0) {
                        console.error('❌ Manifest não tem ícones!');
                    }
                })
                .catch(err => {
                    console.error('❌ Erro ao carregar manifest:', err);
                });
        }
        
        return checks;
    }
    
    // Executar verificação após carregar
    setTimeout(checkPWARequirements, 1000);
    
    // Capturar evento beforeinstallprompt (Chrome/Edge/Opera)
    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('🎉 beforeinstallprompt disparado!');
        e.preventDefault();
        deferredPrompt = e;
        showPrompt();
    });
    
    // Log se o evento não disparar após 5 segundos
    setTimeout(() => {
        if (!deferredPrompt) {
            console.warn('⚠️ beforeinstallprompt não foi disparado. Verifique:');
            console.warn('  1. Manifest está acessível e válido?');
            console.warn('  2. Service Worker está registrado?');
            console.warn('  3. Ícones estão acessíveis?');
            console.warn('  4. Site está em HTTPS ou localhost?');
            checkPWARequirements();
        }
    }, 5000);

    // Para iOS Safari - mostrar instruções
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    
    if (isIOS && isSafari && !isStandalone) {
        // Verificar se foi rejeitado nas últimas 30 dias
        let shouldShowIOS = true;
        if (installPromptDismissed) {
            const daysSinceDismiss = (Date.now() - parseInt(installPromptDismissed)) / (1000 * 60 * 60 * 24);
            if (daysSinceDismiss < 30) {
                shouldShowIOS = false;
            }
        }
        
        if (shouldShowIOS) {
            // Mostrar prompt após alguns segundos para iOS
            setTimeout(() => {
                showIOSPrompt();
            }, 3000);
        }
    }

    function showPrompt() {
        if (deferredPrompt) {
            promptElement.style.display = 'block';
            document.body.classList.add('pwa-prompt-visible');
        }
    }

    function showIOSPrompt() {
        // Criar prompt específico para iOS com instruções
        const iosPrompt = document.createElement('div');
        iosPrompt.id = 'pwa-ios-prompt';
        iosPrompt.innerHTML = `
            <div class="pwa-install-banner">
                <div class="pwa-install-content">
                    <div class="pwa-install-icon">
                        <i class="bi bi-download"></i>
                    </div>
                    <div class="pwa-install-text">
                        <strong>Instalar App</strong>
                        <small>Toque em <i class="bi bi-box-arrow-up"></i> e selecione "Adicionar à Tela de Início"</small>
                    </div>
                    <button id="pwa-ios-dismiss" class="btn btn-sm btn-link text-white" aria-label="Fechar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        `;
        iosPrompt.style.cssText = 'position: fixed; bottom: 0; left: 0; right: 0; z-index: 99998; padding: 0; animation: slideUpPWA 0.3s ease-out;';
        document.body.appendChild(iosPrompt);
        document.body.classList.add('pwa-prompt-visible');

        document.getElementById('pwa-ios-dismiss').addEventListener('click', () => {
            localStorage.setItem('pwa-install-dismissed', Date.now());
            iosPrompt.remove();
            document.body.classList.remove('pwa-prompt-visible');
        });
    }

    // Botão de instalação
    if (installButton) {
        installButton.addEventListener('click', async () => {
            if (!deferredPrompt) return;

            // Mostrar prompt de instalação
            deferredPrompt.prompt();
            
            // Aguardar resposta do usuário
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                localStorage.setItem('pwa-installed', 'true');
                promptElement.style.display = 'none';
                document.body.classList.remove('pwa-prompt-visible');
            }
            
            deferredPrompt = null;
            promptElement.style.display = 'none';
            document.body.classList.remove('pwa-prompt-visible');
        });
    }

    // Botão de fechar
    if (dismissButton) {
        dismissButton.addEventListener('click', () => {
            localStorage.setItem('pwa-install-dismissed', Date.now());
            promptElement.style.display = 'none';
            document.body.classList.remove('pwa-prompt-visible');
        });
    }

    // Detectar quando o app foi instalado (após instalação)
    window.addEventListener('appinstalled', () => {
        localStorage.setItem('pwa-installed', 'true');
        promptElement.style.display = 'none';
        document.body.classList.remove('pwa-prompt-visible');
        deferredPrompt = null;
    });
})();
</script>
