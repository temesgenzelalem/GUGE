<?php

namespace Tests\Feature;

use App\Models\Creator;
use App\Models\Region;
use App\Models\Story;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoryApiTest extends TestCase
{
    private Region $region;

    private Story $story;

    private Creator $creator;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->admin()->create();
        $this->region = Region::factory()->create();
        $this->creator = Creator::factory()->create();
        $this->story = Story::factory()->create([
            'region_id' => $this->region->id,
            'creator_id' => $this->creator->id,
        ]);
    }

    public function test_can_list_stories(): void
    {
        $response = $this->getJson('/api/stories');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.data.0.id', $this->story->id);
    }

    public function test_can_filter_stories_by_type(): void
    {
        $travelStory = Story::factory()->create([
            'region_id' => $this->region->id,
            'type' => 'travel',
        ]);

        $response = $this->getJson('/api/stories?type=travel');

        $response->assertOk();
    }

    public function test_can_search_stories(): void
    {
        $story = Story::factory()->create([
            'region_id' => $this->region->id,
            'title' => 'Amazing Adventure in Ethiopia',
        ]);

        $response = $this->getJson('/api/stories?search=Amazing');

        $response->assertOk();
    }

    public function test_can_view_single_story(): void
    {
        $response = $this->getJson("/api/stories/{$this->story->slug}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => ['id', 'title', 'slug', 'body'],
                    'related' => [],
                ],
            ])
            ->assertJsonPath('data.data.id', $this->story->id);
    }

    public function test_can_create_story(): void
    {
        $data = [
            'title' => 'New Story',
            'region_id' => $this->region->id,
            'creator_id' => $this->creator->id,
            'type' => 'travel',
            'excerpt' => 'A great story excerpt',
            'body' => 'A long body of text for the story',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/stories', $data);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'New Story')
            ->assertJsonPath('data.type', 'travel');

        $this->assertDatabaseHas('stories', ['title' => 'New Story']);
    }

    public function test_can_update_story(): void
    {
        $data = [
            'title' => 'Updated Story',
            'excerpt' => 'Updated excerpt',
            'body' => 'Updated body text',
            'region_id' => $this->region->id,
            'type' => 'culture',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->putJson("/api/stories/{$this->story->slug}", $data);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated Story');
    }

    public function test_can_delete_story(): void
    {
        Sanctum::actingAs($this->adminUser);
        $response = $this->deleteJson("/api/stories/{$this->story->slug}");

        $response->assertOk();

        $this->assertDatabaseMissing('stories', ['id' => $this->story->id]);
    }

    public function test_story_creation_requires_region(): void
    {
        $data = [
            'title' => 'New Story',
            'type' => 'travel',
            'excerpt' => 'A great story excerpt',
            'body' => 'A long body of text for the story',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/stories', $data);

        $response->assertUnprocessable()
            ->assertJsonPath('errors.region_id.0', 'Story must be assigned to a region.');
    }

    public function test_story_slug_auto_generated(): void
    {
        $data = [
            'title' => 'The Amazing Journey Across Ethiopia',
            'region_id' => $this->region->id,
            'type' => 'travel',
            'excerpt' => 'A great story excerpt',
            'body' => 'A long body of text',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/stories', $data);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'the-amazing-journey-across-ethiopia');
    }

    public function test_can_view_related_stories(): void
    {
        $related = Story::factory()->create([
            'region_id' => $this->story->region_id,
        ]);

        $response = $this->getJson("/api/stories/{$this->story->slug}");

        $response->assertOk()
            ->assertJsonPath('data.related.0.id', $related->id);
    }
}
