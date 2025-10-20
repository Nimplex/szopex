<?php

$filename = $_GET['file'] ?? '';
$baseDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/../storage/covers') . DIRECTORY_SEPARATOR;

$filepath = realpath($baseDir . $filename);

if (!$filepath || !str_starts_with($filepath, $baseDir) || !is_file($filepath)) {
    http_response_code(404);
    exit('File not found');
}

$mime = mime_content_type($filepath);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($filepath));

readfile($filepath);
exit;
