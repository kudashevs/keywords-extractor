<?php

namespace Kudashevs\KeywordsExtractor\Tests\Unit\Collections;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class CollectionTestCase extends PHPUnitTestCase
{
    protected function deleteFile(string $filename): bool
    {
        return @unlink($filename);
    }

    protected function deleteFileWithAssertion(string $filename): void
    {
        $this->deleteFile($filename);

        $this->assertFileDoesNotExist($filename);
    }

    protected function deleteDirectory(string $dirname): bool
    {
        return @rmdir($dirname);
    }

    protected function deleteDirectoryWithAssertion(string $dirname): void
    {
        $this->deleteDirectory($dirname);

        $this->assertFileDoesNotExist($dirname);
    }
}
