<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TagApiTest extends TestCase
{
    private User $admin;

    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->tag = Tag::factory()->create();
    }

    public function test_anyone_can_list_tags(): void
    {
        $response = $this->getJson('/api/tags');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success', 'message', 'data',
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_anyone_can_get_all_tags_flat(): void
    {
        Tag::factory()->count(3)->create();

        $response = $this->getJson('/api/tags/all');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_anyone_can_view_single_tag(): void
    {
        $response = $this->getJson("/api/tags/{$this->tag->slug}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $this->tag->id)
            ->assertJsonPath('data.slug', $this->tag->slug);
    }

    public function test_admin_can_create_tag(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tags', ['name' => 'Ethiopian Heritage']);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Ethiopian Heritage')
            ->assertJsonPath('data.slug', 'ethiopian-heritage');

        $this->assertDatabaseHas('tags', ['name' => 'Ethiopian Heritage']);
    }

    public function test_tag_slug_auto_generated_on_create(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/tags', ['name' => 'Rock Hewn Churches']);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'rock-hewn-churches');
    }

    public function test_tag_name_must_be_unique(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/tags', ['name' => $this->tag->name])
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_tag_name_is_required(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/tags', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_admin_can_update_tag(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/tags/{$this->tag->slug}", [
            'name' => 'Updated Tag Name',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Tag Name');

        $this->assertDatabaseHas('tags', ['id' => $this->tag->id, 'name' => 'Updated Tag Name']);
    }

    public function test_admin_can_delete_tag(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/tags/{$this->tag->slug}");

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('tags', ['id' => $this->tag->id]);
    }

    public function test_guest_cannot_create_tag(): void
    {
        $this->postJson('/api/tags', ['name' => 'New Tag'])
            ->assertUnauthorized();
    }

    public function test_non_admin_cannot_create_tag(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/tags', ['name' => 'New Tag'])
            ->assertForbidden();
    }

    public function test_non_admin_cannot_delete_tag(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->deleteJson("/api/tags/{$this->tag->slug}")
            ->assertForbidden();
    }

    public function test_returns_404_for_unknown_tag(): void
    {
        $this->getJson('/api/tags/nonexistent-tag')
            ->assertNotFound();
    }

    public function test_can_search_tags(): void
    {
        Sanctum::actingAs($this->admin);
        Tag::factory()->create(['name' => 'Coffee Culture']);

        $response = $this->getJson('/api/tags?search=Coffee');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }
}
