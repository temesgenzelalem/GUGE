<?php

namespace App\Domain\AI\Contracts;

interface AIServiceInterface
{
    public function generateEmbeddings(string $text): array;

    public function summarize(string $text): string;

    public function classify(string $text, array $labels): string;
}
