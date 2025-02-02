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

    private const WORDS_LISTS_DIRECTORY = 'words';

    private string $name;

    private array $lists;

    private string $buildPath;

    public function __construct(string $name, array $wordsLists, string $path = __DIR__ . '/../../assets/')
    {
        $this->initName($name);
        $this->initLists($wordsLists);
        $this->initBuildPath($path);
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

            $this->lists[] = $list;
        }
    }

    private function initBuildPath(string $path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new InvalidOptionValue(
                sprintf(
                    'The path %s does not exist or is not a directory.',
                    $path,
                )
            );
        }

        if (!is_writable($path)) {
            throw new InvalidOptionValue(
                sprintf(
                    'The is not able to use the %s directory.',
                    $path,
                )
            );
        }

        $buildPath = $this->generateBuildPath($path);

        if (!file_exists($buildPath) || !is_dir($buildPath)) {
            if (!mkdir($buildPath, 0755, false) && !is_dir($buildPath)) {
                throw new InvalidOptionValue(
                    sprintf(
                        'The script was not able to create the %s directory.',
                        $buildPath,
                    )
                );
            }
        }

        $this->buildPath = $buildPath;
    }

    private function generateBuildPath(string $path): string
    {
        $realPath = (rtrim(realpath($path), '\/'));

        return $realPath . DIRECTORY_SEPARATOR . self::WORDS_LISTS_DIRECTORY;
    }

    private function generateInitFilePath(string $name): string
    {
        $realPath = realpath(self::DEFAULT_INIT_LISTS_PATH);

        return $realPath . DIRECTORY_SEPARATOR . $name . '.txt';
    }

    private function generateWordsFilePath(string $name): string
    {
        return $this->buildPath . DIRECTORY_SEPARATOR . $name . '.txt';
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
            $initFile = $this->generateInitFilePath($list);
            $wordsFile = $this->generateWordsFilePath($list);

            if (!file_exists($wordsFile)) {
                copy($initFile, $wordsFile);
            }

            $words = @file($wordsFile, FILE_IGNORE_NEW_LINES) ?: [];

            return array_merge($acc, $words);
        }, []);

        return array_filter($rawWords, function ($word) {
            return !str_starts_with($word, '#');
        });
    }
}
