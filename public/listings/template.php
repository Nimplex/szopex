<?php
@session_start();
if (!isset($_SESSION['user_id']) || !isset($_SERVER['HTTP_HX_REQUEST'])) {
    require __DIR__ . '/../403.php';
    die;
}

$page = $_GET['page'] ?: 1;
// TODO: we gotta get the db connection here and either
// decode JSON or get an array from the function directly
// and then insert it into this html

echo "$page";
?>


