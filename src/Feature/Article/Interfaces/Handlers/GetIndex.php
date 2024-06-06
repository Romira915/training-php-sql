<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Exception;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Infrastructure\QueryServices\CurrentUserServiceQuery;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Common\Interfaces\Session\Session;
use Romira\Zenita\Feature\Article\Infrastructure\QueryServices\ArticleSummaryQueryService;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\Session\TopPageErrorSession;
use Romira\Zenita\Feature\Article\Presentation\IndexViewHelper;

class GetIndex implements SessionHandlerInterface
{
    public const int ARTICLE_LIMIT = 20;

    /**
     * @throws Exception
     */
    public static function handle(HttpRequest $request, array $matches, ?Session &$session = null): HttpResponse
    {
        $limit = $request->get['limit'] ?? self::ARTICLE_LIMIT;
        $limit = (int)$limit;
        if ($limit < 1) {
            return new HttpResponse(statusCode: 400, body: 'limit must be greater than 0');
        }

        $currentUserSession = new CurrentUserSession($session);

        $pdo = PostgresqlConnection::connect();
        $articleSummaryQueryService = new ArticleSummaryQueryService($pdo);

        $articles = $articleSummaryQueryService->getArticleSummaryList($limit);
        $currentUserId = $currentUserSession->getCurrentUser();
        $currentUserDTO = null;
        if ($currentUserId !== null) {
            $currentUserDTO = CurrentUserServiceQuery::getCurrentUserById($pdo, $currentUserId);
        }

        $topPageErrorSession = new TopPageErrorSession($session);
        $helper = new IndexViewHelper($articles, $currentUserDTO, $topPageErrorSession->flashTopPageErrorMessage());
        $html = $helper->render();

        return new HttpResponse(statusCode: 200, body: $html);
    }
}
