<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

final class LengthLimiter implements Limiter
{
    use Limitable;

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

    private function prepare(string $text): string
    {
        $cut = mb_substr($text, 0, $this->maxLength);

        $lastSpacePosition = $this->findLastPosition($cut, ' ');

        return mb_substr($cut, 0, $lastSpacePosition);
    }
}
