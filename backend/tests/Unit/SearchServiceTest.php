<?php

namespace Tests\Unit;

use App\Domain\Search\SearchService;
use App\Models\Creator;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Tests\TestCase;

class SearchServiceTest extends TestCase
{
    private SearchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SearchService;
    }

    public function test_search_returns_expected_structure(): void
    {
        $result = $this->service->search('coffee');

        $this->assertArrayHasKey('regions', $result);
        $this->assertArrayHasKey('products', $result);
        $this->assertArrayHasKey('stories', $result);
        $this->assertArrayHasKey('creators', $result);
    }

    public function test_search_finds_matching_region(): void
    {
        Region::factory()->create(['name' => 'Jimma Coffee Region']);

        $result = $this->service->search('Jimma');

        $this->assertCount(1, $result['regions']);
    }

    public function test_search_finds_matching_product(): void
    {
        $region = Region::factory()->create();
        Product::factory()->create(['name' => 'Yirgacheffe Coffee', 'region_id' => $region->id]);

        $result = $this->service->search('Yirgacheffe');

        $this->assertCount(1, $result['products']);
    }

    public function test_search_finds_matching_published_story(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create(['region_id' => $region->id]);
        Story::factory()->create([
            'title' => 'Gondar Castle Story',
            'status' => 'published',
            'region_id' => $region->id,
            'creator_id' => $creator->id,
        ]);

        $result = $this->service->search('Gondar');

        $this->assertCount(1, $result['stories']);
    }

    public function test_search_excludes_draft_stories(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create(['region_id' => $region->id]);
        Story::factory()->create([
            'title' => 'Draft Story About Harar',
            'status' => 'draft',
            'region_id' => $region->id,
            'creator_id' => $creator->id,
        ]);

        $result = $this->service->search('Harar');

        $this->assertCount(0, $result['stories']);
    }

    public function test_search_finds_matching_creator(): void
    {
        Creator::factory()->create(['full_name' => 'Tigist Alemu Photographer']);

        $result = $this->service->search('Tigist');

        $this->assertCount(1, $result['creators']);
    }
}
