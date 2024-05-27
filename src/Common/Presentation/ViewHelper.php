<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Presentation;

class ViewHelper
{
    private string $html;
    private string $title = 'Zenita';
    private string $body = '';

    public function __construct()
    {
        $html = '
            <!DOCTYPE html>
            <html lang="ja">
              <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>%s</title>
                <link rel="stylesheet" href="/styles/main.css" />
                <script src="/styles/tailwind.js"></script>
              </head>
              <body>
              %s
              </body>
            </html>
        ';

        $this->html = $html;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function render(): string
    {
        $this->html = sprintf($this->html, $this->title, $this->body);

        return $this->html;
    }
}
