<?php

namespace Tests\Unit;

use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Domain\Product\Events\ProductCreated;
use App\Domain\Product\Events\ProductDeleted;
use App\Domain\Product\Events\ProductUpdated;
use App\Domain\Product\ProductService;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    private ProductService $service;

    private Region $region;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->app->make(ProductRepositoryInterface::class);
        $this->service = new ProductService($repository);
        $this->region = Region::factory()->create();
    }

    public function test_create_product_persists_to_database(): void
    {
        $data = [
            'name' => 'Jimma Coffee',
            'region_id' => $this->region->id,
            'category' => 'coffee',
            'description' => 'Forest coffee from Jimma.',
            'wiki_article' => 'Coffea_arabica',
        ];

        $product = $this->service->createProduct($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', ['name' => 'Jimma Coffee']);
    }

    public function test_create_product_dispatches_product_created_event(): void
    {
        Event::fake([ProductCreated::class]);

        $product = $this->service->createProduct([
            'name' => 'Berbere Spice',
            'region_id' => $this->region->id,
            'category' => 'spices',
            'description' => 'Traditional spice mix.',
            'wiki_article' => 'Berbere',
        ]);

        Event::assertDispatched(ProductCreated::class, fn ($e) => $e->product->id === $product->id);
    }

    public function test_update_product_dispatches_product_updated_event(): void
    {
        Event::fake([ProductUpdated::class]);

        $product = Product::factory()->create(['region_id' => $this->region->id]);

        $this->service->updateProduct($product, ['name' => 'Updated Coffee']);

        Event::assertDispatched(ProductUpdated::class);
    }

    public function test_delete_product_dispatches_product_deleted_event(): void
    {
        Event::fake([ProductDeleted::class]);

        $product = Product::factory()->create(['region_id' => $this->region->id]);

        $this->service->deleteProduct($product);

        Event::assertDispatched(ProductDeleted::class);
    }

    public function test_list_products_returns_paginator(): void
    {
        Product::factory()->count(4)->create(['region_id' => $this->region->id]);

        $result = $this->service->listProducts([], 20);

        $this->assertEquals(4, $result->total());
    }

    public function test_list_products_filters_by_category(): void
    {
        Product::factory()->create(['region_id' => $this->region->id, 'category' => 'coffee']);
        Product::factory()->create(['region_id' => $this->region->id, 'category' => 'honey']);
        Product::factory()->create(['region_id' => $this->region->id, 'category' => 'coffee']);

        $result = $this->service->listProducts(['category' => 'coffee'], 20);

        $this->assertEquals(2, $result->total());
    }

    public function test_delete_product_removes_from_database(): void
    {
        Event::fake();

        $product = Product::factory()->create(['region_id' => $this->region->id]);
        $id = $product->id;

        $this->service->deleteProduct($product);

        $this->assertDatabaseMissing('products', ['id' => $id]);
    }
}
