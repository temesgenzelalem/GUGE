<?php

namespace App\Listeners;

use App\Domain\Product\Events\ProductCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleProductCreated implements ShouldQueue
{
    /**
     * Index the new product and update marketplace metrics.
     */
    public function handle(ProductCreated $event): void
    {
        // Placeholder: search indexing, marketplace analytics.
        // $event->product
    }
}
