<?php

namespace App\Listeners;

use App\Domain\Creator\Events\CreatorUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleCreatorUpdated implements ShouldQueue
{
    public function handle(CreatorUpdated $event): void
    {
        // Placeholder: re-index in search, refresh knowledge graph.
        // $event->creator
    }
}
