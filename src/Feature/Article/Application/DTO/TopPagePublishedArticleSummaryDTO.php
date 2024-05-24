<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Application\DTO;

use Romira\Zenita\Config\Config;

readonly class TopPagePublishedArticleSummaryDTO
{
    public string $thumbnail_url;

    public function __construct(
        public int    $id,
        public int    $user_id,
        public string $title,
        public string $body,
        string        $thumbnail_image_path,
    )
    {
        $this->thumbnail_url = Config::getImageBaseUrl() . $thumbnail_image_path;
    }
}
