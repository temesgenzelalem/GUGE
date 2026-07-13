<?php

namespace App\Domain\Dashboard;

use App\Domain\Dashboard\Contracts\DashboardServiceInterface;
use App\Domain\Dashboard\DTO\DashboardSummary;
use App\Domain\Dashboard\Metrics\AnalyticsMetricsService;
use App\Domain\Dashboard\Metrics\CreatorMetricsService;
use App\Domain\Dashboard\Metrics\MarketplaceMetricsService;
use App\Domain\Dashboard\Metrics\ProductMetricsService;
use App\Domain\Dashboard\Metrics\RegionMetricsService;
use App\Domain\Dashboard\Metrics\StoryMetricsService;
use App\Domain\Dashboard\Metrics\SystemMetricsService;
use App\Domain\Dashboard\Metrics\TravelMetricsService;
use App\Domain\Dashboard\Metrics\UserMetricsService;

class DashboardService implements DashboardServiceInterface
{
    public function __construct(
        protected UserMetricsService $userMetricsService,
        protected RegionMetricsService $regionMetricsService,
        protected ProductMetricsService $productMetricsService,
        protected StoryMetricsService $storyMetricsService,
        protected CreatorMetricsService $creatorMetricsService,
        protected MarketplaceMetricsService $marketplaceMetricsService,
        protected TravelMetricsService $travelMetricsService,
        protected AnalyticsMetricsService $analyticsMetricsService,
        protected SystemMetricsService $systemMetricsService,
    ) {}

    public function getMetrics(): DashboardSummary
    {
        return new DashboardSummary(
            users: $this->userMetricsService->getMetrics(),
            regions: $this->regionMetricsService->getMetrics(),
            products: $this->productMetricsService->getMetrics(),
            stories: $this->storyMetricsService->getMetrics(),
            creators: $this->creatorMetricsService->getMetrics(),
            marketplace: $this->marketplaceMetricsService->getMetrics(),
            travel: $this->travelMetricsService->getMetrics(),
            analytics: $this->analyticsMetricsService->getMetrics(),
            system: $this->systemMetricsService->getMetrics(),
        );
    }
}
