<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;
use Romira\Zenita\Feature\Article\Application\DTO\PublishedArticleDetailPageDTO;

class PublishedArticleDetailPageViewHelper extends ViewHelper
{
    private PublishedArticleDetailPageDTO $articleDetail;

    public function __construct(PublishedArticleDetailPageDTO $articleDetail)
    {
        $this->articleDetail = $articleDetail;
        parent::__construct();
    }

    public function render(): string
    {
        $this->setTitle($this->articleDetail->title);
        $this->setBody($this->createBody());

        return parent::render();
    }

    private function createBody(): string
    {
        return "
            {$this->createCheckDeleteScript()}
            <div class='flex flex-col items-center w-[650px] m-auto'>
                <header class=''>
                    {$this->createServiceNameElement()}
                </header>
                <nav class='flex gap-2 self-end'>
                    <a href='/users/{$this->articleDetail->user_id}/articles/{$this->articleDetail->article_id}/edit' class='text-blue-500 underline'>Edit</a>
                    <form method='post' action='/users/{$this->articleDetail->user_id}/articles/{$this->articleDetail->article_id}/delete' onSubmit='return CheckDelete()'>
                        <button type='submit' class='text-red-500 underline'>Delete</button>
                    </form>
                </nav>
                <main class=''>
                    {$this->createArticleElement()}
                </main>
            </div>  
            ";
    }

    private function createServiceNameElement(): string
    {
        return '
            <h1 class="text-4xl py-4">
                <a href="/">
                    Zenita
                </a>
            </h1>
        ';
    }

    private function createArticleElement(): string
    {
        return "
            <article class='flex flex-col px-8 py-4 gap-8 bg-gray-200'>
                <h2 class='text-3xl font-bold'>{$this->articleDetail->title}</h2>
                <img class='object-contain' src='{$this->articleDetail->thumbnail_image_url}' alt='{$this->articleDetail->title}' width='600' height='450' />
                <p class=''>{$this->articleDetail->body}</p>
                {$this->createImageListElement()}
            </article>
            ";
    }

    private function createImageListElement(): string
    {
        $imageListElement = '';
        foreach ($this->articleDetail->image_url_list as $image_url) {
            $imageListElement .= $this->createImageElement($image_url);
        }

        return "
            <ul class=''>
                {$imageListElement}
            </ul>
            ";
    }

    private function createImageElement(string $image_url): string
    {
        return "
            <li class=''>
                <img src='{$image_url}' alt='' class='object-contain' width='300' height='225' />
            </li>
            ";
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
