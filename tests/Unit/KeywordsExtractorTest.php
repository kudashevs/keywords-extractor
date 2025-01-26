<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionType;
use Kudashevs\KeywordsExtractor\KeywordsExtractor;
use PHPUnit\Framework\Attributes\DataProvider;
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
    public function it_throws_an_exception_when_a_wrong_extractor_type(): void
    {
        $this->expectException(InvalidOptionType::class);
        $this->expectExceptionMessage('extractor');

        new KeywordsExtractor(['extractor' => new \stdClass()]);
    }

    #[Test]
    public function it_throws_an_exception_when_a_wrong_limiter_type(): void
    {
        $this->expectException(InvalidOptionType::class);
        $this->expectExceptionMessage('limiter');

        new KeywordsExtractor(['limiter' => new \stdClass()]);
    }

    #[Test]
    public function it_throws_an_exception_when_a_wrong_limit_length_type(): void
    {
        $this->expectException(InvalidOptionType::class);
        $this->expectExceptionMessage('limit_length');

        new KeywordsExtractor(['limit_length' => 'any']);
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
    #[DataProvider('provideAddWords')]
    public function it_can_accept_the_add_words_option_at_instantiation(
        string $text,
        string|array $words,
        string $expected,
    ): void {
        $extractor = new KeywordsExtractor([
            'add_words' => $words,
        ]);

        $keywords = $extractor->extract($text);

        $this->assertSame($expected, $keywords);
    }

    #[Test]
    #[DataProvider('provideAddWords')]
    public function it_can_accept_the_add_words_option_fluently(
        string $text,
        string|array $words,
        string $expected,
    ): void {
        $extractor = new KeywordsExtractor();

        $keywords = $extractor->addWords($words)->extract($text);

        $this->assertSame($expected, $keywords);
    }

    public static function provideAddWords(): array
    {
        return [
            'as a string' => ['this is a test', 'this', 'this, test'],
            'as an array' => ['this is a test', ['this'], 'this, test'],
        ];
    }

    #[Test]
    public function it_throws_an_exception_when_an_invalid_remove_words_type(): void
    {
        $this->expectException(InvalidOptionType::class);
        $this->expectExceptionMessage('remove_words');

        new KeywordsExtractor(['remove_words' => 42]);
    }

    #[Test]
    #[DataProvider('provideRemoveWords')]
    public function it_can_accept_the_remove_words_option_at_instantiation(
        string $text,
        string|array $words,
        string $expected,
    ): void {
        $extractor = new KeywordsExtractor([
            'remove_words' => $words,
        ]);

        $keywords = $extractor->extract($text);

        $this->assertSame($expected, $keywords);
    }

    #[Test]
    #[DataProvider('provideRemoveWords')]
    public function it_can_accept_the_remove_words_option_fluently(
        string $text,
        string|array $words,
        string $expected,
    ): void {
        $extractor = new KeywordsExtractor();

        $keywords = $extractor->removeWords($words)->extract($text);

        $this->assertSame($expected, $keywords);
    }

    public static function provideRemoveWords(): array
    {
        return [
            'as a string' => ['this is a test', 'test', ''],
            'as an array' => ['this is a test', ['test'], ''],
        ];
    }
}
