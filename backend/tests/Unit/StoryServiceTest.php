<?php

namespace Tests\Unit;

use App\Domain\Story\Contracts\StoryRepositoryInterface;
use App\Domain\Story\Events\StoryCreated;
use App\Domain\Story\Events\StoryDeleted;
use App\Domain\Story\Events\StoryUpdated;
use App\Domain\Story\StoryService;
use App\Models\Creator;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StoryServiceTest extends TestCase
{
    private StoryService $service;

    private Region $region;

    private Creator $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->app->make(StoryRepositoryInterface::class);
        $this->service = new StoryService($repository);
        $this->region = Region::factory()->create();
        $this->creator = Creator::factory()->create(['region_id' => $this->region->id]);
    }

    public function test_create_story_persists_to_database(): void
    {
        $data = [
            'title' => 'Coffee Origins of Jimma',
            'region_id' => $this->region->id,
            'creator_id' => $this->creator->id,
            'type' => 'culture',
            'excerpt' => 'The story of coffee.',
            'body' => 'Long content about coffee...',
            'wiki_article' => 'Coffee',
        ];

        $story = $this->service->createStory($data);

        $this->assertInstanceOf(Story::class, $story);
        $this->assertDatabaseHas('stories', ['title' => 'Coffee Origins of Jimma']);
    }

    public function test_create_story_maps_body_to_content(): void
    {
        $data = [
            'title' => 'Test Story',
            'region_id' => $this->region->id,
            'type' => 'travel',
            'excerpt' => 'An excerpt.',
            'body' => 'The body content here.',
            'wiki_article' => 'Test',
        ];

        $story = $this->service->createStory($data);

        $this->assertEquals('The body content here.', $story->fresh()->content);
    }

    public function test_create_story_maps_type_to_category(): void
    {
        $data = [
            'title' => 'Culture Story',
            'region_id' => $this->region->id,
            'type' => 'festival',
            'excerpt' => 'An excerpt.',
            'body' => 'Content.',
            'wiki_article' => 'Test',
        ];

        $story = $this->service->createStory($data);

        $this->assertEquals('festival', $story->fresh()->category);
    }

    public function test_create_story_dispatches_story_created_event(): void
    {
        Event::fake([StoryCreated::class]);

        $story = $this->service->createStory([
            'title' => 'The Hyena Feeders of Harar',
            'region_id' => $this->region->id,
            'type' => 'culture',
            'excerpt' => 'An ancient tradition.',
            'body' => 'Long story...',
            'wiki_article' => 'Harar',
        ]);

        Event::assertDispatched(StoryCreated::class, fn ($e) => $e->story->id === $story->id);
    }

    public function test_update_story_dispatches_story_updated_event(): void
    {
        Event::fake([StoryUpdated::class]);

        $story = Story::factory()->create([
            'region_id' => $this->region->id,
            'creator_id' => $this->creator->id,
        ]);

        $this->service->updateStory($story, ['title' => 'Updated Title', 'type' => 'travel']);

        Event::assertDispatched(StoryUpdated::class);
    }

    public function test_delete_story_dispatches_story_deleted_event(): void
    {
        Event::fake([StoryDeleted::class]);

        $story = Story::factory()->create([
            'region_id' => $this->region->id,
            'creator_id' => $this->creator->id,
        ]);

        $this->service->deleteStory($story);

        Event::assertDispatched(StoryDeleted::class);
    }

    public function test_list_stories_returns_paginator(): void
    {
        Story::factory()->count(3)->create([
            'region_id' => $this->region->id,
            'creator_id' => $this->creator->id,
        ]);

        $result = $this->service->listStories([], 20);

        $this->assertEquals(3, $result->total());
    }

    public function test_delete_story_removes_from_database(): void
    {
        Event::fake();

        $story = Story::factory()->create([
            'region_id' => $this->region->id,
            'creator_id' => $this->creator->id,
        ]);
        $id = $story->id;

        $this->service->deleteStory($story);

        $this->assertDatabaseMissing('stories', ['id' => $id]);
    }
}
