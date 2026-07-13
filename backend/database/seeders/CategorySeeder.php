<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee',          'slug' => 'coffee',          'description' => 'Ethiopian coffee varieties, farms, and ceremonies'],
            ['name' => 'Handicrafts',     'slug' => 'handicrafts',     'description' => 'Traditional handmade crafts, weaving, and pottery'],
            ['name' => 'Spices',          'slug' => 'spices',          'description' => 'Ethiopian spices, berbere, mitmita, and more'],
            ['name' => 'Textiles',        'slug' => 'textiles',        'description' => 'Traditional Ethiopian fabrics and clothing'],
            ['name' => 'Honey',           'slug' => 'honey',           'description' => 'Pure Ethiopian forest honey'],
            ['name' => 'Food',            'slug' => 'food',            'description' => 'Traditional Ethiopian food and ingredients'],
            ['name' => 'Jewelry',         'slug' => 'jewelry',         'description' => 'Handcrafted Ethiopian jewelry and accessories'],
            ['name' => 'Art',             'slug' => 'art',             'description' => 'Ethiopian paintings, religious art, and sculptures'],
            ['name' => 'Tourism',         'slug' => 'tourism',         'description' => 'Travel guides, tour packages, and experiences'],
            ['name' => 'Culture',         'slug' => 'culture',         'description' => 'Cultural traditions, festivals, and ceremonies'],
            ['name' => 'History',         'slug' => 'history',         'description' => 'Historical sites, archaeology, and heritage'],
            ['name' => 'Nature',          'slug' => 'nature',          'description' => 'National parks, wildlife, and landscapes'],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
