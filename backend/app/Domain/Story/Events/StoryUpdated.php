<?php

namespace App\Domain\Story\Events;

use App\Models\Story;
use Illuminate\Queue\SerializesModels;

class StoryUpdated
{
    use SerializesModels;

    public function __construct(public Story $story) {}
}
