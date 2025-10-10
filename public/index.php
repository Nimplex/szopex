<?php

session_start();

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Model\Auth;

$auth = new Auth($db);
$router = new App\Router();

$test_middleware = function () {
    $query_str = json_encode($_GET);
    $body_str = json_encode($_POST);
    echo <<<HTML
    <style>
    #test-middleware {
        display: flex;
        flex-direction: column;
        width: fit-content;
        background: #d1d1d1;
        border: 1px solid grey;
        margin: 8px;
    }

    #test-middleware h3 {
        text-align: center;
    }
    </style>
    <div id="test-middleware">
    <p>+--------------------------------------------------------------+</p>
    <h3>Test middleware got called</h3>
    <p>+--------------------------------------------------------------+</p>
    <p>Query: <code>{$query_str}</code></p>
    <p>Array: <code>{$body_str}</code></p>
    <p>+--------------------------------------------------------------+</p>
    </div>
    HTML;
};

$router->GET("/", function () {
    echo 'home page???<br>';
    echo 'is logged in: ' . (isset($_SESSION['user_id']) ? 'true<br><a href="/api/logout">Logout</a>' : 'false');
})->with($test_middleware);

$router->POST('/api/register', function () use ($auth) {
    $res = $auth->register_from_request($_POST);
    echo $res;
});

$router->GET('/api/logout', function () {
    session_destroy();
    header('Location: /?logout=1', true, 303);
    die;
});

$router->POST('/api/login', fn () => require __DIR__ . '/../resources/api/login.php');
$router->POST('/api/new-listing', fn () => require __DIR__ . '/../resources/api/new-listing.php');

$router->DEFAULT(function () {
    require __DIR__ . '/404.php';
    die;
});

$router->handle();
