<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Extractors;

use Kudashevs\KeywordsExtractor\Extractors\RakeExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RakeExtractorTest extends TestCase
{
    #[Test]
    public function it_can_extract_keywords(): void
    {
        $extractor = new RakeExtractor();

        $keywords = $extractor->extractWords('cool, this is a test');

        $this->assertCount(2, $keywords);
    }
}
