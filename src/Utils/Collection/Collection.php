<?php

declare(strict_types=1);

namespace Romira\Zenita\Utils\Collection;

class Collection
{
    /**
     * Cast all string numbers to integers
     *
     * @param array $array
     * @return array
     */
    public static function castNumbers(array $array): array
    {
        return array_map(function ($value) {
            return is_numeric($value) ? (int)$value : $value;
        }, $array);
    }
}
