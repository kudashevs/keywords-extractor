<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

trait Limitable
{
    private int $maxLength;

    private function isLimitless(): bool
    {
        return $this->maxLength === 0;
    }

    private function findLastPosition(string $text, string $char)
    {
        $textLength = mb_strlen($text);
        $currentPosition = $textLength - 1;

        while ($currentPosition > 0) {
            if ($text[$currentPosition] === $char) {
                return $currentPosition;
            }

            $currentPosition--;
        }

        return $textLength;
    }

    private function cleanUp(string $text): string
    {
        return rtrim($text, ', ');
    }
}
