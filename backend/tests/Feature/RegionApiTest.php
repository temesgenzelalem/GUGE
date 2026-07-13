<?php

namespace Tests\Feature;

use App\Models\Creator;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegionApiTest extends TestCase
{
    private User $admin;

    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->region = Region::factory()->create();
    }

    public function test_region_index_returns_paginated_regions(): void
    {
        Region::factory()->count(3)->create();

        $response = $this->getJson('/api/regions');

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)
                ->where('message', 'Regions retrieved successfully')
                ->has('meta.current_page')
                ->has('links.next')
                ->etc()
            );
    }

    public function test_region_show_returns_region(): void
    {
        $response = $this->getJson("/api/regions/{$this->region->slug}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', $this->region->slug)
            ->assertJsonPath('data.name', $this->region->name);
    }

    public function test_region_show_includes_status_and_featured(): void
    {
        $response = $this->getJson("/api/regions/{$this->region->slug}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['status', 'featured']]);
    }

    public function test_region_index_filters_by_direction(): void
    {
        Region::factory()->create(['direction' => 'north']);
        Region::factory()->create(['direction' => 'south']);

        $response = $this->getJson('/api/regions?direction=north');

        $response->assertOk();
        $regions = $response->json('data.data');
        foreach ($regions as $r) {
            $this->assertEquals('north', $r['direction']);
        }
    }

    public function test_admin_can_create_region(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/regions', [
            'name' => 'Aksum Ancient City',
            'zone' => 'Tigray',
            'direction' => 'north',
            'description' => 'Home of the ancient Aksumite empire.',
            'tagline' => 'Where history meets sky',
            'wiki_article' => 'Aksum',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Aksum Ancient City')
            ->assertJsonPath('data.slug', 'aksum-ancient-city');

        $this->assertDatabaseHas('regions', ['name' => 'Aksum Ancient City']);
    }

    public function test_region_slug_auto_generated(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/regions', [
            'name' => 'Blue Nile Falls',
            'zone' => 'Amhara',
            'direction' => 'north',
            'description' => 'The most powerful waterfall in Africa.',
            'tagline' => 'Nature at its finest',
            'wiki_article' => 'Blue_Nile_Falls',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'blue-nile-falls');
    }

    public function test_region_creation_requires_name(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/regions', [
            'zone' => 'Amhara',
            'direction' => 'north',
            'description' => 'Some description',
            'tagline' => 'Some tagline',
            'wiki_article' => 'Some_Article',
        ])->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_region_creation_requires_valid_direction(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/regions', [
            'name' => 'Test Region',
            'zone' => 'Amhara',
            'direction' => 'diagonal',
            'description' => 'Some description',
            'tagline' => 'Some tagline',
            'wiki_article' => 'Some_Article',
        ])->assertUnprocessable()
            ->assertJsonPath('errors.direction.0', 'Direction must be one of north, south, east or west.');
    }

    public function test_admin_can_update_region(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/regions/{$this->region->slug}", [
            'name' => 'Updated Region Name',
            'zone' => $this->region->zone,
            'direction' => $this->region->direction,
            'description' => 'Updated description',
            'tagline' => $this->region->tagline,
            'wiki_article' => $this->region->wiki_article,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Region Name');

        $this->assertDatabaseHas('regions', ['id' => $this->region->id, 'name' => 'Updated Region Name']);
    }

    public function test_admin_can_delete_region(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/regions/{$this->region->slug}");

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('regions', ['id' => $this->region->id]);
    }

    public function test_guest_cannot_create_region(): void
    {
        $this->postJson('/api/regions', ['name' => 'Test'])->assertUnauthorized();
    }

    public function test_non_admin_cannot_create_region(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/regions', [
            'name' => 'Test Region',
            'zone' => 'Test',
            'direction' => 'north',
            'description' => 'Test',
            'tagline' => 'Test',
            'wiki_article' => 'Test',
        ])->assertForbidden();
    }

    public function test_non_admin_cannot_delete_region(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->deleteJson("/api/regions/{$this->region->slug}")->assertForbidden();
    }

    public function test_can_get_region_products(): void
    {
        Product::factory()->create(['region_id' => $this->region->id]);

        $response = $this->getJson("/api/regions/{$this->region->slug}/products");

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_can_get_region_stories(): void
    {
        $creator = Creator::factory()->create();
        Story::factory()->create([
            'region_id' => $this->region->id,
            'creator_id' => $creator->id,
            'status' => 'published',
        ]);

        $response = $this->getJson("/api/regions/{$this->region->slug}/stories");

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_returns_404_for_unknown_region(): void
    {
        $this->getJson('/api/regions/nonexistent-slug')->assertNotFound();
    }
}
