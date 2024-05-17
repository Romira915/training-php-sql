<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\Article;
use Romira\Zenita\Feature\Article\Domain\Repositories\ArticleRepositoryInterface;
use Romira\Zenita\Feature\Article\Domain\Service\ArticleService;

class GetArticleListAction
{
    /**
     * @param PDO $pdo
     * @param ArticleRepositoryInterface $articleRepository
     * @param int $limit
     * @return array<Article>
     */
    public static function run(PDO $pdo, ArticleRepositoryInterface $articleRepository, int $limit): array
    {
        return ArticleService::getArticle($pdo, $articleRepository, $limit);
    }
}
