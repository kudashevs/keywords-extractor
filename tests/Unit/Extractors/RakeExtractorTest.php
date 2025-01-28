<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Extractors;

use Kudashevs\KeywordsExtractor\Extractors\RakeExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RakeExtractorTest extends TestCase
{
    private RakeExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new RakeExtractor();
    }

    #[Test]
    public function it_can_extract_keywords(): void
    {
        $keywords = $this->extractor->extractWords('cool, this is a test');

        $this->assertCount(2, $keywords);
    }

    #[Test]
    public function it_can_remove_an_adverb_with_ly(): void
    {
        $keywords = $this->extractor->extractWords('warningly this is a test');

        $this->assertCount(1, $keywords);
    }

    #[Test]
    public function it_can_remove_other_adverbs(): void
    {
        $keywords = $this->extractor->extractWords('almost now this is a test');

        $this->assertCount(1, $keywords);
    }
}
