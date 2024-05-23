<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Exception;


use Exception;

class InvalidImageLimitException extends Exception
{
    public function __construct(string $message = '画像のアップロード数が不正です。')
    {
        parent::__construct($message);
    }
}
