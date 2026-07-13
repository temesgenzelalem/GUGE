<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'region_id', 'category',
        'description', 'story', 'wiki_article',
        'image_url', 'tags', 'how_to_order',
        'status', 'featured', 'hidden',
    ];

    protected $casts = [
        'tags' => 'array',
        'featured' => 'boolean',
        'hidden' => 'boolean',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function stories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
