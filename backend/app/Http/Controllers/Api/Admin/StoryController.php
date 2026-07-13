<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Story\Contracts\StoryServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoryRequest;
use App\Http\Requests\UpdateStoryRequest;
use App\Http\Resources\StoryCollection;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function __construct(protected StoryServiceInterface $storyService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Story::class);

        // Admin sees all statuses by default
        $filters = $request->only(['category', 'type', 'region_id', 'creator_id', 'status', 'search', 'featured']);

        $stories = $this->storyService->listStories($filters, $request->integer('per_page', 20));

        return ApiResponse::success(new StoryCollection($stories), 'Stories retrieved successfully', [
            'current_page' => $stories->currentPage(),
            'per_page' => $stories->perPage(),
            'total' => $stories->total(),
            'last_page' => $stories->lastPage(),
        ], [
            'next' => $stories->nextPageUrl(),
            'prev' => $stories->previousPageUrl(),
        ]);
    }

    public function store(StoreStoryRequest $request): JsonResponse
    {
        $this->authorize('create', Story::class);

        $story = $this->storyService->createStory($request->validated());

        return ApiResponse::success(new StoryResource($story), 'Story created successfully', [], [], 201);
    }

    public function show(Story $story): JsonResponse
    {
        $this->authorize('view', $story);

        return ApiResponse::success(new StoryResource($story), 'Story retrieved successfully');
    }

    public function update(UpdateStoryRequest $request, Story $story): JsonResponse
    {
        $this->authorize('update', $story);

        $story = $this->storyService->updateStory($story, $request->validated());

        return ApiResponse::success(new StoryResource($story), 'Story updated successfully');
    }

    public function destroy(Story $story): JsonResponse
    {
        $this->authorize('delete', $story);

        $this->storyService->deleteStory($story);

        return ApiResponse::success(null, 'Story deleted successfully');
    }

    public function publish(Story $story): JsonResponse
    {
        $this->authorize('update', $story);

        $story = $this->storyService->updateStory($story, [
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
        ]);

        return ApiResponse::success(new StoryResource($story), 'Story published successfully');
    }

    public function unpublish(Story $story): JsonResponse
    {
        $this->authorize('update', $story);

        $story = $this->storyService->updateStory($story, ['status' => 'draft']);

        return ApiResponse::success(new StoryResource($story), 'Story unpublished successfully');
    }
}
