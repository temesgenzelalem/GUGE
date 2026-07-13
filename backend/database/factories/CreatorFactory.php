<?php

namespace Database\Factories;

use App\Models\Creator;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreatorFactory extends Factory
{
    protected $model = Creator::class;

    public function definition(): array
    {
        $fullName = $this->faker->unique()->name();

        return [
            'name' => $fullName,
            'full_name' => $fullName,
            'username' => $this->faker->unique()->userName(),
            'slug' => $this->faker->unique()->slug(),
            'region_id' => Region::factory(),
            'role' => $this->faker->randomElement(['photographer', 'writer', 'videographer', 'artist']),
            'bio' => $this->faker->paragraph(2),
            'status' => 'published',
            'specialties' => $this->faker->randomElements(['portrait', 'landscape', 'travel', 'documentary', 'food'], 3),
            'languages' => $this->faker->randomElements(['en', 'am', 'orm', 'tig', 'som'], 2),
            'social_links' => ['https://instagram.com/'.$this->faker->userName()],
            'contact_email' => $this->faker->unique()->safeEmail(),
            'website_url' => $this->faker->url(),
            'portfolio_url' => $this->faker->url(),
            'wiki_article' => $this->faker->slug(),
            'image_url' => $this->faker->imageUrl(400, 400, 'people'),
            'rating' => $this->faker->randomFloat(1, 0, 5),
            'review_count' => $this->faker->numberBetween(0, 120),
            'story_count' => $this->faker->numberBetween(0, 30),
            'product_count' => $this->faker->numberBetween(0, 15),
            'meta_title' => $this->faker->sentence(6),
            'meta_description' => $this->faker->sentence(12),
        ];
    }
}
