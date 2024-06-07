<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use Exception;
use PDO;
use Romira\Zenita\Feature\Article\Application\DTO\ChangeArticleStatusDraftToPublishedDTO;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleTag;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleImageList;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleTagList;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\DraftArticleRepository;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\PublishedArticleRepository;

class ChangeArticleStatusDraftToPublishedUseCase
{
    public static function run(PDO $pdo, DraftArticleRepository $draftArticleRepository, PublishedArticleRepository $publishedArticleRepository, ImageStorageInterface $imageStorage, ChangeArticleStatusDraftToPublishedDTO $toPublishedDTO): void
    {
        $pdo->beginTransaction();
        try {
            $draftArticle = $draftArticleRepository->findByUserIdAndArticleId($pdo, $toPublishedDTO->user_id, $toPublishedDTO->article_id);
            $draftArticleRepository->delete($pdo, $toPublishedDTO->user_id, $toPublishedDTO->article_id);

            if ($toPublishedDTO->thumbnail_image_path !== null) {
                $thumbnail_path = $imageStorage->moveUploadedFile($toPublishedDTO->thumbnail_image_path);
                $thumbnail = new ArticleImage(
                    user_id: $toPublishedDTO->user_id,
                    image_path: $thumbnail_path,
                    id: $draftArticle->getThumbnail()?->getId(),
                    article_id: $draftArticle->getId()
                );
            } else {
                $thumbnail = $draftArticle->getThumbnail();
            }

            if (count($toPublishedDTO->image_path_list) > 0) {
                $images = [];
                foreach ($toPublishedDTO->image_path_list as $image_path) {
                    $image_path = $imageStorage->moveUploadedFile($image_path);
                    $images[] = new ArticleImage(
                        user_id: $toPublishedDTO->user_id,
                        image_path: $image_path,
                        article_id: $draftArticle->getId()
                    );
                }
                $images = new ArticleImageList($images);
            } else {
                $images = $draftArticle->getImages();
            }

            if (count($toPublishedDTO->tags) > 0) {
                $tags = [];
                foreach ($toPublishedDTO->tags as $tag) {
                    $tags[] = new ArticleTag(
                        user_id: $draftArticle->getUserId(),
                        tag_name: $tag,
                        article_id: $draftArticle->getId()
                    );
                }
                $tags = new ArticleTagList($tags);
            } else {
                $tags = $draftArticle->getTags();
            }

            $publishedArticle = new PublishedArticle(
                user_id: $draftArticle->getUserId(),
                title: $toPublishedDTO->title,
                body: $toPublishedDTO->body,
                thumbnail: $thumbnail,
                images: $images,
                tags: $tags,
                id: $draftArticle->getId()
            );

            $publishedArticleRepository->save($pdo, $publishedArticle);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
