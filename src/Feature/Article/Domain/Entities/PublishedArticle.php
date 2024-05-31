<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Entities;

use Romira\Zenita\Feature\Article\Domain\Exception\InvalidImageLimitException;

class PublishedArticle
{
    const int MAX_IMAGES = 20;

    /**
     * @throws InvalidImageLimitException
     */
    public function __construct(
        private int          $user_id,
        private string       $title,
        private string       $body,
        private ArticleImage $thumbnail,
        /** @var array<ArticleImage> */
        private array        $images,
        /** @var array<ArticleTag> */
        private array        $tags,
        private ?int         $id = null
    )
    {
        $this->setImages($images);
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getThumbnail(): ArticleImage
    {
        return $this->thumbnail;
    }

    /**
     * @param array<ArticleImage> $images
     * @return void
     * @throws InvalidImageLimitException
     */
    public function setImages(array $images): void
    {
        if (count($images) > self::MAX_IMAGES) {
            throw new InvalidImageLimitException('画像のアップロード数は最大' . self::MAX_IMAGES . '枚までです。');
        }

        $this->images = $images;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
