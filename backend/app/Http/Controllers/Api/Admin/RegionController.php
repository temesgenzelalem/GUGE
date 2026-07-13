<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Region\Contracts\RegionServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Http\Resources\RegionCollection;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(protected RegionServiceInterface $regionService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Region::class);

        $regions = $this->regionService->listRegions(
            $request->only(['direction', 'search', 'status', 'featured']),
            $request->integer('per_page', 20)
        );

        return ApiResponse::success(new RegionCollection($regions), 'Regions retrieved successfully', [
            'current_page' => $regions->currentPage(),
            'per_page' => $regions->perPage(),
            'total' => $regions->total(),
            'last_page' => $regions->lastPage(),
        ], [
            'next' => $regions->nextPageUrl(),
            'prev' => $regions->previousPageUrl(),
        ]);
    }

    public function store(StoreRegionRequest $request): JsonResponse
    {
        $this->authorize('create', Region::class);

        $region = $this->regionService->createRegion($request->validated());

        return ApiResponse::success(new RegionResource($region), 'Region created successfully', [], [], 201);
    }

    public function show(Region $region): JsonResponse
    {
        $this->authorize('view', $region);

        return ApiResponse::success(new RegionResource($region), 'Region retrieved successfully');
    }

    public function update(UpdateRegionRequest $request, Region $region): JsonResponse
    {
        $this->authorize('update', $region);

        $region = $this->regionService->updateRegion($region, $request->validated());

        return ApiResponse::success(new RegionResource($region), 'Region updated successfully');
    }

    public function destroy(Region $region): JsonResponse
    {
        $this->authorize('delete', $region);

        $this->regionService->deleteRegion($region);

        return ApiResponse::success(null, 'Region deleted successfully');
    }
}
