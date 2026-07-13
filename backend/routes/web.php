<?php

use Illuminate\Support\Facades\Route;

// Simple health / welcome for web
Route::get('/', function () {
    return response()->json([
        'app' => 'GUGE API',
        'version' => '1.0.0',
        'status' => 'running',
        'docs' => url('/api/health'),
    ]);
});
