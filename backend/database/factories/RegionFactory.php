<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->city().'_'.uniqid(),
            'slug' => $this->faker->unique()->slug(),
            'zone' => $this->faker->state(),
            'direction' => $this->faker->randomElement(['north', 'south', 'east', 'west']),
            'description' => $this->faker->paragraph(3),
            'tagline' => $this->faker->sentence(6),
            'wiki_article' => $this->faker->slug(),
            'image_url' => $this->faker->imageUrl(1200, 800, 'nature'),
            'tags' => ['sample', 'region'],
            'stats' => [['label' => 'Featured', 'value' => 'Yes']],
            'status' => 'published',
            'featured' => false,
        ];
    }

    public function featured(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'featured',
            'featured' => true,
        ]);
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
