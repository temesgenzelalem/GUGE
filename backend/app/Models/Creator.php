<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Creator extends Model
{
    protected $fillable = [
        'name', 'slug', 'role', 'bio',
        'region_coverage', 'wiki_article',
        'image_url', 'contact_email',
    ];

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
