<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

final class LengthLimiter implements Limiter
{
    use Keywordable;

    const MAX_LIMIT_LENGTH = 255;

    private array $options = [
        'delimiter' => ' ',
        'max_length' => self::MAX_LIMIT_LENGTH,
    ];

    /**
     * @param array{
     *     delimiter?: string,
     *     max_length?: int,
     * } $options
     */
    public function __construct(array $options = [])
    {
        $this->initDelimiterOption($options);
        $this->initMaxLengthOption($options);
    }

    /**
     * @param array{delimiter?: string} $options
     */
    private function initDelimiterOption(array $options): void
    {
        if (isset($options['delimiter'])) {
            if (mb_strlen($options['delimiter']) > 1) {
                throw new InvalidOptionValue('The delimiter must be one character long.');
            }

            $this->options['delimiter'] = $options['delimiter'];
        }
    }

    /**
     * @param array{max_length?: int} $options
     */
    private function initMaxLengthOption(array $options): void
    {
        if (isset($options['max_length'])) {
            if ($options['max_length'] < 0) {
                throw new InvalidOptionValue('The max length value must be greater or equal to 0.');
            }

            $this->options['max_length'] = $options['max_length'];
        }
    }

    /**
     * @inheritDoc
     */
    public function limit(string $text): string
    {
        /*
         * When the behavior is limitless, the input text does not need to be processed.
         */
        if ($this->isLimitless()) {
            return $this->cleanUp($text);
        }

        return $this->limitText($text);
    }

    private function isLimitless(): bool
    {
        return $this->options['max_length'] === 0;
    }

    private function limitText(string $text): string
    {
        if ($this->isBelowLimit($text)) {
            return $this->cleanUp($text);
        }

        $limitedText = $this->prepare($text);

        return $this->cleanUp($limitedText);
    }

    private function isBelowLimit(string $text): bool
    {
        return mb_strlen($text) <= $this->options['max_length'];
    }

    private function prepare(string $text): string
    {
        $cut = mb_substr($text, 0, $this->options['max_length']);

        if ($this->isEndOfKeywords($text)) {
            return $cut;
        }

        $lastSpacePosition = $this->findLastPosition($cut, ' ');

        return mb_substr($cut, 0, $lastSpacePosition);
    }
}
