@php
    $selection = session('shipping_selection');
    if (!$selection && auth('customer')->check()) {
        $u = auth('customer')->user();
        if (!empty($u->shipping_option)) {
            $selection = is_array($u->shipping_option) ? $u->shipping_option : json_decode($u->shipping_option, true);
        }
    }
    $label = $selection
        ? ( ($selection['delivery_time_text'] ?? '')
            ? ($selection['service_name'] . ' • ' . 'R$ ' . number_format($selection['price'] ?? 0, 2, ',', '.') . ' • ' . $selection['delivery_time_text'])
            : ($selection['service_name'] . ' • ' . 'R$ ' . number_format($selection['price'] ?? 0, 2, ',', '.')) )
        : null;
@endphp

<div class="d-none d-lg-flex align-items-center me-3" id="shipping-mini">
    <i class="bi bi-geo-alt text-white-50 me-2"></i>
    @if($selection)
        <span class="badge bg-primary-subtle text-white rounded-pill px-3 py-2" title="Entrega para CEP {{ $selection['destination_cep'] }}">
            {{ $label }}
        </span>
    @else
        <a href="#" class="text-white-50 small text-decoration-none" onclick="document.querySelector('.shipping-calculator .sc-cep')?.focus(); return false;">
            Calcule o frete
        </a>
    @endif
</div>

<script>
// Listen selection changes to refresh mini widget without reload
window.addEventListener('shipping:selected', function(e){
    fetch('/api/shipping/selection')
      .then(r=>r.json())
      .then(() => { try { location.reload(); } catch(_) {} });
});
</script>
