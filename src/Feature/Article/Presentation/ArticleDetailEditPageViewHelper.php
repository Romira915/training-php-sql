<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;
use Romira\Zenita\Feature\Article\Application\DTO\ArticleDetailEditPageDTO;

class ArticleDetailEditPageViewHelper extends ViewHelper
{
    private ArticleDetailEditPageDTO $articleDetail;

    public function __construct(ArticleDetailEditPageDTO $articleDetail)
    {
        $this->articleDetail = $articleDetail;
        parent::__construct();
    }

    public function render(): string
    {
        $this->setTitle('Edit Article');
        $this->setBody($this->createBody());

        return parent::render();
    }

    public function createBody(): string
    {
        return "
            <div class='flex flex-col items-center w-[650px] m-auto'>
                <header class=''>
                    {$this->createServiceNameElement()}
                </header>
                <nav class='self-end'>
                    <a href='/users/{$this->articleDetail->user_id}/articles/{$this->articleDetail->article_id}' class='text-blue-500 underline'>Back</a>
                </nav>
                <main class='w-full flex flex-col items-center'>
                    {$this->createArticleEditFormElement()}
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

    private function createArticleEditFormElement(): string
    {
        $bodyLineCount = substr_count($this->articleDetail->body, "\n");
        $bodyHeight = $bodyLineCount * 20 + 40;

        return '
            <form class="flex flex-col gap-4 items-center w-fit" action=/users/' . $this->articleDetail->user_id . '/articles/' . $this->articleDetail->article_id . '/edit method="post" enctype="multipart/form-data">
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="title">タイトル</label>
                    <input class="w-[400px] p-1" type="text" id="title" name="title" maxlength="100" value=' . $this->articleDetail->title . ' required>
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="body">本文</label>
                    <textarea class="w-[400px] h-[' . $bodyHeight . 'px]' . ' p-2 leading-[20px]" id="body" name="body" maxlength="8000" required>' . $this->articleDetail->body . '</textarea>
                </div>
                <button type="submit" class="bg-gray-300 px-4 py-1 rounded hover:bg-gray-400">Update</button>
            </form>
        ';
    }
}
