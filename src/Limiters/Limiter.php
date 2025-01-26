<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Limiters;

/**
 * Limiter represents an abstraction that limits the length of the result.
 */
interface Limiter
{
    /**
     * Limit the length of a text.
     *
     * @param string $text
     * @return string
     */
    public function limit(string $text): string;
}
