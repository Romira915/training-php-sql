<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use InvalidArgumentException;
use Monolog\Level;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Http\SeeOtherResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Feature\Article\Application\DTO\DeletePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Application\UseCases\DeletePublishedArticleUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\FileStorage\ImageLocalStorage;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Http\PostUsersIdArticlesIdDeleteRequest;
use Romira\Zenita\Utils\Logger\LoggerFactory;

class PostUsersIdArticleIdDelete implements HandlerInterface
{
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        $deleteRequest = PostUsersIdArticlesIdDeleteRequest::new($matches['user_id'], $matches['article_id']);

        if ($deleteRequest instanceof InvalidArgumentException) {
            return new HttpResponse(statusCode: 404, body: 'Not Found');
        }

        $logger = LoggerFactory::createLogger('PostUsersIdArticleIdDelete', Level::Info);
        $pdo = PostgresqlConnection::connect();
        $articleRepository = new PublishedArticleRepository();
        $imageStorage = new ImageLocalStorage($request->server['DOCUMENT_ROOT']);
        $deleteArticleDTO = new DeletePublishedArticleDTO(
            article_id: $deleteRequest->article_id,
            user_id: $deleteRequest->user_id
        );

        DeletePublishedArticleUseCase::run($logger, $pdo, $articleRepository, $imageStorage, $deleteArticleDTO);

        return new SeeOtherResponse('/');
    }
}
