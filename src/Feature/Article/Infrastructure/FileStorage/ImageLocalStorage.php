<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\FileStorage;

use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Config\Config;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Utils\File;
use Romira\Zenita\Utils\Random\Random;

class ImageLocalStorage implements ImageStorageInterface
{
    const int FILE_NAME_LENGTH = 16;

    public function __construct(
        private readonly string $rootDir
    )
    {
    }

    /**
     * @return string file path
     * @throws InvalidUploadImageException
     */
    public function moveUploadedFile(string $tmpName): string
    {
        $fileName = Random::string(self::FILE_NAME_LENGTH) . '.' . File::getExtensionFromImage($tmpName);

        if (!File::moveUploadedFile($tmpName, $this->rootDir . '/images/' . $fileName)) {
            throw new InvalidUploadImageException('Failed to move uploaded file');
        }

        return Config::IMAGE_PATH_PREFIX . $fileName;
    }

    public function deleteImageFile(string $filePath): bool
    {
        return File::remove($this->rootDir . $filePath);
    }
}
