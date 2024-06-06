<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Presentation;

use Romira\Zenita\Common\Application\DTO\CurrentUserDTO;
use Romira\Zenita\Common\Presentation\ViewHelper;
use Romira\Zenita\Feature\Article\Application\DTO\TopPagePublishedArticleSummaryDTO;

class IndexViewHelper extends ViewHelper
{
    /**
     * @param array<TopPagePublishedArticleSummaryDTO> $articles
     * @param CurrentUserDTO|null $currentUserDTO
     * @param string|null $errorMessage
     */
    public function __construct(private array $articles, private ?CurrentUserDTO $currentUserDTO = null, private ?string $errorMessage = null)
    {
        parent::__construct();
    }

    public function render(): string
    {
        $this->setBody($this->createBody());

        return parent::render();
    }

    private function createBody(): string
    {
        if ($this->errorMessage) {
            $errorElement = "
                <p class='text-red-500'>{$this->errorMessage}</p>
            ";
        } else {
            $errorElement = '';
        }

        return "
            {$this->createCheckDeleteScript()}
            <div class='root'>
                <header class='header'>
                    {$this->createServiceNameElement()}
                </header>
                <main class='flex flex-col items-center'>
                    {$errorElement}
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
        if ($this->currentUserDTO === null) {
            $userInfoElement = '
            <div class="">' .
                $this->createLoginButtonElement() .
                '</div>
            ';
        } else {
            $userInfoElement = '
            <div class="flex items-center gap-1">' .
                $this->createUserIconElement($this->currentUserDTO) .
                $this->createLogoutFormElement() .
                '</div>';
        }

        return '
            <nav class="flex items-center justify-between bg-cyan-200 w-dvw px-4 mb-4">
                <h1 class="text-4xl py-4">Zenita</h1>
                ' . $userInfoElement . '
            </nav>
        ';
    }

    private function createLoginButtonElement(): string
    {
        return '
            <a class="text-lg py-1 px-3 bg-cyan-400 hover:bg-cyan-500 rounded-lg" href="/auth/login">Login</a>
        ';
    }

    private function createLogoutFormElement(): string
    {
        return '
            <form class="" action="/auth/logout" method="post">
                <button type="submit" class="text-lg py-1 px-3 rounded-lg bg-gray-300 rounded hover:bg-gray-400">Logout</button>
            </form>
        ';
    }

    private function createUserIconElement(CurrentUserDTO $currentUser): string
    {
        return '
            <img id="logged-in-user-icon" class="" alt="user_icon" title="' . $currentUser->display_name . '" src="' . $currentUser->icon_path . '" alt="user_icon" width="50" height="50">
        ';
    }

    private function createArticleFormElement(): string
    {
        return '
            <form class="flex flex-col gap-4 items-center w-fit" action="/users/1/articles" method="post" enctype="multipart/form-data">
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
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="images">画像</label>
                    <input type="file" id="images" name="images[]" accept="image/jpeg, image/png, image/gif" multiple>
                </div>
                <div class="flex flex-col items-start gap-2 justify-between w-full">
                    <label for="tags">タグ</label>
                    <input class="w-[400px] p-1" type="text" id="tags" name="tags" required>
                    <p class="text-xs">※タグはカンマ区切りで入力してください</p>
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
            <li class="flex flex-col gap-1 items-center w-full">
                <form class="self-end w-fit" method="post" action="/users/' . $article->user_id . '/articles/' . $article->id . '/delete" onSubmit="return CheckDelete()">
                    <button type="submit" class="text-red-500 underline">Delete</button>
                </form>
                <a class="w-full" href="/users/' . htmlspecialchars((string)$article->user_id) . '/articles/' . htmlspecialchars((string)$article->id) . '">
                    <article class="bg-cyan-200 flex flex-col items-start px-4 py-2">
                        <h2 class="">' . htmlspecialchars($article->title) . '</h2>
                        <p class="">' . htmlspecialchars($article->body) . '</p>
                        <img class="self-center" src="' . htmlspecialchars($article->thumbnail_url) . '" alt="' . htmlspecialchars($article->thumbnail_url) . '" width="300" height="255">
                        ' . $this->createTagsElement($article->tags) . '
                        <div class="flex items-center gap-2">
                            <img class="user__icon
                            " src="' . htmlspecialchars($article->user_icon_path) . '" alt="' . htmlspecialchars($article->user_icon_path) . '" width="50" height="50">
                            <p class="text-sm">' . htmlspecialchars($article->user_display_name) . '</p>
                            <p class="text-sm">' . htmlspecialchars($article->created_at) . '</p>
                        </div>
                    </article>
                </a>            
            </li>
        ';
    }

    /**
     * @param string[] $tags
     * @return string
     */
    private function createTagsElement(array $tags): string
    {
        $tagsHtml = '';
        foreach ($tags as $tag) {
            $tagsHtml .= '<li class="text-sm">#' . htmlspecialchars($tag) . '</li>';
        }

        return '<ul class="flex gap-2">' . $tagsHtml . '</ul>';
    }

    private function createCheckDeleteScript(): string
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
