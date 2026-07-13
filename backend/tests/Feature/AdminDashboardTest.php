<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Creator;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_admin_dashboard_returns_summary_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->create(['status' => 'suspended']);
        $region = Region::factory()->create(['status' => 'published']);
        $creator = Creator::factory()->create([
            'status' => 'published',
            'region_id' => $region->id,
        ]);
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
        ]);
        $product = Product::factory()->create([
            'region_id' => $region->id,
            'category' => 'coffee',
            'status' => 'published',
        ]);
        $story = Story::factory()->create([
            'region_id' => $region->id,
            'creator_id' => $creator->id,
            'status' => 'published',
            'view_count' => 123,
        ]);
        AuditLog::create([
            'actor_id' => $admin->id,
            'action' => 'dashboard_viewed',
            'auditable_type' => User::class,
            'auditable_id' => $otherUser->id,
            'metadata' => ['note' => 'Test audit event'],
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)
                ->where('message', 'Admin dashboard loaded successfully')
                ->has('data.users.total')
                ->has('data.regions.total')
                ->has('data.products.total')
                ->has('data.stories.total')
                ->has('data.creators.total')
                ->has('data.marketplace.total_products')
                ->has('data.travel.top_regions')
                ->has('data.analytics.latest_audit_events')
                ->has('data.system.application_version')
                ->where('data.users.total', 2)
                ->where('data.regions.total', 1)
                ->where('data.products.total', 1)
                ->where('data.stories.total', 1)
                ->where('data.creators.total', 1)
                ->etc()
            );
    }

    public function test_admin_dashboard_cache_refreshes_after_resource_change(): void
    {
        Cache::store('array')->flush();

        $admin = User::factory()->admin()->create();
        $region = Region::factory()->create(['status' => 'published']);

        Sanctum::actingAs($admin);

        $this->getJson('/api/admin/dashboard')->assertOk();

        Product::factory()->create([
            'region_id' => $region->id,
            'category' => 'coffee',
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('success', true)
                ->where('data.products.total', 1)
                ->etc()
            );
    }

    public function test_non_admin_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/dashboard');

        $response->assertForbidden();
    }
}
