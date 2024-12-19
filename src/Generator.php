<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsGenerator;

use Kudashevs\KeywordsGenerator\Analyzers\RakePlusAnalyzer;
use Kudashevs\KeywordsGenerator\Analyzers\TextAnalyzerInterface;

class Generator
{
    protected TextAnalyzerInterface $analyzer;

    public function __construct()
    {
        $this->initAnalyzer();
    }

    protected function initAnalyzer(): void
    {
        $this->analyzer = new RakePlusAnalyzer();
    }

    public static function fromString(string $text): string
    {
        return (new static)->generate($text);
    }

    protected function generate(string $text): string
    {
        $words = $this->analyzer->analyze($text);

        return implode(', ', $words);
    }
}
