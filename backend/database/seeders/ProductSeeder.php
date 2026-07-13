<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Region;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $jimma = Region::where('slug', 'jimma')->first();
        $harar = Region::where('slug', 'harar')->first();
        $gondar = Region::where('slug', 'gondar')->first();
        $lalibela = Region::where('slug', 'lalibela')->first();
        $hawassa = Region::where('slug', 'hawassa')->first();

        $products = [
            [
                'name' => 'Jimma Forest Coffee',
                'slug' => 'jimma-forest-coffee',
                'region_id' => $jimma?->id,
                'category' => 'coffee',
                'description' => 'Wild-harvested coffee from the ancient forests of Jimma. Earthy, full-bodied, with notes of dark chocolate and spice.',
                'story' => 'Jimma is the ancestral home of coffee. These beans grow wild in forests that have never been cultivated.',
                'wiki_article' => 'Coffea_arabica',
                'tags' => ['coffee', 'organic', 'wild-harvest', 'jimma'],
                'how_to_order' => 'Contact via email or visit the Jimma Cooperative directly.',
                'status' => 'published',
                'featured' => true,
                'hidden' => false,
            ],
            [
                'name' => 'Harari Basket Weaving',
                'slug' => 'harari-basket-weaving',
                'region_id' => $harar?->id,
                'category' => 'handicrafts',
                'description' => 'Intricate handwoven baskets made by Harari women using traditional patterns passed down through generations.',
                'story' => 'Basket weaving in Harar is a centuries-old tradition. Each pattern tells a story of the city\'s history.',
                'wiki_article' => 'Harar',
                'tags' => ['handicrafts', 'baskets', 'harar', 'traditional'],
                'how_to_order' => 'Available at the Harar market or contact artisans directly.',
                'status' => 'published',
                'featured' => true,
                'hidden' => false,
            ],
            [
                'name' => 'Berbere Spice Mix',
                'slug' => 'berbere-spice-mix',
                'region_id' => $gondar?->id,
                'category' => 'spices',
                'description' => 'Authentic berbere spice blend from Gondar, the foundation of Ethiopian cuisine. A complex mix of chili, fenugreek, coriander, and more.',
                'story' => 'Gondar berbere is considered among the finest in Ethiopia, with a recipe refined over centuries of royal cuisine.',
                'wiki_article' => 'Berbere',
                'tags' => ['spices', 'berbere', 'cooking', 'gondar'],
                'how_to_order' => 'Order online or visit the Gondar market.',
                'status' => 'published',
                'featured' => false,
                'hidden' => false,
            ],
            [
                'name' => 'Lalibela Honey',
                'slug' => 'lalibela-honey',
                'region_id' => $lalibela?->id,
                'category' => 'honey',
                'description' => 'Pure forest honey from the highland forests surrounding Lalibela. Rich, aromatic, and minimally processed.',
                'story' => 'Beekeeping has been practiced near Lalibela\'s monasteries for centuries. Monks used honey in religious ceremonies.',
                'wiki_article' => 'Ethiopian_honey',
                'tags' => ['honey', 'organic', 'lalibela', 'forest'],
                'how_to_order' => 'Available through the Lalibela Beekeepers Association.',
                'status' => 'published',
                'featured' => false,
                'hidden' => false,
            ],
            [
                'name' => 'Lake Hawassa Tilapia',
                'slug' => 'lake-hawassa-tilapia',
                'region_id' => $hawassa?->id,
                'category' => 'food',
                'description' => 'Fresh Nile tilapia from Lake Hawassa, served grilled with injera at the famous lakeside fish market.',
                'story' => 'The Hawassa fish market is a daily spectacle where pelicans and marabou storks compete with fishermen at the water\'s edge.',
                'wiki_article' => 'Lake_Awasa',
                'tags' => ['food', 'fish', 'tilapia', 'lake', 'hawassa'],
                'how_to_order' => 'Visit the Hawassa fish market early morning for freshest catch.',
                'status' => 'published',
                'featured' => false,
                'hidden' => false,
            ],
        ];

        foreach ($products as $data) {
            if ($data['region_id'] === null) {
                continue;
            }
            Product::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
