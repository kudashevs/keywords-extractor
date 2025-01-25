<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Extractors;

use Kudashevs\RakePhp\Rake;

final class RakeExtractor implements Extractor
{
    private Rake $extractor;

    public function __construct(array $options = [])
    {
        $this->extractor = new Rake([
            'exclude' => $options['add'] ?? [],
            'include' => $options['remove'] ?? [],
        ]);
    }

    public function extract(string $text): array
    {
        $keywords = $this->extractor->extract($text);

        return array_keys($keywords);
    }
}
