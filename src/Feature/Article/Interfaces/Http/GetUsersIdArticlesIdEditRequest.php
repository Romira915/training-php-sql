<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Http;

use InvalidArgumentException;

class GetUsersIdArticlesIdEditRequest
{
    public int $user_id;
    public int $article_id;

    private function __construct(int $user_id, int $article_id)
    {
        $this->user_id = $user_id;
        $this->article_id = $article_id;
    }

    /**
     * @param int|string $user_id
     * @param int|string $article_id
     * @return GetUsersIdArticlesIdEditRequest|InvalidArgumentException
     */
    public static function new(int|string $user_id, int|string $article_id): GetUsersIdArticlesIdEditRequest|InvalidArgumentException
    {
        if (!is_numeric($user_id) || !is_numeric($article_id)) {
            return new InvalidArgumentException('Invalid user_id or article_id');
        }

        return new self((int)$user_id, (int)$article_id);
    }
}
