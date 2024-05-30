<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Http;

use InvalidArgumentException;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidArticleParameterException;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Article\Interfaces\Validator\BodyValidator;
use Romira\Zenita\Feature\Article\Interfaces\Validator\TitleValidator;
use Romira\Zenita\Feature\Article\Interfaces\Validator\UploadImageValidator;

readonly class PostUsersIdArticlesRequest
{
    private function __construct(
        public int    $user_id,
        public string $title,
        public string $body,
        /** @var array{
         *     name: string,
         *     full_path: string,
         *     type: string,
         *     tmp_name: string,
         *     error: int,
         *     size: int,
         *     }
         */
        public array  $thumbnail_file,
        /** @var array{
         *     array{
         *         name: string,
         *         full_path: string,
         *         type: string,
         *         tmp_name: string,
         *         error: int,
         *         size: int,
         *     }
         * }
         */
        public array  $image_files,
    )
    {
    }

    public static function new(string $user_id, string $title, string $body, array $thumbnail_file, array $image_files): PostUsersIdArticlesRequest|InvalidArgumentException|InvalidArticleParameterException|InvalidUploadImageException
    {
        if (!is_numeric($user_id)) {
            return new InvalidArgumentException('Invalid user_id');
        }

        if (!TitleValidator::validate($title) || !BodyValidator::validate($body)) {
            return new InvalidArticleParameterException('Invalid title or body');
        }

        if (empty($thumbnail_file)) {
            return new InvalidUploadImageException('Invalid image');
        }

        $upload_image_validator = UploadImageValidator::validate($thumbnail_file);
        if ($upload_image_validator instanceof InvalidUploadImageException) {
            return $upload_image_validator;
        }

        foreach ($image_files as $image_file) {
            $upload_image_validator = UploadImageValidator::validate($image_file);
            if ($upload_image_validator instanceof InvalidUploadImageException) {
                return $upload_image_validator;
            }
        }

        return new self((int)$user_id, $title, $body, $thumbnail_file, $image_files);
    }
}
