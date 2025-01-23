<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Extractors;

/**
 * Extractor represents an abstraction of a keyword extractor algorithm.
 */
interface Extractor
{
    /**
     * Extract keywords from a text.
     *
     * @param string $text
     * @return array<string, int|float>
     */
    public function extract(string $text): array;
}
