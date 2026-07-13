<?php

namespace App\Domain\Recommendation\Strategies;

interface RecommendationStrategyInterface
{
    public function recommend(array $candidates, array $context, int $limit = 10): array;
}
