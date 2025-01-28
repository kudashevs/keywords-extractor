<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Extractors;

final class RakeExtractorStoplist
{
    private const STOPLIST_FILE_PATH = __DIR__ . '/RakeExtractorStoplist.txt';

    /**
     * @var array<array-key, string>
     */
    private array $words;

    public function __construct()
    {
        $this->initWords();
    }

    private function initWords(): void
    {
        $this->words = @file(self::STOPLIST_FILE_PATH, FILE_IGNORE_NEW_LINES) ?: [];
    }

    /**
     * Return a list of stop words.
     *
     * @return array<array-key, string>
     */
    public function getWords(): array
    {
        $filtered = array_filter($this->words, function ($word) {
            return !str_starts_with($word, '#');
        });

        return array_unique($filtered);
    }
}
