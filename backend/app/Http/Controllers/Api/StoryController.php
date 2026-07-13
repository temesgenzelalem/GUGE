<?php

namespace App\Http\Controllers\Api;

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
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
        $this->authorizeResource(Story::class, 'story', ['except' => ['index', 'show', 'incrementView']]);
    }

    public function index(Request $request): JsonResponse
    {
        $stories = $this->storyService->listStories(
            $request->only(['category', 'type', 'region_id', 'creator_id', 'status', 'search']),
            $request->integer('per_page', 12)
        );

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
        $story = $this->storyService->createStory($request->validated());

        return ApiResponse::success(new StoryResource($story), 'Story created successfully', [], [], 201);
    }

    public function show(Story $story): JsonResponse
    {
        $this->authorize('view', $story);

        $story = $this->storyService->getStory($story);
        $related = $this->storyService->getRelatedStories($story, 3);

        return ApiResponse::success([
            'data' => new StoryResource($story),
            'related' => StoryResource::collection($related),
        ], 'Story retrieved successfully');
    }

    public function update(UpdateStoryRequest $request, Story $story): JsonResponse
    {
        $story = $this->storyService->updateStory($story, $request->validated());

        return ApiResponse::success(new StoryResource($story), 'Story updated successfully');
    }

    public function destroy(Story $story): JsonResponse
    {
        $this->storyService->deleteStory($story);

        return ApiResponse::success(null, 'Story deleted successfully');
    }

    public function incrementView(Story $story): JsonResponse
    {
        $this->storyService->incrementViewCount($story);

        return ApiResponse::success(
            ['view_count' => $story->fresh()->view_count],
            'View count updated'
        );
    }
}
