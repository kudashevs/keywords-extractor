<?php

namespace Unit\Limiters;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;
use Kudashevs\KeywordsExtractor\Limiters\PercentLimiter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PercentLimiterTest extends TestCase
{
    private PercentLimiter $limiter;

    protected function setUp(): void
    {
        $this->limiter = new PercentLimiter();
    }

    #[Test]
    public function it_throws_an_exception_when_pecetn_is_less_or_equal_to_0(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('max length');

        new PercentLimiter(0);
    }

    #[Test]
    public function it_throws_an_exception_when_pecetn_is_greater_than_100(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('max length');

        new PercentLimiter(101);
    }

    #[Test]
    public function it_throws_an_exception_when_max_length_is_less_than_0(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('max length');

        new PercentLimiter(-1);
    }

    #[Test]
    public function it_can_limit_a_text_without_spaces(): void
    {
        $expected = 64;
        $limiter = new PercentLimiter(100, $expected);

        $sequence = $this->generateSequence($expected) . ', ' . $this->generateSequence(42);

        $limited = $limiter->limit($sequence);

        $this->assertSame($expected, strlen($limited));
    }

    #[Test]
    public function it_can_limit_a_text(): void
    {
        $limiter = new PercentLimiter(80);
        $text = 'new york city, beautiful';

        $limited = $limiter->limit($text);

        $this->assertSame('new york city', $limited);
    }

    #[Test]
    #[DataProvider('provideEndOfText')]
    public function it_can_limit_a_text_and_consider_the_end_of_text(
        string $text,
        int $limit,
        string $expected,
    ): void {
        $limiter = new PercentLimiter($limit);

        $limited = $limiter->limit($text);

        $this->assertSame($expected, $limited);
    }

    public static function provideEndOfText(): array
    {
        return [
            'space at the end' => [
                'new york city ',
                100,
                'new york city',
            ],
            'comma at the end' => [
                'new york city,',
                100,
                'new york city',
            ],
        ];
    }

    #[Test]
    public function it_can_use_external_percent_value(): void
    {
        $limiter = new PercentLimiter(50);

        $sequence = $this->generateSequence(10) . ', ' . $this->generateSequence(10);

        $limited = $limiter->limit($sequence);

        $this->assertSame(10, strlen($limited));
    }

    #[Test]
    public function it_can_use_external_limit_value(): void
    {
        $limiter = new PercentLimiter(100, 12);

        $sequence = $this->generateSequence(10) . ', ' . $this->generateSequence(42);

        $limited = $limiter->limit($sequence);

        $this->assertSame(10, strlen($limited));
    }

    #[Test]
    public function it_can_use_external_percent_value_and_be_limitless(): void
    {
        $limiter = new PercentLimiter(100);

        $sequence = $this->generateSequence(10) . ', ' . $this->generateSequence(10);

        $limited = $limiter->limit($sequence);

        $this->assertEquals(22, strlen($limited));
    }

    private function generateSequence(int $length): string
    {
        return str_repeat('A', $length);
    }
}
