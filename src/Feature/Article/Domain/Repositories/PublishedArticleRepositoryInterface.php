<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Repositories;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;

interface PublishedArticleRepositoryInterface
{
    public static function save(PDO $pdo, PublishedArticle $article): PublishedArticle;

    public static function findByUserIdAndArticleId(PDO $pdo, int $user_id, int $article_id): PublishedArticle|null;
}
