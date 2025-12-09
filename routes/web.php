<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\DueOrderController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Order\OrderPendingController;
use App\Http\Controllers\Order\OrderCompleteController;
use App\Http\Controllers\Order\OrderForDeliveryController;
use App\Http\Controllers\Dashboards\DashboardController;
use App\Http\Controllers\Product\ProductExportController;
use App\Http\Controllers\Product\ProductImportController;
use App\Http\Controllers\MeatCutController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Staff\InventoryController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('php/', function () {
    return phpinfo();
});

Route::get('test-customer-login', function () {
    return 'Customer login route is working!';
})->name('test.customer.login');

Route::get('test-deactivate', function () {
    return view('customer.profile');
})->name('test.deactivate');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/customer/landing', function () {
    return view('customer.landing');
})->name('customer.landing');

Route::get('/dashboard', function () {
    return redirect()->route('login');
});

// ============================================================================
// CUSTOMER ROUTES - FIXED TO PREVENT REDIRECT LOOPS
// ============================================================================

// Customer Authentication Routes (Web) - GUEST ONLY
// Uses 'guest:web_customer' middleware - only for non-authenticated customers
Route::middleware('guest:web_customer')->group(function () {
    Route::get('/customer/register', [App\Http\Controllers\Customer\WebAuthController::class, 'showRegistrationForm'])->name('customer.register');
    Route::post('/customer/register', [App\Http\Controllers\Customer\WebAuthController::class, 'register'])->name('customer.register.store');
    Route::get('/customer/login', [App\Http\Controllers\Customer\WebAuthController::class, 'showLoginForm'])->name('customer.login');
    Route::post('/customer/login', [App\Http\Controllers\Customer\WebAuthController::class, 'login'])->name('customer.login.store');
});

