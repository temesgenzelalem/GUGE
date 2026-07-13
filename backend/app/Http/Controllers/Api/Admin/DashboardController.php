<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Dashboard\Contracts\DashboardServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(protected DashboardServiceInterface $dashboardService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        return ApiResponse::success(
            $this->dashboardService->getMetrics()->toArray(),
            'Admin dashboard loaded successfully'
        );
    }
}
