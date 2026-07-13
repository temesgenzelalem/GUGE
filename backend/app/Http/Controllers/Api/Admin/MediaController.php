<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Media\Contracts\MediaServiceInterface;
use App\Domain\Media\MediaUploadService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaCollection;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(
        protected MediaServiceInterface $mediaService,
        protected MediaUploadService $uploadService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Media::class);

        $media = $this->mediaService->listMedia(
            $request->only(['search', 'gallery', 'collection', 'mime_type']),
            $request->integer('per_page', 20)
        );

        return ApiResponse::success(new MediaCollection($media), 'Media retrieved successfully', [
            'current_page' => $media->currentPage(),
            'per_page' => $media->perPage(),
            'total' => $media->total(),
            'last_page' => $media->lastPage(),
        ], [
            'next' => $media->nextPageUrl(),
            'prev' => $media->previousPageUrl(),
        ]);
    }

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $this->authorize('create', Media::class);

        if ($request->hasFile('file')) {
            // Real file upload
            $media = $this->uploadService->upload(
                $request->file('file'),
                $request->user(),
                $request->input('collection', 'default')
            );

            if ($request->boolean('gallery')) {
                $media->update(['gallery' => true]);
            }
        } else {
            // Register metadata manually (for external CDN URLs)
            $media = $this->mediaService->uploadMedia($request->validated());
        }

        return ApiResponse::success(new MediaResource($media), 'Media uploaded successfully', [], [], 201);
    }

    public function show(Media $media): JsonResponse
    {
        $this->authorize('view', $media);

        return ApiResponse::success(new MediaResource($media), 'Media retrieved successfully');
    }

    public function update(UpdateMediaRequest $request, Media $media): JsonResponse
    {
        $this->authorize('update', $media);

        if ($request->hasFile('file')) {
            $media = $this->uploadService->replace($media, $request->file('file'));
        } else {
            $media = $this->mediaService->updateMedia($media, $request->validated());
        }

        return ApiResponse::success(new MediaResource($media), 'Media updated successfully');
    }

    public function destroy(Media $media): JsonResponse
    {
        $this->authorize('delete', $media);

        // Delete physical file too
        $this->uploadService->delete($media);

        return ApiResponse::success(null, 'Media deleted successfully');
    }
}
