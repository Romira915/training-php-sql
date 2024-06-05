<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;

class UserLoginPageViewHelper extends ViewHelper
{
    public function __construct(private readonly ?string $errorMessage = null)
    {
        parent::__construct();
    }

    public function render(): string
    {
        $this->setTitle('Login');
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
                <main class='w-full flex flex-col items-center'>
                    <h2 class='text-2xl py-4'>Login</h2>
                    <nav class='self-end'>
                        <a href='/auth/register' class='text-blue-500 underline'>Register</a>
                    </nav>
                    {$this->createLoginFormElement($errorMessage)}
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

    private function createLoginFormElement(?string $errorMessage): string
    {
        return '
            <form action="/auth/login" method="post" class="w-full flex flex-col items-center gap-4">
                <div class="w-full">
                    <label for="user_name" class="w-full text-left">User name</label>
                    <input type="text" name="user_name" id="user_name" class="w-full p-2 border border-gray-300 rounded-md" maxlength="100" required>
                </div>
                <div class="w-full">
                    <label for="password" class="w-full text-left">Password</label>
                    <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md" maxlength="30" required>
                </div>
                ' . ($errorMessage === null ? '' : '<p class="text-red-500">' . htmlspecialchars($errorMessage) . '</p>') . '
                <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded-md">Login</button>
            </form>
        ';
    }
}
