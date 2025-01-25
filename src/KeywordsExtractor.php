<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor;

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

    public function __construct(array $options = [])
    {
        $this->initOptions($options);

        $this->initExtractor();
    }

    protected function initOptions(array $options): void
    {
        $this->initAddOption($options);
        $this->initRemoveOption($options);
    }

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

    public function generate(string $text): string
    {
        $words = $this->extractor->extract($text);

        return implode(', ', $words);
    }
}
