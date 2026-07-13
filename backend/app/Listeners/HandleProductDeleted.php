<?php

namespace App\Listeners;

use App\Domain\Product\Events\ProductDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleProductDeleted implements ShouldQueue
{
    public function handle(ProductDeleted $event): void
    {
        // Placeholder: remove from search index, update marketplace metrics.
        // $event->product
    }
}
