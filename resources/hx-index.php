<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

global $_target;
if (!isset($_target)) {
    throw new \Exception('The variable `$target` is missing!');
}

$router = new App\Router();

$router->GET("/", function () use ($_target) {
    if (!isset($_SERVER['HTTP_HX_REQUEST'])) return;
    require __DIR__ . '/hx-templates' . $_target;
});

$router->DEFAULT(function () {
    require $_SERVER['DOCUMENT_ROOT'] . '/404.php';
});

$router->handle();

