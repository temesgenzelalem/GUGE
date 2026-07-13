<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            [
                'name' => 'Lalibela',
                'slug' => 'lalibela',
                'zone' => 'North Wollo',
                'direction' => 'north',
                'description' => 'Home to the rock-hewn churches of Lalibela, a UNESCO World Heritage Site and the spiritual heart of Ethiopian Christianity.',
                'tagline' => 'Where stone meets the divine',
                'wiki_article' => 'Lalibela',
                'tags' => ['heritage', 'religion', 'tourism', 'churches'],
                'stats' => [
                    ['label' => 'Altitude', 'value' => '2,500m'],
                    ['label' => 'Churches', 'value' => '11'],
                ],
                'status' => 'published',
                'featured' => true,
            ],
            [
                'name' => 'Jimma',
                'slug' => 'jimma',
                'zone' => 'Jimma',
                'direction' => 'west',
                'description' => 'The birthplace of coffee, Jimma is the cultural and commercial capital of southwest Ethiopia, surrounded by lush coffee forests.',
                'tagline' => 'The birthplace of coffee',
                'wiki_article' => 'Jimma',
                'tags' => ['coffee', 'culture', 'forest', 'market'],
                'stats' => [
                    ['label' => 'Altitude', 'value' => '1,700m'],
                    ['label' => 'Famous For', 'value' => 'Coffee'],
                ],
                'status' => 'published',
                'featured' => true,
            ],
            [
                'name' => 'Harar',
                'slug' => 'harar',
                'zone' => 'Harari',
                'direction' => 'east',
                'description' => 'A walled city with over 82 mosques, Harar is one of Islam\'s holiest cities and famous for its hyena feeding ritual.',
                'tagline' => 'Ancient walls, living traditions',
                'wiki_article' => 'Harar',
                'tags' => ['heritage', 'islam', 'hyenas', 'market', 'walls'],
                'stats' => [
                    ['label' => 'Mosques', 'value' => '82+'],
                    ['label' => 'UNESCO', 'value' => 'World Heritage'],
                ],
                'status' => 'published',
                'featured' => true,
            ],
            [
                'name' => 'Gondar',
                'slug' => 'gondar',
                'zone' => 'North Gondar',
                'direction' => 'north',
                'description' => 'The royal capital of Ethiopia from the 17th to 19th centuries, home to the Fasil Ghebbi fortress complex.',
                'tagline' => 'The Camelot of Africa',
                'wiki_article' => 'Gondar',
                'tags' => ['castles', 'royalty', 'history', 'heritage'],
                'stats' => [
                    ['label' => 'Altitude', 'value' => '2,133m'],
                    ['label' => 'Castles', 'value' => '6+'],
                ],
                'status' => 'published',
                'featured' => false,
            ],
            [
                'name' => 'Axum',
                'slug' => 'axum',
                'zone' => 'Central Tigray',
                'direction' => 'north',
                'description' => 'The ancient capital of the Aksumite Empire, home to towering obelisks, the Ark of the Covenant, and millennia of history.',
                'tagline' => 'Where history reaches the sky',
                'wiki_article' => 'Axum',
                'tags' => ['obelisks', 'history', 'archaeology', 'ark'],
                'stats' => [
                    ['label' => 'Altitude', 'value' => '2,131m'],
                    ['label' => 'UNESCO', 'value' => 'World Heritage'],
                ],
                'status' => 'published',
                'featured' => true,
            ],
            [
                'name' => 'Bahir Dar',
                'slug' => 'bahir-dar',
                'zone' => 'South Gondar',
                'direction' => 'north',
                'description' => 'Gateway to Lake Tana and the Blue Nile Falls, Bahir Dar is a vibrant lakeside city surrounded by ancient island monasteries.',
                'tagline' => 'Where the Blue Nile begins',
                'wiki_article' => 'Bahir_Dar',
                'tags' => ['lake', 'waterfalls', 'monasteries', 'nile'],
                'stats' => [
                    ['label' => 'Lake Tana', 'value' => 'Largest lake in Ethiopia'],
                    ['label' => 'Monasteries', 'value' => '20+ islands'],
                ],
                'status' => 'published',
                'featured' => false,
            ],
            [
                'name' => 'Hawassa',
                'slug' => 'hawassa',
                'zone' => 'Sidama',
                'direction' => 'south',
                'description' => 'A city on the shores of Lake Hawassa, famous for its fish market, pelicans, and gateway to the Rift Valley lakes.',
                'tagline' => 'Lakeside serenity in the Rift Valley',
                'wiki_article' => 'Hawassa',
                'tags' => ['lake', 'fish', 'rift-valley', 'birds'],
                'stats' => [
                    ['label' => 'Altitude', 'value' => '1,708m'],
                    ['label' => 'Lake', 'value' => 'Lake Hawassa'],
                ],
                'status' => 'published',
                'featured' => false,
            ],
            [
                'name' => 'Dire Dawa',
                'slug' => 'dire-dawa',
                'zone' => 'Dire Dawa',
                'direction' => 'east',
                'description' => 'Ethiopia\'s second largest city, a vibrant trade hub blending Ethiopian and colonial architecture, gateway to Eastern Ethiopia.',
                'tagline' => 'Ethiopia\'s crossroads of cultures',
                'wiki_article' => 'Dire_Dawa',
                'tags' => ['trade', 'culture', 'rail', 'east'],
                'stats' => [
                    ['label' => 'Altitude', 'value' => '1,173m'],
                    ['label' => 'Known For', 'value' => 'Trade hub'],
                ],
                'status' => 'published',
                'featured' => false,
            ],
        ];

        foreach ($regions as $data) {
            Region::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
