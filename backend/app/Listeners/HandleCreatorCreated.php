<?php

namespace App\Listeners;

use App\Domain\Creator\Events\CreatorCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleCreatorCreated implements ShouldQueue
{
    /**
     * Index the new creator and update region creator metrics.
     */
    public function handle(CreatorCreated $event): void
    {
        // Placeholder: search indexing, analytics, AI knowledge sync.
        // $event->creator
    }
}
