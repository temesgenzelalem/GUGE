<?php

namespace App\Http\Controllers\Api;

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
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
        $this->authorizeResource(Creator::class, 'creator', ['except' => ['index', 'show']]);
    }

    /** GET /api/creators */
    public function index(Request $request): JsonResponse
    {
        $creators = $this->creatorService->listCreators($request->only(['search', 'role', 'region_id', 'specialties', 'languages', 'status']), $request->integer('per_page', 20));

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

    /** POST /api/creators */
    public function store(StoreCreatorRequest $request): JsonResponse
    {
        $creator = $this->creatorService->createCreator($request->validated());

        return ApiResponse::success(new CreatorResource($creator), 'Creator created successfully', [], [], 201);
    }

    /** GET /api/creators/{slug} */
    public function show(Creator $creator): JsonResponse
    {
        $creator = $this->creatorService->getCreator($creator);

        return ApiResponse::success(new CreatorResource($creator), 'Creator retrieved successfully');
    }

    /** PUT /api/creators/{slug} */
    public function update(UpdateCreatorRequest $request, Creator $creator): JsonResponse
    {
        $creator = $this->creatorService->updateCreator($creator, $request->validated());

        return ApiResponse::success(new CreatorResource($creator), 'Creator updated successfully');
    }

    /** DELETE /api/creators/{slug} */
    public function destroy(Creator $creator): JsonResponse
    {
        $this->creatorService->deleteCreator($creator);

        return ApiResponse::success(null, 'Creator deleted successfully');
    }
}
