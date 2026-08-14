<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ==========================================
// STOREFRONT APIs (Matches old Node.js routes)
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/{slug}', [\App\Http\Controllers\Api\ProductController::class, 'show']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
});

// Protected Storefront Routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/cj-test-dump', [\App\Http\Controllers\Admin\CatalogController::class, 'testCjApiDump']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    
    Route::prefix('cart')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CartController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\CartController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\Api\CartController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\CartController::class, 'destroy']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\OrderController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/', [\App\Http\Controllers\AccountController::class, 'addresses']);
        Route::post('/', [\App\Http\Controllers\AccountController::class, 'saveAddress']);
    });
});

// Unauthenticated CJ Webhook
Route::post('cj/webhook', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'webhook']);

// ==========================================
// ADMIN APIs (Matches old Node.js internal APIs)
// ==========================================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);
    
    // CJ Dropshipping Interop (1:1 Express match)
    Route::prefix('cj')->group(function () {
        Route::get('search', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'search']);
        Route::get('products/search', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'search']);
        Route::post('import', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'import']);
        Route::post('products/import', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'import']);
        Route::post('sync', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'sync']);
        Route::post('orders/{orderId}/place', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'placeOrder']);
        Route::post('orders/{cjOrderId}/cancel', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'cancelOrder']);
        Route::post('shipments/sync/{orderId}', [\App\Http\Controllers\Api\Admin\CJDropshippingController::class, 'syncShipment']);
    });

    // Core Management APIs
    Route::apiResource('inventory', \App\Http\Controllers\InventoryController::class);
    Route::apiResource('media', \App\Http\Controllers\MediaFileController::class);
    Route::apiResource('notifications', \App\Http\Controllers\NotificationController::class);
    Route::apiResource('returns', \App\Http\Controllers\ReturnOrderController::class);
    Route::apiResource('user_sessions', \App\Http\Controllers\UserSessionController::class);
});

// ==========================================
// PAYMENT APIs
// ==========================================
Route::prefix('payment')->group(function () {
    Route::post('paypal/create-order', [\App\Http\Controllers\PaymentController::class, 'paypalCreateOrder']);
    Route::post('paypal/capture-order', [\App\Http\Controllers\PaymentController::class, 'paypalCaptureOrder']);
});
