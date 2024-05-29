<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Http\SeeOtherResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Feature\Article\Application\DTO\UpdatePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Application\UseCases\UpdatePublishedArticleUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Validator\BodyValidator;
use Romira\Zenita\Feature\Article\Interfaces\Validator\TitleValidator;

class PostUsersIdArticleIdEdit implements HandlerInterface
{
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        if (!is_numeric($matches['user_id']) || !is_numeric($matches['article_id'])) {
            return new HttpResponse(statusCode: 404, body: 'Not Found');
        }

        $user_id = (int)$matches['user_id'];
        $article_id = (int)$matches['article_id'];
        $title = $request->post['title'] ?? '';
        $body = $request->post['body'] ?? '';

        if (!TitleValidator::validate($title) || !BodyValidator::validate($body)) {
            return new HttpResponse(statusCode: 400, body: 'Invalid title or body');
        }

        $pdo = PostgresqlConnection::connect();
        $articleRepository = new PublishedArticleRepository();
        $updatePublishedArticleDTO = new UpdatePublishedArticleDTO(
            article_id: $article_id,
            user_id: $user_id,
            title: $title,
            body: $body
        );

        UpdatePublishedArticleUseCase::run($pdo, $articleRepository, $updatePublishedArticleDTO);

        return new SeeOtherResponse('/users/' . $user_id . '/articles/' . $article_id);
    }
}
