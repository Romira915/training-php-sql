<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\ValueObjects;

use InvalidArgumentException;

class ArticleTitle
{
    public const int MAX_TITLE_LENGTH = 191;
    private string $title;

    public function __construct(string $title)
    {
        if (strlen($title) > self::MAX_TITLE_LENGTH) {
            throw new InvalidArgumentException('Title is too long');
        }
        if (strlen($title) === 0) {
            throw new InvalidArgumentException('Title is empty');
        }

        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
