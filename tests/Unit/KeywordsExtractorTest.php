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
        $keywords = $this->extractor->extract('test');

        $this->assertSame('test', $keywords);
    }

    #[Test]
    public function it_can_extract_keywords(): void
    {
        $keywords = $this->extractor->extract('this is a test');

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
    public function it_can_accept_the_add_words_option_at_instantiation(): void
    {
        $extractor = new KeywordsExtractor([
            'add_words' => 'this',
        ]);

        $keywords = $extractor->extract('this is a test');

        $this->assertSame('this, test', $keywords);
    }

    #[Test]
    public function it_can_accept_the_add_words_option_fluently(): void
    {
        $extractor = new KeywordsExtractor();

        $keywords = $extractor->addWords('this')->extract('this is a test');

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
    public function it_can_accept_the_remove_words_option_at_instantiation(): void
    {
        $extractor = new KeywordsExtractor([
            'remove_words' => 'test',
        ]);

        $keywords = $extractor->extract('this is a test');

        $this->assertSame('', $keywords);
    }

    #[Test]
    public function it_can_accept_the_remove_words_option_fluently(): void
    {
        $extractor = new KeywordsExtractor();

        $keywords = $extractor->removeWords('test')->extract('this is a test');

        $this->assertSame('', $keywords);
    }

    #[Test]
    public function it_can_extract_keywords_with_exclusion(): void
    {
        $text = 'The keywords generator greets the world!';

        $keywords = $this->extractor->extract($text);

        $this->assertSame('keywords generator greets, world', $keywords);
    }
}
