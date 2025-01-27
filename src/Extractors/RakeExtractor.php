<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Extractors;

use Kudashevs\RakePhp\Rake;

final class RakeExtractor implements Extractor
{
    private Rake $extractor;

    /**
     * @param array{
     *     add?: array<array-key, string>,
     *     remove?: array<array-key, string>,
     *} $options
     */
    public function __construct(array $options = [])
    {
        $this->extractor = new Rake([
            'exclude' => $options['add'] ?? [],
            'include' => $options['remove'] ?? [],
        ]);
    }

    public function extract(string $text): array
    {
        return $this->extractor->extract($text);
    }

    public function extractWords(string $text): array
    {
        $keywords = $this->extract($text);

        return array_keys($keywords);
    }

    /**
     * @param array<array-key, string> $words
     */
    public function addWords(array $words): void
    {
        $this->extractor = $this->cloneAddWords($words);
    }

    private function cloneAddWords(array $words): Rake
    {
        /*
         * This implementation is very tightly coupled to the library's constructor.
         * @todo don't forget to update it when the constructor's options change.
         */
        $newWithAddedWords = (function (array $words) {
            return new Rake([
                'modifiers' => $this->modifiers,
                'sorter' => $this->sorter,
                'include' => $this->options['include'],
                'exclude' => array_merge($words, $this->options['exclude']),
            ]);
        })->call($this->extractor, $words);

        return $newWithAddedWords;
    }

    /**
     * @param array<array-key, string> $words
     */
    public function removeWords(array $words): void
    {
        $this->extractor = $this->cloneRemoveWords($words);
    }

    private function cloneRemoveWords(array $words): Rake
    {
        /*
         * This implementation is very tightly coupled to the library's constructor.
         * @todo don't forget to update it when the constructor's options change.
         */
        $newWithAddedWords = (function (array $words) {
            return new Rake([
                'modifiers' => $this->modifiers,
                'sorter' => $this->sorter,
                'include' => array_merge($words, $this->options['include']),
                'exclude' => $this->options['exclude'],
            ]);
        })->call($this->extractor, $words);

        return $newWithAddedWords;
    }
}
