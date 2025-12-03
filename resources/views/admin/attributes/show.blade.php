@extends('admin.layouts.app')

@section('title', 'Atributo: ' . $attribute->name)
@section('page-title', 'Atributo: ' . $attribute->name)

@section('content')
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Valores para: {{ $attribute->name }}</h5>
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
            </div>
            <div class="card-body">
                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

                <form action="{{ route('admin.attributes.values.store', $attribute) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="row g-2">
                            <div class="col-md-5">
                            <input type="text" name="value" placeholder="Novo valor" class="form-control" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-center gap-2">
                                <input type="color" id="hexColorPicker" value="#ffffff" class="form-control form-control-color" title="Selecionar cor">
                                <input type="text" id="hexInput" name="hex" placeholder="#hex (opcional)" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary">Adicionar Valor</button>
                        </div>
                    </div>
                </form>

                <table class="table">
                    <thead>
                        <tr><th>Valor</th><th>Hex</th><th>Ativo</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($attribute->values as $val)
                            @php
                                $hex = null;
                                if (!empty($val->hex)) {
                                    $hex = trim($val->hex);
                                    if ($hex !== '' && strpos($hex, '#') !== 0) {
                                        $hex = '#'.$hex;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    @if($hex)
                                        <span class="me-2" style="display:inline-block;width:18px;height:14px;background:{{ $hex }};border:1px solid #ddd;vertical-align:middle;"></span>
                                    @endif
                                    {{ $val->value }}
                                </td>
                                <td style="width:120px;">
                                    @if($hex)
                                        <div style="display:flex;align-items:center;gap:.5rem;">
                                            <span style="width:28px;height:18px;display:inline-block;background:{{ $hex }};border:1px solid #ddd;border-radius:4px;"></span>
                                            <span style="font-size:0.95rem;color:var(--text-secondary, #6b7280);">{{ $val->value }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="value-active-label" data-value-id="{{ $val->id }}">{{ $val->is_active ? 'Sim' : 'Não' }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-toggle-active {{ $val->is_active ? 'btn-success' : 'btn-outline-secondary' }}" data-attribute-id="{{ $attribute->id }}" data-value-id="{{ $val->id }}" data-value="{{ e($val->value) }}" data-active="{{ $val->is_active ? '1' : '0' }}">
                                            {{ $val->is_active ? 'Ativo' : 'Inativo' }}
                                        </button>
                                        <form action="{{ route('admin.attributes.values.destroy', [$attribute, $val]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Excluir valor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var colorPicker = document.getElementById('hexColorPicker');
    var hexInput = document.getElementById('hexInput');

    if(!colorPicker || !hexInput) return;

    // Ensure the initial hex input matches the picker's default
    try {
        hexInput.value = colorPicker.value || hexInput.value || '';
    } catch (err){}

    // Sync color -> hex input
    colorPicker.addEventListener('input', function(e){
        hexInput.value = e.target.value;
    });

    // If user types hex manually, update color picker if valid
    hexInput.addEventListener('input', function(e){
        var v = e.target.value.trim();
        if(/^#([0-9a-fA-F]{6})$/.test(v)){
            colorPicker.value = v;
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

    function setButtonState(btn, active) {
        btn.dataset.active = active ? '1' : '0';
        btn.classList.toggle('btn-success', !!active);
        btn.classList.toggle('btn-outline-secondary', !active);
        btn.textContent = active ? 'Ativo' : 'Inativo';
        const label = btn.closest('tr') ? btn.closest('tr').querySelector('.value-active-label') : null;
        if (label) label.textContent = active ? 'Sim' : 'Não';
    }

    document.querySelectorAll('.btn-toggle-active').forEach(btn => {
        btn.addEventListener('click', function(){
            const attrId = this.dataset.attributeId;
            const valueId = this.dataset.valueId;
            const valueText = this.dataset.value;
            const currentlyActive = this.dataset.active === '1';
            const self = this;

            // visual feedback
            const originalText = self.innerHTML;
            self.disabled = true;
            self.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            // Build request: to activate include is_active key; to deactivate omit it (controller checks presence)
            const url = `/admin/attributes/${attrId}/values/${valueId}`;
            const method = 'PATCH';
            let body = null;
            if (!currentlyActive) {
                body = JSON.stringify({ value: valueText, is_active: true });
            } else {
                // send only value (no is_active) so controller marks as false
                body = JSON.stringify({ value: valueText });
            }

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: body
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    // toggle state locally
                    setButtonState(self, !currentlyActive);
                } else {
                    console.error('Toggle failed', data);
                    alert((data && data.message) ? data.message : 'Erro ao alternar estado do valor');
                }
            }).catch(err => {
                console.error('Erro na requisição', err);
                alert('Erro ao alternar estado do valor');
            }).finally(() => {
                self.disabled = false;
                self.innerHTML = originalText;
            });
        });
    });
});
</script>
@endsection

@push('styles')
<style>
/* Fix color picker sizing so swatch is visible despite global .form-control rules */
#hexColorPicker {
    width: 44px !important;
    height: 44px !important;
    padding: 0 !important;
    border: 1px solid var(--border-color) !important;
    border-radius: .375rem !important;
    background: transparent !important;
}

/* Remove extra inner padding on some browsers */
#hexColorPicker::-webkit-color-swatch-wrapper { padding: 0 !important; }
#hexColorPicker::-webkit-color-swatch { border: none !important; }
</style>
@endpush
            </div>
        </div>
    </div>
</div>
@endsection
