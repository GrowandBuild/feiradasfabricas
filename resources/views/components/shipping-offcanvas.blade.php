<div class="offcanvas offcanvas-end" tabindex="-1" id="shippingOffcanvas" aria-labelledby="shippingOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="shippingOffcanvasLabel"><i class="bi bi-truck me-2"></i>Frete e prazo</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <p class="text-muted small mb-3">Informe seu CEP para calcular frete e prazo e escolher o melhor método de entrega.</p>
    @php
      $defaultItems = [[
        'weight' => 0.3,
        'length' => 20,
        'height' => 20,
        'width'  => 20,
        'value'  => 0,
      ]];
    @endphp
    <x-shipping-calculator :items="$defaultItems" />
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const offcanvasEl = document.getElementById('shippingOffcanvas');
      if(!offcanvasEl) return;
      offcanvasEl.addEventListener('shown.bs.offcanvas', function(){
        try{
          // If there is another shipping-calculator on the page, reuse its items for higher accuracy
          const pageCalc = document.querySelector('main .shipping-calculator');
          if(pageCalc){
            const items = JSON.parse(pageCalc.getAttribute('data-items')||'[]');
            if(Array.isArray(items) && items.length){
              window.dispatchEvent(new CustomEvent('shipping:recalculate',{detail:{items}}));
            }
          }
        }catch(e){}
      });
    });
  </script>
</div>
