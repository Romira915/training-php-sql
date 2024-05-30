<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use Exception;
use Monolog\Logger;
use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\DeletePublishedArticleDTO;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;

class DeletePublishedArticleUseCase
{
    public static function run(Logger $logger, PDO $pdo, PublishedArticleRepository $articleRepository, ImageStorageInterface $imageStorage, DeletePublishedArticleDTO $deletePublishedArticleDTO): void
    {
        $pdo->beginTransaction();
        try {
            $article = $articleRepository::findByUserIdAndArticleId($pdo, $deletePublishedArticleDTO->user_id, $deletePublishedArticleDTO->article_id);

            $articleRepository::delete($pdo, $deletePublishedArticleDTO->user_id, $deletePublishedArticleDTO->article_id);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        foreach ($article->getImages() as $image) {
            if (!$imageStorage->deleteImageFile($image->getImagePath())) {
                $logger->warning('Failed to delete image file', ['image_path' => $image->getImagePath()]);
            }
        }
    }
}
