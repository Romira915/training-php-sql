<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Service;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\Article;
use Romira\Zenita\Feature\Article\Domain\Repositories\ArticleRepositoryInterface;

class ArticleService
{
    /**
     * @param PDO $pdo
     * @param ArticleRepositoryInterface $articleRepository
     * @param int $limit
     * @return array<Article>
     */
    public static function getArticle(PDO $pdo, ArticleRepositoryInterface $articleRepository, int $limit): array
    {
        return $articleRepository::getExcludeImageUrlList($pdo, $limit);
    }
}
