<?php

namespace App\Listeners;

use App\Domain\Story\Events\StoryUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStoryUpdated implements ShouldQueue
{
    public function handle(StoryUpdated $event): void
    {
        // Placeholder: re-index in search, refresh recommendation data.
        // $event->story
    }
}
