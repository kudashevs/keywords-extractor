<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor;

use Kudashevs\KeywordsExtractor\Extractors\Extractor;
use Kudashevs\KeywordsExtractor\Extractors\RakeExtractor;

class KeywordsExtractor
{
    protected const DEFAULT_EXTRACTOR = RakeExtractor::class;

    protected Extractor $extractor;

    public function __construct()
    {
        $this->initExtractor();
    }

    protected function initExtractor(): void
    {
        $this->extractor = new (self::DEFAULT_EXTRACTOR)();
    }

    public function generate(string $text): string
    {
        $words = $this->extractor->extract($text);

        return implode(', ', $words);
    }
}
