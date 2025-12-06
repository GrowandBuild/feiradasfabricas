@extends('admin.layouts.app')

@section('title', 'Gerador de Variações')

@section('content')
<div class="container py-4">
    <h1>Gerador de Variações</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form id="generator-form">
                @csrf
                <div class="mb-3">
                    <label for="product_id" class="form-label">Produto (criar variações para)</label>
                    <select id="product_id" name="product_id" class="form-select">
                        <option value="">-- selecione --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="attributes-container">
                    <div class="attribute-row mb-2 d-flex gap-2 align-items-start">
                        <input type="text" name="attr_name[]" class="form-control" placeholder="Nome do atributo (ex: color, size, ram)">
                        <input type="text" name="attr_values[]" class="form-control" placeholder="Valores separados por vírgula (ex: Preto, Branco)">
                        <button type="button" class="btn btn-outline-danger btn-remove-attr">Remover</button>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="button" id="add-attribute" class="btn btn-sm btn-outline-primary">+ Adicionar atributo</button>
                </div>

                <div class="mb-3">
                    <button type="button" id="generate-combos" class="btn btn-primary">Gerar combinações</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4" id="preview-card" style="display:none;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Pré-visualização de combinações</h5>
                <div>
                    <span id="combos-count" class="text-muted me-2">0 combinações</span>
                    <button id="export-csv" class="btn btn-sm btn-outline-secondary" style="display:none;">Exportar CSV</button>
                </div>
            </div>
            <div id="combos-preview" style="max-height:300px;overflow:auto;"></div>
            <div class="mt-3">
                <div id="combos-warning" class="text-warning small mb-2" style="display:none;"></div>
                <button id="create-combos" class="btn btn-success">Criar variações no produto</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    (function(){
        const container = document.getElementById('attributes-container');
        document.getElementById('add-attribute').addEventListener('click', function(){
            const row = document.createElement('div');
            row.className = 'attribute-row mb-2 d-flex gap-2 align-items-start';
            row.innerHTML = `
                <input type="text" name="attr_name[]" class="form-control" placeholder="Nome do atributo (ex: color, size, ram)">
                <input type="text" name="attr_values[]" class="form-control" placeholder="Valores separados por vírgula (ex: Preto, Branco)">
                <button type="button" class="btn btn-outline-danger btn-remove-attr">Remover</button>
            `;
            container.appendChild(row);
        });

        document.body.addEventListener('click', function(e){
            if (e.target && e.target.classList.contains('btn-remove-attr')) {
                e.target.closest('.attribute-row').remove();
            }
        });

        function cartesianProduct(arrays) {
            return arrays.reduce((a, b) => a.flatMap(d => b.map(e => [...d, e])), [[]]);
        }

        document.getElementById('generate-combos').addEventListener('click', function(){
            // collect attribute rows and build name->values pairs, ignoring empty rows
            const rows = Array.from(document.querySelectorAll('.attribute-row'));
            const pairs = [];

            rows.forEach(row => {
                const nameInput = row.querySelector('input[name="attr_name[]"]');
                const valuesInput = row.querySelector('input[name="attr_values[]"]');
                if (!nameInput || !valuesInput) return;
                const name = nameInput.value.trim();
                const rawValues = valuesInput.value.trim();
                if (!name || !rawValues) return; // skip incomplete rows
                const values = rawValues.split(',').map(s => s.trim()).filter(Boolean);
                if (values.length === 0) return;
                pairs.push({ name, values });
            });

            if (pairs.length === 0) {
                alert('Preencha pelo menos um atributo com valores separados por vírgula (ex.: Preto,Branco). Remova linhas vazias.');
                return;
            }

            // prepare arrays for cartesian product using only valid pairs
            const arrays = pairs.map(p => p.values);
            const product = cartesianProduct(arrays);
            const combos = product.map(p => {
                const obj = {};
                p.forEach((val, idx) => obj[pairs[idx].name] = val);
                return obj;
            });

            const preview = document.getElementById('combos-preview');
            preview.innerHTML = '';

            // thresholds
            const WARN_THRESHOLD = 200;
            const HARD_LIMIT = 2000;

            combos.forEach((c, i) => {
                const el = document.createElement('div');
                el.className = 'combo-item p-2 border rounded mb-2 d-flex justify-content-between align-items-center';

                const left = document.createElement('div');
                left.className = 'combo-left';
                // render attributes as badges for readability
                Object.keys(c).forEach(k => {
                    const span = document.createElement('span');
                    span.className = 'badge bg-secondary me-1';
                    span.textContent = k + ': ' + c[k];
                    left.appendChild(span);
                });

                const right = document.createElement('div');
                right.className = 'combo-actions text-muted small';
                const idx = document.createElement('span');
                idx.className = 'me-2';
                idx.textContent = (i+1) + '.';
                const copyBtn = document.createElement('button');
                copyBtn.type = 'button';
                copyBtn.className = 'btn btn-sm btn-outline-primary';
                copyBtn.textContent = 'Copiar';
                copyBtn.addEventListener('click', () => {
                    navigator.clipboard && navigator.clipboard.writeText(JSON.stringify(c)).then(()=>{
                        copyBtn.textContent = 'Copiado';
                        setTimeout(()=> copyBtn.textContent = 'Copiar', 1200);
                    }).catch(()=> alert('Não foi possível copiar'));
                });

                right.appendChild(idx);
                right.appendChild(copyBtn);

                el.appendChild(left);
                el.appendChild(right);
                preview.appendChild(el);
            });

            // update UI controls
            const previewCard = document.getElementById('preview-card');
            previewCard.style.display = combos.length ? 'block' : 'none';
            window._generatedCombos = combos;

            const countEl = document.getElementById('combos-count');
            const exportBtn = document.getElementById('export-csv');
            const warningEl = document.getElementById('combos-warning');
            const createBtn = document.getElementById('create-combos');

            countEl.textContent = combos.length + (combos.length === 1 ? ' combinação' : ' combinações');
            exportBtn.style.display = combos.length ? 'inline-block' : 'none';

            if (combos.length > WARN_THRESHOLD) {
                warningEl.style.display = 'block';
                warningEl.textContent = 'Atenção: muitas combinações ('+combos.length+'). A criação pode demorar ou sobrecarregar o sistema. Considere exportar e processar em lote.';
            } else {
                warningEl.style.display = 'none';
            }

            if (combos.length > HARD_LIMIT) {
                createBtn.disabled = true;
                warningEl.style.display = 'block';
                warningEl.textContent = 'Limite excedido: não é permitido criar mais de '+HARD_LIMIT+' combinações de uma vez via UI.';
            } else {
                createBtn.disabled = false;
            }

            // export CSV handler
            exportBtn.onclick = function(){
                if (!combos.length) return;
                const keys = Array.from(new Set(combos.flatMap(c => Object.keys(c))));
                const rows = [keys.join(',')];
                combos.forEach(c => {
                    const line = keys.map(k => '"'+(c[k] ? String(c[k]).replace(/"/g,'""') : '')+'"').join(',');
                    rows.push(line);
                });
                const csv = rows.join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'variations.csv';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            };
        });

        document.getElementById('create-combos').addEventListener('click', async function(){
            const combos = window._generatedCombos || [];
            const productId = document.getElementById('product_id').value;
            if (!productId) { alert('Selecione um produto para criar as variações.'); return; }
            if (!combos.length) { alert('Nenhuma combinação gerada.'); return; }

            const WARN_THRESHOLD = 200;
            const HARD_LIMIT = 2000;
            if (combos.length > HARD_LIMIT) {
                alert('Não é possível criar mais de ' + HARD_LIMIT + ' combinações via UI. Exporte como CSV e processe em lote.');
                return;
            }
            if (combos.length > WARN_THRESHOLD) {
                if (!confirm('Você vai criar ' + combos.length + ' variações. Deseja prosseguir? Esta operação pode demorar.')) return;
            }

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await fetch(`/admin/products/${productId}/variations/bulk-add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ combos })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erro ao criar variações');
                alert('Variações solicitadas. Verifique o produto para confirmar.');
                window.location.reload();
            } catch (err) {
                console.error(err);
                alert('Erro: ' + err.message);
            }
        });

        // initialization: if server or older JS left pre-rendered combo lines (e.g. "1. {\"cor\":\"verde\"}"), try to parse and sync state
        (function initFromExistingPreview(){
            try {
                const preview = document.getElementById('combos-preview');
                if (!preview) return;
                const items = Array.from(preview.children).filter(c => c.classList && c.classList.contains('combo-item'));
                if (!items.length) {
                    // try to parse plain text lines (legacy)
                    const text = preview.textContent.trim();
                    if (!text) return;
                    const lines = text.split('\n').map(l => l.trim()).filter(Boolean);
                    const parsed = [];
                    lines.forEach(line => {
                        // remove leading index like "1. " or "1) "
                        const m = line.replace(/^\d+\s*[\.|\)]\s*/, '');
                        try {
                            const obj = JSON.parse(m);
                            parsed.push(obj);
                        } catch(e) {
                            // ignore non-json lines
                        }
                    });
                    if (parsed.length) {
                        window._generatedCombos = parsed;
                        document.getElementById('preview-card').style.display = 'block';
                        document.getElementById('combos-count').textContent = parsed.length + (parsed.length === 1 ? ' combinação' : ' combinações');
                        document.getElementById('export-csv').style.display = parsed.length ? 'inline-block' : 'none';
                    }
                } else {
                    // there are already rendered items (newer JS), update count
                    window._generatedCombos = [];
                    items.forEach(it => {
                        // attempt to parse JSON stored in data-json or fallback to innerText after index
                        const dataJson = it.getAttribute && it.getAttribute('data-json');
                        if (dataJson) {
                            try { window._generatedCombos.push(JSON.parse(dataJson)); return; } catch(e){}
                        }
                        const txt = it.textContent.replace(/^\d+\s*[\.|\)]\s*/, '').trim();
                        try { window._generatedCombos.push(JSON.parse(txt)); } catch(e) { }
                    });
                    const n = window._generatedCombos.length || items.length;
                    document.getElementById('combos-count').textContent = n + (n === 1 ? ' combinação' : ' combinações');
                }
            } catch(e) {
                // don't break the page for any unexpected error
                console.debug('initFromExistingPreview error', e);
            }
        })();
    })();
</script>
@endsection
