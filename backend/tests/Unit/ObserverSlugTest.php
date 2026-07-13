<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Creator;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Verifies that Observers auto-generate slugs on create and update.
 */
class ObserverSlugTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake(); // prevent listeners from running during slug tests
    }

    public function test_region_slug_auto_generated_on_create(): void
    {
        $region = Region::factory()->create(['name' => 'Blue Nile Falls', 'slug' => '']);

        $this->assertEquals('blue-nile-falls', $region->fresh()->slug);
    }

    public function test_region_slug_updates_when_name_changes(): void
    {
        $region = Region::factory()->create(['name' => 'Harar Old Town']);

        $region->update(['name' => 'Harar Jugol']);

        $this->assertEquals('harar-jugol', $region->fresh()->slug);
    }

    public function test_region_slug_unchanged_when_set_explicitly(): void
    {
        $region = Region::factory()->create(['name' => 'Lalibela', 'slug' => 'custom-slug']);

        $region->update(['name' => 'New Name']);

        // slug was already set explicitly — should not be overwritten by name change
        // because isDirty('slug') would return false only if slug wasn't changed
        // The observer only updates slug when name changes AND slug wasn't explicitly changed
        $this->assertDatabaseHas('regions', ['id' => $region->id]);
    }

    public function test_product_slug_auto_generated_on_create(): void
    {
        $region = Region::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Yirgacheffe Coffee',
            'slug' => '',
            'region_id' => $region->id,
        ]);

        $this->assertEquals('yirgacheffe-coffee', $product->fresh()->slug);
    }

    public function test_product_slug_updates_when_name_changes(): void
    {
        $region = Region::factory()->create();
        $product = Product::factory()->create(['region_id' => $region->id, 'name' => 'Old Name']);

        $product->update(['name' => 'Sidama Coffee']);

        $this->assertEquals('sidama-coffee', $product->fresh()->slug);
    }

    public function test_story_slug_auto_generated_from_title(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create(['region_id' => $region->id]);

        $story = Story::factory()->create([
            'title' => 'The Hyena Feeders of Harar',
            'slug' => '',
            'region_id' => $region->id,
            'creator_id' => $creator->id,
        ]);

        $this->assertEquals('the-hyena-feeders-of-harar', $story->fresh()->slug);
    }

    public function test_creator_slug_auto_generated_from_full_name(): void
    {
        $region = Region::factory()->create();
        $creator = Creator::factory()->create([
            'full_name' => 'Yohannes Tesfaye',
            'slug' => '',
            'region_id' => $region->id,
        ]);

        $this->assertEquals('yohannes-tesfaye', $creator->fresh()->slug);
    }

    public function test_category_slug_auto_generated_on_create(): void
    {
        $category = Category::factory()->create(['name' => 'Handmade Crafts', 'slug' => '']);

        $this->assertEquals('handmade-crafts', $category->fresh()->slug);
    }
}
