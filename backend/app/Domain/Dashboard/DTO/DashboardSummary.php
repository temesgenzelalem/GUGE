<?php

namespace App\Domain\Dashboard\DTO;

class DashboardSummary
{
    public function __construct(
        public array $users,
        public array $regions,
        public array $products,
        public array $stories,
        public array $creators,
        public array $marketplace,
        public array $travel,
        public array $analytics,
        public array $system
    ) {}

    public function toArray(): array
    {
        return [
            'users' => $this->users,
            'regions' => $this->regions,
            'products' => $this->products,
            'stories' => $this->stories,
            'creators' => $this->creators,
            'marketplace' => $this->marketplace,
            'travel' => $this->travel,
            'analytics' => $this->analytics,
            'system' => $this->system,
        ];
    }
}
