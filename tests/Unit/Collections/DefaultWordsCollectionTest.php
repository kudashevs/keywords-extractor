<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Collections;

use Kudashevs\KeywordsExtractor\Collections\DefaultWordsCollection;
use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DefaultWordsCollectionTest extends TestCase
{
    #[Test]
    public function it_throws_an_exception_when_a_list_does_not_exist(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('wrong');

        new DefaultWordsCollection(['wrong']);
    }

    #[Test]
    public function it_can_retrieve_lists_of_words(): void
    {
        $collection = new DefaultWordsCollection(['adverbs', 'verbs']);

        $words = $collection->getWords();

        $this->assertNotEmpty($words);
        $this->assertTrue($this->isOneDimensionArray($words));
    }

    private function isOneDimensionArray(array $arr): bool
    {
        return count($arr) === count($arr, COUNT_RECURSIVE);
    }
}
