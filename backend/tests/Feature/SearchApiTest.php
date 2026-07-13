<?php

namespace Tests\Feature;

use App\Models\Creator;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    public function test_search_returns_empty_when_query_too_short(): void
    {
        $response = $this->getJson('/api/search?q=a');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.regions', [])
            ->assertJsonPath('data.products', [])
            ->assertJsonPath('data.stories', [])
            ->assertJsonPath('data.creators', []);
    }

    public function test_search_returns_matching_regions(): void
    {
        Region::factory()->create(['name' => 'Lalibela Ancient']);

        $response = $this->getJson('/api/search?q=Lalibela');

        $response->assertOk()
            ->assertJsonPath('success', true);

        $data = $response->json('data');
        $this->assertNotEmpty($data['regions']);
    }

    public function test_search_returns_matching_products(): void
    {
        $region = Region::factory()->create();
        Product::factory()->create([
            'region_id' => $region->id,
            'name' => 'Yirgacheffe Coffee Blend',
        ]);

        $response = $this->getJson('/api/search?q=Yirgacheffe');

        $response->assertOk();

        $data = $response->json('data');
        $this->assertNotEmpty($data['products']);
    }

    public function test_search_returns_matching_stories(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create();
        Story::factory()->create([
            'region_id' => $region->id,
            'creator_id' => $creator->id,
            'title' => 'Journey Through Gondar Castles',
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/search?q=Gondar');

        $response->assertOk();

        $data = $response->json('data');
        $this->assertNotEmpty($data['stories']);
    }

    public function test_search_returns_matching_creators(): void
    {
        Creator::factory()->create(['name' => 'Tigist Photographer']);

        $response = $this->getJson('/api/search?q=Tigist');

        $response->assertOk();

        $data = $response->json('data');
        $this->assertNotEmpty($data['creators']);
    }

    public function test_search_returns_all_entity_types(): void
    {
        $response = $this->getJson('/api/search?q=coffee');

        $response->assertOk()
            ->assertJsonStructure([
                'success', 'message',
                'data' => ['regions', 'products', 'stories', 'creators'],
            ]);
    }

    public function test_search_endpoint_without_query_returns_empty(): void
    {
        $response = $this->getJson('/api/search');

        $response->assertOk()
            ->assertJsonPath('data.regions', [])
            ->assertJsonPath('data.products', []);
    }
}
