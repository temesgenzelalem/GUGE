<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RegionRelationship extends Model
{
    protected $fillable = [
        'source_region_id',
        'target_type',
        'target_id',
        'target_name',
        'weight',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'source_region_id');
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }
}
