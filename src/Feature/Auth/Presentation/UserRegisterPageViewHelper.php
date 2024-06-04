<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;

class UserRegisterPageViewHelper extends ViewHelper
{
    public function __construct(private readonly ?string $errorMessage = null)
    {
        parent::__construct();
    }

    public function render(): string
    {
        $this->setTitle('ユーザー登録');
        $this->setBody($this->createBody($this->errorMessage));

        return parent::render();
    }

    public function createBody(?string $errorMessage): string
    {
        return "
            <div class='flex flex-col items-center w-[450px] m-auto'>
                <header class=''>
                    {$this->createServiceNameElement()}
                </header>
                <h2 class='text-2xl py-4'>Register</h2>
                <nav class='self-end'>
                    <a href='/auth/login' class='text-blue-500 underline'>Login</a>
                </nav>
                <main class='w-full flex flex-col items-center'>
                    {$this->createRegisterFormElement($errorMessage)}
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

    private function createRegisterFormElement(?string $errorMessage): string
    {
        return '
            <form action="/auth/register" method="post" enctype="multipart/form-data" class="w-full flex flex-col items-center gap-4">
                <div class="w-full">
                    <label for="user_name" class="w-full text-left">User name</label>
                    <input type="text" name="user_name" id="user_name" class="w-full p-2 border border-gray-300 rounded-md" maxlength="30" required>
                </div>  
                <div class="w-full">
                    <label for="password" class="w-full text-left">Password</label>
                    <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md" minlength="8" maxlength="30" required>
                    <p class="text-sm">
                        ※パスワードは8文字以上30文字以内で、大文字、小文字、数字、記号をそれぞれ1文字以上含めてください。記号は@$!%*?&_-が使用可能です。
                    </p>
                </div>
                <div class="w-full">
                    <label for="user_icon" class="w-full text-left">User icon</label>
                    <input type="file" name="user_icon" id="user_icon" class="w-full p-2 border border-gray-300 rounded-md" accept="image/jpeg, image/png, image/gif">
                </div>
                ' . ($errorMessage === null ? '' : '<p class="text-red-500">' . htmlspecialchars($errorMessage) . '</p>') . '
                <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded-md">Register</button>
            </form>
        ';
    }
}
