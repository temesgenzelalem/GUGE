<?php

namespace Tests\Unit;

use App\Domain\Category\CategoryService;
use App\Domain\Category\Contracts\CategoryRepositoryInterface;
use App\Domain\Category\Events\CategoryCreated;
use App\Domain\Category\Events\CategoryDeleted;
use App\Domain\Category\Events\CategoryUpdated;
use App\Models\Category;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    private CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryService($this->app->make(CategoryRepositoryInterface::class));
    }

    public function test_create_category_persists_to_database(): void
    {
        Event::fake();

        $category = $this->service->createCategory(['name' => 'Ethiopian Coffee']);

        $this->assertDatabaseHas('categories', ['name' => 'Ethiopian Coffee']);
        $this->assertInstanceOf(Category::class, $category);
    }

    public function test_create_category_dispatches_event(): void
    {
        Event::fake([CategoryCreated::class]);

        $category = $this->service->createCategory(['name' => 'Spices']);

        Event::assertDispatched(CategoryCreated::class, fn ($e) => $e->category->id === $category->id);
    }

    public function test_update_category_dispatches_event(): void
    {
        Event::fake([CategoryUpdated::class]);

        $category = Category::factory()->create();
        $this->service->updateCategory($category, ['name' => 'Updated Name']);

        Event::assertDispatched(CategoryUpdated::class);
    }

    public function test_delete_category_soft_deletes(): void
    {
        Event::fake([CategoryDeleted::class]);

        $category = Category::factory()->create();
        $id = $category->id;

        $this->service->deleteCategory($category);

        $this->assertSoftDeleted('categories', ['id' => $id]);
        Event::assertDispatched(CategoryDeleted::class);
    }

    public function test_list_categories_returns_paginator(): void
    {
        Category::factory()->count(3)->create();

        $result = $this->service->listCategories([], 20);

        $this->assertEquals(3, $result->total());
    }

    public function test_list_categories_filters_by_search(): void
    {
        Category::factory()->create(['name' => 'Ethiopian Coffee']);
        Category::factory()->create(['name' => 'Handicrafts']);

        $result = $this->service->listCategories(['search' => 'coffee'], 20);

        $this->assertEquals(1, $result->total());
    }
}
