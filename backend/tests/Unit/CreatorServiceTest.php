<?php

namespace Tests\Unit;

use App\Domain\Creator\Contracts\CreatorRepositoryInterface;
use App\Domain\Creator\CreatorService;
use App\Domain\Creator\Events\CreatorCreated;
use App\Domain\Creator\Events\CreatorDeleted;
use App\Domain\Creator\Events\CreatorUpdated;
use App\Models\Creator;
use App\Models\Region;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreatorServiceTest extends TestCase
{
    private CreatorService $service;

    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->app->make(CreatorRepositoryInterface::class);
        $this->service = new CreatorService($repository);
        $this->region = Region::factory()->create();
    }

    public function test_create_creator_persists_to_database(): void
    {
        $data = [
            'full_name' => 'Tigist Alemu',
            'username' => 'tigist_alemu',
            'region_id' => $this->region->id,
            'role' => 'photographer',
            'bio' => 'Documentary photographer.',
            'wiki_article' => 'Photography',
        ];

        $creator = $this->service->createCreator($data);

        $this->assertInstanceOf(Creator::class, $creator);
        $this->assertDatabaseHas('creators', ['name' => 'Tigist Alemu']);
    }

    public function test_create_creator_normalizes_full_name_to_name(): void
    {
        $creator = $this->service->createCreator([
            'full_name' => 'Yohannes Tesfaye',
            'username' => 'yohannes_t',
            'role' => 'writer',
            'bio' => 'Cultural writer.',
        ]);

        $this->assertEquals('Yohannes Tesfaye', $creator->name);
        $this->assertEquals('Yohannes Tesfaye', $creator->full_name);
    }

    public function test_create_creator_dispatches_creator_created_event(): void
    {
        Event::fake([CreatorCreated::class]);

        $creator = $this->service->createCreator([
            'full_name' => 'Fatuma Omar',
            'username' => 'fatuma_omar',
            'role' => 'videographer',
            'bio' => 'Videographer.',
        ]);

        Event::assertDispatched(CreatorCreated::class, fn ($e) => $e->creator->id === $creator->id);
    }

    public function test_update_creator_dispatches_creator_updated_event(): void
    {
        Event::fake([CreatorUpdated::class]);

        $creator = Creator::factory()->create(['region_id' => $this->region->id]);

        $this->service->updateCreator($creator, ['bio' => 'Updated bio.']);

        Event::assertDispatched(CreatorUpdated::class);
    }

    public function test_delete_creator_dispatches_creator_deleted_event(): void
    {
        Event::fake([CreatorDeleted::class]);

        $creator = Creator::factory()->create(['region_id' => $this->region->id]);

        $this->service->deleteCreator($creator);

        Event::assertDispatched(CreatorDeleted::class);
    }

    public function test_delete_creator_soft_deletes(): void
    {
        Event::fake();

        $creator = Creator::factory()->create(['region_id' => $this->region->id]);
        $id = $creator->id;

        $this->service->deleteCreator($creator);

        $this->assertSoftDeleted('creators', ['id' => $id]);
    }

    public function test_list_creators_returns_paginator(): void
    {
        Creator::factory()->count(3)->create(['region_id' => $this->region->id]);

        $result = $this->service->listCreators([], 20);

        $this->assertEquals(3, $result->total());
    }
}
