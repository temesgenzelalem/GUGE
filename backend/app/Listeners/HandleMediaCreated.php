<?php

namespace App\Listeners;

use App\Domain\Media\Events\MediaCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleMediaCreated implements ShouldQueue
{
    public function handle(MediaCreated $event): void
    {
        // Placeholder: future image processing, thumbnail generation, CDN sync.
    }
}
