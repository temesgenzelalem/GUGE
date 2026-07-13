<?php

namespace App\Domain\Recommendation\Contracts;

interface RecommendationServiceInterface
{
    public function getRecommendations(array $context, int $limit = 10): array;
}
