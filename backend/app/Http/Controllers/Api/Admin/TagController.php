<?php

namespace App\Http\Controllers\Api\Admin;

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
    public function __construct(protected TagServiceInterface $tagService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Tag::class);

        $tags = $this->tagService->listTags(
            $request->only(['search']),
            $request->integer('per_page', 50)
        );

        return ApiResponse::success(new TagCollection($tags), 'Tags retrieved successfully', [
            'current_page' => $tags->currentPage(),
            'per_page' => $tags->perPage(),
            'total' => $tags->total(),
            'last_page' => $tags->lastPage(),
        ]);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $this->tagService->createTag($request->validated());

        return ApiResponse::success(new TagResource($tag), 'Tag created successfully', [], [], 201);
    }

    public function show(Tag $tag): JsonResponse
    {
        return ApiResponse::success(new TagResource($tag), 'Tag retrieved successfully');
    }

    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $this->authorize('update', $tag);

        $tag = $this->tagService->updateTag($tag, $request->validated());

        return ApiResponse::success(new TagResource($tag), 'Tag updated successfully');
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $this->authorize('delete', $tag);

        $this->tagService->deleteTag($tag);

        return ApiResponse::success(null, 'Tag deleted successfully');
    }
}