// Customer Password Reset Routes - GUEST ONLY
// Uses 'guest:web_customer' middleware for password reset requests
Route::prefix('customer')->name('customer.')->middleware('guest:web_customer')->group(function () {
    Route::get('forgot-password', [App\Http\Controllers\Customer\CustomerPasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Customer\CustomerPasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [App\Http\Controllers\Customer\CustomerPasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [App\Http\Controllers\Customer\CustomerPasswordResetController::class, 'reset'])->name('password.update');
});

// Customer Email Verification Routes - AUTHENTICATED ONLY
// Uses 'auth:web_customer' middleware - only for authenticated customers
Route::prefix('customer')->name('customer.')->middleware(['auth:web_customer'])->group(function () {
    Route::get('email/verify', [App\Http\Controllers\Customer\CustomerVerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [App\Http\Controllers\Customer\CustomerVerificationController::class, 'verify'])->name('verification.verify');
   
    Route::post('email/resend', [App\Http\Controllers\Customer\CustomerVerificationController::class, 'resend'])->name('verification.resend');
});

// Customer Protected Routes - AUTHENTICATED + VERIFIED ONLY
// Uses 'customer.web.auth' middleware - requires authentication AND email verification
Route::middleware(['customer.web.auth'])->group(function () {
    Route::get('/customer/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('customer.orders');
    Route::get('/my-orders/{order}', [\App\Http\Controllers\Customer\OrderController::class, 'showOrder'])->name('customer.orders.show');
    Route::post('/my-orders/{order}/cancel', [\App\Http\Controllers\Customer\OrderController::class, 'cancel'])->name('customer.orders.cancel');
    Route::post('/my-orders/{order}/received', [\App\Http\Controllers\Customer\OrderController::class, 'received'])->name('customer.orders.received');
    Route::post('/customer/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('customer.orders.cancel.admin');
    Route::get('/customer/orders/{order}/download-invoice', [\App\Http\Controllers\Customer\OrderController::class, 'downloadInvoice'])->name('customer.orders.download-invoice');

    Route::get('/my-profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::patch('/customer/profile', [CustomerController::class, 'update'])->name('customer.profile.update');
    Route::delete('/customer/profile/deactivate', [CustomerController::class, 'deactivate'])->name('customer.profile.deactivate');
    Route::post('/customer/logout', [App\Http\Controllers\Customer\WebAuthController::class, 'logout'])->name('customer.logout');
  
    // Customer Notifications
    Route::prefix('customer/notifications')->name('customer.notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Customer\NotificationController::class, 'index'])->name('index');
        Route::post('/mark-all-read', [App\Http\Controllers\Customer\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::put('/{notification}/read', [App\Http\Controllers\Customer\NotificationController::class, 'markAsRead'])->name('mark-read');
    });

    // Customer Shopping Cart
    Route::get('/cart', [App\Http\Controllers\Customer\CartController::class, 'index'])->name('customer.cart');
    Route::post('/cart/add', [App\Http\Controllers\Customer\CartController::class, 'add'])->name('customer.cart.add');
    Route::put('/cart/update/{rowId}', [App\Http\Controllers\Customer\CartController::class, 'update'])->name('customer.cart.update');
    Route::delete('/cart/remove/{rowId}', [App\Http\Controllers\Customer\CartController::class, 'remove'])->name('customer.cart.remove');
    Route::delete('/cart/clear', [App\Http\Controllers\Customer\CartController::class, 'clear'])->name('customer.cart.clear');
    
    // Customer Checkout
    Route::get('/checkout', [App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('customer.checkout');
    Route::post('/checkout/place-order', [App\Http\Controllers\Customer\CheckoutController::class, 'placeOrder'])->name('customer.checkout.place-order');
});

// Customer Product Routes - Using simpler auth middleware
Route::middleware(['auth:web_customer'])->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('/products', [\App\Http\Controllers\Customer\ProductController::class, 'index'])->name('customer.products');
        Route::get('/products/{product}', [\App\Http\Controllers\Customer\ProductController::class, 'show'])->name('customer.products.show');
        Route::get('/categories/{category}', [\App\Http\Controllers\Customer\ProductController::class, 'category'])->name('customer.products.category');
    });

    // Customer Profile

});

// ============================================================================
// SUPPLIER ROUTES
// ============================================================================

// Supplier Authentication Routes - GUEST ONLY
Route::middleware('guest:web')->group(function () {
    Route::get('/supplier/login', [App\Http\Controllers\Supplier\SupplierAuthController::class, 'showLoginForm'])->name('supplier.login');
    Route::post('/supplier/login', [App\Http\Controllers\Supplier\SupplierAuthController::class, 'login'])->name('supplier.login.store');
    Route::get('/supplier/register', [App\Http\Controllers\Supplier\SupplierAuthController::class, 'showRegistrationForm'])->name('supplier.register');
    Route::post('/supplier/register', [App\Http\Controllers\Supplier\SupplierAuthController::class, 'register'])->name('supplier.register.store');
});

// Supplier Protected Routes - AUTHENTICATED + ROLE CHECK
Route::middleware(['auth:web', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [App\Http\Controllers\Supplier\SupplierAuthController::class, 'logout'])->name('logout');
    
    // Supplier Purchases - Enhanced with communication
    Route::get('/purchases', [App\Http\Controllers\Supplier\PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/{id}', [App\Http\Controllers\Supplier\PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{id}/download-invoice', [App\Http\Controllers\Supplier\PurchaseController::class, 'downloadInvoice'])->name('purchases.download-invoice');
    Route::get('/purchases/{id}/edit-invoice', [App\Http\Controllers\Supplier\PurchaseController::class, 'editInvoice'])->name('purchases.edit-invoice');
    Route::post('/purchases/{id}/preview-invoice', [App\Http\Controllers\Supplier\PurchaseController::class, 'previewInvoice'])->name('purchases.preview-invoice');
    Route::post('/purchases/{id}/notes', [App\Http\Controllers\Supplier\PurchaseController::class, 'updateNotes'])->name('purchases.update-notes');
    Route::post('/purchases/{id}/delivery-status', [App\Http\Controllers\Supplier\PurchaseController::class, 'updateDeliveryStatus'])->name('purchases.update-delivery-status');
    
    // Supplier Deliveries/Procurements - Enhanced with status updates
    Route::get('/deliveries', [App\Http\Controllers\Supplier\DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{id}', [App\Http\Controllers\Supplier\DeliveryController::class, 'show'])->name('deliveries.show');
    Route::post('/deliveries/{id}/status', [App\Http\Controllers\Supplier\DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
});

// ============================================================================
// STAFF/ADMIN ROUTES
// ============================================================================

// Staff/Admin Guest Routes
Route::middleware('guest:web')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Staff/Admin Protected Routes
Route::middleware(['auth:web'])->group(function () {
    // Admin/Staff logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Notifications
    Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [AdminNotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent', [AdminNotificationController::class, 'getRecentNotifications'])->name('recent');
        Route::post('/{admin_notification}/mark-read', [AdminNotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/stats', [AdminNotificationController::class, 'getStats'])->name('stats');
    });

    // Staff Notifications
    Route::prefix('staff/notifications')->name('staff.notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\StaffNotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [App\Http\Controllers\StaffNotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent', [App\Http\Controllers\StaffNotificationController::class, 'getRecentNotifications'])->name('recent');
        Route::post('/{staff_notification}/mark-read', [App\Http\Controllers\StaffNotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [App\Http\Controllers\StaffNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/stats', [App\Http\Controllers\StaffNotificationController::class, 'getStats'])->name('stats');
    });

    // Admin and Staff Routes
    Route::middleware(['role:admin,staff'])->group(function () {
        // Product Management
        Route::resource('/products', ProductController::class);
        Route::get('/products/import', [ProductImportController::class, 'create'])->name('products.import.view');
        Route::post('/products/import', [ProductImportController::class, 'store'])->name('products.import.store');
        Route::get('/products/export', [ProductExportController::class, 'create'])->name('products.export.store');
        
        // Inventory Management
        Route::resource('/categories', CategoryController::class);
        Route::resource('/units', UnitController::class);
        Route::resource('/meat-cuts', MeatCutController::class);
        
        // Order Management
        Route::get('/orders/pending', OrderPendingController::class)->name('orders.pending');
        Route::get('/orders/for-delivery', OrderForDeliveryController::class)->name('orders.for-delivery');
        Route::get('/orders/complete', OrderCompleteController::class)->name('orders.complete');
        Route::post('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    });

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::resource('/users', UserController::class);
        Route::put('/user/change-password/{username}', [UserController::class, 'updatePassword'])->name('users.updatePassword');
        Route::put('/user/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
        
        // Supplier Management
        Route::resource('/suppliers', SupplierController::class);
        Route::post('/suppliers/{supplier}/assign-products', [SupplierController::class, 'assignProducts'])
         ->name('suppliers.assign-products');
        Route::get('/suppliers/{supplier}/purchase-order', [SupplierController::class, 'purchaseOrder'])
         ->name('suppliers.purchase-order');
        Route::post('/suppliers/{supplier}/purchase-order', [SupplierController::class, 'storePurchaseOrder'])
         ->name('suppliers.purchase-order.store');
        Route::post('/suppliers/{supplier}/purchase-order/pdf', [SupplierController::class, 'downloadPurchaseOrderPdf'])
         ->name('suppliers.purchase-order.pdf');
        
    });

    // Shared Routes (All Authenticated Users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/customers', CustomerController::class);

    // Route Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');


    // SHOW ORDER
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    //  Order Status Update
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::post('/admin/orders/{order}/status', [OrderController::class, 'updateOrderStatus'])->name('orders.updateOrderStatus');

    // DUES
    Route::get('/due/orders/', [DueOrderController::class, 'index'])->name('due.index');
    Route::get('/due/order/view/{order}', [DueOrderController::class, 'show'])->name('due.show');
    Route::get('/due/order/edit/{order}', [DueOrderController::class, 'edit'])->name('due.edit');
    Route::put('/due/order/update/{order}', [DueOrderController::class, 'update'])->name('due.update');

    // TODO: Remove from OrderController
    Route::get('/orders/details/{orderId}/download', [OrderController::class, 'downloadInvoice'])->name('order.downloadInvoice');
    Route::get('/orders/{order_id}/edit-invoice', [OrderController::class, 'editInvoice'])->name('orders.edit-invoice');
    Route::post('/orders/{order_id}/preview-invoice', [OrderController::class, 'previewInvoice'])->name('orders.preview-invoice');

    // Route Purchases
    Route::get('/purchases/approved', [PurchaseController::class, 'approvedPurchases'])->name('purchases.approvedPurchases');
    Route::get('/purchases/report', [PurchaseController::class, 'dailyPurchaseReport'])->name('purchases.dailyPurchaseReport');
    Route::get('/purchases/report/export', [PurchaseController::class, 'getPurchaseReport'])->name('purchases.getPurchaseReport');
    Route::post('/purchases/report/export', [PurchaseController::class, 'exportPurchaseReport'])->name('purchases.exportPurchaseReport');

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{purchase}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::put('/purchases/{purchase}/edit', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.delete');
    Route::post('/purchases/{purchase}/mark-received', [PurchaseController::class, 'markAsReceived'])->name('purchases.mark-received');
    Route::post('/purchases/{purchase}/mark-complete', [PurchaseController::class, 'markAsComplete'])->name('purchases.mark-complete');
    Route::get('/purchases/{purchase}/download-invoice', [PurchaseController::class, 'downloadInvoice'])->name('purchases.download-invoice');

    // Supplier Management Routes
    Route::resource('suppliers', SupplierController::class);

    // Reports Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/inventory', [\App\Http\Controllers\InventoryReportController::class, 'index'])->name('reports.inventory');
        Route::get('/reports/inventory/analytics', [\App\Http\Controllers\InventoryReportController::class, 'analytics'])->name('reports.inventory.analytics');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
        Route::get('/reports/stock-levels', [ReportController::class, 'stockLevels'])->name('reports.stock-levels');
        Route::get('/reports/export-inventory', [ReportController::class, 'exportInventory'])->name('reports.export-inventory');
        Route::get('/reports/export-sales', [ReportController::class, 'exportSales'])->name('reports.export-sales');
        
        // Sales Analytics Routes
        Route::get('/reports/sales-analytics', [\App\Http\Controllers\SalesAnalyticsController::class, 'index'])->name('reports.sales.analytics');
        Route::get('/reports/sales-analytics/get-2025', [\App\Http\Controllers\SalesAnalyticsController::class, 'get2025Data'])->name('reports.sales.analytics.get-2025');
        Route::post('/reports/sales-analytics/store-2025', [\App\Http\Controllers\SalesAnalyticsController::class, 'store2025'])->name('reports.sales.analytics.store-2025');
        Route::get('/reports/sales-analytics/export-pdf', [\App\Http\Controllers\SalesAnalyticsController::class, 'exportPDF'])->name('reports.sales.analytics.export-pdf');
        Route::get('/reports/sales-analytics/export-csv', [\App\Http\Controllers\SalesAnalyticsController::class, 'exportCSV'])->name('reports.sales.analytics.export-csv');
        Route::get('/reports/sales-analytics/monthly-details', [\App\Http\Controllers\SalesAnalyticsController::class, 'getMonthlyDetails'])->name('reports.sales.analytics.monthly-details');
        Route::get('/reports/sales-analytics/daily-sales', [\App\Http\Controllers\SalesAnalyticsController::class, 'getDailySales'])->name('reports.sales.analytics.daily-sales');
        Route::get('/reports/sales-analytics/daily-order-products', [\App\Http\Controllers\SalesAnalyticsController::class, 'getDailyOrderProducts'])->name('reports.sales.analytics.daily-order-products');
        
        // Supplier Analytics Routes
        Route::get('/reports/supplier-analytics', [\App\Http\Controllers\SupplierAnalyticsController::class, 'index'])->name('reports.supplier.analytics');
        Route::get('/reports/supplier-analytics/export', [\App\Http\Controllers\SupplierAnalyticsController::class, 'export'])->name('reports.supplier.analytics.export');
        
        // Expense Management Routes
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('index');
            Route::get('/voided', [\App\Http\Controllers\ExpenseController::class, 'voided'])->name('voided');
            
            // Utility Expenses
            Route::resource('utilities', \App\Http\Controllers\UtilityExpenseController::class);
            Route::post('utilities/{utilityExpense}/mark-paid', [\App\Http\Controllers\UtilityExpenseController::class, 'markAsPaid'])->name('utilities.mark-paid');
            Route::post('utilities/{utilityExpense}/void', [\App\Http\Controllers\UtilityExpenseController::class, 'void'])->name('utilities.void');
            
            // Payroll
            Route::resource('payroll', \App\Http\Controllers\PayrollController::class);
            Route::post('payroll/{payrollRecord}/mark-paid', [\App\Http\Controllers\PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');
            Route::get('payroll/generate/{year}/{month}', [\App\Http\Controllers\PayrollController::class, 'generateMonthly'])->name('payroll.generate');
            
            // Other Expenses
            Route::resource('other', \App\Http\Controllers\OtherExpenseController::class);
        });
        
        // Income Report Routes
        Route::get('/reports/income', [\App\Http\Controllers\IncomeReportController::class, 'index'])->name('reports.income');
        Route::get('/reports/income/export-csv', [\App\Http\Controllers\IncomeReportController::class, 'exportCsv'])->name('reports.income.export-csv');
        

        
        // Legacy Sales Analytics Routes (redirects)
        Route::get('/sales-analytics', [\App\Http\Controllers\SalesAnalyticsController::class, 'index'])->name('sales-analytics.index');
        Route::post('/sales-analytics', [\App\Http\Controllers\SalesAnalyticsController::class, 'store'])->name('sales-analytics.store');
        Route::put('/sales-analytics/{id}', [\App\Http\Controllers\SalesAnalyticsController::class, 'update'])->name('sales-analytics.update');
        Route::get('/sales-analytics/expense-breakdown/{year}/{month}', [\App\Http\Controllers\SalesAnalyticsController::class, 'getExpenseBreakdown'])->name('sales-analytics.expense-breakdown');
        
        // Staff Performance Routes (Admin Only)
        Route::resource('staff', \App\Http\Controllers\StaffController::class);
        Route::resource('staff-performance', \App\Http\Controllers\StaffPerformanceController::class);
        Route::get('reports/staff-performance', [\App\Http\Controllers\StaffPerformanceController::class, 'report'])->name('staff.report');
        
        // Market Analysis Routes (Admin Only)
        Route::get('reports/market-analysis', [\App\Http\Controllers\MarketAnalysisController::class, 'index'])->name('reports.market.analysis');
        Route::get('reports/market-analysis/export', [\App\Http\Controllers\MarketAnalysisController::class, 'exportData'])->name('reports.market.analysis.export');
        Route::get('reports/market-analysis/debug', [\App\Http\Controllers\MarketAnalysisController::class, 'debugTrends'])->name('reports.market.analysis.debug');
    });

    // Staff Inventory Management Routes
    Route::middleware(['auth', 'role:staff'])->prefix('staff/inventory')->name('staff.inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{movement}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{movement}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{movement}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/reorder', [InventoryController::class, 'reorder'])->name('reorder');
        Route::get('/follow-up', [InventoryController::class, 'followUp'])->name('follow-up');
        Route::get('/discard', [InventoryController::class, 'discard'])->name('discard');
        Route::put('/{movement}/receive', [InventoryController::class, 'receive'])->name('receive');
        Route::put('/{product}/discard', [InventoryController::class, 'markAsDiscarded'])->name('mark-discarded');
    });

    Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
        Route::get('products', [\App\Http\Controllers\Staff\InventoryController::class, 'products'])->name('products.index');
    });
});

require __DIR__.'/auth.php';