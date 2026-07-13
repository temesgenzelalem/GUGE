<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Region;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    private Region $region;

    private Product $product;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->admin()->create();
        $this->region = Region::factory()->create();
        $this->product = Product::factory()->create(['region_id' => $this->region->id]);
    }

    public function test_can_list_products(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.data.0.id', $this->product->id);
    }

    public function test_can_filter_products_by_category(): void
    {
        $coffeeProduct = Product::factory()->create([
            'region_id' => $this->region->id,
            'category' => 'coffee',
        ]);

        $response = $this->getJson('/api/products?category=coffee');

        $response->assertOk()
            ->assertJsonPath('data.data.0.category', 'coffee');
    }

    public function test_can_search_products(): void
    {
        $product = Product::factory()->create([
            'region_id' => $this->region->id,
            'name' => 'Ethiopian Coffee',
        ]);

        $response = $this->getJson('/api/products?search=Ethiopian');

        $response->assertOk();
    }

    public function test_can_view_single_product(): void
    {
        $response = $this->getJson("/api/products/{$this->product->slug}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => ['id', 'name', 'slug'],
                    'related' => [],
                ],
            ])
            ->assertJsonPath('data.data.id', $this->product->id);
    }

    public function test_can_create_product(): void
    {
        $data = [
            'name' => 'New Product',
            'region_id' => $this->region->id,
            'category' => 'coffee',
            'description' => 'A great product',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/products', $data);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Product')
            ->assertJsonPath('data.category', 'coffee');

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    public function test_can_update_product(): void
    {
        $data = ['name' => 'Updated Product', 'description' => 'Updated description'];

        Sanctum::actingAs($this->adminUser);
        $response = $this->putJson("/api/products/{$this->product->slug}", array_merge([
            'region_id' => $this->region->id,
            'category' => 'coffee',
            'wiki_article' => 'https://example.com',
        ], $data));

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Product');

        $this->assertDatabaseHas('products', ['id' => $this->product->id, 'name' => 'Updated Product']);
    }

    public function test_can_delete_product(): void
    {
        Sanctum::actingAs($this->adminUser);
        $response = $this->deleteJson("/api/products/{$this->product->slug}");

        $response->assertOk();

        $this->assertDatabaseMissing('products', ['id' => $this->product->id]);
    }

    public function test_product_creation_requires_region(): void
    {
        $data = [
            'name' => 'New Product',
            'category' => 'coffee',
            'description' => 'A great product',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/products', $data);

        $response->assertUnprocessable()
            ->assertJsonPath('errors.region_id.0', 'Product must be assigned to a region.');
    }

    public function test_product_slug_auto_generated(): void
    {
        $data = [
            'name' => 'Coffee From Ethiopia',
            'region_id' => $this->region->id,
            'category' => 'coffee',
            'description' => 'A great product',
            'wiki_article' => 'https://example.com',
        ];

        Sanctum::actingAs($this->adminUser);
        $response = $this->postJson('/api/products', $data);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'coffee-from-ethiopia');
    }

    public function test_can_view_related_products(): void
    {
        $related = Product::factory()->create([
            'region_id' => $this->product->region_id,
            'category' => $this->product->category,
        ]);

        $response = $this->getJson("/api/products/{$this->product->slug}");

        $response->assertOk()
            ->assertJsonPath('data.related.0.id', $related->id);
    }
}
