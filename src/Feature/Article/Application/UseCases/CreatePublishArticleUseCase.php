<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use Exception;
use PDO;
use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Article\Application\DTO\CreatePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleTag;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidImageLimitException;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Domain\Repositories\PublishedArticleRepositoryInterface;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleImageList;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleTagList;

class CreatePublishArticleUseCase
{

    /**
     * @throws InvalidUploadImageException|InvalidImageLimitException
     */
    public static function run(PDO $pdo, PublishedArticleRepositoryInterface $articleRepository, ImageStorageInterface $imageStorage, CreatePublishedArticleDTO $publishedArticleDTO): void
    {
        $pdo->beginTransaction();
        try {
            $image_path = $imageStorage->moveUploadedFile($publishedArticleDTO->thumbnail_image_path);
            $thumbnail = new ArticleImage(user_id: $publishedArticleDTO->user_id, image_path: $image_path);

            $images = array_map(
                fn($image_path) => new ArticleImage(user_id: $publishedArticleDTO->user_id, image_path: $imageStorage->moveUploadedFile($image_path)),
                $publishedArticleDTO->image_path_list
            );

            $tags = array_map(
                fn($tag) => new ArticleTag(user_id: $publishedArticleDTO->user_id, tag_name: $tag),
                $publishedArticleDTO->tags
            );

            $article = new PublishedArticle(
                user_id: $publishedArticleDTO->user_id,
                title: $publishedArticleDTO->title,
                body: $publishedArticleDTO->body,
                thumbnail: $thumbnail,
                images: new ArticleImageList($images),
                tags: new ArticleTagList($tags)
            );

            $articleRepository::save($pdo, $article);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
