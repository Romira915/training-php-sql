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

    public static function createPublishedArticle(PDO $pdo, int $article_id, int $user_id): void;

    public static function createArticleDetail(PDO $pdo, Article $article, int $thumbnail_id): void;

    /**
     * @param PDO $pdo
     * @param int $article_id
     * @param int $user_id
     * @param string $image_url
     * @return int image_id
     */
    public static function createArticleImage(PDO $pdo, int $article_id, int $user_id, string $image_url): int;
}
