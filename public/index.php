<?php

// Ensure session is up
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** @var PDO $db */
require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Controller\UserController;

$user = new UserController($db);
$router = new App\Router();

$router->GET('/', function () {
    header('Location: /listings', true, 303);
    die;
});

$check_auth = function () {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /401', true, 303);
        die;
    }
};

//=====LOGIN=================================================================//

$router->GET(
    '/login',
    fn () => require __DIR__ . '/../resources/login.php'
);
$router->GET(
    '/register',
    fn () => require __DIR__ . '/../resources/register.php'
);

//=====LISTINGS==============================================================//

$router->GET(
    '/listings',
    fn () => require __DIR__ . '/../resources/listings/all.php'
)->middleware($check_auth);

$router->GET(
    '/listings/new',
    fn () => require __DIR__ . '/../resources/listings/new.php'
)->middleware($check_auth);

$router->GET(
    '/listings/:id:int',
    fn () => require __DIR__ . '/../resources/listings/view.php'
)->middleware($check_auth);

//=====PROFILE===============================================================//

$router->GET(
    '/profile/favourites',
    fn () => require __DIR__ . '/../resources/profile/@favourites.php'
)->middleware($check_auth);

$router->GET(
    '/profile/listings',
    fn () => require __DIR__ . '/../resources/profile/@listings.php'
)->middleware($check_auth);

$router->GET(
    '/profile/:id:int',
    fn () => require __DIR__ . '/../resources/profile/profile.php'
)->middleware($check_auth);

//=====STORAGE===============================================================//

$router->GET(
    '/storage/covers/:id',
    fn () => require __DIR__ . '/../resources/covers.php'
)->middleware($check_auth);

$router->GET(
    '/storage/profile-pictures/:id',
    fn () => require __DIR__ . '/../resources/profile-picture.php'
)->middleware($check_auth);

//=====ERRORS================================================================//

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

//=====API===================================================================//

$router->GET('/api/logout', function () {
    session_destroy();
    header('Location: /?logout=1', true, 303);
    die;
})->middleware($check_auth);

$router->GET(
    '/api/activate/:id/:token',
    fn () => require __DIR__ . '/../resources/api/activate.php'
);

$router->POST(
    '/api/listings/favourite',
    fn () => require __DIR__ . '/../resources/api/favourites.php'
)->middleware($check_auth);

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
    fn () => require __DIR__ . '/../resources/api/new-listing.php'
)->middleware($check_auth);

$router->DEFAULT(function () {
    header('Location: /404', true, 303);
    die;
});

$router->handle();
