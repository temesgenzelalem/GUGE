<?php

namespace Database\Factories;

use App\Models\Region;
use App\Models\RegionRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionRelationshipFactory extends Factory
{
    protected $model = RegionRelationship::class;

    public function definition(): array
    {
        return [
            'source_region_id' => Region::factory(),
            'target_type' => $this->faker->randomElement(['product', 'story', 'creator', 'region']),
            'target_id' => $this->faker->numberBetween(1, 50),
            'target_name' => $this->faker->words(3, true),
            'weight' => $this->faker->randomFloat(2, 0.1, 1.0),
            'metadata' => ['note' => $this->faker->sentence()],
        ];
    }
}
