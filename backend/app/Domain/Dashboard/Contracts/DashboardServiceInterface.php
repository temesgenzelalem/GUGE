<?php

namespace App\Domain\Dashboard\Contracts;

use App\Domain\Dashboard\DTO\DashboardSummary;

interface DashboardServiceInterface
{
    public function getMetrics(): DashboardSummary;
}
