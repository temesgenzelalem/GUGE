<?php

namespace App\Domain\Region\Events;

use App\Models\Region;
use Illuminate\Queue\SerializesModels;

class RegionCreated
{
    use SerializesModels;

    public function __construct(public Region $region) {}

    public function broadcastOn(): array
    {
        return [];
    }
}
