<?php
/** @var array<string,mixed> $SETTINGS_PAGE */
/** @var string $CONTENT */

$title = 'Ustawienia';

$TITLE = sprintf('%s: %s', $title, $SETTINGS_PAGE['title']);
$HEAD = <<<HTML
<link rel="stylesheet" href="/_dist/css/settings/main.css">
<link rel="stylesheet" href="/_dist/css/sidebar.css">
HTML . ($SETTINGS_PAGE['head'] ?? '');

$SCRIPTS = $SETTINGS_PAGE['scripts'] ?? null;

$SIDEBAR_CFG = [
    'title' => $title,
    'groups' => [
        [
            ['Edytuj profil', '/settings/profile', 'user'],
            ['Powiadomienia', '/settings/notifications', 'bell'],
            ['Zabezpieczenia', '/settings/security', 'lock'],
            ['Ahmed', '/abdul', 'book'],
            ['Ahmed', '/abdul', 'book'],
            ['Ahmed', '/abdul', 'book'],
        ],
        [
            ['Ahmed', '/abdul', 'book'],
            ['Ahmed', '/abdul', 'book'],
            ['Ahmed', '/abdul', 'book'],
        ],
    ],
    'group_names' => [
        'meow',
        'mreow~',
    ],
    'selected' => $SETTINGS_PAGE['self-url'],
];

ob_start();
?>

<div id="sidebar-wrapper">
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/sidebar.php' ?>
    <section id="sidebar-pane">
        <h2><?= $SETTINGS_PAGE['title'] ?></h2>
        <?= $CONTENT ?>
    </section>
</div>
<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
