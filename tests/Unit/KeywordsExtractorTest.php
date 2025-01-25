<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionType;
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

    #[Test]
    public function it_can_extract_keywords(): void
    {
        $keywords = $this->extractor->generate('this is a test');

        $this->assertSame('test', $keywords);
    }

    #[Test]
    public function it_throws_an_exception_when_an_invalid_add_words_type(): void
    {
        $this->expectException(InvalidOptionType::class);
        $this->expectExceptionMessage('add_words');

        new KeywordsExtractor(['add_words' => 42]);
    }

    #[Test]
    public function it_can_add_words_before_extracting_keywords(): void
    {
        $extractor = new KeywordsExtractor([
            'add_words' => 'this',
        ]);

        $keywords = $extractor->generate('this is a test');

        $this->assertSame('this, test', $keywords);
    }

    #[Test]
    public function it_throws_an_exception_when_an_invalid_remove_words_type(): void
    {
        $this->expectException(InvalidOptionType::class);
        $this->expectExceptionMessage('remove_words');

        new KeywordsExtractor(['remove_words' => 42]);
    }

    #[Test]
    public function it_can_remove_words_before_extracting_keywords(): void
    {
        $extractor = new KeywordsExtractor([
            'remove_words' => 'test',
        ]);

        $keywords = $extractor->generate('this is a test');

        $this->assertSame('', $keywords);
    }

    #[Test]
    public function it_can_extract_keywords_with_exclusion(): void
    {
        $text = 'The keywords generator greets the world!';

        $keywords = $this->extractor->generate($text);

        $this->assertSame('keywords generator greets, world', $keywords);
    }
}
