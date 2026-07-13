<?php

namespace App\Domain\Recommendation\Contracts;

interface RecommendationRepositoryInterface
{
    public function findCandidates(array $context, int $limit = 50): array;
}
