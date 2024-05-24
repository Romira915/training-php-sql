<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Validator;

class BodyValidator
{
    public static function validate(string $body): bool
    {
        return mb_strlen($body) > 0 && mb_strlen($body) <= 8000;
    }
}
