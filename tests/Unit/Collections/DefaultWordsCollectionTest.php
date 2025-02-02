<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Collections;

use Kudashevs\KeywordsExtractor\Collections\DefaultWordsCollection;
use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;
use PHPUnit\Framework\Attributes\Test;

class DefaultWordsCollectionTest extends CollectionTestCase
{
    #[Test]
    public function it_throws_an_exception_when_an_illegal_name(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('empty');

        new DefaultWordsCollection('  ', []);
    }

    #[Test]
    public function it_can_generate_a_name(): void
    {
        $collection = new DefaultWordsCollection('remove_words', []);

        $this->assertNotEmpty($collection->getName());
        $this->assertStringContainsString('remove_words', $collection->getName());
    }

    #[Test]
    public function it_throws_an_exception_when_a_list_does_not_exist(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('wrong');

        new DefaultWordsCollection('test', ['wrong']);
    }

    #[Test]
    public function it_throws_an_exception_when_an_illegal_build_dir(): void
    {
        $this->expectException(InvalidOptionValue::class);
        $this->expectExceptionMessage('directory');

        new DefaultWordsCollection('test', ['adverbs'], 'wrong');
    }

    #[Test]
    public function it_can_retrieve_lists_of_words(): void
    {
        $collection = new DefaultWordsCollection('remove_words', ['adverbs', 'verbs']);

        $words = $collection->getWords();

        $this->assertNotEmpty($words);
        $this->assertTrue($this->isOneDimensionArray($words));
    }

    #[Test]
    public function it_can_keep_lists_of_words_in_a_build_dir(): void
    {
        $buildDirectory = __DIR__ . '/../../temp';
        $wordsDirectory = $buildDirectory . '/words';
        $expectedFile = $wordsDirectory . DIRECTORY_SEPARATOR . 'adverbs.txt';

        $collection = new DefaultWordsCollection('remove_words', ['adverbs'], $buildDirectory);

        $this->assertFileDoesNotExist($expectedFile);
        $words = $collection->getWords();
        $this->assertFileExists($expectedFile);

        $this->assertNotEmpty($words);
        $this->deleteFileWithAssertion($expectedFile);
        $this->deleteDirectoryWithAssertion($wordsDirectory);
    }

    private function isOneDimensionArray(array $arr): bool
    {
        return count($arr) === count($arr, COUNT_RECURSIVE);
    }
}
