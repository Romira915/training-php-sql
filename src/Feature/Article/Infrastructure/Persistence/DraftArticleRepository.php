<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\Persistence;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Entities\ArticleTag;
use Romira\Zenita\Feature\Article\Domain\Entities\DraftArticle;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidImageLimitException;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidTagsLimitException;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleImageList;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleTagList;

class DraftArticleRepository
{
    public static function save(PDO $pdo, DraftArticle $article): DraftArticle
    {
        if ($article->getId() !== null) {
            $article_id = $article->getId();
        } else {
            $article_id = self::createArticle($pdo, $article->getUserId());
        }
        self::createDraftArticleIfNotExists($pdo, $article_id, $article->getUserId());

        if (!is_null($article->getThumbnail())) {
            $thumbnail = new ArticleImage(
                user_id: $article->getThumbnail()->getUserId(),
                image_path: $article->getThumbnail()->getImagePath(),
                id: $article->getThumbnail()->getId(),
                article_id: $article_id
            );
            if ($thumbnail->getId() === null) {
                $thumbnail = self::createArticleImage($pdo, $thumbnail);
            } else {
                self::updateArticleImage($pdo, $thumbnail);
            }
        }

        $images = array_map(
            fn(ArticleImage $image) => new ArticleImage(
                user_id: $image->getUserId(),
                image_path: $image->getImagePath(),
                id: $image->getId(),
                article_id: $article_id
            ),
            $article->getImages()->all()
        );
        if (count($images) > 0) {
            // thumbnail以外の画像を削除
            self::deleteArticleImagesByArticleIdAndUserIdExcludeThumbnail($pdo, $article_id, $article->getUserId(), $thumbnail ? $thumbnail->getId() : 0);
            $images = self::createArticleImages($pdo, $images);
        }

        $tags = array_map(
            fn(ArticleTag $tag) => new ArticleTag(
                user_id: $tag->getUserId(),
                tag_name: $tag->getTag(),
                id: $tag->getId(),
                article_id: $article_id
            ),
            $article->getTags()->all()
        );
        if (count($tags) > 0) {
            self::deleteTagsByArticleIdAndUserId($pdo, $article_id, $article->getUserId());
            $tags = self::createTags($pdo, $tags);
        }

        $article = new DraftArticle(
            user_id: $article->getUserId(),
            title: $article->getTitle(),
            body: $article->getBody(),
            thumbnail: $thumbnail,
            images: new ArticleImageList($images),
            tags: new ArticleTagList($tags),
            id: $article_id
        );

        self::upsertArticleDetail($pdo, $article);

        return $article;
    }

    /**
     * @throws InvalidImageLimitException
     * @throws InvalidTagsLimitException
     */
    public static function findByUserIdAndArticleId(PDO $pdo, int $user_id, int $article_id): DraftArticle|null
    {
        $statement = $pdo->prepare("
            SELECT ap.article_id,
                   ap.user_id,
                   ad.title,
                   ad.body,
                   tnai.id         AS thumbnail_id,
                   tnai.image_path AS thumbnail_path,
                   COALESCE(
                                   jsonb_agg(DISTINCT jsonb_build_object('id', ai.id, 'path', ai.image_path))
                                   FILTER (WHERE ai.id IS NOT NULL),
                                   '[]'::jsonb
                   )               AS image_list,
                   COALESCE(
                                   jsonb_agg(DISTINCT jsonb_build_object('id', at.id, 'tag_name', at.tag_name))
                                   FILTER (WHERE at.id IS NOT NULL),
                                   '[]'::jsonb
                   )               AS tag_list
            FROM article_detail AS ad
                     INNER JOIN articles AS a ON ad.article_id = a.id AND ad.user_id = a.user_id
                     INNER JOIN article_draft AS ap ON ad.article_id = ap.article_id AND ad.user_id = ap.user_id
                     INNER JOIN article_images AS tnai ON ad.thumbnail_id = tnai.id
                     LEFT JOIN article_images AS ai ON ad.article_id = ai.article_id AND ad.user_id = ai.user_id
                     LEFT JOIN article_tags AS at ON ad.article_id = at.article_id AND ad.user_id = at.user_id
            WHERE ap.article_id = :article_id
              AND ap.user_id = :user_id
            GROUP BY ap.article_id,
                     ap.user_id,
                     ad.title,
                     ad.body,
                     tnai.id,
                     tnai.image_path
        ");
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $image_list = json_decode($row['image_list'], true);
        $image_list = array_map(
            fn(array $image) => new ArticleImage(
                user_id: $user_id,
                image_path: $image['path'],
                id: $image['id'],
                article_id: $article_id
            ),
            $image_list
        );
        // Exclude thumbnail from image_list
        $image_list = array_filter($image_list, fn(ArticleImage $image) => $image->getId() !== (int)$row['thumbnail_id']);

        $tags = json_decode($row['tag_list'], true);
        $tags = array_map(
            fn(array $tag) => new ArticleTag(
                user_id: $user_id,
                tag_name: $tag['tag_name'],
                id: $tag['id'],
                article_id: $article_id
            ),
            $tags
        );

        return new DraftArticle(
            user_id: (int)$row['user_id'],
            title: $row['title'],
            body: $row['body'],
            thumbnail: new ArticleImage(
                user_id: $user_id,
                image_path: $row['thumbnail_path'],
                id: (int)$row['thumbnail_id'],
                article_id: $article_id
            ),
            images: new ArticleImageList($image_list),
            tags: new ArticleTagList($tags),
            id: (int)$row['article_id'],
        );
    }

    public static function delete(PDO $pdo, int $user_id, int $article_id): void
    {
        $statement = $pdo->prepare('
            DELETE FROM article_draft WHERE article_id = :article_id AND user_id = :user_id
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id
        ]);
    }

