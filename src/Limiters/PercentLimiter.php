<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

final class PercentLimiter implements Limiter
{
    use Keywordable;

    const DEFAULT_PERCENT = 10;

    private array $options = [
        'separator' => ' ',
        'percent' => self::DEFAULT_PERCENT,
        'max_length' => 0,
    ];

    /**
     * @param array{
     *     separator?: string,
     *     percent?: int,
     *     max_length?: int,
     * } $options
     */
    public function __construct(array $options = [])
    {
        $this->initDelimiterOption($options);
        $this->initPercentOption($options);
        $this->initMaxLengthOption($options);
    }

    /**
     * @param array{separator?: string} $options
     */
    private function initDelimiterOption(array $options): void
    {
        if (isset($options['separator']) && is_string($options['separator'])) {
            if (mb_strlen($options['separator']) > 1) {
                throw new InvalidOptionValue('The separator must be one character long.');
            }

            $this->options['separator'] = $options['separator'];
        }
    }

    /**
     * @param array{percent?: int} $options
     */
    private function initPercentOption(array $options): void
    {
        if (isset($options['percent']) && is_int($options['percent'])) {
            if ($options['percent'] <= 0) {
                throw new InvalidOptionValue('The max length value must be greater than 0.');
            }

            if ($options['percent'] > 100) {
                throw new InvalidOptionValue('The max length value must be less or equal to 100.');
            }

            $this->options['percent'] = $options['percent'];
        }
    }

    /**
     * @param array{max_length?: int} $options
     */
    private function initMaxLengthOption(array $options): void
    {
        if (isset($options['max_length']) && is_int($options['max_length'])) {
            if ($options['max_length'] < 0) {
                throw new InvalidOptionValue('The max length value must be greater or equal to 0.');
            }

            $this->options['max_length'] = $options['max_length'];
        }
    }

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
        $percent = $this->retrievePercentForCalculation();

        return $percent === 100 && $this->options['max_length'] === 0;
    }

    private function limitText(string $text): string
    {
        $percent = $this->retrievePercentForCalculation();
        $lengthFromPercent = (int)($percent * mb_strlen($text) / 100);

        if ($this->isLimitedByPercentOnly($percent)) {
            return $this->limitTextByLength($text, $lengthFromPercent);
        }

        $maxLength = $this->findMaxLimitLength($lengthFromPercent);
        return $this->limitTextByLength($text, $maxLength);
    }

    private function limitTextByLength(string $text, int $length): string
    {
        $limitedText = $this->prepare(
            $text,
            $length,
        );

        return $this->cleanUp($limitedText);
    }

    private function isLimitedByPercentOnly(int $percent): bool
    {
        return $percent !== 100 && $this->options['max_length'] === 0;
    }

    private function retrievePercentForCalculation(): int
    {
        return (isset($this->options['percent']) && $this->options['percent'] !== 0)
            ? $this->options['percent']
            : self::DEFAULT_PERCENT;
    }

    private function findMaxLimitLength(int $candidate): int
    {
        return ($this->options['max_length'] === 0 || $candidate < $this->options['max_length'])
            ? $candidate
            : $this->options['max_length'];
    }

    private function prepare(string $text, int $length): string
    {
        $cut = mb_substr($text, 0, $length);

        if ($this->isEndOfKeywords($cut)) {
            return $cut;
        }

        $lastSpacePosition = $this->findLastPosition($cut, ' ');

        return mb_substr($cut, 0, $lastSpacePosition);
    }
}
