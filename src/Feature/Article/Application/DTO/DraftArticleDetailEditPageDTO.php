<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\DTO;

readonly class DraftArticleDetailEditPageDTO
{
    public function __construct(
        public int    $article_id,
        public int    $user_id,
        public string $title,
        public string $body,
        public string $thumbnail_image_url,
        /** @var array<string> */
        public array  $image_url_list,
        /** @var array<string> */
        public array  $tags,
    )
    {
    }
}
