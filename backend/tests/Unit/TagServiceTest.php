<?php

namespace Tests\Unit;

use App\Domain\Tag\Contracts\TagRepositoryInterface;
use App\Domain\Tag\TagService;
use App\Models\Tag;
use Tests\TestCase;

class TagServiceTest extends TestCase
{
    private TagService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TagService($this->app->make(TagRepositoryInterface::class));
    }

    public function test_create_tag_persists_with_slug(): void
    {
        $tag = $this->service->createTag(['name' => 'Rock Hewn Churches']);

        $this->assertDatabaseHas('tags', ['name' => 'Rock Hewn Churches', 'slug' => 'rock-hewn-churches']);
    }

    public function test_update_tag_updates_slug(): void
    {
        $tag = Tag::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

        $this->service->updateTag($tag, ['name' => 'New Name']);

        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'slug' => 'new-name']);
    }

    public function test_delete_tag_removes_from_database(): void
    {
        $tag = Tag::factory()->create();
        $id = $tag->id;

        $this->service->deleteTag($tag);

        $this->assertDatabaseMissing('tags', ['id' => $id]);
    }

    public function test_list_tags_returns_paginator(): void
    {
        Tag::factory()->count(4)->create();

        $result = $this->service->listTags([], 50);

        $this->assertEquals(4, $result->total());
    }

    public function test_all_tags_returns_collection(): void
    {
        Tag::factory()->count(3)->create();

        $result = $this->service->allTags();

        $this->assertCount(3, $result);
    }

    public function test_list_tags_filters_by_search(): void
    {
        Tag::factory()->create(['name' => 'Coffee', 'slug' => 'coffee']);
        Tag::factory()->create(['name' => 'Heritage', 'slug' => 'heritage']);

        $result = $this->service->listTags(['search' => 'coffee'], 50);

        $this->assertEquals(1, $result->total());
    }
}
