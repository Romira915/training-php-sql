<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\TopPagePublishedArticleSummaryDTO;
use Romira\Zenita\Feature\Article\Application\QueryServices\ArticleSummaryQueryServiceInterface;

class GetArticleListUseCase
{
    /**
     * @param PDO $pdo
     * @param ArticleSummaryQueryServiceInterface $articleSummaryQueryService
     * @param int $limit
     * @return array<TopPagePublishedArticleSummaryDTO>
     */
    public static function run(PDO $pdo, ArticleSummaryQueryServiceInterface $articleSummaryQueryService, int $limit): array
    {
        return $articleSummaryQueryService::getArticleSummaryList($pdo, $limit);
    }
}
