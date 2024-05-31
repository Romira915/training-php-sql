<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\QueryServices;

use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\TopPagePublishedArticleSummaryDTO;
use Romira\Zenita\Feature\Article\Application\QueryServices\ArticleSummaryQueryServiceInterface;

readonly class ArticleSummaryQueryService implements ArticleSummaryQueryServiceInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @param int $limit
     * @return array<TopPagePublishedArticleSummaryDTO>
     */
    public function getArticleSummaryList(int $limit): array
    {
        $statement = $this->pdo->prepare('
            SELECT a.id,
                   ad.user_id,
                   ad.title,
                   ad.body,
                   ai.image_path AS thumbnail_url,
                   ad.created_at,
                   ad.updated_at,
                   json_agg(at.tag_name) AS tags
            FROM articles as a
                     INNER JOIN article_published AS ap ON a.id = ap.article_id AND a.user_id = ap.user_id
                     INNER JOIN article_detail AS ad ON a.id = ad.article_id AND a.user_id = ad.user_id
                     INNER JOIN article_images AS ai ON ad.thumbnail_id = ai.id
                     LEFT JOIN article_tags AS at ON a.id = at.article_id AND a.user_id = at.user_id
            GROUP BY a.id, ad.user_id, ad.title, ad.body, ai.image_path, ad.created_at, ad.updated_at
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
            $tags = json_decode($r['tags'], true);
            if ($tags[0] === null) {
                $tags = [];
            }

            $result[] = new TopPagePublishedArticleSummaryDTO(
                id: (int)$r['id'],
                user_id: (int)$r['user_id'],
                title: $r['title'],
                body: $r['body'],
                thumbnail_image_path: $r['thumbnail_url'],
                tags: $tags
            );
        }

        return $result;
    }
}
