<?php

global $_ROUTE;

$filename = $_ROUTE['id'] ?? '';

$filepath = "";

if ($filename === "default") {
    $filepath = realpath($_SERVER['DOCUMENT_ROOT'] . '/_assets/no-pfp.svg');
} else {
    $baseDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../storage/profile-pictures') . DIRECTORY_SEPARATOR;
    $filepath = realpath($baseDir . $filename);

    if (!$filepath || !str_starts_with($filepath, $baseDir) || !is_file($filepath)) {
        require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
        die;
    }
}

if (!is_file($filepath)) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
    die;
}

$mime = mime_content_type($filepath);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filepath));

readfile($filepath);
die;
