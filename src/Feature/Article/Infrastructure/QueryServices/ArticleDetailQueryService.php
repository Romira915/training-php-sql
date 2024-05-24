<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\QueryServices;

use PDO;
use Romira\Zenita\Config\Config;
use Romira\Zenita\Feature\Article\Application\DTO\PublishedArticleDetailPageDTO;

readonly class ArticleDetailQueryService
{
    public function __construct(
        private PDO $pdo
    )
    {
    }

    /**
     * @param int $article_id
     * @param int $user_id
     * @return PublishedArticleDetailPageDTO
     */
    public function getArticleDetail(int $article_id, int $user_id): PublishedArticleDetailPageDTO
    {
        $statement = $this->pdo->prepare('
            SELECT
                ap.article_id,
                ap.user_id,
                ad.title,
                ad.body,
                ai.image_path,
                ap.created_at,
                ad.updated_at
            FROM
                article_detail AS ad
            JOIN articles AS a ON ad.article_id = a.id
            JOIN article_published AS ap ON ad.article_id = ap.article_id
            JOIN article_images AS ai ON ad.thumbnail_id = ai.id
            WHERE
                ap.article_id = :article_id
                AND ap.user_id = :user_id
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        $image_url_list = $this->getArticleImageUrlListByArticleIdAndUserId($article_id, $user_id);

        return new PublishedArticleDetailPageDTO(
            article_id: (int)$row['article_id'],
            user_id: (int)$row['user_id'],
            title: $row['title'],
            body: $row['body'],
            thumbnail_image_url: Config::getImageBaseUrl() . $row['image_path'],
            image_url_list: $image_url_list,
            created_at: $row['created_at'],
            updated_at: $row['updated_at'],
        );
    }

    /**
     * @param int $article_id
     * @param int $user_id
     * @return string[] image url list
     */
    public function getArticleImageUrlListByArticleIdAndUserId(int $article_id, int $user_id): array
    {
        $statement = $this->pdo->prepare('
            SELECT
                ai.image_path
            FROM
                article_images AS ai
            JOIN article_published AS ap ON ai.article_id = ap.article_id
            WHERE
                ap.article_id = :article_id
                AND ap.user_id = :user_id
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
        ]);

        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!$row) {
            return [];
        }

        $result = [];
        foreach ($row as $r) {
            $result[] = Config::getImageBaseUrl() . $r['image_path'];
        }

        return $result;
    }
}
