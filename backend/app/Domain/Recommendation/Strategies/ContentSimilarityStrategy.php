<?php

namespace App\Domain\Recommendation\Strategies;

class ContentSimilarityStrategy implements RecommendationStrategyInterface
{
    public function recommend(array $candidates, array $context, int $limit = 10): array
    {
        return array_slice($candidates, 0, $limit);
    }
}
