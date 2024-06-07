<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use InvalidArgumentException;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Http\SeeOtherResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Common\Interfaces\Session\Session;
use Romira\Zenita\Feature\Article\Application\DTO\ChangeArticleStatusDraftToPublishedDTO;
use Romira\Zenita\Feature\Article\Application\UseCases\ChangeArticleStatusDraftToPublishedUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\FileStorage\ImageLocalStorage;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\DraftArticleRepository;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidArticleParameterException;
use Romira\Zenita\Feature\Article\Interfaces\Http\PostUsersIdDraftArticlesIdPublishRequest;
use Romira\Zenita\Utils\File;

class PostUsersIdDraftArticlesIdPublish implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $currentUserSession = new CurrentUserSession($session);
        if (!$currentUserSession->isLoggedIn()) {
            return new HttpResponse(statusCode: 303, headers: ['Location' => '/auth/login']);
        }

        $draftToPublishRequest = PostUsersIdDraftArticlesIdPublishRequest::new(
            $matches['user_id'],
            $matches['article_id'],
            $request->post['title'] ?? '',
            $request->post['body'] ?? '',
            $request->files['thumbnail'] ?? null,
            File::reshapeFilesArray($request->files['images'] ?? []),
            $request->post['tags'] ?? ''
        );

        if ($draftToPublishRequest instanceof InvalidArgumentException) {
            return new HttpResponse(statusCode: 404, body: 'Not Found');
        }

        if ($draftToPublishRequest instanceof InvalidArticleParameterException) {
            return new HttpResponse(statusCode: 400, body: 'Invalid title or body');
        }

        if ($draftToPublishRequest instanceof InvalidUploadImageException) {
            return new HttpResponse(statusCode: 400, body: 'Invalid thumbnail or images');
        }

        if ($draftToPublishRequest->user_id !== $currentUserSession->getCurrentUser()) {
            return new HttpResponse(statusCode: 403, body: 'Forbidden');
        }

        $pdo = PostgresqlConnection::connect();
        $draftArticleRepository = new DraftArticleRepository();
        $publishedArticleRepository = new PublishedArticleRepository();
        $imageStorage = new ImageLocalStorage($request->server['DOCUMENT_ROOT']);
        $dto = new ChangeArticleStatusDraftToPublishedDTO(
            user_id: $draftToPublishRequest->user_id,
            article_id: $draftToPublishRequest->article_id,
            title: $draftToPublishRequest->title,
            body: $draftToPublishRequest->body,
            thumbnail_image_path: $draftToPublishRequest->thumbnail_file ? $draftToPublishRequest->thumbnail_file['tmp_name'] : null,
            image_path_list: $draftToPublishRequest->image_files ? array_map(fn($file) => $file['tmp_name'], $draftToPublishRequest->image_files) : [],
            tags: $draftToPublishRequest->tags
        );

        ChangeArticleStatusDraftToPublishedUseCase::run($pdo, $draftArticleRepository, $publishedArticleRepository, $imageStorage, $dto);

        return new SeeOtherResponse('/users/' . $draftToPublishRequest->user_id . '/articles/' . $draftToPublishRequest->article_id);
    }
}
