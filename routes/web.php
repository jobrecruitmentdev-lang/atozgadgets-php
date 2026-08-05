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
Route::post('/payment/payoneer', [\App\Http\Controllers\PaymentController::class, 'payWithPayoneer'])->name('payment.payoneer');

Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::prefix('account')->group(function () {
    Route::get('/', [\App\Http\Controllers\AccountController::class, 'dashboard'])->name('account.dashboard');
    Route::get('/orders', [\App\Http\Controllers\AccountController::class, 'orders'])->name('account.orders');
    Route::get('/addresses', [\App\Http\Controllers\AccountController::class, 'addresses'])->name('account.addresses');
    Route::post('/addresses', [\App\Http\Controllers\AccountController::class, 'saveAddress'])->name('account.addresses.save');
});

Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // UI Stub Routes for sidebar navigation
    Route::get('/catalog/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.catalog.products');
    Route::post('/catalog/products', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.catalog.products.store');
    Route::put('/catalog/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.catalog.products.update');
    Route::delete('/catalog/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.catalog.products.destroy');
    
    Route::get('/catalog/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.catalog.categories');
    Route::post('/catalog/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.catalog.categories.store');
    Route::delete('/catalog/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.catalog.categories.destroy');

    Route::get('/catalog/brands', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('admin.catalog.brands');
    Route::post('/catalog/brands', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('admin.catalog.brands.store');
    Route::delete('/catalog/brands/{id}', [\App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('admin.catalog.brands.destroy');

    Route::view('/reports', 'admin.reports')->name('admin.reports');
    Route::view('/settings', 'admin.settings')->name('admin.settings');
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders');
    Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers');
    Route::get('/catalog/import', [\App\Http\Controllers\Admin\CatalogController::class, 'import'])->name('admin.catalog.import');
    Route::get('/api/catalog/search', [\App\Http\Controllers\Admin\CatalogController::class, 'searchCjApi']);
    Route::post('/api/catalog/import-item', [\App\Http\Controllers\Admin\CatalogController::class, 'importCjProduct']);
});
