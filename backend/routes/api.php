<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\CreatorController;
use App\Http\Controllers\Api\SearchController;

/*
|--------------------------------------------------------------------------
| GUGE API Routes  —  prefix: /api
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', fn () => response()->json(['status' => 'ok', 'app' => 'GUGE API']));

// Global search
Route::get('/search', SearchController::class);

// Regions
Route::prefix('regions')->group(function () {
    Route::get('/',              [RegionController::class, 'index']);
    Route::get('/{region}',      [RegionController::class, 'show']);
    Route::get('/{region}/products', [RegionController::class, 'products']);
    Route::get('/{region}/stories',  [RegionController::class, 'stories']);
});

// Products
Route::prefix('products')->group(function () {
    Route::get('/',          [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});

// Stories
Route::prefix('stories')->group(function () {
    Route::get('/',        [StoryController::class, 'index']);
    Route::get('/{story}', [StoryController::class, 'show']);
});

// Creators
Route::prefix('creators')->group(function () {
    Route::get('/',           [CreatorController::class, 'index']);
    Route::get('/{creator}',  [CreatorController::class, 'show']);
});

// Contact form
Route::post('/contact', [\App\Http\Controllers\Api\ContactController::class, 'store']);

// Newsletter
Route::post('/newsletter', [\App\Http\Controllers\Api\NewsletterController::class, 'subscribe']);
