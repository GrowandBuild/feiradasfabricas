<?php

// Endpoint para retornar departamentos em JSON para o modal
Route::get('/admin/departments/json', [App\Http\Controllers\Admin\DepartmentController::class, 'jsonList'])->name('admin.departments.json');

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DangerController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\AlbumPublicController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rotas públicas
// Dynamic manifest so theme_color can reflect admin-chosen theme in session
Route::get('/site.webmanifest', function(Request $request) {
    $deptSlug = session('current_department_slug');
    $dept_setting = function($key, $default = null) use ($deptSlug) {
        if ($deptSlug) {
            $deptKey = 'dept_' . $deptSlug . '_' . $key;
            $val = setting($deptKey);
            if ($val !== null && $val !== '') return $val;
        }
        return setting($key, $default);
    };

    $sessionTheme = session('current_department_theme', null);
    $themeSecondary = $sessionTheme['theme_secondary'] ?? $dept_setting('theme_secondary', '#ff6b35');

    $manifest = [
        'name' => setting('site_name', 'Feira das Fábricas'),
        'short_name' => setting('site_short_name', 'Feira'),
        'start_url' => '/',
        'scope' => '/',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'theme_color' => $themeSecondary,
        'background_color' => $dept_setting('theme_background', '#ffffff'),
        'icons' => [
            ['src' => '/android-chrome-192x192.png', 'sizes' => '192x192', 'type' => 'image/png'],
            ['src' => '/android-chrome-512x512.png', 'sizes' => '512x512', 'type' => 'image/png']
        ]
    ];

    return response()->json($manifest)->header('Content-Type', 'application/manifest+json');
})->name('site.manifest');

Route::get('/', [DepartmentController::class, 'index'])->name('home')->defaults('slug', 'eletronicos');
Route::get('/vitrine-departamentos', [HomeController::class, 'index'])->name('landing.departments');
Route::get('/produtos', [HomeController::class, 'products'])->name('products');
Route::get('/produto/{slug}', [HomeController::class, 'product'])->name('product');
// Página indexável da variação específica
Route::get('/produto/{slug}/{variantSlug}', [HomeController::class, 'productVariant'])->name('product.variant');
// Endpoint AJAX para detalhes de variação
Route::get('/produto/{slug}/variacao', [HomeController::class, 'getProductVariation'])->name('product.variation');
// Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/contato', [ContactController::class, 'index'])->name('contact');
Route::post('/contato', [ContactController::class, 'send'])->name('contact.send');

// Álbuns públicos (novo)
Route::get('/albuns', [AlbumPublicController::class, 'index'])->name('albums.index');
Route::get('/albuns/{slug}', [AlbumPublicController::class, 'show'])->name('albums.show');
// Admin quick actions on public pages
Route::post('/albuns', [AlbumPublicController::class, 'storeAlbum'])
    ->name('albums.store')
    ->middleware('auth:admin');
Route::delete('/albuns/{slug}/images/{image}', [AlbumPublicController::class, 'destroyImage'])
    ->name('albums.images.destroy')
    ->middleware('auth:admin');
// Upload de imagens direto na página do álbum (apenas admins autenticados)
Route::post('/albuns/{slug}/images', [AlbumPublicController::class, 'storeImage'])
    ->name('albums.images.store')
    ->middleware('auth:admin');

// Redireções legadas: "/galeria" e "/galerias" -> "/albuns"
Route::get('/galeria/{any?}', function () {
    return redirect()->route('albums.index');
})->where('any', '.*');
Route::get('/galerias/{any?}', function () {
    return redirect()->route('albums.index');
})->where('any', '.*');

// Rotas de departamentos
Route::prefix('departamento')->name('department.')->group(function () {
    Route::get('/{slug}', [App\Http\Controllers\DepartmentController::class, 'index'])->name('index');
    Route::get('/', [App\Http\Controllers\DepartmentController::class, 'list'])->name('list');
});

// Local-only preview route: quick render admin product edit without auth (DEV ONLY)
use App\Http\Controllers\Dev\AdminPreviewController;

if (app()->environment('local') || config('app.debug')) {
    Route::get('/dev/admin-preview/product/{id}/edit', [AdminPreviewController::class, 'productEdit'])
        ->name('dev.admin-preview.product.edit')
        ->middleware('auth:admin');
}

