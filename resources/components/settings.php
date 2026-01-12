<?php
/** @var array<string,mixed> $SETTINGS_PAGE */
/** @var string $CONTENT */

$title = 'Ustawienia';

$TITLE = sprintf('%s: %s', $title, $SETTINGS_PAGE['title']);
$HEAD = <<<HTML
<link rel="stylesheet" href="/_dist/css/settings/main.css">
<link rel="stylesheet" href="/_dist/css/sidebar.css">
HTML . ($SETTINGS_PAGE['head'] ?? '');

$SCRIPTS = [
    '/_dist/js/sidebar.js',
    ...$SETTINGS_PAGE['scripts'] ?? []
];

$SIDEBAR_CFG = [
    'type' => 'link',
    'title' => $title,
    'groups' => [
        [
            ['Profil', '/settings/profile', 'user'],
            ['Powiadomienia', '/settings/notifications', 'bell'],
            ['Zabezpieczenia', '/settings/security', 'lock'],
        ],
    ],
    'group_names' => [
        'Ogólne',
    ],
    'selected' => $SETTINGS_PAGE['self-url'],
];

ob_start();
?>

<div id="sidebar-wrapper">
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/sidebar.php' ?>
    <section id="sidebar-pane">
        <div id="heading">
            <button id="sidebar-open-button" type="button" onclick="window.openSidebar();">
                <i data-lucide="menu" aria-hidden="true"></i>
                <span class="sr-only">Otwórz panel boczny</span>
            </button>
            <h1><?= $SETTINGS_PAGE['title'] ?></h1>
        </div>
        <hr>
        <?= $CONTENT ?>
    </section>
</div>
<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
