<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\QueryServices;

use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\ArticleDetailEditPageDTO;
use Romira\Zenita\Feature\Article\Application\DTO\DraftArticleDetailEditPageDTO;

class DraftArticleDetailEditQueryService
{
    public function __construct(
        private PDO $pdo
    )
    {
    }

    /**
     * @param int $article_id
     * @param int $user_id
     * @return ArticleDetailEditPageDTO|null
     */
    public function getArticleDetail(int $article_id, int $user_id): DraftArticleDetailEditPageDTO|null
    {
        $statement = $this->pdo->prepare("
            SELECT ap.article_id,
                   ap.user_id,
                   ad.title,
                   ad.body,
                   tnai.id                                          AS thumbnail_id,
                   tnai.image_path                                  AS thumbnail_path,
                   COALESCE(jsonb_agg(DISTINCT jsonb_build_object('id', ai.id, 'image_path', ai.image_path))
                            FILTER (WHERE ai.id IS NOT NULL), '[]') AS image_path_list,
                   COALESCE(jsonb_agg(DISTINCT jsonb_build_object('id', at.id, 'tag_name', at.tag_name)) FILTER (WHERE at.id IS NOT NULL),
                            '[]')                                   AS tag_name_list
            FROM article_detail AS ad
                     INNER JOIN articles AS a ON ad.article_id = a.id
                     INNER JOIN article_draft AS ap ON ad.article_id = ap.article_id
                     LEFT JOIN article_images AS tnai ON ad.thumbnail_id = tnai.id
                     LEFT JOIN article_images AS ai ON ad.article_id = ai.article_id
                     LEFT JOIN article_tags AS at ON ad.article_id = at.article_id
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

        $image_url_list = array_filter(json_decode($row['image_path_list'], true), function ($image) use ($row) {
            return $image['id'] !== $row['thumbnail_id'];
        });
        $image_url_list = array_map(function ($image) {
            return $image['image_path'];
        }, $image_url_list);

        $tag_name_list = array_map(function ($tag) {
            return $tag['tag_name'];
        }, json_decode($row['tag_name_list'], true));

        return new DraftArticleDetailEditPageDTO(
            article_id: (int)$row['article_id'],
            user_id: (int)$row['user_id'],
            title: $row['title'],
            body: $row['body'],
            thumbnail_image_url: $row['thumbnail_path'],
            image_url_list: $image_url_list,
            tags: $tag_name_list,
        );
    }
}
