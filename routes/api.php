<?php

use App\Http\Controllers\Api\AttractionController;
use App\Http\Controllers\Api\CivilizationController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ====================
    // Public Routes
    // ====================

    Route::get('/attractions', [AttractionController::class, 'index']);
    Route::get('/attractions/{attraction}', [AttractionController::class, 'show']);

    Route::get('/civilizations', [CivilizationController::class, 'index']);
    Route::get('/civilizations/{civilization}', [CivilizationController::class, 'show']);
    Route::get('/civilizations/{civilization}/attractions', [CivilizationController::class, 'attractions']);

    Route::get('/regions', [RegionController::class, 'index']);
    Route::get('/regions/{region}', [RegionController::class, 'show']);
    Route::get('/regions/{region}/attractions', [RegionController::class, 'attractions']);

    Route::get('/attractions/{attraction}/reviews', [ReviewController::class, 'index']);

    // ====================
    // Protected Routes
    // ====================

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/reviews', [ReviewController::class, 'store']);

        Route::prefix('favorites')->group(function () {
            Route::get('/', [FavoriteController::class, 'index']);
            Route::post('/', [FavoriteController::class, 'store']);
            Route::delete('/{id}', [FavoriteController::class, 'destroy']);
        });

    });

});