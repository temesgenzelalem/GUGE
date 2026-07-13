<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $filename = $this->faker->slug(2).'.jpg';

        return [
            'uuid' => (string) Str::uuid(),
            'filename' => $filename,
            'path' => 'uploads/'.$filename,
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(10000, 5000000),
            'metadata' => ['width' => 1200, 'height' => 800],
            'gallery' => false,
            'uploaded_by' => null,
        ];
    }

    public function gallery(): self
    {
        return $this->state(fn (array $attributes) => [
            'gallery' => true,
        ]);
    }

    public function uploadedBy(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'uploaded_by' => $user->id,
        ]);
    }
}
