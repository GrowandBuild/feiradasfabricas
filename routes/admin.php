<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DepartmentBadgeController;
use App\Http\Controllers\Admin\MelhorEnvioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Rotas para o painel administrativo
|
*/

// Rotas de autenticação do admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Login
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Rotas protegidas
    Route::middleware('auth:admin')->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Produtos
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');
        Route::get('products/{product}/variations', [ProductController::class, 'getVariations'])->name('products.variations');
        Route::post('products/{product}/variations/toggle', [ProductController::class, 'toggleVariation'])->name('products.variations.toggle');
        Route::post('products/{product}/variations/add', [ProductController::class, 'addVariation'])->name('products.variations.add');
        Route::post('products/{product}/variations/update-stock', [ProductController::class, 'updateStock'])->name('products.variations.update-stock');
        Route::post('products/{product}/variations/color-images', [ProductController::class, 'updateColorImages'])->name('products.variations.color-images');
        Route::delete('products/{product}/variations/{variationId}', [ProductController::class, 'deleteVariation'])->name('products.variations.destroy');
        Route::delete('products/{product}/variations/value', [ProductController::class, 'deleteVariationValue'])->name('products.variations.delete-value');
        Route::delete('products/{product}/variations/inactive/delete-all', [ProductController::class, 'deleteInactiveVariations'])->name('products.variations.delete-inactive');
        Route::post('products/{product}/variations/color-hex', [ProductController::class, 'updateColorHex'])->name('products.variations.color-hex');
        Route::post('products/variations/{variation}/update-price', [ProductController::class, 'updateVariationPrice'])->name('products.variations.update-price');
        Route::post('products/{product}/update-description', [ProductController::class, 'updateDescription'])->name('products.update-description');
        Route::get('products/{product}/images', [ProductController::class, 'getImages'])->name('products.images');
        Route::post('products/{product}/update-images', [ProductController::class, 'updateImages'])->name('products.update-images');
        Route::post('products/{product}/remove-image', [ProductController::class, 'removeImage'])->name('products.remove-image');

        // Categorias
        Route::resource('categories', CategoryController::class);

        // Departamentos
        Route::resource('departments', DepartmentController::class);

        // Pedidos
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
        Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
        Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');

        // Clientes
        Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update']);
        Route::patch('customers/{customer}/b2b-status', [CustomerController::class, 'updateB2BStatus'])->name('customers.update-b2b-status');
        Route::patch('customers/{customer}/reset-password', [CustomerController::class, 'resetPassword'])->name('customers.reset-password');
        Route::patch('customers/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])->name('customers.toggle-active');
        Route::get('customers/{customer}/orders', [CustomerController::class, 'getOrders'])->name('customers.orders');

        // Cupons
        Route::resource('coupons', CouponController::class);
        Route::patch('coupons/{coupon}/toggle-active', [CouponController::class, 'toggleActive'])->name('coupons.toggle-active');

        // Banners
        Route::resource('banners', BannerController::class);
        Route::patch('banners/{banner}/toggle-active', [BannerController::class, 'toggleActive'])->name('banners.toggle-active');

        // Selos de Marcas (Department Badges)
        Route::resource('department-badges', DepartmentBadgeController::class);
        Route::patch('department-badges/{departmentBadge}/toggle-active', [DepartmentBadgeController::class, 'toggleActive'])->name('department-badges.toggle-active');

        // Configurações
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
        Route::post('settings/test-connection', [SettingController::class, 'testConnection'])->name('settings.test-connection');
        Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

    // Configuração específica: Melhor Envio
    Route::get('settings/melhor-envio', [MelhorEnvioController::class, 'index'])->name('melhor-envio.index');
    Route::post('settings/melhor-envio', [MelhorEnvioController::class, 'save'])->name('melhor-envio.save');
    Route::post('settings/melhor-envio/test', [MelhorEnvioController::class, 'test'])->name('melhor-envio.test');
    Route::get('settings/melhor-envio/authorize', [MelhorEnvioController::class, 'authorizeStart'])->name('melhor-envio.authorize');
    Route::get('settings/melhor-envio/callback', [MelhorEnvioController::class, 'oauthCallback'])->name('melhor-envio.callback');
    Route::get('settings/melhor-envio/services', [MelhorEnvioController::class, 'services'])->name('melhor-envio.services');
    Route::post('settings/melhor-envio/services', [MelhorEnvioController::class, 'servicesSave'])->name('melhor-envio.services.save');

    // Painel de Status dos Providers de Frete (ativar/desativar)
    Route::get('settings/shipping/providers', [\App\Http\Controllers\Admin\ShippingProviderController::class, 'index'])->name('shipping-providers.index');
    Route::post('settings/shipping/providers', [\App\Http\Controllers\Admin\ShippingProviderController::class, 'save'])->name('shipping-providers.save');
    Route::post('settings/shipping/providers/clear-cache', [\App\Http\Controllers\Admin\ShippingProviderController::class, 'clearCache'])->name('shipping-providers.clear-cache');

        // Gerenciamento de Usuários Admin (apenas para usuários com permissão)
        Route::middleware('permission:users.manage')->group(function () {
            Route::resource('users', App\Http\Controllers\Admin\UserManagementController::class);
            Route::patch('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::patch('users/{user}/reset-password', [App\Http\Controllers\Admin\UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        });

        // Funcionalidades Avançadas de Produtos
        
        // Importação de Produtos
    Route::get('products/import', [App\Http\Controllers\Admin\ProductImportController::class, 'showImportForm'])->name('products.import');
    Route::post('products/import', [App\Http\Controllers\Admin\ProductImportController::class, 'import'])->name('products.import.store');
        Route::get('products/import/template', [App\Http\Controllers\Admin\ProductImportController::class, 'downloadTemplate'])->name('products.import.template');

        // Clonagem de Produtos
        Route::post('products/{product}/clone', [App\Http\Controllers\Admin\ProductCloneController::class, 'clone'])->name('products.clone');
        Route::post('products/bulk-clone', [App\Http\Controllers\Admin\ProductCloneController::class, 'bulkClone'])->name('products.bulk-clone');

        // Templates de Produtos
        Route::post('products/create-from-template', [App\Http\Controllers\Admin\ProductTemplateController::class, 'createFromTemplate'])->name('products.create-from-template');

        // Ajuste de Estoque em Lote
        Route::post('products/bulk-stock-adjustment', [App\Http\Controllers\Admin\BulkStockController::class, 'bulkStockAdjustment'])->name('products.bulk-stock-adjustment');
        Route::get('products/low-stock-report', [App\Http\Controllers\Admin\BulkStockController::class, 'lowStockReport'])->name('products.low-stock-report');
        Route::post('products/bulk-set-min-stock', [App\Http\Controllers\Admin\BulkStockController::class, 'bulkSetMinStock'])->name('products.bulk-set-min-stock');

        // Regras de Preço
        Route::post('products/apply-price-rules', [App\Http\Controllers\Admin\PriceRuleController::class, 'applyPriceRules'])->name('products.apply-price-rules');
        Route::post('products/bulk-price-update', [App\Http\Controllers\Admin\PriceRuleController::class, 'bulkPriceUpdate'])->name('products.bulk-price-update');
        Route::get('products/price-analysis', [App\Http\Controllers\Admin\PriceRuleController::class, 'priceAnalysis'])->name('products.price-analysis');

        // Alertas de Estoque
        Route::get('products/stock-alerts', [App\Http\Controllers\Admin\StockAlertController::class, 'index'])->name('products.stock-alerts');
        Route::post('products/create-alert', [App\Http\Controllers\Admin\StockAlertController::class, 'createAlert'])->name('products.create-alert');
        Route::post('products/bulk-create-alerts', [App\Http\Controllers\Admin\StockAlertController::class, 'bulkCreateAlerts'])->name('products.bulk-create-alerts');
        Route::get('products/stock-report', [App\Http\Controllers\Admin\StockAlertController::class, 'getStockReport'])->name('products.stock-report');

        // Categorização Automática
        Route::get('products/uncategorized', [App\Http\Controllers\Admin\AutoCategorizeController::class, 'getUncategorizedProducts'])->name('products.uncategorized');
        Route::post('products/auto-categorize', [App\Http\Controllers\Admin\AutoCategorizeController::class, 'autoCategorize'])->name('products.auto-categorize');
        Route::post('products/bulk-auto-categorize', [App\Http\Controllers\Admin\AutoCategorizeController::class, 'bulkAutoCategorize'])->name('products.bulk-auto-categorize');
        Route::post('products/create-category-rule', [App\Http\Controllers\Admin\AutoCategorizeController::class, 'createCategoryRule'])->name('products.create-category-rule');
        Route::get('products/{product}/category-suggestions', [App\Http\Controllers\Admin\AutoCategorizeController::class, 'getSuggestions'])->name('products.category-suggestions');

        // Busca Avançada
        Route::get('products/advanced-search', [App\Http\Controllers\Admin\AdvancedSearchController::class, 'search'])->name('products.advanced-search');
        Route::post('products/bulk-action', [App\Http\Controllers\Admin\AdvancedSearchController::class, 'bulkAction'])->name('products.bulk-action');
        Route::post('products/bulk-availability', [App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])->name('products.bulk-availability');
        Route::post('products/{product}/update-cost-price', [App\Http\Controllers\Admin\ProductController::class, 'updateCostPrice'])->name('products.update-cost-price');
        Route::post('products/{product}/update-profit-margin', [App\Http\Controllers\Admin\ProductController::class, 'updateProfitMargin'])->name('products.update-profit-margin');
        Route::post('products/apply-global-margins', [App\Http\Controllers\Admin\ProductController::class, 'applyGlobalMargins'])->name('products.apply-global-margins');
        Route::get('products/export-search-results', [App\Http\Controllers\Admin\AdvancedSearchController::class, 'exportResults'])->name('products.export-search-results');
        Route::get('products/search-suggestions', [App\Http\Controllers\Admin\AdvancedSearchController::class, 'getSearchSuggestions'])->name('products.search-suggestions');

        // Backup e Sincronização
        Route::get('backup', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backup.index');
        Route::post('backup/create', [App\Http\Controllers\Admin\BackupController::class, 'createBackup'])->name('backup.create');
        Route::post('backup/restore', [App\Http\Controllers\Admin\BackupController::class, 'restoreBackup'])->name('backup.restore');
        Route::get('backup/{filename}/download', [App\Http\Controllers\Admin\BackupController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('backup/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'deleteBackup'])->name('backup.delete');
        Route::get('products/export', [App\Http\Controllers\Admin\BackupController::class, 'exportProducts'])->name('products.export');
        Route::post('products/import-backup', [App\Http\Controllers\Admin\BackupController::class, 'importProducts'])->name('products.import-backup');

        // Analytics de Produtos
        Route::get('analytics/products', [App\Http\Controllers\Admin\ProductAnalyticsController::class, 'index'])->name('analytics.products');
        Route::get('analytics/products/{product}', [App\Http\Controllers\Admin\ProductAnalyticsController::class, 'getProductDetails'])->name('analytics.product-details');
        Route::get('analytics/products/export', [App\Http\Controllers\Admin\ProductAnalyticsController::class, 'exportAnalytics'])->name('analytics.export');
        
    });
});
