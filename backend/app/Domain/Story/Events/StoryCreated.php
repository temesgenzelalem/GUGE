<?php

namespace App\Domain\Story\Events;

use App\Models\Story;
use Illuminate\Queue\SerializesModels;

class StoryCreated
{
    use SerializesModels;

    public function __construct(public Story $story) {}
}
