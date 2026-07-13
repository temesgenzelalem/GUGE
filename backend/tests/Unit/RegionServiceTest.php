<?php

namespace Tests\Unit;

use App\Domain\Region\Contracts\RegionRepositoryInterface;
use App\Domain\Region\Events\RegionCreated;
use App\Domain\Region\Events\RegionDeleted;
use App\Domain\Region\Events\RegionUpdated;
use App\Domain\Region\RegionService;
use App\Models\Region;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegionServiceTest extends TestCase
{
    private RegionService $service;

    private RegionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(RegionRepositoryInterface::class);
        $this->service = new RegionService($this->repository);
    }

    public function test_create_region_persists_to_database(): void
    {
        $data = [
            'name' => 'Lalibela',
            'zone' => 'North Wollo',
            'direction' => 'north',
            'description' => 'Ancient rock-hewn churches.',
            'tagline' => 'Where stone meets the divine',
            'wiki_article' => 'Lalibela',
        ];

        $region = $this->service->createRegion($data);

        $this->assertInstanceOf(Region::class, $region);
        $this->assertDatabaseHas('regions', ['name' => 'Lalibela']);
    }

    public function test_create_region_dispatches_region_created_event(): void
    {
        Event::fake([RegionCreated::class]);

        $region = $this->service->createRegion([
            'name' => 'Gondar',
            'zone' => 'North Gondar',
            'direction' => 'north',
            'description' => 'Royal castles.',
            'tagline' => 'Camelot of Africa',
            'wiki_article' => 'Gondar',
        ]);

        Event::assertDispatched(RegionCreated::class, function ($event) use ($region) {
            return $event->region->id === $region->id;
        });
    }

    public function test_update_region_dispatches_region_updated_event(): void
    {
        Event::fake([RegionUpdated::class]);

        $region = Region::factory()->create();

        $updated = $this->service->updateRegion($region, ['name' => 'Updated Name']);

        Event::assertDispatched(RegionUpdated::class, function ($event) use ($updated) {
            return $event->region->id === $updated->id;
        });
    }

    public function test_delete_region_dispatches_region_deleted_event(): void
    {
        Event::fake([RegionDeleted::class]);

        $region = Region::factory()->create();

        $this->service->deleteRegion($region);

        Event::assertDispatched(RegionDeleted::class);
    }

    public function test_list_regions_returns_paginator(): void
    {
        Region::factory()->count(5)->create();

        $result = $this->service->listRegions([], 20);

        $this->assertEquals(5, $result->total());
    }

    public function test_list_regions_filters_by_direction(): void
    {
        Region::factory()->create(['direction' => 'north']);
        Region::factory()->create(['direction' => 'south']);
        Region::factory()->create(['direction' => 'north']);

        $result = $this->service->listRegions(['direction' => 'north'], 20);

        $this->assertEquals(2, $result->total());
        foreach ($result->items() as $item) {
            $this->assertEquals('north', $item->direction);
        }
    }

    public function test_list_regions_uses_cache(): void
    {
        Cache::spy();

        Region::factory()->count(2)->create();

        $this->service->listRegions([], 20);

        Cache::shouldHaveReceived('remember')->once();
    }

    public function test_get_region_returns_fresh_model(): void
    {
        $region = Region::factory()->create(['name' => 'Original']);

        $result = $this->service->getRegion($region);

        $this->assertInstanceOf(Region::class, $result);
        $this->assertEquals($region->id, $result->id);
    }

    public function test_delete_region_removes_from_database(): void
    {
        Event::fake();

        $region = Region::factory()->create();
        $id = $region->id;

        $this->service->deleteRegion($region);

        $this->assertDatabaseMissing('regions', ['id' => $id]);
    }
}
