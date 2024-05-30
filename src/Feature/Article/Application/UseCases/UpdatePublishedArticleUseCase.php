<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use Exception;
use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\UpdatePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;

class UpdatePublishedArticleUseCase
{
    public static function run(PDO $pdo, PublishedArticleRepository $articleRepository, UpdatePublishedArticleDTO $updatePublishedArticleDTO): void
    {
        $pdo->beginTransaction();
        try {
            $article = $articleRepository::findByUserIdAndArticleId($pdo, $updatePublishedArticleDTO->user_id, $updatePublishedArticleDTO->article_id);

            $article = new PublishedArticle(
                user_id: $article->getUserId(),
                title: $updatePublishedArticleDTO->title,
                body: $updatePublishedArticleDTO->body,
                thumbnail: $article->getThumbnail(),
                images: $article->getImages(),
                id: $article->getId()
            );

            $articleRepository::save($pdo, $article);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
