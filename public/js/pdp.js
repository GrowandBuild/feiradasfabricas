/* PDP standalone initializer - resilient to interference */
(function(){
  'use strict';

  const BOOT = { started: true, galleryInit: false, variationInit: false, errors: [] };
  try { window.__PDP_BOOT = BOOT; } catch(e) {}
  try { console.log('[PDP] external script boot start'); } catch(e) {}

  function getConfig(){
    const el = document.getElementById('pdp-config');
    if(!el) { console.error('[PDP] Missing #pdp-config'); return {}; }
    try { return JSON.parse(el.textContent || '{}'); } catch(e){ console.error('[PDP] Invalid config JSON', e); return {}; }
  }

  const CONFIG = getConfig();
  const FALLBACK_IMAGE = CONFIG.imageFallback || '/images/no-image.svg';

  // Data
  const baseProductImages = Array.isArray(CONFIG.images) ? CONFIG.images : [];
  const variationColorImages = CONFIG.variationColorImages || {};
  const activeVariationsData = Array.isArray(CONFIG.variationData) ? CONFIG.variationData : [];
  const hasVariations = !!CONFIG.hasVariations;

  // Build color -> hex map
  const colorHexMap = activeVariationsData.reduce((acc, variation) => {
    if (variation.color && variation.color_hex) {
      acc[variation.color] = variation.color_hex;
      const key = variation.color.replace(/[^a-zA-Z0-9]/g, '_');
      acc[key] = variation.color_hex;
    }
    return acc;
  }, {});

  // Gallery state
  let productImages = baseProductImages.length ? baseProductImages.slice() : [FALLBACK_IMAGE];
  let totalImages = productImages.length;
  let currentImageIndex = 0;

  function updateImageCounter(current, total) {
    const counter = document.getElementById('imageCounter');
    const totalSpan = document.getElementById('total-images');
    const currentSpan = document.getElementById('current-image');
    const navButtons = document.querySelectorAll('.gallery-nav');

    if (totalSpan) totalSpan.textContent = String(total);
    if (currentSpan) currentSpan.textContent = String(current);

    const shouldShowControls = total > 1;
    if (counter) counter.classList.toggle('d-none', !shouldShowControls);
    navButtons.forEach(btn => btn.classList.toggle('d-none', !shouldShowControls));
  }

  function setMainImage(imageSrc, imageNumber = 1) {
    const mainImage = document.getElementById('main-product-image');
    const currentImageSpan = document.getElementById('current-image');
    const thumbnails = document.querySelectorAll('.thumbnail-img');

    if (mainImage) {
      mainImage.src = imageSrc;
      // Remove artificial fade to make updates instantaneous
      // If you want a subtle effect, use CSS transition only (no JS timeout)
    }

    currentImageIndex = Math.max(0, Math.min(productImages.length - 1, imageNumber - 1));

    if (currentImageSpan) currentImageSpan.textContent = String(imageNumber);

    thumbnails.forEach((thumb, index) => {
      thumb.classList.toggle('active', index === currentImageIndex);
    });

    updateImageCounter(imageNumber, totalImages);
  }
  window.setMainImage = setMainImage; // required by inline handlers

  function scrollToActiveThumbnail() {
    const activeThumbnail = document.querySelector('.thumbnail-img.active');
    if (!activeThumbnail) return;
    activeThumbnail.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
  }

  function changeImage(direction) {
    if (totalImages <= 1) return;
    currentImageIndex += direction;
    if (currentImageIndex >= totalImages) currentImageIndex = 0;
    else if (currentImageIndex < 0) currentImageIndex = totalImages - 1;
    setMainImage(productImages[currentImageIndex], currentImageIndex + 1);
    scrollToActiveThumbnail();
  }
  window.changeImage = changeImage; // required by inline handlers

  function renderThumbnails(images) {
    const wrapper = document.getElementById('thumbnailsWrapper');
    if (!wrapper) return;

    if (!Array.isArray(images) || images.length === 0) {
      wrapper.innerHTML = '<div class="bg-light rounded d-flex align-items-center justify-content-center w-100" style="height: 80px;"><i class="fas fa-image text-muted"></i></div>';
      productImages = [FALLBACK_IMAGE];
      totalImages = 1;
      setMainImage(FALLBACK_IMAGE, 1);
      BOOT.galleryInit = true;
      return;
    }

    productImages = images.slice();
    totalImages = productImages.length;
    currentImageIndex = 0;

    const safeName = (CONFIG.product && CONFIG.product.name) || 'Produto';
    let html = '';
    for (let i=0;i<productImages.length;i++) {
      const img = productImages[i];
      const altTxt = (safeName + ' - Imagem ' + (i+1)).replace(/"/g, '&quot;');
      html += '<div class="thumbnail-item">'
        + '<img src="' + img + '" alt="' + altTxt + '"'
        + ' class="thumbnail-img rounded border ' + (i===0? 'active':'') + '"'
        + ' data-index="' + i + '"'
        + '>'
        + '</div>';
    }
    wrapper.innerHTML = html;

    setMainImage(productImages[0], 1);

    const delegate = function(e){
      const target = e.target.closest('.thumbnail-img'); if(!target) return;
      const idx = parseInt(target.getAttribute('data-index'),10) || 0;
      window.setMainImage(productImages[idx], idx+1);
    };
    wrapper.addEventListener('click', delegate);
    wrapper.addEventListener('mouseenter', delegate, true);

    BOOT.galleryInit = true;
  }

  function applyColorImages(color) {
    const normalizedColor = color || '';
    let imagesToRender = baseProductImages;
    if (normalizedColor && Array.isArray(variationColorImages[normalizedColor]) && variationColorImages[normalizedColor].length > 0) {
      imagesToRender = variationColorImages[normalizedColor];
    }
    if (!Array.isArray(imagesToRender) || imagesToRender.length === 0) {
      imagesToRender = [FALLBACK_IMAGE];
    }
    renderThumbnails(imagesToRender);
  }

  // Shipping
  function showFreteMessage(message, type) {
    const resultBox = document.getElementById('frete-resultado');
    if (!resultBox) return;
    resultBox.innerHTML = '<div class="alert alert-'+type+'"><i class="bi bi-info-circle me-2"></i>'+ message +'</div>';
    resultBox.style.display = 'block';
  }

  function formatArrival(days) {
    if (!Number.isFinite(days)) return '';
    const d = new Date(); d.setDate(d.getDate() + days);
    return d.toLocaleDateString('pt-BR', { day:'2-digit', month:'2-digit' });
  }

  async function calcularFrete(cep, qty) {
    const btn = document.getElementById('btn-calc-frete');
    const resultBox = document.getElementById('frete-resultado');
    if (!btn || !resultBox) return;

    btn.disabled = true;
    btn.querySelector('.label-default')?.classList.add('d-none');
    btn.querySelector('.label-loading')?.classList.remove('d-none');
    resultBox.style.display = 'none';
    resultBox.innerHTML = '';

    try {
      const resp = await fetch(CONFIG.routes.shippingQuote, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CONFIG.csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          product_id: CONFIG.product.id,
          cep: cep,
          quantity: qty
        })
      });
      const data = await resp.json();
      if (!resp.ok || !data.success) {
        const msg = data.message || ('Erro ao calcular frete (HTTP '+resp.status+')');
        showFreteMessage(msg, 'danger');
        return;
      }
      renderQuotes(data.quotes || []);
      if (data.debug) renderDebug(data.debug);
    } catch (err) {
      showFreteMessage('Falha na conexão. Tente novamente.', 'danger');
    } finally {
      btn.disabled = false;
      btn.querySelector('.label-default')?.classList.remove('d-none');
      btn.querySelector('.label-loading')?.classList.add('d-none');
    }
  }

  function renderDebug(d) {
    const panel = document.getElementById('frete-debug-panel');
    if (!panel) return;
    panel.innerHTML = '<div class="border rounded p-2 bg-light">'
      + '<div><strong>Modo declarado:</strong> ' + d.declared_mode + '</div>'
      + '<div><strong>Valor declarado:</strong> R$ ' + Number(d.declared_value).toFixed(2).replace('.',',') + (d.declared_mode==='cap' ? ' (teto R$ ' + Number(d.declared_cap).toFixed(2).replace('.',',') + ')' : '') + '</div>'
      + '<div><strong>Peso real total:</strong> ' + d.weight_real_kg_total + ' kg</div>'
      + '<div><strong>Peso volumétrico:</strong> ' + d.weight_volumetric_kg_total + ' kg</div>'
      + '<div><strong>Peso usado:</strong> ' + d.weight_used_kg + ' kg</div>'
      + '<div><strong>Dimensões (cm):</strong> ' + d.dimensions_cm.length + ' × ' + d.dimensions_cm.width + ' × ' + d.dimensions_cm.height + '</div>'
      + '<div><strong>Camadas empilhadas:</strong> ' + d.stack_layers + '</div>'
      + '<div><strong>Quantidade:</strong> ' + d.quantity + '</div>'
      + '<div><strong>Ambiente:</strong> ' + d.environment + '</div>'
      + '</div>';
  }

  function attachShippingOptionEvents() {
    document.querySelectorAll('.shipping-option').forEach(opt => {
      opt.addEventListener('click', () => selectShippingOption(opt));
      opt.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectShippingOption(opt); }
      });
    });
    document.getElementById('sort-price')?.addEventListener('click', () => sortShipping('price'));
    document.getElementById('sort-speed')?.addEventListener('click', () => sortShipping('speed'));
  }

  function renderQuotes(quotes) {
    const resultBox = document.getElementById('frete-resultado');
    if (!resultBox) return;
    const withPrice = Array.isArray(quotes) ? quotes.filter(q => typeof q.price === 'number') : [];
    if (withPrice.length === 0) { showFreteMessage('Nenhuma opção de frete disponível para o CEP informado.', 'warning'); return; }

    const cheapest = withPrice.reduce((acc, q) => acc && acc.price <= q.price ? acc : q, withPrice[0]);
    const withDays = withPrice.filter(q => typeof q.delivery_days === 'number');
    const fastest = withDays.reduce((acc, q) => acc && acc.delivery_days <= q.delivery_days ? acc : q, withDays[0]);
    const maxPrice = withPrice.reduce((acc, q) => q.price > acc ? q.price : acc, 0);
    const econEl = document.getElementById('economy-hint');
    if (econEl && maxPrice && cheapest) econEl.textContent = 'Economize até R$ ' + (maxPrice - cheapest.price).toFixed(2).replace('.',',');
    const actions = document.getElementById('frete-actions'); if (actions) actions.style.display = 'flex';

    const itens = withPrice.map((q, idx) => {
      const preco = typeof q.price === 'number' ? q.price : null;
      const precoFmt = preco !== null ? 'R$ ' + preco.toFixed(2).replace('.', ',') : '—';
      const prazo = (q.delivery_days != null) ? (q.delivery_days + ' dia(s) úteis') : '';
      let service = q.service || 'Serviço';
      if (service.startsWith('.')) service = 'Jadlog ' + service;
      const isCheapest = cheapest && q === cheapest;
      const isFastest = fastest && q === fastest;
      const arrivalDate = (q.delivery_days != null) ? formatArrival(q.delivery_days) : '';
      const badges = (isCheapest?'<span class="badge bg-success me-1">Mais barato</span>':'') + (isFastest?'<span class="badge bg-info text-dark me-1">Mais rápido</span>':'');
      return (
        '<div class="shipping-option list-group-item '+(isCheapest?'option-cheapest':'')+' '+(isFastest?'option-fastest':'')+'" role="radio" aria-checked="'+(idx===0?'true':'false')+'" tabindex="0" data-index="'+idx+'">'
        + '<div class="d-flex justify-content-between align-items-start w-100">'
        +   '<div class="flex-grow-1 me-2">'
        +     '<div class="d-flex align-items-center mb-1">'
        +       '<input type="radio" name="shipping_service" class="form-check-input me-2" '+(idx===0?'checked':'')+' value="'+service+'" data-price="'+(preco ?? '')+'" data-days="'+(q.delivery_days ?? '')+'" data-service-id="'+(q.service_id ?? '')+'" data-company="'+(q.company ?? '')+'">'
        +       '<span class="fw-semibold service-name">'+service+'</span>'
        +     '</div>'
        +     '<div class="small text-muted">'+prazo + (arrivalDate ? ' • Chegada estimada ' + arrivalDate : '') + '</div>'
        +     '<div class="mt-1">'+badges+'</div>'
        +   '</div>'
        +   '<div class="text-end"><div class="fw-bold price-display">'+precoFmt+'</div></div>'
        + '</div>'
        + '</div>'
      );
    }).join('');

    resultBox.innerHTML = '<div class="list-group list-group-flush border rounded">'+itens+'</div>';
    resultBox.style.display = 'block';
    attachShippingOptionEvents();
    updateSelectedSummary();
  }

  async function selectShippingOption(opt) {
    const radio = opt.querySelector('input[type="radio"]');
    if (!radio) return;
    radio.checked = true;
    document.querySelectorAll('.shipping-option').forEach(o => o.setAttribute('aria-checked','false'));
    opt.setAttribute('aria-checked','true');
    updateSelectedSummary();
    const cep = (document.getElementById('cep-destino')?.value || '').replace(/\D/g, '');
    const qty = parseInt(document.getElementById('qty-shipping')?.value || '1', 10) || 1;
    try {
      await fetch(CONFIG.routes.shippingSelect, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CONFIG.csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          service: radio.value,
          price: parseFloat(radio.getAttribute('data-price') || '0') || 0,
          delivery_days: parseInt(radio.getAttribute('data-days') || '0', 10) || null,
          service_id: parseInt(radio.getAttribute('data-service-id') || '0', 10) || null,
          company: radio.getAttribute('data-company') || null,
          cep: cep,
          product_id: CONFIG.product.id,
          quantity: qty
        })
      });
    } catch (e) {
      console.warn('Falha ao salvar seleção de frete.', e);
    }
  }

  async function updateSelectedSummary() {
    const selectedRadio = document.querySelector('input[name="shipping_service"]:checked');
    const summaryBox = document.getElementById('frete-selecionado');
    if (!selectedRadio || !summaryBox) return;
    const service = selectedRadio.value;
    const price = selectedRadio.getAttribute('data-price');
    const days = selectedRadio.getAttribute('data-days');
    if (!service) { summaryBox.style.display='none'; return; }
    summaryBox.innerHTML = (
      '<div class="alert alert-primary d-flex align-items-center justify-content-between py-2 px-3">'
      + '<div>'
      +   '<strong>'+service+'</strong> — ' + (price?('R$ '+Number(price).toFixed(2).replace('.',',')):'Preço indisponível')
      +   (days?('<small class="ms-2 text-muted">'+days+' dia(s)</small>'):'')
      + '</div>'
      + '<button type="button" class="btn btn-sm btn-outline-primary" id="btn-alterar-frete">Alterar</button>'
      + '</div>'
    );
    summaryBox.style.display = 'block';
    document.getElementById('btn-alterar-frete')?.addEventListener('click', () => {
      document.getElementById('frete-resultado')?.scrollIntoView({behavior:'smooth'});
    });

    const cep = (document.getElementById('cep-destino')?.value || '').replace(/\D/g, '');
    const qty = parseInt(document.getElementById('qty-shipping')?.value || '1', 10) || 1;
    try {
      await fetch(CONFIG.routes.shippingSelect, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CONFIG.csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          service: service,
          price: parseFloat(price || '0') || 0,
          delivery_days: parseInt(days || '0', 10) || null,
          service_id: parseInt(selectedRadio.getAttribute('data-service-id') || '0', 10) || null,
          company: selectedRadio.getAttribute('data-company') || null,
          cep: cep,
          product_id: CONFIG.product.id,
          quantity: qty
        })
      });
    } catch (e) {
      console.warn('Falha ao salvar seleção de frete.', e);
    }
  }

  function sortShipping(mode) {
    const resultBox = document.getElementById('frete-resultado');
    if (!resultBox) return;
    const options = Array.from(resultBox.querySelectorAll('.shipping-option'));
    options.sort((a,b) => {
      const ra = a.querySelector('input');
      const rb = b.querySelector('input');
      if (!ra || !rb) return 0;
      if (mode==='price') return (parseFloat(ra.getAttribute('data-price')||'99999') - parseFloat(rb.getAttribute('data-price')||'99999'));
      return (parseInt(ra.getAttribute('data-days')||'999') - parseInt(rb.getAttribute('data-days')||'999'));
    });
    const container = resultBox.querySelector('.list-group');
    if (container) options.forEach(o => container.appendChild(o));
    const btnPrice = document.getElementById('sort-price');
    const btnSpeed = document.getElementById('sort-speed');
    if (mode==='price') {
      btnPrice?.classList.add('active'); btnSpeed?.classList.remove('active');
      btnPrice?.setAttribute('aria-pressed','true'); btnSpeed?.setAttribute('aria-pressed','false');
    } else {
      btnSpeed?.classList.add('active'); btnPrice?.classList.remove('active');
      btnSpeed?.setAttribute('aria-pressed','true'); btnPrice?.setAttribute('aria-pressed','false');
    }
  }

  // Variations
  function getSelectedValue(type) {
    const input = document.querySelector('input[name="'+type+'"]:checked');
    return input ? input.value : '';
  }

  function refreshActiveVariationOptions() {
    document.querySelectorAll('.variation-option').forEach(option => {
      const input = option.querySelector('input');
      option.classList.toggle('active', !!(input && input.checked && !input.disabled));
    });
  }

  function setOptionSelected(type, value) {
    if (!value) return;
    document.querySelectorAll('input[name="'+type+'"]').forEach(input => {
      if (input.value === value && !input.disabled) input.checked = true;
    });
  }

  function isCombinationAvailable(ram, storage, color) {
    return activeVariationsData.some(variation => {
      if (!variation.in_stock || variation.stock_quantity <= 0) return false;
      const matchesRam = !ram || variation.ram === ram;
      const matchesStorage = !storage || variation.storage === storage;
      const matchesColor = !color || variation.color === color;
      return matchesRam && matchesStorage && matchesColor;
    });
  }

  function applyColorSwatches() {
    document.querySelectorAll('.variation-option[data-variation-type="color"]').forEach(option => {
      const value = option.getAttribute('data-value') || '';
      const key = value.replace(/[^a-zA-Z0-9]/g, '_');
      const swatch = option.querySelector('.swatch'); if (!swatch) return;
      const hex = colorHexMap[value] || colorHexMap[key] || '#f1f5f9';
      swatch.style.background = hex;
    });
  }

  function syncVariationOptionAvailability() {
    const selected = {
      ram: getSelectedValue('ram') || null,
      storage: getSelectedValue('storage') || null,
      color: getSelectedValue('color') || null,
    };

    ['storage', 'color', 'ram'].forEach(type => {
      const options = Array.from(document.querySelectorAll('label[data-variation-type="'+type+'"]'));
      if (!options.length) return;
      let firstAvailable = null;

      options.forEach(option => {
        const input = option.querySelector('input');
        const value = input.value;
        const available = isCombinationAvailable(
          type === 'ram' ? value : selected.ram,
          type === 'storage' ? value : selected.storage,
          type === 'color' ? value : selected.color
        );
        input.disabled = !available;
        option.classList.toggle('disabled', !available);
        if (available && !firstAvailable) firstAvailable = input;
        if (!available && input.checked) input.checked = false;
      });

      const hasChecked = options.some(option => { const input = option.querySelector('input'); return input.checked && !input.disabled; });
      if (!hasChecked && firstAvailable) { firstAvailable.checked = true; selected[type] = firstAvailable.value; }
    });

    refreshActiveVariationOptions();
    applyColorSwatches();
  }

  function setAddToCartDisabled(disabled) {
    const addToCartBtn = document.querySelector('.add-to-cart-component [data-product-id]');
    const addToCartComponent = document.querySelector('.add-to-cart-component');
    if (addToCartBtn) {
      if (typeof addToCartBtn.disabled !== 'undefined') addToCartBtn.disabled = disabled;
      addToCartBtn.classList.toggle('disabled', disabled);
      addToCartBtn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
    }
    if (addToCartComponent && disabled) addToCartComponent.setAttribute('data-variation-id', '');
  }

  function updateVariation() {
    const ram = getSelectedValue('ram');
    const storage = getSelectedValue('storage');
    const color = getSelectedValue('color');
    const unavailableMessage = document.getElementById('variation-unavailable-message');

    const combinationExists = isCombinationAvailable(ram, storage, color);
    applyColorImages(color);

    if (!combinationExists) {
      if (unavailableMessage) unavailableMessage.style.display = 'flex';
      setAddToCartDisabled(true);
      const priceDisplay = document.getElementById('product-price-display');
      if (priceDisplay && CONFIG.product && CONFIG.product.price_fmt) priceDisplay.textContent = 'R$ ' + CONFIG.product.price_fmt;
      const skuDisplay = document.getElementById('variation-sku-display'); if (skuDisplay) skuDisplay.style.display = 'none';
      const stockDisplay = document.getElementById('variation-stock-display'); if (stockDisplay) stockDisplay.style.display = 'none';
      return;
    }

    if (unavailableMessage) unavailableMessage.style.display = 'none';

    const url = new URL(CONFIG.routes.productVariation, window.location.origin);
    if (ram) url.searchParams.append('ram', ram);
    if (storage) url.searchParams.append('storage', storage);
    if (color) url.searchParams.append('color', color);

    fetch(url.toString())
      .then(response => response.json())
      .then(data => {
        if (data.success && data.variation) {
          const priceDisplay = document.getElementById('product-price-display');
          if (priceDisplay && data.variation.price) priceDisplay.textContent = 'R$ ' + data.variation.price;

          const skuDisplay = document.getElementById('variation-sku-display');
          const skuSpan = document.getElementById('selected-variation-sku');
          if (skuDisplay && skuSpan) { skuSpan.textContent = data.variation.sku; skuDisplay.style.display = 'block'; }

          const stockDisplay = document.getElementById('variation-stock-display');
          const stockBadge = document.getElementById('variation-stock-badge');
          if (stockDisplay && stockBadge) {
            if (data.variation.in_stock && data.variation.stock_quantity > 0) {
              stockBadge.className = 'badge bg-success';
              stockBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Em estoque (' + data.variation.stock_quantity + ' unidades)';
              stockDisplay.style.display = 'block';
              setAddToCartDisabled(false);
            } else {
              stockBadge.className = 'badge bg-danger';
              stockBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> Fora de estoque';
              stockDisplay.style.display = 'block';
              setAddToCartDisabled(true);
              if (unavailableMessage) unavailableMessage.style.display = 'flex';
            }
          }

          const addToCartBtn = document.querySelector('.add-to-cart-component [data-product-id]');
          if (addToCartBtn) {
            addToCartBtn.setAttribute('data-variation-id', data.variation.id);
            const addToCartComponent = document.querySelector('.add-to-cart-component');
            if (addToCartComponent) addToCartComponent.setAttribute('data-variation-id', data.variation.id);
          }

          try {
            const cepInput = document.getElementById('cep-destino');
            const qtyShipping = document.getElementById('qty-shipping');
            const cep = (cepInput?.value || '').replace(/\D/g, '').slice(0,8);
            const qty = parseInt(qtyShipping?.value || '1', 10) || 1;
            if (cep && cep.length === 8) calcularFrete(cep, qty);
          } catch (e) { console.warn('Não foi possível recalcular o frete após mudar a variação.', e); }
        } else {
          setAddToCartDisabled(true);
        }
      })
      .catch(error => { console.error('Erro ao buscar variação:', error); });
  }

  function initVariationSelectors() {
    // Click on inputs
    document.querySelectorAll('.variation-option input').forEach(input => {
      input.addEventListener('change', () => {
        syncVariationOptionAvailability();
        applyColorImages(getSelectedValue('color'));
        updateVariation();
      });
    });

    // Click on whole label
    document.querySelectorAll('label.variation-option').forEach(label => {
      label.addEventListener('click', () => {
        const input = label.querySelector('input');
        if (!input || input.disabled) return;
        if (!input.checked) {
          input.checked = true;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    });

    syncVariationOptionAvailability();
    applyColorImages(getSelectedValue('color'));
    updateVariation();
  }

  function boot() {
    try { console.info('[PDP] template ts:', document.querySelector('#pdp-config') ? 'config ok' : 'missing'); } catch(e){}
    // Gallery
    try { renderThumbnails(productImages); } catch(err){ BOOT.errors.push('gallery:'+err.message); console.error('[PDP] gallery init error', err); }

    // Keyboard nav
    document.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowLeft') changeImage(-1);
      else if (e.key === 'ArrowRight') changeImage(1);
    });

    // Double click zoom
    const mainImageElement = document.getElementById('main-product-image');
    if (mainImageElement) {
      mainImageElement.addEventListener('dblclick', function() {
        if (this.style.transform === 'scale(2)') { this.style.transform = 'scale(1)'; this.style.cursor = 'pointer'; }
        else { this.style.transform = 'scale(2)'; this.style.cursor = 'zoom-out'; }
      });
    }

    // Qty sync with main cart input
    const qtyInput = document.getElementById('quantity-' + (CONFIG.product?.id ?? ''));
    const qtyShipping = document.getElementById('qty-shipping');
    if (qtyInput && qtyShipping) {
      qtyShipping.value = qtyInput.value || '1';
      qtyInput.addEventListener('change', () => { qtyShipping.value = qtyInput.value || '1'; });
    }

    // CEP mask
    const cepInput = document.getElementById('cep-destino');
    if (cepInput) {
      cepInput.addEventListener('input', (e) => {
        let v = (e.target.value || '').replace(/\D/g, '').slice(0,8);
        if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
        e.target.value = v;
      });
    }

    // Calc button
    const btnCalc = document.getElementById('btn-calc-frete');
    const resultBox = document.getElementById('frete-resultado');
    if (btnCalc && resultBox) {
      btnCalc.addEventListener('click', async () => {
        const cep = (cepInput?.value || '').replace(/\D/g, '');
        const qty = parseInt(qtyShipping?.value || '1', 10) || 1;
        if (cep.length !== 8) { showFreteMessage('Informe um CEP válido com 8 dígitos.', 'warning'); return; }
        await calcularFrete(cep, qty);
      });
    }

    // Toggle debug panel details
    document.getElementById('toggle-frete-debug')?.addEventListener('click', () => {
      const panel = document.getElementById('frete-debug-panel'); if (!panel) return;
      panel.style.display = (panel.style.display === 'none' || !panel.style.display) ? 'block' : 'none';
    });

    // Variations
    if (hasVariations) { try { initVariationSelectors(); BOOT.variationInit = true; } catch(err){ BOOT.errors.push('variation:'+err.message); console.error('[PDP] variation init error', err); } }
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else try { boot(); } catch(e){ console.error('[PDP] boot error', e); }

})();
