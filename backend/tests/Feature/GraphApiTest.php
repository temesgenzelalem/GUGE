<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GraphApiTest extends TestCase
{
    private Region $region;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->region = Region::factory()->create();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_can_get_related_nodes_for_region(): void
    {
        $response = $this->getJson("/api/regions/{$this->region->slug}/graph/related");

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_can_get_connections_for_region(): void
    {
        $response = $this->getJson("/api/regions/{$this->region->slug}/graph/connections");

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_admin_can_store_region_relationship(): void
    {
        Sanctum::actingAs($this->admin);

        $target = Region::factory()->create();

        $response = $this->postJson("/api/regions/{$this->region->slug}/graph/relationships", [
            'target_type' => 'region',
            'target_id' => $target->id,
            'target_name' => $target->name,
            'weight' => 0.8,
            'metadata' => ['note' => 'connected by culture'],
        ]);

        $response->assertOk()->assertJsonPath('success', true);
    }

    public function test_guest_cannot_store_relationship(): void
    {
        $this->postJson("/api/regions/{$this->region->slug}/graph/relationships", [
            'target_type' => 'region',
            'target_id' => 1,
            'target_name' => 'Test',
        ])->assertUnauthorized();
    }

    public function test_related_nodes_filtered_by_type(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson("/api/regions/{$this->region->slug}/graph/relationships", [
            'target_type' => 'product',
            'target_id' => 1,
            'target_name' => 'Coffee',
        ]);

        $response = $this->getJson("/api/regions/{$this->region->slug}/graph/related?types[]=product");

        $response->assertOk();
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertEquals('product', $item['target_type']);
        }
    }

    public function test_can_get_region_creators(): void
    {
        $response = $this->getJson("/api/regions/{$this->region->slug}/creators");

        $response->assertOk()->assertJsonPath('success', true);
    }
}
