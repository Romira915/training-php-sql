<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Exception;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Feature\Article\Application\UseCases\GetArticleListUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\QueryServices\ArticleSummaryQueryService;
use Romira\Zenita\Feature\Article\Presentation\IndexViewHelper;

class GetIndex implements HandlerInterface
{
    public const int ARTICLE_LIMIT = 20;

    /**
     * @throws Exception
     */
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        $limit = $request->get['limit'] ?? self::ARTICLE_LIMIT;
        $limit = (int)$limit;
        if ($limit < 1) {
            return new HttpResponse(statusCode: 400, body: 'limit must be greater than 0');
        }

        $pdo = PostgresqlConnection::connect();
        $articleSummaryQueryService = new ArticleSummaryQueryService();

        $articles = GetArticleListUseCase::run($pdo, $articleSummaryQueryService, $limit);

        $helper = new IndexViewHelper($articles);
        $html = $helper->render();

        return new HttpResponse(statusCode: 200, body: $html);
    }
}
