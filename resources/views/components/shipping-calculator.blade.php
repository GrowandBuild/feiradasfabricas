<div class="shipping-calculator card border-0 shadow-sm" id="shipping-calculator-{{ uniqid() }}" data-items='@json($items ?? [])'>
    <div class="card-body">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-truck me-2 text-primary"></i>
            <strong>Calcule o frete e prazo</strong>
        </div>
        <div class="input-group mb-2">
            <input type="text" class="form-control sc-cep" inputmode="numeric" pattern="[0-9\-]*" maxlength="9" placeholder="Digite seu CEP" aria-label="CEP">
            <button class="btn btn-primary sc-btn" type="button">{{ __('Calcular') }}</button>
        </div>
        <div class="text-muted small mb-2 sc-address" style="display:none">Entrega para <span class="sc-address-text"></span></div>
        <div class="small text-danger sc-error" style="display:none"></div>
        <div class="small text-muted sc-loading" style="display:none">Calculando frete...</div>
        <ul class="list-group list-group-flush sc-results" style="display:none"></ul>
        <button class="btn btn-link p-0 mt-2 sc-toggle" style="display:none">Ver todas as opções</button>
    </div>

    <script>
    (function(){
        const root = document.currentScript.previousElementSibling.closest('.shipping-calculator');
        const cepInput = root.querySelector('.sc-cep');
        const btn = root.querySelector('.sc-btn');
        const err = root.querySelector('.sc-error');
        const loading = root.querySelector('.sc-loading');
        const results = root.querySelector('.sc-results');
        const toggle = root.querySelector('.sc-toggle');
        const addrWrap = root.querySelector('.sc-address');
        const addrText = root.querySelector('.sc-address-text');
    let items = JSON.parse(root.dataset.items || '[]');
        let expanded = false;
        let quotes = [];
        let selectedKey = null;
        function providerName(p){ return ({correios:'Correios',jadlog:'Jadlog',total_express:'Total Express',loggi:'Loggi',melhor_envio:'Melhor Envio'})[p]||p; }
        function formatPrice(v){ return v===0?'Indisponível': new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'}).format(v); }
        function maskCep(){ cepInput.value = cepInput.value.replace(/[^0-9]/g,'').slice(0,8).replace(/(\d{5})(\d{0,3})/, (m,a,b)=> b?`${a}-${b}`:a); }
        function quoteKey(q){ return `${q.provider}:${q.service_code||q.service_name}`; }
        function render(){
            results.innerHTML='';
            const list = expanded ? quotes : quotes.slice(0,2);
            list.forEach(q=>{
                const li = document.createElement('li');
                const key = quoteKey(q);
                const isSel = key===selectedKey;
                li.className='list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <div class="d-flex align-items-start gap-2">
                        <input type="radio" name="shipping_option" ${isSel?'checked':''} style="margin-top:4px" />
                        <div>
                            <div class="fw-semibold">${q.service_name} · ${providerName(q.provider)}</div>
                            <div class="text-muted small">${q.delivery_time_text||''}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="fw-bold">${formatPrice(q.price)}</div>
                        <button class="btn btn-sm btn-outline-primary sc-select">${isSel?'Selecionado':'Selecionar'}</button>
                    </div>`;
                li.querySelector('input[type="radio"]').addEventListener('change',()=> selectQuote(q));
                li.querySelector('.sc-select').addEventListener('click',()=> selectQuote(q));
                results.appendChild(li);
            });
            results.style.display = quotes.length? 'block':'none';
            toggle.style.display = quotes.length>2? 'inline-block':'none';
            toggle.textContent = expanded? 'Ver menos':'Ver todas as opções';
        }
        function updateEstimatedFromCheapest(){
            try{
                // Only show estimate if there is NO selection yet
                if(selectedKey || !quotes.length) return;
                const cheapest = [...quotes].sort((a,b)=> (a.price||0) - (b.price||0))[0];
                if(!cheapest) return;
                const elShip = document.getElementById('shipping-amount');
                const elMethod = document.getElementById('shipping-method');
                if(elShip){ elShip.textContent = formatPrice(cheapest.price); }
                if(elMethod){ elMethod.textContent = `A partir de ${cheapest.service_name} · ${providerName(cheapest.provider)}`; }
            }catch(e){}
        }
        async function quote(){
            err.style.display='none'; err.textContent='';
            const digits = cepInput.value.replace(/[^0-9]/g,'');
            if(digits.length!==8){ err.textContent='CEP inválido'; err.style.display='block'; return; }
            loading.style.display='block';
            try{
                const a = await fetch(`/api/address/${digits}`);
                if(a.ok){ const aj = await a.json(); if(aj.success){ addrText.textContent = `${aj.city}-${aj.state}`; addrWrap.style.display='block'; } }
                const res = await fetch('/api/shipping/quote',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({destination_cep:digits,items})});
                const data = await res.json();
                const rawQuotes = data.quotes||[];
                const hadOnlyErrors = rawQuotes.length>0 && rawQuotes.every(q=>q.error);
                quotes = rawQuotes.filter(q=>!q.error);
                localStorage.setItem('dest_cep', digits);
                // Broadcast quotes for any listeners and show estimate if none selected yet
                try{ window.dispatchEvent(new CustomEvent('shipping:quoted',{ detail:{ cep: digits, quotes } })); }catch(_e){}
                updateEstimatedFromCheapest();
                // If nothing to show and provider returned only errors, display a friendly message
                if(quotes.length===0 && hadOnlyErrors){
                    err.textContent = 'Não encontramos opções de frete no momento para este CEP. Tente novamente mais tarde.';
                    err.style.display='block';
                }
            }catch(e){ err.textContent='Não foi possível calcular agora. Tente novamente.'; err.style.display='block'; }
            finally{ loading.style.display='none'; render(); }
        }
    async function selectQuote(q){
            const digits = cepInput.value.replace(/[^0-9]/g,'');
            if(digits.length!==8){ err.textContent='Informe um CEP válido antes de selecionar'; err.style.display='block'; return; }
            try{
                const res = await fetch('/api/shipping/select',{
                    method:'POST',
                    headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                    body: JSON.stringify({
                        destination_cep: digits,
                        provider: q.provider,
                        service_code: q.service_code||null,
                        service_name: q.service_name,
                        price: q.price,
                        delivery_time_text: q.delivery_time_text||''
                    })
                });
                const data = await res.json();
                if(data.success){
                    selectedKey = quoteKey(q);
                    render();
                    // Update totals on page if elements exist
                    const s = data.summary || {};
                    const elSub = document.getElementById('subtotal');
                    const elShip = document.getElementById('shipping-amount');
                    const elTot = document.getElementById('total');
                    if(elSub && s.subtotal){ elSub.textContent = 'R$ ' + s.subtotal; }
                    if(elShip && s.shipping){ elShip.textContent = 'R$ ' + s.shipping; }
                    if(elTot && s.total){ elTot.textContent = 'R$ ' + s.total; }
                    const elMethod = document.getElementById('shipping-method');
                    if(elMethod){ elMethod.textContent = `${q.service_name} · ${providerName(q.provider)}`; }
                    // Broadcast global event
                    window.dispatchEvent(new CustomEvent('shipping:selected',{detail:data}));
                } else {
                    err.textContent = data.message || 'Falha ao salvar frete';
                    err.style.display='block';
                }
            }catch(e){ err.textContent='Não foi possível selecionar. Tente novamente.'; err.style.display='block'; }
        }
    cepInput.value = (localStorage.getItem('dest_cep')||'').replace(/(\d{5})(\d{3})/,'$1-$2');
        cepInput.addEventListener('input', maskCep);
        btn.addEventListener('click', quote);
        toggle.addEventListener('click', function(){ expanded=!expanded; render(); });
        // Auto-quote on load if CEP present (localStorage)
        if(cepInput.value && cepInput.value.replace(/[^0-9]/g,'').length===8){
            quote().then(async()=>{
                try{
                    const selRes = await fetch('/api/shipping/selection');
                    const selJson = await selRes.json();
                    const sel = selJson.selection;
                    if(sel){
                        const match = quotes.find(q=> (q.provider===sel.provider) && ((q.service_code||q.service_name)===(sel.service_code||sel.service_name)) );
                        if(match){ selectedKey = quoteKey(match); render(); }
                        // Update totals display
                        const sRes = await fetch('/api/cart/summary');
                        const sJson = await sRes.json();
                        const s = sJson.summary||{};
                        const elShip=document.getElementById('shipping-amount');
                        const elTot=document.getElementById('total');
                        if(elShip&&s.shipping){ elShip.textContent='R$ '+s.shipping; }
                        if(elTot&&s.total){ elTot.textContent='R$ '+s.total; }
                        const elMethod=document.getElementById('shipping-method');
                        if(elMethod && sel && sel.service_name){ elMethod.textContent = `${sel.service_name} · ${providerName(sel.provider||'')}`; }
                    } else {
                        // No saved selection -> show cheapest estimate after auto-quote
                        updateEstimatedFromCheapest();
                    }
                }catch(e){}
            });
        } else {
            // If no local CEP, try session selection (server) and auto-fill CEP to quote
            (async()=>{
                try{
                    const selRes = await fetch('/api/shipping/selection');
                    const selJson = await selRes.json();
                    const sel = selJson.selection;
                    if(sel && sel.destination_cep){
                        const m = String(sel.destination_cep).replace(/[^0-9]/g,'').slice(0,8).replace(/(\d{5})(\d{3})/,'$1-$2');
                        cepInput.value = m;
                        await quote();
                        const match = quotes.find(q=> (q.provider===sel.provider) && ((q.service_code||q.service_name)===(sel.service_code||sel.service_name)) );
                        if(match){ selectedKey = quoteKey(match); render(); }
                        const sRes = await fetch('/api/cart/summary');
                        const sJson = await sRes.json();
                        const s = sJson.summary||{};
                        const elShip=document.getElementById('shipping-amount');
                        const elTot=document.getElementById('total');
                        if(elShip&&s.shipping){ elShip.textContent='R$ '+s.shipping; }
                        if(elTot&&s.total){ elTot.textContent='R$ '+s.total; }
                        const elMethod=document.getElementById('shipping-method');
                        if(elMethod && sel && sel.service_name){ elMethod.textContent = `${sel.service_name} · ${providerName(sel.provider||'')}`; }
                    } else {
                        // No session selection either. If user types CEP and quotes, we'll still show cheapest estimate.
                    }
                }catch(e){}
            })();
        }
        // Listen for external requests to recalculate with new items (e.g., variation change)
        window.addEventListener('shipping:recalculate', function(ev){
            try{
                if(ev.detail && Array.isArray(ev.detail.items)){
                    items = ev.detail.items;
                    // If CEP is ready, re-quote
                    const digits = cepInput.value.replace(/[^0-9]/g,'');
                    if(digits.length===8){ quote(); }
                }
            }catch(e){}
        });
    })();
    </script>
</div>
