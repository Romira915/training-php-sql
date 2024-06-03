<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\ValueObject;

use Romira\Zenita\Feature\Article\Domain\Entities\ArticleTag;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidTagsLimitException;

class ArticleTagList
{
    const int MAX_TAGS = 10;

    /** @var ArticleTag[] */
    private array $tags = [];

    /**
     * @param ArticleTag[] $tags
     * @throws InvalidTagsLimitException
     */
    public function __construct(
        array $tags = []
    )
    {
        if (count($tags) > self::MAX_TAGS) {
            throw new InvalidTagsLimitException('The maximum number of tags is ' . self::MAX_TAGS);
        }

        // duplicate check
        $tagNames = array_map(fn($tag) => $tag->getTag(), $tags);
        if (count($tagNames) !== count(array_unique($tagNames))) {
            throw new InvalidTagsLimitException('Duplicate tags are not allowed');
        }

        $this->tags = $tags;
    }

    /**
     * @return ArticleTag[]
     */
    public function all(): array
    {
        return $this->tags;
    }
}
