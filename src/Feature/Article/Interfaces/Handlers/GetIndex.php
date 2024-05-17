<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Exception;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Config\Config;
use Romira\Zenita\Feature\Article\Application\UseCases\GetArticleListAction;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\ArticleRepository;
use Romira\Zenita\Feature\Article\Presentation\IndexViewHelper;

class GetIndex implements HandlerInterface
{
    public const int ARTICLE_LIMIT = 20;

    /**
     * @throws Exception
     */
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        $limit = $request->get['limit'] ?? self::ARTICLE_LIMIT;
        $limit = (int)$limit;

        $pdo = PostgresqlConnection::connect();
        $articleRepository = new ArticleRepository();

        $articles = GetArticleListAction::run($pdo, $articleRepository, $limit);
        foreach ($articles as $article) {
            $article->setThumbnailUrl(Config::getImageBaseUrl() . $article->getThumbnailUrl());
        }

        $helper = new IndexViewHelper($articles);
        $html = $helper->render();

        return new HttpResponse(statusCode: 200, body: $html);
    }
}
