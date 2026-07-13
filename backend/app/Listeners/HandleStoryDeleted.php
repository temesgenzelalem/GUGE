<?php

namespace App\Listeners;

use App\Domain\Story\Events\StoryDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStoryDeleted implements ShouldQueue
{
    public function handle(StoryDeleted $event): void
    {
        // Placeholder: remove from search index, update creator story count.
        // $event->story
    }
}
