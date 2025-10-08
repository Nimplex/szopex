<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/401.php';
    die;
}
