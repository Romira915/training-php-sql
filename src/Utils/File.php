<?php

declare(strict_types=1);

namespace Romira\Zenita\Utils;

class File
{
    public static function copy(string $from, string $to): bool
    {
        return copy($from, $to);
    }

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

    /**
     * @param array $files
     * @return array $result
     *
     * @example
     * $files = [
     *    'name' => ['file1.jpg', 'file2.jpg'],
     *    'type' => ['image/jpeg', 'image/jpeg'],
     *    'tmp_name' => ['/tmp/php7hj2d', '/tmp/php7hj2e'],
     *    'error' => [0, 0],
     *    'size' => [98174, 98174],
     * ];
     *
     * $result = File::reshapeFilesArray($files);
     *
     * output >>
     * $result = [
     *  0 => [
     *      'name' => 'file1.jpg',
     *      'type' => 'image/jpeg',
     *      'tmp_name' => '/tmp/php7hj2d',
     *      'error' => 0,
     *      'size' => 98174,
     *  ],
     *  1 => [
     *      'name' => 'file2.jpg',
     *      'type' => 'image/jpeg',
     *      'tmp_name' => '/tmp/php7hj2e',
     *      'error' => 0,
     *      'size' => 98174,
     *  ],
     * ];
     */
    public static function reshapeFilesArray(array $files): array
    {
        $result = [];
        foreach ($files as $key => $valueArray) {
            foreach ($valueArray as $index => $value) {
                $result[$index][$key] = $value;
            }
        }

        return $result;
    }
}
