<?php

namespace App\Domain\Media;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadService
{
    private const ALLOWED_IMAGES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];

    private const ALLOWED_VIDEOS = ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/webm'];

    private const ALLOWED_DOCUMENTS = ['application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain'];

    private const MAX_IMAGE_SIZE = 10 * 1024 * 1024;  // 10 MB

    private const MAX_VIDEO_SIZE = 200 * 1024 * 1024; // 200 MB

    private const MAX_DOCUMENT_SIZE = 20 * 1024 * 1024;  // 20 MB

    public function __construct(
        protected MediaRepository $repository,
        protected string $disk = 'public'
    ) {}

    public function upload(UploadedFile $file, ?User $uploader = null, string $collection = 'default'): Media
    {
        $this->validateFile($file);

        $uuid = (string) Str::uuid();
        $ext = $file->getClientOriginalExtension();
        $safeName = $uuid.'.'.$ext;
        $folder = $this->folderForMime($file->getMimeType());
        $path = $file->storeAs($folder, $safeName, $this->disk);

        $url = Storage::disk($this->disk)->url($path);

        return $this->repository->create([
            'uuid' => $uuid,
            'filename' => $safeName,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => $this->disk,
            'url' => $url,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'collection' => $collection,
            'gallery' => false,
            'uploaded_by' => $uploader?->id,
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
                'extension' => $ext,
            ],
        ]);
    }

    public function delete(Media $media): bool
    {
        if (Storage::disk($media->disk ?? $this->disk)->exists($media->path)) {
            Storage::disk($media->disk ?? $this->disk)->delete($media->path);
        }

        return $this->repository->delete($media);
    }

    public function replace(Media $media, UploadedFile $file): Media
    {
        // Delete old file from storage
        if ($media->path && Storage::disk($media->disk ?? $this->disk)->exists($media->path)) {
            Storage::disk($media->disk ?? $this->disk)->delete($media->path);
        }

        $this->validateFile($file);

        $ext = $file->getClientOriginalExtension();
        $safeName = $media->uuid.'.'.$ext;
        $folder = $this->folderForMime($file->getMimeType());
        $path = $file->storeAs($folder, $safeName, $this->disk);
        $url = Storage::disk($this->disk)->url($path);

        return $this->repository->update($media, [
            'filename' => $safeName,
            'original_filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => $this->disk,
            'url' => $url,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
                'extension' => $ext,
            ],
        ]);
    }

    public function publicUrl(Media $media): string
    {
        if ($media->url) {
            return $media->url;
        }

        return Storage::disk($media->disk ?? $this->disk)->url($media->path);
    }

    private function validateFile(UploadedFile $file): void
    {
        $mime = $file->getMimeType();
        $size = $file->getSize();

        $allowed = array_merge(self::ALLOWED_IMAGES, self::ALLOWED_VIDEOS, self::ALLOWED_DOCUMENTS);

        if (! in_array($mime, $allowed)) {
            throw new \InvalidArgumentException("File type '{$mime}' is not allowed.");
        }

        $maxSize = match (true) {
            in_array($mime, self::ALLOWED_IMAGES) => self::MAX_IMAGE_SIZE,
            in_array($mime, self::ALLOWED_VIDEOS) => self::MAX_VIDEO_SIZE,
            in_array($mime, self::ALLOWED_DOCUMENTS) => self::MAX_DOCUMENT_SIZE,
            default => self::MAX_IMAGE_SIZE,
        };

        if ($size > $maxSize) {
            $mb = round($maxSize / 1024 / 1024);
            throw new \InvalidArgumentException("File exceeds the maximum allowed size of {$mb} MB.");
        }
    }

    private function folderForMime(string $mime): string
    {
        return match (true) {
            in_array($mime, self::ALLOWED_IMAGES) => 'media/images',
            in_array($mime, self::ALLOWED_VIDEOS) => 'media/videos',
            in_array($mime, self::ALLOWED_DOCUMENTS) => 'media/documents',
            default => 'media/other',
        };
    }
}
