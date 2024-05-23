<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

class ArticleImage
{
    public function __construct(
        private int    $user_id,
        private string $image_path,
        private ?int   $id = null,
        private ?int   $article_id = null,
    )
    {
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getArticleId(): int|null
    {
        return $this->article_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getImagePath(): string
    {
        return $this->image_path;
    }
}
