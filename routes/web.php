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
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home / Welcome Route (from both files merged)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// PUBLIC MENU ROUTE (No authentication required - anyone can view)
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [MenuController::class, 'category'])->name('menu.category');

// Authentication routes already included by Breeze/Jetstream

Route::middleware(['auth'])->group(function () {

    // MENU FAVORITES
    Route::post('/menu/{menuItem}/favorite', [App\Http\Controllers\MenuFavoriteController::class, 'toggle'])->name('menu.favorite.toggle');

    // Burger Builder Routes
    Route::get('/burger-builder', [BurgerBuilderController::class, 'index'])->name('burger-builder.index');
    Route::post('/burger-builder', [BurgerBuilderController::class, 'store'])->name('burger-builder.store');
    Route::put('/burger-builder/{burger}', [BurgerBuilderController::class, 'update'])->name('burger-builder.update');
    Route::delete('/burger-builder/{burger}', [BurgerBuilderController::class, 'destroy'])->name('burger-builder.destroy');
    Route::post('/burger-builder/{burger}/favorite', [BurgerBuilderController::class, 'toggleFavorite'])->name('burger-builder.favorite');
    Route::post('/burger-builder/{burger}/favorite', [BurgerBuilderController::class, 'toggleFavorite'])->name('burger-builder.favorite');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-menu-item', [CartController::class, 'addMenuItem'])->name('cart.add-menu-item');
    Route::post('/cart/add-new', [CartController::class, 'addNew'])->name('cart.add-new');
    Route::post('/cart/{burger}/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{burger}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Payment Routes
    Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/create-intent', [PaymentController::class, 'createIntent'])->name('payment.create-intent');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/bulk-delete', [OrderController::class, 'bulkDelete'])->name('orders.bulk-delete'); // Bulk delete completed orders

    // ADMIN ROUTES
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/confirm', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/bulk-status', [AdminOrderController::class, 'bulkUpdateStatus'])->name('orders.bulk-status');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
    
    // MENU MANAGEMENT - Categories
    Route::get('/menu/categories', [MenuCategoryController::class, 'index'])->name('menu.categories.index');
    Route::get('/menu/categories/create', [MenuCategoryController::class, 'create'])->name('menu.categories.create');
    Route::post('/menu/categories', [MenuCategoryController::class, 'store'])->name('menu.categories.store');
    Route::get('/menu/categories/{category}/edit', [MenuCategoryController::class, 'edit'])->name('menu.categories.edit');
    Route::put('/menu/categories/{category}', [MenuCategoryController::class, 'update'])->name('menu.categories.update');
    Route::delete('/menu/categories/{category}', [MenuCategoryController::class, 'destroy'])->name('menu.categories.destroy');
    Route::post('/menu/categories/update-order', [MenuCategoryController::class, 'updateOrder'])->name('menu.categories.update-order');
    
    // MENU MANAGEMENT - Items
    Route::get('/menu/items', [MenuItemController::class, 'index'])->name('menu.items.index');
    Route::get('/menu/items/create', [MenuItemController::class, 'create'])->name('menu.items.create');
    Route::post('/menu/items', [MenuItemController::class, 'store'])->name('menu.items.store');
    Route::get('/menu/items/{item}/edit', [MenuItemController::class, 'edit'])->name('menu.items.edit');
    Route::post('/menu/items/{item}', [MenuItemController::class, 'update'])->name('menu.items.update');
    Route::delete('/menu/items/{item}', [MenuItemController::class, 'destroy'])->name('menu.items.destroy');
    Route::post('/menu/items/update-order', [MenuItemController::class, 'updateOrder'])->name('menu.items.update-order');

    // BURGER INGREDIENTS MANAGEMENT
    Route::get('/ingredients', [App\Http\Controllers\Admin\IngredientController::class, 'index'])->name('ingredients.index');
    Route::get('/ingredients/create', [App\Http\Controllers\Admin\IngredientController::class, 'create'])->name('ingredients.create');
    Route::post('/ingredients', [App\Http\Controllers\Admin\IngredientController::class, 'store'])->name('ingredients.store');
    Route::get('/ingredients/{ingredient}/edit', [App\Http\Controllers\Admin\IngredientController::class, 'edit'])->name('ingredients.edit');
    Route::put('/ingredients/{ingredient}', [App\Http\Controllers\Admin\IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/ingredients/{ingredient}', [App\Http\Controllers\Admin\IngredientController::class, 'destroy'])->name('ingredients.destroy');
    Route::post('/ingredients/{ingredient}/toggle', [App\Http\Controllers\Admin\IngredientController::class, 'toggleAvailability'])->name('ingredients.toggle');
});
});

// Additional route includes
require __DIR__.'/settings.php';