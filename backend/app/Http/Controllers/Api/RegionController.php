<?php

namespace App\Http\Controllers\Api;

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
    public function __construct(protected RegionServiceInterface $regionService) {}

    /** GET /api/regions */
    public function index(Request $request): JsonResponse
    {
        $regions = $this->regionService->listRegions($request->only(['direction', 'search']), $request->integer('per_page', 20));

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

    /** GET /api/regions/{slug} */
    public function show(Region $region): JsonResponse
    {
        $region = $this->regionService->getRegion($region);

        return ApiResponse::success(new RegionResource($region), 'Region retrieved successfully');
    }

    /** GET /api/regions/{slug}/products */
    public function products(Region $region): JsonResponse
    {
        $products = $this->regionService->getRegionProducts($region);

        return ApiResponse::success($products, 'Region products retrieved successfully');
    }

    /** GET /api/regions/{slug}/stories */
    public function stories(Region $region): JsonResponse
    {
        $stories = $this->regionService->getRegionStories($region);

        return ApiResponse::success($stories, 'Region stories retrieved successfully');
    }

    /** GET /api/regions/{slug}/creators */
    public function creators(Region $region): JsonResponse
    {
        $creators = $this->regionService->getRegionCreators($region);

        return ApiResponse::success($creators, 'Region creators retrieved successfully');
    }

    /** POST /api/regions */
    public function store(StoreRegionRequest $request): JsonResponse
    {
        $region = $this->regionService->createRegion($request->validated());

        return ApiResponse::success(new RegionResource($region), 'Region created successfully', [], [], 201);
    }

    /** PUT /api/regions/{slug} */
    public function update(UpdateRegionRequest $request, Region $region): JsonResponse
    {
        $region = $this->regionService->updateRegion($region, $request->validated());

        return ApiResponse::success(new RegionResource($region), 'Region updated successfully');
    }

    /** DELETE /api/regions/{slug} */
    public function destroy(Region $region): JsonResponse
    {
        $this->regionService->deleteRegion($region);

        return ApiResponse::success(null, 'Region deleted successfully');
    }
}
