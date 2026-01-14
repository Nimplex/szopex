<?php
use App\FlashMessage;

/** @var \App\Controller\UserController $user_controller */
global $user_controller;

$SETTINGS_PAGE = [
    'self-url' => '/settings/notifications',
    'head' => '<link rel="stylesheet" href="/_dist/css/settings/notifications.css">',
    'title' => 'Powiadomienia',
    'scripts' => [
        '/_dist/js/form_modified.js',
    ],
];

$edit_indicator = '<span class="edit-indicator" aria-hidden="true">&nbsp;•</span>';

$user = $user_controller->user->find_by_id($_SESSION['user_id']);

if (!$user) {
    (new FlashMessage())->setErr('i18n:user_not_found');
    @session_destroy();
    header('Location: /login', true, 303);
    die;
}

ob_start();
?>

<form action="/api/update-notifications" method="POST" enctype="multipart/form-data">
    <table>
        <tbody>
            <tr class="check-updates">
                <td>
                    <label for="notifications_message">Powiadomienia o przychodzących wiadomościach</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_message" id="notifications_message" <?= $user['notifications_message'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="notifications_reports">Powiadomienia dotyczące zgłoszeń (werdykty, przyjęcie)</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_reports" id="notifications_reports" <?= $user['notifications_reports'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="notifications_login">Powiadomienia o logowaniu</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_login" id="notifications_login" <?= $user['notifications_login'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="notifications_listings">Powiadomienia o ogłoszeniach (dodawanie)</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_listings" id="notifications_listings" <?= $user['notifications_listings'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="notifications_administrative">Powiadomienia administracyjne (ważne powiadomienia)</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_administrative" id="notifications_administrative" <?= $user['notifications_administrative'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="notifications_contact">Ogłoszenia ogólne</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_contact" id="notifications_contact" <?= $user['notifications_contact'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="notifications_marketing">Powiadomienia marketingowe</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="notifications_marketing" id="notifications_marketing" <?= $user['notifications_marketing'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="mobile_app_notifications">Powiadomienia push (aplikacja)</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="mobile_app_notifications" id="mobile_app_notifications" <?= $user['mobile_app_notifications'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
            <tr class="check-updates">
                <td>
                    <label for="email_notifications">Powiadomienia mailowe</label>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="email_notifications" id="email_notifications" <?= $user['email_notifications'] == true ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <?= $edit_indicator ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div id="counter-wrapper">
        <button type="submit" class="btn-accent">
            <i data-lucide="save" aria-hidden="true"></i>
            <span>Zapisz zmiany</span>
        </button>
        <span id="update-counter"></span>
    </div>
</form>

<?php
$CONTENT = ob_get_clean();

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/settings.php';
