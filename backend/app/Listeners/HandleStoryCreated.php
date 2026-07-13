<?php

namespace App\Listeners;

use App\Domain\Story\Events\StoryCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleStoryCreated implements ShouldQueue
{
    /**
     * Index the new story, notify subscribers, update creator story count.
     */
    public function handle(StoryCreated $event): void
    {
        // Placeholder: search indexing, creator metrics update, notifications.
        // $event->story
    }
}
