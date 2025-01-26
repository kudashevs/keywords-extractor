<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Limiters;

use Kudashevs\KeywordsExtractor\Limiters\LengthLimiter;
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
        $limiter = new LengthLimiter(10);
        $text = 'new york city, beautiful';

        $limited = $limiter->limit($text);

        $this->assertSame('new york', $limited);
    }

    #[Test]
    public function it_can_use_external_limit_value(): void
    {
        $limiter = new LengthLimiter(10);

        $sequence = $this->generateSequence(10) . ', ' . $this->generateSequence(42);

        $limited = $limiter->limit($sequence);

        $this->assertSame(10, strlen($limited));
    }

    #[Test]
    public function it_can_use_external_limit_value_and_be_limitless(): void
    {
        $limiter = new LengthLimiter(0);
        $default = LengthLimiter::MAX_LIMIT_LENGTH - 1;

        $sequence = $this->generateSequence($default) . ', ' . $this->generateSequence(42);

        $limited = $limiter->limit($sequence);

        $this->assertGreaterThan($default, strlen($limited));
    }

    #[Test]
    public function it_can_use_external_limit_value_and_limit_to_some_extent(): void
    {
        $limiter = new LengthLimiter(16);
        $text = 'new york city, beautiful';

        $limited = $limiter->limit($text);

        $this->assertSame('new york city', $limited);
    }

    private function generateSequence(int $length): string
    {
        return str_repeat('A', $length);
    }
}
