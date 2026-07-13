<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Creator extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'full_name', 'username', 'slug', 'region_id', 'role', 'bio',
        'status', 'specialties', 'languages', 'social_links', 'contact_email',
        'website_url', 'portfolio_url', 'wiki_article', 'image_url',
        'rating', 'review_count', 'story_count', 'product_count',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'specialties' => 'array',
        'languages' => 'array',
        'social_links' => 'array',
        'rating' => 'float',
        'review_count' => 'integer',
        'story_count' => 'integer',
        'product_count' => 'integer',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
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
