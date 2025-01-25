<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor;

use InvalidArgumentException;
use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionType;
use Kudashevs\KeywordsExtractor\Extractors\Extractor;
use Kudashevs\KeywordsExtractor\Extractors\RakeExtractor;

class KeywordsExtractor
{
    protected const DEFAULT_EXTRACTOR = RakeExtractor::class;

    protected Extractor $extractor;

    protected array $options = [
        'add' => [],
        'remove' => [],
    ];

    /**
     * 'add_words'      string|array A string or an array with words to add to the result (if they are ignored by an Extractor).
     * 'remove_words'   string|array An string or an array with words to remove from the result (if they are not ignored by an Extractor).
     *
     * @param array{
     *     add_words?: string|array<array-key, string>,
     *     remove_words?: string|array<array-key, string>,
     * } $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $options = [])
    {
        $this->initOptions($options);

        $this->initExtractor();
    }

    /**
     * @param array<array-key, string|array<array-key, string>> $options
     */
    protected function initOptions(array $options): void
    {
        $this->initAddOption($options);
        $this->initRemoveOption($options);
    }

    /**
     * @param array<array-key, string|array<array-key, string>> $options
     */
    protected function initAddOption(array $options): void
    {
        if (
            isset($options['add_words'])
            && !is_string($options['add_words'])
            && !is_array($options['add_words'])
        ) {
            throw new InvalidOptionType('The add_words option must be a string or an array.');
        }

        $this->options['add'] = (is_string($options['add_words']))
            ? [$options['add_words']]
            : $options['add_words'];
    }

    /**
     * @param array<array-key, string|array<array-key, string>> $options
     */
    protected function initRemoveOption(array $options): void
    {
        if (
            isset($options['remove_words'])
            && !is_string($options['remove_words'])
            && !is_array($options['remove_words'])
        ) {
            throw new InvalidOptionType('The remove_words option must be a string or an array.');
        }

        $this->options['remove'] = (is_string($options['remove_words']))
            ? [$options['remove_words']]
            : $options['remove_words'];
    }

    protected function initExtractor(): void
    {
        $this->extractor = new (self::DEFAULT_EXTRACTOR)($this->options);
    }

    public function extract(string $text): string
    {
        $words = $this->extractor->extract($text);

        return implode(', ', $words);
    }
}
