<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Exception;
use InvalidArgumentException;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Feature\Article\Application\DTO\CreatePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Application\UseCases\CreatePublishArticleUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\FileStorage\ImageLocalStorage;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidArticleParameterException;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Article\Interfaces\Http\PostUsersIdArticlesRequest;
use Romira\Zenita\Utils\File;

class PostUsersIdArticles implements HandlerInterface
{
    /**
     * @throws Exception
     */
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        $createArticleRequest = PostUsersIdArticlesRequest::new(
            $matches['user_id'],
            $request->post['title'] ?? '',
            $request->post['body'] ?? '',
            $request->files['thumbnail'] ?? [],
            File::reshapeFilesArray($request->files['images'] ?? []),
            $request->post['tags'] ?? ''
        );

        if ($createArticleRequest instanceof InvalidArgumentException) {
            return new HttpResponse(statusCode: 400, body: 'Invalid user_id');
        }
        if ($createArticleRequest instanceof InvalidArticleParameterException) {
            return new HttpResponse(statusCode: 400, body: 'Invalid title or body or tags');
        }
        if ($createArticleRequest instanceof InvalidUploadImageException) {
            return new HttpResponse(statusCode: 400, body: 'Invalid Upload image');
        }

        $pdo = PostgresqlConnection::connect();
        $articleRepository = new PublishedArticleRepository();
        $imageStorage = new ImageLocalStorage($request->server['DOCUMENT_ROOT']);
        $createPublishedArticleDTO = new CreatePublishedArticleDTO(
            $createArticleRequest->user_id,
            $createArticleRequest->title,
            $createArticleRequest->body,
            $createArticleRequest->thumbnail_file['tmp_name'],
            array_map(fn($image) => $image['tmp_name'], $createArticleRequest->image_files)
        );

        CreatePublishArticleUseCase::run($pdo, $articleRepository, $imageStorage, $createPublishedArticleDTO);

        return new HttpResponse(statusCode: 302, headers: ['location' => '/']);
    }
}
