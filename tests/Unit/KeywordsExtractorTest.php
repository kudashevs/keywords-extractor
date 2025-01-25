<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit;

use Kudashevs\KeywordsExtractor\KeywordsExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class KeywordsExtractorTest extends TestCase
{
    private KeywordsExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new KeywordsExtractor();
    }

    #[Test]
    public function it_can_extract_a_keyword(): void
    {
        $keywords = $this->extractor->generate('test');

        $this->assertSame('test', $keywords);
    }
}
