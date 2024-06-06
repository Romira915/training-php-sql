<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\UseCases;

use Exception;
use PDO;
use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Article\Application\DTO\CreateDraftArticleDTO;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleTag;
use Romira\Zenita\Feature\Article\Domain\Entities\DraftArticle;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidImageLimitException;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidTagsLimitException;
use Romira\Zenita\Feature\Article\Domain\Repositories\ImageStorageInterface;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleImageList;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleTagList;
use Romira\Zenita\Feature\Article\Infrastructure\Persistence\DraftArticleRepository;

class CreateDraftArticleUseCase
{

    /**
     * @param PDO $pdo
     * @param DraftArticleRepository $articleRepository
     * @param ImageStorageInterface $imageStorage
     * @param CreateDraftArticleDTO $draftArticleDTO
     * @return int article_id
     * @throws InvalidImageLimitException
     * @throws InvalidUploadImageException
     * @throws InvalidTagsLimitException
     */
    public static function run(PDO $pdo, DraftArticleRepository $articleRepository, ImageStorageInterface $imageStorage, CreateDraftArticleDTO $draftArticleDTO): int
    {
        $pdo->beginTransaction();
        try {
            $image_path = $imageStorage->moveUploadedFile($draftArticleDTO->thumbnail_image_path);
            $thumbnail = new ArticleImage(user_id: $draftArticleDTO->user_id, image_path: $image_path);

            $images = array_map(
                fn($image_path) => new ArticleImage(user_id: $draftArticleDTO->user_id, image_path: $imageStorage->moveUploadedFile($image_path)),
                $draftArticleDTO->image_path_list
            );

            $tags = array_map(
                fn($tag) => new ArticleTag(user_id: $draftArticleDTO->user_id, tag_name: $tag),
                $draftArticleDTO->tags
            );

            $article = new DraftArticle(
                user_id: $draftArticleDTO->user_id,
                title: $draftArticleDTO->title,
                body: $draftArticleDTO->body,
                thumbnail: $thumbnail,
                images: new ArticleImageList($images),
                tags: new ArticleTagList($tags)
            );

            $article = $articleRepository::save($pdo, $article);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        return $article->getId();
    }
}
