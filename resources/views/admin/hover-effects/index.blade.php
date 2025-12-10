@extends('admin.layouts.app')

@section('title', 'Efeitos de Hover')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-cursor-fill me-2"></i>
                            Configurações de Efeitos Hover
                        </h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="hoverEffectsForm">
                        @csrf
                        
                        <!-- Enable/Disable Hover Effects -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="hover_effects_enabled" 
                                       name="hover_effects_enabled" 
                                       value="1"
                                       {{ $hoverEffects['enabled'] ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="hover_effects_enabled">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Ativar Efeitos de Hover
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Quando desativado, todos os efeitos de hover serão desabilitados em produtos, cards e botões.
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Transform Scale -->
                        <div class="mb-4">
                            <label for="hover_transform_scale" class="form-label fw-semibold">
                                <i class="bi bi-arrows-angle-expand me-2"></i>
                                Escala no Hover (Zoom)
                            </label>
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <input type="range" 
                                           class="form-range" 
                                           id="hover_transform_scale" 
                                           name="hover_transform_scale" 
                                           min="0.5" 
                                           max="2" 
                                           step="0.05" 
                                           value="{{ $hoverEffects['transform_scale'] }}"
                                           oninput="document.getElementById('scaleValue').textContent = this.value">
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-secondary fs-6" id="scaleValue">{{ $hoverEffects['transform_scale'] }}</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Controla o quanto os elementos aumentam ao passar o mouse (1.0 = sem zoom, 1.05 = 5% maior).
                            </small>
                        </div>

                        <!-- Transform Translate Y -->
                        <div class="mb-4">
                            <label for="hover_transform_translate_y" class="form-label fw-semibold">
                                <i class="bi bi-arrows-expand-vertical me-2"></i>
                                Elevação no Hover (px)
                            </label>
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <input type="range" 
                                           class="form-range" 
                                           id="hover_transform_translate_y" 
                                           name="hover_transform_translate_y" 
                                           min="-50" 
                                           max="50" 
                                           step="1" 
                                           value="{{ $hoverEffects['transform_translate_y'] }}"
                                           oninput="document.getElementById('translateYValue').textContent = this.value + 'px'">
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-secondary fs-6" id="translateYValue">{{ $hoverEffects['transform_translate_y'] }}px</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Controla quantos pixels o elemento sobe ao passar o mouse (negativo = desce, positivo = sobe).
                            </small>
                        </div>

                        <!-- Shadow Intensity -->
                        <div class="mb-4">
                            <label for="hover_shadow_intensity" class="form-label fw-semibold">
                                <i class="bi bi-circle-fill me-2"></i>
                                Intensidade da Sombra (px)
                            </label>
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <input type="range" 
                                           class="form-range" 
                                           id="hover_shadow_intensity" 
                                           name="hover_shadow_intensity" 
                                           min="0" 
                                           max="100" 
                                           step="2" 
                                           value="{{ $hoverEffects['shadow_intensity'] }}"
                                           oninput="document.getElementById('shadowValue').textContent = this.value + 'px'">
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-secondary fs-6" id="shadowValue">{{ $hoverEffects['shadow_intensity'] }}px</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Controla o tamanho da sombra que aparece no hover (0 = sem sombra, maior = sombra mais intensa).
                            </small>
                        </div>

                        <!-- Transition Duration -->
                        <div class="mb-4">
                            <label for="hover_transition_duration" class="form-label fw-semibold">
                                <i class="bi bi-hourglass-split me-2"></i>
                                Duração da Animação (segundos)
                            </label>
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <input type="range" 
                                           class="form-range" 
                                           id="hover_transition_duration" 
                                           name="hover_transition_duration" 
                                           min="0.1" 
                                           max="2" 
                                           step="0.1" 
                                           value="{{ $hoverEffects['transition_duration'] }}"
                                           oninput="document.getElementById('durationValue').textContent = this.value + 's'">
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-secondary fs-6" id="durationValue">{{ $hoverEffects['transition_duration'] }}s</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Controla a velocidade da animação de hover (menor = mais rápido, maior = mais lento).
                            </small>
                        </div>

                        <!-- Transition Easing -->
                        <div class="mb-4">
                            <label for="hover_transition_easing" class="form-label fw-semibold">
                                <i class="bi bi-graph-up me-2"></i>
                                Tipo de Animação
                            </label>
                            <select class="form-select" id="hover_transition_easing" name="hover_transition_easing">
                                <option value="linear" {{ $hoverEffects['transition_easing'] === 'linear' ? 'selected' : '' }}>Linear (constante)</option>
                                <option value="ease" {{ $hoverEffects['transition_easing'] === 'ease' ? 'selected' : '' }}>Ease (suave)</option>
                                <option value="ease-in" {{ $hoverEffects['transition_easing'] === 'ease-in' ? 'selected' : '' }}>Ease-in (acelera no início)</option>
                                <option value="ease-out" {{ $hoverEffects['transition_easing'] === 'ease-out' ? 'selected' : '' }}>Ease-out (desacelera no fim)</option>
                                <option value="ease-in-out" {{ $hoverEffects['transition_easing'] === 'ease-in-out' ? 'selected' : '' }}>Ease-in-out (suave ambos lados)</option>
                                <option value="cubic-bezier(0.4, 0, 0.2, 1)" {{ $hoverEffects['transition_easing'] === 'cubic-bezier(0.4, 0, 0.2, 1)' ? 'selected' : '' }}>Cubic Bezier (material design)</option>
                                <option value="cubic-bezier(0.25, 0.46, 0.45, 0.94)" {{ $hoverEffects['transition_easing'] === 'cubic-bezier(0.25, 0.46, 0.45, 0.94)' ? 'selected' : '' }}>Cubic Bezier (ease-out-quad)</option>
                            </select>
                            <small class="text-muted d-block mt-2">
                                Controla como a animação acelera e desacelera durante a transição.
                            </small>
                        </div>

                        <!-- Border Color Intensity -->
                        <div class="mb-4">
                            <label for="hover_border_color_intensity" class="form-label fw-semibold">
                                <i class="bi bi-palette me-2"></i>
                                Intensidade da Cor da Borda no Hover
                            </label>
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <input type="range" 
                                           class="form-range" 
                                           id="hover_border_color_intensity" 
                                           name="hover_border_color_intensity" 
                                           min="0" 
                                           max="1" 
                                           step="0.1" 
                                           value="{{ $hoverEffects['border_color_intensity'] }}"
                                           oninput="document.getElementById('borderIntensityValue').textContent = Math.round(this.value * 100) + '%'">
                                </div>
                                <div class="col-md-3">
                                    <span class="badge bg-secondary fs-6" id="borderIntensityValue">{{ round($hoverEffects['border_color_intensity'] * 100) }}%</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Controla a opacidade/intensidade da cor da borda ao passar o mouse (0% = transparente, 100% = cor completa).
                            </small>
                        </div>

                        <div class="alert alert-info d-flex align-items-center">
                            <i class="bi bi-info-circle me-2 fs-5"></i>
                            <div>
                                <strong>Dica:</strong> As alterações são aplicadas em tempo real em produtos, cards, botões e elementos interativos do site.
                                Use os sliders para ajustar e ver o preview em tempo real!
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetToDefaults()">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>
                                Restaurar Padrões
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>
                                Salvar Configurações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('hoverEffectsForm');
    const token = document.querySelector('meta[name="csrf-token"]').content;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        
        // Converter checkbox para boolean
        formData.set('hover_effects_enabled', document.getElementById('hover_effects_enabled').checked ? '1' : '0');

        fetch('{{ route("admin.hover-effects.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensagem de sucesso
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-4';
                alert.style.zIndex = '9999';
                alert.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alert);

                // Remover após 3 segundos
                setTimeout(() => {
                    alert.remove();
                }, 3000);

                // Atualizar CSS dinâmico
                updateHoverStyles();
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao salvar configurações. Tente novamente.');
        });
    });

    // Atualizar estilos CSS dinamicamente
    function updateHoverStyles() {
        const enabled = document.getElementById('hover_effects_enabled').checked;
        const scale = document.getElementById('hover_transform_scale').value;
        const translateY = document.getElementById('hover_transform_translate_y').value;
        const shadow = document.getElementById('hover_shadow_intensity').value;
        const duration = document.getElementById('hover_transition_duration').value;
        const easing = document.getElementById('hover_transition_easing').value;
        const borderIntensity = document.getElementById('hover_border_color_intensity').value;

        // Criar ou atualizar style tag
        let styleTag = document.getElementById('dynamic-hover-styles');
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = 'dynamic-hover-styles';
            document.head.appendChild(styleTag);
        }

        if (!enabled) {
            styleTag.textContent = `
                .product-card-modern:hover,
                .product-card:hover,
                .btn:hover,
                .card:hover {
                    transform: none !important;
                    box-shadow: none !important;
                    transition: none !important;
                }
            `;
        } else {
            const shadowBlur = Math.round(shadow * 0.4);
            const shadowSpread = Math.round(shadow * 0.1);
            
            styleTag.textContent = `
                :root {
                    --hover-transform-scale: ${scale};
                    --hover-transform-translate-y: ${translateY}px;
                    --hover-shadow-blur: ${shadowBlur}px;
                    --hover-shadow-spread: ${shadowSpread}px;
                    --hover-transition-duration: ${duration}s;
                    --hover-transition-easing: ${easing};
                    --hover-border-opacity: ${borderIntensity};
                }

                .product-card-modern:hover,
                .product-card:hover {
                    transform: translateY(var(--hover-transform-translate-y)) scale(var(--hover-transform-scale)) !important;
                    box-shadow: 0 var(--hover-shadow-blur) var(--hover-shadow-spread) rgba(0, 0, 0, 0.12) !important;
                    transition: all var(--hover-transition-duration) var(--hover-transition-easing) !important;
                }
            `;
        }
    }

    // Atualizar em tempo real quando sliders mudarem
    const inputs = form.querySelectorAll('input[type="range"], select, input[type="checkbox"]');
    inputs.forEach(input => {
        input.addEventListener('input', updateHoverStyles);
        input.addEventListener('change', updateHoverStyles);
    });

    // Carregar estilos iniciais
    updateHoverStyles();
});

function resetToDefaults() {
    if (confirm('Deseja restaurar todas as configurações para os valores padrão?')) {
        document.getElementById('hover_effects_enabled').checked = true;
        document.getElementById('hover_transform_scale').value = 1.05;
        document.getElementById('hover_transform_translate_y').value = -8;
        document.getElementById('hover_shadow_intensity').value = 24;
        document.getElementById('hover_transition_duration').value = 0.3;
        document.getElementById('hover_transition_easing').value = 'cubic-bezier(0.4, 0, 0.2, 1)';
        document.getElementById('hover_border_color_intensity').value = 0.8;

        // Atualizar valores exibidos
        document.getElementById('scaleValue').textContent = 1.05;
        document.getElementById('translateYValue').textContent = '-8px';
        document.getElementById('shadowValue').textContent = '24px';
        document.getElementById('durationValue').textContent = '0.3s';
        document.getElementById('borderIntensityValue').textContent = '80%';

        // Atualizar estilos
        const event = new Event('input');
        document.getElementById('hover_effects_enabled').dispatchEvent(event);
    }
}
</script>
@endsection

