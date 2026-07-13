<?php

namespace Database\Seeders;

use App\Models\Creator;
use App\Models\Region;
use Illuminate\Database\Seeder;

class CreatorSeeder extends Seeder
{
    public function run(): void
    {
        $jimma = Region::where('slug', 'jimma')->first();
        $lalibela = Region::where('slug', 'lalibela')->first();
        $harar = Region::where('slug', 'harar')->first();
        $gondar = Region::where('slug', 'gondar')->first();

        $creators = [
            [
                'name' => 'Tigist Alemu',
                'full_name' => 'Tigist Alemu',
                'username' => 'tigist_alemu',
                'slug' => 'tigist-alemu',
                'region_id' => $jimma?->id,
                'role' => 'photographer',
                'bio' => 'Documentary photographer capturing the coffee culture and daily life of southwestern Ethiopia.',
                'status' => 'published',
                'specialties' => ['documentary', 'portrait', 'coffee'],
                'languages' => ['en', 'am'],
                'social_links' => ['https://instagram.com/tigist_alemu'],
                'contact_email' => 'tigist@example.com',
                'rating' => 4.8,
                'review_count' => 24,
                'story_count' => 12,
                'product_count' => 0,
            ],
            [
                'name' => 'Yohannes Tesfaye',
                'full_name' => 'Yohannes Tesfaye',
                'username' => 'yohannes_t',
                'slug' => 'yohannes-tesfaye',
                'region_id' => $lalibela?->id,
                'role' => 'writer',
                'bio' => 'Cultural writer and historian specializing in the ancient churches and traditions of northern Ethiopia.',
                'status' => 'published',
                'specialties' => ['history', 'religion', 'heritage'],
                'languages' => ['en', 'am', 'tig'],
                'social_links' => [],
                'contact_email' => 'yohannes@example.com',
                'rating' => 4.6,
                'review_count' => 18,
                'story_count' => 20,
                'product_count' => 0,
            ],
            [
                'name' => 'Fatuma Omar',
                'full_name' => 'Fatuma Omar',
                'username' => 'fatuma_omar',
                'slug' => 'fatuma-omar',
                'region_id' => $harar?->id,
                'role' => 'videographer',
                'bio' => 'Videographer documenting the unique traditions of Harar, including the famous hyena feeding ritual.',
                'status' => 'published',
                'specialties' => ['documentary', 'travel', 'culture'],
                'languages' => ['en', 'am', 'som'],
                'social_links' => ['https://youtube.com/fatuma_omar'],
                'contact_email' => 'fatuma@example.com',
                'rating' => 4.9,
                'review_count' => 31,
                'story_count' => 8,
                'product_count' => 0,
            ],
            [
                'name' => 'Abebe Girma',
                'full_name' => 'Abebe Girma',
                'username' => 'abebe_girma',
                'slug' => 'abebe-girma',
                'region_id' => $gondar?->id,
                'role' => 'artist',
                'bio' => 'Traditional Ethiopian artist creating paintings inspired by the royal heritage of Gondar.',
                'status' => 'published',
                'specialties' => ['painting', 'religious-art', 'history'],
                'languages' => ['en', 'am'],
                'social_links' => [],
                'contact_email' => 'abebe@example.com',
                'rating' => 4.5,
                'review_count' => 12,
                'story_count' => 6,
                'product_count' => 8,
            ],
        ];

        foreach ($creators as $data) {
            if ($data['region_id'] === null) {
                continue;
            }
            Creator::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