// Rota de exemplo para banners
Route::get('/exemplos/banners', function () {
    return view('examples.banner-usage');
})->name('examples.banners');

// Rotas de pagamento
Route::prefix('payment')->name('payment.')->group(function () {
    // Notificação do Mercado Pago
    Route::post('/mercadopago/notification', [App\Http\Controllers\PaymentController::class, 'handleMercadoPagoNotification'])->name('mercadopago.notification');
    
    // Webhook do Stripe
    Route::post('/stripe/webhook', function () {
        \Log::info('Webhook do Stripe recebido', request()->all());
        return response()->json(['status' => 'ok']);
    })->name('stripe.webhook');
    
    // Notificação do PagSeguro
    Route::post('/pagseguro/notification', function () {
        \Log::info('Notificação do PagSeguro recebida', request()->all());
        return response()->json(['status' => 'ok']);
    })->name('pagseguro.notification');
});

// Rotas de autenticação (B2C e B2B)
Route::prefix('customer')->name('customer.')->group(function () {
    // Login
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    
    // Registro B2C
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
    
    // Registro B2B
    Route::get('/register-b2b', [CustomerAuthController::class, 'showB2BRegisterForm'])->name('register.b2b');
    Route::post('/register-b2b', [CustomerAuthController::class, 'registerB2B'])->name('register.b2b.submit');
});

// Rotas do carrinho
Route::prefix('carrinho')->name('cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('/adicionar', [App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::put('/atualizar/{cartItem}', [App\Http\Controllers\CartController::class, 'update'])->name('update');
    Route::delete('/remover/{cartItem}', [App\Http\Controllers\CartController::class, 'remove'])->name('remove');
    Route::delete('/limpar', [App\Http\Controllers\CartController::class, 'clear'])->name('clear');
    Route::get('/contagem', [App\Http\Controllers\CartController::class, 'count'])->name('count');
    Route::post('/migrar', [App\Http\Controllers\CartController::class, 'migrateToCustomer'])->name('migrate');
    
    // Rota para Server-Sent Events (tempo real)
    Route::get('/stream', function() {
        return response()->stream(function() {
            // Configurar headers para SSE
            echo "data: " . json_encode(['type' => 'connected']) . "\n\n";
            
            // Manter conexão viva
            while (true) {
                echo "data: " . json_encode(['type' => 'ping']) . "\n\n";
                ob_flush();
                flush();
                sleep(30);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Cache-Control',
        ]);
    })->name('cart.stream');
});

