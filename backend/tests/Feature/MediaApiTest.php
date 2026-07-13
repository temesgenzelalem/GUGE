<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    private User $admin;

    private Media $media;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->media = Media::factory()->create(['uploaded_by' => $this->admin->id]);
    }

    public function test_admin_can_list_media(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/admin/media');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success', 'message', 'data', 'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_admin_can_view_single_media(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/media/{$this->media->uuid}");

        $response->assertOk()
            ->assertJsonPath('data.uuid', $this->media->uuid)
            ->assertJsonPath('data.filename', $this->media->filename);
    }

    public function test_admin_can_create_media_record(): void
    {
        Sanctum::actingAs($this->admin);

        $uuid = (string) Str::uuid();

        $response = $this->postJson('/api/admin/media', [
            'uuid' => $uuid,
            'filename' => 'test-image.jpg',
            'path' => 'uploads/test-image.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 204800,
            'gallery' => false,
            'uploaded_by' => $this->admin->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.filename', 'test-image.jpg')
            ->assertJsonPath('data.uuid', $uuid);

        $this->assertDatabaseHas('media', ['uuid' => $uuid]);
    }

    public function test_media_creation_requires_uuid(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/admin/media', [
            'filename' => 'test.jpg',
            'path' => 'uploads/test.jpg',
            'size' => 100,
        ])->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['uuid']]);
    }

    public function test_media_uuid_must_be_unique(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/admin/media', [
            'uuid' => $this->media->uuid,
            'filename' => 'duplicate.jpg',
            'path' => 'uploads/duplicate.jpg',
            'size' => 100,
        ])->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['uuid']]);
    }

    public function test_admin_can_update_media(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/admin/media/{$this->media->uuid}", [
            'gallery' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.gallery', true);
    }

    public function test_admin_can_delete_media(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/media/{$this->media->uuid}");

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('media', ['id' => $this->media->id]);
    }

    public function test_guest_cannot_access_media(): void
    {
        $this->getJson('/api/admin/media')->assertUnauthorized();
    }

    public function test_non_admin_cannot_delete_media(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->deleteJson("/api/admin/media/{$this->media->uuid}")
            ->assertForbidden();
    }
}
