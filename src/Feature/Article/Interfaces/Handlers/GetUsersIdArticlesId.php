<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Feature\Article\Infrastructure\QueryServices\ArticleDetailQueryService;
use Romira\Zenita\Feature\Article\Presentation\PublishedArticleDetailPageViewHelper;

class GetUsersIdArticlesId implements HandlerInterface
{
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        $user_id = (int)$matches['user_id'];
        $article_id = (int)$matches['article_id'];

        $pdo = PostgresqlConnection::connect();
        $articleDetailQueryService = new ArticleDetailQueryService($pdo);

        $articleDetail = $articleDetailQueryService->getArticleDetail($article_id, $user_id);

        $helper = new PublishedArticleDetailPageViewHelper($articleDetail);
        $html = $helper->render();

        return new HttpResponse(statusCode: 200, body: $html);
    }
}
