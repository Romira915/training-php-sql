<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Application\DTO;

readonly class CurrentUserDTO
{
    public function __construct(
        public int    $id,
        public string $display_name,
        public string $icon_path,
    )
    {
    }
}
