<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;
use Romira\Zenita\Feature\Article\Application\DTO\TopPagePublishedArticleSummaryDTO;

class IndexViewHelper extends ViewHelper
{
    /** @var array<TopPagePublishedArticleSummaryDTO> */
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
            {$this->createCheckDeleteScript()}
            <div class='root'>
                <header class='header'>
                    {$this->createServiceNameElement()}
                </header>
                <main class='flex flex-col items-center'>
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
        return '<h1 class="text-4xl py-4">Zenita</h1>';
    }

    private function createArticleFormElement(): string
    {
        return '
            <form class="flex flex-col gap-4 items-center w-fit" action="/articles" method="post" enctype="multipart/form-data">
                <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="title" >タイトル</label>
                    <input class="w-[400px] p-1" type="text" id="title" name="title" maxlength="191" required>
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="body">本文</label>
                    <textarea class="w-[400px] p-2 leading-[20px] h-[100px]" id="body" name="body" required></textarea>
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="thumbnail">サムネイル</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg, image/png, image/gif" class="" required>
                </div>
                <button type="submit" class="bg-gray-300 px-4 py-1 rounded hover:bg-gray-400">投稿</button>
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

    private function createArticleElement(TopPagePublishedArticleSummaryDTO $article): string
    {
        return '
            <li class="flex flex-col">
                <form class="self-end w-fit" method="post" action="/users/' . $article->user_id . '/articles/' . $article->id . '/delete" onSubmit="return CheckDelete()">
                    <button type="submit" class="text-red-500 underline">Delete</button>
                </form>
                <a href="/users/' . htmlspecialchars((string)$article->user_id) . '/articles/' . htmlspecialchars((string)$article->id) . '">
                    <article class="article">
                        <h2 class="article__title">' . htmlspecialchars($article->title) . '</h2>
                        <p class="article__body">' . htmlspecialchars($article->body) . '</p>
                        <img class="article__thumbnail" src="' . htmlspecialchars($article->thumbnail_url) . '" alt="' . htmlspecialchars($article->thumbnail_url) . '" width="300" height="255">
                    </article>
                </a>            
            </li>
        ';
    }

    private function createCheckDeleteScript()
    {
        return "
            <script>
                function CheckDelete() {
                    return confirm('Are you sure you want to delete?');
                }
            </script>
        ";
    }
}
