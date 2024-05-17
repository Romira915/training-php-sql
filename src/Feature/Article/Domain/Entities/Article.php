<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

use DateTimeImmutable;
use Romira\Zenita\Feature\Article\Domain\ValueObjects\ArticleTitle;

class Article
{
    private int $id;
    private int $user_id;
    private ArticleTitle $title;
    private string $body;
    private string $thumbnail_url;
    /** @var array<string> */
    private array $image_url_list;
    private DateTimeImmutable $created_at;
    private DateTimeImmutable $updated_at;

    public function __construct(
        int $id,
        int $user_id,
        ArticleTitle $title,
        string $body,
        string $thumbnail_url,
        array $image_url_list,
        DateTimeImmutable $created_at,
        DateTimeImmutable $updated_at
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->body = $body;
        $this->thumbnail_url = $thumbnail_url;
        $this->image_url_list = $image_url_list;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
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
        return $this->title->getTitle();
    }

    public function getBody(): string
    {
        return $this->body;
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
