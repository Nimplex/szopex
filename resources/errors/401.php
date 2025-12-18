<?php

if (isset($_SERVER['REQUEST_URI']) && !isset($_SESSION['user_role'])) {
    header('Location: /login?redir=' . urlencode($_SERVER['REQUEST_URI']), true, 303);
    die;
}

http_response_code(401);

$TITLE = '401';
$HEAD = '<link rel="stylesheet" href="/_dist/css/error.css">';

$CONTENT = <<<HTML
<h1>Unauthorized</h1>
HTML;

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
