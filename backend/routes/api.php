<?php

use App\Http\Controllers\Api\Admin\AuditController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\MediaController;
use App\Http\Controllers\Api\Admin\SettingsController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CreatorController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\RegionGraphController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GUGE API Routes  —  prefix: /api
|--------------------------------------------------------------------------
*/

Route::get('/health', fn () => response()->json(['status' => 'ok', 'app' => 'GUGE API']));

// ── Search ──────────────────────────────────────────────────────────────
Route::get('/search', SearchController::class);

// ── Tags — public reads ─────────────────────────────────────────────────
Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index']);
    Route::get('/all', [TagController::class, 'all']);
    Route::get('/{tag}', [TagController::class, 'show']);
});
Route::prefix('tags')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [TagController::class, 'store']);
    Route::put('/{tag}', [TagController::class, 'update']);
    Route::delete('/{tag}', [TagController::class, 'destroy']);
});

// ── Categories — public reads ───────────────────────────────────────────
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);
});

// ── Regions ─────────────────────────────────────────────────────────────
Route::prefix('regions')->group(function () {
    Route::get('/', [RegionController::class, 'index']);
    Route::get('/{region}', [RegionController::class, 'show']);
    Route::get('/{region}/products', [RegionController::class, 'products']);
    Route::get('/{region}/stories', [RegionController::class, 'stories']);
    Route::get('/{region}/creators', [RegionController::class, 'creators']);
    Route::get('/{region}/graph/related', [RegionGraphController::class, 'related']);
    Route::get('/{region}/graph/connections', [RegionGraphController::class, 'connections']);
});
Route::prefix('regions')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [RegionController::class, 'store']);
    Route::put('/{region}', [RegionController::class, 'update']);
    Route::delete('/{region}', [RegionController::class, 'destroy']);
    Route::post('/{region}/graph/relationships', [RegionGraphController::class, 'store']);
});

// ── Products ─────────────────────────────────────────────────────────────
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});
Route::prefix('products')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [ProductController::class, 'store']);
    Route::put('/{product}', [ProductController::class, 'update']);
    Route::delete('/{product}', [ProductController::class, 'destroy']);
});

// ── Stories ──────────────────────────────────────────────────────────────
Route::prefix('stories')->group(function () {
    Route::get('/', [StoryController::class, 'index']);
    Route::get('/{story}', [StoryController::class, 'show']);
    Route::post('/{story}/view', [StoryController::class, 'incrementView']);
});
Route::prefix('stories')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [StoryController::class, 'store']);
    Route::put('/{story}', [StoryController::class, 'update']);
    Route::delete('/{story}', [StoryController::class, 'destroy']);
});

// ── Creators ─────────────────────────────────────────────────────────────
Route::prefix('creators')->group(function () {
    Route::get('/', [CreatorController::class, 'index']);
    Route::get('/{creator}', [CreatorController::class, 'show']);
});
Route::prefix('creators')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [CreatorController::class, 'store']);
    Route::put('/{creator}', [CreatorController::class, 'update']);
    Route::delete('/{creator}', [CreatorController::class, 'destroy']);
});

// ── Authentication ────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::patch('/profile', [AuthController::class, 'updateProfile']);
    Route::patch('/password', [AuthController::class, 'updatePassword']);
    Route::delete('/tokens/{id}', [AuthController::class, 'deleteToken']);
    Route::post('/email/verification-notification', [AuthController::class, 'sendEmailVerificationNotification']);
});

// ── Contact & Newsletter ──────────────────────────────────────────────────
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/newsletter', [NewsletterController::class, 'subscribe']);

// ── Admin ─────────────────────────────────────────────────────────────────
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Settings & health
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings/cache/clear', [SettingsController::class, 'clearCache']);
    Route::get('/settings/health', [SettingsController::class, 'health']);

    // Users
    Route::apiResource('users', UserController::class)->except(['create', 'edit']);

    // Regions
    Route::apiResource('regions', App\Http\Controllers\Api\Admin\RegionController::class)->except(['create', 'edit']);

    // Products
    Route::apiResource('products', App\Http\Controllers\Api\Admin\ProductController::class)->except(['create', 'edit']);

    // Stories
    Route::apiResource('stories', App\Http\Controllers\Api\Admin\StoryController::class)->except(['create', 'edit']);
    Route::post('stories/{story}/publish', [App\Http\Controllers\Api\Admin\StoryController::class, 'publish']);
    Route::post('stories/{story}/unpublish', [App\Http\Controllers\Api\Admin\StoryController::class, 'unpublish']);

    // Creators
    Route::apiResource('creators', App\Http\Controllers\Api\Admin\CreatorController::class)->except(['create', 'edit']);

    // Categories
    Route::apiResource('categories', CategoryController::class)->except(['create', 'edit']);

    // Tags
    Route::apiResource('tags', App\Http\Controllers\Api\Admin\TagController::class)->except(['create', 'edit']);

    // Media
    Route::apiResource('media', MediaController::class)->except(['create', 'edit']);

    // Audit logs
    Route::get('audit-logs', [AuditController::class, 'index']);
    Route::get('audit-logs/{id}', [AuditController::class, 'show']);
});
