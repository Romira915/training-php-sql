<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use Exception;
use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\UpdateDraftArticleDTO;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleTag;
use Romira\Zenita\Feature\Article\Domain\Entities\DraftArticle;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleImageList;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleTagList;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\DraftArticleRepository;

class UpdateDraftArticleUseCase
{
    public static function run(PDO $pdo, DraftArticleRepository $articleRepository, ImageStorageInterface $imageStorage, UpdateDraftArticleDTO $articleDTO): void
    {
        $pdo->beginTransaction();
        try {
            $article = $articleRepository::findByUserIdAndArticleId($pdo, $articleDTO->user_id, $articleDTO->article_id);

            if ($articleDTO->thumbnail_image_path) {
                $thumbnail_path = $imageStorage->moveUploadedFile($articleDTO->thumbnail_image_path);
                $thumbnail = new ArticleImage(
                    user_id: $article->getUserId(),
                    image_path: $thumbnail_path,
                    id: $article->getThumbnail()?->getId(),
                    article_id: $article->getId()
                );
            } else {
                $thumbnail = $article->getThumbnail();
            }

            if (count($articleDTO->image_path_list) > 0) {
                $images = [];
                foreach ($articleDTO->image_path_list as $image_path) {
                    $image_path = $imageStorage->moveUploadedFile($image_path);
                    $images[] = new ArticleImage(
                        user_id: $article->getUserId(),
                        image_path: $image_path,
                        article_id: $article->getId()
                    );
                }
                $images = new ArticleImageList($images);
            } else {
                $images = $article->getImages();
            }

            if (count($articleDTO->tags) > 0) {
                $tags = [];
                foreach ($articleDTO->tags as $tag) {
                    $tags[] = new ArticleTag(
                        user_id: $article->getUserId(),
                        tag_name: $tag,
                        article_id: $article->getId()
                    );
                }
                $tags = new ArticleTagList($tags);
            } else {
                $tags = $article->getTags();
            }

            $article = new DraftArticle(
                user_id: $article->getUserId(),
                title: $articleDTO->title,
                body: $articleDTO->body,
                thumbnail: $thumbnail,
                images: $images,
                tags: $tags,
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
