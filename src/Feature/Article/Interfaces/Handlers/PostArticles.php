<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Exception;
use Monolog\Level;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Feature\Article\Application\UseCases\CreateArticleAction;
use Romira\Zenita\Feature\Article\Infrastructure\FileStorage\ImageStorage;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedPublishedArticleRepository;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;
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

        $pdo = PostgresqlConnection::connect();
        $articleRepository = new PublishedPublishedArticleRepository();
        $imageStorage = new ImageStorage();

        CreateArticleAction::run($request, $pdo, $articleRepository, $imageStorage);

        return new HttpResponse(statusCode: 302, headers: ['location' => '/']);
    }
}
