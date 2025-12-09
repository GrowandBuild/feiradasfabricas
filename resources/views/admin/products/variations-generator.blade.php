@php
/** Simple variations generator UI for testing.
 * Paste JSON array into textarea and select a product.
 */
@endphp

@extends('admin.layouts.app')

@section('title', 'Gerador de Variações')
@section('page-title', 'Gerador de Variações')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h3 class="mb-3">Gerador de Variações (Teste)</h3>

            <div class="mb-3">
                <label class="form-label">Produto</label>
                <select id="product-select" class="form-select">
                    <option value="">-- selecione um produto --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (ID: {{ $p->id }})</option>
                    @endforeach
                </select>
            </div>

            <form id="variations-generator-form" method="POST" action="" class="mb-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Combinações (JSON)</label>
                    <textarea id="combinations" name="combinations_json" rows="10" class="form-control" placeholder='Cole aqui um array JSON de combinações'></textarea>
                    <div class="form-text">Formato: array de objetos. Ex: [{"ram":"8GB","storage":"128GB","color":"Preto","price":99.99,"stock_quantity":5}]</div>
                </div>

                <div class="d-flex gap-2">
                    <button id="submit-btn" type="button" class="btn btn-primary">Gerar</button>
                    <button type="button" id="fill-sample" class="btn btn-outline-secondary">Exemplo</button>
                </div>
            </form>

            <div id="result"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const fillBtn = document.getElementById('fill-sample');
    const submitBtn = document.getElementById('submit-btn');
    const prodSelect = document.getElementById('product-select');
    const combinationsEl = document.getElementById('combinations');
    const resultEl = document.getElementById('result');
    const form = document.getElementById('variations-generator-form');

    fillBtn && fillBtn.addEventListener('click', function(){
        combinationsEl.value = JSON.stringify([
            {"ram":"8GB","storage":"128GB","color":"Preto","price":99.99,"stock_quantity":5},
            {"ram":"16GB","storage":"256GB","color":"Preto","price":129.99,"stock_quantity":3}
        ], null, 2);
    });

    function getFormAction(productId){
        return '/admin/products/' + productId + '/variations/generate';
    }

    submitBtn && submitBtn.addEventListener('click', function(){
        const prod = prodSelect.value;
        if(!prod){ alert('Selecione um produto'); return; }
        const raw = combinationsEl.value.trim();
        if(!raw){ alert('Cole o JSON das combinações'); return; }

        let combos;
        try{ combos = JSON.parse(raw); }
        catch(e){ alert('JSON inválido: '+e.message); return; }

        const tokenInput = form.querySelector('input[name="_token"]');
        const token = tokenInput ? tokenInput.value : '';
        const url = getFormAction(prod);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ combinations: combos })
        }).then(function(res){
            return res.json().then(function(body){ return { status: res.status, body: body }; });
        }).then(function(r){
            if(r.status >= 200 && r.status < 300){
                let html = '<div class="alert alert-success">';
                html += '<h5 class="mb-2">Resultado</h5><ul class="mb-0">';
                (r.body.results || []).forEach(function(it){ html += '<li>' + (it.action || 'ok') + ' — ID: ' + (it.id || '-') + ' — SKU: ' + (it.sku || '-') + '</li>'; });
                html += '</ul></div>';
                resultEl.innerHTML = html;
            } else {
                resultEl.innerHTML = '<div class="alert alert-danger">Erro: ' + JSON.stringify(r.body) + '</div>';
            }
        }).catch(function(err){
            resultEl.innerHTML = '<div class="alert alert-danger">Erro de rede: '+err.message+'</div>';
        });
    });
});
</script>
@endpush
