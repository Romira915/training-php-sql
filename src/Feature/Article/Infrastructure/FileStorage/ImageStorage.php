<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\FileStorage;

use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Utils\File;
use Romira\Zenita\Utils\Random\Random;

class ImageStorage implements ImageStorageInterface
{
    const int FILE_NAME_LENGTH = 16;

    /**
     * @throws InvalidUploadImageException
     */
    public static function moveUploadedFileToPublic(string $rootDir, string $tmpName): string
    {
        $fileName = Random::string(self::FILE_NAME_LENGTH) . '.' . File::getExtensionFromImage($tmpName);

        if (!File::moveUploadedFile($tmpName, $rootDir . '/images/' . $fileName)) {
            throw new InvalidUploadImageException('Failed to move uploaded file');
        }

        return $fileName;
    }
}
