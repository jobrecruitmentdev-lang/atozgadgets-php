<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
});

Route::middleware('storefront')->group(function () {
    Route::get('/', [\App\Http\Controllers\StorefrontController::class, 'home'])->name('store.home');
    Route::get('/shop', [\App\Http\Controllers\StorefrontController::class, 'shop'])->name('store.shop');
    Route::get('/product/{slug}', [\App\Http\Controllers\StorefrontController::class, 'product'])->name('store.product');

    Route::view('/about-us', 'store.about')->name('store.about');
    Route::view('/contact', 'store.contact')->name('store.contact');
    Route::view('/privacy-policy', 'store.privacy')->name('store.privacy');
    Route::view('/terms-conditions', 'store.terms')->name('store.terms');
    Route::view('/return-and-refund-policy', 'store.returns')->name('store.returns');
    Route::view('/shipping-payment-policy-2', 'store.shipping')->name('store.shipping');
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'viewCart'])->name('store.cart');
    Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'addToCart'])->name('store.cart.add');
    Route::get('/checkout', [\App\Http\Controllers\CartController::class, 'checkout'])->name('store.checkout');
    Route::post('/checkout', [\App\Http\Controllers\CartController::class, 'processCheckout'])->name('store.checkout.process');
    Route::post('/checkout/send-otp', [\App\Http\Controllers\CartController::class, 'sendOtp'])->middleware('throttle:3,1')->name('store.checkout.send-otp');
    Route::post('/checkout/verify-otp', [\App\Http\Controllers\CartController::class, 'verifyOtp'])->name('store.checkout.verify-otp');
    
    // Payments
    Route::post('/payment/payoneer', [\App\Http\Controllers\PaymentController::class, 'payWithPayoneer'])->name('payment.payoneer');
    Route::post('/payment/paypal/create-order', [\App\Http\Controllers\PaymentController::class, 'paypalCreateOrder'])->name('payment.paypal.create');
    Route::post('/payment/paypal/capture-order', [\App\Http\Controllers\PaymentController::class, 'paypalCaptureOrder'])->name('payment.paypal.capture');

    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
});
Route::prefix('account')->middleware(['auth', 'storefront'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/orders', [\App\Http\Controllers\AccountController::class, 'orders'])->name('account.orders');
    Route::get('/addresses', [\App\Http\Controllers\AccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/addresses', [\App\Http\Controllers\AccountController::class, 'saveAddress'])->name('account.addresses.save');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Catalog Products, Categories, Brands
    Route::get('/catalog/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.catalog.products');
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

    // Reports & CSV Exports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export/{type}', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');
    
    // Orders
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::post('/orders/{id}/fulfill-cj', [\App\Http\Controllers\Admin\OrderController::class, 'fulfillWithCj'])->name('admin.orders.fulfill_cj');
    Route::put('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('admin.orders.destroy');
    
    // Customers
    Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers');
    Route::put('/customers/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    
    // CJ Import Gateway
    Route::get('/catalog/import', [\App\Http\Controllers\Admin\CatalogController::class, 'import'])->name('admin.catalog.import');
    Route::get('/api/catalog/search', [\App\Http\Controllers\Admin\CatalogController::class, 'searchCjApi']);
    Route::post('/api/catalog/import-item', [\App\Http\Controllers\Admin\CatalogController::class, 'importCjProduct']);
});

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
