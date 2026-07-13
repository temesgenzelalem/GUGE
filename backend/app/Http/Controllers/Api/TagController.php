<?php

namespace App\Http\Controllers\Api;

use App\Domain\Tag\Contracts\TagServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(protected TagServiceInterface $tagService) {}

    /** GET /api/tags */
    public function index(Request $request): JsonResponse
    {
        $tags = $this->tagService->listTags(
            $request->only(['search']),
            $request->integer('per_page', 50)
        );

        return ApiResponse::success(new TagCollection($tags), 'Tags retrieved successfully', [
            'current_page' => $tags->currentPage(),
            'per_page' => $tags->perPage(),
            'total' => $tags->total(),
            'last_page' => $tags->lastPage(),
        ], [
            'next' => $tags->nextPageUrl(),
            'prev' => $tags->previousPageUrl(),
        ]);
    }

    /** GET /api/tags/all — flat list for dropdowns */
    public function all(): JsonResponse
    {
        $tags = $this->tagService->allTags();

        return ApiResponse::success(TagResource::collection($tags), 'All tags retrieved successfully');
    }

    /** GET /api/tags/{tag} */
    public function show(Tag $tag): JsonResponse
    {
        $tag = $this->tagService->getTag($tag);

        return ApiResponse::success(new TagResource($tag), 'Tag retrieved successfully');
    }

    /** POST /api/tags */
    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->tagService->createTag($request->validated());

        return ApiResponse::success(new TagResource($tag), 'Tag created successfully', [], [], 201);
    }

    /** PUT /api/tags/{tag} */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $tag = $this->tagService->updateTag($tag, $request->validated());

        return ApiResponse::success(new TagResource($tag), 'Tag updated successfully');
    }

    /** DELETE /api/tags/{tag} */
    public function destroy(Tag $tag): JsonResponse
    {
        $this->authorize('delete', $tag);

        $this->tagService->deleteTag($tag);

        return ApiResponse::success(null, 'Tag deleted successfully');
    }
}
