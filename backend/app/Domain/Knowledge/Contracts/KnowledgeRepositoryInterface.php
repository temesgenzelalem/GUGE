<?php

namespace App\Domain\Knowledge\Contracts;

interface KnowledgeRepositoryInterface
{
    public function fetchKnowledge(array $filters = []): array;
}
