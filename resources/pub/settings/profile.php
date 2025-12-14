<?php

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$SETTINGS_PAGE = [
    'self-url' => '/settings/profile',
    'head' => '<link rel="stylesheet" href="/_dist/css/settings/edit_profile.css">',
    'title' => 'Edytuj profil',
    'scripts' => ['/_dist/js/edit_profile.js'],
];

$res = $user_controller->user->get_profile($_SESSION['user_id']);
if (!$res) {
    require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/404.php';
    die;
}

$display_name = htmlspecialchars($res['display_name']);

ob_start();
?>

<form action="/api/update-profile" method="POST" enctype="multipart/form-data">
    <div class="row">
        <section id="set-pfp">
            <label id="set-pfp-inner">
                <input
                    type="file"
                    class="sr-only"
                    onchange="setPreview(this)"
                    name="pfp"
                    accept="image/jpeg,image/png"
                >
                <img
                    src="/api/storage/profile-pictures/<?= urlencode($res['picture_id']); ?>"
                    id="profile-picture"
                    alt="Obecne zdjęcie profilowe"
                >
                <i data-lucide="pen" aria-hidden="true"></i>
                <span class="sr-only">Zmień zdjęcie profilowe</span>
            </label>
            Naciśnij, aby zmienić zdjęcie profilowe
        </section>
        <section id="user_name_values">
            <label>
                Nazwa wyświetlana:
                <div>
                    <input type="text" name="user_name" value="<?= $display_name ?>">
                </div>
            </label>
            <br>
            <label>
                Login:
                <div>
                    <input type="text" name="user_login" value="<?= htmlspecialchars($_SESSION['user_login']); ?>">
                </div>
            </label>
        </section>
    </div>
    <br>
    <button type="submit" class="btn-accent">
        <i data-lucide="save" aria-hidden="true"></i>
        <span>Zapisz zmiany</span>
    </button>
</form>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/settings.php';
