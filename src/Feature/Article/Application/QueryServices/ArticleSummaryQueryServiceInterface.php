<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\QueryServices;

use Romira\Zenita\Feature\Article\Application\DTO\TopPagePublishedArticleSummaryDTO;

interface ArticleSummaryQueryServiceInterface
{
    /**
     * @param int $limit
     * @return array<TopPagePublishedArticleSummaryDTO>
     */
    public function getArticleSummaryList(int $limit): array;
}
