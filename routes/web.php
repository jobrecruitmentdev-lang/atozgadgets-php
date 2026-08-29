<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
});

Route::get('/admin-strategy-hub.html', function () {
    return redirect()->route('admin.strategy_hub');
});

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/llms.txt', function () {
    $path = public_path('llms.txt');
    return file_exists($path)
        ? response(file_get_contents($path), 200, ['Content-Type' => 'text/plain; charset=utf-8'])
        : response('# AtoZGadgets\n\nTrending Gadgets Store USA.', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->name('llms.txt');


Route::middleware('storefront')->group(function () {
    Route::get('/', [\App\Http\Controllers\StorefrontController::class, 'home'])->name('store.home');
    Route::get('/shop', [\App\Http\Controllers\StorefrontController::class, 'shop'])->name('store.shop');
    Route::get('/product/{slug}', [\App\Http\Controllers\StorefrontController::class, 'product'])->name('store.product');
    Route::post('/product/{slug}/review', [\App\Http\Controllers\StorefrontController::class, 'submitReview'])->name('store.product.review');

    // Secure Media Proxy Routes (White-labels external supplier CDNs)
    Route::get('/media/products/{product}/thumbnail', [\App\Http\Controllers\MediaController::class, 'thumbnail'])->name('media.product.thumbnail');
    Route::get('/media/products/{product}/image/{mediaId}', [\App\Http\Controllers\MediaController::class, 'image'])->name('media.product.image');

    Route::view('/about-us', 'store.about')->name('store.about');
    Route::view('/contact', 'store.contact')->name('store.contact');
    Route::view('/privacy-policy', 'store.privacy')->name('store.privacy');
    Route::view('/terms-conditions', 'store.terms')->name('store.terms');
    Route::view('/return-and-refund-policy', 'store.returns')->name('store.returns');
    Route::view('/shipping-payment-policy-2', 'store.shipping')->name('store.shipping');
    Route::view('/shipping-payment-policy', 'store.shipping');
    Route::view('/shipping-policy', 'store.shipping');
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'viewCart'])->name('store.cart');
    Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'addToCart'])->name('store.cart.add');
    Route::get('/checkout', [\App\Http\Controllers\CartController::class, 'checkout'])->name('store.checkout');
    Route::post('/checkout', [\App\Http\Controllers\CartController::class, 'processCheckout'])->name('store.checkout.process');
    Route::post('/checkout/send-otp', [\App\Http\Controllers\CartController::class, 'sendOtp'])->middleware('throttle:3,1')->name('store.checkout.send-otp');
    Route::post('/checkout/verify-otp', [\App\Http\Controllers\CartController::class, 'verifyOtp'])->name('store.checkout.verify-otp');
    Route::post('/checkout/check-eligibility', [\App\Http\Controllers\CartController::class, 'checkShippingEligibility'])->name('store.checkout.eligibility');
    
    // Payments & Order Confirmation
    Route::post('/payment/paypal/create-order', [\App\Http\Controllers\PaymentController::class, 'paypalCreateOrder'])->name('payment.paypal.create');
    Route::post('/payment/paypal/capture-order', [\App\Http\Controllers\PaymentController::class, 'paypalCaptureOrder'])->name('payment.paypal.capture');
    Route::get('/order-confirmation/{order_number}', [\App\Http\Controllers\StorefrontController::class, 'orderConfirmation'])->name('store.order_confirmation');

    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
    
    // Password Reset Flow
    Route::get('/forgot-password', [\App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\AuthController::class, 'resetPassword'])->name('password.update');
});

