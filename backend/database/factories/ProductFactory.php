<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'slug' => $this->faker->unique()->slug(),
            'region_id' => Region::factory(),
            'category' => $this->faker->randomElement(['coffee', 'food', 'craft', 'honey', 'clothing']),
            'description' => $this->faker->paragraph(2),
            'story' => $this->faker->paragraph(3),
            'wiki_article' => $this->faker->slug(),
            'image_url' => $this->faker->imageUrl(800, 600, 'business'),
            'tags' => ['sample', 'product'],
            'how_to_order' => 'Contact via email or phone',
            'status' => 'published',
            'featured' => false,
            'hidden' => false,
        ];
    }

    public function coffee(): self
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'coffee',
        ]);
    }

    public function hidden(): self
    {
        return $this->state(fn (array $attributes) => [
            'hidden' => true,
        ]);
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
