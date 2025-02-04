<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Extractors;

use Kudashevs\RakePhp\Rake;

final class RakeExtractor implements Extractor
{
    private Rake $extractor;

    /**
     * @param array{
     *     add_words?: array<array-key, string>,
     *     remove_words?: array<array-key, string>,
     *} $options
     */
    public function __construct(array $options = [])
    {
        $this->initExtractor($options);
    }

    /**
     * @param array{add_words?: array<array-key, string>, remove_words?: array<array-key, string>} $options
     * @return void
     */
    private function initExtractor(array $options): void
    {
        $exclude = array_merge($this->retrieveDefaultAddWords(), $options['add_words'] ?? []);
        $include = array_merge($this->retrieveDefaultRemoveWords(), $options['remove_words'] ?? []);

        $this->extractor = new Rake([
            'exclude' => $exclude,
            'include' => $include,
        ]);
    }

    /**
     * @return array<array-key, string>
     */
    private function retrieveDefaultAddWords(): array
    {
        return [];
    }

    /**
     * @return array<array-key, string>
     */
    private function retrieveDefaultRemoveWords(): array
    {
        return (new RakeExtractorStoplist())->getWords();
    }

    /**
     * @inheritDoc
     */
    public function extract(string $text): array
    {
        return $this->extractor->extract($text);
    }

    /**
     * @inheritDoc
     */
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
        $this->extractor = $this->cloneExtractor([
            'exclude' => $words,
        ]);
    }

    /**
     * @param array<array-key, string> $words
     */
    public function removeWords(array $words): void
    {
        $this->extractor = $this->cloneExtractor([
            'include' => $words,
        ]);
    }

    private function cloneExtractor(array $arguments): Rake
    {
        return (function (array $arguments) {
            /*
             * This implementation is very tightly coupled to the library's constructor.
             * @todo don't forget to update it when the constructor's options change.
             */
            return new Rake([
                'modifiers' => $this->modifiers,
                'sorter' => $this->sorter,
                'include' => array_merge($arguments['include'] ?? [], $this->options['include']),
                'exclude' => array_merge($arguments['exclude'] ?? [], $this->options['exclude']),
            ]);
        })->call($this->extractor, $arguments);
    }
}
