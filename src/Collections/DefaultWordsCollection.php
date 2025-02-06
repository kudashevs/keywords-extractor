<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Collections;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

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
        $this->initBuildPath($path);
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
     * @param string $path A valid path with write permissions.
     *
     * @throws InvalidOptionValue
     */
    private function initBuildPath(string $path): void
    {
        $this->validateExistingDirectory($path);
        $this->validateWritableDirectory($path);

        $buildPath = $this->generateBuildPath($path);

        if (!file_exists($buildPath)) {
            $this->makeDirectory($buildPath);
        }

        $this->buildPath = $buildPath;
    }

    /**
     * @param array<array-key, array> $lists
     *
     * @throws InvalidOptionValue
     */
    private function initLists(array $lists): void
    {
        foreach ($lists as $list) {
            $this->validateList($list);

            $initFile = $this->generateInitFilePath($list);
            $buildFile = $this->generateBuildFilePath($list);

            if (!file_exists($buildFile)) {
                copy($initFile, $buildFile);
            }

            $this->lists[] = $list;
        }
    }

    private function validateList(string $list): void
    {
        $listFilePath = $this->generateInitFilePath($list);

        if (!file_exists($listFilePath)) {
            throw new InvalidOptionValue(
                sprintf(
                    'There is no corresponding file %s for the list %s.',
                    $listFilePath,
                    $list,
                )
            );
        }
    }

    private function validateExistingDirectory(string $path): void
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new InvalidOptionValue(
                sprintf(
                    'The path %s does not exist or is not a directory.',
                    $path,
                )
            );
        }
    }

    private function validateWritableDirectory(string $path): void
    {
        if (!is_writable($path)) {
            throw new InvalidOptionValue(
                sprintf(
                    'The path %s is not writable.',
                    $path,
                )
            );
        }
    }

    private function makeDirectory(string $path, int $permissions = 0755): void
    {
        if (!mkdir($path, $permissions, false) && !is_dir($path)) {
            throw new InvalidOptionValue(
                sprintf(
                    'The script was not able to create the %s directory.',
                    $path,
                )
            );
        }
    }

    private function generateBuildPath(string $path): string
    {
        $realPath = (rtrim(realpath($path), '\/'));

        return $realPath . DIRECTORY_SEPARATOR . self::WORDS_LISTS_DIRECTORY;
    }

    private function generateInitPath(): string
    {
        return realpath(self::DEFAULT_INIT_LISTS_PATH);
    }

    private function generateInitFilePath(string $name): string
    {
        return $this->generateInitPath() . DIRECTORY_SEPARATOR . $name . '.txt';
    }

    private function generateBuildFilePath(string $name): string
    {
        return $this->buildPath . DIRECTORY_SEPARATOR . $name . '.txt';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getWords(): array
    {
        $rawWords = array_reduce($this->lists, function ($acc, $list) {
            $buildFile = $this->generateBuildFilePath($list);

            $words = @file($buildFile, FILE_IGNORE_NEW_LINES) ?: [];

            return array_merge($acc, $words);
        }, []);

        $filteredWords = array_filter($rawWords, function ($word) {
            return !str_starts_with($word, '#');
        });

        return array_unique($filteredWords);
    }
}
