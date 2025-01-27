<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

final class PercentLimiter implements Limiter
{
    use Keywordable;

    const DEFAULT_PERCENT = 10;

    private int $percent;

    private int $maxLength;

    public function __construct(int $percent = self::DEFAULT_PERCENT, int $maxLength = 0)
    {
        $this->initPercent($percent);
        $this->initMaxLength($maxLength);
    }

    private function initPercent(int $maxPercent): void
    {
        if ($maxPercent <= 0) {
            throw new InvalidOptionValue('The max length value must be greater or equal to 0.');
        }

        if ($maxPercent > 100) {
            throw new InvalidOptionValue('The max length value must be less or equal to 100.');
        }

        $this->percent = $maxPercent;
    }

    private function initMaxLength(int $maxLength): void
    {
        if ($maxLength < 0) {
            throw new InvalidOptionValue('The max length value must be greater or equal to 0.');
        }

        $this->maxLength = $maxLength;
    }

    public function limit(string $text): string
    {
        $percent = $this->retrievePercentForCalculation();

        /*
         * When the behavior is limitless, the input text does not need to be processed.
         */
        if ($this->isLimitless($percent)) {
            return $this->cleanUp($text);
        }

        $lengthFromPercent = (int)($percent * mb_strlen($text) / 100);

        if ($this->isPercentOnlyLimited($percent)) {
            $limitedText = $this->prepare(
                $text,
                $lengthFromPercent,
            );

            return $this->cleanUp($limitedText);
        }

        $maxLength = $this->findMaxLimitLength($lengthFromPercent);
        $limitedText = $this->prepare(
            $text,
            $maxLength,
        );

        return $this->cleanUp($limitedText);
    }

    private function isLimitless(int $percent): bool
    {
        return $percent === 100 && $this->maxLength === 0;
    }

    private function isPercentOnlyLimited(int $percent): bool
    {
        return $percent !== 100 && $this->maxLength === 0;
    }

    private function retrievePercentForCalculation(): int
    {
        return (isset($this->percent) && $this->percent !== 0)
            ? $this->percent
            : self::DEFAULT_PERCENT;
    }

    private function findMaxLimitLength(int $candidate): int
    {
        return ($this->maxLength === 0 || $candidate < $this->maxLength)
            ? $candidate
            : $this->maxLength;
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
