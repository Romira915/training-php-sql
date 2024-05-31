<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Validator;

class TagValidator
{
    public static function validate(string $tag): bool
    {
        return mb_strlen($tag) > 0 && mb_strlen($tag) <= 20;
    }
}
