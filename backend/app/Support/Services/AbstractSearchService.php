<?php

namespace App\Support\Services;

abstract class AbstractSearchService
{
    abstract public function search(string $query, array $filters = []): array;
}
