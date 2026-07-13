<?php

namespace Tests\Feature;

use App\Models\Creator;
use App\Models\Region;
use App\Models\Story;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreatorApiTest extends TestCase
{
    private Creator $creator;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->admin()->create();
        $this->creator = Creator::factory()->create();
    }

    public function test_can_list_creators(): void
    {
        $response = $this->getJson('/api/creators');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.data.0.id', $this->creator->id);
    }

    public function test_can_search_creators(): void
    {
        $creator = Creator::factory()->create(['name' => 'John Photographer']);

        $response = $this->getJson('/api/creators?search=John');

        $response->assertOk();
    }

    public function test_can_view_single_creator(): void
    {
        $response = $this->getJson("/api/creators/{$this->creator->slug}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'slug', 'role', 'bio', 'region_coverage', 'stories'],
            ])
            ->assertJsonPath('data.id', $this->creator->id)
            ->assertJsonPath('data.region_coverage', $this->creator->region->name);
    }

    public function test_can_create_creator(): void
    {
        $data = [
            'full_name' => 'New Creator',
            'username' => 'newcreator',
            'role' => 'photographer',
            'bio' => 'A talented photographer',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/creators', $data);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Creator')
            ->assertJsonPath('data.role', 'photographer');

        $this->assertDatabaseHas('creators', ['name' => 'New Creator']);
    }

    public function test_can_update_creator(): void
    {
        $data = [
            'full_name' => 'Updated Creator',
            'name' => 'Updated Creator',
            'role' => 'writer',
            'bio' => 'Updated bio',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->putJson("/api/creators/{$this->creator->slug}", $data);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Creator');
    }

    public function test_can_delete_creator(): void
    {
        Sanctum::actingAs($this->adminUser);
        $response = $this->deleteJson("/api/creators/{$this->creator->slug}");

        $response->assertOk();

        $this->assertSoftDeleted('creators', ['id' => $this->creator->id]);
    }

    public function test_creator_creation_requires_name(): void
    {
        $data = [
            'role' => 'photographer',
            'bio' => 'A talented photographer',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/creators', $data);

        $response->assertUnprocessable()
            ->assertJsonPath('errors.full_name.0', 'Creator full name is required when no name is provided.');
    }

    public function test_creator_slug_auto_generated(): void
    {
        $data = [
            'full_name' => 'John Smith Photography',
            'role' => 'photographer',
            'bio' => 'A talented photographer',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/creators', $data);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'john-smith-photography');
    }

    public function test_can_view_creator_stories(): void
    {
        $story = Story::factory()->create([
            'creator_id' => $this->creator->id,
            'region_id' => Region::factory(),
        ]);

        $response = $this->getJson("/api/creators/{$this->creator->slug}");

        $response->assertOk()
            ->assertJsonPath('data.stories.0.id', $story->id);
    }
}
