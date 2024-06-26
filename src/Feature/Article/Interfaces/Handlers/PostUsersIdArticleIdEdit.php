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
use Romira\Zenita\Feature\Article\Application\DTO\UpdatePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Application\UseCases\UpdatePublishedArticleUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidArticleParameterException;
use Romira\Zenita\Feature\Article\Interfaces\Http\PostUsersIdArticleIdEditRequest;

class PostUsersIdArticleIdEdit implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $currentUserSession = new CurrentUserSession($session);
        if (!$currentUserSession->isLoggedIn()) {
            return new SeeOtherResponse('/auth/login');
        }

        $editArticleRequest = PostUsersIdArticleIdEditRequest::new($matches['user_id'], $matches['article_id'], $request->post['title'] ?? '', $request->post['body'] ?? '');

        if ($editArticleRequest instanceof InvalidArgumentException) {
            return new HttpResponse(statusCode: 404, body: 'Not Found');
        }

        if ($editArticleRequest instanceof InvalidArticleParameterException) {
            return new HttpResponse(statusCode: 400, body: 'Invalid title or body');
        }

        if ($editArticleRequest->user_id !== $currentUserSession->getCurrentUser()) {
            return new HttpResponse(statusCode: 403, body: 'Forbidden');
        }

        $pdo = PostgresqlConnection::connect();
        $articleRepository = new PublishedArticleRepository();
        $updatePublishedArticleDTO = new UpdatePublishedArticleDTO(
            article_id: $editArticleRequest->article_id,
            user_id: $editArticleRequest->user_id,
            title: $editArticleRequest->title,
            body: $editArticleRequest->body
        );

        UpdatePublishedArticleUseCase::run($pdo, $articleRepository, $updatePublishedArticleDTO);

        return new SeeOtherResponse('/users/' . $editArticleRequest->user_id . '/articles/' . $editArticleRequest->article_id);
    }
}
