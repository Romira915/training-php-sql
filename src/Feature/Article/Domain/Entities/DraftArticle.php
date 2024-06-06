<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleImageList;
use Romira\Zenita\Feature\Article\Domain\ValueObject\ArticleTagList;

class DraftArticle
{
    /**
     * @param int $user_id
     * @param string $title
     * @param string $body
     * @param ArticleImage $thumbnail
     * @param ArticleImageList $images
     * @param ArticleTagList $tags
     * @param int|null $id
     */
    public function __construct(
        private int              $user_id,
        private string           $title,
        private string           $body,
        private ArticleImage     $thumbnail,
        private ArticleImageList $images,
        private ArticleTagList   $tags,
        private ?int             $id = null
    )
    {
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getThumbnail(): ArticleImage
    {
        return $this->thumbnail;
    }

    public function getImages(): ArticleImageList
    {
        return $this->images;
    }

    public function getTags(): ArticleTagList
    {
        return $this->tags;
    }
}
