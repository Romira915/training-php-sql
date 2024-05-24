<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\QueryServices;

use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\TopPagePublishedArticleSummaryDTO;

interface ArticleSummaryQueryServiceInterface
{
    /**
     * @param PDO $pdo
     * @param int $limit
     * @return array<TopPagePublishedArticleSummaryDTO>
     */
    public static function getArticleSummaryList(PDO $pdo, int $limit): array;
}
