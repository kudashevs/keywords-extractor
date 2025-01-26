<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

final class LengthLimiter implements Limiter
{
    const MAX_LIMIT_LENGTH = 255;

    private int $maxLength;

    public function __construct(int $maxLength = self::MAX_LIMIT_LENGTH)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @inheritDoc
     */
    public function limit(string $text): string
    {
        if ($this->isLimitless()) {
            return $text;
        }

        $cut = mb_substr($text, 0, $this->maxLength);

        return $this->cleanUp($cut);
    }

    private function isLimitless(): bool
    {
        return $this->maxLength === 0;
    }

    private function cleanUp(string $text): string
    {
        return rtrim($text, ',');
    }
}
