<?php

namespace Kudashevs\KeywordsExtractor\Tests\Acceptance;

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
    public function it_should_handle_an_empty_string(): void
    {
        $keywords = $this->extractor->extract(' ');

        $this->assertEmpty($keywords);
    }

    #[Test]
    public function it_should_generate_keywords(): void
    {
        $keywords = $this->extractor->extract('this is a test');

        $this->assertNotEmpty($keywords);
        $this->assertSame('test', $keywords);
    }

    #[Test]
    public function it_should_generate_keywords_with_an_added_keyword(): void
    {
        $extractor = new KeywordsExtractor([
            'add_words' => 'new',
        ]);

        $keywords = $extractor->extract('New York City is a beautiful one');

        $this->assertSame('new york city, beautiful', $keywords);
    }

    #[Test]
    public function it_should_generate_keywords_with_an_added_special_case(): void
    {
        $extractor = new KeywordsExtractor([
            'add_words' => 'New',
        ]);

        $keywords = $extractor->extract('New York City is a new beautiful one');

        $this->assertSame('new york city, beautiful', $keywords);
    }

    #[Test]
    public function it_should_generate_keywords_with_a_limited_length(): void
    {
        $extractor = new KeywordsExtractor([
            'add_words' => 'new',
            'limit_length' => 16,
        ]);

        $keywords = $extractor->extract('New York City is a beautiful one');

        $this->assertSame('new york city', $keywords);
    }
}
