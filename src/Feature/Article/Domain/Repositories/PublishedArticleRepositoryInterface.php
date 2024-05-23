<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Repositories;

use PDO;
use Romira\Zenita\Feature\Article\Domain\Entities\PublishedArticle;

interface PublishedArticleRepositoryInterface
{
    public static function save(PDO $pdo, PublishedArticle $article): PublishedArticle;
}
