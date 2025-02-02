<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Collections;

use Kudashevs\KeywordsExtractor\Collections\CacheWordsCollection;
use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;
use PHPUnit\Framework\Attributes\Test;

class CacheWordsCollectionTest extends CollectionTestCase
{
    #[Test]
    public function it_throws_an_exception_when_an_illegal_name(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('empty');

        new CacheWordsCollection('  ', []);
    }

    #[Test]
    public function it_can_generate_a_name(): void
    {
        $collection = new CacheWordsCollection('remove_words', []);

        $this->assertNotEmpty($collection->getName());
        $this->assertStringContainsString('remove_words', $collection->getName());
    }

    #[Test]
    public function it_throws_an_exception_when_a_list_does_not_exist(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('wrong');

        new CacheWordsCollection('test', ['wrong']);
    }

    #[Test]
    public function it_throws_an_exception_when_an_illegal_build_dir(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('directory');

        new CacheWordsCollection('test', ['adverbs'], 'wrong');
    }

    #[Test]
    public function it_can_retrieve_lists_of_words(): void
    {
        $collection = new CacheWordsCollection('remove_words', ['adverbs', 'verbs']);

        $words = $collection->getWords();

        $this->assertNotEmpty($words);
        $this->assertTrue($this->isOneDimensionArray($words));
    }

    #[Test]
    public function it_can_keep_lists_of_words_in_a_build_dir(): void
    {
        $buildDirectory = __DIR__ . '/../../temp';
        $cacheDirectory = $buildDirectory . '/cache';
        $expectedFile = $cacheDirectory . DIRECTORY_SEPARATOR . 'remove_words.dat';

        $collection = new CacheWordsCollection('remove_words', ['adverbs'], $buildDirectory);

        $this->assertFileDoesNotExist($expectedFile);
        $words = $collection->getWords();
        $this->assertFileExists($expectedFile);

        $this->assertNotEmpty($words);
        $this->deleteFileWithAssertion($expectedFile);
        $this->deleteDirectoryWithAssertion($cacheDirectory);
    }


    private function isOneDimensionArray(array $arr): bool
    {
        return count($arr) === count($arr, COUNT_RECURSIVE);
    }
}
