<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Creator\Contracts\CreatorServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreatorRequest;
use App\Http\Requests\UpdateCreatorRequest;
use App\Http\Resources\CreatorCollection;
use App\Http\Resources\CreatorResource;
use App\Models\Creator;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreatorController extends Controller
{
    public function __construct(protected CreatorServiceInterface $creatorService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Creator::class);

        $creators = $this->creatorService->listCreators(
            $request->only(['search', 'role', 'region_id', 'status']),
            $request->integer('per_page', 20)
        );

        return ApiResponse::success(new CreatorCollection($creators), 'Creators retrieved successfully', [
            'current_page' => $creators->currentPage(),
            'per_page' => $creators->perPage(),
            'total' => $creators->total(),
            'last_page' => $creators->lastPage(),
        ], [
            'next' => $creators->nextPageUrl(),
            'prev' => $creators->previousPageUrl(),
        ]);
    }

    public function store(StoreCreatorRequest $request): JsonResponse
    {
        $this->authorize('create', Creator::class);

        $creator = $this->creatorService->createCreator($request->validated());

        return ApiResponse::success(new CreatorResource($creator), 'Creator created successfully', [], [], 201);
    }

    public function show(Creator $creator): JsonResponse
    {
        $this->authorize('view', $creator);

        return ApiResponse::success(new CreatorResource($creator), 'Creator retrieved successfully');
    }

    public function update(UpdateCreatorRequest $request, Creator $creator): JsonResponse
    {
        $this->authorize('update', $creator);

        $creator = $this->creatorService->updateCreator($creator, $request->validated());

        return ApiResponse::success(new CreatorResource($creator), 'Creator updated successfully');
    }

    public function destroy(Creator $creator): JsonResponse
    {
        $this->authorize('delete', $creator);

        $this->creatorService->deleteCreator($creator);

        return ApiResponse::success(null, 'Creator deleted successfully');
    }
}
