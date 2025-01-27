<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Extractors;

/**
 * Extractor represents an abstraction of a keyword extractor algorithm.
 */
interface Extractor
{
    /**
     * Extract keywords from a text in a format keyword => weigh.
     *
     * @param string $text
     * @return array<string, int|float>
     */
    public function extract(string $text): array;

    /**
     * Extract keywords from a text.
     *
     * @param string $text
     * @return array<array-key, string>
     */
    public function extractWords(string $text): array;

    /**
     * An array of words to add to the result.
     *
     * @param array<array-key, string> $words
     * @return void
     */
    public function addWords(array $words): void;

    /**
     * An array of words to remove from the result.
     *
     * @param array<array-key, string> $words
     * @return void
     */
    public function removeWords(array $words): void;
}
