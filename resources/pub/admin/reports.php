<?php

if (isset($_SERVER['HTTP_PARTIAL_REQ'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/templates/reports.php';
    die;
}

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$SETTINGS_PAGE = [
    'self-url' => '/admin/reports',
    'head' => '<link rel="stylesheet" href="/_dist/css/reports.css">',
    'title' => 'Zgłoszenia',
    'scripts' => ''
];

ob_start();
?>

<div id="reports">
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/templates/reports.php'; ?>
</div>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/admin.php';