    /**
     * @param PDO $pdo
     * @param int $user_id
     * @return int article_id
     */
    private static function createArticle(PDO $pdo, int $user_id): int
    {
        $statement = $pdo->prepare('
        INSERT INTO articles (id, user_id) VALUES ((
            SELECT COALESCE(MAX(id), 0) + 1 FROM articles
        ), :user_id) RETURNING id
        ');
        $statement->execute(['user_id' => $user_id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return (int)$row['id'];
    }

    private static function createDraftArticleIfNotExists(PDO $pdo, int $article_id, int $user_id): void
    {
        $statement = $pdo->prepare('
        INSERT INTO article_draft (article_id, user_id)
        SELECT :article_id, :user_id
        WHERE NOT EXISTS (SELECT 1 FROM article_draft WHERE article_id = :article_id AND user_id = :user_id);
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id
        ]);
    }

    private static function upsertArticleDetail(PDO $pdo, DraftArticle $article): void
    {
        $statement = $pdo->prepare('
        INSERT INTO article_detail (article_id, user_id, title, body, thumbnail_id)
        VALUES (:article_id, :user_id, :title, :body, :thumbnail_id)
        ON CONFLICT (article_id, user_id)
            DO UPDATE SET title        = :title,
                          body         = :body,
                          thumbnail_id = :thumbnail_id
        ');
        $statement->execute([
            'article_id' => $article->getId(),
            'user_id' => $article->getUserId(),
            'title' => $article->getTitle(),
            'body' => $article->getBody(),
            'thumbnail_id' => $article->getThumbnail()->getId()
        ]);
    }

    private static function createArticleImage(PDO $pdo, ArticleImage $image): ArticleImage
    {
        $statement = $pdo->prepare('
            INSERT INTO article_images (article_id, user_id, image_path)
            VALUES (:article_id, :user_id, :image_path) RETURNING id
        ');
        $statement->execute([
            'article_id' => $image->getArticleId(),
            'user_id' => $image->getUserId(),
            'image_path' => $image->getImagePath()
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return new ArticleImage(
            user_id: $image->getUserId(),
            image_path: $image->getImagePath(),
            id: (int)$row['id'],
            article_id: $image->getArticleId()
        );
    }

    /**
     * @param PDO $pdo
     * @param array<ArticleImage> $images
     * @return array
     */
    private static function createArticleImages(PDO $pdo, array $images): array
    {
        $values = '';
        foreach ($images as $key => $image) {
            $values .= '(:article_id_' . $key . ', :user_id_' . $key . ', :image_path_' . $key . '),';
        }
        $values = rtrim($values, ',');
        $bindParams = [];
        foreach ($images as $key => $image) {
            $bindParams['article_id_' . $key] = $image->getArticleId();
            $bindParams['user_id_' . $key] = $image->getUserId();
            $bindParams['image_path_' . $key] = $image->getImagePath();
        }

        $statement = $pdo->prepare('
            INSERT INTO article_images (article_id, user_id, image_path)
            VALUES ' . $values . '
            RETURNING id
        ');
        $statement->execute($bindParams);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($images as $key => $image) {
            $result[] = new ArticleImage(
                user_id: $image->getUserId(),
                image_path: $image->getImagePath(),
                id: (int)$rows[$key]['id'],
                article_id: $image->getArticleId()
            );
        }

        return $result;
    }

    private static function updateArticleImage(PDO $pdo, ArticleImage $image): void
    {
        $statement = $pdo->prepare('
            UPDATE article_images SET image_path = :image_path
            WHERE id = :id
        ');
        $statement->execute([
            'image_path' => $image->getImagePath(),
            'id' => $image->getId(),
        ]);
    }

    private static function deleteArticleImagesByArticleIdAndUserIdExcludeThumbnail(PDO $pdo, int $article_id, int $user_id, int $thumbnail_id): void
    {
        $statement = $pdo->prepare('
            DELETE FROM article_images WHERE article_id = :article_id AND user_id = :user_id AND id != :thumbnail_id
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id,
            'thumbnail_id' => $thumbnail_id
        ]);
    }

    /**
     * @param PDO $pdo
     * @param ArticleTag[] $tags
     * @return ArticleTag[]
     */
    private static function createTags(PDO $pdo, array $tags): array
    {
        $values = '';
        for ($i = 0; $i < count($tags); $i++) {
            $values .= '(:article_id_' . $i . ', :user_id_' . $i . ', :tag_' . $i . '),';
        }
        $values = rtrim($values, ',');
        $bindParams = [];
        for ($i = 0; $i < count($tags); $i++) {
            $bindParams['article_id_' . $i] = $tags[$i]->getArticleId();
            $bindParams['user_id_' . $i] = $tags[$i]->getUserId();
            $bindParams['tag_' . $i] = $tags[$i]->getTag();
        }

        $statement = $pdo->prepare('
            INSERT INTO article_tags (article_id, user_id, tag_name)
            VALUES ' . $values . '
            RETURNING id
        ');
        $statement->execute($bindParams);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($tags as $key => $tag) {
            $result[] = new ArticleTag(
                user_id: $tag->getUserId(),
                tag_name: $tag->getTag(),
                id: (int)$rows[$key]['id'],
                article_id: $tag->getArticleId()
            );
        }

        return $result;
    }

    private static function deleteTagsByArticleIdAndUserId(PDO $pdo, int $article_id, int $user_id): void
    {
        $statement = $pdo->prepare('
            DELETE FROM article_tags WHERE article_id = :article_id AND user_id = :user_id
        ');
        $statement->execute([
            'article_id' => $article_id,
            'user_id' => $user_id
        ]);
    }
}
