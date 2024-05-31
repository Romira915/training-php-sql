<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

class ArticleTag
{
    public function __construct(
        private int    $user_id,
        private string $tag_name,
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

    public function getTag(): string
    {
        return $this->tag_name;
    }
}
