<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryAdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MenuAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/testimonial/submit', [HomeController::class, 'submitTestimonial'])->name('testimonial.submit');

// Menu Browse Route (for guests - display only)
Route::get('/menu', [App\Http\Controllers\MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [App\Http\Controllers\MenuDetailController::class, 'show'])->name('menu.detail');

// Logout Route (accessible without shift time check)
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth.custom');

// Customer Orders Routes (Public - for creating orders with full functionality)
Route::get('/orders/create', [App\Http\Controllers\MenuController::class, 'createOrder'])->name('orders.create');
Route::post('/orders/store', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{id}/data', [App\Http\Controllers\OrderController::class, 'getOrderData'])->name('orders.data');
Route::middleware(['auth.custom', 'role:kitchen'])->group(function () {
    // Dashboard Dapur
    Route::get('/dapur', [App\Http\Controllers\DapurController::class, 'index'])->name('dapur');

    // Profile routes for Dapur (without shift time check)
    Route::prefix('dapur/profile')->name('dapur.profile.')->group(function () {
        Route::get('/edit', [App\Http\Controllers\DapurController::class, 'profileEdit'])->name('edit');
        Route::put('/update', [App\Http\Controllers\DapurController::class, 'profileUpdate'])->name('update');
        Route::put('/password', [App\Http\Controllers\DapurController::class, 'profilePassword'])->name('password');
        Route::put('/color', [App\Http\Controllers\DapurController::class, 'profileColorUpdate'])->name('color');
    });

    // Other dapur routes with shift time check
    Route::middleware('check.shift.time')->group(function () {
        // API Routes untuk Dapur
        Route::get('/dapur/orders/active', [App\Http\Controllers\DapurController::class, 'activeOrders'])->name('dapur.orders.active');
        Route::get('/dapur/orders/stream', [App\Http\Controllers\DapurController::class, 'ordersStream'])->name('dapur.orders.stream');
        Route::post('/dapur/orders/{id}/start-cooking', [App\Http\Controllers\DapurController::class, 'startCooking'])->name('dapur.orders.start-cooking');
        Route::post('/dapur/orders/{id}/complete', [App\Http\Controllers\DapurController::class, 'complete'])->name('dapur.orders.complete');

        // Shift Check
        Route::get('/shift/check', [App\Http\Controllers\ShiftController::class, 'checkStatus'])->name('shift.check');

        // Pengaturan Audio
        Route::get('/pengaturan-audio', function () {
            return view('dapur.pengaturan-audio');
        })->name('pengaturan-audio');
        Route::get('/notification-sounds/active', [App\Http\Controllers\NotificationSoundController::class, 'getActive'])->name('notification-sounds.active');

        // Notification Sounds Management
        Route::prefix('notification-sounds')->name('notification-sounds.')->group(function () {
            Route::get('/', [App\Http\Controllers\NotificationSoundController::class, 'index'])->name('index');
            Route::get('/{id}/file', [App\Http\Controllers\NotificationSoundController::class, 'file'])->name('file');
            Route::post('/', [App\Http\Controllers\NotificationSoundController::class, 'store'])->name('store');
            Route::delete('/{id}', [App\Http\Controllers\NotificationSoundController::class, 'destroy'])->name('destroy');
        });
    }); // Close check.shift.time middleware group
});

// Public order route (untuk customer membuat pesanan)
// Note: routes for orders.create, orders.store, orders.show, orders.data sudah didefinisikan di atas

Route::put('/orders/{id}', [App\Http\Controllers\OrderController::class, 'update'])->name('orders.update');
Route::post('/orders/{id}/add-item', [App\Http\Controllers\OrderController::class, 'addItem'])->name('orders.add-item');
Route::delete('/orders/{id}/remove-item/{itemId}', [App\Http\Controllers\OrderController::class, 'removeItem'])->name('orders.remove-item');
Route::post('/orders/{id}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');

// ========================================
// SUPER_ADMIN + ADMIN ROUTES
// All admin and super_admin access /admin dashboard
// Controllers/Policies control what each role can actually do
// ========================================
Route::prefix('admin')->name('admin.')->middleware(['auth.custom', 'role:super_admin|admin'])->group(function () {
    // Profile routes (all roles - without shift time check)
    Route::controller(AdminController::class)->group(function () {
        Route::get('/profile', 'profileEdit')->name('profile.edit');
        Route::put('/profile/update', 'profileUpdate')->name('profile.update');
        Route::put('/profile/color', 'profileColorUpdate')->name('profile.color');
        Route::put('/profile/password', 'profilePassword')->name('profile.password');
    });

    // Other admin routes with shift time check
    Route::middleware('check.shift.time')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');

        // CMS Routes (super_admin & admin - add authorization in controller)
        Route::prefix('cms')->name('cms.')->group(function () {
            Route::get('/hero', [App\Http\Controllers\AdminController::class, 'heroIndex'])->name('hero');
            Route::post('/hero', [App\Http\Controllers\AdminController::class, 'heroUpdate'])->name('hero.update');

            Route::get('/tentang-kami', [App\Http\Controllers\AdminController::class, 'tentangKamiIndex'])->name('tentang-kami');
            Route::post('/tentang-kami', [App\Http\Controllers\AdminController::class, 'tentangKamiUpdate'])->name('tentang-kami.update');

            Route::get('/about-founder', [App\Http\Controllers\AdminController::class, 'aboutFounderIndex'])->name('about-founder');
            Route::post('/about-founder', [App\Http\Controllers\AdminController::class, 'aboutFounderUpdate'])->name('about-founder.update');

            Route::get('/keunggulan-fasilitas', [App\Http\Controllers\AdminController::class, 'keunggulanFasilitasIndex'])->name('keunggulan-fasilitas');
            Route::post('/keunggulan-fasilitas', [App\Http\Controllers\AdminController::class, 'keunggulanFasilitasStore'])->name('keunggulan-fasilitas.store');
            Route::post('/keunggulan-fasilitas/{id}', [App\Http\Controllers\AdminController::class, 'keunggulanFasilitasUpdate'])->name('keunggulan-fasilitas.update');
            Route::delete('/keunggulan-fasilitas/{id}', [App\Http\Controllers\AdminController::class, 'keunggulanFasilitasDestroy'])->name('keunggulan-fasilitas.destroy');

            Route::get('/portfolio-achievement', [App\Http\Controllers\AdminController::class, 'portfolioAchievementIndex'])->name('portfolio-achievement');
            Route::post('/portfolio-achievement', [App\Http\Controllers\AdminController::class, 'portfolioAchievementStore'])->name('portfolio-achievement.store');
            Route::post('/portfolio-achievement/{id}', [App\Http\Controllers\AdminController::class, 'portfolioAchievementUpdate'])->name('portfolio-achievement.update');
            Route::delete('/portfolio-achievement/{id}', [App\Http\Controllers\AdminController::class, 'portfolioAchievementDestroy'])->name('portfolio-achievement.destroy');

            Route::get('/tim-kami', [App\Http\Controllers\AdminController::class, 'timKamiIndex'])->name('tim-kami');
            Route::post('/tim-kami', [App\Http\Controllers\AdminController::class, 'timKamiStore'])->name('tim-kami.store');
            Route::post('/tim-kami/{id}', [App\Http\Controllers\AdminController::class, 'timKamiUpdate'])->name('tim-kami.update');
            Route::delete('/tim-kami/{id}', [App\Http\Controllers\AdminController::class, 'timKamiDestroy'])->name('tim-kami.destroy');

            Route::get('/testimoni-pelanggan', [App\Http\Controllers\AdminController::class, 'testimoniPelangganIndex'])->name('testimoni-pelanggan');
            Route::post('/testimoni-pelanggan', [App\Http\Controllers\AdminController::class, 'testimoniPelangganStore'])->name('testimoni-pelanggan.store');
            Route::post('/testimoni-pelanggan/{id}', [App\Http\Controllers\AdminController::class, 'testimoniPelangganUpdate'])->name('testimoni-pelanggan.update');
            Route::delete('/testimoni-pelanggan/{id}', [App\Http\Controllers\AdminController::class, 'testimoniPelangganDestroy'])->name('testimoni-pelanggan.destroy');

            Route::get('/event', [App\Http\Controllers\AdminController::class, 'eventIndex'])->name('event');
            Route::post('/event', [App\Http\Controllers\AdminController::class, 'eventStore'])->name('event.store');
            Route::post('/event/{id}', [App\Http\Controllers\AdminController::class, 'eventUpdate'])->name('event.update');
            Route::delete('/event/{id}', [App\Http\Controllers\AdminController::class, 'eventDestroy'])->name('event.destroy');

            Route::get('/footer', [App\Http\Controllers\AdminController::class, 'footerIndex'])->name('footer');
            Route::post('/footer', [App\Http\Controllers\AdminController::class, 'footerUpdate'])->name('footer.update');

            Route::get('/contact', [App\Http\Controllers\AdminController::class, 'contactIndex'])->name('contact');
            Route::post('/contact', [App\Http\Controllers\AdminController::class, 'contactUpdate'])->name('contact.update');
        });

        // Order Management (super_admin, admin)
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [App\Http\Controllers\OrderController::class, 'adminIndex'])->name('index');
            Route::get('/check-new', [App\Http\Controllers\OrderController::class, 'checkNewOrders'])->name('check-new');
            Route::post('/{id}/approve', [App\Http\Controllers\OrderController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [App\Http\Controllers\OrderController::class, 'reject'])->name('reject');
            Route::post('/{id}/rekap', [App\Http\Controllers\OrderController::class, 'rekapOrder'])->name('rekap');
            Route::delete('/{id}', [App\Http\Controllers\OrderController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/process-payment', [App\Http\Controllers\OrderController::class, 'processPayment'])->name('process-payment');

            // Recap Orders
            Route::get('/recap', [App\Http\Controllers\OrderController::class, 'recapIndex'])->name('recap.index');
            Route::post('/recap', [App\Http\Controllers\OrderController::class, 'recap'])->name('recap');
            Route::get('/recap/{id}', [App\Http\Controllers\OrderController::class, 'recapDetail'])->name('recap.detail');
            Route::put('/recap/{id}', [App\Http\Controllers\OrderController::class, 'updateRecap'])->name('recap.update');
            Route::get('/recap/{id}/export', [App\Http\Controllers\OrderController::class, 'exportRecap'])->name('recap.export');
            Route::post('/recap/{id}/send-email', [App\Http\Controllers\OrderController::class, 'sendRecapEmail'])->name('recap.send-email');
        });

        // Notification Sounds Management
        Route::prefix('notification-sounds')->name('notification-sounds.')->middleware('permission:notification.manage')->group(function () {
            Route::get('/', [App\Http\Controllers\NotificationSoundController::class, 'index'])->name('index');
            Route::get('/active', [App\Http\Controllers\NotificationSoundController::class, 'getActive'])->name('active');
            Route::get('/{id}/file', [App\Http\Controllers\NotificationSoundController::class, 'file'])->name('file');
            Route::post('/', [App\Http\Controllers\NotificationSoundController::class, 'store'])->name('store');
            Route::post('/{id}/set-active', [App\Http\Controllers\NotificationSoundController::class, 'setActive'])->name('set-active');
            Route::delete('/{id}', [App\Http\Controllers\NotificationSoundController::class, 'destroy'])->name('destroy');
        });

        // Menu Management (super_admin & admin only)
        Route::resource('menus', MenuAdminController::class)->names([
            'index' => 'menus.index',
            'create' => 'menus.create',
            'store' => 'menus.store',
            'edit' => 'menus.edit',
            'update' => 'menus.update',
            'destroy' => 'menus.destroy',
        ]);

        // Category Management (super_admin & admin only)
        Route::resource('categories', CategoryAdminController::class)->names([
            'index' => 'categories.index',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ]);

        // Table Management (super_admin, admin)
        Route::prefix('tables')->name('tables.')->group(function () {
            Route::get('/', [App\Http\Controllers\TableController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\TableController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\TableController::class, 'store'])->name('store');
            Route::post('/{id}/generate-qr', [App\Http\Controllers\TableController::class, 'generateQR'])->name('generate-qr');
            Route::get('/{id}/barcode', [App\Http\Controllers\TableController::class, 'showBarcode'])->name('barcode');
            Route::delete('/{id}', [App\Http\Controllers\TableController::class, 'destroy'])->name('destroy');
        });

        // Redirect old barcode route to tables
        Route::get('/barcode', function () {
            return redirect()->route('admin.tables.index');
        })->name('barcode.index');

        // User Management - check user.view permission
        Route::prefix('manage-users')->name('manage-users.')->middleware('permission:user.view')->group(function () {
            Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\UserController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
        });

        // Menu Sales Chart API (Check report.view permission)
        Route::get('/menu-sales-data', [App\Http\Controllers\AdminController::class, 'getMenuSalesData'])->name('menu-sales-data')->middleware('permission:report.view');

        // Sales Analytics Page (Check report.view permission)
        Route::get('/sales-analytics', [App\Http\Controllers\AdminController::class, 'salesAnalytics'])->name('sales-analytics')->middleware('permission:report.view');

        // Permission Management (super_admin only)
        Route::prefix('permissions')->name('permissions.')->middleware('permission:role.assign')->group(function () {
            Route::get('/select-user', [App\Http\Controllers\PermissionController::class, 'selectUser'])->name('select-user');
            Route::get('/{userId}/manage', [App\Http\Controllers\PermissionController::class, 'managePermissions'])->name('manage');
            Route::post('/{userId}/update', [App\Http\Controllers\PermissionController::class, 'updatePermissions'])->name('update');
            Route::post('/{userId}/toggle', [App\Http\Controllers\PermissionController::class, 'togglePermission'])->name('toggle');
        });

        // Food Inventory Management (super_admin only)
        Route::middleware('permission:inventory.view')->group(function () {
            Route::resource('inventory', App\Http\Controllers\FoodInventoryController::class)->except(['show']);
            Route::get('/inventory/{menu}/status', [App\Http\Controllers\FoodInventoryController::class, 'getStatus'])->name('inventory.status');
        });
    }); // Close check.shift.time middleware group
});
