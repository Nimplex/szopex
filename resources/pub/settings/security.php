<?php

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$SETTINGS_PAGE = [
    'self-url' => '/settings/security',
    'head' => '<link rel="stylesheet" href="/_dist/css/settings/security.css">',
    'title' => 'Zabezpieczenia',
    'scripts' => [],
];

ob_start();
?>

<button>Zmień adres e-mail</button>
<br>
<button>Zmień hasło</button>
<br>
<button>Dodaj weryfikacje dwuetapową</button>
<br>
<button>Usuń konto</button>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/settings.php';
