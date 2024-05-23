<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Domain\Exception;


use Exception;
use Throwable;

class InvalidImageLimitException extends Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
