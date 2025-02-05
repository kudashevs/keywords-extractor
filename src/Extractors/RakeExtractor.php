<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Extractors;

use Kudashevs\KeywordsExtractor\Collections\CacheWordsCollection;
use Kudashevs\KeywordsExtractor\Collections\WordsCollection;
use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;
use Kudashevs\RakePhp\Rake;

final class RakeExtractor implements Extractor
{
    /** @var class-string<WordsCollection> */
    private const DEFAULT_COLLECTION_CLASS = CacheWordsCollection::class;

    private Rake $extractor;

    private array $options = [
        'assets_path' => __DIR__ . '/../../assets/',
        'default_exclusions' => true,
        'default_inclusions' => true,
    ];

    /**
     * @param array{
     *     add_words?: array<array-key, string>,
     *     remove_words?: array<array-key, string>,
     *     assets_path?: string,
     *     default_exclusions?: bool,
     *     default_inclusions?: bool,
     *} $options
     *
     * @throws InvalidOptionValue
     */
    public function __construct(array $options = [])
    {
        $this->initOptions($options);
        $this->initExtractor($options);
    }

    private function initOptions(array $options): void
    {
        $allowedOptions = $this->retrieveAllowedOptions($options);

        $this->options = array_merge($this->options, $allowedOptions);
    }

    private function retrieveAllowedOptions(array $options): array
    {
        return array_intersect_key($options, $this->options);
    }

    /**
     * @param array{add_words?: array<array-key, string>, remove_words?: array<array-key, string>} $options
     * @return void
     */
    private function initExtractor(array $options): void
    {
        $exclude = array_merge($this->getDefaultExclusions(), $options['add_words'] ?? []);
        $include = array_merge($this->getDefaultInclusions(), $options['remove_words'] ?? []);

        $this->extractor = new Rake([
            'exclude' => $exclude,
            'include' => $include,
        ]);
    }

    /**
     * @return array<array-key, string>
     */
    private function getDefaultExclusions(): array
    {
        if (isset($this->options['default_exclusions']) && $this->options['default_exclusions'] === true) {
            $defaultAddWords = new (self::DEFAULT_COLLECTION_CLASS)(
                'exclude_words',
                ['rake_exclude'],
                $this->options['assets_path'],
            );

            return $defaultAddWords->getWords();
        }

        return [];
    }

    /**
     * @return array<array-key, string>
     */
    private function getDefaultInclusions(): array
    {
        if (isset($this->options['default_inclusions']) && $this->options['default_inclusions'] === true) {
            $defaultRemoveWords = new (self::DEFAULT_COLLECTION_CLASS)(
                'include_words',
                ['rake_include', 'adverbs', 'verbs'],
                $this->options['assets_path'],
            );

            return $defaultRemoveWords->getWords();
        }

        return [];
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
