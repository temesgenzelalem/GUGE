<?php

namespace App\Domain\Recommendation;

use App\Domain\Recommendation\Contracts\RecommendationRepositoryInterface;
use App\Models\Product;

class RecommendationRepository implements RecommendationRepositoryInterface
{
    public function findCandidates(array $context, int $limit = 50): array
    {
        return Product::with('region')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
