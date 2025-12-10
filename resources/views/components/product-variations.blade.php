@props(['product', 'productAttributes'])

@php
    // SIMPLIFICADO: Apenas validar se é Eloquent Collection válida
    // Se não for, não renderizar nada
    $hasValidAttributes = false;
    $allCombinations = [];
    
    if (isset($product) && $product && 
        isset($productAttributes) && 
        $productAttributes instanceof \Illuminate\Database\Eloquent\Collection &&
        $productAttributes->count() > 0 &&
        isset($product->has_variations) &&
        $product->has_variations) {
        
        try {
            // CORRIGIDO: Buscar TODAS as combinações (com e sem estoque) para permitir visualização
            $allCombinations = app(\App\Services\VariationService::class)->getAllCombinations($product);
            $selectedAttributes = [];
            // Mostrar componente se houver pelo menos uma combinação (mesmo sem estoque)
            $hasValidAttributes = count($allCombinations) > 0;
        } catch (\Exception $e) {
            // Se houver erro, não mostrar componente
            \Log::warning("Erro ao carregar combinações do produto {$product->id}: " . $e->getMessage());
            $hasValidAttributes = false;
        }
    }
@endphp

@if($hasValidAttributes)
    <div class="product-variations-container" data-product-id="{{ $product->id }}">
        @foreach($productAttributes as $attribute)
            <div class="variation-attribute-group mb-4" data-attribute-id="{{ $attribute->id }}">
                <label class="variation-attribute-label">
                    {{ $attribute->name }}:
                    <span class="selected-value-text" data-attribute-id="{{ $attribute->id }}"></span>
                </label>
                
                <div class="variation-values-wrapper" data-attribute-type="{{ $attribute->type }}">
                    @if($attribute->type === 'color')
                        <!-- Color Swatches -->
                        <div class="color-swatches-grid">
                            @foreach($attribute->values as $value)
                                @php
                                    $isAvailable = false;
                                    $variationId = null;
                                    $stockQuantity = 0;
                                    // Verificar se este valor está disponível em alguma combinação COM ESTOQUE
                                    // A chave da combinação é uma string como "5-8" com os IDs dos valores ordenados
                                    // Continuar procurando até encontrar uma combinação com estoque
                                    foreach($allCombinations as $key => $combo) {
                                        $valueIds = array_map('intval', explode('-', $key));
                                        if(in_array((int)$value->id, $valueIds)) {
                                            if($combo['in_stock']) {
                                                $isAvailable = true;
                                                $variationId = $combo['variation_id'];
                                                $stockQuantity = $combo['stock_quantity'];
                                                break; // Encontrou uma combinação com estoque, pode parar
                                            }
                                        }
                                    }
                                @endphp
                                <button type="button" 
                                        class="color-swatch {{ !$isAvailable ? 'disabled out-of-stock' : '' }}"
                                        data-attribute-id="{{ $attribute->id }}"
                                        data-value-id="{{ $value->id }}"
                                        data-variation-id="{{ $variationId }}"
                                        style="background-color: {{ $value->color_hex ?? '#ccc' }};"
                                        title="{{ $value->display_value ?: $value->value }}{{ !$isAvailable ? ' - Sem estoque' : '' }}"
                                        {{ !$isAvailable ? 'disabled' : '' }}>
                                    @if($isAvailable)
                                        <span class="swatch-check">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span class="swatch-unavailable" title="Sem estoque">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        
                    @elseif($attribute->type === 'size')
                        <!-- Size Buttons -->
                        <div class="size-buttons-grid">
                            @foreach($attribute->values as $value)
                                @php
                                    $isAvailable = false;
                                    $variationId = null;
                                    $stockQuantity = 0;
                                    // Verificar se este valor está disponível em alguma combinação COM ESTOQUE
                                    // Continuar procurando até encontrar uma combinação com estoque
                                    foreach($allCombinations as $key => $combo) {
                                        $valueIds = array_map('intval', explode('-', $key));
                                        if(in_array((int)$value->id, $valueIds)) {
                                            if($combo['in_stock']) {
                                                $isAvailable = true;
                                                $variationId = $combo['variation_id'];
                                                $stockQuantity = $combo['stock_quantity'];
                                                break; // Encontrou uma combinação com estoque, pode parar
                                            }
                                        }
                                    }
                                @endphp
                                <button type="button" 
                                        class="size-button {{ !$isAvailable ? 'disabled out-of-stock' : '' }}"
                                        data-attribute-id="{{ $attribute->id }}"
                                        data-value-id="{{ $value->id }}"
                                        data-variation-id="{{ $variationId }}"
                                        title="{{ !$isAvailable ? 'Sem estoque' : '' }}"
                                        {{ !$isAvailable ? 'disabled' : '' }}>
                                    {{ $value->display_value ?: $value->value }}
                                    @if(!$isAvailable)
                                        <span class="out-of-stock-badge">Sem estoque</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        
                    @elseif($attribute->type === 'number')
                        <!-- Number Dropdown -->
                        <select class="variation-select form-select"
                                data-attribute-id="{{ $attribute->id }}">
                            <option value="">Selecione {{ $attribute->name }}</option>
                            @foreach($attribute->values as $value)
                                @php
                                    $isAvailable = false;
                                    $variationId = null;
                                    foreach($allCombinations as $key => $combo) {
                                        $valueIds = array_map('intval', explode('-', $key));
                                        if(in_array((int)$value->id, $valueIds)) {
                                            $isAvailable = $combo['in_stock'];
                                            $variationId = $combo['variation_id'];
                                            break;
                                        }
                                    }
                                @endphp
                                <option value="{{ $value->id }}" 
                                        data-variation-id="{{ $variationId }}"
                                        {{ !$isAvailable ? 'disabled' : '' }}>
                                    {{ $value->display_value ?: $value->value }}
                                    {{ !$isAvailable ? ' (Sem estoque)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        
                    @elseif($attribute->type === 'image')
                        <!-- Image Swatches -->
                        <div class="image-swatches-grid">
                            @foreach($attribute->values as $value)
                                @php
                                    $isAvailable = false;
                                    $variationId = null;
                                    foreach($allCombinations as $key => $combo) {
                                        $valueIds = array_map('intval', explode('-', $key));
                                        if(in_array((int)$value->id, $valueIds)) {
                                            $isAvailable = $combo['in_stock'];
                                            $variationId = $combo['variation_id'];
                                            break;
                                        }
                                    }
                                @endphp
                                <button type="button" 
                                        class="image-swatch {{ !$isAvailable ? 'disabled out-of-stock' : '' }}"
                                        data-attribute-id="{{ $attribute->id }}"
                                        data-value-id="{{ $value->id }}"
                                        data-variation-id="{{ $variationId }}"
                                        title="{{ $value->display_value ?: $value->value }}{{ !$isAvailable ? ' - Sem estoque' : '' }}"
                                        {{ !$isAvailable ? 'disabled' : '' }}>
                                    @if($value->image_url)
                                        <img src="{{ $value->image_url }}" alt="{{ $value->display_value ?: $value->value }}">
                                    @else
                                        <span>{{ $value->display_value ?: $value->value }}</span>
                                    @endif
                                    @if($isAvailable)
                                        <span class="swatch-check">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span class="swatch-unavailable" title="Sem estoque">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        
                    @else
                        <!-- Text Buttons (default) -->
                        <div class="text-buttons-grid">
                            @foreach($attribute->values as $value)
                                @php
                                    $isAvailable = false;
                                    $variationId = null;
                                    foreach($allCombinations as $key => $combo) {
                                        $valueIds = array_map('intval', explode('-', $key));
                                        if(in_array((int)$value->id, $valueIds)) {
                                            $isAvailable = $combo['in_stock'];
                                            $variationId = $combo['variation_id'];
                                            break;
                                        }
                                    }
                                @endphp
                                <button type="button" 
                                        class="text-button {{ !$isAvailable ? 'disabled out-of-stock' : '' }}"
                                        data-attribute-id="{{ $attribute->id }}"
                                        data-value-id="{{ $value->id }}"
                                        data-variation-id="{{ $variationId }}"
                                        title="{{ !$isAvailable ? 'Sem estoque' : '' }}"
                                        {{ !$isAvailable ? 'disabled' : '' }}>
                                    {{ $value->display_value ?: $value->value }}
                                    @if(!$isAvailable)
                                        <span class="out-of-stock-badge">Sem estoque</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        
        <!-- Selected Variation Info (hidden until selection) -->
        <div class="selected-variation-info" style="display: none;">
            <div class="variation-details">
                <span class="variation-name"></span>
                <span class="variation-sku"></span>
            </div>
        </div>
    </div>
@endif

<style>
    /* Product Variations Styles - Modern & Beautiful */
    .product-variations-container {
        margin: 1.5rem 0;
    }

    .variation-attribute-group {
        margin-bottom: 1.5rem;
    }

    .variation-attribute-label {
        display: block;
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-dark, #1e293b);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .selected-value-text {
        font-weight: 400;
        color: var(--secondary-color, #ff6b35);
        text-transform: none;
        letter-spacing: normal;
        margin-left: 0.5rem;
    }

    /* Color Swatches */
    .color-swatches-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .color-swatch {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        border: 3px solid transparent;
        cursor: pointer;
        position: relative;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .color-swatch:hover:not(.disabled) {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .color-swatch.selected {
        border-color: var(--secondary-color, #ff6b35);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--secondary-color, #ff6b35) 20%, transparent);
    }

    .color-swatch.disabled,
    .color-swatch.out-of-stock {
        opacity: 0.5;
        cursor: not-allowed;
        position: relative;
    }

    .color-swatch.disabled::after,
    .color-swatch.out-of-stock::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #ef4444;
        transform: rotate(45deg);
    }

    .image-swatch.disabled,
    .image-swatch.out-of-stock {
        opacity: 0.5;
        cursor: not-allowed;
        position: relative;
    }

    .image-swatch.disabled::after,
    .image-swatch.out-of-stock::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #ef4444;
        transform: rotate(45deg);
    }

    .swatch-check {
        color: white;
        font-size: 0.875rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .color-swatch.selected .swatch-check {
        opacity: 1;
    }

    .swatch-unavailable {
        color: #ef4444;
        font-size: 0.75rem;
    }

    /* Size Buttons */
    .size-buttons-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .size-button {
        min-width: 48px;
        height: 48px;
        padding: 0 1rem;
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 10px;
        background: white;
        color: var(--text-dark, #1e293b);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .size-button:hover:not(.disabled) {
        border-color: var(--secondary-color, #ff6b35);
        background: color-mix(in srgb, var(--secondary-color, #ff6b35) 5%, white);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .size-button.selected {
        border-color: var(--secondary-color, #ff6b35);
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color, #ff6b35) 90%, black) 100%);
        color: white;
        box-shadow: 0 4px 12px color-mix(in srgb, var(--secondary-color, #ff6b35) 30%, transparent);
    }

    .size-button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        position: relative;
    }

    .size-button.out-of-stock {
        border-color: #e2e8f0;
        background: #f8f9fa;
    }

    .size-button.out-of-stock::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #ef4444;
        transform: translateY(-50%);
    }

    /* Text Buttons */
    .text-buttons-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .text-button {
        padding: 0.625rem 1.25rem;
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 10px;
        background: white;
        color: var(--text-dark, #1e293b);
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .text-button:hover:not(.disabled) {
        border-color: var(--secondary-color, #ff6b35);
        background: color-mix(in srgb, var(--secondary-color, #ff6b35) 5%, white);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .text-button.selected {
        border-color: var(--secondary-color, #ff6b35);
        background: linear-gradient(135deg, var(--secondary-color, #ff6b35) 0%, color-mix(in srgb, var(--secondary-color, #ff6b35) 90%, black) 100%);
        color: white;
        box-shadow: 0 4px 12px color-mix(in srgb, var(--secondary-color, #ff6b35) 30%, transparent);
    }

    .text-button.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        position: relative;
    }

    .text-button.out-of-stock {
        border-color: #e2e8f0;
        background: #f8f9fa;
    }

    .text-button.out-of-stock::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #ef4444;
        transform: translateY(-50%);
    }

    /* Badge "Sem estoque" */
    .out-of-stock-badge {
        display: block;
        font-size: 0.7rem;
        color: #ef4444;
        font-weight: 600;
        margin-top: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Image Swatches */
    .image-swatches-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .image-swatch {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        border: 3px solid transparent;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 0;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-swatch img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-swatch:hover:not(.disabled) {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .image-swatch.selected {
        border-color: var(--secondary-color, #ff6b35);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--secondary-color, #ff6b35) 20%, transparent);
    }

    .image-swatch.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Select Dropdown */
    .variation-select {
        max-width: 300px;
        border: 2px solid var(--border-color, #e2e8f0);
        border-radius: 10px;
        padding: 0.625rem 1rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .variation-select:focus {
        border-color: var(--secondary-color, #ff6b35);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--secondary-color, #ff6b35) 20%, transparent);
        outline: none;
    }

    /* Selected Variation Info */
    .selected-variation-info {
        margin-top: 1rem;
        padding: 1rem;
        background: color-mix(in srgb, var(--secondary-color, #ff6b35) 5%, white);
        border-radius: 12px;
        border: 1px solid color-mix(in srgb, var(--secondary-color, #ff6b35) 20%, transparent);
    }

    .variation-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .variation-name {
        font-weight: 600;
        color: var(--text-dark, #1e293b);
    }

    .variation-sku {
        font-size: 0.875rem;
        color: var(--text-muted, #64748b);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .color-swatch {
            width: 44px;
            height: 44px;
        }

        .size-button,
        .text-button {
            min-width: 44px;
            height: 44px;
            padding: 0 0.875rem;
            font-size: 0.85rem;
        }

        .image-swatch {
            width: 56px;
            height: 56px;
        }
    }
</style>

