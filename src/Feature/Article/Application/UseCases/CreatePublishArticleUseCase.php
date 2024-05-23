<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use PDO;
use Romira\Zenita\Config\Config;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidImageLimitException;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Domain\Repositories\PublishedArticleRepositoryInterface;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;

class CreatePublishArticleUseCase
{

    /**
     * @throws InvalidUploadImageException|InvalidImageLimitException
     */
    public static function run(PDO $pdo, PublishedArticleRepositoryInterface $articleRepository, ImageStorageInterface $imageStorage, string $document_root, string $title, string $body, string $thumbnail_tmp_name): void
    {
        $image_path = Config::IMAGE_PATH_PREFIX . $imageStorage::moveUploadedFileToPublic($document_root, $thumbnail_tmp_name);
        $thumbnail = new ArticleImage(user_id: 1, image_path: $image_path);
        $article = new PublishedArticle(
            user_id: 1,
            title: $title,
            body: $body,
            thumbnail: $thumbnail,
            images: []
        );

        $articleRepository::save($pdo, $article);
    }
}
