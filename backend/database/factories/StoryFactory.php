<?php

namespace Database\Factories;

use App\Models\Creator;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoryFactory extends Factory
{
    protected $model = Story::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(5),
            'slug' => $this->faker->unique()->slug(),
            'region_id' => Region::factory(),
            'creator_id' => Creator::factory(),
            'type' => $this->faker->randomElement(['travel', 'product-origin', 'culture', 'festival', 'history', 'craft']),
            'excerpt' => $this->faker->paragraph(1),
            'body' => $this->faker->paragraphs(5, true),
            'wiki_article' => $this->faker->slug(),
            'image_url' => $this->faker->imageUrl(1200, 800, 'nature'),
            'read_minutes' => $this->faker->numberBetween(3, 15),
            'status' => 'published',
            'featured' => false,
            'language' => 'en',
            'published_at' => $this->faker->dateTime(),
        ];
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function featured(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'featured' => true,
        ]);
    }
}
