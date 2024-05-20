<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\Persistence;

use DateTimeImmutable;
use Exception;
use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\Article;
use Romira\Zenita\Feature\Article\Domain\Repositories\ArticleRepositoryInterface;

class ArticleRepository implements ArticleRepositoryInterface
{
    /**
     * image_url_listは取得しない
     *
     * @param PDO $pdo
     * @param int $limit
     * @return array<Article>
     * @throws Exception
     */
    public static function getExcludeImageUrlList(PDO $pdo, int $limit): array
    {
        $statement = $pdo->prepare('
        SELECT a.id, ad.user_id, ad.title, ad.body, ai.image_url AS thumbnail_url, ad.created_at, ad.updated_at
        FROM articles as a
                 INNER JOIN article_published AS ap ON a.id = ap.article_id AND a.user_id = ap.user_id
                 INNER JOIN article_detail AS ad ON a.id = ad.article_id AND a.user_id = ad.user_id
                 INNER JOIN article_images AS ai ON ad.thumbnail_id = ai.id
        ORDER BY ad.created_at DESC
        LIMIT :limit
            ');
        $statement->execute(['limit' => $limit]);

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($row === false) {
            return [];
        }

        $result = [];
        foreach ($row as $r) {
            $result[] = new Article(
                id: (int)$r['id'],
                user_id: (int)$r['user_id'],
                title: $r['title'],
                body: $r['body'],
                thumbnail_url: $r['thumbnail_url'],
                image_url_list: [],
                created_at: new DateTimeImmutable($r['created_at']),
                updated_at: new DateTimeImmutable($r['updated_at'])
            );
        }

        return $result;
    }

    /**
     * @param PDO $pdo
     * @param int $user_id
     * @return int article_id
     */
    public static function createArticle(PDO $pdo, int $user_id): int
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

    public static function createPublishedArticle(PDO $pdo, int $article_id, int $user_id): void
    {
        $statement = $pdo->prepare('
        INSERT INTO article_published (article_id, user_id) VALUES (:article_id, :user_id)
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id
        ]);
    }

    public static function createArticleDetail(PDO $pdo, Article $article, int $thumbnail_id): void
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
            'thumbnail_id' => $thumbnail_id
        ]);
    }

    public static function createArticleImage(PDO $pdo, int $article_id, int $user_id, string $image_url): int
    {
        $statement = $pdo->prepare('
        INSERT INTO article_images (article_id, user_id, image_url)
        VALUES (:article_id, :user_id, :image_url) RETURNING id
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
            'image_url' => $image_url
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return (int)$row['id'];
    }
}
