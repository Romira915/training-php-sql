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
     * @return PublishedArticleDetailPageDTO|null
     */
    public function getArticleDetail(int $article_id, int $user_id): PublishedArticleDetailPageDTO|null
    {
        $statement = $this->pdo->prepare("
            SELECT ap.article_id,
                   ap.user_id,
                   ad.title,
                   ad.body,
                   tnai.image_path                                  AS thumbnail_path,
                   COALESCE(jsonb_agg(DISTINCT jsonb_build_object('id', ai.id, 'image_path', ai.image_path))
                            FILTER (WHERE ai.id IS NOT NULL), '[]') AS image_path_list,
                   COALESCE(jsonb_agg(DISTINCT jsonb_build_object('id', at.id, 'tag_name', at.tag_name)) FILTER (WHERE at.id IS NOT NULL),
                            '[]')                                   AS tag_name_list,
                   ap.created_at,
                   ad.updated_at
            FROM article_detail AS ad
                     JOIN articles AS a ON ad.article_id = a.id
                     JOIN article_published AS ap ON ad.article_id = ap.article_id
                     JOIN article_images AS tnai ON ad.thumbnail_id = tnai.id
                     LEFT JOIN article_images AS ai ON ad.article_id = ai.article_id
                     LEFT JOIN article_tags AS at ON ad.article_id = at.article_id
            WHERE ap.article_id = :article_id
              AND ap.user_id = :user_id
            GROUP BY ap.article_id,
                     ap.user_id,
                     ad.title,
                     ad.body,
                     tnai.image_path,
                     ap.created_at,
                     ad.updated_at
        ");
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        /** @var array{
         *     id: int,
         *     image_path: string
         * } $image_path_list
         */
        $image_path_list = json_decode($row['image_path_list'], true);
        $image_url_list = [];
        foreach ($image_path_list as $image_path) {
            $image_url_list[] = Config::getImageBaseUrl() . $image_path['image_path'];
        }

        /**
         * @var array{
         *     id: int,
         *     tag_name: string
         * } $tags
         */
        $tags = json_decode($row['tag_name_list'], true);
        $tags = array_map(fn($tag) => $tag['tag_name'], $tags);

        return new PublishedArticleDetailPageDTO(
            article_id: (int)$row['article_id'],
            user_id: (int)$row['user_id'],
            title: $row['title'],
            body: $row['body'],
            thumbnail_image_url: Config::getImageBaseUrl() . $row['thumbnail_path'],
            image_url_list: $image_url_list,
            tags: $tags,
            created_at: $row['created_at'],
            updated_at: $row['updated_at'],
        );
    }
}
