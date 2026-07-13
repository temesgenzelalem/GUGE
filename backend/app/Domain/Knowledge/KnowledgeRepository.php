<?php

namespace App\Domain\Knowledge;

use App\Domain\Knowledge\Contracts\KnowledgeRepositoryInterface;

class KnowledgeRepository implements KnowledgeRepositoryInterface
{
    public function fetchKnowledge(array $filters = []): array
    {
        return [
            'source' => 'knowledge_graph',
            'filters' => $filters,
            'items' => [],
        ];
    }
}
