@if($badge)
<div id="promotional-badge" 
     class="promotional-badge" 
     data-position="{{ $badge->position }}"
     data-auto-close="{{ $badge->auto_close_seconds }}"
     style="display: none;">
    <div class="promotional-badge-content">
        @if($badge->link)
            <a href="{{ $badge->link }}" class="promotional-badge-link">
                @if($badge->image_url)
                    <img src="{{ $badge->image_url }}" alt="{{ $badge->text }}" class="promotional-badge-image">
                @endif
                <div class="promotional-badge-text">{{ $badge->text }}</div>
            </a>
        @else
            <div class="promotional-badge-wrapper">
                @if($badge->image_url)
                    <img src="{{ $badge->image_url }}" alt="{{ $badge->text }}" class="promotional-badge-image">
                @endif
                <div class="promotional-badge-text">{{ $badge->text }}</div>
            </div>
        @endif
        
        @if($badge->show_close_button)
            <button type="button" class="promotional-badge-close" onclick="this.closest('.promotional-badge').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        @endif
    </div>
</div>

<style>
.promotional-badge {
    position: fixed;
    z-index: 99999;
    max-width: 90%;
}

.promotional-badge[data-position="center-bottom"] {
    bottom: 20px;
    left: 50%;
    right: auto;
    transform: translateX(-50%);
    animation: slideUpCenter 0.3s ease-out;
}

.promotional-badge[data-position="bottom-right"] {
    bottom: 20px;
    right: 20px;
    animation: slideUp 0.3s ease-out;
}

.promotional-badge[data-position="bottom-left"] {
    bottom: 20px;
    left: 20px;
    animation: slideUp 0.3s ease-out;
}

.promotional-badge[data-position="top-right"] {
    top: 20px;
    right: 20px;
    animation: slideDown 0.3s ease-out;
}

.promotional-badge[data-position="top-left"] {
    top: 20px;
    left: 20px;
    animation: slideDown 0.3s ease-out;
}

.promotional-badge[data-position="center-top"] {
    top: 20px;
    left: 50%;
    right: auto;
    transform: translateX(-50%);
    animation: slideDownCenter 0.3s ease-out;
}

.promotional-badge[data-position="center"] {
    top: 50%;
    left: 50%;
    right: auto;
    bottom: auto;
    transform: translate(-50%, -50%);
    animation: fadeInScale 0.3s ease-out;
}

.promotional-badge-content {
    background: #ffffff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    position: relative;
    max-width: 400px;
}

.promotional-badge-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.promotional-badge-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.promotional-badge-image {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

.promotional-badge-text {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    text-align: center;
}

.promotional-badge-close {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    border: none;
    background: rgba(0,0,0,0.5);
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.promotional-badge-close:hover {
    background: rgba(239,68,68,0.9);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUpCenter {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideDownCenter {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@media (max-width: 768px) {
    .promotional-badge {
        max-width: calc(100% - 40px);
    }
    
    /* Posições inferiores no mobile */
    .promotional-badge[data-position="center-bottom"],
    .promotional-badge[data-position="bottom-right"],
    .promotional-badge[data-position="bottom-left"] {
        bottom: 20px !important;
        top: auto !important;
        left: 20px !important;
        right: 20px !important;
        transform: none !important;
    }
    
    /* Posições superiores no mobile */
    .promotional-badge[data-position="center-top"],
    .promotional-badge[data-position="top-right"],
    .promotional-badge[data-position="top-left"] {
        top: 20px !important;
        bottom: auto !important;
        left: 20px !important;
        right: 20px !important;
        transform: none !important;
    }
    
    /* Posição central no mobile */
    .promotional-badge[data-position="center"] {
        top: 50% !important;
        left: 50% !important;
        right: auto !important;
        bottom: auto !important;
        transform: translate(-50%, -50%) !important;
        max-width: calc(100% - 40px);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const badge = document.getElementById('promotional-badge');
    if (!badge) return;

    // Verificar se foi fechado recentemente (últimas 24h) - ANTES de qualquer coisa
    const closedTime = localStorage.getItem('promotional_badge_closed');
    if (closedTime) {
        const hoursSinceClose = (Date.now() - parseInt(closedTime)) / (1000 * 60 * 60);
        if (hoursSinceClose < 24) {
            badge.remove();
            return; // Para aqui se foi fechado recentemente
        }
    }

    // Mostrar badge após 1 segundo
    setTimeout(function() {
        // Verificar novamente se o badge ainda existe antes de mostrar
        if (badge && badge.parentNode) {
            badge.style.display = 'block';
        }
    }, 1000);

    // Auto-close se configurado
    const autoClose = parseInt(badge.getAttribute('data-auto-close')) || 0;
    if (autoClose > 0) {
        setTimeout(function() {
            // Verificar se o badge ainda existe
            if (badge && badge.parentNode) {
                badge.style.opacity = '0';
                badge.style.transition = 'opacity 0.3s';
                setTimeout(function() {
                    if (badge && badge.parentNode) {
                        badge.remove();
                    }
                }, 300);
            }
        }, autoClose * 1000);
    }

    // Salvar no localStorage que foi fechado (por 24h)
    const closeBtn = badge.querySelector('.promotional-badge-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            localStorage.setItem('promotional_badge_closed', Date.now());
        });
    }
});
</script>
@endif
