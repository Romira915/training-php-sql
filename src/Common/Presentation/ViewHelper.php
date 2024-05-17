<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Presentation;

class ViewHelper
{
    private string $html;

    public function __construct()
    {
        $html = '
            <!DOCTYPE html>
            <html lang="ja">
              <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>zenita</title>
                <link rel="stylesheet" href="./styles/main.css" />
              </head>
              <body>
              %s
              </body>
            </html>
        ';

        $this->html = $html;
    }

    public function setBody(string $body): void
    {
        $this->html = sprintf($this->html, $body);
    }

    public function render(): string
    {
        return $this->html;
    }
}
