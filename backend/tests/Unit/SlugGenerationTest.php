<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Creator;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Tests\TestCase;

/**
 * Tests that observer-driven slug auto-generation works correctly
 * for all models that use slugs as route keys.
 */
class SlugGenerationTest extends TestCase
{
    // ── Region ─────────────────────────────────────────────────────────────

    public function test_region_slug_auto_generated_from_name(): void
    {
        $region = Region::factory()->create([
            'name' => 'Blue Nile Falls',
            'slug' => '', // force observer to generate it
        ]);

        $this->assertEquals('blue-nile-falls', $region->fresh()->slug);
    }

    public function test_region_slug_not_overwritten_when_provided(): void
    {
        $region = Region::factory()->create([
            'name' => 'Lalibela',
            'slug' => 'custom-slug',
        ]);

        $this->assertEquals('custom-slug', $region->fresh()->slug);
    }

    public function test_region_slug_updated_when_name_changes(): void
    {
        $region = Region::factory()->create(['name' => 'Old Name']);

        $region->update(['name' => 'New Name']);

        $this->assertEquals('new-name', $region->fresh()->slug);
    }

    public function test_region_slug_not_changed_when_explicit_slug_provided_on_update(): void
    {
        $region = Region::factory()->create(['name' => 'Old Name']);

        $region->update(['name' => 'New Name', 'slug' => 'kept-slug']);

        $this->assertEquals('kept-slug', $region->fresh()->slug);
    }

    // ── Product ────────────────────────────────────────────────────────────

    public function test_product_slug_auto_generated_from_name(): void
    {
        $region = Region::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Jimma Forest Coffee',
            'slug' => '',
            'region_id' => $region->id,
        ]);

        $this->assertEquals('jimma-forest-coffee', $product->fresh()->slug);
    }

    public function test_product_slug_updated_when_name_changes(): void
    {
        $region = Region::factory()->create();
        $product = Product::factory()->create(['region_id' => $region->id, 'name' => 'Old Product']);

        $product->update(['name' => 'New Product Name']);

        $this->assertEquals('new-product-name', $product->fresh()->slug);
    }

    // ── Story ──────────────────────────────────────────────────────────────

    public function test_story_slug_auto_generated_from_title(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create(['region_id' => $region->id]);
        $story = Story::factory()->create([
            'title' => 'The Ancient Churches of Lalibela',
            'slug' => '',
            'region_id' => $region->id,
            'creator_id' => $creator->id,
        ]);

        $this->assertEquals('the-ancient-churches-of-lalibela', $story->fresh()->slug);
    }

    public function test_story_slug_updated_when_title_changes(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create(['region_id' => $region->id]);
        $story = Story::factory()->create([
            'region_id' => $region->id,
            'creator_id' => $creator->id,
            'title' => 'Old Title',
        ]);

        $story->update(['title' => 'New Story Title']);

        $this->assertEquals('new-story-title', $story->fresh()->slug);
    }

    // ── Creator ────────────────────────────────────────────────────────────

    public function test_creator_slug_auto_generated_from_full_name(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create([
            'full_name' => 'Abebe Girma',
            'slug' => '',
            'region_id' => $region->id,
        ]);

        $this->assertEquals('abebe-girma', $creator->fresh()->slug);
    }

    public function test_creator_name_synced_from_full_name(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create([
            'full_name' => 'Fatuma Omar',
            'name' => '',
            'region_id' => $region->id,
        ]);

        $this->assertEquals('Fatuma Omar', $creator->fresh()->name);
    }

    // ── Category ───────────────────────────────────────────────────────────

    public function test_category_slug_auto_generated_from_name(): void
    {
        $category = Category::factory()->create([
            'name' => 'Ethiopian Coffee',
            'slug' => '',
        ]);

        $this->assertEquals('ethiopian-coffee', $category->fresh()->slug);
    }

    public function test_category_slug_updated_when_name_changes(): void
    {
        $category = Category::factory()->create(['name' => 'Old Category']);

        $category->update(['name' => 'New Category Name']);

        $this->assertEquals('new-category-name', $category->fresh()->slug);
    }
}
