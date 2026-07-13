<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'filename',
        'original_filename',
        'path',
        'disk',
        'url',
        'mime_type',
        'size',
        'metadata',
        'conversions',
        'collection',
        'gallery',
        'uploaded_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'conversions' => 'array',
        'gallery' => 'boolean',
        'size' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $media) {
            if (empty($media->uuid)) {
                $media->uuid = (string) Str::uuid();
            }
        });
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
