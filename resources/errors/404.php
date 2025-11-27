<?php

http_response_code(404);

$title = '404';

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_dist/css/error.css">
    HTML;
};

$render_content = function (): string {
    return <<<HTML
    <h1>Not found</h1>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
