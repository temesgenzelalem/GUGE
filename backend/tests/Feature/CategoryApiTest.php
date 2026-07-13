<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    private User $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->category = Category::factory()->create();
    }

    public function test_admin_can_list_categories(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/admin/categories');

        $response->assertOk()
            ->assertJsonStructure([
                'success', 'message', 'data', 'meta' => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('success', true);
    }

    public function test_can_search_categories(): void
    {
        Sanctum::actingAs($this->admin);
        $cat = Category::factory()->create(['name' => 'Ethiopian Coffee']);

        $response = $this->getJson('/api/admin/categories?search=Ethiopian');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_admin_can_view_single_category(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/categories/{$this->category->slug}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $this->category->id)
            ->assertJsonPath('data.slug', $this->category->slug);
    }

    public function test_admin_can_create_category(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/admin/categories', [
            'name' => 'Handmade Crafts',
            'description' => 'Traditional Ethiopian crafts',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Handmade Crafts')
            ->assertJsonPath('data.slug', 'handmade-crafts');

        $this->assertDatabaseHas('categories', ['name' => 'Handmade Crafts']);
    }

    public function test_category_slug_auto_generated(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/admin/categories', [
            'name' => 'Woven Textiles',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'woven-textiles');
    }

    public function test_category_name_must_be_unique(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/admin/categories', ['name' => $this->category->name])
            ->assertUnprocessable()
            ->assertJsonPath('errors.name.0', 'Category name must be unique.');
    }

    public function test_category_creation_requires_name(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/admin/categories', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function test_admin_can_update_category(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/admin/categories/{$this->category->slug}", [
            'name' => 'Updated Category',
            'description' => 'Updated description',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Category');

        $this->assertDatabaseHas('categories', ['id' => $this->category->id, 'name' => 'Updated Category']);
    }

    public function test_admin_can_delete_category(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/categories/{$this->category->slug}");

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('categories', ['id' => $this->category->id]);
    }

    public function test_guest_cannot_create_category(): void
    {
        $this->postJson('/api/admin/categories', ['name' => 'New Cat'])
            ->assertUnauthorized();
    }

    public function test_non_admin_cannot_create_category(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/admin/categories', ['name' => 'New Cat'])
            ->assertForbidden();
    }

    public function test_non_admin_cannot_delete_category(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->deleteJson("/api/admin/categories/{$this->category->slug}")
            ->assertForbidden();
    }
}
