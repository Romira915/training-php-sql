<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Http;

use InvalidArgumentException;
use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidArticleParameterException;
use Romira\Zenita\Feature\Article\Interfaces\Validator\BodyValidator;
use Romira\Zenita\Feature\Article\Interfaces\Validator\TitleValidator;

class PostUsersIdArticleIdEditRequest
{
    public int $user_id;
    public int $article_id;
    public string $title;
    public string $body;

    private function __construct(int $user_id, int $article_id, string $title, string $body)
    {
        $this->user_id = $user_id;
        $this->article_id = $article_id;
        $this->title = $title;
        $this->body = $body;
    }
    
    public static function new(int|string $user_id, int|string $article_id, string $title, string $body): PostUsersIdArticleIdEditRequest|InvalidArgumentException|InvalidArticleParameterException
    {
        if (!is_numeric($user_id) || !is_numeric($article_id)) {
            return new InvalidArgumentException('Invalid user_id or article_id');
        }

        if (!TitleValidator::validate($title) || !BodyValidator::validate($body)) {
            return new InvalidArticleParameterException('Invalid title or body');
        }

        return new self((int)$user_id, (int)$article_id, $title, $body);
    }
}
