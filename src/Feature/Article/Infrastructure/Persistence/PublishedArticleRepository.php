<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\Persistence;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Domain\Repositories\PublishedArticleRepositoryInterface;

class PublishedArticleRepository implements PublishedArticleRepositoryInterface
{
    public static function save(PDO $pdo, PublishedArticle $article): PublishedArticle
    {
        if ($article->getId() !== null) {
            $article_id = $article->getId();
        } else {
            $article_id = self::createArticle($pdo, $article->getUserId());
        }
        self::createPublishedArticleIfNotExists($pdo, $article_id, $article->getUserId());

        // TODO: Set article_id to thumbnail and images
        $thumbnail = new ArticleImage(
            user_id: $article->getThumbnail()->getUserId(),
            image_path: $article->getThumbnail()->getImagePath(),
            id: $article->getThumbnail()->getId(),
            article_id: $article_id
        );
        if ($thumbnail->getId() === null) {
            $thumbnail = self::createArticleImage($pdo, $thumbnail);
        } else {
            self::updateArticleImage($pdo, $thumbnail);
        }

        $article = new PublishedArticle(
            user_id: $article->getUserId(),
            title: $article->getTitle(),
            body: $article->getBody(),
            thumbnail: $thumbnail,
            images: $article->getImages(),
            id: $article_id
        );

        self::upsertArticleDetail($pdo, $article);

        return $article;
    }

    public static function findByUserIdAndArticleId(PDO $pdo, int $user_id, int $article_id): PublishedArticle|null
    {
        $statement = $pdo->prepare("
            SELECT ap.article_id,
                   ap.user_id,
                   ad.title,
                   ad.body,
                   tnai.id AS thumbnail_id,
                   tnai.image_path         AS thumbnail_path,
                   json_agg(json_build_object('id', ai.id, 'path', ai.image_path)) AS image_list
            FROM article_detail AS ad
                     JOIN articles AS a ON ad.article_id = a.id
                     JOIN article_published AS ap ON ad.article_id = ap.article_id
                     JOIN article_images AS tnai ON ad.thumbnail_id = tnai.id
                     JOIN article_images AS ai ON ad.article_id = ai.article_id
            WHERE ap.article_id = :article_id
              AND ap.user_id = :user_id
            GROUP BY ap.article_id,
                     ap.user_id,
                     ad.title,
                     ad.body,
                     tnai.id,
                     tnai.image_path
        ");
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $image_list = json_decode($row['image_list'], true);
        $image_list = array_map(
            fn(array $image) => new ArticleImage(
                user_id: $user_id,
                image_path: $image['path'],
                id: $image['id'],
                article_id: $article_id
            ),
            $image_list
        );

        return new PublishedArticle(
            user_id: (int)$row['user_id'],
            title: $row['title'],
            body: $row['body'],
            thumbnail: new ArticleImage(
                user_id: $user_id,
                image_path: $row['thumbnail_path'],
                id: (int)$row['thumbnail_id'],
                article_id: $article_id
            ),
            images: $image_list,
            id: (int)$row['article_id']
        );
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

    private static function createPublishedArticleIfNotExists(PDO $pdo, int $article_id, int $user_id): void
    {
        $statement = $pdo->prepare('
        INSERT INTO article_published (article_id, user_id)
        SELECT :article_id, :user_id
        WHERE NOT EXISTS (SELECT 1 FROM article_published WHERE article_id = :article_id AND user_id = :user_id);
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id
        ]);
    }

    private static function upsertArticleDetail(PDO $pdo, PublishedArticle $article): void
    {
        $statement = $pdo->prepare('
        INSERT INTO article_detail (article_id, user_id, title, body, thumbnail_id)
        VALUES (:article_id, :user_id, :title, :body, :thumbnail_id)
        ON CONFLICT (article_id, user_id)
            DO UPDATE SET title        = :title,
                          body         = :body,
                          thumbnail_id = :thumbnail_id
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

    private static function updateArticleImage(PDO $pdo, ArticleImage $image): void
    {
        $statement = $pdo->prepare('
            UPDATE article_images SET image_path = :image_path
            WHERE id = :id
        ');
        $statement->execute([
            'image_path' => $image->getImagePath(),
            'id' => $image->getId(),
        ]);
    }
}
