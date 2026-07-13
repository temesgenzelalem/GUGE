<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::factory()->count(8)->create();
        $products = Product::factory()->count(8)->create();

        Story::factory()->count(12)->create()->each(function (Story $story) use ($tags, $products) {
            $story->tags()->sync($tags->random(rand(1, 3))->pluck('id')->toArray());
            $story->products()->sync($products->random(rand(1, 2))->pluck('id')->toArray());
        });
    }
}
