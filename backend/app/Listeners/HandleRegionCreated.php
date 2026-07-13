<?php

namespace App\Listeners;

use App\Domain\Region\Events\RegionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleRegionCreated implements ShouldQueue
{
    /**
     * Index the new region in the search layer and update analytics.
     *
     * Phase 3+: trigger full-text search indexing, AI knowledge base sync.
     */
    public function handle(RegionCreated $event): void
    {
        // Placeholder: update search index, analytics, AI knowledge graph.
        // $event->region
    }
}
