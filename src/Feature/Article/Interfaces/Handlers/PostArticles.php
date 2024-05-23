<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Exception;
use Monolog\Level;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Feature\Article\Application\UseCases\CreatePublishArticleUseCase;
use Romira\Zenita\Feature\Article\Infrastructure\FileStorage\ImageStorage;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedPublishedArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Article\Interfaces\Validator\BodyValidator;
use Romira\Zenita\Feature\Article\Interfaces\Validator\TitleValidator;
use Romira\Zenita\Feature\Article\Interfaces\Validator\UploadImageValidator;
use Romira\Zenita\Utils\Logger\LoggerFactory;

class PostArticles implements HandlerInterface
{
    /**
     * @throws Exception
     */
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        $logger = LoggerFactory::createLogger('PostArticles', Level::Debug);

        if (!isset($request->files['thumbnail'])) {
            throw new InvalidUploadImageException('Invalid image');
        }
        $image = $request->files['thumbnail'];

        try {
            UploadImageValidator::validate($image);
        } catch (InvalidUploadImageException $e) {
            $logger->info('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage(), ['exception' => $e]);
            return new HttpResponse(statusCode: 400, body: "Invalid Upload Image");
        }

        $title = $request->post['title'] ?? '';
        $body = $request->post['body'] ?? '';
        if (!TitleValidator::validate($title) || !BodyValidator::validate($body)) {
            return new HttpResponse(statusCode: 400, body: 'Invalid title or body');
        }

        $pdo = PostgresqlConnection::connect();
        $articleRepository = new PublishedPublishedArticleRepository();
        $imageStorage = new ImageStorage();

        CreatePublishArticleUseCase::run($pdo, $articleRepository, $imageStorage, $request->server['DOCUMENT_ROOT'], $title, $body, $image['tmp_name']);

        return new HttpResponse(statusCode: 302, headers: ['location' => '/']);
    }
}
