<?php

namespace App\Domain\Recommendation;

use App\Domain\Recommendation\Contracts\RecommendationRepositoryInterface;
use App\Domain\Recommendation\Contracts\RecommendationServiceInterface;
use App\Domain\Recommendation\Strategies\RecommendationStrategyInterface;

class RecommendationService implements RecommendationServiceInterface
{
    public function __construct(
        protected RecommendationRepositoryInterface $repository,
        protected RecommendationStrategyInterface $strategy,
    ) {}

    public function getRecommendations(array $context, int $limit = 10): array
    {
        $candidates = $this->repository->findCandidates($context, $limit * 5);

        return $this->strategy->recommend($candidates, $context, $limit);
    }
}
