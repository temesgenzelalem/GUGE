<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Story extends Model
{
    protected $fillable = [
        'title', 'slug', 'region_id', 'creator_id', 'type',
        'excerpt', 'body', 'wiki_article',
        'image_url', 'read_minutes', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
