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
        $words = $this->extractor->extractWords('cool, this is a test');

        $this->assertCount(2, $words);
    }

    #[Test]
    public function it_can_apply_exclusions_by_default(): void
    {
        $keywords = $this->extractor->extractWords('this course needs some changes');

        $this->assertCount(2, $keywords);
    }

    #[Test]
    public function it_cannot_apply_exclusions_when_default_is_disabled(): void
    {
        $extractor = new RakeExtractor(['default_exclusions' => false]);

        $keywords = $extractor->extractWords('this course needs some changes');

        $this->assertCount(0, $keywords);
    }

    #[Test]
    public function it_can_include_adverbs_with_ly_by_default(): void
    {
        $keywords = $this->extractor->extractWords('warningly this is a test');

        $this->assertCount(1, $keywords);
    }

    #[Test]
    public function it_cannot_include_adverbs_with_ly_when_default_is_disabled(): void
    {
        $extractor = new RakeExtractor(['default_inclusions' => false]);

        $keywords = $extractor->extractWords('warningly this is a test');

        $this->assertCount(2, $keywords);
    }

    #[Test]
    public function it_can_include_other_adverbs_by_default(): void
    {
        $keywords = $this->extractor->extractWords('this test is pretty OK');

        $this->assertCount(1, $keywords);
    }

    #[Test]
    public function it_cannot_include_other_adverbs_when_default_is_disabled(): void
    {
        $extractor = new RakeExtractor(['default_inclusions' => false]);

        $keywords = $extractor->extractWords('this test is pretty OK');

        $this->assertCount(2, $keywords);
    }

    #[Test]
    public function it_can_remove_regular_verbs_by_default(): void
    {
        $keywords = $this->extractor->extractWords('we benefit from the RAKE');

        $this->assertCount(1, $keywords);
        $this->assertContains('rake', $keywords);
    }

    #[Test]
    public function it_cannot_remove_regular_verbs_when_default_is_disabled(): void
    {
        $extractor = new RakeExtractor(['default_inclusions' => false]);

        $keywords = $extractor->extractWords('we benefit the from RAKE');

        $this->assertCount(2, $keywords);
        $this->assertContains('benefit', $keywords);
    }

    #[Test]
    public function it_can_remove_regular_verbs_in_third_person_by_default(): void
    {
        $keywords = $this->extractor->extractWords('it benefits from the RAKE');

        $this->assertCount(1, $keywords);
        $this->assertContains('rake', $keywords);
    }

    #[Test]
    public function it_cannot_remove_regular_verbs_in_third_person_when_default_is_disabled(): void
    {
        $extractor = new RakeExtractor(['default_inclusions' => false]);

        $keywords = $extractor->extractWords('it benefits from the RAKE');

        $this->assertCount(2, $keywords);
        $this->assertContains('benefits', $keywords);
    }

    #[Test]
    public function it_can_remove_regular_verbs_in_past_by_default(): void
    {
        $keywords = $this->extractor->extractWords('we benefited from the RAKE');

        $this->assertCount(1, $keywords);
        $this->assertContains('rake', $keywords);
    }

    #[Test]
    public function it_can_remove_irregular_verbs_in_past_by_default(): void
    {
        $extractor = new RakeExtractor(['default_inclusions' => false]);

        $keywords = $extractor->extractWords('we benefited from the RAKE');

        $this->assertCount(2, $keywords);
        $this->assertContains('benefited', $keywords);
    }
}
