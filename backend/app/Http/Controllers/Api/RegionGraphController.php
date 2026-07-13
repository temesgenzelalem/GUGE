<?php

namespace App\Http\Controllers\Api;

use App\Domain\Graph\Contracts\RegionGraphServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionGraphController extends Controller
{
    public function __construct(protected RegionGraphServiceInterface $graphService) {}

    public function related(Region $region, Request $request): JsonResponse
    {
        $types = $request->input('types', []);

        return ApiResponse::success(
            $this->graphService->relatedNodes($region->id, (array) $types),
            'Related nodes retrieved successfully'
        );
    }

    public function connections(Region $region, Request $request): JsonResponse
    {
        $depth = $request->integer('depth', 2);

        return ApiResponse::success(
            $this->graphService->connections($region->id, $depth),
            'Region connections retrieved successfully'
        );
    }

    public function store(Region $region, Request $request): JsonResponse
    {
        $payload = $request->validate([
            'target_type' => 'required|string|max:80',
            'target_id' => 'required|integer',
            'target_name' => 'required|string|max:200',
            'weight' => 'nullable|numeric',
            'metadata' => 'nullable|array',
        ]);

        $payload['source_region_id'] = $region->id;

        $this->graphService->saveRelationship($payload);

        return ApiResponse::success(null, 'Region relationship stored successfully');
    }
}
