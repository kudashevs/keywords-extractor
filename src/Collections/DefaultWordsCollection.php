<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Collections;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

/**
 * WordsCollection represents a ValueObject with a collection of words lists.
 */
final class DefaultWordsCollection implements WordsCollection
{
    private const DEFAULT_WORDS_LISTS_PATH = __DIR__ . '/../../assets/words';

    private string $name;

    private array $lists;

    public function __construct(array $wordsLists)
    {
        $this->initLists($wordsLists);
    }

    /**
     * @param array<array-key, array> $lists
     */
    private function initLists(array $lists): void
    {
        foreach ($lists as $list) {
            if (!file_exists($this->generateListFilePath($list))) {
                throw new InvalidOptionValue(
                    sprintf(
                        'There is no corresponding file %s for the list %s',

                        $this->generateListFilePath($list),
                        $list,
                    )
                );
            }

            $this->lists[] = $this->generateListFilePath($list);
        }
    }

    private function generateListFilePath(string $name): string
    {
        $realPath = realpath(self::DEFAULT_WORDS_LISTS_PATH);

        return $realPath . DIRECTORY_SEPARATOR . $name . '.txt';
    }

    /**
     * @return array<array-key, string>
     */
    public function getWords(): array
    {
        $rawWords = array_reduce($this->lists, function ($acc, $list) {
            $words = @file($list, FILE_IGNORE_NEW_LINES) ?: [];

            return array_merge($acc, $words);
        }, []);

        return array_filter($rawWords, function ($word) {
            return !str_starts_with($word, '#');
        });
    }
}
