<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'zone', 'direction',
        'description', 'tagline', 'wiki_article',
        'image_url', 'tags', 'stats',
        'status', 'featured',
    ];

    protected $casts = [
        'tags' => 'array',
        'stats' => 'array',
        'featured' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function creators(): HasMany
    {
        return $this->hasMany(Creator::class);
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(RegionRelationship::class, 'source_region_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
