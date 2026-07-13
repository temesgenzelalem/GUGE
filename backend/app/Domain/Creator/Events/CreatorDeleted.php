<?php

namespace App\Domain\Creator\Events;

use App\Models\Creator;
use Illuminate\Queue\SerializesModels;

class CreatorDeleted
{
    use SerializesModels;

    public function __construct(public Creator $creator) {}
}
