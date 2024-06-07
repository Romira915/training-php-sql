<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\DTO;

class ChangeArticleStatusDraftToPublishedDTO
{
    public function __construct(
        public int     $user_id,
        public int     $article_id,
        public string  $title,
        public string  $body,
        public ?string $thumbnail_image_path,
        /** @var string[] */
        public array   $image_path_list,
        /** @var string[] */
        public array   $tags
    )
    {
    }
}
