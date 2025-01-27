<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

final class LengthLimiter implements Limiter
{
    use Limitable;

    const MAX_LIMIT_LENGTH = 255;

    private int $maxLength;

    public function __construct(int $maxLength = self::MAX_LIMIT_LENGTH)
    {
        $this->initMaxLength($maxLength);
    }

    private function initMaxLength(int $maxLength): void
    {
        if ($maxLength < 0) {
            throw new InvalidOptionValue('The max length value must be greater or equal to 0.');
        }

        $this->maxLength = $maxLength;
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

        $limited = $this->prepare($text);

        return $this->cleanUp($limited);
    }

    private function isLimitless(): bool
    {
        return $this->maxLength === 0;
    }

    private function prepare(string $text): string
    {
        $cut = mb_substr($text, 0, $this->maxLength);

        if ($this->isendoftext($text)) {
            return $cut;
        }

        $lastSpacePosition = $this->findLastPosition($cut, ' ');

        return mb_substr($cut, 0, $lastSpacePosition);
    }
}
