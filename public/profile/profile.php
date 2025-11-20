<?php

session_start();

/** @var PDO $db */
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap.php';
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';
require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

use App\Controller\UserController;

$user = new UserController($db);

$id = $_GET['id'];

$res = $user->user->get_profile($id);

$title = "Użytkownik {$res['display_name']}";

$render_content = function () use ($res) {
    [
        'display_name' => $display_name,
        'created_at' => $created_at,
        'listing_count' => $listing_count,
    ] = $res;

    return <<<HTML
    <div>
        <h1>{$display_name}</h1>
        <p>{$created_at}</p>
        <p>{$listing_count}</p>
    </div>
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
