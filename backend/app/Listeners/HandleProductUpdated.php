<?php

namespace App\Listeners;

use App\Domain\Product\Events\ProductUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleProductUpdated implements ShouldQueue
{
    public function handle(ProductUpdated $event): void
    {
        // Placeholder: re-index in search, refresh marketplace analytics.
        // $event->product
    }
}
