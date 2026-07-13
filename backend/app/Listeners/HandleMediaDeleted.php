<?php

namespace App\Listeners;

use App\Domain\Media\Events\MediaDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleMediaDeleted implements ShouldQueue
{
    public function handle(MediaDeleted $event): void
    {
        // Placeholder: future file cleanup from storage/CDN.
    }
}
