<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use PDO;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Config\Config;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidImageLimitException;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Domain\Repositories\PublishedArticleRepositoryInterface;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;

class CreateArticleAction
{

    /**
     * @throws InvalidUploadImageException|InvalidImageLimitException
     */
    public static function run(HttpRequest $request, PDO $pdo, PublishedArticleRepositoryInterface $articleRepository, ImageStorageInterface $imageStorage): void
    {
        $title = $request->post['title'];
        $body = $request->post['body'];

        $image_path = Config::IMAGE_PATH_PREFIX . $imageStorage::moveUploadedFileToPublic($request->files['thumbnail']['tmp_name']);
        $thumbnail = new ArticleImage(user_id: 1, image_path: $image_path);
        $article = new PublishedArticle(
            user_id: 1,
            title: $title,
            body: $body,
            thumbnail: $thumbnail,
            images: []
        );

        $articleRepository::save($pdo, $article);
    }
}
