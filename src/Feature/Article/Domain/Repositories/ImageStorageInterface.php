<?php

namespace Romira\Zenita\Feature\Article\Domain\Repositories;

use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;

interface ImageStorageInterface
{
    /**
     * @return string The file name
     *
     * @throws InvalidUploadImageException
     */
    public static function moveUploadedFileToPublic(string $rootDir, string $tmpName): string;
}