Route::prefix('account')->middleware(['auth', 'storefront'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/orders', [\App\Http\Controllers\AccountController::class, 'orders'])->name('account.orders');
    Route::get('/addresses', [\App\Http\Controllers\AccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/addresses', [\App\Http\Controllers\AccountController::class, 'saveAddress'])->name('account.addresses.save');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/strategy-hub', function () {
        return response()->view('admin.strategy_hub')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    })->name('admin.strategy_hub');

    Route::get('/admin-strategy-hub.html', function () {
        return redirect()->route('admin.strategy_hub');
    });

    // Catalog Products, Categories, Brands, Import
    Route::get('/catalog/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.catalog.products');
    Route::post('/catalog/products/bulk-action', [\App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])->name('admin.catalog.products.bulk_action');
    Route::post('/catalog/products', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.catalog.products.store');
    Route::put('/catalog/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.catalog.products.update');
    Route::delete('/catalog/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.catalog.products.destroy');
    
    Route::get('/catalog/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.catalog.categories');
    Route::post('/catalog/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.catalog.categories.store');
    Route::put('/catalog/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.catalog.categories.update');
    Route::delete('/catalog/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.catalog.categories.destroy');

    Route::get('/catalog/brands', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('admin.catalog.brands');
    Route::post('/catalog/brands', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('admin.catalog.brands.store');
    Route::put('/catalog/brands/{id}', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('admin.catalog.brands.update');
    Route::delete('/catalog/brands/{id}', [\App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('admin.catalog.brands.destroy');

    Route::get('/catalog/import', [\App\Http\Controllers\Admin\CatalogController::class, 'import'])->name('admin.catalog.import');
    Route::patch('/catalog/products/{id}/toggle-status', [\App\Http\Controllers\Admin\CatalogController::class, 'toggleProductStatus'])->name('admin.catalog.products.toggle_status');
    Route::get('/api/catalog/search', [\App\Http\Controllers\Admin\CatalogController::class, 'searchCjApi']);
    Route::get('/api/catalog/cj-categories', [\App\Http\Controllers\Admin\CatalogController::class, 'getCjCategories']);
    Route::post('/api/catalog/import-item', [\App\Http\Controllers\Admin\CatalogController::class, 'importCjProduct']);

    // Commerce: Orders, Customers, Payments, Reviews
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{id}/fulfill', [\App\Http\Controllers\Admin\OrderController::class, 'fulfillOrder'])->name('admin.orders.fulfill');
    Route::post('/orders/{id}/fulfill-cj', [\App\Http\Controllers\Admin\OrderController::class, 'fulfillWithCj'])->name('admin.orders.fulfill_cj');
    Route::post('/orders/{id}/sync-cj', [\App\Http\Controllers\Admin\OrderController::class, 'syncCjStatus'])->name('admin.orders.sync_cj');
    Route::post('/orders/{id}/refund', [\App\Http\Controllers\Admin\OrderController::class, 'processRefund'])->name('admin.orders.refund');
    Route::put('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('admin.orders.destroy');
    
    Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers');
    Route::put('/customers/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    Route::get('/commerce/payments', [\App\Http\Controllers\Admin\PaymentAdminController::class, 'index'])->name('admin.commerce.payments');
    Route::get('/commerce/reviews', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'index'])->name('admin.commerce.reviews');
    Route::post('/commerce/reviews/{id}/status', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'updateStatus'])->name('admin.commerce.reviews.update_status');
    Route::delete('/commerce/reviews/{id}', [\App\Http\Controllers\Admin\ReviewAdminController::class, 'destroy'])->name('admin.commerce.reviews.destroy');

    // Fulfillment Hub: Overview, Queue, Shipments, Exceptions
    Route::get('/fulfillment', [\App\Http\Controllers\Admin\FulfillmentController::class, 'overview'])->name('admin.fulfillment.overview');
    Route::get('/fulfillment/queue', [\App\Http\Controllers\Admin\FulfillmentController::class, 'queue'])->name('admin.fulfillment.queue');
    Route::get('/fulfillment/shipments', [\App\Http\Controllers\Admin\FulfillmentController::class, 'shipments'])->name('admin.fulfillment.shipments');
    Route::get('/fulfillment/exceptions', [\App\Http\Controllers\Admin\FulfillmentController::class, 'exceptions'])->name('admin.fulfillment.exceptions');
    Route::post('/fulfillment/{id}/retry', [\App\Http\Controllers\Admin\FulfillmentController::class, 'retry'])->name('admin.fulfillment.retry');
    Route::post('/fulfillment/exceptions/{id}/resolve', [\App\Http\Controllers\Admin\FulfillmentController::class, 'resolveException'])->name('admin.fulfillment.resolve_exception');

    // Analytics: Sales, Products, Profitability
    Route::get('/analytics/sales', [\App\Http\Controllers\Admin\AnalyticsAdminController::class, 'sales'])->name('admin.analytics.sales');
    Route::get('/analytics/products', [\App\Http\Controllers\Admin\AnalyticsAdminController::class, 'products'])->name('admin.analytics.products');
    Route::get('/analytics/profitability', [\App\Http\Controllers\Admin\AnalyticsAdminController::class, 'profitability'])->name('admin.analytics.profitability');

    // System: Health, Settings, Audit Logs, Reports
    Route::get('/system/health', [\App\Http\Controllers\Admin\HealthController::class, 'index'])->name('admin.system.health');
    Route::get('/system/audit-logs', [\App\Http\Controllers\Admin\AuditLogAdminController::class, 'index'])->name('admin.system.audit_logs');
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export/{type}', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export');

    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/toggle-cj-sandbox', [\App\Http\Controllers\Admin\SettingController::class, 'toggleCjSandbox'])->name('admin.settings.toggle_cj_sandbox');
    Route::post('/settings/test-cj-connection', [\App\Http\Controllers\Admin\SettingController::class, 'testCjConnection'])->name('admin.settings.test_cj_connection');
});

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');