<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Repositories;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\Article;

interface ArticleRepositoryInterface
{
    /**
     * @param PDO $pdo
     * @param int $limit
     * @return array<Article>
     */
    public static function getExcludeImageUrlList(PDO $pdo, int $limit): array;

    /**
     * @param PDO $pdo
     * @param int $user_id
     * @return int article_id
     */
    public static function createArticle(PDO $pdo, int $user_id): int;

    public static function createPublishedArticle(PDO $pdo, Article $article): void;
}
