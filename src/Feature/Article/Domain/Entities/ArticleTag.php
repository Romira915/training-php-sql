<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

use Romira\Zenita\Feature\Article\Interfaces\Validator\TagValidator;

class ArticleTag
{
    public function __construct(
        private int    $user_id,
        private string $tag_name,
        private ?int   $id = null,
        private ?int   $article_id = null,
    )
    {
        $this->setTag($tag_name);
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

    private function setTag(string $tag_name): void
    {
        if (!TagValidator::validate($tag_name)) {
            throw new \InvalidArgumentException('Invalid tag name');
        }

        $this->tag_name = $tag_name;
    }


    public function getTag(): string
    {
        return $this->tag_name;
    }
}
