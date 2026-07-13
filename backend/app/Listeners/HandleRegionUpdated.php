<?php

namespace App\Listeners;

use App\Domain\Region\Events\RegionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleRegionUpdated implements ShouldQueue
{
    /**
     * Re-index the updated region and refresh related analytics.
     */
    public function handle(RegionUpdated $event): void
    {
        // Placeholder: re-index in search, refresh knowledge graph.
        // $event->region
    }
}
