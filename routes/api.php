<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('products/', [ProductController::class, 'index'])->name('api.product.index');

// Analytics API Routes (Optimized)
Route::prefix('analytics')->group(function () {
    Route::get('/inventory', [App\Http\Controllers\OptimizedReportController::class, 'inventoryAnalytics']);
    Route::get('/sales', [App\Http\Controllers\OptimizedReportController::class, 'salesAnalytics']);
    Route::get('/suppliers', [App\Http\Controllers\OptimizedReportController::class, 'supplierAnalytics']);
    Route::get('/staff', [App\Http\Controllers\OptimizedReportController::class, 'staffPerformanceAnalytics']);
    Route::get('/dashboard', [App\Http\Controllers\OptimizedReportController::class, 'dashboardAnalytics']);
    Route::post('/clear-cache', [App\Http\Controllers\OptimizedReportController::class, 'clearCache']);
});

// Performance Monitoring API Routes
Route::prefix('performance')->group(function () {
    Route::get('/metrics', [App\Http\Controllers\PerformanceController::class, 'getMetrics']);
    Route::get('/diagnostics', [App\Http\Controllers\PerformanceController::class, 'runDiagnostics']);
    Route::post('/optimize', [App\Http\Controllers\PerformanceController::class, 'optimize']);
});

// Customer Authentication Routes (Public)
Route::prefix('customer')->group(function () {
    Route::post('/register', [App\Http\Controllers\Customer\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Customer\AuthController::class, 'login']);
});

// Customer Protected Routes
Route::prefix('customer')->middleware(['auth:sanctum', 'customer.auth'])->group(function () {
    // Auth routes
    Route::post('/logout', [App\Http\Controllers\Customer\AuthController::class, 'logout']);
    Route::get('/profile', [App\Http\Controllers\Customer\AuthController::class, 'profile']);
    Route::put('/profile', [App\Http\Controllers\Customer\AuthController::class, 'updateProfile']);
    Route::put('/change-password', [App\Http\Controllers\Customer\AuthController::class, 'changePassword']);
    Route::get('/dashboard', [App\Http\Controllers\Customer\AuthController::class, 'dashboard']);
    Route::get('/auth-history', [App\Http\Controllers\Customer\AuthController::class, 'authHistory']);

    // Order routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [App\Http\Controllers\Customer\OrderController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Customer\OrderController::class, 'store']);
        Route::get('/statistics', [App\Http\Controllers\Customer\OrderController::class, 'statistics']);
        Route::get('/{order}', [App\Http\Controllers\Customer\OrderController::class, 'show']);
        Route::post('/{order}/cancel', [App\Http\Controllers\Customer\OrderController::class, 'cancel']);
        Route::get('/{order}/track', [App\Http\Controllers\Customer\OrderController::class, 'track']);
    });

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [App\Http\Controllers\Customer\PaymentController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Customer\PaymentController::class, 'store']);
        Route::get('/methods', [App\Http\Controllers\Customer\PaymentController::class, 'paymentMethods']);
        Route::get('/statistics', [App\Http\Controllers\Customer\PaymentController::class, 'statistics']);
        Route::get('/{payment}', [App\Http\Controllers\Customer\PaymentController::class, 'show']);
        Route::put('/{payment}', [App\Http\Controllers\Customer\PaymentController::class, 'update']);
        Route::get('/{payment}/receipt', [App\Http\Controllers\Customer\PaymentController::class, 'downloadReceipt']);
    });

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\Customer\NotificationController::class, 'index']);
        Route::get('/recent', [App\Http\Controllers\Customer\NotificationController::class, 'recent']);
        Route::get('/unread-count', [App\Http\Controllers\Customer\NotificationController::class, 'unreadCount']);
        Route::get('/statistics', [App\Http\Controllers\Customer\NotificationController::class, 'statistics']);
        Route::post('/mark-all-read', [App\Http\Controllers\Customer\NotificationController::class, 'markAllAsRead']);
        Route::get('/{notification}', [App\Http\Controllers\Customer\NotificationController::class, 'show']);
        Route::put('/{notification}/read', [App\Http\Controllers\Customer\NotificationController::class, 'markAsRead']);
        Route::put('/{notification}/unread', [App\Http\Controllers\Customer\NotificationController::class, 'markAsUnread']);
        Route::delete('/{notification}', [App\Http\Controllers\Customer\NotificationController::class, 'destroy']);
    });
});