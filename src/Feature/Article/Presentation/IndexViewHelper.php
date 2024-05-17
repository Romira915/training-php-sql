<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;
use Romira\Zenita\Feature\Article\Domain\Entities\Article;

class IndexViewHelper extends ViewHelper
{
    /** @var array<Article> */
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
            <form class="article-form" action="/articles" method="post" ">
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
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg, image/png, image/gif" class="article-form__thumbnail-input">
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

    private function createArticleElement(Article $article): string
    {
        return '
            <li>
                <article class="article">
                    <h2 class="article__title">' . htmlspecialchars($article->getTitle()) . '</h2>
                    <p class="article__body">' . htmlspecialchars($article->getBody()) . '</p>
                    <img class="article__thumbnail" src="' . htmlspecialchars($article->getThumbnailUrl()) . '" alt="' . htmlspecialchars($article->getTitle()) . '">
                </article>
            </li>
        ';
    }
}
