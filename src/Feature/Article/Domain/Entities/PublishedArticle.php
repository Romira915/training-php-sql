<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

use DateTimeImmutable;

class PublishedArticle
{
    public function __construct(
        private int          $user_id,
        private string       $title,
        private string       $body,
        private ArticleImage $thumbnail,
        /** @var array<ArticleImage> */
        private array        $images,
        private ?int         $id = null,
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

    public function getImages(): array
    {
        return $this->images;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }
}
