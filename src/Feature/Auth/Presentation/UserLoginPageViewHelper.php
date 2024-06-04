<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Presentation;

use Romira\Zenita\Common\Presentation\ViewHelper;

class UserLoginPageViewHelper extends ViewHelper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function render(): string
    {
        $this->setTitle('Login');
        $this->setBody($this->createBody());

        return parent::render();
    }

    public function createBody(): string
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
                    {$this->createLoginFormElement()}
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

    private function createLoginFormElement(): string
    {
        return '
            <form action="/auth/login" method="post" class="w-full flex flex-col items-center">
                <label for="user_name" class="w-full text-left">User name</label>
                <input type="text" name="user_name" id="user_name" class="w-full p-2 border border-gray-300 rounded-md mb-4" maxlength="100" required>
                <label for="password" class="w-full text-left">Password</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md mb-4" maxlength="30" required>
                <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded-md">Login</button>
            </form>
        ';
    }
}
