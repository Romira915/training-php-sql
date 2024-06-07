<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;
use Romira\Zenita\Feature\Article\Application\DTO\DraftArticleDetailEditPageDTO;

class DraftArticleDetailEditPageViewHelper extends ViewHelper
{
    public function __construct(private DraftArticleDetailEditPageDTO $articleDetail)
    {
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
            <div class='flex flex-col items-center w-[400px] m-auto'>
                <header class=''>
                    {$this->createServiceNameElement()}
                </header>
                <main class='w-full flex flex-col items-center mb-4'>
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
            <form id="editArticleForm" class="flex flex-col gap-4 items-center w-fit" action=/users/' . $this->articleDetail->user_id . '/draft-articles/' . $this->articleDetail->article_id . '/edit method="post" enctype="multipart/form-data">
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="title">タイトル</label>
                    <input class="w-[400px] p-1" type="text" id="title" name="title" maxlength="100" value="' . htmlspecialchars($this->articleDetail->title) . '" required>
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="body">本文</label>
                    <textarea class="w-[400px] h-[' . $bodyHeight . 'px]' . ' p-2 leading-[20px]" id="body" name="body" maxlength="8000" required>' . htmlspecialchars($this->articleDetail->body) . '</textarea>
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="thumbnail">サムネイル</label>
                    <img class="w-[200px]" src=' . htmlspecialchars($this->articleDetail->thumbnail_image_url ?? "") . ' alt="thumbnail">
                    <input class="w-[400px]" type="file" id="thumbnail" name="thumbnail" accept="image/jpeg, image/png, image/gif">
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="images">画像</label>
                    ' . $this->createArticleImageElement() . '
                    <input type="file" id="images" name="images[]" accept="image/jpeg, image/png, image/gif" multiple>
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="tags">タグ</label>
                    <input class="w-[400px] p-1" type="text" id="tags" name="tags" value="' . htmlspecialchars(implode(',', $this->articleDetail->tags)) . '" required>
                    <p class="text-xs">※タグはカンマ区切りで入力してください</p>
                </div>
                <div class="flex gap-8">
                    <button id="draft-submit-button" type="submit" class="bg-gray-300 px-4 py-1 rounded hover:bg-gray-400" data-action="/users/' . $this->articleDetail->user_id . '/draft-articles/' . $this->articleDetail->article_id . '/edit">下書き保存</button>
                    <button id="post-submit-button" type="submit" class="bg-cyan-400 hover:bg-cyan-500 px-4 py-1 rounded" data-action="#todo">公開</button>
                </div>
            </form>
        ' . $this->createSubmitArticleScript() . $this->createToggleArticleFormRequiredScript();
    }

    private function createArticleImageElement(): string
    {
        $imageElement = '';
        foreach ($this->articleDetail->image_url_list as $imageUrl) {
            $imageElement .= '
                <img class="w-[100px] object-contain" src=' . $imageUrl . ' alt="article_image">
            ';
        }
        return '<div class="flex flex-row flex-wrap gap-4">' . $imageElement . '</div>';
    }

    private function createSubmitArticleScript(): string
    {
        return "
            <script>
                document.addEventListener('click', (e) => {
                    document.getElementById('editArticleForm').action = e.target.getAttribute('data-action');
                });
            </script>
        ";
    }

    private function createToggleArticleFormRequiredScript(): string
    {
        return "
            <script>
                document.addEventListener('click', (e) => {
                    const form = document.forms.namedItem('editArticleForm');
                    if (e.target.id === 'draft-submit-button') {
                        for (const input of form.querySelectorAll('input, textarea')) {
                            input.required = false;
                        }
                    } else {
                        for (const input of form.querySelectorAll('input, textarea')) {
                            input.required = true;
                        }
                        form.querySelector('input[name=images[]]').required = false;
                    }
                });
            </script>
        ";
    }
}
