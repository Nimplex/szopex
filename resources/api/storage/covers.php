<?php

global $_ROUTE;

$id = filter_var($_ROUTE['id'] ?? null, FILTER_VALIDATE_INT);
// Just in case
if (!isset($id)) {
    header('Location: /', true, 303);
    die;
}

$baseDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../storage/covers') . DIRECTORY_SEPARATOR;
$filepath = realpath($baseDir . $id);

if (!$filepath || !str_starts_with($filepath, $baseDir) || !is_file($filepath)) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
    die;
}

$mime = mime_content_type($filepath);

if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
    die;
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: public, max-age=31536000, immutable');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

readfile($filepath);
die;
