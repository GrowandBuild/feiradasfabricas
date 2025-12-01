@php
    $isAdmin = \Illuminate\Support\Facades\Auth::guard('admin')->check();
    $viewAsUser = session('admin_view_as_user');
    $customerUser = \Illuminate\Support\Facades\Auth::guard('customer')->user();
    $cartCount = 0;
    try {
        if ($customerUser) {
            $cartCount = \App\Models\CartItem::forCustomer($customerUser->id)->sum('quantity');
        } else {
            $cartSessionId = session('cart_session_id');
            if ($cartSessionId) $cartCount = \App\Models\CartItem::forSession($cartSessionId)->sum('quantity');
        }
    } catch (\Throwable $e) { $cartCount = 0; }
@endphp

<style>
    /* Minimal user FAB (bottom nav) to mirror common mobile nav (Loja / Buscar / Carrinho / Conta) */
    .user-fab { position: fixed; left: 50%; right: auto; transform: translateX(-50%); bottom: env(safe-area-inset-bottom,12px); display: inline-flex; flex-direction: row; justify-content: center; align-items: center; gap: 12px; --uf-height:64px; padding: 8px 14px; background: var(--primary-color, #0f172a); color: var(--text-light, #fff); box-shadow: 0 8px 28px rgba(2,6,23,0.14); border-radius: 999px; z-index: 1200; max-width: 920px; }
    .user-fab a, .user-fab button { color: inherit; background: transparent; border: none; display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 10px; font-size: 1.1rem; cursor: pointer; }
    .user-fab .uf-pill { padding: 8px 12px; border-radius: 999px; background: rgba(255,255,255,0.04); color: inherit; font-weight:600; display:flex; align-items:center; gap:8px; }
    .user-fab .cart-badge { position: absolute; top: -6px; right: -6px; min-width:18px; height:18px; border-radius:999px; background: var(--secondary-color); color:#fff; font-size:11px; display:flex; align-items:center; justify-content:center; padding:0 4px; }
    @media (max-width:640px){ .user-fab { max-width: calc(100% - 40px); } }
    .fab-top-toggle { position: absolute; left: 50%; transform: translateX(-50%); bottom: calc(100% + 8px); width:44px; height:44px; border-radius:50%; border:none; display:flex; align-items:center; justify-content:center; background: rgba(0,0,0,0.5); color:#fff; box-shadow: 0 6px 18px rgba(0,0,0,0.28); }
    .fab-top-toggle i { font-size:16px; }
</style>

<div id="userFab" class="user-fab {{ $isAdmin ? ($viewAsUser ? 'fab-visible' : 'fab-hidden') : 'fab-visible' }}" aria-hidden="false">
    @if($isAdmin)
        {{-- Toggle flutuante acima do FAB (compacto) --}}
        <button type="button" class="fab-top-toggle admin-toggle-view-as-user" aria-pressed="{{ $viewAsUser ? 'true' : 'false' }}" title="Alternar ver como usuário" style="position:absolute; left:50%; transform:translateX(-50%); bottom: calc(100% + 8px); width:44px; height:44px; border-radius:50%; border:none; display:flex; align-items:center; justify-content:center; background: rgba(0,0,0,0.5); color:#fff; box-shadow: 0 6px 18px rgba(0,0,0,0.28);">
            @if($viewAsUser)
                <i class="fas fa-eye"></i>
            @else
                <i class="fas fa-eye-slash"></i>
            @endif
        </button>
    @endif
    <a href="{{ route('home') }}" class="uf-btn" title="Loja" aria-label="Loja">
        <i class="fas fa-store"></i>
    </a>

    <button type="button" id="userSearchBtn" class="uf-btn" title="Buscar" aria-label="Buscar">
        <i class="fas fa-search"></i>
    </button>

    <a href="{{ route('cart.index') }}" class="uf-btn" title="Carrinho" aria-label="Carrinho" style="position:relative;">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge">{{ $cartCount }}</span>
    </a>

    @auth('customer')
        <a href="{{ route('orders.index') }}" class="uf-btn" title="Minha Conta" aria-label="Minha Conta">
            <i class="fas fa-user"></i>
        </a>
    @else
        <a href="{{ route('login') }}" class="uf-btn" title="Entrar" aria-label="Entrar">
            <i class="fas fa-user"></i>
        </a>
    @endauth

    <div class="uf-pill" style="margin-left:6px; display:none;">
        Buscar
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var btn = document.getElementById('userSearchBtn');
            if(!btn) return;
            btn.addEventListener('click', function(e){
                try {
                    // prefer the visible search input on the page
                    var el = document.querySelector('.search-bar input') || document.querySelector('#smartSearchInput');
                    if(el){ el.focus(); if(el.select) try{ el.select(); }catch(x){} return; }
                    // fallback: try to open admin smart search if available
                    var adminTrigger = document.getElementById('smartSearchTrigger');
                    if(adminTrigger){ adminTrigger.click(); return; }
                    // last resort: navigate to home (or search page if available)
                    window.location.href = '{{ route("home") }}';
                } catch(err){ console.error('user search open failed', err); }
            });
        });
    </script>
</div>
