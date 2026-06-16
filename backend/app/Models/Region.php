<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = [
        'name', 'slug', 'zone', 'direction',
        'description', 'tagline', 'wiki_article',
        'image_url', 'tags', 'stats',
    ];

    protected $casts = [
        'tags'  => 'array',
        'stats' => 'array',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
