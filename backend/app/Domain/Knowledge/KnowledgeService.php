<?php

namespace App\Domain\Knowledge;

use App\Domain\Knowledge\Contracts\KnowledgeRepositoryInterface;

class KnowledgeService
{
    public function __construct(protected KnowledgeRepositoryInterface $repository) {}

    public function getKnowledge(array $filters = []): array
    {
        return $this->repository->fetchKnowledge($filters);
    }
}
