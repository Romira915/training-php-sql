<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\QueryServices;

use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\ArticleDetailEditPageDTO;

class ArticleDetailEditQueryService
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
    public function getArticleDetail(int $article_id, int $user_id): ArticleDetailEditPageDTO|null
    {
        $statement = $this->pdo->prepare('
            SELECT ap.article_id,
                   ap.user_id,
                   ad.title,
                   ad.body
            FROM article_detail AS ad
                     JOIN articles AS a ON ad.article_id = a.id
                     JOIN article_published AS ap ON ad.article_id = ap.article_id
            WHERE ap.article_id = :article_id
              AND ap.user_id = :user_id
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new ArticleDetailEditPageDTO(
            article_id: (int)$row['article_id'],
            user_id: (int)$row['user_id'],
            title: $row['title'],
            body: $row['body'],
        );
    }
}
