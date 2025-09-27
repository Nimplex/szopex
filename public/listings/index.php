<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    require __DIR__ . '/../401.php';
    die;
}

if (isset($_SERVER['HTTP_HX_REQUEST'])) {
    require __DIR__ . '/template.php';
    die;
}

require __DIR__ . '/doc.php';
