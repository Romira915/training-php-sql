<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\CreatePublishedArticleDTO;
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
    public static function run(PDO $pdo, PublishedArticleRepositoryInterface $articleRepository, ImageStorageInterface $imageStorage, CreatePublishedArticleDTO $publishedArticleDTO): void
    {
        $image_path = $imageStorage->moveUploadedFile($publishedArticleDTO->thumbnail_image_path);
        $thumbnail = new ArticleImage(user_id: $publishedArticleDTO->user_id, image_path: $image_path);
        $article = new PublishedArticle(
            user_id: $publishedArticleDTO->user_id,
            title: $publishedArticleDTO->title,
            body: $publishedArticleDTO->body,
            thumbnail: $thumbnail,
            images: []
        );

        $articleRepository::save($pdo, $article);
    }
}
