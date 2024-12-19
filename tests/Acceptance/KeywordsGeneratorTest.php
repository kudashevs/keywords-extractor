<?php

namespace Kudashevs\KeywordsGenerator\Tests\Acceptance;

use Kudashevs\KeywordsGenerator\Generator;
use PHPUnit\Framework\TestCase;

class KeywordsGeneratorTest extends TestCase
{
    /** @test */
    public function it_should_generate_keywords(): void
    {
        $text = 'New York City is a beautiful, one';
        $keywords = Generator::fromString($text);

        $this->assertSame('New York, city, beautiful', $keywords);
    }
}
