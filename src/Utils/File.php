<?php

declare(strict_types=1);

namespace Romira\Zenita\Utils;

class File
{
    public static function moveUploadedFile(string $source, string $destination): bool
    {
        return move_uploaded_file($source, $destination);
    }

    public static function getExtensionFromImage(string $imagePath): string
    {
        return match (exif_imagetype($imagePath)) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_GIF => 'gif',
            default => 'unknown',
        };
    }

    public static function remove(string $filePath): bool
    {
        return unlink($filePath);
    }
}
