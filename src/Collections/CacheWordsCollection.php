<?php

declare(strict_types=1);

namespace Kudashevs\KeywordsExtractor\Collections;

use Kudashevs\KeywordsExtractor\Exceptions\InvalidOptionValue;

/**
 * WordsCollection represents a ValueObject with a collection of words lists.
 */
final class CacheWordsCollection implements WordsCollection
{
    private const WORDS_CACHE_DIRECTORY = 'cache';

    private const DEFAULT_COLLECTION_CLASS = DefaultWordsCollection::class;

    private WordsCollection $collection;

    private string $buildPath;

    public function __construct(string $name, array $wordsLists, string $path = __DIR__ . '/../../assets/')
    {
        $this->initCollection($name, $wordsLists, $path);
        $this->initBuildPath($path);
    }

    private function initCollection(string $name, array $wordsLists): void
    {
        $this->collection = new (self::DEFAULT_COLLECTION_CLASS)($name, $wordsLists);
    }

    /**
     * @param string $path A valid path with write permissions.
     *
     * @throws InvalidOptionValue
     */
    private function initBuildPath(string $path)
    {
        $this->validateExistingDirectory($path);
        $this->validateWritableDirectory($path);

        $buildPath = $this->generateBuildPath($path);

        $this->makeDirectory($buildPath);

        $this->buildPath = $buildPath;
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
                    'The path %s is now writable.',
                    $path,
                )
            );
        }
    }

    private function makeDirectory(string $path, int $permissions = 0755): void
    {
        if (!file_exists($path) || !is_dir($path)) {
            if (!mkdir($path, $permissions, false) && !is_dir($path)) {
                throw new InvalidOptionValue(
                    sprintf(
                        'The script was not able to create the %s directory.',
                        $path,
                    )
                );
            }
        }
    }

    private function generateBuildPath(string $path): string
    {
        $realPath = (rtrim(realpath($path), '\/'));

        return $realPath . DIRECTORY_SEPARATOR . self::WORDS_CACHE_DIRECTORY;
    }

    /**
     * Get a unique collection name.
     *
     * @return array
     */
    public function getName(): string
    {
        return $this->collection->getName();
    }

    /**
     * @return array<array-key, string>
     */
    public function getWords(): array
    {
        if ($this->isCached()) {
            return $this->getCached();
        }

        $words = $this->collection->getWords();

        $this->cache($words);

        return $words;
    }

    private function isCached(): bool
    {
        return file_exists($this->generateCacheFilePath($this->collection->getName()));
    }

    private function generateCacheFilePath(string $name, string $extension = 'dat'): string
    {
        return $this->buildPath . DIRECTORY_SEPARATOR . $name . '.' . $extension;
    }

    private function getCached(): array
    {
        $serializedWords = @file_get_contents($this->generateCacheFilePath($this->collection->getName())) ?: "";

        return unserialize(
            $serializedWords,
            [
                'allowed_classes' => false,
            ]
        ) ?: [];
    }

    private function cache(array $words): bool
    {
        $serializedWords = serialize($words);

        return (bool)file_put_contents(
            $this->generateCacheFilePath($this->collection->getName()),
            $serializedWords,
        );
    }
}
