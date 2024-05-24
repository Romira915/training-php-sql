<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\QueryServices;

use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\TopPagePublishedArticleSummaryDTO;
use Romira\Zenita\Feature\Article\Application\QueryServices\ArticleSummaryQueryServiceInterface;

class ArticleSummaryQueryService implements ArticleSummaryQueryServiceInterface
{
    /**
     * @param PDO $pdo
     * @param int $limit
     * @return array<TopPagePublishedArticleSummaryDTO>
     */
    public static function getArticleSummaryList(PDO $pdo, int $limit): array
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
            $result[] = new TopPagePublishedArticleSummaryDTO(
                id: (int)$r['id'],
                user_id: (int)$r['user_id'],
                title: $r['title'],
                body: $r['body'],
                thumbnail_image_path: $r['thumbnail_url'],
            );
        }

        return $result;
    }
}
