<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Collections;

/**
 * WordsCollection represents an abstraction of a named collection of words.
 */
interface WordsCollection
{
    /**
     * Get a unique collection name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get a list of words.
     *
     * @return array<array-key, string>
     */
    public function getWords(): array;
}
