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

        $lastSpacePosition = $this->findLastPosition($cut, ' ');

        return mb_substr($cut, 0, $lastSpacePosition);
    }

    private function findLastPosition(string $text, string $char)
    {
        $lastPosition = mb_strlen($text);
        $currentPosition = $lastPosition - 1;

        while ($currentPosition > 0) {
            if ($text[$currentPosition] === $char) {
                return $currentPosition;
            }

            $currentPosition--;
        }

        return $lastPosition;
    }

    private function cleanUp(string $text): string
    {
        return rtrim($text, ', ');
    }
}
