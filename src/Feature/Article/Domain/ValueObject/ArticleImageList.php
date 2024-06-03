<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\ValueObject;

use Romira\Zenita\Feature\Article\Domain\Entities\ArticleImage;
use Romira\Zenita\Feature\Article\Domain\Exception\InvalidImageLimitException;

class ArticleImageList
{
    const int MAX_IMAGES = 20;

    /** @var ArticleImage[] */
    private array $images = [];

    /**
     * @param ArticleImage[] $images
     * @throws InvalidImageLimitException
     */
    public function __construct(
        array $images = []
    )
    {
        if (count($images) > self::MAX_IMAGES) {
            throw new InvalidImageLimitException('The maximum number of images is ' . self::MAX_IMAGES);
        }

        $this->images = $images;
    }

    /**
     * @return ArticleImage[]
     */
    public function all(): array
    {
        return $this->images;
    }
}