// Rotas de checkout
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [App\Http\Controllers\CheckoutController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\CheckoutController::class, 'store'])->name('store');
    Route::get('/payment/{orderNumber}', [App\Http\Controllers\CheckoutController::class, 'payment'])->name('payment');
    Route::post('/payment/{orderNumber}/process', [App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('payment.process');
    
    // Novas rotas seguras
    Route::get('/payment-temp', [App\Http\Controllers\CheckoutController::class, 'paymentTemp'])->name('payment.temp');
    Route::post('/payment-temp/process', [App\Http\Controllers\CheckoutController::class, 'processPaymentAndCreateOrder'])->name('payment.process.temp');
    
    // Rota para PIX
    Route::get('/payment-pix', [App\Http\Controllers\CheckoutController::class, 'paymentPix'])->name('payment.pix');
    
    // Rota para Boleto
    Route::get('/payment-boleto', [App\Http\Controllers\CheckoutController::class, 'paymentBoleto'])->name('payment.boleto');
    
    // Rota para verificar status de pagamento PIX/Boleto
    Route::post('/payment-status-temp', [App\Http\Controllers\CheckoutController::class, 'checkPaymentStatusTemp'])->name('payment.status.temp');
    
    Route::get('/success/{orderNumber}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('success');
    Route::get('/status/{orderNumber}', [App\Http\Controllers\CheckoutController::class, 'checkPaymentStatus'])->name('status');
});

// Rotas de pedidos (requer autenticação)
Route::prefix('pedidos')->name('orders.')->middleware('auth:customer')->group(function () {
    Route::get('/', [App\Http\Controllers\OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('show');
    Route::post('/', [App\Http\Controllers\OrderController::class, 'store'])->name('store');
    Route::post('/{order}/cancelar', [App\Http\Controllers\OrderController::class, 'cancel'])->name('cancel');
    Route::post('/{order}/reordenar', [App\Http\Controllers\OrderController::class, 'reorder'])->name('reorder');
    Route::get('/stats/estatisticas', [App\Http\Controllers\OrderController::class, 'stats'])->name('stats');
});

// Rotas com alias para compatibilidade
Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
Route::get('/register-b2b', [CustomerAuthController::class, 'showB2BRegisterForm'])->name('register.b2b');

// Incluir rotas administrativas
require __DIR__.'/admin.php';

// Endpoint público leve para categorias (usado por autocomplete em páginas públicas)
Route::get('/categories/json', function () {
    $cats = \App\Models\Category::select('id', 'name')->orderBy('name')->get();
    return response()->json(['success' => true, 'categories' => $cats]);
})->name('categories.public');

// API de Busca Avançada
Route::prefix('api/search')->group(function () {
    Route::get('/', [App\Http\Controllers\SearchController::class, 'search']);
    Route::get('/autocomplete', [App\Http\Controllers\SearchController::class, 'autocomplete']);
    Route::get('/live', [App\Http\Controllers\SearchController::class, 'liveSearch']);
    Route::post('/voice', [App\Http\Controllers\SearchController::class, 'voiceSearch']);
    Route::post('/image', [App\Http\Controllers\SearchController::class, 'imageSearch']);
    Route::get('/barcode', [App\Http\Controllers\SearchController::class, 'barcodeSearch']);
});

// Webhooks de pagamento
Route::prefix('webhooks')->group(function () {
    Route::post('/mercadopago', [App\Http\Controllers\WebhookController::class, 'mercadoPago']);
    Route::post('/pagseguro', [App\Http\Controllers\WebhookController::class, 'pagSeguro']);
    Route::post('/paypal', [App\Http\Controllers\WebhookController::class, 'payPal']);
});

// Rotas de OAuth/Callback para integrações
Route::prefix('auth')->name('auth.')->group(function () {
    // Callback genérico para OAuth (Melhor Envio, APIs de pagamento, etc.)
    Route::get('/callback', [App\Http\Controllers\Auth\OAuthController::class, 'callback'])->name('callback');
    Route::get('/redirect', [App\Http\Controllers\Auth\OAuthController::class, 'redirect'])->name('redirect');
});

// Ação perigosa TEMPORÁRIA: apagar dados de frete
Route::post('/danger/drop-shipping', [DangerController::class, 'dropShippingData'])->name('danger.drop-shipping');

// Frete: cálculo de cotação por produto (Melhor Envio)
Route::post('/shipping/quote', [ShippingController::class, 'quote'])->name('shipping.quote');
Route::post('/shipping/quote-cart', [ShippingController::class, 'quoteCart'])->name('shipping.quote.cart');
// Frete: persistência da opção selecionada na sessão
Route::post('/shipping/select', [ShippingController::class, 'select'])->name('shipping.select');
Route::get('/shipping/current', [ShippingController::class, 'current'])->name('shipping.current');
Route::delete('/shipping/selection', [ShippingController::class, 'clear'])->name('shipping.clear');

// Public endpoint to persist per-session logo size preference (small/medium/large/xlarge)
use Illuminate\Http\Request;
Route::post('/logo/size', function(Request $request) {
    $size = $request->input('size');
    $map = [
        'small' => 24,
        'medium' => 36,
        'large' => 60,
        'xlarge' => 100
    ];
    if (!array_key_exists($size, $map)) {
        return response()->json(['success' => false, 'message' => 'Invalid size'], 422);
    }
    session(['user_logo_size' => $map[$size]]);
    return response()->json(['success' => true, 'size' => $map[$size]]);
})->name('logo.size');

// Admin-only: toggle "view as normal user" for bottom nav (stores in session)
Route::post('/admin/ui/toggle-view-as-user', function(Request $request) {
    if (!auth()->guard('admin')->check()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
    $current = session('admin_view_as_user', false);
    $new = !$current;
    session(['admin_view_as_user' => $new]);
    return response()->json(['success' => true, 'value' => $new]);
})->name('admin.ui.toggle_view_as_user');
