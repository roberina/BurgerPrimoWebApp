<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BurgerBuilderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\AddonItemController;
use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ReviewController;

Route::get('/api/addons', [AddonItemController::class, 'publicIndex']);

$nocsrf = \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class;
Route::post('/api/push/subscribe', [PushSubscriptionController::class, 'store'])
    ->withoutMiddleware([$nocsrf]);
Route::post('/api/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])
    ->withoutMiddleware([$nocsrf]);

// Courier tracking — public routes protected by unique token (no login needed)
Route::get('/courier/track/{token}', [CourierController::class, 'track'])->name('courier.track');
$nocsrf = \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class;
Route::post('/courier/location/{token}', [CourierController::class, 'updateLocation'])
    ->name('courier.location')
    ->withoutMiddleware([$nocsrf]);
Route::post('/courier/accept/{token}', [CourierController::class, 'accept'])
    ->name('courier.accept')
    ->withoutMiddleware([$nocsrf]);
Route::post('/courier/decline/{token}', [CourierController::class, 'decline'])
    ->name('courier.decline')
    ->withoutMiddleware([$nocsrf]);
Route::post('/courier/delivered/{token}', [CourierController::class, 'delivered'])
    ->name('courier.delivered')
    ->withoutMiddleware([$nocsrf]);

Route::get('/', function () {
    $featuredItems = \App\Models\MenuItem::with('category')
        ->where('is_featured', true)
        ->where('is_active', true)
        ->get();
    $announcements = [];
    try { $announcements = \App\Models\Announcement::visible()->ordered()->get(); } catch (\Throwable $e) {}
    $reviews = [];
    try { $reviews = \App\Models\Review::approved()->latest()->get(); } catch (\Throwable $e) {}
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'featuredItems' => $featuredItems,
        'announcements' => $announcements,
        'reviews' => $reviews,
    ]);
})->name('home');

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [MenuController::class, 'category'])->name('menu.category');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/kontakt', function () {
    $announcements = [];
    try { $announcements = \App\Models\Announcement::visible()->ordered()->get(); } catch (\Throwable $e) {}
    return Inertia::render('Kontakt/Index', ['announcements' => $announcements]);
})->name('kontakt');

Route::get('/meelelahutus', function () {
    $announcements = [];
    try { $announcements = \App\Models\Announcement::visible()->ordered()->get(); } catch (\Throwable $e) {}
    return Inertia::render('Meelelahutus/Index', ['announcements' => $announcements]);
})->name('meelelahutus');

