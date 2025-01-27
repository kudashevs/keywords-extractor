<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor;

use InvalidArgumentException;
use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionType;
use Kudashevs\KeywordsExtractor\Extractors\Extractor;
use Kudashevs\KeywordsExtractor\Extractors\RakeExtractor;
use Kudashevs\KeywordsExtractor\Limiters\LengthLimiter;
use Kudashevs\KeywordsExtractor\Limiters\Limiter;

class KeywordsExtractor
{
    /** @var class-string<Extractor> */
    protected const DEFAULT_EXTRACTOR = RakeExtractor::class;

    /** @var class-string<Limiter> */
    protected const DEFAULT_LIMITER = LengthLimiter::class;

    protected Extractor $extractor;

    protected Limiter $limiter;

    /**
     * @var array{
     *     length: int,
     *     add: array<array-key, string>,
     *     remove: array<array-key, string>,
     * }
     */
    protected array $options = [
        'length' => 0, // by default, the result is limitless
        'add' => [],
        'remove' => [],
    ];

    /**
     * 'extractor'      Extractor An instance of an Extractor (@see Extractor::class).
     * 'add_words'      string|array A string or an array of words to add to the result (if they are ignored by an Extractor).
     * 'remove_words'   string|array A string or an array of words to remove from the result (if they are not ignored by an Extractor).
     * 'limiter'        Limiter An instance of a Limiter (@see Limiter::class).
     * 'limit_length'   int An integer defines the maximum length of the result (is used only when a Limiter is not provided).
     *
     * @param array{
     *     extractor?: Extractor,
     *     add_words?: string|array<array-key, string>,
     *     remove_words?: string|array<array-key, string>,
     *     limiter?: Limiter,
     *     limit_length?: int,
     * } $options
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $options = [])
    {
        $this->initOptions($options);

        $this->initExtractor($options);
        $this->initLimiter($options);
    }

    /**
     * @param array{extractor?: Extractor,add_words?: string|array<array-key, string>,remove_words?: string|array<array-key, string>,limiter?: Limiter, limit_length?: int} $options
     */
    protected function initOptions(array $options): void
    {
        $this->initAddOption($options);
        $this->initRemoveOption($options);
        $this->initLengthOption($options);
    }

    /**
     * @param array{add_words?: string|array<array-key, string>} $options
     */
    protected function initAddOption(array $options): void
    {
        if (!isset($options['add_words'])) {
            return;
        }

        $this->validateAddOption($options);

        $this->options['add'] = (is_string($options['add_words']))
            ? [$options['add_words']]
            : $options['add_words'];
    }

    /**
     * @param array{add_words: string|array<array-key, string>} $options
     */
    protected function validateAddOption(array $options): void
    {
        if (!is_string($options['add_words']) && !is_array($options['add_words'])) {
            throw new InvalidOptionType('The add_words option must be a string or an array.');
        }
    }

    /**
     * @param array{remove_words?: string|array<array-key, string>} $options
     */
    protected function initRemoveOption(array $options): void
    {
        if (!isset($options['remove_words'])) {
            return;
        }

        $this->validateRemoveOption($options);

        $this->options['remove'] = (is_string($options['remove_words']))
            ? [$options['remove_words']]
            : $options['remove_words'];
    }

    /**
     * @param array{remove_words: string|array<array-key, string>} $options
     */
    protected function validateRemoveOption(array $options): void
    {
        if (!is_string($options['remove_words']) && !is_array($options['remove_words'])) {
            throw new InvalidOptionType('The remove_words option must be a string or an array.');
        }
    }

    /**
     * @param array{limit_length?: int} $options
     */
    protected function initLengthOption(array $options): void
    {
        if (!isset($options['limit_length'])) {
            return;
        }

        if (!is_int($options['limit_length'])) {
            throw new InvalidOptionType('The limit_length option must be an integer.');
        }

        $this->options['length'] = $options['limit_length'];
    }

    /**
     * @param array{extractor?: Extractor} $options
     */
    protected function initExtractor(array $options): void
    {
        if (isset($options['extractor'])) {
            $this->validateExtractorOption($options);
            $this->extractor = $options['extractor'];

            return;
        }

        $this->extractor = new (static::DEFAULT_EXTRACTOR)($this->options);
    }

    /**
     * @param array{extractor: Extractor} $options
     */
    protected function validateExtractorOption(array $options): void
    {
        if (!is_object($options['extractor']) || !is_a($options['extractor'], Extractor::class)) {
            throw new InvalidOptionType('The extractor option must be of type Extractor.');
        }
    }

    /**
     * @param array{limiter?: Limiter} $options
     */
    protected function initLimiter(array $options): void
    {
        if (isset($options['limiter'])) {
            $this->validateLimiterOption($options);
            $this->limiter = $options['limiter'];

            return;
        }

        $this->limiter = new (static::DEFAULT_LIMITER)($this->options['length']);
    }

    /**
     * @param array{limiter: Limiter} $options
     */
    protected function validateLimiterOption(array $options): void
    {
        if (!is_object($options['limiter']) || !is_a($options['limiter'], Limiter::class)) {
            throw new InvalidOptionType('The limiter option must be of type Limiter.');
        }
    }

    /**
     * Allow words to be extracted if they are ignored by an Extractor.
     *
     * @param string|array<array-key, string> $words
     */
    public function addWords(string|array $words): static
    {
        $newWords = is_string($words) ? [$words] : $words;
        $this->extractor->addWords($newWords);

        return $this;
    }

    /**
     * Disallow words to be extracted if they are ignored by an Extractor.
     *
     * @param string|array<array-key, string> $words
     */
    public function removeWords(string|array $words): static
    {
        $newWords = is_string($words) ? [$words] : $words;
        $this->extractor->removeWords($newWords);

        return $this;
    }

    /**
     * Extract relevant keywords from a text.
     *
     * @param string $text
     * @return string
     */
    public function extract(string $text): string
    {
        $words = $this->extractor->extractWords($text);

        $keywords = implode(', ', $words);

        return $this->limiter->limit($keywords);
    }
}
