<?php

// for now disable
// session_set_cookie_params([
//     'samesite' => 'Strict',
//     'secure' => true,
//     'httponly' => true,
// ]);

@session_start();

/** @var PDO $db */
require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

use App\Controller\UserController;

$user_controller = new UserController($db);
$router = new App\Router();

$router->GET('/', function () {
    header('Location: /listings', true, 303);
    die;
});


//==== LOGIN ================================================================//

$router->GET(
    '/login',
    fn () => require __DIR__ . '/../resources/pub/login.php'
);
$router->GET(
    '/register',
    fn () => require __DIR__ . '/../resources/pub/register.php'
);

//==== LISTINGS =============================================================//

$router->GET(
    '/listings',
    fn () => require __DIR__ . '/../resources/pub/listings/all.php',
    true,
);

$router->GET(
    '/listings/new',
    fn () => require __DIR__ . '/../resources/pub/listings/new.php',
    true,
);

$router->GET(
    '/listings/:id',
    fn () => require __DIR__ . '/../resources/pub/listings/view.php',
    true,
);

//==== PROFILE ==============================================================//

$router->GET(
    '/profile/favourites',
    fn () => require __DIR__ . '/../resources/pub/profile/favourites.php',
    true,
);

$router->GET(
    '/profile/listings',
    fn () => require __DIR__ . '/../resources/pub/profile/listings.php',
    true,
);


$router->GET(
    '/profile/:id',
    fn () => require __DIR__ . '/../resources/pub/profile/profile.php',
    true,
);

//==== ADMIN ================================================================//

$router->GET(
    '/admin',
    fn () => require __DIR__ . '/../resources/pub/admin/index.php',
    true,
);

$router->GET(
    '/admin/reports',
    fn () => require __DIR__ . '/../resources/pub/admin/reports.php',
    true,
);

$router->GET(
    '/admin/reports/:id',
    fn () => require __DIR__ . '/../resources/pub/admin/report.php',
    true,
);

//==== SETTINGS =============================================================//

$router->GET(
    '/settings/profile',
    fn () => require __DIR__ . '/../resources/pub/settings/profile.php',
    true,
);

//==== MESSAGES =============================================================//

$router->GET(
    '/messages',
    fn () => require __DIR__ . '/../resources/pub/messages/messages.php',
    true,
);

$router->GET(
    '/messages/:id',
    fn () => require __DIR__ . '/../resources/pub/messages/messages.php',
    true,
);

$router->POST(
    '/api/new-chat',
    fn () => require __DIR__ . '/../resources/api/new-chat.php',
    true
);

$router->POST(
    '/api/new-message',
    fn () => require __DIR__ . '/../resources/api/new-message.php',
    true
);

//==== ERRORS ===============================================================//

$router->GET(
    '/401',
    fn () => require __DIR__ . '/../resources/errors/401.php'
);

$router->GET(
    '/403',
    fn () => require __DIR__ . '/../resources/errors/403.php'
);

$router->GET(
    '/404',
    fn () => require __DIR__ . '/../resources/errors/404.php'
);

//==== API ==================================================================//

$router->GET('/api/logout', function () {
    session_destroy();
    header('Location: /?logout=1', true, 303);
    die;
});

$router->GET(
    '/api/activate/:id/:token',
    fn () => require __DIR__ . '/../resources/api/activate.php'
);

$router->GET(
    '/api/storage/covers/:id',
    fn () => require __DIR__ . '/../resources/api/storage/covers.php',
    true,
);

$router->GET(
    '/api/storage/profile-pictures/:id',
    fn () => require __DIR__ . '/../resources/api/storage/profile-picture.php',
    true,
);

$router->POST(
    '/api/listings/favourite',
    fn () => require __DIR__ . '/../resources/api/favourites.php',
    true,
);

$router->POST(
    '/api/login',
    fn () => require __DIR__ . '/../resources/api/login.php'
);

$router->POST(
    '/api/register',
    fn () => require __DIR__ . '/../resources/api/register.php'
);

$router->POST(
    '/api/new-listing',
    fn () => require __DIR__ . '/../resources/api/new-listing.php',
    true,
);

$router->POST(
    '/api/report',
    fn () => require __DIR__ . '/../resources/api/report.php'
);

$router->DEFAULT(fn () => require __DIR__ . '/../resources/errors/404.php');

$router->handle();
