<?php

namespace App\Listeners;

use App\Domain\Region\Events\RegionDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleRegionDeleted implements ShouldQueue
{
    /**
     * Remove the deleted region from search index and analytics.
     */
    public function handle(RegionDeleted $event): void
    {
        // Placeholder: remove from search index, cleanup related data.
        // $event->region
    }
}
