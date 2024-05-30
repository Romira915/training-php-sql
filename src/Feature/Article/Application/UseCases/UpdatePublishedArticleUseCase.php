<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use Monolog\Logger;
use PDO;
use PDOException;
use Romira\Zenita\Feature\Article\Application\DTO\UpdatePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;

class UpdatePublishedArticleUseCase
{
    public static function run(Logger $logger, PDO $pdo, PublishedArticleRepository $articleRepository, UpdatePublishedArticleDTO $updatePublishedArticleDTO): void
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
        } catch (PDOException $e) {
            $pdo->rollBack();
            $logger->error('UpdatePublishedArticleUseCase failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
