<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Validator;

class DraftBodyValidator
{
    public static function validate(string $title): bool
    {
        return mb_strlen($title) <= 100;
    }
}
