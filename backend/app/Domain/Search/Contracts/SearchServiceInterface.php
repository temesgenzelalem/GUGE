<?php

namespace App\Domain\Search\Contracts;

interface SearchServiceInterface
{
    public function search(string $query, array $filters = []): array;
}
