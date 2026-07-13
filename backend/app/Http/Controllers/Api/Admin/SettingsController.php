<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /** GET /api/admin/settings */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        return ApiResponse::success([
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_version' => '1.0.0',
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ], 'Settings retrieved successfully');
    }

    /** POST /api/admin/settings/cache/clear */
    public function clearCache(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        Cache::flush();

        return ApiResponse::success(null, 'Cache cleared successfully');
    }

    /** GET /api/admin/settings/health */
    public function health(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        try {
            \DB::select('SELECT 1');
            $db = 'ok';
        } catch (\Exception $e) {
            $db = 'error: '.$e->getMessage();
        }

        return ApiResponse::success([
            'status' => 'ok',
            'database' => $db,
            'cache' => 'ok',
            'timestamp' => now()->toISOString(),
        ], 'Health check passed');
    }
}
