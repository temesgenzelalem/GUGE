<?php

namespace App\Listeners;

use App\Domain\Creator\Events\CreatorDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleCreatorDeleted implements ShouldQueue
{
    public function handle(CreatorDeleted $event): void
    {
        // Placeholder: remove from search index, cleanup analytics.
        // $event->creator
    }
}
