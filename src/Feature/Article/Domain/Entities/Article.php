<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

use DateTimeImmutable;

class Article
{
    public function __construct(
        private int               $id,
        private int               $user_id,
        private string            $title,
        private string            $body,
        private string            $thumbnail_url,
        /** @var array<string> */
        private array             $image_url_list,
        private DateTimeImmutable $created_at,
        private DateTimeImmutable $updated_at
    )
    {
    }

    public function getId(): int
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

    public function setThumbnailUrl(string $thumbnail_url): void
    {
        $this->thumbnail_url = $thumbnail_url;
    }

    public function getThumbnailUrl(): string
    {
        return $this->thumbnail_url;
    }

    public function getImageUrlList(): array
    {
        return $this->image_url_list;
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
