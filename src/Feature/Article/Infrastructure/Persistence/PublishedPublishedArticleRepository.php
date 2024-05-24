<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\Persistence;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Domain\Repositories\PublishedArticleRepositoryInterface;

class PublishedPublishedArticleRepository implements PublishedArticleRepositoryInterface
{
    public static function save(PDO $pdo, PublishedArticle $article): PublishedArticle
    {
        $article_id = self::createArticle($pdo, $article->getUserId());
        self::createPublishedArticle($pdo, $article_id, $article->getUserId());

        // TODO: Set article_id to thumbnail and images
        $thumbnail = new ArticleImage(
            user_id: $article->getThumbnail()->getUserId(),
            image_path: $article->getThumbnail()->getImagePath(),
            article_id: $article_id
        );
        $thumbnail = self::createArticleImage($pdo, $thumbnail);

        $article = new PublishedArticle(
            id: $article_id,
            user_id: $article->getUserId(),
            title: $article->getTitle(),
            body: $article->getBody(),
            thumbnail: $thumbnail,
            images: $article->getImages()
        );

        self::createArticleDetail($pdo, $article);

        return $article;
    }

    /**
     * @param PDO $pdo
     * @param int $user_id
     * @return int article_id
     */
    private static function createArticle(PDO $pdo, int $user_id): int
    {
        $statement = $pdo->prepare('
        INSERT INTO articles (id, user_id) VALUES ((
            SELECT COALESCE(MAX(id), 0) + 1 FROM articles
        ), :user_id) RETURNING id
        ');
        $statement->execute(['user_id' => $user_id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return (int)$row['id'];
    }

    private static function createPublishedArticle(PDO $pdo, int $article_id, int $user_id): void
    {
        $statement = $pdo->prepare('
        INSERT INTO article_published (article_id, user_id) VALUES (:article_id, :user_id)
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id
        ]);
    }

    private static function createArticleDetail(PDO $pdo, PublishedArticle $article): void
    {
        $statement = $pdo->prepare('
        INSERT INTO article_detail (article_id, user_id, title, body, thumbnail_id)
        VALUES (:article_id, :user_id, :title, :body, :thumbnail_id)
        ');
        $statement->execute([
            'article_id' => $article->getId(),
            'user_id' => $article->getUserId(),
            'title' => $article->getTitle(),
            'body' => $article->getBody(),
            'thumbnail_id' => $article->getThumbnail()->getId()
        ]);
    }

    private static function createArticleImage(PDO $pdo, ArticleImage $image): ArticleImage
    {
        $statement = $pdo->prepare('
        INSERT INTO article_images (article_id, user_id, image_path)
        VALUES (:article_id, :user_id, :image_path) RETURNING id
        ');
        $statement->execute([
            'article_id' => $image->getArticleId(),
            'user_id' => $image->getUserId(),
            'image_path' => $image->getImagePath()
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return new ArticleImage(
            user_id: $image->getUserId(),
            image_path: $image->getImagePath(),
            id: (int)$row['id'],
            article_id: $image->getArticleId()
        );
    }
}
