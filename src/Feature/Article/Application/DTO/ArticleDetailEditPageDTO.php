<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\DTO;

class ArticleDetailEditPageDTO
{
    public function __construct(
        public int    $article_id,
        public int    $user_id,
        public string $title,
        public string $body,
    )
    {
    }
}
