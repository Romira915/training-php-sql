<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;
use Romira\Zenita\Feature\Article\Application\DTO\PublishedArticleDTO;

class IndexViewHelper extends ViewHelper
{
    /** @var array<PublishedArticleDTO> */
    private array $articles;

    public function __construct(array $articles)
    {
        $this->articles = $articles;
        parent::__construct();
    }

    public function render(): string
    {
        $this->setBody($this->createBody());

        return parent::render();
    }

    private function createBody(): string
    {
        return "
            <div class='root'>
                <header class='header'>
                    {$this->createServiceNameElement()}
                </header>
                <main class='main'>
                    {$this->createArticleFormElement()}
                    <section>
                        {$this->createArticlesElement()}
                    </section>
                </main>
            </div>  
            ";
    }

    private function createServiceNameElement(): string
    {
        return '<h1 class="service-name">Zenita</h1>';
    }

    private function createArticleFormElement(): string
    {
        return '
            <form class="article-form" action="/articles" method="post" enctype="multipart/form-data">
                <div class="article-form__label-wrapper">
                    <label for="title" >タイトル</label>
                    <input type="text" id="title" name="title" maxlength="191" required>
                </div>
                <div class="article-form__label-wrapper">
                    <label for="body">本文</label>
                    <textarea id="body" name="body" required></textarea>
                </div>
                <div class="article-form__label-wrapper">
                    <label for="thumbnail">サムネイル</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg, image/png, image/gif" class="article-form__thumbnail-input" required>
                </div>
                <button type="submit" class="article-form__post-button">投稿</button>
            </form>
        ';
    }

    private function createArticlesElement(): string
    {
        $articlesHtml = '';

        foreach ($this->articles as $article) {
            $articlesHtml .= $this->createArticleElement($article);
        }

        return '<ul class="articles">' . $articlesHtml . '</ul>';
    }

    private function createArticleElement(PublishedArticleDTO $article): string
    {
        return '
            <li>
                <a href="/articles/' . htmlspecialchars((string)$article->id) . '">
                    <article class="article">
                        <h2 class="article__title">' . htmlspecialchars($article->title) . '</h2>
                        <p class="article__body">' . htmlspecialchars($article->body) . '</p>
                        <img class="article__thumbnail" src="' . htmlspecialchars($article->thumbnail_url) . '" alt="' . htmlspecialchars($article->thumbnail_url) . '" width="300" height="255">
                    </article>
                </a>            
            </li>
        ';
    }
}
