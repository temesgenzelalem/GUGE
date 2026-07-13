<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'region_id',
        'creator_id',
        'category',
        'type',
        'excerpt',
        'content',
        'body',
        'wiki_article',
        'featured_image',
        'image_url',
        'gallery',
        'status',
        'featured',
        'read_minutes',
        'language',
        'seo_title',
        'seo_description',
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'gallery' => 'array',
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'read_minutes' => 'integer',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
