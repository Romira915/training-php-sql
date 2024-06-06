<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use InvalidArgumentException;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Http\SeeOtherResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Common\Interfaces\Session\Session;
use Romira\Zenita\Feature\Article\Infrastructure\QueryServices\ArticleDetailEditQueryService;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\Session\TopPageErrorSession;
use Romira\Zenita\Feature\Article\Interfaces\Http\GetUsersIdArticlesIdEditRequest;
use Romira\Zenita\Feature\Article\Presentation\ArticleDetailEditPageViewHelper;

class GetUsersIdArticlesIdEdit implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $currentUserSession = new CurrentUserSession($session);
        if (!$currentUserSession->isLoggedIn()) {
            return new SeeOtherResponse('/auth/login');
        }

        $editArticlePageRequest = GetUsersIdArticlesIdEditRequest::new($matches['user_id'], $matches['article_id']);

        if ($editArticlePageRequest instanceof InvalidArgumentException) {
            return new HttpResponse(statusCode: 404, body: 'Not Found');
        }

        if ($editArticlePageRequest->user_id !== $currentUserSession->getCurrentUser()) {
            (new TopPageErrorSession($session))->setTopPageErrorMessage('他のユーザーの投稿は、編集できません');
            return new SeeOtherResponse('/');
        }

        $pdo = PostgresqlConnection::connect();
        $articleDetailEditQueryService = new ArticleDetailEditQueryService($pdo);
        $article = $articleDetailEditQueryService->getArticleDetail($editArticlePageRequest->article_id, $editArticlePageRequest->user_id);

        if ($article === null) {
            return new HttpResponse(statusCode: 404, body: 'Not Found');
        }

        $helper = new ArticleDetailEditPageViewHelper($article);
        $html = $helper->render();

        return new HttpResponse(statusCode: 200, body: $html);
    }
}
