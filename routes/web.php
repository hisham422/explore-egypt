<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\ReviewController as ApiReviewController;
use App\Http\Controllers\Admin\AttractionController as AdminAttractionController;
use App\Http\Controllers\Admin\AppearanceController as AdminAppearanceController;
use App\Http\Controllers\Admin\CivilizationController as AdminCivilizationController;
use App\Http\Controllers\Admin\CivilizationPeriodController as AdminCivilizationPeriodController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RegionController as AdminRegionController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\TourismController;
use Illuminate\Support\Facades\Route;

// ====================
// Public Pages
// ====================

Route::get('/', [TourismController::class, 'home'])->name('home');

Route::redirect('/discover', '/explore');

Route::get('/explore', [TourismController::class, 'explore'])->name('explore');

Route::get('/civilizations', [TourismController::class, 'civilizations'])->name('civilizations.index');
Route::get('/civilizations/{civilization}', [TourismController::class, 'civilization'])->name('civilizations.show');

Route::get('/regions', [TourismController::class, 'regions'])->name('regions.index');
Route::get('/regions/{region}', [TourismController::class, 'region'])->name('regions.show');

Route::get('/attractions/{attraction}', [TourismController::class, 'attraction'])->name('attractions.show');


// ====================
// Authenticated Routes
// ====================

Route::middleware('auth')->group(function () {

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::post('/attractions/{attraction}/toggle-favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // Reviews
    Route::post('/reviews', [ApiReviewController::class, 'store'])->name('reviews.store');

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

});

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/appearance', [AdminAppearanceController::class, 'edit'])->name('appearance.edit');
        Route::put('/appearance', [AdminAppearanceController::class, 'update'])->name('appearance.update');

        Route::resource('civilizations', AdminCivilizationController::class)->except(['show']);
        Route::resource('civilization-periods', AdminCivilizationPeriodController::class)
            ->parameters(['civilization-periods' => 'period'])
            ->except(['show']);
        
        // Period Attractions Management
        Route::get('periods/{period}/attractions', [AdminCivilizationPeriodController::class, 'attractions'])
            ->name('period-attractions.index');
        Route::post('periods/{period}/attractions', [AdminCivilizationPeriodController::class, 'attachAttraction'])
            ->name('period-attractions.store');
        Route::delete('periods/{period}/attractions/{attraction}', [AdminCivilizationPeriodController::class, 'detachAttraction'])
            ->name('period-attractions.destroy');
        
        Route::resource('regions', AdminRegionController::class)->except(['show']);
        Route::post('attractions/{attraction}/images/reorder', [AdminAttractionController::class, 'reorderImages'])
            ->name('attractions.images.reorder');
        Route::post('attractions/{attraction}/images/{attractionImage}/move', [AdminAttractionController::class, 'moveImage'])
            ->name('attractions.images.move');
        Route::delete('attractions/{attraction}/images/{attractionImage}', [AdminAttractionController::class, 'destroyImage'])
            ->name('attractions.images.destroy');
        Route::delete('attractions/{attraction}/main-image', [AdminAttractionController::class, 'destroyMainImage'])
            ->name('attractions.main-image.destroy');
        Route::resource('attractions', AdminAttractionController::class)->except(['show']);
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::resource('reviews', AdminReviewController::class)->except(['show']);
    });

// Dashboard redirect
Route::get('/dashboard', function () {
    $user = request()->user();

    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/auth.php';