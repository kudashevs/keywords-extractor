<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsGenerator\Analyzers;

use DonatelloZa\RakePlus\RakePlus;

final class RakePlusAnalyzer implements TextAnalyzerInterface
{
    public function __construct()
    {
    }

    public function analyze(string $text): array
    {
        $instance = RakePlus::create($text, 'en_US');

        return $instance->get();
    }
}
