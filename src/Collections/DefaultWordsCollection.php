<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Collections;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

/**
 * WordsCollection represents a ValueObject with a collection of words lists.
 */
final class DefaultWordsCollection implements WordsCollection
{
    private const DEFAULT_INIT_LISTS_PATH = __DIR__ . '/../../assets/init';

    private const DEFAULT_WORDS_LISTS_PATH = __DIR__ . '/../../assets/words';

    private string $name;

    private array $lists;

    public function __construct(string $name, array $wordsLists)
    {
        $this->initName($name);
        $this->initLists($wordsLists);
    }

    private function initName(string $name): void
    {
        if (trim($name) === '') {
            throw new InvalidOptionValue('The collection name cannot be empty.');
        }

        $this->name = $name;
    }

    /**
     * @param array<array-key, array> $lists
     */
    private function initLists(array $lists): void
    {
        foreach ($lists as $list) {
            if (!file_exists($this->generateInitFilePath($list))) {
                throw new InvalidOptionValue(
                    sprintf(
                        'There is no corresponding file %s for the list %s',

                        $this->generateInitFilePath($list),
                        $list,
                    )
                );
            }

            $this->lists[] = $this->generateInitFilePath($list);
        }
    }

    private function generateInitFilePath(string $name): string
    {
        $realPath = realpath(self::DEFAULT_INIT_LISTS_PATH);

        return $realPath . DIRECTORY_SEPARATOR . $name . '.txt';
    }

    /**
     * Get a unique collection name.
     *
     * @return array
     */
    public function getName(): string
    {
        return $this->name;
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
