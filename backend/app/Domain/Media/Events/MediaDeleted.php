<?php

namespace App\Domain\Media\Events;

use App\Models\Media;
use Illuminate\Queue\SerializesModels;

class MediaDeleted
{
    use SerializesModels;

    public function __construct(public Media $media) {}
}
