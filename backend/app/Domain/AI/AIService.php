<?php

namespace App\Domain\AI;

use App\Domain\AI\Contracts\AIServiceInterface;

class AIService implements AIServiceInterface
{
    public function generateEmbeddings(string $text): array
    {
        return [];
    }

    public function summarize(string $text): string
    {
        return '';
    }

    public function classify(string $text, array $labels): string
    {
        return '';
    }
}
