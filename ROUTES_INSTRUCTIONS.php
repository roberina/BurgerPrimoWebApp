<?php
/**
 * ADDON ITEMS ROUTES — add these to your existing routes/web.php
 *
 * 1. Add this PUBLIC route (before the auth middleware group):
 *
 *    Route::get('/api/addons', [App\Http\Controllers\Admin\AddonItemController::class, 'publicIndex']);
 *
 * 2. Add these ADMIN routes inside the admin middleware group:
 *
 *    // Addon Items
 *    Route::get('/addons', [App\Http\Controllers\Admin\AddonItemController::class, 'index'])->name('addons.index');
 *    Route::post('/addons', [App\Http\Controllers\Admin\AddonItemController::class, 'store'])->name('addons.store');
 *    Route::put('/addons/{addonItem}', [App\Http\Controllers\Admin\AddonItemController::class, 'update'])->name('addons.update');
 *    Route::delete('/addons/{addonItem}', [App\Http\Controllers\Admin\AddonItemController::class, 'destroy'])->name('addons.destroy');
 *    Route::post('/addons/{addonItem}/toggle', [App\Http\Controllers\Admin\AddonItemController::class, 'toggle'])->name('addons.toggle');
 *
 * 3. Add these REVIEW routes inside the admin middleware group:
 *
 *    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
 *    Route::post('/reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
 *    Route::post('/reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
 *    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
 *
 * 4. Add these ANNOUNCEMENT routes inside the admin middleware group:
 *
 *    Route::get('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('announcements.index');
 *    Route::post('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('announcements.store');
 *    Route::put('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->name('announcements.update');
 *    Route::delete('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
 *    Route::post('/announcements/{announcement}/toggle', [App\Http\Controllers\Admin\AnnouncementController::class, 'toggle'])->name('announcements.toggle');
 *
 * 5. Add these PUBLIC routes for new pages:
 *
 *    Route::get('/kontakt', function() {
 *        $announcements = \App\Models\Announcement::where('is_active', true)->ordered()->get();
 *        return Inertia::render('Kontakt/Index', ['announcements' => $announcements]);
 *    })->name('kontakt');
 *
 *    Route::get('/meelelahutus', function() {
 *        $announcements = \App\Models\Announcement::where('is_active', true)->ordered()->get();
 *        return Inertia::render('Meelelahutus/Index', ['announcements' => $announcements]);
 *    })->name('meelelahutus');
 *
 *    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
 *
 * 6. Update the home route to pass reviews and announcements:
 *
 *    Route::get('/', function () {
 *        return Inertia::render('Welcome', [
 *            'featuredItems' => \App\Models\MenuItem::with('category')->where('is_featured', true)->where('is_active', true)->get(),
 *            'reviews' => \App\Models\Review::where('status', 'approved')->latest()->take(10)->get(),
 *            'announcements' => \App\Models\Announcement::where('is_active', true)->ordered()->get(),
 *        ]);
 *    })->name('home');
 *
 * 7. Run migrations:
 *    php artisan migrate
 *
 * 8. Seed default addon items:
 *    php artisan db:seed --class=AddonItemSeeder
 */
