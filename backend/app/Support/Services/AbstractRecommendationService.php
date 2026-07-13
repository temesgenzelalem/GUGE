<?php

namespace App\Support\Services;

abstract class AbstractRecommendationService
{
    abstract public function recommend(array $context, int $limit = 10): array;
}
