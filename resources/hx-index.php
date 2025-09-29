<?php

@session_start();
if (!isset($_SESSION['user_id'])) {
    require __DIR__ . '/../public/401.php';
    die;
}

if (isset($_SERVER['HTTP_HX_REQUEST'])) {
    require 'template.php';
    die;
}

require 'doc.php';
