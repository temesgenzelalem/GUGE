<?php

namespace Tests\Unit;

use App\Domain\Media\Contracts\MediaRepositoryInterface;
use App\Domain\Media\MediaService;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class MediaServiceTest extends TestCase
{
    private MediaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MediaService($this->app->make(MediaRepositoryInterface::class));
    }

    public function test_upload_media_persists_to_database(): void
    {
        $user = User::factory()->create();
        $uuid = (string) Str::uuid();

        $media = $this->service->uploadMedia([
            'uuid' => $uuid,
            'filename' => 'test.jpg',
            'path' => 'uploads/test.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 204800,
            'gallery' => false,
            'uploaded_by' => $user->id,
        ]);

        $this->assertInstanceOf(Media::class, $media);
        $this->assertDatabaseHas('media', ['uuid' => $uuid]);
    }

    public function test_update_media_changes_gallery_flag(): void
    {
        $media = Media::factory()->create(['gallery' => false]);

        $updated = $this->service->updateMedia($media, ['gallery' => true]);

        $this->assertTrue($updated->fresh()->gallery);
    }

    public function test_delete_media_removes_record(): void
    {
        $media = Media::factory()->create();
        $id = $media->id;

        $this->service->deleteMedia($media);

        $this->assertDatabaseMissing('media', ['id' => $id]);
    }

    public function test_list_media_returns_paginator(): void
    {
        Media::factory()->count(3)->create();

        $result = $this->service->listMedia([], 20);

        $this->assertEquals(3, $result->total());
    }

    public function test_list_media_filters_by_gallery(): void
    {
        Media::factory()->count(2)->create(['gallery' => true]);
        Media::factory()->count(3)->create(['gallery' => false]);

        $result = $this->service->listMedia(['gallery' => true], 20);

        $this->assertEquals(2, $result->total());
    }
}
