<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

trait Limitable
{
    private function isEndOfKeywords(string $text): bool
    {
        return preg_match('/(\s+|,)$/u', $text) === 1;
    }

    private function cleanUp(string $text): string
    {
        return rtrim($text, ', ');
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
}