Route::middleware(['auth'])->group(function () {

    Route::post('/menu/{menuItem}/favorite', [App\Http\Controllers\MenuFavoriteController::class, 'toggle'])->name('menu.favorite.toggle');

    // Burger Builder
    Route::get('/burger-builder', [BurgerBuilderController::class, 'index'])->name('burger-builder.index');
    Route::post('/burger-builder', [BurgerBuilderController::class, 'store'])->name('burger-builder.store');
    Route::put('/burger-builder/{burger}', [BurgerBuilderController::class, 'update'])->name('burger-builder.update');
    Route::delete('/burger-builder/{burger}', [BurgerBuilderController::class, 'destroy'])->name('burger-builder.destroy');
    Route::post('/burger-builder/{burger}/favorite', [BurgerBuilderController::class, 'toggleFavorite'])->name('burger-builder.favorite');
    Route::post('/burger-builder/{burger}/submit', [BurgerBuilderController::class, 'submitForReview'])->name('burger-builder.submit');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-menu-item', [CartController::class, 'addMenuItem'])->name('cart.add-menu-item');
    Route::post('/cart/add-new', [CartController::class, 'addNew'])->name('cart.add-new');
    Route::post('/cart/{burger}/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{burger}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Payment
    Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/create-intent', [PaymentController::class, 'createIntent'])->name('payment.create-intent');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('/orders/{order}/delivery-location', [OrderController::class, 'updateDeliveryLocation'])->name('orders.delivery-location');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/bulk-delete', [OrderController::class, 'bulkDelete'])->name('orders.bulk-delete');

    // Courier (account-based)
    Route::middleware(['auth'])->prefix('courier')->name('courier.')->group(function () {
        Route::get('/dashboard', [CourierController::class, 'dashboard'])->name('dashboard');
        Route::post('/toggle-online', [CourierController::class, 'toggleOnline'])->name('toggle-online');
        Route::get('/orders/{order}', [CourierController::class, 'showOrder'])->name('order.show');
        Route::post('/orders/{order}/accept', [CourierController::class, 'acceptOrder'])->name('order.accept');
        Route::post('/orders/{order}/decline', [CourierController::class, 'declineOrder'])->name('order.decline');
        Route::post('/orders/{order}/delivered', [CourierController::class, 'deliverOrder'])->name('order.delivered');
        Route::post('/orders/{order}/location', [CourierController::class, 'updateOrderLocation'])->name('order.location');
    });

    // Admin
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/bulk-status', [AdminOrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-status');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/start-delivery', [AdminOrderController::class, 'startDelivery'])->name('orders.start-delivery');

        // Burger Review
        Route::get('/burger-review', [App\Http\Controllers\Admin\BurgerReviewController::class, 'index'])->name('burger-review.index');
        Route::post('/burger-review/{burger}/approve', [App\Http\Controllers\Admin\BurgerReviewController::class, 'approve'])->name('burger-review.approve');
        Route::post('/burger-review/{burger}/reject', [App\Http\Controllers\Admin\BurgerReviewController::class, 'reject'])->name('burger-review.reject');

        // Menu - Categories
        Route::get('/menu/categories', [MenuCategoryController::class, 'index'])->name('menu.categories.index');
        Route::get('/menu/categories/create', [MenuCategoryController::class, 'create'])->name('menu.categories.create');
        Route::post('/menu/categories', [MenuCategoryController::class, 'store'])->name('menu.categories.store');
        Route::get('/menu/categories/{category}/edit', [MenuCategoryController::class, 'edit'])->name('menu.categories.edit');
        Route::put('/menu/categories/{category}', [MenuCategoryController::class, 'update'])->name('menu.categories.update');
        Route::delete('/menu/categories/{category}', [MenuCategoryController::class, 'destroy'])->name('menu.categories.destroy');
        Route::post('/menu/categories/update-order', [MenuCategoryController::class, 'updateOrder'])->name('menu.categories.update-order');

        // Menu - Items
        Route::get('/menu/items', [MenuItemController::class, 'index'])->name('menu.items.index');
        Route::get('/menu/items/create', [MenuItemController::class, 'create'])->name('menu.items.create');
        Route::post('/menu/items', [MenuItemController::class, 'store'])->name('menu.items.store');
        Route::get('/menu/items/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.items.edit');
        Route::post('/menu/items/{item}', [MenuItemController::class, 'update'])->name('menu.items.update');
        Route::delete('/menu/items/{item}', [MenuItemController::class, 'destroy'])->name('menu.items.destroy');
        Route::post('/menu/items/update-order', [MenuItemController::class, 'updateOrder'])->name('menu.items.update-order');

        // Ingredients
        Route::get('/ingredients', [App\Http\Controllers\Admin\IngredientController::class, 'index'])->name('ingredients.index');
        Route::get('/ingredients/create', [App\Http\Controllers\Admin\IngredientController::class, 'create'])->name('ingredients.create');
        Route::post('/ingredients', [App\Http\Controllers\Admin\IngredientController::class, 'store'])->name('ingredients.store');
        Route::get('/ingredients/{ingredient}/edit', [App\Http\Controllers\Admin\IngredientController::class, 'edit'])->name('ingredients.edit');
        Route::put('/ingredients/{ingredient}', [App\Http\Controllers\Admin\IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [App\Http\Controllers\Admin\IngredientController::class, 'destroy'])->name('ingredients.destroy');
        Route::post('/ingredients/{ingredient}/toggle', [App\Http\Controllers\Admin\IngredientController::class, 'toggleAvailability'])->name('ingredients.toggle');

        // Reviews
        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Announcements
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::post('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggle'])->name('announcements.toggle');

        // Users
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/toggle-courier', [\App\Http\Controllers\Admin\UserController::class, 'toggleCourier'])->name('users.toggle-courier');

        // Addons
        Route::get('/addons', [AddonItemController::class, 'index'])->name('addons.index');
        Route::post('/addons', [AddonItemController::class, 'store'])->name('addons.store');
        Route::put('/addons/{addonItem}', [AddonItemController::class, 'update'])->name('addons.update');
        Route::delete('/addons/{addonItem}', [AddonItemController::class, 'destroy'])->name('addons.destroy');
        Route::post('/addons/{addonItem}/toggle', [AddonItemController::class, 'toggle'])->name('addons.toggle');
    });
});

require __DIR__.'/settings.php';