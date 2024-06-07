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
use Romira\Zenita\Feature\Article\Application\DTO\UpdateDraftArticleDTO;
use Romira\Zenita\Feature\Article\Application\UseCases\UpdateDraftArticleUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\FileStorage\ImageLocalStorage;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\DraftArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidArticleParameterException;
use Romira\Zenita\Feature\Article\Interfaces\Http\PostUsersIdDraftArticlesIdEditRequest;
use Romira\Zenita\Utils\File;

class PostUsersIdDraftArticlesIdEdit implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $currentUserSession = new CurrentUserSession($session);
        if (!$currentUserSession->isLoggedIn()) {
            return new SeeOtherResponse('/auth/login');
        }

        $editArticleRequest = PostUsersIdDraftArticlesIdEditRequest::new(
            $matches['user_id'],
            $matches['article_id'],
            $request->post['title'] ?? '',
            $request->post['body'] ?? '',
            $request->files['thumbnail'] ?? null,
            File::reshapeFilesArray($request->files['images'] ?? []),
            $request->post['tags'] ?? ''
        );

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
        $articleRepository = new DraftArticleRepository();
        $imageStorage = new ImageLocalStorage($request->server['DOCUMENT_ROOT']);
        $updatePublishedArticleDTO = new UpdateDraftArticleDTO(
            article_id: $editArticleRequest->article_id,
            user_id: $editArticleRequest->user_id,
            title: $editArticleRequest->title,
            body: $editArticleRequest->body,
            thumbnail_image_path: $editArticleRequest->thumbnail_file ? $editArticleRequest->thumbnail_file['tmp_name'] : null,
            image_path_list: array_map(fn($image) => $image['tmp_name'], $editArticleRequest->image_files),
            tags: $editArticleRequest->tags
        );

        UpdateDraftArticleUseCase::run($pdo, $articleRepository, $imageStorage, $updatePublishedArticleDTO);

        return new SeeOtherResponse('/users/' . $editArticleRequest->user_id . '/draft-articles/' . $editArticleRequest->article_id . '/edit');
    }
}
