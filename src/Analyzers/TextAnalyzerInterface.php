<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsGenerator\Analyzers;

interface TextAnalyzerInterface
{
    /**
     * Analyze a text and return sorted array of words.
     *
     * @param string $text
     * @return array
     */
    public function analyze(string $text): array;
}
