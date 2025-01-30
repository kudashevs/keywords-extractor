<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Collections;

/**
 * WordsCollection represents an abstraction with a collection of words lists.
 */
interface WordsCollection
{
    /**
     * Get a list of words.
     *
     * @return array<array-key, string>
     */
    public function getWords(): array;
}
