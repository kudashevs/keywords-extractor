<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Limiters;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;
use Kudashevs\KeywordsExtractor\Limiters\LengthLimiter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LengthLimiterTest extends TestCase
{
    private LengthLimiter $limiter;

    protected function setUp(): void
    {
        $this->limiter = new LengthLimiter();
    }

    #[Test]
    public function it_throws_an_exception_when_max_length_is_less_than_0(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('max length');

        new LengthLimiter(['max_length' => -1]);
    }

    #[Test]
    public function it_can_limit_a_text_without_spaces(): void
    {
        $expected = LengthLimiter::MAX_LIMIT_LENGTH - 1;

        $sequence = $this->generateSequence($expected) . ', ' . $this->generateSequence(42);

        $limited = $this->limiter->limit($sequence);

        $this->assertSame($expected, strlen($limited));
    }

    #[Test]
    public function it_can_limit_a_text(): void
    {
        $limiter = new LengthLimiter(['max_length' => 10]);
        $text = 'new york city, beautiful';

        $limited = $limiter->limit($text);

        $this->assertSame('new york', $limited);
    }

    #[Test]
    #[DataProvider('provideEndOfKeywords')]
    public function it_can_limit_a_text_and_consider_the_end_of_keywords(
        string $text,
        int $limit,
        string $expected,
    ): void {
        $limiter = new LengthLimiter(['max_length' => $limit]);

        $limited = $limiter->limit($text);

        $this->assertSame($expected, $limited);
    }

    public static function provideEndOfKeywords(): array
    {
        return [
            'space at the end' => [
                'new york city ',
                13,
                'new york city',
            ],
            'comma at the end' => [
                'new york city,',
                13,
                'new york city',
            ],
        ];
    }

    #[Test]
    public function it_can_use_external_limit_value(): void
    {
        $limiter = new LengthLimiter(['max_length' => 10]);

        $sequence = $this->generateSequence(10) . ', ' . $this->generateSequence(42);

        $limited = $limiter->limit($sequence);

        $this->assertSame(10, strlen($limited));
    }

    #[Test]
    public function it_can_use_external_limit_value_and_be_limitless(): void
    {
        $limiter = new LengthLimiter(['max_length' => 0]);
        $default = LengthLimiter::MAX_LIMIT_LENGTH - 1;

        $sequence = $this->generateSequence($default) . ', ' . $this->generateSequence(42);

        $limited = $limiter->limit($sequence);

        $this->assertGreaterThan($default, strlen($limited));
    }

    #[Test]
    public function it_can_use_external_limit_value_and_limit_to_some_extent_with_default_separator(): void
    {
        $limiter = new LengthLimiter(['max_length' => 28]);
        $text = 'new york city, beautiful city';

        $limited = $limiter->limit($text);

        $this->assertSame('new york city, beautiful', $limited);
    }

    #[Test]
    public function it_can_use_external_limit_value_and_limit_to_some_extent_with_provided_separator(): void
    {
        $limiter = new LengthLimiter(['separator' => ',', 'max_length' => 28]);
        $text = 'new york city, beautiful city';

        $limited = $limiter->limit($text);

        $this->assertSame('new york city', $limited);
    }

    private function generateSequence(int $length): string
    {
        return str_repeat('A', $length);
    }
}
